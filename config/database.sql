CREATE TYPE resource_type as ENUM('book', 'magazine', 'essay');
CREATE TYPE resource_status as ENUM('active', 'returned', 'expired');
CREATE TYPE quote_mode as ENUM('draft', 'published');

CREATE TYPE tag_type AS ENUM (
    'inspirational',
    'funny',
    'sad',
    'romantic',
    'philosophical',
    'motivational',
    'historical',
    'spiritual'
);


-- Users 
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    --is_admin BOOLEAN DEFAULT FALSE,
    --created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--Collections (group of resources)
CREATE TABLE collections (
    id SERIAL PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    origin_url TEXT,
    is_printed BOOLEAN DEFAULT FALSE
);

--Resources
CREATE TABLE resources (
    id SERIAL PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type resource_type NOT NULL, 
    collection_id INTEGER REFERENCES collections(id),
    max_read_days INTEGER DEFAULT 20,
    max_users INTEGER DEFAULT 5,
    --created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--Limits for users???
CREATE TABLE user_borrow_limits (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    max_resources INTEGER DEFAULT 5,
    default_days INTEGER DEFAULT 20
);

--Resource Access
CREATE TABLE resource_access (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    resource_id INTEGER REFERENCES resources(id) ON DELETE CASCADE,
    access_start TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    access_end TIMESTAMP NOT NULL,
    status resource_status NOT NULL.
    access_link TEXT,
    --returned_at TIMESTAMP,
    UNIQUE (user_id, resource_id)
);

--Quotes
CREATE TABLE quotes (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    resource_id INTEGER REFERENCES resources(id) ON DELETE SET NULL,
    book_title VARCHAR(255),
    content TEXT NOT NULL,
    mode quote_mode NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    --updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    report_count INTEGER DEFAULT 0
);


--Tags
CREATE TABLE quote_tags (
    quote_id INTEGER REFERENCES quotes(id) ON DELETE CASCADE,
    tag tag_type NOT NULL,
    PRIMARY KEY (quote_id, tag)
);

--Annotations
CREATE TABLE annotations (
    id SERIAL PRIMARY KEY,
    quote_id INTEGER REFERENCES quotes(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--Likes
CREATE TABLE likes (
    quote_id INTEGER REFERENCES quotes(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (quote_id, user_id)
);

--Reports
CREATE TABLE reports (
    id SERIAL PRIMARY KEY,
    quote_id INTEGER REFERENCES quotes(id) ON DELETE CASCADE,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    reason TEXT NOT NULL,
    --created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

--Resource Exports
CREATE TABLE resource_exports (
    id SERIAL PRIMARY KEY,
    user_id INTEGER REFERENCES users(id) ON DELETE CASCADE,
    resource_id INTEGER REFERENCES resources(id) ON DELETE CASCADE,
    export_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    export_type VARCHAR(50) -- e.g. 'PDF'
);

--Resources statistics
CREATE VIEW resource_stats AS
SELECT
    r.id AS resource_id,
    r.title,
    COUNT(ra.id) AS total_borrows,
    COUNT(CASE WHEN ra.status = 'active' THEN 1 END) AS currently_active,
    COUNT(CASE WHEN ra.status = 'returned' THEN 1 END) AS total_returns
FROM resources r
LEFT JOIN resource_access ra ON ra.resource_id = r.id
GROUP BY r.id;

--Quotes statustics
CREATE VIEW quote_statistics AS
SELECT
    q.id AS quote_id,
    COUNT(DISTINCT l.user_id) AS like_count,
    COUNT(DISTINCT a.id) AS annotation_count,
    COUNT(DISTINCT r.id) AS report_count
FROM quotes q
LEFT JOIN likes l ON l.quote_id = q.id
LEFT JOIN annotations a ON a.quote_id = q.id
LEFT JOIN reports r ON r.quote_id = q.id
GROUP BY q.id;

--Users statistics
CREATE VIEW user_reading_stats AS
SELECT
    u.id AS user_id,
    u.name,
    COUNT(ra.id) AS total_resources_read,
    COUNT(DISTINCT ra.resource_id) AS distinct_resources
FROM users u
LEFT JOIN resource_access ra ON ra.user_id = u.id
GROUP BY u.id;

-- users table
CREATE TABLE IF NOT EXISTS users (
                                     id         CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    full_name  VARCHAR(255) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL UNIQUE,
    role       VARCHAR(50) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CHECK (role IN ('user', 'admin'))
    );

-- quotes table
CREATE TABLE IF NOT EXISTS quotes (
                                      id         CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    title      VARCHAR(255) NOT NULL,
    content    TEXT NOT NULL,
    author     VARCHAR(255),
    user_id    CHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

-- collections table
CREATE TABLE IF NOT EXISTS collections (
                                           id          CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name        VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    user_id     CHAR(36),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

-- collection_quotes table
CREATE TABLE IF NOT EXISTS collection_quotes (
                                                 id            CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    collection_id CHAR(36) NOT NULL,
    quote_id      CHAR(36) NOT NULL,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (collection_id) REFERENCES collections(id) ON DELETE CASCADE,
    FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE,
    CONSTRAINT unique_collection_quote UNIQUE (collection_id, quote_id)
    );

-- tags table
CREATE TABLE IF NOT EXISTS tags (
                                    id   CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL UNIQUE
    );

-- annotations table
CREATE TABLE IF NOT EXISTS annotations (
                                           id         CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    quote_id   CHAR(36),
    user_id    CHAR(36),
    note       TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

-- likes table
CREATE TABLE IF NOT EXISTS likes (
                                     id         CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    quote_id   CHAR(36),
    user_id    CHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

-- reports table
CREATE TABLE IF NOT EXISTS reports (
                                       id         CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    quote_id   CHAR(36),
    user_id    CHAR(36),
    reason     TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

-- booked table
CREATE TABLE IF NOT EXISTS booked (
                                      id         CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    quote_id   CHAR(36),
    user_id    CHAR(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (quote_id) REFERENCES quotes(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    );

-- logs table
CREATE TABLE IF NOT EXISTS logs (
                                    id         CHAR(36) PRIMARY KEY DEFAULT (UUID()),
    user_id    CHAR(36),
    action     VARCHAR(255) NOT NULL,
    details    TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    );

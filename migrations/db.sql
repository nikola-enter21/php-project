-- Enable the UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Users table
CREATE TABLE IF NOT EXISTS Users
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    full_name  VARCHAR(255) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL UNIQUE,
    role       VARCHAR(50) NOT NULL DEFAULT 'user',
    created_at TIMESTAMP        DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_user_role CHECK (role IN ('user', 'admin'))
);

-- Quotes table
CREATE TABLE IF NOT EXISTS Quotes
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    title      VARCHAR(255) NOT NULL,
    content    TEXT         NOT NULL,
    author     VARCHAR(255),
    user_id    UUID REFERENCES Users (id) ON DELETE CASCADE,
    created_at TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
);

-- Collections table
CREATE TABLE IF NOT EXISTS Collections 
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name       VARCHAR(255) NOT NULL,
    description     TEXT         NOT NULL,
    user_id    UUID REFERENCES Users (id) ON DELETE CASCADE,
    created_at TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
);

-- Collection_Quotes table
CREATE TABLE IF NOT EXISTS Collection_Quotes
(
    id            UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    collection_id UUID NOT NULL REFERENCES Collections (id) ON DELETE CASCADE,
    quote_id      UUID NOT NULL REFERENCES Quotes (id) ON DELETE CASCADE,
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tags table
CREATE TABLE IF NOT EXISTS Tags
(
    id   UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL UNIQUE
);

-- Annotations table
CREATE TABLE IF NOT EXISTS Annotations
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    quote_id   UUID REFERENCES Quotes (id) ON DELETE CASCADE,
    user_id    UUID REFERENCES Users (id) ON DELETE CASCADE,
    note       TEXT,
    created_at TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
);

-- Likes table
CREATE TABLE IF NOT EXISTS Likes
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    quote_id   UUID REFERENCES Quotes (id) ON DELETE CASCADE,
    user_id    UUID REFERENCES Users (id) ON DELETE CASCADE,
    created_at TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
);

-- Reports table
CREATE TABLE IF NOT EXISTS Reports
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    quote_id   UUID REFERENCES Quotes (id) ON DELETE CASCADE,
    user_id    UUID REFERENCES Users (id) ON DELETE CASCADE,
    reason     TEXT,
    created_at TIMESTAMP        DEFAULT CURRENT_TIMESTAMP
);

-- Booked (saved quotes) table
CREATE TABLE IF NOT EXISTS Booked
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    quote_id   UUID REFERENCES Quotes (id) ON DELETE CASCADE,
    user_id    UUID REFERENCES Users (id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE Collection_Quotes ADD CONSTRAINT unique_collection_quote UNIQUE (collection_id, quote_id);

-- Logs table
CREATE TABLE IF NOT EXISTS Logs
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    user_id    UUID REFERENCES Users (id) ON DELETE SET NULL, -- User performing the action
    action     VARCHAR(255) NOT NULL, -- Type of action (e.g., "delete_quote", "update_role")
    details    TEXT, -- Additional details about the action
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Timestamp of the action
);

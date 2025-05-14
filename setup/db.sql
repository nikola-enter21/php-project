-- Enable the UUID extension
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Users table
CREATE TABLE Users
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    full_name  VARCHAR(255) NOT NULL,
    password   VARCHAR(255) NOT NULL,
    email      VARCHAR(255) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Quotes table
CREATE TABLE Quotes
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    title      VARCHAR(255) NOT NULL,
    content    TEXT         NOT NULL,
    author     VARCHAR(255),
    user_id    UUID REFERENCES Users (id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tags table
CREATE TABLE Tags
(
    id   UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    name VARCHAR(255) NOT NULL UNIQUE
);

-- Annotations table
CREATE TABLE Annotations
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    quote_id   UUID REFERENCES Quotes (id) ON DELETE CASCADE,
    user_id    UUID REFERENCES Users (id) ON DELETE CASCADE,
    note       TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Likes table
CREATE TABLE Likes
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    quote_id   UUID REFERENCES Quotes (id) ON DELETE CASCADE,
    user_id    UUID REFERENCES Users (id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Reports table
CREATE TABLE Reports
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    quote_id   UUID REFERENCES Quotes (id) ON DELETE CASCADE,
    user_id    UUID REFERENCES Users (id) ON DELETE CASCADE,
    reason     TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Booked (saved quotes) table
CREATE TABLE Booked
(
    id         UUID PRIMARY KEY DEFAULT uuid_generate_v4(),
    quote_id   UUID REFERENCES Quotes (id) ON DELETE CASCADE,
    user_id    UUID REFERENCES Users (id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
-- Seed admin user if not exists
INSERT INTO users (id, full_name, email, password, role)
SELECT UUID(), 'QuoteShare Admin', 'admin@quoteshare.com',
       '$2y$12$rKqcLNjBE8t7vARmLq0BPOu0IQh4YkJHx9CPpTwuvQcAhfBayXsCi', 'admin'
    WHERE NOT EXISTS (
    SELECT 1 FROM users WHERE role = 'admin'
);
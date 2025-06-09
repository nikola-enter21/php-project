-- Seed admin user 
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM Users WHERE role = 'admin') THEN
        INSERT INTO Users (full_name, email, password, role)
        VALUES (
            'QuoteShare Admin',
            'admin@quoteshare.com',
            '$2y$12$rKqcLNjBE8t7vARmLq0BPOu0IQh4YkJHx9CPpTwuvQcAhfBayXsCi',
            'admin'
        );
    END IF;
END
$$;

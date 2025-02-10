-- #!sqlite

-- #{ table
    -- #{ tokens
        CREATE TABLE IF NOT EXISTS tokens (
            uuid TEXT PRIMARY KEY,
            name TEXT NOT NULL,
            balance INTEGER NOT NULL DEFAULT 0
        );
    -- #}
-- #}

-- #{ tokens
    -- #{ create
        -- # :uuid string
        -- # :name string
        -- # :balance int
        INSERT INTO tokens (uuid, name, balance)
        VALUES (:uuid, :name, :balance)
        ON CONFLICT(uuid) DO UPDATE SET name = excluded.name;
    -- #}

    -- #{ has
        -- # :uuid string
        SELECT balance FROM tokens WHERE uuid = :uuid;
    -- #}

    -- #{ get
        -- # :uuid string
        SELECT balance FROM tokens WHERE uuid = :uuid;
    -- #}

    -- #{ get_by_name
        -- # :name string
        SELECT uuid FROM tokens WHERE name = :name;
    -- #}

    -- #{ add
        -- # :uuid string
        -- # :amount int
        UPDATE tokens SET balance = balance + :amount WHERE uuid = :uuid;
    -- #}

    -- #{ remove
        -- # :uuid string
        -- # :amount int
        UPDATE tokens SET balance = balance - :amount WHERE uuid = :uuid;
    -- #}

    -- #{ set
        -- # :uuid string
        -- # :amount int
        UPDATE tokens SET balance = :amount WHERE uuid = :uuid;
    -- #}

    -- #{ top
        SELECT name, balance FROM tokens ORDER BY balance DESC LIMIT 10;
    -- #}
-- #}

-- Create the game_events table
CREATE TABLE IF NOT EXISTS game_events (
    aggregate_id VARCHAR(36) NOT NULL,
    version INT NOT NULL,
    date DATETIME NOT NULL,
    event LONGTEXT NOT NULL,
    PRIMARY KEY (aggregate_id, version)
);

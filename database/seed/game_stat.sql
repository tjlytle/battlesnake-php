CREATE TABLE game_stats (
    aggregate_id VARCHAR(36) NOT NULL,
    win BOOLEAN NOT NULL,
    PRIMARY KEY(aggregate_id)
) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB;

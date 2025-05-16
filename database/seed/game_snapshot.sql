CREATE TABLE game_snapshot (
   aggregate_id VARCHAR(36) NOT NULL,
   version INT NOT NULL,
   serialized BLOB NOT NULL,
   PRIMARY KEY (aggregate_id)
);

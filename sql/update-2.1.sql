ALTER TABLE backupset ADD COLUMN time_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
UPDATE backupset SET time_created="0000-00-00 00:00:00";
ALTER TABLE container_type ADD COLUMN time_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
UPDATE container_type SET time_created="0000-00-00 00:00:00";
ALTER TABLE programs ADD COLUMN time_created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
UPDATE programs SET time_created="0000-00-00 00:00:00";



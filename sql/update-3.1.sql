ALTER TABLE backupset MODIFY COLUMN notes longtext DEFAULT '';
UPDATE backupset SET notes="" WHERE notes IS NULL;
ALTER TABLE tape_library MODIFY COLUMN backupset INT DEFAULT '-1';
UPDATE tape_library SET backupset="-1" WHERE backupset IS NULL;

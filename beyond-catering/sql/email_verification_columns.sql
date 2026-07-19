ALTER TABLE users
ADD COLUMN verification_token VARCHAR(255) NULL AFTER email_verified,
ADD COLUMN verification_sent_at DATETIME NULL AFTER verification_token,
ADD COLUMN email_verified_at DATETIME NULL AFTER verification_sent_at;

ALTER TABLE users
MODIFY COLUMN email_verified TINYINT(1) NOT NULL DEFAULT 0;

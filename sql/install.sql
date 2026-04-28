-- AsteriskPBX module database schema for FrontAccounting

-- Extension to employee mapping
CREATE TABLE IF NOT EXISTS `fa_asterisk_extensions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `extension` VARCHAR(20) NOT NULL,
    `employee_id` INT(11) DEFAULT NULL,
    `sip_peer` VARCHAR(100) DEFAULT NULL,
    `email` VARCHAR(255) DEFAULT NULL,
    `forward_to` VARCHAR(20) DEFAULT NULL,
    `voicemail_enabled` TINYINT(1) NOT NULL DEFAULT 1,
    `status` ENUM('Active','Inactive') NOT NULL DEFAULT 'Active',
    PRIMARY KEY (`id`),
    UNIQUE KEY `extension` (`extension`),
    KEY `employee_id` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Call history
CREATE TABLE IF NOT EXISTS `fa_asterisk_calls` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `caller_number` VARCHAR(50) NOT NULL,
    `called_number` VARCHAR(20) NOT NULL,
    `extension` VARCHAR(20) NOT NULL,
    `direction` ENUM('inbound','outbound') NOT NULL DEFAULT 'inbound',
    `status` ENUM('ringing','answered','hung_up','missed') NOT NULL DEFAULT 'ringing',
    `linked_type` ENUM('none','contact','lead','account') NOT NULL DEFAULT 'none',
    `linked_id` INT(11) DEFAULT NULL,
    `unique_id` VARCHAR(100) DEFAULT NULL,
    `channel` VARCHAR(100) DEFAULT NULL,
    `duration` INT(6) DEFAULT NULL,
    `recording_url` VARCHAR(500) DEFAULT NULL,
    `call_start` DATETIME DEFAULT NULL,
    `call_end` DATETIME DEFAULT NULL,
    `notes` TEXT,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `extension` (`extension`),
    KEY `caller_number` (`caller_number`),
    KEY `linked_type` (`linked_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Voicemail
CREATE TABLE IF NOT EXISTS `fa_asterisk_voicemail` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `extension` VARCHAR(20) NOT NULL,
    `caller_number` VARCHAR(50) DEFAULT NULL,
    `duration` INT(5) NOT NULL DEFAULT 0,
    `recording_url` VARCHAR(500) NOT NULL,
    `listened` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `extension` (`extension`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Module version
INSERT INTO `fa_modules` (`name`, `version`, `enabled`, `installed`) VALUES ('AsteriskPBX', '1.0.0', 1, NOW()) ON DUPLICATE KEY UPDATE `version` = '1.0.0';
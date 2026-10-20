-- AsteriskPBX module database schema for FrontAccounting

-- Extension to employee mapping
ALTER TABLE IF EXISTS `0_fa_asterisk_extensions` RENAME TO `0_ksf_asteriskpbx_extensions`;
ALTER TABLE IF EXISTS `0_fa_asterisk_calls` RENAME TO `0_ksf_asteriskpbx_calls`;
ALTER TABLE IF EXISTS `0_fa_asterisk_voicemail` RENAME TO `0_ksf_asteriskpbx_voicemail`;

CREATE TABLE IF NOT EXISTS `0_ksf_asteriskpbx_extensions` (
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
CREATE TABLE IF NOT EXISTS `0_ksf_asteriskpbx_calls` (
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
CREATE TABLE IF NOT EXISTS `0_ksf_asteriskpbx_voicemail` (
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
INSERT INTO `0_ksf_modules` (`name`, `version`, `enabled`, `installed`) VALUES
('AsteriskPBX', '1.0.0', 1, NOW()) ON DUPLICATE KEY UPDATE `version` = '1.0.0';

-- ---------------------------------------------------------------------------
-- Module-local employee directory.
--
-- WHY THIS TABLE EXISTS (and why it is not FA core):
--   FA core has no employee entity. In this estate an employee is an FA
--   person (0_crm_persons) plus a crm_categories row of type 'employee' plus
--   a satellite employment table - the satellite belongs to ksf_FA_HRM
--   (0_hrm_contacts_employment). Reaching into another module's table would
--   make this module depend on that module being activated, which
--   AGENTS_ARCH.md 12.2 forbids ("a shared seam must never be gated on
--   another module's activation"). The contact resolver in ksf-fa-common
--   deliberately does not cover the employee intent either.
--   So this module keeps its OWN employee directory, keyed by the same
--   employee_id its own records already carry. It is module-local by design.
-- ---------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `0_ksf_asteriskpbx_employees` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(120) DEFAULT NULL,
    `active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
<?php
/**
 * FA_AsteriskPBX Module Hooks for FrontAccounting
 */

define('SS_ASTERISK', 120 << 8);

class hooks_fa_asteriskpbx extends hooks {
    var $module_name = 'fa_asteriskpbx';

    function install_options($app) {
        global $path_to_root;

        switch($app->id) {
            case 'CRM':
                $app->add_lapp_function(0, _("Extension Mapping"),
                    $path_to_root."/modules/".$this->module_name."/extensions.php", 'SA_ASTERISKADMIN', MENU_MAINTENANCE);
                $app->add_lapp_function(1, _("Call History"),
                    $path_to_root."/modules/".$this->module_name."/calls.php", 'SA_ASTERISKVIEW', MENU_INQUIRY);
                $app->add_lapp_function(2, _("Voicemail"),
                    $path_to_root."/modules/".$this->module_name."/voicemail.php", 'SA_ASTERISKMANAGE', MENU_INQUIRY);
                break;
        }
    }

    function install_access() {
        $security_sections[SS_ASTERISK] = _("Asterisk PBX");
        $security_areas['SA_ASTERISKADMIN'] = array(SS_ASTERISK | 1, _("Administer Extensions"));
        $security_areas['SA_ASTERISKVIEW'] = array(SS_ASTERISK | 2, _("View Call History"));
        $security_areas['SA_ASTERISKMANAGE'] = array(SS_ASTERISK | 3, _("Manage Calls"));
        return array($security_areas, $security_sections);
    }

    function activate_extension($company, $check_only=true) {
        $updates = array('sql/update.sql' => array($this->module_name));
        $ok = $this->update_databases($company, $updates, $check_only);
        if ($check_only || !$ok) {
            return $ok;
        }
        $this->ensure_asterisk_schema();
        return $ok;
    }

    private function table_exists($table) {
        $sql = "SHOW TABLES LIKE " . db_escape($table);
        $res = db_query($sql, 'Failed checking table existence');
        return db_num_rows($res) > 0;
    }

    private function ensure_asterisk_schema() {
        $tables = array(
            TB_PREF . "fa_asterisk_extensions" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_asterisk_extensions` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `extension` VARCHAR(20) NOT NULL,
                    `employee_id` VARCHAR(100) DEFAULT NULL,
                    `name` VARCHAR(100) DEFAULT NULL,
                    `is_active` TINYINT(1) DEFAULT 1,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `idx_extension` (`extension`),
                    KEY `idx_employee` (`employee_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_asterisk_calls" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_asterisk_calls` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `caller_number` VARCHAR(20) DEFAULT NULL,
                    `called_number` VARCHAR(20) DEFAULT NULL,
                    `extension` VARCHAR(20) DEFAULT NULL,
                    `call_type` VARCHAR(20) DEFAULT 'inbound',
                    `start_time` DATETIME NOT NULL,
                    `end_time` DATETIME DEFAULT NULL,
                    `duration` INT(11) DEFAULT 0,
                    `status` VARCHAR(20) DEFAULT 'completed',
                    `recording_path` VARCHAR(500) DEFAULT NULL,
                    `debtor_no` VARCHAR(20) DEFAULT NULL,
                    `contact_id` INT(11) DEFAULT NULL,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_extension` (`extension`),
                    KEY `idx_debtor` (`debtor_no`),
                    KEY `idx_start_time` (`start_time`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

            TB_PREF . "fa_asterisk_voicemail" => "
                CREATE TABLE IF NOT EXISTS `" . TB_PREF . "fa_asterisk_voicemail` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `extension` VARCHAR(20) NOT NULL,
                    `caller_number` VARCHAR(20) DEFAULT NULL,
                    `message_date` DATETIME NOT NULL,
                    `duration` INT(11) DEFAULT 0,
                    `file_path` VARCHAR(500) DEFAULT NULL,
                    `is_read` TINYINT(1) DEFAULT 0,
                    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    KEY `idx_extension` (`extension`),
                    KEY `idx_read` (`is_read`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        foreach ($tables as $table_name => $sql) {
            db_query($sql, "Could not create Asterisk PBX table: $table_name");
        }
    }

    function db_prevoid($trans_type, $trans_no) {
        // Handle voiding if needed
    }
}
?>

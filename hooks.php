<?php
/**
 * AsteriskPBX Module for FrontAccounting
 * 
 * Admin config: extension to employee mapping
 * User UI: call popups with contact lookup and lead creation
 */

$module_id = 'AsteriskPBX';
$module_version = '1.0.0';
$module_name = 'AsteriskPBX';
$module_description = 'Asterisk PBX integration with call popup and extension management';

$module_tables = [
    'fa_asterisk_extensions',
    'fa_asterisk_calls',
    'fa_asterisk_voicemail',
];

$module_capabilities = [
    'SA_ASTERISKADMIN' => 'Administer Extensions',
    'SA_ASTERISKVIEW' => 'View Call History',
    'SA_ASTERISKMANAGE' => 'Manage Calls',
];

function asteriskpbx_install(): bool
{
    global $db, $db_multi_sql;
    $sql_file = dirname(__FILE__) . '/../sql/install.sql';
    if (!file_exists($sql_file)) return false;
    $sql = file_get_contents($sql_file);
    return $db_multi_sql($sql);
}

function asteriskpbx_enable(): bool
{
    global $db;
    return $db->query("UPDATE " . TB_PREF . "modules SET enabled = 1 WHERE name = 'AsteriskPBX'");
}

function asteriskpbx_disable(): bool
{
    global $db;
    return $db->query("UPDATE " . TB_PREF . "modules SET enabled = 0 WHERE name = 'AsteriskPBX'");
}

function asteriskpbx_remove(): bool
{
    global $db, $db_multi_sql;
    $sql = "DROP TABLE IF EXISTS " . TB_PREF . "asterisk_voicemail;
           DROP TABLE IF EXISTS " . TB_PREF . "asterisk_calls;
           DROP TABLE IF EXISTS " . TB_PREF . "asterisk_extensions;
           DELETE FROM " . TB_PREF . "modules WHERE name = 'AsteriskPBX';";
    return $db_multi_sql($sql);
}

add_module($module_name, $module_version, $module_description);
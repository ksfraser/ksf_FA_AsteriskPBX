<?php
/**
 * AsteriskPBX Admin - Extension Management
 * 
 * Admin page to map extensions to employees
 */

$page_security = 'SA_ASTERISKADMIN';
$path_to_root = "../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FA_AsteriskPBX/includes/asterisk_db.inc");
include_once($path_to_root . "/modules/FA_AsteriskPBX/includes/asterisk_config.inc");

page(_("Asterisk Extension Management"), false, false, "", $js);

$extension_id = isset($_GET['extension_id']) ? $_GET['extension_id'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_extension'])) {
    $extension = $_POST['extension'] ?? '';
    $employee_id = $_POST['employee_id'] ?? null;
    $email = $_POST['email'] ?? '';
    $forward_to = $_POST['forward_to'] ?? '';
    $sip_peer = $_POST['sip_peer'] ?? '';
    
    if ($extension_id > 0) {
        update_extension($extension_id, $employee_id, $email, $forward_to);
        
        // Sync to Asterisk
        $ext = get_extension($extension_id);
        if ($ext) {
            sync_extension_to_asterisk($ext);
        }
        
        display_notification(_("Extension updated and synced to Asterisk"));
    } else {
        $new_id = create_extension($extension, $employee_id, $sip_peer, $email);
        
        // Sync to Asterisk
        $ext = get_extension($new_id);
        if ($ext) {
            sync_extension_to_asterisk($ext);
        }
        
        display_notification(_("Extension created and synced to Asterisk"));
    }
    meta_refresh(null, "?");
    $Ajax->activate('content_area');
}

// Handle settings save
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save_settings'])) {
    save_asterisk_settings(
        $_POST['asterisk_host'],
        $_POST['asterisk_port'],
        $_POST['asterisk_user'],
        $_POST['asterisk_pass'],
        $_POST['asterisk_config_dir']
    );
    display_notification(_("Asterisk settings saved"));
}

echo "<h2>" . _("Asterisk Extensions") . "</h2>";

start_table(TABLESTYLE);
table_header([
    _('Extension'),
    _('Employee'),
    _('SIP Peer'),
    _('Email'),
    _('Status'),
    _('Action'),
]);

$extensions = get_extensions();

while ($ext = db_fetch($extensions)) {
    alt_table_row($ext);
    label_cell($ext['extension']);
    label_cell($ext['employee_name'] ?? '<i>Unassigned</i>');
    label_cell($ext['sip_peer'] ?? '-');
    label_cell($ext['email'] ?? '-');
    label_cell($ext['status']);
    echo "<td><a href='?extension_id=" . $ext['id'] . "'>" . _("Edit") . "</a></td>";
}

end_table(1);

echo "<br><a href='?extension_id=0'>" . _("Add New Extension") . "</a>";

// Asterisk Settings Section
echo "<br><br><h2>" . _("Asterisk Settings") . "</h2>";

$asterisk_host = get_asterisk_host();
$asterisk_port = get_asterisk_port();
$asterisk_user = get_asterisk_admin_user();
$asterisk_config_dir = get_asterisk_config_dir();

start_form(true);
start_table(TABLESTYLE);

text_row(_("Asterisk Host:"), 'asterisk_host', $asterisk_host, 50);
text_row(_("AMI Port:"), 'asterisk_port', $asterisk_port ?: 5038, 10);
text_row(_("AMI Username:"), 'asterisk_user', $asterisk_user, 30);
password_row(_("AMI Password:"), 'asterisk_pass', '');
text_row(_("Config Directory:"), 'asterisk_config_dir', $asterisk_config_dir ?: '/etc/asterisk', 100);

end_table(1);

submit_center('save_settings', _("Save Settings"));
end_form();

if ($extension_id !== null && ($extension_id > 0 || isset($_GET['extension_id']))) {
    $extension = $extension_id > 0 ? get_extensions(['id' => $extension_id]) : null;
    $ext = $extension_id > 0 ? db_fetch($extension) : ['extension' => '', 'employee_id' => '', 'email' => '', 'forward_to' => '', 'sip_peer' => ''];
    
    start_form(true);
    
    start_table(TABLESTYLE);
    
    text_row(_("Extension:"), 'extension', $ext['extension'] ?? '', 20);
    
    $emp_options = ['' => _('-- Select Employee --')] + get_employees_list_options();
    select_row(_("Employee:"), 'employee_id', $emp_options, $ext['employee_id'] ?? '');
    
    text_row(_("SIP Peer Name:"), 'sip_peer', $ext['sip_peer'] ?? '', 50);
    text_row(_("Email:"), 'email', $ext['email'] ?? '', 100);
    text_row(_("Forward To:"), 'forward_to', $ext['forward_to'] ?? '', 20);
    
    end_table(1);
    
    submit_center('save_extension', _("Save Extension"));
    
    end_form();
}

end_page(true);

function get_employees_list_options(): array
{
    $result = get_employees_list();
    $options = [];
    while ($emp = db_fetch($result)) {
        $options[$emp['id']] = $emp['name'];
    }
    return $options;
}

function get_extension(int $id): ?array
{
    global $db;
    $sql = "SELECT * FROM " . TB_PREF . "asterisk_extensions WHERE id = " . $id;
    $result = $db->query($sql);
    if ($result && $db->num_rows($result) > 0) {
        return $db->fetch($result);
    }
    return null;
}
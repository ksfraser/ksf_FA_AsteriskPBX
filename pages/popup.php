<?php
/**
 * AsteriskPBX Call Popup
 * 
 * Shows on incoming call - lookup contact or create new lead
 * Requires real-time update via AJAX or WebSocket
 */

$page_security = 'SA_ASTERISKVIEW';
$path_to_root = "../../..";

include_once($path_to_root . "/includes/session.inc");
include_once($path_to_root . "/includes/ui.inc");
include_once($path_to_root . "/modules/FA_AsteriskPBX/includes/asterisk_db.inc");

// Check for incoming call via AJAX or direct call
$call_id = $_GET['call_id'] ?? 0;
$caller_number = $_GET['caller'] ?? '';

// If AJAX poll for new call
if (isset($_GET['poll'])) {
    $my_ext = get_extension_for_employee($_SESSION["wa_user"]->employee_id);
    if ($my_ext) {
        $calls = get_recent_calls_for_extension($my_ext['extension'], 1);
        $call = db_fetch($calls);
        
        if ($call && in_array($call['status'], ['ringing', 'answered'])) {
            header('Content-Type: application/json');
            echo json_encode(['new_call' => true, 'call_id' => $call['id'], 'caller_number' => $call['caller_number']]);
            exit;
        }
    }
    header('Content-Type: application/json');
    echo json_encode(['new_call' => false]);
    exit;
}

// Direct call with caller ID - show popup
if (!$caller_number) {
    exit;
}

$lookup = lookup_phone_number($caller_number);
$has_contact = !empty($lookup['contacts']) || !empty($lookup['leads']) || !empty($lookup['customers']);

page(_("Incoming Call: ") . $caller_number, false, false, get_js_popup());

start_table(TABLESTYLE);
echo "<tr><td colspan='2' style='background:#e74c3c;color:white;padding:10px;'><h2>" . _("Incoming Call: ") . $caller_number . "</h2></td></tr>";
end_table();

if ($has_contact) {
    // Show found contacts
    echo "<h3>" . _("Found Contacts") . "</h3>";
    
    start_table(TABLESTYLE);
    
    // Existing contacts
    if (!empty($lookup['contacts'])) {
        echo "<tr><th colspan='2'>" . _("Contacts") . "</th></tr>";
        foreach ($lookup['contacts'] as $contact) {
            alt_table_row($contact);
            echo "<td>" . $contact['type'] . " #" . $contact['id'] . "</td>";
            echo "<td><button onclick='select_contact(\"contact\", " . $contact['id'] . ")'>" . _("View") . "</button></td>";
        }
    }
    
    // CRM Leads
    if (!empty($lookup['leads'])) {
        echo "<tr><th colspan='2'>" . _("Leads") . "</th></tr>";
        foreach ($lookup['leads'] as $lead) {
            alt_table_row($lead);
            echo "<td>" . $lead['name'] . "</td>";
            echo "<td><button onclick='select_contact(\"lead\", " . $lead['id'] . ")'>" . _("View") . "</button></td>";
        }
    }
    
    // Customers
    if (!empty($lookup['customers'])) {
        echo "<tr><th colspan='2'>" . _("Customers") . "</th></tr>";
        foreach ($lookup['customers'] as $cust) {
            alt_table_row($cust);
            echo "<td>" . $cust['name'] . "</td>";
            echo "<td><button onclick='select_contact(\"customer\", " . $cust['id'] . ")'>" . _("View") . "</button></td>";
        }
    }
    
    end_table(1);
    
    echo "<br><button onclick='show_new_lead(\"" . $caller_number . "\")'>" . _("Create New Lead") . "</button>";
} else {
    // No contacts found - show new lead form
    echo "<h3>" . _("New Caller") . "</h3>";
    echo "<p>" . _("No existing contact found for this number. Create a new lead?") . "</p>";
}

start_div("id='new_lead_form' style='display:none'");
start_form(true);
start_table(TABLESTYLE);

hidden('caller_number', $caller_number);
text_row(_("Phone:"), 'phone', $caller_number, 20);
text_row(_("Name:"), 'name', '', 50);
text_row(_("Company:"), 'company', '', 50);
text_row(_("Email:"), 'email', '', 100);
textarea_row(_("Notes:"), 'notes', '', 3, 30);

end_table(1);
submit_center('create_lead', _("Create Lead and Accept Call"));
end_form();
end_div();

// Call history for this number
$history = get_call_history($caller_number);
if ($history && db_num_rows($history) > 0) {
    echo "<br><h3>" . _("Call History") . "</h3>";
    
    start_table(TABLESTYLE);
    table_header([_('Date'), _('Direction'), _('Extension'), _('Duration')]);
    
    while ($call = db_fetch($history)) {
        alt_table_row($call);
        label_cell(sql2date($call['call_start']) . " " . substr($call['call_start'], 11, 5));
        label_cell($call['direction']);
        label_cell($call['extension']);
        label_cell($call['duration'] ? $call['duration'] . "s" : "-");
    }
    
    end_table(1);
}

$js = "
function get_js_popup() {
    return '';
}
function show_new_lead(num) {
    document.getElementById('new_lead_form').style.display = 'block';
}
function select_contact(type, id) {
    // Open contact in CRM or new window
    var url = '';
    if (type == 'contact') url = '" . $path_to_root . "/modules/FA_CRM/pages/contact.php?id=' + id;
    else if (type == 'lead') url = '" . $path_to_root . "/modules/FA_CRM/pages/lead.php?id=' + id;
    else if (type == 'customer') url = '" . $path_to_root . "/modules/FA_CRM/pages/customer.php?id=' + id;
    
    if (url) window.open(url, '_blank');
}
";

end_page(true);
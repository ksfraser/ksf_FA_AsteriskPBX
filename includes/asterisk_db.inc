<?php
/**
 * AsteriskPBX Database Functions
 */

// Extension management
function create_extension(string $extension, int $employee_id = null, string $sip_peer = '', string $email = ''): int
{
    global $db;
    $sql = "INSERT INTO " . TB_PREF . "asterisk_extensions 
        (extension, employee_id, sip_peer, email, created_by)
        VALUES (" . $db->escape($extension) . ", " . ($employee_id ? $employee_id : 'NULL') . ", " . $db->escape($sip_peer) . ", " . $db->escape($email) . ", " . user_id() . ")";
    $db->query($sql);
    return $db->insert_id;
}

function get_extensions(array $filters = []): ?object
{
    global $db;
    $sql = "SELECT e.*, emp.name as employee_name FROM " . TB_PREF . "asterisk_extensions e
        LEFT JOIN " . TB_PREF . "hr_employees emp ON e.employee_id = emp.id WHERE 1=1";
    
    if (!empty($filters['employee_id'])) {
        $sql .= " AND e.employee_id = " . $filters['employee_id'];
    }
    if (!empty($filters['status'])) {
        $sql .= " AND e.status = " . $db->escape($filters['status']);
    }
    
    $sql .= " ORDER BY e.extension";
    return $db->query($sql);
}

function update_extension(int $id, int $employee_id = null, string $email = '', string $forward_to = ''): bool
{
    global $db;
    $sql = "UPDATE " . TB_PREF . "asterisk_extensions SET
        employee_id = " . ($employee_id ? $employee_id : 'NULL') . ",
        email = " . $db->escape($email) . ",
        forward_to = " . $db->escape($forward_to) . "
        WHERE id = " . $id;
    return $db->query($sql);
}

function get_extension_for_employee(int $employee_id): ?array
{
    global $db;
    $sql = "SELECT * FROM " . TB_PREF . "asterisk_extensions WHERE employee_id = " . $employee_id . " AND status = 'Active'";
    $result = $db->query($sql);
    if ($result && $db->num_rows($result) > 0) {
        return $db->fetch($result);
    }
    return null;
}

// Call logging
function log_call(string $caller_number, string $called_number, string $extension, string $direction, string $unique_id = '', string $channel = ''): int
{
    global $db;
    $sql = "INSERT INTO " . TB_PREF . "asterisk_calls 
        (caller_number, called_number, extension, direction, unique_id, channel, call_start)
        VALUES (" . $db->escape($caller_number) . ", " . $db->escape($called_number) . ", " . $db->escape($extension) . ", " . $db->escape($direction) . ", " . $db->escape($unique_id) . ", " . $db->escape($channel) . ", NOW())";
    $db->query($sql);
    return $db->insert_id;
}

function update_call_status(int $call_id, string $status, int $duration = 0): bool
{
    global $db;
    $sql = "UPDATE " . TB_PREF . "asterisk_calls SET status = " . $db->escape($status) . ", duration = " . $duration . ", call_end = NOW() WHERE id = " . $call_id;
    return $db->query($sql);
}

function link_call_to_entity(int $call_id, string $linked_type, int $linked_id): bool
{
    global $db;
    $sql = "UPDATE " . TB_PREF . "asterisk_calls SET linked_type = " . $db->escape($linked_type) . ", linked_id = " . $linked_id . " WHERE id = " . $call_id;
    return $db->query($sql);
}

function get_call_history(string $phone_number, int $limit = 10): ?object
{
    global $db;
    $sql = "SELECT c.*, e.extension, emp.name as employee_name FROM " . TB_PREF . "asterisk_calls c
        LEFT JOIN " . TB_PREF . "asterisk_extensions e ON c.extension = e.extension
        LEFT JOIN " . TB_PREF . "hr_employees emp ON e.employee_id = emp.id
        WHERE c.caller_number = " . $db->escape($phone_number) . " 
        ORDER BY c.call_start DESC LIMIT " . $limit;
    return $db->query($sql);
}

function get_recent_calls_for_extension(string $extension, int $limit = 20): ?object
{
    global $db;
    $sql = "SELECT * FROM " . TB_PREF . "asterisk_calls 
        WHERE extension = " . $db->escape($extension) . " 
        ORDER BY call_start DESC LIMIT " . $limit;
    return $db->query($sql);
}

// Phone number lookup - searches contacts, leads, customers
function lookup_phone_number(string $phone_number): array
{
    global $db;
    $results = ['contacts' => [], 'leads' => [], 'customers' => []];
    
    // Search contacts
    $sql = "SELECT * FROM " . TB_PREF . "ext_contact_info WHERE value = " . $db->escape($phone_number) . " LIMIT 5";
    $result = $db->query($sql);
    while ($row = $db->fetch($result)) {
        $results['contacts'][] = ['id' => $row['id'], 'type' => $row['type'], 'phone' => $phone_number];
    }
    
    // Search leads (if CRM module exists)
    if (table_exists('crm_leads')) {
        $sql = "SELECT id, name, phone FROM " . TB_PREF . "crm_leads WHERE phone = " . $db->escape($phone_number) . " LIMIT 5";
        $result = $db->query($sql);
        while ($row = $db->fetch($result)) {
            $results['leads'][] = ['id' => $row['id'], 'name' => $row['name'], 'phone' => $row['phone']];
        }
    }
    
    // Search customers
    if (table_exists('debtors_master')) {
        $sql = "SELECT debtor_no, name, phone FROM " . TB_PREF . "debtors_master WHERE phone = " . $db->escape($phone_number) . " LIMIT 5";
        $result = $db->query($sql);
        while ($row = $db->fetch($result)) {
            $results['customers'][] = ['id' => $row['debtor_no'], 'name' => $row['name'], 'phone' => $row['phone']];
        }
    }
    
    return $results;
}

function table_exists(string $table): bool
{
    global $db;
    $sql = "SHOW TABLES LIKE " . $db->escape(TB_PREF . $table);
    $result = $db->query($sql);
    return $result && $db->num_rows($result) > 0;
}
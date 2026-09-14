# Test Plan - Asterisk PBX Integration (ksf_FA_AsteriskPBX)

**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  
**Date:** May 2026  

---

## 1. Test Overview

### 1.1 Test Objectives

- Verify all database functions work correctly
- Validate extension CRUD operations
- Ensure call history recording works
- Confirm caller popup functionality
- Test WebRTC softphone behavior
- Achieve comprehensive test coverage

### 1.2 Test Scope

| Component | Coverage Target |
|-----------|----------------|
| Database functions | 100% |
| Extension management | 100% |
| Call recording | All scenarios |
| Popup logic | Phone lookup |
| Page rendering | All pages |

---

## 2. Test Cases

### 2.1 Database Function Tests

#### TC-DB001: Create Extension

**Test ID:** TC-DB001  
**Requirement:** EXT-001  
**Description:** Test create_extension function

**Setup:**
- Database connection available

**Execution:**
```php
$id = create_extension(
    ext: "201",
    emp_id: 5,
    sip_peer: "sip201",
    email: "john@example.com"
);
```

**Verification:**
- Returns integer (new extension ID)
- Extension exists in fa_asterisk_extensions

**Expected Result:** Valid ID returned

---

#### TC-DB002: Get Extensions List

**Test ID:** TC-DB002  
**Requirement:** EXT-005  
**Description:** Test get_extensions function

**Setup:**
- Multiple extensions exist

**Execution:**
```php
$extensions = get_extensions();
$count = db_num_rows($extensions);
```

**Verification:**
- Returns result set with all extensions
- Extensions have employee names joined

**Expected Result:** All extensions retrieved

---

#### TC-DB003: Get Extension for Employee

**Test ID:** TC-DB003  
**Requirement:** EXT-003  
**Description:** Test get_extension_for_employee function

**Setup:**
- Extension 201 linked to employee 5

**Execution:**
```php
$ext = get_extension_for_employee(5);
```

**Verification:**
- Returns extension array
- Contains extension number

**Expected Result:** Extension 201 returned

---

#### TC-DB004: Update Extension

**Test ID:** TC-DB004  
**Requirement:** EXT-006  
**Description:** Test update_extension function

**Setup:**
- Extension exists with ID 5

**Execution:**
```php
$result = update_extension(
    id: 5,
    emp_id: 8,
    email: "new@example.com",
    forward: "5551234"
);
```

**Verification:**
- Returns true on success
- Database record updated

**Expected Result:** true

---

#### TC-DB005: Record Call

**Test ID:** TC-DB005  
**Requirement:** CALL-001  
**Description:** Test record_call function

**Setup:**
- Database connection available

**Execution:**
```php
$call_id = record_call([
    'caller_number' => '+15551234',
    'called_number' => '5552000',
    'extension' => '201',
    'call_type' => 'inbound',
    'start_time' => '2026-05-13 10:30:00',
    'end_time' => '2026-05-13 10:35:00',
    'duration' => 300,
    'status' => 'completed'
]);
```

**Verification:**
- Returns call ID
- Record exists in fa_asterisk_calls

**Expected Result:** Valid ID returned

---

#### TC-DB006: Get Recent Calls for Extension

**Test ID:** TC-DB006  
**Description:** Test get_recent_calls_for_extension function

**Setup:**
- Extension 201 has 5 calls

**Execution:**
```php
$calls = get_recent_calls_for_extension('201', 10);
```

**Verification:**
- Returns result set
- Ordered by start_time descending
- Limit respected

**Expected Result:** 5 calls (or up to limit)

---

#### TC-DB007: Get Call History by Phone

**Test ID:** TC-DB007  
**Description:** Test get_call_history function

**Setup:**
- Phone +15551234 has 3 call records

**Execution:**
```php
$history = get_call_history('+15551234');
```

**Verification:**
- Returns all calls for this number
- Ordered by date

**Expected Result:** 3 calls returned

---

#### TC-DB008: Lookup Phone Number

**Test ID:** TC-DB008  
**Requirement:** POP-002  
**Description:** Test lookup_phone_number function

**Setup:**
- Contact exists with phone +15551234

**Execution:**
```php
$result = lookup_phone_number('+15551234');
```

**Verification:**
- Returns array with contacts, leads, customers keys
- Each contains matching records

**Expected Result:** Contact found in contacts array

---

### 2.2 Extension Management Tests

#### TC-EXT001: Create Extension Success

**Test ID:** TC-EXT001  
**Requirement:** EXT-001  
**Description:** Verify extension creation works

**Execution:**
```php
$id = create_extension("301", 10, "sip301", "user@example.com");
```

**Verification:**
- Extension in database
- All fields stored correctly

**Expected Result:** Extension created

---

#### TC-EXT002: Prevent Duplicate Extension

**Test ID:** TC-EXT002  
**Requirement:** EXT-002  
**Description:** Verify duplicate prevention

**Setup:**
- Extension "201" exists

**Execution:**
```php
// Try to create with same number
// (Should check unique constraint or prevent in code)
```

**Verification:**
- Error returned or create fails
- No duplicate record

**Expected Result:** Duplicate prevented

---

#### TC-EXT003: Employee Assignment

**Test ID:** TC-EXT003  
**Requirement:** EXT-003  
**Description:** Verify employee link works

**Execution:**
```php
$id = create_extension("401", 15, "sip401", "emp@example.com");
$ext = get_extension($id);
```

**Verification:**
- Extension has employee_id = 15
- Can retrieve by employee ID

**Expected Result:** Employee linked

---

### 2.3 Call Recording Tests

#### TC-CALL001: Record Inbound Call

**Test ID:** TC-CALL001  
**Requirement:** CALL-001  
**Description:** Verify inbound call recorded

**Execution:**
```php
$call_id = record_call([
    'caller_number' => '+15551111',
    'called_number' => '5552000',
    'extension' => '201',
    'call_type' => 'inbound',
    'start_time' => now(),
    'end_time' => later(),
    'duration' => 180,
    'status' => 'completed'
]);
```

**Verification:**
- Call in database
- All fields stored

**Expected Result:** Call logged

---

#### TC-CALL002: Record Outbound Call

**Test ID:** TC-CALL002  
**Requirement:** CALL-002  
**Description:** Verify outbound call recorded

**Execution:**
```php
$call_id = record_call([
    'caller_number' => '201',
    'called_number' => '+15559999',
    'extension' => '201',
    'call_type' => 'outbound',
    // ... other fields
]);
```

**Verification:**
- call_type = 'outbound'
- caller_number = extension

**Expected Result:** Outbound call logged

---

#### TC-CALL003: Record Missed Call

**Test ID:** TC-CALL003  
**Description:** Verify missed call recorded with status

**Execution:**
```php
$call_id = record_call([
    'caller_number' => '+15551111',
    'called_number' => '5552000',
    'extension' => '201',
    'call_type' => 'inbound',
    'start_time' => now(),
    'end_time' => null,
    'duration' => 0,
    'status' => 'missed'
]);
```

**Verification:**
- status = 'missed'
- duration = 0

**Expected Result:** Missed call logged

---

### 2.4 Caller Popup Tests

#### TC-POP001: Detect New Call

**Test ID:** TC-POP001  
**Requirement:** POP-001  
**Description:** Test call detection mechanism

**Setup:**
- Call with status 'ringing' in database

**Execution:**
```php
// Simulate poll
$ext = get_extension_for_employee($emp_id);
$calls = get_recent_calls_for_extension($ext['extension'], 1);
$call = db_fetch($calls);

if (in_array($call['status'], ['ringing', 'answered'])) {
    // New call detected
}
```

**Verification:**
- Returns call info
- Status is ringing or answered

**Expected Result:** Call detected

---

#### TC-POP002: Phone Number Lookup

**Test ID:** TC-POP002  
**Requirement:** POP-002  
**Description:** Verify phone search across tables

**Setup:**
- Contact with phone +15551234 exists
- Lead with same phone exists

**Execution:**
```php
$result = lookup_phone_number('+15551234');
```

**Verification:**
- contacts array contains match
- leads array contains match
- customers array checked

**Expected Result:** Both contact and lead found

---

#### TC-POP003: No Match Found

**Test ID:** TC-POP003  
**Description:** Verify empty result when no match

**Execution:**
```php
$result = lookup_phone_number('+15550000');
```

**Verification:**
- contacts empty
- leads empty
- customers empty

**Expected Result:** All arrays empty

---

### 2.5 Page Tests

#### TC-PAGE001: Admin Page Load

**Test ID:** TC-PAGE001  
**Description:** Verify admin page renders

**Execution:**
1. Navigate to /modules/FA_AsteriskPBX/pages/admin.php

**Verification:**
- Page loads without fatal error
- Extension list displays
- Settings form visible

**Expected Result:** Page renders correctly

---

#### TC-PAGE002: Popup Page Load

**Test ID:** TC-PAGE002  
**Description:** Verify popup page renders

**Execution:**
1. Navigate to /modules/FA_AsteriskPBX/pages/popup.php?caller=5551234

**Verification:**
- Page loads
- Caller number displayed
- Search results section visible

**Expected Result:** Popup renders

---

#### TC-PAGE003: Softphone Page Load

**Test ID:** TC-PAGE003  
**Description:** Verify softphone page renders

**Execution:**
1. Navigate to /modules/FA_AsteriskPBX/pages/softphone.php

**Verification:**
- Page loads
- Dialpad visible
- Status bar visible

**Expected Result:** Softphone renders

---

#### TC-PAGE004: Softphone JavaScript

**Test ID:** TC-PAGE004  
**Description:** Verify SIP.js initialization code present

**Execution:**
1. View page source of softphone.php

**Verification:**
- SIP.js CDN included
- initSIP() function defined
- Dialpad buttons have onclick handlers

**Expected Result:** JavaScript code present

---

### 2.6 Asterisk Configuration Tests

#### TC-AMI001: Save Asterisk Settings

**Test ID:** TC-AMI001  
**Requirement:** AMI-005  
**Description:** Test settings persistence

**Execution:**
```php
save_asterisk_settings(
    '192.168.1.100',
    '5038',
    'admin',
    'secret',
    '/etc/asterisk'
);
```

**Verification:**
- Settings saved to database/config
- Can be retrieved with getter functions

**Expected Result:** Settings persisted

---

#### TC-AMI002: Retrieve Asterisk Settings

**Test ID:** TC-AMI002  
**Description:** Test settings retrieval

**Execution:**
```php
$host = get_asterisk_host();
$port = get_asterisk_port();
```

**Verification:**
- Returns configured values
- Defaults used if not set

**Expected Result:** Correct values returned

---

## 3. Test Data Matrix

| Test ID | Function | Input | Expected |
|---------|----------|-------|----------|
| TC-DB001 | create_extension | valid data | ID returned |
| TC-DB002 | get_extensions | no filter | all extensions |
| TC-DB005 | record_call | inbound data | ID returned |
| TC-DB006 | get_recent_calls | ext, limit 10 | calls returned |
| TC-DB008 | lookup_phone | valid number | contacts found |
| TC-POP003 | lookup_phone | unknown number | empty result |
| TC-AMI001 | save_asterisk_settings | all params | settings saved |

---

## 4. Mock Strategy

### 4.1 Database Mock

```php
class MockDBResult {
    public $data = [];
    public $position = 0;
    
    public function fetch() {
        return $this->data[$this->position++] ?? null;
    }
}

function db_query($sql) {
    return new MockDBResult();
}
```

### 4.2 FA Globals Mock

```php
$GLOBALS['db'] = new MockDB();
$_SESSION['wa_user'] = new MockUser(['employee_id' => 5]);
```

---

## 5. Pass Criteria

| Criterion | Target |
|-----------|--------|
| All test cases pass | 100% |
| DB function coverage | >= 90% |
| Extension CRUD coverage | 100% |
| Call recording coverage | 100% |
| No regressions | 0 failures |

---

## 6. Test Execution

### 6.1 Unit Tests

Integration testing required due to FA dependencies. Manual test plan documented in UAT section.

### 6.2 Manual Verification Required

- WebRTC connection
- Call popup real-time behavior
- Asterisk AMI integration

---

*Document Version: 1.0*  
*Last Updated: May 2026*
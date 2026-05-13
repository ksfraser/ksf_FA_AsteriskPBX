# Functional Requirements - Asterisk PBX Integration (ksf_FA_AsteriskPBX)

**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  
**Date:** May 2026  

---

## 1. Introduction

This document specifies the functional requirements for the Asterisk PBX Integration module. Requirements are categorized by feature area and traced to test cases.

---

## 2. Extension Management Requirements

### 2.1 Extension CRUD

| ID | Requirement | Priority |
|----|-------------|----------|
| **EXT-001** | System shall allow creating extensions | MUST |
| **EXT-002** | Extension number must be unique | MUST |
| **EXT-003** | Extension can be assigned to FA employee | MUST |
| **EXT-004** | Extension can be linked to SIP peer | SHOULD |
| **EXT-005** | System shall list all extensions | MUST |
| **EXT-006** | Extensions can be edited | MUST |
| **EXT-007** | Extensions can be deactivated | SHOULD |
| **EXT-008** | Extension changes sync to Asterisk | SHOULD |

#### EXT-001: Create Extension

**Description:** Add new extension to system

**Required Fields:**
- extension (unique phone number)
- employee_id (optional, links to FA user)
- sip_peer (optional, Asterisk SIP peer name)
- email (optional, notification email)

**Acceptance Criteria:**
- Extension saved to fa_asterisk_extensions
- Extension number validated as unique
- Success message displayed

---

#### EXT-002: Extension Uniqueness

**Description:** Prevent duplicate extension numbers

**Acceptance Criteria:**
- Creating extension with existing number fails
- Clear error message shown
- No duplicate records created

---

#### EXT-003: Employee Assignment

**Description:** Link extension to FA employee

**Acceptance Criteria:**
- Employee dropdown lists all FA employees
- Selected employee ID stored with extension
- Extension retrievable by employee ID

---

#### EXT-008: Sync to Asterisk

**Description:** Configuration changes pushed to Asterisk

**Acceptance Criteria:**
- When extension created/updated, Asterisk config updated
- sip.conf updated with peer information
- Changes apply on Asterisk reload

---

## 3. Call History Requirements

### 3.1 Call Recording

| ID | Requirement | Priority |
|----|-------------|----------|
| **CALL-001** | System shall record all inbound calls | MUST |
| **CALL-002** | System shall record all outbound calls | MUST |
| **CALL-003** | Call records include caller/called numbers | MUST |
| **CALL-004** | Call records include extension involved | MUST |
| **CALL-005** | Call records include start/end times | MUST |
| **CALL-006** | Call records include duration | MUST |
| **CALL-007** | Call records include status | MUST |
| **CALL-008** | Calls can be linked to customer (debtor_no) | SHOULD |
| **CALL-009** | Calls can be linked to contact (contact_id) | SHOULD |

#### CALL-001: Record Inbound Call

**Description:** Log incoming call details

**Call Record Fields:**
- caller_number: Caller's ANI
- called_number: DID called
- extension: Extension that received call
- call_type: 'inbound'
- start_time: Call start timestamp
- end_time: Call end timestamp
- duration: Call duration in seconds
- status: completed, missed, abandoned

**Acceptance Criteria:**
- Call saved to fa_asterisk_calls
- All fields populated from Asterisk events
- Timestamp accurate to second

---

#### CALL-002: Record Outbound Call

**Description:** Log outgoing call details

**Call Record Fields:**
- caller_number: Our extension
- called_number: Dialed number
- extension: Extension used to call
- call_type: 'outbound'
- Other fields same as inbound

**Acceptance Criteria:**
- Outbound calls logged when made via softphone
- Dialed number captured

---

### 3.2 Call History Display

| ID | Requirement | Priority |
|----|-------------|----------|
| **HIST-001** | System shall list call history | MUST |
| **HIST-002** | History filterable by date range | SHOULD |
| **HIST-003** | History filterable by extension | SHOULD |
| **HIST-004** | History filterable by call type | SHOULD |
| **HIST-005** | User can view own extension history only | SHOULD |

#### HIST-001: Display Call History

**Description:** Show call records in table format

**Display Columns:**
- Date/Time
- Direction (inbound/outbound)
- From/To Number
- Extension
- Duration
- Status
- Customer Link (if any)

**Acceptance Criteria:**
- All calls displayed in chronological order
- Pagination for large datasets
- Quick filters available

---

## 4. Caller Popup Requirements

### 4.1 Phone Number Lookup

| ID | Requirement | Priority |
|----|-------------|----------|
| **POP-001** | System shall detect incoming calls | MUST |
| **POP-002** | System shall search for matching contacts | MUST |
| **POP-003** | Search includes CRM Contacts | MUST |
| **POP-004** | Search includes CRM Leads | MUST |
| **POP-005** | Search includes Customer Debtors | MUST |
| **POP-006** | Matching contact details displayed | MUST |
| **POP-007** | Call history for number displayed | SHOULD |

#### POP-001: Detect Incoming Call

**Description:** Real-time detection of incoming call

**Mechanism:**
- AJAX polling every 3 seconds
- Checks for new ringing/answered calls
- Returns caller number

**Acceptance Criteria:**
- Popup appears within 5 seconds of call
- Caller number correctly extracted
- No false positives (detecting wrong calls)

---

#### POP-002: Phone Number Search

**Description:** Search across multiple tables for phone match

**Search Order:**
1. CRM Contacts (phone fields)
2. CRM Leads (phone fields)
3. Customer Debtors (contact phone)

**Acceptance Criteria:**
- Exact match on full number
- Partial match (last 10 digits) for long numbers
- Multiple matches possible (show all)

---

#### POP-005: Create Lead from Popup

**Description:** Create new CRM lead from unknown caller

**Acceptance Criteria:**
- "Create New Lead" button available
- Quick form with phone pre-filled
- Lead saved with caller number
- Link call to new lead

---

## 5. Voicemail Requirements

### 5.1 Voicemail Management

| ID | Requirement | Priority |
|----|-------------|----------|
| **VM-001** | System shall list voicemails per extension | MUST |
| **VM-002** | Voicemail includes caller number | MUST |
| **VM-003** | Voicemail includes timestamp | MUST |
| **VM-004** | Voicemail includes duration | MUST |
| **VM-005** | Voicemail can be marked as read | SHOULD |
| **VM-006** | Unread count displayed | SHOULD |

#### VM-001: List Voicemails

**Description:** Display voicemail messages

**Display Columns:**
- Date/Time
- Caller Number
- Duration
- Status (read/unread)

**Acceptance Criteria:**
- All voicemails for user's extension shown
- Unread highlighted
- Ordered by date (newest first)

---

## 6. WebRTC Softphone Requirements

### 6.1 Softphone Functionality

| ID | Requirement | Priority |
|----|-------------|----------|
| **SIP-001** | System shall provide browser-based phone | MUST |
| **SIP-002** | Softphone connects to Asterisk via WebRTC | MUST |
| **SIP-003** | User can make calls from softphone | MUST |
| **SIP-004** | User can receive calls via softphone | MUST |
| **SIP-005** | Call status displayed | MUST |
| **SIP-006** | Dial pad available for number entry | MUST |
| **SIP-007** | User can end calls (hang up) | MUST |
| **SIP-008** | DTMF tones can be sent during call | SHOULD |
| **SIP-009** | Recent calls displayed | SHOULD |

#### SIP-001: Browser-Based Phone

**Description:** SIP.js integration for WebRTC

**Acceptance Criteria:**
- Softphone page loads in supported browsers
- SIP.js library loads correctly
- WebSocket connection established
- Extension registered with Asterisk

**Technical Requirements:**
- HTTPS required (WebRTC constraint)
- Valid TLS certificate
- WebSocket port open (default 8089)

---

#### SIP-003: Make Calls

**Description:** Initiate outbound call from softphone

**Steps:**
1. User enters number in dialpad
2. User clicks "Call" or "Dial"
3. SIP.js sends INVITE to Asterisk
4. Asterisk routes call
5. Call connected when answered

**Acceptance Criteria:**
- Number entered correctly
- Call initiated within 2 seconds
- Call status updated
- Call logged to history

---

#### SIP-004: Receive Calls

**Description:** Accept incoming call in browser

**Steps:**
1. Asterisk sends INVITE to softphone
2. SIP.js triggers onInvite callback
3. Incoming call dialog displayed
4. User accepts or rejects
5. Call connected or rejected

**Acceptance Criteria:**
- Incoming call alert appears immediately
- Caller ID displayed
- Accept/Reject buttons work
- Call connected status updated

---

## 7. Asterisk Configuration Requirements

### 7.1 Connection Settings

| ID | Requirement | Priority |
|----|-------------|----------|
| **AMI-001** | System shall store Asterisk host | MUST |
| **AMI-002** | System shall store AMI port | MUST |
| **AMI-003** | System shall store AMI username | MUST |
| **AMI-004** | System shall store AMI password | MUST |
| **AMI-005** | Settings configurable via admin page | MUST |

#### AMI-001: Asterisk Settings Storage

**Description:** Persistent storage of Asterisk connection settings

**Settings:**
- asterisk_host: Server hostname/IP
- asterisk_port: AMI port (default: 5038)
- asterisk_user: Manager username
- asterisk_pass: Manager password (should be encrypted)
- asterisk_config_dir: Path to Asterisk config (default: /etc/asterisk)

**Acceptance Criteria:**
- Settings saved to FA database
- Settings persist across sessions
- Settings validated before save

---

## 8. Security Requirements

### 8.1 Access Control

| Security Area | Permission Level |
|---------------|-------------------|
| SS_ASTERISK | Module section identifier |
| SA_ASTERISKADMIN | Administer extensions and settings |
| SA_ASTERISKVIEW | View call history and popups |
| SA_ASTERISKMANAGE | Manage calls and voicemails |

### 8.2 Page Security

| Page | Required Permission |
|------|-------------------|
| admin.php | SA_ASTERISKADMIN |
| popup.php | SA_ASTERISKVIEW |
| softphone.php | SA_ASTERISKVIEW |

---

## 9. Data Requirements

### 9.1 Tables Required

| Table | Purpose |
|-------|---------|
| fa_asterisk_extensions | Extension mapping |
| fa_asterisk_calls | Call history |
| fa_asterisk_voicemail | Voicemail messages |

### 9.2 Index Requirements

| Table | Indexes |
|-------|---------|
| fa_asterisk_extensions | extension (unique), employee_id |
| fa_asterisk_calls | extension, start_time, debtor_no |
| fa_asterisk_voicemail | extension, is_read |

---

## 10. Requirements Traceability Matrix

| Requirement ID | Use Case | Test Case |
|--------------|----------|-----------|
| EXT-001 | UC-001 | TC-EXT001 |
| EXT-002 | UC-002 | TC-EXT002 |
| EXT-003 | UC-001 | TC-EXT003 |
| CALL-001 | UC-003 | TC-CALL001 |
| CALL-002 | UC-004 | TC-CALL002 |
| HIST-001 | UC-005 | TC-HIST001 |
| POP-001 | UC-006 | TC-POP001 |
| POP-002 | UC-006 | TC-POP002 |
| VM-001 | UC-007 | TC-VM001 |
| SIP-001 | UC-008 | TC-SIP001 |
| SIP-003 | UC-008 | TC-SIP003 |
| SIP-004 | UC-009 | TC-SIP004 |
| AMI-001 | UC-010 | TC-AMI001 |

---

*Document Version: 1.0*  
*Last Updated: May 2026*
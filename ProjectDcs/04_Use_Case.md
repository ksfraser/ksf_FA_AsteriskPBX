# Use Case Specification - Asterisk PBX Integration (ksf_FA_AsteriskPBX)

**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  
**Date:** May 2026  

---

## 1. Use Case Overview

| ID | Use Case | Actor | Priority |
|----|----------|-------|----------|
| UC-001 | Create Extension | Admin | HIGH |
| UC-002 | Prevent Duplicate Extensions | System | HIGH |
| UC-003 | Record Inbound Call | System | HIGH |
| UC-004 | Record Outbound Call | System | HIGH |
| UC-005 | View Call History | Agent, Manager | HIGH |
| UC-006 | Display Caller Popup | Agent | HIGH |
| UC-007 | Manage Voicemail | Agent | HIGH |
| UC-008 | Make WebRTC Call | Agent | HIGH |
| UC-009 | Receive WebRTC Call | Agent | HIGH |
| UC-010 | Configure Asterisk Settings | Admin | HIGH |

---

## 2. Use Case Definitions

### UC-001: Create Extension

**Actor:** Admin  
**Precondition:** User has SA_ASTERISKADMIN permission  
**Trigger:** Need to add new extension

**Steps:**
1. Navigate to Extension Mapping page
2. Click "Add New Extension"
3. Enter extension number (unique)
4. Select assigned employee (optional)
5. Enter SIP peer name (optional)
6. Enter email address (optional)
7. Enter call forward number (optional)
8. Click Save
9. System syncs configuration to Asterisk

**Postcondition:** Extension created and synced to Asterisk

**Success Scenario:**
```
Input:
  - Extension: 201
  - Employee: John Smith (id: 5)
  - SIP Peer: sip201
  - Email: john@company.com

Result: Extension 201 created, linked to employee 5
Asterisk updated with SIP peer configuration
```

---

### UC-002: Prevent Duplicate Extensions

**Actor:** System  
**Precondition:** Extension already exists  
**Trigger:** Attempt to create duplicate extension

**Steps:**
1. Admin enters extension number "201"
2. System checks for existing extension
3. If exists, return error
4. Admin must choose different number

**Postcondition:** No duplicate extension created

**Error Scenario:**
```
Input: Extension "201" (already exists)
Result: Error message "Extension 201 already exists"
Extension not created
```

---

### UC-003: Record Inbound Call

**Actor:** System (automated)  
**Precondition:** Call reaches Asterisk  
**Trigger:** Incoming call answered by extension

**Steps:**
1. Asterisk detects incoming call
2. Call routed to extension
3. Extension answers call
4. System captures call details:
   - Caller number (ANI)
   - Called number (DID)
   - Extension
   - Start time
5. Call ends
6. System captures:
   - End time
   - Duration
   - Status (completed/missed)
7. Call record saved to database

**Postcondition:** Call logged with all details

**Success Scenario:**
```
Call Details:
  - Caller: +1-555-1234
  - Called: +1-555-2000 (main line)
  - Extension: 201 (answered)
  - Start: 2026-05-13 10:30:00
  - End: 2026-05-13 10:35:00
  - Duration: 300 seconds
  - Status: completed

Result: Call record created in fa_asterisk_calls
```

---

### UC-004: Record Outbound Call

**Actor:** System (automated)  
**Precondition:** User initiates call from softphone  
**Trigger:** Outgoing call connected

**Steps:**
1. User enters number in softphone
2. User clicks "Call"
3. Softphone initiates SIP call
4. Asterisk routes call
5. Call connected
6. System records:
   - Extension (our line)
   - Called number
   - Start time
7. Call ends
8. System records:
   - End time
   - Duration
   - Status

**Postcondition:** Outbound call logged

**Success Scenario:**
```
Call Details:
  - Extension: 201 (our softphone)
  - Called: +1-555-9999
  - Start: 2026-05-13 11:00:00
  - End: 2026-05-13 11:05:00
  - Duration: 300 seconds

Result: Outbound call record created
```

---

### UC-005: View Call History

**Actor:** Agent, Manager  
**Precondition:** User has SA_ASTERISKVIEW permission  
**Trigger:** Need to review call activity

**Steps:**
1. Navigate to Call History page
2. View default list (all calls, recent first)
3. Optionally filter:
   a. Select extension
   b. Select date range
   c. Select call type (inbound/outbound)
4. Browse results
5. Click call row for details (future)

**Postcondition:** Call history displayed

**Display:**
| Date/Time | Direction | From | To | Extension | Duration | Status |
|-----------|-----------|------|----|-----------|----------|--------|
| 05/13 10:30 | Inbound | +1-555-1234 | Main | 201 | 5:00 | Completed |

---

### UC-006: Display Caller Popup

**Actor:** Agent  
**Precondition:** Incoming call detected, user logged in  
**Trigger:** Incoming call for user's extension

**Steps:**
1. Popup page polls for new calls every 3 seconds
2. New call detected with caller number
3. System searches for phone number match:
   a. Search CRM Contacts
   b. Search CRM Leads
   c. Search Customer Debtors
4. If match found:
   a. Display customer/lead details
   b. Show "View Contact" button
5. If no match:
   a. Display "No contact found"
   b. Show "Create New Lead" button
6. Display call history for this number
7. Agent sees context before answering

**Postcondition:** Popup shows customer info or create form

**Success Scenario (Contact Found):**
```
Incoming Call: +1-555-1234

Search Results:
- Customer: ABC Corp (Debtor #123)
- Contact: John Smith (Sales Rep)
- Previous Calls: 5 (last: 2 days ago)

Display:
- Customer Name: ABC Corp
- Contact: John Smith
- Phone: +1-555-1234
- Last Contact: 2 days ago

Actions:
- [View Customer] [View Lead] [Create New Lead]
```

**Success Scenario (No Match):**
```
Incoming Call: +1-555-9999

Search Results: No matches found

Display:
- Caller: +1-555-9999
- No contact found

Actions:
- [Create New Lead from This Number]
```

---

### UC-007: Manage Voicemail

**Actor:** Agent  
**Precondition:** Voicemail exists for user's extension  
**Trigger:** Agent needs to check voicemails

**Steps:**
1. Navigate to Voicemail page
2. View list of voicemails
3. Unread voicemails highlighted
4. Click voicemail to:
   a. Play audio (media player)
   b. Mark as read
   c. Delete (optional)
5. Voicemail marked as read

**Postcondition:** Voicemail reviewed and marked

**Display:**
| Date | Caller | Duration | Status |
|------|--------|----------|--------|
| 05/13 09:00 | +1-555-1234 | 0:45 | Unread |
| 05/12 14:30 | +1-555-5678 | 1:20 | Read |

---

### UC-008: Make WebRTC Call

**Actor:** Agent  
**Precondition:** User logged into softphone  
**Trigger:** Need to call customer

**Steps:**
1. Navigate to WebRTC Softphone page
2. Verify connection status: "Connected"
3. Enter customer phone number in dialpad
4. Click "Call" button
5. Call initiated to Asterisk
6. Asterisk routes call to destination
7. Call connects
8. Call timer starts
9. User converses
10. Click "Hang Up"
11. Call duration recorded

**Postcondition:** Call made and logged

**Success Scenario:**
```
1. User enters: 555-9999
2. Clicks "Call"
3. Status: "Calling 555-9999..."
4. Call connects
5. Status: "In Call: 555-9999"
6. Timer: 00:05:32
7. Click "Hang Up"
8. Status: "Connected"
9. Call logged: Duration 5:32
```

---

### UC-009: Receive WebRTC Call

**Actor:** Agent  
**Precondition:** Softphone connected, incoming call  
**Trigger:** Call arrives for user's extension

**Steps:**
1. Asterisk sends INVITE to softphone
2. Popup appears: "Incoming call from: +1-555-1234"
3. User clicks "Accept" or "Reject"
4. If Accept:
   a. Call connected
   b. Status shows "In Call"
   c. Timer starts
   d. User converses
5. If Reject:
   a. Call rejected
   b. Status returns to "Connected"

**Postcondition:** Incoming call handled

**Alert Dialog:**
```
Incoming Call

From: +1-555-1234

[Accept]  [Reject]
```

---

### UC-010: Configure Asterisk Settings

**Actor:** Admin  
**Precondition:** User has SA_ASTERISKADMIN permission  
**Trigger:** Initial setup or configuration change

**Steps:**
1. Navigate to Extension Mapping page
2. Scroll to "Asterisk Settings" section
3. Enter Asterisk host/IP
4. Enter AMI port (default: 5038)
5. Enter AMI username
6. Enter AMI password
7. Enter config directory path
8. Click "Save Settings"
9. System validates connection
10. Settings saved

**Postcondition:** Asterisk connection configured

**Settings Form:**
- Asterisk Host: _______________
- AMI Port: ____ (default 5038)
- AMI Username: _______________
- AMI Password: ********
- Config Directory: ____________ (default /etc/asterisk)

---

## 3. Use Case Matrix

| Use Case | Actor | Trigger | Precondition | Postcondition |
|----------|-------|---------|--------------|---------------|
| UC-001 | Admin | Add extension | Admin permission | Extension created |
| UC-002 | System | Duplicate entry | Extension exists | Error shown |
| UC-003 | System | Incoming call | Asterisk active | Call logged |
| UC-004 | System | Outbound call | Softphone active | Call logged |
| UC-005 | Agent | Review history | View permission | History displayed |
| UC-006 | Agent | Incoming call | Popup active | Info shown |
| UC-007 | Agent | Check voicemails | Voicemail exists | VM reviewed |
| UC-008 | Agent | Make call | Softphone connected | Call made |
| UC-009 | Agent | Receive call | Softphone active | Call answered |
| UC-010 | Admin | Configure Asterisk | Admin permission | Settings saved |

---

## 4. Error Handling

### EH-001: Extension Already Exists

**Trigger:** Create extension with existing number  
**Response:** Error message displayed  
**Action:** User enters different number

### EH-002: Asterisk Connection Failed

**Trigger:** Cannot connect to Asterisk AMI  
**Response:** Error message on admin page  
**Action:** Check host, port, credentials

### EH-003: WebRTC Connection Failed

**Trigger:** SIP.js cannot connect to Asterisk  
**Response:** "Connection Failed" status  
**Action:** Check WebSocket URL, SSL certificate

### EH-004: No Extension Assigned

**Trigger:** User has no extension mapped  
**Response:** Softphone shows "No extension" message  
**Action:** Admin assigns extension to user

---

## 5. Alternative Flows

### AF-001: Multiple Contacts Match

**Trigger:** Phone number matches multiple records  
**Flow:**
1. Search returns multiple contacts
2. Display all matches in popup
3. User selects correct contact
4. Context updated

### AF-002: Call Forward Active

**Trigger:** Extension has call forward configured  
**Flow:**
1. Call arrives for extension
2. Immediately forwarded to forward number
3. Call logged with original extension
4. Forward number noted in record

### AF-003: Missed Call

**Trigger:** Call not answered  
**Flow:**
1. Call arrives
2. Rings but not answered
3. Voicemail triggered or call abandoned
4. Status = "missed" or "abandoned"
5. Call logged with 0 duration

---

*Document Version: 1.0*  
*Last Updated: May 2026*
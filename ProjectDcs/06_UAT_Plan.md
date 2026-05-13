# UAT Plan - Asterisk PBX Integration (ksf_FA_AsteriskPBX)

**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  
**Date:** May 2026  

---

## 1. UAT Objectives

### 1.1 Primary Objectives

1. **Verify Module Activation**: Clean installation without errors
2. **Validate Extension Management**: CRUD operations work correctly
3. **Confirm Call Recording**: All calls logged with correct details
4. **Test Caller Popup**: Customer info displays on incoming calls
5. **Verify Voicemail Access**: Messages retrievable
6. **Test WebRTC Softphone**: Make and receive calls from browser
7. **Validate Asterisk Integration**: Settings and sync work

### 1.2 Success Criteria

| Metric | Target |
|--------|--------|
| Module activates cleanly | 100% |
| Extension CRUD functional | 100% |
| Call history records correctly | 100% match |
| Caller popup displays | Within 5 seconds |
| Voicemail listing works | 100% |
| WebRTC calls work | Make/receive functional |
| Security permissions enforced | 100% |

---

## 2. UAT Scenarios

### 2.1 Module Activation Scenarios

#### UAT-ACT001: Install Module

**Scenario:** Fresh installation of FA_AsteriskPBX module  
**Steps:**
1. Access FrontAccounting Extensions page
2. Select ksf_FA_AsteriskPBX module
3. Click Install/Activate
4. Verify module appears in Modules list
5. Verify menu items appear under CRM app
6. Verify database tables created

**Expected Result:**
- Module activated without errors
- 3 tables created: fa_asterisk_extensions, fa_asterisk_calls, fa_asterisk_voicemail
- Menu items visible: Extension Mapping, Call History, Voicemail

**Pass Criteria:**
- [ ] Activation completes without error
- [ ] Tables exist in database
- [ ] Menu items appear in CRM section
- [ ] Security areas registered

---

#### UAT-ACT002: Configure Asterisk Settings

**Scenario:** Set up Asterisk connection  
**Steps:**
1. Navigate to Extension Mapping page
2. Scroll to Asterisk Settings section
3. Enter Asterisk host IP
4. Enter AMI port (default 5038)
5. Enter AMI username
6. Enter AMI password
7. Enter config directory path
8. Click Save Settings
9. Verify success message

**Expected Result:** Settings saved and testable

**Pass Criteria:**
- [ ] Settings form visible
- [ ] Validation accepts valid inputs
- [ ] Settings saved successfully
- [ ] Settings persist after reload

---

### 2.2 Extension Management Scenarios

#### UAT-EXT001: Create Extension

**Scenario:** Add new extension  
**Steps:**
1. Navigate to Extension Mapping
2. Click "Add New Extension"
3. Enter extension number: 201
4. Select employee: John Smith
5. Enter SIP Peer: sip201
6. Enter email: john@company.com
7. Click Save

**Expected Result:** Extension created and listed

**Pass Criteria:**
- [ ] Extension saved to database
- [ ] Extension appears in list
- [ ] Employee linked
- [ ] Success message displayed

---

#### UAT-EXT002: Edit Extension

**Scenario:** Modify existing extension  
**Steps:**
1. Click Edit on extension 201
2. Change assigned employee to Jane Doe
3. Update email address
4. Click Save

**Expected Result:** Extension updated

**Pass Criteria:**
- [ ] Changes saved
- [ ] List reflects updates
- [ ] Previous values not retained

---

#### UAT-EXT003: Prevent Duplicate Extension

**Scenario:** Verify unique constraint  
**Steps:**
1. Extension 201 exists
2. Try to create extension 201 again
3. Verify error message

**Expected Result:** Error displayed, no duplicate

**Pass Criteria:**
- [ ] Error message shown
- [ ] Duplicate not created
- [ ] Original extension unchanged

---

#### UAT-EXT004: Deactivate Extension

**Scenario:** Disable extension without deletion  
**Steps:**
1. Edit extension 201
2. Toggle active status to inactive
3. Save

**Expected Result:** Extension marked inactive

**Pass Criteria:**
- [ ] Status updated
- [ ] Extension still in list
- [ ] Marked as inactive in display

---

### 2.3 Call Recording Scenarios

#### UAT-CALL001: Record Inbound Call

**Scenario:** Verify inbound call logged  
**Steps:**
1. Simulate incoming call to extension 201
2. Call answered
3. Call duration: 3 minutes
4. Call ended
5. Access Call History
6. Verify call record exists

**Expected Result:** Call logged with all details

**Pass Criteria:**
- [ ] Call appears in history
- [ ] Direction: inbound
- [ ] Caller number correct
- [ ] Duration accurate
- [ ] Extension correct

---

#### UAT-CALL002: Record Outbound Call

**Scenario:** Verify outbound call logged  
**Steps:**
1. Using softphone, dial external number
2. Call connected
3. Call duration: 2 minutes
4. End call
5. Access Call History
6. Verify call record

**Expected Result:** Outbound call logged

**Pass Criteria:**
- [ ] Call appears in history
- [ ] Direction: outbound
- [ ] Called number correct
- [ ] Duration accurate

---

#### UAT-CALL003: Record Missed Call

**Scenario:** Verify missed call logged  
**Steps:**
1. Simulate incoming call (let ring, no answer)
2. Caller hangs up or voicemail
3. Access Call History
4. Verify missed call record

**Expected Result:** Missed call logged with status

**Pass Criteria:**
- [ ] Call appears in history
- [ ] Status: missed or abandoned
- [ ] Duration: 0 or minimal

---

#### UAT-CALL004: Call Linked to Customer

**Scenario:** Verify call can be associated with debtor  
**Steps:**
1. Call from known customer number
2. System identifies customer
3. Call record shows debtor_no link
4. View call details show customer name

**Expected Result:** Call associated with customer

**Pass Criteria:**
- [ ] debtor_no populated
- [ ] Customer name displayed
- [ ] Link clickable to customer page

---

### 2.4 Caller Popup Scenarios

#### UAT-POP001: Display Popup for Known Customer

**Scenario:** Show customer info on incoming call  
**Steps:**
1. Configure popup page auto-load
2. Simulate incoming call from known phone number
3. Popup auto-displays
4. Verify customer details shown
5. Verify previous call history displayed

**Expected Result:** Customer info and history visible

**Pass Criteria:**
- [ ] Popup appears within 5 seconds
- [ ] Customer name displayed
- [ ] Contact details shown
- [ ] Previous call history visible
- [ ] "View Customer" button works

---

#### UAT-POP002: Display Popup for Unknown Caller

**Scenario:** Handle unknown phone number  
**Steps:**
1. Simulate call from new/unknown number
2. Popup displays with "No contact found"
3. Verify "Create New Lead" button visible

**Expected Result:** Unknown caller handled gracefully

**Pass Criteria:**
- [ ] "No contact found" message shown
- [ ] "Create New Lead" button visible
- [ ] Click leads to lead creation form

---

#### UAT-POP003: Create Lead from Popup

**Scenario:** Quick lead creation from popup  
**Steps:**
1. Incoming call from unknown number
2. Click "Create New Lead"
3. Form appears with phone pre-filled
4. Enter name and company
5. Save lead

**Expected Result:** New lead created with phone

**Pass Criteria:**
- [ ] Form pre-fills phone number
- [ ] Lead saved with phone
- [ ] Lead linkable to call record

---

### 2.5 Voicemail Scenarios

#### UAT-VM001: View Voicemail List

**Scenario:** Access voicemails for extension  
**Steps:**
1. Navigate to Voicemail page
2. View list of voicemails
3. Verify unread highlighted
4. Verify read messages normal style

**Expected Result:** Voicemail list displayed

**Pass Criteria:**
- [ ] All voicemails for extension shown
- [ ] Unread highlighted differently
- [ ] Date, time, duration visible

---

#### UAT-VM002: Mark Voicemail as Read

**Scenario:** Update voicemail status  
**Steps:**
1. View voicemail list
2. Click unread voicemail
3. Play voicemail (if audio file)
4. Verify status changes to read

**Expected Result:** Voicemail marked as read

**Pass Criteria:**
- [ ] Status updates
- [ ] Visual indicator changes
- [ ] Unread count decreases

---

### 2.6 WebRTC Softphone Scenarios

#### UAT-SIP001: Softphone Page Load

**Scenario:** Verify softphone renders correctly  
**Steps:**
1. Navigate to WebRTC Softphone page
2. Verify dialpad visible
3. Verify status bar visible
4. Verify recent calls list visible

**Expected Result:** Softphone UI renders

**Pass Criteria:**
- [ ] Dialpad buttons present (0-9, *, #)
- [ ] Call/Hangup buttons visible
- [ ] Status: Disconnected (or Connected if auto-login)

---

#### UAT-SIP002: Connect to Asterisk

**Scenario:** Establish WebRTC connection  
**Steps:**
1. Ensure Asterisk settings configured
2. Click Connect (or auto-connect on page load)
3. Verify status changes to "Connected"
4. Verify extension registered

**Expected Result:** WebRTC connected to Asterisk

**Pass Criteria:**
- [ ] Status shows "Connected"
- [ ] No error messages
- [ ] Extension visible in Asterisk

---

#### UAT-SIP003: Make Outbound Call

**Scenario:** Initiate call from softphone  
**Steps:**
1. Enter phone number in dialpad or input
2. Click "Call" button
3. Verify status: "Calling..."
4. Wait for connection
5. Verify status: "In Call"
6. Converse
7. Click "Hang Up"

**Expected Result:** Call made and ended successfully

**Pass Criteria:**
- [ ] Call initiated
- [ ] Status updates correctly
- [ ] Call connects
- [ ] Hang up works
- [ ] Call logged in history

---

#### UAT-SIP004: Receive Inbound Call

**Scenario:** Accept incoming call in browser  
**Steps:**
1. Softphone connected
2. External caller dials extension
3. Incoming call dialog appears
4. Click "Accept"
5. Verify call connected
6. Verify status: "In Call"
7. End call

**Expected Result:** Incoming call handled

**Pass Criteria:**
- [ ] Alert popup appears
- [ ] Caller ID shown
- [ ] Accept/Reject buttons work
- [ ] Call connects on accept

---

#### UAT-SIP005: Reject Inbound Call

**Scenario:** Decline incoming call  
**Steps:**
1. Softphone connected
2. Incoming call arrives
3. Click "Reject"
4. Verify call rejected
5. Verify status returns to "Connected"

**Expected Result:** Call rejected gracefully

**Pass Criteria:**
- [ ] Alert appears
- [ ] Reject button works
- [ ] Call not connected
- [ ] Status normal

---

### 2.7 Report Scenarios

#### UAT-REPT001: View Call History Report

**Scenario:** Generate call activity report  
**Steps:**
1. Navigate to Call History
2. View default list
3. Apply filter: Date range (today)
4. Apply filter: Extension (201)
5. View results

**Expected Result:** Filtered call list displayed

**Pass Criteria:**
- [ ] Filters work correctly
- [ ] Results match filter criteria
- [ ] Sorting by date works

---

## 3. UAT Execution Matrix

| Scenario | Tester | Date | Result | Sign-off |
|----------|--------|------|--------|----------|
| UAT-ACT001 | | | | |
| UAT-ACT002 | | | | |
| UAT-EXT001 | | | | |
| UAT-EXT002 | | | | |
| UAT-EXT003 | | | | |
| UAT-EXT004 | | | | |
| UAT-CALL001 | | | | |
| UAT-CALL002 | | | | |
| UAT-CALL003 | | | | |
| UAT-CALL004 | | | | |
| UAT-POP001 | | | | |
| UAT-POP002 | | | | |
| UAT-POP003 | | | | |
| UAT-VM001 | | | | |
| UAT-VM002 | | | | |
| UAT-SIP001 | | | | |
| UAT-SIP002 | | | | |
| UAT-SIP003 | | | | |
| UAT-SIP004 | | | | |
| UAT-SIP005 | | | | |
| UAT-REPT001 | | | | |

---

## 4. Sign-off Criteria

### 4.1 Prerequisites for Sign-off

- [ ] All 21 scenarios executed
- [ ] All scenarios pass (100% pass rate)
- [ ] No critical or high severity issues open
- [ ] Module functions correctly
- [ ] WebRTC working (make/receive calls)
- [ ] Security working as expected

### 4.2 Sign-off Declaration

| Role | Name | Date | Signature |
|------|------|------|-----------|
| UAT Lead | | | |
| Technical Lead | | | |
| Product Owner | | | |

---

## 5. Known Limitations

| Limitation | Impact | Workaround |
|------------|--------|------------|
| HTTPS required | WebRTC needs SSL | Use valid SSL cert |
| Asterisk required | Needs PBX system | Provide test Asterisk |
| Browser compatibility | Limited browser support | Use Chrome/Firefox |
| No call recording playback | Voicemail audio | Future enhancement |

---

## 6. Defect Severity Definitions

| Severity | Definition | Example |
|----------|------------|---------|
| Critical | System unusable | Module won't activate |
| High | Major feature broken | WebRTC won't connect |
| Medium | Minor feature affected | Popup delay > 10 sec |
| Low | Cosmetic issue | Text alignment |

---

*Document Version: 1.0*  
*Last Updated: May 2026*
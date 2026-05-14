# FA_AsteriskPBX - Test Plan

**Document ID:** TP-FASTPBX-001  
**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  

---

## 1. Test Scope

- WebRTC connection
- Call initiation
- Call reception
- Call termination
- Call history display
- Admin functions

## 2. Test Cases

### 2.1 Softphone Connection Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-001 | testSoftphone_PageLoads | valid user with extension | Page renders |
| TC-002 | testSoftphone_NoExtension | user without extension | Error message shown |
| TC-003 | testSoftphone_ConnectionStatus | after init | Status = "Connected" |
| TC-004 | testSoftphone_ConnectionFailed | invalid Asterisk host | Error displayed |

### 2.2 Outbound Call Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-010 | testMakeCall_Initiation | valid phone number | Status = "Calling" |
| TC-011 | testMakeCall_Connected | call accepted | Status = "In Call" |
| TC-012 | testMakeCall_Failed | invalid number | Error displayed |
| TC-013 | testHangup | active call | Call terminated |

### 2.3 Inbound Call Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-020 | testIncomingCall_Notification | INVITE received | Popup/alert shown |
| TC-021 | testIncomingCall_Accept | click Accept | Call connected |
| TC-022 | testIncomingCall_Reject | click Reject | Call declined |
| TC-023 | testIncomingCall_AutoEnd | remote disconnects | Status updated |

### 2.4 DTMF Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-030 | testDTMF_DialDigit | during call | Tone sent |

### 2.5 Call History Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-040 | testCallHistory_Display | with recent calls | Calls listed |
| TC-041 | testCallHistory_Columns | - | Time, from, to, status shown |

### 2.6 Admin Tests

| ID | Test | Test Data | Pass Criteria |
|---------|-----------|-----------|---------------|
| TC-050 | testAdmin_ListExtensions | with extensions | Extension list shown |
| TC-051 | testAdmin_AssignEmployee | valid assignment | Extension updated |
| TC-052 | testAdmin_ConfigureHost | valid host | Config saved |
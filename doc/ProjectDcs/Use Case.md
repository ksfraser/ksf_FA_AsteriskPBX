# FA_AsteriskPBX - Use Cases

**Document ID:** UC-FASTPBX-001  
**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  

---

## 1. Use Case Overview

### UC-001: Make Outbound Call

**Description:** Employee makes outbound call using softphone.

**Primary Flow:**
1. Employee navigates to softphone page
2. System validates extension assignment
3. System auto-connects to Asterisk
4. Employee clicks Dial Number
5. Employee enters phone number
6. System initiates SIP call
7. Status shows "Calling"
8. Remote party answers
9. Status shows "In Call"
10. Employee clicks Hang Up
11. System terminates call

**Preconditions:** User has extension assigned, SA_ASTERISKVIEW permission.

---

### UC-002: Receive Incoming Call

**Description:** Employee receives incoming call notification.

**Primary Flow:**
1. Asterisk routes call to employee extension
2. System sends SIP INVITE to softphone
3. Browser displays notification with caller ID
4. Employee clicks Accept
5. System accepts invitation
6. Audio streams begin
7. Call ends when remote party disconnects
8. System updates call log

**Alternative Flow - Reject:**
1. Employee clicks Reject
2. System rejects SIP invitation
3. Call ends

**Preconditions:** User has extension assigned and is logged in.

---

### UC-003: View Call History

**Description:** Employee reviews recent calls.

**Primary Flow:**
1. Employee navigates to softphone page
2. System displays call history section
3. System shows recent 10 calls
4. Employee reviews call details

**Preconditions:** User has SA_ASTERISKVIEW permission.

---

### UC-004: Admin Configure Extension

**Description:** Admin assigns extension to employee.

**Primary Flow:**
1. Admin navigates to admin page
2. Admin selects extension
3. Admin searches for employee
4. Admin assigns employee to extension
5. System saves assignment

**Preconditions:** User has SA_ASTERISKADMIN permission.

## 2. Actors

| Actor | Role |
|-------|------|
| Employee | Make/receive calls, view history |
| Admin | Manage extensions and configuration |
| Asterisk PBX | Telephony backend |
| Remote Caller | External party calling in |
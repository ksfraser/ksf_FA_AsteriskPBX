# FA_AsteriskPBX - Business Requirements

**Document ID:** BR-FASTPBX-001  
**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  

---

## 1. Overview

FA_AsteriskPBX integrates Asterisk VoIP PBX functionality with FrontAccounting ERP. It provides browser-based WebRTC softphone, call popup notifications, and admin management of extensions within the FA system.

## 2. Purpose

The module enables organizations to leverage Asterisk telephony features directly within FrontAccounting, allowing employees to make and receive calls, view call history, and manage extensions without requiring separate IP phone hardware.

## 3. Scope

### 3.1 Core Features

- **WebRTC Softphone**
  - Browser-based SIP client using SIP.js
  - Real-time calling from any modern browser
  - Dialpad interface
  - Incoming call handling with accept/reject
  - Call history display
  - Automatic connection on page load

- **Extension Management**
  - Employee-to-extension mapping
  - SIP peer configuration
  - Extension status tracking

- **Admin Interface**
  - Extension administration
  - Call queue management
  - System configuration

- **Call Popup**
  - Incoming call notifications
  - Customer record popups (integrates with CRM)
  - Click-to-dial from records

- **Call Logging**
  - Recent calls history
  - Call duration tracking
  - Call status (answered, missed, failed)

### 3.2 Out of Scope

- IVR/Auto-attendant configuration
- Call recording storage
- Conference calling
- Voicemail
- Queue statistics reports

## 4. Integration Dependencies

| Module | Dependency Type | Purpose |
|--------|-----------------|---------|
| FrontAccounting Core | Required | UI, session, permissions |
| Asterisk PBX | Required | Telephony backend |
| ksf_FA_CRM | Optional | Caller ID lookup |
| ksf_FA_AsteriskPBX | Required | Backend includes |

## 5. User Roles

| Role | Permissions |
|------|-------------|
| Employee | SA_ASTERISKVIEW - Use softphone |
| Admin | SA_ASTERISKADMIN - Manage extensions |
| Manager | View call reports |

## 6. Acceptance Criteria

- [ ] Softphone connects to Asterisk via WebSocket
- [ ] Users can make outbound calls
- [ ] Incoming calls display popup notification
- [ ] Call history displays recent calls
- [ ] Admin can manage extensions
- [ ] Extension status reflects registration state
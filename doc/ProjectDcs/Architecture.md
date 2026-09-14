# FA_AsteriskPBX - Architecture

**Document ID:** ARCH-FASTPBX-001  
**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  

---

## 1. Module Overview

FA_AsteriskPBX implements WebRTC softphone integration with Asterisk using SIP.js for browser-based SIP client functionality.

## 2. Component Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                    Pages (UI Layer)                          │
├─────────────────────────────────────────────────────────────┤
│ - softphone.php                                              │
│   ├─ WebRTC connection management                            │
│   ├─ Dialpad UI                                             │
│   ├─ Call display                                           │
│   └─ Recent calls list                                      │
│                                                               │
│ - popup.php                                                  │
│   └─ Incoming call popup                                     │
│                                                               │
│ - admin.php                                                  │
│   └─ Extension management                                    │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ includes
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   Includes (Backend)                        │
├─────────────────────────────────────────────────────────────┤
│ - asterisk_db.inc                                            │
│   ├─ get_extension_for_employee()                            │
│   ├─ get_recent_calls_for_extension()                        │
│   └─ save_call_log()                                        │
│                                                               │
│ - asterisk_config.inc                                       │
│   ├─ get_asterisk_host()                                    │
│   └─ get_webrtc_js()                                        │
└─────────────────────────────────────────────────────────────┘
                              │
                              │ SIP.js
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    Asterisk PBX                             │
├─────────────────────────────────────────────────────────────┤
│ - WebSocket connection (wss://host:8089/ws)                  │
│ - SIP registrar/proxy                                        │
│ - RTP media streams                                         │
└─────────────────────────────────────────────────────────────┘
```

## 3. Directory Structure

```
ksf_FA_AsteriskPBX/
├── pages/
│   ├── softphone.php
│   ├── popup.php
│   └── admin.php
├── includes/
│   ├── asterisk_db.inc
│   └── asterisk_config.inc
├── hooks.php
└── doc/ProjectDcs/
```

## 4. Technology Stack

| Component | Technology |
|-----------|------------|
| Language | PHP |
| UI | FrontAccounting UI helpers |
| SIP Client | SIP.js (v0.20.0) |
| Telephony | Asterisk PBX |
| Protocol | WebRTC, WebSocket |
| Permissions | SA_ASTERISKVIEW, SA_ASTERISKADMIN |

## 5. WebRTC Flow

```
Browser ──── WebSocket ──── Asterisk
  │                               │
  │    (SIP signaling)             │
  │                               │
  └─────── RTP Media ─────────────┘
```

## 6. Database Tables

```sql
CREATE TABLE fa_asterisk_extensions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    extension VARCHAR(10) NOT NULL,
    employee_id INT,
    sip_peer VARCHAR(100),
    status VARCHAR(20) DEFAULT 'active'
);

CREATE TABLE fa_asterisk_calls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    extension VARCHAR(10),
    caller_number VARCHAR(50),
    called_number VARCHAR(50),
    call_start DATETIME,
    call_end DATETIME,
    duration INT,
    status VARCHAR(20)
);
```
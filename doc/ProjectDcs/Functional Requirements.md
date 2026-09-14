# FA_AsteriskPBX - Functional Requirements

**Document ID:** FR-FASTPBX-001  
**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  

---

## 1. Functional Requirements

### 1.1 WebRTC Softphone

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-001 | System SHALL connect to Asterisk via WebSocket | MUST |
| FR-002 | System SHALL display user extension on page | MUST |
| FR-003 | System SHALL show connection status | MUST |
| FR-004 | System SHALL auto-connect on page load | MUST |
| FR-005 | User SHALL see error if extension not assigned | MUST |

### 1.2 Dialpad

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-010 | System SHALL display numeric dialpad (0-9, *, #) | MUST |
| FR-011 | Clicking digit SHALL add to dialed number | MUST |
| FR-012 | System SHALL provide "Dial Number" action | MUST |
| FR-013 | System SHALL provide "Hang Up" action | MUST |
| FR-014 | DTMF tones SHALL be sent during call | SHOULD |

### 1.3 Making Calls

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-020 | User SHALL initiate call by clicking Dial Number | MUST |
| FR-021 | System SHALL use SIP URI format for dialing | MUST |
| FR-022 | System SHALL update status to "Calling" | MUST |
| FR-023 | System SHALL update status to "In Call" on connect | MUST |
| FR-024 | System SHALL handle call failure | MUST |

### 1.4 Receiving Calls

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-030 | Incoming call SHALL trigger browser notification | MUST |
| FR-031 | User SHALL have option to Accept or Reject | MUST |
| FR-032 | Accepting call SHALL connect audio | MUST |
| FR-033 | Status SHALL update to "In Call" | MUST |
| FR-034 | Rejecting call SHALL cancel invitation | MUST |

### 1.5 Call History

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-040 | System SHALL display recent calls list | MUST |
| FR-041 | List SHALL show time, from, to, status | MUST |
| FR-042 | System SHALL retrieve last 10 calls by default | MUST |

### 1.6 Admin Functions

| ID | Requirement | Priority |
|----|-------------|----------|
| FR-050 | Admin SHALL view all extensions | MUST |
| FR-051 | Admin SHALL add/edit extensions | MUST |
| FR-052 | Admin SHALL assign extensions to employees | MUST |
| FR-053 | Admin SHALL configure Asterisk host | MUST |

## 2. Status States

| Status | Description |
|--------|-------------|
| Disconnected | Not connected to Asterisk |
| Connected | Connected, idle |
| Calling | Outbound call in progress |
| In Call | Active call ongoing |
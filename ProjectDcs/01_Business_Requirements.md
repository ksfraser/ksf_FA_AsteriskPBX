# Business Requirements - Asterisk PBX Integration (ksf_FA_AsteriskPBX)

**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  
**Date:** May 2026  
**Author:** Ksfraser Development Team  

---

## 1. Executive Summary

The Asterisk PBX Integration module (ksf_FA_AsteriskPBX) bridges FrontAccounting with an Asterisk PBX telephone system, enabling call center functionality, customer lookup on incoming calls, and browser-based VoIP calling (WebRTC). This integration transforms FA from a pure financial system into a customer relationship hub by combining telephony data with customer records.

The module provides extension mapping, call history tracking, voicemail management, and a WebRTC softphone—all accessible from within FrontAccounting's familiar interface.

---

## 2. Problem Statement

### 2.1 Telephony Integration Challenges

Organizations using FrontAccounting with Asterisk face significant operational gaps:

1. **Disconnected Systems**: Phone system and FA operate independently, requiring manual context switching.

2. **No Caller Identification**: Agents cannot see customer information when calls arrive.

3. **Missed Context**: Previous call history not visible during conversations.

4. **Manual Dialing**: Agents must enter numbers manually, losing productivity.

5. **Scattered Communication Data**: Call logs exist only in Asterisk, not accessible to FA users.

### 2.2 Business Impact

- Reduced agent productivity from context switching
- Poor customer experience (agents lack history)
- Inability to link calls to customer records
- Manual processes for call logging
- Limited remote working capabilities

---

## 3. Project Scope

### 3.1 In Scope

| Component | Description |
|-----------|-------------|
| Extension Mapping | Link Asterisk extensions to FA employees |
| Call History | Track incoming/outgoing calls with customer links |
| Caller Popup | Display customer info on incoming calls |
| Voicemail Management | View and manage voicemail messages |
| WebRTC Softphone | Browser-based VoIP phone |
| Asterisk AMI Integration | Configuration for Asterisk connectivity |

### 3.2 Out of Scope

- Asterisk server installation/management
- SIP trunk configuration
- Call recording storage
- Advanced call routing
- Queue management
- SMS integration

---

## 4. Features and Capabilities

### 4.1 Extension Management

**Purpose:** Map Asterisk extensions to FrontAccounting users

**Functionality:**
- Create/edit/delete extensions
- Assign extension to FA employee
- Configure SIP peer settings
- Set call forwarding rules
- Sync configuration to Asterisk

**Extension Record Fields:**
- Extension Number (unique)
- Employee Assignment
- SIP Peer Name
- Email Address
- Call Forward Number
- Active/Inactive Status

### 4.2 Call History

**Purpose:** Track all phone activity with CRM integration

**Call Record Fields:**
- Caller Number
- Called Number
- Extension (answered by)
- Call Type (inbound/outbound)
- Start Time
- End Time
- Duration (seconds)
- Status (completed, missed, abandoned)
- Recording Path (if applicable)
- Customer Link (debtor_no)
- Contact Link (contact_id)

**Features:**
- View call history by extension
- Filter by date range
- Filter by call type
- Link calls to customer records
- Access recordings (if stored)

### 4.3 Caller Popup

**Purpose:** Display customer information when call arrives

**Functionality:**
- Real-time call detection via AJAX polling
- Phone number lookup across FA database:
  - CRM Contacts
  - CRM Leads
  - Customer Debtors
- Display matching records
- Quick action buttons:
  - View Customer
  - View Lead
  - Create New Lead
- Call history for the phone number

**Popup Information:**
- Caller phone number
- Matched customer name and details
- Previous call history
- Quick notes section

### 4.4 Voicemail Management

**Purpose:** Access voicemails from within FA

**Voicemail Record Fields:**
- Extension
- Caller Number
- Message Date/Time
- Duration (seconds)
- File Path
- Read/Unread Status

**Features:**
- List voicemails per extension
- Mark as read/unread
- Playback (via media player)
- Delete old voicemails

### 4.5 WebRTC Softphone

**Purpose:** Browser-based VoIP phone using WebRTC

**Features:**
- SIP.js integration for WebRTC
- Dial pad interface
- Make/receive calls from browser
- Call status display
- DTMF tone sending
- Call history display
- Auto-connect on page load

**Technical Requirements:**
- Asterisk with WebSocket support
- Valid SSL certificate
- Supported browsers: Chrome, Firefox, Edge

---

## 5. Use Cases

### 5.1 Agent Receives Customer Call

**Scenario:** Customer calls company, agent sees popup

```
Flow:
1. Customer calls company number
2. Asterisk routes to agent extension
3. AsteriskPBX module detects incoming call
4. Popup displays with caller ID
5. System searches for matching phone number
6. Agent sees customer info and history
7. Agent answers call with full context
```

### 5.2 Agent Makes Outbound Call

**Scenario:** Agent initiates call from softphone

```
Flow:
1. Agent logs into WebRTC softphone
2. Agent enters customer number or clicks contact
3. Softphone initiates SIP call via Asterisk
4. Asterisk connects to destination
5. Call connected, duration tracked
6. Call recorded in history
```

### 5.3 Manager Reviews Call Metrics

**Scenario:** Manager analyzes call center performance

```
Flow:
1. Manager accesses Call History report
2. Filters by date range and agent
3. Views call counts, durations, missed calls
4. Exports data for analysis
5. Identifies training opportunities
```

---

## 6. Integration Dependencies

### 6.1 Asterisk Configuration

| Setting | Description |
|---------|-------------|
| AMI Host | Asterisk Manager Interface host |
| AMI Port | Default: 5038 |
| AMI Username | Manager account username |
| AMI Password | Manager account password |
| Config Directory | Asterisk config path (default: /etc/asterisk) |

### 6.2 Database Tables

| Table | Purpose |
|-------|---------|
| fa_asterisk_extensions | Extension mapping |
| fa_asterisk_calls | Call history |
| fa_asterisk_voicemail | Voicemail records |

### 6.3 External Systems

| System | Integration Point |
|--------|-------------------|
| Asterisk PBX | AMI for events, SIP for calls |
| Web Browser | SIP.js for WebRTC |
| FA CRM | Customer/Lead lookup |

---

## 7. Technical Constraints

### 7.1 PHP Version Requirements

- **Minimum:** PHP 7.3
- **Recommended:** PHP 8.0+
- **Target:** PHP 8.2

### 7.2 Asterisk Version Compatibility

- **Minimum:** Asterisk 13 (for WebRTC)
- **Recommended:** Asterisk 16+

### 7.3 Browser Requirements

- Chrome 80+
- Firefox 75+
- Edge 80+
- WebSocket support required

### 7.4 Network Requirements

- Asterisk reachable on configured host/port
- WebSocket accessible for WebRTC
- HTTPS required for WebRTC (mixed content blocked)

---

## 8. Success Criteria

| Criterion | Measurement |
|-----------|-------------|
| Extension mapping functional | Extensions sync to Asterisk |
| Call history captures all calls | All calls logged to database |
| Popup displays on incoming calls | Customer info shown |
| Voicemail accessible | Messages retrievable |
| WebRTC calls work | Make/receive calls functional |
| Security permissions enforced | Only authorized access |

---

## 9. Future Roadmap

| Version | Feature | Description |
|---------|---------|-------------|
| 1.1.0 | Click-to-Call | Click phone number to dial |
| 1.2.0 | Call Recording | Store and playback recordings |
| 1.3.0 | Queue Integration | FA integration with call queues |
| 2.0.0 | Analytics Dashboard | Call metrics and KPIs |
| 2.0.0 | Screen Pop CRM | Full CRM integration |

---

## 10. Glossary

| Term | Definition |
|------|------------|
| Asterisk | Open-source PBX software |
| AMI | Asterisk Manager Interface - API for controlling Asterisk |
| WebRTC | Web Real-Time Communication - browser-based calling |
| SIP | Session Initiation Protocol - VoIP signaling |
| Extension | Internal phone number |
| DTMF | Dual-Tone Multi-Frequency - phone keypad tones |
| Softphone | Software-based phone (vs hardware) |

---

*Document Version: 1.0*  
*Last Updated: May 2026*
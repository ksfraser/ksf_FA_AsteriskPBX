# Architecture - Asterisk PBX Integration (ksf_FA_AsteriskPBX)

**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  
**Date:** May 2026  

---

## 1. Architecture Overview

The Asterisk PBX Integration module follows FrontAccounting's extension pattern while integrating with external Asterisk telephony systems. The architecture combines FA's menu and security system with Asterisk's AMI (Manager Interface) for control and SIP.js for WebRTC functionality.

### 1.1 High-Level Architecture

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                     FrontAccounting Core                                      │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐        │
│  │   Session   │  │   Security  │  │    Menu     │  │  Database   │        │
│  └─────────────┘  └─────────────┘  └─────────────┘  └──────┬──────┘        │
└──────────────────────────────────────────────────────────────┼───────────────┘
                                                                     │
                                          ┌─────────────────────────┼───────────────┐
                                          │                   hooks_fa_asteriskpbx │
                                          │                          │              │
                                          │  ┌──────────────────────────────────────┐│
                                          │  │         Module Integration           ││
                                          │  │  - install_options()                 ││
                                          │  │  - install_access()                   ││
                                          │  │  - activate_extension()               ││
                                          │  └──────────────────────────────────────┘│
                                          │                          │              │
                                          │     ┌─────────────────────┴─────────────┐│
                                          │     │                                    ││
                                          │     ▼                                    ▼│
                                          │  ┌─────────────┐  ┌─────────────┐  ┌─────────────┐│
                                          │  │   pages/    │  │  includes/  │  │   sql/      ││
                                          │  │   admin.php │  │ asterisk_db │  │  update.sql ││
                                          │  │   popup.php │  │ asterisk_  │  │             ││
                                          │  │ softphone.php│  │  config.inc│  │             ││
                                          │  └─────────────┘  └─────────────┘  └─────────────┘│
                                          └─────────────────────────────────────────────────┘
                                                                     │
                                                                     ▼
                                          ┌─────────────────────────────────────────────┐
                                          │           Asterisk PBX System               │
                                          │  ┌─────────────┐  ┌─────────────┐           │
                                          │  │  AMI        │  │  SIP        │           │
                                          │  │  (Manager)  │  │  (Calls)    │           │
                                          │  └─────────────┘  └─────────────┘           │
                                          └─────────────────────────────────────────────┘
                                                                     │
                                          ┌─────────────────────────┬──────────────────────┐
                                          │                         │                      │
                                          ▼                         ▼                      ▼
                                  ┌───────────────┐       ┌───────────────┐       ┌───────────────┐
                                  │   Browser     │       │   SIP Phone   │       │  Other PBX   │
                                  │   (WebRTC)    │       │   (Hardware)  │       │  (Trunks)    │
                                  └───────────────┘       └───────────────┘       └───────────────┘
```

---

## 2. Module Structure

### 2.1 File Structure

```
ksf_FA_AsteriskPBX/
├── hooks.php                    # FA extension hooks
├── includes/
│   ├── asterisk_db.inc          # Database functions
│   └── asterisk_config.inc       # Asterisk configuration
├── pages/
│   ├── admin.php                 # Extension management
│   ├── popup.php                 # Caller popup window
│   └── softphone.php             # WebRTC softphone
├── sql/
│   └── update.sql                # Database schema
├── ProjectDcs/                   # Documentation
└── AGENTS.md
```

### 2.2 Hooks Integration

```php
class hooks_fa_asteriskpbx extends hooks {
    var $module_name = 'fa_asteriskpbx';
    
    function install_options($app) {
        // CRM app menu items
    }
    
    function install_access() {
        // Security areas
    }
    
    function activate_extension($company, $check_only=true) {
        // Create database tables
    }
}
```

---

## 3. Database Schema

### 3.1 Entity Relationship Diagram

```
┌─────────────────────────────────┐         ┌─────────────────────────────────┐
│    fa_asterisk_extensions      │         │      fa_asterisk_calls           │
├─────────────────────────────────┤         ├─────────────────────────────────┤
│ id (PK)                         │1       *│ id (PK)                         │
│ extension (UNIQUE)              │─────────│ extension (FK)                  │
│ employee_id                     │         │ caller_number                   │
│ name                            │         │ called_number                   │
│ is_active                       │         │ call_type (inbound/outbound)    │
│ created_at                      │         │ start_time                      │
│ updated_at                      │         │ end_time                        │
└─────────────────────────────────┘         │ duration                        │
                                            │ status                          │
                                            │ recording_path                  │
                                            │ debtor_no (FK)                  │
                                            │ contact_id                      │
                                            └─────────────────────────────────┘
                                                   │
                                                   │
                                            ┌──────┴──────┐
                                            │             │
                                            ▼             ▼
                        ┌─────────────────────────────────┐
                        │     fa_asterisk_voicemail       │
                        ├─────────────────────────────────┤
                        │ id (PK)                         │
                        │ extension (FK)                  │
                        │ caller_number                  │
                        │ message_date                   │
                        │ duration                       │
                        │ file_path                      │
                        │ is_read                        │
                        │ created_at                     │
                        └─────────────────────────────────┘
```

### 3.2 Table Definitions

#### fa_asterisk_extensions

```sql
CREATE TABLE IF NOT EXISTS `fa_asterisk_extensions` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `extension` VARCHAR(20) NOT NULL,
    `employee_id` VARCHAR(100) DEFAULT NULL,
    `name` VARCHAR(100) DEFAULT NULL,
    `is_active` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_extension` (`extension`),
    KEY `idx_employee` (`employee_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

#### fa_asterisk_calls

```sql
CREATE TABLE IF NOT EXISTS `fa_asterisk_calls` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `caller_number` VARCHAR(20) DEFAULT NULL,
    `called_number` VARCHAR(20) DEFAULT NULL,
    `extension` VARCHAR(20) DEFAULT NULL,
    `call_type` VARCHAR(20) DEFAULT 'inbound',
    `start_time` DATETIME NOT NULL,
    `end_time` DATETIME DEFAULT NULL,
    `duration` INT(11) DEFAULT 0,
    `status` VARCHAR(20) DEFAULT 'completed',
    `recording_path` VARCHAR(500) DEFAULT NULL,
    `debtor_no` VARCHAR(20) DEFAULT NULL,
    `contact_id` INT(11) DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_extension` (`extension`),
    KEY `idx_debtor` (`debtor_no`),
    KEY `idx_start_time` (`start_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

---

## 4. Page Architecture

### 4.1 Admin Page (Extension Management)

```
┌─────────────────────────────────────────────────────────────────┐
│                    Extension Management Flow                      │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Page Load                                                        │
│      │                                                            │
│      ▼                                                            │
│  ┌─────────────────────────────────────────────┐                │
│  │  Display Extension List                      │                │
│  │  - Extension Number                          │                │
│  │  - Employee Assignment                       │                │
│  │  - SIP Peer                                 │                │
│  │  - Status                                   │                │
│  └─────────────────────────────────────────────┘                │
│      │                                                            │
│      ├── [Edit Extension] ───┐                                   │
│      │                        │                                   │
│      ▼                        ▼                                   │
│  ┌─────────────────────────────────────────────┐                │
│  │  Extension Form (Edit/Add)                   │                │
│  │  - Extension Number                          │                │
│  │  - Employee Selection                        │                │
│  │  - SIP Peer Name                            │                │
│  │  - Email                                    │                │
│  │  - Forward To                               │                │
│  └─────────────────────────────────────────────┘                │
│      │                                                            │
│      ▼                                                            │
│  ┌─────────────────────────────────────────────┐                │
│  │  Sync to Asterisk                            │                │
│  │  - Update SIP peer                           │                │
│  │  - Apply forward rules                       │                │
│  └─────────────────────────────────────────────┘                │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### 4.2 Call Popup Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                       Call Popup Flow                            │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Incoming Call (AJAX Poll)                                       │
│      │                                                            │
│      ▼                                                            │
│  ┌─────────────────────────────────────────────┐                │
│  │  Poll for New Call                           │                │
│  │  GET /popup.php?poll=1                       │                │
│  │  Returns: {new_call: bool, call_id, caller} │                │
│  └─────────────────────────────────────────────┘                │
│      │                                                            │
│      ├── No new call → Sleep and retry                           │
│      │                                                            │
│      ▼                                                            │
│  ┌─────────────────────────────────────────────┐                │
│  │  New Call Detected                           │                │
│  │  - caller_number                            │                │
│  └─────────────────────────────────────────────┘                │
│      │                                                            │
│      ▼                                                            │
│  ┌─────────────────────────────────────────────┐                │
│  │  Phone Number Lookup                         │                │
│  │  - Search CRM Contacts                       │                │
│  │  - Search CRM Leads                         │                │
│  │  - Search Customer Debtors                  │                │
│  └─────────────────────────────────────────────┘                │
│      │                                                            │
│      ├── Contact Found → Display Details                          │
│      └── No Contact → Show "Create New Lead"                     │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

### 4.3 WebRTC Softphone Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                      WebRTC Softphone Flow                       │
├─────────────────────────────────────────────────────────────────┤
│                                                                  │
│  Page Load                                                        │
│      │                                                            │
│      ▼                                                            │
│  ┌─────────────────────────────────────────────┐                │
│  │  Get User Extension                          │                │
│  │  - From FA session or extension mapping      │                │
│  │  - Retrieve SIP credentials                  │                │
│  └─────────────────────────────────────────────┘                │
│      │                                                            │
│      ▼                                                            │
│  ┌─────────────────────────────────────────────┐                │
│  │  Initialize SIP.js UserAgent                  │                │
│  │  - WebSocket connection (wss://)            │                │
│  │  - SIP credentials                           │                │
│  │  - ICE servers (STUN)                        │                │
│  └─────────────────────────────────────────────┘                │
│      │                                                            │
│      ├── Connection Failed → Show error                            │
│      │                                                            │
│      ▼                                                            │
│  ┌─────────────────────────────────────────────┐                │
│  │  Softphone Ready                             │                │
│  │  - Display connected status                  │                │
│  │  - Show dialpad                             │                │
│  │  - Display recent calls                      │                │
│  └─────────────────────────────────────────────┘                │
│                                                                  │
│  ┌─────────────────────────────────────────────┐                │
│  │  Making a Call                               │                │
│  │  1. User enters number                       │                │
│  │  2. User clicks "Dial" or "Call"             │                │
│  │  3. SIP.js initiates INVITE                  │                │
│  │  4. Asterisk routes call                     │                │
│  │  5. Call connected (or failed)               │                │
│  └─────────────────────────────────────────────┘                │
│                                                                  │
│  ┌─────────────────────────────────────────────┐                │
│  │  Receiving a Call                             │                │
│  │  1. Asterisk sends INVITE to softphone        │                │
│  │  2. onInvite callback triggered               │                │
│  │  3. Show incoming call dialog                │                │
│  │  4. User accepts or rejects                   │                │
│  │  5. Call connected or rejected               │                │
│  └─────────────────────────────────────────────┘                │
│                                                                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 5. Data Access Layer

### 5.1 Database Functions

```php
// Extension Management
function get_extensions(array $filters = []): ?object
function get_extension(int $id): ?array
function create_extension(string $ext, ?int $emp_id, string $sip_peer, string $email): int
function update_extension(int $id, ?int $emp_id, string $email, string $forward): bool
function get_extension_for_employee(int $employee_id): ?array

// Call History
function record_call(array $call_data): int
function get_recent_calls_for_extension(string $extension, int $limit): ?object
function get_call_history(string $phone_number): ?object

// Voicemail
function get_voicemails_for_extension(string $extension): ?object

// Phone Lookup
function lookup_phone_number(string $phone): array

// Asterisk Configuration
function get_asterisk_host(): string
function get_asterisk_port(): int
function get_asterisk_config_dir(): string
function save_asterisk_settings(...): void
function sync_extension_to_asterisk(array $extension): void
```

### 5.2 Function Relationships

```
┌─────────────────────────────────────────────────────────────────┐
│                       Page Layer                                 │
│  ┌───────────┐  ┌───────────┐  ┌───────────┐                   │
│  │  admin.php │  │  popup.php │  │softphone.php│                  │
│  └─────┬─────┘  └─────┬─────┘  └─────┬─────┘                   │
│        │              │              │                          │
│        └──────────────┼──────────────┘                          │
│                       │                                         │
│                       ▼                                         │
│         ┌─────────────────────────┐                           │
│         │    asterisk_db.inc      │                           │
│         └────────────┬────────────┘                           │
│                      │                                         │
│        ┌─────────────┼─────────────┐                          │
│        │             │             │                          │
│        ▼             ▼             ▼                          │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐                   │
│  │ Extensions│ │   Calls   │ │  Voicemail │                   │
│  │    CRUD   │ │  History  │ │  Management│                   │
│  └───────────┘ └───────────┘ └───────────┘                   │
└─────────────────────────────────────────────────────────────────┘
```

---

## 6. Security Architecture

### 6.1 Security Areas

```php
define('SS_ASTERISK', 120 << 8);  // Section 120

$security_areas['SA_ASTERISKADMIN'] = array(SS_ASTERISK | 1, _("Administer Extensions"));
$security_areas['SA_ASTERISKVIEW'] = array(SS_ASTERISK | 2, _("View Call History"));
$security_areas['SA_ASTERISKMANAGE'] = array(SS_ASTERISK | 3, _("Manage Calls"));
```

### 6.2 Page Security

| Page | Required Permission |
|------|-------------------|
| admin.php | SA_ASTERISKADMIN |
| popup.php | SA_ASTERISKVIEW |
| softphone.php | SA_ASTERISKVIEW |

### 6.3 Menu Integration

```php
$app->add_lapp_function(0, _("Extension Mapping"), 
    ".../extensions.php", 'SA_ASTERISKADMIN', MENU_MAINTENANCE);
$app->add_lapp_function(1, _("Call History"), 
    ".../calls.php", 'SA_ASTERISKVIEW', MENU_INQUIRY);
$app->add_lapp_function(2, _("Voicemail"), 
    ".../voicemail.php", 'SA_ASTERISKMANAGE', MENU_INQUIRY);
```

---

## 7. Asterisk Integration

### 7.1 AMI Integration

The module connects to Asterisk via the Manager Interface (AMI):

```php
// Asterisk Manager Connection
$host = get_asterisk_host();
$port = get_asterisk_port();
$user = get_asterisk_user();
$pass = get_asterisk_pass();

// AMI Actions
- Command: Execute Asterisk CLI commands
- ExtensionState: Check extension status
- Redirect: Transfer calls
- Originate: Make outbound calls
```

### 7.2 SIP Configuration Sync

Extensions are synced to Asterisk sip.conf:

```ini
[extension_number]
type=friend
host=dynamic
secret=sip_password
context=from-internal
```

### 7.3 WebRTC Integration

WebRTC requires Asterisk 13+ with WebSocket support:

```bash
# /etc/asterisk/http.conf
[general]
enabled=yes
bindaddr=0.0.0.0
bindport=8088
tlsenable=yes
tlsbindaddr=0.0.0.0:8089
```

WebSocket URL format:
```
wss://asterisk_host:8089/ws
```

---

## 8. External Dependencies

### 8.1 FrontAccounting Dependencies

| Component | Purpose |
|-----------|---------|
| FA Database | Extension, call, voicemail storage |
| FA Session | Employee identification |
| FA CRM | Customer/Lead lookup |
| FA Security | Permission enforcement |

### 8.2 External Dependencies

| System | Protocol | Purpose |
|--------|----------|---------|
| Asterisk | AMI | Control interface |
| Asterisk | SIP | VoIP signaling |
| Asterisk | WebSocket | WebRTC transport |
| Browser | WebRTC | Browser calling |
| SIP.js | JavaScript | WebRTC client |

---

## 9. Package Structure

```
ksf_FA_AsteriskPBX/
├── hooks.php                      # FA hooks integration
├── includes/
│   ├── asterisk_db.inc             # Database functions
│   └── asterisk_config.inc         # Asterisk configuration
├── pages/
│   ├── admin.php                   # Extension management
│   ├── popup.php                   # Caller popup
│   └── softphone.php               # WebRTC softphone
├── sql/
│   └── update.sql                  # Database schema
└── ProjectDcs/
    ├── 01_Business_Requirements.md
    ├── 02_Architecture.md
    ├── 03_Functional_Requirements.md
    ├── 04_Use_Case.md
    ├── 05_Test_Plan.md
    └── 06_UAT_Plan.md
```

---

*Document Version: 1.0*  
*Last Updated: May 2026*
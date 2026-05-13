# AGENTS.md - ksf_FA_AsteriskPBX#

## Architecture Overview#

**FA Module** for Asterisk PBX integration - click-to-call, call logging, and telephony features.

### Core Principles#
- **SOLID**: Single Responsibility, Open/Closed, Liskov Substitution, Interface Segregation, Dependency Inversion#
- **DRY**: Don't Repeat Yourself#
- **TDD**: Test-Driven Development#
- **DI**: Dependency Injection#
- **SRP**: Single Responsibility Principle#

## Repository Structure#

```
ksf_FA_AsteriskPBX/
├── sql/                    # Database schemas#
│   ├── fa_asterisk_extensions.sql#
│   ├── fa_asterisk_calls.sql#
│   └── fa_asterisk_logs.sql#
├── includes/              # FA-specific DB classes#
│   ├── extensions_db.inc#
│   ├── calls_db.inc#
│   └── logs_db.inc#
├── src/                    # Business logic#
│   ├── Services/#
│   │   ├── AsteriskService.php#
│   │   └── CallLogger.php#
│   └── ValueObjects/#
│       └── Extension.php#
├── pages/                 # UI pages#
├── hooks.php#
├── composer.json#
└── ProjectDocs/#
    ├── Requirements.md#
    ├── RTM.md#
    ├── BABOK.md#
    └── UML.md#
```

## Dependencies#

- **ksf_FA_AsteriskPBX_Core** (business logic)#
- **ksf_FA_CRM** (link calls to contacts)#
- **FrontAccounting 2.4+**#

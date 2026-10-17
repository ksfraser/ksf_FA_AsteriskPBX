<!-- Repo-specific appendix to the shared AGENTS.md. Generic conventions live in AGENTS_ARCH.md (hardlinked). -->

# AGENTS.local.md — ksf_FA_AsteriskPBX
## Architecture Overview
**FA Module** for Asterisk PBX integration - click-to-call, call logging, and telephony features.
## Repository Structure
```
ksf_FA_AsteriskPBX/
├── sql/                    # Database schemas
│   ├── fa_asterisk_extensions.sql
│   ├── fa_asterisk_calls.sql
│   └── fa_asterisk_logs.sql
├── includes/              # FA-specific DB classes
│   ├── extensions_db.inc
│   ├── calls_db.inc
│   └── logs_db.inc
├── src/                    # Business logic
│   ├── Services/
│   │   ├── AsteriskService.php
│   │   └── CallLogger.php
│   └── ValueObjects/
│       └── Extension.php
├── pages/                 # UI pages
├── hooks.php
├── composer.json
└── ProjectDocs/
    ├── Requirements.md
    ├── RTM.md
    ├── BABOK.md
    └── UML.md
```
## Dependencies
- **ksf_FA_AsteriskPBX_Core** (business logic)
- **ksf_FA_CRM** (link calls to contacts)
- **FrontAccounting 2.4+**
## Development Workflow
All development is done in the **devel tree** (`~/Documents/ksf_FA_AsteriskPBX`). Do **not** edit files in the UAT bind point directly.
### Workflow Steps
1. **Develop** in this repo (feature branches preferred)
2. **Test**: run repo-appropriate tests
3. **Lint**: `php -l` on modified PHP files (no syntax errors)
4. **Commit** and **Push** branch to GitHub
5. **Merge** to `master` when ready
6. **Push** `master` to GitHub
7. **Deploy** to UAT by pulling in the Infrastructure bind point:
   ```
   cd ~/ksf_Infrastructure/fa_modules/ksf_FA_AsteriskPBX
   git stash -u
   git pull origin master
   git stash pop
   ```
### UAT Bind Point
| Path | Purpose |
|------|---------|
| `~/Documents/ksf_FA_AsteriskPBX` | Devel tree — all development, testing, commits |
| `~/ksf_Infrastructure/fa_modules/ksf_FA_AsteriskPBX` | UAT bind point — deployment target, integration testing (if mirrored) |

# FA_AsteriskPBX - UAT Plan

**Document ID:** UAT-FASTPBX-001  
**Module:** ksf_FA_AsteriskPBX  
**Version:** 1.0.0  

---

## 1. UAT Objectives

Verify that:
1. Softphone connects to Asterisk successfully
2. Users can make and receive calls
3. Call notifications display correctly
4. Call history is accurate
5. Admin extension management works

## 2. Test Scenarios

| Scenario | Expected | Tester |
|----------|----------|--------|
| UAT-001: User with extension loads page | Softphone ready, connected | Employee |
| UAT-002: User without extension loads page | Error message shown | Employee |
| UAT-003: Make outbound call | Call connects | Employee |
| UAT-004: Receive inbound call | Notification popup | Employee |
| UAT-005: Accept incoming call | Audio connected | Employee |
| UAT-006: Reject incoming call | Call declined | Employee |
| UAT-007: Hang up call | Call terminated | Employee |
| UAT-008: View call history | Recent calls displayed | Employee |
| UAT-009: Admin assigns extension | Employee linked | Admin |
| UAT-010: Admin configures host | Asterisk host set | Admin |

## 3. Test Data

| Employee | Extension | Status |
|----------|-----------|--------|
| Test User 1 | 1001 | Active |
| Test User 2 | - | No extension |
| Admin User | 1000 | Admin |

## 4. Sign-Off

| Role | Name | Date |
|------|------|------|
| Employee | | |
| Admin | | |
| QA Lead | | |

## 5. Defect Categories

| Severity | Definition |
|----------|-------------|
| Critical | Call cannot be made/received |
| High | Call drops frequently |
| Medium | UI display issues |
| Low | Minor cosmetic issues |
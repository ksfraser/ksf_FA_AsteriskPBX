<?php

declare(strict_types=1);

namespace Ksfraser\Tests\Unit\FA\AsteriskPBX;

use PHPUnit\Framework\TestCase;

/**
 * The caller's-ID lookup used three tables that exist nowhere:
 *
 *  - 0_ext_contact_info   (never created by any schema)
 *  - 0_crm_leads          (the CRM's table is 0_ksf_crm_leads)
 *  - 0_hr_employees       (never created by any schema; FA has no employee
 *                          entity)
 *
 * and it also searched FA core's 0_debtors_master for a `phone` column that
 * the debtor record does not have (it has no phone column at all), so that
 * query could only ever fail.
 */
class ContactAccessTest extends TestCase
{
    private const EMPLOYEE_TABLE = 'ksf_asteriskpbx_employees';

    private string $moduleDir;

    protected function setUp(): void
    {
        $this->moduleDir = dirname(__DIR__, 2);
    }

    public function testLookupDoesNotAddressTheNonExistentExtContactInfoTable(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/includes/asterisk_db.inc');

        $this->assertStringNotContainsString(
            'TB_PREF . "ext_contact_info',
            $contents,
            'the lookup must not address 0_ext_contact_info: that table is created by no schema'
        );
    }

    public function testLookupDoesNotAddressTheNonExistentCrmLeadsTable(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/includes/asterisk_db.inc');

        $this->assertStringNotContainsString(
            'TB_PREF . "crm_leads',
            $contents,
            "the CRM's leads table is 0_ksf_crm_leads, not 0_crm_leads"
        );
        $this->assertStringContainsString(
            'TB_PREF . "ksf_crm_leads',
            $contents,
            'the lookup must read the CRM module\'s own leads table'
        );
    }

    public function testLookupDoesNotAddressTheNonExistentHrEmployeesTable(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/includes/asterisk_db.inc');

        $this->assertStringNotContainsString(
            'hr_employees',
            $contents,
            'includes/asterisk_db.inc must not address 0_hr_employees: that table is created by no schema'
        );
        $this->assertSame(
            2,
            substr_count($contents, 'TB_PREF . "' . self::EMPLOYEE_TABLE),
            'both employee joins must read the module-owned directory table'
        );
    }

    public function testLookupDoesNotSearchTheDebtorRecordForAPhoneColumn(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/includes/asterisk_db.inc');

        $this->assertStringNotContainsString(
            'TB_PREF . "debtors_master',
            $contents,
            "0_debtors_master has no phone column; a debtor can never be matched by phone"
        );
    }

    public function testLookupUsesTheSharedContactResolver(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/includes/asterisk_db.inc');

        $this->assertStringContainsString(
            'asterisk_contact_resolver()',
            $contents,
            'the contact lookup must go through the shared contact resolver'
        );
        $this->assertStringContainsString(
            '->search($phone_number',
            $contents,
            'the contact lookup must use the resolver\'s search()'
        );
    }

    public function testSchemaCreatesTheEmployeeDirectory(): void
    {
        $contents = (string) file_get_contents($this->moduleDir . '/sql/install.sql');

        $this->assertStringContainsString(
            'CREATE TABLE IF NOT EXISTS `0_' . self::EMPLOYEE_TABLE . '`',
            $contents
        );
    }

    public function testTheSharedResolverPackageIsRequired(): void
    {
        $composer = json_decode(
            (string) file_get_contents($this->moduleDir . '/composer.json'),
            true
        );

        $this->assertArrayHasKey(
            'ksfraser/ksf-fa-common',
            $composer['require'] ?? [],
            'the module needs the shared contact resolver package'
        );
    }
}

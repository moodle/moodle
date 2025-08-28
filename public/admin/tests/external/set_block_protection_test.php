<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

declare(strict_types=1);

namespace core_admin\external;

/**
 * Unit tests to test block protection changes.
 *
 * @package     core
 * @covers      \core_admin\external\set_block_protection
 * @copyright   2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class set_block_protection_test extends \core_external\tests\externallib_testcase {
    /**
     * Test execute method with no login.
     */
    public function test_execute_no_login(): void {
        $this->expectException(\require_login_exception::class);
        set_block_protection::execute('block_login', 1);
    }

    /**
     * Test execute method with no login.
     */
    public function test_execute_no_capability(): void {
        $this->resetAfterTest();
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->expectException(\required_capability_exception::class);
        set_block_protection::execute('block_login', 1);
    }

    /**
     * Test the execute function with a range of parameters.
     *
     * @dataProvider execute_provider
     * @param string $block
     * @param int $targetstate
     * @param bool $isundeletable
     */
    public function test_execute(
        string $block,
        int $targetstate,
        bool $isundeletable,
    ): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        set_block_protection::execute($block, $targetstate);

        $undeletable = \block_manager::get_undeletable_block_types();
        [, $pluginname] = explode('_', $block, 2);

        if ($isundeletable) {
            $this->assertNotFalse(array_search($pluginname, $undeletable));
        } else {
            $this->assertFalse(array_search($pluginname, $undeletable));
        }
        $this->assertCount(1, \core\notification::fetch());
    }

    /**
     * Data provider for test_execute.
     *
     * @return array
     */
    public static function execute_provider(): array {
        return [
            [
                'block_login',
                1,
                true,
            ],
            [
                'block_login',
                0,
                false,
            ],
        ];
    }

    /**
     * Assert that an exception is thrown when the block does not exist.
     */
    public function execute_block_does_not_exist(): void {
        $this->expectException(\dml_missing_record_exception::class);

        set_block_protection::execute('fake_block', 1);
        $this->assertDebuggingCalledCount(1);
    }
}

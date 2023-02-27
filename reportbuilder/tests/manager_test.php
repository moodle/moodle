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

namespace core_reportbuilder;

use context_system;
use core_reportbuilder_generator;
use core_reportbuilder_testcase;
use core_user\reportbuilder\datasource\users;
use stdClass;
use core_reportbuilder\local\models\report;
use core_reportbuilder\local\report\base;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for the report manager class
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\manager
 * @copyright   2020 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager_test extends core_reportbuilder_testcase {

    /**
     * Test creating a report instance from persistent
     */
    public function test_get_report_from_persistent(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");

        $this->resetAfterTest();

        $report = manager::create_report_persistent((object) [
            'type' => base::TYPE_SYSTEM_REPORT,
            'source' => system_report_available::class,
        ]);

        $systemreport = manager::get_report_from_persistent($report);
        $this->assertInstanceOf(system_report::class, $systemreport);
    }

    /**
     * Test creating a report instance from persistent differs per-user, using a report source whose own initialization is
     * dependent on the current user (the users report source, loading available user profile fields)
     *
     * Note: internally the {@see get_custom_report_content} test helper calls {@see manager::get_report_from_persistent}
     */
    public function test_get_report_from_persistent_per_user(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Custom profile field, visible only to the admin.
        $this->getDataGenerator()->create_custom_profile_field([
            'shortname' => 'text', 'name' => 'Text field', 'datatype' => 'text', 'visible' => 0]);
        $user = $this->getDataGenerator()->create_user(['username' => 'usertwo', 'profile_field_text' => 'Hello']);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'Hidden profile field', 'source' => users::class, 'default' => 0]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:username', 'sortenabled' => 1]);
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:profilefield_text']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            ['admin', ''],
            ['usertwo', 'Hello'],
        ], array_map('array_values', $content));

        // Now switch to second, non-admin, user.
        $this->setUser($user);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            ['admin'],
            ['usertwo'],
        ], array_map('array_values', $content));
    }

    /**
     * Test creating a report instance from persistent with an invalid source
     */
    public function test_get_report_from_persistent_invalid(): void {
        $this->resetAfterTest();

        $report = manager::create_report_persistent((object) [
            'type' => base::TYPE_SYSTEM_REPORT,
            'source' => stdClass::class,
        ]);

        $this->expectException(source_invalid_exception::class);
        manager::get_report_from_persistent($report);
    }

    /**
     * Test creating a report instance from persistent with an unavailable source
     */
    public function test_get_report_from_persistent_unavailable(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_unavailable.php");

        $this->resetAfterTest();

        $report = manager::create_report_persistent((object) [
            'type' => base::TYPE_SYSTEM_REPORT,
            'source' => system_report_unavailable::class,
        ]);

        $this->expectException(source_unavailable_exception::class);
        manager::get_report_from_persistent($report);
    }

    /**
     * Test report source exists
     */
    public function test_report_source_exists(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");
        $this->assertTrue(manager::report_source_exists(system_report_available::class));

        $this->assertFalse(manager::report_source_exists(stdClass::class));
    }

    /**
     * Test report source available
     */
    public function test_report_source_available(): void {
        global $CFG;

        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");
        $this->assertTrue(manager::report_source_available(system_report_available::class));

        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_unavailable.php");
        $this->assertFalse(manager::report_source_available(system_report_unavailable::class));
    }

    /**
     * Test creating a report persistent model
     */
    public function test_create_report_persistent(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");

        $this->resetAfterTest();

        $report = manager::create_report_persistent((object) [
            'type' => base::TYPE_SYSTEM_REPORT,
            'source' => \core_reportbuilder\system_report_available::class,
        ]);

        $this->assertInstanceOf(report::class, $report);
        $this->assertEquals(base::TYPE_SYSTEM_REPORT, $report->get('type'));
        $this->assertEquals(system_report_available::class, $report->get('source'));
        $this->assertInstanceOf(context_system::class, $report->get_context());
    }
}

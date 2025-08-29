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

namespace core_reportbuilder\external\systemreports;

use core\context\system;
use core_reportbuilder_generator;
use core_external\external_api;
use core_reportbuilder\exception\report_access_exception;
use core_reportbuilder\local\systemreports\reports_list;
use core_user\reportbuilder\datasource\users;

/**
 * Unit tests of external class for retrieving system report content
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\systemreports\retrieve
 * @copyright   2023 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class retrieve_test extends \core_external\tests\externallib_testcase {
    /**
     * Text execute method
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        // Two reports, created one second apart to ensure consistent ordering by time created.
        $generator->create_report(['name' => 'My first report', 'source' => users::class]);
        $this->waitForSecond();
        $generator->create_report(['name' => 'My second report', 'source' => users::class, 'tags' => ['cat', 'dog']]);

        // Retrieve paged results.
        $result = retrieve::execute(reports_list::class, ['contextid' => system::instance()->id], '', '', 0, [], 0, 1);
        $result = external_api::clean_returnvalue(retrieve::execute_returns(), $result);

        $this->assertArrayHasKey('data', $result);
        $this->assertEquals([
            'Name',
            'Report source',
            'Tags',
            'Time created',
            'Time modified',
            'Modified by',
        ], $result['data']['headers']);

        $this->assertCount(1, $result['data']['rows']);
        [$name, $source, $tags, $timecreated, $timemodified, $modifiedby] = $result['data']['rows'][0]['columns'];

        $this->assertStringContainsString('My second report', $name);
        $this->assertEquals(users::get_name(), $source);
        $this->assertMatchesRegularExpression('/cat.*dog/', $tags);
        $this->assertNotEmpty($timecreated);
        $this->assertNotEmpty($timemodified);
        $this->assertEquals('Admin User', $modifiedby);

        $this->assertEquals(2, $result['data']['totalrowcount']);

        $this->assertEmpty($result['warnings']);
    }

    /**
     * Test execute method for a user without permission to view report
     */
    public function test_execute_access_exception(): void {
        global $DB;

        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $userrole = $DB->get_field('role', 'id', ['shortname' => 'user']);
        unassign_capability('moodle/reportbuilder:view', $userrole, system::instance());

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot view this report');
        retrieve::execute(reports_list::class, ['contextid' => system::instance()->id]);
    }
}

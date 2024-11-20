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

namespace core_reportbuilder\external\audiences;

use context_system;
use core_reportbuilder\local\models\audience;
use core_reportbuilder_generator;
use core_external\external_api;
use externallib_advanced_testcase;
use core_reportbuilder\exception\report_access_exception;
use core_user\reportbuilder\datasource\users;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/webservice/tests/helpers.php");

/**
 * Unit tests of external class for deleting report audiences
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\external\audiences\delete
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_test extends externallib_advanced_testcase {

    /**
     * Text execute method
     */
    public function test_execute(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report([
            'name' => 'My report',
            'source' => users::class,
            'default' => false,
        ]);

        $audience1 = $generator->create_audience([
            'reportid' => $report->get('id'),
            'configdata' => [],
        ]);
        $audience2 = $generator->create_audience([
            'reportid' => $report->get('id'),
            'configdata' => [],
        ]);

        $audiences = audience::get_records(['reportid' => $report->get('id')]);
        $this->assertCount(2, $audiences);

        // Delete the first audience.
        $result = delete::execute($report->get('id'), $audience1->get_persistent()->get('id'));
        $result = external_api::clean_returnvalue(delete::execute_returns(), $result);
        $this->assertTrue($result);

        $audiences = audience::get_records(['reportid' => $report->get('id')]);
        $this->assertCount(1, $audiences);
        $audience = reset($audiences);
        $this->assertEquals($audience2->get_persistent()->get('id'), $audience->get('id'));
    }

    /**
     * Test execute method for a user without permission to edit reports
     */
    public function test_execute_access_exception(): void {
        $this->resetAfterTest();

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');

        $report = $generator->create_report(['name' => 'My report', 'source' => users::class]);
        $audience1 = $generator->create_audience([
            'reportid' => $report->get('id'),
            'configdata' => [],
        ]);

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        $this->expectException(report_access_exception::class);
        $this->expectExceptionMessage('You cannot edit this report');
        delete::execute($report->get('id'), $audience1->get_persistent()->get('id'));
    }
}

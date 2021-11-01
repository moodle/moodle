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

namespace core_cohort\reportbuilder\datasource;

use core_reportbuilder_testcase;
use core_reportbuilder_generator;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for cohorts datasource
 *
 * @package     core_cohort
 * @covers      \core_cohort\reportbuilder\datasource\cohorts
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class datasource_test extends core_reportbuilder_testcase {

    /**
     * Test cohorts datasource
     */
    public function test_cohorts_datasource(): void {
        $this->resetAfterTest();

        // Test subject.
        $cohort = $this->getDataGenerator()->create_cohort([
            'name' => 'Legends',
            'idnumber' => 'C101',
            'description' => 'Cohort for the legends',
        ]);

        $user = $this->getDataGenerator()->create_user(['firstname' => 'Lionel', 'lastname' => 'Richards']);
        cohort_add_member($cohort->id, $user->id);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Cohorts', 'source' => cohorts::class]);

        // Add user fullname column to the report.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:fullname']);

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertCount(1, $content);

        $contentrow = array_values(reset($content));
        $this->assertEquals([
            'System', // Context.
            'Legends', // Name.
            'C101', // ID number.
            '<div class="text_to_html">Cohort for the legends</div>', // Description.
            'Lionel Richards', // User.
        ], $contentrow);
    }
}

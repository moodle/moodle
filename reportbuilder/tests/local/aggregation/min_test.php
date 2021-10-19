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

namespace core_reportbuilder\local\aggregation;

use core_reportbuilder_testcase;
use core_reportbuilder_generator;
use core_user\reportbuilder\datasource\users;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->dirroot}/reportbuilder/tests/helpers.php");

/**
 * Unit tests for min aggregation
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\local\aggregation\base
 * @covers      \core_reportbuilder\local\aggregation\min
 * @copyright   2021 Paul Holden <paulh@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class min_test extends core_reportbuilder_testcase {

    /**
     * Test aggregation when applied to column
     */
    public function test_column_aggregation(): void {
        $this->resetAfterTest();

        // Test subjects.
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'suspended' => 1]);
        $this->getDataGenerator()->create_user(['firstname' => 'Bob', 'suspended' => 0]);

        /** @var core_reportbuilder_generator $generator */
        $generator = $this->getDataGenerator()->get_plugin_generator('core_reportbuilder');
        $report = $generator->create_report(['name' => 'Users', 'source' => users::class, 'default' => 0]);

        // First column, sorted.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:firstname'])
            ->set('sortenabled', true)
            ->update();

        // This is the column we'll aggregate.
        $generator->create_column(['reportid' => $report->get('id'), 'uniqueidentifier' => 'user:suspended'])
            ->set('aggregation', min::get_class_name())
            ->update();

        $content = $this->get_custom_report_content($report->get('id'));
        $this->assertEquals([
            [
                'c0_firstname' => 'Admin',
                'c1_suspended' => 'No',
            ],
            [
                'c0_firstname' => 'Bob',
                'c1_suspended' => 'No',
            ],
        ], $content);
    }
}

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

use advanced_testcase;
use context_system;
use core_reportbuilder\local\report\action;

/**
 * Unit tests for the system report class
 *
 * @package     core_reportbuilder
 * @covers      \core_reportbuilder\system_report
 * @copyright   2021 David Matamoros <davidmc@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class system_report_test extends advanced_testcase {
    /**
     * Test for actions
     */
    public function test_actions(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");

        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance(),
            '', '', 0, ['withactions' => true]);
        $actions = $systemreport->get_actions();
        $this->assertCount(1, $actions);
        $action = reset($actions);
        $this->assertInstanceOf(action::class, $action);

        $systemreport->add_action($action);
        $actions = $systemreport->get_actions();
        $this->assertCount(2, $actions);
        $this->assertTrue($systemreport->has_actions());
    }

    /**
     * Test for parameters
     */
    public function test_parameters(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");

        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance(),
            '', '', 0, ['withactions' => true]);

        $this->assertEquals(['withactions' => true], $systemreport->get_parameters());

        $systemreport->set_parameters(['withfilters' => true, 'secondparameter' => false]);
        $this->assertEquals(['withfilters' => true, 'secondparameter' => false], $systemreport->get_parameters());

        $this->assertFalse((bool)$systemreport->get_parameter('secondparameter', true, PARAM_BOOL));
        // Get a param that does not exist.
        $this->assertEquals('3', $systemreport->get_parameter('thirdparameter', '3', PARAM_INT));
    }

    /**
     * Test for column sorting
     */
    public function test_initial_sort_column(): void {
        global $CFG;
        require_once("{$CFG->dirroot}/reportbuilder/tests/fixtures/system_report_available.php");

        $this->resetAfterTest();

        $systemreport = system_report_factory::create(system_report_available::class, context_system::instance());
        $systemreport->set_initial_sort_column('user:username', SORT_DESC);
        $column = $systemreport->get_column('user:username');

        $this->assertEquals($column, $systemreport->get_initial_sort_column());
        $this->assertEquals(SORT_DESC, $systemreport->get_initial_sort_direction());
    }
}

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

namespace qbank_columnsortorder;

defined('MOODLE_INTERNAL') || die();

use advanced_testcase;
use context_course;
use core_question\local\bank\question_edit_contexts;
use core_question\local\bank\view;
use moodle_url;
use qbank_columnsortorder\external\set_columnbank_order;

global $CFG;
require_once($CFG->dirroot . '/question/tests/fixtures/testable_core_question_column.php');
require_once($CFG->dirroot . '/question/classes/external.php');

/**
 * Test class for columnsortorder feature.
 *
 * @package    qbank_columnsortorder
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Ghaly Marc-Alexandre <marc-alexandreghaly@catalyst-ca.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \qbank_columnsortorder\column_manager
 */
class column_manager_test extends advanced_testcase {


    /** @var \stdClass course record. */
    protected $course;

    /** @var \core_question\local\bank\view  */
    protected $questionbank;

    /** @var array  */
    protected $columns;

    /** @var \qbank_columnsortorder\column_manager  */
    protected $columnmanager;

    /**
     * Setup testcase.
     */
    public function setUp(): void {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $this->course = $this->getDataGenerator()->create_course();
        // Creates question bank view.
        $this->questionbank = new view(
            new question_edit_contexts(context_course::instance($this->course->id)),
            new moodle_url('/'),
            $this->course
        );

        // Get current view columns.
        $this->columns = [];
        foreach ($this->questionbank->get_visiblecolumns() as $columnn) {
            $this->columns[] = get_class($columnn);
        }
        $this->columnmanager = new column_manager();
    }

    /**
     * Test function get_columns in helper class, that proper data is returned.
     *
     * @covers ::get_columns
     */
    public function test_getcolumns_function(): void {
        $questionlistcolumns = $this->columnmanager->get_columns();
        $this->assertIsArray($questionlistcolumns);
        foreach ($questionlistcolumns as $columnnobject) {
            $this->assertObjectHasAttribute('class', $columnnobject);
            $this->assertObjectHasAttribute('name', $columnnobject);
            $this->assertObjectHasAttribute('colname', $columnnobject);
        }
    }

    /**
     * Test function sort columns method.
     *
     * @covers ::get_sorted_columns
     */
    public function test_get_sorted_columns(): void {
        $neworder = $this->columnmanager->get_sorted_columns($this->columns);
        shuffle($neworder);
        set_columnbank_order::execute($neworder);
        $currentconfig = get_config('qbank_columnsortorder', 'enabledcol');
        $currentconfig = explode(',', $currentconfig);
        ksort($currentconfig);
        $this->assertSame($neworder, $currentconfig);
    }

    /**
     * Test function enabing and disablingcolumns.
     *
     * @covers ::enable_columns
     * @covers ::disable_columns
     */
    public function test_enable_disable_columns(): void {
        $neworder = $this->columnmanager->get_sorted_columns($this->columns);
        shuffle($neworder);
        set_columnbank_order::execute($neworder);
        $currentconfig = get_config('qbank_columnsortorder', 'enabledcol');
        $currentconfig = explode(',', $currentconfig);
        $class = $currentconfig[array_rand($currentconfig, 1)];
        $randomplugintodisable = explode('\\', $class)[0];
        $olddisabledconfig = get_config('qbank_columnsortorder', 'disabledcol');
        $this->columnmanager->disable_columns($randomplugintodisable);
        $newdisabledconfig = get_config('qbank_columnsortorder', 'disabledcol');
        $this->assertNotEquals($olddisabledconfig, $newdisabledconfig);
        $this->columnmanager->enable_columns($randomplugintodisable);
        $newdisabledconfig = get_config('qbank_columnsortorder', 'disabledcol');
        $this->assertEmpty($newdisabledconfig);
        $enabledconfig = get_config('qbank_columnsortorder', 'enabledcol');
        $contains = strpos($enabledconfig, $randomplugintodisable);
        $this->assertNotFalse($contains);
        $this->assertIsInt($contains);
    }
}

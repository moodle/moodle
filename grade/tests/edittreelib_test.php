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

/**
 * Unit tests for grade/edit/tree/lib.php.
 *
 * @package  core_grades
 * @category phpunit
 * @author   Andrew Davis
 * @license  http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot.'/grade/edit/tree/lib.php');


/**
 * Tests grade_edit_tree (deals with the data on the 'Gradebook setup' page in the gradebook)
 */
class core_grade_edittreelib_testcase extends advanced_testcase {
    public function test_format_number() {
        $numinput = array(0,   1,   1.01, '1.010', 1.2345);
        $numoutput = array(0.0, 1.0, 1.01,  1.01,   1.2345);

        for ($i = 0; $i < count($numinput); $i++) {
            $msg = 'format_number() testing '.$numinput[$i].' %s';
            $this->assertEquals(grade_edit_tree::format_number($numinput[$i]), $numoutput[$i], $msg);
        }
    }

    public function test_grade_edit_tree_column_range_get_item_cell() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        // Make some things we need.
        $scale = $this->getDataGenerator()->create_scale();
        $course = $this->getDataGenerator()->create_course();
        $assign = $this->getDataGenerator()->create_module('assign', array('course' => $course->id));
        $modulecontext = context_module::instance($assign->cmid);
        // The generator returns a dummy object, lets get the real assign object.
        $assign = new assign($modulecontext, false, false);
        $cm = $assign->get_course_module();

        // Get range column.
        $column = grade_edit_tree_column::factory('range');

        $gradeitemparams = array(
            'itemtype'     => 'mod',
            'itemmodule'   => $cm->modname,
            'iteminstance' => $cm->instance,
            'courseid'     => $cm->course,
            'itemnumber'   => 0
        );

        // Lets set the grade to something we know.
        $instance = $assign->get_instance();
        $instance->grade = 70;
        $instance->instance = $instance->id;
        $assign->update_instance($instance);

        $gradeitem = grade_item::fetch($gradeitemparams);
        $cell = $column->get_item_cell($gradeitem, array());

        $this->assertEquals(GRADE_TYPE_VALUE, $gradeitem->gradetype);
        $this->assertEquals(null, $gradeitem->scaleid);
        $this->assertEqualsWithDelta(70.0, (float) $cell->text, 0.01, "Grade text is 70");

        // Now change it to a scale.
        $instance = $assign->get_instance();
        $instance->grade = -($scale->id);
        $instance->instance = $instance->id;
        $assign->update_instance($instance);

        $gradeitem = grade_item::fetch($gradeitemparams);
        $cell = $column->get_item_cell($gradeitem, array());

        // Make the expected scale text.
        $scaleitems = null;
        $scaleitems = explode(',', $scale->scale);
        // Make sure that we expect grademax (displayed in parenthesis) be the same
        // as number of items in the scale.
        $scalestring = end($scaleitems) . ' (' .
                format_float(count($scaleitems), 2) . ')';

        $this->assertEquals(GRADE_TYPE_SCALE, $gradeitem->gradetype);
        $this->assertEquals($scale->id, $gradeitem->scaleid);
        $this->assertEquals($scalestring, $cell->text, "Grade text matches scale");

        // Now change it to no grade with gradebook feedback enabled.
        $adminconfig = $assign->get_admin_config();
        $gradebookplugin = $adminconfig->feedback_plugin_for_gradebook;
        $gradebookplugin .= '_enabled';

        $instance = $assign->get_instance();
        $instance->grade = 0;
        $instance->$gradebookplugin = 1;
        $instance->instance = $instance->id;
        $assign->update_instance($instance);

        $gradeitem = grade_item::fetch($gradeitemparams);
        $cell = $column->get_item_cell($gradeitem, array());

        $this->assertEquals(GRADE_TYPE_TEXT, $gradeitem->gradetype);
        $this->assertEquals(null, $gradeitem->scaleid);
        $this->assertEquals(' - ', $cell->text, 'Grade text matches empty value of " - "');

        // Now change it to no grade with gradebook feedback disabled.
        $instance = $assign->get_instance();
        $instance->grade = 0;
        $instance->$gradebookplugin = 0;
        $instance->instance = $instance->id;
        $assign->update_instance($instance);

        $gradeitem = grade_item::fetch($gradeitemparams);
        $cell = $column->get_item_cell($gradeitem, array());

        $this->assertEquals(GRADE_TYPE_NONE, $gradeitem->gradetype);
        $this->assertEquals(null, $gradeitem->scaleid);
    }
}



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
 * @package    core_grades
 * @category   phpunit
 * @copyright  nicolas@moodle.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/fixtures/lib.php');


class core_grade_scale_testcase extends grade_base_testcase {

    public function test_grade_scale() {
        $this->sub_test_scale_construct();
        $this->sub_test_grade_scale_insert();
        $this->sub_test_grade_scale_update();
        $this->sub_test_grade_scale_delete();
        $this->sub_test_grade_scale_fetch();
        $this->sub_test_scale_load_items();
        $this->sub_test_scale_compact_items();
    }

    protected function sub_test_scale_construct() {
        $params = new stdClass();
        $params->name        = 'unittestscale3';
        $params->courseid    = $this->course->id;
        $params->userid      = $this->userid;
        $params->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $params->description = 'This scale is used to mark standard assignments.';
        $params->timemodified = time();

        $scale = new grade_scale($params, false);

        $this->assertEquals($params->name, $scale->name);
        $this->assertEquals($params->scale, $scale->scale);
        $this->assertEquals($params->description, $scale->description);

    }

    protected function sub_test_grade_scale_insert() {
        $grade_scale = new grade_scale();
        $this->assertTrue(method_exists($grade_scale, 'insert'));

        $grade_scale->name        = 'unittestscale3';
        $grade_scale->courseid    = $this->courseid;
        $grade_scale->userid      = $this->userid;
        $grade_scale->scale       = 'Distinction, Very Good, Good, Pass, Fail';
        $grade_scale->description = 'This scale is used to mark standard assignments.';

        $grade_scale->insert();

        $last_grade_scale = end($this->scale);

        $this->assertEquals($grade_scale->id, $last_grade_scale->id + 1);
        $this->assertNotEmpty($grade_scale->timecreated);
        $this->assertNotEmpty($grade_scale->timemodified);
    }

    protected function sub_test_grade_scale_update() {
        global $DB;
        $grade_scale = new grade_scale($this->scale[1], false);
        $this->assertTrue(method_exists($grade_scale, 'update'));

        $grade_scale->name = 'Updated info for this unittest grade_scale';
        $this->assertTrue($grade_scale->update());
        $name = $DB->get_field('scale', 'name', array('id' => $this->scale[1]->id));
        $this->assertEquals($grade_scale->name, $name);
    }

    protected function sub_test_grade_scale_delete() {
        global $DB;
        $grade_scale = new grade_scale($this->scale[4], false); // Choose one we're not using elsewhere.
        $this->assertTrue(method_exists($grade_scale, 'delete'));

        $this->assertTrue($grade_scale->delete());
        $this->assertFalse($DB->get_record('scale', array('id' => $grade_scale->id)));

        // Keep the reference collection the same as what is in the database.
        unset($this->scale[4]);
    }

    protected function sub_test_grade_scale_fetch() {
        $grade_scale = new grade_scale();
        $this->assertTrue(method_exists($grade_scale, 'fetch'));

        $grade_scale = grade_scale::fetch(array('id'=>$this->scale[0]->id));
        $this->assertEquals($this->scale[0]->id, $grade_scale->id);
        $this->assertEquals($this->scale[0]->name, $grade_scale->name);
    }

    protected function sub_test_scale_load_items() {
        $scale = new grade_scale($this->scale[0], false);
        $this->assertTrue(method_exists($scale, 'load_items'));

        $scale->load_items();
        $this->assertCount(7, $scale->scale_items);
        $this->assertEquals('Fairly neutral', $scale->scale_items[2]);

    }

    protected function sub_test_scale_compact_items() {
        $scale = new grade_scale($this->scale[0], false);
        $this->assertTrue(method_exists($scale, 'compact_items'));

        $scale->load_items();
        $scale->scale = null;
        $scale->compact_items();

        // The original string and the new string may have differences in whitespace around the delimiter, and that's OK.
        $this->assertEquals(preg_replace('/\s*,\s*/', ',', $this->scale[0]->scale), $scale->scale);
    }
}

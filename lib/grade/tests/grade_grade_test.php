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


class grade_grade_testcase extends grade_base_testcase {

    public function test_grade_grade() {
        $this->sub_test_grade_grade_construct();
        $this->sub_test_grade_grade_insert();
        $this->sub_test_grade_grade_update();
        $this->sub_test_grade_grade_fetch();
        $this->sub_test_grade_grade_fetch_all();
        $this->sub_test_grade_grade_load_grade_item();
        $this->sub_test_grade_grade_standardise_score();
        $this->sub_test_grade_grade_is_locked();
        $this->sub_test_grade_grade_set_hidden();
        $this->sub_test_grade_grade_is_hidden();
    }

    protected function sub_test_grade_grade_construct() {
        $params = new stdClass();

        $params->itemid = $this->grade_items[0]->id;
        $params->userid = 1;
        $params->rawgrade = 88;
        $params->rawgrademax = 110;
        $params->rawgrademin = 18;

        $grade_grade = new grade_grade($params, false);
        $this->assertEquals($params->itemid, $grade_grade->itemid);
        $this->assertEquals($params->rawgrade, $grade_grade->rawgrade);
    }

    protected function sub_test_grade_grade_insert() {
        $grade_grade = new grade_grade();
        $this->assertTrue(method_exists($grade_grade, 'insert'));

        $grade_grade->itemid = $this->grade_items[0]->id;
        $grade_grade->userid = 10;
        $grade_grade->rawgrade = 88;
        $grade_grade->rawgrademax = 110;
        $grade_grade->rawgrademin = 18;

        // Check the grade_item's needsupdate variable first
        $grade_grade->load_grade_item();
        $this->assertEmpty($grade_grade->grade_item->needsupdate);

        $grade_grade->insert();

        $last_grade_grade = end($this->grade_grades);

        $this->assertEquals($grade_grade->id, $last_grade_grade->id + 1);

        // timecreated will only be set if the grade was submitted by an activity module
        $this->assertTrue(empty($grade_grade->timecreated));
        // timemodified will only be set if the grade was submitted by an activity module
        $this->assertTrue(empty($grade_grade->timemodified));

        //keep our collection the same as is in the database
        $this->grade_grades[] = $grade_grade;
    }

    protected function sub_test_grade_grade_update() {
        $grade_grade = new grade_grade($this->grade_grades[0], false);
        $this->assertTrue(method_exists($grade_grade, 'update'));
    }

    protected function sub_test_grade_grade_fetch() {
        $grade_grade = new grade_grade();
        $this->assertTrue(method_exists($grade_grade, 'fetch'));

        $grades = grade_grade::fetch(array('id'=>$this->grade_grades[0]->id));
        $this->assertEquals($this->grade_grades[0]->id, $grades->id);
        $this->assertEquals($this->grade_grades[0]->rawgrade, $grades->rawgrade);
    }

    protected function sub_test_grade_grade_fetch_all() {
        $grade_grade = new grade_grade();
        $this->assertTrue(method_exists($grade_grade, 'fetch_all'));

        $grades = grade_grade::fetch_all(array());
        $this->assertEquals(count($this->grade_grades), count($grades));
    }

    protected function sub_test_grade_grade_load_grade_item() {
        $grade_grade = new grade_grade($this->grade_grades[0], false);
        $this->assertTrue(method_exists($grade_grade, 'load_grade_item'));
        $this->assertNull($grade_grade->grade_item);
        $this->assertNotEmpty($grade_grade->itemid);
        $this->assertNotNull($grade_grade->load_grade_item());
        $this->assertNotNull($grade_grade->grade_item);
        $this->assertEquals($this->grade_items[0]->id, $grade_grade->grade_item->id);
    }


    protected function sub_test_grade_grade_standardise_score() {
        $this->assertEquals(4, round(grade_grade::standardise_score(6, 0, 7, 0, 5)));
        $this->assertEquals(40, grade_grade::standardise_score(50, 30, 80, 0, 100));
    }


    /*
     * Disabling this test: the set_locked() arguments have been modified, rendering these tests useless until they are re-written

    protected function test_grade_grade_set_locked() {
        $grade_item = new grade_item($this->grade_items[0]);
        $grade = new grade_grade($grade_item->get_final(1));
        $this->assertTrue(method_exists($grade, 'set_locked'));

        $this->assertTrue(empty($grade_item->locked));
        $this->assertTrue(empty($grade->locked));

        $this->assertTrue($grade->set_locked(true));
        $this->assertFalse(empty($grade->locked));
        $this->assertTrue($grade->set_locked(false));
        $this->assertTrue(empty($grade->locked));

        $this->assertTrue($grade_item->set_locked(true, true));
        $grade = new grade_grade($grade_item->get_final(1));

        $this->assertFalse(empty($grade->locked));
        $this->assertFalse($grade->set_locked(true, false));

        $this->assertTrue($grade_item->set_locked(true, false));
        $grade = new grade_grade($grade_item->get_final(1));

        $this->assertTrue($grade->set_locked(true, false));
    }
    */

    protected function sub_test_grade_grade_is_locked() {
        $grade = new grade_grade($this->grade_grades[0], false);
        $this->assertTrue(method_exists($grade, 'is_locked'));

        $this->assertFalse($grade->is_locked());
        $grade->locked = time();
        $this->assertTrue($grade->is_locked());
    }

    protected function sub_test_grade_grade_set_hidden() {
        $grade = new grade_grade($this->grade_grades[0], false);
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade, 'set_hidden'));

        $this->assertEquals(0, $grade_item->hidden);
        $this->assertEquals(0, $grade->hidden);

        $grade->set_hidden(0);
        $this->assertEquals(0, $grade->hidden);

        $grade->set_hidden(1);
        $this->assertEquals(1, $grade->hidden);

        $grade->set_hidden(0);
        $this->assertEquals(0, $grade->hidden);
    }

    protected function sub_test_grade_grade_is_hidden() {
        $grade = new grade_grade($this->grade_grades[0], false);
        $this->assertTrue(method_exists($grade, 'is_hidden'));

        $this->assertFalse($grade->is_hidden());
        $grade->hidden = 1;
        $this->assertTrue($grade->is_hidden());

        $grade->hidden = time()-666;
        $this->assertFalse($grade->is_hidden());

        $grade->hidden = time()+666;
        $this->assertTrue($grade->is_hidden());
    }
}

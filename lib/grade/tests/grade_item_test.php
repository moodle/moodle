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

class grade_item_testcase extends grade_base_testcase {
    public function test_grade_item() {
        $this->sub_test_grade_item_construct();
        $this->sub_test_grade_item_insert();
        $this->sub_test_grade_item_delete();
        $this->sub_test_grade_item_update();
        $this->sub_test_grade_item_load_scale();
        $this->sub_test_grade_item_load_outcome();
        $this->sub_test_grade_item_qualifies_for_regrading();
        $this->sub_test_grade_item_force_regrading();
        $this->sub_test_grade_item_fetch();
        $this->sub_test_grade_item_fetch_all();
        $this->sub_test_grade_item_get_all_finals();
        $this->sub_test_grade_item_get_final();
        $this->sub_test_grade_item_get_sortorder();
        $this->sub_test_grade_item_set_sortorder();
        $this->sub_test_grade_item_move_after_sortorder();
        $this->sub_test_grade_item_get_name();
        $this->sub_test_grade_item_set_parent();
        $this->sub_test_grade_item_get_parent_category();
        $this->sub_test_grade_item_load_parent_category();
        $this->sub_test_grade_item_get_item_category();
        $this->sub_test_grade_item_load_item_category();
        $this->sub_test_grade_item_regrade_final_grades();
        $this->sub_test_grade_item_adjust_raw_grade();
        $this->sub_test_grade_item_set_locked();
        $this->sub_test_grade_item_is_locked();
        $this->sub_test_grade_item_set_hidden();
        $this->sub_test_grade_item_is_hidden();
        $this->sub_test_grade_item_is_category_item();
        $this->sub_test_grade_item_is_course_item();
        $this->sub_test_grade_item_fetch_course_item();
        $this->sub_test_grade_item_depends_on();
        $this->sub_test_refresh_grades();
        $this->sub_test_grade_item_is_calculated();
        $this->sub_test_grade_item_set_calculation();
        $this->sub_test_grade_item_get_calculation();
        $this->sub_test_grade_item_compute();
        $this->sub_test_update_final_grade();
        $this->sub_test_grade_item_can_control_visibility();
    }

    protected function sub_test_grade_item_construct() {
        $params = new stdClass();

        $params->courseid = $this->courseid;
        $params->categoryid = $this->grade_categories[1]->id;
        $params->itemname = 'unittestgradeitem4';
        $params->itemtype = 'mod';
        $params->itemmodule = 'database';
        $params->iteminfo = 'Grade item used for unit testing';

        $grade_item = new grade_item($params, false);

        $this->assertEquals($params->courseid, $grade_item->courseid);
        $this->assertEquals($params->categoryid, $grade_item->categoryid);
        $this->assertEquals($params->itemmodule, $grade_item->itemmodule);
    }

    protected function sub_test_grade_item_insert() {
        $grade_item = new grade_item();
        $this->assertTrue(method_exists($grade_item, 'insert'));

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'unittestgradeitem4';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminfo = 'Grade item used for unit testing';

        $grade_item->insert();

        $last_grade_item = end($this->grade_items);

        $this->assertEquals($grade_item->id, $last_grade_item->id + 1);
        $this->assertEquals(12, $grade_item->sortorder);

        //keep our reference collection the same as what is in the database
        $this->grade_items[] = $grade_item;
    }

    protected function sub_test_grade_item_delete() {
        global $DB;
        $grade_item = new grade_item($this->grade_items[7],false);//use a grade item not touched by previous (or future) tests
        $this->assertTrue(method_exists($grade_item, 'delete'));

        $this->assertTrue($grade_item->delete());

        $this->assertFalse($DB->get_record('grade_items', array('id' => $grade_item->id)));

        //keep our reference collection the same as the database
        unset($this->grade_items[7]);
    }

    protected function sub_test_grade_item_update() {
        global $DB;
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'update'));

        $grade_item->iteminfo = 'Updated info for this unittest grade_item';

        $this->assertTrue($grade_item->update());

        $grade_item->grademin = 14;
        $this->assertTrue($grade_item->qualifies_for_regrading());
        $this->assertTrue($grade_item->update());

        $iteminfo = $DB->get_field('grade_items', 'iteminfo', array('id' => $this->grade_items[0]->id));
        $this->assertEquals($grade_item->iteminfo, $iteminfo);
    }

    protected function sub_test_grade_item_load_scale() {
        $grade_item = new grade_item($this->grade_items[2], false);
        $this->assertTrue(method_exists($grade_item, 'load_scale'));
        $scale = $grade_item->load_scale();
        $this->assertFalse(empty($grade_item->scale));
        $this->assertEquals($scale->id, $this->grade_items[2]->scaleid);
    }

    protected function sub_test_grade_item_load_outcome() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'load_outcome'));
        //TODO: add tests
    }

    protected function sub_test_grade_item_qualifies_for_regrading() {
        $grade_item = new grade_item($this->grade_items[3], false);//use a grade item not touched by previous tests
        $this->assertTrue(method_exists($grade_item, 'qualifies_for_regrading'));

        $this->assertFalse($grade_item->qualifies_for_regrading());

        $grade_item->iteminfo = 'Updated info for this unittest grade_item';

        $this->assertFalse($grade_item->qualifies_for_regrading());

        $grade_item->grademin = 14;

        $this->assertTrue($grade_item->qualifies_for_regrading());
    }

    protected function sub_test_grade_item_force_regrading() {
        $grade_item = new grade_item($this->grade_items[3], false);//use a grade item not touched by previous tests
        $this->assertTrue(method_exists($grade_item, 'force_regrading'));

        $this->assertEquals(0, $grade_item->needsupdate);

        $grade_item->force_regrading();
        $this->assertEquals(1, $grade_item->needsupdate);
        $grade_item->update_from_db();
        $this->assertEquals(1, $grade_item->needsupdate);
    }

    protected function sub_test_grade_item_fetch() {
        $grade_item = new grade_item();
        $this->assertTrue(method_exists($grade_item, 'fetch'));

        //not using $this->grade_items[0] as it's iteminfo was modified by sub_test_grade_item_qualifies_for_regrading()
        $grade_item = grade_item::fetch(array('id'=>$this->grade_items[1]->id));
        $this->assertEquals($this->grade_items[1]->id, $grade_item->id);
        $this->assertEquals($this->grade_items[1]->iteminfo, $grade_item->iteminfo);

        $grade_item = grade_item::fetch(array('itemtype'=>$this->grade_items[1]->itemtype, 'itemmodule'=>$this->grade_items[1]->itemmodule));
        $this->assertEquals($this->grade_items[1]->id, $grade_item->id);
        $this->assertEquals($this->grade_items[1]->iteminfo, $grade_item->iteminfo);
    }

    protected function sub_test_grade_item_fetch_all() {
        $grade_item = new grade_item();
        $this->assertTrue(method_exists($grade_item, 'fetch_all'));

        $grade_items = grade_item::fetch_all(array('courseid'=>$this->courseid));
        $this->assertEquals(count($this->grade_items), count($grade_items)-1);//-1 to account for the course grade item
    }

    // Retrieve all final scores for a given grade_item.
    protected function sub_test_grade_item_get_all_finals() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'get_final'));

        $final_grades = $grade_item->get_final();
        $this->assertEquals(3, count($final_grades));
    }


    // Retrieve all final scores for a specific userid.
    protected function sub_test_grade_item_get_final() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'get_final'));
        $final_grade = $grade_item->get_final($this->user[1]->id);
        $this->assertEquals($this->grade_grades[0]->finalgrade, $final_grade->finalgrade);
    }

    protected function sub_test_grade_item_get_sortorder() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'get_sortorder'));
        $sortorder = $grade_item->get_sortorder();
        $this->assertEquals($this->grade_items[0]->sortorder, $sortorder);
    }

    protected function sub_test_grade_item_set_sortorder() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'set_sortorder'));
        $grade_item->set_sortorder(999);
        $this->assertEquals($grade_item->sortorder, 999);
    }

    protected function sub_test_grade_item_move_after_sortorder() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'move_after_sortorder'));
        $grade_item->move_after_sortorder(5);
        $this->assertEquals($grade_item->sortorder, 6);

        $grade_item = grade_item::fetch(array('id'=>$this->grade_items[0]->id));
        $this->assertEquals($grade_item->sortorder, 6);

        $after = grade_item::fetch(array('id'=>$this->grade_items[6]->id));
        $this->assertEquals($after->sortorder, 8);
    }

    protected function sub_test_grade_item_get_name() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'get_name'));

        $name = $grade_item->get_name();
        $this->assertEquals($this->grade_items[0]->itemname, $name);
    }

    protected function sub_test_grade_item_set_parent() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'set_parent'));

        $old = $grade_item->get_parent_category();
        $new = new grade_category($this->grade_categories[3], false);
        $new_item = $new->get_grade_item();

        $this->assertTrue($grade_item->set_parent($new->id));

        $new_item->update_from_db();
        $grade_item->update_from_db();

        $this->assertEquals($grade_item->categoryid, $new->id);
    }

    protected function sub_test_grade_item_get_parent_category() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'get_parent_category'));

        $category = $grade_item->get_parent_category();
        $this->assertEquals($this->grade_categories[1]->fullname, $category->fullname);
    }

    protected function sub_test_grade_item_load_parent_category() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'load_parent_category'));

        $category = $grade_item->load_parent_category();
        $this->assertEquals($this->grade_categories[1]->fullname, $category->fullname);
        $this->assertEquals($this->grade_categories[1]->fullname, $grade_item->parent_category->fullname);
    }

    protected function sub_test_grade_item_get_item_category() {
        $grade_item = new grade_item($this->grade_items[3], false);
        $this->assertTrue(method_exists($grade_item, 'get_item_category'));

        $category = $grade_item->get_item_category();
        $this->assertEquals($this->grade_categories[0]->fullname, $category->fullname);
    }

    protected function sub_test_grade_item_load_item_category() {
        $grade_item = new grade_item($this->grade_items[3], false);
        $this->assertTrue(method_exists($grade_item, 'load_item_category'));

        $category = $grade_item->load_item_category();
        $this->assertEquals($this->grade_categories[0]->fullname, $category->fullname);
        $this->assertEquals($this->grade_categories[0]->fullname, $grade_item->item_category->fullname);
    }

    // Test update of all final grades
    protected function sub_test_grade_item_regrade_final_grades() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'regrade_final_grades'));
        $this->assertEquals(true, $grade_item->regrade_final_grades());
        //TODO: add more tests
    }

    // Test the adjust_raw_grade method
    protected function sub_test_grade_item_adjust_raw_grade() {
        $grade_item = new grade_item($this->grade_items[2], false); // anything but assignment module!
        $this->assertTrue(method_exists($grade_item, 'adjust_raw_grade'));

        $grade_raw = new stdClass();
        $grade_raw->rawgrade = 40;
        $grade_raw->grademax = 100;
        $grade_raw->grademin = 0;

        $grade_item->gradetype = GRADE_TYPE_VALUE;
        $grade_item->multfactor = 1;
        $grade_item->plusfactor = 0;
        $grade_item->grademax = 50;
        $grade_item->grademin = 0;

        $original_grade_raw  = clone($grade_raw);
        $original_grade_item = clone($grade_item);

        $this->assertEquals(20, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try a larger maximum grade
        $grade_item->grademax = 150;
        $grade_item->grademin = 0;
        $this->assertEquals(60, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try larger minimum grade
        $grade_item->grademin = 50;

        $this->assertEquals(90, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Rescaling from a small scale (0-50) to a larger scale (0-100)
        $grade_raw->grademax = 50;
        $grade_raw->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->grademin = 0;

        $this->assertEquals(80, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Rescaling from a small scale (0-50) to a larger scale with offset (40-100)
        $grade_item->grademax = 100;
        $grade_item->grademin = 40;

        $this->assertEquals(88, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try multfactor and plusfactor
        $grade_raw = clone($original_grade_raw);
        $grade_item = clone($original_grade_item);
        $grade_item->multfactor = 1.23;
        $grade_item->plusfactor = 3;

        $this->assertEquals(27.6, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try multfactor below 0 and a negative plusfactor
        $grade_raw = clone($original_grade_raw);
        $grade_item = clone($original_grade_item);
        $grade_item->multfactor = 0.23;
        $grade_item->plusfactor = -3;

        $this->assertEquals(round(1.6), round($grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax)));
    }

    // Test locking of grade items
    protected function sub_test_grade_item_set_locked() {
        //getting a grade_item from the DB as set_locked() will fail if the grade items needs to be updated
        //also needs to have at least one grade_grade or $grade_item->get_final(1) returns null
        //$grade_item = new grade_item($this->grade_items[8]);
        $grade_item = grade_item::fetch(array('id'=>$this->grade_items[8]->id));

        $this->assertTrue(method_exists($grade_item, 'set_locked'));

        $grade_grade = new grade_grade($grade_item->get_final($this->user[1]->id), false);
        $this->assertTrue(empty($grade_item->locked));//not locked
        $this->assertTrue(empty($grade_grade->locked));//not locked

        $this->assertTrue($grade_item->set_locked(true, true, false));
        $grade_grade = new grade_grade($grade_item->get_final($this->user[1]->id), false);

        $this->assertFalse(empty($grade_item->locked));//locked
        $this->assertFalse(empty($grade_grade->locked)); // individual grades should be locked too

        $this->assertTrue($grade_item->set_locked(false, true, false));
        $grade = new grade_grade($grade_item->get_final($this->user[1]->id), false);

        $this->assertTrue(empty($grade_item->locked));
        $this->assertTrue(empty($grade->locked)); // individual grades should be unlocked too
    }

    protected function sub_test_grade_item_is_locked() {
        $grade_item = new grade_item($this->grade_items[10], false);
        $this->assertTrue(method_exists($grade_item, 'is_locked'));

        $this->assertFalse($grade_item->is_locked());
        $this->assertFalse($grade_item->is_locked($this->user[1]->id));
        $this->assertTrue($grade_item->set_locked(true, true, false));
        $this->assertTrue($grade_item->is_locked());
        $this->assertTrue($grade_item->is_locked($this->user[1]->id));
    }

    // Test hiding of grade items
    protected function sub_test_grade_item_set_hidden() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'set_hidden'));

        $grade = new grade_grade($grade_item->get_final($this->user[1]->id), false);
        $this->assertEquals(0, $grade_item->hidden);
        $this->assertEquals(0, $grade->hidden);

        $grade_item->set_hidden(666, true);
        $grade = new grade_grade($grade_item->get_final($this->user[1]->id), false);

        $this->assertEquals(666, $grade_item->hidden);
        $this->assertEquals(666, $grade->hidden);
    }

    protected function sub_test_grade_item_is_hidden() {
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'is_hidden'));

        $this->assertFalse($grade_item->is_hidden());
        $this->assertFalse($grade_item->is_hidden(1));

        $grade_item->set_hidden(1);
        $this->assertTrue($grade_item->is_hidden());
        $this->assertTrue($grade_item->is_hidden(1));

        $grade_item->set_hidden(666);
        $this->assertFalse($grade_item->is_hidden());
        $this->assertFalse($grade_item->is_hidden(1));

        $grade_item->set_hidden(time()+666);
        $this->assertTrue($grade_item->is_hidden());
        $this->assertTrue($grade_item->is_hidden(1));
    }

    protected function sub_test_grade_item_is_category_item() {
        $grade_item = new grade_item($this->grade_items[3], false);
        $this->assertTrue(method_exists($grade_item, 'is_category_item'));
        $this->assertTrue($grade_item->is_category_item());
    }

    protected function sub_test_grade_item_is_course_item() {
        $grade_item = grade_item::fetch_course_item($this->courseid);
        $this->assertTrue(method_exists($grade_item, 'is_course_item'));
        $this->assertTrue($grade_item->is_course_item());
    }

    protected function sub_test_grade_item_fetch_course_item() {
        $grade_item = grade_item::fetch_course_item($this->courseid);
        $this->assertTrue(method_exists($grade_item, 'fetch_course_item'));
        $this->assertEquals($grade_item->itemtype, 'course');
    }

    protected function sub_test_grade_item_depends_on() {
        $grade_item = new grade_item($this->grade_items[1], false);

        // calculated grade dependency
        $deps = $grade_item->depends_on();
        sort($deps, SORT_NUMERIC); // for comparison
        $this->assertEquals(array($this->grade_items[0]->id), $deps);

        // simulate depends on returns none when locked
        $grade_item->locked = time();
        $grade_item->update();
        $deps = $grade_item->depends_on();
        sort($deps, SORT_NUMERIC); // for comparison
        $this->assertEquals(array(), $deps);

        // category dependency
        $grade_item = new grade_item($this->grade_items[3], false);
        $deps = $grade_item->depends_on();
        sort($deps, SORT_NUMERIC); // for comparison
        $res = array($this->grade_items[4]->id, $this->grade_items[5]->id);
        $this->assertEquals($res, $deps);
    }

    protected function sub_test_refresh_grades() {
        // Testing with the grade item for a mod_assignment instance.
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue(method_exists($grade_item, 'refresh_grades'));
        $this->assertTrue($grade_item->refresh_grades());

        // Break the grade item and check error handling.
        $grade_item->iteminstance = 123456789;
        $this->assertFalse($grade_item->refresh_grades());
        $this->assertDebuggingCalled();
    }

    protected function sub_test_grade_item_is_calculated() {
        $grade_item = new grade_item($this->grade_items[1], false);
        $this->assertTrue(method_exists($grade_item, 'is_calculated'));
        $this->assertTrue($grade_item->is_calculated());

        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertFalse($grade_item->is_calculated());
    }

    protected function sub_test_grade_item_set_calculation() {
        $grade_item = new grade_item($this->grade_items[1], false);
        $this->assertTrue(method_exists($grade_item, 'set_calculation'));
        $grade_itemsource = new grade_item($this->grade_items[0], false);

        $grade_item->set_calculation('=[['.$grade_itemsource->idnumber.']]');

        $this->assertTrue(!empty($grade_item->needsupdate));
        $this->assertEquals('=##gi'.$grade_itemsource->id.'##', $grade_item->calculation);
    }

    protected function sub_test_grade_item_get_calculation() {
        $grade_item = new grade_item($this->grade_items[1], false);
        $this->assertTrue(method_exists($grade_item, 'get_calculation'));
        $grade_itemsource = new grade_item($this->grade_items[0], false);

        $denormalizedformula = str_replace('##gi'.$grade_itemsource->id.'##', '[['.$grade_itemsource->idnumber.']]', $this->grade_items[1]->calculation);

        $formula = $grade_item->get_calculation();
        $this->assertTrue(!empty($grade_item->needsupdate));
        $this->assertEquals($denormalizedformula, $formula);
    }

    public function sub_test_grade_item_compute() {
        $grade_item = grade_item::fetch(array('id'=>$this->grade_items[1]->id));
        $this->assertTrue(method_exists($grade_item, 'compute'));

        //check the grade_grades in the array match those in the DB then delete $this->grade_items[1]'s grade_grades
        $this->grade_grades[3] = grade_grade::fetch(array('id'=>$this->grade_grades[3]->id));
        $grade_grade = grade_grade::fetch(array('id'=>$this->grade_grades[3]->id));
        $grade_grade->delete();

        $this->grade_grades[4] = grade_grade::fetch(array('id'=>$this->grade_grades[4]->id));
        $grade_grade = grade_grade::fetch(array('id'=>$this->grade_grades[4]->id));
        $grade_grade->delete();

        $this->grade_grades[5] = grade_grade::fetch(array('id'=>$this->grade_grades[5]->id));
        $grade_grade = grade_grade::fetch(array('id'=>$this->grade_grades[5]->id));
        $grade_grade->delete();

        //recalculate the grades (its a calculation so pulls values from other grade_items) and reinsert them
        $grade_item->compute();

        $grade_grade = grade_grade::fetch(array('userid'=>$this->grade_grades[3]->userid, 'itemid'=>$this->grade_grades[3]->itemid));
        $this->assertEquals($this->grade_grades[3]->finalgrade, $grade_grade->finalgrade);

        $grade_grade = grade_grade::fetch(array('userid'=>$this->grade_grades[4]->userid, 'itemid'=>$this->grade_grades[4]->itemid));
        $this->assertEquals($this->grade_grades[4]->finalgrade, $grade_grade->finalgrade);

        $grade_grade = grade_grade::fetch(array('userid'=>$this->grade_grades[5]->userid, 'itemid'=>$this->grade_grades[5]->itemid));
        $this->assertEquals($this->grade_grades[5]->finalgrade, $grade_grade->finalgrade);
    }

    protected function sub_test_update_final_grade() {

        // MDL-31713 Check that min and max are set on the grade_grade instance
        // if the grade is overridden before the activity has supplied a grade.
        $min = 2;
        $max = 8;

        // Create a brand new grade item.
        $grade_item = new grade_item();
        $this->assertTrue(method_exists($grade_item, 'insert'));

        $grade_item->courseid = $this->courseid;
        $grade_item->categoryid = $this->grade_categories[1]->id;
        $grade_item->itemname = 'brand new unit test grade item';
        $grade_item->itemtype = 'mod';
        $grade_item->itemmodule = 'quiz';
        $grade_item->iteminfo = 'Grade item used for unit testing';
        $grade_item->grademin = $min;
        $grade_item->grademax = $max;
        $grade_item->insert();

        // Override the student grade.
        $grade_item->update_final_grade($this->user[1]->id, 7, 'gradebook', '', FORMAT_MOODLE);

        // Check the student's grade has the correct min and max grade.
        $grade_grade = grade_grade::fetch(array('userid'=>$this->user[1]->id, 'itemid'=>$grade_item->id));
        $this->assertEquals($min, $grade_grade->rawgrademin);
        $this->assertEquals($max, $grade_grade->rawgrademax);
    }

    protected function sub_test_grade_item_can_control_visibility() {
        // Grade item 0 == Course module 0 == Assignment.
        $grade_item = new grade_item($this->grade_items[0], false);
        $this->assertTrue($grade_item->can_control_visibility());

        // Grade item  == Course module 7 == Quiz.
        $grade_item = new grade_item($this->grade_items[11], false);
        $this->assertFalse($grade_item->can_control_visibility());
    }
}

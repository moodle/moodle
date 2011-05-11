<?php

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.org                                            //
//                                                                       //
// Copyright (C) 1999 onwards Martin Dougiamas  http://dougiamas.com     //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

/**
 * Unit tests for grade_item object.
 *
 * @author nicolas@moodle.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package moodlecore
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once($CFG->libdir.'/simpletest/fixtures/gradetest.php');

Mock::generatePartial('grade_item', 'mock_grade_item_for_test_is_calculated', array('set_calculation'));

@set_time_limit(0);

class grade_item_test extends grade_test {
    function test_grade_item() {
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
        $this->sub_test_grade_item_is_calculated();
        $this->sub_test_grade_item_set_calculation();
        $this->sub_test_grade_item_get_calculation();
        $this->sub_test_grade_item_compute();
    }

    function sub_test_grade_item_construct() {
        $params = new stdClass();

        $params->courseid = $this->courseid;
        $params->categoryid = $this->grade_categories[1]->id;
        $params->itemname = 'unittestgradeitem4';
        $params->itemtype = 'mod';
        $params->itemmodule = 'database';
        $params->iteminfo = 'Grade item used for unit testing';

        $grade_item = new grade_item($params, false);

        $this->assertEqual($params->courseid, $grade_item->courseid);
        $this->assertEqual($params->categoryid, $grade_item->categoryid);
        $this->assertEqual($params->itemmodule, $grade_item->itemmodule);
    }

    function sub_test_grade_item_insert() {
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

        $this->assertEqual($grade_item->id, $last_grade_item->id + 1);
        $this->assertEqual(11, $grade_item->sortorder);

        //keep our reference collection the same as what is in the database
        $this->grade_items[] = $grade_item;
    }

    function sub_test_grade_item_delete() {
        global $DB;
        $grade_item = new grade_item($this->grade_items[7]);//use a grade item not touched by previous (or future) tests
        $this->assertTrue(method_exists($grade_item, 'delete'));

        $this->assertTrue($grade_item->delete());

        $this->assertFalse($DB->get_record('grade_items', array('id' => $grade_item->id)));

        //keep our reference collection the same as the database
        unset($this->grade_items[7]);
    }

    function sub_test_grade_item_update() {
        global $DB;
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'update'));

        $grade_item->iteminfo = 'Updated info for this unittest grade_item';

        $this->assertTrue($grade_item->update());

        $grade_item->grademin = 14;
        $this->assertTrue($grade_item->qualifies_for_regrading());
        $this->assertTrue($grade_item->update());

        $iteminfo = $DB->get_field('grade_items', 'iteminfo', array('id' => $this->grade_items[0]->id));
        $this->assertEqual($grade_item->iteminfo, $iteminfo);
    }

    function sub_test_grade_item_load_scale() {
        $grade_item = new grade_item($this->grade_items[2]);
        $this->assertTrue(method_exists($grade_item, 'load_scale'));
        $scale = $grade_item->load_scale();
        $this->assertFalse(empty($grade_item->scale));
        $this->assertEqual($scale->id, $this->grade_items[2]->scaleid);
    }

    function sub_test_grade_item_load_outcome() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'load_outcome'));
        //TODO: add tests
    }

    function sub_test_grade_item_qualifies_for_regrading() {
        $grade_item = new grade_item($this->grade_items[1]);//use a grade item not touched by previous tests
        $this->assertTrue(method_exists($grade_item, 'qualifies_for_regrading'));

        $this->assertFalse($grade_item->qualifies_for_regrading());

        $grade_item->iteminfo = 'Updated info for this unittest grade_item';

        $this->assertFalse($grade_item->qualifies_for_regrading());

        $grade_item->grademin = 14;

        $this->assertTrue($grade_item->qualifies_for_regrading());
    }

    function sub_test_grade_item_force_regrading() {
        $grade_item = new grade_item($this->grade_items[2]);//use a grade item not touched by previous tests
        $this->assertTrue(method_exists($grade_item, 'force_regrading'));

        $this->assertEqual(0, $grade_item->needsupdate);

        $grade_item->force_regrading();
        $this->assertEqual(1, $grade_item->needsupdate);
        $grade_item->update_from_db();
        $this->assertEqual(1, $grade_item->needsupdate);
    }

    function sub_test_grade_item_fetch() {
        $grade_item = new grade_item();
        $this->assertTrue(method_exists($grade_item, 'fetch'));

        //not using $this->grade_items[0] as it's iteminfo was modified by sub_test_grade_item_qualifies_for_regrading()
        $grade_item = grade_item::fetch(array('id'=>$this->grade_items[1]->id));
        $this->assertEqual($this->grade_items[1]->id, $grade_item->id);
        $this->assertEqual($this->grade_items[1]->iteminfo, $grade_item->iteminfo);

        $grade_item = grade_item::fetch(array('itemtype'=>$this->grade_items[1]->itemtype, 'itemmodule'=>$this->grade_items[1]->itemmodule));
        $this->assertEqual($this->grade_items[1]->id, $grade_item->id);
        $this->assertEqual($this->grade_items[1]->iteminfo, $grade_item->iteminfo);
    }

    function sub_test_grade_item_fetch_all() {
        $grade_item = new grade_item();
        $this->assertTrue(method_exists($grade_item, 'fetch_all'));

        $grade_items = grade_item::fetch_all(array('courseid'=>$this->courseid));
        $this->assertEqual(count($this->grade_items), count($grade_items)-1);//-1 to account for the course grade item
    }

    // Retrieve all final scores for a given grade_item.
    function sub_test_grade_item_get_all_finals() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'get_final'));

        $final_grades = $grade_item->get_final();
        $this->assertEqual(3, count($final_grades));
    }


    // Retrieve all final scores for a specific userid.
    function sub_test_grade_item_get_final() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'get_final'));
        $final_grade = $grade_item->get_final($this->userid);
        $this->assertEqual($this->grade_grades[0]->finalgrade, $final_grade->finalgrade);
    }

    function sub_test_grade_item_get_sortorder() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'get_sortorder'));
        $sortorder = $grade_item->get_sortorder();
        $this->assertEqual($this->grade_items[0]->sortorder, $sortorder);
    }

    function sub_test_grade_item_set_sortorder() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'set_sortorder'));
        $grade_item->set_sortorder(999);
        $this->assertEqual($grade_item->sortorder, 999);
    }

    function sub_test_grade_item_move_after_sortorder() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'move_after_sortorder'));
        $grade_item->move_after_sortorder(5);
        $this->assertEqual($grade_item->sortorder, 6);

        $grade_item = grade_item::fetch(array('id'=>$this->grade_items[0]->id));
        $this->assertEqual($grade_item->sortorder, 6);

        $after = grade_item::fetch(array('id'=>$this->grade_items[6]->id));
        $this->assertEqual($after->sortorder, 8);
    }

    function sub_test_grade_item_get_name() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'get_name'));

        $name = $grade_item->get_name();
        $this->assertEqual($this->grade_items[0]->itemname, $name);
    }

    function sub_test_grade_item_set_parent() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'set_parent'));

        $old = $grade_item->get_parent_category();
        $new = new grade_category($this->grade_categories[3]);
        $new_item = $new->get_grade_item();

        $this->assertTrue($grade_item->set_parent($new->id));

        $new_item->update_from_db();
        $grade_item->update_from_db();

        $this->assertEqual($grade_item->categoryid, $new->id);
    }

    function sub_test_grade_item_get_parent_category() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'get_parent_category'));

        $category = $grade_item->get_parent_category();
        $this->assertEqual($this->grade_categories[1]->fullname, $category->fullname);
    }

    function sub_test_grade_item_load_parent_category() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'load_parent_category'));

        $category = $grade_item->load_parent_category();
        $this->assertEqual($this->grade_categories[1]->fullname, $category->fullname);
        $this->assertEqual($this->grade_categories[1]->fullname, $grade_item->parent_category->fullname);
    }

    function sub_test_grade_item_get_item_category() {
        $grade_item = new grade_item($this->grade_items[3]);
        $this->assertTrue(method_exists($grade_item, 'get_item_category'));

        $category = $grade_item->get_item_category();
        $this->assertEqual($this->grade_categories[0]->fullname, $category->fullname);
    }

    function sub_test_grade_item_load_item_category() {
        $grade_item = new grade_item($this->grade_items[3]);
        $this->assertTrue(method_exists($grade_item, 'load_item_category'));

        $category = $grade_item->load_item_category();
        $this->assertEqual($this->grade_categories[0]->fullname, $category->fullname);
        $this->assertEqual($this->grade_categories[0]->fullname, $grade_item->item_category->fullname);
    }

    // Test update of all final grades
    function sub_test_grade_item_regrade_final_grades() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'regrade_final_grades'));
        $this->assertEqual(true, $grade_item->regrade_final_grades());
        //TODO: add more tests
    }

    // Test the adjust_raw_grade method
    function sub_test_grade_item_adjust_raw_grade() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'adjust_raw_grade'));
        $grade_raw = new stdClass();

        $grade_raw->rawgrade = 40;
        $grade_raw->grademax = 100;
        $grade_raw->grademin = 0;

        $grade_item->multfactor = 1;
        $grade_item->plusfactor = 0;
        $grade_item->grademax = 50;
        $grade_item->grademin = 0;

        $original_grade_raw  = clone($grade_raw);
        $original_grade_item = clone($grade_item);

        $this->assertEqual(20, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try a larger maximum grade
        $grade_item->grademax = 150;
        $grade_item->grademin = 0;
        $this->assertEqual(60, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try larger minimum grade
        $grade_item->grademin = 50;

        $this->assertEqual(90, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Rescaling from a small scale (0-50) to a larger scale (0-100)
        $grade_raw->grademax = 50;
        $grade_raw->grademin = 0;
        $grade_item->grademax = 100;
        $grade_item->grademin = 0;

        $this->assertEqual(80, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Rescaling from a small scale (0-50) to a larger scale with offset (40-100)
        $grade_item->grademax = 100;
        $grade_item->grademin = 40;

        $this->assertEqual(88, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try multfactor and plusfactor
        $grade_raw = clone($original_grade_raw);
        $grade_item = clone($original_grade_item);
        $grade_item->multfactor = 1.23;
        $grade_item->plusfactor = 3;

        $this->assertEqual(27.6, $grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax));

        // Try multfactor below 0 and a negative plusfactor
        $grade_raw = clone($original_grade_raw);
        $grade_item = clone($original_grade_item);
        $grade_item->multfactor = 0.23;
        $grade_item->plusfactor = -3;

        $this->assertEqual(round(1.6), round($grade_item->adjust_raw_grade($grade_raw->rawgrade, $grade_raw->grademin, $grade_raw->grademax)));
    }

    // Test locking of grade items
    function sub_test_grade_item_set_locked() {
        
        //getting a grade_item from the DB as set_locked() will fail if the grade items needs to be updated
        //also needs to have at least one grade_grade or $grade_item->get_final(1) returns null
        //$grade_item = new grade_item($this->grade_items[8]);
        $grade_item = grade_item::fetch(array('id'=>$this->grade_items[8]->id));

        $this->assertTrue(method_exists($grade_item, 'set_locked'));

        $grade_grade = new grade_grade($grade_item->get_final(1));
        $this->assertTrue(empty($grade_item->locked));//not locked
        $this->assertTrue(empty($grade_grade->locked));//not locked

        $this->assertTrue($grade_item->set_locked(true, true, false));
        $grade_grade = new grade_grade($grade_item->get_final(1));

        $this->assertFalse(empty($grade_item->locked));//locked
        $this->assertFalse(empty($grade_grade->locked)); // individual grades should be locked too

        $this->assertTrue($grade_item->set_locked(false, true, false));
        $grade = new grade_grade($grade_item->get_final(1));

        $this->assertTrue(empty($grade_item->locked));
        $this->assertTrue(empty($grade->locked)); // individual grades should be unlocked too
    }

    function sub_test_grade_item_is_locked() {
        $grade_item = new grade_item($this->grade_items[10]);
        $this->assertTrue(method_exists($grade_item, 'is_locked'));

        $this->assertFalse($grade_item->is_locked());
        $this->assertFalse($grade_item->is_locked(1));
        $this->assertTrue($grade_item->set_locked(true, true, false));
        $this->assertTrue($grade_item->is_locked());
        $this->assertTrue($grade_item->is_locked(1));
    }

    // Test hiding of grade items
    function sub_test_grade_item_set_hidden() {
        $grade_item = new grade_item($this->grade_items[0]);
        $this->assertTrue(method_exists($grade_item, 'set_hidden'));

        $grade = new grade_grade($grade_item->get_final(1));
        $this->assertEqual(0, $grade_item->hidden);
        $this->assertEqual(0, $grade->hidden);

        $grade_item->set_hidden(666, true);
        $grade = new grade_grade($grade_item->get_final(1));

        $this->assertEqual(666, $grade_item->hidden);
        $this->assertEqual(666, $grade->hidden);
    }

    function sub_test_grade_item_is_hidden() {
        $grade_item = new grade_item($this->grade_items[0]);
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

    function sub_test_grade_item_is_category_item() {
        $grade_item = new grade_item($this->grade_items[3]);
        $this->assertTrue(method_exists($grade_item, 'is_category_item'));
        $this->assertTrue($grade_item->is_category_item());
    }

    function sub_test_grade_item_is_course_item() {
        $grade_item = grade_item::fetch_course_item($this->courseid);
        $this->assertTrue(method_exists($grade_item, 'is_course_item'));
        $this->assertTrue($grade_item->is_course_item());
    }

    function sub_test_grade_item_fetch_course_item() {
        $grade_item = grade_item::fetch_course_item($this->courseid);
        $this->assertTrue(method_exists($grade_item, 'fetch_course_item'));
        $this->assertTrue($grade_item->itemtype, 'course');
    }

    function sub_test_grade_item_depends_on() {
        $grade_item = new grade_item($this->grade_items[1]);

        // calculated grade dependency
        $deps = $grade_item->depends_on();
        sort($deps, SORT_NUMERIC); // for comparison
        $this->assertEqual(array($this->grade_items[0]->id), $deps);

        // simulate depends on returns none when locked
        $grade_item->locked = time();
        $grade_item->update();
        $deps = $grade_item->depends_on();
        sort($deps, SORT_NUMERIC); // for comparison
        $this->assertEqual(array(), $deps);

        // category dependency
        $grade_item = new grade_item($this->grade_items[3]);
        $deps = $grade_item->depends_on();
        sort($deps, SORT_NUMERIC); // for comparison
        $res = array($this->grade_items[4]->id, $this->grade_items[5]->id);
        $this->assertEqual($res, $deps);
    }

    function sub_test_grade_item_is_calculated() {
        $grade_item = new mock_grade_item_for_test_is_calculated($this);
        $grade_item->set_properties($grade_item, $this->grade_items[1]);
        $this->assertTrue(method_exists($grade_item, 'is_calculated'));
        $grade_itemsource = new grade_item($this->grade_items[0]);
        $normalizedformula = str_replace("[[$grade_itemsource->idnumber]]", "##gi$grade_itemsource->id##", $this->grade_items[1]->calculation);

        $grade_item->expectOnce('set_calculation', array($grade_item->calculation));
        $grade_item->setReturnValue('set_calculation', $normalizedformula);
        $this->assertTrue($grade_item->is_calculated());
    }

    function sub_test_grade_item_set_calculation() {
        $grade_item = new grade_item($this->grade_items[1]);
        $this->assertTrue(method_exists($grade_item, 'set_calculation'));
        $grade_itemsource = new grade_item($this->grade_items[0]);

        $grade_item->set_calculation('=[['.$grade_itemsource->idnumber.']]');

        $this->assertTrue(!empty($grade_item->needsupdate));
        $this->assertEqual('=##gi'.$grade_itemsource->id.'##', $grade_item->calculation);
    }

    function sub_test_grade_item_get_calculation() {
        $grade_item = new grade_item($this->grade_items[1]);
        $this->assertTrue(method_exists($grade_item, 'get_calculation'));
        $grade_itemsource = new grade_item($this->grade_items[0]);

        $denormalizedformula = str_replace('##gi'.$grade_itemsource->id.'##', '[['.$grade_itemsource->idnumber.']]', $this->grade_items[1]->calculation);

        $formula = $grade_item->get_calculation();
        $this->assertTrue(!empty($grade_item->needsupdate));
        $this->assertEqual($denormalizedformula, $formula);
    }

    function sub_test_grade_item_compute() {
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
        $this->assertEqual($this->grade_grades[3]->finalgrade, $grade_grade->finalgrade);
        
        $grade_grade = grade_grade::fetch(array('userid'=>$this->grade_grades[4]->userid, 'itemid'=>$this->grade_grades[4]->itemid));
        $this->assertEqual($this->grade_grades[4]->finalgrade, $grade_grade->finalgrade);
        
        $grade_grade = grade_grade::fetch(array('userid'=>$this->grade_grades[5]->userid, 'itemid'=>$this->grade_grades[5]->itemid));
        $this->assertEqual($this->grade_grades[5]->finalgrade, $grade_grade->finalgrade);

    }

}

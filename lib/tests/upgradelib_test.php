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
 * Unit tests for the lib/upgradelib.php library.
 *
 * @package   core
 * @category  phpunit
 * @copyright 2013 onwards Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir.'/upgradelib.php');


/**
 * Tests various classes and functions in upgradelib.php library.
 */
class core_upgradelib_testcase extends advanced_testcase {

    /**
     * Test the {@link upgrade_stale_php_files_present() function
     */
    public function test_upgrade_stale_php_files_present() {
        // Just call the function, must return bool false always
        // if there aren't any old files in the codebase.
        $this->assertFalse(upgrade_stale_php_files_present());
    }

    /**
     * Test the {@link upgrade_grade_item_fix_sortorder() function with
     * faked duplicate sortorder data.
     */
    public function test_upgrade_grade_item_fix_sortorder() {
        global $DB;

        $this->resetAfterTest(true);

        // Create fake items in course1, with sortorder duplicates.
        $course1 = $this->getDataGenerator()->create_course();
        $course1item = array();
        $course1item[0] = $this->insert_fake_grade_item_sortorder($course1->id, 1);

        $course1item[1] = $this->insert_fake_grade_item_sortorder($course1->id, 2);
        $course1item[2] = $this->insert_fake_grade_item_sortorder($course1->id, 2);

        $course1item[3] = $this->insert_fake_grade_item_sortorder($course1->id, 3);
        $course1item[4] = $this->insert_fake_grade_item_sortorder($course1->id, 3);

        $course1item[5] = $this->insert_fake_grade_item_sortorder($course1->id, 4);
        $course1item[6] = $this->insert_fake_grade_item_sortorder($course1->id, 5);

        // Create fake items in course2 which need no action.
        $course2 = $this->getDataGenerator()->create_course();
        $course2item = array();
        $course2item[0] = $this->insert_fake_grade_item_sortorder($course2->id, 1);
        $course2item[1] = $this->insert_fake_grade_item_sortorder($course2->id, 2);
        $course2item[2] = $this->insert_fake_grade_item_sortorder($course2->id, 3);

        // Create a new course which only has sortorder duplicates.
        $course3 = $this->getDataGenerator()->create_course();
        $course3item = array();
        $course3item[0] = $this->insert_fake_grade_item_sortorder($course3->id, 1);
        $course3item[1] = $this->insert_fake_grade_item_sortorder($course3->id, 1);

        // A course with non-sequential sortorders and duplicates.
        $course4 = $this->getDataGenerator()->create_course();
        $course4item = array();
        $course4item[0] = $this->insert_fake_grade_item_sortorder($course4->id, 3);
        $course4item[1] = $this->insert_fake_grade_item_sortorder($course4->id, 3);

        $course4item[2] = $this->insert_fake_grade_item_sortorder($course4->id, 5);
        $course4item[3] = $this->insert_fake_grade_item_sortorder($course4->id, 6);
        $course4item[4] = $this->insert_fake_grade_item_sortorder($course4->id, 6);

        $course4item[5] = $this->insert_fake_grade_item_sortorder($course4->id, 9);
        $course4item[6] = $this->insert_fake_grade_item_sortorder($course4->id, 10);
        // Create some items with non-sequential id and sortorder relationship.
        $course4item[7] = $this->insert_fake_grade_item_sortorder($course4->id, 7);
        $course4item[8] = $this->insert_fake_grade_item_sortorder($course4->id, 8);

        $duplicatedetectionsql = "SELECT courseid, sortorder
                                    FROM {grade_items}
                                GROUP BY courseid, sortorder
                                  HAVING COUNT(id) > 1";

        // Verify there are duplicates before we start the fix.
        $dupes = $DB->record_exists_sql($duplicatedetectionsql);
        $this->assertTrue($dupes);

        // Do the work.
        upgrade_grade_item_fix_sortorder();

        // Verify that no duplicates are left in the database.
        $dupes = $DB->record_exists_sql($duplicatedetectionsql);
        $this->assertFalse($dupes);

        // Load all grade items for ease.
        $afterfixgradeitems = $DB->get_records('grade_items');

        // Verify that the duplicate sortorders have been removed from course1.
        $this->assertNotEquals($afterfixgradeitems[$course1item[1]->id]->sortorder,
            $afterfixgradeitems[$course1item[2]->id]->sortorder);
        $this->assertNotEquals($afterfixgradeitems[$course1item[3]->id]->sortorder,
            $afterfixgradeitems[$course1item[4]->id]->sortorder);
        // Verify that the order has been respected in course1.
        $this->assertGreaterThan($afterfixgradeitems[$course1item[0]->id]->sortorder,
            $afterfixgradeitems[$course1item[1]->id]->sortorder);
        $this->assertGreaterThan($afterfixgradeitems[$course1item[2]->id]->sortorder,
            $afterfixgradeitems[$course1item[3]->id]->sortorder);
        $this->assertGreaterThan($afterfixgradeitems[$course1item[3]->id]->sortorder,
            $afterfixgradeitems[$course1item[5]->id]->sortorder);
        $this->assertGreaterThan($afterfixgradeitems[$course1item[5]->id]->sortorder,
            $afterfixgradeitems[$course1item[6]->id]->sortorder);

        // Verify that no other fields have been modified in course1.
        foreach ($course1item as $originalitem) {
            $newitem = $afterfixgradeitems[$originalitem->id];

            // Ignore changes to sortorder.
            unset($originalitem->sortorder);
            unset($newitem->sortorder);

            $this->assertEquals($originalitem, $newitem);
        }

        // Verify that course2 items are completely unmodified.
        foreach ($course2item as $originalitem) {
            $newitem = $afterfixgradeitems[$originalitem->id];
            $this->assertEquals($originalitem, $newitem);
        }

        // Verify that the duplicates in course3 have been removed.
        $this->assertNotEquals($afterfixgradeitems[$course3item[0]->id]->sortorder,
            $afterfixgradeitems[$course3item[1]->id]->sortorder);

        // Verify that no other fields in course3 have been modified.
        foreach ($course3item as $originalitem) {
            $newitem = $afterfixgradeitems[$originalitem->id];

            // Ignore changes to sortorder.
            unset($originalitem->sortorder);
            unset($newitem->sortorder);

            $this->assertEquals($originalitem, $newitem);
        }

        // Verify that the duplicates in course4 have been removed.
        $this->assertNotEquals($afterfixgradeitems[$course4item[0]->id]->sortorder,
            $afterfixgradeitems[$course4item[1]->id]->sortorder);
        $this->assertNotEquals($afterfixgradeitems[$course4item[3]->id]->sortorder,
            $afterfixgradeitems[$course4item[4]->id]->sortorder);

        // Verify that the order has been respected in course4.
        $this->assertGreaterThan($afterfixgradeitems[$course4item[1]->id]->sortorder,
            $afterfixgradeitems[$course4item[2]->id]->sortorder, "2 grater than 1");
        $this->assertGreaterThan($afterfixgradeitems[$course4item[4]->id]->sortorder,
            $afterfixgradeitems[$course4item[5]->id]->sortorder);
        $this->assertGreaterThan($afterfixgradeitems[$course4item[5]->id]->sortorder,
            $afterfixgradeitems[$course4item[6]->id]->sortorder);

        // Check the items created with non-sequential id and sortorder relationship
        // are converted correclty.
        $this->assertGreaterThan($afterfixgradeitems[$course4item[7]->id]->sortorder,
            $afterfixgradeitems[$course4item[5]->id]->sortorder);
        $this->assertGreaterThan($afterfixgradeitems[$course4item[7]->id]->sortorder,
            $afterfixgradeitems[$course4item[8]->id]->sortorder);

        // Verify that no other fields in course4 have been modified.
        foreach ($course4item as $originalitem) {
            $newitem = $afterfixgradeitems[$originalitem->id];

            // Ignore changes to sortorder.
            unset($originalitem->sortorder);
            unset($newitem->sortorder);

            $this->assertEquals($originalitem, $newitem);
        }
    }

    /**
     * Populate some fake grade items into the database with specified
     * sortorder and course id.
     *
     * NOTE: This function doesn't make much attempt to respect the
     * gradebook internals, its simply used to fake some data for
     * testing the upgradelib function. Please don't use it for other
     * purposes.
     *
     * @param int $courseid id of course
     * @param int $sortorder numeric sorting order of item
     * @return stdClass grade item object from the database.
     */
    private function insert_fake_grade_item_sortorder($courseid, $sortorder) {
        global $DB, $CFG;
        require_once($CFG->libdir.'/gradelib.php');

        $item = new stdClass();
        $item->courseid = $courseid;
        $item->sortorder = $sortorder;
        $item->gradetype = GRADE_TYPE_VALUE;
        $item->grademin = 30;
        $item->grademax = 110;
        $item->itemnumber = 1;
        $item->iteminfo = '';
        $item->timecreated = time();
        $item->timemodified = time();

        $item->id = $DB->insert_record('grade_items', $item);

        return $DB->get_record('grade_items', array('id' => $item->id));
    }
}

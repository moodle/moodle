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

        // The purpose of this test is to make sure that after upgrade script
        // there is no duplicates in the field grade_items.sortorder (for each course)
        // and the result of query "SELECT id FROM grade_items WHERE courseid=? ORDER BY sortorder, id" does not change.
        $sequencesql = 'SELECT id FROM {grade_items} WHERE courseid=? ORDER BY sortorder, id';

        // Each set is used for filling the db with fake data and will be representing the result of query:
        // "SELECT sortorder from {grade_items} WHERE courseid=? ORDER BY id".
        $testsets = array(
            // Items that need no action.
            array(1,2,3),
            array(5,6,7),
            array(7,6,1,3,2,5),
            // Items with sortorder duplicates
            array(1,2,2,3,3,4,5),
            // Only one sortorder duplicate.
            array(1,1),
            array(3,3),
            // Non-sequential sortorders with one or multiple duplicates.
            array(3,3,7,5,6,6,9,10,8,3),
            array(7,7,3),
            array(3,4,5,3,5,4,7,1)
        );
        $origsequences = array();

        // Generate the data and remember the initial sequence or items.
        foreach ($testsets as $testset) {
            $course = $this->getDataGenerator()->create_course();
            foreach ($testset as $sortorder) {
                $this->insert_fake_grade_item_sortorder($course->id, $sortorder);
            }
            $DB->get_records('grade_items');
            $origsequences[$course->id] = $DB->get_fieldset_sql($sequencesql, array($course->id));
        }

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

        // Verify that sequences are exactly the same as they were before upgrade script.
        $idx = 0;
        foreach ($origsequences as $courseid => $origsequence) {
            if (count(($testsets[$idx])) == count(array_unique($testsets[$idx]))) {
                // If there were no duplicates for this course verify that sortorders are not modified.
                $newsortorders = $DB->get_fieldset_sql("SELECT sortorder from {grade_items} WHERE courseid=? ORDER BY id", array($courseid));
                $this->assertEquals($testsets[$idx], $newsortorders);
            }
            $newsequence = $DB->get_fieldset_sql($sequencesql, array($courseid));
            $this->assertEquals($origsequence, $newsequence,
                    "Sequences do not match for test set $idx : ".join(',', $testsets[$idx]));
            $idx++;
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

    public function test_upgrade_fix_missing_root_folders() {
        global $DB, $SITE;

        $this->resetAfterTest(true);

        // Setup some broken data...
        // Create two resources (and associated file areas).
        $this->setAdminUser();
        $resource1 = $this->getDataGenerator()->get_plugin_generator('mod_resource')
            ->create_instance(array('course' => $SITE->id));
        $resource2 = $this->getDataGenerator()->get_plugin_generator('mod_resource')
            ->create_instance(array('course' => $SITE->id));

        // Delete the folder record of resource1 to simulate broken data.
        $context = context_module::instance($resource1->cmid);
        $selectargs = array('contextid' => $context->id,
                            'component' => 'mod_resource',
                            'filearea' => 'content',
                            'itemid' => 0);

        // Verify file records exist.
        $areafilecount = $DB->count_records('files', $selectargs);
        $this->assertNotEmpty($areafilecount);

        // Delete the folder record.
        $folderrecord = $selectargs;
        $folderrecord['filepath'] = '/';
        $folderrecord['filename'] = '.';

        // Get previous folder record.
        $oldrecord = $DB->get_record('files', $folderrecord);
        $DB->delete_records('files', $folderrecord);

        // Verify the folder record has been removed.
        $newareafilecount = $DB->count_records('files', $selectargs);
        $this->assertSame($newareafilecount, $areafilecount - 1);

        $this->assertFalse($DB->record_exists('files', $folderrecord));

        // Run the upgrade step!
        upgrade_fix_missing_root_folders();

        // Verify the folder record has been restored.
        $newareafilecount = $DB->count_records('files', $selectargs);
        $this->assertSame($newareafilecount, $areafilecount);

        $newrecord = $DB->get_record('files', $folderrecord, '*', MUST_EXIST);
        // Verify the hash is correctly created.
        $this->assertSame($oldrecord->pathnamehash, $newrecord->pathnamehash);
    }
}

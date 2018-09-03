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
 * Assign search unit tests.
 *
 * @package     mod_assign
 * @category    test
 * @copyright   2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
require_once($CFG->dirroot . '/mod/assign/locallib.php');

/**
 * Provides the unit tests for forum search.
 *
 * @package     mod_assign
 * @category    test
 * @copyright   2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_assign_search_testcase extends advanced_testcase {

    /**
     * Test for assign file attachments.
     *
     * @return void
     */
    public function test_attach_files() {
        global $USER;

        $this->resetAfterTest(true);
        set_config('enableglobalsearch', true);

        $assignareaid = \core_search\manager::generate_areaid('mod_assign', 'activity');

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = testable_core_search::instance();

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();

        $fs = get_file_storage();
        $usercontext = context_user::instance($USER->id);

        $record = new stdClass();
        $record->course = $course->id;

        $assign = $this->getDataGenerator()->create_module('assign', $record);
        $context = context_module::instance($assign->cmid);

        // Attach the main file. We put them in the draft area, create_module will move them.
        $filerecord = array(
            'contextid' => $context->id,
            'component' => 'mod_assign',
            'filearea'  => ASSIGN_INTROATTACHMENT_FILEAREA,
            'itemid'    => 0,
            'filepath'  => '/'
        );

        // Attach 4 files.
        for ($i = 1; $i <= 4; $i++) {
            $filerecord['filename'] = 'myfile'.$i;
            $fs->create_file_from_string($filerecord, 'Test assign file '.$i);
        }

        // And a fifth in a sub-folder.
        $filerecord['filename'] = 'myfile5';
        $filerecord['filepath'] = '/subfolder/';
        $fs->create_file_from_string($filerecord, 'Test assign file 5');

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($assignareaid);
        $this->assertInstanceOf('\mod_assign\search\activity', $searcharea);

        $recordset = $searcharea->get_recordset_by_timestamp(0);
        $nrecords = 0;
        foreach ($recordset as $record) {
            $doc = $searcharea->get_document($record);
            $searcharea->attach_files($doc);
            $files = $doc->get_files();

            // Assign should return all files attached.
            $this->assertCount(5, $files);

            // We don't know the order, so get all the names, then sort, then check.
            $filenames = array();
            foreach ($files as $file) {
                $filenames[] = $file->get_filename();
            }
            sort($filenames);

            for ($i = 1; $i <= 5; $i++) {
                $this->assertEquals('myfile'.$i, $filenames[($i - 1)]);
            }

            $nrecords++;
        }

        // If there would be an error/failure in the foreach above the recordset would be closed on shutdown.
        $recordset->close();
        $this->assertEquals(1, $nrecords);
    }

}

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
 * Resource search unit tests.
 *
 * @package     mod_resource
 * @category    test
 * @copyright   2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');

/**
 * Provides the unit tests for forum search.
 *
 * @package     mod_resource
 * @category    test
 * @copyright   2016 Eric Merrill {@link http://www.merrilldigital.com}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_resource_search_testcase extends advanced_testcase {

    /**
     * @var string Area id
     */
    protected $resourceareaid = null;

    public function setUp() {
        $this->resetAfterTest(true);
        set_config('enableglobalsearch', true);

        $this->resourceareaid = \core_search\manager::generate_areaid('mod_resource', 'activity');

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = testable_core_search::instance();
    }

    /**
     * Test for resource file attachments.
     *
     * @return void
     */
    public function test_attach_files() {
        global $USER;

        $this->setAdminUser();
        // Setup test data.
        $course = $this->getDataGenerator()->create_course();

        $fs = get_file_storage();
        $usercontext = context_user::instance($USER->id);

        $record = new stdClass();
        $record->course = $course->id;
        $record->files = file_get_unused_draft_itemid();

        // Attach the main file. We put them in the draft area, create_module will move them.
        $filerecord = array(
            'contextid' => $usercontext->id,
            'component' => 'user',
            'filearea'  => 'draft',
            'itemid'    => $record->files,
            'filepath'  => '/',
            'filename'  => 'mainfile',
            'sortorder' => 1
        );
        $fs->create_file_from_string($filerecord, 'Test resource file');

        // Attach a second file.
        $filerecord['filename'] = 'extrafile';
        $filerecord['sortorder'] = 0;
        $fs->create_file_from_string($filerecord, 'Test resource file 2');

        $resource = $this->getDataGenerator()->create_module('resource', $record);

        $searcharea = \core_search\manager::get_search_area($this->resourceareaid);
        $this->assertInstanceOf('\mod_resource\search\activity', $searcharea);

        $recordset = $searcharea->get_recordset_by_timestamp(0);
        $nrecords = 0;
        foreach ($recordset as $record) {
            $doc = $searcharea->get_document($record);
            $searcharea->attach_files($doc);
            $files = $doc->get_files();

            // Resources should return all added files.
            $this->assertCount(2, $files);

            $filenames = array();
            foreach ($files as $file) {
                $filenames[] = $file->get_filename();
            }
            $this->assertContains('mainfile', $filenames);
            $this->assertContains('extrafile', $filenames);

            $nrecords++;
        }

        $recordset->close();
        $this->assertEquals(1, $nrecords);
    }

}

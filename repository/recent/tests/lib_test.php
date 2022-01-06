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
 * Unit test for recent repository
 *
 * @package repository_recent
 *
 * @author  Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/repository/lib.php');
require_once($CFG->dirroot . '/files/externallib.php');
/**
 * Unit test for recent repository
 *
 * @package repository_recent
 *
 * @author  Nathan Nguyen <nathannguyen@catalyst-au.net>
 * @copyright  Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class repository_recent_lib_testcase extends advanced_testcase {

    /** @var repository Recent repository */
    private $repo;

    /** @var context repository */
    private $usercontext;

    /**
     * SetUp to create an repository instance.
     */
    protected function setUp(): void {
        global $USER;
        $this->setAdminUser();
        $this->usercontext = context_user::instance($USER->id);
        $repoid = $this->getDataGenerator()->create_repository('recent')->id;
        $this->repo = repository::get_repository_by_id($repoid, $this->usercontext);
    }

    /**
     * Test get listing
     */
    public function test_get_listing_with_duplicate_file() {
        global $itemid;
        $this->resetAfterTest(true);

        // Set global itemid for draft file (file manager mockup).
        $itemid = file_get_unused_draft_itemid();

        // No recent file.
        $filelist = $this->repo->get_listing()['list'];
        $this->assertCount(0, $filelist);

        // Create test file 1.
        $this->create_test_file('TestFile1', 'draft', $itemid);
        $filelist = $this->repo->get_listing()['list'];
        $this->assertCount(1, $filelist);

        // Due to create_test_file function, same filename means same content as the content is the filename hash.
        $this->create_test_file('TestFile1', 'private');
        $filelist = $this->repo->get_listing()['list'];
        $this->assertCount(1, $filelist);

        // Create test file 2, different area.
        $this->create_test_file('TestFile2', 'private');
        $filelist = $this->repo->get_listing()['list'];
        $this->assertCount(2, $filelist);
    }

    /**
     * Test get listing reference file
     */
    public function test_get_listing_with_reference_file() {
        $this->resetAfterTest(true);
        // Create test file 1.
        $file1 = $this->create_test_file('TestFile1', 'private');
        $filelist = $this->repo->get_listing()['list'];
        $this->assertCount(1, $filelist);

        // Create reference file.
        $file2 = $this->create_reference_file($file1, 'TestFile2', 'private');
        $filelist = $this->repo->get_listing()['list'];
        $this->assertCount(1, $filelist);

        // Delete reference.
        $file2->delete_reference();
        $filelist = $this->repo->get_listing()['list'];
        $this->assertCount(2, $filelist);
    }

    /**
     * Test number limit
     */
    public function test_get_listing_number_limit() {
        $this->resetAfterTest(true);
        $this->create_multiple_test_files('private', 75);
        $filelist = $this->repo->get_listing()['list'];
        $this->assertCount(50, $filelist);

        // The number limit is set as property of the repo, so we need to create new repo instance.
        set_config('recentfilesnumber', 100, 'recent');
        $repoid = $this->getDataGenerator()->create_repository('recent')->id;
        $repo = repository::get_repository_by_id($repoid, $this->usercontext);
        $filelist = $repo->get_listing()['list'];
        $this->assertCount(75, $filelist);
    }

    /**
     * Test time limit
     */
    public function test_get_listing_time_limit() {
        $this->resetAfterTest(true);
        $this->create_multiple_test_files('private', 25);
        $file1 = $this->create_test_file('TestFileTimeLimit', 'private');
        // Set time modified back to a year ago.
        $file1->set_timemodified(time() - YEARSECS);

        // There is no time limit by default.
        $filelist = $this->repo->get_listing()['list'];
        $this->assertCount(26, $filelist);

        // The time limit is set as property of the repo, so we need to create new repo instance.
        set_config('recentfilestimelimit', 3600, 'recent');
        $repoid = $this->getDataGenerator()->create_repository('recent')->id;
        $repo = repository::get_repository_by_id($repoid, $this->usercontext);
        $filelist = $repo->get_listing()['list'];
        // Only get the recent files in the last hour.
        $this->assertCount(25, $filelist);
    }

    /**
     * Create multiple test file
     *
     * @param string $filearea file area
     * @param int $numberoffiles number of files to be created
     */
    private function create_multiple_test_files($filearea, $numberoffiles) {
        for ($i = 0; $i < $numberoffiles; ++$i) {
            $filename = "TestFile$i" . time();
            $this->create_test_file($filename, $filearea);
        }
    }

    /**
     * Create test file
     *
     * @param string $filename file name
     * @param string $filearea file area
     * @param int $itemid item id
     * @return stored_file the newly created file
     */
    private function create_test_file($filename, $filearea, $itemid = 0) {
        global $USER;

        $filerecord = array();
        $filerecord['contextid'] = $this->usercontext->id;
        $filerecord['component'] = 'user';
        $filerecord['filearea'] = $filearea;
        $filerecord['itemid'] = $itemid;
        $filerecord['filepath'] = '/';
        $filerecord['filename'] = $filename;
        $filerecord['userid'] = $USER->id;

        $fs = get_file_storage();
        $content = hash("md5", $filename);
        return $fs->create_file_from_string($filerecord, $content);
    }

    /**
     * Create reference file
     *
     * @param stored_file $file source file
     * @param string $filename file name
     * @param string $filearea file area
     * @param int $itemid item id
     * @return stored_file the newly created file
     */
    private function create_reference_file($file, $filename, $filearea, $itemid = 0) {
        global $USER, $DB;

        $newfilerecord = array();
        $newfilerecord['contextid'] = $this->usercontext->id;
        $newfilerecord['component'] = 'user';
        $newfilerecord['filearea'] = $filearea;
        $newfilerecord['itemid'] = $itemid;
        $newfilerecord['filepath'] = '/';
        $newfilerecord['filename'] = $filename;
        $newfilerecord['userid'] = $USER->id;

        $fs = get_file_storage();
        $oldfilerecord = $DB->get_record('files', ['id' => $file->get_id()]);
        $ref = $fs->pack_reference($oldfilerecord);
        return $fs->create_file_from_reference($newfilerecord, $this->repo->id, $ref);
    }
}

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
 * mod_h5pactivity generator tests
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use mod_h5pactivity\local\manager;

/**
 * Genarator tests class for mod_h5pactivity.
 *
 * @package    mod_h5pactivity
 * @category   test
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_h5pactivity_generator_testcase extends advanced_testcase {

    /**
     * Test on H5P activity creation.
     */
    public function test_create_instance() {
        global $DB, $CFG, $USER;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Create one activity.
        $this->assertFalse($DB->record_exists('h5pactivity', ['course' => $course->id]));
        $activity = $this->getDataGenerator()->create_module('h5pactivity', ['course' => $course]);
        $records = $DB->get_records('h5pactivity', ['course' => $course->id], 'id');
        $this->assertEquals(15, $activity->displayoptions);
        $this->assertEquals(1, count($records));
        $this->assertTrue(array_key_exists($activity->id, $records));

        // Create a second one with different name and dusplay options.
        $params = [
            'course' => $course->id, 'name' => 'Another h5pactivity', 'displayoptions' => 6,
            'enabletracking' => 0, 'grademethod' => manager::GRADELASTATTEMPT,
        ];
        $activity = $this->getDataGenerator()->create_module('h5pactivity', $params);
        $records = $DB->get_records('h5pactivity', ['course' => $course->id], 'id');
        $this->assertEquals(6, $activity->displayoptions);
        $this->assertEquals(0, $activity->enabletracking);
        $this->assertEquals(manager::GRADELASTATTEMPT, $activity->grademethod);
        $this->assertEquals(manager::REVIEWCOMPLETION, $activity->reviewmode);
        $this->assertEquals(2, count($records));
        $this->assertEquals('Another h5pactivity', $records[$activity->id]->name);

        // Examples of specifying the package file (do not validate anything, just check for exceptions).
        // 1. As path to the file in filesystem.
        $params = [
            'course' => $course->id,
            'packagefilepath' => $CFG->dirroot.'/h5p/tests/fixtures/filltheblanks.h5p'
        ];
        $activity = $this->getDataGenerator()->create_module('h5pactivity', $params);

        // 2. As file draft area id.
        $fs = get_file_storage();
        $params = [
            'course' => $course->id,
            'packagefile' => file_get_unused_draft_itemid()
        ];
        $usercontext = context_user::instance($USER->id);
        $filerecord = ['component' => 'user', 'filearea' => 'draft',
                'contextid' => $usercontext->id, 'itemid' => $params['packagefile'],
                'filename' => 'singlescobasic.zip', 'filepath' => '/'];
        $filepath = $CFG->dirroot.'/h5p/tests/fixtures/filltheblanks.h5p';
        $fs->create_file_from_pathname($filerecord, $filepath);
        $activity = $this->getDataGenerator()->create_module('h5pactivity', $params);
    }

    /**
     * Test that a new H5P activity cannot be generated without a valid file
     * other user.
     */
    public function test_create_file_exception() {
        global $CFG;
        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Testing generator exceptions.
        $params = [
            'course' => $course->id,
            'packagefilepath' => $CFG->dirroot.'/h5p/tests/fixtures/wrong_file_.xxx'
        ];
        $this->expectException(coding_exception::class);
        $activity = $this->getDataGenerator()->create_module('h5pactivity', $params);
    }
}

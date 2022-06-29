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

namespace core_course;

use core_course\external\course_summary_exporter;
use context_user;
use context_course;

/**
 * Functional test for class course_summary_exporter
 *
 * @package    core
 * @subpackage course
 * @author     Dmitrii Metelkin <dmitriim@catalyst-au.net>
 * @copyright  2021 Catalyst IT
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course_summary_exporter_test extends \advanced_testcase {

    /**
     * Test that if no course overview images uploaded get_course_image returns false.
     */
    public function test_get_course_image_when_no_overview_images_uploaded() {
        $this->resetAfterTest(true);
        $this->setAdminUser();
        $course = $this->getDataGenerator()->create_course();

        $this->assertFalse(course_summary_exporter::get_course_image($course));
    }

    /**
     * Test that if course overview images uploaded get_course_image returns an image URL.
     */
    public function test_get_course_image_when_overview_images_are_uploaded() {
        global $USER;

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $draftid = file_get_unused_draft_itemid();
        $filerecord = [
            'component' => 'user',
            'filearea' => 'draft',
            'contextid' => context_user::instance($USER->id)->id,
            'itemid' => $draftid,
            'filename' => 'image.jpg',
            'filepath' => '/',
        ];
        $fs = get_file_storage();
        $fs->create_file_from_string($filerecord, file_get_contents(__DIR__ . '/fixtures/image.jpg'));
        $course = $this->getDataGenerator()->create_course(['overviewfiles_filemanager' => $draftid]);
        $coursecontext = context_course::instance($course->id);

        $expected = 'https://www.example.com/moodle/pluginfile.php/' . $coursecontext->id . '/course/overviewfiles/image.jpg';
        $actual = course_summary_exporter::get_course_image($course);
        $this->assertSame($expected, $actual);
    }

}

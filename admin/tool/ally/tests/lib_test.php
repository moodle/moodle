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
 * Tests for main lib.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_ally;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__.'/abstract_testcase.php');

/**
 * Tests for observer.
 *
 * @package   tool_ally
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lib_test extends abstract_testcase {
    protected function setUp(): void {
        // Prevent it from creating a backup of the deleted module.
        set_config('coursebinenable', 0, 'tool_recyclebin');
    }

    /**
     * Test file deletion callback.
     */
    public function test_tool_ally_after_file_deleted() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course   = $this->getDataGenerator()->create_course();
        $resource = $this->getDataGenerator()->create_module('resource', ['course' => $course->id]);
        $file     = $this->get_resource_file($resource);
        $time     = time();

        course_delete_module($resource->cmid);

        $deletes = $DB->get_records('tool_ally_deleted_files');

        $this->assertCount(1, $deletes);

        $delete = current($deletes);

        $this->assertEquals($course->id, $delete->courseid);
        $this->assertEquals($file->get_pathnamehash(), $delete->pathnamehash);
        $this->assertEquals($file->get_contenthash(), $delete->contenthash);
        $this->assertEquals($file->get_mimetype(), $delete->mimetype);
        $this->assertGreaterThanOrEqual($time, $delete->timedeleted);
    }

    /**
     * Test section deletion callback.
     */
    public function test_tool_ally_after_section_deleted() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminUser();

        $course = $this->getDataGenerator()->create_course();

        // Add file to a soon to be deleted section.
        $section = $this->getDataGenerator()->create_course_section(
            ['section' => 1, 'course' => $course->id]);
        $coursectx = \context_course::instance($course->id);
        $filename = 'shouldbeanimage.jpg';
        $filecontents = 'image contents (not really)';
        // Add a fake inline image to the post.
        $filerecordinline = array(
            'contextid' => $coursectx->id,
            'component' => 'course',
            'filearea'  => 'section',
            'itemid'    => $section->id,
            'filepath'  => '/',
            'filename'  => $filename,
        );
        $fs = get_file_storage();
        // This file should not appear in the service returned files if section is deleted.
        $file = $fs->create_file_from_string($filerecordinline, $filecontents);
        $time = time();

        course_delete_section($course->id, 1, true);

        $deletes = $DB->get_records('tool_ally_deleted_files');

        $this->assertCount(1, $deletes);

        $delete = current($deletes);

        $this->assertEquals($course->id, $delete->courseid);
        $this->assertEquals($file->get_pathnamehash(), $delete->pathnamehash);
        $this->assertEquals($file->get_contenthash(), $delete->contenthash);
        $this->assertEquals($file->get_mimetype(), $delete->mimetype);
        $this->assertGreaterThanOrEqual($time, $delete->timedeleted);
    }
}

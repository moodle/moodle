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
 * Test for H5P content bank plugin.
 *
 * @package    contenttype_h5p
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Test for H5P content bank plugin.
 *
 * @package    contenttype_h5p
 * @category   test
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \contenttype_h5p\contenttype
 */
class contenttype_h5p_contenttype_plugin_testcase extends advanced_testcase {

    /**
     * Tests can_upload behavior.
     *
     * @covers ::can_upload
     */
    public function test_can_upload() {
        $this->resetAfterTest();

        $systemcontext = \context_system::instance();
        $systemtype = new \contenttype_h5p\contenttype($systemcontext);

        // Admins can upload.
        $this->setAdminUser();
        $this->assertTrue($systemtype->can_upload());

        // Teacher can upload in the course but not at system level.
        $course = $this->getDataGenerator()->create_course();
        $teacher = $this->getDataGenerator()->create_and_enrol($course, 'editingteacher');
        $coursecontext = \context_course::instance($course->id);
        $coursetype = new \contenttype_h5p\contenttype($coursecontext);
        $this->setUser($teacher);
        $this->assertTrue($coursetype->can_upload());
        $this->assertFalse($systemtype->can_upload());

        // Users can't upload.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $this->assertFalse($coursetype->can_upload());
        $this->assertFalse($systemtype->can_upload());
    }
}

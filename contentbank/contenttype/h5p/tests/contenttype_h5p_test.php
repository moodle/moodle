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
     * Test the behaviour of delete_content().
     */
    public function test_delete_content() {
        global $CFG, $USER, $DB;

        $this->resetAfterTest();
        $systemcontext = context_system::instance();

        // Create users.
        $roleid = $DB->get_field('role', 'id', array('shortname' => 'manager'));
        $manager = $this->getDataGenerator()->create_user();
        $this->getDataGenerator()->role_assign($roleid, $manager->id);
        $this->setUser($manager);

        // Add an H5P file to the content bank.
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/filltheblanks.h5p';
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_h5p', 2, $USER->id, $systemcontext, true, $filepath);
        $content1 = array_shift($contents);
        $content2 = array_shift($contents);

        // Load this H5P file though the player to create the H5P DB entries.
        $h5pplayer = new \core_h5p\player($content1->get_file_url(), new \stdClass(), true);
        $h5pplayer->add_assets_to_page();
        $h5pplayer->output();
        $h5pplayer = new \core_h5p\player($content2->get_file_url(), new \stdClass(), true);
        $h5pplayer->add_assets_to_page();
        $h5pplayer->output();

        // Check the H5P content has been created.
        $this->assertEquals(2, $DB->count_records('h5p'));
        $this->assertEquals(2, $DB->count_records('contentbank_content'));

        // Check the H5P content is removed after calling this method.
        $contenttype = new \contenttype_h5p\contenttype($systemcontext);
        $contenttype->delete_content($content1);
        $this->assertEquals(1, $DB->count_records('h5p'));
        $this->assertEquals(1, $DB->count_records('contentbank_content'));
    }

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

    /**
     * Tests get_icon result.
     *
     * @covers ::get_icon
     */
    public function test_get_icon() {
        global $CFG;

        $this->resetAfterTest();
        $systemcontext = context_system::instance();
        $this->setAdminUser();
        $contenttype = new contenttype_h5p\contenttype($systemcontext);

        // Add an H5P fill the blanks file to the content bank.
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/filltheblanks.h5p';
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_h5p', 1, 0, $systemcontext, true, $filepath);
        $filltheblanks = array_shift($contents);

        // Add an H5P find the words file to the content bank.
        $filepath = $CFG->dirroot . '/h5p/tests/fixtures/find-the-words.h5p';
        $generator = $this->getDataGenerator()->get_plugin_generator('core_contentbank');
        $contents = $generator->generate_contentbank_data('contenttype_h5p', 1, 0, $systemcontext, true, $filepath);
        $findethewords = array_shift($contents);

        // Check before deploying the icon for both contents is the same: default one.
        // Because we don't know specific H5P content type yet.
        $defaulticon = $contenttype->get_icon($filltheblanks);
        $this->assertEquals($defaulticon, $contenttype->get_icon($findethewords));
        $this->assertContains('h5p', $defaulticon);

        // Deploy one of the contents though the player to create the H5P DB entries and know specific content type.
        $h5pplayer = new \core_h5p\player($findethewords->get_file_url(), new \stdClass(), true);
        $h5pplayer->add_assets_to_page();
        $h5pplayer->output();

        // Once the H5P has been deployed, we know the specific H5P content type, so the icon returned is not default one.
        $findicon = $contenttype->get_icon($findethewords);
        $this->assertNotEquals($defaulticon, $findicon);
        $this->assertContains('find', $findicon, '', true);
    }
}

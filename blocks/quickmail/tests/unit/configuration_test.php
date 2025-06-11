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
 * @package    block_quickmail
 * @copyright  2008 onwards Louisiana State University
 * @copyright  2008 onwards Chad Mazilly, Robert Russo, Jason Peak, Dave Elliott, Adam Zapletal, Philip Cali
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(dirname(__FILE__) . '/traits/unit_testcase_traits.php');

class block_quickmail_configuration_testcase extends advanced_testcase {

    use has_general_helpers,
        sets_up_courses;

    public function test_fetches_block_config_as_array() {
        $this->resetAfterTest(true);

        $config = block_quickmail_config::block();

        $this->assertIsArray($config);
    }

    public function test_fetches_course_config_as_array() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $config = block_quickmail_config::course($course);

        $this->assertIsArray($config);
    }

    public function test_fetches_course_id_config_as_array() {
        $this->resetAfterTest(true);

        $course = $this->getDataGenerator()->create_course();

        $config = block_quickmail_config::course($course->id);

        $this->assertIsArray($config);
    }

    public function test_fetches_role_selection_setting_as_array() {
        $this->resetAfterTest(true);

        // Get default block setting - 3,4,5.
        $setting = block_quickmail_config::get_role_selection_array();

        $this->assertIsArray($setting);
        $this->assertCount(3, $setting);
        $this->assertContains('3', $setting);
        $this->assertContains('4', $setting);
        $this->assertContains('5', $setting);

        // Get default course setting - 3,4,5.
        $course = $this->getDataGenerator()->create_course();

        $setting = block_quickmail_config::get_role_selection_array($course);

        $this->assertIsArray($setting);
        $this->assertCount(3, $setting);
        $this->assertContains('3', $setting);
        $this->assertContains('4', $setting);
        $this->assertContains('5', $setting);

        // Update the course's settings.
        $newparams = [
            'allowstudents' => '1',
            'roleselection' => '1,2',
            'receipt' => '1',
            'prepend_class' => 'fullname',
            'ferpa' => 'noferpa',
            'downloads' => '1',
            'allow_mentor_copy' => '1',
            'additionalemail' => '1',
            'default_message_type' => 'email',
            'message_types_available' => 'email',
            'send_now_threshold' => '32',
        ];

        // Update the courses config.
        block_quickmail_config::update_course_config($course, $newparams);

        $setting = block_quickmail_config::get_role_selection_array($course);

        $this->assertIsArray($setting);
        $this->assertCount(2, $setting);
        $this->assertContains('1', $setting);
        $this->assertContains('2', $setting);
    }

    public function test_reports_course_ferpa_strictness() {
        $this->resetAfterTest(true);

        // Create course with default setting (strictferpa).
        $course = $this->getDataGenerator()->create_course();

        $bestrict = block_quickmail_config::be_ferpa_strict_for_course($course);

        $this->assertTrue($bestrict);
    }

    public function test_updates_a_courses_config() {
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $defaultparams = block_quickmail_config::block('', false);

        $newparams = [
            'allowstudents' => '1',
            'roleselection' => '1,2',
            'receipt' => '1',
            'allow_mentor_copy' => '1',
            'prepend_class' => 'fullname',
            'ferpa' => 'noferpa',
            'downloads' => '1',
            'additionalemail' => '1',
            'default_message_type' => 'message',
            'message_types_available' => 'all',
            'send_now_threshold' => '32',
        ];

        // Update the courses config.
        block_quickmail_config::update_course_config($course, $newparams);

        // Get the courses new config.
        $courseconfig = block_quickmail_config::course($course, '', false);

        // Check attributes that CAN be changed by a course.
        $this->assertNotEquals($defaultparams['allowstudents'], $courseconfig['allowstudents']);
        $this->assertNotEquals($defaultparams['roleselection'], $courseconfig['roleselection']);
        $this->assertNotEquals($defaultparams['receipt'], $courseconfig['receipt']);
        $this->assertNotEquals($defaultparams['prepend_class'], $courseconfig['prepend_class']);

        // Check attributes that CANNOT be changed by a course (only changed at system level).
        $this->assertEquals($defaultparams['ferpa'], $courseconfig['ferpa']);
        $this->assertEquals($defaultparams['downloads'], $courseconfig['downloads']);
        $this->assertEquals($defaultparams['allow_mentor_copy'], $courseconfig['allow_mentor_copy']);
        $this->assertEquals($defaultparams['additionalemail'], $courseconfig['additionalemail']);
        $this->assertEquals($defaultparams['message_types_available'], $courseconfig['message_types_available']);
        $this->assertEquals($defaultparams['send_now_threshold'], $courseconfig['send_now_threshold']);
    }

    public function test_restores_a_courses_config_to_default() {
        $this->resetAfterTest(true);

        // Set up a course with a teacher and students.
        list($course, $userteacher, $userstudents) = $this->setup_course_with_teacher_and_students();

        $defaultparams = block_quickmail_config::block('', false);

        $newparams = [
            'allowstudents' => '1',
            'roleselection' => '1,2',
            'receipt' => '1',
            'prepend_class' => 'fullname',
            'ferpa' => 'noferpa',
            'downloads' => '1',
            'additionalemail' => '1',
            'default_message_type' => 'email',
            'message_types_available' => 'email',
        ];

        // Update the courses config.
        block_quickmail_config::update_course_config($course, $newparams);

        // Get the courses new config.
        $courseconfig = block_quickmail_config::course($course, '', false);

        // Check attributes that CAN be changed by a course.
        $this->assertNotEquals($defaultparams['allowstudents'], $courseconfig['allowstudents']);
        $this->assertNotEquals($defaultparams['roleselection'], $courseconfig['roleselection']);
        $this->assertNotEquals($defaultparams['receipt'], $courseconfig['receipt']);
        $this->assertNotEquals($defaultparams['prepend_class'], $courseconfig['prepend_class']);

        // Restore to default config.
        block_quickmail_config::delete_course_config($course);

        // Get the courses new (default) config.
        $courseconfig = block_quickmail_config::course($course, '', false);

        $this->assertEquals($defaultparams['allowstudents'], $courseconfig['allowstudents']);
        $this->assertEquals($defaultparams['roleselection'], $courseconfig['roleselection']);
        $this->assertEquals($defaultparams['receipt'], $courseconfig['receipt']);
        $this->assertEquals($defaultparams['prepend_class'], $courseconfig['prepend_class']);
        $this->assertEquals($defaultparams['default_message_type'], $courseconfig['default_message_type']);
        $this->assertEquals($defaultparams['ferpa'], $courseconfig['ferpa']);
        $this->assertEquals($defaultparams['downloads'], $courseconfig['downloads']);
        $this->assertEquals($defaultparams['allow_mentor_copy'], $courseconfig['allow_mentor_copy']);
        $this->assertEquals($defaultparams['additionalemail'], $courseconfig['additionalemail']);
        $this->assertEquals($defaultparams['message_types_available'], $courseconfig['message_types_available']);
        $this->assertEquals($defaultparams['send_now_threshold'], $courseconfig['send_now_threshold']);
    }

}

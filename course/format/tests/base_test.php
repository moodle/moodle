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

defined('MOODLE_INTERNAL') || die();

/**
 * Course related unit tests
 *
 * @package    core_course
 * @copyright  2014 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class base_test extends advanced_testcase {

    /**
     * Setup to ensure that fixtures are loaded.
     */
    public static function setupBeforeClass(): void {
        global $CFG;
        require_once($CFG->dirroot . '/course/lib.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest_output_course_format_state.php');
        require_once($CFG->dirroot . '/course/format/tests/fixtures/format_theunittest_output_course_format_invalidoutput.php');
    }

    public function test_available_hook() {
        global $DB;
        $this->resetAfterTest();

        // Generate a course with two sections (0 and 1) and two modules. Course format is set to 'theunittest'.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course(array('format' => 'theunittest'));
        $this->assertEquals('theunittest', $course1->format);
        course_create_sections_if_missing($course1, array(0, 1));
        $assign0 = $generator->create_module('assign', array('course' => $course1, 'section' => 0));
        $assign1 = $generator->create_module('assign', array('course' => $course1, 'section' => 1));
        $assign2 = $generator->create_module('assign', array('course' => $course1, 'section' => 0, 'visible' => 0));

        // Create a courseoverview role based on the student role.
        $roleattr = array('name' => 'courseoverview', 'shortname' => 'courseoverview', 'archetype' => 'student');
        $generator->create_role($roleattr);

        // Create user student, editingteacher, teacher and courseoverview.
        $student = $generator->create_user();
        $teacher = $generator->create_user();
        $editingteacher = $generator->create_user();
        $courseoverviewuser = $generator->create_user();

        // Enrol users into their roles.
        $roleids = $DB->get_records_menu('role', null, '', 'shortname, id');
        $generator->enrol_user($student->id, $course1->id, $roleids['student']);
        $generator->enrol_user($teacher->id, $course1->id, $roleids['teacher']);
        $generator->enrol_user($editingteacher->id, $course1->id, $roleids['editingteacher']);
        $generator->enrol_user($courseoverviewuser->id, $course1->id, $roleids['courseoverview']);

        // Remove the ignoreavailabilityrestrictions from the teacher role.
        role_change_permission($roleids['teacher'], context_system::instance(0),
                'moodle/course:ignoreavailabilityrestrictions', CAP_PREVENT);

        // Allow the courseoverview role to ingore available restriction.
        role_change_permission($roleids['courseoverview'], context_system::instance(0),
                'moodle/course:ignoreavailabilityrestrictions', CAP_ALLOW);

        // Make sure that initially both sections and both modules are available and visible for a student.
        $modinfostudent = get_fast_modinfo($course1, $student->id);
        $this->assertTrue($modinfostudent->get_section_info(1)->available);
        $this->assertTrue($modinfostudent->get_cm($assign0->cmid)->available);
        $this->assertTrue($modinfostudent->get_cm($assign0->cmid)->uservisible);
        $this->assertTrue($modinfostudent->get_cm($assign1->cmid)->available);
        $this->assertTrue($modinfostudent->get_cm($assign1->cmid)->uservisible);
        $this->assertFalse($modinfostudent->get_cm($assign2->cmid)->uservisible);

        // Set 'hideoddsections' for the course to 1.
        // Section1 and assign1 will be unavailable, uservisible will be false for student and true for teacher.
        $data = (object)array('id' => $course1->id, 'hideoddsections' => 1);
        course_get_format($course1)->update_course_format_options($data);
        $modinfostudent = get_fast_modinfo($course1, $student->id);
        $this->assertFalse($modinfostudent->get_section_info(1)->available);
        $this->assertEmpty($modinfostudent->get_section_info(1)->availableinfo);
        $this->assertFalse($modinfostudent->get_section_info(1)->uservisible);
        $this->assertTrue($modinfostudent->get_cm($assign0->cmid)->available);
        $this->assertTrue($modinfostudent->get_cm($assign0->cmid)->uservisible);
        $this->assertFalse($modinfostudent->get_cm($assign1->cmid)->available);
        $this->assertFalse($modinfostudent->get_cm($assign1->cmid)->uservisible);
        $this->assertFalse($modinfostudent->get_cm($assign2->cmid)->uservisible);

        $modinfoteacher = get_fast_modinfo($course1, $teacher->id);
        $this->assertFalse($modinfoteacher->get_section_info(1)->available);
        $this->assertEmpty($modinfoteacher->get_section_info(1)->availableinfo);
        $this->assertFalse($modinfoteacher->get_section_info(1)->uservisible);
        $this->assertTrue($modinfoteacher->get_cm($assign0->cmid)->available);
        $this->assertTrue($modinfoteacher->get_cm($assign0->cmid)->uservisible);
        $this->assertFalse($modinfoteacher->get_cm($assign1->cmid)->available);
        $this->assertFalse($modinfoteacher->get_cm($assign1->cmid)->uservisible);
        $this->assertTrue($modinfoteacher->get_cm($assign2->cmid)->available);
        $this->assertTrue($modinfoteacher->get_cm($assign2->cmid)->uservisible);

        $modinfoteacher = get_fast_modinfo($course1, $editingteacher->id);
        $this->assertFalse($modinfoteacher->get_section_info(1)->available);
        $this->assertEmpty($modinfoteacher->get_section_info(1)->availableinfo);
        $this->assertTrue($modinfoteacher->get_section_info(1)->uservisible);
        $this->assertTrue($modinfoteacher->get_cm($assign0->cmid)->available);
        $this->assertTrue($modinfoteacher->get_cm($assign0->cmid)->uservisible);
        $this->assertFalse($modinfoteacher->get_cm($assign1->cmid)->available);
        $this->assertTrue($modinfoteacher->get_cm($assign1->cmid)->uservisible);
        $this->assertTrue($modinfoteacher->get_cm($assign2->cmid)->uservisible);

        $modinfocourseoverview = get_fast_modinfo($course1, $courseoverviewuser->id);
        $this->assertFalse($modinfocourseoverview->get_section_info(1)->available);
        $this->assertEmpty($modinfocourseoverview->get_section_info(1)->availableinfo);
        $this->assertTrue($modinfocourseoverview->get_section_info(1)->uservisible);
        $this->assertTrue($modinfocourseoverview->get_cm($assign0->cmid)->available);
        $this->assertTrue($modinfocourseoverview->get_cm($assign0->cmid)->uservisible);
        $this->assertFalse($modinfocourseoverview->get_cm($assign1->cmid)->available);
        $this->assertTrue($modinfocourseoverview->get_cm($assign1->cmid)->uservisible);
        $this->assertFalse($modinfocourseoverview->get_cm($assign2->cmid)->uservisible);

        // Set 'hideoddsections' for the course to 2.
        // Section1 and assign1 will be unavailable, uservisible will be false for student and true for teacher.
        // Property availableinfo will be not empty.
        $data = (object)array('id' => $course1->id, 'hideoddsections' => 2);
        course_get_format($course1)->update_course_format_options($data);
        $modinfostudent = get_fast_modinfo($course1, $student->id);
        $this->assertFalse($modinfostudent->get_section_info(1)->available);
        $this->assertNotEmpty($modinfostudent->get_section_info(1)->availableinfo);
        $this->assertFalse($modinfostudent->get_section_info(1)->uservisible);
        $this->assertTrue($modinfostudent->get_cm($assign0->cmid)->available);
        $this->assertTrue($modinfostudent->get_cm($assign0->cmid)->uservisible);
        $this->assertFalse($modinfostudent->get_cm($assign1->cmid)->available);
        $this->assertFalse($modinfostudent->get_cm($assign1->cmid)->uservisible);

        $modinfoteacher = get_fast_modinfo($course1, $editingteacher->id);
        $this->assertFalse($modinfoteacher->get_section_info(1)->available);
        $this->assertNotEmpty($modinfoteacher->get_section_info(1)->availableinfo);
        $this->assertTrue($modinfoteacher->get_section_info(1)->uservisible);
        $this->assertTrue($modinfoteacher->get_cm($assign0->cmid)->available);
        $this->assertTrue($modinfoteacher->get_cm($assign0->cmid)->uservisible);
        $this->assertFalse($modinfoteacher->get_cm($assign1->cmid)->available);
        $this->assertTrue($modinfoteacher->get_cm($assign1->cmid)->uservisible);
    }

    /**
     * Test for supports_news() with a course format plugin that doesn't define 'news_items' in default blocks.
     */
    public function test_supports_news() {
        $this->resetAfterTest();
        $format = course_get_format((object)['format' => 'testformat']);
        $this->assertFalse($format->supports_news());
    }

    /**
     * Test for supports_news() for old course format plugins that defines 'news_items' in default blocks.
     */
    public function test_supports_news_legacy() {
        $this->resetAfterTest();
        $format = course_get_format((object)['format' => 'testlegacy']);
        $this->assertTrue($format->supports_news());
    }

    /**
     * Test for get_view_url() to ensure that the url is only given for the correct cases
     */
    public function test_get_view_url() {
        global $CFG;
        $this->resetAfterTest();

        $linkcoursesections = $CFG->linkcoursesections;

        // Generate a course with two sections (0 and 1) and two modules. Course format is set to 'testformat'.
        // This will allow us to test the default implementation of get_view_url.
        $generator = $this->getDataGenerator();
        $course1 = $generator->create_course(array('format' => 'testformat'));
        course_create_sections_if_missing($course1, array(0, 1));

        $data = (object)['id' => $course1->id];
        $format = course_get_format($course1);
        $format->update_course_format_options($data);

        // In page.
        $CFG->linkcoursesections = 0;
        $this->assertNotEmpty($format->get_view_url(null));
        $this->assertNotEmpty($format->get_view_url(0));
        $this->assertNotEmpty($format->get_view_url(1));
        $CFG->linkcoursesections = 1;
        $this->assertNotEmpty($format->get_view_url(null));
        $this->assertNotEmpty($format->get_view_url(0));
        $this->assertNotEmpty($format->get_view_url(1));

        // Navigation.
        $CFG->linkcoursesections = 0;
        $this->assertNull($format->get_view_url(1, ['navigation' => 1]));
        $this->assertNull($format->get_view_url(0, ['navigation' => 1]));
        $CFG->linkcoursesections = 1;
        $this->assertNotEmpty($format->get_view_url(1, ['navigation' => 1]));
        $this->assertNotEmpty($format->get_view_url(0, ['navigation' => 1]));
    }

    /**
     * Test for get_output_classname method.
     *
     * @dataProvider get_output_classname_provider
     * @param string $find the class to find
     * @param string $result the expected result classname
     * @param bool $exception if the method will raise an exception
     */
    public function test_get_output_classname($find, $result, $exception) {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course(['format' => 'theunittest']);
        $courseformat = course_get_format($course);

        if ($exception) {
            $this->expectException(coding_exception::class);
        }

        $courseclass = $courseformat->get_output_classname($find);
        $this->assertEquals($result, $courseclass);
    }

    /**
     * Data provider for test_get_output_classname.
     *
     * @return array the testing scenarios
     */
    public function get_output_classname_provider(): array {
        return [
            'overridden class' => [
                'find' => 'state\\course',
                'result' => 'format_theunittest\\output\\courseformat\\state\\course',
                'exception' => false,
            ],
            'original class' => [
                'find' => 'state\\section',
                'result' => 'core_courseformat\\output\\local\\state\\section',
                'exception' => false,
            ],
            'invalid overridden class' => [
                'find' => 'state\\invalidoutput',
                'result' => '',
                'exception' => true,
            ],
        ];
    }

    /**
     * Test for the default delete format data behaviour.
     *
     * @covers ::get_sections_preferences
     */
    public function test_get_sections_preferences() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_and_enrol($course, 'student');

        // Create fake preferences generated by the frontend js module.
        $data = (object)[
            'pref1' => [1,2],
            'pref2' => [1],
        ];
        set_user_preference('coursesectionspreferences_' . $course->id, json_encode($data), $user->id);

        $format = course_get_format($course);

        // Load data from user 1.
        $this->setUser($user);
        $preferences = $format->get_sections_preferences();

        $this->assertEquals(
            (object)['pref1' => true, 'pref2' => true],
            $preferences[1]
        );
        $this->assertEquals(
            (object)['pref1' => true],
            $preferences[2]
        );
    }

    /**
     * Test for the default delete format data behaviour.
     *
     * @covers ::set_sections_preference
     */
    public function test_set_sections_preference() {
        $this->resetAfterTest();
        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        $user = $generator->create_and_enrol($course, 'student');

        $format = course_get_format($course);
        $this->setUser($user);

        // Load data from user 1.
        $format->set_sections_preference('pref1', [1, 2]);
        $format->set_sections_preference('pref2', [1]);
        $format->set_sections_preference('pref3', []);

        $preferences = $format->get_sections_preferences();
        $this->assertEquals(
            (object)['pref1' => true, 'pref2' => true],
            $preferences[1]
        );
        $this->assertEquals(
            (object)['pref1' => true],
            $preferences[2]
        );
    }

    /**
     * Test for the default delete format data behaviour.
     *
     * @covers ::delete_format_data
     * @dataProvider delete_format_data_provider
     * @param bool $usehook if it should use course_delete to trigger $format->delete_format_data as a hook
     */
    public function test_delete_format_data(bool $usehook) {
        global $DB;

        $this->resetAfterTest();

        $generator = $this->getDataGenerator();
        $course = $generator->create_course();
        course_create_sections_if_missing($course, [0, 1]);
        $user = $generator->create_and_enrol($course, 'student');

        // Create a coursesectionspreferences_XX preference.
        $key = 'coursesectionspreferences_' . $course->id;
        $fakevalue = 'No dark sarcasm in the classroom';
        set_user_preference($key, $fakevalue, $user->id);
        $this->assertEquals(
            $fakevalue,
            $DB->get_field('user_preferences', 'value', ['name' => $key, 'userid' => $user->id])
        );

        // Create another random user preference.
        $key2 = 'somepreference';
        $fakevalue2 = "All in all it's just another brick in the wall";
        set_user_preference($key2, $fakevalue2, $user->id);
        $this->assertEquals(
            $fakevalue2,
            $DB->get_field('user_preferences', 'value', ['name' => $key2, 'userid' => $user->id])
        );

        if ($usehook) {
            delete_course($course, false);
        } else {
            $format = course_get_format($course);
            $format->delete_format_data();
        }

        // Check which the preferences exists.
        $this->assertFalse(
            $DB->record_exists('user_preferences', ['name' => $key, 'userid' => $user->id])
        );
        set_user_preference($key2, $fakevalue2, $user->id);
        $this->assertEquals(
            $fakevalue2,
            $DB->get_field('user_preferences', 'value', ['name' => $key2, 'userid' => $user->id])
        );
    }

    /**
     * Data provider for test_delete_format_data.
     *
     * @return array the testing scenarios
     */
    public function delete_format_data_provider(): array {
        return [
            'direct call' => [
                'usehook' => false
            ],
            'use hook' => [
                'usehook' => true,
            ]
        ];
    }
}

/**
 * Class format_testformat.
 *
 * A test class that simulates a course format that doesn't define 'news_items' in default blocks.
 *
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_testformat extends core_courseformat\base {
    /**
     * Returns the list of blocks to be automatically added for the newly created course.
     *
     * @return array
     */
    public function get_default_blocks() {
        return [
            BLOCK_POS_RIGHT => [],
            BLOCK_POS_LEFT => []
        ];
    }
}

/**
 * Class format_testlegacy.
 *
 * A test class that simulates old course formats that define 'news_items' in default blocks.
 *
 * @copyright 2016 Jun Pataleta <jun@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class format_testlegacy extends core_courseformat\base {
    /**
     * Returns the list of blocks to be automatically added for the newly created course.
     *
     * @return array
     */
    public function get_default_blocks() {
        return [
            BLOCK_POS_RIGHT => ['news_items'],
            BLOCK_POS_LEFT => []
        ];
    }
}

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
 * Unit tests for frontend of relativedate condition.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace availability_relativedate;

/**
 * Unit tests for frontend of relativedate condition.
 *
 * @package   availability_relativedate
 * @copyright eWallah (www.eWallah.net)
 * @author    Renaat Debleu <info@eWallah.net>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @coversDefaultClass \availability_relativedate\frontend
 */
final class frontend_test extends \advanced_testcase {

    /**
     * Enable completion and availability.
     */
    public function setUp(): void {
        global $CFG;
        parent::setUp();
        $this->resetAfterTest();
        $this->setAdminUser();
        $CFG->enablecompletion = true;
        $CFG->enableavailability = true;
        set_config('enableavailability', true);
    }

    /**
     * Tests using relativedate condition in front end.
     * @covers \availability_relativedate\frontend
     */
    public function test_frontend(): void {
        global $DB;
        $enabled = enrol_get_plugins(true);
        $enabled['self'] = true;
        set_config('enrol_plugins_enabled', implode(',', array_keys($enabled)));
        $dg = $this->getDataGenerator();
        $course = $dg->create_course(['enablecompletion' => 1]);
        $instance = $DB->get_record('enrol', ['courseid' => $course->id, 'enrol' => 'self'], '*', MUST_EXIST);
        $DB->set_field('enrol', 'enrolenddate', time() + 10000, ['id' => $instance->id]);
        $DB->set_field('enrol', 'enrolstartdate', time() - 100, ['id' => $instance->id]);
        $page = $dg->get_plugin_generator('mod_page')->create_instance(['course' => $course, 'completion' => 1]);
        $modinfo = get_fast_modinfo($course);
        $cms = $modinfo->get_instances();
        $cm = $cms['page'][$page->id];
        $DB->set_field('course_modules', 'deletioninprogress', true, ['id' => $cm->id]);
        $arr = $this->call_method([$course]);
        $this->assertCount(5, $arr);
        $this->assertCount(5, $arr[1]);
        $expected = [
            ['field' => 1, 'display' => 'after course start date'],
            ['field' => 6, 'display' => 'before course start date'],
            ['field' => 3, 'display' => 'after user enrolment date'],
            ['field' => 4, 'display' => 'after enrolment method end date'],
            ['field' => 7, 'display' => 'after completion of activity'],
        ];
        $this->assertEquals($expected, $arr[1]);
        $this->assertTrue($arr[2]);
        $this->assertCount(0, $arr[3]);
        $this->assertCount(1, $arr[4]);
        $this->assertCount(2, $arr[4][0]);
    }

    /**
     * Test course
     * @covers \availability_relativedate\frontend
     */
    public function test_javascript_course(): void {
        $course = $this->getDataGenerator()->create_course(['enablecompletion' => 1]);
        $arr = $this->call_method([$course]);
        $this->assertCount(5, $arr);

        // Hours - minutes.
        $this->assertCount(5, $arr[0]);
        $expected = [
            (object)['field' => 0, 'display' => 'minutes'],
            (object)['field' => 1, 'display' => 'hours'],
            (object)['field' => 2, 'display' => 'days'],
            (object)['field' => 3, 'display' => 'weeks'],
            (object)['field' => 4, 'display' => 'months'],
        ];
        $this->assertEquals($expected, $arr[0]);

        $this->assertCount(3, $arr[1]);
        $expected = [
            ['field' => 1, 'display' => 'after course start date'],
            ['field' => 6, 'display' => 'before course start date'],
            ['field' => 3, 'display' => 'after user enrolment date'],
        ];
        $this->assertEquals($expected, $arr[1]);

        $this->assertTrue($arr[2]);
        $this->assertCount(0, $arr[3]);
        $this->assertCount(0, $arr[4]);
    }

    /**
     * Test section
     * @covers \availability_relativedate\frontend
     */
    public function test_javascript_section(): void {
        $dg = $this->getDataGenerator();
        $course = $dg->create_course(['enablecompletion' => 1, 'enddate' => time() + 666666]);
        $page1 = $dg->get_plugin_generator('mod_page')->create_instance(['course' => $course, 'completion' => 1]);
        $page2 = $dg->get_plugin_generator('mod_page')->create_instance(['course' => $course, 'completion' => 1]);
        $page3 = $dg->get_plugin_generator('mod_page')->create_instance(['course' => $course, 'completion' => 0]);
        $section = get_fast_modinfo($course)->get_section_info(0);
        $arr = $this->call_method([$course, null, $section]);
        $this->assertCount(5, $arr);

        $this->assertCount(5, $arr[0]);
        $this->assertCount(6, $arr[1]);
        $expected = [
            ['field' => 1, 'display' => 'after course start date'],
            ['field' => 6, 'display' => 'before course start date'],
            ['field' => 5, 'display' => 'after course end date'],
            ['field' => 2, 'display' => 'before course end date'],
            ['field' => 3, 'display' => 'after user enrolment date'],
            ['field' => 7, 'display' => 'after completion of activity'],
        ];
        $this->assertEquals($expected, $arr[1]);

        $this->assertFalse($arr[2]);
        $this->assertCount(0, $arr[3]);
        $this->assertCount(1, $arr[4]);
        $expected = [
            [
                'name' => 'Section 0',
                'coursemodules' => [
                    1 => ['id' => $page2->cmid,  'name' => 'Page 2', 'completionenabled' => true],
                    2 => ['id' => $page3->cmid,  'name' => 'Page 3', 'completionenabled' => false],
                    0 => ['id' => $page1->cmid,  'name' => 'Page 1', 'completionenabled' => true],
                ],
            ],
        ];
        $this->assertEquals($expected, $arr[4]);
    }

    /**
     * Test module
     * @covers \availability_relativedate\frontend
     */
    public function test_javascript_module(): void {
        $dg = $this->getDataGenerator();
        $course = $dg->create_course(['enablecompletion' => 1, 'startdate' => time() - 666666]);
        $page1 = $dg->get_plugin_generator('mod_page')->create_instance(
            [
                'course' => $course,
                'completion' => 1,
                'title' => '<a>44^55</a>',
            ]
        );
        $page2 = $dg->get_plugin_generator('mod_page')->create_instance(['course' => $course, 'completion' => 1]);
        $modinfo = get_fast_modinfo($course);
        $cms = $modinfo->get_instances();
        $cm = $cms['page'][$page2->id];
        $arr = $this->call_method([$course, $cm]);
        $this->assertCount(5, $arr);

        $this->assertCount(5, $arr[0]);
        $this->assertCount(4, $arr[1]);
        $expected = [
            ['field' => 1, 'display' => 'after course start date'],
            ['field' => 6, 'display' => 'before course start date'],
            ['field' => 3, 'display' => 'after user enrolment date'],
            ['field' => 7, 'display' => 'after completion of activity'],
        ];
        $this->assertEquals($expected, $arr[1]);

        $this->assertTrue($arr[2]);
        $this->assertCount(0, $arr[3]);
        $this->assertCount(1, $arr[4]);
        $expected = [
            [
                'name' => 'Section 0',
                'coursemodules' => [
                    0 => ['id' => $page1->cmid,  'name' => 'Page 1', 'completionenabled' => true],
                ],
            ],
        ];
        $this->assertEquals($expected, $arr[4]);
    }

    /**
     * Test behat funcs
     * @covers \behat_availability_relativedate
     */
    public function test_behat(): void {
        global $CFG;
        require_once($CFG->dirroot . '/availability/condition/relativedate/tests/behat/behat_availability_relativedate.php');
        $dg = $this->getDataGenerator();
        $course = $dg->create_course(['enablecompletion' => true]);
        $dg->get_plugin_generator('mod_page')->create_instance(['course' => $course, 'idnumber' => 'page1']);
        $dg->get_plugin_generator('mod_page')->create_instance(['course' => $course, 'idnumber' => 'page2']);
        $class = new \behat_availability_relativedate();
        $class->selfenrolment_exists_in_course_starting($course->fullname, '');
        $class->selfenrolment_exists_in_course_starting($course->fullname, '##-10 days noon##');
        $class->selfenrolment_exists_in_course_ending($course->fullname, '');
        $class->selfenrolment_exists_in_course_ending($course->fullname, '## today ##');
        $this->expectExceptionMessage('behat_context_helper');
        $class->i_make_activity_relative_date_depending_on('page1', 'page2');
        $class->i_should_see_relativedate('##-10 days noon##');
    }

    /**
     * Test behat funcs
     * @param array $params
     * @return array
     */
    private function call_method(array $params): array {
        $frontend = new frontend();
        $name = '\availability_relativedate\frontend';
        return \phpunit_util::call_internal_method($frontend, 'get_javascript_init_params', $params, $name);
    }
}

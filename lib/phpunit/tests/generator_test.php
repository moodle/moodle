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
 * PHPUnit integration tests
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();


/**
 * Test data generator
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_phpunit_generator_testcase extends advanced_testcase {
    public function test_create() {
        global $DB;

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $count = $DB->count_records('user');
        $user = $generator->create_user();
        $this->assertEquals($count+1, $DB->count_records('user'));
        $this->assertSame($user->username, clean_param($user->username, PARAM_USERNAME));
        $this->assertSame($user->email, clean_param($user->email, PARAM_EMAIL));
        $user = $generator->create_user(array('firstname'=>'Žluťoučký', 'lastname'=>'Koníček'));
        $this->assertSame($user->username, clean_param($user->username, PARAM_USERNAME));
        $this->assertSame($user->email, clean_param($user->email, PARAM_EMAIL));

        $count = $DB->count_records('course_categories');
        $category = $generator->create_category();
        $this->assertEquals($count+1, $DB->count_records('course_categories'));
        $this->assertRegExp('/^Course category \d/', $category->name);
        $this->assertSame('', $category->idnumber);
        $this->assertRegExp('/^Test course category \d/', $category->description);
        $this->assertSame(FORMAT_MOODLE, $category->descriptionformat);

        $count = $DB->count_records('course');
        $course = $generator->create_course();
        $this->assertEquals($count+1, $DB->count_records('course'));
        $this->assertRegExp('/^Test course \d/', $course->fullname);
        $this->assertRegExp('/^tc_\d/', $course->shortname);
        $this->assertSame('', $course->idnumber);
        $this->assertSame('topics', $course->format);
        $this->assertEquals(0, $course->newsitems);
        $this->assertEquals(5, $course->numsections);
        $this->assertRegExp('/^Test course \d/', $course->summary);
        $this->assertSame(FORMAT_MOODLE, $course->summaryformat);

        $section = $generator->create_course_section(array('course'=>$course->id, 'section'=>3));
        $this->assertEquals($course->id, $section->course);

        $scale = $generator->create_scale();
        $this->assertNotEmpty($scale);
    }

    public function test_create_module() {
        global $CFG, $SITE;
        if (!file_exists("$CFG->dirroot/mod/page/")) {
            $this->markTestSkipped('Can not find standard Page module');
        }

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $page = $generator->create_module('page', array('course'=>$SITE->id));
        $this->assertNotEmpty($page);
        $cm = get_coursemodule_from_instance('page', $page->id, $SITE->id, true);
        $this->assertEquals(0, $cm->sectionnum);

        $page = $generator->create_module('page', array('course'=>$SITE->id), array('section'=>3));
        $this->assertNotEmpty($page);
        $cm = get_coursemodule_from_instance('page', $page->id, $SITE->id, true);
        $this->assertEquals(3, $cm->sectionnum);
    }

    public function test_create_block() {
        global $CFG;
        if (!file_exists("$CFG->dirroot/blocks/online_users/")) {
            $this->markTestSkipped('Can not find standard Online users block');
        }

        $this->resetAfterTest(true);
        $generator = $this->getDataGenerator();

        $page = $generator->create_block('online_users');
        $this->assertNotEmpty($page);
    }
}

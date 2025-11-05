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

namespace mod_board\phpunit\external;

use mod_board\external\get_configuration;
use mod_board\board;

/**
 * Test external method for getting of board configuration.
 *
 * @package    mod_board
 * @copyright  2025 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \mod_board\external\get_configuration
 */
final class get_configuration_test extends \advanced_testcase {
    public function test_execute(): void {
        $this->resetAfterTest();

        $course = $this->getDataGenerator()->create_course([]);
        $board1 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 1',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGDISABLED,
            'hideheaders' => 0,
            'sortby' => board::SORTBYDATE,
            'enableblanktarget' => 0,
        ]);
        $cm1 = get_coursemodule_from_instance('board', $board1->id, $course->id, false, MUST_EXIST);
        $context1 = \context_module::instance($cm1->id);
        $board2 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 2',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PRIVATE,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGBYALL,
            'hideheaders' => 0,
            'sortby' => board::SORTBYDATE,
            'enableblanktarget' => 0,
        ]);
        $cm2 = get_coursemodule_from_instance('board', $board2->id, $course->id, false, MUST_EXIST);
        $context2 = \context_module::instance($cm2->id);
        $board3 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 3',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_PUBLIC,
            'groupmode' => NOGROUPS,
            'addrating' => board::RATINGBYSTUDENTS,
            'hideheaders' => 0,
            'sortby' => board::SORTBYDATE,
            'enableblanktarget' => 0,
        ]);
        $cm3 = get_coursemodule_from_instance('board', $board3->id, $course->id, false, MUST_EXIST);
        $context3 = \context_module::instance($cm3->id);
        $board4 = $this->getDataGenerator()->create_module('board', [
            'name' => 'Board 4',
            'course' => $course->id,
            'singleusermode' => board::SINGLEUSER_DISABLED,
            'groupmode' => SEPARATEGROUPS,
            'addrating' => board::RATINGBYTEACHERS,
            'hideheaders' => 0,
            'sortby' => board::SORTBYDATE,
            'enableblanktarget' => 0,
        ]);
        $cm4 = get_coursemodule_from_instance('board', $board4->id, $course->id, false, MUST_EXIST);
        $context4 = \context_module::instance($cm4->id);

        $group1 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);
        $group2 = $this->getDataGenerator()->create_group(['courseid' => $course->id]);

        $teacher1 = $this->getDataGenerator()->create_user();
        $student1 = $this->getDataGenerator()->create_user();
        $student2 = $this->getDataGenerator()->create_user();
        $student3 = $this->getDataGenerator()->create_user();
        $student4 = $this->getDataGenerator()->create_user();
        $student5 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher1->id, $course->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student3->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($student4->id, $course->id, 'guest');

        $this->getDataGenerator()->create_group_member(['userid' => $student1->id, 'groupid' => $group1->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student2->id, 'groupid' => $group2->id]);
        $this->getDataGenerator()->create_group_member(['userid' => $student3->id, 'groupid' => $group1->id]);

        $this->setUser($student1);

        $response = get_configuration::execute($board1->id, 0, 0);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board1, $settings->board);
        $this->assertSame($context1->id, $settings->contextid);
        $this->assertFalse($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($student1->id, $settings->userId);
        $this->assertSame(0, $settings->ownerId);
        $this->assertSame(0, $settings->groupId);
        $this->assertFalse($settings->readonly);
        $this->assertFalse($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $response = get_configuration::execute($board2->id, $student1->id, 0);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board2, $settings->board);
        $this->assertSame($context2->id, $settings->contextid);
        $this->assertFalse($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($student1->id, $settings->userId);
        $this->assertSame($student1->id, (string)$settings->ownerId);
        $this->assertSame(0, $settings->groupId);
        $this->assertFalse($settings->readonly);
        $this->assertTrue($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $response = get_configuration::execute($board3->id, $student1->id, 0);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board3, $settings->board);
        $this->assertSame($context3->id, $settings->contextid);
        $this->assertFalse($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($student1->id, $settings->userId);
        $this->assertSame($student1->id, (string)$settings->ownerId);
        $this->assertSame(0, $settings->groupId);
        $this->assertFalse($settings->readonly);
        $this->assertTrue($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $response = get_configuration::execute($board4->id, 0, 0);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board4, $settings->board);
        $this->assertSame($context4->id, $settings->contextid);
        $this->assertFalse($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($student1->id, $settings->userId);
        $this->assertSame(0, $settings->ownerId);
        $this->assertSame(0, $settings->groupId);
        $this->assertTrue($settings->readonly);
        $this->assertTrue($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $response = get_configuration::execute($board4->id, 0, $group1->id);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board4, $settings->board);
        $this->assertSame($context4->id, $settings->contextid);
        $this->assertFalse($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($student1->id, $settings->userId);
        $this->assertSame(0, $settings->ownerId);
        $this->assertSame($group1->id, (string)$settings->groupId);
        $this->assertFalse($settings->readonly);
        $this->assertTrue($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $response = get_configuration::execute($board4->id, 0, $group2->id);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board4, $settings->board);
        $this->assertSame($context4->id, $settings->contextid);
        $this->assertFalse($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($student1->id, $settings->userId);
        $this->assertSame(0, $settings->ownerId);
        $this->assertSame($group2->id, (string)$settings->groupId);
        $this->assertTrue($settings->readonly);
        $this->assertTrue($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $this->setUser($teacher1);

        $response = get_configuration::execute($board1->id, 0, 0);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board1, $settings->board);
        $this->assertSame($context1->id, $settings->contextid);
        $this->assertTrue($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($teacher1->id, $settings->userId);
        $this->assertSame(0, $settings->ownerId);
        $this->assertSame(0, $settings->groupId);
        $this->assertFalse($settings->readonly);
        $this->assertFalse($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $response = get_configuration::execute($board2->id, $student1->id, 0);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board2, $settings->board);
        $this->assertSame($context2->id, $settings->contextid);
        $this->assertTrue($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($teacher1->id, $settings->userId);
        $this->assertSame($student1->id, (string)$settings->ownerId);
        $this->assertSame(0, $settings->groupId);
        $this->assertFalse($settings->readonly);
        $this->assertTrue($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $response = get_configuration::execute($board3->id, $student1->id, 0);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board3, $settings->board);
        $this->assertSame($context3->id, $settings->contextid);
        $this->assertTrue($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($teacher1->id, $settings->userId);
        $this->assertSame($student1->id, (string)$settings->ownerId);
        $this->assertSame(0, $settings->groupId);
        $this->assertFalse($settings->readonly);
        $this->assertTrue($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $response = get_configuration::execute($board4->id, 0, 0);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board4, $settings->board);
        $this->assertSame($context4->id, $settings->contextid);
        $this->assertTrue($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($teacher1->id, $settings->userId);
        $this->assertSame(0, $settings->ownerId);
        $this->assertSame(0, $settings->groupId);
        $this->assertTrue($settings->readonly);
        $this->assertTrue($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $response = get_configuration::execute($board4->id, 0, $group1->id);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board4, $settings->board);
        $this->assertSame($context4->id, $settings->contextid);
        $this->assertTrue($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($teacher1->id, $settings->userId);
        $this->assertSame(0, $settings->ownerId);
        $this->assertSame($group1->id, (string)$settings->groupId);
        $this->assertFalse($settings->readonly);
        $this->assertTrue($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $response = get_configuration::execute($board4->id, 0, $group2->id);
        $response = get_configuration::clean_returnvalue(get_configuration::execute_returns(), $response);
        $this->assertSame([], $response['warnings']);
        $settings = json_decode($response['settings']);
        $this->assertEquals($board4, $settings->board);
        $this->assertSame($context4->id, $settings->contextid);
        $this->assertTrue($settings->isEditor);
        $this->assertSame('0', $settings->usersCanEdit);
        $this->assertSame($teacher1->id, $settings->userId);
        $this->assertSame(0, $settings->ownerId);
        $this->assertSame($group2->id, (string)$settings->groupId);
        $this->assertFalse($settings->readonly);
        $this->assertTrue($settings->ratingenabled);
        $this->assertFalse($settings->hideheaders);
        $this->assertSame('1', $settings->sortby);
        $this->assertSame('0', $settings->enableblanktarget);

        $this->setUser($student4);

        try {
            get_configuration::execute($board1->id, 0, 0);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\core\exception\require_login_exception::class, $ex);
            $this->assertSame('Course or activity not accessible. (Activity is hidden)', $ex->getMessage());
        }

        $this->setUser($student5);

        try {
            get_configuration::execute($board1->id, 0, 0);
            $this->fail('Exception expected');
        } catch (\core\exception\moodle_exception $ex) {
            $this->assertInstanceOf(\core\exception\require_login_exception::class, $ex);
            $this->assertSame('Course or activity not accessible. (Not enrolled)', $ex->getMessage());
        }
    }
}

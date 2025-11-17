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

namespace mod_bigbluebuttonbn\local\helpers;

use context_course;
use mod_bigbluebuttonbn\instance;
use mod_bigbluebuttonbn\test\testcase_helper_trait;

/**
 * BBB Library tests class.
 *
 * @package   mod_bigbluebuttonbn
 * @copyright 2018 - present, Blindside Networks Inc
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Laurent David (laurent@call-learning.fr)
 * @covers \mod_bigbluebuttonbn\local\helpers\roles
 */
final class roles_test extends \advanced_testcase {
    use testcase_helper_trait;

    /**
     * Test select separate group prevent all
     *
     */
    public function test_get_users_select_separate_groups_prevent_all(): void {
        $this->resetAfterTest();
        $numstudents = 12;
        $numteachers = 3;
        $groupsnum = 3;
        list($course, $groups, $students, $teachers, $bbactivity, $roleids) =
            $this->setup_course_students_teachers(
                (object) ['enablecompletion' => true, 'groupmode' => strval(SEPARATEGROUPS), 'groupmodeforce' => 1],
                $numstudents, $numteachers, $groupsnum);
        $context = context_course::instance($course->id);
        // Prevent access all groups.
        role_change_permission($roleids['teacher'], $context, 'moodle/site:accessallgroups', CAP_PREVENT);
        $this->setUser($teachers[0]);
        $users = roles::get_users_array($context, $bbactivity);
        $this->assertCount(($numstudents + $numteachers) / $groupsnum, $users);
        $this->setUser($teachers[1]);
        $users = roles::get_users_array($context, $bbactivity);
        $this->assertCount(($numstudents + $numteachers) / $groupsnum, $users);
        $this->setUser($teachers[2]);
        $users = roles::get_users_array($context, $bbactivity);
        $this->assertCount(($numstudents + $numteachers) / $groupsnum, $users);
        $course->groupmode = strval(SEPARATEGROUPS);
        $course->groupmodeforce = "0";
        update_course($course);
        $this->setUser($teachers[2]);
        $users = roles::get_users_array($context, $bbactivity);
        $this->assertCount($numstudents + $numteachers, $users);

    }

    /**
     * Test select separate groups
     *
     */
    public function test_get_users_select_separate_groups(): void {
        $this->resetAfterTest();
        $numstudents = 12;
        $numteachers = 3;
        $groupsnum = 3;
        list($course, $groups, $students, $teachers, $bbactivity, $roleids) =
            $this->setup_course_students_teachers(
                (object) ['enablecompletion' => true, 'groupmode' => strval(VISIBLEGROUPS), 'groupmodeforce' => 1],
                $numstudents, $numteachers, $groupsnum);

        $context = context_course::instance($course->id);
        $this->setUser($teachers[0]);
        $users = roles::get_users_array($context, $bbactivity);
        $this->assertCount($numstudents + $numteachers, $users);
        $this->setUser($teachers[1]);
        $users = roles::get_users_array($context, $bbactivity);
        $this->assertCount($numstudents + $numteachers, $users);
        $this->setUser($teachers[1]);
        $users = roles::get_users_array($context, $bbactivity);
        $this->assertCount($numstudents + $numteachers, $users);
    }

    /**
     * Test getting courses from which we can import
     */
    public function test_import_get_courses_for_select(): void {
        $this->resetAfterTest();
        $this->setAdminUser();

        filter_set_global_state('multilang', TEXTFILTER_ON);
        filter_set_applies_to_strings('multilang', true);

        $courseone = $this->getDataGenerator()->create_course();
        [$context, $cm, $activity] = $this->create_instance($courseone);
        $instance = instance::get_from_instanceid($activity->id);

        // Course two should be returned, because it has an activity instance.
        $coursetwo = $this->getDataGenerator()->create_course([
            'fullname' => '<span class="multilang" lang="en">English</span><span class="multilang" lang="es">Spanish</span>',
        ]);
        $this->create_instance($coursetwo);

        // Course three should not be returned, because it has no activity instance.
        $coursethree = $this->getDataGenerator()->create_course();

        $coursesforselect = roles::import_get_courses_for_select($instance);
        $this->assertEquals([
            $courseone->id => $courseone->fullname,
            $coursetwo->id => 'English',
        ], $coursesforselect);

        // Display extended course names.
        set_config('courselistshortnames', 1);

        $coursesforselect = roles::import_get_courses_for_select($instance);
        $this->assertEquals([
            $courseone->id => "{$courseone->shortname} {$courseone->fullname}",
            $coursetwo->id => "{$coursetwo->shortname} English",
        ], $coursesforselect);
    }
}

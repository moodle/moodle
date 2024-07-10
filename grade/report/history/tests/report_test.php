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

namespace gradereport_history;

defined('MOODLE_INTERNAL') || die();

/**
 * Grade history report test class.
 *
 * @package    gradereport_history
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class report_test extends \advanced_testcase {

    /**
     * Create some grades.
     */
    public function test_query_db(): void {
        $this->resetAfterTest();

        // Making the setup.
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c1ctx = \context_course::instance($c1->id);
        $c2ctx = \context_course::instance($c2->id);

        // Users.
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $u4 = $this->getDataGenerator()->create_user();
        $u5 = $this->getDataGenerator()->create_user();
        $grader1 = $this->getDataGenerator()->create_user();
        $grader2 = $this->getDataGenerator()->create_user();
        self::getDataGenerator()->enrol_user($grader1->id, $c1->id, 'teacher');
        self::getDataGenerator()->enrol_user($grader2->id, $c1->id, 'teacher');
        self::getDataGenerator()->enrol_user($u2->id, $c1->id, 'student');
        self::getDataGenerator()->enrol_user($u3->id, $c1->id, 'student');
        self::getDataGenerator()->enrol_user($u4->id, $c1->id, 'student');
        self::getDataGenerator()->enrol_user($u5->id, $c1->id, 'student');

        self::getDataGenerator()->enrol_user($grader1->id, $c2->id, 'teacher');
        self::getDataGenerator()->enrol_user($u5->id, $c2->id, 'student');

        // Modules.
        $c1m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c1));
        $c1m2 = $this->getDataGenerator()->create_module('assign', array('course' => $c1));
        $c1m3 = $this->getDataGenerator()->create_module('assign', array('course' => $c1));
        $c2m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c2));
        $c2m2 = $this->getDataGenerator()->create_module('assign', array('course' => $c2));

        // Creating fake history data.
        $giparams = array('itemtype' => 'mod', 'itemmodule' => 'assign');
        $grades = array();

        $this->setUser($grader1);

        $gi = \grade_item::fetch($giparams + array('iteminstance' => $c1m1->id));
        $grades['c1m1u1'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id,
                'timemodified' => time() - 3600));
        $grades['c1m1u2'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u2->id,
                'timemodified' => time() + 3600));
        $grades['c1m1u3'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u3->id));
        $grades['c1m1u4'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u4->id));
        $grades['c1m1u5'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u5->id));

        $gi = \grade_item::fetch($giparams + array('iteminstance' => $c1m2->id));
        $grades['c1m2u1'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id));
        $grades['c1m2u2'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u2->id));

        $gi = \grade_item::fetch($giparams + array('iteminstance' => $c1m3->id));
        $grades['c1m3u1'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id));

        $gi = \grade_item::fetch($giparams + array('iteminstance' => $c2m1->id));
        $grades['c2m1u1'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id,
            'usermodified' => $grader1->id));
        $grades['c2m1u2'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u2->id,
            'usermodified' => $grader1->id));
        $grades['c2m1u3'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u3->id,
            'usermodified' => $grader1->id));
        $grades['c2m1u4'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u4->id,
            'usermodified' => $grader2->id));

        // Histories where grades have not been revised..
        $grades['c2m1u5a'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u5->id,
            'timemodified' => time() - 60));
        $grades['c2m1u5b'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u5->id,
            'timemodified' => time()));
        $grades['c2m1u5c'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u5->id,
            'timemodified' => time() + 60));

        // Histories where grades have been revised and not revised.
        $now = time();
        $gi = \grade_item::fetch($giparams + array('iteminstance' => $c2m2->id));
        $grades['c2m2u1a'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id,
            'timemodified' => $now - 60, 'finalgrade' => 50));
        $grades['c2m2u1b'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id,
            'timemodified' => $now - 50, 'finalgrade' => 50));      // Not revised.
        $grades['c2m2u1c'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id,
            'timemodified' => $now, 'finalgrade' => 75));
        $grades['c2m2u1d'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id,
            'timemodified' => $now + 10, 'finalgrade' => 75));      // Not revised.
        $grades['c2m2u1e'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id,
            'timemodified' => $now + 60, 'finalgrade' => 25));
        $grades['c2m2u1f'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id,
            'timemodified' => $now + 70, 'finalgrade' => 25));      // Not revised.

        // TODO MDL-46736 Handle deleted/non-existing grade items.
        // Histories with missing grade items, considered as deleted.
        // $grades['c2x1u5'] = $this->create_grade_history($giparams + array('itemid' => -1, 'userid' => $u5->id, 'courseid' => $c1->id));
        // $grades['c2x2u5'] = $this->create_grade_history($giparams + array('itemid' => 999999, 'userid' => $u5->id, 'courseid' => $c1->id));

        // Basic filtering based on course id.
        $this->assertEquals(8, $this->get_tablelog_results($c1ctx, array(), true));
        $this->assertEquals(13, $this->get_tablelog_results($c2ctx, array(), true));

        // Filtering on 1 user the current user cannot access should return all records.
        $this->assertEquals(8, $this->get_tablelog_results($c1ctx, array('userids' => $u1->id), true));

        // Filtering on 2 users, only one of whom the current user can access.
        $this->assertEquals(1, $this->get_tablelog_results($c1ctx, ['userids' => "$u1->id,$u3->id"], true));
        $results = $this->get_tablelog_results($c1ctx, ['userids' => "$u1->id,$u3->id"]);
        $this->assertGradeHistoryIds([$grades['c1m1u3']->id], $results);

        // Filtering on 2 users, both of whom the current user can access.
        $this->assertEquals(3, $this->get_tablelog_results($c1ctx, ['userids' => "$u2->id,$u3->id"], true));
        $results = $this->get_tablelog_results($c1ctx, ['userids' => "$u2->id,$u3->id"]);
        $this->assertGradeHistoryIds([$grades['c1m1u2']->id, $grades['c1m1u3']->id, $grades['c1m2u2']->id], $results);

        // Filtering based on one grade item.
        $gi = \grade_item::fetch($giparams + array('iteminstance' => $c1m1->id));
        $this->assertEquals(5, $this->get_tablelog_results($c1ctx, array('itemid' => $gi->id), true));
        $gi = \grade_item::fetch($giparams + array('iteminstance' => $c1m3->id));
        $this->assertEquals(1, $this->get_tablelog_results($c1ctx, array('itemid' => $gi->id), true));

        // Filtering based on the grader.
        $this->assertEquals(3, $this->get_tablelog_results($c2ctx, array('grader' => $grader1->id), true));
        $this->assertEquals(1, $this->get_tablelog_results($c2ctx, array('grader' => $grader2->id), true));

        // Filtering based on date.
        $results = $this->get_tablelog_results($c1ctx, array('datefrom' => time() + 1800));
        $this->assertGradeHistoryIds(array($grades['c1m1u2']->id), $results);
        $results = $this->get_tablelog_results($c1ctx, array('datetill' => time() - 1800));
        $this->assertGradeHistoryIds(array($grades['c1m1u1']->id), $results);
        $results = $this->get_tablelog_results($c1ctx, array('datefrom' => time() - 1800, 'datetill' => time() + 1800));
        $this->assertGradeHistoryIds(array($grades['c1m1u3']->id, $grades['c1m1u4']->id, $grades['c1m1u5']->id,
            $grades['c1m2u1']->id, $grades['c1m2u2']->id, $grades['c1m3u1']->id), $results);

        // Filtering based on revised only.
        $this->assertEquals(3, $this->get_tablelog_results($c2ctx, array('userids' => $u5->id), true));
        $this->assertEquals(1, $this->get_tablelog_results($c2ctx, array('userids' => $u5->id, 'revisedonly' => true), true));

        // More filtering based on revised only.
        $gi = \grade_item::fetch($giparams + array('iteminstance' => $c2m2->id));
        $this->assertEquals(6, $this->get_tablelog_results($c2ctx, array('userids' => $u1->id, 'itemid' => $gi->id), true));
        $results = $this->get_tablelog_results($c2ctx, array('userids' => $u1->id, 'itemid' => $gi->id, 'revisedonly' => true));
        $this->assertGradeHistoryIds(array($grades['c2m2u1a']->id, $grades['c2m2u1c']->id, $grades['c2m2u1e']->id), $results);

        // Checking the value of the previous grade.
        $this->assertEquals(null, $results[$grades['c2m2u1a']->id]->prevgrade);
        $this->assertEquals($grades['c2m2u1a']->finalgrade, $results[$grades['c2m2u1c']->id]->prevgrade);
        $this->assertEquals($grades['c2m2u1c']->finalgrade, $results[$grades['c2m2u1e']->id]->prevgrade);

        // Put course in separate groups mode, add grader1 and two students to the same group.
        $c1->groupmode = SEPARATEGROUPS;
        update_course($c1);
        $this->assertFalse(has_capability('moodle/site:accessallgroups', \context_course::instance($c1->id)));
        $g1 = self::getDataGenerator()->create_group(['courseid' => $c1->id, 'name' => 'g1']);
        self::getDataGenerator()->create_group_member(['groupid' => $g1->id, 'userid' => $grader1->id]);
        self::getDataGenerator()->create_group_member(['groupid' => $g1->id, 'userid' => $u1->id]);
        self::getDataGenerator()->create_group_member(['groupid' => $g1->id, 'userid' => $u2->id]);
        $this->assertEquals(2, $this->get_tablelog_results($c1ctx, array(), true));

        // Grader2 is not in any groups.
        $this->setUser($grader2);
        $this->assertEquals(0, $this->get_tablelog_results($c1ctx, array(), true));
    }

    /**
     * Test the get users helper method.
     */
    public function test_get_users(): void {
        $this->resetAfterTest();

        // Making the setup.
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c1ctx = \context_course::instance($c1->id);
        $c2ctx = \context_course::instance($c2->id);

        $c1m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c1));
        $c2m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c2));

        // Users.
        $u1 = $this->getDataGenerator()->create_user(array('firstname' => 'Eric', 'lastname' => 'Cartman'));
        $u2 = $this->getDataGenerator()->create_user(array('firstname' => 'Stan', 'lastname' => 'Marsh'));
        $u3 = $this->getDataGenerator()->create_user(array('firstname' => 'Kyle', 'lastname' => 'Broflovski'));
        $u4 = $this->getDataGenerator()->create_user(array('firstname' => 'Kenny', 'lastname' => 'McCormick'));

        // Creating grade history for some users.
        $gi = \grade_item::fetch(array('iteminstance' => $c1m1->id, 'itemtype' => 'mod', 'itemmodule' => 'assign'));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u2->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u3->id));

        $gi = \grade_item::fetch(array('iteminstance' => $c2m1->id, 'itemtype' => 'mod', 'itemmodule' => 'assign'));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u4->id));

        // Checking fetching some users.
        $users = \gradereport_history\helper::get_users($c1ctx);
        $this->assertCount(3, $users);
        $this->assertArrayHasKey($u3->id, $users);
        $users = \gradereport_history\helper::get_users($c2ctx);
        $this->assertCount(1, $users);
        $this->assertArrayHasKey($u4->id, $users);
        $users = \gradereport_history\helper::get_users($c1ctx, 'c');
        $this->assertCount(1, $users);
        $this->assertArrayHasKey($u1->id, $users);
        $users = \gradereport_history\helper::get_users($c1ctx, '', 0, 2);
        $this->assertCount(2, $users);
        $this->assertArrayHasKey($u3->id, $users);
        $this->assertArrayHasKey($u1->id, $users);
        $users = \gradereport_history\helper::get_users($c1ctx, '', 1, 2);
        $this->assertCount(1, $users);
        $this->assertArrayHasKey($u2->id, $users);

        // Checking the count of users.
        $this->assertEquals(3, \gradereport_history\helper::get_users_count($c1ctx));
        $this->assertEquals(1, \gradereport_history\helper::get_users_count($c2ctx));
        $this->assertEquals(1, \gradereport_history\helper::get_users_count($c1ctx, 'c'));
    }

    /**
     * Data provider for \gradereport_history_report_testcase::test_get_users_with_profile_fields()
     * Testing get_users() and get_users_count() test cases.
     *
     * @return array List of data sets (test cases)
     */
    public static function get_users_with_profile_fields_provider(): array {
        return [
            // User identity check boxes, 'email', 'profile_field_lang' and 'profile_field_height' are checked.
                'show email,lang and height;search for all users' =>
                        ['email,profile_field_lang,profile_field_height', '', ['u1', 'u2', 'u3', 'u4']],
                'show email,lang and height;search for users on .org ' =>
                        ['email,profile_field_lang,profile_field_height', '.org', ['u1', 'u2', 'u4']],
                'show email,lang and height;search for users on .com ' =>
                        ['email,profile_field_lang,profile_field_height', '.com', []],
                'show email,lang and height;search for users on .uk ' =>
                        ['email,profile_field_lang,profile_field_height', '.uk', ['u3']],
                'show email,lang and height,search for Spanish speakers' =>
                        ['email,profile_field_lang,profile_field_height', 'spanish', ['u1', 'u4']],
                'show email,lang and height,search for Spanish speakers (using spa)' =>
                        ['email,profile_field_lang,profile_field_height', 'spa', ['u1', 'u4']],
                'show email,lang and height,search for German speakers' =>
                        ['email,profile_field_lang,profile_field_height', 'german', ['u2']],
                'show email,lang and height,search for German speakers (using ger)' =>
                        ['email,profile_field_lang,profile_field_height', 'ger', ['u2']],
                'show email,lang and height,search for English speakers' =>
                        ['email,profile_field_lang,profile_field_height', 'english', ['u3']],
                'show email,lang and height,search for English speakers (using eng)' =>
                        ['email,profile_field_lang,profile_field_height', 'eng', ['u3']],
                'show email,lang and height,search for users with height 180cm' =>
                        ['email,profile_field_lang,profile_field_height', '180', ['u2', 'u3', 'u4']],
                'show email,lang and height,search for users with height 170cm' =>
                        ['email,profile_field_lang,profile_field_height', '170', ['u1']],

            // User identity check boxes, 'email' and 'profile_field_height' are checked.
                'show email and height;search for users on .org' =>
                        ['email,profile_field_height', '.org', ['u1', 'u2', 'u4']],
                'show email and height;search for users on .com' =>
                        ['email,profile_field_height', '.com', []],
                'show email and height;search for users on .co' =>
                        ['email,profile_field_height', '.co', ['u3']],
                'show email and height,search for Spanish speakers' =>
                        ['email,profile_field_height', 'spanish', []],
                'show email and height,search for German speakers' =>
                        ['email,profile_field_height', 'german', []],
                'show email and height,search for English speakers' =>
                        ['email,profile_field_height', 'english', []],
                'show email and height,search for users with height 180cm' =>
                        ['email,profile_field_height', '180', ['u2', 'u3', 'u4']],
                'show email and height,search for users with height 170cm' =>
                        ['email,profile_field_height', '170', ['u1']],

            // User identity check boxes, only 'email' is checked.
                'show email only;search for users on .org' => ['email', '.org', ['u1', 'u2', 'u4']],
                'show email only;search for users on .com' => ['email', '.com', []],
                'show email only;search for users on .co.uk' => ['email', 'co.uk', ['u3']],
                'show email only;search for users on .uk' => ['email', '.uk', ['u3']],
                'show email only;search for users on .co' => ['email', '.co', ['u3']],
                'show email only;search for Spanish speakers' => ['email', 'spanish', []],
                'show email only;search for German speakers' => ['email', 'german', []],
                'show email only;search for English speakers' => ['email', 'english', []],
                'show email only;search for users with height 180cm' => ['email', '180', []],
                'show email only;search for users with height 170cm' => ['email', '170', []],
        ];
    }

    /**
     * Testing the search functionality on get_users() and get_users_count() and their inner methods.
     *
     * @dataProvider get_users_with_profile_fields_provider
     *
     * @param string $showuseridentity, list of user identities to be shown.
     * @param string $searchstring, the string to be searched.
     * @param array $expectedusernames, a list of expected usernames.
     * @return void
     */
    public function test_get_users_with_profile_fields(string $showuseridentity, string $searchstring,
            array $expectedusernames): void {
        global $CFG, $DB;
        require_once($CFG->dirroot . '/user/profile/lib.php');
        $this->resetAfterTest();

        // Create a couple of custom profile fields, which are in user identity.
        $generator = $this->getDataGenerator();
        $generator->create_custom_profile_field(['datatype' => 'text',
                'shortname' => 'lang', 'name' => 'Language']);
        $generator->create_custom_profile_field(['datatype' => 'text',
                'shortname' => 'height', 'name' => 'Height']);

        // Create a couple of test users.
        $u1 = $generator->create_user(['firstname' => 'Eduardo', 'lastname' => 'Gomes',
                'username' => 'u1', 'email' => 'u1@x.org', 'profile_field_lang' => 'Spanish',
                'profile_field_height' => '170cm']);
        $u2 = $generator->create_user(['firstname' => 'Dieter', 'lastname' => 'Schmitt',
                'username' => 'u2', 'email' => 'u2@x.org', 'profile_field_lang' => 'German',
                'profile_field_height' => '180cm']);

        $u3 = $generator->create_user(['firstname' => 'Peter', 'lastname' => 'Jones',
                'username' => 'u3', 'email' => 'u3@x.co.uk', 'profile_field_lang' => 'English',
                'profile_field_height' => '180cm']);
        $u4 = $generator->create_user(['firstname' => 'Pedro', 'lastname' => 'Gomes',
                'username' => 'u4', 'email' => 'u3@x.org', 'profile_field_lang' => 'Spanish',
                'profile_field_height' => '180cm']);

        // Do this as admin user.
        $this->setAdminUser();

        // Making the setup.
        $c1 = $this->getDataGenerator()->create_course();
        $c1ctx = \context_course::instance($c1->id);
        $c1m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c1));

        // Creating grade history for some users.
        $gi = \grade_item::fetch(array('iteminstance' => $c1m1->id, 'itemtype' => 'mod', 'itemmodule' => 'assign'));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u2->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u3->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u4->id));

        // Checking fetching some users with this config settings.
        set_config('showuseridentity', $showuseridentity);
        $numberofexpectedusers = count($expectedusernames);
        $users = \gradereport_history\helper::get_users($c1ctx, $searchstring);
        $userscount = \gradereport_history\helper::get_users_count($c1ctx, $searchstring);
        $this->assertEquals($numberofexpectedusers, $userscount);
        $this->assertCount($numberofexpectedusers, $users);
        foreach ($users as $user) {
            if (in_array($user->username, $expectedusernames)) {
                $this->assertArrayHasKey($user->id, $users);
            } else {
                $this->assertArrayNotHasKey($user->id, $users);
            }
        }
    }

    /**
     * Data provider method for \gradereport_history_report_testcase::test_get_users_with_groups()
     */
    public static function get_users_provider(): array {
        return [
            'Visible groups, non-editing teacher, not in any group' => [
                VISIBLEGROUPS, 'teacher', ['g1', 'g2'], ['s1', 's2', 's3', 's4', 's5']
            ],
            'Visible groups, non-editing teacher' => [
                VISIBLEGROUPS, 'teacher', [], ['s1', 's2', 's3', 's4', 's5']
            ],
            'Visible groups, editing teacher' => [
                VISIBLEGROUPS, 'editingteacher', ['g1', 'g2'], ['s1', 's2', 's3', 's4', 's5']
            ],
            'Separate groups, non-editing teacher' => [
                SEPARATEGROUPS, 'teacher', ['g1', 'g2'], ['s1', 's2']
            ],
            'Separate groups, non-editing teacher, not in any group' => [
                SEPARATEGROUPS, 'teacher', [], []
            ],
            'Separate groups, non-editing teacher and student share two groups' => [
                SEPARATEGROUPS, 'teacher', ['g4', 'g5'], ['s5']
            ],
            'Separate groups, editing teacher' => [
                SEPARATEGROUPS, 'editingteacher', ['g1', 'g2'], ['s1', 's2', 's3', 's4', 's5']
            ],
        ];
    }

    /**
     * Test for helper::get_users() with course group mode set.
     *
     * @dataProvider get_users_provider
     * @param $groupmode
     * @param $teacherrole
     * @param $teachergroups
     * @param $expectedusers
     */
    public function test_get_users_with_groups($groupmode, $teacherrole, $teachergroups, $expectedusers): void {
        global $DB;
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Create a test course.
        $course = $generator->create_course(['groupmode' => $groupmode]);

        // Create an assignment module.
        $assign = $generator->create_module('assign', ['course' => $course]);

        // Fetch roles.
        $role = $DB->get_record('role', ['shortname' => $teacherrole], '*', MUST_EXIST);
        $studentrole =  $DB->get_record('role', ['shortname' => 'student'], '*', MUST_EXIST);

        // Create users.
        $t1 = $generator->create_user(['username' => 't1', 'email' => 't1@example.com']);
        $s1 = $generator->create_user(['username' => 's1', 'email' => 's1@example.com']);
        $s2 = $generator->create_user(['username' => 's2', 'email' => 's2@example.com']);
        $s3 = $generator->create_user(['username' => 's3', 'email' => 's3@example.com']);
        $s4 = $generator->create_user(['username' => 's4', 'email' => 's4@example.com']);
        $s5 = $generator->create_user(['username' => 's5', 'email' => 's5@example.com']);

        // Enrol users.
        $generator->enrol_user($t1->id, $course->id, $role->id);
        $generator->enrol_user($s1->id, $course->id, $studentrole->id);
        $generator->enrol_user($s2->id, $course->id, $studentrole->id);
        $generator->enrol_user($s3->id, $course->id, $studentrole->id);
        $generator->enrol_user($s4->id, $course->id, $studentrole->id);
        $generator->enrol_user($s5->id, $course->id, $studentrole->id);

        // Create groups.
        $groups = [];
        $groups['g1'] = $generator->create_group(['courseid' => $course->id, 'name' => 'g1']);
        $groups['g2'] = $generator->create_group(['courseid' => $course->id, 'name' => 'g2']);
        $groups['g3'] = $generator->create_group(['courseid' => $course->id, 'name' => 'g3']);
        $groups['g4'] = $generator->create_group(['courseid' => $course->id, 'name' => 'g4']);
        $groups['g5'] = $generator->create_group(['courseid' => $course->id, 'name' => 'g5']);

        // Add teacher to the assigned groups.
        foreach ($teachergroups as $groupname) {
            $group = $groups[$groupname];
            $generator->create_group_member(['groupid' => $group->id, 'userid' => $t1->id]);
        }

        // Add students to groups.
        $generator->create_group_member(['groupid' => $groups['g1']->id, 'userid' => $s1->id]);
        $generator->create_group_member(['groupid' => $groups['g2']->id, 'userid' => $s2->id]);
        $generator->create_group_member(['groupid' => $groups['g3']->id, 'userid' => $s3->id]);
        $generator->create_group_member(['groupid' => $groups['g4']->id, 'userid' => $s5->id]);
        $generator->create_group_member(['groupid' => $groups['g5']->id, 'userid' => $s5->id]);

        // Creating grade history for the students.
        $gi = \grade_item::fetch(['iteminstance' => $assign->id, 'itemtype' => 'mod', 'itemmodule' => 'assign']);
        $this->create_grade_history(['itemid' => $gi->id, 'userid' => $s1->id]);
        $this->create_grade_history(['itemid' => $gi->id, 'userid' => $s2->id]);
        $this->create_grade_history(['itemid' => $gi->id, 'userid' => $s3->id]);
        $this->create_grade_history(['itemid' => $gi->id, 'userid' => $s4->id]);
        $this->create_grade_history(['itemid' => $gi->id, 'userid' => $s5->id]);

        // Log in as the teacher.
        $this->setUser($t1);

        // Fetch the users.
        $users = \gradereport_history\helper::get_users(\context_course::instance($course->id));
        // Confirm that the number of users fetched is the same as the count of expected users.
        $this->assertCount(count($expectedusers), $users);
        foreach ($users as $user) {
            // Confirm that each user returned is in the list of expected users.
            $this->assertTrue(in_array($user->username, $expectedusers));
        }
    }

    /**
     * Test the get graders helper method.
     */
    public function test_graders(): void {
        $this->resetAfterTest();

        // Making the setup.
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c3 = $this->getDataGenerator()->create_course(['groupmode' => SEPARATEGROUPS]);

        $c1m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c1));
        $c2m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c2));
        $c3m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c3));

        // Users.
        $u1 = $this->getDataGenerator()->create_user(array('firstname' => 'Eric', 'lastname' => 'Cartman'));
        $u2 = $this->getDataGenerator()->create_user(array('firstname' => 'Stan', 'lastname' => 'Marsh'));
        $u3 = $this->getDataGenerator()->create_user(array('firstname' => 'Kyle', 'lastname' => 'Broflovski'));
        $u4 = $this->getDataGenerator()->create_user(array('firstname' => 'Kenny', 'lastname' => 'McCormick'));

        foreach ([$c1, $c2, $c3] as $course) {
            foreach ([$u1, $u2, $u3, $u4] as $user) {
                self::getDataGenerator()->enrol_user($user->id, $course->id, 'student');
            }
        }

        // Creating grade history for some users.
        $gi = \grade_item::fetch(array('iteminstance' => $c1m1->id, 'itemtype' => 'mod', 'itemmodule' => 'assign'));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id, 'usermodified' => $u1->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id, 'usermodified' => $u2->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id, 'usermodified' => $u3->id));

        $gi = \grade_item::fetch(array('iteminstance' => $c2m1->id, 'itemtype' => 'mod', 'itemmodule' => 'assign'));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id, 'usermodified' => $u4->id));

        $gi = \grade_item::fetch(array('iteminstance' => $c3m1->id, 'itemtype' => 'mod', 'itemmodule' => 'assign'));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id, 'usermodified' => $u1->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u2->id, 'usermodified' => $u2->id));

        // Checking fetching some users.
        $graders = \gradereport_history\helper::get_graders($c1->id);
        $this->assertCount(4, $graders); // Including "all graders" .
        $this->assertArrayHasKey($u1->id, $graders);
        $this->assertArrayHasKey($u2->id, $graders);
        $this->assertArrayHasKey($u3->id, $graders);
        $graders = \gradereport_history\helper::get_graders($c2->id);
        $this->assertCount(2, $graders); // Including "all graders" .
        $this->assertArrayHasKey($u4->id, $graders);

        // Third course is in separate groups mode. Only graders from the same group will be returned.
        $g = self::getDataGenerator()->create_group(['courseid' => $course->id, 'name' => 'g1']);
        self::getDataGenerator()->create_group_member(['groupid' => $g->id, 'userid' => $u1->id]);
        self::getDataGenerator()->create_group_member(['groupid' => $g->id, 'userid' => $u2->id]);
        $this->setUser($u1);
        $graders = \gradereport_history\helper::get_graders($c3->id);
        $this->assertCount(3, $graders); // Including "all graders" .
        $this->setUser($u3);
        $graders = \gradereport_history\helper::get_graders($c3->id);
        $this->assertCount(1, $graders); // Including "all graders" .
    }

    /**
     * Asserts that the array of grade objects contains exactly the right IDs.
     *
     * @param array $expectedids Array of expected IDs.
     * @param array $objects Array of objects returned by the table.
     */
    protected function assertGradeHistoryIds(array $expectedids, array $objects) {
        $this->assertCount(count($expectedids), $objects);
        $expectedids = array_flip($expectedids);
        foreach ($objects as $object) {
            $this->assertArrayHasKey($object->id, $expectedids);
            unset($expectedids[$object->id]);
        }
        $this->assertCount(0, $expectedids);
    }

    /**
     * Create a new grade history entry.
     *
     * @param array $params Of values.
     * @return object The grade object.
     */
    protected function create_grade_history($params) {
        global $DB;
        $params = (array) $params;

        if (!isset($params['itemid'])) {
            throw new \coding_exception('Missing itemid key.');
        }
        if (!isset($params['userid'])) {
            throw new \coding_exception('Missing userid key.');
        }

        // Default object.
        $grade = new \stdClass();
        $grade->itemid = 0;
        $grade->userid = 0;
        $grade->oldid = 123;
        $grade->rawgrade = 50;
        $grade->finalgrade = 50;
        $grade->timecreated = time();
        $grade->timemodified = time();
        $grade->information = '';
        $grade->informationformat = FORMAT_PLAIN;
        $grade->feedback = '';
        $grade->feedbackformat = FORMAT_PLAIN;
        $grade->usermodified = 2;

        // Merge with data passed.
        $grade = (object) array_merge((array) $grade, $params);

        // Insert record.
        $grade->id = $DB->insert_record('grade_grades_history', $grade);

        return $grade;
    }

    /**
     * Returns a table log object.
     *
     * @param context_course $coursecontext The course context.
     * @param array $filters An array of filters.
     * @param boolean $count When true, returns a count rather than an array of objects.
     * @return mixed Count or array of objects.
     */
    protected function get_tablelog_results($coursecontext, $filters = array(), $count = false) {
        $table = new gradereport_history_tests_tablelog('something', $coursecontext, new \moodle_url(''), $filters);
        return $table->get_test_results($count);
    }

}

/**
 * Extended table log class.
 */
class gradereport_history_tests_tablelog extends \gradereport_history\output\tablelog {

    /**
     * Get the test results.
     *
     * @param boolean $count Whether or not we want the count.
     * @return mixed Count or array of objects.
     */
    public function get_test_results($count = false) {
        global $DB;
        if ($count) {
            list($sql, $params) = $this->get_sql_and_params(true);
            return $DB->count_records_sql($sql, $params);
        } else {
            $this->setup();
            list($sql, $params) = $this->get_sql_and_params();
            return $DB->get_records_sql($sql, $params);
        }
    }

}

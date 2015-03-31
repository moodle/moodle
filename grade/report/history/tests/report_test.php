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
 * Grade history report test.
 *
 * @package    gradereport_history
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Grade history report test class.
 *
 * @package    gradereport_history
 * @copyright  2014 Frédéric Massart - FMCorz.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradereport_history_report_testcase extends advanced_testcase {

    /**
     * Create some grades.
     */
    public function test_query_db() {
        $this->resetAfterTest();

        // Making the setup.
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        // Users.
        $u1 = $this->getDataGenerator()->create_user();
        $u2 = $this->getDataGenerator()->create_user();
        $u3 = $this->getDataGenerator()->create_user();
        $u4 = $this->getDataGenerator()->create_user();
        $u5 = $this->getDataGenerator()->create_user();
        $grader1 = $this->getDataGenerator()->create_user();
        $grader2 = $this->getDataGenerator()->create_user();

        // Modules.
        $c1m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c1));
        $c1m2 = $this->getDataGenerator()->create_module('assign', array('course' => $c1));
        $c1m3 = $this->getDataGenerator()->create_module('assign', array('course' => $c1));
        $c2m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c2));
        $c2m2 = $this->getDataGenerator()->create_module('assign', array('course' => $c2));

        // Creating fake history data.
        $giparams = array('itemtype' => 'mod', 'itemmodule' => 'assign');
        $grades = array();

        $gi = grade_item::fetch($giparams + array('iteminstance' => $c1m1->id));
        $grades['c1m1u1'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id,
                'timemodified' => time() - 3600));
        $grades['c1m1u2'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u2->id,
                'timemodified' => time() + 3600));
        $grades['c1m1u3'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u3->id));
        $grades['c1m1u4'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u4->id));
        $grades['c1m1u5'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u5->id));

        $gi = grade_item::fetch($giparams + array('iteminstance' => $c1m2->id));
        $grades['c1m2u1'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id));
        $grades['c1m2u2'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u2->id));

        $gi = grade_item::fetch($giparams + array('iteminstance' => $c1m3->id));
        $grades['c1m3u1'] = $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id));

        $gi = grade_item::fetch($giparams + array('iteminstance' => $c2m1->id));
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
        $gi = grade_item::fetch($giparams + array('iteminstance' => $c2m2->id));
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

        // Filtering on 1 user.
        $this->assertEquals(3, $this->get_tablelog_results($c1ctx, array('userids' => $u1->id), true));

        // Filtering on more users.
        $this->assertEquals(4, $this->get_tablelog_results($c1ctx, array('userids' => "$u1->id,$u3->id"), true));

        // Filtering based on one grade item.
        $gi = grade_item::fetch($giparams + array('iteminstance' => $c1m1->id));
        $this->assertEquals(5, $this->get_tablelog_results($c1ctx, array('itemid' => $gi->id), true));
        $gi = grade_item::fetch($giparams + array('iteminstance' => $c1m3->id));
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
        $gi = grade_item::fetch($giparams + array('iteminstance' => $c2m2->id));
        $this->assertEquals(6, $this->get_tablelog_results($c2ctx, array('userids' => $u1->id, 'itemid' => $gi->id), true));
        $results = $this->get_tablelog_results($c2ctx, array('userids' => $u1->id, 'itemid' => $gi->id, 'revisedonly' => true));
        $this->assertGradeHistoryIds(array($grades['c2m2u1a']->id, $grades['c2m2u1c']->id, $grades['c2m2u1e']->id), $results);

        // Checking the value of the previous grade.
        $this->assertEquals(null, $results[$grades['c2m2u1a']->id]->prevgrade);
        $this->assertEquals($grades['c2m2u1a']->finalgrade, $results[$grades['c2m2u1c']->id]->prevgrade);
        $this->assertEquals($grades['c2m2u1c']->finalgrade, $results[$grades['c2m2u1e']->id]->prevgrade);
    }

    /**
     * Test the get users helper method.
     */
    public function test_get_users() {
        $this->resetAfterTest();

        // Making the setup.
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();
        $c1ctx = context_course::instance($c1->id);
        $c2ctx = context_course::instance($c2->id);

        $c1m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c1));
        $c2m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c2));

        // Users.
        $u1 = $this->getDataGenerator()->create_user(array('firstname' => 'Eric', 'lastname' => 'Cartman'));
        $u2 = $this->getDataGenerator()->create_user(array('firstname' => 'Stan', 'lastname' => 'Marsh'));
        $u3 = $this->getDataGenerator()->create_user(array('firstname' => 'Kyle', 'lastname' => 'Broflovski'));
        $u4 = $this->getDataGenerator()->create_user(array('firstname' => 'Kenny', 'lastname' => 'McCormick'));

        // Creating grade history for some users.
        $gi = grade_item::fetch(array('iteminstance' => $c1m1->id, 'itemtype' => 'mod', 'itemmodule' => 'assign'));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u2->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u3->id));

        $gi = grade_item::fetch(array('iteminstance' => $c2m1->id, 'itemtype' => 'mod', 'itemmodule' => 'assign'));
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
     * Test the get graders helper method.
     */
    public function test_graders() {
        $this->resetAfterTest();

        // Making the setup.
        $c1 = $this->getDataGenerator()->create_course();
        $c2 = $this->getDataGenerator()->create_course();

        $c1m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c1));
        $c2m1 = $this->getDataGenerator()->create_module('assign', array('course' => $c2));

        // Users.
        $u1 = $this->getDataGenerator()->create_user(array('firstname' => 'Eric', 'lastname' => 'Cartman'));
        $u2 = $this->getDataGenerator()->create_user(array('firstname' => 'Stan', 'lastname' => 'Marsh'));
        $u3 = $this->getDataGenerator()->create_user(array('firstname' => 'Kyle', 'lastname' => 'Broflovski'));
        $u4 = $this->getDataGenerator()->create_user(array('firstname' => 'Kenny', 'lastname' => 'McCormick'));

        // Creating grade history for some users.
        $gi = grade_item::fetch(array('iteminstance' => $c1m1->id, 'itemtype' => 'mod', 'itemmodule' => 'assign'));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id, 'usermodified' => $u1->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id, 'usermodified' => $u2->id));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id, 'usermodified' => $u3->id));

        $gi = grade_item::fetch(array('iteminstance' => $c2m1->id, 'itemtype' => 'mod', 'itemmodule' => 'assign'));
        $this->create_grade_history(array('itemid' => $gi->id, 'userid' => $u1->id, 'usermodified' => $u4->id));

        // Checking fetching some users.
        $graders = \gradereport_history\helper::get_graders($c1->id);
        $this->assertCount(4, $graders); // Including "all graders" .
        $this->assertArrayHasKey($u1->id, $graders);
        $this->assertArrayHasKey($u2->id, $graders);
        $this->assertArrayHasKey($u3->id, $graders);
        $graders = \gradereport_history\helper::get_graders($c2->id);
        $this->assertCount(2, $graders); // Including "all graders" .
        $this->assertArrayHasKey($u4->id, $graders);
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
            throw new coding_exception('Missing itemid key.');
        }
        if (!isset($params['userid'])) {
            throw new coding_exception('Missing userid key.');
        }

        // Default object.
        $grade = new stdClass();
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
        $table = new gradereport_history_tests_tablelog('something', $coursecontext, new moodle_url(''), $filters);
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

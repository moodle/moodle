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
 * External completion functions unit tests
 *
 * @package    core_completion
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once($CFG->dirroot . '/webservice/tests/helpers.php');

/**
 * External completion functions unit tests
 *
 * @package    core_completion
 * @category   external
 * @copyright  2015 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 2.9
 */
class core_completion_externallib_testcase extends externallib_advanced_testcase {

    /**
     * Test update_activity_completion_status_manually
     */
    public function test_update_activity_completion_status_manually() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $CFG->enablecompletion = true;
        $user = $this->getDataGenerator()->create_user();
        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));
        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id),
                                                             array('completion' => 1));
        $cm = get_coursemodule_from_id('data', $data->cmid);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($user->id, $course->id, $studentrole->id);

        $this->setUser($user);

        $result = core_completion_external::update_activity_completion_status_manually($data->cmid, true);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(
            core_completion_external::update_activity_completion_status_manually_returns(), $result);

        // Check in DB.
        $this->assertEquals(1, $DB->get_field('course_modules_completion', 'completionstate',
                            array('coursemoduleid' => $data->cmid)));

        // Check using the API.
        $completion = new completion_info($course);
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(1, $completiondata->completionstate);
        $this->assertTrue($result['status']);

        $result = core_completion_external::update_activity_completion_status_manually($data->cmid, false);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(
            core_completion_external::update_activity_completion_status_manually_returns(), $result);

        $this->assertEquals(0, $DB->get_field('course_modules_completion', 'completionstate',
                            array('coursemoduleid' => $data->cmid)));
        $completiondata = $completion->get_data($cm);
        $this->assertEquals(0, $completiondata->completionstate);
        $this->assertTrue($result['status']);
    }

    /**
     * Test update_activity_completion_status
     */
    public function test_get_activities_completion_status() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $CFG->enablecompletion = true;
        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1,
                                                                    'groupmode' => SEPARATEGROUPS,
                                                                    'groupmodeforce' => 1));

        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id),
                                                             array('completion' => 1));
        $forum = $this->getDataGenerator()->create_module('forum',  array('course' => $course->id),
                                                             array('completion' => 1));
        $assign = $this->getDataGenerator()->create_module('assign',  array('course' => $course->id));
        $page = $this->getDataGenerator()->create_module('page',  array('course' => $course->id),
                                                            array('completion' => 1, 'visible' => 0));

        $cmdata = get_coursemodule_from_id('data', $data->cmid);
        $cmforum = get_coursemodule_from_id('forum', $forum->cmid);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));

        // Teacher and student in different groups initially.
        groups_add_member($group1->id, $student->id);
        groups_add_member($group2->id, $teacher->id);

        $this->setUser($student);
        // Forum complete.
        $completion = new completion_info($course);
        $completion->update_state($cmforum, COMPLETION_COMPLETE);

        $result = core_completion_external::get_activities_completion_status($course->id, $student->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(
            core_completion_external::get_activities_completion_status_returns(), $result);

        // We added 4 activities, but only 3 with completion enabled and one of those is hidden.
        $this->assertCount(2, $result['statuses']);

        $activitiesfound = 0;
        foreach ($result['statuses'] as $status) {
            if ($status['cmid'] == $forum->cmid and $status['modname'] == 'forum' and $status['instance'] == $forum->id) {
                $activitiesfound++;
                $this->assertEquals(COMPLETION_COMPLETE, $status['state']);
                $this->assertEquals(COMPLETION_TRACKING_MANUAL, $status['tracking']);
            } else if ($status['cmid'] == $data->cmid and $status['modname'] == 'data' and $status['instance'] == $data->id) {
                $activitiesfound++;
                $this->assertEquals(COMPLETION_INCOMPLETE, $status['state']);
                $this->assertEquals(COMPLETION_TRACKING_MANUAL, $status['tracking']);
            }
        }
        $this->assertEquals(2, $activitiesfound);

        // Teacher should see students status, they are in different groups but the teacher can access all groups.
        $this->setUser($teacher);
        $result = core_completion_external::get_activities_completion_status($course->id, $student->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(
            core_completion_external::get_activities_completion_status_returns(), $result);

        // We added 4 activities, but only 3 with completion enabled and one of those is hidden.
        $this->assertCount(3, $result['statuses']);

        // Change teacher role capabilities (disable access al goups).
        $context = context_course::instance($course->id);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $teacherrole->id, $context);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            $result = core_completion_external::get_activities_completion_status($course->id, $student->id);
            $this->fail('Exception expected due to groups permissions.');
        } catch (moodle_exception $e) {
            $this->assertEquals('accessdenied', $e->errorcode);
        }

        // Now add the teacher in the same group.
        groups_add_member($group1->id, $teacher->id);
        $result = core_completion_external::get_activities_completion_status($course->id, $student->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(
            core_completion_external::get_activities_completion_status_returns(), $result);
        // We added 4 activities, but only 3 with completion enabled and one of those is hidden.
        $this->assertCount(3, $result['statuses']);
    }

    /**
     * Test get_course_completion_status
     */
    public function test_get_course_completion_status() {
        global $DB, $CFG, $COMPLETION_CRITERIA_TYPES;
        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_self.php');
        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_date.php');
        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_unenrol.php');
        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_activity.php');
        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_duration.php');
        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_grade.php');
        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_role.php');
        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_course.php');

        $this->resetAfterTest(true);

        $CFG->enablecompletion = true;
        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1,
                                                                    'groupmode' => SEPARATEGROUPS,
                                                                    'groupmodeforce' => 1));

        $data = $this->getDataGenerator()->create_module('data', array('course' => $course->id),
                                                             array('completion' => 1));
        $forum = $this->getDataGenerator()->create_module('forum',  array('course' => $course->id),
                                                             array('completion' => 1));
        $assign = $this->getDataGenerator()->create_module('assign',  array('course' => $course->id));

        $cmdata = get_coursemodule_from_id('data', $data->cmid);
        $cmforum = get_coursemodule_from_id('forum', $forum->cmid);

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $teacherrole = $DB->get_record('role', array('shortname' => 'editingteacher'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);
        $this->getDataGenerator()->enrol_user($teacher->id, $course->id, $teacherrole->id);

        $group1 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        $group2 = $this->getDataGenerator()->create_group(array('courseid' => $course->id));
        // Teacher and student in different groups initially.
        groups_add_member($group1->id, $student->id);
        groups_add_member($group2->id, $teacher->id);

        // Set completion rules.
        $completion = new completion_info($course);

        // Loop through each criteria type and run its update_config() method.

        $criteriadata = new stdClass();
        $criteriadata->id = $course->id;
        $criteriadata->criteria_activity = array();
        // Some activities.
        $criteriadata->criteria_activity[$cmdata->id] = 1;
        $criteriadata->criteria_activity[$cmforum->id] = 1;

        // In a week criteria date value.
        $criteriadata->criteria_date_value = time() + WEEKSECS;

        // Self completion.
        $criteriadata->criteria_self = 1;

        foreach ($COMPLETION_CRITERIA_TYPES as $type) {
            $class = 'completion_criteria_'.$type;
            $criterion = new $class();
            $criterion->update_config($criteriadata);
        }

        // Handle overall aggregation.
        $aggdata = array(
            'course'        => $course->id,
            'criteriatype'  => null
        );
        $aggregation = new completion_aggregation($aggdata);
        $aggregation->setMethod(COMPLETION_AGGREGATION_ALL);
        $aggregation->save();

        $aggdata['criteriatype'] = COMPLETION_CRITERIA_TYPE_ACTIVITY;
        $aggregation = new completion_aggregation($aggdata);
        $aggregation->setMethod(COMPLETION_AGGREGATION_ALL);
        $aggregation->save();

        $this->setUser($student);

        $result = core_completion_external::get_course_completion_status($course->id, $student->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $studentresult = external_api::clean_returnvalue(
            core_completion_external::get_course_completion_status_returns(), $result);

        // 3 different criteria.
        $this->assertCount(3, $studentresult['completionstatus']['completions']);

        $this->assertEquals(COMPLETION_AGGREGATION_ALL, $studentresult['completionstatus']['aggregation']);
        $this->assertFalse($studentresult['completionstatus']['completed']);

        $this->assertEquals('No', $studentresult['completionstatus']['completions'][0]['status']);
        $this->assertEquals('No', $studentresult['completionstatus']['completions'][1]['status']);
        $this->assertEquals('No', $studentresult['completionstatus']['completions'][2]['status']);

        // Teacher should see students status, they are in different groups but the teacher can access all groups.
        $this->setUser($teacher);
        $result = core_completion_external::get_course_completion_status($course->id, $student->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $teacherresult = external_api::clean_returnvalue(
            core_completion_external::get_course_completion_status_returns(), $result);

        $this->assertEquals($studentresult, $teacherresult);

        // Change teacher role capabilities (disable access al goups).
        $context = context_course::instance($course->id);
        assign_capability('moodle/site:accessallgroups', CAP_PROHIBIT, $teacherrole->id, $context);
        accesslib_clear_all_caches_for_unit_testing();

        try {
            $result = core_completion_external::get_course_completion_status($course->id, $student->id);
            $this->fail('Exception expected due to groups permissions.');
        } catch (moodle_exception $e) {
            $this->assertEquals('accessdenied', $e->errorcode);
        }

        // Now add the teacher in the same group.
        groups_add_member($group1->id, $teacher->id);
        $result = core_completion_external::get_course_completion_status($course->id, $student->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $teacherresult = external_api::clean_returnvalue(
            core_completion_external::get_course_completion_status_returns(), $result);

        $this->assertEquals($studentresult, $teacherresult);

    }

    /**
     * Test mark_course_self_completed
     */
    public function test_mark_course_self_completed() {
        global $DB, $CFG;
        require_once($CFG->dirroot.'/completion/criteria/completion_criteria_self.php');

        $this->resetAfterTest(true);

        $CFG->enablecompletion = true;
        $student = $this->getDataGenerator()->create_user();
        $teacher = $this->getDataGenerator()->create_user();

        $course = $this->getDataGenerator()->create_course(array('enablecompletion' => 1));

        $studentrole = $DB->get_record('role', array('shortname' => 'student'));
        $this->getDataGenerator()->enrol_user($student->id, $course->id, $studentrole->id);

        // Set completion rules.
        $completion = new completion_info($course);

        $criteriadata = new stdClass();
        $criteriadata->id = $course->id;
        $criteriadata->criteria_activity = array();

        // Self completion.
        $criteriadata->criteria_self = COMPLETION_CRITERIA_TYPE_SELF;
        $class = 'completion_criteria_self';
        $criterion = new $class();
        $criterion->update_config($criteriadata);

        // Handle overall aggregation.
        $aggdata = array(
            'course'        => $course->id,
            'criteriatype'  => null
        );
        $aggregation = new completion_aggregation($aggdata);
        $aggregation->setMethod(COMPLETION_AGGREGATION_ALL);
        $aggregation->save();

        $this->setUser($student);

        $result = core_completion_external::mark_course_self_completed($course->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(
            core_completion_external::mark_course_self_completed_returns(), $result);

        // We expect a valid result.
        $this->assertEquals(true, $result['status']);

        $result = core_completion_external::get_course_completion_status($course->id, $student->id);
        // We need to execute the return values cleaning process to simulate the web service server.
        $result = external_api::clean_returnvalue(
            core_completion_external::get_course_completion_status_returns(), $result);

        // Course must be completed.
        $this->assertEquals(COMPLETION_COMPLETE, $result['completionstatus']['completions'][0]['complete']);

        try {
            $result = core_completion_external::mark_course_self_completed($course->id);
            $this->fail('Exception expected due course already self completed.');
        } catch (moodle_exception $e) {
            $this->assertEquals('useralreadymarkedcomplete', $e->errorcode);
        }

    }

}

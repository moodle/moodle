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
 * Unit tests for privacy.
 *
 * @package   core_analytics
 * @copyright 2018 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_analytics\privacy;

use core_analytics\privacy\provider;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../fixtures/test_indicator_max.php');
require_once(__DIR__ . '/../fixtures/test_indicator_min.php');
require_once(__DIR__ . '/../fixtures/test_target_site_users.php');
require_once(__DIR__ . '/../fixtures/test_target_course_users.php');

/**
 * Unit tests for privacy.
 *
 * @package   core_analytics
 * @copyright 2018 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class provider_test extends \core_privacy\tests\provider_testcase {

    /** @var \core_analytics\model Store Model 1. */
    protected $model1;

    /** @var \core_analytics\model Store Model 2. */
    protected $model2;

    /** @var \stdClass $modelobj1 Store Model 1 object. */
    protected $modelobj1;

    /** @var \stdClass $modelobj2 Store Model 2 object. */
    protected $modelobj2;

    /** @var \stdClass $u1 User 1 record. */
    protected $u1;

    /** @var \stdClass $u2 User 2 record. */
    protected $u2;

    /** @var \stdClass $u3 User 3 record. */
    protected $u3;

    /** @var \stdClass $u4 User 4 record. */
    protected $u4;

    /** @var \stdClass $u5 User 5 record. */
    protected $u5;

    /** @var \stdClass $u6 User 6 record. */
    protected $u6;

    /** @var \stdClass $u7 User 7 record. */
    protected $u7;

    /** @var \stdClass $u8 User 8 record. */
    protected $u8;

    /** @var \stdClass $c1 Course 1 record. */
    protected $c1;

    /** @var \stdClass $c2 Course 2 record. */
    protected $c2;

    public function setUp(): void {

        $this->resetAfterTest(true);
        $this->setAdminUser();

        $timesplittingid = '\core\analytics\time_splitting\single_range';
        $target = \core_analytics\manager::get_target('test_target_site_users');
        $indicators = array('test_indicator_max');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }
        $this->model1 = \core_analytics\model::create($target, $indicators, $timesplittingid);
        $this->modelobj1 = $this->model1->get_model_obj();

        $target = \core_analytics\manager::get_target('test_target_course_users');
        $indicators = array('test_indicator_min');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }
        $this->model2 = \core_analytics\model::create($target, $indicators, $timesplittingid);
        $this->modelobj2 = $this->model1->get_model_obj();

        $this->u1 = $this->getDataGenerator()->create_user(['firstname' => 'a111111111111', 'lastname' => 'a']);
        $this->u2 = $this->getDataGenerator()->create_user(['firstname' => 'a222222222222', 'lastname' => 'a']);
        $this->u3 = $this->getDataGenerator()->create_user(['firstname' => 'b333333333333', 'lastname' => 'b']);
        $this->u4 = $this->getDataGenerator()->create_user(['firstname' => 'b444444444444', 'lastname' => 'b']);
        $this->u5 = $this->getdatagenerator()->create_user(['firstname' => 'a555555555555', 'lastname' => 'a']);
        $this->u6 = $this->getdatagenerator()->create_user(['firstname' => 'a666666666666', 'lastname' => 'a']);
        $this->u7 = $this->getdatagenerator()->create_user(['firstname' => 'b777777777777', 'lastname' => 'b']);
        $this->u8 = $this->getDataGenerator()->create_user(['firstname' => 'b888888888888', 'lastname' => 'b']);

        $this->c1 = $this->getDataGenerator()->create_course(['visible' => false]);
        $this->c2 = $this->getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($this->u1->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u2->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u3->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u4->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u5->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u6->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u7->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u8->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u1->id, $this->c2->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u2->id, $this->c2->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u3->id, $this->c2->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u4->id, $this->c2->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u5->id, $this->c2->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u6->id, $this->c2->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u7->id, $this->c2->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u8->id, $this->c2->id, 'student');

        $this->setAdminUser();

        $this->model1->enable();
        $this->model1->train();
        $this->model1->predict();
        $this->model2->enable();
        $this->model2->train();
        $this->model2->predict();

        list($total, $predictions) = $this->model2->get_predictions(\context_course::instance($this->c1->id));

        $this->setUser($this->u3);
        $prediction = reset($predictions);
        $prediction->action_executed(\core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED, $this->model2->get_target());

        $this->setAdminUser();
    }

    /**
     * Test fetching users within a context.
     */
    public function test_get_users_in_context() {
        global $CFG;

        $component = 'core_analytics';
        $course1context = \context_course::instance($this->c1->id);
        $course2context = \context_course::instance($this->c2->id);
        $systemcontext = \context_system::instance();
        $expected = [$this->u1->id, $this->u2->id, $this->u3->id, $this->u4->id, $this->u5->id, $this->u6->id,
            $this->u7->id, $this->u8->id];

        // Check users exist in the relevant contexts.
        $userlist = new \core_privacy\local\request\userlist($course1context, $component);
        provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        sort($actual);
        $this->assertEquals($expected, $actual);

        $userlist = new \core_privacy\local\request\userlist($course2context, $component);
        provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        sort($actual);
        $this->assertEquals($expected, $actual);

        // System context will also find guest and admin user, add to expected before testing.
        $expected = array_merge($expected, [$CFG->siteguest, get_admin()->id]);
        sort($expected);

        $userlist = new \core_privacy\local\request\userlist($systemcontext, $component);
        provider::get_users_in_context($userlist);
        $actual = $userlist->get_userids();
        sort($actual);
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test delete a context.
     *
     * @return null
     */
    public function test_delete_context_data() {
        global $DB;

        // We have 4 predictions for model1 and 8 predictions for model2.
        $this->assertEquals(12, $DB->count_records('analytics_predictions'));
        $this->assertEquals(26, $DB->count_records('analytics_indicator_calc'));

        // We have 1 prediction action.
        $this->assertEquals(1, $DB->count_records('analytics_prediction_actions'));

        $coursecontext = \context_course::instance($this->c1->id);

        // Delete the course that was used for prediction.
        provider::delete_data_for_all_users_in_context($coursecontext);

        // The course1 predictions are deleted.
        $this->assertEquals(8, $DB->count_records('analytics_predictions'));

        // Calculations related to that context are deleted.
        $this->assertEmpty($DB->count_records('analytics_indicator_calc', ['contextid' => $coursecontext->id]));

        // The deleted context prediction actions are deleted as well.
        $this->assertEquals(0, $DB->count_records('analytics_prediction_actions'));
    }

    /**
     * Test delete a user.
     *
     * @return null
     */
    public function test_delete_user_data() {
        global $DB;

        $usercontexts = provider::get_contexts_for_userid($this->u3->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->u3, 'core_analytics',
                                                                            $usercontexts->get_contextids());
        provider::delete_data_for_user($contextlist);

        // The site level prediction for u3 was deleted.
        $this->assertEquals(9, $DB->count_records('analytics_predictions'));
        $this->assertEquals(0, $DB->count_records('analytics_prediction_actions'));

        $usercontexts = provider::get_contexts_for_userid($this->u1->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->u1, 'core_analytics',
                                                                            $usercontexts->get_contextids());
        provider::delete_data_for_user($contextlist);
        // We have nothing for u1.
        $this->assertEquals(9, $DB->count_records('analytics_predictions'));

        $usercontexts = provider::get_contexts_for_userid($this->u4->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->u4, 'core_analytics',
                                                                            $usercontexts->get_contextids());
        provider::delete_data_for_user($contextlist);
        $this->assertEquals(6, $DB->count_records('analytics_predictions'));
    }

    /**
     * Test deleting multiple users in a context.
     */
    public function test_delete_data_for_users() {
        global $DB;

        $component = 'core_analytics';
        $course1context = \context_course::instance($this->c1->id);
        $course2context = \context_course::instance($this->c2->id);
        $systemcontext = \context_system::instance();

        // Ensure all records exist in expected contexts.
        $expectedcontexts = [$course1context->id, $course2context->id, $systemcontext->id];
        sort($expectedcontexts);

        $actualcontexts = [
            $this->u1->id => provider::get_contexts_for_userid($this->u1->id)->get_contextids(),
            $this->u2->id => provider::get_contexts_for_userid($this->u2->id)->get_contextids(),
            $this->u3->id => provider::get_contexts_for_userid($this->u3->id)->get_contextids(),
            $this->u4->id => provider::get_contexts_for_userid($this->u4->id)->get_contextids(),
            $this->u5->id => provider::get_contexts_for_userid($this->u5->id)->get_contextids(),
            $this->u6->id => provider::get_contexts_for_userid($this->u6->id)->get_contextids(),
            $this->u7->id => provider::get_contexts_for_userid($this->u7->id)->get_contextids(),
            $this->u8->id => provider::get_contexts_for_userid($this->u8->id)->get_contextids(),
        ];

        foreach ($actualcontexts as $userid => $unused) {
            sort($actualcontexts[$userid]);
            $this->assertEquals($expectedcontexts, $actualcontexts[$userid]);
        }

        // Test initial record counts are as expected.
        $this->assertEquals(12, $DB->count_records('analytics_predictions'));
        $this->assertEquals(1, $DB->count_records('analytics_prediction_actions'));
        $this->assertEquals(26, $DB->count_records('analytics_indicator_calc'));

        // Delete u1 and u3 from system context.
        $approveduserids = [$this->u1->id, $this->u3->id];
        $approvedlist = new approved_userlist($systemcontext, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        // Ensure u1 and u3 system context data deleted only.
        $expectedcontexts = [
            $this->u1->id => [$course1context->id, $course2context->id],
            $this->u2->id => [$systemcontext->id, $course1context->id, $course2context->id],
            $this->u3->id => [$course1context->id, $course2context->id],
            $this->u4->id => [$systemcontext->id, $course1context->id, $course2context->id],
            $this->u5->id => [$systemcontext->id, $course1context->id, $course2context->id],
            $this->u6->id => [$systemcontext->id, $course1context->id, $course2context->id],
            $this->u7->id => [$systemcontext->id, $course1context->id, $course2context->id],
            $this->u8->id => [$systemcontext->id, $course1context->id, $course2context->id],
        ];

        $actualcontexts = [
            $this->u1->id => provider::get_contexts_for_userid($this->u1->id)->get_contextids(),
            $this->u2->id => provider::get_contexts_for_userid($this->u2->id)->get_contextids(),
            $this->u3->id => provider::get_contexts_for_userid($this->u3->id)->get_contextids(),
            $this->u4->id => provider::get_contexts_for_userid($this->u4->id)->get_contextids(),
            $this->u5->id => provider::get_contexts_for_userid($this->u5->id)->get_contextids(),
            $this->u6->id => provider::get_contexts_for_userid($this->u6->id)->get_contextids(),
            $this->u7->id => provider::get_contexts_for_userid($this->u7->id)->get_contextids(),
            $this->u8->id => provider::get_contexts_for_userid($this->u8->id)->get_contextids(),
        ];

        foreach ($actualcontexts as $userid => $unused) {
            sort($expectedcontexts[$userid]);
            sort($actualcontexts[$userid]);
            $this->assertEquals($expectedcontexts[$userid], $actualcontexts[$userid]);
        }

        // Test expected number of records have been deleted.
        $this->assertEquals(11, $DB->count_records('analytics_predictions'));
        $this->assertEquals(1, $DB->count_records('analytics_prediction_actions'));
        $this->assertEquals(24, $DB->count_records('analytics_indicator_calc'));

        // Delete for all 8 users in course 2 context.
        $approveduserids = [$this->u1->id, $this->u2->id, $this->u3->id, $this->u4->id, $this->u5->id, $this->u6->id,
            $this->u7->id, $this->u8->id];
        $approvedlist = new approved_userlist($course2context, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        // Ensure all course 2 context data deleted for all 4 users.
        $expectedcontexts = [
            $this->u1->id => [$course1context->id],
            $this->u2->id => [$systemcontext->id, $course1context->id],
            $this->u3->id => [$course1context->id],
            $this->u4->id => [$systemcontext->id, $course1context->id],
            $this->u5->id => [$systemcontext->id, $course1context->id],
            $this->u6->id => [$systemcontext->id, $course1context->id],
            $this->u7->id => [$systemcontext->id, $course1context->id],
            $this->u8->id => [$systemcontext->id, $course1context->id],
        ];

        $actualcontexts = [
            $this->u1->id => provider::get_contexts_for_userid($this->u1->id)->get_contextids(),
            $this->u2->id => provider::get_contexts_for_userid($this->u2->id)->get_contextids(),
            $this->u3->id => provider::get_contexts_for_userid($this->u3->id)->get_contextids(),
            $this->u4->id => provider::get_contexts_for_userid($this->u4->id)->get_contextids(),
            $this->u5->id => provider::get_contexts_for_userid($this->u5->id)->get_contextids(),
            $this->u6->id => provider::get_contexts_for_userid($this->u6->id)->get_contextids(),
            $this->u7->id => provider::get_contexts_for_userid($this->u7->id)->get_contextids(),
            $this->u8->id => provider::get_contexts_for_userid($this->u8->id)->get_contextids(),
        ];

        foreach ($actualcontexts as $userid => $unused) {
            sort($actualcontexts[$userid]);
            sort($expectedcontexts[$userid]);
            $this->assertEquals($expectedcontexts[$userid], $actualcontexts[$userid]);
        }

        // Test expected number of records have been deleted.
        $this->assertEquals(7, $DB->count_records('analytics_predictions'));
        $this->assertEquals(1, $DB->count_records('analytics_prediction_actions'));
        $this->assertEquals(16, $DB->count_records('analytics_indicator_calc'));

        $approveduserids = [$this->u3->id];
        $approvedlist = new approved_userlist($course1context, $component, $approveduserids);
        provider::delete_data_for_users($approvedlist);

        // Ensure all course 1 context data deleted for u3.
        $expectedcontexts = [
            $this->u1->id => [$course1context->id],
            $this->u2->id => [$systemcontext->id, $course1context->id],
            $this->u3->id => [],
            $this->u4->id => [$systemcontext->id, $course1context->id],
            $this->u5->id => [$systemcontext->id, $course1context->id],
            $this->u6->id => [$systemcontext->id, $course1context->id],
            $this->u7->id => [$systemcontext->id, $course1context->id],
            $this->u8->id => [$systemcontext->id, $course1context->id],
        ];

        $actualcontexts = [
            $this->u1->id => provider::get_contexts_for_userid($this->u1->id)->get_contextids(),
            $this->u2->id => provider::get_contexts_for_userid($this->u2->id)->get_contextids(),
            $this->u3->id => provider::get_contexts_for_userid($this->u3->id)->get_contextids(),
            $this->u4->id => provider::get_contexts_for_userid($this->u4->id)->get_contextids(),
            $this->u5->id => provider::get_contexts_for_userid($this->u5->id)->get_contextids(),
            $this->u6->id => provider::get_contexts_for_userid($this->u6->id)->get_contextids(),
            $this->u7->id => provider::get_contexts_for_userid($this->u7->id)->get_contextids(),
            $this->u8->id => provider::get_contexts_for_userid($this->u8->id)->get_contextids(),
        ];
        foreach ($actualcontexts as $userid => $unused) {
            sort($actualcontexts[$userid]);
            sort($expectedcontexts[$userid]);
            $this->assertEquals($expectedcontexts[$userid], $actualcontexts[$userid]);
        }

        // Test expected number of records have been deleted.
        $this->assertEquals(6, $DB->count_records('analytics_predictions'));
        $this->assertEquals(0, $DB->count_records('analytics_prediction_actions'));
        $this->assertEquals(15, $DB->count_records('analytics_indicator_calc'));
    }

    /**
     * Test export user data.
     *
     * @return null
     */
    public function test_export_data() {
        global $DB;

        $system = \context_system::instance();
        list($total, $predictions) = $this->model1->get_predictions($system);
        foreach ($predictions as $key => $prediction) {
            if ($prediction->get_prediction_data()->sampleid !== $this->u3->id) {
                $otheruserprediction = $prediction;
                break;
            }
        }
        $this->setUser($this->u3);
        $otheruserprediction->action_executed(\core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED, $this->model1->get_target());
        $this->setAdminUser();

        $this->export_context_data_for_user($this->u3->id, $system, 'core_analytics');
        $writer = \core_privacy\local\request\writer::with_context($system);
        $this->assertTrue($writer->has_any_data());

        $u3prediction = $DB->get_record('analytics_predictions', ['contextid' => $system->id, 'sampleid' => $this->u3->id]);
        $data = $writer->get_data([get_string('analytics', 'analytics'),
            get_string('privacy:metadata:analytics:predictions', 'analytics'), $u3prediction->id]);
        $this->assertEquals(get_string('adminhelplogs'), $data->target);
        $this->assertEquals(get_string('coresystem'), $data->context);
        $this->assertEquals('firstname first char is not A', $data->prediction);

        $u3calculation = $DB->get_record('analytics_indicator_calc', ['contextid' => $system->id, 'sampleid' => $this->u3->id]);
        $data = $writer->get_data([get_string('analytics', 'analytics'),
            get_string('privacy:metadata:analytics:indicatorcalc', 'analytics'), $u3calculation->id]);
        $this->assertEquals('Allow stealth activities', $data->indicator);
        $this->assertEquals(get_string('coresystem'), $data->context);
        $this->assertEquals(get_string('yes'), $data->calculation);

        $sql = "SELECT apa.id FROM {analytics_prediction_actions} apa
                  JOIN {analytics_predictions} ap ON ap.id = apa.predictionid
                 WHERE ap.contextid = :contextid AND apa.userid = :userid AND ap.modelid = :modelid";
        $params = ['contextid' => $system->id, 'userid' => $this->u3->id, 'modelid' => $this->model1->get_id()];
        $u3action = $DB->get_record_sql($sql, $params);
        $data = $writer->get_data([get_string('analytics', 'analytics'),
            get_string('privacy:metadata:analytics:predictionactions', 'analytics'), $u3action->id]);
        $this->assertEquals(get_string('adminhelplogs'), $data->target);
        $this->assertEquals(get_string('coresystem'), $data->context);
        $this->assertEquals(\core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED, $data->action);

    }
}

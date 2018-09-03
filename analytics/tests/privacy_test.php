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

use \core_analytics\privacy\provider;
use core_privacy\local\request\transform;
use core_privacy\local\request\writer;
use core_privacy\local\request\approved_contextlist;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_indicator_max.php');
require_once(__DIR__ . '/fixtures/test_indicator_min.php');
require_once(__DIR__ . '/fixtures/test_target_site_users.php');
require_once(__DIR__ . '/fixtures/test_target_course_users.php');
require_once(__DIR__ . '/fixtures/test_analyser.php');

/**
 * Unit tests for privacy.
 *
 * @package   core_analytics
 * @copyright 2018 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_analytics_privacy_model_testcase extends \core_privacy\tests\provider_testcase {

    public function setUp() {

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

        $this->c1 = $this->getDataGenerator()->create_course(['visible' => false]);
        $this->c2 = $this->getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($this->u1->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u2->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u3->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u4->id, $this->c1->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u1->id, $this->c2->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u2->id, $this->c2->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u3->id, $this->c2->id, 'student');
        $this->getDataGenerator()->enrol_user($this->u4->id, $this->c2->id, 'student');

        $this->setAdminUser();

        $this->model1->train();
        $this->model1->predict();
        $this->model2->train();
        $this->model2->predict();

        list($total, $predictions) = $this->model2->get_predictions(\context_course::instance($this->c1->id));

        $this->setUser($this->u3);
        $prediction = reset($predictions);
        $prediction->action_executed('notuseful', $this->model2->get_target());

        $this->setAdminUser();
    }

    /**
     * Test delete a context.
     *
     * @return null
     */
    public function test_delete_context_data() {
        global $DB;

        // We have 2 predictions for model1 and 4 predictions for model2.
        $this->assertEquals(6, $DB->count_records('analytics_predictions'));
        $this->assertEquals(14, $DB->count_records('analytics_indicator_calc'));

        // We have 1 prediction action.
        $this->assertEquals(1, $DB->count_records('analytics_prediction_actions'));

        $coursecontext = \context_course::instance($this->c1->id);

        // Delete the course that was used for prediction.
        provider::delete_data_for_all_users_in_context($coursecontext);

        // The course predictions are deleted.
        $this->assertEquals(4, $DB->count_records('analytics_predictions'));

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
        $this->assertEquals(3, $DB->count_records('analytics_predictions'));
        $this->assertEquals(0, $DB->count_records('analytics_prediction_actions'));

        $usercontexts = provider::get_contexts_for_userid($this->u1->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->u1, 'core_analytics',
                                                                            $usercontexts->get_contextids());
        provider::delete_data_for_user($contextlist);
        // We have nothing for u1.
        $this->assertEquals(3, $DB->count_records('analytics_predictions'));

        $usercontexts = provider::get_contexts_for_userid($this->u4->id);
        $contextlist = new \core_privacy\local\request\approved_contextlist($this->u4, 'core_analytics',
                                                                            $usercontexts->get_contextids());
        provider::delete_data_for_user($contextlist);
        $this->assertEquals(0, $DB->count_records('analytics_predictions'));
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
        $otheruserprediction->action_executed('notuseful', $this->model1->get_target());
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
        $this->assertEquals('notuseful', $data->action);

    }
}

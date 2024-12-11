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

namespace core_analytics;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_indicator_max.php');
require_once(__DIR__ . '/fixtures/test_target_shortname.php');

/**
 * Unit tests for prediction actions.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class prediction_actions_test extends \advanced_testcase {

    /** @var model Store Model. */
    protected $model;

    /** @var \stdClass Store model object. */
    protected $modelobj;

    /** @var \stdClass Course 1 record. */
    protected $course1;

    /** @var \stdClass Course 2 record. */
    protected $course2;

    /** @var \context_course Store Model. */
    protected $context;

    /** @var \stdClass Teacher 1 user record. */
    protected $teacher1;

    /** @var \stdClass Teacher 2 user record. */
    protected $teacher2;

    /** @var \stdClass Teacher 3 user record. */
    protected $teacher3;

    /**
     * Common startup tasks
     */
    public function setUp(): void {
        global $DB;
        parent::setUp();

        $this->setAdminUser();
        $target = \core_analytics\manager::get_target('test_target_shortname');
        $indicators = array('test_indicator_max');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }

        $this->model = \core_analytics\model::create($target, $indicators);
        $this->modelobj = $this->model->get_model_obj();
        $this->model->enable('\core\analytics\time_splitting\single_range');

        $this->resetAfterTest(true);

        $this->course1 = $this->getDataGenerator()->create_course();
        $this->course2 = $this->getDataGenerator()->create_course();
        $this->context = \context_course::instance($this->course1->id);

        $this->teacher1 = $this->getDataGenerator()->create_user();
        $this->teacher2 = $this->getDataGenerator()->create_user();
        $this->teacher3 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($this->teacher1->id, $this->course1->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($this->teacher2->id, $this->course1->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($this->teacher3->id, $this->course1->id, 'editingteacher');

        // The only relevant fields are modelid, contextid and sampleid. I'm cheating and setting
        // contextid as the course context so teachers can access these predictions.
        $pred = new \stdClass();
        $pred->modelid = $this->model->get_id();
        $pred->contextid = $this->context->id;
        $pred->sampleid = $this->course1->id;
        $pred->rangeindex = 1;
        $pred->prediction = 1;
        $pred->predictionscore = 1;
        $pred->calculations = json_encode(array('test_indicator_max' => 1));
        $pred->timecreated = time();
        $DB->insert_record('analytics_predictions', $pred);

        $pred->sampleid = $this->course2->id;
        $DB->insert_record('analytics_predictions', $pred);
    }

    /**
     * test_get_predictions
     */
    public function test_action_executed(): void {
        global $DB;

        $this->assertEquals(0, $DB->count_records('analytics_prediction_actions'));

        // Teacher 2 flags a prediction (it doesn't matter which one) as fixed.
        $this->setUser($this->teacher2);
        list($ignored, $predictions) = $this->model->get_predictions($this->context, true);
        $prediction = reset($predictions);
        $prediction->action_executed(\core_analytics\prediction::ACTION_FIXED, $this->model->get_target());

        $recordset = $this->model->get_prediction_actions($this->context);
        $this->assertCount(1, $recordset);
        $recordset->close();
        $this->assertEquals(1, $DB->count_records('analytics_prediction_actions'));
        $action = $DB->get_record('analytics_prediction_actions', array('userid' => $this->teacher2->id));
        $this->assertEquals(\core_analytics\prediction::ACTION_FIXED, $action->actionname);

        $prediction->action_executed(\core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED, $this->model->get_target());
        $recordset = $this->model->get_prediction_actions($this->context);
        $this->assertCount(2, $recordset);
        $recordset->close();
        $this->assertEquals(2, $DB->count_records('analytics_prediction_actions'));
    }

    /**
     * Data provider for test_get_executed_actions.
     *
     * @return  array
     */
    public static function execute_actions_provider(): array {
        return [
            'Empty actions with no filter' => [
                [],
                [],
                0
            ],
            'Empty actions with filter' => [
                [],
                [\core_analytics\prediction::ACTION_FIXED],
                0
            ],
            'Multiple actions with no filter' => [
                [
                    \core_analytics\prediction::ACTION_FIXED,
                    \core_analytics\prediction::ACTION_FIXED,
                    \core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED
                ],
                [],
                3
            ],
            'Multiple actions applying filter' => [
                [
                    \core_analytics\prediction::ACTION_FIXED,
                    \core_analytics\prediction::ACTION_FIXED,
                    \core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED
                ],
                [\core_analytics\prediction::ACTION_FIXED],
                2
            ],
            'Multiple actions not applying filter' => [
                [
                    \core_analytics\prediction::ACTION_FIXED,
                    \core_analytics\prediction::ACTION_FIXED,
                    \core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED
                ],
                [\core_analytics\prediction::ACTION_NOT_APPLICABLE],
                0
            ],
            'Multiple actions with multiple filter' => [
                [
                    \core_analytics\prediction::ACTION_FIXED,
                    \core_analytics\prediction::ACTION_FIXED,
                    \core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED
                ],
                [\core_analytics\prediction::ACTION_FIXED, \core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED],
                3
            ],
        ];
    }

    /**
     * Tests for get_executed_actions() function.
     *
     * @dataProvider    execute_actions_provider
     * @param   array   $actionstoexecute    An array of actions to execute
     * @param   array   $actionnamefilter   Actions to filter
     * @param   int     $returned             Number of actions returned
     *
     * @covers \core_analytics\prediction::get_executed_actions
     */
    public function test_get_executed_actions(array $actionstoexecute, array $actionnamefilter, int $returned): void {

        $this->setUser($this->teacher2);
        list($ignored, $predictions) = $this->model->get_predictions($this->context, true);
        $prediction = reset($predictions);
        $target = $this->model->get_target();
        foreach($actionstoexecute as $action) {
            $prediction->action_executed($action, $target);
        }

        $filteredactions = $prediction->get_executed_actions($actionnamefilter);
        $this->assertCount($returned, $filteredactions);
    }

    /**
     * test_get_predictions
     */
    public function test_get_predictions(): void {
        global $DB;

        // Already logged in as admin.
        list($ignored, $predictions) = $this->model->get_predictions($this->context, true);
        $this->assertCount(2, $predictions);

        $this->setUser($this->teacher1);
        list($ignored, $predictions) = $this->model->get_predictions($this->context, true);
        $this->assertCount(2, $predictions);

        $this->setUser($this->teacher2);
        list($ignored, $predictions) = $this->model->get_predictions($this->context, false);
        $this->assertCount(2, $predictions);

        // Teacher 2 flags a prediction (it doesn't matter which one).
        $prediction = reset($predictions);
        $prediction->action_executed(\core_analytics\prediction::ACTION_FIXED, $this->model->get_target());
        $prediction->action_executed(\core_analytics\prediction::ACTION_NOT_APPLICABLE, $this->model->get_target());
        $prediction->action_executed(\core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED, $this->model->get_target());

        $recordset = $this->model->get_prediction_actions($this->context);
        $this->assertCount(3, $recordset);
        $recordset->close();

        list($ignored, $predictions) = $this->model->get_predictions($this->context, true);
        $this->assertCount(1, $predictions);
        list($ignored, $predictions) = $this->model->get_predictions($this->context, false);
        $this->assertCount(2, $predictions);

        // Teacher 1 can still see both predictions.
        $this->setUser($this->teacher1);
        list($ignored, $predictions) = $this->model->get_predictions($this->context, true);
        $this->assertCount(2, $predictions);
        list($ignored, $predictions) = $this->model->get_predictions($this->context, false);
        $this->assertCount(2, $predictions);

        $recordset = $this->model->get_prediction_actions($this->context);
        $this->assertCount(3, $recordset);
        $recordset->close();

        // Trying with a deleted course.
        $DB->delete_records('course', ['id' => $this->course2->id]);
        $this->setUser($this->teacher3);
        list($ignored, $predictions) = $this->model->get_predictions($this->context);
        $this->assertCount(1, $predictions);
        reset($predictions)->action_executed(\core_analytics\prediction::ACTION_FIXED, $this->model->get_target());
        $this->assertEmpty($this->model->get_predictions($this->context));
    }
}

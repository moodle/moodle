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
 * Unit tests for report_insights externallib.
 *
 * @package   report_insights
 * @copyright 2019 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace report_insights;

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../../../analytics/tests/fixtures/test_indicator_max.php');
require_once(__DIR__ . '/../../../analytics/tests/fixtures/test_target_shortname.php');

/**
 * Unit tests for report_insights externallib.
 *
 * @package   report_insights
 * @copyright 2019 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class externallib_test extends \advanced_testcase {

    /**
     * test_action_executed
     */
    public function test_action_executed() {
        global $DB;

        $this->setAdminUser();
        $target = \core_analytics\manager::get_target('test_target_shortname');
        $indicators = array('test_indicator_max');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }

        $model = \core_analytics\model::create($target, $indicators);
        $modelobj = $model->get_model_obj();
        $model->enable('\core\analytics\time_splitting\single_range');

        $this->resetAfterTest(true);

        $course1 = $this->getDataGenerator()->create_course();
        $course2 = $this->getDataGenerator()->create_course();
        $context = \context_course::instance($course1->id);

        $teacher1 = $this->getDataGenerator()->create_user();
        $teacher2 = $this->getDataGenerator()->create_user();

        $this->getDataGenerator()->enrol_user($teacher1->id, $course1->id, 'editingteacher');
        $this->getDataGenerator()->enrol_user($teacher2->id, $course1->id, 'editingteacher');

        // The only relevant fields are modelid, contextid and sampleid. I'm cheating and setting
        // contextid as the course context so teachers can access these predictions.
        $pred = new \stdClass();
        $pred->modelid = $model->get_id();
        $pred->contextid = $context->id;
        $pred->sampleid = $course1->id;
        $pred->rangeindex = 1;
        $pred->prediction = 1;
        $pred->predictionscore = 1;
        $pred->calculations = json_encode(array('test_indicator_max' => 1));
        $pred->timecreated = time();
        $DB->insert_record('analytics_predictions', $pred);

        $pred->sampleid = $course2->id;
        $DB->insert_record('analytics_predictions', $pred);

        $this->assertEquals(0, $DB->count_records('analytics_prediction_actions'));

        // Teacher 2 flags a prediction (it doesn't matter which one) as fixed.
        $this->setUser($teacher2);
        list($ignored, $predictions) = $model->get_predictions($context, true);
        $prediction = reset($predictions);

        \report_insights\external::action_executed(\core_analytics\prediction::ACTION_FIXED,
            [$prediction->get_prediction_data()->id]);
        $recordset = $model->get_prediction_actions($context);
        $this->assertCount(1, $recordset);
        $recordset->close();
        $this->assertEquals(1, $DB->count_records('analytics_prediction_actions'));
        $action = $DB->get_record('analytics_prediction_actions', array('userid' => $teacher2->id));
        $this->assertEquals(\core_analytics\prediction::ACTION_FIXED, $action->actionname);

        \report_insights\external::action_executed(\core_analytics\prediction::ACTION_INCORRECTLY_FLAGGED,
            [$prediction->get_prediction_data()->id]);
        $recordset = $model->get_prediction_actions($context);
        $this->assertCount(2, $recordset);
        $recordset->close();
        $this->assertEquals(2, $DB->count_records('analytics_prediction_actions'));
    }
}

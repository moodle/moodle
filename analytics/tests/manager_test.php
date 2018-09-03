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
 * Unit tests for the manager.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_indicator_max.php');
require_once(__DIR__ . '/fixtures/test_indicator_min.php');
require_once(__DIR__ . '/fixtures/test_indicator_fullname.php');
require_once(__DIR__ . '/fixtures/test_target_course_level_shortname.php');

/**
 * Unit tests for the manager.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analytics_manager_testcase extends advanced_testcase {

    /**
     * test_deleted_context
     */
    public function test_deleted_context() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminuser();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');

        $target = \core_analytics\manager::get_target('test_target_course_level_shortname');
        $indicators = array('test_indicator_max', 'test_indicator_min', 'test_indicator_fullname');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }

        $model = \core_analytics\model::create($target, $indicators);
        $modelobj = $model->get_model_obj();

        $coursepredict1 = $this->getDataGenerator()->create_course(array('visible' => 0));
        $coursepredict2 = $this->getDataGenerator()->create_course(array('visible' => 0));
        $coursetrain1 = $this->getDataGenerator()->create_course(array('visible' => 1));
        $coursetrain2 = $this->getDataGenerator()->create_course(array('visible' => 1));

        $model->enable('\core\analytics\time_splitting\no_splitting');

        $model->train();
        $model->predict();

        // Generate a prediction action to confirm that it is deleted when there is an important update.
        $predictions = $DB->get_records('analytics_predictions');
        $prediction = reset($predictions);
        $prediction = new \core_analytics\prediction($prediction, array('whatever' => 'not used'));
        $prediction->action_executed(\core_analytics\prediction::ACTION_FIXED, $model->get_target());

        $predictioncontextid = $prediction->get_prediction_data()->contextid;

        $npredictions = $DB->count_records('analytics_predictions', array('contextid' => $predictioncontextid));
        $npredictionactions = $DB->count_records('analytics_prediction_actions',
            array('predictionid' => $prediction->get_prediction_data()->id));
        $nindicatorcalc = $DB->count_records('analytics_indicator_calc', array('contextid' => $predictioncontextid));

        \core_analytics\manager::cleanup();

        // Nothing is incorrectly deleted.
        $this->assertEquals($npredictions, $DB->count_records('analytics_predictions',
            array('contextid' => $predictioncontextid)));
        $this->assertEquals($npredictionactions, $DB->count_records('analytics_prediction_actions',
            array('predictionid' => $prediction->get_prediction_data()->id)));
        $this->assertEquals($nindicatorcalc, $DB->count_records('analytics_indicator_calc',
            array('contextid' => $predictioncontextid)));

        // Now we delete a context, the course predictions and prediction actions should be deleted.
        $deletedcontext = \context::instance_by_id($predictioncontextid);
        delete_course($deletedcontext->instanceid, false);

        \core_analytics\manager::cleanup();

        $this->assertEmpty($DB->count_records('analytics_predictions', array('contextid' => $predictioncontextid)));
        $this->assertEmpty($DB->count_records('analytics_prediction_actions',
            array('predictionid' => $prediction->get_prediction_data()->id)));
        $this->assertEmpty($DB->count_records('analytics_indicator_calc', array('contextid' => $predictioncontextid)));

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }

    /**
     * test_deleted_analysable
     */
    public function test_deleted_analysable() {
        global $DB;

        $this->resetAfterTest(true);
        $this->setAdminuser();
        set_config('enabled_stores', 'logstore_standard', 'tool_log');

        $target = \core_analytics\manager::get_target('test_target_course_level_shortname');
        $indicators = array('test_indicator_max', 'test_indicator_min', 'test_indicator_fullname');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }

        $model = \core_analytics\model::create($target, $indicators);
        $modelobj = $model->get_model_obj();

        $coursepredict1 = $this->getDataGenerator()->create_course(array('visible' => 0));
        $coursepredict2 = $this->getDataGenerator()->create_course(array('visible' => 0));
        $coursetrain1 = $this->getDataGenerator()->create_course(array('visible' => 1));
        $coursetrain2 = $this->getDataGenerator()->create_course(array('visible' => 1));

        $model->enable('\core\analytics\time_splitting\no_splitting');

        $model->train();
        $model->predict();

        $npredictsamples = $DB->count_records('analytics_predict_samples');
        $ntrainsamples = $DB->count_records('analytics_train_samples');

        // Now we delete an analysable, stored predict and training samples should be deleted.
        $deletedcontext = \context_course::instance($coursepredict1->id);
        delete_course($coursepredict1, false);

        \core_analytics\manager::cleanup();

        $this->assertEmpty($DB->count_records('analytics_predict_samples', array('analysableid' => $coursepredict1->id)));
        $this->assertEmpty($DB->count_records('analytics_train_samples', array('analysableid' => $coursepredict1->id)));

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }

}

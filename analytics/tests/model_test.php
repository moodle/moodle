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
 * Unit tests for the model.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/fixtures/test_indicator_max.php');
require_once(__DIR__ . '/fixtures/test_indicator_min.php');
require_once(__DIR__ . '/fixtures/test_indicator_fullname.php');
require_once(__DIR__ . '/fixtures/test_target_shortname.php');
require_once(__DIR__ . '/fixtures/test_static_target_shortname.php');
require_once(__DIR__ . '/fixtures/test_target_course_level_shortname.php');
require_once(__DIR__ . '/fixtures/test_analyser.php');

/**
 * Unit tests for the model.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class analytics_model_testcase extends advanced_testcase {

    public function setUp() {

        $this->setAdminUser();

        $target = \core_analytics\manager::get_target('test_target_shortname');
        $indicators = array('test_indicator_max', 'test_indicator_min', 'test_indicator_fullname');
        foreach ($indicators as $key => $indicator) {
            $indicators[$key] = \core_analytics\manager::get_indicator($indicator);
        }

        $this->model = testable_model::create($target, $indicators);
        $this->modelobj = $this->model->get_model_obj();
    }

    public function test_enable() {
        $this->resetAfterTest(true);

        $this->assertEquals(0, $this->model->get_model_obj()->enabled);
        $this->assertEquals(0, $this->model->get_model_obj()->trained);
        $this->assertEquals('', $this->model->get_model_obj()->timesplitting);

        $this->model->enable('\core\analytics\time_splitting\quarters');
        $this->assertEquals(1, $this->model->get_model_obj()->enabled);
        $this->assertEquals(0, $this->model->get_model_obj()->trained);
        $this->assertEquals('\core\analytics\time_splitting\quarters', $this->model->get_model_obj()->timesplitting);
    }

    public function test_create() {
        $this->resetAfterTest(true);

        $target = \core_analytics\manager::get_target('\core\analytics\target\course_dropout');
        $indicators = array(
            \core_analytics\manager::get_indicator('\core\analytics\indicator\any_write_action'),
            \core_analytics\manager::get_indicator('\core\analytics\indicator\read_actions')
        );
        $model = \core_analytics\model::create($target, $indicators);
        $this->assertInstanceOf('\core_analytics\model', $model);
    }

    /**
     * test_delete
     */
    public function test_delete() {
        global $DB;

        $this->resetAfterTest(true);
        set_config('enabled_stores', 'logstore_standard', 'tool_log');

        $coursepredict1 = $this->getDataGenerator()->create_course(array('visible' => 0));
        $coursepredict2 = $this->getDataGenerator()->create_course(array('visible' => 0));
        $coursetrain1 = $this->getDataGenerator()->create_course(array('visible' => 1));
        $coursetrain2 = $this->getDataGenerator()->create_course(array('visible' => 1));

        $this->model->enable('\core\analytics\time_splitting\no_splitting');

        $this->model->train();
        $this->model->predict();

        // Fake evaluation results record to check that it is actually deleted.
        $this->add_fake_log();

        $modeloutputdir = $this->model->get_output_dir(array(), true);
        $this->assertTrue(is_dir($modeloutputdir));

        // Generate a prediction action to confirm that it is deleted when there is an important update.
        $predictions = $DB->get_records('analytics_predictions');
        $prediction = reset($predictions);
        $prediction = new \core_analytics\prediction($prediction, array('whatever' => 'not used'));
        $prediction->action_executed(\core_analytics\prediction::ACTION_FIXED, $this->model->get_target());

        $this->model->delete();
        $this->assertEmpty($DB->count_records('analytics_models', array('id' => $this->modelobj->id)));
        $this->assertEmpty($DB->count_records('analytics_models_log', array('modelid' => $this->modelobj->id)));
        $this->assertEmpty($DB->count_records('analytics_predictions'));
        $this->assertEmpty($DB->count_records('analytics_prediction_actions'));
        $this->assertEmpty($DB->count_records('analytics_train_samples'));
        $this->assertEmpty($DB->count_records('analytics_predict_samples'));
        $this->assertEmpty($DB->count_records('analytics_used_files'));
        $this->assertFalse(is_dir($modeloutputdir));

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }

    /**
     * test_clear
     */
    public function test_clear() {
        global $DB;

        $this->resetAfterTest(true);
        set_config('enabled_stores', 'logstore_standard', 'tool_log');

        $coursepredict1 = $this->getDataGenerator()->create_course(array('visible' => 0));
        $coursepredict2 = $this->getDataGenerator()->create_course(array('visible' => 0));
        $coursetrain1 = $this->getDataGenerator()->create_course(array('visible' => 1));
        $coursetrain2 = $this->getDataGenerator()->create_course(array('visible' => 1));

        $this->model->enable('\core\analytics\time_splitting\no_splitting');

        $this->model->train();
        $this->model->predict();

        // Fake evaluation results record to check that it is actually deleted.
        $this->add_fake_log();

        // Generate a prediction action to confirm that it is deleted when there is an important update.
        $predictions = $DB->get_records('analytics_predictions');
        $prediction = reset($predictions);
        $prediction = new \core_analytics\prediction($prediction, array('whatever' => 'not used'));
        $prediction->action_executed(\core_analytics\prediction::ACTION_FIXED, $this->model->get_target());

        $modelversionoutputdir = $this->model->get_output_dir();
        $this->assertTrue(is_dir($modelversionoutputdir));

        // Update to an empty time splitting method to force model::clear execution.
        $this->model->clear();
        $this->assertFalse(is_dir($modelversionoutputdir));

        // Check that most of the stuff got deleted.
        $this->assertEquals(1, $DB->count_records('analytics_models', array('id' => $this->modelobj->id)));
        $this->assertEquals(1, $DB->count_records('analytics_models_log', array('modelid' => $this->modelobj->id)));
        $this->assertEmpty($DB->count_records('analytics_predictions'));
        $this->assertEmpty($DB->count_records('analytics_prediction_actions'));
        $this->assertEmpty($DB->count_records('analytics_train_samples'));
        $this->assertEmpty($DB->count_records('analytics_predict_samples'));
        $this->assertEmpty($DB->count_records('analytics_used_files'));

        set_config('enabled_stores', '', 'tool_log');
        get_log_manager(true);
    }

    public function test_model_manager() {
        $this->resetAfterTest(true);

        $this->assertCount(3, $this->model->get_indicators());
        $this->assertInstanceOf('\core_analytics\local\target\binary', $this->model->get_target());

        // Using evaluation as the model is not yet enabled.
        $this->model->init_analyser(array('evaluation' => true));
        $this->assertInstanceOf('\core_analytics\local\analyser\base', $this->model->get_analyser());

        $this->model->enable('\core\analytics\time_splitting\quarters');
        $this->assertInstanceOf('\core\analytics\analyser\site_courses', $this->model->get_analyser());
    }

    public function test_output_dir() {
        $this->resetAfterTest(true);

        $dir = make_request_directory();
        set_config('modeloutputdir', $dir, 'analytics');

        $modeldir = $dir . DIRECTORY_SEPARATOR . $this->modelobj->id . DIRECTORY_SEPARATOR . $this->modelobj->version;
        $this->assertEquals($modeldir, $this->model->get_output_dir());
        $this->assertEquals($modeldir . DIRECTORY_SEPARATOR . 'testing', $this->model->get_output_dir(array('testing')));
    }

    public function test_unique_id() {
        global $DB;

        $this->resetAfterTest(true);

        $originaluniqueid = $this->model->get_unique_id();

        // Same id across instances.
        $this->model = new testable_model($this->modelobj);
        $this->assertEquals($originaluniqueid, $this->model->get_unique_id());

        // We will restore it later.
        $originalversion = $this->modelobj->version;

        // Generates a different id if timemodified changes.
        $this->modelobj->version = $this->modelobj->version + 10;
        $DB->update_record('analytics_models', $this->modelobj);
        $this->model = new testable_model($this->modelobj);
        $this->assertNotEquals($originaluniqueid, $this->model->get_unique_id());

        // Restore original timemodified to continue testing.
        $this->modelobj->version = $originalversion;
        $DB->update_record('analytics_models', $this->modelobj);
        // Same when updating through an action that changes the model.
        $this->model = new testable_model($this->modelobj);

        $this->model->mark_as_trained();
        $this->assertEquals($originaluniqueid, $this->model->get_unique_id());

        // Wait for the current timestamp to change.
        $this->waitForSecond();
        $this->model->enable('\core\analytics\time_splitting\deciles');
        $this->assertNotEquals($originaluniqueid, $this->model->get_unique_id());
        $uniqueid = $this->model->get_unique_id();

        // Wait for the current timestamp to change.
        $this->waitForSecond();
        $this->model->enable('\core\analytics\time_splitting\quarters');
        $this->assertNotEquals($originaluniqueid, $this->model->get_unique_id());
        $this->assertNotEquals($uniqueid, $this->model->get_unique_id());
    }

    /**
     * test_exists
     *
     * @return void
     */
    public function test_exists() {
        $this->resetAfterTest(true);

        global $DB;

        $count = $DB->count_records('analytics_models');

        // No new models added if the builtin ones already exist.
        \core_analytics\manager::add_builtin_models();
        $this->assertCount($count, $DB->get_records('analytics_models'));

        $target = \core_analytics\manager::get_target('\core\analytics\target\no_teaching');
        $this->assertTrue(\core_analytics\model::exists($target));
    }

    /**
     * test_model_timelimit
     *
     * @return null
     */
    public function test_model_timelimit() {
        global $DB;

        $this->resetAfterTest(true);

        set_config('modeltimelimit', 2, 'analytics');

        $courses = array();
        for ($i = 0; $i < 5; $i++) {
            $course = $this->getDataGenerator()->create_course();
            $analysable = new \core_analytics\course($course);
            $courses[$analysable->get_id()] = $course;
        }

        $target = new test_target_course_level_shortname();
        $analyser = new test_analyser(1, $target, [], [], []);

        // Each analysable element takes 1.1 secs, so the max (and likely) number of analysable
        // elements that will be processed is 2.
        $analyser->get_analysable_data(false);
        $params = array('modelid' => 1, 'action' => 'prediction');
        $this->assertLessThanOrEqual(2, $DB->count_records('analytics_used_analysables', $params));

        $analyser->get_analysable_data(false);
        $this->assertLessThanOrEqual(4, $DB->count_records('analytics_used_analysables', $params));

        // Check that analysable elements have been processed following the analyser order
        // (course->sortorder here). We can not check this nicely after next get_analysable_data round
        // because the first analysed element will be analysed again.
        $analysedelems = $DB->get_records('analytics_used_analysables', $params, 'timeanalysed ASC');
        // Just a default for the first checked element.
        $last = (object)['sortorder' => PHP_INT_MAX];
        foreach ($analysedelems as $analysed) {
            if ($courses[$analysed->analysableid]->sortorder > $last->sortorder) {
                $this->fail('Analysable elements have not been analysed sorted by course sortorder.');
            }
            $last = $courses[$analysed->analysableid];
        }

        $analyser->get_analysable_data(false);
        $this->assertGreaterThanOrEqual(5, $DB->count_records('analytics_used_analysables', $params));

        // New analysable elements are immediately pulled.
        $this->getDataGenerator()->create_course();
        $analyser->get_analysable_data(false);
        $this->assertGreaterThanOrEqual(6, $DB->count_records('analytics_used_analysables', $params));

        // Training and prediction data do not get mixed.
        $analyser->get_analysable_data(true);
        $params = array('modelid' => 1, 'action' => 'training');
        $this->assertLessThanOrEqual(2, $DB->count_records('analytics_used_analysables', $params));
    }

    /**
     * Test model_config::get_class_component.
     */
    public function test_model_config_get_class_component() {
        $this->resetAfterTest(true);

        $this->assertEquals('core',
            \core_analytics\model_config::get_class_component('\\core\\analytics\\indicator\\read_actions'));
        $this->assertEquals('core',
            \core_analytics\model_config::get_class_component('core\\analytics\\indicator\\read_actions'));
        $this->assertEquals('core',
            \core_analytics\model_config::get_class_component('\\core_course\\analytics\\indicator\\completion_enabled'));
        $this->assertEquals('mod_forum',
            \core_analytics\model_config::get_class_component('\\mod_forum\\analytics\\indicator\\cognitive_depth'));

        $this->assertEquals('core', \core_analytics\model_config::get_class_component('\\core_class'));
    }

    /**
     * Test that import_model import models' configurations.
     */
    public function test_import_model_config() {
        $this->resetAfterTest(true);

        $this->model->enable('\\core\\analytics\\time_splitting\\quarters');
        $zipfilepath = $this->model->export_model('yeah-config.zip');

        $this->modelobj = $this->model->get_model_obj();

        $importedmodelobj = \core_analytics\model::import_model($zipfilepath)->get_model_obj();

        $this->assertSame($this->modelobj->target, $importedmodelobj->target);
        $this->assertSame($this->modelobj->indicators, $importedmodelobj->indicators);
        $this->assertSame($this->modelobj->timesplitting, $importedmodelobj->timesplitting);

        $predictionsprocessor = $this->model->get_predictions_processor();
        $this->assertSame('\\' . get_class($predictionsprocessor), $importedmodelobj->predictionsprocessor);
    }

    /**
     * Test can export configuration
     */
    public function test_can_export_configuration() {
        $this->resetAfterTest(true);

        // No time splitting method.
        $this->assertFalse($this->model->can_export_configuration());

        $this->model->enable('\\core\\analytics\\time_splitting\\quarters');
        $this->assertTrue($this->model->can_export_configuration());

        $this->model->update(true, [], false);
        $this->assertFalse($this->model->can_export_configuration());

        $statictarget = new test_static_target_shortname();
        $indicators['test_indicator_max'] = \core_analytics\manager::get_indicator('test_indicator_max');
        $model = \core_analytics\model::create($statictarget, $indicators, '\\core\\analytics\\time_splitting\\quarters');
        $this->assertFalse($model->can_export_configuration());
    }

    /**
     * Test export_config
     */
    public function test_export_config() {
        $this->resetAfterTest(true);

        $this->model->enable('\\core\\analytics\\time_splitting\\quarters');

        $modelconfig = new \core_analytics\model_config($this->model);

        $method = new ReflectionMethod('\\core_analytics\\model_config', 'export_model_data');
        $method->setAccessible(true);

        $modeldata = $method->invoke($modelconfig);

        $this->assertArrayHasKey('core', $modeldata->dependencies);
        $this->assertInternalType('float', $modeldata->dependencies['core']);
        $this->assertNotEmpty($modeldata->target);
        $this->assertNotEmpty($modeldata->timesplitting);
        $this->assertCount(3, $modeldata->indicators);

        $indicators['test_indicator_max'] = \core_analytics\manager::get_indicator('test_indicator_max');
        $this->model->update(true, $indicators, false);

        $modeldata = $method->invoke($modelconfig);

        $this->assertCount(1, $modeldata->indicators);
    }

    /**
     * Generates a model log record.
     */
    private function add_fake_log() {
        global $DB, $USER;

        $log = new stdClass();
        $log->modelid = $this->modelobj->id;
        $log->version = $this->modelobj->version;
        $log->target = $this->modelobj->target;
        $log->indicators = $this->modelobj->indicators;
        $log->score = 1;
        $log->info = json_encode([]);
        $log->dir = 'not important';
        $log->timecreated = time();
        $log->usermodified = $USER->id;
        $DB->insert_record('analytics_models_log', $log);
    }
}

/**
 * Testable version to change methods' visibility.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class testable_model extends \core_analytics\model {

    /**
     * init_analyser
     *
     * @param array $options
     * @return void
     */
    public function init_analyser($options = array()) {
        parent::init_analyser($options);
    }
}

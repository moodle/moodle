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

    /**
     * Tests for the {@link \core_analytics\manager::load_default_models_for_component()} implementation.
     */
    public function test_load_default_models_for_component() {
        $this->resetAfterTest();

        // Attempting to load builtin models should always work without throwing exception.
        \core_analytics\manager::load_default_models_for_component('core');

        // Attempting to load from a core subsystem without its own subsystem directory.
        $this->assertSame([], \core_analytics\manager::load_default_models_for_component('core_access'));

        // Attempting to load from a non-existing subsystem.
        $this->assertSame([], \core_analytics\manager::load_default_models_for_component('core_nonexistingsubsystem'));

        // Attempting to load from a non-existing plugin of a known plugin type.
        $this->assertSame([], \core_analytics\manager::load_default_models_for_component('mod_foobarbazquaz12240996776'));

        // Attempting to load from a non-existing plugin type.
        $this->assertSame([], \core_analytics\manager::load_default_models_for_component('foo_bar2776327736558'));
    }

    /**
     * Tests for the successful execution of the {@link \core_analytics\manager::validate_models_declaration()}.
     */
    public function test_validate_models_declaration() {
        $this->resetAfterTest();

        // This is expected to run without an exception.
        $models = $this->load_models_from_fixture_file('no_teaching');
        \core_analytics\manager::validate_models_declaration($models);
    }

    /**
     * Tests for the exceptions thrown by {@link \core_analytics\manager::validate_models_declaration()}.
     *
     * @dataProvider validate_models_declaration_exceptions_provider
     * @param array $models Models declaration.
     * @param string $exception Expected coding exception message.
     */
    public function test_validate_models_declaration_exceptions(array $models, string $exception) {
        $this->resetAfterTest();

        $this->expectException(\coding_exception::class);
        $this->expectExceptionMessage($exception);
        \core_analytics\manager::validate_models_declaration($models);
    }

    /**
     * Data provider for the {@link self::test_validate_models_declaration_exceptions()}.
     *
     * @return array of (string)testcase => [(array)models, (string)expected exception message]
     */
    public function validate_models_declaration_exceptions_provider() {
        return [
            'missing_target' => [
                $this->load_models_from_fixture_file('missing_target'),
                'Missing target declaration',
            ],
            'invalid_target' => [
                $this->load_models_from_fixture_file('invalid_target'),
                'Invalid target classname',
            ],
            'missing_indicators' => [
                $this->load_models_from_fixture_file('missing_indicators'),
                'Missing indicators declaration',
            ],
            'invalid_indicators' => [
                $this->load_models_from_fixture_file('invalid_indicators'),
                'Invalid indicator classname',
            ],
            'invalid_time_splitting' => [
                $this->load_models_from_fixture_file('invalid_time_splitting'),
                'Invalid time splitting classname',
            ],
            'invalid_time_splitting_fq' => [
                $this->load_models_from_fixture_file('invalid_time_splitting_fq'),
                'Expecting fully qualified time splitting classname',
            ],
            'invalid_enabled' => [
                $this->load_models_from_fixture_file('invalid_enabled'),
                'Cannot enable a model without time splitting method specified',
            ],
        ];
    }

    /**
     * Loads models as declared in the given fixture file.
     *
     * @param string $filename
     * @return array
     */
    protected function load_models_from_fixture_file(string $filename) {
        global $CFG;

        $models = null;

        require($CFG->dirroot.'/analytics/tests/fixtures/db_analytics_php/'.$filename.'.php');

        return $models;
    }

    /**
     * Test the implementation of the {@link \core_analytics\manager::create_declared_model()}.
     */
    public function test_create_declared_model() {
        global $DB;

        $this->resetAfterTest();
        $this->setAdminuser();

        $declaration = [
            'target' => 'test_target_course_level_shortname',
            'indicators' => [
                'test_indicator_max',
                'test_indicator_min',
                'test_indicator_fullname',
            ],
        ];

        $declarationwithtimesplitting = array_merge($declaration, [
            'timesplitting' => '\core\analytics\time_splitting\no_splitting',
        ]);

        $declarationwithtimesplittingenabled = array_merge($declarationwithtimesplitting, [
            'enabled' => true,
        ]);

        // Check that no such model exists yet.
        $target = \core_analytics\manager::get_target('test_target_course_level_shortname');
        $this->assertEquals(0, $DB->count_records('analytics_models', ['target' => $target->get_id()]));
        $this->assertFalse(\core_analytics\model::exists($target));

        // Check that the model is created.
        $created = \core_analytics\manager::create_declared_model($declaration);
        $this->assertTrue($created instanceof \core_analytics\model);
        $this->assertTrue(\core_analytics\model::exists($target));
        $this->assertEquals(1, $DB->count_records('analytics_models', ['target' => $target->get_id()]));
        $modelid = $created->get_id();

        // Check that created models are disabled by default.
        $existing = new \core_analytics\model($modelid);
        $this->assertEquals(0, $existing->get_model_obj()->enabled);
        $this->assertEquals(0, $DB->get_field('analytics_models', 'enabled', ['target' => $target->get_id()], MUST_EXIST));

        // Let the admin enable the model.
        $existing->enable('\core\analytics\time_splitting\no_splitting');
        $this->assertEquals(1, $DB->get_field('analytics_models', 'enabled', ['target' => $target->get_id()], MUST_EXIST));

        // Check that further calls create a new model.
        $repeated = \core_analytics\manager::create_declared_model($declaration);
        $this->assertTrue($repeated instanceof \core_analytics\model);
        $this->assertEquals(2, $DB->count_records('analytics_models', ['target' => $target->get_id()]));

        // Delete the models.
        $existing->delete();
        $repeated->delete();
        $this->assertEquals(0, $DB->count_records('analytics_models', ['target' => $target->get_id()]));
        $this->assertFalse(\core_analytics\model::exists($target));

        // Create it again, this time with time splitting method specified.
        $created = \core_analytics\manager::create_declared_model($declarationwithtimesplitting);
        $this->assertTrue($created instanceof \core_analytics\model);
        $this->assertTrue(\core_analytics\model::exists($target));
        $this->assertEquals(1, $DB->count_records('analytics_models', ['target' => $target->get_id()]));
        $modelid = $created->get_id();

        // Even if the time splitting method was specified, the model is still not enabled automatically.
        $existing = new \core_analytics\model($modelid);
        $this->assertEquals(0, $existing->get_model_obj()->enabled);
        $this->assertEquals(0, $DB->get_field('analytics_models', 'enabled', ['target' => $target->get_id()], MUST_EXIST));
        $existing->delete();

        // Let's define the model so that it is enabled by default.
        $enabled = \core_analytics\manager::create_declared_model($declarationwithtimesplittingenabled);
        $this->assertTrue($enabled instanceof \core_analytics\model);
        $this->assertTrue(\core_analytics\model::exists($target));
        $this->assertEquals(1, $DB->count_records('analytics_models', ['target' => $target->get_id()]));
        $modelid = $enabled->get_id();
        $existing = new \core_analytics\model($modelid);
        $this->assertEquals(1, $existing->get_model_obj()->enabled);
        $this->assertEquals(1, $DB->get_field('analytics_models', 'enabled', ['target' => $target->get_id()], MUST_EXIST));

        // Let the admin disable the model.
        $existing->update(0, false, false);
        $this->assertEquals(0, $DB->get_field('analytics_models', 'enabled', ['target' => $target->get_id()], MUST_EXIST));
    }

    /**
     * Test the implementation of the {@link \core_analytics\manager::update_default_models_for_component()}.
     */
    public function test_update_default_models_for_component() {

        $this->resetAfterTest();
        $this->setAdminuser();

        $noteaching = \core_analytics\manager::get_target('\core\analytics\target\no_teaching');
        $dropout = \core_analytics\manager::get_target('\core\analytics\target\course_dropout');

        $this->assertTrue(\core_analytics\model::exists($noteaching));
        $this->assertTrue(\core_analytics\model::exists($dropout));

        foreach (\core_analytics\manager::get_all_models() as $model) {
            $model->delete();
        }

        $this->assertFalse(\core_analytics\model::exists($noteaching));
        $this->assertFalse(\core_analytics\model::exists($dropout));

        $updated = \core_analytics\manager::update_default_models_for_component('moodle');

        $this->assertEquals(2, count($updated));
        $this->assertTrue(array_pop($updated) instanceof \core_analytics\model);
        $this->assertTrue(array_pop($updated) instanceof \core_analytics\model);
        $this->assertTrue(\core_analytics\model::exists($noteaching));
        $this->assertTrue(\core_analytics\model::exists($dropout));

        $repeated = \core_analytics\manager::update_default_models_for_component('moodle');

        $this->assertSame([], $repeated);
    }
}

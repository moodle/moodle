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
        $this->assertEquals($modeldir . DIRECTORY_SEPARATOR . 'asd', $this->model->get_output_dir(array('asd')));
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

        $this->model->enable();
        $this->assertEquals($originaluniqueid, $this->model->get_unique_id());

        // Wait 1 sec so the timestamp changes.
        sleep(1);
        $this->model->enable('\core\analytics\time_splitting\quarters');
        $this->assertNotEquals($originaluniqueid, $this->model->get_unique_id());
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
     * get_output_dir
     *
     * @param array $subdirs
     * @return string
     */
    public function get_output_dir($subdirs = array()) {
        return parent::get_output_dir($subdirs);
    }

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

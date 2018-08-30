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
 * Privacy tests for gradingform_rubric
 *
 * @package    gradingform_rubric
 * @category   test
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use \core_privacy\tests\provider_testcase;
use \core_privacy\local\request\writer;
use \gradingform_rubric\privacy\provider;

/**
 * Privacy tests for gradingform_rubric
 *
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradingform_rubric_privacy_testcase extends provider_testcase {

    /**
     * Test the export of rubric data.
     */
    public function test_get_gradingform_export_data() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);

        $modulecontext = context_module::instance($module->cmid);
        $rubric = new test_rubric($modulecontext, 'testrubrib', 'Description text');
        $criterion = new test_criterion('Spelling is important');
        $criterion->add_level('Nothing but mistakes', 0);
        $criterion->add_level('Several mistakes', 1);
        $criterion->add_level('No mistakes', 2);
        $rubric->add_criteria($criterion);
        $criterion = new test_criterion('Pictures');
        $criterion->add_level('No pictures', 0);
        $criterion->add_level('One picture', 1);
        $criterion->add_level('More than one picture', 2);
        $rubric->add_criteria($criterion);
        $rubric->create_rubric();

        $controller = $rubric->manager->get_controller('rubric');
        // In the situation of mod_assign this would be the id from assign_grades.
        $itemid = 1;
        $instance = $controller->create_instance($user->id, $itemid);
        // I need the ids for the criteria and there doesn't seem to be a nice method to get it.
        $criteria = $DB->get_records('gradingform_rubric_criteria');
        $data = ['criteria' => []];
        foreach ($criteria as $key => $value) {
            if ($value->description == 'Spelling is important') {
                $level = $DB->get_record('gradingform_rubric_levels', ['criterionid' => $key, 'score' => 1]);
                $data['criteria'][$key]['levelid'] = $level->id;
                $data['criteria'][$key]['remark'] = 'This user made several mistakes.';
            } else {
                $level = $DB->get_record('gradingform_rubric_levels', ['criterionid' => $key, 'score' => 0]);
                $data['criteria'][$key]['levelid'] = $level->id;
                $data['criteria'][$key]['remark'] = 'Please add more pictures.';
            }
        }
        $data['itemid'] = $itemid;

        // Update this instance with data.
        $instance->update($data);
        $instanceid = $instance->get_data('id');

        // Let's try the method we are testing.
        provider::export_gradingform_instance_data($modulecontext, $instance->get_id(), ['Test']);
        $data = (array) writer::with_context($modulecontext)->get_data(['Test', 'Rubric', $instanceid]);
        $this->assertCount(2, $data);
        $this->assertEquals('Spelling is important', $data['Spelling is important']->description);
        $this->assertEquals('This user made several mistakes.', $data['Spelling is important']->remark);
        $this->assertEquals('Pictures', $data['Pictures']->description);
        $this->assertEquals('Please add more pictures.', $data['Pictures']->remark);
    }

    /**
     * Test the deletion of rubric user information via the instance ID.
     */
    public function test_delete_gradingform_for_instances() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);

        $modulecontext = context_module::instance($module->cmid);
        $rubric = new test_rubric($modulecontext, 'testrubrib', 'Description text');
        $criterion = new test_criterion('Spelling is important');
        $criterion->add_level('Nothing but mistakes', 0);
        $criterion->add_level('Several mistakes', 1);
        $criterion->add_level('No mistakes', 2);
        $rubric->add_criteria($criterion);
        $criterion = new test_criterion('Pictures');
        $criterion->add_level('No pictures', 0);
        $criterion->add_level('One picture', 1);
        $criterion->add_level('More than one picture', 2);
        $rubric->add_criteria($criterion);
        $rubric->create_rubric();

        $controller = $rubric->manager->get_controller('rubric');
        // In the situation of mod_assign this would be the id from assign_grades.
        $itemid = 1;
        $instance = $controller->create_instance($user->id, $itemid);
        // I need the ids for the criteria and there doesn't seem to be a nice method to get it.
        $criteria = $DB->get_records('gradingform_rubric_criteria');
        $data = ['criteria' => []];
        foreach ($criteria as $key => $value) {
            if ($value->description == 'Spelling is important') {
                $level = $DB->get_record('gradingform_rubric_levels', ['criterionid' => $key, 'score' => 1]);
                $data['criteria'][$key]['levelid'] = $level->id;
                $data['criteria'][$key]['remark'] = 'This user made several mistakes.';
            } else {
                $level = $DB->get_record('gradingform_rubric_levels', ['criterionid' => $key, 'score' => 0]);
                $data['criteria'][$key]['levelid'] = $level->id;
                $data['criteria'][$key]['remark'] = 'Please add more pictures.';
            }
        }
        $data['itemid'] = $itemid;

        // Update this instance with data.
        $instance->update($data);

        // Second instance.
        $itemid = 2;
        $instance = $controller->create_instance($user->id, $itemid);
        // I need the ids for the criteria and there doesn't seem to be a nice method to get it.
        $criteria = $DB->get_records('gradingform_rubric_criteria');
        $data = ['criteria' => []];
        foreach ($criteria as $key => $value) {
            if ($value->description == 'Spelling is important') {
                $level = $DB->get_record('gradingform_rubric_levels', ['criterionid' => $key, 'score' => 0]);
                $data['criteria'][$key]['levelid'] = $level->id;
                $data['criteria'][$key]['remark'] = 'Too many mistakes. Please try again.';
            } else {
                $level = $DB->get_record('gradingform_rubric_levels', ['criterionid' => $key, 'score' => 2]);
                $data['criteria'][$key]['levelid'] = $level->id;
                $data['criteria'][$key]['remark'] = 'Great number of pictures. Well done.';
            }
        }
        $data['itemid'] = $itemid;

        // Update this instance with data.
        $instance->update($data);

        // Check how many records we have in the fillings table.
        $records = $DB->get_records('gradingform_rubric_fillings');
        $this->assertCount(4, $records);
        // Let's delete one of the instances (the last one would be the easiest).
        provider::delete_gradingform_for_instances([$instance->get_id()]);
        $records = $DB->get_records('gradingform_rubric_fillings');
        $this->assertCount(2, $records);
        foreach ($records as $record) {
            $this->assertNotEquals($instance->get_id(), $record->instanceid);
        }
    }
}

/**
 * Convenience class to create rubrics.
 *
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_rubric {

    /** @var array $criteria The criteria for this rubric. */
    protected $criteria = [];
    /** @var context The context that this rubric is in. */
    protected $context;
    /** @var string The name of this rubric. */
    protected $name;
    /** @var string A description for this rubric. */
    protected $text;
    /** @var integer The current criterion ID. This is incremented when a new criterion is added. */
    protected $criterionid = 0;
    /** @var grading_manager An object for managing the rubric. */
    public $manager;

    /**
     * Constuctor for this rubric.
     *
     * @param context $context The context that this rubric is being used in.
     * @param string $name Name of the rubric.
     * @param string $text Description of the rubric.
     */
    public function __construct($context, $name, $text) {
        $this->context = $context;
        $this->name = $name;
        $this->text = $text;
        $this->manager = get_grading_manager();
        $this->manager->set_context($context);
        $this->manager->set_component('mod_assign');
        $this->manager->set_area('submission');
    }

    /**
     * Creates the rubric using the appropriate APIs.
     */
    public function create_rubric() {

        $data = (object) [
            'areaid' => $this->context->id,
            'returnurl' => '',
            'name' => $this->name,
            'description_editor' => [
                'text' => $this->text,
                'format' => 1,
                'itemid' => 1
            ],
            'rubric' => [
                'criteria' => $this->criteria,
                'options' => [
                    'sortlevelsasc' => 1,
                    'lockzeropoints' => 1,
                    'showdescriptionteacher' => 1,
                    'showdescriptionstudent' => 1,
                    'showscoreteacher' => 1,
                    'showscorestudent' => 1,
                    'enableremarks' => 1,
                    'showremarksstudent' => 1
                ]
            ],
            'saverubric' => 'Save rubric and make it ready',
            'status' => 20
        ];

        $controller = $this->manager->get_controller('rubric');
        $controller->update_definition($data);
    }

    /**
     * Adds a criterion to the rubric.
     *
     * @param test_criterion $criterion The criterion object (class below).
     */
    public function add_criteria($criterion) {

        $this->criterionid++;
        $this->criteria['NEWID' . $this->criterionid] = [
            'description' => $criterion->description,
            'sortorder' => $this->criterionid,
            'levels' => $criterion->levels
        ];
    }

}

/**
 * Convenience class to create rubric criterion.
 *
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_criterion {

    /** @var string $description A description of the criterion. */
    public $description;
    /** @var integer $sortorder sort order of the criterion. */
    public $sortorder = 0;
    /** @var integer $levelid The current level id  for this level*/
    public $levelid = 0;
    /** @var array $levels The levels for this criterion. */
    public $levels = [];

    /**
     * Constructor for this test_criterion object
     *
     * @param string $description A description of this criterion.
     */
    public function __construct($description) {
        $this->description = $description;
    }

    /**
     * Adds levels to the criterion.
     *
     * @param string $definition The definition for this level.
     * @param int $score      The score received if this level is selected.
     */
    public function add_level($definition, $score) {
        $this->levelid++;
        $this->levels['NEWID' . $this->levelid] = [
            'definition' => $definition,
            'score' => $score
        ];
    }
}

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
namespace gradingform_rubric\privacy;

use core_privacy\tests\provider_testcase;
use core_privacy\local\request\writer;
use gradingform_rubric\privacy\provider;
use gradingform_rubric_controller;
use context_module;

/**
 * Privacy tests for gradingform_rubric
 *
 * @copyright  2018 Adrian Greeve <adriangreeve.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class provider_test extends provider_testcase {

    /**
     * Test the export of rubric data.
     */
    public function test_get_gradingform_export_data(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $modulecontext = context_module::instance($module->cmid);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Generate a test rubric and get its controller.
        $controller = $this->get_test_rubric($modulecontext, 'assign', 'submissions');

        // In the situation of mod_assign this would be the id from assign_grades.
        $itemid = 1;
        $instance = $controller->create_instance($user->id, $itemid);

        $data = $this->get_test_form_data(
            $controller,
            $itemid,
            1, 'This user made several mistakes.',
            0, 'Please add more pictures.'
        );

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
    public function test_delete_gradingform_for_instances(): void {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $modulecontext = context_module::instance($module->cmid);
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);

        // Generate a test rubric and get its controller.
        $controller = $this->get_test_rubric($modulecontext, 'assign', 'submissions');

        // In the situation of mod_assign this would be the id from assign_grades.
        $itemid = 1;
        $instance = $controller->create_instance($user->id, $itemid);

        $data = $this->get_test_form_data(
            $controller,
            $itemid,
            1, 'This user made several mistakes.',
            0, 'Please add more pictures.'
        );

        // Update this instance with data.
        $instance->update($data);

        // Second instance.
        $itemid = 2;
        $instance = $controller->create_instance($user->id, $itemid);

        $data = $this->get_test_form_data(
            $controller,
            $itemid,
            0, 'Too many mistakes. Please try again.',
            2, 'Great number of pictures. Well done.'
        );

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

    /**
     * Generate a rubric controller with sample data required for testing of this class.
     *
     * @param context_module $context
     * @param string $component
     * @param string $area
     * @return gradingform_rubric_controller
     */
    protected function get_test_rubric(context_module $context, string $component, string $area): gradingform_rubric_controller {
        $generator = \testing_util::get_data_generator();
        $rubricgenerator = $generator->get_plugin_generator('gradingform_rubric');

        return $rubricgenerator->get_test_rubric($context, $component, $area);
    }

    /**
     * Fetch a set of sample data.
     *
     * @param gradingform_rubric_controller $controller
     * @param int $itemid
     * @param float $spellingscore
     * @param string $spellingremark
     * @param float $picturescore
     * @param string $pictureremark
     * @return array
     */
    protected function get_test_form_data(
        gradingform_rubric_controller $controller,
        int $itemid,
        float $spellingscore,
        string $spellingremark,
        float $picturescore,
        string $pictureremark
    ): array {
        $generator = \testing_util::get_data_generator();
        $rubricgenerator = $generator->get_plugin_generator('gradingform_rubric');

        return $rubricgenerator->get_test_form_data(
            $controller,
            $itemid,
            $spellingscore,
            $spellingremark,
            $picturescore,
            $pictureremark
        );
    }
}

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
 * Privacy tests for gradingform_guide.
 *
 * @package    gradingform_guide
 * @category   test
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\tests\provider_testcase;
use \core_privacy\local\request\writer;
use \gradingform_guide\privacy\provider;

/**
 * Privacy tests for gradingform_guide.
 *
 * @package    gradingform_guide
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradingform_guide_privacy_testcase extends provider_testcase {

    /**
     * Ensure that export_user_preferences returns no data if the user has no data.
     */
    public function test_export_user_preferences_not_defined() {
        $user = \core_user::get_user_by_username('admin');
        provider::export_user_preferences($user->id);

        $writer = writer::with_context(\context_system::instance());
        $this->assertFalse($writer->has_any_data());
    }

    /**
     * Ensure that export_user_preferences returns single preferences.
     */
    public function test_export_user_preferences() {
        $this->resetAfterTest();

        // Define a user preference.
        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        set_user_preference('gradingform_guide-showmarkerdesc', 0, $user);
        set_user_preference('gradingform_guide-showstudentdesc', 1, $user);

        // Validate exported data.
        provider::export_user_preferences($user->id);
        $context = context_user::instance($user->id);
        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());
        $prefs = $writer->get_user_preferences('gradingform_guide');
        $this->assertCount(2, (array) $prefs);
        $this->assertEquals(
            get_string('privacy:metadata:preference:showstudentdesc', 'gradingform_guide'),
            $prefs->{'gradingform_guide-showstudentdesc'}->description
        );
        $this->assertEquals(get_string('no'), $prefs->{'gradingform_guide-showmarkerdesc'}->value);
        $this->assertEquals(get_string('yes'), $prefs->{'gradingform_guide-showstudentdesc'}->value);
    }

    /**
     * Test the export of guide data.
     */
    public function test_get_gradingform_export_data() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);

        $modulecontext = context_module::instance($module->cmid);
        $controller = $this->get_test_guide($modulecontext);

        // In the situation of mod_assign this would be the id from assign_grades.
        $itemid = 1;
        $instance = $controller->create_instance($user->id, $itemid);
        $data = $this->get_test_form_data(
            $controller,
            $itemid,
            5, 'This user made several mistakes.',
            10, 'This user has two pictures.'
        );

        $instance->update($data);
        $instanceid = $instance->get_data('id');

        // Let's try the method we are testing.
        provider::export_gradingform_instance_data($modulecontext, $instance->get_id(), ['Test']);
        $data = (array) writer::with_context($modulecontext)->get_data(['Test', 'Marking guide', $instanceid]);
        $this->assertCount(2, $data);
        $this->assertEquals('Spelling mistakes', $data['Spelling mistakes']->shortname);
        $this->assertEquals('This user made several mistakes.', $data['Spelling mistakes']->remark);
        $this->assertEquals('Pictures', $data['Pictures']->shortname);
        $this->assertEquals('This user has two pictures.', $data['Pictures']->remark);
    }

    /**
     * Test the deletion of guide user information via the instance ID.
     */
    public function test_delete_gradingform_for_instances() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $user = $this->getDataGenerator()->create_user();

        $this->setUser($user);

        $modulecontext = context_module::instance($module->cmid);
        $controller = $this->get_test_guide($modulecontext);

        // In the situation of mod_assign this would be the id from assign_grades.
        $itemid = 1;
        $instance = $controller->create_instance($user->id, $itemid);
        $data = $this->get_test_form_data(
            $controller,
            $itemid,
            5, 'This user made several mistakes.',
            10, 'This user has two pictures.'
        );

        $instance->update($data);
        $instanceid = $instance->get_data('id');

        $itemid = 2;
        $instance = $controller->create_instance($user->id, $itemid);
        $data = $this->get_test_form_data(
            $controller,
            $itemid,
            25, 'This user made no mistakes.',
            5, 'This user has one pictures.'
        );

        $instance->update($data);
        $instanceid = $instance->get_data('id');

        // Check how many records we have in the fillings table.
        $records = $DB->get_records('gradingform_guide_fillings');
        $this->assertCount(4, $records);
        // Let's delete one of the instances (the last one would be the easiest).
        provider::delete_gradingform_for_instances([$instance->get_id()]);
        $records = $DB->get_records('gradingform_guide_fillings');
        $this->assertCount(2, $records);
        foreach ($records as $record) {
            $this->assertNotEquals($instance->get_id(), $record->instanceid);
        }
    }

    /**
     * Generate a guide controller with sample data required for testing of this class.
     *
     * @param context_module $context
     * @return gradingform_guide_controller
     */
    protected function get_test_guide(context_module $context): gradingform_guide_controller {
        $generator = \testing_util::get_data_generator();
        $guidegenerator = $generator->get_plugin_generator('gradingform_guide');

        return $guidegenerator->get_test_guide($context);
    }

    /**
     * Fetch a set of sample data.
     *
     * @param gradingform_guide_controller $controller
     * @param int $itemid
     * @param float $spellingscore
     * @param string $spellingremark
     * @param float $picturescore
     * @param string $pictureremark
     * @return array
     */
    protected function get_test_form_data(
        gradingform_guide_controller $controller,
        int $itemid,
        float $spellingscore,
        string $spellingremark,
        float $picturescore,
        string $pictureremark
    ): array {
        $generator = \testing_util::get_data_generator();
        $guidegenerator = $generator->get_plugin_generator('gradingform_guide');

        return $guidegenerator->get_test_form_data(
            $controller,
            $itemid,
            $spellingscore,
            $spellingremark,
            $picturescore,
            $pictureremark
        );
    }
}

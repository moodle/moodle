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
 * Privacy tests for core_grading.
 *
 * @package    core_grading
 * @category   test
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

use \core_privacy\tests\provider_testcase;
use \core_privacy\local\request\approved_contextlist;
use \core_privacy\local\request\transform;
use \core_privacy\local\request\writer;
use \core_grading\privacy\provider;

/**
 * Privacy tests for core_grading.
 *
 * @copyright  2018 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_grading_privacy_testcase extends provider_testcase {

    /** @var stdClass User without data. */
    protected $user0;

    /** @var stdClass User with data. */
    protected $user1;

    /** @var stdClass User with data. */
    protected $user2;

    /** @var context context_module of an activity without grading definitions. */
    protected $instancecontext0;

    /** @var context context_module of the activity where the grading definitions are. */
    protected $instancecontext1;

    /** @var context context_module of the activity where the grading definitions are. */
    protected $instancecontext2;

    /**
     * Test getting the context for the user ID related to this plugin.
     */
    public function test_get_contexts_for_userid() {
        global $DB;

        $this->resetAfterTest();
        $this->grading_setup_test_scenario_data();
        $this->assertCount(2, $DB->get_records('grading_definitions'));

        // User1 has created grading definitions for instance1 and instance2.
        $contextlist = provider::get_contexts_for_userid($this->user1->id);
        $this->assertCount(2, $contextlist);
        $this->assertContains($this->instancecontext1->id, $contextlist->get_contextids());
        $this->assertContains($this->instancecontext2->id, $contextlist->get_contextids());
        $this->assertNotContains($this->instancecontext0->id, $contextlist->get_contextids());

        // User2 has only modified grading definitions for instance2.
        $contextlist = provider::get_contexts_for_userid($this->user2->id);
        $this->assertCount(1, $contextlist);
        $this->assertContains($this->instancecontext2->id, $contextlist->get_contextids());

        // User0 hasn't created or modified any grading definition.
        $contextlist = provider::get_contexts_for_userid($this->user0->id);
        $this->assertCount(0, $contextlist);
    }

    /**
     * Test retrieval of user ids in a given context.
     */
    public function test_get_users_in_context() {
        $this->resetAfterTest();
        $this->grading_setup_test_scenario_data();
        // Instance two has one user who created the definitions and another who modified it.
        $userlist = new \core_privacy\local\request\userlist($this->instancecontext2, 'core_grading');
        provider::get_users_in_context($userlist);
        // Check that we get both.
        $this->assertCount(2, $userlist->get_userids());
    }

    /**
     * Export for a user with no grading definitions created or modified will not have any data exported.
     */
    public function test_export_user_data_no_content() {
        $this->resetAfterTest();

        $user = $this->getDataGenerator()->create_user();
        $this->setUser($user);
        $context = \context_system::instance();

        $writer = writer::with_context($context);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($user->id, $context, 'core_grading');
        $this->assertFalse(writer::with_context($context)->has_any_data());
    }

    /**
     * Test that data is exported correctly for this plugin.
     */
    public function test_export_user_data() {
        global $DB;

        $this->resetAfterTest();
        $now = time();
        $defnameprefix = 'fakename';
        $this->grading_setup_test_scenario_data($defnameprefix, $now);
        $this->assertCount(2, $DB->get_records('grading_definitions'));

        // Validate exported data: instance1 - user0 has NO data.
        $this->setUser($this->user0);
        writer::reset();
        $writer = writer::with_context($this->instancecontext1);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($this->user0->id, $this->instancecontext1, 'core_grading');
        $data = $writer->get_data([get_string('gradingmethod', 'grading')]);
        $this->assertEmpty($data);

        // Validate exported data: instance0 - user1 has NO data.
        $this->setUser($this->user1);
        writer::reset();
        $writer = writer::with_context($this->instancecontext0);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($this->user1->id, $this->instancecontext0, 'core_grading');
        $data = $writer->get_data([get_string('gradingmethod', 'grading')]);
        $this->assertEmpty($data);

        // Validate exported data: instance1 - user1 has data (user has created and modified it).
        writer::reset();
        $writer = writer::with_context($this->instancecontext1);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($this->user1->id, $this->instancecontext1, 'core_grading');
        $data = $writer->get_data([get_string('gradingmethod', 'grading')]);
        $this->assertCount(1, $data->definitions);

        $firstkey = reset($data->definitions);
        $this->assertNotEmpty($firstkey->name);
        $this->assertEquals('test_method', $firstkey->method);
        $this->assertEquals(transform::datetime($now), $firstkey->timecreated);
        $this->assertEquals($this->user1->id, $firstkey->usercreated);
        $this->assertEquals($defnameprefix.'1', $firstkey->name);

        // Validate exported data: instance2 - user1 has data (user has created it).
        writer::reset();
        $writer = writer::with_context($this->instancecontext2);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($this->user1->id, $this->instancecontext2, 'core_grading');
        $data = $writer->get_data([get_string('gradingmethod', 'grading')]);
        $this->assertCount(1, $data->definitions);

        $firstkey = reset($data->definitions);
        $this->assertNotEmpty($firstkey->name);
        $this->assertEquals('test_method', $firstkey->method);
        $this->assertEquals(transform::datetime($now), $firstkey->timecreated);
        $this->assertEquals($this->user1->id, $firstkey->usercreated);
        $this->assertEquals($defnameprefix.'2', $firstkey->name);

        // Validate exported data: instance1 - user2 has NO data.
        $this->setUser($this->user2);
        writer::reset();
        $writer = writer::with_context($this->instancecontext1);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($this->user2->id, $this->instancecontext1, 'core_grading');
        $data = $writer->get_data([get_string('gradingmethod', 'grading')]);
        $this->assertEmpty($data);

        // Validate exported data: instance2 - user2 has data (user has modified it).
        $this->setUser($this->user2);
        writer::reset();
        $writer = writer::with_context($this->instancecontext2);
        $this->assertFalse($writer->has_any_data());
        $this->export_context_data_for_user($this->user2->id, $this->instancecontext2, 'core_grading');
        $data = $writer->get_data([get_string('gradingmethod', 'grading')]);
        $this->assertCount(1, $data->definitions);
    }

    /**
     * Test for provider::delete_data_for_all_users_in_context().
     */
    public function test_delete_data_for_all_users_in_context() {
        global $DB;

        $this->resetAfterTest();
        $this->grading_setup_test_scenario_data();

        // Before deletion, we should have 2 grading_definitions.
        $this->assertCount(2, $DB->get_records('grading_definitions'));

        // Delete data.
        provider::delete_data_for_all_users_in_context($this->instancecontext0);
        provider::delete_data_for_all_users_in_context($this->instancecontext1);
        provider::delete_data_for_all_users_in_context($this->instancecontext2);

        // Before deletion, we should have same grading_definitions (nothing was deleted).
        $this->assertCount(2, $DB->get_records('grading_definitions'));
    }

    /**
     * Test for provider::delete_data_for_user().
     */
    public function test_delete_data_for_user() {
        global $DB;

        $this->resetAfterTest();
        $this->grading_setup_test_scenario_data();

        // Before deletion, we should have 2 grading_definitions.
        $this->assertCount(2, $DB->get_records('grading_definitions'));

        // Delete data for $user0.
        $contextlist = provider::get_contexts_for_userid($this->user0->id);
        $approvedcontextlist = new approved_contextlist(
            $this->user0,
            'core_grading',
            $contextlist->get_contextids()
        );
        provider::delete_data_for_user($approvedcontextlist);

        // Delete data for $user1.
        $contextlist = provider::get_contexts_for_userid($this->user1->id);
        $approvedcontextlist = new approved_contextlist(
            $this->user1,
            'core_grading',
            $contextlist->get_contextids()
        );
        provider::delete_data_for_user($approvedcontextlist);

        // Delete data for $user2.
        $contextlist = provider::get_contexts_for_userid($this->user2->id);
        $approvedcontextlist = new approved_contextlist(
            $this->user2,
            'core_grading',
            $contextlist->get_contextids()
        );
        provider::delete_data_for_user($approvedcontextlist);

        // Before deletion, we should have same grading_definitions (nothing was deleted).
        $this->assertCount(2, $DB->get_records('grading_definitions'));
    }

    /**
     * Test exporting user data relating to an item ID.
     */
    public function test_export_item_data() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $user = $this->getDataGenerator()->create_user();
        $guidegenerator = \testing_util::get_data_generator()->get_plugin_generator('gradingform_guide');

        $this->setUser($user);

        $modulecontext = context_module::instance($module->cmid);
        $controller = $guidegenerator->get_test_guide($modulecontext);

        // In the situation of mod_assign this would be the id from assign_grades.
        $itemid = 1;
        $instance = $controller->create_instance($user->id, $itemid);
        $data = $guidegenerator->get_test_form_data(
            $controller,
            $itemid,
            5, 'This user made several mistakes.',
            10, 'This user has two pictures.'
        );

        $instance->update($data);
        $instanceid = $instance->get_data('id');

        provider::export_item_data($modulecontext, $itemid, ['Test']);
        $data = (array) writer::with_context($modulecontext)->get_data(['Test', 'Marking guide', $instance->get_data('id')]);
        $this->assertCount(2, $data);
        $this->assertEquals('This user made several mistakes.', $data['Spelling mistakes']->remark);
        $this->assertEquals(5, $data['Spelling mistakes']->score);
        $this->assertEquals('This user has two pictures.', $data['Pictures']->remark);
        $this->assertEquals(10, $data['Pictures']->score);
    }

    /**
     * Test deleting user data related to a context and item ID.
     */
    public function test_delete_instance_data() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $user = $this->getDataGenerator()->create_user();
        $guidegenerator = \testing_util::get_data_generator()->get_plugin_generator('gradingform_guide');

        $this->setUser($user);

        $modulecontext = context_module::instance($module->cmid);
        $controller = $guidegenerator->get_test_guide($modulecontext);

        // In the situation of mod_assign this would be the id from assign_grades.
        $itemid = 1;
        $instance = $controller->create_instance($user->id, $itemid);
        $data = $guidegenerator->get_test_form_data(
            $controller,
            $itemid,
            5, 'This user made several mistakes.',
            10, 'This user has two pictures.'
        );
        $instance->update($data);

        $itemid = 2;
        $instance = $controller->create_instance($user->id, $itemid);
        $data = $guidegenerator->get_test_form_data(
            $controller,
            $itemid,
            25, 'This user made no mistakes.',
            5, 'This user has one picture.'
        );
        $instance->update($data);

        // Check how many records we have in the fillings table.
        $records = $DB->get_records('gradingform_guide_fillings');
        $this->assertCount(4, $records);
        // Let's delete one of the instances (the last one would be the easiest).
        provider::delete_instance_data($modulecontext, $itemid);
        $records = $DB->get_records('gradingform_guide_fillings');
        $this->assertCount(2, $records);
        foreach ($records as $record) {
            $this->assertNotEquals($instance->get_id(), $record->instanceid);
        }
        // This will delete all the rest of the instances for this context.
        provider::delete_instance_data($modulecontext);
        $records = $DB->get_records('gradingform_guide_fillings');
        $this->assertEmpty($records);
    }

    /**
     * Test the deletion of multiple instances at once.
     */
    public function test_delete_data_for_instances() {
        global $DB;
        $this->resetAfterTest();
        $course = $this->getDataGenerator()->create_course();
        $module = $this->getDataGenerator()->create_module('assign', ['course' => $course]);
        $user1 = $this->getDataGenerator()->create_user();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();
        $guidegenerator = \testing_util::get_data_generator()->get_plugin_generator('gradingform_guide');

        $this->setUser($user1);

        $modulecontext = context_module::instance($module->cmid);
        $controller = $guidegenerator->get_test_guide($modulecontext);

        // In the situation of mod_assign this would be the id from assign_grades.
        $itemid1 = 1;
        $instance1 = $controller->create_instance($user1->id, $itemid1);
        $data = $guidegenerator->get_test_form_data(
            $controller,
            $itemid1,
            5, 'This user made several mistakes.',
            10, 'This user has two pictures.'
        );
        $instance1->update($data);

        $itemid2 = 2;
        $instance2 = $controller->create_instance($user2->id, $itemid2);
        $data = $guidegenerator->get_test_form_data(
            $controller,
            $itemid2,
            15, 'This user made a couple of mistakes.',
            10, 'This user has one picture.'
        );
        $instance2->update($data);

        $itemid3 = 3;
        $instance3 = $controller->create_instance($user3->id, $itemid3);
        $data = $guidegenerator->get_test_form_data(
            $controller,
            $itemid3,
            20, 'This user made one mistakes.',
            10, 'This user has one picture.'
        );
        $instance3->update($data);

        $records = $DB->get_records('gradingform_guide_fillings');
        $this->assertCount(6, $records);

        // Delete all user data for items 1 and 3.
        provider::delete_data_for_instances($modulecontext, [$itemid1, $itemid3]);

        $records = $DB->get_records('gradingform_guide_fillings');
        $this->assertCount(2, $records);
        $instanceid = $instance2->get_data('id');
        // The instance id should match for all remaining records.
        foreach ($records as $record) {
            $this->assertEquals($instanceid, $record->instanceid);
        }
    }

    /**
     * Helper function to setup the environment.
     *
     * course
     *  |
     *  +--instance0 (assignment)
     *  |   |
     *  +--instance1 (assignment)
     *  |   |
     *  |   +--grading_definition1 (created and modified by user1)
     *  |   |
     *  +--instance2 (assignment)
     *  |   |
     *  |   +--grading_definition2 (created by user1 and modified by user2)
     *
     *
     * user0 hasn't any data.
     *
     * @param string $defnameprefix
     * @param timestamp $now
     */
    protected function grading_setup_test_scenario_data($defnameprefix = null, $now = null) {
        global $DB;

        $this->user0 = $this->getDataGenerator()->create_user();
        $this->user1 = $this->getDataGenerator()->create_user();
        $this->user2 = $this->getDataGenerator()->create_user();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();
        $coursecontext = context_course::instance($course->id);

        // Create some assignment instances.
        $params = (object)array(
            'course' => $course->id,
            'name'   => 'Testing instance'
        );
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_assign');
        $instance0 = $generator->create_instance($params);
        $cm0 = get_coursemodule_from_instance('assign', $instance0->id);
        $this->instancecontext0 = context_module::instance($cm0->id);
        $instance1 = $generator->create_instance($params);
        $cm1 = get_coursemodule_from_instance('assign', $instance1->id);
        $this->instancecontext1 = context_module::instance($cm1->id);
        $instance2 = $generator->create_instance($params);
        $cm2 = get_coursemodule_from_instance('assign', $instance2->id);
        $this->instancecontext2 = context_module::instance($cm2->id);

        // Create fake grading areas.
        $fakearea1 = (object)array(
            'contextid'    => $this->instancecontext1->id,
            'component'    => 'mod_assign',
            'areaname'     => 'submissions',
            'activemethod' => 'test_method'
        );
        $fakeareaid1 = $DB->insert_record('grading_areas', $fakearea1);
        $fakearea2 = clone($fakearea1);
        $fakearea2->contextid = $this->instancecontext2->id;
        $fakeareaid2 = $DB->insert_record('grading_areas', $fakearea2);

        // Create fake grading definitions.
        if (empty($now)) {
            $now = time();
        }
        if (empty($defnameprefix)) {
            $defnameprefix = 'fakename';
        }
        $fakedefinition1 = (object)array(
            'areaid'       => $fakeareaid1,
            'method'       => 'test_method',
            'name'         => $defnameprefix.'1',
            'status'       => 0,
            'timecreated'  => $now,
            'usercreated'  => $this->user1->id,
            'timemodified' => $now + 1,
            'usermodified' => $this->user1->id,
        );
        $fakedefid1 = $DB->insert_record('grading_definitions', $fakedefinition1);
        $fakedefinition2 = clone($fakedefinition1);
        $fakedefinition2->areaid = $fakeareaid2;
        $fakedefinition2->name = $defnameprefix.'2';
        $fakedefinition2->usermodified = $this->user2->id;
        $fakedefid2 = $DB->insert_record('grading_definitions', $fakedefinition2);
    }
}

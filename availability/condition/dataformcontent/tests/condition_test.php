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
 * Unit tests for the completion condition.
 *
 * @package availability_dataformcontent
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use availability_dataformcontent\condition;

/**
 * Unit tests for the completion condition.
 *
 * @package availability_dataformcontent
 * @category phpunit
 * @group availability_dataformcontent
 * @group mod_dataform
 * @copyright 2015 Itamar Tzadok
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class availability_dataformcontent_condition_testcase extends advanced_testcase {
    /** @var mod_dataform_dataform Dataform instance for testing */
    protected $df;

    /** @var array Array of user IDs for whome we already set the profile field */
    protected $setusers = array();

    /** @var condition Current condition */
    private $cond;

    /** @var \core_availability\info Current info */
    private $info;

    public function setUp() {
        global $CFG;

        $this->resetAfterTest();

        // Clear static cache.
        \availability_dataformcontent\condition::wipe_static_cache();
        \mod_dataform_instance_store::unregister();

        $CFG->availability_dataformcontent_activityref = 'name';
        $CFG->availability_dataformcontent_reservedfield = 'Conditional Activity';
        $CFG->availability_dataformcontent_reservedfilter = 'Availability';
    }

    /**
     * Tests the constructor including error conditions. Also tests the
     * string conversion feature (intended for debugging only).
     */
    public function atest_constructor() {
        $structure = new stdClass();

        // Expecting int for id.
        $invalidcases = array(
            null, // Empty.
            'string', // String.
            1.5, // Float.
        );

        foreach ($invalidcases as $case) {
            $structure->id = $case;
            try {
                $cond = new condition($structure);
                $this->fail();
            } catch (coding_exception $e) {
                $this->assertContains('Invalid ->id for dataformcontent condition', $e->getMessage());
            }
        }

        // Valid (no id).
        unset($structure->id);
        $cond = new condition($structure);
        $this->assertEquals('{dataformcontent:none}', (string)$cond);

        // Valid id 0.
        $structure->id = 0;
        $cond = new condition($structure);
        $this->assertEquals('{dataformcontent:none}', (string)$cond);

        // Valid some id.
        $structure->id = 1478;
        $cond = new condition($structure);
        $this->assertEquals('{dataformcontent:#1478}', (string)$cond);
    }

    /**
     * Tests the save() function.
     */
    public function atest_save() {
        $structure = (object)array('id' => 42);
        $cond = new condition($structure);
        $structure->type = 'dataformcontent';
        $this->assertEquals($structure, $cond->save());

        $structure = (object)array();
        $cond = new condition($structure);
        $structure->type = 'dataformcontent';
        $this->assertEquals($structure, $cond->save());
    }

    /**
     * Tests the update_dependency_id() function.
     */
    public function atest_update_dependency_id() {
        $cond = new condition((object)array('id' => 123));
        $this->assertFalse($cond->update_dependency_id('frog', 123, 456));
        $this->assertFalse($cond->update_dependency_id('dataform', 12, 34));
        $this->assertTrue($cond->update_dependency_id('dataform', 123, 456));
        $after = $cond->save();
        $this->assertEquals(456, $after->id);
    }

    /**
     * Tests constructing and using a condition as part of tree.
     */
    public function atest_in_tree() {
        global $USER, $CFG;

        $this->setAdminUser();

        // Prepare info_module.
        $conditioned = $this->get_a_dataform(array('name' => 'Conditional Dataform'));
        $info = $this->get_an_info($conditioned);

        // Create a dashboard dataform.
        $reservedname = \availability_dataformcontent\condition::get_reserved_field_name();
        $dashboard = $this->get_a_dashboard_dataform();
        $field = $dashboard->field_manager->get_field_by_name($reservedname);
        $view = $dashboard->view_manager->get_view_by_id($dashboard->defaultview);

        $structure = (object) array(
            'op' => '|',
            'show' => true,
            'c' => array(
                (object) array(
                    'type' => 'dataformcontent',
                    'id' => (int) $dashboard->id,
                )
            )
        );
        $tree = new \core_availability\tree($structure);

        // Initial check (no entry for user in the target dataform).
        $result = $tree->check_available(false, $info, true, $USER->id);
        $this->assertFalse($result->is_available());

        // Add entry for user.
        $data = (object) array(
            'submitbutton_save' => 'Save',
            "field_{$field->id}_-1" => 'Conditional Dataform',
            'entry_-1_userid' => $USER->id,
        );
        $view->entry_manager->process_entries('update', array(-1), $data, true);

        // Now it's true!
        $result = $tree->check_available(false, $info, true, $USER->id);
        $this->assertTrue($result->is_available());
    }

    /**
     * Tests the is_available() and is_available_to_all() functions.
     */
    public function test_is_available() {
        global $DB;

        $this->setAdminUser();

        // Create a course.
        $course = $this->getDataGenerator()->create_course();

        $roles = $DB->get_records_menu('role', array(), '', 'shortname,id');

        // Add students.
        $student1 = $this->getDataGenerator()->create_user(array('username' => 'student1'));
        $this->getDataGenerator()->enrol_user($student1->id, $course->id, $roles['student']);
        $student2 = $this->getDataGenerator()->create_user(array('username' => 'student2'));
        $this->getDataGenerator()->enrol_user($student2->id, $course->id, $roles['student']);

        // Prepare info_module.
        $params = array('name' => 'Conditional Dataform', 'course' => $course->id);
        $conditioned = $this->get_a_dataform($params);
        $info = $this->get_an_info($conditioned);

        // Create a dashboard dataform.
        $reservedname = \availability_dataformcontent\condition::get_reserved_field_name();
        $params = array('name' => 'Dashboard', 'course' => $course->id);
        $dashboard = $this->get_a_dashboard_dataform($params);
        $activityfield = $dashboard->field_manager->get_field_by_name($reservedname);
        $fromfield = $dashboard->field_manager->get_field_by_name('From');
        $tofield = $dashboard->field_manager->get_field_by_name('To');
        $view = $dashboard->view_manager->get_view_by_id($dashboard->defaultview);

        // Condition.
        $cond = new condition((object) array(
            'type' => 'dataformcontent',
            'id' => (int) $dashboard->id,
        ));

        // Student1 entry.
        $entry1 = (object) array(
            'submitbutton_save' => 'Save',
            "field_{$activityfield->id}_-1" => 'Conditional Dataform',
            "field_{$fromfield->id}_-1" => strtotime('yesterday'),
            "field_{$tofield->id}_-1" => strtotime('now -1 hour'),
            'entry_-1_userid' => $student1->id,
        );

        // Student2 entry.
        $entry2 = (object) array(
            'submitbutton_save' => 'Save',
            "field_{$activityfield->id}_-1" => 'Conditional Dataform',
            "field_{$fromfield->id}_-1" => strtotime('yesterday'),
            "field_{$tofield->id}_-1" => strtotime('tomorrow'),
            'entry_-1_userid' => $student2->id,
        );

        // No entries, not available.
        $this->setUser($student1);
        $this->assertFalse($cond->is_available(false, $info, true, $student1->id));
        $this->setUser($student2);
        $this->assertFalse($cond->is_available(false, $info, true, $student2->id));

        // Add student1 entry.
        $this->setAdminUser();
        $view->entry_manager->process_entries('update', array(-1), $entry1, true);

        // Dashboard not individualized so everyone gets access.
        $this->setUser($student1);
        $this->assertTrue($cond->is_available(false, $info, true, $student1->id));
        $this->setUser($student2);
        $this->assertTrue($cond->is_available(false, $info, true, $student2->id));

        // Set the Dashboard to individualized.
        $this->setAdminUser();
        $dashboard->individualized = 1;

        // Only student1 gets access.
        $this->setUser($student1);
        $this->assertTrue($cond->is_available(false, $info, true, $student1->id));
        $this->setUser($student2);
        $this->assertFalse($cond->is_available(false, $info, true, $student2->id));

        // Add student2 entry.
        $this->setAdminUser();
        $view->entry_manager->process_entries('update', array(-1), $entry2, true);

        // Both students get access.
        $this->setUser($student1);
        $this->assertTrue($cond->is_available(false, $info, true, $student1->id));
        $this->setUser($student2);
        $this->assertTrue($cond->is_available(false, $info, true, $student2->id));

        // Add designated filter.
        $this->setAdminUser();
        $searchoptions = array(
            "AND,$fromfield->name,content,,<=,now",
            "AND,$tofield->name,content,,>=,now",
        );
        $filter = array(
            'name' => \availability_dataformcontent\condition::get_reserved_filter_name(),
            'dataid' => $dashboard->id,
            'searchoptions' => implode(';', $searchoptions),
        );
        $this->getDataGenerator()->get_plugin_generator('mod_dataform')->create_filter($filter);

        // Only student2 gets access.
        $this->setUser($student1);
        $this->assertFalse($cond->is_available(false, $info, true, $student1->id));
        $this->setUser($student2);
        $this->assertTrue($cond->is_available(false, $info, true, $student2->id));
    }

    /**
     * Generates and returns a dataform activity in a course.
     *
     * @param array $options
     * @return mod_dataform_dataform
     */
    protected function get_a_dataform(array $options = array()) {
        global $SITE;

        // The generator used to create a data module.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_dataform');

        if (empty($options['id'])) {
            if (empty($options['course'])) {
                $options['course'] = $SITE->id;
            }
            // Create a dataform instance.
            $data = $generator->create_instance($options);
            $dataformid = $data->id;
        } else {
            $dataformid = $options['id'];
        }
        $df = mod_dataform_dataform::instance($dataformid);

        return $df;
    }

    /**
     * Generates and returns a dashboard dataform activity in a course.
     *
     * @return mod_dataform_dataform
     */
    protected function get_a_dashboard_dataform(array $options = array()) {
        $df = $this->get_a_dataform($options);
        // Add a Conditional Activity field.
        $field = $df->field_manager->add_field('text');
        $field->name = \availability_dataformcontent\condition::get_reserved_field_name();
        $field->update($field->data);
        // Add a From field.
        $field = $df->field_manager->add_field('time');
        $field->name = 'From';
        $field->update($field->data);
        // Add a To field.
        $field = $df->field_manager->add_field('time');
        $field->name = 'To';
        $field->update($field->data);
        // Add a view.
        $view = $df->view_manager->add_view('aligned');
        // Make the view default.
        $df->update(array('defaultview' => $view->id));

        return $df;
    }

    /**
     * Generates and an info module object for the specified dataform.
     *
     * @param \mod_dataform_dataform $df
     * @return \core_availability\info_module
     */
    protected function get_an_info(\mod_dataform_dataform $df) {
        $cminfo = cm_info::create($df->cm);
        $info = new \core_availability\info_module($cminfo);

        return $info;
    }
}

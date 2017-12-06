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

defined('MOODLE_INTERNAL') or die;

/**
 * Grading test case.
 *
 * @package    dataformfield_entrystate
 * @category   phpunit
 * @group      dataformfield_entrystate
 * @group      dataformfield
 * @group      mod_dataform_grading
 * @group      mod_dataform
 * @copyright  2014 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataformfield_entrystate_grading_testcase extends advanced_testcase {

    protected $course;
    protected $teacher;
    protected $student1;
    protected $student2;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        global $DB;

        $this->resetAfterTest();

        // Reset dataform local cache.
        \mod_dataform_instance_store::unregister();

        // Create a course we are going to add a data module to.
        $this->course = $this->getDataGenerator()->create_course();
        $courseid = $this->course->id;

        $roles = $DB->get_records_menu('role', array(), '', 'shortname,id');

        // Teacher.
        $user = $this->getDataGenerator()->create_user(array('username' => 'teacher'));
        $roleshortname = \mod_dataform\helper\testing::get_role_shortname('editingteacher');
        $this->getDataGenerator()->enrol_user($user->id, $courseid, $roles[$roleshortname]);
        $this->teacher = $user;

        // Student 1.
        $user = $this->getDataGenerator()->create_user(array('username' => 'student1'));
        $roleshortname = \mod_dataform\helper\testing::get_role_shortname('student');
        $this->getDataGenerator()->enrol_user($user->id, $courseid, $roles[$roleshortname]);
        $this->student1 = $user;

        // Student 2.
        $user = $this->getDataGenerator()->create_user(array('username' => 'student2'));
        $roleshortname = \mod_dataform\helper\testing::get_role_shortname('student');
        $this->getDataGenerator()->enrol_user($user->id, $courseid, $roles[$roleshortname]);
        $this->student2 = $user;
    }

    /**
     * Set up function. In this instance we are setting up dataform
     * entries to be used in the unit tests.
     */
    public function test_calculated_grading() {
        global $DB;

        $this->setAdminUser();

        // Course.
        $courseid = $this->course->id;

        // Dataform.
        $params = array(
            'course' => $courseid,
            'grade' => 100,
            'gradeitems' => serialize(array(0 => array('ca' => 'SUM(##:entrystate##)'))),
        );
        $dataform = $this->getDataGenerator()->create_module('dataform', $params);
        $df = mod_dataform_dataform::instance($dataform->id);

        // Add a field.
        $field = $df->field_manager->add_field('entrystate');
        $field->param1 = base64_encode(serialize(array('states' => "Submitted\nApproved")));
        $field->update($field->data);

        // Add a view.
        $view = $df->view_manager->add_view('aligned');

        // Get an entry manager.
        $entryman = $view->entry_manager;

        // Fetch the grade item.
        $params = array(
            'itemtype' => 'mod',
            'itemmodule' => 'dataform',
            'iteminstance' => $df->id,
            'courseid' => $courseid,
            'itemnumber' => 0
        );
        $gitem = grade_item::fetch($params);

        // No grade yet for Student 1.
        $grade = $gitem->get_grade($this->student1->id, false);
        $this->assertEquals(null, $grade->finalgrade);

        // Student 1 adds 5 entries.
        $this->setUser($this->student1);
        $eids = range(-1, -5, -1);
        list(, $eids) = $entryman->process_entries('update', $eids, (object) array('submitbutton_save' => 'Save'), true);

        $entryid1 = $eids[0];
        $entryid2 = $eids[1];
        $entryid3 = $eids[2];
        $entryid4 = $eids[3];

        // Grade 0 for Student 1.
        $grade = $gitem->get_grade($this->student1->id, false);
        $this->assertEquals(0, $grade->finalgrade);

        // Teacher approves 1 entry for Student 1.
        $this->setUser($this->teacher);
        $data = array(
            'submitbutton_save' => 'Save',
            "field_{$field->id}_{$entryid1}" => 1,
        );
        $entryman->set_content(array('filter' => $view->filter));
        $entryman->process_entries('update', array($entryid1), (object) $data, true);

        // Grade for Student 1 is 1.
        $grade = $gitem->get_grade($this->student1->id, false);
        $this->assertEquals(1, $grade->finalgrade);

        // Teacher approves two more entries for Student 1.
        $data = array(
            'submitbutton_save' => 'Save',
            "field_{$field->id}_{$entryid2}" => 1,
            "field_{$field->id}_{$entryid3}" => 1,
        );
        $entryman->set_content(array('filter' => $view->filter));
        $entryman->process_entries('update', array($entryid2, $entryid3), (object) $data, true);

        // Grade for Student 1 is 3.
        $grade = $gitem->get_grade($this->student1->id, false);
        $this->assertEquals(3, $grade->finalgrade);

        // Teacher disapprove one entry for Student 1.
        $data = array(
            'submitbutton_save' => 'Save',
            "field_{$field->id}_{$entryid1}" => 0,
        );
        $entryman->set_content(array('filter' => $view->filter));
        $entryman->process_entries('update', array($entryid1), (object) $data, true);

        // Grade for Student 1 is 2.
        $grade = $gitem->get_grade($this->student1->id, false);
        $this->assertEquals(2, $grade->finalgrade);

        // Teacher changes calculation.
        $gradeitems = serialize(array(0 => array('ca' => 'SUM(##:entrystate##)*2')));
        $df->update((object) array('gradeitems' => $gradeitems));

        // Grade for Student 1 is 4.
        $grade = $gitem->get_grade($this->student1->id, false);
        $this->assertEquals(2, $grade->finalgrade);

        // Grade for Student 2 is 0.
        $grade = $gitem->get_grade($this->student2->id, false);
        $this->assertEquals(0, $grade->finalgrade);

        $df->delete();
    }
}

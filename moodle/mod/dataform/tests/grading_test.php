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
 * @package    mod_dataform
 * @category   phpunit
 * @group      mod_dataform_grading_test
 * @group      mod_dataform_grading
 * @group      mod_dataform
 * @copyright  2014 Itamar Tzadok
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_dataform_grading_testcase extends advanced_testcase {

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
        $editingteacherrolename = \mod_dataform\helper\testing::get_role_shortname('editingteacher');
        $studentrolename = \mod_dataform\helper\testing::get_role_shortname('student');

        // Teacher.
        $user = $this->getDataGenerator()->create_user(array('username' => 'teacher'));
        $this->getDataGenerator()->enrol_user($user->id, $courseid, $roles[$editingteacherrolename]);
        $this->teacher = $user;

        // Student 1.
        $user = $this->getDataGenerator()->create_user(array('username' => 'student1'));
        $this->getDataGenerator()->enrol_user($user->id, $courseid, $roles[$studentrolename]);
        $this->student1 = $user;

        // Student 2.
        $user = $this->getDataGenerator()->create_user(array('username' => 'student2'));
        $this->getDataGenerator()->enrol_user($user->id, $courseid, $roles[$studentrolename]);
        $this->student2 = $user;
    }

    /**
     * A grade item should be created on adding a new gradable dataform.
     */
    public function test_add_gradable_instance() {
        $this->setAdminUser();

        // Course.
        $courseid = $this->course->id;

        // Add instance with grade point.
        $params = array(
            'name' => 'Graded Dataform',
            'course' => $courseid,
            'grade' => 100,
        );
        $dataform = $this->getDataGenerator()->create_module('dataform', $params);
        $df = mod_dataform_dataform::instance($dataform->id);
        $this->assertEquals($params['grade'], $df->grade);
        $gitem = $this->fetch_grade_item($df->id, 0);
        $this->verify_grade_item($gitem, $params);

        // Add instance with grade scale.
        $scale = $this->getDataGenerator()->create_scale();
        $params = array(
            'name' => 'Scale Graded Dataform',
            'course' => $courseid,
            'grade' => -$scale->id,
        );
        $dataform = $this->getDataGenerator()->create_module('dataform', $params);
        $df = mod_dataform_dataform::instance($dataform->id);
        $this->assertEquals($params['grade'], $df->grade);
        $gitem = $this->fetch_grade_item($df->id, 0);
        $this->verify_grade_item($gitem, $params);

        // Add instance with no grade.
        $params = array(
            'name' => 'Scale Graded Dataform',
            'course' => $courseid,
        );
        $dataform = $this->getDataGenerator()->create_module('dataform', $params);
        $df = mod_dataform_dataform::instance($dataform->id);
        $this->assertEquals(0, $df->grade);
        $gitem = $this->fetch_grade_item($df->id, 0);
        $this->assertEquals(false, $gitem);

    }

    /**
     * A grade item should be added on updating a non-gradable dataform.
     * A grade item should be updated on updating a gradable dataform.
     */
    public function test_update_gradable_instance() {
        $this->setAdminUser();

        // Course.
        $courseid = $this->course->id;

        $params = array(
            'name' => 'Resource Dataform',
            'course' => $courseid,
        );

        // Add instance.
        $dataform = $this->getDataGenerator()->create_module('dataform', $params);
        $df = mod_dataform_dataform::instance($dataform->id);
        $grademan = $df->grade_manager;

        // Make instance gradable with grade point.
        $params['grade'] = 75;
        $df->update($params);
        $this->assertEquals($params['grade'], $df->grade);
        $gitem = $this->fetch_grade_item($df->id, 0);
        $this->verify_grade_item($gitem, $params);

        // Make instance gradable with scale point.
        $scale = $this->getDataGenerator()->create_scale();
        $params['grade'] = -$scale->id;
        $df->update($params);
        $this->assertEquals($params['grade'], $df->grade);
        $gitem = $this->fetch_grade_item($df->id, 0);
        $this->verify_grade_item($gitem, $params);

        // Change instance name.
        $params['name'] = 'Peer review';
        $df->update($params);
        $this->assertEquals($params['grade'], $df->grade);
        $gitem = $this->fetch_grade_item($df->id, 0);
        $this->verify_grade_item($gitem, $params);

        // Make instance non gradable.
        $params['grade'] = 0;
        $df->update($params);
        $this->assertEquals(0, $df->grade);
        $gitem = $this->fetch_grade_item($df->id, 0);
        $this->assertEquals(false, $gitem);
    }

    /**
     * A grade item should be deleted on deleting a gradable dataform.
     */
    public function test_delete_gradale_instance() {
        $this->setAdminUser();

        // Course.
        $courseid = $this->course->id;

        // Add instance with grade point.
        $params = array(
            'name' => 'Graded Dataform',
            'course' => $courseid,
            'grade' => 100,
        );
        $dataform = $this->getDataGenerator()->create_module('dataform', $params);
        $df = mod_dataform_dataform::instance($dataform->id);
        $df->delete();
        $gitem = $this->fetch_grade_item($dataform->id, 0);
        $this->assertEquals(false, $gitem);
    }

    /**
     * A grade item should be added on updating a non-gradable dataform.
     * A grade item should be updated on updating a gradable dataform.
     */
    public function test_multiple_grade_items() {
        $this->setAdminUser();

        // Course.
        $courseid = $this->course->id;

        // Scales.
        $scale1 = $this->getDataGenerator()->create_scale();
        $scale2 = $this->getDataGenerator()->create_scale();

        // Grade item definitions.
        $gidata = array(
            // Grade item 0 with scale.
            array(
                'name' => 'Gradable Dataform',
                'grade' => 85,
                'gradecalc' => '',
            ),
            // Grade item 1 with scale.
            array(
                'name' => 'Gradable Scaled',
                'grade' => -$scale1->id,
                'gradecalc' => '',
            ),
            // Grade item 2 with grade calculation.
            array(
                'name' => 'Gradable calc',
                'grade' => 5,
                'gradecalc' => '##numentries##',
            ),
            // Grade item 3 with scale calculation.
            array(
                'name' => 'Gradable scale calc',
                'grade' => -$scale2->id,
                'gradecalc' => 'MAX(##:entrystate##)',
            ),
        );

        // Add instance with default grade item.
        $params = array(
            'course' => $courseid,
        );
        $params = array_merge($params, $gidata[0]);
        $dataform = $this->getDataGenerator()->create_module('dataform', $params);

        $df = mod_dataform_dataform::instance($dataform->id);
        $grademan = $df->grade_manager;

        // Add the additional grade items.
        foreach ($gidata as $itemnumber => $itemdata) {
            $itemparams = $grademan->get_grade_item_params_from_data($itemdata);
            $grademan->update_grade_item($itemnumber, $itemparams);
            $grademan->adjust_dataform_settings($itemnumber, $itemdata);
        }

        // Verify the grade items.
        foreach ($gidata as $itemnumber => $itemdata) {
            $gitem = $this->fetch_grade_item($df->id, $itemnumber);
            $this->verify_grade_item($gitem, $itemdata);
        }

        // Modify the main grade item.
        $itemdata = array(
            'name' => 'Gradable 90',
            'grade' => 90,
        );
        $itemparams = $grademan->get_grade_item_params_from_data($itemdata);
        $grademan->update_grade_item(0, $itemparams);
        $grademan->adjust_dataform_settings($itemnumber, $itemparams);
        $gitem = $this->fetch_grade_item($df->id, 0);
        $this->verify_grade_item($gitem, $itemdata);

        // Make the dataform non-graded.
        $df->update(array('grade' => 0));
        foreach ($gidata as $itemnumber => $itemdata) {
            $gitem = $this->fetch_grade_item($df->id, $itemnumber);
            $this->assertEquals(false, $gitem);
        }
    }

    /**
     * Set up function. In this instance we are setting up dataform
     * entries to be used in the unit tests.
     */
    public function test_calculated_grading() {
        global $DB;

        $this->resetAfterTest();

        $this->setAdminUser();

        // Course.
        $courseid = $this->course->id;

        // Dataform.
        $params = array(
            'name' => 'Calculated Grade Dataform',
            'course' => $courseid,
            'grade' => 100,
            'gradeitems' => serialize(array(0 => array('ca' => '##numentries##'))),
        );
        $dataform = $this->getDataGenerator()->create_module('dataform', $params);
        $df = mod_dataform_dataform::instance($dataform->id);

        // Add a view.
        $view = $df->view_manager->add_view('aligned');

        // Get an entry manager.
        $entryman = $view->entry_manager;

        // Fetch the grade item.
        $gitem = $this->fetch_grade_item($df->id, 0);

        // Student 1 grade.
        $grade = $gitem->get_grade($this->student1->id, false);
        $this->assertEquals(null, $grade->finalgrade);

        // Add 5 entries.
        $this->setUser($this->student1);
        $eids = range(-1, -5, -1);
        $data = (object) array('submitbutton_save' => 'Save');
        list(, $eids) = $entryman->process_entries('update', $eids, $data, true);

        // Grade should be 5.
        $grade = $gitem->get_grade($this->student1->id, false);
        $this->assertEquals(5, (int) $grade->finalgrade);

        // Delete 1 entry.
        $entrytodelete = reset($eids);
        list(, $eids) = $entryman->process_entries('delete', $entrytodelete, null, true);

        // Grade should be 4.
        $grade = $gitem->get_grade($this->student1->id, false);
        $this->assertEquals(4, $grade->finalgrade);

        $this->setAdminUser();
    }

    /**
     * Returns the grade item with the specified item number, for the specified Dataform.
     */
    protected function fetch_grade_item($dataformid, $itemnumber = 0) {
        $grademan = new \mod_dataform_grade_manager($dataformid);
        $gradeitems = $grademan->grade_items;
        if (!empty($gradeitems[$itemnumber])) {
            return $gradeitems[$itemnumber];
        }
        return false;
    }

    /**
     *
     */
    protected function verify_grade_item($gitem, $itemdata) {
        $name = $itemdata['name'];
        $grade = $itemdata['grade'];
        $gradeguide = !empty($itemdata['gradeguide']) ? $itemdata['gradeguide'] : null;
        $gradecalc = !empty($itemdata['gradecalc']) ? $itemdata['gradecalc'] : null;

        // Check the name.
        $this->assertEquals($name, $gitem->itemname);

        // Check the grade.
        if ($grade > 0) {
            // Check point grade.
            $this->assertEquals($grade, (int) $gitem->grademax);
        } else if ($grade < 0) {
            // Check scale grade.
            $this->assertEquals(-$grade, $gitem->scaleid);
        }

        // Check grade guide.
        $this->assertEquals($gradeguide, $gitem->gradeguide);

        // Check grade calc.
        $this->assertEquals($gradecalc, $gitem->gradecalc);
    }

}

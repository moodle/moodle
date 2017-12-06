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

defined('MOODLE_INTERNAL') || die();

/**
 * Filtering testcase
 *
 * @package    mod_dataform
 * @copyright  2015 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @category   phpunit
 * @group      mod_dataform
 * @group      mod_dataform_filter
 */
class mod_dataform_filter_testcase extends advanced_testcase {
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
     * Test filter events.
     */
    public function test_filter() {
        global $DB;

        $generator = $this->getDataGenerator();
        $dataformgenerator = $generator->get_plugin_generator('mod_dataform');

        $this->setAdminUser();

        // Add a dataform.
        $dataform = $dataformgenerator->create_instance(array('course' => $this->course));
        $dataformid = $dataform->id;
        $df = \mod_dataform_dataform::instance($dataformid);

        // Add entries.
        $student1entry = array('dataid' => $dataformid, 'userid' => $this->student1->id);
        $student2entry = array('dataid' => $dataformid, 'userid' => $this->student2->id);

        $dataformgenerator->create_entry($student1entry);
        $dataformgenerator->create_entry($student1entry);
        $dataformgenerator->create_entry($student1entry);
        $dataformgenerator->create_entry($student2entry);
        $dataformgenerator->create_entry($student2entry);

        // Get an entry manager for a view.
        $view = $df->view_manager->add_view('aligned');
        $entryman = $view->entry_manager;

        // Default filter (= no filter).
        $instance = $dataformgenerator->create_filter(array(
            'dataid' => $df->id,
        ));
        $filter = new \mod_dataform\pluginbase\dataformfilter($instance);
        $entryman->set_content(array('filter' => $filter));
        $this->assertEquals(5, $entryman->get_count($entryman::COUNT_VIEWABLE));
        $this->assertEquals(5, $entryman->get_count($entryman::COUNT_FILTERED));
        $this->assertEquals(5, $entryman->get_count($entryman::COUNT_DISPLAYED));
        $entries = $entryman->entries;
        $firstentry = reset($entries);
        $this->assertEquals('student1', $firstentry->username);

        // Perpage.
        $instance = $dataformgenerator->create_filter(array(
            'dataid' => $df->id,
            'perpage' => 2,
        ));
        $filter = new \mod_dataform\pluginbase\dataformfilter($instance);
        $entryman->set_content(array('filter' => $filter));
        $this->assertEquals(5, $entryman->get_count($entryman::COUNT_VIEWABLE));
        $this->assertEquals(5, $entryman->get_count($entryman::COUNT_FILTERED));
        $this->assertEquals(2, $entryman->get_count($entryman::COUNT_DISPLAYED));
        $entries = $entryman->entries;
        $firstentry = reset($entries);
        $this->assertEquals('student1', $firstentry->username);

        // Custom sort.
        $instance = $dataformgenerator->create_filter(array(
            'dataid' => $df->id,
            'sortoptions' => 'EAU,username,1',
        ));
        $filter = new \mod_dataform\pluginbase\dataformfilter($instance);
        $entryman->set_content(array('filter' => $filter));
        $this->assertEquals(5, $entryman->get_count($entryman::COUNT_VIEWABLE));
        $this->assertEquals(5, $entryman->get_count($entryman::COUNT_FILTERED));
        $this->assertEquals(5, $entryman->get_count($entryman::COUNT_DISPLAYED));
        $entries = $entryman->entries;
        $firstentry = reset($entries);
        $this->assertEquals('student2', $firstentry->username);


    }

}

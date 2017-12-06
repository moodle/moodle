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
 * @group      mod_dataform_filter_search
 */
class mod_dataform_filter_search_testcase extends advanced_testcase {
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
     * Test is/not empty criterion.
     */
    public function test_is_not_empty() {
        global $DB;

        $generator = $this->getDataGenerator();
        $dataformgenerator = $generator->get_plugin_generator('mod_dataform');

        $this->setAdminUser();

        // Add a dataform.
        $dataform = $dataformgenerator->create_instance(array('course' => $this->course));
        $dataformid = $dataform->id;
        $df = \mod_dataform_dataform::instance($dataformid);

        // Add content fields.
        $fieldtypes = array(
            'text',
            'textarea',
            'select',
            'radiobutton',
            'selectmulti',
            'checkbox',
            'url',
            'number',
            'time',
        );
        $fields = array();
        foreach ($fieldtypes as $type) {
            $fields[$type] = $df->field_manager->add_field($type);
        }

        // Add csv view.
        $importview = $df->view_manager->add_view('csv');

        // Import entries.
        $eaufieldid = dataformfield_entryauthor_entryauthor::INTERNALID;
        $options = array('settings' => array());
        foreach ($fields as $type => $field) {
            $settings = array('name' => $type);
            if (in_array($type, array('select', 'radiobutton', 'selectmulti', 'checkbox'))) {
                $settings['allownew'] = true;
            }
            $options['settings'][$field->id] = array('' => $settings);
        }

        $content1 = array(
            'text' => 'Some single line text.',
            'textarea' => 'First line of multiline text.<br /> Second line of multiline text.',
            'select' => 'SL 1',
            'radiobutton' => 'RB 1',
            'selectmulti' => 'SLM 1',
            'checkbox' => 'CB 1',
            'url' => 'http://substantialmethods.com',
            'number' => '7',
            'time' => '22 July 2015 1:10 PM',
        );

        $content2 = array(
            'text' => '',
            'textarea' => '',
            'select' => '',
            'radiobutton' => '',
            'selectmulti' => '',
            'checkbox' => '',
            'url' => '',
            'number' => '',
            'time' => '',
        );

        $csvdata = array(
            implode(',', array_keys($content1)),
            implode(',', $content1),
            implode(',', $content2),
            implode(',', $content2),
        );

        $data = new stdClass;
        $data->eids = array();
        $data->errors = array();
        $data = $importview->process_csv($data, implode("\n", $csvdata), $options);

        $importresult = $importview->execute_import($data);

        // Get an entry manager for a view.
        $entryman = $importview->entry_manager;

        // Search is empty.
        foreach ($fieldtypes as $type) {
            $instance = $dataformgenerator->create_filter(array(
                'dataid' => $df->id,
                'searchoptions' => "AND,$type,content,,,",
            ));
            $filter = new \mod_dataform\pluginbase\dataformfilter($instance);
            $entryman->set_content(array('filter' => $filter));
            $this->assertEquals(3, $entryman->get_count($entryman::COUNT_VIEWABLE));
            $this->assertEquals(2, $entryman->get_count($entryman::COUNT_FILTERED));
        }

        // Search not empty.
        foreach ($fieldtypes as $type) {
            $instance = $dataformgenerator->create_filter(array(
                'dataid' => $df->id,
                'searchoptions' => "AND,$type,content,NOT,,",
            ));
            $filter = new \mod_dataform\pluginbase\dataformfilter($instance);
            $entryman->set_content(array('filter' => $filter));
            $this->assertEquals(3, $entryman->get_count($entryman::COUNT_VIEWABLE));
            $this->assertEquals(1, $entryman->get_count($entryman::COUNT_FILTERED));
        }

    }

}

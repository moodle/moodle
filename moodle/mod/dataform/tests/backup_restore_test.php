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

global $CFG;
require_once($CFG->libdir . '/completionlib.php');

/**
 * PHPUnit dataform backup restore testcase
 *
 * @package    mod_dataform
 * @category   phpunit
 * @group      mod_dataform
 * @group      mod_dataform_backup
 * @copyright  2014 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_dataform_backup_restore_testcase extends advanced_testcase {

    /**
     * Tests the backup and restore of single activity to same course (duplicate)
     * when it contains fields and views.
     */
    public function test_duplicate() {
        global $DB, $CFG;

        $this->resetAfterTest(true);

        $this->setAdminUser();

        $generator = $this->getDataGenerator();
        $dataformgenerator = $generator->get_plugin_generator('mod_dataform');

        // Create a course.
        $course = $generator->create_course();

        // DATAFORM 1.
        $params = array('course' => $course->id, 'grade' => 100);
        $dataform1 = $dataformgenerator->create_instance($params);
        $df1 = mod_dataform_dataform::instance($dataform1->id);
        // Add fields.
        $fieldtypes = array_keys(core_component::get_plugin_list('dataformfield'));
        $fieldtypescount = count($fieldtypes);
        foreach ($fieldtypes as $type) {
            $df1->field_manager->add_field($type);
        }
        // Add views.
        $viewtypes = array_keys(core_component::get_plugin_list('dataformview'));
        $viewtypescount = count($viewtypes);
        foreach ($viewtypes as $type) {
            $df1->view_manager->add_view($type);
        }
        // Fetch the grade item.
        $params = array(
            'itemtype' => 'mod',
            'itemmodule' => 'dataform',
            'iteminstance' => $dataform1->id,
            'courseid' => $course->id,
            'itemnumber' => 0
        );
        $gradeitem1 = grade_item::fetch($params);

        // Check number of dataforms.
        $this->assertEquals(1, $DB->count_records('dataform'));

        // Check number of fields.
        $this->assertEquals($fieldtypescount, $DB->count_records('dataform_fields'));
        $this->assertEquals($fieldtypescount, $DB->count_records('dataform_fields', array('dataid' => $dataform1->id)));

        // Check number of views.
        $this->assertEquals($viewtypescount, $DB->count_records('dataform_views'));
        $this->assertEquals($viewtypescount, $DB->count_records('dataform_views', array('dataid' => $dataform1->id)));

        // Check number of filters.
        // $this->assertEquals(2, $DB->count_records('dataform_filters'));
        // $this->assertEquals(2, $DB->count_records('dataform_filters', array('dataid' => $dataform1->id)));.

        // DUPLICATE the dataform instance.
        $dataform2 = $dataformgenerator->duplicate_instance($course, $dataform1->cmid);

        // Check number of dataforms.
        $this->assertEquals(2, $DB->count_records('dataform'));

        // Check duplication of fields.
        $this->assertEquals($fieldtypescount * 2, $DB->count_records('dataform_fields'));
        $this->assertEquals($fieldtypescount, $DB->count_records('dataform_fields', array('dataid' => $dataform1->id)));
        $this->assertEquals($fieldtypescount, $DB->count_records('dataform_fields', array('dataid' => $dataform2->id)));

        // Check duplication of views.
        $this->assertEquals($viewtypescount * 2, $DB->count_records('dataform_views'));
        $this->assertEquals($viewtypescount, $DB->count_records('dataform_views', array('dataid' => $dataform1->id)));
        $this->assertEquals($viewtypescount, $DB->count_records('dataform_views', array('dataid' => $dataform2->id)));

        // Check number of filters.
        // $this->assertEquals(4, $DB->count_records('dataform_filters');
        // $this->assertEquals(2, $DB->count_records('dataform_filters', array('dataid' => $dataform1->id));
        // $this->assertEquals(2, $DB->count_records('dataform_filters', array('dataid' => $dataform2->id));.

        // Dataform cleanup.
        $dataformgenerator->delete_all_instances();
    }
}

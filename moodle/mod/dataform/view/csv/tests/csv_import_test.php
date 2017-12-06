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
 * @package    dataformview_csv
 * @category   phpunit
 * @copyright  2014 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

/**
 * PHPUnit dataform import testcase
 *
 * @package    dataformview_csv
 * @category   phpunit
 * @group      mod_dataform
 * @group      dataformview_csv
 * @copyright  2014 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataformview_csv_import_testcase extends advanced_testcase {

    protected $_course;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();

        // Create a course we are going to add a data module to.
        $this->_course = $this->getDataGenerator()->create_course();
    }

    /**
     * Sets up a dataform activity in a course.
     *
     * @return mod_dataform_dataform
     */
    protected function get_a_dataform($dataformid = null) {
        $this->setAdminUser();

        // The generator used to create a data module.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_dataform');

        if (!$dataformid) {
            // Create a dataform instance.
            $data = $generator->create_instance(array('course' => $this->_course));
            $dataformid = $data->id;
        }
        return mod_dataform_dataform::instance($dataformid);
    }

    /**
     * Test 1: Number of imported entries and contents.
     */
    public function test_csv_import() {
        global $DB;

        $df = $this->get_a_dataform();

        // Add fields.
        $text = $df->field_manager->add_field('text');
        $textarea = $df->field_manager->add_field('textarea');
        $checkbox = $df->field_manager->add_field('checkbox');
        $df->field_manager->add_field('select');
        $df->field_manager->add_field('selectmulti');
        $df->field_manager->add_field('radiobutton');
        $df->field_manager->add_field('url');
        $df->field_manager->add_field('file');
        $df->field_manager->add_field('picture');
        $df->field_manager->add_field('number');
        $df->field_manager->add_field('time');
        $df->field_manager->add_field('entrystate');
        $this->assertEquals(12, $DB->count_records('dataform_fields'));

        // Add csv view.
        $importview = $df->view_manager->add_view('csv');
        $this->assertEquals(1, $DB->count_records('dataform_views'));
        $importview->param2 = "EAU:id\n". $importview->param2;
        $importview->update($importview->data);

        $this->assertEquals(0, $DB->count_records('dataform_entries'));

        // Import entries.
        $eaufieldid = dataformfield_entryauthor_entryauthor::INTERNALID;
        $options = array(
            'settings' => array(
                $eaufieldid => array('id' => array('name' => 'Author')),
                $text->id => array('' => array('name' => 'text')),
                $textarea->id => array('' => array('name' => 'textarea')),
            ),
        );
        $csvdata = array(
            'Author,text,textarea',
            '2,Hello,Oh my god',
            '2,World,It wasn\'t me',
        );

        $data = new stdClass;
        $data->eids = array();
        $data->errors = array();
        $data = $importview->process_csv($data, implode("\n", $csvdata), $options);

        $importresult = $importview->execute_import($data);

        $this->assertEquals(2, $DB->count_records('dataform_entries'));
        $this->assertEquals(4, $DB->count_records('dataform_contents'));

        // Text content.
        $contents = array_values($DB->get_records_menu('dataform_contents', array('fieldid' => $text->id), 'id', 'id,content'));
        $this->assertEquals('Hello', $contents[0]);
        $this->assertEquals('World', $contents[1]);

        // Textarea content.
        $contents = array_values($DB->get_records_menu('dataform_contents', array('fieldid' => $textarea->id), 'id', 'id,content'));
        $this->assertEquals('Oh my god', $contents[0]);
        $this->assertEquals('It wasn\'t me', $contents[1]);

        $df->delete();
        $this->assertEquals(0, $DB->count_records('dataform_entries'));
    }
}

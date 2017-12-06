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
 * @package    dataformfield_textarea
 * @category   phpunit
 * @copyright  2016 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') or die;

/**
 * PHPUnit dataform import testcase
 *
 * @package    dataformfield_textarea
 * @category   phpunit
 * @group      mod_dataform
 * @group      dataformfield_textarea
 * @copyright  2016 Itamar Tzadok {@link http://substantialmethods.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class dataformfield_textarea_import_testcase extends advanced_testcase {

    protected $_course;
    protected $_dataform;
    protected $_importview;

    /**
     * Test set up.
     *
     * This is executed before running any test in this file.
     */
    public function setUp() {
        $this->resetAfterTest();

        $this->setAdminUser();

        // Create a course we are going to add a data module to.
        $this->_course = $this->getDataGenerator()->create_course();

        // The generator used to create a data module.

        // Create a dataform instance.
        $generator = $this->getDataGenerator()->get_plugin_generator('mod_dataform');
        $data = $generator->create_instance(array('course' => $this->_course));
        $this->_dataform = \mod_dataform_dataform::instance($data->id);
    }

    /**
     * Import test.
     */
    public function test_import() {
        $df = $this->_dataform;

        // Add fields.
        $field = $df->field_manager->add_field('textarea');

        // Add csv view.
        $importview = $df->view_manager->add_view('csv');

        // Test default settings with plain text.
        $this->case1($field, $importview);

        // Test default settings with new line replacement.
        $this->case2($field, $importview);
    }

    /**
     * Test case 1: default settings, simple text.
     */
    protected function case1($field, $importview) {
        global $DB;

        // Import settings.
        $options = array(
            'settings' => array(
                $field->id => array('' => array('name' => 'textarea')),
            ),
        );

        // Import data.
        $csvdata = array(
            'textarea',
            'Oh my god',
        );

        $data = new \stdClass;
        $data->eids = array();
        $data->errors = array();
        $data = $importview->process_csv($data, implode("\n", $csvdata), $options);

        $importresult = $importview->execute_import($data);

        // Verify field content.
        $contents = array_values($DB->get_records_menu('dataform_contents', array('fieldid' => $field->id), 'id', 'id,content'));
        $this->assertEquals('Oh my god', $contents[0]);

        $this->_dataform->reset_user_data();
    }

    /**
     * Test case 2: default settings, multi-line content.
     */
    protected function case2($field, $importview) {
        global $DB;

        // Import settings.
        $options = array(
            'settings' => array(
                $field->id => array('' => array(
                    'name' => 'textarea',
                    'newline' => '#nl#',
                )),
            ),
        );

        // Import data.
        $csvdata = array(
            'textarea',
            'The#nl#Big#nl#Bang#nl#Theory',
        );

        $data = new \stdClass;
        $data->eids = array();
        $data->errors = array();
        $data = $importview->process_csv($data, implode("\n", $csvdata), $options);

        $importresult = $importview->execute_import($data);

        // Verify field content.
        $contents = array_values($DB->get_records_menu('dataform_contents', array('fieldid' => $field->id), 'id', 'id,content'));
        $expected = "The\nBig\nBang\nTheory";
        $this->assertEquals($expected, $contents[0]);

        $this->_dataform->reset_user_data();
    }
}

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
 * Unit tests for importing csv files.
 *
 * @package    mod_data
 * @category   test
 * @copyright  2019 Tobias Reischmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/data/lib.php');
require_once($CFG->dirroot . '/lib/datalib.php');
require_once($CFG->dirroot . '/lib/csvlib.class.php');
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
require_once($CFG->dirroot . '/mod/data/tests/generator/lib.php');

/**
 * Unit tests for import.php.
 *
 * @package    mod_data
 * @copyright  2019 Tobias Reischmann
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_data_import_test extends advanced_testcase {

    /** @var object $cm Course module of data instance. */
    private $cm;

    /** @var object $data Data instance. */
    private $data;

    /** @var mod_data_generator $generator */
    private $generator;

    /** @var object $student Student object */
    private $student;

    /** @var object $teacher Teacher object */
    private $teacher;

    /**
     * Set up function. In this instance we are setting up database
     * records to be used in the unit tests.
     */
    protected function setUp() {
        parent::setUp();

        $this->resetAfterTest(true);

        $this->generator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $course = $this->getDataGenerator()->create_course();
        $this->teacher = $this->getDataGenerator()->create_and_enrol($course, 'teacher');
        $this->setUser($this->teacher);
        $this->student = $this->getDataGenerator()->create_and_enrol($course, 'student', array('username' => 'student'));

        $this->data = $this->generator->create_instance(array('course' => $course->id));
        $this->cm = get_coursemodule_from_instance('data', $this->data->id);

        // Add fields.
        $fieldrecord = new StdClass();
        $fieldrecord->name = 'ID'; // Identifier of the records for testing.
        $fieldrecord->type = 'number';
        $this->generator->create_field($fieldrecord, $this->data);

        $fieldrecord->name = 'Param2';
        $fieldrecord->type = 'text';
        $this->generator->create_field($fieldrecord, $this->data);
    }

    /**
     * Test uploading entries for a data instance without userdata.
     * @throws dml_exception
     */
    public function test_import() {
        $filecontent = file_get_contents(__DIR__ . '/fixtures/test_data_import.csv');
        ob_start();
        data_import_csv($this->cm, $this->data, $filecontent, 'UTF-8', 'comma');
        ob_end_clean();

        // No userdata is present in the file: Fallback is to assign the uploading user as author.
        $expecteduserids = array();
        $expecteduserids[1] = $this->teacher->id;
        $expecteduserids[2] = $this->teacher->id;

        $records = $this->get_data_records($this->data->id);
        $this->assertCount(2, $records);
        foreach ($records as $record) {
            $identifier = $record->items['ID']->content;
            $this->assertEquals($expecteduserids[$identifier], $record->userid);
        }
    }

    /**
     * Test uploading entries for a data instance with userdata.
     *
     * At least one entry has an identifiable user, which is assigned as author.
     * @throws dml_exception
     */
    public function test_import_with_userdata() {
        $filecontent = file_get_contents(__DIR__ . '/fixtures/test_data_import_with_userdata.csv');
        ob_start();
        data_import_csv($this->cm, $this->data, $filecontent, 'UTF-8', 'comma');
        ob_end_clean();

        $expecteduserids = array();
        $expecteduserids[1] = $this->student->id; // User student exists and is assigned as author.
        $expecteduserids[2] = $this->teacher->id; // User student2 does not exist. Fallback is the uploading user.

        $records = $this->get_data_records($this->data->id);
        $this->assertCount(2, $records);
        foreach ($records as $record) {
            $identifier = $record->items['ID']->content;
            $this->assertEquals($expecteduserids[$identifier], $record->userid);
        }
    }

    /**
     * Test uploading entries for a data instance with userdata and a defined field 'Username'.
     *
     * This should test the corner case, in which a user has defined a data fields, which has the same name
     * as the current lang string for username. In that case, the first Username entry is used for the field.
     * The second one is used to identify the author.
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_import_with_field_username() {

        // Add username field.
        $fieldrecord = new StdClass();
        $fieldrecord->name = 'Username';
        $fieldrecord->type = 'text';
        $this->generator->create_field($fieldrecord, $this->data);

        $filecontent = file_get_contents(__DIR__ . '/fixtures/test_data_import_with_field_username.csv');
        ob_start();
        data_import_csv($this->cm, $this->data, $filecontent, 'UTF-8', 'comma');
        ob_end_clean();

        $expecteduserids = array();
        $expecteduserids[1] = $this->student->id; // User student exists and is assigned as author.
        $expecteduserids[2] = $this->teacher->id; // User student2 does not exist. Fallback is the uploading user.
        $expecteduserids[3] = $this->student->id; // User student exists and is assigned as author.

        $expectedcontent = array();
        $expectedcontent[1] = array(
            'Username' => 'otherusername1',
            'Param2' => 'My first entry',
        );
        $expectedcontent[2] = array(
            'Username' => 'otherusername2',
            'Param2' => 'My second entry',
        );
        $expectedcontent[3] = array(
            'Username' => 'otherusername3',
            'Param2' => 'My third entry',
        );

        $records = $this->get_data_records($this->data->id);
        $this->assertCount(3, $records);
        foreach ($records as $record) {
            $identifier = $record->items['ID']->content;
            $this->assertEquals($expecteduserids[$identifier], $record->userid);

            foreach ($expectedcontent[$identifier] as $field => $value) {
                $this->assertEquals($value, $record->items[$field]->content,
                    "The value of field \"$field\" for the record at position \"$identifier\" ".
                    "which is \"{$record->items[$field]->content}\" does not match the expected value \"$value\".");
            }
        }
    }

    /**
     * Test uploading entries for a data instance with a field 'Username' but only one occurrence in the csv file.
     *
     * This should test the corner case, in which a user has defined a data fields, which has the same name
     * as the current lang string for username. In that case, the only Username entry is used for the field.
     * The author should not be set.
     * @throws coding_exception
     * @throws dml_exception
     */
    public function test_import_with_field_username_without_userdata() {

        // Add username field.
        $fieldrecord = new StdClass();
        $fieldrecord->name = 'Username';
        $fieldrecord->type = 'text';
        $this->generator->create_field($fieldrecord, $this->data);

        $filecontent = file_get_contents(__DIR__ . '/fixtures/test_data_import_with_userdata.csv');
        ob_start();
        data_import_csv($this->cm, $this->data, $filecontent, 'UTF-8', 'comma');
        ob_end_clean();

        // No userdata is present in the file: Fallback is to assign the uploading user as author.
        $expecteduserids = array();
        $expecteduserids[1] = $this->teacher->id;
        $expecteduserids[2] = $this->teacher->id;

        $expectedcontent = array();
        $expectedcontent[1] = array(
            'Username' => 'student',
            'Param2' => 'My first entry',
        );
        $expectedcontent[2] = array(
            'Username' => 'student2',
            'Param2' => 'My second entry',
        );

        $records = $this->get_data_records($this->data->id);
        $this->assertCount(2, $records);
        foreach ($records as $record) {
            $identifier = $record->items['ID']->content;
            $this->assertEquals($expecteduserids[$identifier], $record->userid);

            foreach ($expectedcontent[$identifier] as $field => $value) {
                $this->assertEquals($value, $record->items[$field]->content,
                    "The value of field \"$field\" for the record at position \"$identifier\" ".
                    "which is \"{$record->items[$field]->content}\" does not match the expected value \"$value\".");
            }
        }
    }

    /**
     * Returns the records of the data instance.
     *
     * Each records has an item entry, which contains all fields associated with this item.
     * Each fields has the parameters name, type and content.
     * @param int $dataid Id of the data instance.
     * @return array The records of the data instance.
     * @throws dml_exception
     *
     */
    private function get_data_records($dataid) {
        global $DB;
        $records = $DB->get_records('data_records', ['dataid' => $dataid]);
        foreach ($records as $record) {
            $sql = 'SELECT f.name, f.type, con.content FROM
                {data_content} con JOIN {data_fields} f ON con.fieldid = f.id
                WHERE con.recordid = :recordid';
            $items = $DB->get_records_sql($sql, array('recordid' => $record->id));
            $record->items = $items;
        }
        return $records;
    }
}

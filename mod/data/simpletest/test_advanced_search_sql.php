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
 * Unit tests for data_get_all_recordsids(), data_get_advance_search_ids(), data_get_record_ids(),
 * and data_get_advanced_search_sql()
 *
 * @package    mod_data
 * @copyright  2012 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');
}


require_once($CFG->dirroot . '/mod/data/lib.php');
require_once($CFG->dirroot . '/lib/csvlib.class.php');

/**
 * Unit tests for {@see data_get_all_recordids()}.
 *                {@see data_get_advanced_search_ids()}
 *                {@see data_get_record_ids()}
 *                {@see data_get_advanced_search_sql()}
 *
 * @package    mod_data
 * @copyright  2012 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class data_advanced_search_sql_test extends UnitTestCaseUsingDatabase {
    /**
     * @var stdObject $recorddata An object that holds information from the table data.
     */
    public $recorddata = null;
    /**
     * @var int $recordcontentid The content ID.
     */
    public $recordcontentid = null;
    /**
     * @var int $recordrecordid The record ID.
     */
    public $recordrecordid = null;
    /**
     * @var int $recordfieldid The field ID.
     */
    public $recordfieldid = null;
    /**
     * @var array $recordsearcharray An array of stdClass which contains search criteria.
     */
    public $recordsearcharray = null;

    // CONSTANTS

    /**
     * @var int $datarecordcount   The number of records in the database.
     */
    public $datarecordcount = 100;

    /**
     * @var array $datarecordset   Expected record IDs.
     */
    public $datarecordset = array('0' => '6');

    /**
     * @var array $finalrecord   Final record for comparison with test four.
     */
    public $finalrecord = array();

    /**
     * Set up function. In this instance we are setting up database
     * records to be used in the unit tests.
     * @todo MDL-32271 use a class for creating safe core moodle tables. This isn't
     * possible at the moment.
     */

    public function setUp() {
        global $DB, $CFG;

        // Set up test database and appropriate tables.
        parent::setUp();
        $this->create_test_tables(array('data', 'data_fields', 'data_records', 'data_content'), 'mod/data');
        $this->create_test_tables(array('user', 'modules', 'course_modules','context'), 'lib');
        $this->switch_to_test_db();

        // Set up data for the test database.
        $tablename = array('0' => 'user',
                           '1' => 'data_fields',
                           '2' => 'data_records',
                           '3' => 'data_content',
                           '4' => 'modules',
                           '5' => 'course_modules',
                           '6' => 'context');

        for ($i = 0; $i < 7; $i++) {
            $filename = $CFG->dirroot . '/mod/data/simpletest/test_' . $tablename[$i] . '.csv';
            if (file_exists($filename)) {
                $file = file_get_contents($filename);
            }
            $this->insert_data_from_csv($file, $tablename[$i]);
        }

        // Set up a data record.
        $datarecord = new stdClass();
        $datarecord->course = '99999';
        $datarecord->name = 'test database';
        $datarecord->intro = 'Test Database for unit testing';
        $datarecord->introformat = '1';
        $DB->insert_record('data', $datarecord, false);
        $data = $DB->get_record('data', array('id'=> '1'));
        $this->recorddata = $data;

        // Create the search array which contains our advanced search criteria.

        $fieldinfo = array('0' => new stdClass(),
                           '1' => new stdClass(),
                           '2' => new stdClass(),
                           '3' => new stdClass(),
                           '4' => new stdClass());
        $fieldinfo['0']->id = 1;
        $fieldinfo['0']->data = '3.721,46.6126';
        $fieldinfo['1']->id = 2;
        $fieldinfo['1']->data = 'Hahn Premium';
        $fieldinfo['2']->id = 5;
        $fieldinfo['2']->data = 'Female';
        $fieldinfo['3']->id = 7;
        $fieldinfo['3']->data = 'kel';
        $fieldinfo['4']->id = 9;
        $fieldinfo['4']->data = 'VIC';

        foreach($fieldinfo as $field) {
            $searchfield = data_get_field_from_id($field->id, $data);
            if ($field->id == 2) {
                $searchfield->field->param1 = 'Hahn Premium';
                $val = array();
                $val['selected'] = array('0' => 'Hahn Premium');
                $val['allrequired'] = 0;
            } else {
                $val = $field->data;
            }
            $search_array[$field->id] = new stdClass();
            list($search_array[$field->id]->sql, $search_array[$field->id]->params) = $searchfield->generate_sql('c' . $field->id, $val);
        }

        $this->recordsearcharray = $search_array;

        // Setting up the comparison stdClass for the last test.
        $this->finalrecord[6] = new stdClass();
        $this->finalrecord[6]->id = 6;
        $this->finalrecord[6]->approved = 1;
        $this->finalrecord[6]->timecreated = 1234567891;
        $this->finalrecord[6]->timemodified = 1234567892;
        $this->finalrecord[6]->userid = 6;
        $this->finalrecord[6]->firstname = 'Benedict';
        $this->finalrecord[6]->lastname = 'Horn';
    }

    /**
     * Tear Down function. Here we remove all the database entries that we created
     * for testing the unit tests.
     */
    public function tearDown() {
        $this->revert_to_real_db();
        parent::tearDown();
    }

    /**
     * Test 1: The function data_get_all_recordids.
     *
     * Test 2: This tests the data_get_advance_search_ids() function. The function takes a set
     * of all the record IDs in the database and then with the search details ($this->recordsearcharray)
     * returns a comma seperated string of record IDs that match the search criteria.
     *
     * Test 3: This function tests data_get_recordids(). This is the function that is nested in the last
     * function (@see data_get_advance_search_ids). This function takes a couple of
     * extra parameters. $alias is the field alias used in the sql query and $commaid
     * is a comma seperated string of record IDs.
     *
     * Test 4: data_get_advanced_search_sql provides an array which contains an sql string to be used for displaying records
     * to the user when they use the advanced search criteria and the parameters that go with the sql statement. This test
     * takes that information and does a search on the database, returning a record.
     */
    function test_advanced_search_sql_section() {
        global $DB;

        // Test 1
        $recordids = data_get_all_recordids($this->recorddata->id);
        $this->assertEqual(count($recordids), $this->datarecordcount);

        // Test 2
        $key = array_keys($this->recordsearcharray);
        $alias = $key[0];
        $newrecordids = data_get_recordids($alias, $this->recordsearcharray, $this->recorddata->id, $recordids);
        $this->assertEqual($this->datarecordset, $newrecordids);

        // Test 3
        $newrecordids = data_get_advance_search_ids($recordids, $this->recordsearcharray, $this->recorddata->id);
        $this->assertEqual($this->datarecordset, $newrecordids);

        // Test 4
        $sortorder = 'ORDER BY r.timecreated ASC , r.id ASC';
        $html = data_get_advanced_search_sql('0', $this->recorddata, $newrecordids, '', $sortorder);
        $allparams = array_merge($html['params'], array('dataid' => $this->recorddata->id));
        $records = $DB->get_records_sql($html['sql'], $allparams);
        $this->assertEqual($records, $this->finalrecord);
    }

    /**
     * Inserts data from a csv file into the data module table specified.
     *
     * @param string $file comma seperated value file
     * @param string $tablename name of the table for the data to be inserted into.
     */
    function insert_data_from_csv($file, $tablename) {
        global $DB;
        $iid = csv_import_reader::get_new_iid('moddata');
        $csvdata = new csv_import_reader($iid, 'moddata');
        $fielddata = $csvdata->load_csv_content($file, 'utf8', 'comma');
        $columns = $csvdata->get_columns();
        $columncount = count($columns);
        $csvdata->init();
        $fieldinfo = array();
        for ($j = 0; $j < $fielddata; $j++) {
            $thing = $csvdata->next();
            $fieldinfo[$j] = new stdClass();
            for ($i = 0; $i < $columncount; $i++) {
                $fieldinfo[$j]->$columns[$i] = $thing[$i];
            }
            $DB->insert_record($tablename, $fieldinfo[$j], false);
        }
    }
}

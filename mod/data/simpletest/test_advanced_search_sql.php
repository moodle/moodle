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
     * @var array $recorddataid An array of data IDs.
     */
    public $recorddataid = null;
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
    /**
     * @var stdClass $recorddata A stdClass that contains the data ID
     */
    public $recorddata = null;

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
     */

    public function setUp() {
        global $DB, $CFG;

        // Set up test database and appropriate tables.
        parent::setUp();
        $this->create_test_tables(array('data', 'data_fields', 'data_records', 'data_content'), 'mod/data');
        $this->create_test_table('user', 'lib');
        $this->switch_to_test_db();

        // Set up data for the test database.
        $tablename = array('0' => 'user',
                           '1' => 'data_fields',
                           '2' => 'data_records',
                           '3' => 'data_content');

        for ($i = 0; $i < 4; $i++) {
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
        $dataid = $DB->get_field('data', 'id', array('course' => '99999'));
        $this->recorddataid = $dataid;

        // Create the search array which contains our advanced search criteria.

        // Latitude and Longitude
        $search_array = array();
        $search_array['1'] = new stdClass();
        $search_array['1']->params = array();
        $search_array['1']->params['df_latlong1_1'] = '3.721';
        $search_array['1']->params['df_latlong2_1'] = '46.6126';
        $search_array['1']->sql = '(c1.fieldid = 1 AND c1.content = :df_latlong1_1 AND c1.content1 = :df_latlong2_1) ';
        $search_array['1']->data = '3.721,46.6126';

        // Mulitmenu
        $search_array['2'] = new stdClass();
        $search_array['2']->params = array();
        $search_array['2']->params['df_multimenu_1_1a'] = 'Hahn Premium';
        $search_array['2']->params['df_multimenu_1_1b'] = 'Hahn Premium##%';
        $search_array['2']->params['df_multimenu_1_1c'] = '%##Hahn Premium';
        $search_array['2']->params['df_multimenu_1_1d'] = '%##Hahn Premium##%';
        $search_array['2']->sql = '((c2.fieldid = 2 AND (c2.content = :df_multimenu_1_1a
                                                        OR c2.content LIKE :df_multimenu_1_1b
                                                        OR c2.content LIKE :df_multimenu_1_1c
                                                        OR c2.content LIKE :df_multimenu_1_1d)))';
        $search_array['2']->data = array();
        $search_array['2']->data['selected'] = array();
        $search_array['2']->data['selected']['0'] = 'Hahn Premium';
        $search_array['2']->data['allrequired'] = '0';

        // Radiobutton
        $search_array['5'] = new stdClass();
        $search_array['5']->params = array();
        $search_array['5']->params['df_radiobutton_1'] = 'Female';
        $search_array['5']->sql = '(c5.fieldid = 5 AND c5.content = :df_radiobutton_1)';
        $search_array['5']->data = 'Female';

        // Textbox
        $search_array['7'] = new stdClass();
        $search_array['7']->params = array();
        $search_array['7']->params['df_text_1'] = '%kel%';
        $search_array['7']->sql = ' (c7.fieldid = 7 AND LOWER(c7.content) LIKE LOWER(:df_text_1) COLLATE utf8_bin ESCAPE \'\\\\\') ';
        $search_array['7']->data = 'kel';

        // Menu
        $search_array['9'] = new stdClass();
        $search_array['9']->params = array();
        $search_array['9']->params['df_menu_1'] = 'VIC';
        $search_array['9']->sql = '(c9.fieldid = 9 AND c9.content = :df_menu_1)';
        $search_array['9']->data = 'VIC';
        $this->recordsearcharray = $search_array;

        // Normally data_get_advanced_search_sql takes a data module variable
        // which contains a large amount of information, but all that we
        // need is the data ID in a certain format.
        $this->recorddata = new stdClass();
        $this->recorddata->id = $this->recorddataid;

        // Setting up the comparison stdClass for the last test.
        $this->finalrecord[6] = new stdClass();
        $this->finalrecord[6]->id = 6;
        $this->finalrecord[6]->approved = 1;
        $this->finalrecord[6]->timecreated = 1234567891;
        $this->finalrecord[6]->timemodified = 1234567892;
        $this->finalrecord[6]->userid = 6;
        $this->finalrecord[6]->firstname = 'Benedict';
        $this->finalrecord[6]->lastname = 'Horn';
        $this->finalrecord[6]->_order = 3.721;
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
        $recordids = data_get_all_recordids($this->recorddataid);
        $this->assertEqual(count($recordids), $this->datarecordcount);

        // Test 2
        $key = array_keys($this->recordsearcharray);
        $alias = $key[0];
        $newrecordids = data_get_recordids($alias, $this->recordsearcharray, $this->recorddataid, $recordids);
        $this->assertEqual($this->datarecordset, $newrecordids);

        // Test 3
        $newrecordids = data_get_advance_search_ids($recordids, $this->recordsearcharray, $this->recorddataid);
        $this->assertEqual($this->datarecordset, $newrecordids);

        // Test 4
        $sortorder = 'ORDER BY _order ASC , r.id ASC';
        $html = data_get_advanced_search_sql('0', $this->recorddata, $newrecordids, '', $sortorder);
        $allparams = array_merge($html['params'], array('dataid' => $this->recorddataid));
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
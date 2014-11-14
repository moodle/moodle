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
 * @category   phpunit
 * @copyright  2012 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
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
class data_advanced_search_sql_test extends advanced_testcase {
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
     * @var int $groupdatarecordcount  The number of records in the database in groups 0 and 1.
     */
    public $groupdatarecordcount = 75;

    /**
     * @var array $datarecordset   Expected record IDs.
     */
    public $datarecordset = array('0' => '6');

    /**
     * @var array $finalrecord   Final record for comparison with test four.
     */
    public $finalrecord = array();

    /**
     * @var int $approvedatarecordcount  The number of approved records in the database.
     */
    public $approvedatarecordcount = 89;

    /**
     * Set up function. In this instance we are setting up database
     * records to be used in the unit tests.
     */
    protected function setUp() {
        global $DB, $CFG;
        parent::setUp();

        $this->resetAfterTest(true);


        // we already have 2 users, we need 98 more - let's ignore the fact that guest can not post anywhere
        // We reset the user sequence here to ensure we get the expected numbers.
        // TODO: Invent a better way for managing data file input against database sequence id's.
        $DB->get_manager()->reset_sequence('user');
        for($i=3;$i<=100;$i++) {
            $this->getDataGenerator()->create_user();
        }

        // create database module - there should be more of these I guess
        $course = $this->getDataGenerator()->create_course();
        $data = $this->getDataGenerator()->create_module('data', array('course'=>$course->id));
        $this->recorddata = $data;

        // Set up data for the test database.
        $files = array(
            'data_fields'  => __DIR__.'/fixtures/test_data_fields.csv',
            'data_records' => __DIR__.'/fixtures/test_data_records.csv',
            'data_content' => __DIR__.'/fixtures/test_data_content.csv',
        );
        $this->loadDataSet($this->createCsvDataSet($files));
        // Set dataid to the correct value now the data has been inserted by csv file.
        $DB->execute('UPDATE {data_fields} SET dataid = ?', array($data->id));
        $DB->execute('UPDATE {data_records} SET dataid = ?', array($data->id));

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
        $user = $DB->get_record('user', array('id'=>6));
        $this->finalrecord[6] = new stdClass();
        $this->finalrecord[6]->id = 6;
        $this->finalrecord[6]->approved = 1;
        $this->finalrecord[6]->timecreated = 1234567891;
        $this->finalrecord[6]->timemodified = 1234567892;
        $this->finalrecord[6]->userid = 6;
        $this->finalrecord[6]->firstname = $user->firstname;
        $this->finalrecord[6]->lastname = $user->lastname;
        $this->finalrecord[6]->firstnamephonetic = $user->firstnamephonetic;
        $this->finalrecord[6]->lastnamephonetic = $user->lastnamephonetic;
        $this->finalrecord[6]->middlename = $user->middlename;
        $this->finalrecord[6]->alternatename = $user->alternatename;
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
     *
     * Test 5: Returning to data_get_all_recordids(). Here we are ensuring that the total amount of record ids is reduced to
     * match the group conditions that are provided. There are 25 entries which relate to group 2. They are removed
     * from the total so we should only have 75 records total.
     *
     * Test 6: data_get_all_recordids() again. This time we are testing approved database records. We only want to
     * display the records that have been approved. In this record set we have 89 approved records.
     */
    function test_advanced_search_sql_section() {
        global $DB;

        // Test 1
        $recordids = data_get_all_recordids($this->recorddata->id);
        $this->assertEquals(count($recordids), $this->datarecordcount);

        // Test 2
        $key = array_keys($this->recordsearcharray);
        $alias = $key[0];
        $newrecordids = data_get_recordids($alias, $this->recordsearcharray, $this->recorddata->id, $recordids);
        $this->assertEquals($this->datarecordset, $newrecordids);

        // Test 3
        $newrecordids = data_get_advance_search_ids($recordids, $this->recordsearcharray, $this->recorddata->id);
        $this->assertEquals($this->datarecordset, $newrecordids);

        // Test 4
        $sortorder = 'ORDER BY r.timecreated ASC , r.id ASC';
        $html = data_get_advanced_search_sql('0', $this->recorddata, $newrecordids, '', $sortorder);
        $allparams = array_merge($html['params'], array('dataid' => $this->recorddata->id));
        $records = $DB->get_records_sql($html['sql'], $allparams);
        $this->assertEquals($records, $this->finalrecord);

        // Test 5
        $groupsql = " AND (r.groupid = :currentgroup OR r.groupid = 0)";
        $params = array('currentgroup' => 1);
        $recordids = data_get_all_recordids($this->recorddata->id, $groupsql, $params);
        $this->assertEquals($this->groupdatarecordcount, count($recordids));

        // Test 6
        $approvesql = ' AND r.approved=1 ';
        $recordids = data_get_all_recordids($this->recorddata->id, $approvesql, $params);
        $this->assertEquals($this->approvedatarecordcount, count($recordids));
    }
}

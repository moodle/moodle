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

namespace mod_data\search;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/data/lib.php');
require_once($CFG->dirroot . '/lib/datalib.php');
require_once($CFG->dirroot . '/lib/csvlib.class.php');
require_once($CFG->dirroot . '/search/tests/fixtures/testable_core_search.php');
require_once($CFG->dirroot . '/mod/data/tests/generator/lib.php');

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
class search_test extends \advanced_testcase {
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
     * @var string Area id
     */
    protected $databaseentryareaid = null;

    /**
     * Set up function. In this instance we are setting up database
     * records to be used in the unit tests.
     */
    protected function setUp(): void {
        global $DB, $CFG;
        parent::setUp();

        $this->resetAfterTest(true);

        set_config('enableglobalsearch', true);

        $this->databaseentryareaid = \core_search\manager::generate_areaid('mod_data', 'entry');

        // Set \core_search::instance to the mock_search_engine as we don't require the search engine to be working to test this.
        $search = \testable_core_search::instance();

    }

    /**
     * Test 1: The function data_get_all_recordids.
     *
     * Test 2: This tests the data_get_advance_search_ids() function. The function takes a set
     * of all the record IDs in the database and then with the search details ($this->recordsearcharray)
     * returns a comma seperated string of record IDs that match the search criteria.
     *
     * Test 3: This function tests data_get_recordids(). This is the function that is nested in the last
     * function (see data_get_advance_search_ids). This function takes a couple of
     * extra parameters. $alias is the field alias used in the sql query and $commaid
     * is a comma seperated string of record IDs.
     *
     * Test 3.1: This tests that if no recordids are provided (In a situation where a search is done on an empty database)
     * That an empty array is returned.
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
    public function test_advanced_search_sql_section() {
        global $DB;

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
                'data_fields'  => __DIR__.'/../fixtures/test_data_fields.csv',
                'data_records' => __DIR__.'/../fixtures/test_data_records.csv',
                'data_content' => __DIR__.'/../fixtures/test_data_content.csv',
        );
        $this->dataset_from_files($files)->to_database();
        // Set dataid to the correct value now the data has been inserted by csv file.
        $DB->execute('UPDATE {data_fields} SET dataid = ?', array($data->id));
        $DB->execute('UPDATE {data_records} SET dataid = ?', array($data->id));

        // Create the search array which contains our advanced search criteria.
        $fieldinfo = array('0' => new \stdClass(),
                '1' => new \stdClass(),
                '2' => new \stdClass(),
                '3' => new \stdClass(),
                '4' => new \stdClass());
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
            $search_array[$field->id] = new \stdClass();
            list($search_array[$field->id]->sql, $search_array[$field->id]->params) = $searchfield->generate_sql('c' . $field->id, $val);
        }

        $this->recordsearcharray = $search_array;

        // Setting up the comparison stdClass for the last test.
        $user = $DB->get_record('user', array('id'=>6));
        $this->finalrecord[6] = new \stdClass();
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
        $this->finalrecord[6]->picture = $user->picture;
        $this->finalrecord[6]->imagealt = $user->imagealt;
        $this->finalrecord[6]->email = $user->email;

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

        // Test 3.1
        $resultrecordids = data_get_advance_search_ids(array(), $this->recordsearcharray, $this->recorddata->id);
        $this->assertEmpty($resultrecordids);

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

    public function test_advanced_search_tags() {
        $this->resetAfterTest();
        $this->setAdminUser();

        // Setup test data.
        $datagenerator = $this->getDataGenerator()->get_plugin_generator('mod_data');
        $course1 = $this->getDataGenerator()->create_course();

        $fieldrecord = new \stdClass();
        $fieldrecord->name = 'field-1';
        $fieldrecord->type = 'text';
        $fieldrecord->titlefield = true;

        $data1 = $this->getDataGenerator()->create_module('data', array('course' => $course1->id, 'approval' => true));
        $field1 = $datagenerator->create_field($fieldrecord, $data1);

        $record11 = $datagenerator->create_entry($data1, [$field1->field->id => 'value11'], 0, ['Cats', 'Dogs']);
        $record12 = $datagenerator->create_entry($data1, [$field1->field->id => 'value12'], 0, ['Cats', 'mice']);
        $record13 = $datagenerator->create_entry($data1, [$field1->field->id => 'value13'], 0, ['Bats']);

        $searcharray = [];
        $searcharray[DATA_TAGS] = new \stdClass();
        $searcharray[DATA_TAGS]->params = [];
        $searcharray[DATA_TAGS]->rawtagnames = ['Cats'];
        $searcharray[DATA_TAGS]->sql = '';

        $recordids = data_get_all_recordids($data1->id);
        $newrecordids = data_get_advance_search_ids($recordids, $searcharray, $data1->id);

        $this->assertContainsEquals($record11, $newrecordids);
        $this->assertContainsEquals($record12, $newrecordids);
        $this->assertNotContainsEquals($record13, $newrecordids);
    }

    /**
     * Indexing database entries contents.
     *
     * @return void
     */
    public function test_data_entries_indexing() {
        global $DB;

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->databaseentryareaid);
        $this->assertInstanceOf('\mod_data\search\entry', $searcharea);

        $user1 = self::getDataGenerator()->create_user();

        $course1 = self::getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'student');

        $record = new \stdClass();
        $record->course = $course1->id;

        $this->setUser($user1);

        // Available for both student and teacher.
        $data1 = $this->getDataGenerator()->create_module('data', $record);

        // Excluding LatLong and Picture as we aren't indexing LatLong and Picture fields any way
        // ...and they're complex and not of any use to consider for this test.
        // Excluding File as we are indexing files seperately and its complex to implement.
        $fieldtypes = array( 'checkbox', 'date', 'menu', 'multimenu', 'number', 'radiobutton', 'text', 'textarea', 'url' );

        $this->create_default_data_fields($fieldtypes, $data1);

        $data1record1id = $this->create_default_data_record($data1);
        // All records.
        $recordset = $searcharea->get_recordset_by_timestamp(0);

        $this->assertTrue($recordset->valid());

        $nrecords = 0;
        foreach ($recordset as $record) {
            $this->assertInstanceOf('stdClass', $record);
            $doc = $searcharea->get_document($record);
            $this->assertInstanceOf('\core_search\document', $doc);
            $nrecords++;
        }

        // If there would be an error/failure in the foreach above the recordset would be closed on shutdown.
        $recordset->close();
        $this->assertEquals(1, $nrecords);

        // The +2 is to prevent race conditions.
        $recordset = $searcharea->get_recordset_by_timestamp(time() + 2);

        // No new records.
        $this->assertFalse($recordset->valid());
        $recordset->close();

        // Create a second database, also with one record.
        $data2 = $this->getDataGenerator()->create_module('data', ['course' => $course1->id]);
        $this->create_default_data_fields($fieldtypes, $data2);
        $this->create_default_data_record($data2);

        // Test indexing with contexts.
        $rs = $searcharea->get_document_recordset(0, \context_module::instance($data1->cmid));
        $this->assertEquals(1, iterator_count($rs));
        $rs->close();
        $rs = $searcharea->get_document_recordset(0, \context_module::instance($data2->cmid));
        $this->assertEquals(1, iterator_count($rs));
        $rs->close();
        $rs = $searcharea->get_document_recordset(0, \context_course::instance($course1->id));
        $this->assertEquals(2, iterator_count($rs));
        $rs->close();
    }

    /**
     * Document contents.
     *
     * @return void
     */
    public function test_data_entries_document() {
        global $DB;

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->databaseentryareaid);
        $this->assertInstanceOf('\mod_data\search\entry', $searcharea);

        $user1 = self::getDataGenerator()->create_user();

        $course = self::getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');

        $record = new \stdClass();
        $record->course = $course->id;

        $this->setAdminUser();

        // First Case.
        $data1 = $this->getDataGenerator()->create_module('data', $record);

        $fieldtypes = array( 'checkbox', 'date', 'menu', 'multimenu', 'number', 'radiobutton', 'text', 'textarea', 'url' );

        $this->create_default_data_fields($fieldtypes, $data1);

        $data1record1id = $this->create_default_data_record($data1);

        $data1entry1 = $this->get_entry_for_id($data1record1id);

        $data1doc = $searcharea->get_document($data1entry1);

        $this->assertEquals($data1doc->get('courseid'), $course->id);
        $this->assertEquals($data1doc->get('title'), 'text for testing');
        $this->assertEquals($data1doc->get('content'), 'menu1');
        $this->assertEquals($data1doc->get('description1'), 'radioopt1');
        $this->assertEquals($data1doc->get('description2'), 'opt1 opt2 opt3 opt4 multimenu1 multimenu2 multimenu3 multimenu4 text area testing http://example.url');

        // Second Case.
        $data2 = $this->getDataGenerator()->create_module('data', $record);

        $fieldtypes = array(
                array('checkbox', 1),
                array('textarea', 0),
                array('menu', 0),
                array('number', 1),
                array('url', 0),
                array('text', 0)
        );

        $this->create_default_data_fields($fieldtypes, $data2);

        $data2record1id = $this->create_default_data_record($data2);

        $data2entry1 = $this->get_entry_for_id($data2record1id);

        $data2doc = $searcharea->get_document($data2entry1);

        $this->assertEquals($data2doc->get('courseid'), $course->id);
        $this->assertEquals($data2doc->get('title'), 'opt1 opt2 opt3 opt4');
        $this->assertEquals($data2doc->get('content'), 'text for testing');
        $this->assertEquals($data2doc->get('description1'), 'menu1');
        $this->assertEquals($data2doc->get('description2'), 'text area testing http://example.url');

        // Third Case.
        $data3 = $this->getDataGenerator()->create_module('data', $record);

        $fieldtypes = array( 'url' );

        $this->create_default_data_fields($fieldtypes, $data3);

        $data3record1id = $this->create_default_data_record($data3);

        $data3entry1 = $this->get_entry_for_id($data3record1id);

        $this->assertFalse($searcharea->get_document($data3entry1));

        // Fourth Case.
        $data4 = $this->getDataGenerator()->create_module('data', $record);

        $fieldtypes = array( array('date', 1), array('text', 1));

        $this->create_default_data_fields($fieldtypes, $data4);

        $data4record1id = $this->create_default_data_record($data4);

        $data4entry1 = $this->get_entry_for_id($data4record1id);

        $this->assertFalse($searcharea->get_document($data4entry1));

        // Fifth Case.
        $data5 = $this->getDataGenerator()->create_module('data', $record);

        $fieldtypes = array(
                array('checkbox', 0),
                array('number', 1),
                array('text', 0),
                array('date', 1),
                array('textarea', 0),
                array('url', 1));

        $this->create_default_data_fields($fieldtypes, $data5);

        $data5record1id = $this->create_default_data_record($data5);

        $data5entry1 = $this->get_entry_for_id($data5record1id);

        $data5doc = $searcharea->get_document($data5entry1);

        $this->assertEquals($data5doc->get('courseid'), $course->id);
        $this->assertEquals($data5doc->get('title'), 'http://example.url');
        $this->assertEquals($data5doc->get('content'), 'text for testing');
        $this->assertEquals($data5doc->get('description1'), 'opt1 opt2 opt3 opt4');
        $this->assertEquals($data5doc->get('description2'), 'text area testing');

        // Sixth Case.
        $data6 = $this->getDataGenerator()->create_module('data', $record);

        $fieldtypes = array( array('date', 1), array('number', 1));

        $this->create_default_data_fields($fieldtypes, $data6);

        $data6record1id = $this->create_default_data_record($data6);

        $data6entry1 = $this->get_entry_for_id($data6record1id);

        $data6doc = $searcharea->get_document($data6entry1);

        $this->assertFalse($data6doc);

        // Seventh Case.
        $data7 = $this->getDataGenerator()->create_module('data', $record);

        $fieldtypes = array( array('date', 1), array('number', 1),
                array('text', 0), array('textarea', 0));

        $this->create_default_data_fields($fieldtypes, $data7);

        $data7record1id = $this->create_default_data_record($data7);

        $data7entry1 = $this->get_entry_for_id($data7record1id);

        $data7doc = $searcharea->get_document($data7entry1);

        $this->assertEquals($data7doc->get('courseid'), $course->id);
        $this->assertEquals($data7doc->get('title'), 'text for testing');
        $this->assertEquals($data7doc->get('content'), 'text area testing');

        // Eight Case.
        $data8 = $this->getDataGenerator()->create_module('data', $record);

        $fieldtypes = array('url', 'url', 'url', 'text');

        $this->create_default_data_fields($fieldtypes, $data8);

        $data8record1id = $this->create_default_data_record($data8);

        $data8entry1 = $this->get_entry_for_id($data8record1id);

        $data8doc = $searcharea->get_document($data8entry1);

        $this->assertEquals($data8doc->get('courseid'), $course->id);
        $this->assertEquals($data8doc->get('title'), 'text for testing');
        $this->assertEquals($data8doc->get('content'), 'http://example.url');
        $this->assertEquals($data8doc->get('description1'), 'http://example.url');
        $this->assertEquals($data8doc->get('description2'), 'http://example.url');

        // Ninth Case.
        $data9 = $this->getDataGenerator()->create_module('data', $record);

        $fieldtypes = array('radiobutton', 'menu', 'multimenu');

        $this->create_default_data_fields($fieldtypes, $data9);

        $data9record1id = $this->create_default_data_record($data9);

        $data9entry1 = $this->get_entry_for_id($data9record1id);

        $data9doc = $searcharea->get_document($data9entry1);

        $this->assertEquals($data9doc->get('courseid'), $course->id);
        $this->assertEquals($data9doc->get('title'), 'radioopt1');
        $this->assertEquals($data9doc->get('content'), 'menu1');
        $this->assertEquals($data9doc->get('description1'), 'multimenu1 multimenu2 multimenu3 multimenu4');

        // Tenth Case.
        $data10 = $this->getDataGenerator()->create_module('data', $record);

        $fieldtypes = array('checkbox', 'textarea', 'multimenu');

        $this->create_default_data_fields($fieldtypes, $data10);

        $data10record1id = $this->create_default_data_record($data10);

        $data10entry1 = $this->get_entry_for_id($data10record1id);

        $data10doc = $searcharea->get_document($data10entry1);

        $this->assertEquals($data10doc->get('courseid'), $course->id);
        $this->assertEquals($data10doc->get('title'), 'opt1 opt2 opt3 opt4');
        $this->assertEquals($data10doc->get('content'), 'text area testing');
        $this->assertEquals($data10doc->get('description1'), 'multimenu1 multimenu2 multimenu3 multimenu4');

    }

    /**
     * Group support for data entries.
     */
    public function test_data_entries_group_support() {
        global $DB;

        // Get the search area and test generators.
        $searcharea = \core_search\manager::get_search_area($this->databaseentryareaid);
        $generator = $this->getDataGenerator();
        $datagenerator = $generator->get_plugin_generator('mod_data');

        // Create a course, a user, and two groups.
        $course = $generator->create_course();
        $user = $generator->create_user();
        $generator->enrol_user($user->id, $course->id, 'teacher');
        $group1 = $generator->create_group(['courseid' => $course->id]);
        $group2 = $generator->create_group(['courseid' => $course->id]);

        // Separate groups database.
        $data = self::getDataGenerator()->create_module('data', ['course' => $course->id,
                'groupmode' => SEPARATEGROUPS]);
        $fieldtypes = ['text', 'textarea'];
        $this->create_default_data_fields($fieldtypes, $data);
        $fields = $DB->get_records('data_fields', array('dataid' => $data->id));
        foreach ($fields as $field) {
            switch ($field->type) {
                case 'text' :
                    $textid = $field->id;
                    break;
                case 'textarea' :
                    $textareaid = $field->id;
                    break;
            }
        }

        // As admin, create entries with each group and all groups.
        $this->setAdminUser();
        $fieldvalues = [$textid => 'Title', $textareaid => 'Content'];
        $e1 = $datagenerator->create_entry($data, $fieldvalues, $group1->id);
        $e2 = $datagenerator->create_entry($data, $fieldvalues, $group2->id);
        $e3 = $datagenerator->create_entry($data, $fieldvalues);

        // Do the indexing of all 3 entries.
        $rs = $searcharea->get_recordset_by_timestamp(0);
        $results = [];
        foreach ($rs as $rec) {
            $results[$rec->id] = $rec;
        }
        $rs->close();
        $this->assertCount(3, $results);

        // Check each has the correct groupid.
        $doc = $searcharea->get_document($results[$e1]);
        $this->assertTrue($doc->is_set('groupid'));
        $this->assertEquals($group1->id, $doc->get('groupid'));
        $doc = $searcharea->get_document($results[$e2]);
        $this->assertTrue($doc->is_set('groupid'));
        $this->assertEquals($group2->id, $doc->get('groupid'));
        $doc = $searcharea->get_document($results[$e3]);
        $this->assertFalse($doc->is_set('groupid'));

        // While we're here, also test that the search area requests restriction by group.
        $modinfo = get_fast_modinfo($course);
        $this->assertTrue($searcharea->restrict_cm_access_by_group($modinfo->get_cm($data->cmid)));

        // In visible groups mode, it won't request restriction by group.
        set_coursemodule_groupmode($data->cmid, VISIBLEGROUPS);
        $modinfo = get_fast_modinfo($course);
        $this->assertFalse($searcharea->restrict_cm_access_by_group($modinfo->get_cm($data->cmid)));
    }

    /**
     * Document accesses.
     *
     * @return void
     */
    public function test_data_entries_access() {
        global $DB;

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->databaseentryareaid);
        $this->assertInstanceOf('\mod_data\search\entry', $searcharea);

        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $userteacher1 = self::getDataGenerator()->create_user();

        $course1 = self::getDataGenerator()->create_course();
        $course2 = self::getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($user1->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course1->id, 'student');
        $this->getDataGenerator()->enrol_user($userteacher1->id, $course1->id, 'teacher');

        $this->getDataGenerator()->enrol_user($user3->id, $course2->id, 'student');

        $record = new \stdClass();
        $record->course = $course1->id;

        $this->setUser($userteacher1);

        $data1 = $this->getDataGenerator()->create_module('data', $record);

        $fieldtypes = array( 'checkbox', 'date', 'menu', 'multimenu', 'number', 'radiobutton', 'text', 'textarea', 'url' );

        $this->create_default_data_fields($fieldtypes, $data1);

        $this->setUser($user1);
        $data1record1id = $this->create_default_data_record($data1);

        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data1record1id));
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access(-1));

        $this->setUser($user2);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data1record1id));

        $this->setUser($user3);
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($data1record1id));

        $this->setUser($userteacher1);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data1record1id));

        $this->setAdminUser();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data1record1id));

        $this->setGuestUser();
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($data1record1id));

        // Case with groups.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $user3 = self::getDataGenerator()->create_user();
        $userteacher1 = self::getDataGenerator()->create_user();

        $course = self::getDataGenerator()->create_course(array('groupmode' => 1, 'groupmodeforce' => 1));

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($userteacher1->id, $course->id, 'teacher');
        $this->getDataGenerator()->enrol_user($user3->id, $course->id, 'student');

        $groupa = $this->getDataGenerator()->create_group(array('courseid' => $course->id, 'name' => 'groupA'));
        $groupb = $this->getDataGenerator()->create_group(array('courseid' => $course->id, 'name' => 'groupB'));

        $this->getDataGenerator()->create_group_member(array('userid' => $user1->id, 'groupid' => $groupa->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $user2->id, 'groupid' => $groupa->id));
        $this->getDataGenerator()->create_group_member(array('userid' => $userteacher1->id, 'groupid' => $groupa->id));

        $this->getDataGenerator()->create_group_member(array('userid' => $user3->id, 'groupid' => $groupb->id));

        $record = new \stdClass();
        $record->course = $course->id;

        $this->setUser($userteacher1);

        $data2 = $this->getDataGenerator()->create_module('data', $record);

        $cm = get_coursemodule_from_instance('data', $data2->id, $course->id);
        $cm->groupmode = '1';
        $cm->effectivegroupmode = '1';

        $fieldtypes = array( 'checkbox', 'date', 'menu', 'multimenu', 'number', 'radiobutton', 'text', 'textarea', 'url' );

        $this->create_default_data_fields($fieldtypes, $data2);

        $this->setUser($user1);

        $data2record1id = $this->create_default_data_record($data2, $groupa->id);

        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data2record1id));
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access(-1));

        $this->setUser($user2);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data2record1id));

        $this->setUser($user3);
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($data2record1id));

        $this->setUser($userteacher1);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data2record1id));

        $this->setAdminUser();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data2record1id));

        $this->setGuestUser();
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($data2record1id));

        // Case with approval.
        $user1 = self::getDataGenerator()->create_user();
        $user2 = self::getDataGenerator()->create_user();
        $userteacher1 = self::getDataGenerator()->create_user();

        $course = self::getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($user2->id, $course->id, 'student');
        $this->getDataGenerator()->enrol_user($userteacher1->id, $course->id, 'teacher');

        $record = new \stdClass();
        $record->course = $course->id;

        $this->setUser($userteacher1);

        $data3 = $this->getDataGenerator()->create_module('data', $record);

        $DB->update_record('data', array('id' => $data3->id, 'approval' => 1));

        $fieldtypes = array( 'checkbox', 'date', 'menu', 'multimenu', 'number', 'radiobutton', 'text', 'textarea', 'url' );

        $this->create_default_data_fields($fieldtypes, $data3);

        $this->setUser($user1);

        $data3record1id = $this->create_default_data_record($data3);

        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data3record1id));
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access(-1));

        $this->setUser($user2);
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($data3record1id));

        $this->setUser($userteacher1);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data3record1id));

        $this->setAdminUser();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data3record1id));

        $this->setGuestUser();
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($data3record1id));

        $DB->update_record('data_records', array('id' => $data3record1id, 'approved' => 1));

        $this->setUser($user1);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data3record1id));
        $this->assertEquals(\core_search\manager::ACCESS_DELETED, $searcharea->check_access(-1));

        $this->setUser($user2);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data3record1id));

        $this->setUser($userteacher1);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data3record1id));

        $this->setAdminUser();
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data3record1id));

        $this->setGuestUser();
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($data3record1id));

        // Case with requiredentriestoview.
        $this->setAdminUser();

        $record->requiredentriestoview = 2;

        $data4 = $this->getDataGenerator()->create_module('data', $record);
        $fieldtypes = array( 'checkbox', 'date', 'menu', 'multimenu', 'number', 'radiobutton', 'text', 'textarea', 'url' );

        $this->create_default_data_fields($fieldtypes, $data4);

        $data4record1id = $this->create_default_data_record($data4);

        $this->setUser($user1);
        $this->assertEquals(\core_search\manager::ACCESS_DENIED, $searcharea->check_access($data4record1id));

        $data4record2id = $this->create_default_data_record($data4);
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data4record1id));
        $this->assertEquals(\core_search\manager::ACCESS_GRANTED, $searcharea->check_access($data4record2id));
    }

    /**
     * Test for file contents.
     *
     * @return void
     */
    public function test_attach_files() {
        global $DB, $USER;

        $fs = get_file_storage();

        // Returns the instance as long as the area is supported.
        $searcharea = \core_search\manager::get_search_area($this->databaseentryareaid);
        $this->assertInstanceOf('\mod_data\search\entry', $searcharea);

        $user1 = self::getDataGenerator()->create_user();

        $course = self::getDataGenerator()->create_course();

        $this->getDataGenerator()->enrol_user($user1->id, $course->id, 'student');

        $record = new \stdClass();
        $record->course = $course->id;

        $this->setAdminUser();

        // Creating database activity instance.
        $data1 = $this->getDataGenerator()->create_module('data', $record);

        // Creating file field.
        $record = new \stdClass;
        $record->type = 'file';
        $record->dataid = $data1->id;
        $record->required = 0;
        $record->name = 'FileFld';
        $record->description = 'Just another file field';
        $record->param3 = 0;
        $record->param1 = '';
        $record->param2 = '';

        $data1filefieldid = $DB->insert_record('data_fields', $record);

        // Creating text field.
        $record = new \stdClass;
        $record->type = 'text';
        $record->dataid = $data1->id;
        $record->required = 0;
        $record->name = 'TextFld';
        $record->description = 'Just another text field';
        $record->param3 = 0;
        $record->param1 = '';
        $record->param2 = '';

        $data1textfieldid = $DB->insert_record('data_fields', $record);

        // Creating textarea field.
        $record = new \stdClass;
        $record->type = 'textarea';
        $record->dataid = $data1->id;
        $record->required = 0;
        $record->name = 'TextAreaFld';
        $record->description = 'Just another textarea field';
        $record->param1 = '';
        $record->param2 = 60;
        $record->param3 = 35;
        $record->param3 = 1;
        $record->param3 = 0;

        $data1textareafieldid = $DB->insert_record('data_fields', $record);

        // Creating 1st entry.
        $record = new \stdClass;
        $record->userid = $USER->id;
        $record->dataid = $data1->id;
        $record->groupid = 0;

        $data1record1id = $DB->insert_record('data_records', $record);

        $filerecord = array(
                'contextid' => \context_module::instance($data1->cmid)->id,
                'component' => 'mod_data',
                'filearea'  => 'content',
                'itemid'    => $data1record1id,
                'filepath'  => '/',
                'filename'  => 'myfile1.txt'
        );

        $data1record1file = $fs->create_file_from_string($filerecord, 'Some contents 1');

        $record = new \stdClass;
        $record->fieldid = $data1filefieldid;
        $record->recordid = $data1record1id;
        $record->content = 'myfile1.txt';
        $DB->insert_record('data_content', $record);

        $record = new \stdClass;
        $record->fieldid = $data1textfieldid;
        $record->recordid = $data1record1id;
        $record->content = 'sample text';
        $DB->insert_record('data_content', $record);

        $record = new \stdClass;
        $record->fieldid = $data1textareafieldid;
        $record->recordid = $data1record1id;
        $record->content = '<br>sample text<p /><br/>';
        $record->content1 = 1;
        $DB->insert_record('data_content', $record);

        // Creating 2nd entry.
        $record = new \stdClass;
        $record->userid = $USER->id;
        $record->dataid = $data1->id;
        $record->groupid = 0;
        $data1record2id = $DB->insert_record('data_records', $record);

        $filerecord['itemid'] = $data1record2id;
        $filerecord['filename'] = 'myfile2.txt';
        $data1record2file = $fs->create_file_from_string($filerecord, 'Some contents 2');

        $record = new \stdClass;
        $record->fieldid = $data1filefieldid;
        $record->recordid = $data1record2id;
        $record->content = 'myfile2.txt';
        $DB->insert_record('data_content', $record);

        $record = new \stdClass;
        $record->fieldid = $data1textfieldid;
        $record->recordid = $data1record2id;
        $record->content = 'sample text';
        $DB->insert_record('data_content', $record);

        $record = new \stdClass;
        $record->fieldid = $data1textareafieldid;
        $record->recordid = $data1record2id;
        $record->content = '<br>sample text<p /><br/>';
        $record->content1 = 1;
        $DB->insert_record('data_content', $record);

        // Now get all the posts and see if they have the right files attached.
        $searcharea = \core_search\manager::get_search_area($this->databaseentryareaid);
        $recordset = $searcharea->get_recordset_by_timestamp(0);
        $nrecords = 0;
        foreach ($recordset as $record) {
            $doc = $searcharea->get_document($record);
            $searcharea->attach_files($doc);
            $files = $doc->get_files();
            // Now check that each doc has the right files on it.
            switch ($doc->get('itemid')) {
                case ($data1record1id):
                    $this->assertCount(1, $files);
                    $this->assertEquals($data1record1file->get_id(), $files[$data1record1file->get_id()]->get_id());
                    break;
                case ($data1record2id):
                    $this->assertCount(1, $files);
                    $this->assertEquals($data1record2file->get_id(), $files[$data1record2file->get_id()]->get_id());
                    break;
                default:
                    $this->fail('Unexpected entry returned');
                    break;
            }
            $nrecords++;
        }
        $recordset->close();
        $this->assertEquals(2, $nrecords);
    }

    /**
     * Creates default fields for a database instance
     *
     * @param array $fieldtypes
     * @param mod_data $data
     * @return void
     */
    protected function create_default_data_fields($fieldtypes, $data) {
        $count = 1;

        // Creating test Fields with default parameter values.
        foreach ($fieldtypes as $fieldtype) {

            // Creating variables dynamically.
            $fieldname = 'field-'.$count;
            $record = new \stdClass();
            $record->name = $fieldname;

            if (is_array($fieldtype)) {
                $record->type = $fieldtype[0];
                $record->required = $fieldtype[1];
            } else {
                $record->type = $fieldtype;
                $record->required = 0;
            }

            ${$fieldname} = $this->getDataGenerator()->get_plugin_generator('mod_data')->create_field($record, $data);
            $count++;
        }
    }

    /**
     * Creates default database entry content values for default field param values
     *
     * @param mod_data $data
     * @param int $groupid
     * @return int
     */
    protected function create_default_data_record($data, $groupid = 0) {
        global $DB;

        $fields = $DB->get_records('data_fields', array('dataid' => $data->id));

        $fieldcontents = array();
        foreach ($fields as $fieldrecord) {
            switch ($fieldrecord->type) {
                case 'checkbox':
                    $fieldcontents[$fieldrecord->id] = array('opt1', 'opt2', 'opt3', 'opt4');
                    break;

                case 'multimenu':
                    $fieldcontents[$fieldrecord->id] = array('multimenu1', 'multimenu2', 'multimenu3', 'multimenu4');
                    break;

                case 'date':
                    $fieldcontents[$fieldrecord->id] = '27-07-2016';
                    break;

                case 'menu':
                    $fieldcontents[$fieldrecord->id] = 'menu1';
                    break;

                case 'radiobutton':
                    $fieldcontents[$fieldrecord->id] = 'radioopt1';
                    break;

                case 'number':
                    $fieldcontents[$fieldrecord->id] = '12345';
                    break;

                case 'text':
                    $fieldcontents[$fieldrecord->id] = 'text for testing';
                    break;

                case 'textarea':
                    $fieldcontents[$fieldrecord->id] = '<p>text area testing<br /></p>';
                    break;

                case 'url':
                    $fieldcontents[$fieldrecord->id] = array('example.url', 'sampleurl');
                    break;

                default:
                    $this->fail('Unexpected field type');
                    break;
            }

        }

        return $this->getDataGenerator()->get_plugin_generator('mod_data')->create_entry($data, $fieldcontents, $groupid);
    }

    /**
     * Creates default database entry content values for default field param values
     *
     * @param int $recordid
     * @return stdClass
     */
    protected function get_entry_for_id($recordid ) {
        global $DB;

        $sql = "SELECT dr.*, d.course
                  FROM {data_records} dr
                  JOIN {data} d ON d.id = dr.dataid
                 WHERE dr.id = :drid";
        return $DB->get_record_sql($sql, array('drid' => $recordid));
    }

}

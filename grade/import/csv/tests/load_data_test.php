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

namespace gradeimport_csv;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/grade/import/csv/tests/fixtures/phpunit_gradeimport_csv_load_data.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once($CFG->libdir . '/grade/grade_item.php');
require_once($CFG->libdir . '/grade/tests/fixtures/lib.php');

/**
 * Unit tests for lib.php
 *
 * @package    gradeimport_csv
 * @copyright  2014 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class load_data_test extends \grade_base_testcase {

    /** @var string $oktext Text to be imported. This data should have no issues being imported. */
    protected $oktext = '"First name","Last name","ID number",Institution,Department,"Email address","Assignment: Assignment for grape group", "Feedback: Assignment for grape group","Assignment: Second new grade item","Course total"
Anne,Able,,"Moodle HQ","Rock on!",student7@example.com,56.00,"We welcome feedback",,56.00
Bobby,Bunce,,"Moodle HQ","Rock on!",student5@example.com,75.00,,45.0,75.00';

    /** @var string $badtext Text to be imported. This data has an extra column and should not succeed in being imported. */
    protected $badtext = '"First name","Last name","ID number",Institution,Department,"Email address","Assignment: Assignment for grape group","Course total"
Anne,Able,,"Moodle HQ","Rock on!",student7@example.com,56.00,56.00,78.00
Bobby,Bunce,,"Moodle HQ","Rock on!",student5@example.com,75.00,75.00';

    /** @var string $csvtext CSV data to be imported with Last download from this course column. */
    protected $csvtext = '"First name","Last name","ID number",Institution,Department,"Email address","Assignment: Assignment for grape group", "Feedback: Assignment for grape group","Course total","Last downloaded from this course"
Anne,Able,,"Moodle HQ","Rock on!",student7@example.com,56.00,"We welcome feedback",56.00,{exportdate}
Bobby,Bunce,,"Moodle HQ","Rock on!",student5@example.com,75.00,,75.00,{exportdate}';

    /** @var int $iid Import ID. */
    protected $iid;

    /** @var object $csvimport a csv_import_reader object that handles the csv import. */
    protected $csvimport;

    /** @var array $columns The first row of the csv file. These are the columns of the import file.*/
    protected $columns;

    public function tearDown(): void {
        $this->csvimport = null;
    }

    /**
     * Load up the above text through the csv import.
     *
     * @param string $content Text to be imported into the gradebook.
     * @return array All text separated by commas now in an array.
     */
    protected function csv_load($content) {
        // Import the csv strings.
        $this->iid = \csv_import_reader::get_new_iid('grade');
        $this->csvimport = new \csv_import_reader($this->iid, 'grade');

        $this->csvimport->load_csv_content($content, 'utf8', 'comma');
        $this->columns = $this->csvimport->get_columns();

        $this->csvimport->init();
        while ($line = $this->csvimport->next()) {
            $testarray[] = $line;
        }

        return $testarray;
    }

    /**
     * Test loading data and returning preview content.
     */
    public function test_load_csv_content() {
        $encoding = 'utf8';
        $separator = 'comma';
        $previewrows = 5;
        $csvpreview = new \phpunit_gradeimport_csv_load_data();
        $csvpreview->load_csv_content($this->oktext, $encoding, $separator, $previewrows);

        $expecteddata = array(array(
                'Anne',
                'Able',
                '',
                'Moodle HQ',
                'Rock on!',
                'student7@example.com',
                56.00,
                'We welcome feedback',
                '',
                56.00
            ),
            array(
                'Bobby',
                'Bunce',
                '',
                'Moodle HQ',
                'Rock on!',
                'student5@example.com',
                75.00,
                '',
                45.0,
                75.00
            )
        );

        $expectedheaders = array(
            'First name',
            'Last name',
            'ID number',
            'Institution',
            'Department',
            'Email address',
            'Assignment: Assignment for grape group',
            'Feedback: Assignment for grape group',
            'Assignment: Second new grade item',
            'Course total'
        );
        // Check that general data is returned as expected.
        $this->assertEquals($csvpreview->get_previewdata(), $expecteddata);
        // Check that headers are returned as expected.
        $this->assertEquals($csvpreview->get_headers(), $expectedheaders);

        // Check that errors are being recorded.
        $csvpreview = new \phpunit_gradeimport_csv_load_data();
        $csvpreview->load_csv_content($this->badtext, $encoding, $separator, $previewrows);
        // Columns shouldn't match.
        $this->assertEquals($csvpreview->get_error(), get_string('csvweirdcolumns', 'error'));
    }

    /**
     * Test fetching grade items for the course.
     */
    public function test_fetch_grade_items() {

        $gradeitemsarray = \grade_item::fetch_all(array('courseid' => $this->courseid));
        $gradeitems = \phpunit_gradeimport_csv_load_data::fetch_grade_items($this->courseid);

        // Make sure that each grade item is located in the gradeitemsarray.
        foreach ($gradeitems as $key => $gradeitem) {
            $this->assertArrayHasKey($key, $gradeitemsarray);
        }

        // Get the key for a specific grade item.
        $quizkey = null;
        foreach ($gradeitemsarray as $key => $value) {
            if ($value->itemname == "Quiz grade item") {
                $quizkey = $key;
            }
        }

        // Expected modified item name.
        $testitemname = get_string('modulename', $gradeitemsarray[$quizkey]->itemmodule) . ': ' .
                $gradeitemsarray[$quizkey]->itemname;
        // Check that an item that is a module, is concatenated properly.
        $this->assertEquals($testitemname, $gradeitems[$quizkey]);
    }

    /**
     * Test the inserting of grade record data.
     */
    public function test_insert_grade_record() {
        global $DB, $USER;

        $user = $this->getDataGenerator()->create_user();
        $this->setAdminUser();

        $record = new \stdClass();
        $record->itemid = 4;
        $record->newgradeitem = 25;
        $record->finalgrade = 62.00;
        $record->feedback = 'Some test feedback';

        $testobject = new \phpunit_gradeimport_csv_load_data();

        $testobject->test_insert_grade_record($record, $user->id, new \grade_item());

        $gradeimportvalues = $DB->get_records('grade_import_values');
        // Get the insert id.
        $key = key($gradeimportvalues);

        $testarray = array();
        $testarray[$key] = new \stdClass();
        $testarray[$key]->id = $key;
        $testarray[$key]->itemid = $record->itemid;
        $testarray[$key]->newgradeitem = $record->newgradeitem;
        $testarray[$key]->userid = $user->id;
        $testarray[$key]->finalgrade = $record->finalgrade;
        $testarray[$key]->feedback = $record->feedback;
        $testarray[$key]->importcode = $testobject->get_importcode();
        $testarray[$key]->importer = $USER->id;
        $testarray[$key]->importonlyfeedback = 0;

        // Check that the record was inserted into the database.
        $this->assertEquals($gradeimportvalues, $testarray);
    }

    /**
     * Test preparing a new grade item for import into the gradebook.
     */
    public function test_import_new_grade_item() {
        global $DB;

        $this->setAdminUser();
        $this->csv_load($this->oktext);
        $columns = $this->columns;

        // The assignment is item 6.
        $key = 6;
        $testobject = new \phpunit_gradeimport_csv_load_data();

        // Key for this assessment.
        $this->csvimport->init();
        $testarray = array();
        while ($line = $this->csvimport->next()) {
            $testarray[] = $testobject->test_import_new_grade_item($columns, $key, $line[$key]);
        }

        // Query the database and check how many results were inserted.
        $newgradeimportitems = $DB->get_records('grade_import_newitem');
        $this->assertEquals(count($testarray), count($newgradeimportitems));
    }

    /**
     * Data provider for \gradeimport_csv_load_data_testcase::test_check_user_exists().
     *
     * @return array
     */
    public function check_user_exists_provider() {
        return [
            'Fetch by email' => [
                'email', 's1@example.com', true
            ],
            'Fetch by email, different case' => [
                'email', 'S1@EXAMPLE.COM', true
            ],
            'Fetch data using a non-existent email' => [
                'email', 's2@example.com', false
            ],
            'Multiple accounts with the same email' => [
                'email', 's1@example.com', false, 1
            ],
            'Fetch data using a valid user ID' => [
                'id', true, true
            ],
            'Fetch data using a non-existent user ID' => [
                'id', false, false
            ],
            'Fetch data using a valid username' => [
                'username', 's1', true
            ],
            'Fetch data using a valid username, different case' => [
                'username', 'S1', true
            ],
            'Fetch data using an invalid username' => [
                'username', 's2', false
            ],
            'Fetch data using a valid ID Number' => [
                'idnumber', 's1', true
            ],
            'Fetch data using an invalid ID Number' => [
                'idnumber', 's2', false
            ],
        ];
    }

    /**
     * Check that the user matches a user in the system.
     *
     * @dataProvider check_user_exists_provider
     * @param string $field The field to use for the query.
     * @param string|boolean $value The field value. When fetching by ID, set true to fetch valid user ID, false otherwise.
     * @param boolean $successexpected Whether we expect for a user to be found or not.
     * @param int $allowaccountssameemail Value for $CFG->allowaccountssameemail
     */
    public function test_check_user_exists($field, $value, $successexpected, $allowaccountssameemail = 0) {
        $this->resetAfterTest();

        $generator = $this->getDataGenerator();

        // Need to add one of the users into the system.
        $user = $generator->create_user([
            'firstname' => 'Anne',
            'lastname' => 'Able',
            'email' => 's1@example.com',
            'idnumber' => 's1',
            'username' => 's1',
        ]);

        if ($allowaccountssameemail) {
            // Create another user with the same email address.
            $generator->create_user(['email' => 's1@example.com']);
        }

        // Since the data provider can't know what user ID to use, do a special handling for ID field tests.
        if ($field === 'id') {
            if ($value) {
                // Test for fetching data using a valid user ID. Use the generated user's ID.
                $value = $user->id;
            } else {
                // Test for fetching data using a non-existent user ID.
                $value = $user->id + 1;
            }
        }

        $userfields = [
            'field' => $field,
            'label' => 'Field label: ' . $field
        ];

        $testobject = new \phpunit_gradeimport_csv_load_data();

        // Check whether the user exists. If so, then the user id is returned. Otherwise, it returns null.
        $userid = $testobject->test_check_user_exists($value, $userfields);

        if ($successexpected) {
            // Check that the user id returned matches with the user that we created.
            $this->assertEquals($user->id, $userid);

            // Check that there are no errors.
            $this->assertEmpty($testobject->get_gradebookerrors());

        } else {
            // Check that the userid is null.
            $this->assertNull($userid);

            // Check that expected error message and actual message match.
            $gradebookerrors = $testobject->get_gradebookerrors();
            $mappingobject = (object)[
                'field' => $userfields['label'],
                'value' => $value,
            ];
            if ($allowaccountssameemail) {
                $expectederrormessage = get_string('usermappingerrormultipleusersfound', 'grades', $mappingobject);
            } else {
                $expectederrormessage = get_string('usermappingerror', 'grades', $mappingobject);
            }

            $this->assertEquals($expectederrormessage, $gradebookerrors[0]);
        }
    }

    /**
     * Test preparing feedback for inserting / updating into the gradebook.
     */
    public function test_create_feedback() {

        $testarray = $this->csv_load($this->oktext);
        $testobject = new \phpunit_gradeimport_csv_load_data();

        // Try to insert some feedback for an assessment.
        $feedback = $testobject->test_create_feedback($this->courseid, 1, $testarray[0][7]);

        // Expected result.
        $expectedfeedback = array('itemid' => 1, 'feedback' => $testarray[0][7]);
        $this->assertEquals((array)$feedback, $expectedfeedback);
    }

    /**
     * Test preparing grade_items for upgrading into the gradebook.
     */
    public function test_update_grade_item() {

        $testarray = $this->csv_load($this->oktext);
        $testobject = new \phpunit_gradeimport_csv_load_data();

        // We're not using scales so no to this option.
        $verbosescales = 0;
        // Map and key are to retrieve the grade_item that we are updating.
        $map = array(1);
        $key = 0;
        // We return the new grade array for saving.
        $newgrades = $testobject->test_update_grade_item($this->courseid, $map, $key, $verbosescales, $testarray[0][6]);

        $expectedresult = array();
        $expectedresult[0] = new \stdClass();
        $expectedresult[0]->itemid = 1;
        $expectedresult[0]->finalgrade = $testarray[0][6];

        $this->assertEquals($newgrades, $expectedresult);

        // Try sending a bad grade value (A letter instead of a float / int).
        $newgrades = $testobject->test_update_grade_item($this->courseid, $map, $key, $verbosescales, 'A');
        // The $newgrades variable should be null.
        $this->assertNull($newgrades);
        $expectederrormessage = get_string('badgrade', 'grades');
        // Check that the error message is what we expect.
        $gradebookerrors = $testobject->get_gradebookerrors();
        $this->assertEquals($expectederrormessage, $gradebookerrors[0]);
    }

    /**
     * Test importing data and mapping it with items in the course.
     */
    public function test_map_user_data_with_value() {
        // Need to add one of the users into the system.
        $user = new \stdClass();
        $user->firstname = 'Anne';
        $user->lastname = 'Able';
        $user->email = 'student7@example.com';
        $userdetail = $this->getDataGenerator()->create_user($user);

        $testarray = $this->csv_load($this->oktext);
        $testobject = new \phpunit_gradeimport_csv_load_data();

        // We're not using scales so no to this option.
        $verbosescales = 0;
        // Map and key are to retrieve the grade_item that we are updating.
        $map = array(1);
        $key = 0;

        // Test new user mapping. This should return the user id if there were no problems.
        $userid = $testobject->test_map_user_data_with_value('useremail', $testarray[0][5], $this->columns, $map, $key,
                $this->courseid, $map[$key], $verbosescales);
        $this->assertEquals($userid, $userdetail->id);

        $newgrades = $testobject->test_map_user_data_with_value('new', $testarray[0][6], $this->columns, $map, $key,
                $this->courseid, $map[$key], $verbosescales);
        // Check that the final grade is the same as the one inserted.
        $this->assertEquals($testarray[0][6], $newgrades[0]->finalgrade);

        $newgrades = $testobject->test_map_user_data_with_value('new', $testarray[0][8], $this->columns, $map, $key,
                $this->courseid, $map[$key], $verbosescales);
        // Check that the final grade is the same as the one inserted.
        // The testobject should now contain 2 new grade items.
        $this->assertEquals(2, count($newgrades));
        // Because this grade item is empty, the value for final grade should be null.
        $this->assertNull($newgrades[1]->finalgrade);

        $feedback = $testobject->test_map_user_data_with_value('feedback', $testarray[0][7], $this->columns, $map, $key,
                $this->courseid, $map[$key], $verbosescales);
        // Expected result.
        $resultarray = array();
        $resultarray[0] = new \stdClass();
        $resultarray[0]->itemid = 1;
        $resultarray[0]->feedback = $testarray[0][7];
        $this->assertEquals($feedback, $resultarray);

        // Default behaviour (update a grade item).
        $newgrades = $testobject->test_map_user_data_with_value('default', $testarray[0][6], $this->columns, $map, $key,
                $this->courseid, $map[$key], $verbosescales);
        $this->assertEquals($testarray[0][6], $newgrades[0]->finalgrade);
    }

    /**
     * Test importing data into the gradebook.
     */
    public function test_prepare_import_grade_data() {
        global $DB;

        // Need to add one of the users into the system.
        $user = new \stdClass();
        $user->firstname = 'Anne';
        $user->lastname = 'Able';
        $user->email = 'student7@example.com';
        // Insert user 1.
        $this->getDataGenerator()->create_user($user);
        $user = new \stdClass();
        $user->firstname = 'Bobby';
        $user->lastname = 'Bunce';
        $user->email = 'student5@example.com';
        // Insert user 2.
        $this->getDataGenerator()->create_user($user);

        $this->csv_load($this->oktext);

        $importcode = 007;
        $verbosescales = 0;

        // Form data object.
        $formdata = new \stdClass();
        $formdata->mapfrom = 5;
        $formdata->mapto = 'useremail';
        $formdata->mapping_0 = 0;
        $formdata->mapping_1 = 0;
        $formdata->mapping_2 = 0;
        $formdata->mapping_3 = 0;
        $formdata->mapping_4 = 0;
        $formdata->mapping_5 = 0;
        $formdata->mapping_6 = 'new';
        $formdata->mapping_7 = 'feedback_2';
        $formdata->mapping_8 = 0;
        $formdata->mapping_9 = 0;
        $formdata->map = 1;
        $formdata->id = 2;
        $formdata->iid = $this->iid;
        $formdata->importcode = $importcode;
        $formdata->forceimport = false;

        // Blam go time.
        $testobject = new \phpunit_gradeimport_csv_load_data();
        $dataloaded = $testobject->prepare_import_grade_data($this->columns, $formdata, $this->csvimport, $this->courseid, '', '',
                $verbosescales);
        // If everything inserted properly then this should be true.
        $this->assertTrue($dataloaded);
    }

    /*
     * Test importing csv data into the gradebook using "Last downloaded from this course" column and force import option.
     */
    public function test_force_import_option() {

        // Need to add users into the system.
        $user = new \stdClass();
        $user->firstname = 'Anne';
        $user->lastname = 'Able';
        $user->email = 'student7@example.com';
        $user->id_number = 1;
        $user1 = $this->getDataGenerator()->create_user($user);
        $user = new \stdClass();
        $user->firstname = 'Bobby';
        $user->lastname = 'Bunce';
        $user->email = 'student5@example.com';
        $user->id_number = 2;
        $user2 = $this->getDataGenerator()->create_user($user);

        // Create a new grade item.
        $params = array(
            'itemtype'  => 'manual',
            'itemname'  => 'Grade item 1',
            'gradetype' => GRADE_TYPE_VALUE,
            'courseid'  => $this->courseid
        );
        $gradeitem = new \grade_item($params, false);
        $gradeitemid = $gradeitem->insert();

        $importcode = 001;
        $verbosescales = 0;

        // Form data object.
        $formdata = new \stdClass();
        $formdata->mapfrom = 5;
        $formdata->mapto = 'useremail';
        $formdata->mapping_0 = 0;
        $formdata->mapping_1 = 0;
        $formdata->mapping_2 = 0;
        $formdata->mapping_3 = 0;
        $formdata->mapping_4 = 0;
        $formdata->mapping_5 = 0;
        $formdata->mapping_6 = $gradeitemid;
        $formdata->mapping_7 = 'feedback_2';
        $formdata->mapping_8 = 0;
        $formdata->mapping_9 = 0;
        $formdata->map = 1;
        $formdata->id = 2;
        $formdata->iid = $this->iid;
        $formdata->importcode = $importcode;
        $formdata->forceimport = false;

        // Add last download from this course column to csv content.
        $exportdate = time();
        $newcsvdata = str_replace('{exportdate}', $exportdate, $this->csvtext);
        $this->csv_load($newcsvdata);
        $testobject = new \phpunit_gradeimport_csv_load_data();
        $dataloaded = $testobject->prepare_import_grade_data($this->columns, $formdata, $this->csvimport,
                $this->courseid, '', '', $verbosescales);
        $this->assertTrue($dataloaded);

        // We must update the last modified date.
        grade_import_commit($this->courseid, $importcode, false, false);

        // Test using force import disabled and a date in the past.
        $pastdate = strtotime('-1 day', time());
        $newcsvdata = str_replace('{exportdate}', $pastdate, $this->csvtext);
        $this->csv_load($newcsvdata);
        $testobject = new \phpunit_gradeimport_csv_load_data();
        $dataloaded = $testobject->prepare_import_grade_data($this->columns, $formdata, $this->csvimport,
                $this->courseid, '', '', $verbosescales);
        $this->assertFalse($dataloaded);
        $errors = $testobject->get_gradebookerrors();
        $this->assertEquals($errors[0], get_string('gradealreadyupdated', 'grades', fullname($user1)));

        // Test using force import enabled and a date in the past.
        $formdata->forceimport = true;
        $testobject = new \phpunit_gradeimport_csv_load_data();
        $dataloaded = $testobject->prepare_import_grade_data($this->columns, $formdata, $this->csvimport,
                $this->courseid, '', '', $verbosescales);
        $this->assertTrue($dataloaded);

        // Test importing using an old exported file (2 years ago).
        $formdata->forceimport = false;
        $twoyearsago = strtotime('-2 year', time());
        $newcsvdata = str_replace('{exportdate}', $twoyearsago, $this->csvtext);
        $this->csv_load($newcsvdata);
        $testobject = new \phpunit_gradeimport_csv_load_data();
        $dataloaded = $testobject->prepare_import_grade_data($this->columns, $formdata, $this->csvimport,
                $this->courseid, '', '', $verbosescales);
        $this->assertFalse($dataloaded);
        $errors = $testobject->get_gradebookerrors();
        $this->assertEquals($errors[0], get_string('invalidgradeexporteddate', 'grades'));

        // Test importing using invalid exported date.
        $baddate = '0123A56B89';
        $newcsvdata = str_replace('{exportdate}', $baddate, $this->csvtext);
        $this->csv_load($newcsvdata);
        $formdata->mapping_6 = $gradeitemid;
        $testobject = new \phpunit_gradeimport_csv_load_data();
        $dataloaded = $testobject->prepare_import_grade_data($this->columns, $formdata, $this->csvimport,
                $this->courseid, '', '', $verbosescales);
        $this->assertFalse($dataloaded);
        $errors = $testobject->get_gradebookerrors();
        $this->assertEquals($errors[0], get_string('invalidgradeexporteddate', 'grades'));

        // Test importing using date in the future.
        $oneyearahead = strtotime('+1 year', time());
        $oldcsv = str_replace('{exportdate}', $oneyearahead, $this->csvtext);
        $this->csv_load($oldcsv);
        $formdata->mapping_6 = $gradeitemid;
        $testobject = new \phpunit_gradeimport_csv_load_data();
        $dataloaded = $testobject->prepare_import_grade_data($this->columns, $formdata, $this->csvimport,
            $this->courseid, '', '', $verbosescales);
        $this->assertFalse($dataloaded);
        $errors = $testobject->get_gradebookerrors();
        $this->assertEquals($errors[0], get_string('invalidgradeexporteddate', 'grades'));
    }
}

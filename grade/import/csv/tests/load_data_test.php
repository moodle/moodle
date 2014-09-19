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
 * Unit tests for the class in load_data.php
 *
 * @package    gradeimport_csv
 * @category   phpunit
 * @copyright  2014 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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
class gradeimport_csv_load_data_testcase extends grade_base_testcase {

    /** @var string $oktext Text to be imported. This data should have no issues being imported. */
    protected $oktext = '"First name",Surname,"ID number",Institution,Department,"Email address","Assignment: Assignment for grape group", "Feedback: Assignment for grape group","Course total"
Anne,Able,,"Moodle HQ","Rock on!",student7@mail.com,56.00,"We welcome feedback",56.00
Bobby,Bunce,,"Moodle HQ","Rock on!",student5@mail.com,75.00,,75.00';

    /** @var string $badtext Text to be imported. This data has an extra column and should not succeed in being imported. */
    protected $badtext = '"First name",Surname,"ID number",Institution,Department,"Email address","Assignment: Assignment for grape group","Course total"
Anne,Able,,"Moodle HQ","Rock on!",student7@mail.com,56.00,56.00,78.00
Bobby,Bunce,,"Moodle HQ","Rock on!",student5@mail.com,75.00,75.00';

    /** @var int $iid Import ID. */
    protected $iid;

    /** @var object $csvimport a csv_import_reader object that handles the csv import. */
    protected $csvimport;

    /** @var array $columns The first row of the csv file. These are the columns of the import file.*/
    protected $columns;

    /**
     * Load up the above text through the csv import.
     *
     * @param string $content Text to be imported into the gradebook.
     * @return array All text separated by commas now in an array.
     */
    protected function csv_load($content) {
        // Import the csv strings.
        $this->iid = csv_import_reader::get_new_iid('grade');
        $this->csvimport = new csv_import_reader($this->iid, 'grade');

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
        $csvpreview = new phpunit_gradeimport_csv_load_data();
        $csvpreview->load_csv_content($this->oktext, $encoding, $separator, $previewrows);

        $expecteddata = array(array(
                'Anne',
                'Able',
                '',
                'Moodle HQ',
                'Rock on!',
                'student7@mail.com',
                56.00,
                'We welcome feedback',
                56.00
            ),
            array(
                'Bobby',
                'Bunce',
                '',
                'Moodle HQ',
                'Rock on!',
                'student5@mail.com',
                75.00,
                '',
                75.00
            )
        );

        $expectedheaders = array(
            'First name',
            'Surname',
            'ID number',
            'Institution',
            'Department',
            'Email address',
            'Assignment: Assignment for grape group',
            'Feedback: Assignment for grape group',
            'Course total'
        );
        // Check that general data is returned as expected.
        $this->assertEquals($csvpreview->get_previewdata(), $expecteddata);
        // Check that headers are returned as expected.
        $this->assertEquals($csvpreview->get_headers(), $expectedheaders);

        // Check that errors are being recorded.
        $csvpreview = new phpunit_gradeimport_csv_load_data();
        $csvpreview->load_csv_content($this->badtext, $encoding, $separator, $previewrows);
        // Columns shouldn't match.
        $this->assertEquals($csvpreview->get_error(), get_string('csvweirdcolumns', 'error'));
    }

    /**
     * Test fetching grade items for the course.
     */
    public function test_fetch_grade_items() {

        $gradeitemsarray = grade_item::fetch_all(array('courseid' => $this->courseid));
        $gradeitems = phpunit_gradeimport_csv_load_data::fetch_grade_items($this->courseid);

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

        $record = new stdClass();
        $record->itemid = 4;
        $record->newgradeitem = 25;
        $record->finalgrade = 62.00;
        $record->feedback = 'Some test feedback';

        $testobject = new phpunit_gradeimport_csv_load_data();
        $testobject->test_insert_grade_record($record, $user->id);

        $gradeimportvalues = $DB->get_records('grade_import_values');
        // Get the insert id.
        $key = key($gradeimportvalues);

        $testarray = array();
        $testarray[$key] = new stdClass();
        $testarray[$key]->id = $key;
        $testarray[$key]->itemid = $record->itemid;
        $testarray[$key]->newgradeitem = $record->newgradeitem;
        $testarray[$key]->userid = $user->id;
        $testarray[$key]->finalgrade = $record->finalgrade;
        $testarray[$key]->feedback = $record->feedback;
        $testarray[$key]->importcode = $testobject->get_importcode();
        $testarray[$key]->importer = $USER->id;

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
        $testobject = new phpunit_gradeimport_csv_load_data();

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
     * Check that the user matches a user in the system.
     */
    public function test_check_user_exists() {

        // Need to add one of the users into the system.
        $user = new stdClass();
        $user->firstname = 'Anne';
        $user->lastname = 'Able';
        $user->email = 'student7@mail.com';
        $userdetail = $this->getDataGenerator()->create_user($user);

        $testobject = new phpunit_gradeimport_csv_load_data();

        $testarray = $this->csv_load($this->oktext);

        $userfields = array('field' => 'email', 'label' => 'Email address');
        // If the user exists then the user id is returned.
        $userid = $testobject->test_check_user_exists($testarray[0][5] , $userfields);
        // Check that the user id returned matches with the user that we created.
        $this->assertEquals($userid, $userdetail->id);

        // Check for failure.
        // Try for an exception.
        $userfields = array('field' => 'id', 'label' => 'userid');
        $userid = $testobject->test_check_user_exists($testarray[0][0], $userfields);
        // Check that the userid is null.
        $this->assertNull($userid);

        // Expected error message.
        $mappingobject = new stdClass();
        $mappingobject->field = $userfields['label'];
        $mappingobject->value = $testarray[0][0];
        $expectederrormessage = get_string('usermappingerror', 'grades', $mappingobject);
        // Check that expected error message and actual message match.
        $gradebookerrors = $testobject->get_gradebookerrors();
        $this->assertEquals($expectederrormessage, $gradebookerrors[0]);

        // The field mapping is correct, but the student does not exist.
        $userid = $testobject->test_check_user_exists($testarray[1][5], $userfields);
        // Check that the userid is null.
        $this->assertNull($userid);

        // Expected error message.
        $mappingobject = new stdClass();
        $mappingobject->field = $userfields['label'];
        $mappingobject->value = $testarray[1][5];
        $expectederrormessage = get_string('usermappingerror', 'grades', $mappingobject);
        // Check that expected error message and actual message match.
        $gradebookerrors = $testobject->get_gradebookerrors();
        // This is the second error in the array of gradebook errors.
        $this->assertEquals($expectederrormessage, $gradebookerrors[1]);
    }

    /**
     * Test preparing feedback for inserting / updating into the gradebook.
     */
    public function test_create_feedback() {

        $testarray = $this->csv_load($this->oktext);
        $testobject = new phpunit_gradeimport_csv_load_data();

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
        $testobject = new phpunit_gradeimport_csv_load_data();

        // We're not using scales so no to this option.
        $verbosescales = 0;
        // Map and key are to retrieve the grade_item that we are updating.
        $map = array(1);
        $key = 0;
        // We return the new grade array for saving.
        $newgrades = $testobject->test_update_grade_item($this->courseid, $map, $key, $verbosescales, $testarray[0][6]);

        $expectedresult = array();
        $expectedresult[0] = new stdClass();
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
        $user = new stdClass();
        $user->firstname = 'Anne';
        $user->lastname = 'Able';
        $user->email = 'student7@mail.com';
        $userdetail = $this->getDataGenerator()->create_user($user);

        $testarray = $this->csv_load($this->oktext);
        $testobject = new phpunit_gradeimport_csv_load_data();

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

        $feedback = $testobject->test_map_user_data_with_value('feedback', $testarray[0][7], $this->columns, $map, $key,
                $this->courseid, $map[$key], $verbosescales);
        // Expected result.
        $resultarray = array();
        $resultarray[0] = new stdClass();
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
        $user = new stdClass();
        $user->firstname = 'Anne';
        $user->lastname = 'Able';
        $user->email = 'student7@mail.com';
        // Insert user 1.
        $this->getDataGenerator()->create_user($user);
        $user = new stdClass();
        $user->firstname = 'Bobby';
        $user->lastname = 'Bunce';
        $user->email = 'student5@mail.com';
        // Insert user 2.
        $this->getDataGenerator()->create_user($user);

        $this->csv_load($this->oktext);

        $importcode = 007;
        $verbosescales = 0;

        // Form data object.
        $formdata = new stdClass();
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
        $formdata->map = 1;
        $formdata->id = 2;
        $formdata->iid = $this->iid;
        $formdata->importcode = $importcode;

        // Blam go time.
        $testobject = new phpunit_gradeimport_csv_load_data();
        $dataloaded = $testobject->prepare_import_grade_data($this->columns, $formdata, $this->csvimport, $this->courseid, '', '',
                $verbosescales);
        // If everything inserted properly then this should be true.
        $this->assertTrue($dataloaded);
    }
}

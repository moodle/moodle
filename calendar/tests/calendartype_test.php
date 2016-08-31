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
 * This file contains the class that handles testing the calendar type system.
 *
 * @package core_calendar
 * @copyright 2013 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

// The test calendar type.
require_once($CFG->dirroot . '/calendar/tests/calendartype_test_example.php');

// Used to test the dateselector elements.
require_once($CFG->libdir . '/form/dateselector.php');
require_once($CFG->libdir . '/form/datetimeselector.php');

// Used to test the calendar/lib.php functions.
require_once($CFG->dirroot . '/calendar/lib.php');

// Used to test the user datetime profile field.
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->dirroot . '/user/profile/definelib.php');
require_once($CFG->dirroot . '/user/profile/index_field_form.php');

/**
 * Unit tests for the calendar type system.
 *
 * @package core_calendar
 * @copyright 2013 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.6
 */
class core_calendar_type_testcase extends advanced_testcase {

    /**
     * The test user.
     */
    private $user;

    /**
     * Test set up.
     */
    protected function setUp() {
        // The user we are going to test this on.
        $this->user = self::getDataGenerator()->create_user();
        self::setUser($this->user);
    }

    /**
     * Test that setting the calendar type works.
     */
    public function test_calendar_type_set() {
        // We want to reset the test data after this run.
        $this->resetAfterTest();

        // Test setting it as the 'Test' calendar type.
        $this->set_calendar_type('test_example');
        $this->assertEquals('test_example', \core_calendar\type_factory::get_calendar_type());

        // Test setting it as the 'Gregorian' calendar type.
        $this->set_calendar_type('gregorian');
        $this->assertEquals('gregorian', \core_calendar\type_factory::get_calendar_type());
    }

    /**
     * Test that calling core Moodle functions responsible for displaying the date
     * have the same results as directly calling the same function in the calendar type.
     */
    public function test_calendar_type_core_functions() {
        // We want to reset the test data after this run.
        $this->resetAfterTest();

        // Test that the core functions reproduce the same results as the Gregorian calendar.
        $this->core_functions_test('gregorian');

        // Test that the core functions reproduce the same results as the test calendar.
        $this->core_functions_test('test_example');
    }

    /**
     * Test that dates selected using the date selector elements are being saved as unixtime, and that the
     * unixtime is being converted back to a valid date to display in the date selector elements for
     * different calendar types.
     */
    public function test_calendar_type_dateselector_elements() {
        global $CFG;

        // We want to reset the test data after this run.
        $this->resetAfterTest();

        $this->setTimezone('UTC');

        // Note: this test is pretty useless because it does not test current user timezones.

        // Check converting dates to Gregorian when submitting a date selector element works. Note: the test
        // calendar is 2 years, 2 months, 2 days, 2 hours and 2 minutes ahead of the Gregorian calendar.
        $date1 = array();
        $date1['day'] = 4;
        $date1['month'] = 7;
        $date1['year'] = 2013;
        $date1['hour'] = 0;
        $date1['minute'] = 0;
        $date1['timestamp'] = 1372896000;
        $this->convert_dateselector_to_unixtime_test('dateselector', 'gregorian', $date1);

        $date2 = array();
        $date2['day'] = 7;
        $date2['month'] = 9;
        $date2['year'] = 2015;
        $date2['hour'] = 0; // The dateselector element does not have hours.
        $date2['minute'] = 0; // The dateselector element does not have minutes.
        $date2['timestamp'] = 1372896000;
        $this->convert_dateselector_to_unixtime_test('dateselector', 'test_example', $date2);

        $date3 = array();
        $date3['day'] = 4;
        $date3['month'] = 7;
        $date3['year'] = 2013;
        $date3['hour'] = 23;
        $date3['minute'] = 15;
        $date3['timestamp'] = 1372979700;
        $this->convert_dateselector_to_unixtime_test('datetimeselector', 'gregorian', $date3);

        $date4 = array();
        $date4['day'] = 7;
        $date4['month'] = 9;
        $date4['year'] = 2015;
        $date4['hour'] = 1;
        $date4['minute'] = 17;
        $date4['timestamp'] = 1372979700;
        $this->convert_dateselector_to_unixtime_test('datetimeselector', 'test_example', $date4);

        // The date selector element values are set by using the function usergetdate, here we want to check that
        // the unixtime passed is being successfully converted to the correct values for the calendar type.
        $this->convert_unixtime_to_dateselector_test('gregorian', $date3);
        $this->convert_unixtime_to_dateselector_test('test_example', $date4);
    }

    /**
     * Test that the user profile field datetime minimum and maximum year settings are saved as the
     * equivalent Gregorian years.
     */
    public function test_calendar_type_datetime_field_submission() {
        // We want to reset the test data after this run.
        $this->resetAfterTest();

        // Create an array with the input values and expected values once submitted.
        $date = array();
        $date['inputminyear'] = '1970';
        $date['inputmaxyear'] = '2013';
        $date['expectedminyear'] = '1970';
        $date['expectedmaxyear'] = '2013';
        $this->datetime_field_submission_test('gregorian', $date);

        // The test calendar is 2 years, 2 months, 2 days in the future, so when the year 1970 is submitted,
        // the year 1967 should be saved in the DB, as 1/1/1970 converts to 30/10/1967 in Gregorian.
        $date['expectedminyear'] = '1967';
        $date['expectedmaxyear'] = '2010';
        $this->datetime_field_submission_test('test_example', $date);
    }

    /**
     * Test all the core functions that use the calendar type system.
     *
     * @param string $type the calendar type we want to test
     */
    private function core_functions_test($type) {
        $this->set_calendar_type($type);

        // Get the calendar.
        $calendar = \core_calendar\type_factory::get_calendar_instance();

        // Test the userdate function.
        $this->assertEquals($calendar->timestamp_to_date_string($this->user->timecreated, '', 99, true, true),
            userdate($this->user->timecreated));

        // Test the calendar/lib.php functions.
        $this->assertEquals($calendar->get_weekdays(), calendar_get_days());
        $this->assertEquals($calendar->get_starting_weekday(), calendar_get_starting_weekday());
        $this->assertEquals($calendar->get_num_days_in_month('1986', '9'), calendar_days_in_month('9', '1986'));
        $this->assertEquals($calendar->get_next_month('1986', '9'), calendar_add_month('9', '1986'));
        $this->assertEquals($calendar->get_prev_month('1986', '9'), calendar_sub_month('9', '1986'));

        // Test the lib/moodle.php functions.
        $this->assertEquals($calendar->get_num_days_in_month('1986', '9'), days_in_month('9', '1986'));
        $this->assertEquals($calendar->get_weekday('1986', '9', '16'), dayofweek('16', '9', '1986'));
    }

    /**
     * Simulates submitting a form with a date selector element and tests that the chosen dates
     * are converted into unixtime before being saved in DB.
     *
     * @param string $element the form element we are testing
     * @param string $type the calendar type we want to test
     * @param array $date the date variables
     */
    private function convert_dateselector_to_unixtime_test($element, $type, $date) {
        $this->set_calendar_type($type);

        if ($element == 'dateselector') {
            $el = new MoodleQuickForm_date_selector('dateselector', null, array('timezone' => 0.0, 'step' => 1));
        } else {
            $el = new MoodleQuickForm_date_time_selector('dateselector', null, array('timezone' => 0.0, 'step' => 1));
        }
        $el->_createElements();
        $submitvalues = array('dateselector' => $date);

        $this->assertSame(array('dateselector' => $date['timestamp']), $el->exportValue($submitvalues, true));
    }

    /**
     * Test converting dates from unixtime to a date for the calendar type specified.
     *
     * @param string $type the calendar type we want to test
     * @param array $date the date variables
     */
    private function convert_unixtime_to_dateselector_test($type, $date) {
        $this->set_calendar_type($type);

        // Get the calendar.
        $calendar = \core_calendar\type_factory::get_calendar_instance();

        $usergetdate = $calendar->timestamp_to_date_array($date['timestamp'], 0.0);
        $comparedate = array(
            'minute' => $usergetdate['minutes'],
            'hour' => $usergetdate['hours'],
            'day' => $usergetdate['mday'],
            'month' => $usergetdate['mon'],
            'year' => $usergetdate['year'],
            'timestamp' => $date['timestamp']
        );

        $this->assertEquals($comparedate, $date);
    }

    /**
     * Test saving the minimum and max year settings for the user datetime field.
     *
     * @param string $type the calendar type we want to test
     * @param array $date the date variables
     */
    private function datetime_field_submission_test($type, $date) {
        $this->set_calendar_type($type);

        // Get the data we are submitting for the form.
        $formdata = array();
        $formdata['id'] = 0;
        $formdata['shortname'] = 'Shortname';
        $formdata['name'] = 'Name';
        $formdata['param1'] = $date['inputminyear'];
        $formdata['param2'] = $date['inputmaxyear'];

        // Mock submitting this.
        field_form::mock_submit($formdata);

        // Create the user datetime form.
        $form = new field_form(null, 'datetime');

        // Get the data from the submission.
        $submissiondata = $form->get_data();
        // On the user profile field page after get_data, the function define_save is called
        // in the field base class, which then calls the field's function define_save_preprocess.
        $field = new profile_define_datetime();
        $submissiondata = $field->define_save_preprocess($submissiondata);

        // Create an array we want to compare with the date passed.
        $comparedate = $date;
        $comparedate['expectedminyear'] = $submissiondata->param1;
        $comparedate['expectedmaxyear'] = $submissiondata->param2;

        $this->assertEquals($comparedate, $date);
    }

    /**
     * Set the calendar type for this user.
     *
     * @param string $type the calendar type we want to set
     */
    private function set_calendar_type($type) {
        $this->user->calendartype = $type;
        \core\session\manager::set_user($this->user);
    }
}

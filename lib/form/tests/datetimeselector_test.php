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
 * Unit tests for datetimeselector form element
 *
 * This file contains unit test related to datetimeselector form element
 *
 * @package    core_form
 * @category   phpunit
 * @copyright  2012 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/form/datetimeselector.php');
require_once($CFG->libdir.'/formslib.php');

/**
 * Form object to be used in test case
 */
class temp_form_datetime extends moodleform {
    /**
     * Form defination.
     */
    public function definition() {
        // No definition required.
    }
    /**
     * Returns form reference.
     * @return MoodleQuickForm
     */
    public function getform() {
        $mform = $this->_form;
        // set submitted flag, to simulate submission
        $mform->_flagSubmitted = true;
        return $mform;
    }
}

/**
 * Unit tests for MoodleQuickForm_date_time_selector
 *
 * Contains test cases for testing MoodleQuickForm_date_time_selector
 *
 * @package    core_form
 * @category   phpunit
 * @copyright  2012 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class datetimeselector_form_element_testcase extends basic_testcase {
    /** @var MoodleQuickForm Keeps reference of dummy form object */
    private $mform;
    /** @var stdClass saves current user data */
    private $olduser;
    /** @var int|float|string saves forcetimezone config variable */
    private $cfgforcetimezone;
    /** @var int|float|string saves current user timezone */
    private $userstimezone;
    /** @var string saves system locale */
    private $oldlocale;
    /** @var string saves system timezone */
    private $systemdefaulttimezone;
    /** @var array test fixtures */
    private $testvals;

    /**
     * Initalize test wide variable, it is called in start of the testcase
     */
    public function setUp() {
        // Get form data
        $form = new temp_form_datetime();
        $this->mform = $form->getform();

        // Set test values
        $this->testvals = array(
            array (
                'minute' => 0,
                'hour' => 0,
                'day' => 1,
                'month' => 7,
                'year' => 2011,
                'usertimezone' => 'America/Moncton',
                'timezone' => 'America/Moncton',
                'timestamp' => 1309489200
            ),
            array (
                'minute' => 0,
                'hour' => 0,
                'day' => 1,
                'month' => 7,
                'year' => 2011,
                'usertimezone' => 'America/Moncton',
                'timezone' => 99,
                'timestamp' => 1309489200
            ),
            array (
                'minute' => 0,
                'hour' => 23,
                'day' => 30,
                'month' => 6,
                'year' => 2011,
                'usertimezone' => 'America/Moncton',
                'timezone' => -4,
                'timestamp' => 1309489200
            ),
            array (
                'minute' => 0,
                'hour' => 23,
                'day' => 30,
                'month' => 6,
                'year' => 2011,
                'usertimezone' => -4,
                'timezone' => 99,
                'timestamp' => 1309489200
            ),
            array (
                'minute' => 0,
                'hour' => 0,
                'day' => 1,
                'month' => 7,
                'year' => 2011,
                'usertimezone' => 0.0,
                'timezone' => 0.0,
                'timestamp' => 1309478400 // 6am at UTC+0
            ),
            array (
                'minute' => 0,
                'hour' => 0,
                'day' => 1,
                'month' => 7,
                'year' => 2011,
                'usertimezone' => 0.0,
                'timezone' => 99,
                'timestamp' => 1309478400 // 6am at UTC+0
            )
        );
    }

    /**
     * Clears the data set in the setUp() method call.
     * @see datetimeselector_form_element_testcase::setUp()
     */
    public function tearDown() {
        unset($this->testvals);
    }

    /**
     * Testcase to check exportvalue
     */
    public function test_exportvalue() {
        global $USER;
        $testvals = $this->testvals;

        // Set timezone to Australia/Perth for testing.
        $this->settimezone();

        foreach ($testvals as $vals) {
            // Set user timezone to test value.
            $USER->timezone = $vals['usertimezone'];

            // Create dateselector element with different timezones.
            $elparams = array('optional'=>false, 'timezone' => $vals['timezone']);
            $el = new MoodleQuickForm_date_time_selector('dateselector', null, $elparams);
            $el->_createElements();
            $submitvalues = array('dateselector' => $vals);

            $this->assertSame($el->exportValue($submitvalues), array('dateselector' => $vals['timestamp']),
                    "Please check if timezones are updated (Site adminstration -> location -> update timezone)");
        }

        // Restore user orignal timezone.
        $this->restoretimezone();
    }

    /**
     * Testcase to check onQuickformEvent
     */
    public function test_onquickformevent() {
        global $USER;
        $testvals = $this->testvals;
        // Get dummy form for data
        $mform = $this->mform;
        // Set timezone to Australia/Perth for testing.
        $this->settimezone();
        foreach ($testvals as $vals) {
            // Set user timezone to test value.
            $USER->timezone = $vals['usertimezone'];

            // Create dateselector element with different timezones.
            $elparams = array('optional'=>false, 'timezone' => $vals['timezone']);
            $el = new MoodleQuickForm_date_time_selector('dateselector', null, $elparams);
            $el->_createElements();
            $expectedvalues = array(
                'day' => array($vals['day']),
                'month' => array($vals['month']),
                'year' => array($vals['year']),
                'hour' => array($vals['hour']),
                'minute' => array($vals['minute'])
                );
            $mform->_submitValues = array('dateselector' => $vals['timestamp']);
            $el->onQuickFormEvent('updateValue', null, $mform);
            $this->assertSame($el->getValue(), $expectedvalues);
        }

        // Restore user orignal timezone.
        $this->restoretimezone();
    }

    /**
     * Set user timezone to Australia/Perth for testing
     */
    private function settimezone() {
        global $USER, $CFG, $DB;
        $this->olduser = $USER;
        $USER = $DB->get_record('user', array('id'=>2)); //admin

        // Check if forcetimezone is set then save it and set it to use user timezone.
        $this->cfgforcetimezone = null;
        if (isset($CFG->forcetimezone)) {
            $this->cfgforcetimezone = $CFG->forcetimezone;
            $CFG->forcetimezone = 99; //get user default timezone.
        }

        // Store user default timezone to restore later.
        $this->userstimezone = $USER->timezone;

        // The string version of date comes from server locale setting and does
        // not respect user language, so it is necessary to reset that.
        $this->oldlocale = setlocale(LC_TIME, '0');
        setlocale(LC_TIME, 'en_AU.UTF-8');

        // Set default timezone to Australia/Perth, else time calculated
        // will not match expected values. Before that save system defaults.
        $this->systemdefaulttimezone = date_default_timezone_get();
        date_default_timezone_set('Australia/Perth');
    }

    /**
     * Restore user timezone to orignal state
     */
    private function restoretimezone() {
        global $USER, $CFG;
        // Restore user timezone back to what it was.
        $USER->timezone = $this->userstimezone;

        // Restore forcetimezone.
        if (!is_null($this->cfgforcetimezone)) {
            $CFG->forcetimezone = $this->cfgforcetimezone;
        }

        // Restore system default values.
        date_default_timezone_set($this->systemdefaulttimezone);
        setlocale(LC_TIME, $this->oldlocale);

        $USER = $this->olduser;
    }
}
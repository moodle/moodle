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
 * Unit tests for dateselector form element
 *
 * This file contains unit test related to dateselector form element
 *
 * @package    core_form
 * @category   phpunit
 * @copyright  2012 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/form/dateselector.php');
require_once($CFG->libdir.'/formslib.php');

/**
 * Unit tests for MoodleQuickForm_date_selector
 *
 * Contains test cases for testing MoodleQuickForm_date_selector
 *
 * @package    core_form
 * @category   phpunit
 * @copyright  2012 Rajesh Taneja
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_form_dateselector_testcase extends advanced_testcase {
    /** @var MoodleQuickForm Keeps reference of dummy form object */
    private $mform;
    /** @var array test fixtures */
    private $testvals;

    /**
     * Initalize test wide variable, it is called in start of the testcase
     */
    protected function setUp() {
        global $CFG;
        parent::setUp();

        $this->resetAfterTest();
        $this->setAdminUser();

        $this->setTimezone('Australia/Perth');

        // Get form data.
        $form = new temp_form_date();
        $this->mform = $form->getform();

        // Set test values.
        $this->testvals = array(
            array (
                'day' => 1,
                'month' => 7,
                'year' => 2011,
                'usertimezone' => 'America/Moncton',
                'timezone' => 'America/Moncton',
                'timestamp' => 1309489200
            ),
            array (
                'day' => 1,
                'month' => 7,
                'year' => 2011,
                'usertimezone' => 'America/Moncton',
                'timezone' => 99,
                'timestamp' => 1309489200
            ),
            array (
                'day' => 30,
                'month' => 6,
                'year' => 2011,
                'usertimezone' => 'America/Moncton',
                'timezone' => -4,
                'timestamp' => 1309406400
            ),
            array (
                'day' => 30,
                'month' => 6,
                'year' => 2011,
                'usertimezone' => -4,
                'timezone' => 99,
                'timestamp' => 1309406400
            ),
            array (
                'day' => 1,
                'month' => 7,
                'year' => 2011,
                'usertimezone' => 0.0,
                'timezone' => 0.0,
                'timestamp' => 1309478400 // 6am at UTC+0
            ),
            array (
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
     * Testcase to check exportvalue
     */
    public function test_exportvalue() {
        global $USER;
        $testvals = $this->testvals;

        foreach ($testvals as $vals) {
            // Set user timezone to test value.
            $USER->timezone = $vals['usertimezone'];

            // Create dateselector element with different timezones.
            $elparams = array('optional'=>false, 'timezone' => $vals['timezone']);
            $el = new MoodleQuickForm_date_selector('dateselector', null, $elparams);
            $el->_createElements();
            $submitvalues = array('dateselector' => $vals);

            $this->assertSame(array('dateselector' => $vals['timestamp']), $el->exportValue($submitvalues),
                    "Please check if timezones are updated (Site adminstration -> location -> update timezone)");
        }
    }

    /**
     * Testcase to check onQuickformEvent
     */
    public function test_onquickformevent() {
        global $USER;
        $testvals = $this->testvals;
        // Get dummy form for data.
        $mform = $this->mform;

        foreach ($testvals as $vals) {
            // Set user timezone to test value.
            $USER->timezone = $vals['usertimezone'];

            // Create dateselector element with different timezones.
            $elparams = array('optional'=>false, 'timezone' => $vals['timezone']);
            $el = new MoodleQuickForm_date_selector('dateselector', null, $elparams);
            $el->_createElements();
            $expectedvalues = array(
                'day' => array($vals['day']),
                'month' => array($vals['month']),
                'year' => array($vals['year'])
                );
            $mform->_submitValues = array('dateselector' => $vals['timestamp']);
            $el->onQuickFormEvent('updateValue', null, $mform);
            $this->assertSame($expectedvalues, $el->getValue());
        }
    }
}

/**
 * Form object to be used in test case.
 */
class temp_form_date extends moodleform {
    /**
     * Form definition.
     */
    public function definition() {
        // No definition required.
    }
    /**
     * Returns form reference
     * @return MoodleQuickForm
     */
    public function getform() {
        $mform = $this->_form;
        // set submitted flag, to simulate submission
        $mform->_flagSubmitted = true;
        return $mform;
    }
}

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
 * Calendar Ical unit tests
 *
 * @package    core_calendar
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;


/**
 * Unit tests for ical APIs.
 *
 * @package    core_calendar
 * @copyright  2013 Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.5
 */
class core_calendar_ical_testcase extends advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp() {
        global $CFG;
        require_once($CFG->dirroot . '/calendar/lib.php');
    }

    /**
     * @expectedException coding_exception
     */
    public function test_calendar_update_subscription() {
        $this->resetAfterTest(true);

        $subscription = new stdClass();
        $subscription->eventtype = 'site';
        $subscription->name = 'test';
        $id = calendar_add_subscription($subscription);

        $subscription = new stdClass();
        $subscription = calendar_get_subscription($id);
        $subscription->name = 'awesome';
        calendar_update_subscription($subscription);
        $sub = calendar_get_subscription($id);
        $this->assertEquals($subscription->name, $sub->name);

        $subscription = new stdClass();
        $subscription = calendar_get_subscription($id);
        $subscription->name = 'awesome2';
        $subscription->pollinterval = 604800;
        calendar_update_subscription($subscription);
        $sub = calendar_get_subscription($id);
        $this->assertEquals($subscription->name, $sub->name);
        $this->assertEquals($subscription->pollinterval, $sub->pollinterval);

        $subscription = new stdClass();
        $subscription->name = 'awesome4';
        calendar_update_subscription($subscription);
    }

    public function test_calendar_add_subscription() {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/lib/bennu/bennu.inc.php');

        $this->resetAfterTest(true);

        // Test for Microsoft Outlook 2010.
        $subscription = new stdClass();
        $subscription->name = 'Microsoft Outlook 2010';
        $subscription->importfrom = CALENDAR_IMPORT_FROM_FILE;
        $subscription->eventtype = 'site';
        $id = calendar_add_subscription($subscription);

        $calendar = file_get_contents($CFG->dirroot . '/lib/tests/fixtures/ms_outlook_2010.ics');
        $ical = new iCalendar();
        $ical->unserialize($calendar);
        $this->assertEquals($ical->parser_errors, array());

        $sub = calendar_get_subscription($id);
        $result = calendar_import_icalendar_events($ical, $sub->courseid, $sub->id);
        $count = $DB->count_records('event', array('subscriptionid' => $sub->id));
        $this->assertEquals($count, 1);

        // Test for OSX Yosemite.
        $subscription = new stdClass();
        $subscription->name = 'OSX Yosemite';
        $subscription->importfrom = CALENDAR_IMPORT_FROM_FILE;
        $subscription->eventtype = 'site';
        $id = calendar_add_subscription($subscription);

        $calendar = file_get_contents($CFG->dirroot . '/lib/tests/fixtures/osx_yosemite.ics');
        $ical = new iCalendar();
        $ical->unserialize($calendar);
        $this->assertEquals($ical->parser_errors, array());

        $sub = calendar_get_subscription($id);
        $result = calendar_import_icalendar_events($ical, $sub->courseid, $sub->id);
        $count = $DB->count_records('event', array('subscriptionid' => $sub->id));
        $this->assertEquals($count, 1);

        // Test for Google Gmail.
        $subscription = new stdClass();
        $subscription->name = 'Google Gmail';
        $subscription->importfrom = CALENDAR_IMPORT_FROM_FILE;
        $subscription->eventtype = 'site';
        $id = calendar_add_subscription($subscription);

        $calendar = file_get_contents($CFG->dirroot . '/lib/tests/fixtures/google_gmail.ics');
        $ical = new iCalendar();
        $ical->unserialize($calendar);
        $this->assertEquals($ical->parser_errors, array());

        $sub = calendar_get_subscription($id);
        $result = calendar_import_icalendar_events($ical, $sub->courseid, $sub->id);
        $count = $DB->count_records('event', array('subscriptionid' => $sub->id));
        $this->assertEquals($count, 1);
    }
}

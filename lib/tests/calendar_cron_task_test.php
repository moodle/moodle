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
 * Contains the class containing unit tests for the calendar cron task.
 *
 * @package   core
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/calendar/lib.php');

/**
 * Class containing unit tests for the calendar cron task.
 *
 * @package core
 * @copyright 2017 Mark Nelson <markn@moodle.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_calendar_cron_task_testcase extends advanced_testcase {

    /**
     * Tests set up
     */
    protected function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Test calendar cron task with a working subscription URL.
     */
    public function test_cron_working_url() {
        // ICal URL from external test repo.
        $subscriptionurl = $this->getExternalTestFileUrl('/ical.ics');

        $subscription = new stdClass();
        $subscription->eventtype = 'site';
        $subscription->name = 'test';
        $subscription->url = $subscriptionurl;
        $subscription->pollinterval = 86400;
        $subscription->lastupdated = 0;
        calendar_add_subscription($subscription);

        $this->expectOutputRegex('/Events imported: .* Events skipped: .* Events updated:/');
        $task = new \core\task\calendar_cron_task();
        $task->execute();
    }

    /**
     * Test calendar cron task with a broken subscription URL.
     */
    public function test_cron_broken_url() {
        $subscription = new stdClass();
        $subscription->eventtype = 'site';
        $subscription->name = 'test';
        $subscription->url = 'brokenurl';
        $subscription->pollinterval = 86400;
        $subscription->lastupdated = 0;
        calendar_add_subscription($subscription);

        $this->expectOutputRegex('/Error updating calendar subscription: The given iCal URL is invalid/');
        $task = new \core\task\calendar_cron_task();
        $task->execute();
    }
}

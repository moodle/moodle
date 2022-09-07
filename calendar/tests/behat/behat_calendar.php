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
 * Behat calendar-related steps definitions.
 *
 * @package    core_calendar
 * @category   test
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL used, this file may be required by behat before including /config.php.
require_once(__DIR__ . '/../../../lib/behat/behat_base.php');

use Behat\Gherkin\Node\TableNode as TableNode;

/**
 * Contains functions used by behat to test functionality.
 *
 * @package    core_calendar
 * @category   test
 * @copyright  2013 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_calendar extends behat_base {

    /**
     * Return the list of partial named selectors.
     *
     * @return array
     */
    public static function get_partial_named_selectors(): array {
        return [
            new behat_component_named_selector('mini calendar block', [".//*[@data-block='calendar_month']"]),
            new behat_component_named_selector('full calendar page', [".//*[@id='page-calendar-view']"]),
            new behat_component_named_selector('calendar day', [".//*[@data-region='day'][@data-day=%locator%]"]),
        ];
    }

    /**
     * Create event when starting on the front page.
     *
     * @Given /^I create a calendar event with form data:$/
     * @param TableNode $data
     */
    public function i_create_a_calendar_event_with_form_data($data) {
        // Go to current month page.
        $this->execute("behat_general::click_link", get_string('fullcalendar', 'calendar'));

        // Create event.
        $this->i_create_a_calendar_event($data);
    }

    /**
     * Create event.
     *
     * @Given /^I create a calendar event:$/
     * @param TableNode $data
     */
    public function i_create_a_calendar_event($data) {
        // Get the event name.
        $eventname = $data->getRow(1);
        $eventname = $eventname[1];

        $this->execute("behat_general::wait_until_the_page_is_ready");

        if ($this->running_javascript()) {
            // Click to create new event.
            $this->execute("behat_general::i_click_on", array(get_string('newevent', 'calendar'), "button"));

            // Set form fields.
            $this->execute("behat_forms::i_set_the_following_fields_to_these_values", $data);

            // Save event.
            $this->execute("behat_forms::press_button", get_string('save'));
        }
    }

    /**
     * Hover over a specific day in the mini-calendar.
     *
     * @Given /^I hover over day "(?P<dayofmonth>\d+)" of this month in the mini-calendar block$/
     * @param int $day The day of the current month
     */
    public function i_hover_over_day_of_this_month_in_mini_calendar_block(int $day): void {
        $this->execute("behat_general::i_hover_in_the",
            [$day, 'core_calendar > calendar day', '', 'core_calendar > mini calendar block']);
    }

    /**
     * Hover over a specific day in the full calendar page.
     *
     * @Given /^I hover over day "(?P<dayofmonth>\d+)" of this month in the full calendar page$/
     * @param int $day The day of the current month
     */
    public function i_hover_over_day_of_this_month_in_full_calendar_page(int $day): void {
        $this->execute("behat_general::i_hover_in_the",
            [$day, 'core_calendar > calendar day', '', 'core_calendar > full calendar page']);
    }

    /**
     * Hover over today in the mini-calendar.
     *
     * @Given /^I hover over today in the mini-calendar block$/
     */
    public function i_hover_over_today_in_mini_calendar_block(): void {
        // For window's compatibility, using %d and not %e.
        $todaysday = trim(strftime('%d'));
        $todaysday = ltrim($todaysday, '0');
        $this->i_hover_over_day_of_this_month_in_mini_calendar_block($todaysday);
    }

    /**
     * Hover over today in the calendar.
     *
     * @Given /^I hover over today in the calendar$/
     */
    public function i_hover_over_today_in_the_calendar() {
        // For window's compatibility, using %d and not %e.
        $todaysday = trim(strftime('%d'));
        $todaysday = ltrim($todaysday, '0');
        return $this->i_hover_over_day_of_this_month_in_calendar($todaysday);
    }

    /**
     * Navigate to a specific month in the calendar.
     *
     * @Given /^I view the calendar for "(?P<month>\d+)" "(?P<year>\d+)"$/
     * @param int $month the month selected as a number
     * @param int $year the four digit year
     */
    public function i_view_the_calendar_for($month, $year) {
        $this->view_the_calendar('month', 1, $month, $year);
    }

    /**
     * Navigate to a specific date in the calendar.
     *
     * @Given /^I view the calendar for "(?P<day>\d+)" "(?P<month>\d+)" "(?P<year>\d+)"$/
     * @param int $day the day selected as a number
     * @param int $month the month selected as a number
     * @param int $year the four digit year
     */
    public function i_view_the_calendar_day_view(int $day, int $month, int $year) {
        $this->view_the_calendar('day', $day, $month, $year);
    }

    /**
     * View the correct calendar view with specific day
     *
     * @param string $type type of calendar view: month or day
     * @param int $day the day selected as a number
     * @param int $month the month selected as a number
     * @param int $year the four digit year
     */
    private function view_the_calendar(string $type, int $day, int $month, int $year) {
        $time = make_timestamp($year, $month, $day);
        $this->execute('behat_general::i_visit', ['/calendar/view.php?view=' . $type . '&course=1&time=' . $time]);
    }

    /**
     * Navigate to site calendar.
     *
     * @Given /^I am viewing site calendar$/
     * @throws coding_exception
     * @return void
     */
    public function i_am_viewing_site_calendar() {
        $url = new moodle_url('/calendar/view.php', ['view' => 'month']);
        $this->execute('behat_general::i_visit', [$url]);
    }
}

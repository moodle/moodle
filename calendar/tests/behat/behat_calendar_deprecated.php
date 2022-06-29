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

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../lib/behat/behat_deprecated_base.php');

/**
 * Steps definitions that are now deprecated and will be removed in the next releases.
 *
 * This file only contains the steps that previously were in the behat_*.php files in the SAME DIRECTORY.
 * When deprecating steps from other components or plugins, create a behat_COMPONENT_deprecated.php
 * file in the same directory where the steps were defined.
 *
 * @package    core_calendar
 * @category   test
 * @copyright  2022 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_calendar_deprecated extends behat_deprecated_base {

    /**
     * Hover over a specific day in the calendar.
     *
     * @Given /^I hover over day "(?P<dayofmonth>\d+)" of this month in the calendar$/
     * @param int $day The day of the current month
     *
     * @deprecated since 4.0 MDL-72810. This tested the three-month calendar pseudo block, which has been removed.
     * @todo MDL-73117 This will be deleted in Moodle 4.4.
     */
    public function i_hover_over_day_of_this_month_in_calendar($day) {
        $this->deprecated_message('behat_calendar::i_hover_over_day_of_this_month_in_mini_calendar_block');

        $summarytitle = userdate(time(), get_string('strftimemonthyear'));
        // The current month table.
        $currentmonth = "table[descendant::*[self::caption[contains(concat(' ', normalize-space(.), ' '), ' {$summarytitle} ')]]]";

        // Strings for the class cell match.
        $cellclasses  = "contains(concat(' ', normalize-space(@class), ' '), ' day ')";
        $daycontains  = "text()[contains(concat(' ', normalize-space(.), ' '), ' {$day} ')]";
        $daycell      = "td[{$cellclasses}]";
        $dayofmonth   = "a[{$daycontains}]";

        $xpath = '//' . $currentmonth . '/descendant::' . $daycell . '/' . $dayofmonth;
        $this->execute("behat_general::i_hover", [$xpath, "xpath_element"]);
    }

    /**
     * Click a specific day in the calendar.
     *
     * @Given /^I click day "(?P<dayofmonth>\d+)" of this month in the calendar$/
     * @param int $day The day of the current month
     *
     * @deprecated since 4.0 MDL-72810. This tested the three-month calendar pseudo block, which has been removed.
     * @todo MDL-73117 This will be deleted in Moodle 4.4.
     */
    public function i_click_day_of_this_month_in_calendar($day) {
        $this->deprecated_message([
            'behat_general::i_click_on',
            'behat_caendar::i_hover_over_day_of_this_month_in_full_calendar_page',
        ]);

        $summarytitle = userdate(time(), get_string('strftimemonthyear'));
        // The current month table.
        $currentmonth = "table[descendant::*[self::caption[contains(concat(' ', normalize-space(.), ' '), ' {$summarytitle} ')]]]";

        // Strings for the class cell match.
        $cellclasses  = "contains(concat(' ', normalize-space(@class), ' '), ' day ')";
        $daycontains  = "text()[contains(concat(' ', normalize-space(.), ' '), ' {$day} ')]";
        $daycell      = "td[{$cellclasses}]";
        $dayofmonth   = "a[{$daycontains}]";

        $xpath = '//' . $currentmonth . '/descendant::' . $daycell . '/' . $dayofmonth;
        $this->execute("behat_general::wait_until_the_page_is_ready");
        $this->execute("behat_general::i_click_on", array($xpath, "xpath_element"));
        $this->execute("behat_general::wait_until_the_page_is_ready");
    }
}

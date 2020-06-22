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
 * Behat course-related step definition overrides for the IomadBootstrap theme.
 *
 * @package    theme_iomadbootstrap
 * @category   test
 * @copyright  2019 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// NOTE: no MOODLE_INTERNAL test here, this file may be required by behat before including /config.php.

require_once(__DIR__ . '/../../../../course/tests/behat/behat_course.php');

/**
 * Course-related step definition overrides for the IomadBootstrap theme.
 *
 * @package    theme_iomadbootstrap
 * @category   test
 * @copyright  2019 Michael Hawkins
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_iomadbootstrap_behat_course extends behat_course {

    /**
     * Go to the course participants.
     */
    public function i_navigate_to_course_participants() {
        $coursestr = behat_context_helper::escape(get_string('courses'));
        $mycoursestr = behat_context_helper::escape(get_string('mycourses'));
        $xpath = "//div[contains(@class,'block')]//li[p/*[string(.)=$coursestr or string(.)=$mycoursestr]]";
        $this->execute('behat_general::i_click_on_in_the', [get_string('participants'), 'link', $xpath, 'xpath_element']);
    }
}

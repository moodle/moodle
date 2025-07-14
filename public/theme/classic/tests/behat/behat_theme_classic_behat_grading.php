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

require_once(__DIR__ . '/../../../../grade/grading/tests/behat/behat_grading.php');

/**
 * Step definitions related to blocks overrides for the Classic theme.
 *
 * @package    theme_classic
 * @category   test
 * @copyright  2021 Mathew May
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_classic_behat_grading extends behat_grading {
    /**
     * Goes to the selected advanced grading page. You should be in the course page when this step begins.
     *
     * @param string $activityname
     */
    public function i_go_to_advanced_grading_page($activityname) {

        $this->execute("behat_general::i_click_on_in_the", [$this->escape($activityname), 'link', 'page', 'region']);

        $this->execute('behat_navigation::i_navigate_to_in_current_page_administration',
            get_string('gradingmanagement', 'grading'));
    }
}

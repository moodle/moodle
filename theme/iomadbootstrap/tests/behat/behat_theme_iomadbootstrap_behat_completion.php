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

require_once(__DIR__ . '/../../../../completion/tests/behat/behat_completion.php');

/**
 * Step definitions related to blocks overrides for the Classic theme.
 *
 * @package    theme_iomadbootstrap
 * @category   test
 * @copyright  2022 Derick Turner
 * @author    Derick Turner
 * @based on theme_clean by Mathew May
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class behat_theme_iomadbootstrap_behat_completion extends behat_completion {
    /**
     * Goes to the current course activity completion report.
     */
    public function go_to_the_current_course_activity_completion_report() {
        $completionnode = get_string('pluginname', 'report_progress');
        $reportsnode = get_string('reports');

        $this->execute("behat_navigation::i_navigate_to_in_current_page_administration",
            $reportsnode . ' > ' . $completionnode);
    }
}

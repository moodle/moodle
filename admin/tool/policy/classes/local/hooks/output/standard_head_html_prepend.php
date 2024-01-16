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

namespace tool_policy\local\hooks\output;

/**
 * Allows plugins to add any elements to the page <head> html tag
 *
 * @package    tool_policy
 * @copyright  2023 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class standard_head_html_prepend {

    /**
     * Load policy message for guests.
     *
     * @param \core\hook\output\standard_head_html_prepend $hook
     */
    public static function callback(\core\hook\output\standard_head_html_prepend $hook): void {
        global $CFG, $PAGE, $USER;

        if (!empty($CFG->sitepolicyhandler)
                && $CFG->sitepolicyhandler == 'tool_policy'
                && empty($USER->policyagreed)
                && (isguestuser() || !isloggedin())) {
            $output = $PAGE->get_renderer('tool_policy');
            try {
                $page = new \tool_policy\output\guestconsent();
                $hook->add_html($output->render($page));
                // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
            } catch (\dml_read_exception $e) {
                // During upgrades, the new plugin code with new SQL could be in place but the DB not upgraded yet.
            }
        }
    }
}

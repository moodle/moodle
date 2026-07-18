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
 * Library functions for Student Activity Dashboard.
 *
 * @package    local_student_dashboard
 * @copyright  2026 Antigravity
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * Extends the custom navigation node to add Student Dashboard
 *
 * @param global_navigation $navigation The global navigation object
 */
function local_student_dashboard_extend_navigation(global_navigation $navigation) {
    if (isloggedin() && !isguestuser()) {
        $url = new moodle_url('/local/student_dashboard/index.php');
        $navigation->add(
            get_string('pluginname', 'local_student_dashboard'),
            $url,
            navigation_node::TYPE_SETTING,
            null,
            'local_student_dashboard',
            new pix_icon('i/dashboard', '')
        );
    }
}

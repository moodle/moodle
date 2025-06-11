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
 * Plugin lib for block_lsu_people
 *
 * Adds Course Roster link to course admin navigation
 *
 * @package    block_lsu_people
 * @copyright  2025 onwards Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extend course settings navigation with Course Roster link.
 *
 * @param navigation_node $settingsnode
 * @param stdClass $course
 * @param context_course $context
 */
function block_lsu_people_extend_navigation_course($settingsnode, $course, $context) {
    global $PAGE;

    // Only show to users with permission.
    if (!has_capability('block/lsu_people:view', $context)) {
        return;
    }

    // Only add link under Users section of course admin.
    if ($usersnode = $settingsnode->get('users')) {

        $label = get_string('courseroster', 'block_lsu_people');
        $url = new \moodle_url('/blocks/lsu_people/view.php', ['id' => $course->id]);

        // Dummy icon to avoid nav-missing-icon.
        $icon = new \pix_icon('spacer', '', 'moodle');

        // Add the link to the users node.
        $node = $usersnode->add(
            $label,
            $url,
            \navigation_node::TYPE_CONTAINER,
            null,
            'block_lsu_people_roster',
            $icon
        );

        $node->add_class('fa fa-paw');
    }
}

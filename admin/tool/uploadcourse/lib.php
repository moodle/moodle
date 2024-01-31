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
 * Plugin callbacks for tool_uploadcourse.
 *
 * @package     tool_uploadcourse
 * @copyright   2019 Marina Glancy
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Extends the navigation of the category admin menu with the upload courses link.
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param context $coursecategorycontext The context of the course category
 */
function tool_uploadcourse_extend_navigation_category_settings(navigation_node $navigation, context $coursecategorycontext): void {
    if (has_capability('tool/uploadcourse:use', $coursecategorycontext)) {
        $title = get_string('uploadcourses', 'tool_uploadcourse');
        $path = new moodle_url('/admin/tool/uploadcourse/index.php', ['categoryid' => $coursecategorycontext->instanceid]);
        $settingsnode = navigation_node::create(
            $title,
            $path,
            navigation_node::TYPE_SETTING,
            null,
            null,
            new pix_icon('i/course', ''));
        $navigation->add_node($settingsnode);
    }
}

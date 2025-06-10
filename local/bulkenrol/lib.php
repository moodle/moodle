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
 * Local plugin "bulkenrol" - Library
 *
 * @package   local_bulkenrol
 * @copyright 2017 Soon Systems GmbH on behalf of Alexander Bias, Ulm University <alexander.bias@uni-ulm.de>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('LOCALBULKENROL_NAV_COURSE', 'navcourse');
define('LOCALBULKENROL_NAV_PARTICIPANTS', 'navpart');
define('LOCALBULKENROL_NAV_BOTH', 'navboth');

/**
 * This function extends the course navigation with the bulkenrol item
 *
 * @param navigation_node $navigation The navigation node to extend
 * @param stdClass $course The course to object for the report
 * @param stdClass $context The context of the course
 */
function local_bulkenrol_extend_navigation_course($navigation, $course, $context) {
    if (has_capability('local/bulkenrol:enrolusers', $context)) {
        // Create the navigation node.
        $url = new moodle_url('/local/bulkenrol/index.php', ['id' => $course->id]);
        $bulkenrolnode = navigation_node::create(get_string('pluginname', 'local_bulkenrol'), $url,
                navigation_node::TYPE_SETTING, null, 'local_bulkenrol', new pix_icon('i/users', ''));

        // Get the navigation node placement setting.
        $navigationplacement = get_config('local_bulkenrol', 'navigation');

        // If the admin wanted to add the navigation node to the participants page jump menu.
        if ($navigationplacement == LOCALBULKENROL_NAV_PARTICIPANTS || $navigationplacement == LOCALBULKENROL_NAV_BOTH) {
            $usersnode = $navigation->get('users');
            if (isset($bulkenrolnode) && !empty($usersnode)) {
                $usersnode->add_node($bulkenrolnode);
            }
        }

        // If the admin wanted to add the navigation node to the course navigation.
        if ($navigationplacement == LOCALBULKENROL_NAV_COURSE || $navigationplacement == LOCALBULKENROL_NAV_BOTH) {
            if (isset($bulkenrolnode)) {
                $navigation->add_node($bulkenrolnode);
            }
        }
    }
}

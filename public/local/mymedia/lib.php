<?php
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
 * Kaltura my media library script
 *
 * @package    local_mymedia
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

define('LOCAL_KALTURAMYMEDIA_LINK_LOCATION_TOP_NAVIGATION_MENU', 0);
define('LOCAL_KALTURAMYMEDIA_LINK_LOCATION_SIDE_NAVIGATION_MENU', 1);
define('LOCAL_KALTURAMYMEDIA_LINK_LOCATION_USER_NAVIGATION_MENU', 2);

/**
 * This function adds my media links to the navigation block
 * @param global_navigation $navigation a global_navigation object
 * @return void
 */
function local_mymedia_extend_navigation($navigation) {
    global $USER, $DB, $PAGE, $CFG;

    if (empty($USER->id)) {
        return;
    }

    // When on the admin-index page, first check if the capability exists.
    // This is to cover the edge case on the Plugins check page, where a check for the capability is performed before the capability has been added to the Moodle mdl_capabilities
    // table.
    if ('admin-index' === $PAGE->pagetype) {
        $exists = $DB->record_exists('capabilities', array('name' => 'local/mymedia:view'));

        if (!$exists) {
            return;
        }
    }

    $context = context_user::instance($USER->id);

    if (!has_capability('local/mymedia:view', $context, $USER)) {
        return;
    }

    $linkLocation = get_config('local_mymedia', 'link_location');
    $mymediaString = get_string('nav_mymedia', 'local_mymedia');
    $mymediaUrl = '/local/mymedia/mymedia.php';
    $fullMenuItem = "\n$mymediaString|$mymediaUrl";

    // handle link placement based on link_location configuration
    switch ($linkLocation) {
        case LOCAL_KALTURAMYMEDIA_LINK_LOCATION_SIDE_NAVIGATION_MENU:
            // add to side navigation
            $nodehome = $navigation->get('home');
            if (empty($nodehome)){
                $nodehome = $navigation;
            }
            $icon = new pix_icon('my-media', '', 'local_mymedia');
            $nodemymedia = $nodehome->add($mymediaString, new moodle_url($mymediaUrl), navigation_node::NODETYPE_LEAF, $mymediaString, 'mymedia', $icon);
            $nodemymedia->showinflatnavigation = true;
            break;
        case LOCAL_KALTURAMYMEDIA_LINK_LOCATION_USER_NAVIGATION_MENU:
            // add menu item to user menu if it does not already exist
            if (strpos($CFG->customusermenuitems, $mymediaString) === false) {
                $CFG->customusermenuitems .= $fullMenuItem;
            }
            break;
        case LOCAL_KALTURAMYMEDIA_LINK_LOCATION_TOP_NAVIGATION_MENU:
        default:
            // add menu item to top navigation menu if it does not already exist
            if (strpos($CFG->custommenuitems, $mymediaString) === false) {
                $CFG->custommenuitems .= $fullMenuItem;
            }
            break;
    }
}

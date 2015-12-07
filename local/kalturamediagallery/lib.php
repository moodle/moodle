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
 * Kaltura media gallery lib script.
 *
 * @package    local_kalturamediagallery
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

/**
 * This function adds Kaltura media gallery link to the navigation block.  The code ensures that the Kaltura media gallery link is only displayed in the 'Current courses'
 * menu true.  In addition it check if the current context is one that is below the course context.
 * @param global_navigation $navigation a global_navigation object
 * @return void
 */
function local_kalturamediagallery_extend_navigation($navigation) {
    global $USER, $PAGE, $DB;

    if (empty($USER->id)) {
        return;
    }

    // When on the admin-index page, first check if the capability exists.
    // This is to cover the edge case on the Plugins check page, where a check for the capability is performed before the capability has been added to the Moodle mdl_capabilities
    // table.
    if ('admin-index' === $PAGE->pagetype) {
        $exists = $DB->record_exists('capabilities', array('name' => 'local/kalturamediagallery:view'));

        if (!$exists) {
            return;
        }
    }

    // Check the current page context.  If the context is not of a course or module then we are in another area of Moodle and return void.
    $context = context::instance_by_id($PAGE->context->id);
    $isvalidcontext = ($context instanceof context_course || $context instanceof context_module) ? true : false;
    if (!$isvalidcontext) {
        return;
    }

    // If the context if a module then get the parent context.
    $coursecontext = null;
    if ($context instanceof context_module) {
        $coursecontext = $context->get_course_context();
    } else {
        $coursecontext = $context;
    }

    $mycoursesnode = $navigation->find('currentcourse', $navigation::TYPE_ROOTNODE);

    if (empty($mycoursesnode) || !has_capability('local/kalturamediagallery:view', $coursecontext, $USER)) {
        return;
    }

    $name = get_string('nav_mediagallery', 'local_kalturamediagallery');
    $url = new moodle_url('/local/kalturamediagallery/index.php', array('courseid' => $coursecontext->instanceid));
    $kalmedgalnode = $mycoursesnode->add($name, $url, navigation_node::NODETYPE_LEAF, $name, 'kalcrsgal');
}
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
 * External user API
 *
 * @package   core_user
 * @copyright 2009 Moodle Pty Ltd (http://moodle.com)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('USER_FILTER_ENROLMENT', 1);
define('USER_FILTER_GROUP', 2);
define('USER_FILTER_LAST_ACCESS', 3);
define('USER_FILTER_ROLE', 4);
define('USER_FILTER_STATUS', 5);
define('USER_FILTER_STRING', 6);

/**
 * Return a list of page types
 * @param string $pagetype current page type
 * @param stdClass $parentcontext Block's parent context
 * @param stdClass $currentcontext Current context of block
 * @return array
 */
function user_page_type_list($pagetype, $parentcontext, $currentcontext) {
    return array('user-profile' => get_string('page-user-profile', 'pagetype'));
}

/**
 * Callback for inplace editable API.
 *
 * @param string $itemtype - Only user_roles is supported.
 * @param string $itemid - Courseid and userid separated by a :
 * @param string $newvalue - json encoded list of roleids.
 * @return \core\output\inplace_editable|null
 */
function core_user_inplace_editable($itemtype, $itemid, $newvalue) {
    if ($itemtype === 'user_roles') {
        return \core_user\output\user_roles_editable::update($itemid, $newvalue);
    }
}

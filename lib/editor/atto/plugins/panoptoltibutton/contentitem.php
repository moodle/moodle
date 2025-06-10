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
 * Handle sending a user to a tool provider to initiate a content-item selection.
 *
 * @package    atto_panoptoltibutton
 * @copyright  2020 Panopto
 * @author     Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../../config.php');
require_once(dirname(__FILE__) . '/lib/panopto_lti_utility.php');
require_once(dirname(__FILE__) . '/../../../../../mod/lti/lib.php');
require_once(dirname(__FILE__) . '/../../../../../mod/lti/locallib.php');


$id = required_param('id', PARAM_INT);
$courseid = required_param('course', PARAM_INT);
$callback = required_param('callback', PARAM_ALPHANUMEXT);

// Check access and capabilities.
$course = get_course($courseid);
require_login($course);
$context = context_course::instance($courseid);

// Students will access this tool for the student submission workflow. Assume student can submit an assignment?
if (!\panopto_lti_utility::panoptoltibutton_is_active_user_enrolled($context)) {
    require_capability('moodle/course:manageactivities', $context);
    require_capability('mod/lti:addcoursetool', $context);
}

// Set the return URL. We send the launch container along to help us avoid
// frames-within-frames when the user returns.
$returnurlparams = [
    'course' => $course->id,
    'id' => $id,
    'sesskey' => sesskey(),
    'callback' => $callback,
];
$returnurl = new \moodle_url('/lib/editor/atto/plugins/panoptoltibutton/contentitem_return.php', $returnurlparams);

// Prepare the request.
$request = lti_build_content_item_selection_request(
    $id, $course, $returnurl, '', '', [], [],
    false, false, false, false, false
);

// Get the launch HTML.
$content = lti_post_launch_html($request->params, $request->url, false);

echo $content;
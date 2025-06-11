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
 * @package    tiny_panoptoltibutton
 * @copyright  2023 Panopto
 * @author     Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../../../config.php');
require_once($CFG->dirroot . '/blocks/panopto/lib/lti/panoptoblock_lti_utility.php');
require_once($CFG->dirroot . '/mod/lti/lib.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');

$id = required_param('id', PARAM_INT);
$courseid = required_param('course', PARAM_INT);
$callback = required_param('callback', PARAM_ALPHANUMEXT);

/**
 * LTI Tiny path.
 */
const LTI_TINY_PATH = '/lib/editor/tiny/plugins/panoptoltibutton/contentitem_return.php';

// Check access and capabilities.
$course = get_course($courseid);
require_login($course);

// LTI 1.3 login request.
$config = lti_get_type_type_config($id);

if ($config->lti_ltiversion === LTI_VERSION_1P3) {
    $lti = null;
    $isthismoodle41 = empty($CFG->version) ? false : $CFG->version >= 2022112800.00;
    if ($isthismoodle41) {
        // Moodle 4.1 needs LTI object.
        $lti = new stdClass();

        // Give it some random id, this is not used in the code but will create a PHP notice if not provided.
        $ltiviewerurl = new moodle_url(LTI_TINY_PATH);
        $resourcelinkid = sha1($ltiviewerurl->out(false) .
            '&' . $courseid .
            '&' . $course->timecreated
        );
        $lti->id = $resourcelinkid;
    }
    if (!isset($SESSION->lti_initiatelogin_status)) {
        echo lti_initiate_login($courseid, "tiny_panoptoltibutton, {$callback}", $lti, $config);
        exit;
    }
}

$context = context_course::instance($courseid);

// Set the return URL. We send the launch container along to help us avoid
// frames-within-frames when the user returns.
$returnurlparams = [
    'course' => $course->id,
    'id' => $id,
    'sesskey' => sesskey(),
    'callback' => $callback,
];

$returnurl = new \moodle_url(LTI_TINY_PATH, $returnurlparams);

// Prepare the request.
$request = lti_build_content_item_selection_request(
    $id, $course, $returnurl, '', '', [], [], false, false, false, false, false
);

// Get the launch HTML.
$content = lti_post_launch_html($request->params, $request->url, false);

echo $content;

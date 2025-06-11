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
 * LTI launch script for the Panopto Student Submission module.
 *
 * @package mod_panoptosubmission
 * @copyright  Panopto 2021
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/mod/lti/lib.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/mod/lti/locallib.php');
require_once(dirname(__FILE__) . '/locallib.php');
require_once($CFG->dirroot . '/blocks/panopto/lib/lti/panoptoblock_lti_utility.php');

$courseid = required_param('courseid', PARAM_INT);

/**
 * @var string Student Submission path.
 */
const STUDENT_SUBMISSION_PATH = '/mod/panoptosubmission/contentitem_return.php';

// Check access and capabilities.
$course = get_course($courseid);
require_login($course);

$toolid = \panoptoblock_lti_utility::get_course_tool_id($courseid, 'panopto_student_submission_tool');

// If no lti tool exists then we can not continue.
if (is_null($toolid)) {
    throw new moodle_exception('no_existing_lti_tools', 'panoptosubmission');
    return;
}

// LTI 1.3 login request.
$isthismoodle41 = empty($CFG->version) ? false : $CFG->version >= 2022112800.00;
$config = lti_get_type_type_config($toolid);

if ($config->lti_ltiversion === LTI_VERSION_1P3) {
    $lti = null;
    if ($isthismoodle41) {
        // Moodle 4.1 needs LTI object.
        $lti = new stdClass();

        // Give it some random id, this is not used in the code but will create a PHP notice if not provided.
        $ltiviewerurl = new moodle_url(STUDENT_SUBMISSION_PATH);
        $resourcelinkid = sha1($ltiviewerurl->out(false) .
            '&' . $courseid .
            '&' . $course->timecreated
        );
        $lti->id = $resourcelinkid;
    }
    if (!isset($SESSION->lti_initiatelogin_status)) {
        echo lti_initiate_login($courseid, "mod_panoptosubmission", $lti, $config);
        exit;
    } else {
        unset($SESSION->lti_initiatelogin_status);
    }
}

// Set the return URL. We send the launch container along to help us avoid
// frames-within-frames when the user returns.
$returnurlparams = [
    'course' => $course->id,
    'id' => $toolid,
    'sesskey' => sesskey(),
];

$returnurl = new \moodle_url(STUDENT_SUBMISSION_PATH, $returnurlparams);

// Prepare the request.
$request = lti_build_content_item_selection_request(
    $toolid, $course, $returnurl, '', '', [], [],
    false, false, false, false, false
);

// Get the launch HTML.
$content = lti_post_launch_html($request->params, $request->url, false);

echo $content;

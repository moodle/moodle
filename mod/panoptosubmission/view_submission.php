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
 * This file launches LTI-tools enabled to be launched from a rich text editor
 *
 * @package    mod_panoptosubmission
 * @copyright  2021 Panopto
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/blocks/panopto/lib/lti/panoptoblock_lti_utility.php');
require_once(dirname(__FILE__). '/locallib.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/mod/lti/lib.php');
require_once(dirname(dirname(dirname(__FILE__))) . '/mod/lti/locallib.php');

$courseid = required_param('course', PARAM_INT);
$contenturl = urldecode(optional_param('contenturl', '', PARAM_URL));
$customdata = urldecode(optional_param('custom', '', PARAM_RAW_TRIMMED));
$resourcelinkid = optional_param('resourcelinkid', '', PARAM_RAW_TRIMMED);

$course = get_course($courseid);

$context = context_course::instance($courseid);

if (empty($resourcelinkid)) {
    $ltiviewerurl = new moodle_url("/mod/panoptosubmission/view_submission.php");
    $resourcelinkid = sha1($ltiviewerurl->out(false) .
        '&' . $courseid .
        '&' . $course->timecreated
    );
}

require_login($course);

// Provision the course if we can.
if (panoptosubmission_verify_panopto($courseid)) {
    // Get a matching LTI tool for the course.
    $toolid = \panoptoblock_lti_utility::get_course_tool_id($courseid, 'panopto_student_submission_tool');

    if (is_null($toolid)) {
        throw new moodle_exception('no_existing_lti_tools', 'panoptosubmission');
        return;
    }
} else {
    // If we were unable to provision the course, we cannot continue.
    return;
}

$lti = new stdClass();

// Give it some random id, this is not used in the code but will create a PHP notice if not provided.
$lti->id = $resourcelinkid;
$lti->typeid = $toolid;
$lti->launchcontainer = LTI_LAUNCH_CONTAINER_WINDOW;
$lti->toolurl = $contenturl;
$lti->custom = new stdClass();
$lti->instructorcustomparameters = [];
$lti->debuglaunch = false;
if ($customdata) {
    $decoded = json_decode($customdata, true);

    foreach ($decoded as $key => $value) {
        $lti->custom->$key = $value;
    }
}

// LTI 1.3 login request.
$config = lti_get_type_type_config($toolid);
if ($config->lti_ltiversion === LTI_VERSION_1P3) {
    if (!isset($SESSION->lti_initiatelogin_status)) {
        echo lti_initiate_login(
            $courseid,
            "mod_panoptosubmission,'',{$toolid},{$resourcelinkid},{$contenturl},{$customdata}",
            $lti,
            $config
        );
        exit;
    } else {
        unset($SESSION->lti_initiatelogin_status);
    }
}

echo \panoptoblock_lti_utility::launch_tool($lti);

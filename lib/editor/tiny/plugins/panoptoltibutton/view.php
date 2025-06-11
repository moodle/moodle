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
 * @package    tiny_panoptoltibutton
 * @copyright  2023 Panopto
 * @author     Panopto with contributions from Adis Hrnjica
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// @codingStandardsIgnoreLine
global $DB, $CFG, $COURSE;
if (empty($CFG)) {
    // @codingStandardsIgnoreLine
    require_once(dirname(__FILE__) . '/../../../../../config.php');
}
require_once($CFG->dirroot . '/blocks/panopto/lib/block_panopto_lib.php');
require_once($CFG->libdir .'/accesslib.php'); // Access control functions.
require_once($CFG->dirroot . '/mod/lti/lib.php');
require_once($CFG->dirroot . '/mod/lti/locallib.php');
require_once($CFG->dirroot . '/blocks/panopto/lib/lti/panoptoblock_lti_utility.php');

$configuredserverarray = panopto_get_configured_panopto_servers();

$contenturl = optional_param('contenturl', '', PARAM_URL);

$contentverified = false;

if ($contenturl) {
    foreach ($configuredserverarray as $possibleserver) {
        $contenthost = parse_url($contenturl, PHP_URL_HOST);

        if (stripos($contenthost, $possibleserver) !== false) {
            $contentverified = true;
            break;
        }
    }
} else {
    $contentverified = true;
}

if ($contentverified) {
    $resourcelinkid = required_param('resourcelinkid', PARAM_ALPHANUMEXT);
    $ltitypeid = required_param('ltitypeid', PARAM_INT);
    $customdata = optional_param('custom', '', PARAM_RAW_TRIMMED);

    require_login();

    // Make sure $ltitypeid is valid.
    $ltitype = $DB->get_record('lti_types', ['id' => $ltitypeid], '*', MUST_EXIST);

    $lti = new stdClass();

    // Try to detect if we are viewing content from an iframe nested in course, get the Id param if it exists.
    $courseid = 0;
    if (!empty($_SERVER['HTTP_REFERER']) && (strpos($_SERVER['HTTP_REFERER'], "/course/view.php") !== false)) {
        $components = parse_url($_SERVER['HTTP_REFERER']);
        parse_str($components['query'], $results);

        if (!empty($results['id'])) {
            $lti->course = $results['id'];
            $course = $DB->get_record('course', array('id' => $results['id']), '*', MUST_EXIST);
            $courseid = $course->id;
            $context = context_course::instance($results['id']);
            $PAGE->set_context($context);
            require_login($course, true);
        }
    }

    $lti->id = $resourcelinkid;
    $lti->typeid = $ltitypeid;
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
    $config = lti_get_type_type_config($ltitypeid);
    if ($config->lti_ltiversion === LTI_VERSION_1P3) {
        if (!isset($SESSION->lti_initiatelogin_status)) {
            echo lti_initiate_login($courseid,
                "tiny_panoptoltibutton,'',{$ltitypeid},{$resourcelinkid},{$contenturl},{$customdata}",
                $lti,
                $config
            );
            exit;
        }
    }

    echo \panoptoblock_lti_utility::launch_tool($lti);
} else {
    echo get_string('invalid_content_host', 'tiny_panoptoltibutton');
}

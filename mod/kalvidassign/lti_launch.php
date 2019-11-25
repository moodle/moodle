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
 * Kaltura video assignment LTI launch script.
 *
 * @package    mod_kalvidassign
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/local/kaltura/locallib.php');
require_once(dirname(__FILE__).'/locallib.php');

global $USER;

require_login();
$courseid = required_param('courseid', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);
$height = required_param('height', PARAM_INT);
$width = required_param('width', PARAM_INT);
$withblocks = optional_param('withblocks', 0, PARAM_INT);
$source = optional_param('source', '', PARAM_URL);

$context = context_course::instance($courseid);
require_capability('mod/kalvidassign:submit', $context);
$course = get_course($courseid);

$launch = array();
$launch['id'] = 1;
$launch['cmid'] = $cmid;
$launch['title'] = 'Kaltura video assignment';
$launch['module'] = KAF_BROWSE_EMBED_MODULE;
$launch['course'] = $course;
$launch['width'] = $width;
$launch['height'] = $height;
$launch['custom_publishdata'] = '';

$source = $source = local_kaltura_add_kaf_uri_token($source);

if (!$cm = get_coursemodule_from_id('kalvidassign', $cmid)) {
    print_error('invalidcoursemodule');
}

if (!$kalvidassignobj = $DB->get_record('kalvidassign', array('id' => $cm->instance))) {
    print_error('invalidid', 'kalvidassign');
}

$submissionParams = array('vidassignid' => $kalvidassignobj->id, 'userid' => $USER->id);
$submission = $DB->get_record('kalvidassign_submission', $submissionParams);

if (false === local_kaltura_url_contains_configured_hostname($source) && !empty($source)) {
    echo get_string('invalid_source_parameter', 'mod_kalvidres');
    die;
} else {
    $launch['source'] = urldecode($source);
}

$isResubmit = !empty($submission->entry_id) || !empty($submission->timecreated);
$isExpired = kalvidassign_assignemnt_submission_expired($kalvidassignobj);
$isReplaceMediaDisabled = $isExpired || !$kalvidassignobj->resubmit;

if ($isResubmit && $isReplaceMediaDisabled && empty($source)) {
    echo get_string('notallowedtoreplacemedia', 'mod_kalvidassign');
    die;
}
if (!empty(get_config(KALTURA_PLUGIN_NAME, 'enable_assignment'))) {
    $launch['assignment'] = 'yes';
}
if (local_kaltura_validate_browseembed_required_params($launch)) {
    $content = local_kaltura_request_lti_launch($launch, $withblocks);
    echo $content;
} else {
    echo get_string('invalid_launch_parameters', 'mod_kalvidassign');
}

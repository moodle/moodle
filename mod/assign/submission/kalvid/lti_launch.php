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
 * Kaltura video submission LTI launch script.
 *
 * @package    assignsubmission_kalvid
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  2025 Kaltura Inc
 */

require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/config.php');
require_once(dirname(dirname(dirname(dirname(dirname(__FILE__))))).'/local/kaltura/locallib.php');

global $USER;

require_login();
$courseid = required_param('courseid', PARAM_INT);
$cmid = required_param('cmid', PARAM_INT);
$height = required_param('height', PARAM_INT);
$width = required_param('width', PARAM_INT);
$withblocks = optional_param('withblocks', 0, PARAM_INT);
$source = optional_param('source', '', PARAM_URL);

$context = context_course::instance($courseid);
$course = get_course($courseid);

$launch = array();
$launch['id'] = 1;
$launch['cmid'] = $cmid;
$launch['title'] = 'Kaltura video submission';
$launch['module'] = KAF_BROWSE_EMBED_MODULE;
$launch['course'] = $course;
$launch['width'] = $width;
$launch['height'] = $height;
$launch['custom_publishdata'] = '';

$source = local_kaltura_add_kaf_uri_token($source);

if (!$cm = get_coursemodule_from_id('assign', $cmid)) {
    throw new \moodle_exception('invalidcoursemodule');
}

if (!$kalvidassignobj = $DB->get_record('assign', array('id' => $cm->instance))) {
    throw new \moodle_exception('invalidid', 'assignsubmission_kalvid');
}

$submissionParams = array('vidassignid' => $kalvidassignobj->id, 'userid' => $USER->id);
$submission = $DB->get_record('kalvidassign_submission', $submissionParams);

if (false === local_kaltura_url_contains_configured_hostname($source) && !empty($source)) {
    echo get_string('invalid_source_parameter', 'assignsubmission_kalvid');
    die;
} else {
    $launch['source'] = urldecode($source);
}

if (!empty(get_config(KALTURA_PLUGIN_NAME, 'enable_submission'))) {
    $launch['submission'] = 'yes';
}
if (local_kaltura_validate_browseembed_required_params($launch)) {
    $content = local_kaltura_request_lti_launch($launch, $withblocks);
    echo $content;
} else {
    echo get_string('invalid_launch_parameters', 'assignsubmission_kalvid');
}

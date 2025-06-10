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
 * Kaltura filter plug-in LTI launch page.
 *
 * @package    filter_kaltura
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');
require_once(dirname(dirname(dirname(__FILE__))).'/local/kaltura/locallib.php');

global $SITE;

require_login();

$courseid = required_param('courseid', PARAM_INT);
$height = required_param('height', PARAM_INT);
$width = required_param('width', PARAM_INT);
$withblocks = optional_param('withblocks', 0, PARAM_INT);
$source = optional_param('source', '', PARAM_URL);

// If a course id of zero is passed, then we must be in the system context.
if (0 != $courseid) {
    $context = context_course::instance($courseid);
} else {
    $context = context_system::instance();
}

// Check if we're in a course context.
if ($context instanceof context_course) {
    $course = get_course($courseid);

    // Check if the user has the capability to view comments in Moodle.
    if (!has_capability('moodle/comment:view', $context)) {
        echo get_string('nocapabilitytousethisservice', 'error');
        die();
    }
} else {
    $course = $SITE;
}

$launch = array();
$launch['id'] = 1;
$launch['cmid'] = 0;
$launch['title'] = 'Kaltura video resource';
$launch['module'] = KAF_BROWSE_EMBED_MODULE;
$launch['course'] = $course;
$launch['width'] = $width;
$launch['height'] = $height;
$launch['custom_publishdata'] = '';

if (false === local_kaltura_url_contains_configured_hostname($source) && !empty($source)) {
    echo get_string('invalid_source_parameter', 'mod_kalvidres');
    die;
} else {
    $launch['source'] = urldecode($source);
}

if (local_kaltura_validate_browseembed_required_params($launch)) {
    $content = local_kaltura_request_lti_launch($launch, $withblocks);
    echo $content;
} else {
    echo get_string('invalid_launch_parameters', 'mod_kalvidres');
}
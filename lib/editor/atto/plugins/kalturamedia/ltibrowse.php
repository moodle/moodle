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
 * Kaltura media LTI launch page.
 *
 * @package    tinymce_kalturamedia
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once(dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))).'/config.php');
require_once($CFG->dirroot.'/local/kaltura/locallib.php');
//require_once('renderer.php');

global $PAGE, $USER;

require_login();

$contextid = required_param('contextid', PARAM_INT);
$height = required_param('height', PARAM_INT);
$width = required_param('width', PARAM_INT);
$withblocks = optional_param('withblocks', 0, PARAM_INT);

$context = context::instance_by_id($contextid);

$launch = array();
$course = 0;

if ($context instanceof context_course) {
    $course = get_course($context->instanceid);

} else if ($context instanceof context_system || $context instanceof context_coursecat) {
    $course = get_course(1);
} else {
    // Find parent context
    $parentcontexts = $context->get_parent_contexts(false);

    foreach ($parentcontexts as $ctx) {
        if ($ctx instanceof context_course) {
            $course = get_course($ctx->instanceid);
            break;
        } else if ($ctx instanceof context_system || $ctx instanceof context_coursecat) {
            $course = get_course(1);
            break;
        }
    }
}

$launch['id'] = 1;
$launch['cmid'] = 0;
$launch['title'] = 'Kaltura media';
$launch['module'] = KAF_BROWSE_EMBED_MODULE;
$launch['course'] = $course;
$launch['width'] = $width;
$launch['height'] = $height;
$launch['custom_publishdata'] = '';

if (local_kaltura_validate_browseembed_required_params($launch)) {
    $content = local_kaltura_request_lti_launch($launch, $withblocks, $editor = 'atto');
    echo $content;
} else {
    echo get_string('invalid_launch_parameters', 'mod_kalvidres');
}

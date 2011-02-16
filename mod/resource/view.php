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
 * Resource module version information
 *
 * @package    mod
 * @subpackage resource
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require('../../config.php');
require_once($CFG->dirroot.'/mod/resource/locallib.php');
require_once($CFG->libdir.'/completionlib.php');

$id       = optional_param('id', 0, PARAM_INT); // Course Module ID
$r        = optional_param('r', 0, PARAM_INT);  // Resource instance ID
$redirect = optional_param('redirect', 0, PARAM_BOOL);

if ($r) {
    if (!$resource = $DB->get_record('resource', array('id'=>$r))) {
        resource_redirect_if_migrated($r, 0);
        print_error('invalidaccessparameter');
    }
    $cm = get_coursemodule_from_instance('resource', $resource->id, $resource->course, false, MUST_EXIST);

} else {
    if (!$cm = get_coursemodule_from_id('resource', $id)) {
        resource_redirect_if_migrated(0, $id);
        print_error('invalidcoursemodule');
    }
    $resource = $DB->get_record('resource', array('id'=>$cm->instance), '*', MUST_EXIST);
}

$course = $DB->get_record('course', array('id'=>$cm->course), '*', MUST_EXIST);

require_course_login($course, true, $cm);
$context = get_context_instance(CONTEXT_MODULE, $cm->id);
require_capability('mod/resource:view', $context);

add_to_log($course->id, 'resource', 'view', 'view.php?id='.$cm->id, $resource->id, $cm->id);

// Update 'viewed' state if required by completion system
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

$PAGE->set_url('/mod/resource/view.php', array('id' => $cm->id));

if ($resource->tobemigrated) {
    resource_print_tobemigrated($resource, $cm, $course);
    die;
}

$fs = get_file_storage();
$files = $fs->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false); // TODO: this is not very efficient!!
if (count($files) < 1) {
    resource_print_filenotfound($resource, $cm, $course);
    die;
} else {
    $file = reset($files);
    unset($files);
}

if ($redirect) {
    // coming from course page or url index page
    // this redirect trick solves caching problems when tracking views ;-)
    $path = '/'.$context->id.'/mod_resource/content/'.$resource->revision.$file->get_filepath().$file->get_filename();
    $fullurl = file_encode_url($CFG->wwwroot.'/pluginfile.php', $path, false);
    redirect($fullurl);
}

$resource->mainfile = $file->get_filename();
switch (resource_get_final_display_type($resource)) {
    case RESOURCELIB_DISPLAY_EMBED:
        resource_display_embed($resource, $cm, $course, $file);
        break;
    case RESOURCELIB_DISPLAY_FRAME:
        resource_display_frame($resource, $cm, $course, $file);
        break;
    default:
        resource_print_workaround($resource, $cm, $course, $file);
        break;
}


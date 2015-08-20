<?php
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
 * Kaltura video resource view page.
 *
 * @package    mod_kalvidres
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

$id = optional_param('id', 0, PARAM_INT);

// Retrieve module instance.
if (empty($id)) {
    print_error('invalidid', 'kalvidres');
}

if (!empty($id)) {

    if (!$cm = get_coursemodule_from_id('kalvidres', $id)) {
        print_error('invalidcoursemodule');
    }

    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }

    if (!$kalvidres = $DB->get_record('kalvidres', array("id" => $cm->instance))) {
        print_error('invalidid', 'kalvidres');
    }
}

require_course_login($course->id, true, $cm);

global $SESSION, $CFG;

$PAGE->set_url('/mod/kalvidres/view.php', array('id' => $id));
$PAGE->set_title(format_string($kalvidres->name));
$PAGE->set_heading($course->fullname);
$pageclass = 'kaltura-kalvidres-body';
$PAGE->add_body_class($pageclass);

$context = $PAGE->context;

$event = \mod_kalvidres\event\video_resource_viewed::create(array(
    'objectid' => $kalvidres->id,
    'context' => context_module::instance($cm->id)
));
$event->trigger();

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

echo $OUTPUT->header();

$description = format_module_intro('kalvidres', $kalvidres, $cm->id);
if (!empty($description)) {
    echo $OUTPUT->box_start('generalbox');
    echo $description;
    echo $OUTPUT->box_end();
}

$renderer = $PAGE->get_renderer('mod_kalvidres');

// Require a YUI module to make the object tag be as large as possible.
$params = array(
    'bodyclass' => $pageclass,
    'lastheight' => null,
    'padding' => 15,
    'width' => $kalvidres->width,
    'height' => $kalvidres->height
);
$PAGE->requires->yui_module('moodle-local_kaltura-lticontainer', 'M.local_kaltura.init', array($params), null, true);

echo $renderer->display_iframe($kalvidres, $course->id);

echo $OUTPUT->footer();

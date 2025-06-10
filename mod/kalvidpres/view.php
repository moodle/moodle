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
 * Kaltura video presentation view page.
 *
 * @package    mod_kalvidpres
 * @author     Remote-Learner.net Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  (C) 2014 Remote Learner.net Inc http://www.remote-learner.net
 */

require_once(dirname(dirname(dirname(__FILE__))).'/config.php');

$id = optional_param('id', 0, PARAM_INT);

// Retrieve module instance.
if (empty($id)) {
    print_error('invalidid', 'kalvidpres');
}

if (!empty($id)) {

    if (!$cm = get_coursemodule_from_id('kalvidpres', $id)) {
        print_error('invalidcoursemodule');
    }

    if (!$course = $DB->get_record('course', array('id' => $cm->course))) {
        print_error('coursemisconf');
    }

    if (!$kalvidpres = $DB->get_record('kalvidpres', array("id" => $cm->instance))) {
        print_error('invalidid', 'kalvidpres');
    }
}

require_course_login($course->id, true, $cm);

global $SESSION, $CFG;

$PAGE->set_url('/mod/kalvidpres/view.php', array('id' => $id));
$PAGE->set_title(format_string($kalvidpres->name));
$PAGE->set_heading($course->fullname);
$pageclass = 'kaltura-kalvidpres-body';
$PAGE->add_body_class($pageclass);

$context = $PAGE->context;

$event = \mod_kalvidpres\event\video_resource_viewed::create(array(
    'objectid' => $kalvidpres->id,
    'context' => context_module::instance($cm->id)
));
$event->trigger();

$completion = new completion_info($course);
$completion->set_module_viewed($cm);

echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('mod_kalvidpres');

echo $OUTPUT->box_start('generalbox');

echo format_module_intro('kalvidpres', $kalvidpres, $cm->id);

echo $OUTPUT->box_end();

// Require a YUI module to make the object tag be as large as possible.
$params = array(
    'bodyclass' => $pageclass,
    'lastheight' => null,
    'padding' => 15,
    'kalvidwidth' => $kalvidpres->width,
    'width' => $kalvidpres->width,
    'height' => $kalvidpres->height,
);
$PAGE->requires->yui_module('moodle-local_kaltura-lticontainer', 'M.local_kaltura.init', array($params), null, true);

echo $renderer->display_iframe($kalvidpres, $course->id);

echo $OUTPUT->footer();

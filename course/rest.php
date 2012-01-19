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
 * Provide interface for topics AJAX course formats
 *
 * @copyright 1999 Martin Dougiamas  http://dougiamas.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package course
 */

require_once('../config.php');
require_once($CFG->dirroot.'/course/lib.php');

// Initialise ALL the incoming parameters here, up front.
$courseid   = required_param('courseId', PARAM_INT);
$class      = required_param('class', PARAM_ALPHA);
$field      = optional_param('field', '', PARAM_ALPHA);
$instanceid = optional_param('instanceId', 0, PARAM_INT);
$sectionid  = optional_param('sectionId', 0, PARAM_INT);
$beforeid   = optional_param('beforeId', 0, PARAM_INT);
$value      = optional_param('value', 0, PARAM_INT);
$column     = optional_param('column', 0, PARAM_ALPHA);
$id         = optional_param('id', 0, PARAM_INT);
$summary    = optional_param('summary', '', PARAM_RAW);
$sequence   = optional_param('sequence', '', PARAM_SEQUENCE);
$visible    = optional_param('visible', 0, PARAM_INT);
$pageaction = optional_param('action', '', PARAM_ALPHA); // Used to simulate a DELETE command

$PAGE->set_url('/course/rest.php', array('courseId'=>$courseid,'class'=>$class));

// Authorise the user and verify some incoming data
if (!$course = $DB->get_record('course', array('id'=>$courseid))) {
    error_log('AJAX commands.php: Course does not exist');
    die;
}

$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_login($course);
require_capability('moodle/course:update', $context);

if (empty($CFG->enablecourseajax)) {
    error_log('Course AJAX not allowed');
    die;
}

require_sesskey();

// OK, now let's process the parameters and do stuff
// MDL-10221 the DELETE method is not allowed on some web servers, so we simulate it with the action URL param
$requestmethod = $_SERVER['REQUEST_METHOD'];
if ($pageaction == 'DELETE') {
    $requestmethod = 'DELETE';
}

switch($requestmethod) {
    case 'POST':

        switch ($class) {
            case 'block':

                switch ($field) {
                    case 'visible':
                        blocks_execute_action($PAGE, $pageblocks, 'toggle', $blockinstance);
                        break;

                    case 'position':  // Misleading case. Should probably call it 'move'.
                        // We want to move the block around. This means changing
                        // the column (position field) and/or block sort order
                        // (weight field).
                        blocks_move_block($PAGE, $blockinstance, $column, $value);
                        break;
                }
                break;

            case 'section':

                if (!$DB->record_exists('course_sections', array('course'=>$course->id, 'section'=>$id))) {
                    error_log('AJAX commands.php: Bad Section ID '.$id);
                    die;
                }

                switch ($field) {
                    case 'visible':
                        set_section_visible($course->id, $id, $value);
                        break;

                    case 'move':
                        move_section_to($course, $id, $value);
                        break;
                }
                rebuild_course_cache($course->id);
                break;

            case 'resource':
                if (!$cm = get_coursemodule_from_id('', $id, $course->id)) {
                    error_log('AJAX commands.php: Bad course module ID '.$id);
                    die;
                }
                switch ($field) {
                    case 'visible':
                        set_coursemodule_visible($cm->id, $value);
                        break;

                    case 'groupmode':
                        set_coursemodule_groupmode($cm->id, $value);
                        break;

                    case 'indentleft':
                        if ($cm->indent > 0) {
                            $cm->indent--;
                            $DB->update_record('course_modules', $cm);
                        }
                        break;

                    case 'indentright':
                        $cm->indent++;
                        $DB->update_record('course_modules', $cm);
                        break;

                    case 'move':
                        if (!$section = $DB->get_record('course_sections', array('course'=>$course->id, 'section'=>$sectionid))) {
                            error_log('AJAX commands.php: Bad section ID '.$sectionid);
                            die;
                        }

                        if ($beforeid > 0){
                            $beforemod = get_coursemodule_from_id('', $beforeid, $course->id);
                            $beforemod = $DB->get_record('course_modules', array('id'=>$beforeid));
                        } else {
                            $beforemod = NULL;
                        }

                        if (debugging('',DEBUG_DEVELOPER)) {
                            error_log(serialize($beforemod));
                        }

                        moveto_module($cm, $section, $beforemod);
                        break;
                }
                rebuild_course_cache($course->id);
                break;

            case 'course':
                switch($field) {
                    case 'marker':
                        course_set_marker($course->id, $value);
                        break;
                }
                break;
        }
        break;

    case 'DELETE':
        switch ($class) {
            case 'block':
                blocks_execute_action($PAGE, $pageblocks, 'delete', $blockinstance);
                break;

            case 'resource':
                if (!$cm = get_coursemodule_from_id('', $id, $course->id)) {
                    error_log('AJAX rest.php: Bad course module ID '.$id);
                    die;
                }
                $modlib = "$CFG->dirroot/mod/$cm->modname/lib.php";

                if (file_exists($modlib)) {
                    include_once($modlib);
                } else {
                    error_log("Ajax rest.php: This module is missing mod/$cm->modname/lib.php");
                    die;
                }
                $deleteinstancefunction = $cm->modname."_delete_instance";

                // Run the module's cleanup funtion.
                if (!$deleteinstancefunction($cm->instance)) {
                    error_log("Ajax rest.php: Could not delete the $cm->modname $cm->name (instance)");
                    die;
                }

                $modcontext = get_context_instance(CONTEXT_MODULE, $cm->id);

                // remove all module files in case modules forget to do that
                $fs = get_file_storage();
                $fs->delete_area_files($modcontext->id);

                if (!delete_course_module($cm->id)) {
                    error_log("Ajax rest.php: Could not delete the $cm->modname $cm->name (coursemodule)");
                }
                // Remove the course_modules entry.
                if (!delete_mod_from_section($cm->id, $cm->section)) {
                    error_log("Ajax rest.php: Could not delete the $cm->modname $cm->name from section");
                }

                // Trigger a mod_deleted event with information about this module.
                $eventdata = new stdClass();
                $eventdata->modulename = $cm->modname;
                $eventdata->cmid       = $cm->id;
                $eventdata->courseid   = $course->id;
                $eventdata->userid     = $USER->id;
                events_trigger('mod_deleted', $eventdata);

                rebuild_course_cache($course->id);

                add_to_log($courseid, "course", "delete mod",
                           "view.php?id=$courseid",
                           "$cm->modname $cm->instance", $cm->id);
                break;
        }
        break;
}



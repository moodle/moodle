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
 * This script lists all the instances of questionnaire in a particular course
 *
 * @package    mod
 * @subpackage questionnaire
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


require_once("../../config.php");
require_once($CFG->dirroot.'/mod/questionnaire/locallib.php');

$id = required_param('id', PARAM_INT);
$PAGE->set_url('/mod/questionnaire/index.php', array('id' => $id));
if (! $course = $DB->get_record('course', array('id' => $id))) {
    print_error('incorrectcourseid', 'questionnaire');
}
$coursecontext = context_course::instance($id);
require_login($course->id);
$PAGE->set_pagelayout('incourse');

$event = \mod_questionnaire\event\course_module_instance_list_viewed::create(array(
                'context' => context_course::instance($course->id)));
$event->trigger();

// Print the header.
$strquestionnaires = get_string("modulenameplural", "questionnaire");
$PAGE->navbar->add($strquestionnaires);
$PAGE->set_title("$course->shortname: $strquestionnaires");
$PAGE->set_heading(format_string($course->fullname));
echo $OUTPUT->header();

// Get all the appropriate data.
if (!$questionnaires = get_all_instances_in_course("questionnaire", $course)) {
    notice(get_string('thereareno', 'moodle', $strquestionnaires), "../../course/view.php?id=$course->id");
    die;
}

// Check if we need the closing date header.
$showclosingheader = false;
foreach ($questionnaires as $questionnaire) {
    if ($questionnaire->closedate != 0) {
        $showclosingheader = true;
    }
    if ($showclosingheader) {
        break;
    }
}

// Configure table for displaying the list of instances.
$headings = array(get_string('name'));
$align = array('left');

if ($showclosingheader) {
    array_push($headings, get_string('questionnairecloses', 'questionnaire'));
    array_push($align, 'left');
}

array_unshift($headings, get_string('sectionname', 'format_'.$course->format));
array_unshift($align, 'left');

$showing = '';

// Current user role == admin or teacher.
if (has_capability('mod/questionnaire:viewsingleresponse', $coursecontext)) {
    array_push($headings, get_string('responses', 'questionnaire'));
    array_push($align, 'center');
    $showing = 'stats';
    array_push($headings, get_string('realm', 'questionnaire'));
    array_push($align, 'left');
    // Current user role == student.
} else if (has_capability('mod/questionnaire:submit', $coursecontext)) {
    array_push($headings, get_string('status'));
    array_push($align, 'left');
    $showing = 'responses';
}

$table = new html_table();
$table->head = $headings;
$table->align = $align;

// Populate the table with the list of instances.
$currentsection = '';
foreach ($questionnaires as $questionnaire) {
    $cmid = $questionnaire->coursemodule;
    $data = array();
    $realm = $DB->get_field('questionnaire_survey', 'realm', array('id' => $questionnaire->sid));
    // Template surveys should NOT be displayed as an activity to students.
    if (!($realm == 'template' && !has_capability('mod/questionnaire:manage', context_module::instance($cmid)))) {
        // Section number if necessary.
        $strsection = '';
        if ($questionnaire->section != $currentsection) {
            $strsection = get_section_name($course, $questionnaire->section);
            $currentsection = $questionnaire->section;
        }
        $data[] = $strsection;
        // Show normal if the mod is visible.
        $class = '';
        if (!$questionnaire->visible) {
            $class = ' class="dimmed"';
        }
        $data[] = "<a$class href=\"view.php?id=$cmid\">$questionnaire->name</a>";

        // Close date.
        if ($questionnaire->closedate) {
            $data[] = userdate($questionnaire->closedate);
        } else if ($showclosingheader) {
            $data[] = '';
        }

        if ($showing == 'responses') {
            $status = '';
            if ($responses = questionnaire_get_user_responses($questionnaire->sid, $USER->id, $complete = false)) {
                foreach ($responses as $response) {
                    if ($response->complete == 'y') {
                        $status .= get_string('submitted', 'questionnaire').' '.userdate($response->submitted).'<br />';
                    } else {
                        $status .= get_string('attemptstillinprogress', 'questionnaire').' '.
                            userdate($response->submitted).'<br />';
                    }
                }
            }
            $data[] = $status;
        } else if ($showing == 'stats') {
            $data[] = $DB->count_records('questionnaire_response', array('survey_id' => $questionnaire->sid, 'complete' => 'y'));
            if ($survey = $DB->get_record('questionnaire_survey', array('id' => $questionnaire->sid))) {
                // For a public questionnaire, look for the original public questionnaire that it is based on.
                if ($survey->realm == 'public') {
                    $strpreview = get_string('preview_questionnaire', 'questionnaire');
                    if ($survey->courseid != $course->id) {
                        $publicoriginal = '';
                        $originalcourse = $DB->get_record('course', ['id' => $survey->courseid]);
                        $originalcoursecontext = context_course::instance($survey->courseid);
                        $originalquestionnaire = $DB->get_record('questionnaire',
                            ['sid' => $survey->id, 'course' => $survey->courseid]);
                        $cm = get_coursemodule_from_instance("questionnaire", $originalquestionnaire->id, $survey->courseid);
                        $context = context_course::instance($survey->courseid, MUST_EXIST);
                        $canvieworiginal = has_capability('mod/questionnaire:preview', $context, $USER->id, true);
                        // If current user can view questionnaires in original course,
                        // provide a link to the original public questionnaire.
                        if ($canvieworiginal) {
                            $publicoriginal = '<br />'.get_string('publicoriginal', 'questionnaire').'&nbsp;'.
                                '<a href="'.$CFG->wwwroot.'/mod/questionnaire/preview.php?id='.
                                $cm->id.'" title="'.$strpreview.']">'.$originalquestionnaire->name.' ['.
                                $originalcourse->fullname.']</a>';
                        } else {
                            // If current user is not enrolled as teacher in original course,
                            // only display the original public questionnaire's name and course name.
                            $publicoriginal = '<br />'.get_string('publicoriginal', 'questionnaire').'&nbsp;'.
                                $originalquestionnaire->name.' ['.$originalcourse->fullname.']';
                        }
                        $data[] = get_string($realm, 'questionnaire').' '.$publicoriginal;
                    } else {
                        // Original public questionnaire was created in current course.
                        // Find which courses it is used in.
                        $publiccopy = '';
                        $select = 'course != '.$course->id.' AND sid = '.$questionnaire->sid;
                        if ($copies = $DB->get_records_select('questionnaire', $select, null,
                                $sort = 'course ASC', $fields = 'id, course, name')) {
                            foreach ($copies as $copy) {
                                $copycourse = $DB->get_record('course', array('id' => $copy->course));
                                $select = 'course = '.$copycourse->id.' AND sid = '.$questionnaire->sid;
                                $copyquestionnaire = $DB->get_record('questionnaire',
                                    array('id' => $copy->id, 'sid' => $survey->id, 'course' => $copycourse->id));
                                $cm = get_coursemodule_from_instance("questionnaire", $copyquestionnaire->id, $copycourse->id);
                                $context = context_course::instance($copycourse->id, MUST_EXIST);
                                $canviewcopy = has_capability('mod/questionnaire:view', $context, $USER->id, true);
                                if ($canviewcopy) {
                                    $publiccopy .= '<br />'.get_string('publiccopy', 'questionnaire').'&nbsp;:&nbsp;'.
                                        '<a href = "'.$CFG->wwwroot.'/mod/questionnaire/preview.php?id='.
                                        $cm->id.'" title = "'.$strpreview.'">'.
                                        $copyquestionnaire->name.' ['.$copycourse->fullname.']</a>';
                                } else {
                                    // If current user does not have "view" capability in copy course,
                                    // only display the copied public questionnaire's name and course name.
                                    $publiccopy .= '<br />'.get_string('publiccopy', 'questionnaire').'&nbsp;:&nbsp;'.
                                        $copyquestionnaire->name.' ['.$copycourse->fullname.']';
                                }
                            }
                        }
                        $data[] = get_string($realm, 'questionnaire').' '.$publiccopy;
                    }
                } else {
                    $data[] = get_string($realm, 'questionnaire');
                }
            } else {
                // If a questionnaire is a copy of a public questionnaire which has been deleted.
                $data[] = get_string('removenotinuse', 'questionnaire');
            }
        }
    }
    $table->data[] = $data;
} // End of loop over questionnaire instances.

echo html_writer::table($table);

// Finish the page.
echo $OUTPUT->footer();
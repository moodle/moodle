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
 * Version information
 *
 * @package    mod
 * @subpackage choicegroup
 * @copyright  2013 Université de Lausanne
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->libdir . '/completionlib.php');

$id         = required_param('id', PARAM_INT);                 // Course Module ID
$action     = optional_param('action', '', PARAM_ALPHA);
$userids    = optional_param_array('userid', array(), PARAM_INT); // array of attempt ids for delete action
$notify     = optional_param('notify', '', PARAM_ALPHA);

$url = new moodle_url('/mod/choicegroup/view.php', array('id'=>$id));
if ($action !== '') {
    $url->param('action', $action);
}
$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('choicegroup', $id)) {
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
    print_error('coursemisconf');
}

require_login($course, false, $cm);
$PAGE->requires->js_call_amd('mod_choicegroup/choicegroupdatadisplay', 'init');
if (!$choicegroup = choicegroup_get_choicegroup($cm->instance)) {
    print_error('invalidcoursemodule');
}
$choicegroup_groups = choicegroup_get_groups($choicegroup);
$choicegroup_users = array();

$strchoicegroup = get_string('modulename', 'choicegroup');
$strchoicegroups = get_string('modulenameplural', 'choicegroup');

if (!$context = context_module::instance($cm->id)) {
    print_error('badcontext');
}

$eventparams = array(
    'context' => $context,
    'objectid' => $choicegroup->id
);

$current = choicegroup_get_user_answer($choicegroup, $USER);
if ($action == 'delchoicegroup' and confirm_sesskey() and is_enrolled($context, NULL, 'mod/choicegroup:choose') and $choicegroup->allowupdate and !($choicegroup->timeclose and (time() > $choicegroup->timeclose))) {
    // user wants to delete his own choice:
    if ($current !== false) {
        if (groups_is_member($current->id, $USER->id)) {
            $currentgroup = $DB->get_record('groups', array('id' => $current->id), 'id,name', MUST_EXIST);
            groups_remove_member($current->id, $USER->id);
            $event = \mod_choicegroup\event\choice_removed::create($eventparams);
            $event->add_record_snapshot('course_modules', $cm);
            $event->add_record_snapshot('course', $course);
            $event->add_record_snapshot('choicegroup', $choicegroup);
            $event->trigger();
        }
        $current = choicegroup_get_user_answer($choicegroup, $USER, FALSE, TRUE);
        // Update completion state
        $completion = new completion_info($course);
        if ($completion->is_enabled($cm) && $choicegroup->completionsubmit) {
            $completion->update_state($cm, COMPLETION_INCOMPLETE);
        }
    }
}

$PAGE->set_title(format_string($choicegroup->name));
$PAGE->set_heading($course->fullname);

/// Mark as viewed
$completion=new completion_info($course);
$completion->set_module_viewed($cm);

/// Submit any new data if there is any
if (data_submitted() && is_enrolled($context, NULL, 'mod/choicegroup:choose') && confirm_sesskey()) {

    if ($choicegroup->multipleenrollmentspossible == 1) {
        $number_of_groups = optional_param('number_of_groups', '', PARAM_INT);

        for ($i = 0; $i < $number_of_groups; $i++) {
            $answer_value = optional_param('answer_' . $i, '', PARAM_INT);
            if ($answer_value != '') {
                choicegroup_user_submit_response($answer_value, $choicegroup, $USER->id, $course, $cm);
            } else {
                $answer_value_group_id = optional_param('answer_'.$i.'_groupid', '', PARAM_INT);
                if (groups_is_member($answer_value_group_id, $USER->id)) {
                    $answer_value_group = $DB->get_record('groups', array('id' => $answer_value_group_id), 'id,name', MUST_EXIST);
                    groups_remove_member($answer_value_group_id, $USER->id);
                    $event = \mod_choicegroup\event\choice_removed::create($eventparams);
                    $event->add_record_snapshot('course_modules', $cm);
                    $event->add_record_snapshot('course', $course);
                    $event->add_record_snapshot('choicegroup', $choicegroup);
                    $event->trigger();
                }
            }
        }


    } else { // multipleenrollmentspossible != 1

        $timenow = time();
        if (has_capability('mod/choicegroup:deleteresponses', $context)) {
            if ($action == 'delete') { //some responses need to be deleted
                choicegroup_delete_responses($userids, $choicegroup, $cm, $course); //delete responses.
                redirect("view.php?id=$cm->id");
            }
        }

        $answer = optional_param('answer', '', PARAM_INT);

        if (empty($answer)) {
            redirect(new moodle_url('/mod/choicegroup/view.php',
                array('id' => $cm->id, 'notify' => 'mustchooseone', 'sesskey' => sesskey())));
        } else {
            choicegroup_user_submit_response($answer, $choicegroup, $USER->id, $course, $cm);
            redirect(new moodle_url('/mod/choicegroup/view.php',
                array('id' => $cm->id, 'notify' => 'choicegroupsaved', 'sesskey' => sesskey())));
        }
    }
}


/// Display the choicegroup and possibly results


$event = \mod_choicegroup\event\course_module_viewed::create($eventparams);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('choicegroup', $choicegroup);
$event->trigger();

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($choicegroup->name));

if ($notify and confirm_sesskey()) {
    if ($notify === 'choicegroupsaved') {
        echo $OUTPUT->notification(get_string('choicegroupsaved', 'choicegroup'), 'notifysuccess');
    } else if ($notify === 'mustchooseone') {
        echo $OUTPUT->notification(get_string('mustchooseone', 'choicegroup'), 'notifyproblem');
    }
}

/// Check to see if groups are being used in this choicegroup
$groupmode = groups_get_activity_groupmode($cm);

if ($groupmode) {
    groups_get_activity_group($cm, true);
    groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/choicegroup/view.php?id='.$id);
}

$allresponses = choicegroup_get_response_data($choicegroup, $cm);   // Big function, approx 6 SQL calls per user


if (has_capability('mod/choicegroup:readresponses', $context)) {
    choicegroup_show_reportlink($choicegroup, $allresponses, $cm);
}

echo '<div class="clearer"></div>';

if ($choicegroup->intro) {
    echo $OUTPUT->box(format_module_intro('choicegroup', $choicegroup, $cm->id), 'generalbox', 'intro');
}

//if user has already made a selection, and they are not allowed to update it, show their selected answer.
if (isloggedin() && ($current !== false) ) {
    if ($choicegroup->multipleenrollmentspossible == 1) {
        $currents = choicegroup_get_user_answer($choicegroup, $USER, TRUE, true);

        $names = array();
        if (is_array($currents)) {
            foreach ($currents as $current) {
                $names[] = format_string($current->name);
            }
        }
        $formatted_names = join(' '.get_string("and", "choicegroup").' ', array_filter(array_merge(array(join(', ', array_slice($names, 0, -1))), array_slice($names, -1))));
        echo $OUTPUT->box(get_string("yourselection", "choicegroup", userdate($choicegroup->timeopen)).": ".$formatted_names, 'generalbox', 'yourselection');

    } else {
        echo $OUTPUT->box(get_string("yourselection", "choicegroup", userdate($choicegroup->timeopen)).": ".format_string($current->name), 'generalbox', 'yourselection');
    }
}

/// Print the form
$choicegroupopen = true;
$timenow = time();
if ($choicegroup->timeclose !=0) {
    if ($choicegroup->timeopen > $timenow ) {
        echo $OUTPUT->box(get_string("notopenyet", "choicegroup", userdate($choicegroup->timeopen)), "generalbox notopenyet");
        echo $OUTPUT->footer();
        exit;
    } else if ($timenow > $choicegroup->timeclose) {
        echo $OUTPUT->box(get_string("expired", "choicegroup", userdate($choicegroup->timeclose)), "generalbox expired");
        $choicegroupopen = false;
    }
}

$options = choicegroup_prepare_options($choicegroup, $USER, $cm, $allresponses);
$renderer = $PAGE->get_renderer('mod_choicegroup');
if ( (!$current or $choicegroup->allowupdate) and $choicegroupopen and is_enrolled($context, NULL, 'mod/choicegroup:choose')) {
// They haven't made their choicegroup yet or updates allowed and choicegroup is open

    echo $renderer->display_options($options, $cm->id, $choicegroup->display, $choicegroup->publish, $choicegroup->limitanswers, $choicegroup->showresults, $current, $choicegroupopen, false, $choicegroup->multipleenrollmentspossible);
} else {
    // form can not be updated
    echo $renderer->display_options($options, $cm->id, $choicegroup->display, $choicegroup->publish, $choicegroup->limitanswers, $choicegroup->showresults, $current, $choicegroupopen, true, $choicegroup->multipleenrollmentspossible);
}
$choicegroupformshown = true;

$sitecontext = context_system::instance();

if (isguestuser()) {
    // Guest account
    echo $OUTPUT->confirm(get_string('noguestchoose', 'choicegroup').'<br /><br />'.get_string('liketologin'),
                    get_login_url(), new moodle_url('/course/view.php', array('id'=>$course->id)));
} else if (!is_enrolled($context)) {
    // Only people enrolled can make a choicegroup
    $SESSION->wantsurl = $FULLME;
    $SESSION->enrolcancel = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';

    $coursecontext = context_course::instance($course->id);
    $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));

    echo $OUTPUT->box_start('generalbox', 'notice');
    echo '<p class="center">'. get_string('notenrolledchoose', 'choicegroup') .'</p>';
    echo $OUTPUT->container_start('continuebutton');
    echo $OUTPUT->single_button(new moodle_url('/enrol/index.php?', array('id'=>$course->id)), get_string('enrolme', 'core_enrol', $courseshortname));
    echo $OUTPUT->container_end();
    echo $OUTPUT->box_end();

}

// print the results at the bottom of the screen
if ( $choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_ALWAYS or
    ($choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_AFTER_ANSWER and $current) or
    ($choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_AFTER_CLOSE and !$choicegroupopen)) {
}
else if ($choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_NOT) {
    echo $OUTPUT->box(get_string('neverresultsviewable', 'choicegroup'));
}
else if ($choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_AFTER_ANSWER && !$current) {
    echo $OUTPUT->box(get_string('afterresultsviewable', 'choicegroup'));
}
else if ($choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_AFTER_CLOSE and $choicegroupopen) {
    echo $OUTPUT->box(get_string('notyetresultsviewable', 'choicegroup'));
}
else if (!$choicegroupformshown) {
    echo $OUTPUT->box(get_string('noresultsviewable', 'choicegroup'));
}

echo $OUTPUT->footer();


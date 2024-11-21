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
 * This page prints a particular instance of choicegroup
 *
 * @package    mod_choicegroup
 * @copyright  2013 Université de Lausanne
 * @author     Nicolas Dunand <Nicolas.Dunand@unil.ch>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->dirroot.'/group/lib.php');
require_once($CFG->libdir . '/completionlib.php');

$id         = required_param('id', PARAM_INT);                 // Course Module ID.
$action     = optional_param('action', '', PARAM_ALPHA);
$userids    = optional_param_array('userid', [], PARAM_INT); // Array of attempt ids for delete action.
$notify     = optional_param('notify', '', PARAM_ALPHA);

$url = new moodle_url('/mod/choicegroup/view.php', ['id' => $id]);
if ($action !== '') {
    $url->param('action', $action);
}
$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('choicegroup', $id)) {
    throw new moodle_exception('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", ["id" => $cm->course])) {
    throw new moodle_exception('coursemisconf');
}

require_login($course, false, $cm);
$PAGE->requires->js_call_amd('mod_choicegroup/choicegroupdatadisplay', 'init');
if (!$choicegroup = choicegroup_get_choicegroup($cm->instance)) {
    throw new moodle_exception('invalidcoursemodule');
}
$choicegroupgroups = choicegroup_get_groups($choicegroup);
$choicegroupusers = [];

$strchoicegroup = get_string('modulename', 'choicegroup');
$strchoicegroups = get_string('modulenameplural', 'choicegroup');

if (!$context = context_module::instance($cm->id)) {
    throw new moodle_exception('badcontext');
}

$eventparams = [
    'context' => $context,
    'objectid' => $choicegroup->id,
];

$current = choicegroup_get_user_answer($choicegroup, $USER);
if ($action == 'delchoicegroup' && confirm_sesskey() && is_enrolled($context, null, 'mod/choicegroup:choose') &&
    $choicegroup->allowupdate && !($choicegroup->timeclose && (time() > $choicegroup->timeclose))) {
    // User wants to delete his own choice.
    if ($current !== false) {
        if (groups_is_member($current->id, $USER->id)) {
            $currentgroup = $DB->get_record('groups', ['id' => $current->id], 'id,name', MUST_EXIST);
            groups_remove_member($current->id, $USER->id);
            $event = \mod_choicegroup\event\choice_removed::create($eventparams);
            $event->add_record_snapshot('course_modules', $cm);
            $event->add_record_snapshot('course', $course);
            $event->add_record_snapshot('choicegroup', $choicegroup);
            $event->trigger();
        }
        $current = choicegroup_get_user_answer($choicegroup, $USER, false, true);
        // Update completion state.
        $completion = new completion_info($course);
        if ($completion->is_enabled($cm) && $choicegroup->completionsubmit) {
            $completion->update_state($cm, COMPLETION_INCOMPLETE);
        }
    }
}

$PAGE->set_title(format_string($choicegroup->name));
$PAGE->set_heading($course->fullname);

// Mark as viewed.
$completion = new completion_info($course);
$completion->set_module_viewed($cm);

// Submit any new data if there is any.
if (data_submitted() && is_enrolled($context, null, 'mod/choicegroup:choose') && confirm_sesskey()) {

    if ($choicegroup->multipleenrollmentspossible == 1) {
        $numberofgroups = optional_param('number_of_groups', '', PARAM_INT);
        $enrollmentscount = 0;

        if ($choicegroup->maxenrollments > 0) {
            for ($i = 0; $i < $numberofgroups; $i++) {
                $answervalue = optional_param('answer_' . $i, '', PARAM_INT);
                if ($answervalue != '') {
                    $enrollmentscount++;
                }
            }
            if ($enrollmentscount > $choicegroup->maxenrollments) {
                redirect(new moodle_url('/mod/choicegroup/view.php',
                    ['id' => $cm->id, 'notify' => 'mustchoosemax', 'sesskey' => sesskey()]));
            }
        }

        for ($i = 0; $i < $numberofgroups; $i++) {
            $answervalue = optional_param('answer_' . $i, '', PARAM_INT);
            if ($answervalue != '') {
                choicegroup_user_submit_response($answervalue, $choicegroup, $USER->id, $course, $cm);
            } else {
                $answervaluegroupid = optional_param('answer_'.$i.'_groupid', '', PARAM_INT);
                if (groups_is_member($answervaluegroupid, $USER->id)) {
                    $answervaluegroup = $DB->get_record('groups', ['id' => $answervaluegroupid], 'id,name', MUST_EXIST);
                    groups_remove_member($answervaluegroupid, $USER->id);
                    $event = \mod_choicegroup\event\choice_removed::create($eventparams);
                    $event->add_record_snapshot('course_modules', $cm);
                    $event->add_record_snapshot('course', $course);
                    $event->add_record_snapshot('choicegroup', $choicegroup);
                    $event->trigger();
                }
            }
        }


    } else { // Multipleenrollmentspossible != 1.

        $timenow = time();
        if (has_capability('mod/choicegroup:deleteresponses', $context)) {
            if ($action == 'delete') { // Some responses need to be deleted.
                choicegroup_delete_responses($userids, $choicegroup, $cm, $course); // Delete responses.
                redirect("view.php?id=$cm->id");
            }
        }

        $answer = optional_param('answer', '', PARAM_INT);

        if (empty($answer)) {
            redirect(new moodle_url('/mod/choicegroup/view.php',
                ['id' => $cm->id, 'notify' => 'mustchooseone', 'sesskey' => sesskey()]));
        } else {
            choicegroup_user_submit_response($answer, $choicegroup, $USER->id, $course, $cm);
            redirect(new moodle_url('/mod/choicegroup/view.php',
                ['id' => $cm->id, 'notify' => 'choicegroupsaved', 'sesskey' => sesskey()]));
        }
    }
}


// Display the choicegroup and possibly results.


$event = \mod_choicegroup\event\course_module_viewed::create($eventparams);
$event->add_record_snapshot('course_modules', $cm);
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('choicegroup', $choicegroup);
$event->trigger();

echo $OUTPUT->header();
if ($CFG->branch < 400) {
    echo $OUTPUT->heading(format_string($choicegroup->name));
}
if ($notify && confirm_sesskey()) {
    if ($notify === 'choicegroupsaved') {
        echo $OUTPUT->notification(get_string('choicegroupsaved', 'choicegroup'), 'notifysuccess');
    } else if ($notify === 'mustchooseone') {
        echo $OUTPUT->notification(get_string('mustchooseone', 'choicegroup'), 'notifyproblem');
    } else if ($notify === 'mustchoosemax') {
        echo $OUTPUT->notification(get_string('mustchoosemax', 'choicegroup', $choicegroup->maxenrollments), 'notifyproblem');
    }
}

if ($CFG->branch < 400) {
    if (class_exists('\core_completion\cm_completion_details') && class_exists('\core\activity_dates')) {
        // Show the activity dates and completion details.
        $modinfo = get_fast_modinfo($course);
        $cminfo = $modinfo->get_cm($cm->id);
        $cmcompletion = \core_completion\cm_completion_details::get_instance($cminfo, $USER->id);
        $activitydates = \core\activity_dates::get_dates_for_module($cminfo, $USER->id);
        echo $OUTPUT->activity_information($cminfo, $cmcompletion, $activitydates);
    }
}

// Check to see if groups are being used in this choicegroup.
$groupmode = groups_get_activity_groupmode($cm);

if ($groupmode) {
    groups_get_activity_group($cm, true);
    groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/choicegroup/view.php?id='.$id);
}

// Big function, approx 6 SQL calls per user.
$allresponses = choicegroup_get_response_data($choicegroup, $cm, $groupmode, $choicegroup->onlyactive);


if (has_capability('mod/choicegroup:readresponses', $context)) {
    choicegroup_show_reportlink($choicegroup, $allresponses, $cm);
}

echo '<div class="clearer"></div>';

if ($choicegroup->intro) {
    if ($CFG->branch < 400) {
        echo $OUTPUT->box(format_module_intro('choicegroup', $choicegroup, $cm->id), 'generalbox', 'intro');
    }
}

// If user has already made a selection, and they are not allowed to update it, show their selected answer.
if (isloggedin() && ($current !== false) ) {
    if ($choicegroup->multipleenrollmentspossible == 1) {
        $currents = choicegroup_get_user_answer($choicegroup, $USER, true, true);

        $names = [];
        if (is_array($currents)) {
            foreach ($currents as $current) {
                $names[] = format_string($current->name);
            }
        }
        $formattednames = join(' '.get_string("and", "choicegroup").' ',
            array_filter(array_merge([join(', ', array_slice($names, 0, -1))],
                array_slice($names, -1))));
        echo $OUTPUT->box(get_string("yourselection", "choicegroup",
                userdate($choicegroup->timeopen)).": ".$formattednames, 'generalbox', 'yourselection');

    } else {
        echo $OUTPUT->box(get_string("yourselection", "choicegroup",
                userdate($choicegroup->timeopen)).": ".format_string($current->name), 'generalbox', 'yourselection');
    }
}

// Print the form.
$choicegroupopen = true;
$timenow = time();
if ($choicegroup->timeclose != 0) {
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

if ((!$current || $choicegroup->allowupdate) && $choicegroupopen && is_enrolled($context, null, 'mod/choicegroup:choose')) {
    // They haven't made their choicegroup yet or updates allowed and choicegroup is open.

    echo $renderer->display_options($options, $cm->id, $choicegroup->display, $choicegroup->publish, $choicegroup->limitanswers,
        $choicegroup->showresults, $current, $choicegroupopen, false, $choicegroup->multipleenrollmentspossible,
        $choicegroup->onlyactive, $choicegroup->defaultgroupdescriptionstate);
} else {
    // Form can not be updated.
    echo $renderer->display_options($options, $cm->id, $choicegroup->display, $choicegroup->publish, $choicegroup->limitanswers,
        $choicegroup->showresults, $current, $choicegroupopen, true, $choicegroup->multipleenrollmentspossible,
        $choicegroup->onlyactive, $choicegroup->defaultgroupdescriptionstate);
}
$choicegroupformshown = true;

$sitecontext = context_system::instance();

if (isguestuser()) {
    // Guest account.
    echo $OUTPUT->confirm(get_string('noguestchoose', 'choicegroup').'<br /><br />'.get_string('liketologin'),
                    get_login_url(), new moodle_url('/course/view.php', ['id' => $course->id]));
} else if (!is_enrolled($context)) {
    // Only people enrolled can make a choicegroup.
    $SESSION->wantsurl = $FULLME;
    $SESSION->enrolcancel = (!empty($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '';

    $coursecontext = context_course::instance($course->id);
    $courseshortname = format_string($course->shortname, true, ['context' => $coursecontext]);

    echo $OUTPUT->box_start('generalbox', 'notice');
    echo '<p class="center">'. get_string('notenrolledchoose', 'choicegroup') .'</p>';
    echo $OUTPUT->container_start('continuebutton');
    echo $OUTPUT->single_button(new moodle_url('/enrol/index.php?', ['id' => $course->id]),
        get_string('enrolme', 'core_enrol', $courseshortname));
    echo $OUTPUT->container_end();
    echo $OUTPUT->box_end();

}

// Print the results at the bottom of the screen.
// phpcs:disable Generic.CodeAnalysis.EmptyStatement
if ($choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_ALWAYS ||
    ($choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_AFTER_ANSWER && $current) ||
    ($choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_AFTER_CLOSE && !$choicegroupopen)) {

} else if ($choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_NOT) {
    echo $OUTPUT->box(get_string('neverresultsviewable', 'choicegroup'));
} else if ($choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_AFTER_ANSWER && !$current) {
    echo $OUTPUT->box(get_string('afterresultsviewable', 'choicegroup'));
} else if ($choicegroup->showresults == CHOICEGROUP_SHOWRESULTS_AFTER_CLOSE && $choicegroupopen) {
    echo $OUTPUT->box(get_string('notyetresultsviewable', 'choicegroup'));
} else if (!$choicegroupformshown) {
    echo $OUTPUT->box(get_string('noresultsviewable', 'choicegroup'));
}

echo $OUTPUT->footer();


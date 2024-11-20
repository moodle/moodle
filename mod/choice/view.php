<?php

require_once("../../config.php");
require_once("lib.php");
require_once($CFG->libdir . '/completionlib.php');

$id         = required_param('id', PARAM_INT);                 // Course Module ID
$action     = optional_param('action', '', PARAM_ALPHANUMEXT);
$attemptids = optional_param_array('attemptid', array(), PARAM_INT); // Get array of responses to delete or modify.
$userids    = optional_param_array('userid', array(), PARAM_INT); // Get array of users whose choices need to be modified.
$notify     = optional_param('notify', '', PARAM_ALPHA);

$url = new moodle_url('/mod/choice/view.php', array('id'=>$id));
if ($action !== '') {
    $url->param('action', $action);
}
$PAGE->set_url($url);

if (! $cm = get_coursemodule_from_id('choice', $id)) {
    throw new \moodle_exception('invalidcoursemodule');
}
$cm = cm_info::create($cm);

if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
    throw new \moodle_exception('coursemisconf');
}

require_course_login($course, false, $cm);

if (!$choice = choice_get_choice($cm->instance)) {
    throw new \moodle_exception('invalidcoursemodule');
}

$strchoice = get_string('modulename', 'choice');
$strchoices = get_string('modulenameplural', 'choice');

$context = context_module::instance($cm->id);

list($choiceavailable, $warnings) = choice_get_availability_status($choice);

if ($action == 'delchoice' and confirm_sesskey() and is_enrolled($context, NULL, 'mod/choice:choose') and $choice->allowupdate
        and $choiceavailable) {
    $answercount = $DB->count_records('choice_answers', array('choiceid' => $choice->id, 'userid' => $USER->id));
    if ($answercount > 0) {
        $choiceanswers = $DB->get_records('choice_answers', array('choiceid' => $choice->id, 'userid' => $USER->id),
            '', 'id');
        $todelete = array_keys($choiceanswers);
        choice_delete_responses($todelete, $choice, $cm, $course);
        redirect("view.php?id=$cm->id");
    }
}

$PAGE->set_title($choice->name);
$PAGE->set_heading($course->fullname);

/// Submit any new data if there is any
if (data_submitted() && !empty($action) && confirm_sesskey()) {
    $timenow = time();
    if (has_capability('mod/choice:deleteresponses', $context)) {
        if ($action === 'delete') {
            // Some responses need to be deleted.
            choice_delete_responses($attemptids, $choice, $cm, $course);
            redirect("view.php?id=$cm->id");
        }
        if (preg_match('/^choose_(\d+)$/', $action, $actionmatch)) {
            // Modify responses of other users.
            $newoptionid = (int)$actionmatch[1];
            choice_modify_responses($userids, $attemptids, $newoptionid, $choice, $cm, $course);
            redirect("view.php?id=$cm->id");
        }
    }

    // Redirection after all POSTs breaks block editing, we need to be more specific!
    if ($choice->allowmultiple) {
        $answer = optional_param_array('answer', array(), PARAM_INT);
    } else {
        $answer = optional_param('answer', '', PARAM_INT);
    }

    if (!$choiceavailable) {
        $reason = current(array_keys($warnings));
        throw new moodle_exception($reason, 'choice', '', $warnings[$reason]);
    }

    if ($answer && is_enrolled($context, null, 'mod/choice:choose')) {
        choice_user_submit_response($answer, $choice, $USER->id, $course, $cm);
        redirect(new moodle_url('/mod/choice/view.php',
            array('id' => $cm->id, 'notify' => 'choicesaved', 'sesskey' => sesskey())));
    } else if (empty($answer) and $action === 'makechoice') {
        // We cannot use the 'makechoice' alone because there might be some legacy renderers without it,
        // outdated renderers will not get the 'mustchoose' message - bad luck.
        redirect(new moodle_url('/mod/choice/view.php',
            array('id' => $cm->id, 'notify' => 'mustchooseone', 'sesskey' => sesskey())));
    }
}

// Completion and trigger events.
choice_view($choice, $course, $cm, $context);

$PAGE->add_body_class('limitedwidth');

echo $OUTPUT->header();

if ($notify and confirm_sesskey()) {
    if ($notify === 'choicesaved') {
        echo $OUTPUT->notification(get_string('choicesaved', 'choice'), 'notifysuccess');
    } else if ($notify === 'mustchooseone') {
        echo $OUTPUT->notification(get_string('mustchooseone', 'choice'), 'notifyproblem');
    }
}

/// Display the choice and possibly results
$eventdata = array();
$eventdata['objectid'] = $choice->id;
$eventdata['context'] = $context;

/// Check to see if groups are being used in this choice
$groupmode = groups_get_activity_groupmode($cm);

// Check if we want to include responses from inactive users.
$onlyactive = $choice->includeinactive ? false : true;

$allresponses = choice_get_response_data($choice, $cm, $groupmode, $onlyactive);   // Big function, approx 6 SQL calls per user.


if (has_capability('mod/choice:readresponses', $context) && !$PAGE->has_secondary_navigation()) {
    choice_show_reportlink($allresponses, $cm);
}

echo '<div class="clearer"></div>';

$timenow = time();
$current = choice_get_my_response($choice);
//if user has already made a selection, and they are not allowed to update it or if choice is not open, show their selected answer.
if (isloggedin() && (!empty($current)) &&
    (empty($choice->allowupdate) || ($timenow > $choice->timeclose)) ) {
    $choicetexts = array();
    foreach ($current as $c) {
        $choicetexts[] = format_string(choice_get_option_text($choice, $c->optionid));
    }
    echo $OUTPUT->box(get_string("yourselection", "choice") . ": " . implode('; ', $choicetexts), 'generalbox', 'yourselection');
}

/// Print the form
$choiceopen = true;
if ((!empty($choice->timeopen)) && ($choice->timeopen > $timenow)) {
    if ($choice->showpreview) {
        echo $OUTPUT->box(get_string('previewing', 'choice'), 'generalbox alert');
    } else {
        echo $OUTPUT->footer();
        exit;
    }
} else if ((!empty($choice->timeclose)) && ($timenow > $choice->timeclose)) {
    $choiceopen = false;
}

if ( (!$current or $choice->allowupdate) and $choiceopen and is_enrolled($context, NULL, 'mod/choice:choose')) {

    // Show information on how the results will be published to students.
    $publishinfo = null;
    switch ($choice->showresults) {
        case CHOICE_SHOWRESULTS_NOT:
            $publishinfo = get_string('publishinfonever', 'choice');
            break;

        case CHOICE_SHOWRESULTS_AFTER_ANSWER:
            if ($choice->publish == CHOICE_PUBLISH_ANONYMOUS) {
                $publishinfo = get_string('publishinfoanonafter', 'choice');
            } else {
                $publishinfo = get_string('publishinfofullafter', 'choice');
            }
            break;

        case CHOICE_SHOWRESULTS_AFTER_CLOSE:
            if ($choice->publish == CHOICE_PUBLISH_ANONYMOUS) {
                $publishinfo = get_string('publishinfoanonclose', 'choice');
            } else {
                $publishinfo = get_string('publishinfofullclose', 'choice');
            }
            break;

        default:
            // No need to inform the user in the case of CHOICE_SHOWRESULTS_ALWAYS since it's already obvious that the results are
            // being published.
            break;
    }

    // Show info if necessary.
    if (!empty($publishinfo)) {
        echo $OUTPUT->notification($publishinfo, 'info');
    }

    // They haven't made their choice yet or updates allowed and choice is open.
    $options = choice_prepare_options($choice, $USER, $cm, $allresponses);
    $renderer = $PAGE->get_renderer('mod_choice');
    echo $renderer->display_options($options, $cm->id, $choice->display, $choice->allowmultiple);
    $choiceformshown = true;
} else {
    $choiceformshown = false;
}

if (!$choiceformshown) {
    $sitecontext = context_system::instance();

    if (isguestuser()) {
        // Guest account
        echo $OUTPUT->confirm(get_string('noguestchoose', 'choice').'<br /><br />'.get_string('liketologin'),
                     get_login_url(), new moodle_url('/course/view.php', array('id'=>$course->id)));
    } else if (!is_enrolled($context)) {
        // Only people enrolled can make a choice
        $SESSION->wantsurl = qualified_me();
        $SESSION->enrolcancel = get_local_referer(false);

        $coursecontext = context_course::instance($course->id);
        $courseshortname = format_string($course->shortname, true, array('context' => $coursecontext));

        echo $OUTPUT->box_start('generalbox', 'notice');
        echo '<p align="center">'. get_string('notenrolledchoose', 'choice') .'</p>';
        echo $OUTPUT->container_start('continuebutton');
        echo $OUTPUT->single_button(new moodle_url('/enrol/index.php?', array('id'=>$course->id)), get_string('enrolme', 'core_enrol', $courseshortname));
        echo $OUTPUT->container_end();
        echo $OUTPUT->box_end();

    }
}

// print the results at the bottom of the screen
if (choice_can_view_results($choice, $current, $choiceopen)) {
    $results = prepare_choice_show_results($choice, $course, $cm, $allresponses);
    $renderer = $PAGE->get_renderer('mod_choice');
    if ($results->publish) { // If set to publish full results, display a heading for the responses section.
        echo html_writer::tag('h3', format_string(get_string("responses", "choice")), ['class' => 'mt-4']);
    }

    if ($groupmode) { // If group mode is enabled, display the groups selector.
        groups_get_activity_group($cm, true);
        $groupsactivitymenu = groups_print_activity_menu($cm, new moodle_url('/mod/choice/view.php', ['id' => $id]),
            true);
        echo html_writer::div($groupsactivitymenu, 'mt-3 mb-1');
    }

    $resultstable = $renderer->display_result($results);
    echo $OUTPUT->box($resultstable);

} else if (!$choiceformshown) {
    echo $OUTPUT->box(get_string('noresultsviewable', 'choice'));
}

echo $OUTPUT->footer();

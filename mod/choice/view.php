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
    print_error('invalidcoursemodule');
}

if (! $course = $DB->get_record("course", array("id" => $cm->course))) {
    print_error('coursemisconf');
}

require_course_login($course, false, $cm);

if (!$choice = choice_get_choice($cm->instance)) {
    print_error('invalidcoursemodule');
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

echo $OUTPUT->header();
echo $OUTPUT->heading(format_string($choice->name), 2, null);

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

if ($groupmode) {
    groups_get_activity_group($cm, true);
    groups_print_activity_menu($cm, $CFG->wwwroot . '/mod/choice/view.php?id='.$id);
}

// Check if we want to include responses from inactive users.
$onlyactive = $choice->includeinactive ? false : true;

$allresponses = choice_get_response_data($choice, $cm, $groupmode, $onlyactive);   // Big function, approx 6 SQL calls per user.


if (has_capability('mod/choice:readresponses', $context)) {
    choice_show_reportlink($allresponses, $cm);
}

echo '<div class="clearer"></div>';

if ($choice->intro) {
    echo $OUTPUT->box(format_module_intro('choice', $choice, $cm->id), 'generalbox', 'intro');
}

$timenow = time();
$current = choice_get_my_response($choice);
//if user has already made a selection, and they are not allowed to update it or if choice is not open, show their selected answer.
if (isloggedin() && (!empty($current)) &&
    (empty($choice->allowupdate) || ($timenow > $choice->timeclose)) ) {
    $choicetexts = array();
    foreach ($current as $c) {
        $choicetexts[] = format_string(choice_get_option_text($choice, $c->optionid));
    }
    echo $OUTPUT->box(get_string("yourselection", "choice", userdate($choice->timeopen)).": ".implode('; ', $choicetexts), 'generalbox', 'yourselection');
}

/// Print the form
$choiceopen = true;
if ((!empty($choice->timeopen)) && ($choice->timeopen > $timenow)) {
    if ($choice->showpreview) {
        echo $OUTPUT->box(get_string('previewonly', 'choice', userdate($choice->timeopen)), 'generalbox alert');
    } else {
        echo $OUTPUT->box(get_string("notopenyet", "choice", userdate($choice->timeopen)), "generalbox notopenyet");
        echo $OUTPUT->footer();
        exit;
    }
} else if ((!empty($choice->timeclose)) && ($timenow > $choice->timeclose)) {
    echo $OUTPUT->box(get_string("expired", "choice", userdate($choice->timeclose)), "generalbox expired");
    $choiceopen = false;
}

if ( (!$current or $choice->allowupdate) and $choiceopen and is_enrolled($context, NULL, 'mod/choice:choose')) {
// They haven't made their choice yet or updates allowed and choice is open

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
    echo $renderer->display_result($results);

} else if (!$choiceformshown) {
    echo $OUTPUT->box(get_string('noresultsviewable', 'choice'));
}

echo $OUTPUT->footer();

<?php

// preferences.php - user prefs for calendar

require_once('../config.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->dirroot.'/calendar/preferences_form.php');

$courseid = required_param('course', PARAM_INT);
$course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

$PAGE->set_url(new moodle_url('/calendar/preferences.php', array('course' => $courseid)));
$PAGE->set_pagelayout('standard');

require_login($course);

if ($courseid == SITEID) {
    $viewurl = new moodle_url('/calendar/view.php', array('view' => 'month'));
} else {
    $viewurl = new moodle_url('/calendar/view.php', array('view' => 'month', 'course' => $courseid));
}
navigation_node::override_active_url($viewurl);

$defaultlookahead = CALENDAR_DEFAULT_UPCOMING_LOOKAHEAD;
if (isset($CFG->calendar_lookahead)) {
    $defaultlookahead = intval($CFG->calendar_lookahead);
}
$defaultmaxevents = CALENDAR_DEFAULT_UPCOMING_MAXEVENTS;
if (isset($CFG->calendar_maxevents)) {
    $defaultmaxevents = intval($CFG->calendar_maxevents);
}

$prefs = new stdClass;
$prefs->timeformat = get_user_preferences('calendar_timeformat', '');
$prefs->startwday  = calendar_get_starting_weekday();
$prefs->maxevents  = get_user_preferences('calendar_maxevents', $defaultmaxevents);
$prefs->lookahead  = get_user_preferences('calendar_lookahead', $defaultlookahead);
$prefs->persistflt = get_user_preferences('calendar_persistflt', 0);

$form = new calendar_preferences_form($PAGE->url);
$form->set_data($prefs);

if ($form->is_cancelled()) {
    redirect($viewurl);
} else if ($form->is_submitted() && $form->is_validated() && confirm_sesskey()) {
    $data = $form->get_data();
    if ($data->timeformat != CALENDAR_TF_12 && $data->timeformat != CALENDAR_TF_24) {
        $data->timeformat = '';
    }
    set_user_preference('calendar_timeformat', $data->timeformat);

    $data->startwday = intval($data->startwday);
    if ($data->startwday < 0 || $data->startwday > 6) {
        $data->startwday = abs($data->startwday % 7);
    }
    set_user_preference('calendar_startwday', $data->startwday);

    if (intval($data->maxevents) >= 1) {
        set_user_preference('calendar_maxevents', $data->maxevents);
    }

    if (intval($data->lookahead) >= 1) {
        set_user_preference('calendar_lookahead', $data->lookahead);
    }

    set_user_preference('calendar_persistflt', intval($data->persistflt));
    redirect($viewurl, get_string('changessaved'), 1);
    exit;
}

$strcalendar = get_string('calendar', 'calendar');
$strpreferences = get_string('calendarpreferences', 'calendar');

$PAGE->navbar->add($strpreferences);
$PAGE->set_pagelayout('admin');
$PAGE->set_title("$course->shortname: $strcalendar: $strpreferences");
$PAGE->set_heading($course->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($strpreferences);
echo $OUTPUT->box_start('generalbox boxaligncenter');
$form->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
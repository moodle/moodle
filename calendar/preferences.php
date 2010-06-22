<?php

// preferences.php - user prefs for calendar

require_once('../config.php');
require_once($CFG->dirroot.'/calendar/lib.php');
require_once($CFG->dirroot.'/calendar/preferences_form.php');

$course = $site = get_site();
if (isset($SESSION->cal_course_referer)) {
    $course = $DB->get_record('course', array('id'=>$SESSION->cal_course_referer), '*', MUST_EXIST);
}

$PAGE->set_url('/calendar/preferences.php');

if ($course->id != SITEID) {
    require_login($course);
} else {
    require_login();
}
// Initialize the session variables
calendar_session_vars();


$prefs = new stdClass;
$prefs->timeformat = get_user_preferences('calendar_timeformat', '');
$prefs->startwday  = get_user_preferences('calendar_startwday', calendar_get_starting_weekday());
$prefs->maxevents  = get_user_preferences('calendar_maxevents', CALENDAR_UPCOMING_MAXEVENTS);
$prefs->lookahead  = get_user_preferences('calendar_lookahead', CALENDAR_UPCOMING_DAYS);
$prefs->persistflt = get_user_preferences('calendar_persistflt', 0);

$form = new calendar_preferences_form();
$form->set_data($prefs);

if ($data = $form->get_data() && confirm_sesskey()) {
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
    redirect(new moodle_url('/calendar/view.php', array('course'=>$course->id)), get_string('changessaved'), 1);
    exit;
}

$strcalendar = get_string('calendar', 'calendar');
$strpreferences = get_string('calendarpreferences', 'calendar');

$PAGE->navbar->add($strpreferences, new moodle_url('/calendar/view.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title("$site->shortname: $strcalendar: $strpreferences");
$PAGE->set_heading($COURSE->fullname);

echo $OUTPUT->header();
echo $OUTPUT->heading($strpreferences);
echo $OUTPUT->box_start('generalbox boxaligncenter');
$form->display();
echo $OUTPUT->box_end();
echo $OUTPUT->footer();
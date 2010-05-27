<?php

// preferences.php - user prefs for calendar

require_once('../config.php');
require_once($CFG->dirroot.'/calendar/lib.php');

if (isset($SESSION->cal_course_referer)) {
    if (! $course = $DB->get_record('course', array('id'=>$SESSION->cal_course_referer))) {
        $course = get_site();
    }
}

$PAGE->set_url('/calendar/preferences.php');

if ($course->id != SITEID) {
    require_login($course->id);
}
// Initialize the session variables
calendar_session_vars();

/// If data submitted, then process and store.

if ($form = data_submitted() and confirm_sesskey()) {
    foreach ($form as $preference => $value) {
        switch ($preference) {
            case 'timeformat':
                if ($value != CALENDAR_TF_12 and $value != CALENDAR_TF_24) {
                    $value = '';
                }
                set_user_preference('calendar_timeformat', $value);
            break;
            case 'startwday':
                $value = intval($value);
                if ($value < 0 or $value > 6) {
                    $value = abs($value % 7);
                }
                set_user_preference('calendar_startwday', $value);
            break;
            case 'maxevents':
                if (intval($value) >= 1) {
                    set_user_preference('calendar_maxevents', $value);
                }
            break;
            case 'lookahead':
                if (intval($value) >= 1) {
                    set_user_preference('calendar_lookahead', $value);
                }
            break;
            case 'persistflt':
                set_user_preference('calendar_persistflt', intval($value));
            break;
        }
    }
    redirect('view.php?course='.$course->id, get_string('changessaved'), 1);
    exit;
}

$site = get_site();

$strcalendar = get_string('calendar', 'calendar');
$strpreferences = get_string('preferences', 'calendar');

if ($course->id != SITEID) {
   $PAGE->navbar-add($course->shortname, new moodle_url('/course/view.php', array('id'=>$course->id)));
}
$PAGE->navbar->add($strpreferences, new moodle_url('/calendar/view.php'));

$PAGE->set_title("$site->shortname: $strcalendar: $strpreferences");
$PAGE->set_heading($COURSE->fullname);

echo $OUTPUT->header();

echo $OUTPUT->heading($strpreferences);

echo $OUTPUT->box_start('generalbox boxaligncenter');

$prefs->timeformat = get_user_preferences('calendar_timeformat', '');
$prefs->startwday  = get_user_preferences('calendar_startwday', calendar_get_starting_weekday());
$prefs->maxevents  = get_user_preferences('calendar_maxevents', CALENDAR_UPCOMING_MAXEVENTS);
$prefs->lookahead  = get_user_preferences('calendar_lookahead', CALENDAR_UPCOMING_DAYS);
$prefs->persistflt = get_user_preferences('calendar_persistflt', 0);

include('./preferences.html');
echo $OUTPUT->box_end();

echo $OUTPUT->footer();
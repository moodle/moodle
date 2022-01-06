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
 * Private googlemeet module utility functions
 *
 * @package     mod_googlemeet
 * @copyright   2020 Rone Santos <ronefel@hotmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once("$CFG->dirroot/mod/googlemeet/lib.php");

/**
 * Print googlemeet header.
 * @param object $googlemeet
 * @param object $cm
 * @param object $course
 * @return void
 */
function googlemeet_print_header($googlemeet, $cm, $course) {
    global $PAGE, $OUTPUT;

    $PAGE->set_title($course->shortname . ': ' . $googlemeet->name);
    $PAGE->set_heading($course->fullname);
    $PAGE->set_activity_record($googlemeet);
    echo $OUTPUT->header();
}

/**
 * Print googlemeet heading.
 * @param object $googlemeet
 * @param object $cm
 * @param object $course
 * @param bool $notused This variable is no longer used.
 * @return void
 */
function googlemeet_print_heading($googlemeet, $cm, $course, $notused = false) {
    global $OUTPUT;
    echo $OUTPUT->heading(format_string($googlemeet->name), 2);
}

/**
 * Print googlemeet introduction.
 * @param object $googlemeet
 * @param object $cm
 * @param object $course
 * @param bool $ignoresettings print even if not specified in modedit
 * @return void
 */
function googlemeet_print_intro($googlemeet, $cm, $course, $ignoresettings = false) {
    global $OUTPUT;

    $options = empty($googlemeet->displayoptions) ? array() : unserialize($googlemeet->displayoptions);
    if ($ignoresettings or !empty($options['printintro'])) {
        if (trim(strip_tags($googlemeet->intro))) {
            echo $OUTPUT->box_start('mod_introbox', 'googlemeetintro');
            echo format_module_intro('googlemeet', $googlemeet, $cm->id);
            echo $OUTPUT->box_end();
        }
    }
}

/**
 * Get event data from the form.
 *
 * @param stdClass $googlemeet moodleform.
 * @return array list of events
 */
function googlemeet_construct_events_data_for_add($googlemeet) {
    global $CFG;

    $eventstarttime = $googlemeet->starthour * HOURSECS + $googlemeet->startminute * MINSECS;
    $eventendtime = $googlemeet->endhour * HOURSECS + $googlemeet->endminute * MINSECS;
    $eventdate = $googlemeet->eventdate + $eventstarttime;
    $duration = $eventendtime - $eventstarttime;

    $events = array();

    $event = new stdClass();
    $event->googlemeetid = $googlemeet->id;
    $event->eventdate = $eventdate;
    $event->duration = $duration;
    $event->timemodified = time();
    $events[] = $event;

    if (isset($googlemeet->addmultiply)) {
        $startdate = $eventdate + DAYSECS;
        $enddate = $googlemeet->eventenddate + $eventendtime;

        // Getting first day of week.
        $sdate = $startdate;
        $dayinfo = usergetdate($sdate);
        if ($CFG->calendar_startwday === '0') { // Week start from sunday.
            $startweek = $sdate - $dayinfo['wday'] * DAYSECS; // Call new variable.
        } else {
            $wday = $dayinfo['wday'] === 0 ? 7 : $dayinfo['wday'];
            $startweek = $sdate - ($wday - 1) * DAYSECS;
        }

        $wdaydesc = [0 => 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

        while ($sdate < $enddate) {
            if ($sdate < $startweek + WEEKSECS) {
                $dayinfo = usergetdate($sdate);
                if (isset($googlemeet->days) && array_key_exists($wdaydesc[$dayinfo['wday']], $googlemeet->days)) {
                    $event = new stdClass();
                    $event->googlemeetid = $googlemeet->id;
                    $event->eventdate = make_timestamp(
                        $dayinfo['year'],
                        $dayinfo['mon'],
                        $dayinfo['mday'],
                        $googlemeet->starthour,
                        $googlemeet->startminute
                    );
                    $event->duration = $duration;
                    $event->timemodified = time();

                    $events[] = $event;
                }
                $sdate += DAYSECS;
            } else {
                $startweek += WEEKSECS * $googlemeet->period;
                $sdate = $startweek;
            }
        }
    }

    return $events;
}

/**
 * This excludes all Google Meet events.
 * @param int $googlemeetid
 * @return void
 */
function googlemeet_delete_events($googlemeetid) {
    global $DB;

    $events = $DB->get_records('googlemeet_events', ['googlemeetid' => $googlemeetid]);

    foreach ($events as $event) {
        $DB->delete_records('googlemeet_notify_done', ['eventid' => $event->id]);
    }

    $DB->delete_records('googlemeet_events', ['googlemeetid' => $googlemeetid]);
}

/**
 * This creates new events given as timeopen and timeclose by $googlemeet.
 *
 * @param array $events list of events
 * @return void
 */
function googlemeet_set_events($events) {
    global $DB;

    googlemeet_delete_events($events[0]->googlemeetid);

    $DB->insert_records('googlemeet_events', $events);
}

/**
 * This creates new events given as timeopen and timeclose by googlemeet.
 *
 * @param object $googlemeet
 * @param object $cm
 * @param object $context
 * @return void
 */
function googlemeet_print_recordings($googlemeet, $cm, $context) {
    global $CFG, $PAGE, $OUTPUT;

    $config = get_config('googlemeet');

    if (!$config->clientid && !$config->apikey) {
        return;
    }

    $params = ['googlemeetid' => $googlemeet->id];
    $hascapability = has_capability('mod/googlemeet:editrecording', $context);
    if (!$hascapability) {
        $params['visible'] = true;
    }

    $html = '<div id="googlemeet_recordings" class="googlemeet_recordings">';

    $recordings = googlemeet_list_recordings($params);

    $html .= $OUTPUT->render_from_template('mod_googlemeet/recordingstable', [
        'recordings' => $recordings,
        'coursemoduleid' => $cm->id,
        'hascapability' => $hascapability
    ]);

    $PAGE->requires->js(new moodle_url($CFG->wwwroot . '/mod/googlemeet/assets/js/build/jstable.min.js'));

    if ($hascapability) {
        $lastsync = get_string('never', 'googlemeet');
        if ($googlemeet->lastsync) {
            $lastsync = userdate($googlemeet->lastsync, get_string('timedate', 'googlemeet'));
        }

        $redordingname = '"' . substr($googlemeet->url, 24, 12) . '" ';
        if ($googlemeet->originalname) {
            $redordingname .= get_string('or', 'googlemeet') . ' "' . $googlemeet->originalname . '"';
        }

        $html .= $OUTPUT->render_from_template('mod_googlemeet/syncbutton', [
            'lastsync' => $lastsync,
            'creatoremail' => $googlemeet->creatoremail,
            'redordingname' => $redordingname
        ]);

        $PAGE->requires->js_call_amd('mod_googlemeet/view', 'init', [
            $config->clientid,
            $config->apikey,
            $googlemeet,
            googlemeet_has_recording($googlemeet->id),
            $cm->id,
            has_capability('mod/googlemeet:editrecording', $context)
        ]);
    }

    $html .= '</div>';

    echo $html;
}

/**
 * This clears the url.
 *
 * @param string $url
 * @return mixed The url if valid or false if invalid
 */
function googlemeet_clear_url($url) {
    $pattern = "/meet.google.com\/[a-zA-Z0-9]{3}-[a-zA-Z0-9]{4}-[a-zA-Z0-9]{3}/";
    preg_match($pattern, $url, $matches, PREG_OFFSET_CAPTURE);

    if ($matches) {
        return 'https://' . $matches[0][0];
    }

    return null;
}

/**
 * This checks if have recordings from the googlemeet.
 *
 * @param int $googlemeetid
 * @return boolean
 */
function googlemeet_has_recording($googlemeetid) {
    global $DB;

    $recordings = $DB->get_records('googlemeet_recordings', ['googlemeetid' => $googlemeetid]);

    return $recordings ? true : false;
}

/**
 * Generates a list of users who have not yet been notified.
 *
 * @param int $eventid the event ID
 * @return stdClass list of users
 */
function googlemeet_get_users_to_notify($eventid) {
    global $DB;

    $sql = "SELECT DISTINCT
                   u.*
              FROM {googlemeet_events} me
        INNER JOIN {googlemeet} m
                ON m.id = me.googlemeetid
        INNER JOIN {course_modules} cm
                ON (cm.instance = m.id AND cm.visible = 1 AND cm.deletioninprogress = 0)
        INNER JOIN {course} c
                ON (c.id = cm.course AND c.visible = 1)
        INNER JOIN {modules} md
                ON (md.id = cm.module AND md.name = 'googlemeet')
        INNER JOIN {context} ctx
                ON ctx.instanceid = c.id
        INNER JOIN {role_assignments} ra
                ON (ra.contextid = ctx.id AND ra.roleid = 5)
        INNER JOIN {user} u
                ON u.id = ra.userid
             WHERE me.id = {$eventid}
               AND (SELECT count(*) = 0
                      FROM {googlemeet_notify_done} nd
                     WHERE nd.eventid = me.id AND nd.userid = u.id)";

    return $DB->get_records_sql($sql);
}

/**
 * Returns a list of future events
 */
function googlemeet_get_future_events() {
    global $DB;

    $now = time();

    $sql = "SELECT DISTINCT
                   me.id,
                   me.eventdate,
                   me.duration,
                   m.id AS googlemeetid,
                   m.name AS googlemeetname,
                   m.url,
                   cm.id AS cmid,
                   c.id AS courseid,
                   c.fullname AS coursename
              FROM {googlemeet_events} me
        INNER JOIN {googlemeet} m
                ON m.id = me.googlemeetid
        INNER JOIN {course_modules} cm
                ON (cm.instance = m.id AND cm.visible = 1 AND cm.deletioninprogress = 0)
        INNER JOIN {course} c
                ON (c.id = cm.course AND c.visible = 1)
        INNER JOIN {modules} md
                ON (md.id = cm.module AND md.name = 'googlemeet')
             WHERE {$now} BETWEEN me.eventdate - m.minutesbefore * 60 AND me.eventdate
               AND m.notify = 1";

    return $DB->get_records_sql($sql);
}

/**
 * Send a notification to students in the class about the event.
 *
 * @param object $user
 * @param object $event
 * @return void
 */
function googlemeet_send_notification($user, $event) {
    global $CFG;

    $startdate = userdate($event->eventdate, get_string('strftimedmy', 'googlemeet'), $user->timezone);
    $starttime = userdate($event->eventdate, get_string('strftimehm', 'googlemeet'), $user->timezone);
    $endtime = userdate($event->eventdate + $event->duration, get_string('strftimehm', 'googlemeet'), $user->timezone);
    $usertimezone = usertimezone($user->timezone);
    $notificationstr = get_string('notification', 'googlemeet');
    $subject = "{$notificationstr}: {$event->googlemeetname} - {$startdate} {$starttime} - {$endtime} ($usertimezone)";
    $url = $CFG->wwwroot . '/mod/googlemeet/view.php?id=' . $event->cmid;

    $message = new \core\message\message();
    $message->component = 'mod_googlemeet';
    $message->name = 'notification';
    $message->userfrom = core_user::get_noreply_user();
    $message->userto = $user;
    $message->subject = $subject;
    $message->fullmessage = googlemeet_get_messagehtml($user, $event);
    $message->fullmessageformat = FORMAT_MARKDOWN;
    $message->fullmessagehtml = googlemeet_get_messagehtml($user, $event);
    $message->smallmessage = $subject;
    $message->notification = 1;
    $message->contexturl = $url;
    $message->contexturlname = $event->googlemeetname;
    $message->courseid = $event->courseid;

    message_send($message);
}

/**
 * Records the sending of the notification to not send repeated.
 *
 * @param int $userid
 * @param int $eventid
 */
function googlemeet_notify_done($userid, $eventid) {
    global $DB;

    $notifydone = new stdClass();
    $notifydone->userid = $userid;
    $notifydone->eventid = $eventid;
    $notifydone->timesent = time();

    return $DB->insert_record('googlemeet_notify_done', $notifydone);
}

/**
 * Removes records of past event notification notifications.
 */
function googlemeet_remove_notify_done_from_old_events() {
    global $DB;

    $now = time();

    $sql = "SELECT id
              FROM {googlemeet_events}
             WHERE eventdate < {$now}";

    $oldevents = $DB->get_records_sql($sql);

    foreach ($oldevents as $oldevent) {
        $DB->delete_records('googlemeet_notify_done', ['eventid' => $oldevent->id]);
    }
}

/**
 * Mount the body content of the notification.
 *
 * @param object $user db record of user
 * @param object $event db record of event
 * @return string - the content of the notification after assembly.
 */
function googlemeet_get_messagehtml($user, $event) {
    global $CFG;

    $config = get_config('googlemeet');

    $startdate = userdate($event->eventdate, get_string('strftimedmy', 'googlemeet'), $user->timezone);
    $starttime = userdate($event->eventdate, get_string('strftimehm', 'googlemeet'), $user->timezone);
    $endtime = userdate($event->eventdate + $event->duration, get_string('strftimehm', 'googlemeet'), $user->timezone);
    $url = "<a href=\"{$CFG->wwwroot}/mod/googlemeet/view.php?id={$event->cmid}\">
        {$CFG->wwwroot}/mod/googlemeet/view.php?id={$event->cmid}</a>";

    $templatevars = [
        '/%userfirstname%/' => $user->firstname,
        '/%userlastname%/' => $user->lastname,
        '/%coursename%/' => $event->coursename,
        '/%googlemeetname%/' => $event->googlemeetname,
        '/%eventdate%/' => $startdate,
        '/%duration%/' => $starttime . ' – ' . $endtime,
        '/%timezone%/' => usertimezone($user->timezone),
        '/%url%/' => $url,
        '/%cmid%/' => $event->cmid,
    ];

    $patterns = array_keys($templatevars); // The placeholders which are to be replaced.

    $replacements = array_values($templatevars); // The values which are to be templated in for the placeholders.

    // Replace %variable% with relevant value everywhere it occurs.
    $emailcontent = preg_replace($patterns, $replacements, $config->emailcontent);

    return $emailcontent;
}

/**
 * upcoming googlemeet events.
 *
 * @param int $googlemeetid db record of user
 */
function googlemeet_get_upcoming_events($googlemeetid) {
    global $DB, $OUTPUT, $USER;

    $now = time() - MINSECS;

    $sql = "SELECT id,eventdate,duration
              FROM {googlemeet_events}
             WHERE googlemeetid = {$googlemeetid}
               AND (eventdate > {$now} OR eventdate = {$now})
             LIMIT 5";

    $events = $DB->get_records_sql($sql);
    $upcomingevents = [];

    if ($events) {
        foreach ($events as $event) {
            $start = $event->eventdate;
            $end = $event->eventdate + $event->duration;
            $duration = $event->duration;

            $datetime = new DateTime();
            $datetime->setTimestamp(time());
            $nowdate = $datetime->format('Y-m-d');

            $datetime->setTimestamp($start);
            $startdate = $datetime->format('Y-m-d');

            $upcomingevent = new stdClass();
            $upcomingevent->today = $nowdate === $startdate;
            $upcomingevent->startdate = userdate($start, get_string('strftimedm', 'googlemeet'), $USER->timezone);
            array_push($upcomingevents, $upcomingevent);
        }

        echo $OUTPUT->render_from_template('mod_googlemeet/upcomingevents', [
            'upcomingevents' => $upcomingevents,
            'starttime' => userdate($start, get_string('strftimehm', 'googlemeet'), $USER->timezone),
            'endtime' => userdate($end, get_string('strftimehm', 'googlemeet'), $USER->timezone),
            'duration' => $duration,
        ]);
    }
}

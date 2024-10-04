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
 * List of deprecated calendar functions.
 *
 * @package     core_calendar
 * @copyright   2025 Amaia Anabitarte <amaia@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use core\url;
use core_calendar\output\humandate;
use core_calendar\output\humantimeperiod;

/**
 * Return the representation day.
 *
 * @param int $tstamp Timestamp in GMT
 * @param int|bool $now current Unix timestamp
 * @param bool $usecommonwords
 * @return string the formatted date/time
 *
 * @deprecated since Moodle 5.0.
 * @todo MDL-84268 Final deprecation in Moodle 6.0.
 */
#[\core\attribute\deprecated(
    replacement: '\core_calendar\output\humandate',
    since: '5.0',
    mdl: 'MDL-83873',
)]
function calendar_day_representation($tstamp, $now = false, $usecommonwords = true) {
    static $shortformat;

    \core\deprecation::emit_deprecation_if_present(__FUNCTION__);

    if (empty($shortformat)) {
        $shortformat = get_string('strftimedayshort');
    }

    if ($now === false) {
        $now = time();
    }

    // To have it in one place, if a change is needed.
    $formal = userdate($tstamp, $shortformat);

    $datestamp = usergetdate($tstamp);
    $datenow = usergetdate($now);

    if ($usecommonwords == false) {
        // We don't want words, just a date.
        return $formal;
    } else if ($datestamp['year'] == $datenow['year'] && $datestamp['yday'] == $datenow['yday']) {
        return get_string('today', 'calendar');
    } else if (($datestamp['year'] == $datenow['year'] && $datestamp['yday'] == $datenow['yday'] - 1 ) ||
            ($datestamp['year'] == $datenow['year'] - 1 && $datestamp['mday'] == 31 && $datestamp['mon'] == 12
                    && $datenow['yday'] == 1)) {
        return get_string('yesterday', 'calendar');
    } else if (($datestamp['year'] == $datenow['year'] && $datestamp['yday'] == $datenow['yday'] + 1 ) ||
            ($datestamp['year'] == $datenow['year'] + 1 && $datenow['mday'] == 31 && $datenow['mon'] == 12
                    && $datestamp['yday'] == 1)) {
        return get_string('tomorrow', 'calendar');
    } else {
        return $formal;
    }
}

/**
 * return the formatted representation time.
 *
 * @param int $time the timestamp in UTC, as obtained from the database
 * @return string the formatted date/time
 *
 * @deprecated since Moodle 5.0.
 * @todo MDL-84268 Final deprecation in Moodle 6.0.
 */
#[\core\attribute\deprecated(
    replacement: '\core_calendar\output\humandate',
    since: '5.0',
    mdl: 'MDL-83873',
)]
function calendar_time_representation($time) {
    \core\deprecation::emit_deprecation_if_present(__FUNCTION__);

    global $OUTPUT;

    $humantime = humandate::create_from_timestamp(
        timestamp: $time,
        near: null,
        timeonly: true
    );
    return $OUTPUT->render($humantime);
}

/**
 * Get event format time.
 *
 * @param calendar_event $event event object
 * @param int $now current time in gmt
 * @param array $linkparams list of params for event link
 * @param bool $usecommonwords the words as formatted date/time.
 * @param int $showtime determine the show time GMT timestamp
 * @return string $eventtime link/string for event time
 *
 * @deprecated since Moodle 5.0.
 * @todo MDL-84268 Final deprecation in Moodle 6.0.
 */
#[\core\attribute\deprecated(
    replacement: '\core_calendar\output\humantimeperiod',
    since: '5.0',
    mdl: 'MDL-83873',
)]
function calendar_format_event_time($event, $now, $linkparams = null, $usecommonwords = true, $showtime = 0) {
    \core\deprecation::emit_deprecation_if_present(__FUNCTION__);

    global $OUTPUT;

    $humanperiod = humantimeperiod::create_from_timestamp(
        starttimestamp: $event->timestart,
        endtimestamp: $event->timestart + $event->timeduration,
        link: new url(CALENDAR_URL . 'view.php'),
    );

    return $OUTPUT->render($humanperiod);

}

/**
 * @deprecated 3.9
 */
#[\core\attribute\deprecated(
    replacement: 'calendar_add_event_metadata no longer used',
    since: '3.9',
    mdl: 'MDL-58866',
    final: true,
)]
function calendar_add_event_metadata() {
    \core\deprecation::emit_deprecation_if_present(__FUNCTION__);
}

/**
 * Get a HTML link to a course.
 *
 * @param int|stdClass $course the course id or course object
 * @return string a link to the course (as HTML); empty if the course id is invalid
 *
 * @deprecated since 5.0
 * @todo MDL-84268 Final deprecation in Moodle 6.0.
 */
#[\core\attribute\deprecated(
    replacement: 'calendar_get_courselink no longer used',
    since: '5.0',
    mdl: 'MDL-84617',
)]
function calendar_get_courselink($course) {
    \core\deprecation::emit_deprecation_if_present(__FUNCTION__);

    if (!$course) {
        return '';
    }

    if (!is_object($course)) {
        $course = calendar_get_course_cached($coursecache, $course);
    }
    $context = \context_course::instance($course->id);
    $fullname = format_string($course->fullname, true, ['context' => $context]);
    $url = new \moodle_url('/course/view.php', ['id' => $course->id]);
    $link = \html_writer::link($url, $fullname);

    return $link;
}

/**
 * Get per-day basis events
 *
 * @param array $events list of events
 * @param int $month the number of the month
 * @param int $year the number of the year
 * @param array $eventsbyday event on specific day
 * @param array $durationbyday duration of the event in days
 * @param array $typesbyday event type (eg: site, course, user, or group)
 * @param array $courses list of courses
 * @return void
 *
 * @deprecated since 5.0
 * @todo MDL-84268 Final deprecation in Moodle 6.0.
 */
#[\core\attribute\deprecated(
    replacement: 'calendar_events_by_day not used since 3.4',
    since: '5.0',
    mdl: 'MDL-84617',
)]
function calendar_events_by_day($events, $month, $year, &$eventsbyday, &$durationbyday, &$typesbyday, &$courses) {
    \core\deprecation::emit_deprecation_if_present(__FUNCTION__);

    $calendartype = \core_calendar\type_factory::get_calendar_instance();

    $eventsbyday = [];
    $typesbyday = [];
    $durationbyday = [];

    if ($events === false) {
        return;
    }

    foreach ($events as $event) {
        $startdate = $calendartype->timestamp_to_date_array($event->timestart);
        if ($event->timeduration) {
            $enddate = $calendartype->timestamp_to_date_array($event->timestart + $event->timeduration - 1);
        } else {
            $enddate = $startdate;
        }

        // Simple arithmetic: $year * 13 + $month is a distinct integer for each distinct ($year, $month) pair.
        if (!($startdate['year'] * 13 + $startdate['mon'] <= $year * 13 + $month) &&
            ($enddate['year'] * 13 + $enddate['mon'] >= $year * 13 + $month)) {
            continue;
        }

        $eventdaystart = intval($startdate['mday']);

        if ($startdate['mon'] == $month && $startdate['year'] == $year) {
            // Give the event to its day.
            $eventsbyday[$eventdaystart][] = $event->id;

            // Mark the day as having such an event.
            if ($event->courseid == SITEID && $event->groupid == 0) {
                $typesbyday[$eventdaystart]['startsite'] = true;
                // Set event class for site event.
                $events[$event->id]->class = 'calendar_event_site';
            } else if ($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {
                $typesbyday[$eventdaystart]['startcourse'] = true;
                // Set event class for course event.
                $events[$event->id]->class = 'calendar_event_course';
            } else if ($event->groupid) {
                $typesbyday[$eventdaystart]['startgroup'] = true;
                // Set event class for group event.
                $events[$event->id]->class = 'calendar_event_group';
            } else if ($event->userid) {
                $typesbyday[$eventdaystart]['startuser'] = true;
                // Set event class for user event.
                $events[$event->id]->class = 'calendar_event_user';
            }
        }

        if ($event->timeduration == 0) {
            // Proceed with the next.
            continue;
        }

        // The event starts on $month $year or before.
        if ($startdate['mon'] == $month && $startdate['year'] == $year) {
            $lowerbound = intval($startdate['mday']);
        } else {
            $lowerbound = 0;
        }

        // Also, it ends on $month $year or later.
        if ($enddate['mon'] == $month && $enddate['year'] == $year) {
            $upperbound = intval($enddate['mday']);
        } else {
            $upperbound = calendar_days_in_month($month, $year);
        }

        // Mark all days between $lowerbound and $upperbound (inclusive) as duration.
        for ($i = $lowerbound + 1; $i <= $upperbound; ++$i) {
            $durationbyday[$i][] = $event->id;
            if ($event->courseid == SITEID && $event->groupid == 0) {
                $typesbyday[$i]['durationsite'] = true;
            } else if ($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) {
                $typesbyday[$i]['durationcourse'] = true;
            } else if ($event->groupid) {
                $typesbyday[$i]['durationgroup'] = true;
            } else if ($event->userid) {
                $typesbyday[$i]['durationuser'] = true;
            }
        }

    }
    return;
}

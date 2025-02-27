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

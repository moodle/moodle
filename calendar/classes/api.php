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
 * Contains class containing the calendar API.
 *
 * @package    core_calendar
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar;

use context_course;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/calendar/lib.php');

/**
 * Class containing the calendar API.
 *
 * @package    core_calendar
 * @copyright  2017 Mark Nelson <markn@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api {

    /**
     * Return the days of the week.
     *
     * @return array array of days
     */
    public static function get_days() {
        $calendartype = type_factory::get_calendar_instance();
        return $calendartype->get_weekdays();
    }

    /**
     * Get the subscription from a given id.
     *
     * @param int $id id of the subscription
     * @return \stdClass Subscription record from DB
     * @throws \moodle_exception for an invalid id
     */
    public static function get_subscription($id) {
        global $DB;

        $cache = \cache::make('core', 'calendar_subscriptions');
        $subscription = $cache->get($id);
        if (empty($subscription)) {
            $subscription = $DB->get_record('event_subscriptions', array('id' => $id), '*', MUST_EXIST);
            $cache->set($id, $subscription);
        }

        return $subscription;
    }

    /**
     * Gets the first day of the week.
     *
     * @return int
     */
    public static function get_starting_weekday() {
        $calendartype = type_factory::get_calendar_instance();
        return $calendartype->get_starting_weekday();
    }

    /**
     * Generates the HTML for a miniature calendar.
     *
     * @param array $courses list of course to list events from
     * @param array $groups list of group
     * @param array $users user's info
     * @param int|bool $calmonth calendar month in numeric, default is set to false
     * @param int|bool $calyear calendar month in numeric, default is set to false
     * @param string|bool $placement the place/page the calendar is set to appear - passed on the the controls function
     * @param int|bool $courseid id of the course the calendar is displayed on - passed on the the controls function
     * @param int $time the unixtimestamp representing the date we want to view, this is used instead of $calmonth
     *     and $calyear to support multiple calendars
     * @return string $content return html table for mini calendar
     */
    public static function get_mini_calendar($courses, $groups, $users, $calmonth = false, $calyear = false,
                                             $placement = false, $courseid = false, $time = 0) {
        global $CFG, $OUTPUT, $PAGE;

        // Get the calendar type we are using.
        $calendartype = type_factory::get_calendar_instance();

        $display = new \stdClass;

        // Assume we are not displaying this month for now.
        $display->thismonth = false;

        $content = '';

        // Do this check for backwards compatibility.
        // The core should be passing a timestamp rather than month and year.
        // If a month and year are passed they will be in Gregorian.
        if (!empty($calmonth) && !empty($calyear)) {
            // Ensure it is a valid date, else we will just set it to the current timestamp.
            if (checkdate($calmonth, 1, $calyear)) {
                $time = make_timestamp($calyear, $calmonth, 1);
            } else {
                $time = time();
            }
            $date = usergetdate($time);
            if ($calmonth == $date['mon'] && $calyear == $date['year']) {
                $display->thismonth = true;
            }
            // We can overwrite date now with the date used by the calendar type,
            // if it is not Gregorian, otherwise there is no need as it is already in Gregorian.
            if ($calendartype->get_name() != 'gregorian') {
                $date = $calendartype->timestamp_to_date_array($time);
            }
        } else if (!empty($time)) {
            // Get the specified date in the calendar type being used.
            $date = $calendartype->timestamp_to_date_array($time);
            $thisdate = $calendartype->timestamp_to_date_array(time());
            if ($date['month'] == $thisdate['month'] && $date['year'] == $thisdate['year']) {
                $display->thismonth = true;
                // If we are the current month we want to set the date to the current date, not the start of the month.
                $date = $thisdate;
            }
        } else {
            // Get the current date in the calendar type being used.
            $time = time();
            $date = $calendartype->timestamp_to_date_array($time);
            $display->thismonth = true;
        }

        list($d, $m, $y) = array($date['mday'], $date['mon'], $date['year']); // This is what we want to display.

        // Get Gregorian date for the start of the month.
        $gregoriandate = $calendartype->convert_to_gregorian($date['year'], $date['mon'], 1);

        // Store the gregorian date values to be used later.
        list($gy, $gm, $gd, $gh, $gmin) = array($gregoriandate['year'], $gregoriandate['month'], $gregoriandate['day'],
            $gregoriandate['hour'], $gregoriandate['minute']);

        // Get the max number of days in this month for this calendar type.
        $display->maxdays = self::get_days_in_month($m, $y);
        // Get the starting week day for this month.
        $startwday = dayofweek(1, $m, $y);
        // Get the days in a week.
        $daynames = self::get_days();
        // Store the number of days in a week.
        $numberofdaysinweek = $calendartype->get_num_weekdays();

        // Set the min and max weekday.
        $display->minwday = self::get_starting_weekday();
        $display->maxwday = $display->minwday + ($numberofdaysinweek - 1);

        // These are used for DB queries, so we want unixtime, so we need to use Gregorian dates.
        $display->tstart = make_timestamp($gy, $gm, $gd, $gh, $gmin, 0);
        $display->tend = $display->tstart + ($display->maxdays * DAYSECS) - 1;

        // Align the starting weekday to fall in our display range.
        // This is simple, not foolproof.
        if ($startwday < $display->minwday) {
            $startwday += $numberofdaysinweek;
        }

        // Get the events matching our criteria. Don't forget to offset the timestamps for the user's TZ.
        $events = self::get_events($display->tstart, $display->tend, $users, $groups, $courses);

        // Set event course class for course events.
        if (!empty($events)) {
            foreach ($events as $eventid => $event) {
                if (!empty($event->modulename)) {
                    $cm = get_coursemodule_from_instance($event->modulename, $event->instance);
                    if (!\core_availability\info_module::is_user_visible($cm, 0, false)) {
                        unset($events[$eventid]);
                    }
                }
            }
        }

        // This is either a genius idea or an idiot idea: in order to not complicate things, we use this rule: if, after
        // possibly removing SITEID from $courses, there is only one course left, then clicking on a day in the month
        // will also set the $SESSION->cal_courses_shown variable to that one course. Otherwise, we 'd need to add extra
        // arguments to this function.
        $hrefparams = array();
        if (!empty($courses)) {
            $courses = array_diff($courses, array(SITEID));
            if (count($courses) == 1) {
                $hrefparams['course'] = reset($courses);
            }
        }

        // We want to have easy access by day, since the display is on a per-day basis.
        self::get_events_by_day($events, $m, $y, $eventsbyday, $durationbyday, $typesbyday, $courses);

        // Accessibility: added summary and <abbr> elements.
        $summary = get_string('calendarheading', 'calendar', userdate($display->tstart, get_string('strftimemonthyear')));
        // Begin table.
        $content .= '<table class="minicalendar calendartable" summary="' . $summary . '">';
        if (($placement !== false) && ($courseid !== false)) {
            $content .= '<caption>' . self::get_top_controls($placement,
                    array('id' => $courseid, 'time' => $time)) . '</caption>';
        }
        $content .= '<tr class="weekdays">'; // Header row: day names.

        // Print out the names of the weekdays.
        for ($i = $display->minwday; $i <= $display->maxwday; $i++) {
            $pos = $i % $numberofdaysinweek;
            $content .= '<th scope="col"><abbr title="' . $daynames[$pos]['fullname'] . '">' .
                $daynames[$pos]['shortname'] . "</abbr></th>\n";
        }

        $content .= '</tr><tr>'; // End of day names; prepare for day numbers.

        // For the table display. $week is the row; $dayweek is the column.
        $dayweek = $startwday;

        // Padding (the first week may have blank days in the beginning).
        for ($i = $display->minwday; $i < $startwday; ++$i) {
            $content .= '<td class="dayblank">&nbsp;</td>' ."\n";
        }

        $weekend = CALENDAR_DEFAULT_WEEKEND;
        if (isset($CFG->calendar_weekend)) {
            $weekend = intval($CFG->calendar_weekend);
        }

        // Now display all the calendar.
        $daytime = strtotime('-1 day', $display->tstart);
        for ($day = 1; $day <= $display->maxdays; ++$day, ++$dayweek) {
            $cellattributes = array();
            $daytime = strtotime('+1 day', $daytime);
            if ($dayweek > $display->maxwday) {
                // We need to change week (table row).
                $content .= '</tr><tr>';
                $dayweek = $display->minwday;
            }

            // Reset vars.
            if ($weekend & (1 << ($dayweek % $numberofdaysinweek))) {
                // Weekend. This is true no matter what the exact range is.
                $class = 'weekend day';
            } else {
                // Normal working day.
                $class = 'day';
            }

            $eventids = array();
            if (!empty($eventsbyday[$day])) {
                $eventids = $eventsbyday[$day];
            }

            if (!empty($durationbyday[$day])) {
                $eventids = array_unique(array_merge($eventids, $durationbyday[$day]));
            }

            $finishclass = false;

            if (!empty($eventids)) {
                // There is at least one event on this day.
                $class .= ' hasevent';
                $hrefparams['view'] = 'day';
                $dayhref = self::get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $hrefparams), 0, 0, 0, $daytime);

                $popupcontent = '';
                foreach ($eventids as $eventid) {
                    if (!isset($events[$eventid])) {
                        continue;
                    }
                    $event = new event($events[$eventid]);
                    $popupalt  = '';
                    $component = 'moodle';
                    if (!empty($event->modulename)) {
                        $popupicon = 'icon';
                        $popupalt  = $event->modulename;
                        $component = $event->modulename;
                    } else if ($event->courseid == SITEID) { // Site event.
                        $popupicon = 'i/siteevent';
                    } else if ($event->courseid != 0 && $event->courseid != SITEID
                        && $event->groupid == 0) { // Course event.
                        $popupicon = 'i/courseevent';
                    } else if ($event->groupid) { // Group event.
                        $popupicon = 'i/groupevent';
                    } else { // Must be a user event.
                        $popupicon = 'i/userevent';
                    }

                    if ($event->timeduration) {
                        $startdate = $calendartype->timestamp_to_date_array($event->timestart);
                        $enddate = $calendartype->timestamp_to_date_array($event->timestart + $event->timeduration - 1);
                        if ($enddate['mon'] == $m && $enddate['year'] == $y && $enddate['mday'] == $day) {
                            $finishclass = true;
                        }
                    }

                    $dayhref->set_anchor('event_' . $event->id);

                    $popupcontent .= \html_writer::start_tag('div');
                    $popupcontent .= $OUTPUT->pix_icon($popupicon, $popupalt, $component);
                    // Show ical source if needed.
                    if (!empty($event->subscription) && $CFG->calendar_showicalsource) {
                        $a = new \stdClass();
                        $a->name = format_string($event->name, true);
                        $a->source = $event->subscription->name;
                        $name = get_string('namewithsource', 'calendar', $a);
                    } else {
                        if ($finishclass) {
                            $samedate = $startdate['mon'] == $enddate['mon'] &&
                                $startdate['year'] == $enddate['year'] &&
                                $startdate['mday'] == $enddate['mday'];

                            if ($samedate) {
                                $name = format_string($event->name, true);
                            } else {
                                $name = format_string($event->name, true) . ' (' . get_string('eventendtime', 'calendar') . ')';
                            }
                        } else {
                            $name = format_string($event->name, true);
                        }
                    }
                    $popupcontent .= \html_writer::link($dayhref, $name);
                    $popupcontent .= \html_writer::end_tag('div');
                }

                if ($display->thismonth && $day == $d) {
                    $popupdata = self::get_popup(true, $daytime, $popupcontent);
                } else {
                    $popupdata = self::get_popup(false, $daytime, $popupcontent);
                }

                // Class and cell content.
                if (isset($typesbyday[$day]['startglobal'])) {
                    $class .= ' calendar_event_global';
                } else if (isset($typesbyday[$day]['startcourse'])) {
                    $class .= ' calendar_event_course';
                } else if (isset($typesbyday[$day]['startgroup'])) {
                    $class .= ' calendar_event_group';
                } else if (isset($typesbyday[$day]['startuser'])) {
                    $class .= ' calendar_event_user';
                }
                if ($finishclass) {
                    $class .= ' duration_finish';
                }
                $data = array(
                    'url' => $dayhref,
                    'day' => $day,
                    'content' => $popupdata['data-core_calendar-popupcontent'],
                    'title' => $popupdata['data-core_calendar-title']
                );
                $cell = $OUTPUT->render_from_template('core_calendar/minicalendar_day_link', $data);
            } else {
                $cell = $day;
            }

            $durationclass = false;
            if (isset($typesbyday[$day]['durationglobal'])) {
                $durationclass = ' duration_global';
            } else if (isset($typesbyday[$day]['durationcourse'])) {
                $durationclass = ' duration_course';
            } else if (isset($typesbyday[$day]['durationgroup'])) {
                $durationclass = ' duration_group';
            } else if (isset($typesbyday[$day]['durationuser'])) {
                $durationclass = ' duration_user';
            }
            if ($durationclass) {
                $class .= ' duration ' . $durationclass;
            }

            // If event has a class set then add it to the table day <td> tag.
            // Note: only one colour for minicalendar.
            if (isset($eventsbyday[$day])) {
                foreach ($eventsbyday[$day] as $eventid) {
                    if (!isset($events[$eventid])) {
                        continue;
                    }
                    $event = $events[$eventid];
                    if (!empty($event->class)) {
                        $class .= ' ' . $event->class;
                    }
                    break;
                }
            }

            if ($display->thismonth && $day == $d) {
                // The current cell is for today - add appropriate classes and additional information for styling.
                $class .= ' today';
                $today = get_string('today', 'calendar') . ' ' . userdate(time(), get_string('strftimedayshort'));

                if (!isset($eventsbyday[$day]) && !isset($durationbyday[$day])) {
                    $class .= ' eventnone';
                    $popupdata = self::get_popup(true, false);
                    $data = array(
                        'url' => '#',
                        'day' => $day,
                        'content' => $popupdata['data-core_calendar-popupcontent'],
                        'title' => $popupdata['data-core_calendar-title']
                    );
                    $cell = $OUTPUT->render_from_template('core_calendar/minicalendar_day_link', $data);
                }
                $cell = get_accesshide($today . ' ') . $cell;
            }

            // Just display it.
            $cellattributes['class'] = $class;
            $content .= \html_writer::tag('td', $cell, $cellattributes);
        }

        // Padding (the last week may have blank days at the end).
        for ($i = $dayweek; $i <= $display->maxwday; ++$i) {
            $content .= '<td class="dayblank">&nbsp;</td>';
        }
        $content .= '</tr>'; // Last row ends.

        $content .= '</table>'; // Tabular display of days ends.
        return $content;
    }

    /**
     * Gets the calendar popup.
     *
     * @param bool $today false except when called on the current day.
     * @param mixed $timestart $events[$eventid]->timestart, OR false if there are no events.
     * @param string $popupcontent content for the popup window/layout.
     * @return string eventid for the calendar_tooltip popup window/layout.
     */
    public static function get_popup($today = false, $timestart, $popupcontent = '') {
        $popupcaption = '';
        if ($today) {
            $popupcaption = get_string('today', 'calendar') . ' ';
        }

        if (false === $timestart) {
            $popupcaption .= userdate(time(), get_string('strftimedayshort'));
            $popupcontent = get_string('eventnone', 'calendar');

        } else {
            $popupcaption .= get_string('eventsfor', 'calendar', userdate($timestart, get_string('strftimedayshort')));
        }

        return array(
            'data-core_calendar-title' => $popupcaption,
            'data-core_calendar-popupcontent' => $popupcontent,
        );
    }

    /**
     * Gets the calendar upcoming events.
     *
     * @param array $courses array of courses
     * @param array|int|bool $groups array of groups, group id or boolean for all/no group events
     * @param array|int|bool $users array of users, user id or boolean for all/no user events
     * @param int $daysinfuture number of days in the future we 'll look
     * @param int $maxevents maximum number of events
     * @param int $fromtime start time
     * @return array $output array of upcoming events
     */
    public static function get_upcoming($courses, $groups, $users, $daysinfuture, $maxevents, $fromtime = 0) {
        global $COURSE;

        $display = new \stdClass;
        $display->range = $daysinfuture; // How many days in the future we 'll look.
        $display->maxevents = $maxevents;

        $output = array();

        $processed = 0;
        $now = time(); // We 'll need this later.
        $usermidnighttoday = usergetmidnight($now);

        if ($fromtime) {
            $display->tstart = $fromtime;
        } else {
            $display->tstart = $usermidnighttoday;
        }

        // This works correctly with respect to the user's DST, but it is accurate
        // only because $fromtime is always the exact midnight of some day!
        $display->tend = usergetmidnight($display->tstart + DAYSECS * $display->range + 3 * HOURSECS) - 1;

        // Get the events matching our criteria.
        $events = self::get_events($display->tstart, $display->tend, $users, $groups, $courses);

        // This is either a genius idea or an idiot idea: in order to not complicate things, we use this rule: if, after
        // possibly removing SITEID from $courses, there is only one course left, then clicking on a day in the month
        // will also set the $SESSION->cal_courses_shown variable to that one course. Otherwise, we 'd need to add extra
        // arguments to this function.
        $hrefparams = array();
        if (!empty($courses)) {
            $courses = array_diff($courses, array(SITEID));
            if (count($courses) == 1) {
                $hrefparams['course'] = reset($courses);
            }
        }

        if ($events !== false) {
            $modinfo = get_fast_modinfo($COURSE);
            foreach ($events as $event) {
                if (!empty($event->modulename)) {
                    if ($event->courseid == $COURSE->id) {
                        if (isset($modinfo->instances[$event->modulename][$event->instance])) {
                            $cm = $modinfo->instances[$event->modulename][$event->instance];
                            if (!$cm->uservisible) {
                                continue;
                            }
                        }
                    } else {
                        if (!$cm = get_coursemodule_from_instance($event->modulename, $event->instance)) {
                            continue;
                        }
                        if (!\core_availability\info_module::is_user_visible($cm, 0, false)) {
                            continue;
                        }
                    }
                }

                if ($processed >= $display->maxevents) {
                    break;
                }

                $event->time = self::get_format_event_time($event, $now, $hrefparams);
                $output[] = $event;
                $processed++;
            }
        }

        return $output;
    }

    /**
     * Get a HTML link to a course.
     *
     * @param int $courseid the course id
     * @return string a link to the course (as HTML); empty if the course id is invalid
     */
    public static function get_courselink($courseid) {
        if (!$courseid) {
            return '';
        }

        self::get_course_cached($coursecache, $courseid);
        $context = \context_course::instance($courseid);
        $fullname = format_string($coursecache[$courseid]->fullname, true, array('context' => $context));
        $url = new \moodle_url('/course/view.php', array('id' => $courseid));
        $link = \html_writer::link($url, $fullname);

        return $link;
    }

    /**
     * Get current module cache.
     *
     * @param array $coursecache list of course cache
     * @param string $modulename name of the module
     * @param int $instance module instance number
     * @return \stdClass|bool $module information
     */
    public static function get_module_cached(&$coursecache, $modulename, $instance) {
        $module = get_coursemodule_from_instance($modulename, $instance);

        if ($module === false) {
            return false;
        }

        if (!self::get_course_cached($coursecache, $module->course)) {
            return false;
        }
        return $module;
    }

    /**
     * Get current course cache.
     *
     * @param array $coursecache list of course cache
     * @param int $courseid id of the course
     * @return \stdClass $coursecache[$courseid] return the specific course cache
     */
    public static function get_course_cached(&$coursecache, $courseid) {
        if (!isset($coursecache[$courseid])) {
            $coursecache[$courseid] = get_course($courseid);
        }
        return $coursecache[$courseid];
    }

    /**
     * Get group from groupid for calendar display.
     *
     * @param int $groupid
     * @return \stdClass group object with fields 'id', 'name' and 'courseid'
     */
    public static function get_group_cached($groupid) {
        static $groupscache = array();
        if (!isset($groupscache[$groupid])) {
            $groupscache[$groupid] = groups_get_group($groupid, 'id,name,courseid');
        }
        return $groupscache[$groupid];
    }

    /**
     * Add calendar event metadata.
     *
     * @param \stdClass $event event info
     * @return \stdClass $event metadata
     */
    public static function add_event_metadata($event) {
        global $CFG, $OUTPUT;

        // Support multilang in event->name.
        $event->name = format_string($event->name, true);

        if (!empty($event->modulename)) { // Activity event.
            // The module name is set. I will assume that it has to be displayed, and
            // also that it is an automatically-generated event. And of course that the
            // fields for get_coursemodule_from_instance are set correctly.
            $module = self::get_module_cached($coursecache, $event->modulename, $event->instance);

            if ($module === false) {
                return;
            }

            $modulename = get_string('modulename', $event->modulename);
            if (get_string_manager()->string_exists($event->eventtype, $event->modulename)) {
                // Will be used as alt text if the event icon.
                $eventtype = get_string($event->eventtype, $event->modulename);
            } else {
                $eventtype = '';
            }
            $icon = $OUTPUT->pix_url('icon', $event->modulename) . '';

            $event->icon = '<img src="' . $icon . '" alt="' . $eventtype . '" title="' . $modulename . '" class="icon" />';
            $event->referer = '<a href="' . $CFG->wwwroot . '/mod/' . $event->modulename . '/view.php?id=' .
                $module->id . '">' . $event->name . '</a>';
            $event->courselink = self::get_courselink($module->course);
            $event->cmid = $module->id;
        } else if ($event->courseid == SITEID) { // Site event.
            $event->icon = '<img src="' . $OUTPUT->pix_url('i/siteevent') . '" alt="' .
                get_string('globalevent', 'calendar') . '" class="icon" />';
            $event->cssclass = 'calendar_event_global';
        } else if ($event->courseid != 0 && $event->courseid != SITEID && $event->groupid == 0) { // Course event.
            $event->icon = '<img src="' . $OUTPUT->pix_url('i/courseevent') . '" alt="' .
                get_string('courseevent', 'calendar') . '" class="icon" />';
            $event->courselink = self::get_courselink($event->courseid);
            $event->cssclass = 'calendar_event_course';
        } else if ($event->groupid) { // Group event.
            if ($group = self::get_group_cached($event->groupid)) {
                $groupname = format_string($group->name, true, \context_course::instance($group->courseid));
            } else {
                $groupname = '';
            }
            $event->icon = \html_writer::empty_tag('image', array('src' => $OUTPUT->pix_url('i/groupevent'),
                'alt' => get_string('groupevent', 'calendar'), 'title' => $groupname, 'class' => 'icon'));
            $event->courselink = self::get_courselink($event->courseid) . ', ' . $groupname;
            $event->cssclass = 'calendar_event_group';
        } else if ($event->userid) { // User event.
            $event->icon = '<img src="' . $OUTPUT->pix_url('i/userevent') . '" alt="' .
                get_string('userevent', 'calendar') . '" class="icon" />';
            $event->cssclass = 'calendar_event_user';
        }

        return $event;
    }

    /**
     * Get calendar events
     *
     * @param int $tstart Start time of time range for events
     * @param int $tend End time of time range for events
     * @param array|int|boolean $users array of users, user id or boolean for all/no user events
     * @param array|int|boolean $groups array of groups, group id or boolean for all/no group events
     * @param array|int|boolean $courses array of courses, course id or boolean for all/no course events
     * @param boolean $withduration whether only events starting within time range selected
     *                              or events in progress/already started selected as well
     * @param boolean $ignorehidden whether to select only visible events or all events
     * @return array $events of selected events or an empty array if there aren't any (or there was an error)
     */
    public static function get_events($tstart, $tend, $users, $groups, $courses, $withduration = true, $ignorehidden = true) {
        global $DB;

        $params = array();
        // Quick test.
        if (empty($users) && empty($groups) && empty($courses)) {
            return array();
        }

        // Array of filter conditions. To be concatenated by the OR operator.
        $filters = [];

        // User filter.
        if ((is_array($users) && !empty($users)) or is_numeric($users)) {
            // Events from a number of users.
            list($insqlusers, $inparamsusers) = $DB->get_in_or_equal($users, SQL_PARAMS_NAMED);
            $filters[] = "(e.userid $insqlusers AND e.courseid = 0 AND e.groupid = 0)";
            $params = array_merge($params, $inparamsusers);
        } else if ($users === true) {
            // Events from ALL users.
            $filters[] = "(e.userid != 0 AND e.courseid = 0 AND e.groupid = 0)";
        }
        // Boolean false (no users at all): We don't need to do anything.

        // Group filter.
        if ((is_array($groups) && !empty($groups)) or is_numeric($groups)) {
            // Events from a number of groups.
            list($insqlgroups, $inparamsgroups) = $DB->get_in_or_equal($groups, SQL_PARAMS_NAMED);
            $filters[] = "e.groupid $insqlgroups";
            $params = array_merge($params, $inparamsgroups);
        } else if ($groups === true) {
            // Events from ALL groups.
            $filters[] = "e.groupid != 0";
        }
        // Boolean false (no groups at all): We don't need to do anything.

        // Course filter.
        if ((is_array($courses) && !empty($courses)) or is_numeric($courses)) {
            list($insqlcourses, $inparamscourses) = $DB->get_in_or_equal($courses, SQL_PARAMS_NAMED);
            $filters[] = "(e.groupid = 0 AND e.courseid $insqlcourses)";
            $params = array_merge($params, $inparamscourses);
        } else if ($courses === true) {
            // Events from ALL courses.
            $filters[] = "(e.groupid = 0 AND e.courseid != 0)";
        }

        // Security check: if, by now, we have NOTHING in $whereclause, then it means
        // that NO event-selecting clauses were defined. Thus, we won't be returning ANY
        // events no matter what. Allowing the code to proceed might return a completely
        // valid query with only time constraints, thus selecting ALL events in that time frame!
        if (empty($filters)) {
            return array();
        }

        // Build our clause for the filters.
        $filterclause = implode(' OR ', $filters);

        // Array of where conditions for our query. To be concatenated by the AND operator.
        $whereconditions = ["($filterclause)"];

        // Time clause.
        if ($withduration) {
            $timeclause = "((e.timestart >= :tstart1 OR e.timestart + e.timeduration > :tstart2) AND e.timestart <= :tend)";
            $params['tstart1'] = $tstart;
            $params['tstart2'] = $tstart;
            $params['tend'] = $tend;
        } else {
            $timeclause = "(e.timestart >= :tstart AND e.timestart <= :tend)";
            $params['tstart'] = $tstart;
            $params['tend'] = $tend;
        }
        $whereconditions[] = $timeclause;

        // Show visible only.
        if ($ignorehidden) {
            $whereconditions[] = "(e.visible = 1)";
        }

        // Build the main query's WHERE clause.
        $whereclause = implode(' AND ', $whereconditions);

        // Build SQL subquery and conditions for filtered events based on priorities.
        $subquerywhere = '';
        $subqueryconditions = [];

        // Get the user's courses. Otherwise, get the default courses being shown by the calendar.
        $usercourses = self::get_default_courses();

        // Set calendar filters.
        list($usercourses, $usergroups, $user) = self::set_filters($usercourses, true);
        $subqueryparams = [];

        // Flag to indicate whether the query needs to exclude group overrides.
        $viewgroupsonly = false;

        if ($user) {
            // Set filter condition for the user's events.
            $subqueryconditions[] = "(ev.userid = :user AND ev.courseid = 0 AND ev.groupid = 0)";
            $subqueryparams['user'] = $user;

            foreach ($usercourses as $courseid) {
                if (has_capability('moodle/site:accessallgroups', context_course::instance($courseid))) {
                    $usergroupmembership = groups_get_all_groups($courseid, $user, 0, 'g.id');
                    if (count($usergroupmembership) == 0) {
                        $viewgroupsonly = true;
                        break;
                    }
                }
            }
        }

        // Set filter condition for the user's group events.
        if ($usergroups === true || $viewgroupsonly) {
            // Fetch group events, but not group overrides.
            $subqueryconditions[] = "(ev.groupid != 0 AND ev.eventtype = 'group')";
        } else if (!empty($usergroups)) {
            // Fetch group events and group overrides.
            list($inusergroups, $inusergroupparams) = $DB->get_in_or_equal($usergroups, SQL_PARAMS_NAMED);
            $subqueryconditions[] = "(ev.groupid $inusergroups)";
            $subqueryparams = array_merge($subqueryparams, $inusergroupparams);
        }

        // Get courses to be used for the subquery.
        $subquerycourses = [];
        if (is_array($courses)) {
            $subquerycourses = $courses;
        } else if (is_numeric($courses)) {
            $subquerycourses[] = $courses;
        }
        // Merge with user courses, if necessary.
        if (!empty($usercourses)) {
            $subquerycourses = array_merge($subquerycourses, $usercourses);
            // Make sure we remove duplicate values.
            $subquerycourses = array_unique($subquerycourses);
        }

        // Set subquery filter condition for the courses.
        if (!empty($subquerycourses)) {
            list($incourses, $incoursesparams) = $DB->get_in_or_equal($subquerycourses, SQL_PARAMS_NAMED);
            $subqueryconditions[] = "(ev.groupid = 0 AND ev.courseid $incourses)";
            $subqueryparams = array_merge($subqueryparams, $incoursesparams);
        }

        // Build the WHERE condition for the sub-query.
        if (!empty($subqueryconditions)) {
            $subquerywhere = 'WHERE ' . implode(" OR ", $subqueryconditions);
        }

        // Merge subquery parameters to the parameters of the main query.
        if (!empty($subqueryparams)) {
            $params = array_merge($params, $subqueryparams);
        }

        // Sub-query that fetches the list of unique events that were filtered based on priority.
        $subquery = "SELECT ev.modulename,
                            ev.instance,
                            ev.eventtype,
                            MAX(ev.priority) as priority
                       FROM {event} ev
                      $subquerywhere
                   GROUP BY ev.modulename, ev.instance, ev.eventtype";

        // Build the main query.
        $sql = "SELECT e.*
                  FROM {event} e
            INNER JOIN ($subquery) fe
                    ON e.modulename = fe.modulename
                       AND e.instance = fe.instance
                       AND e.eventtype = fe.eventtype
                       AND (e.priority = fe.priority OR (e.priority IS NULL AND fe.priority IS NULL))
             LEFT JOIN {modules} m
                    ON e.modulename = m.name
                 WHERE (m.visible = 1 OR m.visible IS NULL) AND $whereclause
              ORDER BY e.timestart";
        $events = $DB->get_records_sql($sql, $params);

        if ($events === false) {
            $events = array();
        }
        return $events;
    }

    /**
     * Get calendar events by id.
     *
     * @param array $eventids list of event ids
     * @return array Array of event entries, empty array if nothing found
     */
    public static function get_events_by_id($eventids) {
        global $DB;

        if (!is_array($eventids) || empty($eventids)) {
            return array();
        }

        list($wheresql, $params) = $DB->get_in_or_equal($eventids);
        $wheresql = "id $wheresql";

        return $DB->get_records_select('event', $wheresql, $params);
    }

    /**
     * Get control options for calendar.
     *
     * @param string $type of calendar
     * @param array $data calendar information
     * @return string $content return available control for the calender in html
     */
    public static function get_top_controls($type, $data) {
        global $PAGE, $OUTPUT;

        // Get the calendar type we are using.
        $calendartype = type_factory::get_calendar_instance();

        $content = '';

        // Ensure course id passed if relevant.
        $courseid = '';
        if (!empty($data['id'])) {
            $courseid = '&amp;course=' . $data['id'];
        }

        // If we are passing a month and year then we need to convert this to a timestamp to
        // support multiple calendars. No where in core should these be passed, this logic
        // here is for third party plugins that may use this function.
        if (!empty($data['m']) && !empty($date['y'])) {
            if (!isset($data['d'])) {
                $data['d'] = 1;
            }
            if (!checkdate($data['m'], $data['d'], $data['y'])) {
                $time = time();
            } else {
                $time = make_timestamp($data['y'], $data['m'], $data['d']);
            }
        } else if (!empty($data['time'])) {
            $time = $data['time'];
        } else {
            $time = time();
        }

        // Get the date for the calendar type.
        $date = $calendartype->timestamp_to_date_array($time);

        $urlbase = $PAGE->url;

        // We need to get the previous and next months in certain cases.
        if ($type == 'frontpage' || $type == 'course' || $type == 'month') {
            $prevmonth = self::get_prev_month($date['mon'], $date['year']);
            $prevmonthtime = $calendartype->convert_to_gregorian($prevmonth[1], $prevmonth[0], 1);
            $prevmonthtime = make_timestamp($prevmonthtime['year'], $prevmonthtime['month'], $prevmonthtime['day'],
                $prevmonthtime['hour'], $prevmonthtime['minute']);

            $nextmonth = self::get_next_month($date['mon'], $date['year']);
            $nextmonthtime = $calendartype->convert_to_gregorian($nextmonth[1], $nextmonth[0], 1);
            $nextmonthtime = make_timestamp($nextmonthtime['year'], $nextmonthtime['month'], $nextmonthtime['day'],
                $nextmonthtime['hour'], $nextmonthtime['minute']);
        }

        switch ($type) {
            case 'frontpage':
                $prevlink = self::get_link_previous(get_string('monthprev', 'access'), $urlbase, false, false, false,
                    true, $prevmonthtime);
                $nextlink = self::get_link_next(get_string('monthnext', 'access'), $urlbase, false, false, false, true,
                    $nextmonthtime);
                $calendarlink = self::get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', array('view' => 'month')),
                    false, false, false, $time);

                if (!empty($data['id'])) {
                    $calendarlink->param('course', $data['id']);
                }

                $right = $nextlink;

                $content .= \html_writer::start_tag('div', array('class' => 'calendar-controls'));
                $content .= $prevlink . '<span class="hide"> | </span>';
                $content .= \html_writer::tag('span', \html_writer::link($calendarlink,
                    userdate($time, get_string('strftimemonthyear')), array('title' => get_string('monththis', 'calendar'))
                    ), array('class' => 'current'));
                $content .= '<span class="hide"> | </span>' . $right;
                $content .= "<span class=\"clearer\"><!-- --></span>\n";
                $content .= \html_writer::end_tag('div');

                break;
            case 'course':
                $prevlink = self::get_link_previous(get_string('monthprev', 'access'), $urlbase, false, false, false,
                    true, $prevmonthtime);
                $nextlink = self::get_link_next(get_string('monthnext', 'access'), $urlbase, false, false, false,
                    true, $nextmonthtime);
                $calendarlink = self::get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', array('view' => 'month')),
                    false, false, false, $time);

                if (!empty($data['id'])) {
                    $calendarlink->param('course', $data['id']);
                }

                $content .= \html_writer::start_tag('div', array('class' => 'calendar-controls'));
                $content .= $prevlink . '<span class="hide"> | </span>';
                $content .= \html_writer::tag('span', \html_writer::link($calendarlink,
                    userdate($time, get_string('strftimemonthyear')), array('title' => get_string('monththis', 'calendar'))
                    ), array('class' => 'current'));
                $content .= '<span class="hide"> | </span>' . $nextlink;
                $content .= "<span class=\"clearer\"><!-- --></span>";
                $content .= \html_writer::end_tag('div');
                break;
            case 'upcoming':
                $calendarlink = self::get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', array('view' => 'upcoming')),
                    false, false, false, $time);
                if (!empty($data['id'])) {
                    $calendarlink->param('course', $data['id']);
                }
                $calendarlink = \html_writer::link($calendarlink, userdate($time, get_string('strftimemonthyear')));
                $content .= \html_writer::tag('div', $calendarlink, array('class' => 'centered'));
                break;
            case 'display':
                $calendarlink = self::get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', array('view' => 'month')),
                    false, false, false, $time);
                if (!empty($data['id'])) {
                    $calendarlink->param('course', $data['id']);
                }
                $calendarlink = \html_writer::link($calendarlink, userdate($time, get_string('strftimemonthyear')));
                $content .= \html_writer::tag('h3', $calendarlink);
                break;
            case 'month':
                $prevlink = self::get_link_previous(userdate($prevmonthtime, get_string('strftimemonthyear')),
                    'view.php?view=month' . $courseid . '&amp;', false, false, false, false, $prevmonthtime);
                $nextlink = self::get_link_next(userdate($nextmonthtime, get_string('strftimemonthyear')),
                    'view.php?view=month' . $courseid . '&amp;', false, false, false, false, $nextmonthtime);

                $content .= \html_writer::start_tag('div', array('class' => 'calendar-controls'));
                $content .= $prevlink . '<span class="hide"> | </span>';
                $content .= $OUTPUT->heading(userdate($time, get_string('strftimemonthyear')), 2, 'current');
                $content .= '<span class="hide"> | </span>' . $nextlink;
                $content .= '<span class="clearer"><!-- --></span>';
                $content .= \html_writer::end_tag('div')."\n";
                break;
            case 'day':
                $days = self::get_days();

                $prevtimestamp = strtotime('-1 day', $time);
                $nexttimestamp = strtotime('+1 day', $time);

                $prevdate = $calendartype->timestamp_to_date_array($prevtimestamp);
                $nextdate = $calendartype->timestamp_to_date_array($nexttimestamp);

                $prevname = $days[$prevdate['wday']]['fullname'];
                $nextname = $days[$nextdate['wday']]['fullname'];
                $prevlink = self::get_link_previous($prevname, 'view.php?view=day' . $courseid . '&amp;', false, false,
                    false, false, $prevtimestamp);
                $nextlink = self::get_link_next($nextname, 'view.php?view=day' . $courseid . '&amp;', false, false, false,
                    false, $nexttimestamp);

                $content .= \html_writer::start_tag('div', array('class' => 'calendar-controls'));
                $content .= $prevlink;
                $content .= '<span class="hide"> | </span><span class="current">' .userdate($time,
                        get_string('strftimedaydate')) . '</span>';
                $content .= '<span class="hide"> | </span>' . $nextlink;
                $content .= "<span class=\"clearer\"><!-- --></span>";
                $content .= \html_writer::end_tag('div') . "\n";

                break;
        }

        return $content;
    }

    /**
     * Formats a filter control element.
     *
     * @param \moodle_url $url of the filter
     * @param int $type constant defining the type filter
     * @return string html content of the element
     */
    public static function get_filter_controls_element(\moodle_url $url, $type) {
        global $OUTPUT;

        switch ($type) {
            case CALENDAR_EVENT_GLOBAL:
                $typeforhumans = 'global';
                $class = 'calendar_event_global';
                break;
            case CALENDAR_EVENT_COURSE:
                $typeforhumans = 'course';
                $class = 'calendar_event_course';
                break;
            case CALENDAR_EVENT_GROUP:
                $typeforhumans = 'groups';
                $class = 'calendar_event_group';
                break;
            case CALENDAR_EVENT_USER:
                $typeforhumans = 'user';
                $class = 'calendar_event_user';
                break;
        }

        if (self::show_event_type($type)) {
            $icon = $OUTPUT->pix_icon('t/hide', get_string('hide'));
            $str = get_string('hide' . $typeforhumans . 'events', 'calendar');
        } else {
            $icon = $OUTPUT->pix_icon('t/show', get_string('show'));
            $str = get_string('show' . $typeforhumans . 'events', 'calendar');
        }
        $content = \html_writer::start_tag('li', array('class' => 'calendar_event'));
        $content .= \html_writer::start_tag('a', array('href' => $url, 'rel' => 'nofollow'));
        $content .= \html_writer::tag('span', $icon, array('class' => $class));
        $content .= \html_writer::tag('span', $str, array('class' => 'eventname'));
        $content .= \html_writer::end_tag('a');
        $content .= \html_writer::end_tag('li');

        return $content;
    }

    /**
     * Get the controls filter for calendar.
     *
     * Filter is used to hide calendar info from the display page
     *
     * @param \moodle_url $returnurl return-url for filter controls
     * @return string $content return filter controls in html
     */
    public static function get_filter_controls(\moodle_url $returnurl) {
        $groupevents = true;

        $seturl = new \moodle_url('/calendar/set.php', array('return' => base64_encode($returnurl->out_as_local_url(false)),
            'sesskey' => sesskey()));
        $content = \html_writer::start_tag('ul');

        $seturl->param('var', 'showglobal');
        $content .= self::get_filter_controls_element($seturl, CALENDAR_EVENT_GLOBAL);

        $seturl->param('var', 'showcourses');
        $content .= self::get_filter_controls_element($seturl, CALENDAR_EVENT_COURSE);

        if (isloggedin() && !isguestuser()) {
            if ($groupevents) {
                // This course MIGHT have group events defined, so show the filter.
                $seturl->param('var', 'showgroups');
                $content .= self::get_filter_controls_element($seturl, CALENDAR_EVENT_GROUP);
            }
            $seturl->param('var', 'showuser');
            $content .= self::get_filter_controls_element($seturl, CALENDAR_EVENT_USER);
        }
        $content .= \html_writer::end_tag('ul');

        return $content;
    }

    /**
     * Return the representation day
     *
     * @param int $tstamp Timestamp in GMT
     * @param int|bool $now current Unix timestamp
     * @param bool $usecommonwords
     * @return string the formatted date/time
     */
    public static function get_day_representation($tstamp, $now = false, $usecommonwords = true) {

        static $shortformat;
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
     * return the formatted representation time
     *
     * @param int $time the timestamp in UTC, as obtained from the database
     * @return string the formatted date/time
     */
    public static function get_time_representation($time) {
        static $langtimeformat = null;

        if ($langtimeformat === null) {
            $langtimeformat = get_string('strftimetime');
        }

        $timeformat = get_user_preferences('calendar_timeformat');
        if (empty($timeformat)) {
            $timeformat = get_config(null, 'calendar_site_timeformat');
        }

        return userdate($time, empty($timeformat) ? $langtimeformat : $timeformat);
    }

    /**
     * Adds day, month, year arguments to a URL and returns a moodle_url object.
     *
     * @param string|\moodle_url $linkbase
     * @param int $d The number of the day.
     * @param int $m The number of the month.
     * @param int $y The number of the year.
     * @param int $time the unixtime, used for multiple calendar support. The values $d,
     *     $m and $y are kept for backwards compatibility.
     * @return \moodle_url|null $linkbase
     */
    public static function get_link_href($linkbase, $d, $m, $y, $time = 0) {
        if (empty($linkbase)) {
            return null;
        }

        if (!($linkbase instanceof \moodle_url)) {
            $linkbase = new \moodle_url($linkbase);
        }

        // If a day, month and year were passed then convert it to a timestamp. If these were passed
        // then we can assume the day, month and year are passed as Gregorian, as no where in core
        // should we be passing these values rather than the time.
        if (!empty($d) && !empty($m) && !empty($y)) {
            if (checkdate($m, $d, $y)) {
                $time = make_timestamp($y, $m, $d);
            } else {
                $time = time();
            }
        } else if (empty($time)) {
            $time = time();
        }

        $linkbase->param('time', $time);

        return $linkbase;
    }

    /**
     * Build and return a previous month HTML link, with an arrow.
     *
     * @param string $text The text label.
     * @param string|\moodle_url $linkbase The URL stub.
     * @param int $d The number of the date.
     * @param int $m The number of the month.
     * @param int $y year The number of the year.
     * @param bool $accesshide Default visible, or hide from all except screenreaders.
     * @param int $time the unixtime, used for multiple calendar support. The values $d,
     *     $m and $y are kept for backwards compatibility.
     * @return string HTML string.
     */
    public static function get_link_previous($text, $linkbase, $d, $m, $y, $accesshide = false, $time = 0) {
        $href = self::get_link_href(new \moodle_url($linkbase), $d, $m, $y, $time);

        if (empty($href)) {
            return $text;
        }

        return link_arrow_left($text, (string)$href, $accesshide, 'previous');
    }

    /**
     * Build and return a next month HTML link, with an arrow.
     *
     * @param string $text The text label.
     * @param string|\moodle_url $linkbase The URL stub.
     * @param int $d the number of the Day
     * @param int $m The number of the month.
     * @param int $y The number of the year.
     * @param bool $accesshide Default visible, or hide from all except screenreaders.
     * @param int $time the unixtime, used for multiple calendar support. The values $d,
     *     $m and $y are kept for backwards compatibility.
     * @return string HTML string.
     */
    public static function get_link_next($text, $linkbase, $d, $m, $y, $accesshide = false, $time = 0) {
        $href = self::get_link_href(new \moodle_url($linkbase), $d, $m, $y, $time);

        if (empty($href)) {
            return $text;
        }

        return link_arrow_right($text, (string)$href, $accesshide, 'next');
    }

    /**
     * Return the number of days in month
     *
     * @param int $month the number of the month.
     * @param int $year the number of the year
     * @return int
     */
    public static function get_days_in_month($month, $year) {
        $calendartype = type_factory::get_calendar_instance();
        return $calendartype->get_num_days_in_month($year, $month);
    }

    /**
     * Get the next following month
     *
     * @param int $month the number of the month.
     * @param int $year the number of the year.
     * @return array the following month
     */
    public static function get_next_month($month, $year) {
        $calendartype = type_factory::get_calendar_instance();
        return $calendartype->get_next_month($year, $month);
    }

    /**
     * Get the previous month.
     *
     * @param int $month the number of the month.
     * @param int $year the number of the year.
     * @return array previous month
     */
    public static function get_prev_month($month, $year) {
        $calendartype = type_factory::get_calendar_instance();
        return $calendartype->get_prev_month($year, $month);
    }

    /**
     * Get per-day basis events.
     *
     * @param array $events list of events
     * @param int $month the number of the month
     * @param int $year the number of the year
     * @param array $eventsbyday event on specific day
     * @param array $durationbyday duration of the event in days
     * @param array $typesbyday event type (eg: global, course, user, or group)
     * @param array $courses list of courses
     * @return void
     */
    public static function get_events_by_day($events, $month, $year, &$eventsbyday, &$durationbyday, &$typesbyday, &$courses) {
        $calendartype = type_factory::get_calendar_instance();

        $eventsbyday = array();
        $typesbyday = array();
        $durationbyday = array();

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
                    $typesbyday[$eventdaystart]['startglobal'] = true;
                    // Set event class for global event.
                    $events[$event->id]->class = 'calendar_event_global';
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
                $upperbound = self::get_days_in_month($month, $year);
            }

            // Mark all days between $lowerbound and $upperbound (inclusive) as duration.
            for ($i = $lowerbound + 1; $i <= $upperbound; ++$i) {
                $durationbyday[$i][] = $event->id;
                if ($event->courseid == SITEID && $event->groupid == 0) {
                    $typesbyday[$i]['durationglobal'] = true;
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

    /**
     * Returns the courses to load events for, the
     *
     * @param array $courseeventsfrom An array of courses to load calendar events for
     * @param bool $ignorefilters specify the use of filters, false is set as default
     * @return array An array of courses, groups, and user to load calendar events for based upon filters
     */
    public static function set_filters(array $courseeventsfrom, $ignorefilters = false) {
        global $USER, $CFG;

        // For backwards compatability we have to check whether the courses array contains
        // just id's in which case we need to load course objects.
        $coursestoload = array();
        foreach ($courseeventsfrom as $id => $something) {
            if (!is_object($something)) {
                $coursestoload[] = $id;
                unset($courseeventsfrom[$id]);
            }
        }

        $courses = array();
        $user = false;
        $group = false;

        // Get the capabilities that allow seeing group events from all groups.
        $allgroupscaps = array('moodle/site:accessallgroups', 'moodle/calendar:manageentries');

        $isloggedin = isloggedin();

        if ($ignorefilters || self::show_event_type(CALENDAR_EVENT_COURSE)) {
            $courses = array_keys($courseeventsfrom);
        }
        if ($ignorefilters || self::show_event_type(CALENDAR_EVENT_GLOBAL)) {
            $courses[] = SITEID;
        }
        $courses = array_unique($courses);
        sort($courses);

        if (!empty($courses) && in_array(SITEID, $courses)) {
            // Sort courses for consistent colour highlighting.
            // Effectively ignoring SITEID as setting as last course id.
            $key = array_search(SITEID, $courses);
            unset($courses[$key]);
            $courses[] = SITEID;
        }

        if ($ignorefilters || ($isloggedin && self::show_event_type(CALENDAR_EVENT_USER))) {
            $user = $USER->id;
        }

        if (!empty($courseeventsfrom) && (self::show_event_type(CALENDAR_EVENT_GROUP) || $ignorefilters)) {

            if (count($courseeventsfrom) == 1) {
                $course = reset($courseeventsfrom);
                if (has_any_capability($allgroupscaps, \context_course::instance($course->id))) {
                    $coursegroups = groups_get_all_groups($course->id, 0, 0, 'g.id');
                    $group = array_keys($coursegroups);
                }
            }
            if ($group === false) {
                if (!empty($CFG->calendar_adminseesall) && has_any_capability($allgroupscaps, \context_system::instance())) {
                    $group = true;
                } else if ($isloggedin) {
                    $groupids = array();
                    foreach ($courseeventsfrom as $courseid => $course) {
                        // If the user is an editing teacher in there.
                        if (!empty($USER->groupmember[$course->id])) {
                            // We've already cached the users groups for this course so we can just use that.
                            $groupids = array_merge($groupids, $USER->groupmember[$course->id]);
                        } else if ($course->groupmode != NOGROUPS || !$course->groupmodeforce) {
                            // If this course has groups, show events from all of those related to the current user.
                            $coursegroups = groups_get_user_groups($course->id, $USER->id);
                            $groupids = array_merge($groupids, $coursegroups['0']);
                        }
                    }
                    if (!empty($groupids)) {
                        $group = $groupids;
                    }
                }
            }
        }
        if (empty($courses)) {
            $courses = false;
        }

        return array($courses, $group, $user);
    }

    /**
     * Return the capability for editing calendar event
     *
     * @param event $event event object
     * @return bool capability to edit event
     */
    public static function can_edit_event($event) {
        global $USER, $DB;

        // Must be logged in.
        if (!isloggedin()) {
            return false;
        }

        // Can not be using guest account.
        if (isguestuser()) {
            return false;
        }

        // You cannot edit calendar subscription events presently.
        if (!empty($event->subscriptionid)) {
            if (!empty($event->subscription->url)) {
                // This event can be updated externally, so it cannot be edited.
                return false;
            }
        }

        $sitecontext = \context_system::instance();

        // If user has manageentries at site level, return true.
        if (has_capability('moodle/calendar:manageentries', $sitecontext)) {
            return true;
        }

        // If groupid is set, it's definitely a group event.
        if (!empty($event->groupid)) {
            // Allow users to add/edit group events if -
            // 1) They have manageentries for the course OR
            // 2) They have managegroupentries AND are in the group.
            $group = $DB->get_record('groups', array('id' => $event->groupid));
            return $group && (
                    has_capability('moodle/calendar:manageentries', $event->context) ||
                    (has_capability('moodle/calendar:managegroupentries', $event->context)
                        && groups_is_member($event->groupid)));
        } else if (!empty($event->courseid)) {
            // If groupid is not set, but course is set, it's definiely a course event.
            return has_capability('moodle/calendar:manageentries', $event->context);
        } else if (!empty($event->userid) && $event->userid == $USER->id) {
            // If course is not set, but userid id set, it's a user event.
            return (has_capability('moodle/calendar:manageownentries', $event->context));
        } else if (!empty($event->userid)) {
            return (has_capability('moodle/calendar:manageentries', $event->context));
        }

        return false;
    }

    /**
     * Returns the default courses to display on the calendar when there isn't a specific
     * course to display.
     *
     * @return array $courses Array of courses to display
     */
    public static function get_default_courses() {
        global $CFG, $DB;

        if (!isloggedin()) {
            return array();
        }

        if (!empty($CFG->calendar_adminseesall) && has_capability('moodle/calendar:manageentries', \context_system::instance())) {
            $select = ', ' . \context_helper::get_preload_record_columns_sql('ctx');
            $join = "LEFT JOIN {context} ctx ON (ctx.instanceid = c.id AND ctx.contextlevel = :contextlevel)";
            $sql = "SELECT c.* $select
                      FROM {course} c
                      $join
                     WHERE EXISTS (SELECT 1 FROM {event} e WHERE e.courseid = c.id)
                  ";
            $courses = $DB->get_records_sql($sql, array('contextlevel' => CONTEXT_COURSE), 0, 20);
            foreach ($courses as $course) {
                \context_helper::preload_from_record($course);
            }
            return $courses;
        }

        $courses = enrol_get_my_courses();

        return $courses;
    }

    /**
     * Get event format time
     *
     * @param event $event event object
     * @param int $now current time in gmt
     * @param array $linkparams list of params for event link
     * @param bool $usecommonwords the words as formatted date/time.
     * @param int $showtime determine the show time GMT timestamp
     * @return string $eventtime link/string for event time
     */
    public static function get_format_event_time($event, $now, $linkparams = null, $usecommonwords = true, $showtime = 0) {
        $starttime = $event->timestart;
        $endtime = $event->timestart + $event->timeduration;

        if (empty($linkparams) || !is_array($linkparams)) {
            $linkparams = array();
        }

        $linkparams['view'] = 'day';

        // OK, now to get a meaningful display.
        // Check if there is a duration for this event.
        if ($event->timeduration) {
            // Get the midnight of the day the event will start.
            $usermidnightstart = usergetmidnight($starttime);
            // Get the midnight of the day the event will end.
            $usermidnightend = usergetmidnight($endtime);
            // Check if we will still be on the same day.
            if ($usermidnightstart == $usermidnightend) {
                // Check if we are running all day.
                if ($event->timeduration == DAYSECS) {
                    $time = get_string('allday', 'calendar');
                } else { // Specify the time we will be running this from.
                    $datestart = self::get_time_representation($starttime);
                    $dateend = self::get_time_representation($endtime);
                    $time = $datestart . ' <strong>&raquo;</strong> ' . $dateend;
                }

                // Set printable representation.
                if (!$showtime) {
                    $day = self::get_day_representation($event->timestart, $now, $usecommonwords);
                    $url = self::get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $linkparams), 0, 0, 0, $endtime);
                    $eventtime = \html_writer::link($url, $day) . ', ' . $time;
                } else {
                    $eventtime = $time;
                }
            } else { // It must spans two or more days.
                $daystart = self::get_day_representation($event->timestart, $now, $usecommonwords) . ', ';
                if ($showtime == $usermidnightstart) {
                    $daystart = '';
                }
                $timestart = self::get_time_representation($event->timestart);
                $dayend = self::get_day_representation($event->timestart + $event->timeduration, $now, $usecommonwords) . ', ';
                if ($showtime == $usermidnightend) {
                    $dayend = '';
                }
                $timeend = self::get_time_representation($event->timestart + $event->timeduration);

                // Set printable representation.
                if ($now >= $usermidnightstart && $now < strtotime('+1 day', $usermidnightstart)) {
                    $url = self::get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $linkparams), 0, 0, 0, $endtime);
                    $eventtime = $timestart . ' <strong>&raquo;</strong> ' . \html_writer::link($url, $dayend) . $timeend;
                } else {
                    // The event is in the future, print start and end links.
                    $url = self::get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $linkparams), 0, 0, 0, $starttime);
                    $eventtime = \html_writer::link($url, $daystart) . $timestart . ' <strong>&raquo;</strong> ';

                    $url = self::get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $linkparams),  0, 0, 0, $endtime);
                    $eventtime .= \html_writer::link($url, $dayend) . $timeend;
                }
            }
        } else { // There is no time duration.
            $time = self::get_time_representation($event->timestart);
            // Set printable representation.
            if (!$showtime) {
                $day = self::get_day_representation($event->timestart, $now, $usecommonwords);
                $url = self::get_link_href(new \moodle_url(CALENDAR_URL . 'view.php', $linkparams),  0, 0, 0, $starttime);
                $eventtime = \html_writer::link($url, $day) . ', ' . trim($time);
            } else {
                $eventtime = $time;
            }
        }

        // Check if It has expired.
        if ($event->timestart + $event->timeduration < $now) {
            $eventtime = '<span class="dimmed_text">' . str_replace(' href=', ' class="dimmed" href=', $eventtime) . '</span>';
        }

        return $eventtime;
    }

    /**
     * Checks to see if the requested type of event should be shown for the given user.
     *
     * @param int $type The type to check the display for (default is to display all)
     * @param \stdClass|int|null $user The user to check for - by default the current user
     * @return bool True if the tyep should be displayed false otherwise
     */
    public static function show_event_type($type, $user = null) {
        $default = CALENDAR_EVENT_GLOBAL + CALENDAR_EVENT_COURSE + CALENDAR_EVENT_GROUP + CALENDAR_EVENT_USER;

        if (get_user_preferences('calendar_persistflt', 0, $user) === 0) {
            global $SESSION;
            if (!isset($SESSION->calendarshoweventtype)) {
                $SESSION->calendarshoweventtype = $default;
            }
            return $SESSION->calendarshoweventtype & $type;
        } else {
            return get_user_preferences('calendar_savedflt', $default, $user) & $type;
        }
    }

    /**
     * Sets the display of the event type given $display.
     *
     * If $display = true the event type will be shown.
     * If $display = false the event type will NOT be shown.
     * If $display = null the current value will be toggled and saved.
     *
     * @param int $type object of CALENDAR_EVENT_XXX
     * @param bool $display option to display event type
     * @param \stdClass|int $user moodle user object or id, null means current user
     */
    public static function set_event_type_display($type, $display = null, $user = null) {
        $persist = get_user_preferences('calendar_persistflt', 0, $user);
        $default = CALENDAR_EVENT_GLOBAL + CALENDAR_EVENT_COURSE + CALENDAR_EVENT_GROUP + CALENDAR_EVENT_USER;
        if ($persist === 0) {
            global $SESSION;
            if (!isset($SESSION->calendarshoweventtype)) {
                $SESSION->calendarshoweventtype = $default;
            }
            $preference = $SESSION->calendarshoweventtype;
        } else {
            $preference = get_user_preferences('calendar_savedflt', $default, $user);
        }
        $current = $preference & $type;
        if ($display === null) {
            $display = !$current;
        }
        if ($display && !$current) {
            $preference += $type;
        } else if (!$display && $current) {
            $preference -= $type;
        }
        if ($persist === 0) {
            $SESSION->calendarshoweventtype = $preference;
        } else {
            if ($preference == $default) {
                unset_user_preference('calendar_savedflt', $user);
            } else {
                set_user_preference('calendar_savedflt', $preference, $user);
            }
        }
    }

    /**
     * Get calendar's allowed types
     *
     * @param \stdClass $allowed list of allowed edit for event  type
     * @param \stdClass|int $course object of a course or course id
     */
    public static function get_allowed_types(&$allowed, $course = null) {
        global $USER, $DB;

        $allowed = new \stdClass();
        $allowed->user = has_capability('moodle/calendar:manageownentries', \context_system::instance());
        $allowed->groups = false;
        $allowed->courses = false;
        $allowed->site = has_capability('moodle/calendar:manageentries', \context_course::instance(SITEID));

        if (!empty($course)) {
            if (!is_object($course)) {
                $course = $DB->get_record('course', array('id' => $course), '*', MUST_EXIST);
            }
            if ($course->id != SITEID) {
                $coursecontext = \context_course::instance($course->id);
                $allowed->user = has_capability('moodle/calendar:manageownentries', $coursecontext);

                if (has_capability('moodle/calendar:manageentries', $coursecontext)) {
                    $allowed->courses = array($course->id => 1);

                    if ($course->groupmode != NOGROUPS || !$course->groupmodeforce) {
                        if (has_capability('moodle/site:accessallgroups', $coursecontext)) {
                            $allowed->groups = groups_get_all_groups($course->id);
                        } else {
                            $allowed->groups = groups_get_all_groups($course->id, $USER->id);
                        }
                    }
                } else if (has_capability('moodle/calendar:managegroupentries', $coursecontext)) {
                    if ($course->groupmode != NOGROUPS || !$course->groupmodeforce) {
                        if (has_capability('moodle/site:accessallgroups', $coursecontext)) {
                            $allowed->groups = groups_get_all_groups($course->id);
                        } else {
                            $allowed->groups = groups_get_all_groups($course->id, $USER->id);
                        }
                    }
                }
            }
        }
    }

    /**
     * See if user can add calendar entries at all
     * used to print the "New Event" button
     *
     * @param \stdClass $course object of a course or course id
     * @return bool has the capability to add at least one event type
     */
    public static function can_add_event_to_course($course) {
        if (!isloggedin() || isguestuser()) {
            return false;
        }

        self::get_allowed_types($allowed, $course);

        return (bool)($allowed->user || $allowed->groups || $allowed->courses || $allowed->site);
    }

    /**
     * Check wether the current user is permitted to add events
     *
     * @param \stdClass $event object of event
     * @return bool has the capability to add event
     */
    public static function can_add_event($event) {
        global $USER, $DB;

        // Can not be using guest account.
        if (!isloggedin() or isguestuser()) {
            return false;
        }

        $sitecontext = \context_system::instance();

        // If user has manageentries at site level, always return true.
        if (has_capability('moodle/calendar:manageentries', $sitecontext)) {
            return true;
        }

        switch ($event->eventtype) {
            case 'course':
                return has_capability('moodle/calendar:manageentries', $event->context);
            case 'group':
                // Allow users to add/edit group events if -
                // 1) They have manageentries (= entries for whole course).
                // 2) They have managegroupentries AND are in the group.
                $group = $DB->get_record('groups', array('id' => $event->groupid));
                return $group && (
                        has_capability('moodle/calendar:manageentries', $event->context) ||
                        (has_capability('moodle/calendar:managegroupentries', $event->context)
                            && groups_is_member($event->groupid)));
            case 'user':
                if ($event->userid == $USER->id) {
                    return (has_capability('moodle/calendar:manageownentries', $event->context));
                }
                // There is intentionally no 'break'.
            case 'site':
                return has_capability('moodle/calendar:manageentries', $event->context);
            default:
                return has_capability('moodle/calendar:manageentries', $event->context);
        }
    }

    /**
     * Returns option list for the poll interval setting.
     *
     * @return array An array of poll interval options. Interval => description.
     */
    public static function get_poll_interval_choices() {
        return array(
            '0' => new \lang_string('never', 'calendar'),
            HOURSECS => new \lang_string('hourly', 'calendar'),
            DAYSECS => new \lang_string('daily', 'calendar'),
            WEEKSECS => new \lang_string('weekly', 'calendar'),
            '2628000' => new \lang_string('monthly', 'calendar'),
            YEARSECS => new \lang_string('annually', 'calendar')
        );
    }

    /**
     * Returns option list of available options for the calendar event type, given the current user and course.
     *
     * @param int $courseid The id of the course
     * @return array An array containing the event types the user can create.
     */
    public static function get_event_type_choices($courseid) {
        $choices = array();
        $allowed = new \stdClass;
        self::get_allowed_types($allowed, $courseid);

        if ($allowed->user) {
            $choices['user'] = get_string('userevents', 'calendar');
        }
        if ($allowed->site) {
            $choices['site'] = get_string('siteevents', 'calendar');
        }
        if (!empty($allowed->courses)) {
            $choices['course'] = get_string('courseevents', 'calendar');
        }
        if (!empty($allowed->groups) and is_array($allowed->groups)) {
            $choices['group'] = get_string('group');
        }

        return array($choices, $allowed->groups);
    }

    /**
     * Add an iCalendar subscription to the database.
     *
     * @param \stdClass $sub The subscription object (e.g. from the form)
     * @return int The insert ID, if any.
     */
    public static function add_subscription($sub) {
        global $DB, $USER, $SITE;

        if ($sub->eventtype === 'site') {
            $sub->courseid = $SITE->id;
        } else if ($sub->eventtype === 'group' || $sub->eventtype === 'course') {
            $sub->courseid = $sub->course;
        } else {
            // User events.
            $sub->courseid = 0;
        }
        $sub->userid = $USER->id;

        // File subscriptions never update.
        if (empty($sub->url)) {
            $sub->pollinterval = 0;
        }

        if (!empty($sub->name)) {
            if (empty($sub->id)) {
                $id = $DB->insert_record('event_subscriptions', $sub);
                // We cannot cache the data here because $sub is not complete.
                $sub->id = $id;
                // Trigger event, calendar subscription added.
                $eventparams = array('objectid' => $sub->id,
                    'context' => self::get_calendar_context($sub),
                    'other' => array('eventtype' => $sub->eventtype, 'courseid' => $sub->courseid)
                );
                $event = \core\event\calendar_subscription_created::create($eventparams);
                $event->trigger();
                return $id;
            } else {
                // Why are we doing an update here?
                self::update_subscription($sub);
                return $sub->id;
            }
        } else {
            print_error('errorbadsubscription', 'importcalendar');
        }
    }

    /**
     * Add an iCalendar event to the Moodle calendar.
     *
     * @param \stdClass $event The RFC-2445 iCalendar event
     * @param int $courseid The course ID
     * @param int $subscriptionid The iCalendar subscription ID
     * @param string $timezone The X-WR-TIMEZONE iCalendar property if provided
     * @return int Code: CALENDAR_IMPORT_EVENT_UPDATED = updated,  CALENDAR_IMPORT_EVENT_INSERTED = inserted, 0 = error
     */
    public static function add_icalendar_event($event, $courseid, $subscriptionid, $timezone='UTC') {
        global $DB;

        // Probably an unsupported X-MICROSOFT-CDO-BUSYSTATUS event.
        if (empty($event->properties['SUMMARY'])) {
            return 0;
        }

        $name = $event->properties['SUMMARY'][0]->value;
        $name = str_replace('\n', '<br />', $name);
        $name = str_replace('\\', '', $name);
        $name = preg_replace('/\s+/u', ' ', $name);

        $eventrecord = new \stdClass;
        $eventrecord->name = clean_param($name, PARAM_NOTAGS);

        if (empty($event->properties['DESCRIPTION'][0]->value)) {
            $description = '';
        } else {
            $description = $event->properties['DESCRIPTION'][0]->value;
            $description = clean_param($description, PARAM_NOTAGS);
            $description = str_replace('\n', '<br />', $description);
            $description = str_replace('\\', '', $description);
            $description = preg_replace('/\s+/u', ' ', $description);
        }
        $eventrecord->description = $description;

        // Probably a repeating event with RRULE etc. TODO: skip for now.
        if (empty($event->properties['DTSTART'][0]->value)) {
            return 0;
        }

        if (isset($event->properties['DTSTART'][0]->parameters['TZID'])) {
            $tz = $event->properties['DTSTART'][0]->parameters['TZID'];
        } else {
            $tz = $timezone;
        }
        $tz = \core_date::normalise_timezone($tz);
        $eventrecord->timestart = strtotime($event->properties['DTSTART'][0]->value . ' ' . $tz);
        if (empty($event->properties['DTEND'])) {
            $eventrecord->timeduration = 0; // No duration if no end time specified.
        } else {
            if (isset($event->properties['DTEND'][0]->parameters['TZID'])) {
                $endtz = $event->properties['DTEND'][0]->parameters['TZID'];
            } else {
                $endtz = $timezone;
            }
            $endtz = \core_date::normalise_timezone($endtz);
            $eventrecord->timeduration = strtotime($event->properties['DTEND'][0]->value . ' ' . $endtz) - $eventrecord->timestart;
        }

        // Check to see if it should be treated as an all day event.
        if ($eventrecord->timeduration == DAYSECS) {
            // Check to see if the event started at Midnight on the imported calendar.
            date_default_timezone_set($timezone);
            if (date('H:i:s', $eventrecord->timestart) === "00:00:00") {
                // This event should be an all day event.
                $eventrecord->timeduration = 0;
            }
            \core_date::set_default_server_timezone();
        }

        $eventrecord->uuid = $event->properties['UID'][0]->value;
        $eventrecord->timemodified = time();

        // Add the iCal subscription details if required.
        // We should never do anything with an event without a subscription reference.
        $sub = self::get_subscription($subscriptionid);
        $eventrecord->subscriptionid = $subscriptionid;
        $eventrecord->userid = $sub->userid;
        $eventrecord->groupid = $sub->groupid;
        $eventrecord->courseid = $sub->courseid;
        $eventrecord->eventtype = $sub->eventtype;

        if ($updaterecord = $DB->get_record('event', array('uuid' => $eventrecord->uuid,
            'subscriptionid' => $eventrecord->subscriptionid))) {
            $eventrecord->id = $updaterecord->id;
            $return = CALENDAR_IMPORT_EVENT_UPDATED; // Update.
        } else {
            $return = CALENDAR_IMPORT_EVENT_INSERTED; // Insert.
        }
        if ($createdevent = event::create($eventrecord, false)) {
            if (!empty($event->properties['RRULE'])) {
                // Repeating events.
                date_default_timezone_set($tz); // Change time zone to parse all events.
                $rrule = new rrule_manager($event->properties['RRULE'][0]->value);
                $rrule->parse_rrule();
                $rrule->create_events($createdevent);
                \core_date::set_default_server_timezone(); // Change time zone back to what it was.
            }
            return $return;
        } else {
            return 0;
        }
    }

    /**
     * Update a subscription from the form data in one of the rows in the existing subscriptions table.
     *
     * @param int $subscriptionid The ID of the subscription we are acting upon.
     * @param int $pollinterval The poll interval to use.
     * @param int $action The action to be performed. One of update or remove.
     * @return string A log of the import progress, including errors
     */
    public static function process_subscription_row($subscriptionid, $pollinterval, $action) {
        // Fetch the subscription from the database making sure it exists.
        $sub = self::get_subscription($subscriptionid);

        // Update or remove the subscription, based on action.
        switch ($action) {
            case CALENDAR_SUBSCRIPTION_UPDATE:
                // Skip updating file subscriptions.
                if (empty($sub->url)) {
                    break;
                }
                $sub->pollinterval = $pollinterval;
                self::update_subscription($sub);

                // Update the events.
                return "<p>" . get_string('subscriptionupdated', 'calendar', $sub->name) . "</p>" .
                    self::update_subscription_events($subscriptionid);
            case CALENDAR_SUBSCRIPTION_REMOVE:
                self::delete_subscription($subscriptionid);
                return get_string('subscriptionremoved', 'calendar', $sub->name);
                break;
            default:
                break;
        }
        return '';
    }

    /**
     * Delete subscription and all related events.
     *
     * @param int|\stdClass $subscription subscription or it's id, which needs to be deleted.
     */
    public static function delete_subscription($subscription) {
        global $DB;

        if (!is_object($subscription)) {
            $subscription = $DB->get_record('event_subscriptions', array('id' => $subscription), '*', MUST_EXIST);
        }

        // Delete subscription and related events.
        $DB->delete_records('event', array('subscriptionid' => $subscription->id));
        $DB->delete_records('event_subscriptions', array('id' => $subscription->id));
        \cache_helper::invalidate_by_definition('core', 'calendar_subscriptions', array(), array($subscription->id));

        // Trigger event, calendar subscription deleted.
        $eventparams = array('objectid' => $subscription->id,
            'context' => self::get_calendar_context($subscription),
            'other' => array('courseid' => $subscription->courseid)
        );
        $event = \core\event\calendar_subscription_deleted::create($eventparams);
        $event->trigger();
    }

    /**
     * From a URL, fetch the calendar and return an iCalendar object.
     *
     * @param string $url The iCalendar URL
     * @return \iCalendar The iCalendar object
     * @throws \moodle_exception
     */
    public static function get_icalendar($url) {
        global $CFG;

        require_once($CFG->libdir . '/filelib.php');

        $curl = new \curl();
        $curl->setopt(array('CURLOPT_FOLLOWLOCATION' => 1, 'CURLOPT_MAXREDIRS' => 5));
        $calendar = $curl->get($url);

        // Http code validation should actually be the job of curl class.
        if (!$calendar || $curl->info['http_code'] != 200 || !empty($curl->errorno)) {
            throw new \moodle_exception('errorinvalidicalurl', 'calendar');
        }

        $ical = new \iCalendar();
        $ical->unserialize($calendar);

        return $ical;
    }

    /**
     * Import events from an iCalendar object into a course calendar.
     *
     * @param \iCalendar $ical The iCalendar object.
     * @param int $courseid The course ID for the calendar.
     * @param int $subscriptionid The subscription ID.
     * @return string A log of the import progress, including errors.
     */
    public static function import_icalendar_events($ical, $courseid, $subscriptionid = null) {
        global $DB;

        $return = '';
        $eventcount = 0;
        $updatecount = 0;

        // Large calendars take a while...
        if (!CLI_SCRIPT) {
            \core_php_time_limit::raise(300);
        }

        // Mark all events in a subscription with a zero timestamp.
        if (!empty($subscriptionid)) {
            $sql = "UPDATE {event} SET timemodified = :time WHERE subscriptionid = :id";
            $DB->execute($sql, array('time' => 0, 'id' => $subscriptionid));
        }

        // Grab the timezone from the iCalendar file to be used later.
        if (isset($ical->properties['X-WR-TIMEZONE'][0]->value)) {
            $timezone = $ical->properties['X-WR-TIMEZONE'][0]->value;
        } else {
            $timezone = 'UTC';
        }

        $return = '';
        foreach ($ical->components['VEVENT'] as $event) {
            $res = self::add_icalendar_event($event, $courseid, $subscriptionid, $timezone);
            switch ($res) {
                case CALENDAR_IMPORT_EVENT_UPDATED:
                    $updatecount++;
                    break;
                case CALENDAR_IMPORT_EVENT_INSERTED:
                    $eventcount++;
                    break;
                case 0:
                    $return .= '<p>' . get_string('erroraddingevent', 'calendar') . ': ';
                    if (empty($event->properties['SUMMARY'])) {
                        $return .= '(' . get_string('notitle', 'calendar') . ')';
                    } else {
                        $return .= $event->properties['SUMMARY'][0]->value;
                    }
                    $return .= "</p>\n";
                    break;
            }
        }

        $return .= "<p>" . get_string('eventsimported', 'calendar', $eventcount) . "</p> ";
        $return .= "<p>" . get_string('eventsupdated', 'calendar', $updatecount) . "</p>";

        // Delete remaining zero-marked events since they're not in remote calendar.
        if (!empty($subscriptionid)) {
            $deletecount = $DB->count_records('event', array('timemodified' => 0, 'subscriptionid' => $subscriptionid));
            if (!empty($deletecount)) {
                $DB->delete_records('event', array('timemodified' => 0, 'subscriptionid' => $subscriptionid));
                $return .= "<p> " . get_string('eventsdeleted', 'calendar') . ": {$deletecount} </p>\n";
            }
        }

        return $return;
    }

    /**
     * Fetch a calendar subscription and update the events in the calendar.
     *
     * @param int $subscriptionid The course ID for the calendar.
     * @return string A log of the import progress, including errors.
     */
    public static function update_subscription_events($subscriptionid) {
        $sub = self::get_subscription($subscriptionid);

        // Don't update a file subscription.
        if (empty($sub->url)) {
            return 'File subscription not updated.';
        }

        $ical = self::get_icalendar($sub->url);
        $return = self::import_icalendar_events($ical, $sub->courseid, $subscriptionid);
        $sub->lastupdated = time();

        self::update_subscription($sub);

        return $return;
    }

    /**
     * Update a calendar subscription. Also updates the associated cache.
     *
     * @param \stdClass|array $subscription Subscription record.
     * @throws \coding_exception If something goes wrong
     */
    public static function update_subscription($subscription) {
        global $DB;

        if (is_array($subscription)) {
            $subscription = (object)$subscription;
        }
        if (empty($subscription->id) || !$DB->record_exists('event_subscriptions', array('id' => $subscription->id))) {
            throw new \coding_exception('Cannot update a subscription without a valid id');
        }

        $DB->update_record('event_subscriptions', $subscription);

        // Update cache.
        $cache = \cache::make('core', 'calendar_subscriptions');
        $cache->set($subscription->id, $subscription);

        // Trigger event, calendar subscription updated.
        $eventparams = array('userid' => $subscription->userid,
            'objectid' => $subscription->id,
            'context' => self::get_calendar_context($subscription),
            'other' => array('eventtype' => $subscription->eventtype, 'courseid' => $subscription->courseid)
        );
        $event = \core\event\calendar_subscription_updated::create($eventparams);
        $event->trigger();
    }

    /**
     * Checks to see if the user can edit a given subscription feed.
     *
     * @param mixed $subscriptionorid Subscription object or id
     * @return bool true if current user can edit the subscription else false
     */
    public static function can_edit_subscription($subscriptionorid) {
        if (is_array($subscriptionorid)) {
            $subscription = (object)$subscriptionorid;
        } else if (is_object($subscriptionorid)) {
            $subscription = $subscriptionorid;
        } else {
            $subscription = self::get_subscription($subscriptionorid);
        }

        $allowed = new \stdClass;
        $courseid = $subscription->courseid;
        $groupid = $subscription->groupid;

        self::get_allowed_types($allowed, $courseid);
        switch ($subscription->eventtype) {
            case 'user':
                return $allowed->user;
            case 'course':
                if (isset($allowed->courses[$courseid])) {
                    return $allowed->courses[$courseid];
                } else {
                    return false;
                }
            case 'site':
                return $allowed->site;
            case 'group':
                if (isset($allowed->groups[$groupid])) {
                    return $allowed->groups[$groupid];
                } else {
                    return false;
                }
            default:
                return false;
        }
    }

    /**
     * Helper function to determine the context of a calendar subscription.
     * Subscriptions can be created in two contexts COURSE, or USER.
     *
     * @param \stdClass $subscription
     * @return \context instance
     */
    public static function get_calendar_context($subscription) {
        // Determine context based on calendar type.
        if ($subscription->eventtype === 'site') {
            $context = \context_course::instance(SITEID);
        } else if ($subscription->eventtype === 'group' || $subscription->eventtype === 'course') {
            $context = \context_course::instance($subscription->courseid);
        } else {
            $context = \context_user::instance($subscription->userid);
        }
        return $context;
    }
}

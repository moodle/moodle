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
 * Defines calendar class to manage recurrence rule (rrule) during ical imports.
 *
 * @package core_calendar
 * @copyright 2014 onwards Ankit Agarwal
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_calendar;
defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot . '/calendar/lib.php');

/**
 * Defines calendar class to manage recurrence rule (rrule) during ical imports.
 *
 * Please refer to RFC 2445 {@link http://www.ietf.org/rfc/rfc2445.txt} for detail explanation of the logic.
 * Here is a basic extract from it to explain various params:-
 * recur = "FREQ"=freq *(
 *      ; either UNTIL or COUNT may appear in a 'recur',
 *      ; but UNTIL and COUNT MUST NOT occur in the same 'recur'
 *      ( ";" "UNTIL" "=" enddate ) /
 *      ( ";" "COUNT" "=" 1*DIGIT ) /
 *      ; the rest of these keywords are optional,
 *      ; but MUST NOT occur more than once
 *      ( ";" "INTERVAL" "=" 1*DIGIT )          /
 *      ( ";" "BYSECOND" "=" byseclist )        /
 *      ( ";" "BYMINUTE" "=" byminlist )        /
 *      ( ";" "BYHOUR" "=" byhrlist )           /
 *      ( ";" "BYDAY" "=" bywdaylist )          /
 *      ( ";" "BYMONTHDAY" "=" bymodaylist )    /
 *      ( ";" "BYYEARDAY" "=" byyrdaylist )     /
 *      ( ";" "BYWEEKNO" "=" bywknolist )       /
 *      ( ";" "BYMONTH" "=" bymolist )          /
 *      ( ";" "BYSETPOS" "=" bysplist )         /
 *      ( ";" "WKST" "=" weekday )              /
 *      ( ";" x-name "=" text )
 *   )
 *
 * freq       = "SECONDLY" / "MINUTELY" / "HOURLY" / "DAILY"
 * / "WEEKLY" / "MONTHLY" / "YEARLY"
 * enddate    = date
 * enddate    =/ date-time            ;An UTC value
 * byseclist  = seconds / ( seconds *("," seconds) )
 * seconds    = 1DIGIT / 2DIGIT       ;0 to 59
 * byminlist  = minutes / ( minutes *("," minutes) )
 * minutes    = 1DIGIT / 2DIGIT       ;0 to 59
 * byhrlist   = hour / ( hour *("," hour) )
 * hour       = 1DIGIT / 2DIGIT       ;0 to 23
 * bywdaylist = weekdaynum / ( weekdaynum *("," weekdaynum) )
 * weekdaynum = [([plus] ordwk / minus ordwk)] weekday
 * plus       = "+"
 * minus      = "-"
 * ordwk      = 1DIGIT / 2DIGIT       ;1 to 53
 * weekday    = "SU" / "MO" / "TU" / "WE" / "TH" / "FR" / "SA"
 *      ;Corresponding to SUNDAY, MONDAY, TUESDAY, WEDNESDAY, THURSDAY,
 *      ;FRIDAY, SATURDAY and SUNDAY days of the week.
 * bymodaylist = monthdaynum / ( monthdaynum *("," monthdaynum) )
 * monthdaynum = ([plus] ordmoday) / (minus ordmoday)
 * ordmoday   = 1DIGIT / 2DIGIT       ;1 to 31
 * byyrdaylist = yeardaynum / ( yeardaynum *("," yeardaynum) )
 * yeardaynum = ([plus] ordyrday) / (minus ordyrday)
 * ordyrday   = 1DIGIT / 2DIGIT / 3DIGIT      ;1 to 366
 * bywknolist = weeknum / ( weeknum *("," weeknum) )
 * weeknum    = ([plus] ordwk) / (minus ordwk)
 * bymolist   = monthnum / ( monthnum *("," monthnum) )
 * monthnum   = 1DIGIT / 2DIGIT       ;1 to 12
 * bysplist   = setposday / ( setposday *("," setposday) )
 * setposday  = yeardaynum
 *
 * @package core_calendar
 * @copyright 2014 onwards Ankit Agarwal <ankit.agrr@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rrule_manager {

    /** const string Frequency constant */
    const FREQ_YEARLY = 'yearly';

    /** const string Frequency constant */
    const FREQ_MONTHLY = 'monthly';

    /** const string Frequency constant */
    const FREQ_WEEKLY = 'weekly';

    /** const string Frequency constant */
    const FREQ_DAILY = 'daily';

    /** const string Frequency constant */
    const FREQ_HOURLY = 'hourly';

    /** const string Frequency constant */
    const FREQ_MINUTELY = 'everyminute';

    /** const string Frequency constant */
    const FREQ_SECONDLY = 'everysecond';

    /** const string Day constant */
    const DAY_MONDAY = 'Monday';

    /** const string Day constant */
    const DAY_TUESDAY = 'Tuesday';

    /** const string Day constant */
    const DAY_WEDNESDAY = 'Wednesday';

    /** const string Day constant */
    const DAY_THURSDAY = 'Thursday';

    /** const string Day constant */
    const DAY_FRIDAY = 'Friday';

    /** const string Day constant */
    const DAY_SATURDAY = 'Saturday';

    /** const string Day constant */
    const DAY_SUNDAY = 'Sunday';

    /** const int For forever repeating events, repeat for this many years */
    const TIME_UNLIMITED_YEARS = 10;

    /** @var string string representing the recurrence rule */
    protected $rrule;

    /** @var string Frequency of event */
    protected $freq;

    /** @var int defines a timestamp value which bounds the recurrence rule in an inclusive manner.*/
    protected $until = 0;

    /** @var int Defines the number of occurrences at which to range-bound the recurrence */
    protected $count = 0;

    /** @var int This rule part contains a positive integer representing how often the recurrence rule repeats */
    protected $interval = 1;

    /** @var array List of second rules */
    protected $bysecond = array();

    /** @var array List of Minute rules */
    protected $byminute = array();

    /** @var array List of hour rules */
    protected $byhour = array();

    /** @var array List of day rules */
    protected $byday = array();

    /** @var array List of monthday rules */
    protected $bymonthday = array();

    /** @var array List of yearday rules */
    protected $byyearday = array();

    /** @var array List of weekno rules */
    protected $byweekno = array();

    /** @var array List of month rules */
    protected $bymonth = array();

    /** @var array List of setpos rules */
    protected $bysetpos = array();

    /** @var array week start rules */
    protected $wkst;

    /**
     * Constructor for the class
     *
     * @param string $rrule Recurrence rule
     */
    public function __construct($rrule) {
        $this->rrule = $rrule;
    }

    /**
     * Parse the recurrence rule and setup all properties.
     */
    public function parse_rrule() {
        $rules = explode(';', $this->rrule);
        if (empty($rules)) {
            return;
        }
        foreach ($rules as $rule) {
            $this->parse_rrule_property($rule);
        }
    }

    /**
     * Parse a property of the recurrence rule.
     *
     * @param string $prop property string with type-value pair
     * @throws \moodle_exception
     */
    protected function parse_rrule_property($prop) {
        list($property, $value) = explode('=', $prop);
        switch ($property) {
            case 'FREQ' :
                $this->set_frequency($value);
                break;
            case 'UNTIL' :
                $this->until = strtotime($value);
                break;
            CASE 'COUNT' :
                $this->count = intval($value);
                break;
            CASE 'INTERVAL' :
                $this->interval = intval($value);
                break;
            CASE 'BYSECOND' :
                $this->bysecond = explode(',', $value);
                break;
            CASE 'BYMINUTE' :
                $this->byminute = explode(',', $value);
                break;
            CASE 'BYHOUR' :
                $this->byhour = explode(',', $value);
                break;
            CASE 'BYDAY' :
                $this->byday = explode(',', $value);
                break;
            CASE 'BYMONTHDAY' :
                $this->bymonthday = explode(',', $value);
                break;
            CASE 'BYYEARDAY' :
                $this->byyearday = explode(',', $value);
                break;
            CASE 'BYWEEKNO' :
                $this->byweekno = explode(',', $value);
                break;
            CASE 'BYMONTH' :
                $this->bymonth = explode(',', $value);
                break;
            CASE 'BYSETPOS' :
                $this->bysetpos = explode(',', $value);
                break;
            CASE 'WKST' :
                $this->wkst = $this->get_day($value);
                break;
            default:
                // We should never get here, something is very wrong.
                throw new \moodle_exception('errorrrule', 'calendar');
        }
    }

    /**
     * Sets Frequency property.
     *
     * @param string $freq Frequency of event
     * @throws \moodle_exception
     */
    protected function set_frequency($freq) {
        switch ($freq) {
            case 'YEARLY':
                $this->freq = self::FREQ_YEARLY;
                break;
            case 'MONTHLY':
                $this->freq = self::FREQ_MONTHLY;
                break;
            case 'WEEKLY':
                $this->freq = self::FREQ_WEEKLY;
                break;
            case 'DAILY':
                $this->freq = self::FREQ_DAILY;
                break;
            case 'HOURLY':
                $this->freq = self::FREQ_HOURLY;
                break;
            case 'MINUTELY':
                $this->freq = self::FREQ_MINUTELY;
                break;
            case 'SECONDLY':
                $this->freq = self::FREQ_SECONDLY;
                break;
            default:
                // We should never get here, something is very wrong.
                throw new \moodle_exception('errorrrulefreq', 'calendar');
        }
    }

    /**
     * Gets the day from day string.
     *
     * @param string $daystring Day string (MO, TU, etc)
     * @throws \moodle_exception
     *
     * @return string Day represented by the parameter.
     */
    protected function get_day($daystring) {
        switch ($daystring) {
            case 'MO':
                return self::DAY_MONDAY;
                break;
            case 'TU':
                return self::DAY_TUESDAY;
                break;
            case 'WE':
                return self::DAY_WEDNESDAY;
                break;
            case 'TH':
                return self::DAY_THURSDAY;
                break;
            case 'FR':
                return self::DAY_FRIDAY;
                break;
            case 'SA':
                return self::DAY_SATURDAY;
                break;
            case 'SU':
                return self::DAY_SUNDAY;
                break;
            default:
                // We should never get here, something is very wrong.
                throw new \moodle_exception('errorrruleday', 'calendar');
        }
    }

    /**
     * Create events for specified rrule.
     *
     * @param \calendar_event $passedevent Properties of event to create.
     * @throws \moodle_exception
     */
    public function create_events($passedevent) {
        global $DB;

        $event = clone($passedevent);
        // If Frequency is not set, there is nothing to do.
        if (empty($this->freq)) {
            return;
        }

        // Delete all child events in case of an update. This should be faster than verifying if the event exists and updating it.
        $where = "repeatid = ? AND id != ?";
        $DB->delete_records_select('event', $where, array($event->id, $event->id));
        $eventrec = $event->properties();

        switch ($this->freq) {
            case self::FREQ_DAILY :
                $this->create_repeated_events($eventrec, DAYSECS);
                break;
            case self::FREQ_WEEKLY :
                $this->create_weekly_events($eventrec);
                break;
            case self::FREQ_MONTHLY :
                $this->create_monthly_events($eventrec);
                break;
            case self::FREQ_YEARLY :
                $this->create_yearly_events($eventrec);
                break;
            default :
                // We should never get here, something is very wrong.
                throw new \moodle_exception('errorrulefreq', 'calendar');

        }

    }

    /**
     * Create repeated events.
     *
     * @param \stdClass $event Event properties to create event
     * @param int $timediff Time difference between events in seconds
     * @param bool $currenttime If set, the event timestart is used as the timestart for the first event,
     *                          else timestart + timediff used as the timestart for the first event. Set to true if
     *                          parent event is not a part of this chain.
     */
    protected function create_repeated_events($event, $timediff, $currenttime = false) {

        $event = clone($event); // We don't want to edit the master record.
        $event->repeatid = $event->id; // Set parent id for all events.
        unset($event->id); // We want new events created, not update the existing one.
        unset($event->uuid); // uuid should be unique.
        $count = $this->count;

        // Multiply by interval if used.
        if ($this->interval) {
            $timediff *= $this->interval;
        }
        if (!$currenttime) {
            $event->timestart += $timediff;
        }

        // Create events.
        if ($count > 0) {
            // Count specified, use it.
            if (!$currenttime) {
                $count--; // Already a parent event has been created.
            }
            for ($i = 0; $i < $count; $i++, $event->timestart += $timediff) {
                unset($event->id); // It is set during creation.
                \calendar_event::create($event, false);
            }
        } else {
            // No count specified, use datetime constraints.
            $until = $this->until;
            if (empty($until)) {
                // Forever event. We don't have any such concept in Moodle, hence we repeat it for a constant time.
                $until = time() + (YEARSECS * self::TIME_UNLIMITED_YEARS);
            }
            for (; $event->timestart < $until; $event->timestart += $timediff) {
                unset($event->id); // It is set during creation.
                \calendar_event::create($event, false);
            }
        }
    }

    /**
     * Create repeated events based on offsets.
     *
     * @param \stdClass $event
     * @param int $secsoffset Seconds since the start of the day that this event occurs
     * @param int $dayoffset Day offset.
     * @param int $monthoffset Months offset.
     * @param int $yearoffset Years offset.
     * @param int $start timestamp to apply offsets onto.
     * @param bool $currenttime If set, the event timestart is used as the timestart for the first event,
     *                          else timestart + timediff(monthly offset + yearly offset) used as the timestart for the first
     *                          event.Set to true if parent event is not a part of this chain.
     */
    protected function create_repeated_events_by_offsets($event, $secsoffset, $dayoffset, $monthoffset, $yearoffset, $start,
                                                         $currenttime = false) {

        $event = clone($event); // We don't want to edit the master record.
        $event->repeatid = $event->id; // Set parent id for all events.
        unset($event->id); // We want new events created, not update the existing one.
        unset($event->uuid); // uuid should be unique.
        $count = $this->count;
        // First event time in this chain.
        $event->timestart = strtotime("+$dayoffset days", $start) + $secsoffset;

        if (!$currenttime) {
            // Skip one event, since parent event is a part of this chain.
            $event->timestart = strtotime("+$monthoffset months +$yearoffset years", $event->timestart);
        }

        // Create events.
        if ($count > 0) {
            // Count specified, use it.
            if (!$currenttime) {
                $count--; // Already a parent event has been created.
            }
            for ($i = 0; $i < $count; $i++) {
                unset($event->id); // It is set during creation.
                \calendar_event::create($event, false);
                $event->timestart = strtotime("+$monthoffset months +$yearoffset years", $event->timestart);
            }
        } else {
            // No count specified, use datetime constraints.
            $until = $this->until;
            if (empty($until)) {
                // Forever event. We don't have any such concept in Moodle, hence we repeat it for a constant time.
                $until = time() + (YEARSECS * self::TIME_UNLIMITED_YEARS );
            }
            for (; $event->timestart < $until;) {
                unset($event->id); // It is set during creation.
                \calendar_event::create($event, false);
                $event->timestart = strtotime("+$monthoffset months +$yearoffset years", $event->timestart);

            }
        }
    }

    /**
     * Create repeated events based on offsets from a fixed start date.
     *
     * @param \stdClass $event
     * @param int $secsoffset Seconds since the start of the day that this event occurs
     * @param string $prefix Prefix string to add to strtotime while calculating next date for the event.
     * @param int $monthoffset Months offset.
     * @param int $yearoffset Years offset.
     * @param int $start timestamp to apply offsets onto.
     * @param bool $currenttime If set, the event timestart is used as the timestart + offset for the first event,
     *                          else timestart + timediff(monthly offset + yearly offset) + offset used as the timestart for the
     *                          first event, from the given fixed start time. Set to true if parent event is not a part of this
     *                          chain.
     */
    protected function create_repeated_events_by_offsets_from_fixedstart($event, $secsoffset, $prefix, $monthoffset,
                                                                         $yearoffset, $start, $currenttime = false) {

        $event = clone($event); // We don't want to edit the master record.
        $event->repeatid = $event->id; // Set parent id for all events.
        unset($event->id); // We want new events created, not update the existing one.
        unset($event->uuid); // uuid should be unique.
        $count = $this->count;

        // First event time in this chain.
        if (!$currenttime) {
            // Skip one event, since parent event is a part of this chain.
            $moffset = $monthoffset;
            $yoffset = $yearoffset;
            $event->timestart = strtotime("+$monthoffset months +$yearoffset years", $start);
            $event->timestart = strtotime($prefix, $event->timestart) + $secsoffset;
        } else {
            $moffset = 0;
            $yoffset = 0;
            $event->timestart = strtotime($prefix, $event->timestart) + $secsoffset;
        }
        // Create events.
        if ($count > 0) {
            // Count specified, use it.
            if (!$currenttime) {
                $count--; // Already a parent event has been created.
            }
            for ($i = 0; $i < $count; $i++) {
                unset($event->id); // It is set during creation.
                \calendar_event::create($event, false);
                $moffset += $monthoffset;
                $yoffset += $yearoffset;
                $event->timestart = strtotime("+$moffset months +$yoffset years", $start);
                $event->timestart = strtotime($prefix, $event->timestart) + $secsoffset;
            }
        } else {
            // No count specified, use datetime constraints.
            $until = $this->until;
            if (empty($until)) {
                // Forever event. We don't have any such concept in Moodle, hence we repeat it for a constant time.
                $until = time() + (YEARSECS * self::TIME_UNLIMITED_YEARS );
            }
            for (; $event->timestart < $until;) {
                unset($event->id); // It is set during creation.
                \calendar_event::create($event, false);
                $moffset += $monthoffset;
                $yoffset += $yearoffset;
                $event->timestart = strtotime("+$moffset months +$yoffset years", $start);
                $event->timestart = strtotime($prefix, $event->timestart) + $secsoffset;
            }
        }
    }

    /**
     * Create events for weekly frequency.
     *
     * @param \stdClass $event Event properties to create event
     */
    protected function create_weekly_events($event) {
        // If by day is not present, it means all days of the week.
        if (empty($this->byday)) {
            $this->byday = array('MO', 'TU', 'WE', 'TH', 'FR', 'SA', 'SU');
        }
        // This much seconds after the start of the day.
        $offset = $event->timestart - mktime(0, 0, 0, date("n", $event->timestart), date("j", $event->timestart), date("Y",
                $event->timestart));
        foreach ($this->byday as $daystring) {
            $day = $this->get_day($daystring);
            if (date('l', $event->timestart) == $day) {
                // Parent event is a part of this day chain.
                $this->create_repeated_events($event, WEEKSECS, false);
            } else {
                // Parent event is not a part of this day chain.
                $cpyevent = clone($event); // We don't want to change timestart of master record.
                $cpyevent->timestart = strtotime("+$offset seconds next $day", $cpyevent->timestart);
                $this->create_repeated_events($cpyevent, WEEKSECS, true);
            }
        }
    }

    /**
     * Create events for monthly frequency.
     *
     * @param \stdClass $event Event properties to create event
     */
    protected function create_monthly_events($event) {
        // Either bymonthday or byday should be set.
        if (empty($this->bymonthday) && empty($this->byday)
                || !empty($this->bymonthday) && !empty($this->byday)) {
            return;
        }
        // This much seconds after the start of the day.
        $offset = $event->timestart - mktime(0, 0, 0, date("n", $event->timestart), date("j", $event->timestart), date("Y",
                $event->timestart));
        $monthstart = mktime(0, 0, 0, date("n", $event->timestart), 1, date("Y", $event->timestart));
        if (!empty($this->bymonthday)) {
            foreach ($this->bymonthday as $monthday) {
                $dayoffset = $monthday - 1; // Number of days we want to add to the first day.
                if ($monthday == date("j", $event->timestart)) {
                    // Parent event is a part of this day chain.
                    $this->create_repeated_events_by_offsets($event, $offset, $dayoffset, $this->interval, 0, $monthstart,
                        false);
                } else {
                    // Parent event is not a part of this day chain.
                    $this->create_repeated_events_by_offsets($event, $offset, $dayoffset, $this->interval, 0, $monthstart, true);
                }
            }
        } else {
            foreach ($this->byday as $dayrule) {
                $day = substr($dayrule, strlen($dayrule) - 2); // Last two chars.
                $prefix = str_replace($day, '', $dayrule);
                if (empty($prefix) || !is_numeric($prefix)) {
                    return;
                }
                $day = $this->get_day($day);
                if ($day == date('l', $event->timestart)) {
                    // Parent event is a part of this day chain.
                    $this->create_repeated_events_by_offsets_from_fixedstart($event, $offset, "$prefix $day", $this->interval, 0,
                        $monthstart, false);
                } else {
                    // Parent event is not a part of this day chain.
                    $this->create_repeated_events_by_offsets_from_fixedstart($event, $offset, "$prefix $day", $this->interval, 0,
                        $monthstart, true);
                }

            }
        }
    }

    /**
     * Create events for yearly frequency.
     *
     * @param \stdClass $event Event properties to create event
     */
    protected function create_yearly_events($event) {

        // This much seconds after the start of the month.
        $offset = $event->timestart - mktime(0, 0, 0, date("n", $event->timestart), date("j", $event->timestart), date("Y",
                $event->timestart));

        if (empty($this->bymonth)) {
            // Event's month is taken if not specified.
            $this->bymonth = array(date("n", $event->timestart));
        }
        foreach ($this->bymonth as $month) {
            if (empty($this->byday)) {
                // If byday is not present, the rule must represent the same month as the event start date. Basically we only
                // have to add + $this->interval number of years to get the next event date.
                if ($month == date("n", $event->timestart)) {
                    // Parent event is a part of this month chain.
                    $this->create_repeated_events_by_offsets($event, 0, 0, 0, $this->interval, $event->timestart, false);
                }
            } else {
                $dayrule = reset($this->byday);
                $day = substr($dayrule, strlen($dayrule) - 2); // Last two chars.
                $prefix = str_replace($day, '', $dayrule);
                if (empty($prefix) || !is_numeric($prefix)) {
                    return;
                }
                $day = $this->get_day($day);
                $monthstart = mktime(0, 0, 0, $month, 1, date("Y", $event->timestart));
                if ($day == date('l', $event->timestart)) {
                    // Parent event is a part of this day chain.
                    $this->create_repeated_events_by_offsets_from_fixedstart($event, $offset, "$prefix $day", 0,
                            $this->interval, $monthstart, false);
                } else {
                    // Parent event is not a part of this day chain.
                    $this->create_repeated_events_by_offsets_from_fixedstart($event, $offset, "$prefix $day", 0,
                        $this->interval, $monthstart, true);
                }
            }
        }
    }
}
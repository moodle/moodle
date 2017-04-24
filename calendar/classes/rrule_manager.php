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

use calendar_event;
use DateInterval;
use DateTime;
use moodle_exception;
use stdClass;

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

    /** const array Array of days in a week. */
    const DAYS_OF_WEEK = [
        'MO' => self::DAY_MONDAY,
        'TU' => self::DAY_TUESDAY,
        'WE' => self::DAY_WEDNESDAY,
        'TH' => self::DAY_THURSDAY,
        'FR' => self::DAY_FRIDAY,
        'SA' => self::DAY_SATURDAY,
        'SU' => self::DAY_SUNDAY,
    ];

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

    /** @var string Week start rule. Default is Monday. */
    protected $wkst = self::DAY_MONDAY;

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
        // Validate the rules as a whole.
        $this->validate_rules();
    }

    /**
     * Create events for specified rrule.
     *
     * @param calendar_event $passedevent Properties of event to create.
     * @throws moodle_exception
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

        // Generate timestamps that obey the rrule.
        $eventtimes = $this->generate_recurring_event_times($eventrec);

        // Adjust the parent event's timestart, if necessary.
        if (count($eventtimes) > 0 && !in_array($eventrec->timestart, $eventtimes)) {
            $calevent = new calendar_event($eventrec);
            $updatedata = (object)['timestart' => $eventtimes[0], 'repeatid' => $eventrec->id];
            $calevent->update($updatedata, false);
            $eventrec->timestart = $calevent->timestart;
        }

        // Create the recurring calendar events.
        $this->create_recurring_events($eventrec, $eventtimes);
    }

    /**
     * Parse a property of the recurrence rule.
     *
     * @param string $prop property string with type-value pair
     * @throws moodle_exception
     */
    protected function parse_rrule_property($prop) {
        list($property, $value) = explode('=', $prop);
        switch ($property) {
            case 'FREQ' :
                $this->set_frequency($value);
                break;
            case 'UNTIL' :
                $this->set_until($value);
                break;
            CASE 'COUNT' :
                $this->set_count($value);
                break;
            CASE 'INTERVAL' :
                $this->set_interval($value);
                break;
            CASE 'BYSECOND' :
                $this->set_bysecond($value);
                break;
            CASE 'BYMINUTE' :
                $this->set_byminute($value);
                break;
            CASE 'BYHOUR' :
                $this->set_byhour($value);
                break;
            CASE 'BYDAY' :
                $this->set_byday($value);
                break;
            CASE 'BYMONTHDAY' :
                $this->set_bymonthday($value);
                break;
            CASE 'BYYEARDAY' :
                $this->set_byyearday($value);
                break;
            CASE 'BYWEEKNO' :
                $this->set_byweekno($value);
                break;
            CASE 'BYMONTH' :
                $this->set_bymonth($value);
                break;
            CASE 'BYSETPOS' :
                $this->set_bysetpos($value);
                break;
            CASE 'WKST' :
                $this->wkst = $this->get_day($value);
                break;
            default:
                // We should never get here, something is very wrong.
                throw new moodle_exception('errorrrule', 'calendar');
        }
    }

    /**
     * Sets Frequency property.
     *
     * @param string $freq Frequency of event
     * @throws moodle_exception
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
                throw new moodle_exception('errorrrulefreq', 'calendar');
        }
    }

    /**
     * Gets the day from day string.
     *
     * @param string $daystring Day string (MO, TU, etc)
     * @throws moodle_exception
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
                throw new moodle_exception('errorrruleday', 'calendar');
        }
    }

    /**
     * Sets the UNTIL rule.
     *
     * @param string $until The date string representation of the UNTIL rule.
     * @throws moodle_exception
     */
    protected function set_until($until) {
        $this->until = strtotime($until);
    }

    /**
     * Sets the COUNT rule.
     *
     * @param string $count The count value.
     * @throws moodle_exception
     */
    protected function set_count($count) {
        $this->count = intval($count);
    }

    /**
     * Sets the INTERVAL rule.
     *
     * The INTERVAL rule part contains a positive integer representing how often the recurrence rule repeats.
     * The default value is "1", meaning:
     *  - every second for a SECONDLY rule, or
     *  - every minute for a MINUTELY rule,
     *  - every hour for an HOURLY rule,
     *  - every day for a DAILY rule,
     *  - every week for a WEEKLY rule,
     *  - every month for a MONTHLY rule and
     *  - every year for a YEARLY rule.
     *
     * @param string $intervalstr The value for the interval rule.
     * @throws moodle_exception
     */
    protected function set_interval($intervalstr) {
        $interval = intval($intervalstr);
        if ($interval < 1) {
            throw new moodle_exception('errorinvalidinterval', 'calendar');
        }
        $this->interval = $interval;
    }

    /**
     * Sets the BYSECOND rule.
     *
     * The BYSECOND rule part specifies a comma-separated list of seconds within a minute.
     * Valid values are 0 to 59.
     *
     * @param string $bysecond Comma-separated list of seconds within a minute.
     * @throws moodle_exception
     */
    protected function set_bysecond($bysecond) {
        $seconds = explode(',', $bysecond);
        $bysecondrules = [];
        foreach ($seconds as $second) {
            if ($second < 0 || $second > 59) {
                throw new moodle_exception('errorinvalidbysecond', 'calendar');
            }
            $bysecondrules[] = (int)$second;
        }
        $this->bysecond = $bysecondrules;
    }

    /**
     * Sets the BYMINUTE rule.
     *
     * The BYMINUTE rule part specifies a comma-separated list of seconds within an hour.
     * Valid values are 0 to 59.
     *
     * @param string $byminute Comma-separated list of minutes within an hour.
     * @throws moodle_exception
     */
    protected function set_byminute($byminute) {
        $minutes = explode(',', $byminute);
        $byminuterules = [];
        foreach ($minutes as $minute) {
            if ($minute < 0 || $minute > 59) {
                throw new moodle_exception('errorinvalidbyminute', 'calendar');
            }
            $byminuterules[] = (int)$minute;
        }
        $this->byminute = $byminuterules;
    }

    /**
     * Sets the BYHOUR rule.
     *
     * The BYHOUR rule part specifies a comma-separated list of hours of the day.
     * Valid values are 0 to 23.
     *
     * @param string $byhour Comma-separated list of hours of the day.
     * @throws moodle_exception
     */
    protected function set_byhour($byhour) {
        $hours = explode(',', $byhour);
        $byhourrules = [];
        foreach ($hours as $hour) {
            if ($hour < 0 || $hour > 23) {
                throw new moodle_exception('errorinvalidbyhour', 'calendar');
            }
            $byhourrules[] = (int)$hour;
        }
        $this->byhour = $byhourrules;
    }

    /**
     * Sets the BYDAY rule.
     *
     * The BYDAY rule part specifies a comma-separated list of days of the week;
     *  - MO indicates Monday;
     *  - TU indicates Tuesday;
     *  - WE indicates Wednesday;
     *  - TH indicates Thursday;
     *  - FR indicates Friday;
     *  - SA indicates Saturday;
     *  - SU indicates Sunday.
     *
     * Each BYDAY value can also be preceded by a positive (+n) or negative (-n) integer.
     * If present, this indicates the nth occurrence of the specific day within the MONTHLY or YEARLY RRULE.
     * For example, within a MONTHLY rule, +1MO (or simply 1MO) represents the first Monday within the month,
     * whereas -1MO represents the last Monday of the month.
     * If an integer modifier is not present, it means all days of this type within the specified frequency.
     * For example, within a MONTHLY rule, MO represents all Mondays within the month.
     *
     * @param string $byday Comma-separated list of days of the week.
     * @throws moodle_exception
     */
    protected function set_byday($byday) {
        $weekdays = array_keys(self::DAYS_OF_WEEK);
        $days = explode(',', $byday);
        $bydayrules = [];
        foreach ($days as $day) {
            $suffix = substr($day, -2);
            if (!in_array($suffix, $weekdays)) {
                throw new moodle_exception('errorinvalidbydaysuffix', 'calendar');
            }

            $bydayrule = new stdClass();
            $bydayrule->day = substr($suffix, -2);
            $bydayrule->value = (int)str_replace($suffix, '', $day);

            $bydayrules[] = $bydayrule;
        }

        $this->byday = $bydayrules;
    }

    /**
     * Sets the BYMONTHDAY rule.
     *
     * The BYMONTHDAY rule part specifies a comma-separated list of days of the month.
     * Valid values are 1 to 31 or -31 to -1. For example, -10 represents the tenth to the last day of the month.
     *
     * @param string $bymonthday Comma-separated list of days of the month.
     * @throws moodle_exception
     */
    protected function set_bymonthday($bymonthday) {
        $monthdays = explode(',', $bymonthday);
        $bymonthdayrules = [];
        foreach ($monthdays as $day) {
            // Valid values are 1 to 31 or -31 to -1.
            if ($day < -31 || $day > 31 || $day == 0) {
                throw new moodle_exception('errorinvalidbymonthday', 'calendar');
            }
            $bymonthdayrules[] = (int)$day;
        }

        // Sort these MONTHDAY rules in ascending order.
        sort($bymonthdayrules);

        $this->bymonthday = $bymonthdayrules;
    }

    /**
     * Sets the BYYEARDAY rule.
     *
     * The BYYEARDAY rule part specifies a comma-separated list of days of the year.
     * Valid values are 1 to 366 or -366 to -1. For example, -1 represents the last day of the year (December 31st)
     * and -306 represents the 306th to the last day of the year (March 1st).
     *
     * @param string $byyearday Comma-separated list of days of the year.
     * @throws moodle_exception
     */
    protected function set_byyearday($byyearday) {
        $yeardays = explode(',', $byyearday);
        $byyeardayrules = [];
        foreach ($yeardays as $day) {
            // Valid values are 1 to 366 or -366 to -1.
            if ($day < -366 || $day > 366 || $day == 0) {
                throw new moodle_exception('errorinvalidbyyearday', 'calendar');
            }
            $byyeardayrules[] = (int)$day;
        }
        $this->byyearday = $byyeardayrules;
    }

    /**
     * Sets the BYWEEKNO rule.
     *
     * The BYWEEKNO rule part specifies a comma-separated list of ordinals specifying weeks of the year.
     * Valid values are 1 to 53 or -53 to -1. This corresponds to weeks according to week numbering as defined in [ISO 8601].
     * A week is defined as a seven day period, starting on the day of the week defined to be the week start (see WKST).
     * Week number one of the calendar year is the first week which contains at least four (4) days in that calendar year.
     * This rule part is only valid for YEARLY rules. For example, 3 represents the third week of the year.
     *
     * Note: Assuming a Monday week start, week 53 can only occur when Thursday is January 1 or if it is a leap year and Wednesday
     * is January 1.
     *
     * @param string $byweekno Comma-separated list of number of weeks.
     * @throws moodle_exception
     */
    protected function set_byweekno($byweekno) {
        $weeknumbers = explode(',', $byweekno);
        $byweeknorules = [];
        foreach ($weeknumbers as $week) {
            // Valid values are 1 to 53 or -53 to -1.
            if ($week < -53 || $week > 53 || $week == 0) {
                throw new moodle_exception('errorinvalidbyweekno', 'calendar');
            }
            $byweeknorules[] = (int)$week;
        }
        $this->byweekno = $byweeknorules;
    }

    /**
     * Sets the BYMONTH rule.
     *
     * The BYMONTH rule part specifies a comma-separated list of months of the year.
     * Valid values are 1 to 12.
     *
     * @param string $bymonth Comma-separated list of months of the year.
     * @throws moodle_exception
     */
    protected function set_bymonth($bymonth) {
        $months = explode(',', $bymonth);
        $bymonthrules = [];
        foreach ($months as $month) {
            // Valid values are 1 to 12.
            if ($month < 1 || $month > 12) {
                throw new moodle_exception('errorinvalidbymonth', 'calendar');
            }
            $bymonthrules[] = (int)$month;
        }
        $this->bymonth = $bymonthrules;
    }

    /**
     * Sets the BYSETPOS rule.
     *
     * The BYSETPOS rule part specifies a comma-separated list of values which corresponds to the nth occurrence within the set of
     * events specified by the rule. Valid values are 1 to 366 or -366 to -1.
     * It MUST only be used in conjunction with another BYxxx rule part.
     *
     * For example "the last work day of the month" could be represented as: RRULE:FREQ=MONTHLY;BYDAY=MO,TU,WE,TH,FR;BYSETPOS=-1
     *
     * @param string $bysetpos Comma-separated list of values.
     * @throws moodle_exception
     */
    protected function set_bysetpos($bysetpos) {
        $setposes = explode(',', $bysetpos);
        $bysetposrules = [];
        foreach ($setposes as $pos) {
            // Valid values are 1 to 366 or -366 to -1.
            if ($pos < -366 || $pos > 366 || $pos == 0) {
                throw new moodle_exception('errorinvalidbysetpos', 'calendar');
            }
            $bysetposrules[] = (int)$pos;
        }
        $this->bysetpos = $bysetposrules;
    }

    /**
     * Validate the rules as a whole.
     *
     * @throws moodle_exception
     */
    protected function validate_rules() {
        // UNTIL and COUNT cannot be in the same recurrence rule.
        if (!empty($this->until) && !empty($this->count)) {
            throw new moodle_exception('errorhasuntilandcount', 'calendar');
        }

        // BYSETPOS only be used in conjunction with another BYxxx rule part.
        if (!empty($this->bysetpos) && empty($this->bymonth) && empty($this->bymonthday) && empty($this->bysecond)
            && empty($this->byday) && empty($this->byweekno) && empty($this->byhour) && empty($this->byminute)
            && empty($this->byyearday)) {
            throw new moodle_exception('errormustbeusedwithotherbyrule', 'calendar');
        }

        // Integer values preceding BYDAY rules can only be present for MONTHLY or YEARLY RRULE.
        foreach ($this->byday as $bydayrule) {
            if (!empty($bydayrule->value) && $this->freq != self::FREQ_MONTHLY && $this->freq != self::FREQ_YEARLY) {
                throw new moodle_exception('errorinvalidbydayprefix', 'calendar');
            }
        }

        // The BYWEEKNO rule is only valid for YEARLY rules.
        if (!empty($this->byweekno) && $this->freq != self::FREQ_YEARLY) {
            throw new moodle_exception('errornonyearlyfreqwithbyweekno', 'calendar');
        }
    }

    /**
     * Creates calendar events for the recurring events.
     *
     * @param stdClass $event The parent event.
     * @param int[] $eventtimes The timestamps of the recurring events.
     */
    protected function create_recurring_events($event, $eventtimes) {
        $count = false;
        if ($this->count) {
            $count = $this->count;
        }

        foreach ($eventtimes as $time) {
            // Skip if time is the same time with the parent event's timestamp.
            if ($time == $event->timestart) {
                continue;
            }

            // Decrement count, if set.
            if ($count !== false) {
                $count--;
                if ($count == 0) {
                    break;
                }
            }

            // Create the recurring event.
            $cloneevent = clone($event);
            $cloneevent->repeatid = $event->id;
            $cloneevent->timestart = $time;
            unset($cloneevent->id);
            calendar_event::create($cloneevent, false);
        }

        // If COUNT rule is defined and the number of the generated event times is less than the the COUNT rule,
        // repeat the processing until the COUNT rule is satisfied.
        if ($count !== false && $count > 0) {
            // Set count to the remaining counts.
            $this->count = $count;
            // Clone the original event, but set the timestart to the last generated event time.
            $tmpevent = clone($event);
            $tmpevent->timestart = end($eventtimes);
            // Generate the additional event times.
            $additionaleventtimes = $this->generate_recurring_event_times($tmpevent);
            // Create the additional events.
            $this->create_recurring_events($event, $additionaleventtimes);
        }
    }

    /**
     * Generates recurring events based on the parent event and the RRULE set.
     *
     * If multiple BYxxx rule parts are specified, then after evaluating the specified FREQ and INTERVAL rule parts,
     * the BYxxx rule parts are applied to the current set of evaluated occurrences in the following order:
     * BYMONTH, BYWEEKNO, BYYEARDAY, BYMONTHDAY, BYDAY, BYHOUR, BYMINUTE, BYSECOND and BYSETPOS;
     * then COUNT and UNTIL are evaluated.
     *
     * @param stdClass $event The event object.
     * @return array The list of timestamps that obey the given RRULE.
     */
    protected function generate_recurring_event_times($event) {
        $interval = $this->get_interval();

        // Candidate event times.
        $eventtimes = [];

        $eventdatetime = new DateTime(date('Y-m-d H:i:s', $event->timestart));

        $until = null;
        if (empty($this->count)) {
            if ($this->until) {
                $until = $this->until;
            } else {
                // Forever event. However, since there's no such thing as 'forever' (at least not in Moodle),
                // we only repeat the events until 10 years from the current time.
                $untildate = new DateTime();
                $foreverinterval = new DateInterval('P' . self::TIME_UNLIMITED_YEARS . 'Y');
                $untildate->add($foreverinterval);
                $until = $untildate->getTimestamp();
            }
        } else {
            // If count is defined, let's define a tentative until date. We'll just trim the number of events later.
            $untildate = clone($eventdatetime);
            $count = $this->count;
            while ($count >= 0) {
                $untildate->add($interval);
                $count--;
            }
            $until = $untildate->getTimestamp();
        }

        // No filters applied. Generate recurring events right away.
        if (!$this->has_by_rules()) {
            // Get initial list of prospective events.
            $tmpstart = clone($eventdatetime);
            while ($tmpstart->getTimestamp() <= $until) {
                $eventtimes[] = $tmpstart->getTimestamp();
                $tmpstart->add($interval);
            }
            return $eventtimes;
        }

        // Get all of potential dates covered by the periods from the event's start date until the last.
        $dailyinterval = new DateInterval('P1D');
        $boundslist = $this->get_period_bounds_list($eventdatetime->getTimestamp(), $until);
        foreach ($boundslist as $bounds) {
            $tmpdate = new DateTime(date('Y-m-d H:i:s', $bounds->start));
            while ($tmpdate->getTimestamp() >= $bounds->start && $tmpdate->getTimestamp() < $bounds->next) {
                $eventtimes[] = $tmpdate->getTimestamp();
                $tmpdate->add($dailyinterval);
            }
        }

        // Evaluate BYMONTH rules.
        $eventtimes = $this->filter_by_month($eventtimes);

        // Evaluate BYWEEKNO rules.
        $eventtimes = $this->filter_by_weekno($eventtimes);

        // Evaluate BYYEARDAY rules.
        $eventtimes = $this->filter_by_yearday($eventtimes);

        // If BYYEARDAY, BYMONTHDAY and BYDAY are not set, default to BYMONTHDAY based on the DTSTART's day.
        if ($this->freq != self::FREQ_DAILY && empty($this->byyearday) && empty($this->bymonthday) && empty($this->byday)) {
            $this->bymonthday = [$eventdatetime->format('j')];
        }

        // Evaluate BYMONTHDAY rules.
        $eventtimes = $this->filter_by_monthday($eventtimes);

        // Evaluate BYDAY rules.
        $eventtimes = $this->filter_by_day($event, $eventtimes, $until);

        // Evaluate BYHOUR rules.
        $eventtimes = $this->apply_hour_minute_second_rules($eventdatetime, $eventtimes);

        // Evaluate BYSETPOS rules.
        $eventtimes = $this->filter_by_setpos($event, $eventtimes, $until);

        // Sort event times in ascending order.
        sort($eventtimes);

        // Finally, filter candidate event times to make sure they are within the DTSTART and UNTIL/tentative until boundaries.
        $results = [];
        foreach ($eventtimes as $time) {
            // Skip out-of-range events.
            if ($time < $eventdatetime->getTimestamp()) {
                continue;
            }
            // End if event time is beyond the until limit.
            if ($time > $until) {
                break;
            }
            $results[] = $time;
        }

        return $results;
    }

    /**
     * Generates a DateInterval object based on the FREQ and INTERVAL rules.
     *
     * @return DateInterval
     * @throws moodle_exception
     */
    protected function get_interval() {
        $intervalspec = null;
        switch ($this->freq) {
            case self::FREQ_YEARLY:
                $intervalspec = 'P' . $this->interval . 'Y';
                break;
            case self::FREQ_MONTHLY:
                $intervalspec = 'P' . $this->interval . 'M';
                break;
            case self::FREQ_WEEKLY:
                $intervalspec = 'P' . $this->interval . 'W';
                break;
            case self::FREQ_DAILY:
                $intervalspec = 'P' . $this->interval . 'D';
                break;
            case self::FREQ_HOURLY:
                $intervalspec = 'PT' . $this->interval . 'H';
                break;
            case self::FREQ_MINUTELY:
                $intervalspec = 'PT' . $this->interval . 'M';
                break;
            case self::FREQ_SECONDLY:
                $intervalspec = 'PT' . $this->interval . 'S';
                break;
            default:
                // We should never get here, something is very wrong.
                throw new moodle_exception('errorrrulefreq', 'calendar');
        }

        return new DateInterval($intervalspec);
    }

    /**
     * Determines whether the RRULE has BYxxx rules or not.
     *
     * @return bool True if there is one or more BYxxx rules to process. False, otherwise.
     */
    protected function has_by_rules() {
        return !empty($this->bymonth) || !empty($this->bymonthday) || !empty($this->bysecond) || !empty($this->byday)
            || !empty($this->byweekno) || !empty($this->byhour) || !empty($this->byminute) || !empty($this->byyearday);
    }

    /**
     * Filter event times based on the BYMONTH rule.
     *
     * @param int[] $eventdates Timestamps of event times to be filtered.
     * @return int[] Array of filtered timestamps.
     */
    protected function filter_by_month($eventdates) {
        if (empty($this->bymonth)) {
            return $eventdates;
        }

        $filteredbymonth = [];
        foreach ($eventdates as $time) {
            foreach ($this->bymonth as $month) {
                $prospectmonth = date('n', $time);
                if ($month == $prospectmonth) {
                    $filteredbymonth[] = $time;
                    break;
                }
            }
        }
        return $filteredbymonth;
    }

    /**
     * Filter event times based on the BYWEEKNO rule.
     *
     * @param int[] $eventdates Timestamps of event times to be filtered.
     * @return int[] Array of filtered timestamps.
     */
    protected function filter_by_weekno($eventdates) {
        if (empty($this->byweekno)) {
            return $eventdates;
        }

        $filteredbyweekno = [];
        $weeklyinterval = null;
        foreach ($eventdates as $time) {
            $tmpdate = new DateTime(date('Y-m-d H:i:s', $time));
            foreach ($this->byweekno as $weekno) {
                if ($weekno > 0) {
                    if ($tmpdate->format('W') == $weekno) {
                        $filteredbyweekno[] = $time;
                        break;
                    }
                } else if ($weekno < 0) {
                    if ($weeklyinterval === null) {
                        $weeklyinterval = new DateInterval('P1W');
                    }
                    $weekstart = new DateTime();
                    $weekstart->setISODate($tmpdate->format('Y'), $weekno);
                    $weeknext = clone($weekstart);
                    $weeknext->add($weeklyinterval);

                    $tmptimestamp = $tmpdate->getTimestamp();

                    if ($tmptimestamp >= $weekstart->getTimestamp() && $tmptimestamp < $weeknext->getTimestamp()) {
                        $filteredbyweekno[] = $time;
                        break;
                    }
                }
            }
        }
        return $filteredbyweekno;
    }

    /**
     * Filter event times based on the BYYEARDAY rule.
     *
     * @param int[] $eventdates Timestamps of event times to be filtered.
     * @return int[] Array of filtered timestamps.
     */
    protected function filter_by_yearday($eventdates) {
        if (empty($this->byyearday)) {
            return $eventdates;
        }

        $filteredbyyearday = [];
        foreach ($eventdates as $time) {
            $tmpdate = new DateTime(date('Y-m-d', $time));

            foreach ($this->byyearday as $yearday) {
                $dayoffset = abs($yearday) - 1;
                $dayoffsetinterval = new DateInterval("P{$dayoffset}D");

                if ($yearday > 0) {
                    $tmpyearday = (int)$tmpdate->format('z') + 1;
                    if ($tmpyearday == $yearday) {
                        $filteredbyyearday[] = $time;
                        break;
                    }
                } else if ($yearday < 0) {
                    $yeardaydate = new DateTime('last day of ' . $tmpdate->format('Y'));
                    $yeardaydate->sub($dayoffsetinterval);

                    $tmpdate->getTimestamp();

                    if ($yeardaydate->format('z') == $tmpdate->format('z')) {
                        $filteredbyyearday[] = $time;
                        break;
                    }
                }
            }
        }
        return $filteredbyyearday;
    }

    /**
     * Filter event times based on the BYMONTHDAY rule.
     *
     * @param int[] $eventdates The event times to be filtered.
     * @return int[] Array of filtered timestamps.
     */
    protected function filter_by_monthday($eventdates) {
        if (empty($this->bymonthday)) {
            return $eventdates;
        }

        $filteredbymonthday = [];
        foreach ($eventdates as $time) {
            $eventdatetime = new DateTime(date('Y-m-d', $time));
            foreach ($this->bymonthday as $monthday) {
                // Days to add/subtract.
                $daysoffset = abs($monthday) - 1;
                $dayinterval = new DateInterval("P{$daysoffset}D");

                if ($monthday > 0) {
                    if ($eventdatetime->format('j') == $monthday) {
                        $filteredbymonthday[] = $time;
                        break;
                    }
                } else if ($monthday < 0) {
                    $tmpdate = clone($eventdatetime);
                    // Reset to the first day of the month.
                    $tmpdate->modify('first day of this month');
                    // Then go to last day of the month.
                    $tmpdate->modify('last day of this month');
                    if ($daysoffset > 0) {
                        // Then subtract the monthday value.
                        $tmpdate->sub($dayinterval);
                    }
                    if ($eventdatetime->format('j') == $tmpdate->format('j')) {
                        $filteredbymonthday[] = $time;
                        break;
                    }
                }
            }
        }
        return $filteredbymonthday;
    }

    /**
     * Filter event times based on the BYDAY rule.
     *
     * @param stdClass $event The parent event.
     * @param int[] $eventdates The event times to be filtered.
     * @param int $until Event times generation limit date.
     * @return int[] Array of filtered timestamps.
     */
    protected function filter_by_day($event, $eventdates, $until) {
        if (empty($this->byday)) {
            return $eventdates;
        }

        $filteredbyday = [];

        $bounds = $this->get_period_bounds_list($event->timestart, $until);

        $nextmonthinterval = new DateInterval('P1M');
        foreach ($eventdates as $time) {
            $tmpdatetime = new DateTime(date('Y-m-d', $time));

            foreach ($this->byday as $day) {
                $dayname = self::DAYS_OF_WEEK[$day->day];

                // Skip if they day name of the event time does not match the day part of the BYDAY rule.
                if ($tmpdatetime->format('l') !== $dayname) {
                    continue;
                }

                if (empty($day->value)) {
                    // No modifier value. Applies to all weekdays of the given period.
                    $filteredbyday[] = $time;
                    break;
                } else if ($day->value > 0) {
                    // Positive value.
                    if ($this->freq === self::FREQ_YEARLY && empty($this->bymonth)) {
                        // Get the first day of the year.
                        $firstdaydate = $tmpdatetime->format('Y') . '-01-01';
                    } else {
                        // Get the first day of the month.
                        $firstdaydate = $tmpdatetime->format('Y-m') . '-01';
                    }
                    $expecteddate = new DateTime($firstdaydate);
                    $count = $day->value;
                    // Get the nth week day of the year/month.
                    $expecteddate->modify("+$count $dayname");
                    if ($expecteddate->format('Y-m-d') === $tmpdatetime->format('Y-m-d')) {
                        $filteredbyday[] = $time;
                        break;
                    }

                } else {
                    // Negative value.
                    $count = $day->value;
                    if ($this->freq === self::FREQ_YEARLY && empty($this->bymonth)) {
                        // The -Nth week day of the year.
                        $eventyear = (int)$tmpdatetime->format('Y');
                        // Get temporary DateTime object starting from the first day of the next year.
                        $expecteddate = new DateTime((++$eventyear) . '-01-01');
                        while ($count < 0) {
                            // Get the start of the previous week.
                            $expecteddate->modify('last ' . $this->wkst);
                            $tmpexpecteddate = clone($expecteddate);
                            if ($tmpexpecteddate->format('l') !== $dayname) {
                                $tmpexpecteddate->modify('next ' . $dayname);
                            }
                            if ($this->in_bounds($tmpexpecteddate->getTimestamp(), $bounds)) {
                                $expecteddate = $tmpexpecteddate;
                                $count++;
                            }
                        }
                        if ($expecteddate->format('l') !== $dayname) {
                            $expecteddate->modify('next ' . $dayname);
                        }
                        if ($expecteddate->getTimestamp() == $time) {
                            $filteredbyday[] = $time;
                            break;
                        }

                    } else {
                        // The -Nth week day of the month.
                        $expectedmonthyear = $tmpdatetime->format('F Y');
                        $expecteddate = new DateTime("first day of $expectedmonthyear");
                        $expecteddate->add($nextmonthinterval);
                        while ($count < 0) {
                            // Get the start of the previous week.
                            $expecteddate->modify('last ' . $this->wkst);
                            $tmpexpecteddate = clone($expecteddate);
                            if ($tmpexpecteddate->format('l') !== $dayname) {
                                $tmpexpecteddate->modify('next ' . $dayname);
                            }
                            if ($this->in_bounds($tmpexpecteddate->getTimestamp(), $bounds)) {
                                $expecteddate = $tmpexpecteddate;
                                $count++;
                            }
                        }

                        // Compare the expected date with the event's timestamp.
                        if ($expecteddate->getTimestamp() == $time) {
                            $filteredbyday[] = $time;
                            break;
                        }
                    }
                }
            }
        }
        return $filteredbyday;
    }

    /**
     * Applies BYHOUR, BYMINUTE and BYSECOND rules to the calculated event dates.
     * Defaults to the DTSTART's hour/minute/second component when not defined.
     *
     * @param DateTime $eventdatetime The parent event DateTime object pertaining to the DTSTART.
     * @param int[] $eventdates Array of candidate event date timestamps.
     * @return array List of updated event timestamps that contain the time component of the event times.
     */
    protected function apply_hour_minute_second_rules(DateTime $eventdatetime, $eventdates) {
        // If BYHOUR rules are not set, set the hour of the events from the DTSTART's hour component.
        if (empty($this->byhour)) {
            $this->byhour = [$eventdatetime->format('G')];
        }
        // If BYMINUTE rules are not set, set the hour of the events from the DTSTART's minute component.
        if (empty($this->byminute)) {
            $this->byminute = [(int)$eventdatetime->format('i')];
        }
        // If BYSECOND rules are not set, set the hour of the events from the DTSTART's second component.
        if (empty($this->bysecond)) {
            $this->bysecond = [(int)$eventdatetime->format('s')];
        }

        $results = [];
        foreach ($eventdates as $time) {
            $datetime = new DateTime(date('Y-m-d', $time));
            foreach ($this->byhour as $hour) {
                foreach ($this->byminute as $minute) {
                    foreach ($this->bysecond as $second) {
                        $datetime->setTime($hour, $minute, $second);
                        $results[] = $datetime->getTimestamp();
                    }
                }
            }
        }
        return $results;
    }

    /**
     * Filter event times based on the BYSETPOS rule.
     *
     * @param stdClass $event The parent event.
     * @param int[] $eventtimes The event times to be filtered.
     * @param int $until Event times generation limit date.
     * @return int[] Array of filtered timestamps.
     */
    protected function filter_by_setpos($event, $eventtimes, $until) {
        if (empty($this->bysetpos)) {
            return $eventtimes;
        }

        $filteredbysetpos = [];
        $boundslist = $this->get_period_bounds_list($event->timestart, $until);
        sort($eventtimes);
        foreach ($boundslist as $bounds) {
            // Generate a list of candidate event times based that are covered in a period's bounds.
            $prospecttimes = [];
            foreach ($eventtimes as $time) {
                if ($time >= $bounds->start && $time < $bounds->next) {
                    $prospecttimes[] = $time;
                }
            }
            if (empty($prospecttimes)) {
                continue;
            }
            // Add the event times that correspond to the set position rule into the filtered results.
            foreach ($this->bysetpos as $pos) {
                $tmptimes = $prospecttimes;
                if ($pos < 0) {
                    rsort($tmptimes);
                }
                $index = abs($pos) - 1;
                if (isset($tmptimes[$index])) {
                    $filteredbysetpos[] = $tmptimes[$index];
                }
            }
        }
        return $filteredbysetpos;
    }

    /**
     * Gets the list of period boundaries covered by the recurring events.
     *
     * @param int $eventtime The event timestamp.
     * @param int $until The end timestamp.
     * @return array List of period bounds, with start and next properties.
     */
    protected function get_period_bounds_list($eventtime, $until) {
        $interval = $this->get_interval();
        $periodbounds = $this->get_period_boundaries($eventtime);
        $periodstart = $periodbounds['start'];
        $periodafter = $periodbounds['next'];
        $bounds = [];
        if ($until !== null) {
            while ($periodstart->getTimestamp() < $until) {
                $bounds[] = (object)[
                    'start' => $periodstart->getTimestamp(),
                    'next' => $periodafter->getTimestamp()
                ];
                $periodstart->add($interval);
                $periodafter->add($interval);
            }
        } else {
            $count = $this->count;
            while ($count > 0) {
                $bounds[] = (object)[
                    'start' => $periodstart->getTimestamp(),
                    'next' => $periodafter->getTimestamp()
                ];
                $periodstart->add($interval);
                $periodafter->add($interval);
                $count--;
            }
        }

        return $bounds;
    }

    /**
     * Determine whether the date-time in question is within the bounds of the periods that are covered by the RRULE.
     *
     * @param int $time The timestamp to be evaluated.
     * @param array $bounds Array of period boundaries covered by the RRULE.
     * @return bool
     */
    protected function in_bounds($time, $bounds) {
        foreach ($bounds as $bound) {
            if ($time >= $bound->start && $time < $bound->next) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determines the start and end DateTime objects that serve as references to determine whether a calculated event timestamp
     * falls on the period defined by these DateTimes objects.
     *
     * @param int $eventtime Unix timestamp of the event time.
     * @return DateTime[]
     * @throws moodle_exception
     */
    protected function get_period_boundaries($eventtime) {
        $nextintervalspec = null;

        switch ($this->freq) {
            case self::FREQ_YEARLY:
                $nextintervalspec = 'P1Y';
                $timestart = date('Y-01-01', $eventtime);
                break;
            case self::FREQ_MONTHLY:
                $nextintervalspec = 'P1M';
                $timestart = date('Y-m-01', $eventtime);
                break;
            case self::FREQ_WEEKLY:
                $nextintervalspec = 'P1W';
                if (date('l', $eventtime) === $this->wkst) {
                    $weekstarttime = $eventtime;
                } else {
                    $weekstarttime = strtotime('last ' . $this->wkst, $eventtime);
                }
                $timestart = date('Y-m-d', $weekstarttime);
                break;
            case self::FREQ_DAILY:
                $nextintervalspec = 'P1D';
                $timestart = date('Y-m-d', $eventtime);
                break;
            case self::FREQ_HOURLY:
                $nextintervalspec = 'PT1H';
                $timestart = date('Y-m-d H:00:00', $eventtime);
                break;
            case self::FREQ_MINUTELY:
                $nextintervalspec = 'PT1M';
                $timestart = date('Y-m-d H:i:00', $eventtime);
                break;
            case self::FREQ_SECONDLY:
                $nextintervalspec = 'PT1S';
                $timestart = date('Y-m-d H:i:s', $eventtime);
                break;
            default:
                // We should never get here, something is very wrong.
                throw new moodle_exception('errorrrulefreq', 'calendar');
        }

        $eventstart = new DateTime($timestart);
        $eventnext = clone($eventstart);
        $nextinterval = new DateInterval($nextintervalspec);
        $eventnext->add($nextinterval);

        return [
            'start' => $eventstart,
            'next' => $eventnext,
        ];
    }
}

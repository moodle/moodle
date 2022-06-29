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
 * Scheduled task abstract class.
 *
 * @package    core
 * @category   task
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Abstract class defining a scheduled task.
 * @copyright  2013 Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class scheduled_task extends task_base {

    /** Minimum minute value. */
    const MINUTEMIN = 0;
    /** Maximum minute value. */
    const MINUTEMAX = 59;

    /** Minimum hour value. */
    const HOURMIN = 0;
    /** Maximum hour value. */
    const HOURMAX = 23;

    /** Minimum day of month value. */
    const DAYMIN = 1;
    /** Maximum day of month value. */
    const DAYMAX = 31;

    /** Minimum month value. */
    const MONTHMIN = 1;
    /** Maximum month value. */
    const MONTHMAX = 12;

    /** Minimum dayofweek value. */
    const DAYOFWEEKMIN = 0;
    /** Maximum dayofweek value. */
    const DAYOFWEEKMAX = 6;

    /**
     * Minute field identifier.
     */
    const FIELD_MINUTE = 'minute';
    /**
     * Hour field identifier.
     */
    const FIELD_HOUR = 'hour';
    /**
     * Day-of-month field identifier.
     */
    const FIELD_DAY = 'day';
    /**
     * Month field identifier.
     */
    const FIELD_MONTH = 'month';
    /**
     * Day-of-week field identifier.
     */
    const FIELD_DAYOFWEEK = 'dayofweek';

    /** @var string $hour - Pattern to work out the valid hours */
    private $hour = '*';

    /** @var string $minute - Pattern to work out the valid minutes */
    private $minute = '*';

    /** @var string $day - Pattern to work out the valid days */
    private $day = '*';

    /** @var string $month - Pattern to work out the valid months */
    private $month = '*';

    /** @var string $dayofweek - Pattern to work out the valid dayofweek */
    private $dayofweek = '*';

    /** @var int $lastruntime - When this task was last run */
    private $lastruntime = 0;

    /** @var boolean $customised - Has this task been changed from it's default schedule? */
    private $customised = false;

    /** @var boolean $overridden - Does the task have values set VIA config? */
    private $overridden = false;

    /** @var int $disabled - Is this task disabled in cron? */
    private $disabled = false;

    /**
     * Get the last run time for this scheduled task.
     *
     * @return int
     */
    public function get_last_run_time() {
        return $this->lastruntime;
    }

    /**
     * Set the last run time for this scheduled task.
     *
     * @param int $lastruntime
     */
    public function set_last_run_time($lastruntime) {
        $this->lastruntime = $lastruntime;
    }

    /**
     * Has this task been changed from it's default config?
     *
     * @return bool
     */
    public function is_customised() {
        return $this->customised;
    }

    /**
     * Has this task been changed from it's default config?
     *
     * @param bool
     */
    public function set_customised($customised) {
        $this->customised = $customised;
    }

    /**
     * Has this task been changed from it's default config?
     *
     * @return bool
     */
    public function is_overridden(): bool {
        return $this->overridden;
    }

    /**
     * Set the overridden value.
     *
     * @param bool $overridden
     */
    public function set_overridden(bool $overridden): void {
        $this->overridden = $overridden;
    }

    /**
     * Setter for $minute. Accepts a special 'R' value
     * which will be translated to a random minute.
     *
     * @param string $minute
     * @param bool $expandr - if true (default) an 'R' value in a time is expanded to an appropriate int.
     *      If false, they are left as 'R'
     */
    public function set_minute($minute, $expandr = true) {
        if ($minute === 'R' && $expandr) {
            $minute = mt_rand(self::MINUTEMIN, self::MINUTEMAX);
        }
        $this->minute = $minute;
    }

    /**
     * Getter for $minute.
     *
     * @return string
     */
    public function get_minute() {
        return $this->minute;
    }

    /**
     * Setter for $hour. Accepts a special 'R' value
     * which will be translated to a random hour.
     *
     * @param string $hour
     * @param bool $expandr - if true (default) an 'R' value in a time is expanded to an appropriate int.
     *      If false, they are left as 'R'
     */
    public function set_hour($hour, $expandr = true) {
        if ($hour === 'R' && $expandr) {
            $hour = mt_rand(self::HOURMIN, self::HOURMAX);
        }
        $this->hour = $hour;
    }

    /**
     * Getter for $hour.
     *
     * @return string
     */
    public function get_hour() {
        return $this->hour;
    }

    /**
     * Setter for $month.
     *
     * @param string $month
     */
    public function set_month($month) {
        $this->month = $month;
    }

    /**
     * Getter for $month.
     *
     * @return string
     */
    public function get_month() {
        return $this->month;
    }

    /**
     * Setter for $day.
     *
     * @param string $day
     */
    public function set_day($day) {
        $this->day = $day;
    }

    /**
     * Getter for $day.
     *
     * @return string
     */
    public function get_day() {
        return $this->day;
    }

    /**
     * Setter for $dayofweek.
     *
     * @param string $dayofweek
     * @param bool $expandr - if true (default) an 'R' value in a time is expanded to an appropriate int.
     *      If false, they are left as 'R'
     */
    public function set_day_of_week($dayofweek, $expandr = true) {
        if ($dayofweek === 'R' && $expandr) {
            $dayofweek = mt_rand(self::DAYOFWEEKMIN, self::DAYOFWEEKMAX);
        }
        $this->dayofweek = $dayofweek;
    }

    /**
     * Getter for $dayofweek.
     *
     * @return string
     */
    public function get_day_of_week() {
        return $this->dayofweek;
    }

    /**
     * Setter for $disabled.
     *
     * @param bool $disabled
     */
    public function set_disabled($disabled) {
        $this->disabled = (bool)$disabled;
    }

    /**
     * Getter for $disabled.
     * @return bool
     */
    public function get_disabled() {
        return $this->disabled;
    }

    /**
     * Override this function if you want this scheduled task to run, even if the component is disabled.
     *
     * @return bool
     */
    public function get_run_if_component_disabled() {
        return false;
    }

    /**
     * Informs whether the given field is valid.
     * Use the constants FIELD_* to identify the field.
     * Have to be called after the method set_{field}(string).
     *
     * @param string $field field identifier; expected values from constants FIELD_*.
     *
     * @return bool true if given field is valid. false otherwise.
     */
    public function is_valid(string $field): bool {
        return !empty($this->get_valid($field));
    }

    /**
     * Calculates the list of valid values according to the given field and stored expression.
     *
     * @param string $field field identifier. Must be one of those FIELD_*.
     *
     * @return array(int) list of matching values.
     *
     * @throws \coding_exception when passed an invalid field identifier.
     */
    private function get_valid(string $field): array {
        switch($field) {
            case self::FIELD_MINUTE:
                $min = self::MINUTEMIN;
                $max = self::MINUTEMAX;
                break;
            case self::FIELD_HOUR:
                $min = self::HOURMIN;
                $max = self::HOURMAX;
                break;
            case self::FIELD_DAY:
                $min = self::DAYMIN;
                $max = self::DAYMAX;
                break;
            case self::FIELD_MONTH:
                $min = self::MONTHMIN;
                $max = self::MONTHMAX;
                break;
            case self::FIELD_DAYOFWEEK:
                $min = self::DAYOFWEEKMIN;
                $max = self::DAYOFWEEKMAX;
                break;
            default:
                throw new \coding_exception("Field '$field' is not a valid crontab identifier.");
        }

        return $this->eval_cron_field($this->{$field}, $min, $max);
    }

    /**
     * Take a cron field definition and return an array of valid numbers with the range min-max.
     *
     * @param string $field - The field definition.
     * @param int $min - The minimum allowable value.
     * @param int $max - The maximum allowable value.
     * @return array(int)
     */
    public function eval_cron_field($field, $min, $max) {
        // Cleanse the input.
        $field = trim($field);

        // Format for a field is:
        // <fieldlist> := <range>(/<step>)(,<fieldlist>)
        // <step>  := int
        // <range> := <any>|<int>|<min-max>
        // <any>   := *
        // <min-max> := int-int
        // End of format BNF.

        // This function is complicated but is covered by unit tests.
        $range = array();

        $matches = array();
        preg_match_all('@[0-9]+|\*|,|/|-@', $field, $matches);

        $last = 0;
        $inrange = false;
        $instep = false;
        foreach ($matches[0] as $match) {
            if ($match == '*') {
                array_push($range, range($min, $max));
            } else if ($match == '/') {
                $instep = true;
            } else if ($match == '-') {
                $inrange = true;
            } else if (is_numeric($match)) {
                if ($min > $match || $match > $max) {
                    // This is a value error: The value lays out of the expected range of values.
                    return [];
                }
                if ($instep) {
                    for ($i = 0; $i < count($range[count($range) - 1]); $i++) {
                        if (($i) % $match != 0) {
                            $range[count($range) - 1][$i] = -1;
                        }
                    }
                    $instep = false;
                } else if ($inrange) {
                    if (count($range)) {
                        $range[count($range) - 1] = range($last, $match);
                    }
                    $inrange = false;
                } else {
                    array_push($range, $match);
                    $last = $match;
                }
            }
        }

        // If inrange or instep were not processed, there is a syntax error.
        // Cleanup any existing values to show up the error.
        if ($inrange || $instep) {
            return [];
        }

        // Flatten the result.
        $result = array();
        foreach ($range as $r) {
            if (is_array($r)) {
                foreach ($r as $rr) {
                    if ($rr >= $min && $rr <= $max) {
                        $result[$rr] = 1;
                    }
                }
            } else if (is_numeric($r)) {
                if ($r >= $min && $r <= $max) {
                    $result[$r] = 1;
                }
            }
        }
        $result = array_keys($result);
        sort($result, SORT_NUMERIC);
        return $result;
    }

    /**
     * Assuming $list is an ordered list of items, this function returns the item
     * in the list that is greater than or equal to the current value (or 0). If
     * no value is greater than or equal, this will return the first valid item in the list.
     * If list is empty, this function will return 0.
     *
     * @param int $current The current value
     * @param int[] $list The list of valid items.
     * @return int $next.
     */
    private function next_in_list($current, $list) {
        foreach ($list as $l) {
            if ($l >= $current) {
                return $l;
            }
        }
        if (count($list)) {
            return $list[0];
        }

        return 0;
    }

    /**
     * Calculate when this task should next be run based on the schedule.
     *
     * @return int $nextruntime.
     */
    public function get_next_scheduled_time() {
        // We need to change to the server timezone before using php date() functions.
        \core_date::set_default_server_timezone();

        $validminutes = $this->get_valid(self::FIELD_MINUTE);
        $validhours = $this->get_valid(self::FIELD_HOUR);
        $validdays = $this->get_valid(self::FIELD_DAY);
        $validdaysofweek = $this->get_valid(self::FIELD_DAYOFWEEK);
        $validmonths = $this->get_valid(self::FIELD_MONTH);

        $nextvalidyear = date('Y');

        $currentminute = date("i") + 1;
        $currenthour = date("H");
        $currentday = date("j");
        $currentmonth = date("n");
        $currentdayofweek = date("w");

        $nextvalidminute = $this->next_in_list($currentminute, $validminutes);
        if ($nextvalidminute < $currentminute) {
            $currenthour += 1;
        }
        $nextvalidhour = $this->next_in_list($currenthour, $validhours);
        if ($nextvalidhour < $currenthour) {
            $currentdayofweek += 1;
            $currentday += 1;
        }
        $nextvaliddayofmonth = $this->next_in_list($currentday, $validdays);
        $nextvaliddayofweek = $this->next_in_list($currentdayofweek, $validdaysofweek);
        $daysincrementbymonth = $nextvaliddayofmonth - $currentday;
        $daysinmonth = date('t');
        if ($nextvaliddayofmonth < $currentday) {
            $daysincrementbymonth += $daysinmonth;
        }

        $daysincrementbyweek = $nextvaliddayofweek - $currentdayofweek;
        if ($nextvaliddayofweek < $currentdayofweek) {
            $daysincrementbyweek += 7;
        }

        // Special handling for dayofmonth vs dayofweek:
        // if either field is * - use the other field
        // otherwise - choose the soonest (see man 5 cron).
        if ($this->dayofweek == '*') {
            $daysincrement = $daysincrementbymonth;
        } else if ($this->day == '*') {
            $daysincrement = $daysincrementbyweek;
        } else {
            // Take the smaller increment of days by month or week.
            $daysincrement = $daysincrementbymonth;
            if ($daysincrementbyweek < $daysincrementbymonth) {
                $daysincrement = $daysincrementbyweek;
            }
        }

        $nextvaliddayofmonth = $currentday + $daysincrement;
        if ($nextvaliddayofmonth > $daysinmonth) {
            $currentmonth += 1;
            $nextvaliddayofmonth -= $daysinmonth;
        }

        $nextvalidmonth = $this->next_in_list($currentmonth, $validmonths);
        if ($nextvalidmonth < $currentmonth) {
            $nextvalidyear += 1;
        }

        // Work out the next valid time.
        $nexttime = mktime($nextvalidhour,
                           $nextvalidminute,
                           0,
                           $nextvalidmonth,
                           $nextvaliddayofmonth,
                           $nextvalidyear);

        return $nexttime;
    }

    /**
     * Informs whether this task can be run.
     *
     * @return bool true when this task can be run. false otherwise.
     */
    public function can_run(): bool {
        return $this->is_component_enabled() || $this->get_run_if_component_disabled();
    }

    /**
     * Checks whether the component and the task disabled flag enables to run this task.
     * This do not checks whether the task manager allows running them or if the
     * site allows tasks to "run now".
     *
     * @return bool true if task is enabled. false otherwise.
     */
    public function is_enabled(): bool {
        return $this->can_run() && !$this->get_disabled();
    }

    /**
     * Produces a valid id string to use as id attribute based on the given FQCN class name.
     *
     * @param string $classname FQCN of a task.
     * @return string valid string to be used as id attribute.
     */
    public static function get_html_id(string $classname): string {
        return str_replace('\\', '-', ltrim($classname, '\\'));
    }

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    abstract public function get_name();

}

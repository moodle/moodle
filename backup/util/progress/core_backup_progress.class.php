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
 * Base class for handling progress information during a backup and restore.
 *
 * Subclasses should generally override the current_progress function which
 * summarises all progress information.
 *
 * @package core_backup
 * @copyright 2013 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class core_backup_progress {
    /**
     * @var int Constant indicating that the number of progress calls is unknown.
     */
    const INDETERMINATE = -1;

    /**
     * @var int The number of seconds that can pass without progress() calls.
     */
    const TIME_LIMIT_WITHOUT_PROGRESS = 120;

    /**
     * @var int Time of last progress call.
     */
    protected $lastprogresstime;

    /**
     * @var int Number of progress calls (restricted to ~ 1/second).
     */
    protected $count;

    /**
     * @var array Array of progress descriptions for each stack level.
     */
    protected $descriptions = array();

    /**
     * @var array Array of maximum progress values for each stack level.
     */
    protected $maxes = array();

    /**
     * @var array Array of current progress values.
     */
    protected $currents = array();

    /**
     * @var int Array of counts within parent progress entry (ignored for first)
     */
    protected $parentcounts = array();

    /**
     * Marks the start of an operation that will display progress.
     *
     * This can be called multiple times for nested progress sections. It must
     * be paired with calls to end_progress.
     *
     * The progress maximum may be INDETERMINATE if the current operation has
     * an unknown number of steps. (This is default.)
     *
     * Calling this function will always result in a new display, so this
     * should not be called exceedingly frequently.
     *
     * When it is complete by calling end_progress, each start_progress section
     * automatically adds progress to its parent, as defined by $parentcount.
     *
     * @param string $description Description to display
     * @param int $max Maximum value of progress for this section
     * @param int $parentcount How many progress points this section counts for
     * @throws coding_exception If max is invalid
     */
    public function start_progress($description, $max = self::INDETERMINATE,
            $parentcount = 1) {
        if ($max != self::INDETERMINATE && $max < 0) {
            throw new coding_exception(
                    'start_progress() max value cannot be negative');
        }
        if ($parentcount < 1) {
            throw new coding_exception(
                    'start_progress() parent progress count must be at least 1');
        }
        if (!empty($this->descriptions)) {
            $prevmax = end($this->maxes);
            if ($prevmax !== self::INDETERMINATE) {
                $prevcurrent = end($this->currents);
                if ($prevcurrent + $parentcount > $prevmax) {
                    throw new coding_exception(
                            'start_progress() parent progress would exceed max');
                }
            }
        } else {
            if ($parentcount != 1) {
                throw new coding_exception(
                        'start_progress() progress count must be 1 when no parent');
            }
        }
        $this->descriptions[] = $description;
        $this->maxes[] = $max;
        $this->currents[] = 0;
        $this->parentcounts[] = $parentcount;
        $this->update_progress();
    }

    /**
     * Marks the end of an operation that will display progress.
     *
     * This must be paired with each start_progress call.
     *
     * If there is a parent progress section, its progress will be increased
     * automatically to reflect the end of the child section.
     *
     * @throws coding_exception If progress hasn't been started
     */
    public function end_progress() {
        if (!count($this->descriptions)) {
            throw new coding_exception('end_progress() without start_progress()');
        }
        array_pop($this->descriptions);
        array_pop($this->maxes);
        array_pop($this->currents);
        $parentcount = array_pop($this->parentcounts);
        if (!empty($this->descriptions)) {
            $lastmax = end($this->maxes);
            if ($lastmax != self::INDETERMINATE) {
                $lastvalue = end($this->currents);
                $this->currents[key($this->currents)] = $lastvalue + $parentcount;
            }
        }
        $this->update_progress();
    }

    /**
     * Indicates that progress has occurred.
     *
     * The progress value should indicate the total progress so far, from 0
     * to the value supplied for $max (inclusive) in start_progress.
     *
     * You do not need to call this function for every value. It is OK to skip
     * values. It is also OK to call this function as often as desired; it
     * doesn't do anything if called more than once per second.
     *
     * It must be INDETERMINATE if start_progress was called with $max set to
     * INDETERMINATE. Otherwise it must not be indeterminate.
     *
     * @param int $progress Progress so far
     * @throws coding_exception If progress value is invalid
     */
    public function progress($progress = self::INDETERMINATE) {
        // Ignore too-frequent progress calls (more than once per second).
        $now = $this->get_time();
        if ($now === $this->lastprogresstime) {
            return;
        }

        // Check we are inside a progress section.
        $max = end($this->maxes);
        if ($max === false) {
            throw new coding_exception(
                    'progress() without start_progress');
        }

        // Check and apply new progress.
        if ($progress === self::INDETERMINATE) {
            // Indeterminate progress.
            if ($max !== self::INDETERMINATE) {
                throw new coding_exception(
                        'progress() INDETERMINATE, expecting value');
            }
        } else {
            // Determinate progress.
            $current = end($this->currents);
            if ($max === self::INDETERMINATE) {
                throw new coding_exception(
                        'progress() with value, expecting INDETERMINATE');
            } else if ($progress < 0 || $progress > $max) {
                throw new coding_exception(
                        'progress() value out of range');
            } else if ($progress < $current) {
                throw new coding_Exception(
                        'progress() value may not go backwards');
            }
            $this->currents[key($this->currents)] = $progress;
        }

        // Update progress.
        $this->count++;
        $this->lastprogresstime = $now;
        set_time_limit(self::TIME_LIMIT_WITHOUT_PROGRESS);
        $this->update_progress();
    }

    /**
     * Gets time (this is provided so that unit tests can override it).
     *
     * @return int Current system time
     */
    protected function get_time() {
        return time();
    }

    /**
     * Called whenever new progress should be displayed.
     */
    protected abstract function update_progress();

    /**
     * @return bool True if currently inside a progress section
     */
    public function is_in_progress_section() {
        return !empty($this->descriptions);
    }

    /**
     * Checks max value of current progress section.
     *
     * @return int Current max value (may be core_backup_progress::INDETERMINATE)
     * @throws coding_exception If not in a progress section
     */
    public function get_current_max() {
        $max = end($this->maxes);
        if ($max === false) {
            throw new coding_exception('Not inside progress section');
        }
        return $max;
    }

    /**
     * @return string Current progress section description
     */
    public function get_current_description() {
        $description = end($this->descriptions);
        if ($description === false) {
            throw new coding_exception('Not inside progress section');
        }
        return $description;
    }

    /**
     * Obtains current progress in a way suitable for drawing a progress bar.
     *
     * Progress is returned as a minimum and maximum value. If there is no
     * indeterminate progress, these values will be identical. If there is
     * intermediate progress, these values can be different. (For example, if
     * the top level progress sections is indeterminate, then the values will
     * always be 0.0 and 1.0.)
     *
     * @return array Minimum and maximum possible progress proportions
     */
    public function get_progress_proportion_range() {
        // If there is no progress underway, we must have finished.
        if (empty($this->currents)) {
            return array(1.0, 1.0);
        }
        $count = count($this->currents);
        $min = 0.0;
        $max = 1.0;
        for ($i = 0; $i < $count; $i++) {
            // Get max value at that section - if it's indeterminate we can tell
            // no more.
            $sectionmax = $this->maxes[$i];
            if ($sectionmax === self::INDETERMINATE) {
                return array($min, $max);
            }

            // Special case if current value is max (this should only happen
            // just before ending a section).
            $sectioncurrent = $this->currents[$i];
            if ($sectioncurrent === $sectionmax) {
                return array($max, $max);
            }

            // Using the current value at that section, we know we are somewhere
            // between 'current' and the next 'current' value which depends on
            // the parentcount of the nested section (if any).
            $newmin = ($sectioncurrent / $sectionmax) * ($max - $min) + $min;
            $nextcurrent = $sectioncurrent + 1;
            if ($i + 1 < $count) {
                $weight = $this->parentcounts[$i + 1];
                $nextcurrent = $sectioncurrent + $weight;
            }
            $newmax = ($nextcurrent / $sectionmax) * ($max - $min) + $min;
            $min = $newmin;
            $max = $newmax;
        }

        // If there was nothing indeterminate, we use the min value as current.
        return array($min, $min);
    }

    /**
     * Obtains current indeterminate progress in a way suitable for adding to
     * the progress display.
     *
     * This returns the number of indeterminate calls (at any level) during the
     * lifetime of this progress reporter, whether or not there is a current
     * indeterminate step. (The number will not be ridiculously high because
     * progress calls are limited to one per second.)
     *
     * @return int Number of indeterminate progress calls
     */
    public function get_progress_count() {
        return $this->count;
    }
}

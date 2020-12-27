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
 * Time splitting method that generates predictions regularly.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\time_splitting;

defined('MOODLE_INTERNAL') || die();

/**
 * Time splitting method that generates predictions periodically.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class periodic extends base {

    /**
     * The periodicity of the predictions / training data generation.
     *
     * @return \DateInterval
     */
    abstract protected function periodicity();

    /**
     * Gets the next range with start on the provided time.
     *
     * @param  \DateTimeImmutable $time
     * @return array
     */
    abstract protected function get_next_range(\DateTimeImmutable $time);

    /**
     * Get the start of the first time range.
     *
     * @return int A timestamp.
     */
    abstract protected function get_first_start();

    /**
     * Returns whether the analysable can be processed by this time splitting method or not.
     *
     * @param \core_analytics\analysable $analysable
     * @return bool
     */
    public function is_valid_analysable(\core_analytics\analysable $analysable) {
        if (!$analysable->get_start()) {
            return false;
        }
        return true;
    }

    /**
     * define_ranges
     *
     * @return array
     */
    protected function define_ranges() {

        $periodicity = $this->periodicity();

        if ($this->analysable->get_end()) {
            $end = (new \DateTimeImmutable())->setTimestamp($this->analysable->get_end());
        }
        $nexttime = (new \DateTimeImmutable())->setTimestamp($this->get_first_start());

        $now = new \DateTimeImmutable('now', \core_date::get_server_timezone_object());

        $range = $this->get_next_range($nexttime);
        if (!$range) {
            $nexttime = $nexttime->add($periodicity);
            $range = $this->get_next_range($nexttime);

            if (!$range) {
                throw new \coding_exception('The get_next_range implementation is broken. The difference between two consecutive
                    ranges can not be more than the periodicity.');
            }
        }

        $ranges = [];
        $endreached = false;
        while (($this->ready_to_predict($range) || $this->ready_to_train($range)) && !$endreached) {
            $ranges[] = $range;
            $nexttime = $nexttime->add($periodicity);
            $range = $this->get_next_range($nexttime);

            $endreached = (!empty($end) && $nexttime > $end);
        }

        if ($ranges && !$endreached) {
            // If this analysable is not finished we adjust the start and end of the last element in $ranges
            // so that it ends in time().The reason is that the start of these ranges is based on the analysable
            // start and the end is calculated based on the start. This is to prevent the same issue we had in MDL-65348.
            //
            // An example of the situation we want to avoid is:
            // A course started on a Monday, in 2015. It has no end date. Now the system is upgraded to Moodle 3.8, which
            // includes this code. This happens on Wednesday. Periodic ranges (e.g. weekly) will be calculated from a Monday
            // so the data provided by the time-splitting method would be from Monday to Monday, when we really want to
            // provide data from Wednesday to the past Wednesday.
            $ranges = $this->update_last_range($ranges);
        }

        return $ranges;
    }

    /**
     * Overwritten as all generated rows are comparable.
     *
     * @return bool
     */
    public function include_range_info_in_training_data() {
        return false;
    }

    /**
     * Overwritting as the last range may be for prediction.
     *
     * @return array
     */
    public function get_training_ranges() {
        // Cloning the array.
        $trainingranges = $this->ranges;

        foreach ($trainingranges as $rangeindex => $range) {
            if (!$this->ready_to_train($range)) {
                unset($trainingranges[$rangeindex]);
            }
        }

        return $trainingranges;
    }

    /**
     * Allows child classes to update the last range provided.
     *
     * @param  array  $ranges
     * @return array
     */
    protected function update_last_range(array $ranges) {
        return $ranges;
    }
}

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
        $next = (new \DateTimeImmutable())->setTimestamp($this->get_first_start());

        $now = new \DateTimeImmutable('now', \core_date::get_server_timezone_object());

        $ranges = [];
        while ($next < $now &&
                (empty($end) || $next < $end)) {
            $range = $this->get_next_range($next);
            if ($range) {
                $ranges[] = $range;
            }
            $next = $next->add($periodicity);
        }

        $nextrange = $this->get_next_range($next);
        if ($this->ready_to_predict($nextrange) && (empty($end) || $next < $end)) {
            // Add the next one if we have not reached the analysable end yet.
            // It will be used to get predictions.
            $ranges[] = $nextrange;
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
     * The next range is based on the past period.
     *
     * @param  \DateTimeImmutable $next
     * @return array
     */
    protected function get_next_range(\DateTimeImmutable $next) {

        $end = $next->getTimestamp();
        $start = $next->sub($this->periodicity())->getTimestamp();

        if ($start < $this->analysable->get_start()) {
            // We skip the first range generated as its start is prior to the analysable start.
            return false;
        }

        return [
            'start' => $start,
            'end' => $end,
            'time' => $end
        ];
    }

    /**
     * Get the start of the first time range.
     *
     * @return int A timestamp.
     */
    protected function get_first_start() {
        return $this->analysable->get_start();
    }
}

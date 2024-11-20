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
abstract class past_periodic extends periodic implements before_now {

    /**
     * Gets the next range with start on the provided time.
     *
     * The next range is based on the past period so we substract this
     * range's periodicity from $time.
     *
     * @param  \DateTimeImmutable $time
     * @return array
     */
    protected function get_next_range(\DateTimeImmutable $time) {

        $end = $time->getTimestamp();
        $start = $time->sub($this->periodicity())->getTimestamp();

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

    /**
     * Guarantees that the last range dates end right now.
     *
     * @param  array  $ranges
     * @return array
     */
    protected function update_last_range(array $ranges) {
        $lastrange = end($ranges);

        if ($lastrange['time'] > time()) {
            // We just need to wait in this case.
            return $lastrange;
        }

        $timetoenddiff = time() - $lastrange['time'];

        $ranges[count($ranges) - 1] = [
            'start' => $lastrange['start'] + $timetoenddiff,
            'end' => $lastrange['end'] + $timetoenddiff,
            'time' => $lastrange['time'] + $timetoenddiff,
        ];

        return $ranges;
    }
}

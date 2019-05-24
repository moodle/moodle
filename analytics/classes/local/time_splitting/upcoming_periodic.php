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
 * Time splitting method that generates predictions periodically.
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
abstract class upcoming_periodic extends periodic implements after_now {

    /**
     * The next range indicator calculations should be based on upcoming dates.
     *
     * @param  \DateTimeImmutable $next
     * @return array
     */
    protected function get_next_range(\DateTimeImmutable $next) {

        $start = $next->getTimestamp();
        $end = $next->add($this->periodicity())->getTimestamp();
        return [
            'start' => $start,
            'end' => $end,
            'time' => $start
        ];
    }

    /**
     * Whether to cache or not the indicator calculations.
     * @return bool
     */
    public function cache_indicator_calculations(): bool {
        return false;
    }

    /**
     * Overriden as these time-splitting methods are based on future dates.
     *
     * @return bool
     */
    public function valid_for_evaluation(): bool {
        return false;
    }

    /**
     * Get the start of the first time range.
     *
     * Overwriten to start generating predictions about upcoming stuff from time().
     *
     * @return int A timestamp.
     */
    protected function get_first_start() {
        global $DB;

        $cache = \cache::make('core', 'modelfirstanalyses');

        $key = $this->modelid . '_' . $this->analysable->get_id();
        $firstanalysis = $cache->get($key);
        if (!empty($firstanalysis)) {
            return $firstanalysis;
        }

        // This analysable has not yet been analysed, the start is therefore now (-1 so ready_to_predict can be executed).
        return time() - 1;
    }
}

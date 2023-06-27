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
 * Time splitting method that generates predictions X days/weeks/months after the analysable start.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\time_splitting;

defined('MOODLE_INTERNAL') || die();

/**
 * Time splitting method that generates predictions X days/weeks/months after the analysable start.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class after_start extends \core_analytics\local\time_splitting\base implements before_now {

    /**
     * The period we should wait until we generate predictions for this.
     *
     * @param  \core_analytics\analysable $analysable
     * @return \DateInterval
     */
    abstract protected function wait_period(\core_analytics\analysable $analysable);

    /**
     * Returns whether the course can be processed by this time splitting method or not.
     *
     * @param \core_analytics\analysable $analysable
     * @return bool
     */
    public function is_valid_analysable(\core_analytics\analysable $analysable) {

        if (!$analysable->get_start()) {
            return false;
        }

        $predictionstart = $this->get_prediction_interval_start($analysable);
        if ($analysable->get_start() > $predictionstart) {
            // We still need to wait.
            return false;
        }

        return true;
    }

    /**
     * This time-splitting method returns one single range, the start to two days before the end.
     *
     * @return array The list of ranges, each of them including 'start', 'end' and 'time'
     */
    protected function define_ranges() {

        $now = time();
        $ranges = [
            [
                'start' => $this->analysable->get_start(),
                'end' => $now,
                'time' => $now,
            ]
        ];

        return $ranges;
    }

    /**
     * Whether to cache or not the indicator calculations.
     *
     * @return bool
     */
    public function cache_indicator_calculations(): bool {
        return false;
    }

    /**
     * Calculates the interval start time backwards, from now.
     *
     * @param  \core_analytics\analysable $analysable
     * @return int
     */
    protected function get_prediction_interval_start(\core_analytics\analysable $analysable) {

        // The prediction time is always time(). We don't want to reuse the firstanalysis time
        // because otherwise samples (e.g. students) which start after the analysable (e.g. course)
        // start would use an incorrect analysis interval.
        $predictionstart = new \DateTime('now');
        $predictionstart->sub($this->wait_period($analysable));

        return $predictionstart->getTimestamp();
    }
}

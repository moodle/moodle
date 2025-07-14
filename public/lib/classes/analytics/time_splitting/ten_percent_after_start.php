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
 * Time splitting method that generates predictions 3 days after the analysable start.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\analytics\time_splitting;

defined('MOODLE_INTERNAL') || die();

/**
 * Time splitting method that generates predictions 3 days after the analysable start.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class ten_percent_after_start extends \core_analytics\local\time_splitting\after_start {

    /**
     * The time splitting method name.
     *
     * @return \lang_string
     */
    public static function get_name(): \lang_string {
        return new \lang_string('timesplitting:tenpercentafterstart');
    }

    /**
     * Extended as we require and end date here.
     *
     * @param \core_analytics\analysable $analysable
     * @return bool
     */
    public function is_valid_analysable(\core_analytics\analysable $analysable) {

        // We require an end date to calculate the 10%.
        if (!$analysable->get_end()) {
            return false;
        }

        return parent::is_valid_analysable($analysable);
    }

    /**
     * The period we should wait until we generate predictions for this.
     *
     * @throws \coding_exception
     * @param  \core_analytics\analysable $analysable
     * @return \DateInterval
     */
    protected function wait_period(\core_analytics\analysable $analysable) {

        if (!$analysable->get_end() || !$analysable->get_start()) {
            throw new \coding_exception('Analysables with no start or end should be discarded in is_valid_analysable.');
        }

        $diff = $analysable->get_end() - $analysable->get_start();

        // A 10% of $diff.
        return new \DateInterval('PT' . intval($diff / 10) . 'S');
    }
}

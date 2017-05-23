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
 * Range processor splitting the course in ten parts and accumulating data.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\time_splitting;

defined('MOODLE_INTERNAL') || die();

/**
 * Range processor splitting the course in ten parts and accumulating data.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class deciles_accum extends base {

    public function get_name() {
        return get_string('timesplitting:decilesaccum', 'analytics');
    }

    protected function define_ranges() {
        $rangeduration = floor(($this->analysable->get_end() - $this->analysable->get_start()) / 10);

        $ranges = array();
        for ($i = 0; $i < 10; $i++) {
            $ranges[] = array(
                'start' => $this->analysable->get_start(),
                'end' => $this->analysable->get_start() + ($rangeduration * ($i + 1))
            );
        }

        return $ranges;
    }
}

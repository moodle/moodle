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
 * Range processor splitting the course in parts and accumulating data from the start.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_analytics\local\time_splitting;

defined('MOODLE_INTERNAL') || die();

/**
 * Range processor splitting the course in parts and accumulating data from the start.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllao {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class accumulative_parts extends base implements before_now {

    /**
     * The number of parts to split the analysable duration in.
     *
     * @return int
     */
    abstract protected function get_number_parts();

    /**
     * define_ranges
     *
     * @return array
     */
    protected function define_ranges() {

        $nparts = $this->get_number_parts();

        $rangeduration = ($this->analysable->get_end() - $this->analysable->get_start()) / $nparts;

        $ranges = array();
        for ($i = 0; $i < $nparts; $i++) {
            $end = $this->analysable->get_start() + intval($rangeduration * ($i + 1));
            if ($i === ($nparts - 1)) {
                // Better to use the end for the last one as we are using floor above.
                $end = $this->analysable->get_end();
            }
            $ranges[$i] = array(
                'start' => $this->analysable->get_start(),
                'end' => $end,
                'time' => $end
            );
        }

        return $ranges;
    }
}

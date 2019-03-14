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
 * Test time splitting.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test time splitting.
 *
 * @package   core_analytics
 * @copyright 2019 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_timesplitting_seconds extends \core_analytics\local\time_splitting\periodic {

    /**
     * Every second.
     * @return \DateInterval
     */
    public function periodicity() {
        return new \DateInterval('PT1S');
    }

    /**
     * Just to comply with the interface.
     *
     * @return \lang_string
     */
    public static function get_name() : \lang_string {
        return new \lang_string('error');
    }
}

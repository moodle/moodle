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
 * Test analyser.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Test analyser.
 *
 * @package   core_analytics
 * @copyright 2017 David Monllaó {@link http://www.davidmonllao.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class test_analysis extends \core_analytics\analysis {

    /**
     * Overwritten to add a delay.
     *
     * @param \core_analytics\analysable $analysable
     * @return array
     */
    public function process_analysable(\core_analytics\analysable $analysable): array {
        // Half a second.
        sleep(1);
        return parent::process_analysable($analysable);
    }
}

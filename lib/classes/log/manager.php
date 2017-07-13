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
 * Log storage manager interface.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\log;

defined('MOODLE_INTERNAL') || die();

/**
 * Interface describing log readers.
 *
 * This is intended for reports, use get_log_manager() to get
 * the configured instance.
 *
 * @package core
 */
interface manager {
    /**
     * Return list of available log readers.
     *
     * @param string $interface All returned readers must implement this interface.
     *
     * @return \core\log\reader[]
     */
    public function get_readers($interface = null);

    /**
     * Dispose all initialised stores.
     * @return void
     */
    public function dispose();

    /**
     * For a given report, returns a list of log stores that are supported.
     *
     * @param string $component component.
     *
     * @return false|array list of logstores that support the given report. It returns false if the given $component doesn't
     *      require logstores.
     */
    public function get_supported_logstores($component);
}

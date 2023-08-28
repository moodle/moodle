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
 * One Roster Enrolment Client.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace enrol_oneroster\local\interfaces;

use enrol_oneroster\local\command;
use enrol_oneroster\local\interfaces\container as container_interface;
use enrol_oneroster\local\interfaces\filter;
use progress_trace;
use stdClass;

/**
 * One Roster Client interface.
 *
 * @package    enrol_oneroster
 * @copyright  Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface client {

    /**
     * Set the log tracer.
     *
     * @param progress_trace $trace
     */
    public function set_trace(progress_trace $trace): void;

    /**
     * Get the log tracer.
     *
     * @return progress_trace
     */
    public function get_trace(): progress_trace;

    /**
     * Execute the supplied command.
     *
     * @param   command $command The command to execute
     * @param   filter $filter
     * @return  stdClass
     */
    public function execute(command $command, filter $filter = null): stdClass;

    /**
     * Get the entity factory for this One Roster implementation.
     *
     * @return  container_interface
     */
    public function get_container(): container_interface;

    /**
     * Perform all availilable synchronisations.
     *
     * @param   int $onlysincetime
     */
    public function synchronise(?int $onlysincetime = null): void;

    /**
     * Authenticate against the One Roster endpoint as required.
     */
    public function authenticate(): void;
}

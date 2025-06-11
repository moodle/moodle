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
 * Interface for all health checks.
 *
 * @package local_o365
 * @author James McQuillan <james.mcquillan@remote-learner.net>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright (C) 2014 onwards Microsoft, Inc. (http://microsoft.com/)
 */

namespace local_o365\healthcheck;

/**
 * Interface for all health checks.
 */
interface healthcheckinterface {
    /**
     * @var int SEVERITY_OK
     */
    const SEVERITY_OK = 0;
    /**
     * @var int SEVERITY_TRIVIAL
     */
    const SEVERITY_TRIVIAL = 1;
    /**
     * @var int SEVERITY_WARNING
     */
    const SEVERITY_WARNING = 2;
    /**
     * @var int SEVERITY_FATAL
     */
    const SEVERITY_FATAL = 3;

    /**
     * Run the health check.
     *
     * @return array Array of result data. Must include:
     *               bool result Whether the health check passed or not.
     *               int severity If the health check failed, how bad a problem is it? This is one of the SEVERITY_* constants.
     *               string message A message to show the user.
     */
    public function run();

    /**
     * Get a human-readable name for the health check.
     *
     * @return string A name for the health check.
     */
    public function get_name();
}

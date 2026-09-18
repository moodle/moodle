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

namespace core\exception;

/**
 * An exception which is aware of the WWW-Authenticate headers associated with an auth failure.
 *
 * @package    core
 * @copyright  Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
interface www_authenticate_aware_exception {
    /**
     * Get the WWW-Authenticate header auth-params associated with this auth failure.
     *
     * Multiple params can be returned as an array of strings.
     * Each value will be combined into a single WWW-Authenticate header.
     *
     * @return string[] The WWW-Authenticate header auth-params.
     */
    public function get_www_authenticate_params(): array;
}

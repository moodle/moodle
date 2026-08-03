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
 * Base API token exception.
 *
 * @package    core
 * @subpackage exception
 * @copyright  2026 Mihail Geshoski <mihailgesoski@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class api_token_exception extends moodle_exception {
    /**
     * Constructor.
     *
     * @param string $errorcode The error code
     * @param string $module The module name
     * @param mixed $a Additional information
     * @param string|null $debuginfo Information to aid the debugging process
     */
    public function __construct(string $errorcode, string $module, $a = null, ?string $debuginfo = null) {
        parent::__construct($errorcode, $module, '', $a, $debuginfo);
    }
}

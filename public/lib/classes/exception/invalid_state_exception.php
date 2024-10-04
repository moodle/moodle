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
 * An exception that indicates something really weird happened. For example,
 * if you do switch ($context->contextlevel), and have one case for each
 * CONTEXT_... constant. You might throw an invalid_state_exception in the
 * default case, to just in case something really weird is going on, and
 * $context->contextlevel is invalid - rather than ignoring this possibility.
 *
 * @package    core
 * @subpackage exception
 * @copyright  2009 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class invalid_state_exception extends moodle_exception {
    /**
     * Constructor.
     *
     * @param string $hint short description of problem
     * @param string $debuginfo optional more detailed information
     */
    public function __construct($hint, $debuginfo = null) {
        parent::__construct('invalidstatedetected', 'debug', '', $hint, $debuginfo);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(invalid_state_exception::class, \invalid_state_exception::class);

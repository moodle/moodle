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
 * Course/activity access exception.
 *
 * This exception is thrown from require_login()
 *
 * @package    core
 * @subpackage exception
 * @copyright  2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class require_login_exception extends moodle_exception {
    /**
     * Constructor.
     *
     * @param string $debuginfo Information to aid the debugging process
     */
    public function __construct($debuginfo) {
        parent::__construct('requireloginerror', 'error', '', null, $debuginfo);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(require_login_exception::class, \require_login_exception::class);

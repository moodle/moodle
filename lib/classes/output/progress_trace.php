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

namespace core\output;

/**
 * Progress trace class.
 *
 * Use this class from long operations where you want to output occasional information about
 * what is going on, but don't know if, or in what format, the output should be.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 */
abstract class progress_trace {
    /**
     * Output an progress message in whatever format.
     *
     * @param string $message the message to output.
     * @param int $depth indent depth for this message.
     */
    abstract public function output(
        string $message,
        int $depth = 0,
    );

    /**
     * Called when the processing is finished.
     */
    public function finished() {
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(progress_trace::class, \progress_trace::class);

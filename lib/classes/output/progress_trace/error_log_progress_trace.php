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
 * This subclass of progress_trace outputs to error log.
 *
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 */
class error_log_progress_trace extends progress_trace {
    /** @var string log prefix */
    protected $prefix;

    /**
     * Constructor.
     * @param string $prefix optional log prefix
     */
    public function __construct($prefix = '') {
        $this->prefix = $prefix;
    }

    /**
     * Output the trace message.
     *
     * @param string $message
     * @param int $depth
     * @return void Output is sent to error log.
     */
    public function output($message, $depth = 0) {
        error_log($this->prefix . str_repeat('  ', $depth) . $message);
    }
}

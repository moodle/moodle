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

namespace core\output\progress_trace;

use core\output\progress_trace;

/**
 * This subclass of progress_trace outputs to error log.
 *
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 */
class error_log_progress_trace extends progress_trace {
    /**
     * Constructor.
     * @param string $prefix optional log prefix
     */
    public function __construct(
        /** @var string The prefix to use in the error_log messages */
        protected string $prefix = '',
    ) {
    }

    #[\Override]
    public function output(
        string $message,
        int $depth = 0,
    ): void {
        // phpcs:ignore moodle.PHP.ForbiddenFunctions.FoundWithAlternative
        error_log($this->prefix . str_repeat('  ', $depth) . $message);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(error_log_progress_trace::class, \error_log_progress_trace::class);

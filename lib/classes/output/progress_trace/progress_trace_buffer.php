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
 * Special type of trace that can be used for catching of output of other traces.
 *
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 */
class progress_trace_buffer extends progress_trace {
    /** @var string output buffer */
    protected string $buffer = '';

    /**
     * Constructor.
     *
     * @param progress_trace $trace
     * @param bool $passthrough true means output and buffer, false means just buffer and no output
     */
    public function __construct(
        /** @var progress_trace The progress_trace to pass content to */
        protected progress_trace $trace,
        /** @var bool Whether we pass output out */
        protected bool $passthrough = true,
    ) {
        $this->buffer      = '';
    }

    #[\Override]
    public function output(
        string $message,
        int $depth = 0,
    ): void {
        ob_start();
        $this->trace->output($message, $depth);
        $this->buffer .= ob_get_contents();
        if ($this->passthrough) {
            ob_end_flush();
        } else {
            ob_end_clean();
        }
    }

    #[\Override]
    public function finished(): void {
        ob_start();
        $this->trace->finished();
        $this->buffer .= ob_get_contents();
        if ($this->passthrough) {
            ob_end_flush();
        } else {
            ob_end_clean();
        }
    }

    /**
     * Reset the internal text buffer.
     */
    public function reset_buffer(): void {
        $this->buffer = '';
    }

    /**
     * Return the internal text buffer.
     *
     * @return string buffered plain text
     */
    public function get_buffer(): string {
        return $this->buffer;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(progress_trace_buffer::class, \progress_trace_buffer::class);

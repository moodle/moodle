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
 * Special type of trace that can be used for catching of output of other traces.
 *
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 */
class progress_trace_buffer extends progress_trace {
    /** @var progress_trace */
    protected $trace;
    /** @var bool do we pass output out */
    protected $passthrough;
    /** @var string output buffer */
    protected $buffer;

    /**
     * Constructor.
     *
     * @param progress_trace $trace
     * @param bool $passthrough true means output and buffer, false means just buffer and no output
     */
    public function __construct(progress_trace $trace, $passthrough = true) {
        $this->trace       = $trace;
        $this->passthrough = $passthrough;
        $this->buffer      = '';
    }

    /**
     * Output the trace message.
     *
     * @param string $message the message to output.
     * @param int $depth indent depth for this message.
     * @return void output stored in buffer
     */
    public function output($message, $depth = 0) {
        ob_start();
        $this->trace->output($message, $depth);
        $this->buffer .= ob_get_contents();
        if ($this->passthrough) {
            ob_end_flush();
        } else {
            ob_end_clean();
        }
    }

    /**
     * Called when the processing is finished.
     */
    public function finished() {
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
     * Reset internal text buffer.
     */
    public function reset_buffer() {
        $this->buffer = '';
    }

    /**
     * Return internal text buffer.
     * @return string buffered plain text
     */
    public function get_buffer() {
        return $this->buffer;
    }
}

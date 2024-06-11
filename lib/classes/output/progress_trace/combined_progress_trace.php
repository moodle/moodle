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
 * Special type of trace that can be used for redirecting to multiple other traces.
 *
 * @copyright Petr Skoda {@link http://skodak.org}
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 */
class combined_progress_trace extends progress_trace {

    /**
     * An array of traces.
     * @var array
     */
    protected $traces;

    /**
     * Constructs a new instance.
     *
     * @param array $traces multiple traces
     */
    public function __construct(array $traces) {
        $this->traces = $traces;
    }

    /**
     * Output an progress message in whatever format.
     *
     * @param string $message the message to output.
     * @param integer $depth indent depth for this message.
     */
    public function output($message, $depth = 0) {
        foreach ($this->traces as $trace) {
            $trace->output($message, $depth);
        }
    }

    /**
     * Called when the processing is finished.
     */
    public function finished() {
        foreach ($this->traces as $trace) {
            $trace->finished();
        }
    }
}

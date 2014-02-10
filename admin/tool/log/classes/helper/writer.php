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
 * Helper trait writer
 *
 * @package    tool_log
 * @copyright  2014 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_log\helper;
defined('MOODLE_INTERNAL') || die();

/**
 * Helper trait writer. Adds buffer support for the store.
 * \tool_log\helper\store must be included before using this trait.
 *
 * @package    tool_log
 * @copyright  2014 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait writer {

    /** @var array $buffer buffer of events. */
    protected $buffer = array();

    /** @var array $buffer buffer size of events. */
    protected $buffersize;

    /** @var int $count Counter. */
    protected $count = 0;

    /**
     * Write event in the store with buffering. Insert_events() must be
     * defined. override in stores if the store doesn't support buffering.
     *
     * @param \core\event\base $event
     *
     * @return void
     */
    public function write(\core\event\base $event) {
        $this->buffer[] = $event;
        $this->count++;

        if (!isset($this->buffersize)) {
            $this->buffersize = $this->get_config('buffersize', 50);
        }

        if ($this->count >= $this->buffersize) {
            $this->flush();
        }
    }

    /**
     * Flush event buffer.
     */
    public function flush() {
        if ($this->count == 0) {
            return;
        }
        $events = $this->buffer;
        $this->count = 0;
        $this->buffer = array();
        $this->insert_events($events);
    }

    /**
     * Push any remaining events to the database. Insert_events() must be
     * defined. override in stores if the store doesn't support buffering.
     *
     */
    public function dispose() {
        $this->flush();
    }
}

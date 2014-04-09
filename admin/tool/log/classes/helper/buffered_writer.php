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
 * Helper trait buffered_writer
 *
 * @package    tool_log
 * @copyright  2014 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_log\helper;
defined('MOODLE_INTERNAL') || die();

/**
 * Helper trait buffered_writer. Adds buffer support for the store.
 *
 * @package    tool_log
 * @copyright  2014 onwards Ankit Agarwal
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait buffered_writer {

    /** @var array $buffer buffer of events. */
    protected $buffer = array();

    /** @var array $buffer buffer size of events. */
    protected $buffersize;

    /** @var int $count Counter. */
    protected $count = 0;

    /**
     * Should the event be ignored (== not logged)?
     * @param \core\event\base $event
     * @return bool
     */
    abstract protected function is_event_ignored(\core\event\base $event);

    /**
     * Write event in the store with buffering. Method insert_event_entries() must be
     * defined.
     *
     * @param \core\event\base $event
     *
     * @return void
     */
    public function write(\core\event\base $event) {
        global $PAGE;

        if ($this->is_event_ignored($event)) {
            return;
        }

        // We need to capture current info at this moment,
        // at the same time this lowers memory use because
        // snapshots and custom objects may be garbage collected.
        $entry = $event->get_data();
        $entry['other'] = serialize($entry['other']);
        $entry['origin'] = $PAGE->requestorigin;
        $entry['ip'] = $PAGE->requestip;
        $entry['realuserid'] = \core\session\manager::is_loggedinas() ? $_SESSION['USER']->realuser : null;

        $this->buffer[] = $entry;
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
        $this->insert_event_entries($events);
    }

    /**
     * Bulk write a given array of events to the backend. Stores must implement this.
     *
     * @param array $evententries raw event data
     */
    abstract protected function insert_event_entries($evententries);

    /**
     * Get a config value for the store.
     *
     * @param string $name Config name
     * @param mixed $default default value
     *
     * @return mixed config value if set, else the default value.
     */
    abstract protected function get_config($name, $default = null);

    /**
     * Push any remaining events to the database. Insert_events() must be
     * defined. override in stores if the store doesn't support buffering.
     *
     */
    public function dispose() {
        $this->flush();
    }
}

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
 * Message sink.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Message sink.
 *
 * @package    core
 * @category   phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class phpunit_message_sink {
    /** @var array of records from messages table */
    protected $messages = array();

    /**
     * Stop message redirection.
     *
     * Use if you do not want message redirected any more.
     */
    public function close() {
        phpunit_util::stop_message_redirection();
    }

    /**
     * To be called from phpunit_util only!
     *
     * @param stdClass $message record from messages table
     */
    public function add_message($message) {
        /* Number messages from 0. */
        $this->messages[] = $message;
    }

    /**
     * Returns all redirected messages.
     *
     * The instances are records from the messages table.
     * The array indexes are numbered from 0 and the order is matching
     * the creation of events.
     *
     * @param callable|null $filter Use to filter the messages.
     * @return array
     */
    public function get_messages(?callable $filter = null): array {
        if ($filter) {
            return array_filter($this->messages, $filter);
        }
        return $this->messages;
    }

    /**
     * Return all redirected messages for a given component.
     *
     * @param string $component Component name.
     * @return array List of messages.
     */
    public function get_messages_by_component(string $component): array {
        $component = core_component::normalize_componentname($component);

        return $this->get_messages(
            fn ($message) => core_component::normalize_componentname($message->component) === $component,
        );
    }

    /**
     * Return all redirected messages for a given component and type.
     *
     * @param string $component Component name.
     * @param string $type Message type.
     * @return array List of messages.
     */
    public function get_messages_by_component_and_type(
        string $component,
        string $type,
    ): array {
        return array_filter($this->get_messages_by_component($component), function($message) use ($type) {
            return $message->eventtype == $type;
        });
    }

    /**
     * Return number of messages redirected to this sink.
     * @return int
     */
    public function count() {
        return count($this->messages);
    }

    /**
     * Removes all previously stored messages.
     */
    public function clear() {
        $this->messages = array();
    }
}

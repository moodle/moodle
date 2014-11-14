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
 * Fixtures for Inbound Message tests.
 *
 * @package    core_message
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\test;
defined('MOODLE_INTERNAL') || die();

/**
 * A base handler for unit testing.
 *
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class handler_base extends \core\message\inbound\handler {
    /**
     * Get the description for unit tests.
     */
    public function get_description() {
    }

    /**
     * Get the name for unit tests.
     */
    public function get_name() {
    }

    /**
     * Process a message for unit tests.
     *
     * @param stdClass $record The record to process
     * @param stdClass $messagedata The message data
     */
    public function process_message(\stdClass $record, \stdClass $messagedata) {
    }
}

/**
 * A handler for unit testing.
 *
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class handler_one extends handler_base {
}

/**
 * A handler for unit testing.
 *
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class handler_two extends handler_base {
}

/**
 * A handler for unit testing.
 *
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class handler_three extends handler_base {
}

/**
 * A handler for unit testing.
 *
 * @copyright  2014 Andrew Nicols
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class handler_four extends handler_base {
}

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
 * Event name reason.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\reason;

/**
 * Event name reason.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class event_name_reason implements reason {

    /** @var string The event name. */
    protected $eventname;

    /**
     * Constructor.
     *
     * @param string $eventname The event name.
     */
    public function __construct($eventname) {
        $this->eventname = $eventname;
    }

    /**
     * Get a signature.
     *
     * @return string
     */
    public function get_signature() {
        return $this->eventname;
    }

    /**
     * Get the type.
     *
     * @return string
     */
    public static function get_type() {
        return __CLASS__;
    }

    /**
     * Reloads the object from its signature.
     *
     * @param string $signature The signature.
     * @return self
     */
    public static function from_signature($signature) {
        return new static($signature);
    }

}

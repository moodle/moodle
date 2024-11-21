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
 * Action.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\action;

use DateTimeImmutable;

/**
 * Action.
 *
 * @package    block_xp
 * @copyright  2024 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class static_action implements action {

    /** @var string The type. */
    protected $type;
    /** @var \context The context. */
    protected $context;
    /** @var int The user ID. */
    protected $userid;
    /** @var int|null The object ID. */
    protected $objectid;
    /** @var DateTimeImmutable The time. */
    protected $time;

    /**
     * Constructor.
     *
     * @param string $type The type.
     * @param \context $context The context.
     * @param int $userid The user ID.
     * @param int|null $objectid The object ID.
     */
    public function __construct(string $type, \context $context, int $userid, ?int $objectid = null) {
        $this->type = $type;
        $this->context = $context;
        $this->userid = $userid;
        $this->objectid = $objectid;
        $this->time = new \DateTimeImmutable();
    }

    /**
     * Get the context.
     *
     * @return \context
     */
    public function get_context(): \context {
        return $this->context;
    }

    /**
     * Get the object ID.
     *
     * @return int|null
     */
    public function get_object_id(): ?int {
        return $this->objectid;
    }

    /**
     * Get the time of the action.
     *
     * @return DateTimeImmutable
     */
    public function get_time(): DateTimeImmutable {
        return $this->time;
    }

    /**
     * Get the type of the action.
     *
     * @return string
     */
    public function get_type(): string {
        return $this->type;
    }

    /**
     * Get the user ID of the person performing the action.
     *
     * @return int
     */
    public function get_user_id(): int {
        return $this->userid;
    }

}

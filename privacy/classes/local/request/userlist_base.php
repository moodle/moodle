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
 * Base implementation of a userlist.
 *
 * @package    core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_privacy\local\request;

defined('MOODLE_INTERNAL') || die();

/**
 * Base implementation of a userlist used to store a set of users.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class userlist_base implements
        // Implement an Iterator to fetch the Context objects.
        \Iterator,

        // Implement the Countable interface to allow the number of returned results to be queried easily.
        \Countable {

    /**
     * @var array List of user IDs.
     *
     * Note: this must not be updated using set_userids only as this
     * ensures uniqueness.
     */
    private $userids = [];

    /**
     * @var string component the frankenstyle component name.
     */
    protected $component = '';

    /**
     * @var int Current position of the iterator.
     */
    protected $iteratorposition = 0;

    /** @var \context The context that this userlist belongs to */
    protected $context;

    /**
     * Constructor to create a new userlist.
     *
     * @param   \context    $context
     * @param   string      $component
     */
    public function __construct(\context $context, string $component) {
        $this->context = $context;
        $this->set_component($component);
    }

    /**
     * Set the userids.
     *
     * @param   array   $userids The list of users.
     * @return  $this
     */
    protected function set_userids(array $userids) : userlist_base {
        $this->userids = array_values(array_unique($userids));

        return $this;
    }

    /**
     * Add a set of additional userids.
     *
     * @param   array   $userids The list of users.
     * @return  $this
     */
    protected function add_userids(array $userids) : userlist_base {
        $this->set_userids(array_merge($this->get_userids(), $userids));

        return $this;
    }

    /**
     * Get the list of user IDs that relate to this request.
     *
     * @return  int[]
     */
    public function get_userids() : array {
        return $this->userids;
    }

    /**
     * Get the complete list of user objects that relate to this request.
     *
     * @return  \stdClass[]
     */
    public function get_users() : array {
        $users = [];
        foreach ($this->userids as $userid) {
            if ($user = \core_user::get_user($userid)) {
                $users[] = $user;
            }
        }

        return $users;
    }

    /**
     * Sets the component for this userlist.
     *
     * @param string $component the frankenstyle component name.
     * @return  $this
     */
    protected function set_component($component) : userlist_base {
        $this->component = $component;

        return $this;
    }

    /**
     * Get the name of the component to which this userlist belongs.
     *
     * @return string the component name associated with this userlist.
     */
    public function get_component() : string {
        return $this->component;
    }

    /**
     * Return the current user.
     *
     * @return  \user
     */
    #[\ReturnTypeWillChange]
    public function current() {
        $user = \core_user::get_user($this->userids[$this->iteratorposition]);

        if (false === $user) {
            // This user was not found.
            unset($this->userids[$this->iteratorposition]);

            // Check to see if there are any more users left.
            if ($this->count()) {
                // Move the pointer to the next record and try again.
                $this->next();
                $user = $this->current();
            } else {
                // There are no more context ids left.
                return null;
            }
        }

        return $user;
    }

    /**
     * Return the key of the current element.
     *
     * @return  mixed
     */
    #[\ReturnTypeWillChange]
    public function key() {
        return $this->iteratorposition;
    }

    /**
     * Move to the next user in the list.
     */
    public function next(): void {
        ++$this->iteratorposition;
    }

    /**
     * Check if the current position is valid.
     *
     * @return  bool
     */
    public function valid(): bool {
        return isset($this->userids[$this->iteratorposition]) && $this->current();
    }

    /**
     * Rewind to the first found user.
     *
     * The list of users is uniqued during the rewind.
     * The rewind is called at the start of most iterations.
     */
    public function rewind(): void {
        $this->iteratorposition = 0;
    }

    /**
     * Return the number of users.
     */
    public function count(): int {
        return count($this->userids);
    }

    /**
     * Get the context for this userlist
     *
     * @return  \context
     */
    public function get_context() : \context {
        return $this->context;
    }
}

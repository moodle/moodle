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
 * This file defines the userlist_collection class object.
 *
 * The userlist_collection is used to organize a collection of userlists.
 *
 * @package core_privacy
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_privacy\local\request;

defined('MOODLE_INTERNAL') || die();

/**
 * A collection of userlist items.
 *
 * @copyright  2018 Andrew Nicols <andrew@nicols.co.uk>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class userlist_collection implements \Iterator, \Countable {

    /**
     * @var \context $context The context that the userlist collection belongs to.
     */
    protected $context = null;

    /**
     * @var array $userlists the internal array of userlist objects.
     */
    protected $userlists = [];

    /**
     * @var int Current position of the iterator.
     */
    protected $iteratorposition = 0;

    /**
     * Constructor to create a new userlist_collection.
     *
     * @param   \context    $context The context to which this collection belongs.
     */
    public function __construct(\context $context) {
        $this->context = $context;
    }

    /**
     * Return the context that this collection relates to.
     *
     * @return  int
     */
    public function get_context() : \context {
        return $this->context;
    }

    /**
     * Add a userlist to this collection.
     *
     * @param   userlist_base $userlist the userlist to export.
     * @return  $this
     */
    public function add_userlist(userlist_base $userlist) : userlist_collection {
        $component = $userlist->get_component();
        if (isset($this->userlists[$component])) {
            throw new \moodle_exception("A userlist has already been added for the '{$component}' component");
        }

        $this->userlists[$component] = $userlist;

        return $this;
    }

    /**
     * Get the userlists in this collection.
     *
     * @return  array   the associative array of userlists in this collection, indexed by component name.
     * E.g. mod_assign => userlist, core_comment => userlist.
     */
    public function get_userlists() : array {
        return $this->userlists;
    }

    /**
     * Get the userlist for the specified component.
     *
     * @param   string      $component the frankenstyle name of the component to fetch for.
     * @return  userlist_base|null
     */
    public function get_userlist_for_component(string $component) {
        if (isset($this->userlists[$component])) {
            return $this->userlists[$component];
        }

        return null;
    }

    /**
     * Return the current contexlist.
     *
     * @return  \user
     */
    #[\ReturnTypeWillChange]
    public function current() {
        $key = $this->get_key_from_position();
        return $this->userlists[$key];
    }

    /**
     * Return the key of the current element.
     *
     * @return  mixed
     */
    #[\ReturnTypeWillChange]
    public function key() {
        return $this->get_key_from_position();
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
        return ($this->iteratorposition < count($this->userlists));
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
     * Get the key for the current iterator position.
     *
     * @return  string
     */
    protected function get_key_from_position() {
        $keylist = array_keys($this->userlists);
        if (isset($keylist[$this->iteratorposition])) {
            return $keylist[$this->iteratorposition];
        }

        return null;
    }

    /**
     * Return the number of users.
     */
    public function count(): int {
        return count($this->userlists);
    }
}

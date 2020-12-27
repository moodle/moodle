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
 * This file defines the contextlist_collection class object.
 *
 * The contextlist_collection is used to organize a collection of contextlists.
 *
 * @package core_privacy
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core_privacy\local\request;

defined('MOODLE_INTERNAL') || die();

/**
 * A collection of contextlist items.
 *
 * @copyright 2018 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class contextlist_collection implements \Iterator, \Countable {

    /**
     * @var int $userid The ID of the user that the contextlist collection belongs to.
     */
    protected $userid = null;

    /**
     * @var array $contextlists the internal array of contextlist objects.
     */
    protected $contextlists = [];

    /**
     * @var int Current position of the iterator.
     */
    protected $iteratorposition = 0;

    /**
     * Constructor to create a new contextlist_collection.
     *
     * @param   int     $userid The userid to which this collection belongs.
     */
    public function __construct($userid) {
        $this->userid = $userid;
    }

    /**
     * Return the ID of the user whose collection this is.
     *
     * @return  int
     */
    public function get_userid() : int {
        return $this->userid;
    }

    /**
     * Add a contextlist to this collection.
     *
     * @param   contextlist_base $contextlist the contextlist to export.
     * @return  $this
     */
    public function add_contextlist(contextlist_base $contextlist) {
        $component = $contextlist->get_component();
        if (empty($component)) {
            throw new \moodle_exception("The contextlist must have a component set");
        }
        if (isset($this->contextlists[$component])) {
            throw new \moodle_exception("A contextlist has already been added for the '{$component}' component");
        }

        $this->contextlists[$component] = $contextlist;

        return $this;
    }

    /**
     * Get the contextlists in this collection.
     *
     * @return  array   the associative array of contextlists in this collection, indexed by component name.
     * E.g. mod_assign => contextlist, core_comment => contextlist.
     */
    public function get_contextlists() : array {
        return $this->contextlists;
    }

    /**
     * Get the contextlist for the specified component.
     *
     * @param   string      $component the frankenstyle name of the component to fetch for.
     * @return  contextlist_base|null
     */
    public function get_contextlist_for_component(string $component) {
        if (isset($this->contextlists[$component])) {
            return $this->contextlists[$component];
        }

        return null;
    }

    /**
     * Return the current contexlist.
     *
     * @return  \context
     */
    public function current() {
        $key = $this->get_key_from_position();
        return $this->contextlists[$key];
    }

    /**
     * Return the key of the current element.
     *
     * @return  mixed
     */
    public function key() {
        return $this->get_key_from_position();
    }

    /**
     * Move to the next context in the list.
     */
    public function next() {
        ++$this->iteratorposition;
    }

    /**
     * Check if the current position is valid.
     *
     * @return  bool
     */
    public function valid() {
        return ($this->iteratorposition < count($this->contextlists));
    }

    /**
     * Rewind to the first found context.
     *
     * The list of contexts is uniqued during the rewind.
     * The rewind is called at the start of most iterations.
     */
    public function rewind() {
        $this->iteratorposition = 0;
    }

    /**
     * Get the key for the current iterator position.
     *
     * @return  string
     */
    protected function get_key_from_position() {
        $keylist = array_keys($this->contextlists);
        if (isset($keylist[$this->iteratorposition])) {
            return $keylist[$this->iteratorposition];
        }

        return null;
    }

    /**
     * Return the number of contexts.
     */
    public function count() {
        return count($this->contextlists);
    }
}

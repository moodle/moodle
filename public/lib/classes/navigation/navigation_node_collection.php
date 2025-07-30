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

namespace core\navigation;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Navigation node collection
 *
 * This class is responsible for managing a collection of navigation nodes.
 * It is required because a node's unique identifier is a combination of both its
 * key and its type.
 *
 * Originally an array was used with a string key that was a combination of the two
 * however it was decided that a better solution would be to use a class that
 * implements the standard IteratorAggregate interface.
 *
 * @package   core
 * @category  navigation
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class navigation_node_collection implements Countable, IteratorAggregate {
    /**
     * A multidimensional array to where the first key is the type and the second
     * key is the nodes key.
     * @var array
     */
    protected $collection = [];
    /**
     * An array that contains references to nodes in the same order they were added.
     * This is maintained as a progressive array.
     * @var array
     */
    protected $orderedcollection = [];
    /**
     * A reference to the last node that was added to the collection
     * @var navigation_node
     */
    protected $last = null;
    /**
     * The total number of items added to this array.
     * @var int
     */
    protected $count = 0;

    /**
     * Label for collection of nodes.
     * @var string
     */
    protected $collectionlabel = '';

    /**
     * Adds a navigation node to the collection.
     *
     * @param navigation_node $node Node to add
     * @param string $beforekey If specified, adds before a node with this key,
     *   otherwise adds at end
     * @return navigation_node Added node
     */
    public function add(navigation_node $node, $beforekey = null) {
        global $CFG;
        $key = $node->key;
        $type = $node->type;

        // First check we have a 2nd dimension for this type.
        if (!array_key_exists($type, $this->orderedcollection)) {
            $this->orderedcollection[$type] = [];
        }
        // Check for a collision and report if debugging is turned on.
        if ($CFG->debug && array_key_exists($key, $this->orderedcollection[$type])) {
            debugging('Navigation node intersect: Adding a node that already exists ' . $key, DEBUG_DEVELOPER);
        }

        // Find the key to add before.
        $newindex = $this->count;
        $last = true;
        if ($beforekey !== null) {
            foreach ($this->collection as $index => $othernode) {
                if ($othernode->key === $beforekey) {
                    $newindex = $index;
                    $last = false;
                    break;
                }
            }
            if ($newindex === $this->count) {
                debugging('Navigation node add_before: Reference node not found ' . $beforekey .
                        ', options: ' . implode(' ', $this->get_key_list()), DEBUG_DEVELOPER);
            }
        }

        // Add the node to the appropriate place in the by-type structure (which
        // is not ordered, despite the variable name).
        $this->orderedcollection[$type][$key] = $node;
        if (!$last) {
            // Update existing references in the ordered collection (which is the
            // one that isn't called 'ordered') to shuffle them along if required.
            for ($oldindex = $this->count; $oldindex > $newindex; $oldindex--) {
                $this->collection[$oldindex] = $this->collection[$oldindex - 1];
            }
        }
        // Add a reference to the node to the progressive collection.
        $this->collection[$newindex] = $this->orderedcollection[$type][$key];
        // Update the last property to a reference to this new node.
        $this->last = $this->orderedcollection[$type][$key];

        // Reorder the array by index if needed.
        if (!$last) {
            ksort($this->collection);
        }
        $this->count++;
        // Return the reference to the now added node.
        return $node;
    }

    /**
     * Return a list of all the keys of all the nodes.
     *
     * @return array the keys.
     */
    public function get_key_list() {
        $keys = [];
        foreach ($this->collection as $node) {
            $keys[] = $node->key;
        }
        return $keys;
    }

    /**
     * Set a label for this collection.
     *
     * @param string $label
     */
    public function set_collectionlabel($label) {
        $this->collectionlabel = $label;
    }

    /**
     * Return a label for this collection.
     *
     * @return string
     */
    public function get_collectionlabel() {
        return $this->collectionlabel;
    }

    /**
     * Fetches a node from this collection.
     *
     * @param string|int $key The key of the node we want to find.
     * @param int $type One of navigation_node::TYPE_*.
     * @return navigation_node|null|false
     */
    public function get($key, $type = null) {
        if ($type !== null) {
            // If the type is known then we can simply check and fetch.
            if (!empty($this->orderedcollection[$type][$key])) {
                return $this->orderedcollection[$type][$key];
            }
        } else {
            // Because we don't know the type we look in the progressive array.
            foreach ($this->collection as $node) {
                if ($node->key === $key) {
                    return $node;
                }
            }
        }
        return false;
    }

    /**
     * Searches for a node with matching key and type.
     *
     * This function searches both the nodes in this collection and all of
     * the nodes in each collection belonging to the nodes in this collection.
     *
     * Recursive.
     *
     * @param string|int $key  The key of the node we want to find.
     * @param int $type  One of navigation_node::TYPE_*.
     * @return navigation_node|false
     */
    public function find($key, $type = null) {
        if (
            $type !== null
            && array_key_exists($type, $this->orderedcollection)
            && array_key_exists($key, $this->orderedcollection[$type])
        ) {
            return $this->orderedcollection[$type][$key];
        } else {
            $nodes = $this->getIterator();

            // Search immediate children first.
            foreach ($nodes as &$node) {
                if ($node->key === $key && ($type === null || $type === $node->type)) {
                    return $node;
                }
            }

            // Now search each childs children.
            foreach ($nodes as &$node) {
                $result = $node->children->find($key, $type);
                if ($result !== false) {
                    return $result;
                }
            }
        }
        return false;
    }

    /**
     * Fetches the last node that was added to this collection
     *
     * @return navigation_node
     */
    public function last() {
        return $this->last;
    }

    /**
     * Fetches all nodes of a given type from this collection
     *
     * @param string|int $type  node type being searched for.
     * @return array ordered collection
     */
    public function type($type) {
        if (!array_key_exists($type, $this->orderedcollection)) {
            $this->orderedcollection[$type] = [];
        }
        return $this->orderedcollection[$type];
    }
    /**
     * Removes the node with the given key and type from the collection
     *
     * @param string|int $key The key of the node we want to find.
     * @param int $type
     * @return bool
     */
    public function remove($key, $type = null) {
        $child = $this->get($key, $type);
        if ($child !== false) {
            foreach ($this->collection as $colkey => $node) {
                if ($node->key === $key && (is_null($type) || $node->type == $type)) {
                    unset($this->collection[$colkey]);
                    $this->collection = array_values($this->collection);
                    break;
                }
            }
            unset($this->orderedcollection[$child->type][$child->key]);
            $this->count--;
            return true;
        }
        return false;
    }

    #[\Override]
    public function count(): int {
        return $this->count;
    }

    #[\Override]
    public function getIterator(): Traversable {
        return new ArrayIterator($this->collection);
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(navigation_node_collection::class, \navigation_node_collection::class);

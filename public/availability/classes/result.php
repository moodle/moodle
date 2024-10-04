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
 * Class represents the result of an availability check for the user.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_availability;

defined('MOODLE_INTERNAL') || die();

/**
 * Class represents the result of an availability check for the user.
 *
 * You can pass an object of this class to tree::get_result_information to
 * display suitable student information about the result.
 *
 * @package core_availability
 * @copyright 2014 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class result {
    /** @var bool True if the item is available */
    protected $available;

    /** @var tree_node[] Array of nodes to display in failure information (node=>node). */
    protected $shownodes = array();

    /**
     * Constructs result.
     *
     * @param bool $available True if available
     * @param tree_node $node Node if failed & should be displayed
     * @param result[] $failedchildren Array of children who failed too
     */
    public function __construct($available, ?tree_node $node = null,
            array $failedchildren = array()) {
        $this->available = $available;
        if (!$available) {
            if ($node) {
                $this->shownodes[spl_object_hash($node)] = $node;
            }
            foreach ($failedchildren as $child) {
                foreach ($child->shownodes as $key => $node) {
                    $this->shownodes[$key] = $node;
                }
            }
        }
    }

    /**
     * Checks if the result was a yes.
     *
     * @return bool True if the activity is available
     */
    public function is_available() {
        return $this->available;
    }

    /**
     * Filters the provided array so that it only includes nodes which are
     * supposed to be displayed in the result output. (I.e. those for which
     * the user failed the test, and which are not set to totally hide
     * output.)
     *
     * @param tree_node[] $array Input array of nodes
     * @return array Output array containing only those nodes set for display
     */
    public function filter_nodes(array $array) {
        $out = array();
        foreach ($array as $key => $node) {
            if (array_key_exists(spl_object_hash($node), $this->shownodes)) {
                $out[$key] = $node;
            }
        }
        return $out;
    }
}

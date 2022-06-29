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
 * Class to sort items.
 *
 * @package    mod_forum
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_forum\local\entities;

defined('MOODLE_INTERNAL') || die();

/**
 * Class to sort lists of items.
 *
 * @copyright  2019 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sorter {
    /** @var callable $getid Function used to get the id from an item */
    private $getid;
    /** @var callable $getparentid Function used to get the parent id from an item */
    private $getparentid;

    /**
     * Constructor.
     *
     * Allows the calling code to provide 2 functions to get the id and parent id from
     * the list of items it is intended to process.
     *
     * This allows this class to be composed in numerous different ways to support various
     * types of items while keeping the underlying sorting algorithm consistent.
     *
     * @param callable $getid Function used to get the id from an item
     * @param callable $getparentid Function used to get the parent id from an item
     */
    public function __construct(callable $getid, callable $getparentid) {
        $this->getid = $getid;
        $this->getparentid = $getparentid;
    }

    /**
     * Sort a list of items into a parent/child data structure. The resulting data structure
     * is a recursive array of arrays where the first element is the parent and the second is
     * an array of it's children.
     *
     * For example
     * If we have an array of items A, B, C, and D where D is a child of C, B and C are children
     * of A.
     *
     * This function would sort them into the following:
     * [
     *      [
     *          A,
     *          [
     *              [
     *                  B,
     *                  []
     *              ],
     *              [
     *                  C,
     *                  [
     *                      [
     *                          D,
     *                          []
     *                      ]
     *                  ]
     *              ]
     *          ]
     *      ]
     * ]
     *
     * @param array $items The list of items to sort.
     * @return array
     */
    public function sort_into_children(array $items) : array {
        $ids = array_reduce($items, function($carry, $item) {
            $carry[($this->getid)($item)] = true;
            return $carry;
        }, []);

        // Split out the items into "parents" and "replies" (children). These are unsorted
        // at this point.
        [$parents, $replies] = array_reduce($items, function($carry, $item) use ($ids) {
            $parentid = ($this->getparentid)($item);

            if (!empty($ids[$parentid])) {
                // This is a child to another item in the list so add it to the children list.
                $carry[1][] = $item;
            } else {
                // This isn't a child to anything in our list so it's a parent.
                $carry[0][] = $item;
            }

            return $carry;
        }, [[], []]);

        if (empty($replies)) {
            return array_map(function($parent) {
                return [$parent, []];
            }, $parents);
        }

        // Recurse to sort the replies into the correct nesting.
        $sortedreplies = $this->sort_into_children($replies);

        // Sort the parents and sorted replies into their matching pairs.
        return array_map(function($parent) use ($sortedreplies) {
            $parentid = ($this->getid)($parent);
            return [
                $parent,
                array_values(array_filter($sortedreplies, function($replydata) use ($parentid) {
                    return ($this->getparentid)($replydata[0]) == $parentid;
                }))
            ];
        }, $parents);
    }

    /**
     * Take the data structure returned from "sort_into_children" and flatten it back
     * into an array. It does a depth first flatten which maintains the reply ordering.
     *
     * @param array $items Items in the data structure returned by "sort_into_children"
     * @return array A flat array.
     */
    public function flatten_children(array $items) : array {
        $result = [];

        foreach ($items as [$item, $children]) {
            $result[] = $item;
            $result = array_merge($result, $this->flatten_children($children));
        }

        return $result;
    }
}

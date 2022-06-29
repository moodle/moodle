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

namespace core\navigation\views;

use navigation_node;
use navigation_node_collection;

/**
 * Class view.
 *
 * The base view class which expands on the navigation_node,
 *
 * @package     core
 * @category    navigation
 * @copyright   2021 onwards Peter Dias
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class view extends navigation_node {
    /** @var stdClass $context the current context */
    protected $context;
    /** @var moodle_page $page the moodle page that the navigation belongs to */
    protected $page;
    /** @var bool $initialised A switch to see if the navigation node is initialised */
    protected $initialised = false;
    /** @var navigation_node $activenode A string identifier for the active node*/
    public $activenode;

    /**
     * Function to initialise the respective view
     * @return void
     */
    abstract public function initialise(): void;

    /**
     * navigation constructor.
     * @param \moodle_page $page
     */
    public function __construct(\moodle_page $page) {
        global $FULLME;

        if (during_initial_install()) {
            return false;
        }

        $this->page = $page;
        $this->context = $this->page->context;
        $this->children = new navigation_node_collection();
    }

    /**
     * Get the leaf nodes for the nav view
     *
     * @param navigation_node $source The settingsnav OR navigation object
     * @param array $nodes An array of nodes to fetch from the source which specifies the node type and final order
     * @return array $nodesordered The fetched nodes ordered based on final specification.
     */
    protected function get_leaf_nodes(navigation_node $source, array $nodes): array {
        $nodesordered = [];
        foreach ($nodes as $type => $leaves) {
            foreach ($leaves as $leaf => $location) {
                if ($node = $source->find($leaf, $type)) {
                    $nodesordered["$location"] = $nodesordered["$location"] ?? $node;
                }
            }
        }

        return $nodesordered;
    }

    /**
     * Scan the given node for the active node. It starts first with a strict search and then switches to a base search if
     * required.
     *
     * @param navigation_node $node The node to scan.
     * @return navigation_node|null The active node or null.
     */
    protected function scan_for_active_node(navigation_node $node): ?navigation_node {
        $result = $this->active_node_scan($node);
        if (!is_null($result)) {
            return $result;
        } else {
            return $this->active_node_scan($node, URL_MATCH_BASE);
        }
    }

    /**
     * This function recursively scans nodes until it finds the active node or there
     * are no more nodes. We are using a custom implementation here to adjust the strictness
     * and also because we need the parent node and not the specific child node in the new views.
     * e.g. Structure for site admin,
     *      SecondaryNav
     *          - Site Admin
     *          - Users
     *              - User policies
     *          - Courses
     * In the above example, if we are on the 'User Policies' page, the active node should be 'Users'
     *
     * @param navigation_node $node
     * @param int $strictness How stict to be with the scan for the active node.
     * @return navigation_node|null
     */
    protected function active_node_scan(navigation_node $node,
        int $strictness = URL_MATCH_EXACT): ?navigation_node {

        $result = null;
        $activekey = $this->page->get_secondary_active_tab();
        if ($activekey) {
            if ($node->key && $activekey === $node->key) {
                return $node;
            }
        } else if ($node->check_if_active($strictness)) {
            return $node; // No need to continue, exit function.
        }

        foreach ($node->children as $child) {
            if ($this->active_node_scan($child, $strictness)) {
                // If node is one of the new views then set the active node to the child.
                if (!$node instanceof view) {
                    $node->make_active();
                    $result = $node;
                } else {
                    $child->make_active();
                    $this->activenode = $child;
                    $result = $child;
                }

                // If the secondary active tab not set then just return the result (fallback).
                if ($activekey === null) {
                    return $result;
                }
            } else {
                // Make sure to reset the active state.
                $child->make_inactive();
            }
        }

        return $result;
    }
}

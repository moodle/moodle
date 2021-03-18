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

        // Not all pages override the active url. Do it now.
        if ($this->page->has_set_url()) {
            self::override_active_url(new \moodle_url($this->page->url));
        } else {
            self::override_active_url(new \moodle_url($FULLME));
        }

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
                    $nodesordered["$location"] = $node;
                }
            }
        }

        return $nodesordered;
    }

    /**
     * This function recursively scans nodes until it finds the active node or there
     * are no more nodes. We are using a custom implementation here to be more strict with the comparison
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
     * @return navigation_node|null
     */
    protected function scan_for_active_node(navigation_node $node): ?navigation_node {
        if ($node->check_if_active()) {
            return $node; // No need to continue, exit function.
        }

        if ($node->children->count() > 0) {
            foreach ($node->children as $child) {
                if ($this->scan_for_active_node($child)) {
                    // If node is one of the new views then set the active node to the child.
                    if (!$node instanceof view) {
                        $node->make_active();
                        $child->make_inactive();
                    } else {
                        $child->make_active();
                    }
                    return $node; // We have found the active node, set the parent status, no need to continue.
                }
            }
        }

        return null;
    }
}

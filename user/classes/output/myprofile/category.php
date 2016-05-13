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
 * Defines a category in my profile page navigation.
 *
 * @package   core_user
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_user\output\myprofile;
defined('MOODLE_INTERNAL') || die();

/**
 * Defines a category in my profile page navigation.
 *
 * @since     Moodle 2.9
 * @package   core_user
 * @copyright 2015 onwards Ankit Agarwal
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class category implements \renderable {

    /**
     * @var string Name of the category after which this category should appear.
     */
    private $after;

    /**
     * @var string Name of the category.
     */
    private $name;

    /**
     * @var string Title of the category.
     */
    private $title;

    /**
     * @var node[] Array of nodes associated with this category.
     */
    private $nodes = array();

    /**
     * @var string HTML class attribute for this category. Classes should be separated by a space, e.g. 'class1 class2'
     */
    private $classes;

    /**
     * @var array list of properties publicly accessible via __get.
     */
    private $properties = array('after', 'name', 'title', 'nodes', 'classes');

    /**
     * Constructor for category class.
     *
     * @param string $name Category name.
     * @param string $title category title.
     * @param null|string $after Name of category after which this category should appear.
     * @param null|string $classes a list of css classes.
     */
    public function __construct($name, $title, $after = null, $classes = null) {
        $this->after = $after;
        $this->name = $name;
        $this->title = $title;
        $this->classes = $classes;
    }

    /**
     * Add a node to this category.
     *
     * @param node $node node object.
     * @see \core_user\output\myprofile\tree::add_node()
     *
     * @throws \coding_exception
     */
    public function add_node(node $node) {
        $name = $node->name;
        if (isset($this->nodes[$name])) {
            throw new \coding_exception("Node with name $name already exists");
        }
        if ($node->parentcat !== $this->name) {
            throw new \coding_exception("Node parent must match with the category it is added to");
        }
        $this->nodes[$node->name] = $node;
    }

    /**
     * Sort nodes of the category in the order in which they should be displayed.
     *
     * @see \core_user\output\myprofile\tree::sort_categories()
     * @throws \coding_exception
     */
    public function sort_nodes() {
        $tempnodes = array();
        $this->validate_after_order();

        // First content noes.
        foreach ($this->nodes as $node) {
            $after = $node->after;
            $content = $node->content;
            if (($after == null && !empty($content)) || $node->name === 'editprofile') {
                // Can go anywhere in the cat. Also show content nodes first.
                $tempnodes = array_merge($tempnodes, array($node->name => $node), $this->find_nodes_after($node));
            }
        }

        // Now nodes with no content.
        foreach ($this->nodes as $node) {
            $after = $node->after;
            $content = $node->content;
            if ($after == null && empty($content)) {
                // Can go anywhere in the cat. Also show content nodes first.
                $tempnodes = array_merge($tempnodes, array($node->name => $node), $this->find_nodes_after($node));
            }
        }

        if (count($tempnodes) !== count($this->nodes)) {
            // Orphan nodes found.
            throw new \coding_exception('Some of the nodes specified contains invalid \'after\' property');
        }
        $this->nodes = $tempnodes;
    }

    /**
     * Verifies that node with content can come after node with content only . Also verifies the same thing for nodes without
     * content.
     * @throws \coding_exception
     */
    protected function validate_after_order() {
        $nodearray = $this->nodes;
        foreach ($this->nodes as $node) {
            $after = $node->after;
            if (!empty($after)) {
                if (empty($nodearray[$after])) {
                    throw new \coding_exception('node {$node->name} specified contains invalid \'after\' property');
                } else {
                    // Valid node found.
                    $afternode = $nodearray[$after];
                    $beforecontent = $node->content;
                    $aftercontent = $afternode->content;

                    if ((empty($beforecontent) && !empty($aftercontent)) || (!empty($beforecontent) && empty($aftercontent))) {
                        // Only node with content are allowed after content nodes. Same goes for no content nodes.
                        throw new \coding_exception('node {$node->name} specified contains invalid \'after\' property');
                    }
                }
            }
        }
    }

    /**
     * Given a node object find all node objects that should appear after it.
     *
     * @param node $node node object
     *
     * @return array
     */
    protected function find_nodes_after($node) {
        $return = array();
        $nodearray = $this->nodes;
        foreach ($nodearray as $nodeelement) {
            if ($nodeelement->after === $node->name) {
                // Find all nodes that comes after this node as well.
                $return = array_merge($return, array($nodeelement->name => $nodeelement), $this->find_nodes_after($nodeelement));
            }
        }
        return $return;
    }

    /**
     * Magic get method.
     *
     * @param string $prop property to get.
     *
     * @return mixed
     * @throws \coding_exception
     */
    public function __get($prop) {
        if (in_array($prop, $this->properties)) {
            return $this->$prop;
        }
        throw new \coding_exception('Property "' . $prop . '" doesn\'t exist');
    }
}

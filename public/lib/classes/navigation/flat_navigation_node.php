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

use core\exception\coding_exception;

/**
 * Subclass of navigation_node allowing different rendering for the flat navigation
 * in particular allowing dividers and indents.
 *
 * @deprecated since Moodle 4.0 - do not use any more. Leverage secondary/tertiary navigation concepts
 * @package   core
 * @category  navigation
 * @copyright 2016 Damyon Wiese
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class flat_navigation_node extends navigation_node {
    /** @var $indent integer The indent level */
    private $indent = 0;

    /** @var $showdivider bool Show a divider before this element */
    private $showdivider = false;

    /** @var $collectionlabel string Label for a group of nodes */
    private $collectionlabel = '';

    /**
     * A proxy constructor
     *
     * @param mixed $navnode A navigation_node or an array
     */
    public function __construct($navnode, $indent) {
        debugging("Flat nav has been deprecated in favour of primary/secondary navigation concepts");
        if (is_array($navnode)) {
            parent::__construct($navnode);
        } else if ($navnode instanceof navigation_node) {
            // Just clone everything.
            $objvalues = get_object_vars($navnode);
            foreach ($objvalues as $key => $value) {
                 $this->$key = $value;
            }
        } else {
            throw new coding_exception('Not a valid flat_navigation_node');
        }
        $this->indent = $indent;
    }

    /**
     * Setter, a label is required for a flat navigation node that shows a divider.
     *
     * @param string $label
     */
    public function set_collectionlabel($label) {
        $this->collectionlabel = $label;
    }

    /**
     * Getter, get the label for this flat_navigation node, or it's parent if it doesn't have one.
     *
     * @return string
     */
    public function get_collectionlabel() {
        if (!empty($this->collectionlabel)) {
            return $this->collectionlabel;
        }
        if ($this->parent && ($this->parent instanceof flat_navigation_node || $this->parent instanceof flat_navigation)) {
            return $this->parent->get_collectionlabel();
        }
        debugging('Navigation region requires a label', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does this node represent a course section link.
     * @return boolean
     */
    public function is_section() {
        return $this->type == navigation_node::TYPE_SECTION;
    }

    /**
     * In flat navigation - sections are active if we are looking at activities in the section.
     * @return boolean
     */
    public function isactive() {
        global $PAGE;

        if ($this->is_section()) {
            $active = $PAGE->navigation->find_active_node();
            if ($active) {
                while ($active = $active->parent) {
                    if ($active->key == $this->key && $active->type == $this->type) {
                        return true;
                    }
                }
            }
        }
        return $this->isactive;
    }

    /**
     * Getter for "showdivider"
     * @return boolean
     */
    public function showdivider() {
        return $this->showdivider;
    }

    /**
     * Setter for "showdivider"
     * @param $val boolean
     * @param $label string Label for the group of nodes
     */
    public function set_showdivider($val, $label = '') {
        $this->showdivider = $val;
        if ($this->showdivider && empty($label)) {
            debugging('Navigation region requires a label', DEBUG_DEVELOPER);
        } else {
            $this->set_collectionlabel($label);
        }
    }

    /**
     * Getter for "indent"
     * @return boolean
     */
    public function get_indent() {
        return $this->indent;
    }

    /**
     * Setter for "indent"
     * @param $val boolean
     */
    public function set_indent($val) {
        $this->indent = $val;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(flat_navigation_node::class, \flat_navigation_node::class);

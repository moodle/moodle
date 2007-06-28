<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2003  Martin Dougiamas  http://dougiamas.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once $CFG->libdir . '/gradelib.php';

/**
 * This class represents a complete tree of categories, grade_items and final grades,
 * organises as an array primarily, but which can also be converted to other formats.
 * It has simple method calls with complex implementations, allowing for easy insertion,
 * deletion and moving of items and categories within the tree.
 */
class grade_tree {

    /**
     * The basic representation of the tree as a hierarchical, 3-tiered array.
     * @var array $tree_array
     */
    var $tree_array = array();

    /**
     * Whether or not this grade_tree should load and store all the grades in addition to the categories and items.
     * @var boolean $include_grades
     */
    var $include_grades;

    /**
     * A string of GET URL variables, namely courseid and sesskey, used in most URLs built by this class.
     * @var string $commonvars
     */
    var $commonvars;

    /**
     * 2D array of grade items and categories
     */
    var $levels;

    /**
     * Constructor, retrieves and stores a hierarchical array of all grade_category and grade_item
     * objects for the given courseid. Full objects are instantiated.
     * and renumbering.
     * @param int $courseid
     * @param boolean $include_grades
     * @param boolean $fillers include fillers and colspans, make the levels var "rectabgular"
     * @param boolean $include_cagegory_items inclute the items for categories in the tree
     */
    function grade_tree($courseid, $include_grades=false, $fillers=true, $include_cagegory_items=false) {
        global $USER;

        $this->courseid = $courseid;
        $this->include_grades = $include_grades;
        $this->commonvars = "&amp;sesskey=$USER->sesskey&amp;id=$this->courseid";

        // get course grade tree
        $this->tree_array =& grade_category::fetch_course_tree($courseid, $include_grades, $include_cagegory_items);

        if ($fillers) {
            // inject fake categories == fillers
            grade_tree::inject_fillers($this->tree_array, 0);
            // add colspans to categories and fillers
            grade_tree::inject_colspans($this->tree_array);
        }

        $this->levels = array();
        grade_tree::fill_levels($this->levels, $this->tree_array, 0);
    }


    /**
     * Static recursive helper - fills the levels array, useful when accessing tree elements of one level
     */
    function fill_levels(&$levels, &$tree, $depth) {
        if (!array_key_exists($depth, $levels)) {
            $levels[$depth] = array();
        }
        $levels[$depth][] =& $tree;
        $depth++;
        if (empty($tree['children'])) {
            return;
        }
        $prev = 0;
        foreach ($tree['children'] as $sortorder=>$child) {
            grade_tree::fill_levels($levels, $tree['children'][$sortorder], $depth);
            $tree['children'][$sortorder]['prev'] = $prev;
            $tree['children'][$sortorder]['next'] = 0;
            if ($prev) {
                $tree['children'][$prev]['next'] = $sortorder;
            }
            $prev = $sortorder;
        }
    }

    /**
     * Static recursive helper - makes full tree (all leafes are at the same level)
     */
    function inject_fillers(&$tree, $depth) {
        $depth++;

        if (empty($tree['children'])) {
            return $depth;
        }
        $chdepths = array();
        $chids = array_keys($tree['children']);

        foreach ($chids as $chid) {
            $chdepths[$chid] = grade_tree::inject_fillers($tree['children'][$chid], $depth);
        }
        arsort($chdepths);

        $maxdepth = reset($chdepths);
        foreach ($chdepths as $chid=>$chd) {
            if ($chd == $maxdepth) {
                continue;
            }
            for ($i=0; $i < $maxdepth-$chd; $i++) {
                $oldchild =& $tree['children'][$chid];
                $tree['children'][$chid] = array('object'=>'filler', 'children'=>array($oldchild));
            }
        }

        return $maxdepth;
    }

    /**
     * Static recursive helper - add colspan information into categories
     */
    function inject_colspans(&$tree) {
        if (empty($tree['children'])) {
            return 1;
        }
        $count = 0;
        foreach ($tree['children'] as $key=>$child) {
            $count += grade_tree::inject_colspans($tree['children'][$key]);
        }
        if ($count > 1) {
            $tree['colspan'] = $count - 1;
        }
        return $count;
    }

    /**
     * Parses the array in search of a given sort order (the elements are indexed by
     * sortorder), and returns a stdClass object with vital information about the
     * element it has found.
     * @param int $sortorder
     * @return object element
     */
    function locate_element($sortorder) {
        foreach ($this->levels as $row) {
            foreach ($row as $element) {
                if (empty($element['object']->sortorder)) {
                    continue;
                }
                if ($element['object']->sortorder == $sortorder) {
                    return $element;
                }
            }
        }

        return null;
    }

    /**
     * Given an element object, returns its type (topcat, subcat or item).
     * The $element can be a straight object (fully instantiated), an array of 'object' and 'children'/'final_grades', or a stdClass element
     * as produced by grade_tree::locate_element(). This method supports all three types of inputs.
     * @param object $element
     * @return string Type
     */
    function get_element_type($element) {
        if ($element['object'] == 'filler') {
            return 'filler';
        }

        if (get_class($element['object']) == 'grade_category') {
            if ($element['object']->depth == 2) {
                return 'topcat';
            } else {
                return 'subcat';
            }
        }

        return 'item';
    }

    /**
     * Removes the given element (a stdClass object or a sortorder), remove_elements
     * it from the tree. This does not renumber the tree. If a sortorder (int) is given, this
     * method will first retrieve the referenced element from the tree, then re-run the method with that object.
     * @var object $element An stdClass object typically returned by $this->locate(), or a sortorder (int)
     * @return boolean
     */
    function remove_element($element) {
        //TODO: fix me
        return false;
/*
        if (empty($this->first_sortorder)) {
            $this->reset_first_sortorder();
        }

        if (isset($element->index)) {
            // Decompose the element's index and build string for eval(unset) statement to follow
            $indices = explode('/', $element->index);
            $element_to_unset = '$this->tree_array[' . $indices[0] . ']';

            if (isset($indices[1])) {
                $element_to_unset .= "['children'][" . $indices[1] . ']';
            }

            if (isset($indices[2])) {
                $element_to_unset .= "['children'][" . $indices[2] . ']';
            }

            eval("unset($element_to_unset);");

            if (empty($element->element['object'])) {
                debugging("Could not delete this element from the DB due to missing information.");
                return false;
            }

            $this->need_delete[$element->element['object']->id] = $element->element['object'];

            return true;
        } else {
            $element = $this->locate_element($element);
            if (!empty($element)) {
                return $this->remove_element($element);
            } else {
                debugging("The element you provided grade_tree::remove_element() is not valid.");
                return false;
            }
        }

        debugging("Unable to remove an element from the grade_tree.");
        return false;*/
    }

    /**
     * Inserts an element in the tree. This can be either an array as returned by the grade_category methods, or
     * an element object returned by grade_tree.
     * @param mixed $element array or object. If object, the sub-tree is contained in $object->element
     * @param int $destination_sortorder Where to insert the element
     * @param string $position Either 'before' the destination_sortorder or 'after'
     * @param boolean
     */
    function insert_element($element, $destination_sortorder, $position='before') {
        //TODO: fix me
        return false;
/*        if (empty($this->first_sortorder)) {
            $this->reset_first_sortorder();
        }

        if ($position == 'before') {
            $offset = -1;
        } elseif ($position == 'after') {
            $offset = 0;
        } else {
            debugging('move_element(..... $position) can only be "before" or "after", you gave ' . $position);
            return false;
        }

        if (is_array($element)) {
            $new_element = new stdClass();
            $new_element->element = $element;
        } elseif (is_object($element)) {
            $new_element = $element;
        }

        $new_element_class = get_class($new_element->element['object']);
        $has_final_grades = !empty($new_element->element['final_grades']);

        // If the object is a grade_item, but the final_grades index isn't yet loaded, make the switch now. Same for grade_category and children
        if ($new_element_class == 'grade_item' && !$has_final_grades && $this->include_grades) {
            $new_element->element['final_grades'] = $new_element->element['object']->get_final();

        } elseif ($new_element_class == 'grade_category' && empty($new_element->element['children']) && $new_element->element['object']->has_children()) {
            $new_element->element['children'] = $new_element->element['object']->get_children(1);
            unset($new_element->element['object']->children);
        }

        $destination_array = array($destination_sortorder => $new_element->element);

        // Get the position of the destination element
        $destination_element = $this->locate_element($destination_sortorder);
        $position = $destination_element->position;

        // Decompose the element's index and build string for eval(array_splice) statement to follow
        $indices = explode('/', $destination_element->index);

        if (empty($indices)) {
            debugging("The destination element did not have a valid index (as assigned by grade_tree::locate_element).");
            return false;
        }

        $element_to_splice = '$this->tree_array';

        if (isset($indices[1])) {
            $element_to_splice .= '[' . $indices[0] . "]['children']";
        }

        if (isset($indices[2])) {
            $element_to_splice .= '[' . $indices[1] . "]['children']";
        }

        eval("array_splice($element_to_splice, \$position + \$offset, 0, \$destination_array);");

        if (!is_object($new_element)) {
            debugging("Could not insert this element into the DB due to missing information.");
            return false;
        }

        $this->need_insert[$new_element->element['object']->id] = $new_element->element['object'];

        return true;*/
    }

    /**
     * Moves an existing element in the tree to another position OF EQUAL LEVEL. This
     * constraint is essential and very important.
     * @param int $source_sortorder The sortorder of the element to move
     * @param int $destination_sortorder The sortorder where the element will go
     * @param string $position Either 'before' the destination_sortorder or 'after' it
     * @return boolean
     */
    function move_element($source_sortorder, $destination_sortorder, $position='before') {
        //TODO: fix me
        return false;
/*        if (empty($this->first_sortorder)) {
            $this->reset_first_sortorder();
        }

        // Locate the position of the source element in the tree
        $source = $this->locate_element($source_sortorder);

        // Remove this element from the tree
        $this->remove_element($source);

        $destination = $this->locate_element($destination_sortorder);

        // Insert the element before the destination sortorder
        $this->insert_element($source, $destination_sortorder, $position);

        return true;*/
    }


}

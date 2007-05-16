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

/**
 * This class represents a complete tree of categories, grade_items and final grades,
 * organises as an array primarily, but which can also be converted to other formats.
 * It has simple method calls with complex implementations, allowing for easy insertion,
 * deletion and moving of items and categories within the tree.
 */ 
class grade_tree {
    /**
     * The first sortorder for this tree, before any changes were made.
     * @var int $first_sortorder
     */
    var $first_sortorder;

    /**
     * The basic representation of the tree as a hierarchical, 3-tiered array.
     * @var $tree_array
     */
    var $tree_array = array();

    /**
     * Parses the array in search of a given sort order (the elements are indexed by 
     * sortorder), and returns a stdClass object with vital information about the 
     * element it has found.
     * @param int $sortorder
     * @return object element
     */
    function locate_element($sortorder) {
        $topcatcount = 0;

        foreach ($this->tree_array as $topcatkey => $topcat) {
            $topcatcount++;
            $subcatcount = 0;
            $retval = new stdClass();
            $retval->topcatindex = $topcatkey;

            if ($topcatkey == $sortorder) {
                $retval->depth = 1;
                $retval->element = $topcat;
                $retval->position = $topcatcount;
                return $retval;
            }

            if (is_array($topcat)) {
                foreach ($topcat as $subcatkey => $subcat) {
                    $subcatcount++;
                    $itemcount = 0;

                    $retval->subcatindex = $subcatkey;
                    if ($subcatkey == $sortorder) {
                        $retval->depth = 2;
                        $retval->element = $subcat;
                        $retval->position = $subcatcount;
                        return $retval;
                    }
                    
                    if (is_array($subcat)) {
                        foreach ($subcat as $itemkey => $item) {
                            $itemcount++;
                            $retval->itemindex = $itemkey;
                            if ($itemkey == $sortorder) {
                                $retval->depth = 3;
                                $retval->element = $item;
                                $retval->position = $itemcount;
                                return $retval;
                            } 
                        }
                    }
                }
            }
        } 
        return $retval;
    }

    /**
     * Removes the given element (a stdClass object or a sortorder), remove_elements
     * it from the tree. This does not renumber the tree.
     * @var object $element An stdClass object typically returned by $this->locate(), or a sortorder
     * @return boolean
     */
    function remove_element($element) {
        if (isset($element->depth)) { 
            switch ($element->depth) {
                case 1:
                    unset($this->tree_array[$element->topcatindex]);
                    break;
                case 2:
                    unset($this->tree_array[$element->topcatindex][$element->subcatindex]);
                    break;
                case 3:
                    unset($this->tree_array[$element->topcatindex][$element->subcatindex][$element->itemindex]);
                    break;
            }
            return true;
        } else {
            $element = $this->locate_element($element);
            if (!empty($element)) {
                return $this->remove_element($element);
            } else {
                return false;
            }
        }
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
        if ($position == 'before') {
            $offset = -1;
        } elseif ($position == 'after') {
            $offset = 0;
        } else {
            die ('move_element(..... $position) can only be "before" or "after", you gave ' . $position);
        }

        // TODO Problem when moving topcategories: sortorder gets reindexed when splicing the array
        $destination_array = array($destination_sortorder => $source->element);
        switch($element->depth) {
            case 1:
                array_splice($this->tree_array, 
                    $element->position + $offset, 0, 
                    $destination_array); 
                break;
            case 2:
                array_splice($this->tree_array[$element->topcatindex], 
                    $element->position + $offset, 0, 
                    $destination_array); 
                break;
            case 3:
                array_splice($this->tree_array[$element->topcatindex][$element->subcatindex], 
                    $element->position + $offset, 0, 
                    $destination_array); 
                break; 
        }

        return true; 
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
        // Locate the position of the source element in the tree
        $source = $this->locate_element($source_sortorder);

        // Remove this element from the tree
        $this->remove_element($source);

        $destination = $this->locate_element($destination_sortorder);

        if ($destination->depth != $source->depth) {
            echo "Source and Destination were at different levels.";
            return false; 
        } 
        
        // Insert the element before the destination sortorder
        $this->insert_element($destination, $destination_sortorder, $position); 

        return true;
    }
    

    /**
     * One at a time, re-assigns new sort orders for every element in the tree, starting 
     * with a base number.
     * @return boolean;
     */
    function renumber($starting_sortorder=NULL) {
        $sortorder = $starting_sortorder;
        
        if (empty($starting_sortorder)) { 
            $sortorder = $this->first_sortorder;
        }
        
        $newtree = array();

        foreach ($this->tree_array as $topcat) {
            $sortorder++; 
            if (is_array($topcat)) {
                $topcatsortorder = $sortorder;
                foreach ($topcat as $subcat) {
                    $sortorder++; 
                    if(is_array($subcat)) {
                        $subcatsortorder = $sortorder;
                        foreach ($subcat as $item) {
                            $sortorder++;
                            $newtree[$topcatsortorder][$subcatsortorder][$sortorder] = $item;
                        }
                    } else {
                        $newtree[$topcatsortorder][$sortorder] = $subcat; 
                    } 
                }
            } else { 
                $newtree[$sortorder] = $topcat;
            } 
        }
            
        $this->tree_array = $newtree;
        return true;
    }
}

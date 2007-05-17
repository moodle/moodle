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
     * @var array $tree_array
     */
    var $tree_array = array();

    /**
     * Another array with fillers for categories and items that do not have a parent, but have
     * are not at level 2. This is used by the display_grades method.
     * @var array $tree_filled
     */
    var $tree_filled = array();

    /**
     * An array of objects that need updating (normally just grade_item.sortorder).
     * @var array $need_update
     */
    var $need_update = array();

    /**
     * Constructor, retrieves and stores a hierarchical array of all grade_category and grade_item
     * objects for the given courseid or the entire site if no courseid given. Full objects are instantiated
     * by default, but this can be switched off. The tree is indexed by sortorder, to facilitate CRUD operations
     * and renumbering.
     * @param int $courseid
     * @param boolean $fullobjects
     * @param array $tree
     */
    function grade_tree($courseid=NULL, $fullobjects=true, $tree=NULL) {
        $this->courseid = $courseid;
        if (!empty($tree)) {
            $this->tree_array = $tree;
        } else {
            $this->tree_array = $this->get_tree($fullobjects);
        }
    }

    /**
     * Parses the array in search of a given sort order (the elements are indexed by 
     * sortorder), and returns a stdClass object with vital information about the 
     * element it has found.
     * @param int $sortorder
     * @return object element
     */
    function locate_element($sortorder) {
        $topcatcount = 0;
        $retval = false;

        foreach ($this->tree_array as $topcatkey => $topcat) {
            $topcatcount++;
            $subcatcount = 0;
            $retval = new stdClass();
            $retval->topcatindex = $topcatkey;
            unset($retval->subcatindex);
            unset($retval->itemindex);

            if ($topcatkey == $sortorder) {
                $retval->depth = 1;
                $retval->element = $topcat;
                $retval->position = $topcatcount;
                return $retval;
            }

            if (!empty($topcat['children'])) {
                foreach ($topcat['children'] as $subcatkey => $subcat) {
                    $subcatcount++;
                    unset($retval->itemindex);
                    $itemcount = 0;

                    $retval->subcatindex = $subcatkey;
                    if ($subcatkey == $sortorder) {
                        $retval->depth = 2;
                        $retval->element = $subcat;
                        $retval->position = $subcatcount;
                        return $retval;
                    }
                    
                    if (!empty($subcat['children'])) {
                        foreach ($subcat['children'] as $itemkey => $item) {
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
        if (empty($this->first_sortorder)) { 
            $this->first_sortorder = key($this->tree_array);
        }
        
        if (isset($element->depth)) { 
            switch ($element->depth) {
                case 1:
                    unset($this->tree_array[$element->topcatindex]);
                    break;
                case 2:
                    unset($this->tree_array[$element->topcatindex]['children'][$element->subcatindex]);
                    break;
                case 3:
                    unset($this->tree_array[$element->topcatindex]['children'][$element->subcatindex]['children'][$element->itemindex]);
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
        if (empty($this->first_sortorder)) { 
            $this->first_sortorder = key($this->tree_array);
        }
        
        if ($position == 'before') {
            $offset = -1;
        } elseif ($position == 'after') {
            $offset = 0;
        } else {
            die ('move_element(..... $position) can only be "before" or "after", you gave ' . $position);
        }

        // TODO Problem when moving topcategories: sortorder gets reindexed when splicing the array
        $destination_array = array($destination_sortorder => $element->element);

        // Get the position of the destination element
        $destination_element = $this->locate_element($destination_sortorder);
        $position = $destination_element->position;

        switch($element->depth) {
            case 1:
                array_splice($this->tree_array, 
                    $position + $offset, 0, 
                    $destination_array); 
                break;
            case 2:
                array_splice($this->tree_array[$destination_element->topcatindex]['children'], 
                    $position + $offset, 0, 
                    $destination_array); 
                break;
            case 3:
                array_splice($this->tree_array[$destination_element->topcatindex]['children'][$destination_element->subcatindex]['children'], 
                    $position + $offset, 0, 
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
        if (empty($this->first_sortorder)) { 
            $this->first_sortorder = key($this->tree_array);
        } 

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
        $this->insert_element($source, $destination_sortorder, $position); 

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
            $sortorder = $this->first_sortorder - 1;
        }
        
        $newtree = array();

        foreach ($this->tree_array as $topcat) {
            $sortorder++; 
            if (!empty($topcat['children'])) {
                $topcatsortorder = $sortorder;
                foreach ($topcat['children'] as $subcat) {
                    $sortorder++; 
                    if (!empty($subcat['children'])) {
                        $subcatsortorder = $sortorder;
                        foreach ($subcat['children'] as $item) {
                            $sortorder++;
                            $newtree[$topcatsortorder]['children'][$subcatsortorder]['children'][$sortorder] = $item;
                        }
                        $newtree[$topcatsortorder]['children'][$subcatsortorder]['object'] = $subcat['object'];
                    } else {
                        $newtree[$topcatsortorder]['children'][$sortorder] = $subcat; 
                    } 
                }
                $newtree[$topcatsortorder]['object'] = $topcat['object'];
            } else { 
                $newtree[$sortorder] = $topcat;
            } 
        }
        $this->tree_array = $newtree;
        unset($this->first_sortorder);
        return true;
    }
    
    /**
     * Static method that returns a sorted, nested array of all grade_categories and grade_items for 
     * a given course, or for the entire site if no courseid is given.
     * @param boolean $fullobjects Whether to instantiate full objects based on the data or not
     * @return array
     */
    function get_tree($fullobjects=true) {
        global $CFG;
        global $db;
        $db->debug = false;
        $tree = array();
        $fillers = array();

        $category_table = $CFG->prefix . 'grade_categories';
        $items_table = $CFG->prefix . 'grade_items';

        $catconstraint = '';
        $itemconstraint = '';

        if (!empty($this->courseid)) {
            $catconstraint = " AND $category_table.courseid = $this->courseid ";
            $itemconstraint = " AND $items_table.courseid = $this->courseid ";
        }
        
        // Get ordered list of grade_items (not category type)
        $query = "SELECT * FROM $items_table WHERE itemtype <> 'category' $itemconstraint ORDER BY sortorder";
        $grade_items = get_records_sql($query);

        // For every grade_item that doesn't have a parent category, create category fillers
        foreach ($grade_items as $itemid => $item) {
            if (empty($item->categoryid)) {
                if ($fullobjects) {
                    $item = new grade_item($item);
                }
                $fillers[$item->sortorder] = $item;
            }
        }

        // Get all top categories
        $query = "SELECT $category_table.*, sortorder FROM $category_table, $items_table 
                  WHERE iteminstance = $category_table.id $catconstraint ORDER BY sortorder";

        $topcats = get_records_sql($query);
        
        if (empty($topcats)) {
            return null;
        }
        
        // If any of these categories has grade_items as children, create a topcategory filler with colspan=count(children)
        foreach ($topcats as $topcatid => $topcat) {
            $topcatobject = new grade_category($topcat, false);
            if ($topcatobject->get_childrentype() == 'grade_item' && empty($topcatobject->parent)) {
                $topcatobject->childrencount = $topcatobject->has_children();
                $fillers[$topcat->sortorder] = $topcatobject;
                unset($topcats[$topcatid]);
            }
        }
        
        foreach ($topcats as $topcatid => $topcat) {
            // Check the fillers array, see if one must be inserted before this topcat
            if (key($fillers) < $topcat->sortorder) {
                $sortorder = key($fillers);
                $object = current($fillers);
                unset($fillers[$sortorder]);
                
                $this->tree_filled[$sortorder] = $this->get_filler($object, $fullobjects);
            }

            $query = "SELECT $category_table.*, sortorder FROM $category_table, $items_table 
                      WHERE iteminstance = $category_table.id AND parent = $topcatid ORDER BY sortorder";
            $subcats = get_records_sql($query);
            $subcattree = array();
            
            if (empty($subcats)) {
                continue;
            }

            foreach ($subcats as $subcatid => $subcat) {
                $itemtree = array();
                $items = get_records('grade_items', 'categoryid', $subcatid, 'sortorder');
                
                if (empty($items)) {
                    continue;
                }
                
                foreach ($items as $itemid => $item) { 
                    $finaltree = array();
                    
                    if ($fullobjects) {
                        $final = new grade_grades_final();
                        $final->itemid = $itemid;
                        $finals = $final->fetch_all_using_this();
                    } else {
                        $finals = get_records('grade_grades_final', 'itemid', $itemid);
                    }

                    if ($fullobjects) {
                        $sortorder = $item->sortorder;
                        $item = new grade_item($item);
                        $item->sortorder = $sortorder;
                    }

                    $itemtree[$item->sortorder] = array('object' => $item, 'finalgrades' => $finals);
                }
                
                if ($fullobjects) {
                    $sortorder = $subcat->sortorder;
                    $subcat = new grade_category($subcat, false);
                    $subcat->sortorder = $sortorder;
                }
                $subcattree[$subcat->sortorder] = array('object' => $subcat, 'children' => $itemtree);
            }
            
            if ($fullobjects) {
                $sortorder = $topcat->sortorder;
                $topcat = new grade_category($topcat, false);
                $topcat->sortorder = $sortorder;
            }

            $tree[$topcat->sortorder] = array('object' => $topcat, 'children' => $subcattree);
            $this->tree_filled[$topcat->sortorder] = array('object' => $topcat, 'children' => $subcattree);
        }

        // If there are still grade_items or grade_categories without a top category, add another filler
        if (!empty($fillers)) {
            foreach ($fillers as $sortorder => $object) { 
                $this->tree_filled[$sortorder] = $this->get_filler($object, $fullobjects);
            }
        }
        
        $db->debug = false;
        return $tree;
    }

    /**
     * Returns a hierarchical array, prefilled with the values needed to populate
     * the tree of grade_items in the cases where a grade_item or grade_category doesn't have a 
     * 2nd level topcategory.
     * @param object $object A grade_item or a grade_category object
     * @param boolean $fullobjects Whether to instantiate full objects or just return stdClass objects
     * @return array
     */
    function get_filler($object, $fullobjects=true) { 
        $filler_array = array();

        // Depending on whether the filler is for a grade_item or a category...
        if (isset($object->itemname)) {
            if (get_class($object) == 'grade_item') {
                $finals = $object->load_final();
            } else {
                $item_object = new grade_item($object, false);
                $finals = $object->load_final();
            }

            $filler_array = array('object' => 'filler', 'children' => 
                array(0 => array('object' => 'filler', 'children' => 
                    array(0 => array('object' => $object, 'finalgrades' => $finals))))); 
        } else {
            $subcat_children = $object->get_children(0, 'flat');
            $children_for_tree = array();
            foreach ($subcat_children as $itemid => $item) {
                if (get_class($item) == 'grade_item') {
                    $finals = $item->load_final();
                } else {
                    $item_object = new grade_item($item, false);
                    $finals = $item->load_final();
                }
                
                $children_for_tree[$itemid] = array('object' => $item, 'finalgrades' => $finals); 
            }

            $filler_array = array('object' => 'filler', 'colspan' => $object->childrencount, 'children' =>
                array(0 => array('object' => $object, 'children' => $children_for_tree)));
        } 

        return $filler_array;
    }
    
    /**
     * Returns a HTML table with all the grades in the course requested, or all the grades in the site.
     * IMPORTANT: This method (and its associated methods) assumes that we are using only 2 levels of categories (topcat and subcat)
     * @todo Return extra column for students
     * @todo Return a row of final grades for each student
     * @todo Return icons
     * @todo Return totals
     * @todo Return row below headers for grading range
     * @return string HTML table
     */
    function display_grades() {
        // 1. Fetch all top-level categories for this course, with all children preloaded, sorted by sortorder
        $tree = $this->tree_filled;
        $topcathtml = '<tr>';
        $cathtml    = '<tr>';
        $itemhtml   = '<tr>';
        
        foreach ($tree as $topcat) {
            $itemcount = 0;
            
            foreach ($topcat['children'] as $catkey => $cat) {
                $catitemcount = 0;

                foreach ($cat['children'] as $item) {
                    $itemcount++;
                    $catitemcount++;
                    $itemhtml .= '<td>' . $item['object']->itemname . '</td>';
                }
                
                if ($cat['object'] == 'filler') {
                    $cathtml .= '<td class="subfiller">&nbsp;</td>';
                } else {
                    $cat['object']->load_grade_item();
                    $cathtml .= '<td colspan="' . $catitemcount . '">' . $cat['object']->fullname . '</td>';
                }
            }

            if ($topcat['object'] == 'filler') {
                $colspan = null;
                if (!empty($topcat['colspan'])) {
                    $colspan = 'colspan="' . $topcat['colspan'] . '" ';
                }
                $topcathtml .= '<td ' . $colspan . 'class="topfiller">&nbsp;</td>';
            } else {
                $topcathtml .= '<th colspan="' . $itemcount . '">' . $topcat['object']->fullname . '</th>';
            }

        }
        
        $itemhtml   .= '</tr>';
        $cathtml    .= '</tr>';
        $topcathtml .= '</tr>';

        return "<table style=\"text-align: center\" border=\"1\">$topcathtml$cathtml$itemhtml</table>";

    } 
}

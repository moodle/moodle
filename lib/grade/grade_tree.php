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

require_once $CFG->libdir . '/grade/grade_category.php';
require_once $CFG->libdir . '/grade/grade_item.php';
require_once $CFG->libdir . '/grade/grade_grades_final.php';
require_once $CFG->libdir . '/grade/grade_grades_raw.php';

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
     * An array of objects that need inserting in the DB.
     * @var array $need_insert
     */
    var $need_insert = array();

    /**
     * An array of objects that need deleting from the DB.
     * @var array $need_delete
     */
    var $need_delete = array();

    /**
     * Whether or not this grade_tree should load and store all the grades in addition to the categories and items.
     * @var boolean $include_grades
     */
    var $include_grades;

    /**
     * Constructor, retrieves and stores a hierarchical array of all grade_category and grade_item
     * objects for the given courseid or the entire site if no courseid given. Full objects are instantiated
     * by default, but this can be switched off. The tree is indexed by sortorder, to facilitate CRUD operations
     * and renumbering.
     * @param int $courseid
     * @param boolean $include_grades
     * @param array $tree
     */
    function grade_tree($courseid=NULL, $include_grades=false, $tree=NULL) {
        $this->courseid = $courseid;
        $this->include_grades = $include_grades;
        if (!empty($tree)) {
            $this->tree_array = $tree;
        } else {
            $this->tree_array = $this->get_tree();
        }
        
        if (!empty($this->tree_array)) {
            $this->first_sortorder = key($this->tree_array);
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
        
        if (empty($this->tree_array)) {
            debugging("grade_tree->tree_array was empty, I could not locate the element at sortorder $sortorder");
            return false;
        }
        
        $level1count = 0;
        foreach ($this->tree_array as $level1key => $level1) {
            $level1count++;
            $level2count = 0;
            $retval = new stdClass();
            $retval->index = $level1key;

            if ($level1key == $sortorder) {
                $retval->element = $level1;
                $retval->position = $level1count;
                return $retval;
            }

            if (!empty($level1['children'])) {
                foreach ($level1['children'] as $level2key => $level2) {
                    $level2count++;
                    $level3count = 0;

                    $retval->index = "$level1key/$level2key";
                    if ($level2key == $sortorder) {
                        $retval->element = $level2;
                        $retval->position = $level2count;
                        return $retval;
                    }
                    
                    if (!empty($level2['children'])) {
                        foreach ($level2['children'] as $level3key => $level3) {
                            $level3count++;
                            $retval->index = "$level1key/$level2key/$level3key";
                            
                            if ($level3key == $sortorder) {
                                $retval->element = $level3;
                                $retval->position = $level3count;
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
     * Given an element object, returns its type (topcat, subcat or item). 
     * The $element can be a straight object (fully instantiated), an array of 'object' and 'children'/'final_grades', or a stdClass element
     * as produced by grade_tree::locate_element(). This method supports all three types of inputs.
     * @param object $element
     * @return string Type
     */
    function get_element_type($element) {
        $object = $this->get_object_from_element($element);
        
        if (empty($object)) {
            debugging("Invalid element given to grade_tree::get_element_type.");
            return false;
        }

        if (get_class($object) == 'grade_item') {
            return 'item';
        } elseif (get_class($object) == 'grade_category') {
            $object->get_children();
            if (!empty($object->children)) {
                $first_child = current($object->children);
                if (get_class($first_child) == 'grade_item') {
                    return 'subcat';
                } elseif (get_class($first_child) == 'grade_category') {
                    return 'topcat';
                } else {
                    debugging("The category's first child was neither a category nor an item.");
                    return false;
                }
            } else {
                debugging("The category did not have any children.");
                return false;
            }
        } else {
            debugging("Invalid element given to grade_tree::get_element_type.");
            return false;
        } 

        debugging("Could not determine the type of the given element.");
        return false;
    }

    /**
     * Removes the given element (a stdClass object or a sortorder), remove_elements
     * it from the tree. This does not renumber the tree. If a sortorder (int) is given, this 
     * method will first retrieve the referenced element from the tree, then re-run the method with that object.
     * @var object $element An stdClass object typically returned by $this->locate(), or a sortorder (int)
     * @return boolean
     */
    function remove_element($element) {
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
        return false;
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
        
        // If the object is a grade_item, but the final_grades index isn't yet loaded, make the switch now. Same for grade_category and children
        if (get_class($new_element->element['object']) == 'grade_item' && empty($new_element->element['final_grades']) && $this->include_grades) {
            $new_element->element['final_grades'] = $new_element->element['object']->load_final();
            unset($new_element->element['object']->grade_grades_final);
        } elseif (get_class($new_element->element['object']) == 'grade_category' && 
                    empty($new_element->element['children']) &&
                    $new_element->element['object']->has_children()) {
            $new_element->element['children'] = $new_element->element['object']->get_children(1);
            unset($new_element->element['object']->children);
        }


        // TODO Problem when moving topcategories: sortorder gets reindexed when splicing the array
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
            $this->reset_first_sortorder();
        } 
        
        // Locate the position of the source element in the tree
        $source = $this->locate_element($source_sortorder);

        // Remove this element from the tree
        $this->remove_element($source);

        $destination = $this->locate_element($destination_sortorder);
        
        // Insert the element before the destination sortorder
        $this->insert_element($source, $destination_sortorder, $position); 

        return true;
    }
    
    /**
     * Uses the key of the first entry in this->tree_array to reset the first_sortorder of this tree. Essential 
     * after each renumbering.
     */
    function reset_first_sortorder() { 
        if (count($this->tree_array) < 1) {
            debugging("Cannot reset the grade_tree's first_sortorder because the tree_array hasn't been loaded or is empty.");
            return false;
        }
        reset($this->tree_array);
        $this->first_sortorder = key($this->tree_array);
        
        return $this->first_sortorder;
    }

    /**
     * One at a time, re-assigns new sort orders for every element in the tree, recursively moving
     * down and across the tree.
     * @param int $starting_sortorder Used by recursion to "seed" the first element in each sub-tree
     * @param array $element A sub-tree given to each layer of recursion. If null, level 0 of recursion is assumed.
     * @return array A debugging array which shows the progression of variables throughout this method. This is very useful
     * to identify problems and implement new functionality.
     */
    function renumber($starting_sortorder=NULL, $elements=NULL) {
        $sortorder = $starting_sortorder;
        
        if (empty($elements) && empty($starting_sortorder)) {
            if (empty($this->first_sortorder)) {
                debugging("The tree's first_order variable isn't set, you must provide a starting_sortorder to the renumber method.");
                return false;
            }
            $sortorder = $this->first_sortorder - 1; 
            $elements = $this->tree_array;
        } elseif(!empty($elements) && empty($starting_sortorder)) {
            debugging("Entered second level of recursion without a starting_sortorder.");
        }

        $newtree = array();
        $this->first_sortorder = $sortorder;

        foreach ($elements as $key => $element) {
            $this->first_sortorder++;

            if (!empty($element['children'])) {
                $newtree[$this->first_sortorder] = $element;
                $newtree[$this->first_sortorder]['children'] = $this->renumber($this->first_sortorder, $element['children']); 
            }  else { 
                
                $element['object']->previous_sortorder = $this->get_neighbour_sortorder($element, 'previous');
                $element['object']->next_sortorder = $this->get_neighbour_sortorder($element, 'next');
                $newtree[$this->first_sortorder] = $element; 
                
                if ($this->first_sortorder != $element['object']->sortorder) {
                    $this->need_update[$element['object']->get_item_id()] = 
                        array('old_sortorder' => $element['object']->sortorder, 
                              'new_sortorder' => $this->first_sortorder);
                }
            } 
        }
        
        // If no starting sortorder was given, it means we have finished building the tree, so assign it to this->tree_array. Otherwise return the new tree.
        if (empty($starting_sortorder)) {
            $this->tree_array = $newtree;
            unset($this->first_sortorder);
            $this->build_tree_filled(); 
            return true;
        } else {
            return $newtree;
        }
    }
  
    /**
     * Because the $element referred to in this class is rather loosely defined, it 
     * may come in different flavours and forms. However, it will almost always contain
     * an object (or be an object). This method takes care of type checking and returns
     * the object within the $element, if present.
     * @param mixed $element
     * @return object
     */
    function get_object_from_element($element) {
        if (is_object($element) && get_class($element) != 'stdClass') {
            return $element;
        } elseif (!empty($element->element['object'])) {
            return $element->element['object']; 
        } elseif (!empty($element['object'])) {
            return $element['object'];
        } elseif (!method_exists($object, 'get_sortorder')) {
            return null;
        } else {
            return null;
        }
    }


    /** 
     * Given an element array ('object' => object, 'children' => array), 
     * searches for the element at the same level placed immediately before this one
     * in sortorder, and returns its sortorder if found. Recursive function.
     * @param array $element
     * @param string $position 'previous' or 'next'
     * @param array $array of elements to search. Defaults to $this->tree_array
     * @return int Sortorder (or null if none found)
     */
    function get_neighbour_sortorder($element, $position, $array=null) {
        if (empty($this->tree_array) || empty($element) || empty($position) || !in_array($position, array('previous', 'next'))) {
            return null;
        }
        
        $object = $this->get_object_from_element($element);
        
        if (empty($object)) {
            debugging("Invalid element given to grade_tree::get_neighbour_sortorder.");
            return false;
        }
        
        if (empty($array)) {
            $array = $this->tree_array;
        }

        $lastsortorder = null;
        $returnnextelement = false;

        foreach ($array as $sortorder => $child) {
            if ($returnnextelement) {
                return $sortorder;
            }

            if ($object->get_sortorder() == $sortorder) {
                if ($position == 'previous') {
                    return $lastsortorder;
                } elseif ($position == 'next') {
                    $returnnextelement = true;
                }
                continue;
            }

            if (!empty($child['children'])) {
                return $this->get_neighbour_sortorder($element, $position, $child['children']);
            }

            $lastsortorder = $sortorder;
        }
        return null;
    }

    /**
     * Static method that returns a sorted, nested array of all grade_categories and grade_items for 
     * a given course, or for the entire site if no courseid is given.
     * @TODO Break this up in more nuclear methods
     * @TODO Apply recursion to tree-building code (get_tree($first_parent=NULL))
     *  NOTE the above todos are tricky in this instance because we are building two arrays simultaneously: tree_array and tree_filled
     * @return array
     */
    function get_tree() {
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
        
        if (empty($grade_items)) {
            return null;
        }
        
        // For every grade_item that doesn't have a parent category, create category fillers
        foreach ($grade_items as $itemid => $item) {
            if (empty($item->categoryid)) {
                $item = new grade_item($item);
                if (empty($item->sortorder)) {
                    $fillers[] = $item;
                } else {
                    $fillers[$item->sortorder] = $item;
                }
            }
        }
        
        // Get all top categories
        $query = "SELECT $category_table.*, sortorder FROM $category_table, $items_table 
                  WHERE iteminstance = $category_table.id $catconstraint ORDER BY sortorder";

        $topcats = get_records_sql($query);
        
        if (empty($topcats)) {
            $topcats = $grade_items;
            $topcats[0] = new stdClass();
            $topcats[0]->sortorder = 0;
            $topcats[0]->courseid = $this->courseid;
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
            
        $last_topsortorder = null;
        
        foreach ($topcats as $topcatid => $topcat) {
            $last_subsortorder = null;

            // Check the fillers array, see if one must be inserted before this topcat
            if (key($fillers) < $topcat->sortorder) {
                $sortorder = key($fillers);
                $object = current($fillers);
                unset($fillers[$sortorder]);
                
                $this->tree_filled[$sortorder] = $this->get_filler($object);
                $element = array();

                if (get_class($object) == 'grade_category') {
                    $children = $object->get_children(1);
                    unset($object->children);
                    $last_itemsortorder = null;
                    $itemtree = array();

                    foreach ($children as $element) { 
                        $finals = array();

                        if ($this->include_grades) {
                            $final = new grade_grades_final();
                            $final->itemid = $element['object']->id;
                            $finals = $final->fetch_all_using_this();
                        }

                        $element['object']->previous_sortorder = $last_itemsortorder;
                        $itemtree[$element['object']->sortorder] = array('object' => $element['object'], 'finalgrades' => $finals);
                        
                        if (!empty($itemtree[$last_itemsortorder])) {
                            $itemtree[$last_itemsortorder]['object']->next_sortorder = $element['object']->sortorder;
                        }

                        $last_itemsortorder = $element['object']->sortorder;
                    }

                    $element['children'] = $itemtree;
                } elseif (get_class($object) == 'grade_item' && $this->include_grades) {
                    $final_grades = $object->get_final();
                    unset($object->grade_grades_final);
                    $element['final_grades'] = $final_grades;
                }

                $object->sortorder = $sortorder;
                $object->previous_sortorder = $last_topsortorder;
                $element['object'] = $object;
                $tree[$sortorder] = $element;
                
                if (!empty($tree[$last_topsortorder])) {
                    $tree[$last_topsortorder]['object']->next_sortorder = $sortorder;
                }
                
                $last_topsortorder = $sortorder;
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
                $last_itemsortorder = null;
                
                if (empty($items)) {
                    continue;
                }
                
                foreach ($items as $itemid => $item) { 
                    $finals = array();

                    if ($this->include_grades) {
                        $final = new grade_grades_final();
                        $final->itemid = $itemid;
                        $finals = $final->fetch_all_using_this();
                    }

                    $sortorder = $item->sortorder;
                    $item = new grade_item($item);
                    $item->sortorder = $sortorder;

                    $item->previous_sortorder = $last_itemsortorder;
                    $itemtree[$item->sortorder] = array('object' => $item, 'finalgrades' => $finals);
                    
                    if (!empty($itemtree[$last_itemsortorder])) {
                        $itemtree[$last_itemsortorder]['object']->next_sortorder = $item->sortorder;
                    }

                    $last_itemsortorder = $item->sortorder;
                }
                
                $sortorder = $subcat->sortorder;
                $subcat = new grade_category($subcat, false);
                $subcat->sortorder = $sortorder;
                $subcat->previous_sortorder = $last_subsortorder;
                $subcattree[$subcat->sortorder] = array('object' => $subcat, 'children' => $itemtree);
                
                if (!empty($subcattree[$last_subsortorder])) {
                    $subcattree[$last_subsortorder]['object']->next_sortorder = $subcat->sortorder;
                }

                $last_subsortorder = $subcat->sortorder;
            }
            
            $sortorder = $topcat->sortorder;
            $topcat = new grade_category($topcat, false);
            $topcat->sortorder = $sortorder;
            
            $topcat->previous_sortorder = $last_topsortorder;
            $tree[$topcat->sortorder] = array('object' => $topcat, 'children' => $subcattree);
            $this->tree_filled[$topcat->sortorder] = array('object' => $topcat, 'children' => $subcattree);
            
            if (!empty($topcattree[$last_topsortorder])) {
                $topcattree[$last_topsortorder]['object']->next_sortorder = $topcat->sortorder;
            }

            $last_topsortorder = $topcat->sortorder;
        }

        // If there are still grade_items or grade_categories without a top category, add another filler
        if (!empty($fillers)) {
            foreach ($fillers as $sortorder => $object) { 
                $this->tree_filled[$sortorder] = $this->get_filler($object);
                
                if (get_class($object) == 'grade_category') {
                    $children = $object->get_children(1);
                    unset($object->children);
                    $last_itemsortorder = null;
                    $itemtree = array();

                    foreach ($children as $element) { 
                        $finals = array();

                        if ($this->include_grades) {
                            $final = new grade_grades_final();
                            $final->itemid = $element['object']->id;
                            $finals = $final->fetch_all_using_this();
                        }

                        $element['object']->previous_sortorder = $last_itemsortorder;
                        $itemtree[$element['object']->sortorder] = array('object' => $element['object'], 'finalgrades' => $finals);
                        
                        if (!empty($itemtree[$last_itemsortorder])) {
                            $itemtree[$last_itemsortorder]['object']->next_sortorder = $element['object']->sortorder;
                        }

                        $last_itemsortorder = $element['object']->sortorder;
                    }

                    $element['children'] = $itemtree;
                } elseif (get_class($object) == 'grade_item' && $this->include_grades) {
                    $final_grades = $object->get_final();
                    unset($object->grade_grades_final);
                    $element['final_grades'] = $final_grades;
                }

                $object->sortorder = $sortorder;
                $object->previous_sortorder = $last_topsortorder;
                $element['object'] = $object;
                $tree[$sortorder] = $element;
                
                if (!empty($tree[$last_topsortorder])) {
                    $tree[$last_topsortorder]['object']->next_sortorder = $sortorder;
                }
                
                $last_topsortorder = $sortorder;
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
     * @return array
     */
    function get_filler($object) { 
        $filler_array = array();

        // Depending on whether the filler is for a grade_item or a category...
        if (isset($object->itemname)) {
            $finals = array();
            if ($this->include_grades) {
                if (get_class($object) == 'grade_item') {
                    $finals = $object->load_final();
                } else {
                    $item_object = new grade_item($object, false);
                    $finals = $object->load_final();
                }
            }

            $filler_array = array('object' => 'filler', 'children' => 
                array(0 => array('object' => 'filler', 'children' => 
                    array(0 => array('object' => $object, 'finalgrades' => $finals))))); 
        } elseif (method_exists($object, 'get_children')) {

            $subcat_children = $object->get_children(0, 'flat');
            $children_for_tree = array();
            foreach ($subcat_children as $itemid => $item) {
                $finals = array();
                
                if ($this->include_grades) {
                    if (get_class($item) == 'grade_item') {
                        $finals = $item->load_final();
                    } else {
                        $item_object = new grade_item($item, false);
                        if (method_exists($item, 'load_final')) {
                            $finals = $item->load_final();
                        }
                    }
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

        if (empty($this->tree_filled)) {
            debugging("The tree_filled array wasn't initialised, grade_tree could not display the grades correctly.");
            return false;
        }

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

    /**
     * Using $this->tree_array, builds $this->tree_filled, which is the same array but with fake categories as
     * fillers. These are used by display_grades, to print out empty cells over orphan grade_items and grade_categories.
     * @return boolean Success or Failure.
     */
    function build_tree_filled() {
        if (empty($this->tree_array)) {
            debugging("You cannot build the tree_filled array until the tree_array is filled.");
            return false;
        }
        
        $this->tree_filled = array();

        foreach ($this->tree_array as $level1order => $level1) {
            if ($this->get_element_type($level1) == 'item' || $this->get_element_type($level1) == 'subcat') {
                $this->tree_filled[$level1order] = $this->get_filler($level1['object']);
            } else {
                $this->tree_filled[$level1order] = $level1;
            }
        }

        reset($this->tree_array);

        return true;
    }

    /**
     * Performs any delete, insert or update queries required, depending on the objects
     * stored in $this->need_update, need_insert and need_delete.
     * @return boolean Success or Failure
     */
    function update_db() {
        // Perform deletions first
        foreach ($this->need_delete as $id => $object) {
            // If an item is both in the delete AND insert arrays, it must be an existing object that only needs updating, so ignore it.
            if (empty($this->need_insert[$id])) {
                if (!$object->delete()) {
                    debugging("Could not delete object from DB.");
                }
            }
        }

        foreach ($this->need_insert as $id => $object) {
            if (empty($this->need_delete[$id])) {
                if (!$object->insert()) {
                    debugging("Could not insert object into DB.");
                }
            }
        }

        $this->need_delete = array();
        $this->need_insert = array();

        // The items' sortorder are updated
        foreach ($this->need_update as $id => $element) {
            if (!set_field('grade_items', 'sortorder', $element['new_sortorder'], 'id', $id)) {
                debugging("Could not update the grade_item's sortorder in DB.");
            } 
        } 

        $this->need_update = array();
    }

    /**
     * Returns a HTML list with sorting arrows and insert boxes. This is a recursive method.
     * @return string HTML code
     */
    function get_edit_tree($level=1, $elements=null) {
        if (empty($this->tree_array)) {
            return null;
        } else {
            global $USER;
            global $CFG;

            $strmoveup   = get_string("moveup");
            $strmovedown = get_string("movedown");
            
            if (empty($elements)) {
                $list = '<ul id="grade_edit_tree">';
                $elements = $this->tree_array;
            } else {
                $list = '<ul class="level' . $level . 'children">';
            } 
            
            $first = true;
            $count = 1;
            $last = false;
            
            if (count($elements) == 1) {
                $last = true;
            }

            foreach ($elements as $sortorder => $element) {
                if (empty($element->next_sortorder)) {
                    $element->next_sortorder = null;
                }
                $list .= '<li class="level' . $level . 'element sortorder' 
                      . $element['object']->get_sortorder() . '">' 
                      . $element['object']->get_name();
                
                if (!$first) {
                    $list .= '<a title="'.$strmoveup.'" href="category.php?courseid='.$this->courseid
                          . '&amp;source=' . $sortorder . '&amp;moveup=' . $element['object']->previous_sortorder 
                          . '&amp;sesskey='.$USER->sesskey.'">'
                          . '<img src="'.$CFG->pixpath.'/t/up.gif" class="iconsmall" alt="'.$strmoveup.'" /></a> ';
                } else {
                    $list .= '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" class="iconsmall" alt="" /> ';
                }

                if (!$last) {
                    $list .= '<a title="'.$strmovedown.'" href="category.php?courseid='.$this->courseid
                          . '&amp;source=' . $sortorder . '&amp;movedown=' . $element['object']->next_sortorder 
                          . '&amp;sesskey='.$USER->sesskey.'">'
                          . '<img src="'.$CFG->pixpath.'/t/down.gif" class="iconsmall" alt="'.$strmovedown.'" /></a> ';
                } else {
                    $list .= '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" class="iconsmall" alt="" /> ';
                }
                
                if (!empty($element['children'])) {
                    $list .= $this->get_edit_tree($level + 1, $element['children']);
                }
                
                $list .= '</li>';

                $first = false;
                $count++;
                if ($count == count($elements)) {
                    $last = true;
                } 
            }
                    
            $list .= '</ul>';

            return $list;
        }

        return false;
    }
}

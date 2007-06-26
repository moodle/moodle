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
require_once $CFG->libdir . '/grade/grade_grades.php';

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
     * An array of grade_items and grade_categories that have no parent and are not top-categories.
     * @var arra $fillers
     */
    var $fillers = array();

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
     * An flat array of final grades indexed by userid.
     * @var array $grades
     */
    var $grades = array();

    /**
     * A string of GET URL variables, namely courseid and sesskey, used in most URLs built by this class.
     * @var string $commonvars
     */
    var $commonvars;

    /**
     * Constructor, retrieves and stores a hierarchical array of all grade_category and grade_item
     * objects for the given courseid or the entire site if no courseid given. Full objects are instantiated
     * by default, but this can be switched off. The tree is indexed by sortorder, to facilitate CRUD operations
     * and renumbering.
     * @param int $courseid If null, a blank object is instantiated. If 0, all courses are retrieved in the entire site (can be very slow!)
     * @param boolean $include_grades
     * @param array $tree
     */
    function grade_tree($courseid=NULL, $include_grades=false, $tree=NULL) {
        if (is_null($courseid)) {
            // empty object, do nothing
        } else {
            if ($courseid == 0) {
                $courseid = null;
            }

            global $USER;

            $this->courseid = $courseid;
            $this->include_grades = $include_grades;
            $this->commonvars = "&amp;sesskey=$USER->sesskey&amp;id=$this->courseid";

            if (!empty($tree)) {
                $this->tree_array = $tree;
            } else {
                $this->tree_array = $this->get_tree();
            }

            if (!empty($this->tree_array)) {
                $this->first_sortorder = key($this->tree_array);
                $this->renumber();
            }
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
     * @param int $parentid The id of the element within which this iteration of the method is running. Used to reassign element parentage.
     * @return array A debugging array which shows the progression of variables throughout this method. This is very useful
     * to identify problems and implement new functionality.
     */
    function renumber($starting_sortorder=NULL, $elements=NULL, $parentid=NULL) {
        $sortorder = $starting_sortorder;

        if (empty($elements) && empty($starting_sortorder)) {
            if (!isset($this->first_sortorder)) {
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
            $new_sortorder = $this->first_sortorder;
            $old_sortorder = $element['object']->get_sortorder();

            // Assign new sortorder
            $element['object']->sortorder = $new_sortorder;

            $element['object']->previous_sortorder = $this->get_neighbour_sortorder($element, 'previous');
            $element['object']->next_sortorder = $this->get_neighbour_sortorder($element, 'next');

            if (!empty($element['children'])) {
                $newtree[$this->first_sortorder] = $element;
                $newtree[$this->first_sortorder]['children'] = $this->renumber($this->first_sortorder, $element['children'], $element['object']->id);
            }  else {
                $newtree[$this->first_sortorder] = $element;
            }

            if ($new_sortorder != $old_sortorder) {
                $element['object']->set_parent_id($parentid);
                $element['object']->set_sortorder($new_sortorder);
                $this->need_update[] = $element['object'];
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
    function get_neighbour_sortorder($element, $position, $array=null, $lastsortorder=null) {
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
        $result = null;

        $returnnextelement = false;
        $count = 0;

        foreach ($array as $key => $child) {
            $sortorder = $child['object']->get_sortorder();
            if ($returnnextelement) {
                return $sortorder;
            }

            if ($object->get_sortorder() == $sortorder) {
                if ($position == 'previous') {
                    if ($count > 0) {
                        return $lastsortorder;
                    }
                } elseif ($position == 'next') {
                    $returnnextelement = true;
                }
                continue;
            }

            $lastsortorder = $sortorder;

            if (!empty($child['children'])) {
                $result = $this->get_neighbour_sortorder($element, $position, $child['children'], $lastsortorder);
                if ($result) {
                    break;
                }
            }

            $count++;
        }
        return $result;
    }

    /**
     * Provided $this->fillers is ready, and given a $tree array and a grade_category or grade_item,
     * checks the fillers array to see if the current element needs to be included before the given
     * object, and includes it if needed, or appends the filler to the tree if no object is given.
     * The inserted filler is then deleted from the fillers array. The tree array is then returned.
     * @param array $tree
     * @param object $object Optional object before which to insert any fillers with a lower sortorder.
     *           If null, the current filler is appended to the tree.
     * @return array $tree
     */
    function include_fillers($tree, $object=NULL) {
        if (empty($this->fillers)) {
            return $tree;
        }

        // Look at the current key of the fillers array. It is a sortorder.
        if (empty($object) || key($this->fillers) < $object->sortorder) {
            $sortorder = key($this->fillers);
            $filler_object = current($this->fillers);

            // Remove filler so it doesn't get included again later
            unset($this->fillers[$sortorder]);

            $element = array();

            if (get_class($filler_object) == 'grade_category') {
                $children = $filler_object->get_children(1);
                unset($filler_object->children);

                $itemtree = array();

                foreach ($children as $element) {

                    if (!$this->include_grades or !$finals = grade_grades::fetch_all(array('itemid'=>$element['object']->id))) {
                        $finals = array();
                    }

                    $itemtree[$element['object']->sortorder] = array('object' => $element['object'], 'finalgrades' => $finals);
                }

                ksort($itemtree);
                $element['children'] = $itemtree;
            } elseif (get_class($filler_object) == 'grade_item' && $this->include_grades) {
                $final_grades = $filler_object->get_final();
                $element['final_grades'] = $final_grades;
            }

            $filler_object->sortorder = $sortorder;

            $element['object'] = $filler_object;
            $tree[$sortorder] = $element;
        }

        return $tree;
    }

    /**
     * Given an array of  grade_categories or a grade_items, guesses whether each needs to be added to the fillers
     * array or not (by checking children if a category, or checking parents if an item). It then
     * instantiates the objects if needed and adds them to the fillers array. The element is then
     * removed from the given array of objects, and the array is returned.
     * @param array $object array of stdClass objects or grade_categories or grade_items
     */
    function add_fillers($objects) {
        foreach ($objects as $key => $object) {

            if (get_class($object) == 'grade_item' || !empty($object->itemname)) {

                if (empty($object->categoryid)) {
                    $item = new grade_item($object);
                    $sortorder = $item->get_sortorder();
                    if (!empty($sortorder)) {
                        $this->fillers[$sortorder] = $item;
                    }
                }

            } elseif (get_class($object) == 'grade_category' || !empty($object->fullname)) {
                $topcatobject = new grade_category($object, false);

                if ($topcatobject->get_childrentype() == 'grade_item' && empty($topcatobject->parent)) {
                    $topcatobject->childrencount = $topcatobject->has_children();
                    $this->fillers[$object->sortorder] = $topcatobject;
                    unset($objects[$key]);
                }
            }
        }
        return $objects;
    }

    /**
     * Once the tree_array has been built, fills the $grades array by browsing through the tree
     * and adding each final grade that is found.
     * @return array $grades
     */
    function fill_grades($array = null) {
        if (empty($array)) {
            $array = $this->tree_array;
        }

        if (empty($array)) {
            return null;
        } else {
            foreach ($array as $level1order => $level1) {
                // If $level1 is a category, enter another recursive layer
                if ($this->get_element_type($level1) == 'topcat' || $this->get_element_type($level1) == 'subcat') {
                    $this->fill_grades($level1['children']);
                } else {
                    if (!empty($level1['finalgrades'])) {
                        foreach ($level1['finalgrades'] as $final_grade) {
                            $this->grades[$final_grade->userid][$final_grade->itemid] = $final_grade->finalgrade;
                        }
                    }
                }
            }

            reset($array);
            return true;
        }
    }


    /**
     * Static method that returns a sorted, nested array of all grade_categories and grade_items for
     * a given course, or for the entire site if no courseid is given. This method is not recursive
     * by design, because we want to limit the layers to 3, and because we want to avoid accessing
     * the DB with recursive methods.
     * @return array
     */
    function get_tree() {
        global $CFG;
        $tree = array();

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
        $grade_items = $this->add_fillers($grade_items);

        // Get all top categories
        $query = "SELECT $category_table.*, sortorder FROM $category_table, $items_table
                  WHERE iteminstance = $category_table.id AND itemtype = 'category' $catconstraint ORDER BY sortorder";

        $topcats = get_records_sql($query);

        if (empty($topcats)) {
            $topcats = $grade_items;
            $topcats[0] = new stdClass();
            $topcats[0]->sortorder = 0;
            $topcats[0]->courseid = $this->courseid;
        }

        // If any of these categories has grade_items as children, create a topcategory filler with colspan=count(children)
        $topcats = $this->add_fillers($topcats);

        foreach ($topcats as $topcatid => $topcat) {

            // Check the fillers array, see if one must be inserted before this topcat
            $tree = $this->include_fillers($tree, $topcat);

            $query = "SELECT $category_table.*, sortorder FROM $category_table, $items_table
                      WHERE iteminstance = $category_table.id AND parent = $topcatid $catconstraint ORDER BY sortorder";
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
                    if (!$this->include_grades or !$finals = grade_grades::fetch_all(array('itemid'=>$itemid))) {
                        $finals = array();
                    }

                    $sortorder = $item->sortorder;
                    $item = new grade_item($item);
                    $item->sortorder = $sortorder;

                    $itemtree[$item->sortorder] = array('object' => $item, 'finalgrades' => $finals);
                }

                ksort($itemtree);
                $sortorder = $subcat->sortorder;
                $subcat = new grade_category($subcat, false);
                $subcat->sortorder = $sortorder;
                $subcattree[$subcat->sortorder] = array('object' => $subcat, 'children' => $itemtree);
            }

            ksort($subcattree);
            $sortorder = $topcat->sortorder;
            $topcat = new grade_category($topcat, false);
            $topcat->sortorder = $sortorder;
            $tree[$topcat->sortorder] = array('object' => $topcat, 'children' => $subcattree);
        }

        // If there are still grade_items or grade_categories without a top category, add another filler
        if (!empty($this->fillers)) {
            ksort($this->fillers);
            foreach ($this->fillers as $sortorder => $object) {
                $tree = $this->include_fillers($tree);
            }
        }

        $db->debug = false;
        ksort($tree);
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
                    $finals = $object->get_final();
                } else {
                    $item_object = new grade_item($object, false);
                    $finals = $object->get_final();
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
                        $finals = $item->get_final();
                    } else {
                        $item_object = new grade_item($item, false);
                        if (method_exists($item, 'get_final')) {
                            $finals = $item->get_final();
                        }
                    }
                }

                $children_for_tree[$itemid] = array('object' => $item, 'finalgrades' => $finals);
            }

            if (empty($object->childrencount)) {
                $object->childrencount = 1;
            }

            $filler_array = array('object' => 'filler', 'colspan' => $object->childrencount, 'children' =>
                array(0 => array('object' => $object, 'children' => $children_for_tree)));
        }

        return $filler_array;
    }


    /**
     * Using $this->tree_array, builds $this->tree_filled, which is the same array but with fake categories as
     * fillers. These are used by display_grades, to print out empty cells over orphan grade_items and grade_categories.
     * Recursive method
     * @return boolean Success or Failure.
     */
    function build_tree_filled() {
        if (empty($this->tree_array)) {
            debugging("You cannot build the tree_filled array until the tree_array is filled.");
            return false;
        }

        $this->tree_filled = array();

        // Detect any category that is now child-less and delete it
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
        // Updates
        foreach ($this->need_update as $object) {
            if (!$object->update()) {
                debugging("Could not update the object in DB.");
            } elseif ($object->is_old_parent_childless()) {
                $this->need_delete[$object->old_parent->id] = $object->old_parent;
            }
        }

        // Deletions
        foreach ($this->need_delete as $id => $object) {
            // If an item is both in the delete AND insert arrays, it must be an existing object that only needs updating, so ignore it.
            if (empty($this->need_insert[$id])) {
                if (!$object->delete()) {
                    debugging("Could not delete object from DB.");
                }
            }
        }

        // Insertions
        foreach ($this->need_insert as $id => $object) {
            if (empty($this->need_delete[$id])) {
                if (!$object->insert()) {
                    debugging("Could not insert object into DB.");
                }
            }
        }

        $this->need_update = array();
        $this->need_delete = array();
        $this->need_insert = array();

        $this->reset_first_sortorder();
        $this->renumber();
    }

}

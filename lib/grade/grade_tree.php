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
            $this->commonvars = "&amp;sesskey=$USER->sesskey&amp;courseid=$this->courseid";

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
                    $finals = array();

                    if ($this->include_grades) {
                        $final = new grade_grades();
                        $final->itemid = $element['object']->id;
                        $finals = $final->fetch_all_using_this();
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
                    $finals = array();

                    if ($this->include_grades) {
                        $final = new grade_grades();
                        $final->itemid = $itemid;
                        $finals = $final->fetch_all_using_this();
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
        global $CFG;

        // 1. Fetch all top-level categories for this course, with all children preloaded, sorted by sortorder
        $tree = $this->tree_filled;
        $this->fill_grades();

        if (empty($this->tree_filled)) {
            debugging("The tree_filled array wasn't initialised, grade_tree could not display the grades correctly.");
            return false;
        }

        // Fetch array of students enroled in this course
        if (!$context = get_context_instance(CONTEXT_COURSE, $this->courseid)) {
            return false;
        }

        $users = get_role_users(@implode(',', $CFG->gradebookroles), $context);

        $topcathtml = '<tr><td class="filler">&nbsp;</td>';
        $cathtml    = '<tr><td class="filler">&nbsp;</td>';
        $itemhtml   = '<tr><td class="filler">&nbsp;</td>';
        $items = array();

        foreach ($tree as $topcat) {
            $itemcount = 0;

            foreach ($topcat['children'] as $catkey => $cat) {
                $catitemcount = 0;

                foreach ($cat['children'] as $item) {
                    $itemcount++;
                    $catitemcount++;
                    $itemhtml .= '<td>' . $item['object']->itemname . '</td>';
                    $items[] = $item;
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

        $studentshtml = '';

        foreach ($users as $userid => $user) {
            $studentshtml .= '<tr><th>' . $user->firstname . ' ' . $user->lastname . '</th>';
            foreach ($items as $item) {
                if (!empty($this->grades[$userid][$item['object']->id])) {
                    $studentshtml .= '<td>' . $this->grades[$userid][$item['object']->id] . '</td>' . "\n";
                } else {
                    $studentshtml .= '<td>0</td>' . "\n";
                }
            }
            $studentshtml .= '</tr>';
        }

        $itemhtml   .= '</tr>';
        $cathtml    .= '</tr>';
        $topcathtml .= '</tr>';

        $reporthtml = "<table style=\"text-align: center\" border=\"1\">$topcathtml$cathtml$itemhtml";
        $reporthtml .= $studentshtml;
        $reporthtml .= "</table>";
        return $reporthtml;

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

    /**
     * Returns a HTML list with sorting arrows and insert boxes. This is a recursive method.
     * @param int $level The level of recursion
     * @param array $elements The elements to display in a list. Defaults to this->tree_array
     * @param int $source_sortorder A source sortorder, given when an element needs to be moved or inserted.
     * @param string $action 'move' or 'insert'
     * @param string $source_type 'topcat', 'subcat' or 'item'
     * @return string HTML code
     */
    function get_edit_tree($level=1, $elements=NULL, $source_sortorder=NULL, $action=NULL, $source_type=NULL) {
        if (empty($this->tree_array)) {
            return null;
        } else {
            global $CFG;
            global $USER;

            $strmove           = get_string("move");
            $strmoveup         = get_string("moveup");
            $strmovedown       = get_string("movedown");
            $strmovehere       = get_string("movehere");
            $strcancel         = get_string("cancel");
            $stredit           = get_string("edit");
            $strdelete         = get_string("delete");
            $strhide           = get_string("hide");
            $strshow           = get_string("show");
            $strlock           = get_string("lock", 'grades');
            $strunlock         = get_string("unlock", 'grades');
            $strnewcategory    = get_string("newcategory", 'grades');
            $strcategoryname   = get_string("categoryname", 'grades');
            $strcreatecategory = get_string("createcategory", 'grades');
            $strsubcategory    = get_string("subcategory", 'grades');
            $stritems          = get_string("items", 'grades');
            $strcategories     = get_string("categories", 'grades');

            $list = '';
            $closing_form_tags = '';

            if (empty($elements)) {
                $list .= '<form action="category.php" method="post">' . "\n";
                $list .= '<ul id="grade_edit_tree">' . "\n";
                $elements = $this->tree_array;

                $element_type_options = '<select name="element_type">' . "\n";
                $element_type_options .= "<option value=\"items\">$stritems</option><option value=\"categories\">$strcategories</option>\n";
                $element_type_options .= "</select>\n";

                $strforelementtypes= get_string("forelementtypes", 'grades', $element_type_options);

                $closing_form_tags .= '<fieldset><legend>' . $strnewcategory . '</legend>' . "\n";
                $closing_form_tags .= '<input type="hidden" name="sesskey" value="' . $USER->sesskey . '" />' . "\n";
                $closing_form_tags .= '<input type="hidden" name="courseid" value="' . $this->courseid . '" />' . "\n";
                $closing_form_tags .= '<input type="hidden" name="action" value="create" />' . "\n";
                $closing_form_tags .= '<label for="category_name">' . $strcategoryname . '</label>' . "\n";
                $closing_form_tags .= '<input id="category_name" type="text" name="category_name" size="40" />' . "\n";
                $closing_form_tags .= '<input type="submit" value="' . $strcreatecategory . '" />' . "\n";
                $closing_form_tags .= $strforelementtypes;
                $closing_form_tags .= '</fieldset>' . "\n";
                $closing_form_tags .= "</form>\n";
            } else {
                $list = '<ul class="level' . $level . 'children">' . "\n";
            }

            $first = true;
            $count = 1;
            $last = false;
            $last_sortorder = null;

            if (count($elements) == 1) {
                $last = true;
            }

            foreach ($elements as $sortorder => $element) {
                $object = $element['object'];

                $object_name = $object->get_name();
                $object_class = get_class($object);
                $object_parent = $object->get_parent_id();
                $element_type = $this->get_element_type($element);

                $highlight_class = '';

                if ($source_sortorder == $sortorder && !empty($action)) {
                    $highlight_class = ' selected_element ';
                }

                // Prepare item icon if appropriate
                $module_icon = '';
                if (!empty($object->itemmodule)) {
                    $module_icon = '<div class="moduleicon">'
                        . '<label for="checkbox_select_' . $sortorder . '">'
                        . '<img src="'
                        . $CFG->modpixpath . '/' . $object->itemmodule . '/icon.gif" alt="'
                        . $object->itemmodule . '" title="' . $object->itemmodule . '" /></label></div>';
                }

                // Add dimmed_text span around object name if set to hidden
                $hide_show = 'hide';
                if ($object->get_hidden()) {
                    $object_name = '<span class="dimmed_text">' . $object_name . '</span>';
                    $hide_show = 'show';
                }

                // Prepare lock/unlock string
                $lock_unlock = 'lock';
                if ($object->is_locked()) {
                    $lock_unlock = 'unlock';
                }

                // Prepare select checkbox for subcats and items
                $select_checkbox = '';
                if ($element_type != 'topcat') {
                    $group = 'items';
                    if ($element_type == 'subcat') {
                        $group = 'categories';
                    }

                    $select_checkbox = '<div class="select_checkbox">' . "\n"
                        . '<input id="checkbox_select_' . $sortorder . '" type="checkbox" name="' . $group . '[' . $sortorder . ']" />' . "\n"
                        . '</div>' . "\n";

                    // Add a label around the object name to trigger the checkbox
                    $object_name = '<label for="checkbox_select_' . $sortorder . '">' . $object_name . '</label>';
                }

                $list .= '<li class="level' . $level . 'element sortorder'
                      . $object->get_sortorder() . $highlight_class . '">' . "\n"
                      . $select_checkbox . $module_icon . $object_name;


                $list .= '<div class="icons">' . "\n";

                // Print up arrow
                if (!$first) {
                    $list .= '<a href="category.php?'."source=$sortorder&amp;moveup={$object->previous_sortorder}$this->commonvars\">\n";
                    $list .= '<img src="'.$CFG->pixpath.'/t/up.gif" class="iconsmall" ' . 'alt="'.$strmoveup.'" title="'.$strmoveup.'" /></a>'. "\n";
                } else {
                    $list .= '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" class="iconsmall" alt="" /> '. "\n";
                }

                // Print down arrow
                if (!$last) {
                    $list .= '<a href="category.php?'."source=$sortorder&amp;movedown={$object->next_sortorder}$this->commonvars\">\n";
                    $list .= '<img src="'.$CFG->pixpath.'/t/down.gif" class="iconsmall" ' . 'alt="'.$strmovedown.'" title="'.$strmovedown.'" /></a>'. "\n";
                } else {
                    $list .= '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" class="iconsmall" alt="" /> ' . "\n";
                }

                // Print move icon
                if ($element_type != 'topcat') {
                    $list .= '<a href="category.php?'."source=$sortorder&amp;action=move&amp;type=$element_type$this->commonvars\">\n";
                    $list .= '<img src="'.$CFG->pixpath.'/t/move.gif" class="iconsmall" alt="'.$strmove.'" title="'.$strmove.'" /></a>'. "\n";
                } else {
                    $list .= '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" class="iconsmall" alt="" /> ' . "\n";
                }

                // Print edit icon
                $list .= '<a href="category.php?'."target=$sortorder&amp;action=edit$this->commonvars\">\n";
                $list .= '<img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'
                      .$stredit.'" title="'.$stredit.'" /></a>'. "\n";

                // Print delete icon
                $list .= '<a href="category.php?'."target=$sortorder&amp;action=delete$this->commonvars\">\n";
                $list .= '<img src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" alt="'
                      .$strdelete.'" title="'.$strdelete.'" /></a>'. "\n";

                // Print hide/show icon
                $list .= '<a href="category.php?'."target=$sortorder&amp;action=$hide_show$this->commonvars\">\n";
                $list .= '<img src="'.$CFG->pixpath.'/t/'.$hide_show.'.gif" class="iconsmall" alt="'
                      .${'str' . $hide_show}.'" title="'.${'str' . $hide_show}.'" /></a>'. "\n";
                // Print lock/unlock icon
                $list .= '<a href="category.php?'."target=$sortorder&amp;action=$lock_unlock$this->commonvars\">\n";
                $list .= '<img src="'.$CFG->pixpath.'/t/'.$lock_unlock.'.gif" class="iconsmall" alt="'
                      .${'str' . $lock_unlock}.'" title="'.${'str' . $lock_unlock}.'" /></a>'. "\n";

                $list .= '</div> <!-- end icons div -->';

                if (!empty($element['children'])) {
                    $list .= $this->get_edit_tree($level + 1, $element['children'], $source_sortorder, $action, $source_type);
                }

                $list .= '</li>' . "\n";

                $first = false;
                $count++;
                if ($count == count($elements)) {
                    $last = true;
                }

                $last_sortorder = $sortorder;
            }

            // Add an insertion box if source_sortorder is given and a few other constraints are satisfied
            if ($source_sortorder && !empty($action)) {
                $moving_item_near_subcat = $element_type == 'subcat' && $source_type == 'item' && $level > 1;
                $moving_cat_to_lower_level = ($level == 2 && $source_type == 'topcat') || ($level > 2 && $source_type == 'subcat');
                $moving_subcat_near_item_in_cat = $element_type == 'item' && $source_type == 'subcat' && $level > 1;
                $moving_element_near_itself = $sortorder == $source_sortorder;

                if (!$moving_item_near_subcat && !$moving_cat_to_lower_level && !$moving_subcat_near_item_in_cat && !$moving_element_near_itself) {
                    $list .= '<li class="insertion">' . "\n";
                    $list .= '<a href="category.php?' . "source=$source_sortorder&amp;$action=$last_sortorder$this->commonvars\">\n";
                    $list .= '<img class="movetarget" src="'.$CFG->wwwroot.'/pix/movehere.gif" alt="'.$strmovehere.'" title="'.$strmovehere.'" />' . "\n";
                    $list .= "</a>\n</li>";
                }
            }

            $list .= '</ul>' . "\n$closing_form_tags";

            return $list;
        }

        return false;
    }
}

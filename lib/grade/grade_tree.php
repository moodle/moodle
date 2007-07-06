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
     * @var object $top_element
     */
    var $top_element;

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
     * @param boolean $fillers include fillers and colspans, make the levels var "rectangular"
     * @param boolean $include_grades
     * @param boolean $category_grade_last category grade item is the last child
     * @param boolean $aggregation_view Either full view (0) or compact view (1)
     */
    function grade_tree($courseid, $fillers=true, $include_grades=false, $category_grade_last=true, 
                        $aggregation_view=GRADER_REPORT_AGGREGATION_VIEW_FULL) {
        global $USER;

        $this->courseid = $courseid;
        $this->include_grades = $include_grades;
        $this->commonvars = "&amp;sesskey=$USER->sesskey&amp;id=$this->courseid";
        $this->levels = array();

        // get course grade tree
        $this->top_element =& grade_category::fetch_course_tree($courseid, $include_grades, true);

        if ($category_grade_last) {
            grade_tree::category_grade_last($this->top_element);
        }

        if ($fillers) {
            // inject fake categories == fillers
            grade_tree::inject_fillers($this->top_element, 0);
            // add colspans to categories and fillers
            grade_tree::inject_colspans($this->top_element);
        }

        grade_tree::fill_levels($this->levels, $this->top_element, 0);
    }


    /**
     * Static recursive helper - makes the grade_item for category the last children
     * @static
     * @param array $element The seed of the recursion
     * @return void
     */
    function category_grade_last(&$element) {
        if (empty($element['children'])) {
            return;
        }
        if (count($element['children']) < 2) {
            return;
        }
        $category_item = reset($element['children']);
        $order = key($element['children']);
        unset($element['children'][$order]);
        $element['children'][$order] =& $category_item;
        foreach ($element['children'] as $sortorder=>$child) {
            grade_tree::category_grade_last($element['children'][$sortorder]);
        }
    }

    /**
     * Static recursive helper - fills the levels array, useful when accessing tree elements of one level
     * @static
     * @param int $levels
     * @param array $element The seed of the recursion
     * @param int $depth
     * @return void
     */
    function fill_levels(&$levels, &$element, $depth) {
        if (!array_key_exists($depth, $levels)) {
            $levels[$depth] = array();
        }

        // prepare unique identifier
        if ($element['type'] == 'category') {
            $element['eid'] = 'c'.$element['object']->id;
        } else if (in_array($element['type'], array('item', 'courseitem', 'categoryitem'))) {
            $element['eid'] = 'i'.$element['object']->id;
        }

        $levels[$depth][] =& $element;
        $depth++;
        if (empty($element['children'])) {
            return;
        }
        $prev = 0;
        foreach ($element['children'] as $sortorder=>$child) {
            grade_tree::fill_levels($levels, $element['children'][$sortorder], $depth);
            $element['children'][$sortorder]['prev'] = $prev;
            $element['children'][$sortorder]['next'] = 0;
            if ($prev) {
                $element['children'][$prev]['next'] = $sortorder;
            }
            $prev = $sortorder;
        }
    }

    /**
     * Static recursive helper - makes full tree (all leafes are at the same level)
     */
    function inject_fillers(&$element, $depth) {
        $depth++;

        if (empty($element['children'])) {
            return $depth;
        }
        $chdepths = array();
        $chids = array_keys($element['children']);
        $last_child  = end($chids);
        $first_child = reset($chids);

        foreach ($chids as $chid) {
            $chdepths[$chid] = grade_tree::inject_fillers($element['children'][$chid], $depth);
        }
        arsort($chdepths);

        $maxdepth = reset($chdepths);
        foreach ($chdepths as $chid=>$chd) {
            if ($chd == $maxdepth) {
                continue;
            }
            for ($i=0; $i < $maxdepth-$chd; $i++) {
                if ($chid == $first_child) {
                    $type = 'fillerfirst';
                } else if ($chid == $last_child) {
                    $type = 'fillerlast';
                } else {
                    $type = 'filler';
                }
                $oldchild =& $element['children'][$chid];
                $element['children'][$chid] = array('object'=>'filler', 'type'=>$type, 'eid'=>'', 'depth'=>$element['object']->depth,'children'=>array($oldchild));
            }
        }

        return $maxdepth;
    }

    /**
     * Static recursive helper - add colspan information into categories
     */
    function inject_colspans(&$element) {
        if (empty($element['children'])) {
            return 1;
        }
        $count = 0;
        foreach ($element['children'] as $key=>$child) {
            $count += grade_tree::inject_colspans($element['children'][$key]);
        }
        $element['colspan'] = $count;
        return $count;
    }

    /**
     * Parses the array in search of a given eid and returns a element object with
     * information about the element it has found.
     * @param int $eid
     * @return object element
     */
    function locate_element($eid) {
        if (strpos($eid, 'g') === 0) {
            // it is a grade  construct a new object
            $id = (int)substr($eid, 1);
            if (!$grade = grade_grades::fetch(array('id'=>$id))) {
                return null;
            }
            //extra security check - the grade item must be in this tree
            if (!$item_el = $this->locate_element('i'.$grade->itemid)) {
                return null;
            }
            $grade->grade_item =& $item_el['object']; // this may speedup grade_grades methods!
            return array('eid'=>'g'.$id,'object'=>$grade, 'type'=>'grade');
        }

        // it is a category or item
        foreach ($this->levels as $row) {
            foreach ($row as $element) {
                if ($element['type'] == 'filler') {
                    continue;
                }
                if ($element['eid'] == $eid) {
                    return $element;
                }
            }
        }

        return null;
    }

}

<?php // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 2001-2007  Martin Dougiamas  http://dougiamas.com       //
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
set_time_limit(0);
require_once '../../../config.php';
require_once $CFG->libdir . '/grade/grade_tree.php';
require_once $CFG->libdir . '/gradelib.php';
print_header('Edit categories');

$param = new stdClass();
$param->courseid     = optional_param('courseid', 0 , PARAM_INT);
$param->moveup       = optional_param('moveup', 0, PARAM_INT);
$param->movedown     = optional_param('movedown', 0, PARAM_INT);
$param->source       = optional_param('source', 0, PARAM_INT);
$param->action       = optional_param('action', 0, PARAM_ALPHA);
$param->move         = optional_param('move', 0, PARAM_INT);
$param->type         = optional_param('type', 0, PARAM_ALPHA);
$param->target       = optional_param('target', 0, PARAM_INT);
$param->confirm      = optional_param('confirm', 0, PARAM_INT);
$param->items        = optional_param('items', 0, PARAM_INT);
$param->categories   = optional_param('categories', 0, PARAM_INT);
$param->element_type = optional_param('element_type', 0, PARAM_ALPHA);
$param->category_name= optional_param('category_name', 0, PARAM_ALPHA);

$tree = new grade_tree($param->courseid);
$select_source = false;

if (!empty($param->action) && !empty($param->source) && confirm_sesskey()) {
    $element = $tree->locate_element($param->source);
    $element_name = $element->element['object']->get_name();
    
    $strselectdestination = get_string('selectdestination', 'grades', $element_name);
    $strmovingelement     = get_string('movingelement', 'grades', $element_name);
    $strcancel            = get_string('cancel');

    print_heading($strselectdestination); 

    echo $strmovingelement . ' (<a href="category.php?cancelmove=true' . $tree->commonvars . '">' . $strcancel . '</a>)' . "\n";
} elseif (!empty($param->source) && confirm_sesskey()) {
    if (!empty($param->moveup)) {
        $tree->move_element($param->source, $param->moveup); 
    } elseif(!empty($param->movedown)) {
        $tree->move_element($param->source, $param->movedown, 'after');
    } elseif(!empty($param->move)) {
        $tree->move_element($param->source, $param->move, 'after');
    }
    
    $tree->renumber();
    $tree->update_db();
} elseif (!empty($param->target) && !empty($param->action) && confirm_sesskey()) {
    $element = $tree->locate_element($param->target);
    switch ($param->action) {
        case 'edit':
            break;
        case 'delete':
            if ($param->confirm == 1) { 
                $tree->remove_element($param->target);
                $tree->renumber();
                $tree->update_db();
                // Print result message
                
            } else {
                // Print confirmation dialog
                $strdeletecheckfull = get_string('deletecheck', '', $element->element['object']->get_name());
                $linkyes = "category.php?target=$param->target&amp;action=delete&amp;confirm=1$tree->commonvars";
                $linkno = "category.php?$tree->commonvars";
                notice_yesno($strdeletecheckfull, $linkyes, $linkno);
            }
            break;
        
        case 'hide':
        // TODO Implement calendar for selection of a date to hide element until
            if (!$element->element['object']->set_hidden(1)) {
                debugging("Could not update the element's hidden state!");
            } else {
                $tree = new grade_tree($param->courseid);
            }
            break;
        case 'show':
            if (!$element->element['object']->set_hidden(0)) {
                debugging("Could not update the element's hidden state!");
            } else {
                $tree = new grade_tree($param->courseid);
            }
            break;
        case 'lock':
        // TODO Implement calendar for selection of a date to lock element after
            if (!$element->element['object']->set_locked(1)) {
                debugging("Could not update the element's locked state!");
            } else {
                $tree = new grade_tree($param->courseid);
            }
            break;
        case 'unlock':
            if (!$element->element['object']->set_locked(0)) {
                debugging("Could not update the element's locked state!");
            } else {
                $tree = new grade_tree($param->courseid);
            }
            break;
        default:
            break;
    }
    unset($param->target);
} elseif (!empty($param->element_type) && !empty($param->action) && $param->action == 'create' && confirm_sesskey() && !empty($param->category_name)) {
    if ($param->element_type == 'items') {
        if (!empty($param->items)) {
            $category = new grade_category();
            $category->fullname = $param->category_name;
            $category->courseid = $tree->courseid;
            $items = array();
            foreach ($param->items as $sortorder => $useless_var) {
                $element = $tree->locate_element($sortorder);
                $items[$element->element['object']->id] = $element->element['object'];
            }
            print_object($items);
            /*
            if ($category->set_as_parent($items) && $category->update()) {
                $tree = new grade_tree($param->courseid);
            } else { // creation of category didn't work, print a message
                debugging("Could not create a parent category over the items you selected..");
            }
            */

        } else { // No items selected. Can't create a category over them...
            notice("Couldn't create a category, you did not select any grade items."); 
        }
    } elseif ($param->element_type == 'categories') {
        if (!empty($param->categories)) {
            $category = new grade_category();
            $category->fullname = $param->category_name;
            $category->courseid = $tree->courseid;
            $categories = array();
            foreach ($param->categories as $sortorder => $useless_var) {
                $element = $tree->locate_element($sortorder);
                $categories[$element->element['object']->id] = $element->element['object'];
            }

            if ($category->set_as_parent($categories) && $category->update()) {
                $tree = new grade_tree($param->courseid);
            } else { // creation of category didn't work, print a message
                debugging("Could not create a parent category over the categories you selected..");
            }

        } else { // No categories selected. Can't create a category over them...
            notice("Couldn't create a category, you did not select any sub-categories.");
        }

    } else { // The element_type wasn't set properly, throw up an error

    }
}

echo $tree->get_edit_tree(1, null, $param->source, $param->action, $param->type);

print_footer();
?>

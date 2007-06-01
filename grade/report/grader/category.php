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
$param->courseid = optional_param('courseid', 0 , PARAM_INT);
$param->moveup   = optional_param('moveup', 0, PARAM_INT);
$param->movedown = optional_param('movedown', 0, PARAM_INT);
$param->source   = optional_param('source', 0, PARAM_INT);
$param->action   = optional_param('action', 0, PARAM_ALPHA);
$param->move     = optional_param('move', 0, PARAM_INT);
$param->type     = optional_param('type', 0, PARAM_ALPHA);
$param->target   = optional_param('target', 0, PARAM_INT);
$param->confirm  = optional_param('confirm', 0, PARAM_INT);

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
                print_simple_box_start('center', '60%', '#FFAAAA', 20, 'noticebox');
                print_heading($strdeletecheckfull);
                include_once('category_delete.html');
                print_simple_box_end(); 
                exit();
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
}

echo $tree->get_edit_tree(1, null, $param->source, $param->action, $param->type);

print_footer();
?>

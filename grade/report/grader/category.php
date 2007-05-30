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
$param->moveup = optional_param('moveup', 0, PARAM_INT);
$param->movedown = optional_param('movedown', 0, PARAM_INT);
$param->source = optional_param('source', 0, PARAM_INT);
$param->action = optional_param('action', 0, PARAM_ALPHA);
$param->move = optional_param('move', 0, PARAM_INT);

$tree = new grade_tree(641);
$select_source = false;

if (!empty($param->action)) {
    if (empty($param->source)) {
        $select_source = true;
    } else {
        print_heading("Select the destination for the selected element."); 
    }
} elseif (!empty($param->source)) {
    if (!empty($param->moveup)) {
        $tree->move_element($param->source, $param->moveup); 
    } elseif(!empty($param->movedown)) {
        $tree->move_element($param->source, $param->movedown, 'after');
    } elseif(!empty($param->move)) {
        $tree->move_element($param->source, $param->move, 'after');
    }
    
    $tree->renumber();
    $tree->update_db();
} 

if ($select_source) {
    print_heading("Select an element to move");
}

echo $tree->get_edit_tree($select_source, 1, null, $param->source, $param->action);

echo '<form id="move_button" action="category.php" method="post"><div>' . "\n";
echo '<input type="hidden" name="action" value="move" />' . "\n";
echo '<input type="submit" value="Move" />' . "\n";
echo '</div></form>';

print_footer();
?>

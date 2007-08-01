<?php  // $Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
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

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';

$courseid = required_param('id', PARAM_INT);
$action   = optional_param('action', 0, PARAM_ALPHA);
$eid      = optional_param('eid', 0, PARAM_ALPHANUM);


/// Make sure they can even access this course

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/grade:manage', $context);

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'tree', 'courseid'=>$courseid));
$returnurl = $gpr->get_return_url(null);

//first make sure we have proper final grades - we need it for locking changes
grade_regrade_final_grades($courseid);

// get the grading tree object
// note: total must be first for moving to work correctly, if you want it last moving code must be rewritten!
$gtree = new grade_tree($courseid, false, false);

if (empty($eid)) {
    $element = null;
    $object  = null;

} else {
    if (!$element = $gtree->locate_element($eid)) {
        error('Incorrect element id!', $returnurl);
    }
    $object = $element['object'];
}


$strgrades         = get_string('grades');
$strgraderreport   = get_string('graderreport', 'grades');
$strcategoriesedit = get_string('categoriesedit', 'grades');

$nav = array(array('name'=>$strgrades,'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
             array('name'=>$strcategoriesedit, 'link'=>'', 'type'=>'misc'));

$navigation = build_navigation($nav);
$moving = false;

switch ($action) {
    case 'delete':
        if ($eid) {
            $confirm = optional_param('confirm', 0, PARAM_BOOL);

            if ($confirm and confirm_sesskey()) {
                $object->delete('grade/report/grader/category');
                redirect($returnurl);

            } else {
                print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strcategoriesedit, $navigation, '', '', true, '', navmenu($course));
                $strdeletecheckfull = get_string('deletecheck', '', $object->get_name());
                $optionsyes = array('eid'=>$eid, 'confirm'=>1, 'sesskey'=>sesskey(), 'id'=>$course->id, 'action'=>'delete');
                $optionsno  = array('id'=>$course->id);
                notice_yesno($strdeletecheckfull, 'tree.php', 'tree.php', $optionsyes, $optionsno, 'post', 'get');
                print_footer($course);
                die;
            }
        }
        break;

    case 'autosort':
        //TODO: implement autosorting based on order of mods on course page, categories first, manual items last
        break;

    case 'synclegacy':
        grade_grab_legacy_grades($course->id);
        redirect($returnurl);

    case 'move':
        if ($eid and confirm_sesskey()) {
            $moveafter = required_param('moveafter', PARAM_ALPHANUM);
            if(!$after_el = $gtree->locate_element($moveafter)) {
                error('Incorect element id in moveafter', $returnurl);
            }
            $after = $after_el['object'];
            $parent = $after->get_parent_category();
            $sortorder = $after->get_sortorder();

            $object->set_parent($parent->id);
            $object->move_after_sortorder($sortorder);

            redirect($returnurl);
        }
        break;

    case 'moveselect':
        if ($eid and confirm_sesskey()) {
            $moving = $eid;
        }
        break;

    default:
        break;
}

print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strcategoriesedit, $navigation, '', '', true, '', navmenu($course));

/// Print the plugin selector at the top
print_grade_plugin_selector($courseid, 'edit', 'tree');

print_heading(get_string('categoriesedit', 'grades'));


print_box_start('gradetreebox generalbox');
echo '<ul id="grade_tree">';
print_grade_tree($gtree, $gtree->top_element, $moving, $gpr);
echo '</ul>';
print_box_end();

echo '<div class="buttons">';
if ($moving) {
    print_single_button('tree.php', array('id'=>$course->id), get_string('cancel'), 'get');
} else {
    print_single_button('category.php', array('courseid'=>$course->id), get_string('addcategory', 'grades'), 'get');
    print_single_button('item.php', array('courseid'=>$course->id), get_string('additem', 'grades'), 'get');
    if (!empty($CFG->enableoutcomes)) {
        print_single_button('outcomeitem.php', array('courseid'=>$course->id), get_string('addoutcomeitem', 'grades'), 'get');
    }
    //print_single_button('index.php', array('id'=>$course->id, 'action'=>'autosort'), get_string('autosort', 'grades'), 'get');
    print_single_button('index.php', array('id'=>$course->id, 'action'=>'synclegacy'), get_string('synclegacygrades', 'grades'), 'get');
}
echo '</div>';
print_footer($course);
die;




function print_grade_tree(&$gtree, $element, $moving, &$gpr) {
    global $CFG, $COURSE;

/// fetch needed strings
    $strmove     = get_string('move');
    $strmovehere = get_string('movehere');
    $strdelete   = get_string('delete');

    $object = $element['object'];
    $eid    = $element['eid'];

/// prepare actions
    $actions = $gtree->get_edit_icon($element, $gpr);

    if ($element['type'] == 'item' or ($element['type'] == 'category' and $element['depth'] > 1)) {
        $actions .= '<a href="tree.php?id='.$COURSE->id.'&amp;action=delete&amp;eid='.$eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" alt="'.$strdelete.'" title="'.$strdelete.'"/></a>';
        $actions .= '<a href="tree.php?id='.$COURSE->id.'&amp;action=moveselect&amp;eid='.$eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/move.gif" class="iconsmall" alt="'.$strmove.'" title="'.$strmove.'"/></a>';
    }

    $actions .= $gtree->get_locking_icon($element, $gpr);

    $name = $object->get_name();

    //TODO: improve outcome visulisation
    if ($element['type'] == 'item' and !empty($object->outcomeid)) {
        $name = $name.' ('.get_string('outcome', 'grades').')';
    }

    if ($object->is_hidden()) {
        $name = '<span class="dimmed_text">'.$name.'</span>';
    }
    $actions .= $gtree->get_hiding_icon($element, $gpr);

/// prepare icon
    $icon = '<img src="'.$CFG->wwwroot.'/pix/spacer.gif" class="icon" alt=""/>';
    switch ($element['type']) {
        case 'item':
            if ($object->itemtype == 'mod') {
                $icon = '<img src="'.$CFG->modpixpath.'/'.$object->itemmodule.'/icon.gif" class="icon" alt="'.get_string('modulename', $object->itemmodule).'"/>';
            } else if ($object->itemtype == 'manual') {
                //TODO: add manual grading icon
                $icon = '<img src="'.$CFG->pixpath.'/t/edit.gif" class="icon" alt="'.get_string('manualgrade', 'grades').'"/>'; // TODO: localize
            }
            break;
        case 'courseitem':
        case 'categoryitem':
            $icon = '<img src="'.$CFG->pixpath.'/i/category_grade.gif" class="icon" alt="'.get_string('categorygrade').'"/>'; // TODO: localize
            break;
        case 'category':
            $icon = '<img src="'.$CFG->pixpath.'/f/folder.gif" class="icon" alt="'.get_string('category').'"/>';
            break;
    }

/// prepare move target if needed
    $moveto = '';
    if ($moving) {
        $actions = ''; // no action icons when moving
        $moveto = '<li><a href="tree.php?id='.$COURSE->id.'&amp;action=move&amp;eid='.$moving.'&amp;moveafter='.$eid.'&amp;sesskey='.sesskey().'"><img class="movetarget" src="'.$CFG->wwwroot.'/pix/movehere.gif" alt="'.$strmovehere.'" title="'.$strmovehere.'" /></a></li>';
    }

/// print the list items now
    if ($moving == $eid) {
        // do not diplay children
        echo '<li class="'.$element['type'].' moving">'.$icon.$name.'('.get_string('move').')</li>';

    } else if ($element['type'] != 'category') {
        echo '<li class="'.$element['type'].'">'.$icon.$name.$actions.'</li>'.$moveto;

    } else {
        echo '<li class="'.$element['type'].'">'.$icon.$name.$actions;
        echo '<ul class="catlevel'.$element['depth'].'">';
        foreach($element['children'] as $child_el) {
            print_grade_tree($gtree, $child_el, $moving, $gpr);
        }
        echo '</ul></li>';
        if ($element['depth'] > 1) {
            echo $moveto; // can not move after the top category
        }
    }
}


?>

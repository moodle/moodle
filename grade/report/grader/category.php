<?php
// $Id$

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

require_once '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';

$courseid = required_param('id', PARAM_INT);
$action   = optional_param('action', 0, PARAM_ALPHA);
$eid      = optional_param('eid', 0, PARAM_ALPHANUM);


/// Make sure they can even access this course

if (!$course = get_record('course', 'id', $courseid)) {
	print_error('nocourseid');
}

require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $course->id);
//require_capability() here!!

// default return url
$returnurl = 'category.php?id='.$course->id;

// get the grading tree object
// note: total must be first for moving to work correctly, if you want it last moving code must be rewritten!
$gtree = new grade_tree($courseid, false, false, false);

if (empty($eid)) {
    $element = null;
    $object  = null;

} else {
    if (!$element = $gtree->locate_element($eid)) {
        error('Incorrect element id!', $returnurl);
    }
    $object  = $element['object'];
}


$strgrades         = get_string('grades');
$strgraderreport   = get_string('graderreport', 'grades');
$strcategoriesedit = get_string('categoriesedit', 'grades');

$nav = array(array('name'=>$strgrades,'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
             array('name'=>$strgraderreport, 'link'=>$CFG->wwwroot.'/grade/report.php?id='.$courseid.'&amp;report=grader', 'type'=>'misc'),
             array('name'=>$strcategoriesedit, 'link'=>'', 'type'=>'misc'));

$navigation = build_navigation($nav);
$moving = false;

switch ($action) {
    case 'edit':
        if ($eid and confirm_sesskey()) {
            if ($element['type'] == 'category') {
                redirect('edit_category.php?courseid='.$course->id.'&amp;id='.$object->id);
            } else {
                redirect('edit_item.php?courseid='.$course->id.'&amp;id='.$object->id);
            }
        }
        break;

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
                notice_yesno($strdeletecheckfull, 'category.php', 'category.php', $optionsyes, $optionsno, 'post', 'get');
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

    case 'hide':
        if ($eid and confirm_sesskey()) {
            $object->set_hidden(1);
            redirect($returnurl);
        }
        break;

    case 'show':
        if ($eid and confirm_sesskey()) {
            $object->set_hidden(0);
            redirect($returnurl);
        }
        break;

    case 'lock':
        if ($eid and confirm_sesskey()) {
            //TODO: add error handling in redirect
            $object->set_locked(1);
            redirect($returnurl);
        }
        break;

    case 'unlock':
        if ($eid and confirm_sesskey()) {
            $object->set_locked(0);
            redirect($returnurl);
        }
        break;

    default:
        break;
}

print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strcategoriesedit, $navigation, '', '', true, '', navmenu($course));

print_heading(get_string('categoriesedit', 'grades'));

// Add tabs
$currenttab = 'editcategory';
include('tabs.php');

print_box_start('gradetreebox generalbox');
echo '<ul id="grade_tree">';
print_grade_tree($gtree->top_element, $moving);
echo '</ul>';
print_box_end();

echo '<div class="buttons">';
if ($moving) {
    print_single_button('category.php', array('id'=>$course->id), get_string('cancel'), 'get');
} else {
    print_single_button('edit_category.php', array('courseid'=>$course->id), get_string('addcategory', 'grades'), 'get');
    print_single_button('edit_item.php', array('courseid'=>$course->id), get_string('additem', 'grades'), 'get'); // TODO: localize
    print_single_button('category.php', array('id'=>$course->id, 'action'=>'autosort'), get_string('autosort', 'grades'), 'get'); //TODO: localize
    print_single_button('category.php', array('id'=>$course->id, 'action'=>'synclegacy'), get_string('synclegacygrades', 'grades'), 'get'); //TODO: localize
}
echo '</div>';
print_footer($course);
die;




function print_grade_tree($element, $moving) {
    global $CFG, $COURSE;

/// fetch needed strings
    $strmove     = get_string('move');
    $strmovehere = get_string('movehere');
    $stredit     = get_string('edit');
    $strdelete   = get_string('delete');
    $strhide     = get_string('hide');
    $strshow     = get_string('show');
    $strlock     = get_string('lock', 'grades');
    $strunlock   = get_string('unlock', 'grades');

    $object = $element['object'];
    $eid    = $element['eid'];

/// prepare actions
    $actions = '<a href="category.php?id='.$COURSE->id.'&amp;action=edit&amp;eid='.$eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/edit.gif" class="iconsmall" alt="'.$stredit.'" title="'.$stredit.'"/></a>';

    if ($element['type'] == 'item' or ($element['type'] == 'category' and $element['depth'] > 1)) {
        $actions .= '<a href="category.php?id='.$COURSE->id.'&amp;action=delete&amp;eid='.$eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/delete.gif" class="iconsmall" alt="'.$strdelete.'" title="'.$strdelete.'"/></a>';
        $actions .= '<a href="category.php?id='.$COURSE->id.'&amp;action=moveselect&amp;eid='.$eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/move.gif" class="iconsmall" alt="'.$strmove.'" title="'.$strmove.'"/></a>';
    }

    if ($object->is_locked()) {
        $actions .= '<a href="category.php?id='.$COURSE->id.'&amp;action=unlock&amp;eid='.$eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/unlock.gif" class="iconsmall" alt="'.$strunlock.'" title="'.$strunlock.'"/></a>';
    } else {
        $actions .= '<a href="category.php?id='.$COURSE->id.'&amp;action=lock&amp;eid='.$eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/lock.gif" class="iconsmall" alt="'.$strlock.'" title="'.$strlock.'"/></a>';
    }

    if ($object->is_hidden()) {
        $name = '<span class="dimmed_text">'.$object->get_name().'</span>';
        $actions .= '<a href="category.php?id='.$COURSE->id.'&amp;action=show&amp;eid='.$eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/show.gif" class="iconsmall" alt="'.$strshow.'" title="'.$strshow.'"/></a>';
    } else {
        $name = $object->get_name();
        $actions .= '<a href="category.php?id='.$COURSE->id.'&amp;action=hide&amp;eid='.$eid.'&amp;sesskey='.sesskey().'"><img src="'.$CFG->pixpath.'/t/hide.gif" class="iconsmall" alt="'.$strhide.'" title="'.$strhide.'"/></a>';
    }

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
        $moveto = '<li><a href="category.php?id='.$COURSE->id.'&amp;action=move&amp;eid='.$moving.'&amp;moveafter='.$eid.'&amp;sesskey='.sesskey().'"><img class="movetarget" src="'.$CFG->wwwroot.'/pix/movehere.gif" alt="'.$strmovehere.'" title="'.$strmovehere.'" /></a></li>';
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
            print_grade_tree($child_el, $moving);
        }
        echo '</ul></li>';
        if ($element['depth'] > 1) {
            echo $moveto; // can not move after the top category
        }
    }
}


?>

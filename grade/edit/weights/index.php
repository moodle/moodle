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
require_once $CFG->dirroot.'/grade/report/lib.php'; // for preferences

$courseid = required_param('id', PARAM_INT);
$action   = optional_param('action', 0, PARAM_ALPHA);
$eid      = optional_param('eid', 0, PARAM_ALPHANUM);

require_js(array('yui_yahoo', 'yui_dom', 'yui_event', 'yui_json', 'yui_connection', 'yui_dragdrop', 'yui_treeview'));

/// Make sure they can even access this course

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/grade:manage', $context);

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'weight', 'courseid'=>$courseid));
$returnurl = $gpr->get_return_url(null);

//first make sure we have proper final grades - we need it for locking changes
grade_regrade_final_grades($courseid);

// get the grading tree object
// note: total must be first for moving to work correctly, if you want it last moving code must be rewritten!
$gtree = new grade_tree($courseid, false, false);

$switch = grade_get_setting($course->id, 'aggregationposition', $CFG->grade_aggregationposition);

$strgrades             = get_string('grades');
$strgraderreport       = get_string('graderreport', 'grades');
$strcategoriesedit     = get_string('categoriesedit', 'grades');
$strcategoriesanditems = get_string('categoriesanditems', 'grades');

$navigation = grade_build_nav(__FILE__, $strcategoriesanditems, array('courseid' => $courseid));
$moving = false;

switch ($action) {
    default:
        break;
}

$CFG->stylesheets[] = $CFG->wwwroot.'/grade/edit/weights/tree.css';
print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strcategoriesedit, $navigation, '', '', true, '', navmenu($course));

/// Print the plugin selector at the top
print_grade_plugin_selector($courseid, 'edit', 'tree');

print_heading(get_string('categoriesedit', 'grades'));
$tree_json = json_encode(get_tree_json($gtree, $gtree->top_element));

require_once('ajax.php');

print_box_start('gradetreebox generalbox');

echo '<div id="expandcontractdiv">
       <a id="expand" href="#">Expand all</a>
	    <a id="collapse" href="#">Collapse all</a>
	</div> ';

echo '<form method="post" action="'.$returnurl.'">';
echo '<div id="weightstree">';

// print_grade_tree($gtree, $gtree->top_element, $gpr, $switch);
//
echo '</div>';
echo '</form>';
print_box_end();

print_footer($course);
die;

function get_tree_json(&$gtree, $element) {

    $return_array = array();

    $object = $element['object'];
    $eid    = $element['eid'];
    $object->name = $gtree->get_element_header($element, false, false, false);

    $return_array['item'] = $object;

    if ($element['type'] == 'category') {
        foreach($element['children'] as $child_el) {
            $return_array['children'][] = get_tree_json($gtree, $child_el);
        }
    }

    return $return_array;
}
?>

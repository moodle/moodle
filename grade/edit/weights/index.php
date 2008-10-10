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
require_once $CFG->dirroot.'/grade/edit/weights/lib.php';

$courseid        = required_param('id', PARAM_INT);
$action          = optional_param('action', 0, PARAM_ALPHA);
$eid             = optional_param('eid', 0, PARAM_ALPHANUM);
$category        = optional_param('category', null, PARAM_INT);
$aggregationtype = optional_param('aggregationtype', null, PARAM_INT);

require_js(array('yui_yahoo', 'yui_dom', 'yui_event', 'yui_json', 'yui_connection', 'yui_dragdrop', 'yui_treeview'));

/// Make sure they can even access this course

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/grade:manage', $context);

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'weights', 'courseid'=>$courseid));
$returnurl = $gpr->get_return_url(null);

// Change category aggregation if requested
if (!is_null($category) && !is_null($aggregationtype)) {
    if (!$grade_category = grade_category::fetch(array('id'=>$category, 'courseid'=>$courseid))) {
        error('Incorrect category id!');
    }
    $data->aggregation = $aggregationtype;
    grade_category::set_properties($grade_category, $data);
    $grade_category->update();
}

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

$CFG->stylesheets[] = $CFG->wwwroot.'/grade/edit/weights/tree.css';
print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strcategoriesedit, $navigation, '', '', true, '', navmenu($course));

/// Print the plugin selector at the top
print_grade_plugin_selector($courseid, 'edit', 'tree');

print_heading(get_string('weightsedit', 'grades'));
$tree_json = json_encode(get_tree_json($gtree, $gtree->top_element));

$form_key = optional_param('sesskey', null, PARAM_ALPHANUM);

if ($form_key) {
    $data = data_submitted();

    foreach ($data as $key => $value) {
        if (preg_match('/aggregation_type_([0-9]*)/', $key, $matches)) {
            $aggtype = required_param($matches[0], PARAM_INT);
            $a->id = $matches[1];

            if (!$DB->update_record('grade_categories', array('id' => $matches[1], 'aggregation' => $aggtype))) {
                print_error('errorupdatinggradecategoryaggregation', 'grades', $a);
            }

        } elseif (preg_match('/weight_([0-9]*)/', $key, $matches)) {
            $weight = required_param($matches[0], PARAM_NUMBER);
            $a->id = $matches[1];

            if (!$DB->update_record('grade_items', array('id' => $matches[1], 'aggregationcoef' => $weight))) {
                print_error('errorupdatinggradeitemaggregationcoef', 'grades', $a);
            }

        } elseif (preg_match('/extracredit_original_([0-9]*)/', $key, $matches)) { // Sum extra credit checkbox
            $extracredit = optional_param("extracredit_{$matches[1]}", null, PARAM_BOOL);
            $original_value = required_param($matches[0], PARAM_BOOL);
            $a->id = $matches[1];
            $newvalue = null;

            if ($original_value == 1 && is_null($extracredit)) {
                $newvalue = 0;
            } elseif ($original_value == 0 && $extracredit == 1) {
                $newvalue = 1;
            } else {
                continue;
            }

            if (!$DB->update_record('grade_items', array('id' => $matches[1], 'aggregationcoef' => $newvalue))) {
                print_error('errorupdatinggradeitemaggregationcoef', 'grades', $a);
            }

        } elseif (preg_match('/aggregate(onlygraded|subcats|outcomes)_original_([0-9]*)/', $key, $matches)) {
            $setting = optional_param('aggregate'.$matches[1].'_'.$matches[2], null, PARAM_BOOL);
            $original_value = required_param($matches[0], PARAM_BOOL);
            $a->id = $matches[2];

            $newvalue = null;
            if ($original_value == 1 && is_null($setting)) {
                $newvalue = 0;
            } elseif ($original_value == 0 && $setting == 1) {
                $newvalue = 1;
            } else {
                continue;
            }

            if (!$DB->update_record('grade_categories', array('id' => $matches[2], 'aggregate'.$matches[1] => $newvalue))) {
                print_error('errorupdatinggradecategoryaggregate'.$matches[1], 'grades', $a);
            }
        }
    }
}

// AJAX interface not really needed: adds nice tree functions but not very useful
// require_once('ajax.php');

print_box_start('gradetreebox generalbox');

// echo '<div id="expandcontractdiv"> <a id="expand" href="#">Expand all</a> <a id="collapse" href="#">Collapse all</a> </div> ';

echo '<form method="post" action="'.$returnurl.'">';
echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';
echo '<div id="gradetree">';
echo build_html_tree(get_tree_json($gtree, $gtree->top_element), null, $returnurl);

// print_grade_tree($gtree, $gtree->top_element, $gpr, $switch);
//
echo '</div><div>';

echo '<input type="submit" value="Update weights" />';
echo '</div></form>';
print_box_end();

print_footer($course);
die;

?>

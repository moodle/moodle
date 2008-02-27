<?php  //$Id$

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
require_once $CFG->dirroot.'/grade/report/lib.php';
require_once 'category_form.php';

$courseid = required_param('courseid', PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
require_capability('moodle/grade:manage', $context);

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url('index.php?id='.$course->id);


$mform = new edit_category_form(null, array('gpr'=>$gpr));

if ($id) {
    if (!$grade_category = grade_category::fetch(array('id'=>$id, 'courseid'=>$course->id))) {
        error('Incorrect category id!');
    }
    $grade_category->apply_forced_settings();
    $category = $grade_category->get_record_data();
    // Get Category preferences
    $category->pref_aggregationview = grade_report::get_pref('aggregationview', $id);
    // Load agg coef
    $grade_item = $grade_category->load_grade_item();
    $category->aggregationcoef = format_float($grade_item->aggregationcoef, 4);
    // set parent
    $category->parentcategory = $grade_category->parent;

} else {
    $grade_category = new grade_category(array('courseid'=>$courseid), false);
    $grade_category->apply_default_settings();
    $grade_category->apply_forced_settings();

    $category = $grade_category->get_record_data();
    $category->aggregationcoef = format_float(0, 4);
}

$mform->set_data($category);

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data(false)) {
    // If no fullname is entered for a course category, put ? in the DB
    if (!isset($data->fullname) || $data->fullname == '') {
        $data->fullname = '?';
    }

    if (!isset($data->aggregateonlygraded)) {
        $data->aggregateonlygraded = 0;
    }
    if (!isset($data->aggregateoutcomes)) {
        $data->aggregateoutcomes = 0;
    }
    grade_category::set_properties($grade_category, $data);

    if (empty($grade_category->id)) {
        $grade_category->insert();

    } else {
        $grade_category->update();
    }

    // Handle user preferences
    if (isset($data->pref_aggregationview)) {
        if (!grade_report::set_pref('aggregationview', $data->pref_aggregationview, $grade_category->id)) {
            error("Could not set preference aggregationview to $value for this grade category");
        }
    }

    // set parent if needed
    if (isset($data->parentcategory)) {
        $grade_category->set_parent($data->parentcategory, 'gradebook');
    }

    // update agg coef if needed
    if (isset($data->aggregationcoef)) {
        $data->aggregationcoef = unformat_float($data->aggregationcoef);
        $grade_item = $grade_category->load_grade_item();
        $grade_item->aggregationcoef = $data->aggregationcoef;
        $grade_item->update();
    }

    redirect($returnurl);
}


$strgrades         = get_string('grades');
$strgraderreport   = get_string('graderreport', 'grades');
$strcategoriesedit = get_string('categoryedit', 'grades');
$strcategory       = get_string('category', 'grades');

$navigation = grade_build_nav(__FILE__, $strcategory, array('courseid' => $courseid));

print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $strcategoriesedit, $navigation, '', '', true, '', navmenu($course));

$mform->display();

print_footer($course);
die;

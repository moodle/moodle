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
require_once 'item_form.php';

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

$mform = new edit_item_form(null, array('gpr'=>$gpr));

if ($mform->is_cancelled()) {
    redirect($returnurl);
}

if ($grade_item = grade_item::fetch(array('id'=>$id, 'courseid'=>$courseid))) {
    // redirect if outcomeid present
    if (!empty($grade_item->outcomeid) && !empty($CFG->enableoutcomes)) {
        $url = $CFG->wwwroot.'/grade/edit/tree/outcomeitem.php?id='.$id.'&amp;courseid='.$courseid;
        redirect($gpr->add_url_params($url));
    }
    $item = $grade_item->get_record_data();

    if ($grade_item->is_course_item()) {
        $parent_category = null;
        $item->parentcategory = 0;
    } else if ($grade_item->is_category_item()) {
        $parent_category = $grade_item->get_parent_category();
        $parent_category = $parent_category->get_parent_category();
        $item->parentcategory = $parent_category->id;
    } else {
        $parent_category = $grade_item->get_parent_category();
        $item->parentcategory = $parent_category->id;
    }
} else {
    $grade_item = new grade_item(array('courseid'=>$courseid, 'itemtype'=>'manual'), false);
    $item = $grade_item->get_record_data();
    $parent_category = grade_category::fetch_course_category($courseid);
    $item->parentcategory = $parent_category->id;
}
$decimalpoints = $grade_item->get_decimals();

if ($item->hidden > 1) {
    $item->hiddenuntil = $item->hidden;
    $item->hidden = 0;
} else {
    $item->hiddenuntil = 0;
}

$item->locked = !empty($item->locked);

$item->grademax        = format_float($item->grademax, $decimalpoints);
$item->grademin        = format_float($item->grademin, $decimalpoints);
$item->gradepass       = format_float($item->gradepass, $decimalpoints);
$item->multfactor      = format_float($item->multfactor, 4);
$item->plusfactor      = format_float($item->plusfactor, 4);

if (empty($parent_category)) {
    $item->aggregationcoef = 0;
} else if ($parent_category->aggregation == GRADE_AGGREGATE_SUM) {
    $item->aggregationcoef = $item->aggregationcoef > 0 ? 1 : 0;
} else {
    $item->aggregationcoef = format_float($item->aggregationcoef, 4);
}

$mform->set_data($item);

if ($data = $mform->get_data(false)) {

    if (!isset($data->aggregationcoef)) {
        $data->aggregationcoef = 0;
    }

    $hidden      = empty($data->hidden) ? 0: $data->hidden;
    $hiddenuntil = empty($data->hiddenuntil) ? 0: $data->hiddenuntil;
    unset($data->hidden);
    unset($data->hiddenuntil);

    $locked   = empty($data->locked) ? 0: $data->locked;
    $locktime = empty($data->locktime) ? 0: $data->locktime;
    unset($data->locked);
    unset($data->locktime);

    $convert = array('grademax', 'grademin', 'gradepass', 'multfactor', 'plusfactor', 'aggregationcoef');
    foreach ($convert as $param) {
        if (array_key_exists($param, $data)) {
            $data->$param = unformat_float($data->$param);
        }
    }

    $grade_item = new grade_item(array('id'=>$id, 'courseid'=>$courseid));
    grade_item::set_properties($grade_item, $data);
    $grade_item->outcomeid = null;

    // Handle null decimals value
    if (!array_key_exists('decimals', $data) or $data->decimals < 0) {
        $grade_item->decimals = null;
    }

    if (empty($grade_item->id)) {
        $grade_item->itemtype = 'manual'; // all new items to be manual only
        $grade_item->insert();

        // set parent if needed
        if (isset($data->parentcategory)) {
            $grade_item->set_parent($data->parentcategory, 'gradebook');
        }

    } else {
        $grade_item->update();
    }

    // update hiding flag
    if ($hiddenuntil) {
        $grade_item->set_hidden($hiddenuntil, false);
    } else {
        $grade_item->set_hidden($hidden, false);
    }

    $grade_item->set_locktime($locktime); // locktime first - it might be removed when unlocking
    $grade_item->set_locked($locked, false, true);

    redirect($returnurl);
}

$strgrades       = get_string('grades');
$strgraderreport = get_string('graderreport', 'grades');
$stritemsedit    = get_string('itemsedit', 'grades');
$stritem         = get_string('item', 'grades');

$navigation = grade_build_nav(__FILE__, $stritem, array('courseid' => $courseid));


print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $stritemsedit, $navigation, '', '', true, '', navmenu($course));

$mform->display();

print_footer($course);

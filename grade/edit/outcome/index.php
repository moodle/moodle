<?php // $Id$
      // Allows a creator to edit custom outcomes, and also display help about outcomes

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
require_once $CFG->libdir.'/gradelib.php';

$courseid = optional_param('id', 0, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);

/// Make sure they can even access this course
if ($courseid) {
    if (!$course = get_record('course', 'id', $courseid)) {
        print_error('nocourseid');
    }
    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/grade:manageoutcomes', $context);

    if (empty($CFG->enableoutcomes)) {
        redirect('../../index.php?id='.$courseid);
    }

} else {
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('outcomes');
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'outcome', 'courseid'=>$courseid));


$strgrades = get_string('grades');
$pagename  = get_string('outcomes', 'grades');

$navigation = grade_build_nav(__FILE__, $pagename, $courseid);

$strshortname        = get_string('shortname');
$strfullname         = get_string('fullname');
$strscale            = get_string('scale');
$strstandardoutcome  = get_string('outcomesstandard', 'grades');
$strcustomoutcomes   = get_string('outcomescustom', 'grades');
$strdelete           = get_string('delete');
$stredit             = get_string('edit');
$srtcreatenewoutcome = get_string('outcomecreate', 'grades');
$stritems            = get_string('items', 'grades');
$strcourses          = get_string('courses');
$stredit             = get_string('edit');
$strexport           = get_string('export', 'grades');

switch ($action) {
    case 'delete':
        if (!confirm_sesskey()) {
            break;
        }
        $outcomeid = required_param('outcomeid', PARAM_INT);
        if (!$outcome = grade_outcome::fetch(array('id'=>$outcomeid))) {
            break;
        }

        if (empty($outcome->courseid)) {
            require_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM));
        } else if ($outcome->courseid != $courseid) {
            error('Incorrect courseid!');
        }

        if (!$outcome->can_delete()) {
            break;
        }

        //TODO: add confirmation
        $outcome->delete();
        break;
}

$systemcontext = get_context_instance(CONTEXT_SYSTEM);
$caneditsystemscales = has_capability('moodle/course:managescales', $systemcontext);

if ($courseid) {
    /// Print header
    print_header_simple($strgrades.': '.$pagename, ': '.$strgrades, $navigation, '', '', true, '', navmenu($course));
    /// Print the plugin selector at the top
    print_grade_plugin_selector($courseid, 'edit', 'outcome');

    $caneditcoursescales = has_capability('moodle/course:managescales', $context);

    $currenttab = 'outcomes';
    require('tabs.php');

} else {
    admin_externalpage_print_header();

    $caneditcoursescales = $caneditsystemscales;
}

print('<form action="export.php" method="post">' ."\n");

if ($courseid and $outcomes = grade_outcome::fetch_all_local($courseid)) {

    print_heading($strcustomoutcomes);
    $data = array();
    foreach($outcomes as $outcome) {
        $line = array();
        $line[] = $outcome->get_name();
        $line[] = $outcome->get_shortname();

        $scale = $outcome->load_scale();
        if (empty($scale->id)) {   // hopefully never happens
            $line[] = $scale->get_name();
        } else {
            if (empty($scale->courseid)) {
                $caneditthisscale = $caneditsystemscales;
            } else if ($scale->courseid == $courseid) {
                $caneditthisscale = $caneditcoursescales;
            } else {
                $context = get_context_instance(CONTEXT_COURSE, $scale->courseid);
                $caneditthisscale = has_capability('moodle/course:managescales', $context);
            }
            if ($caneditthisscale) {
                $url = $CFG->wwwroot.'/grade/edit/scale/edit.php?courseid='.$courseid.'&amp;id='.$scale->id;
                $url = $gpr->add_url_params($url);
                $line[] = '<a href="'.$url.'">'.$scale->get_name().'</a>';
            } else {
                $line[] = $scale->get_name();
            }
        }

        $line[] = $outcome->get_item_uses_count();

        $buttons = "";
        $buttons .= "<a title=\"$stredit\" href=\"edit.php?courseid=$courseid&amp;id=$outcome->id\"><img".
                    " src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> ";
        if ($outcome->can_delete()) {
            $buttons .= "<a title=\"$strdelete\" href=\"index.php?id=$courseid&amp;outcomeid=$outcome->id&amp;action=delete&amp;sesskey=$USER->sesskey\"><img".
                        " src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strdelete\" /></a> ";
        }
        $line[] = $buttons;
        
        $buttons = '<input type="checkbox" name="export[]" value="'. $outcome->id .'">';
        $line[] = $buttons;

        $data[] = $line;
    }
    $table = new object();
    $table->head  = array($strfullname, $strshortname, $strscale, $stritems, $stredit, $strexport);
    $table->size  = array('30%', '18%', '18%', '18%', '8%', '8%' );
    $table->align = array('left', 'left', 'left', 'center', 'center', 'center');
    $table->width = '90%';
    $table->data  = $data;
    print_table($table);
}


if ($outcomes = grade_outcome::fetch_all_global()) {
    
    print_heading($strstandardoutcome); 
    $data = array();
    foreach($outcomes as $outcome) {
        $line = array();
        $line[] = $outcome->get_name();
        $line[] = $outcome->get_shortname();

        $scale = $outcome->load_scale();
        if (empty($scale->id)) {   // hopefully never happens
            $line[] = $scale->get_name();
        } else {
            if (empty($scale->courseid)) {
                $caneditthisscale = $caneditsystemscales;
            } else if ($scale->courseid == $courseid) {
                $caneditthisscale = $caneditcoursescales;
            } else {
                $context = get_context_instance(CONTEXT_COURSE, $scale->courseid);
                $caneditthisscale = has_capability('moodle/course:managescales', $context);
            }
            if ($caneditthisscale) {
                $url = $CFG->wwwroot.'/grade/edit/scale/edit.php?courseid='.$courseid.'&amp;id='.$scale->id;
                $url = $gpr->add_url_params($url);
                $line[] = '<a href="'.$url.'">'.$scale->get_name().'</a>';
            } else {
                $line[] = $scale->get_name();
            }
        }

        $line[] = $outcome->get_course_uses_count();
        $line[] = $outcome->get_item_uses_count();

        $buttons = "";
        if (has_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM))) {
            $buttons .= "<a title=\"$stredit\" href=\"edit.php?courseid=$courseid&amp;id=$outcome->id\"><img".
                        " src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> ";
        }
        if (has_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM)) and $outcome->can_delete()) {
            $buttons .= "<a title=\"$strdelete\" href=\"index.php?id=$courseid&amp;outcomeid=$outcome->id&amp;action=delete&amp;sesskey=$USER->sesskey\"><img".
                        " src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strdelete\" /></a> ";
        }
        $line[] = $buttons;

        $buttons = '<input type="checkbox" name="export[]" value="'. $outcome->id .'">';
        $line[] = $buttons;

        $data[] = $line;
    }
    $table = new object();
    $table->head  = array($strfullname, $strshortname, $strscale, $strcourses, $stritems, $stredit, $strexport);
    $table->size  = array('30%', '19%', '19%', '8%', '8%', '8%', '8%');
    $table->align = array('left', 'left', 'left', 'center', 'center', 'center', 'center');
    $table->width = '90%';
    $table->data  = $data;
    print_table($table);
}


echo '<div class="buttons">';
echo "<input type=\"hidden\" name=\"sesskey\" value=\"$USER->sesskey\" />";
print('<input type="submit" value="'. get_string('exportselectedoutcomes', 'grades') .'" name="export_outcomes"></form>');
print_single_button('edit.php', array('courseid'=>$courseid), $srtcreatenewoutcome);
echo '</div>';

echo '<div>'; 
$upload_max_filesize = get_max_upload_file_size($CFG->maxbytes);
$filesize = display_size($upload_max_filesize);

$strimportoutcomes = get_string('importoutcomes', 'grades');
$struploadthisfile = get_string('uploadthisfile');
$strimportcustom = get_string('importcustom', 'grades');
$strimportstandard = get_string('importstandard', 'grades');
$strmaxsize = get_string("maxsize", "", $filesize);

require_once($CFG->dirroot.'/lib/uploadlib.php');

echo '<div>';
echo '<form enctype="multipart/form-data" method="post" action="import.php">';
echo '<input type="hidden" name="action" value="upload" />';
echo '<input type="hidden" name="id" value="'. $courseid .'" />';
echo '<input type="hidden" name="sesskey" value="'. $USER->sesskey .'" />';
echo '<table class="generalbox boxaligncenter" width="50%" cellspacing="1" cellpadding="5">';
if ($courseid && has_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM))) {
    echo '<tr><td><ul style="list-style-type:none;">';
    echo '<li><label><input type="radio" name="scope" value="local" checked="checked">'. $strimportcustom .'</label>';
    echo '<li><label><input type="radio" name="scope" value="global">'. $strimportstandard .'</label>';
    echo '</ul></td></tr>';
}
echo '<tr><td><p>'. $strimportoutcomes .'('. $strmaxsize .')</p></td></tr>';
echo '<tr><td>'. 
    upload_print_form_fragment(1,array('userfile'),null,false,null,$upload_max_filesize,0,true) .
    '<input type="submit" name="save" value="'. $struploadthisfile .'" /></td></tr>';
echo '</table>';
echo '</div>';
echo '</form>';
echo '</div>';

if ($courseid) {
    print_footer($course);
} else {
    admin_externalpage_print_footer();
}


?>

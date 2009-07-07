<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

require_once '../../../config.php';
require_once($CFG->dirroot.'/lib/formslib.php');
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
    if (empty($CFG->enableoutcomes)) {
        redirect('../../../');
    }
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('outcomes');
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'outcome', 'courseid'=>$courseid));

require_once('import_outcomes_form.php');
$upload_form = new import_outcomes_form();

if ($upload_form_data = $upload_form->get_data()) {
    require_once('import.php');
    exit();
}


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
$strcreatenewoutcome = get_string('outcomecreate', 'grades');
$stritems            = get_string('items', 'grades');
$strcourses          = get_string('courses');
$stredit             = get_string('edit');

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

        $deleteconfirmed = optional_param('deleteconfirmed', 0, PARAM_BOOL);

        if(!$deleteconfirmed){
            print_header(get_string('outcomedelete', 'grades'));
            notice_yesno(get_string('outcomeconfirmdelete', 'grades', $outcome->fullname),
                    "index.php?id={$courseid}", "index.php?id={$courseid}",
                    array('outcomeid' => $outcome->id,
                        'action'=> 'delete',
                        'sesskey' =>  $USER->sesskey,
                        'deleteconfirmed'=> 1)
                    );
            print_footer();
            die;
        }else{
            $outcome->delete();
        }
        break;
}

$systemcontext = get_context_instance(CONTEXT_SYSTEM);
$caneditsystemscales = has_capability('moodle/course:managescales', $systemcontext);

if ($courseid) {

    $caneditcoursescales = has_capability('moodle/course:managescales', $context);

} else {
    admin_externalpage_print_header();
    $caneditcoursescales = $caneditsystemscales;
}


$outcomes_tables = array();
$heading = get_string('outcomes', 'grades');

if ($courseid and $outcomes = grade_outcome::fetch_all_local($courseid)) {
    $return = print_heading($strcustomoutcomes, '', 3, 'main', true);
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

        $data[] = $line;
    }
    $table = new object();
    $table->head  = array($strfullname, $strshortname, $strscale, $stritems, $stredit);
    $table->size  = array('30%', '20%', '20%', '20%', '10%' );
    $table->align = array('left', 'left', 'left', 'center', 'center');
    $table->width = '90%';
    $table->data  = $data;
    $return .= print_table($table, true);
    $outcomes_tables[] = $return;
}


if ($outcomes = grade_outcome::fetch_all_global()) {

    $return = print_heading($strstandardoutcome, '', 2, 'main', true);
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

        $data[] = $line;
    }
    $table = new object();
    $table->head  = array($strfullname, $strshortname, $strscale, $strcourses, $stritems, $stredit);
    $table->size  = array('30%', '20%', '20%', '10%', '10%', '10%');
    $table->align = array('left', 'left', 'left', 'center', 'center', 'center');
    $table->width = '90%';
    $table->data  = $data;
    $return .= print_table($table, true);
    $outcomes_tables[] = $return;
}

if ($courseid) {
    /// Print header
    print_grade_page_head($courseid, 'outcome', 'edit', $heading);
}

foreach($outcomes_tables as $table) {
    print($table);
}

echo '<div class="buttons">';
print_single_button('edit.php', array('courseid'=>$courseid), $strcreatenewoutcome);
if ( !empty($outcomes_tables) ) {
    print_single_button('export.php', array('id'=>$courseid, 'sesskey'=>sesskey()),  get_string('exportalloutcomes', 'grades'));
}
echo '</div>';

$upload_form->display();

if ($courseid) {
    print_footer($course);
} else {
    admin_externalpage_print_footer();
}

?>

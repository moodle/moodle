<?php // $Id$
      // Allows a creator to edit custom outcomes, and also display help about outcomes

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
    require_capability('moodle/grade:manage', $context);

} else {
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('outcomes');
}

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'outcome', 'courseid'=>$courseid));


$strgrades = get_string('grades');
$pagename  = get_string('outcomes', 'grades');

$navlinks = array(array('name'=>$strgrades, 'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
                  array('name'=>$pagename, 'link'=>'', 'type'=>'misc'));
$navigation = build_navigation($navlinks);

$strshortname        = get_string('shortname');
$strfullname         = get_string('fullname');
$strscale            = get_string('scale');
$strstandardoutcome  = get_string('outcomesstandard', 'grades');
$strcustomoutcomes   = get_string('outcomescustom', 'grades');
$strdelete           = get_string('delete');
$stredit             = get_string('edit');
$srtcreatenewoutcome = get_string('outcomecreate', 'grades');
$stractivities       = get_string('activities');
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
                $line[] = '<a href="'.$CFG->wwwroot.'/grade/edit/scale/edit.php?courseid='.$courseid.'&amp;id='.$scale->id.'">'.$scale->get_name().'</a>';
            } else {
                $line[] = $scale->get_name();
            }
        }

        $outcomes_uses = $outcome->get_uses_count();
        $line[] = $outcomes_uses;

        $buttons = "";
        $buttons .= "<a title=\"$stredit\" href=\"edit.php?courseid=$courseid&amp;id=$outcome->id\"><img".
                    " src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> ";
        if (empty($outcomes_uses)) {
            $buttons .= "<a title=\"$strdelete\" href=\"index.php?id=$courseid&amp;outcomeid=$outcome->id&amp;action=delete&amp;sesskey=$USER->sesskey\"><img".
                        " src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strdelete\" /></a> ";
        }
        $line[] = $buttons;
        $data[] = $line;
    }
    $table = new object();
    $table->head  = array($strfullname, $strshortname, $strscale, $stractivities, $stredit);
    $table->size  = array('30%', '20%', '20%', '20%', '10%');
    $table->align = array('left', 'left', 'left', 'center', 'center');
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
                $line[] = '<a href="'.$CFG->wwwroot.'/grade/edit/scale/edit.php?courseid='.$courseid.'&amp;id='.$scale->id.'">'.$scale->get_name().'</a>';
            } else {
                $line[] = $scale->get_name();
            }
        }

        $outcomes_uses = $outcome->get_uses_count();
        $line[] = $outcomes_uses;

        $buttons = "";
        if (has_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM))) {
            $buttons .= "<a title=\"$stredit\" href=\"edit.php?courseid=$courseid&amp;id=$outcome->id\"><img".
                        " src=\"$CFG->pixpath/t/edit.gif\" class=\"iconsmall\" alt=\"$stredit\" /></a> ";
        }
        if (empty($outcomes_uses) and has_capability('moodle/grade:manage', get_context_instance(CONTEXT_SYSTEM))) {
            $buttons .= "<a title=\"$strdelete\" href=\"index.php?id=$courseid&amp;outcomeid=$outcome->id&amp;action=delete&amp;sesskey=$USER->sesskey\"><img".
                        " src=\"$CFG->pixpath/t/delete.gif\" class=\"iconsmall\" alt=\"$strdelete\" /></a> ";
        }
        $line[] = $buttons;
        $data[] = $line;
    }
    $table = new object();
    $table->head  = array($strfullname, $strshortname, $strscale, $stractivities, $stredit);
    $table->size  = array('30%', '20%', '20%', '20%', '10%');
    $table->align = array('left', 'left', 'left', 'center', 'center');
    $table->width = '90%';
    $table->data  = $data;
    print_table($table);
}


echo '<div class="buttons">';
print_single_button('edit.php', array('courseid'=>$courseid), $srtcreatenewoutcome);
echo '</div>';

if ($courseid) {
    print_footer($course);
} else {
    admin_externalpage_print_footer();
}


?>

<?php // $Id$

require '../../../config.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once 'edit_form.php';


$contextid = optional_param('id', SYSCONTEXTID, PARAM_INT);

if (!$context = get_context_instance_by_id($contextid)) {
    error('Incorrect context id');
}

if ($context->contextlevel == CONTEXT_SYSTEM or $context->contextlevel == CONTEXT_COURSECAT) {
    require_once $CFG->libdir.'/adminlib.php';
    require_login();
    admin_externalpage_setup('letters');
    $admin = true;
    $returnurl = "$CFG->wwwroot/$CFG->admin";


} else if ($context->contextlevel == CONTEXT_COURSE) {
    require_login($context->instanceid);
    $admin = false;
    $returnurl = $CFG->wwwroot.'/grade/edit/letter/index.php?id='.$context->instanceid;

} else {
    error('Incorrect context level');
}

require_capability('moodle/grade:manageletters', $context);

$strgrades = get_string('grades');
$pagename  = get_string('letters', 'grades');

$letters = grade_get_letters($context);
$num = count($letters) + 3;

$data = new object();
$data->id = $context->id;

$i = 1;
foreach ($letters as $boundary=>$letter) {
    $gradelettername = 'gradeletter'.$i;
    $gradeboundaryname = 'gradeboundary'.$i;

    $data->$gradelettername   = $letter;
    $data->$gradeboundaryname = $boundary;
    $i++;
}
$data->override = record_exists('grade_letters', 'contextid', $contextid);

$mform = new edit_letter_form(null, array('num'=>$num, 'admin'=>$admin));
$mform->set_data($data);

if ($mform->is_cancelled()) {
    redirect($returnurl);

} else if ($data = $mform->get_data()) {
    if (empty($data->override)) {
        delete_records('grade_letters', 'contextid', $context->id);
        redirect($returnurl);
    }

    $letters = array();
    for($i=1; $i<$num+1; $i++) {
        $gradelettername = 'gradeletter'.$i;
        $gradeboundaryname = 'gradeboundary'.$i;

        if (array_key_exists($gradeboundaryname, $data) and $data->$gradeboundaryname != -1) {
            $letter = trim($data->$gradelettername);
            if ($letter == '') {
                continue;
            }
            $letters[$data->$gradeboundaryname] = $letter;
        }
    }
    krsort($letters, SORT_NUMERIC);

    $old_ids = array();
    if ($records = get_records('grade_letters', 'contextid', $context->id, 'lowerboundary ASC', 'id')) {
        $old_ids = array_keys($records);
    }

    foreach($letters as $boundary=>$letter) {
        $record = new object();
        $record->letter        = $letter;
        $record->lowerboundary = $boundary;
        $record->contextid     = $context->id;

        if ($old_id = array_pop($old_ids)) {
            $record->id = $old_id;
            update_record('grade_letters', $record);
        } else {
            insert_record('grade_letters', $record);
        }
    }

    foreach($old_ids as $old_id) {
        delete_records('grade_letters', 'id', $old_id);
    }

    redirect($returnurl);
}


//page header
if ($admin) {
    admin_externalpage_print_header();

} else {
    $navigation = grade_build_nav(__FILE__, $pagename, $COURSE->id);
    /// Print header
    print_header_simple($strgrades.': '.$pagename, ': '.$strgrades, $navigation, '', '', true, '', navmenu($COURSE));

    $currenttab = 'lettersedit';
    require('tabs.php');
}

$mform->display();

print_footer($COURSE);
?>

<?php // $Id$
      // Allows a creator to edit custom scales, and also display help about scales

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->libdir.'/gradelib.php';
require_once $CFG->dirroot.'/grade/report/lib.php';
require_once 'gradedisplay_form.php';

$courseid = optional_param('id', 0, PARAM_INT);
$action   = optional_param('action', '', PARAM_ALPHA);

/// Make sure they can even access this course
if ($courseid) {
    if (!$course = get_record('course', 'id', $courseid)) {
        print_error('nocourseid');
    }
    require_login($course);
    $context = get_context_instance(CONTEXT_COURSE, $course->id);
    require_capability('moodle/course:managescales', $context);
} else {
    require_once $CFG->libdir.'/adminlib.php';
    admin_externalpage_setup('scales');
}

$straddelement = get_string('addelement', 'grades');
switch ($action) {
    case $straddelement:
        // Insert a record in the grade_letters table, with 0 as lower boundary and  ' - ' as letter
        $record = new stdClass();
        $record->contextid = $context->id;
        $record->letter = '-';
        $record->lowerboundary = 0;
        insert_record('grade_letters', $record);
        break;
    default:
        break;
}

$course_has_letters = get_field('grade_letters', 'contextid', 'contextid', $context->id);

/// return tracking object
$gpr = new grade_plugin_return(array('type'=>'edit', 'plugin'=>'gradedisplay', 'courseid'=>$courseid));
$returnurl = $gpr->get_return_url($CFG->wwwroot.'/grade/edit/gradedisplay/index.php?id='.$course->id);

$mform = new edit_grade_display_form(null, array('gpr'=>$gpr, 'course_has_letters' => $course_has_letters, 'action' => $action));

if ($mform->is_cancelled()) {
    redirect($returnurl);

// form processing
} else if ($data = $mform->get_data(false)) {
    // Delete existing grade_letters for this contextid, whether we add, update or set the grade letters to defaults
    if ($course_has_letters) {
        delete_records('grade_letters', 'contextid', $context->id);
    }

    // Update course item's gradedisplay type
    if (isset($data->gradedisplaytype)) {
        set_field('grade_items', 'display', $data->gradedisplaytype, 'courseid', $courseid, 'itemtype', 'course');
    }

    // If override is present, add/update entries in grade_letters table
    if (!empty($data->override)) {
        $records = array();

        // Loop through grade letters and boundaries
        foreach ($data as $key => $variable) {
            preg_match('/[gradeletter|gradeboundary]([0-9]{1,2})/', $key, $matches);
            $index = null;
            if (isset($matches[1])) {
                $index = $matches[1];
            }

            if (strstr($key, 'gradeletter')) {
                $records[$index] = new stdClass();
                $records[$index]->letter = $variable;
            } elseif (strstr($key, 'gradeboundary')) {
                if (!empty($records[$index])) {
                    $records[$index]->lowerboundary = $variable;
                }
            }
        }

        foreach ($records as $key => $record) {
            // Do not insert if either value is empty or set to "unused"
            $values_set = isset($record->letter) && isset($record->lowerboundary);

            if ($values_set && strlen($record->letter) > 0 && strlen($record->lowerboundary) > 0) {
                $record->contextid = $context->id;
                if ($id = insert_record('grade_letters', $record)) {
                    $record = new stdClass();
                } else {
                    debugging('Error inserting grade_letters record!');
                    die();
                }
            }
        }
    } else {

    }

    redirect($returnurl, get_string('coursegradedisplayupdated', 'grades'));
}

$strgrades = get_string('grades');
$pagename = get_string('gradedisplay');

$navigation = grade_build_nav(__FILE__, $pagename, array('courseid' => $courseid));

$strname           = get_string('name');
$strdelete         = get_string('delete');
$stredit           = get_string('edit');
$strused           = get_string('used');
$stredit           = get_string('edit');

switch ($action) {
    case 'delete':
        if (!confirm_sesskey()) {
            break;
        }
        $scaleid = required_param('scaleid', PARAM_INT);
        if (!$scale = grade_scale::fetch(array('id'=>$scaleid))) {
            break;
        }

        if (empty($scale->courseid)) {
            require_capability('moodle/course:managescales', get_context_instance(CONTEXT_SYSTEM));
        } else if ($scale->courseid != $courseid) {
            error('Incorrect courseid!');
        }

        if (!$scale->can_delete()) {
            break;
        }

        //TODO: add confirmation
        $scale->delete();
        break;
}

if ($courseid) {
    /// Print header
    print_header_simple($strgrades.': '.$pagename, ': '.$strgrades, $navigation, '', '', true, '', navmenu($course));
    /// Print the plugin selector at the top
    print_grade_plugin_selector($courseid, 'edit', 'scale');

} else {
    admin_externalpage_print_header();
}

print_simple_box_start("center");
$mform->display();
print_simple_box_end();

if ($courseid) {
    print_footer($course);
} else {
    admin_externalpage_print_footer();
}


?>


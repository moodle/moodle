<?php  //$Id$
require_once '../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/lib.php';
require_once $CFG->libdir.'/gradelib.php';
require_once 'item_form.php';

$courseid = required_param('courseid', PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);

$context = get_context_instance(CONTEXT_COURSE, $course->id);
//require_capability() here!!

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url('tree.php?id='.$course->id);

$mform = new edit_item_form(null, array('gpr'=>$gpr));

if ($mform->is_cancelled()) {
    redirect($returnurl);
}

if ($item = get_record('grade_items', 'id', $id, 'courseid', $course->id)) {
    // Get Item preferences
    $item->pref_gradedisplaytype = get_user_preferences('grade_report_gradedisplaytype' . $id, 'default');
    $item->pref_decimalpoints    = get_user_preferences('grade_report_decimalpoints' . $id, 'default');

    $item->calculation = grade_item::denormalize_formula($item->calculation, $course->id);
    $mform->set_data($item);

} else {
    // defaults for new items
    $mform->set_data(array('courseid'=>$course->id, 'itemtype'=>'manual'));
}

if ($data = $mform->get_data()) {
    if (array_key_exists('calculation', $data)) {
        $data->calculation = grade_item::normalize_formula($data->calculation, $course->id);
    }

    $grade_item = new grade_item(array('id'=>$id, 'courseid'=>$course->id));
    grade_item::set_properties($grade_item, $data);

    if (empty($grade_item->id)) {
        $grade_item->itemtype = 'manual'; // all new items to be manual only
        $grade_item->insert();

    } else {
        $grade_item->update();
    }

    // Handle user preferences
    if (isset($data->pref_gradedisplaytype)) {
        if (!grade_report::set_pref('gradedisplaytype', $data->pref_gradedisplaytype, $grade_item->id)) {
            error("Could not set preference gradedisplaytype to $value for this grade item");
        }
    }

    if (isset($data->pref_decimalpoints)) {
        if (!grade_report::set_pref('decimalpoints', $data->pref_decimalpoints, $grade_item->id)) {
            error("Could not set preference decimalpoints to $value for this grade item");
        }
    }

    redirect($returnurl, 'temporary debug delay', 50);
}

$strgrades       = get_string('grades');
$strgraderreport = get_string('graderreport', 'grades');
$stritemsedit    = get_string('itemsedit', 'grades');

$nav = array(array('name'=>$strgrades,'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
             array('name'=>$stritemsedit, 'link'=>'', 'type'=>'misc'));

$navigation = build_navigation($nav);


print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $stritemsedit, $navigation, '', '', true, '', navmenu($course));

$mform->display();

print_footer($course);

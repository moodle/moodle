<?php  //$Id$

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

if ($item = get_record('grade_items', 'id', $id, 'courseid', $course->id)) {
    // redirect if outcomeid present
    if (!empty($item->outcomeid) && !empty($CFG->enableoutcomes)) {
        $url = $CFG->wwwroot.'/grade/edit/tree/outcomeitem.php?id='.$id.'&amp;courseid='.$courseid;
        redirect($gpr->add_url_params($url));
    }
    // Get Item preferences
    $item->pref_gradedisplaytype = grade_report::get_pref('gradedisplaytype', $item->id);
    $item->pref_decimalpoints    = grade_report::get_pref('decimalpoints', $item->id);

    $item->calculation = grade_item::denormalize_formula($item->calculation, $course->id);

    $decimalpoints = grade_report::get_pref('decimalpoints', $item->id);

} else {
    $item = new grade_item(array('courseid'=>$courseid, 'itemtype'=>'manual'));
    // Get Item preferences
    $item->pref_gradedisplaytype = grade_report::get_pref('gradedisplaytype');
    $item->pref_decimalpoints    = grade_report::get_pref('decimalpoints');

    $decimalpoints = grade_report::get_pref('decimalpoints');
}

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
$item->aggregationcoef = format_float($item->aggregationcoef, 4);

$mform->set_data($item);

if ($data = $mform->get_data(false)) {
    if (array_key_exists('calculation', $data)) {
        $data->calculation = grade_item::normalize_formula($data->calculation, $course->id);
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

    if (empty($grade_item->id)) {
        $grade_item->itemtype = 'manual'; // all new items to be manual only
        $grade_item->insert();

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

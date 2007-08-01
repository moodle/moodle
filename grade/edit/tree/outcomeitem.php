<?php  //$Id$

require_once '../../../config.php';
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/lib.php';
require_once 'outcomeitem_form.php';

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

$mform = new edit_outcomeitem_form(null, array('gpr'=>$gpr));

if ($mform->is_cancelled() || empty($CFG->enableoutcomes)) {
    redirect($returnurl);
}

if ($item = get_record('grade_items', 'id', $id, 'courseid', $course->id)) {
    // redirect if outcomeid present
    if (empty($item->outcomeid)) {
        $url = $CFG->wwwroot.'/grade/edit/tree/outcome.php?id='.$id.'&amp;courseid='.$courseid;
        redirect($gpr->add_url_params($url));
    }

//    $item->calculation = grade_item::denormalize_formula($item->calculation, $course->id);

    if ($item->itemtype == 'mod') {
        $cm = get_coursemodule_from_instance($item->itemmodule, $item->iteminstance, $item->courseid);
        $item->cmid = $cm->id;
    } else {
        $item->cmid = 0;
    }

    $mform->set_data($item);
}

if ($data = $mform->get_data(false)) {
    if (array_key_exists('calculation', $data)) {
//        $data->calculation = grade_item::normalize_formula($data->calculation, $course->id);
    }

    $grade_item = new grade_item(array('id'=>$id, 'courseid'=>$courseid));
    grade_item::set_properties($grade_item, $data);

    // fix activity links
    if (empty($data->cmid)) {
        // manual item
        $grade_item->itemtype     = 'manual';
        $grade_item->itemmodule   = null;
        $grade_item->iteminstance = null;
        $grade_item->itemnumber   = 0;

    } else {
        $module = get_record_sql("SELECT cm.*, m.name as modname
                                    FROM {$CFG->prefix}modules m, {$CFG->prefix}course_modules cm
                                   WHERE cm.id = {$data->cmid} AND cm.module = m.id ");
        $grade_item->itemtype     = 'mod';
        $grade_item->itemmodule   = $module->modname;
        $grade_item->iteminstance = $module->instance;

        if ($items = grade_item::fetch_all(array('itemtype'=>'mod', 'itemmodule'=>$grade_item->itemmodule,
                                           'iteminstance'=>$grade_item->iteminstance, 'courseid'=>$COURSE->id))) {
            if (!empty($grade_item->id) and in_array($grade_item, $items)) {
                //no change needed
            } else {
                $max = 999;
                foreach($items as $item) {
                    if (empty($item->outcomeid)) {
                        continue;
                    }
                    if ($item->itemnumber > $max) {
                        $max = $item->itemnumber;
                    }
                }
                $grade_item->itemnumber = $max + 1;
            }
        } else {
            $grade_item->itemnumber = 1000;
        }
    }

    // fix scale used
    $outcome = grade_outcome::fetch(array('id'=>$data->outcomeid));
    $grade_item->gradetype = GRADE_TYPE_SCALE;
    $grade_item->scaleid = $outcome->scaleid; //TODO: we might recalculate existing outcome grades when changing scale

    if (empty($grade_item->id)) {
        $grade_item->insert();
        // move next to activity if adding linked outcome
        if ($grade_item->itemtype == 'mod') {
            if ($item = grade_item::fetch(array('itemtype'=>'mod', 'itemmodule'=>$grade_item->itemmodule,
                         'iteminstance'=>$grade_item->iteminstance, 'itemnumber'=>0, 'courseid'=>$COURSE->id))) {
                $grade_item->set_parent($item->categoryid);
                $grade_item->move_after_sortorder($item->sortorder);
            }
        }

    } else {
        $grade_item->update();
    }

    redirect($returnurl);
}

$strgrades       = get_string('grades');
$strgraderreport = get_string('graderreport', 'grades');
$stroutcomesedit = get_string('outcomeitemsedit', 'grades');

$nav = array(array('name'=>$strgrades,'link'=>$CFG->wwwroot.'/grade/index.php?id='.$courseid, 'type'=>'misc'),
             array('name'=>$stroutcomesedit, 'link'=>'', 'type'=>'misc'));

$navigation = build_navigation($nav);


print_header_simple($strgrades . ': ' . $strgraderreport, ': ' . $stroutcomesedit, $navigation, '', '', true, '', navmenu($course));

$mform->display();

print_footer($course);

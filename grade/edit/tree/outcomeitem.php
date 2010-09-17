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
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/lib.php';
require_once 'outcomeitem_form.php';

$courseid = required_param('courseid', PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);

$url = new moodle_url('/grade/edit/tree/outcomeitem.php', array('courseid'=>$courseid));
if ($id !== 0) {
    $url->param('id', $id);
}
$PAGE->set_url($url);

if (!$course = $DB->get_record('course', array('id' => $courseid))) {
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

$heading = get_string('outcomeitemsedit', 'grades');

if ($grade_item = grade_item::fetch(array('id'=>$id, 'courseid'=>$courseid))) {
    // redirect if outcomeid present
    if (empty($grade_item->outcomeid)) {
        $url = $CFG->wwwroot.'/grade/edit/tree/item.php?id='.$id.'&amp;courseid='.$courseid;
        redirect($gpr->add_url_params($url));
    }
    $item = $grade_item->get_record_data();

    $parent_category = $grade_item->get_parent_category();
    $item->parentcategory = $parent_category->id;

    if ($item->itemtype == 'mod') {
        $cm = get_coursemodule_from_instance($item->itemmodule, $item->iteminstance, $item->courseid);
        $item->cmid = $cm->id;
    } else {
        $item->cmid = 0;
    }

} else {
    $heading = get_string('newoutcomeitem', 'grades');
    $grade_item = new grade_item(array('courseid'=>$courseid, 'itemtype'=>'manual'), false);
    $item = $grade_item->get_record_data();
    $item->cmid = 0;
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

$item->gradepass       = format_float($item->gradepass, $decimalpoints);

if (empty($parent_category)) {
    $item->aggregationcoef = 0;
} else if ($parent_category->aggregation == GRADE_AGGREGATE_SUM) {
    $item->aggregationcoef = $item->aggregationcoef > 0 ? 1 : 0;
} else {
    $item->aggregationcoef = format_float($item->aggregationcoef, 4);
}

$mform->set_data($item);


if ($data = $mform->get_data()) {

    if (!isset($data->aggregationcoef)) {
        $data->aggregationcoef = 0;
    }

    if (property_exists($data, 'calculation')) {
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

    $convert = array('gradepass', 'aggregationcoef');
    foreach ($convert as $param) {
        if (property_exists($data, $param)) {
            $data->$param = unformat_float($data->$param);
        }
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
        $params = array($data->cmid);
        $module = $DB->get_record_sql("SELECT cm.*, m.name as modname
                                    FROM {modules} m, {course_modules} cm
                                   WHERE cm.id = ? AND cm.module = m.id ", $params);
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
        } else {
            // set parent if needed
            if (isset($data->parentcategory)) {
                $grade_item->set_parent($data->parentcategory, 'gradebook');
            }
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

print_grade_page_head($courseid, 'edittree', null, $heading);

if (!grade_outcome::fetch_all_available($COURSE->id)) {
    echo $OUTPUT->confirm(get_string('nooutcomes', 'grades'), $CFG->wwwroot.'/grade/edit/outcome/course.php?id='.$courseid, $returnurl);
    echo $OUTPUT->footer();
    die();
}

$mform->display();

echo $OUTPUT->footer();

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
require_once 'grade_form.php';

$courseid = required_param('courseid', PARAM_INT);
$id       = optional_param('id', 0, PARAM_INT);
$itemid   = optional_param('itemid', 0, PARAM_INT);
$userid   = optional_param('userid', 0, PARAM_INT);

if (!$course = get_record('course', 'id', $courseid)) {
    print_error('nocourseid');
}

require_login($course);
$context = get_context_instance(CONTEXT_COURSE, $course->id);
if (!has_capability('moodle/grade:manage', $context)) {
    require_capability('moodle/grade:edit', $context);
}

// default return url
$gpr = new grade_plugin_return();
$returnurl = $gpr->get_return_url($CFG->wwwroot.'/grade/report.php?id='.$course->id);

// security checks!
if (!empty($id)) {
    if (!$grade = get_record('grade_grades', 'id', $id)) {
        error('Incorrect grade id');
    }

    if (!empty($itemid) and $itemid != $grade->itemid) {
        error('Incorrect itemid');
    }
    $itemid = $grade->itemid;

    if (!empty($userid) and $userid != $grade->userid) {
        error('Incorrect userid');
    }
    $userid = $grade->userid;

    unset($grade);

} else if (empty($userid) or empty($itemid)) {
    error('Missing userid and itemid');
}

if (!$grade_item = grade_item::fetch(array('id'=>$itemid, 'courseid'=>$courseid))) {
    error('Can not find grade_item');
}

// now verify grading user has access to all groups or is member of the same group when separate groups used in course
if (groups_get_course_groupmode($COURSE) == SEPARATEGROUPS and !has_capability('moodle/site:accessallgroups', $context)) {
    if ($groups = groups_get_all_groups($COURSE->id, $userid)) {
        $ok = false;
        foreach ($groups as $group) {
            if (groups_is_member($group->id, $USER->id)) {
                $ok = true;
            }
        }
        if (!$ok) {
            error('Can not grade this user');
        }
    } else {
        error('Can not grade this user');
    }
}

$mform = new edit_grade_form(null, array('grade_item'=>$grade_item, 'gpr'=>$gpr));

if ($grade = get_record('grade_grades', 'itemid', $grade_item->id, 'userid', $userid)) {

    // always clean existing feedback - grading should not have XSS risk
    if (can_use_html_editor()) {
        if (empty($grade->feedback)) {
            $grade->feedback  = '';
        } else {
            $options = new object();
            $options->smiley  = false;
            $options->filter  = false;
            $options->noclean = false;
            $grade->feedback  = format_text($grade->feedback, $grade->feedbackformat, $options);
        }
        $grade->feedbackformat = FORMAT_HTML;
    } else {
        $grade->feedback       = clean_text($grade->feedback, $grade->feedbackformat);
    }

    $grade->locked      = $grade->locked     > 0 ? 1:0;
    $grade->overridden  = $grade->overridden > 0 ? 1:0;
    $grade->excluded    = $grade->excluded   > 0 ? 1:0;

    if ($grade->hidden > 1) {
        $grade->hiddenuntil = $grade->hidden;
        $grade->hidden = 1;
    } else {
        $grade->hiddenuntil = 0;
    }

    if ($grade_item->is_hidden()) {
        $grade->hidden = 1;
    }

    if ($grade_item->is_locked()) {
        $grade->locked = 1;
    }

    // normalize the final grade value
    if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
        if (empty($grade->finalgrade)) {
            $grade->finalgrade = -1;
        } else {
            $grade->finalgrade = (int)$grade->finalgrade;
        }
    } else if ($grade_item->gradetype == GRADE_TYPE_VALUE) {
        $grade->finalgrade = format_float($grade->finalgrade, $grade_item->get_decimals());
    }

    $grade->oldgrade    = $grade->finalgrade;
    $grade->oldfeedback = $grade->feedback;

    $mform->set_data($grade);

} else {
    $mform->set_data(array('itemid'=>$itemid, 'userid'=>$userid, 'locked'=>$grade_item->locked, 'locktime'=>$grade_item->locktime));
}

if ($mform->is_cancelled()) {
    redirect($returnurl);

// form processing
} else if ($data = $mform->get_data(false)) {
    $old_grade_grade = new grade_grade(array('userid'=>$data->userid, 'itemid'=>$grade_item->id), true); //might not exist yet

    // fix no grade for scales
    if (!isset($data->finalgrade) or $data->finalgrade == $data->oldgrade) {
        $data->finalgrade = $old_grade_grade->finalgrade;

    } else if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
        if ($data->finalgrade < 1) {
            $data->finalgrade = NULL;
        }

    } else if ($grade_item->gradetype == GRADE_TYPE_VALUE) {
        $data->finalgrade = unformat_float($data->finalgrade);

    } else {
        //this shoul not happen
        $data->finalgrade = $old_grade_grade->finalgrade;
    }

    // the overriding of feedback is tricky - we have to care about external items only
    if (!array_key_exists('feedback', $data) or $data->feedback == $data->oldfeedback) {
        $data->feedback       = $old_grade_grade->feedback;
        $data->feedbackformat = $old_grade_grade->feedbackformat;
    }
    // update final grade or feedback
    $grade_item->update_final_grade($data->userid, $data->finalgrade, 'editgrade', $data->feedback, $data->feedbackformat);

    $grade_grade = new grade_grade(array('userid'=>$data->userid, 'itemid'=>$grade_item->id), true);
    $grade_grade->grade_item =& $grade_item; // no db fetching

    if (has_capability('moodle/grade:manage', $context) or has_capability('moodle/grade:edit', $context)) {
        if (!grade_floats_different($data->finalgrade, $old_grade_grade->finalgrade)
          and $data->feedback === $old_grade_grade->feedback) {
            // change overridden flag only if grade or feedback not changed
            if (!isset($data->overridden)) {
                $data->overridden = 0; // checkbox
            }
            $grade_grade->set_overridden($data->overridden);
        }
    }

    if (has_capability('moodle/grade:manage', $context) or has_capability('moodle/grade:hide', $context)) {
        $hidden      = empty($data->hidden) ? 0: $data->hidden;
        $hiddenuntil = empty($data->hiddenuntil) ? 0: $data->hiddenuntil;

        if ($grade_item->is_hidden()) {
            if ($old_grade_grade->hidden == 1 and $hiddenuntil == 0) {
                //nothing to do - grade was originally hidden, we want to keep it that way
            } else {
                $grade_grade->set_hidden($hiddenuntil);
            }
        } else {
            if ($hiddenuntil) {
                $grade_grade->set_hidden($hiddenuntil);
            } else {
                $grade_grade->set_hidden($hidden); // checkbox data might be undefined
            }
        }
    }

    if (isset($data->locked) and !$grade_item->is_locked()) {
        if (($old_grade_grade->locked or $old_grade_grade->locktime)
          and (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:unlock', $context))) {
            //ignore data

        } else if ((!$old_grade_grade->locked and !$old_grade_grade->locktime)
          and (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:lock', $context))) {
            //ignore data

        } else {
            $grade_grade->set_locktime($data->locktime); //set_lock may reset locktime
            $grade_grade->set_locked($data->locked, false, true);
            // reload grade in case it was regraded from activity
            $grade_grade = new grade_grade(array('userid'=>$data->userid, 'itemid'=>$grade_item->id), true);
            $grade_grade->grade_item =& $grade_item; // no db fetching
        }
    }

    if (isset($data->excluded) and has_capability('moodle/grade:manage', $context)) {
        $grade_grade->set_excluded($data->excluded);
    }

    // detect cases when we need to do full regrading
    if ($old_grade_grade->excluded != $grade_grade->excluded) {
        $parent = $grade_item->get_parent_category();
        $parent->force_regrading();

    } else if ($old_grade_grade->overridden != $grade_grade->overridden and empty($grade_grade->overridden)) { // only when unoverriding
        $grade_item->force_regrading();

    } else if ($old_grade_grade->locktime != $grade_grade->locktime) {
        $grade_item->force_regrading();
    }

    redirect($returnurl);
}

$strgrades       = get_string('grades');
$strgraderreport = get_string('graderreport', 'grades');
$strgradeedit    = get_string('editgrade', 'grades');
$struser         = get_string('user');

$navigation = grade_build_nav(__FILE__, $strgradeedit, array('courseid' => $courseid));

/*********** BEGIN OUTPUT *************/

print_header_simple($strgrades . ': ' . $strgraderreport . ': ' . $strgradeedit,
    ': ' . $strgradeedit , $navigation, '', '', true, '', navmenu($course));

print_heading($strgradeedit);

print_simple_box_start("center");

// Form if in edit or add modes
$mform->display();

print_simple_box_end();

print_footer($course);
die;

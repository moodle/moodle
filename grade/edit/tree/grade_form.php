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

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class edit_grade_form extends moodleform {

    function definition() {
        global $CFG, $COURSE, $DB;

        $mform =& $this->_form;

        $grade_item = $this->_customdata['grade_item'];
        $gpr        = $this->_customdata['gpr'];

        if ($grade_item->is_course_item()) {
            $grade_category = null;
        } else if ($grade_item->is_category_item()) {
            $grade_category = $grade_item->get_item_category();
            $grade_category = $grade_category->get_parent_category();
        } else {
            $grade_category = $grade_item->get_parent_category();
        }

        /// information fields
        $mform->addElement('static', 'user', get_string('user'));
        $mform->addElement('static', 'itemname', get_string('itemname', 'grades'));

        $mform->addElement('checkbox', 'overridden', get_string('overridden', 'grades'));
        $mform->addHelpButton('overridden', 'overridden', 'grades');

        /// actual grade - numeric or scale
        if ($grade_item->gradetype == GRADE_TYPE_VALUE) {
            // numeric grade
            $mform->addElement('text', 'finalgrade', get_string('finalgrade', 'grades'));
            $mform->addHelpButton('finalgrade', 'finalgrade', 'grades');
            $mform->disabledIf('finalgrade', 'overridden', 'notchecked');

        } else if ($grade_item->gradetype == GRADE_TYPE_SCALE) {
            // scale grade
            $scaleopt = array();

            if (empty($grade_item->outcomeid)) {
                $scaleopt[-1] = get_string('nograde');
            } else {
                $scaleopt[-1] = get_string('nooutcome', 'grades');
            }

            $i = 1;
            if ($scale = $DB->get_record('scale', array('id' => $grade_item->scaleid))) {
                foreach (explode(",", $scale->scale) as $option) {
                    $scaleopt[$i] = $option;
                    $i++;
                }
            }

            $mform->addElement('select', 'finalgrade', get_string('finalgrade', 'grades'), $scaleopt);
            $mform->addHelpButton('finalgrade', 'finalgrade', 'grades');
            $mform->disabledIf('finalgrade', 'overridden', 'notchecked');
        }

        if ($grade_category and $grade_category->aggregation == GRADE_AGGREGATE_SUM) {
            $mform->addElement('advcheckbox', 'excluded', get_string('excluded', 'grades'), '<small>('.get_string('warningexcludedsum', 'grades').')</small>');
        } else {
            $mform->addElement('advcheckbox', 'excluded', get_string('excluded', 'grades'));
        }
        $mform->addHelpButton('excluded', 'excluded', 'grades');

        /// hiding
        /// advcheckbox is not compatible with disabledIf !!
        $mform->addElement('checkbox', 'hidden', get_string('hidden', 'grades'));
        $mform->addHelpButton('hidden', 'hidden', 'grades');
        $mform->addElement('date_time_selector', 'hiddenuntil', get_string('hiddenuntil', 'grades'), array('optional'=>true));
        $mform->disabledIf('hidden', 'hiddenuntil[off]', 'notchecked');

        /// locking
        $mform->addElement('advcheckbox', 'locked', get_string('locked', 'grades'));
        $mform->addHelpButton('locked', 'locked', 'grades');
        $mform->addElement('date_time_selector', 'locktime', get_string('locktime', 'grades'), array('optional'=>true));
        $mform->disabledIf('locktime', 'gradetype', 'eq', GRADE_TYPE_NONE);

        // Feedback format is automatically converted to html if user has enabled editor
        $feedbackoptions = array('maxfiles'=>0, 'maxbytes'=>0); //TODO: no files here for now, if ever gets implemented use component 'grade' and filearea 'feedback'
        $mform->addElement('editor', 'feedback', get_string('feedback', 'grades'), null, $feedbackoptions);
        $mform->addHelpButton('feedback', 'feedback', 'grades');
        $mform->setType('text', PARAM_RAW); // to be cleaned before display, no XSS risk

        // hidden params
        $mform->addElement('hidden', 'oldgrade');
        $mform->setType('oldgrade', PARAM_RAW);
        $mform->addElement('hidden', 'oldfeedback');
        $mform->setType('oldfeedback', PARAM_RAW);

        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'itemid', 0);
        $mform->setType('itemid', PARAM_INT);

        $mform->addElement('hidden', 'userid', 0);
        $mform->setType('userid', PARAM_INT);

        $mform->addElement('hidden', 'courseid', $COURSE->id);
        $mform->setType('courseid', PARAM_INT);

/// add return tracking info
        $gpr->add_mform_elements($mform);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

    function definition_after_data() {
        global $CFG, $COURSE, $DB;

        $context = get_context_instance(CONTEXT_COURSE, $COURSE->id);

        $mform =& $this->_form;
        $grade_item = $this->_customdata['grade_item'];

        // fill in user name if user still exists
        $userid = $mform->getElementValue('userid');
        if ($user = $DB->get_record('user', array('id' => $userid))) {
            $username = '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$userid.'">'.fullname($user).'</a>';
            $user_el =& $mform->getElement('user');
            $user_el->setValue($username);
        }

        // add activity name + link
        if ($grade_item->itemtype == 'mod') {
            $cm = get_coursemodule_from_instance($grade_item->itemmodule, $grade_item->iteminstance, $grade_item->courseid);
            $itemname = '<a href="'.$CFG->wwwroot.'/mod/'.$grade_item->itemmodule.'/view.php?id='.$cm->id.'">'.$grade_item->get_name().'</a>';
        } else {
            $itemname = $grade_item->get_name();
        }
        $itemname_el =& $mform->getElement('itemname');
        $itemname_el->setValue($itemname);

        // access control - disable not allowed elements
        if (!has_capability('moodle/grade:manage', $context)) {
            $mform->hardFreeze('excluded');
        }

        if (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:hide', $context)) {
            $mform->hardFreeze('hidden');
            $mform->hardFreeze('hiddenuntil');
        }

        $old_grade_grade = new grade_grade(array('itemid'=>$grade_item->id, 'userid'=>$userid));

        if (!$grade_item->is_overridable_item()) {
            $mform->removeElement('overridden');
        }

        if ($grade_item->is_hidden()) {
            $mform->hardFreeze('hidden');
        }

        if ($old_grade_grade->is_locked()) {
            if ($grade_item->is_locked()) {
                $mform->hardFreeze('locked');
                $mform->hardFreeze('locktime');
            }

            $mform->hardFreeze('overridden');
            $mform->hardFreeze('finalgrade');
            $mform->hardFreeze('feedback');

        } else {
            if (empty($old_grade_grade->id)) {
                $old_grade_grade->locked = $grade_item->locked;
                $old_grade_grade->locktime = $grade_item->locktime;
            }

            if (($old_grade_grade->locked or $old_grade_grade->locktime)
              and (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:unlock', $context))) {
                $mform->hardFreeze('locked');
                $mform->hardFreeze('locktime');

            } else if ((!$old_grade_grade->locked and !$old_grade_grade->locktime)
              and (!has_capability('moodle/grade:manage', $context) and !has_capability('moodle/grade:lock', $context))) {
                $mform->hardFreeze('locked');
                $mform->hardFreeze('locktime');
            }
        }
    }
}



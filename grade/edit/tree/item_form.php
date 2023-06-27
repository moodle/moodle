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

/**
 * A moodleform allowing the editing of the grade options for an individual grade item
 *
 * @package   core_grades
 * @copyright 2007 Petr Skoda
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class edit_item_form extends moodleform {
    private $displayoptions;

    function definition() {
        global $COURSE, $CFG, $DB;

        $mform =& $this->_form;

        $item = $this->_customdata['current'];

/// visible elements
        $mform->addElement('header', 'general', get_string('gradeitem', 'grades'));

        $mform->addElement('text', 'itemname', get_string('itemname', 'grades'));
        $mform->setType('itemname', PARAM_TEXT);
        $mform->addElement('text', 'iteminfo', get_string('iteminfo', 'grades'));
        $mform->addHelpButton('iteminfo', 'iteminfo', 'grades');
        $mform->setType('iteminfo', PARAM_TEXT);

        $mform->addElement('text', 'idnumber', get_string('idnumbermod'));
        $mform->addHelpButton('idnumber', 'idnumbermod');
        $mform->setType('idnumber', PARAM_RAW);

        if (!empty($item->id)) {
            $gradeitem = new grade_item(array('id' => $item->id, 'courseid' => $item->courseid));
            // If grades exist set a message so the user knows why they can not alter the grade type or scale.
            // We could never change the grade type for external items, so only need to show this for manual grade items.
            if ($gradeitem->has_grades() && !$gradeitem->is_external_item()) {
                // Set a message so the user knows why they can not alter the grade type or scale.
                if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
                    $gradesexistmsg = get_string('modgradecantchangegradetyporscalemsg', 'grades');
                } else {
                    $gradesexistmsg = get_string('modgradecantchangegradetypemsg', 'grades');
                }

                $gradesexisthtml = '<div class=\'alert\'>' . $gradesexistmsg . '</div>';
                $mform->addElement('static', 'gradesexistmsg', '', $gradesexisthtml);
            }
        }

        // Manual grade items cannot have grade type GRADE_TYPE_NONE.
        $options = array(GRADE_TYPE_VALUE => get_string('typevalue', 'grades'),
                         GRADE_TYPE_SCALE => get_string('typescale', 'grades'),
                         GRADE_TYPE_TEXT => get_string('typetext', 'grades'));

        $mform->addElement('select', 'gradetype', get_string('gradetype', 'grades'), $options);
        $mform->addHelpButton('gradetype', 'gradetype', 'grades');
        $mform->setDefault('gradetype', GRADE_TYPE_VALUE);

        //$mform->addElement('text', 'calculation', get_string('calculation', 'grades'));
        //$mform->disabledIf('calculation', 'gradetype', 'eq', GRADE_TYPE_TEXT);
        //$mform->disabledIf('calculation', 'gradetype', 'eq', GRADE_TYPE_NONE);

        $options = array(0=>get_string('usenoscale', 'grades'));
        if ($scales = grade_scale::fetch_all_local($COURSE->id)) {
            foreach ($scales as $scale) {
                $options[$scale->id] = $scale->get_name();
            }
        }
        if ($scales = grade_scale::fetch_all_global()) {
            foreach ($scales as $scale) {
                $options[$scale->id] = $scale->get_name();
            }
        }
        // ugly BC hack - it was possible to use custom scale from other courses :-(
        if (!empty($item->scaleid) and !isset($options[$item->scaleid])) {
            if ($scale = grade_scale::fetch(array('id'=>$item->scaleid))) {
                $options[$scale->id] = $scale->get_name().get_string('incorrectcustomscale', 'grades');
            }
        }
        $mform->addElement('select', 'scaleid', get_string('scale'), $options);
        $mform->addHelpButton('scaleid', 'typescale', 'grades');
        $mform->disabledIf('scaleid', 'gradetype', 'noteq', GRADE_TYPE_SCALE);

        $choices = array();
        $choices[''] = get_string('choose');
        $choices['no'] = get_string('no');
        $choices['yes'] = get_string('yes');
        $mform->addElement('select', 'rescalegrades', get_string('modgraderescalegrades', 'grades'), $choices);
        $mform->addHelpButton('rescalegrades', 'modgraderescalegrades', 'grades');
        $mform->disabledIf('rescalegrades', 'gradetype', 'noteq', GRADE_TYPE_VALUE);

        $mform->addElement('float', 'grademax', get_string('grademax', 'grades'));
        $mform->addHelpButton('grademax', 'grademax', 'grades');
        $mform->disabledIf('grademax', 'gradetype', 'noteq', GRADE_TYPE_VALUE);

        if ((bool) get_config('moodle', 'grade_report_showmin')) {
            $mform->addElement('float', 'grademin', get_string('grademin', 'grades'));
            $mform->addHelpButton('grademin', 'grademin', 'grades');
            $mform->disabledIf('grademin', 'gradetype', 'noteq', GRADE_TYPE_VALUE);
        }

        $mform->addElement('float', 'gradepass', get_string('gradepass', 'grades'));
        $mform->addHelpButton('gradepass', 'gradepass', 'grades');
        $mform->disabledIf('gradepass', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('gradepass', 'gradetype', 'eq', GRADE_TYPE_TEXT);

        $mform->addElement('float', 'multfactor', get_string('multfactor', 'grades'));
        $mform->addHelpButton('multfactor', 'multfactor', 'grades');
        $mform->disabledIf('multfactor', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('multfactor', 'gradetype', 'eq', GRADE_TYPE_TEXT);

        $mform->addElement('float', 'plusfactor', get_string('plusfactor', 'grades'));
        $mform->addHelpButton('plusfactor', 'plusfactor', 'grades');
        $mform->disabledIf('plusfactor', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('plusfactor', 'gradetype', 'eq', GRADE_TYPE_TEXT);

        /// grade display prefs
        $default_gradedisplaytype = grade_get_setting($COURSE->id, 'displaytype', $CFG->grade_displaytype);
        $options = array(GRADE_DISPLAY_TYPE_DEFAULT            => get_string('default', 'grades'),
                         GRADE_DISPLAY_TYPE_REAL               => get_string('real', 'grades'),
                         GRADE_DISPLAY_TYPE_PERCENTAGE         => get_string('percentage', 'grades'),
                         GRADE_DISPLAY_TYPE_LETTER             => get_string('letter', 'grades'),
                         GRADE_DISPLAY_TYPE_REAL_PERCENTAGE    => get_string('realpercentage', 'grades'),
                         GRADE_DISPLAY_TYPE_REAL_LETTER        => get_string('realletter', 'grades'),
                         GRADE_DISPLAY_TYPE_LETTER_REAL        => get_string('letterreal', 'grades'),
                         GRADE_DISPLAY_TYPE_LETTER_PERCENTAGE  => get_string('letterpercentage', 'grades'),
                         GRADE_DISPLAY_TYPE_PERCENTAGE_LETTER  => get_string('percentageletter', 'grades'),
                         GRADE_DISPLAY_TYPE_PERCENTAGE_REAL    => get_string('percentagereal', 'grades')
                         );

        asort($options);

        foreach ($options as $key=>$option) {
            if ($key == $default_gradedisplaytype) {
                $options[GRADE_DISPLAY_TYPE_DEFAULT] = get_string('defaultprev', 'grades', $option);
                break;
            }
        }
        $mform->addElement('select', 'display', get_string('gradedisplaytype', 'grades'), $options);
        $mform->addHelpButton('display', 'gradedisplaytype', 'grades');
        $mform->disabledIf('display', 'gradetype', 'eq', GRADE_TYPE_TEXT);

        $default_gradedecimals = grade_get_setting($COURSE->id, 'decimalpoints', $CFG->grade_decimalpoints);
        $options = array(-1=>get_string('defaultprev', 'grades', $default_gradedecimals), 0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5);
        $mform->addElement('select', 'decimals', get_string('decimalpoints', 'grades'), $options);
        $mform->addHelpButton('decimals', 'decimalpoints', 'grades');
        $mform->setDefault('decimals', -1);
        $mform->disabledIf('decimals', 'display', 'eq', GRADE_DISPLAY_TYPE_LETTER);
        if ($default_gradedisplaytype == GRADE_DISPLAY_TYPE_LETTER) {
            $mform->disabledIf('decimals', 'display', "eq", GRADE_DISPLAY_TYPE_DEFAULT);
        }
        $mform->disabledIf('decimals', 'gradetype', 'eq', GRADE_TYPE_TEXT);

        /// hiding
        if ($item->cancontrolvisibility) {
            $mform->addElement('advcheckbox', 'hidden', get_string('hidden', 'grades'), '', [], [0, 1]);
            $mform->addElement('date_time_selector', 'hiddenuntil', get_string('hiddenuntil', 'grades'), array('optional'=>true));
            $mform->disabledIf('hidden', 'hiddenuntil[enabled]', 'checked');
        } else {
            $mform->addElement('static', 'hidden', get_string('hidden', 'grades'),
                    get_string('componentcontrolsvisibility', 'grades'));
            // Unset hidden to avoid data override.
            unset($item->hidden);
        }
        $mform->addHelpButton('hidden', 'hidden', 'grades');

        /// locking
        $mform->addElement('advcheckbox', 'locked', get_string('locked', 'grades'));
        $mform->addHelpButton('locked', 'locked', 'grades');

        $mform->addElement('date_time_selector', 'locktime', get_string('locktime', 'grades'), array('optional'=>true));
        $mform->disabledIf('locktime', 'gradetype', 'eq', GRADE_TYPE_NONE);

/// parent category related settings
        $mform->addElement('header', 'headerparent', get_string('parentcategory', 'grades'));

        $mform->addElement('advcheckbox', 'weightoverride', get_string('adjustedweight', 'grades'));
        $mform->addHelpButton('weightoverride', 'weightoverride', 'grades');
        $mform->disabledIf('weightoverride', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('weightoverride', 'gradetype', 'eq', GRADE_TYPE_TEXT);

        $mform->addElement('float', 'aggregationcoef2', get_string('weight', 'grades'));
        $mform->addHelpButton('aggregationcoef2', 'weight', 'grades');
        $mform->disabledIf('aggregationcoef2', 'weightoverride');
        $mform->disabledIf('aggregationcoef2', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('aggregationcoef2', 'gradetype', 'eq', GRADE_TYPE_TEXT);

        $options = array();
        $coefstring = '';
        $categories = grade_category::fetch_all(array('courseid'=>$COURSE->id));

        foreach ($categories as $cat) {
            $cat->apply_forced_settings();
            $options[$cat->id] = $cat->get_name();
        }

        if (count($categories) > 1) {
            $mform->addElement('select', 'parentcategory', get_string('gradecategory', 'grades'), $options);
        }

/// hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', $COURSE->id);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'itemtype', 'manual'); // all new items are manual only
        $mform->setType('itemtype', PARAM_ALPHA);

/// add return tracking info
        $gpr = $this->_customdata['gpr'];
        $gpr->add_mform_elements($mform);
//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
//-------------------------------------------------------------------------------
        $this->set_data($item);
    }


/// tweak the form - depending on existing data
    function definition_after_data() {
        global $CFG, $COURSE;

        $mform =& $this->_form;

        if ($id = $mform->getElementValue('id')) {
            $gradeitem = grade_item::fetch(array('id' => $id));
            $parentcategory = $gradeitem->get_parent_category();
        } else {
            // If we do not have an id, we are creating a new grade item.
            $gradeitem = new grade_item(array('courseid' => $COURSE->id, 'itemtype' => 'manual'), false);

            // Assign the course category to this grade item.
            $parentcategory = grade_category::fetch_course_category($COURSE->id);
            $gradeitem->parent_category = $parentcategory;
        }

        if (!$gradeitem->is_raw_used()) {
            $mform->removeElement('plusfactor');
            $mform->removeElement('multfactor');
        }

        if ($gradeitem->is_outcome_item()) {
            // We have to prevent incompatible modifications of outcomes if outcomes disabled.
            $mform->removeElement('grademax');
            if ($mform->elementExists('grademin')) {
                $mform->removeElement('grademin');
            }
            $mform->removeElement('gradetype');
            $mform->removeElement('display');
            $mform->removeElement('decimals');
            $mform->hardFreeze('scaleid');

        } else {
            if ($gradeitem->is_external_item()) {
                // Following items are set up from modules and should not be overrided by user.
                if ($mform->elementExists('grademin')) {
                    // The site setting grade_report_showmin may have prevented grademin being added to the form.
                    $mform->hardFreeze('grademin');
                }
                $mform->hardFreeze('itemname,gradetype,grademax,scaleid');
                if ($gradeitem->itemnumber == 0) {
                    // The idnumber of grade itemnumber 0 is synced with course_modules.
                    $mform->hardFreeze('idnumber');
                }

                // For external items we can not change the grade type, even if no grades exist, so if it is set to
                // scale, then remove the grademax and grademin fields from the form - no point displaying them.
                if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
                    $mform->removeElement('grademax');
                    if ($mform->elementExists('grademin')) {
                        $mform->removeElement('grademin');
                    }
                } else { // Not using scale, so remove it.
                    $mform->removeElement('scaleid');
                }

                // Always remove the rescale grades element if it's an external item.
                $mform->removeElement('rescalegrades');
            } else if ($gradeitem->has_grades()) {
                // Can't change the grade type or the scale if there are grades.
                $mform->hardFreeze('gradetype, scaleid');

                // If we are using scales then remove the unnecessary rescale and grade fields.
                if ($gradeitem->gradetype == GRADE_TYPE_SCALE) {
                    $mform->removeElement('rescalegrades');
                    $mform->removeElement('grademax');
                    if ($mform->elementExists('grademin')) {
                        $mform->removeElement('grademin');
                    }
                } else { // Remove the scale field.
                    $mform->removeElement('scaleid');
                    // Set the maximum grade to disabled unless a grade is chosen.
                    $mform->disabledIf('grademax', 'rescalegrades', 'eq', '');
                }
            } else {
                // Remove the rescale element if there are no grades.
                $mform->removeElement('rescalegrades');
            }
        }

        // If we wanted to change parent of existing item - we would have to verify there are no circular references in parents!!!
        if ($id && $mform->elementExists('parentcategory')) {
            $mform->hardFreeze('parentcategory');
        }

        $parentcategory->apply_forced_settings();

        if (!$parentcategory->is_aggregationcoef_used()) {
            if ($mform->elementExists('aggregationcoef')) {
                $mform->removeElement('aggregationcoef');
            }

        } else {
            $coefstring = $gradeitem->get_coefstring();

            if ($coefstring !== '') {
                if ($coefstring == 'aggregationcoefextrasum' || $coefstring == 'aggregationcoefextraweightsum') {
                    // The advcheckbox is not compatible with disabledIf!
                    $coefstring = 'aggregationcoefextrasum';
                    $element =& $mform->createElement('checkbox', 'aggregationcoef', get_string($coefstring, 'grades'));
                } else {
                    $element =& $mform->createElement('text', 'aggregationcoef', get_string($coefstring, 'grades'));
                }
                if ($mform->elementExists('parentcategory')) {
                    $mform->insertElementBefore($element, 'parentcategory');
                } else {
                    $mform->insertElementBefore($element, 'id');
                }
                $mform->addHelpButton('aggregationcoef', $coefstring, 'grades');
            }
            $mform->disabledIf('aggregationcoef', 'gradetype', 'eq', GRADE_TYPE_NONE);
            $mform->disabledIf('aggregationcoef', 'gradetype', 'eq', GRADE_TYPE_TEXT);
            $mform->disabledIf('aggregationcoef', 'parentcategory', 'eq', $parentcategory->id);
        }

        // Remove fields used by natural weighting if the parent category is not using natural weighting.
        // Or if the item is a scale and scales are not used in aggregation.
        if ($parentcategory->aggregation != GRADE_AGGREGATE_SUM
                || (empty($CFG->grade_includescalesinaggregation) && $gradeitem->gradetype == GRADE_TYPE_SCALE)) {
            if ($mform->elementExists('weightoverride')) {
                $mform->removeElement('weightoverride');
            }
            if ($mform->elementExists('aggregationcoef2')) {
                $mform->removeElement('aggregationcoef2');
            }
        }

        if ($category = $gradeitem->get_item_category()) {
            if ($category->aggregation == GRADE_AGGREGATE_SUM) {
                if ($mform->elementExists('gradetype')) {
                    $mform->hardFreeze('gradetype');
                }
                if ($mform->elementExists('grademin')) {
                    $mform->hardFreeze('grademin');
                }
                if ($mform->elementExists('grademax')) {
                    $mform->hardFreeze('grademax');
                }
                if ($mform->elementExists('scaleid')) {
                    $mform->removeElement('scaleid');
                }
            }
        }

        // no parent header for course category
        if (!$mform->elementExists('aggregationcoef') and !$mform->elementExists('parentcategory')) {
            $mform->removeElement('headerparent');
        }
    }

/// perform extra validation before submission
    function validation($data, $files) {
        global $COURSE;
        $grade_item = false;
        if ($data['id']) {
            $grade_item = new grade_item(array('id' => $data['id'], 'courseid' => $data['courseid']));
        }

        $errors = parent::validation($data, $files);

        if (array_key_exists('idnumber', $data)) {
            if ($grade_item) {
                if ($grade_item->itemtype == 'mod') {
                    $cm = get_coursemodule_from_instance($grade_item->itemmodule, $grade_item->iteminstance, $grade_item->courseid);
                } else {
                    $cm = null;
                }
            } else {
                $grade_item = null;
                $cm = null;
            }
            if (!grade_verify_idnumber($data['idnumber'], $COURSE->id, $grade_item, $cm)) {
                $errors['idnumber'] = get_string('idnumbertaken');
            }
        }

        if (array_key_exists('gradetype', $data) and $data['gradetype'] == GRADE_TYPE_SCALE) {
            if (empty($data['scaleid'])) {
                $errors['scaleid'] = get_string('missingscale', 'grades');
            }
        }

        // We need to make all the validations related with grademax and grademin
        // with them being correct floats, keeping the originals unmodified for
        // later validations / showing the form back...
        // TODO: Note that once MDL-73994 is fixed we'll have to re-visit this and
        // adapt the code below to the new values arriving here, without forgetting
        // the special case of empties and nulls.
        $grademax = isset($data['grademax']) ? unformat_float($data['grademax']) : null;
        $grademin = isset($data['grademin']) ? unformat_float($data['grademin']) : null;

        if (!is_null($grademin) and !is_null($grademax)) {
            if ($grademax == $grademin or $grademax < $grademin) {
                $errors['grademin'] = get_string('incorrectminmax', 'grades');
                $errors['grademax'] = get_string('incorrectminmax', 'grades');
            }
        }

        // We do not want the user to be able to change the grade type or scale for this item if grades exist.
        if ($grade_item && $grade_item->has_grades()) {
            // Check that grade type is set - should never not be set unless form has been modified.
            if (!isset($data['gradetype'])) {
                $errors['gradetype'] = get_string('modgradecantchangegradetype', 'grades');
            } else if ($data['gradetype'] !== $grade_item->gradetype) { // Check if we are changing the grade type.
                $errors['gradetype'] = get_string('modgradecantchangegradetype', 'grades');
            } else if ($data['gradetype'] == GRADE_TYPE_SCALE) {
                // Check if we are changing the scale - can't do this when grades exist.
                if (isset($data['scaleid']) && ($data['scaleid'] !== $grade_item->scaleid)) {
                    $errors['scaleid'] = get_string('modgradecantchangescale', 'grades');
                }
            }
        }
        if ($grade_item) {
            if ($grade_item->gradetype == GRADE_TYPE_VALUE) {
                if ((((bool) get_config('moodle', 'grade_report_showmin')) &&
                    grade_floats_different($grademin, $grade_item->grademin)) ||
                    grade_floats_different($grademax, $grade_item->grademax)) {
                    if ($grade_item->has_grades() && empty($data['rescalegrades'])) {
                        $errors['rescalegrades'] = get_string('mustchooserescaleyesorno', 'grades');
                    }
                }
            }
        }

        return $errors;
    }

}


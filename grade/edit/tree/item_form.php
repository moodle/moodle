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

        $mform->addElement('text', 'grademax', get_string('grademax', 'grades'));
        $mform->addHelpButton('grademax', 'grademax', 'grades');
        $mform->disabledIf('grademax', 'gradetype', 'noteq', GRADE_TYPE_VALUE);
        $mform->setType('grademax', PARAM_RAW);

        if ((bool) get_config('moodle', 'grade_report_showmin')) {
            $mform->addElement('text', 'grademin', get_string('grademin', 'grades'));
            $mform->addHelpButton('grademin', 'grademin', 'grades');
            $mform->disabledIf('grademin', 'gradetype', 'noteq', GRADE_TYPE_VALUE);
            $mform->setType('grademin', PARAM_RAW);
        }

        $mform->addElement('text', 'gradepass', get_string('gradepass', 'grades'));
        $mform->addHelpButton('gradepass', 'gradepass', 'grades');
        $mform->disabledIf('gradepass', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('gradepass', 'gradetype', 'eq', GRADE_TYPE_TEXT);
        $mform->setType('gradepass', PARAM_RAW);

        $mform->addElement('text', 'multfactor', get_string('multfactor', 'grades'));
        $mform->addHelpButton('multfactor', 'multfactor', 'grades');
        $mform->setAdvanced('multfactor');
        $mform->disabledIf('multfactor', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('multfactor', 'gradetype', 'eq', GRADE_TYPE_TEXT);
        $mform->setType('multfactor', PARAM_RAW);

        $mform->addElement('text', 'plusfactor', get_string('plusfactor', 'grades'));
        $mform->addHelpButton('plusfactor', 'plusfactor', 'grades');
        $mform->setAdvanced('plusfactor');
        $mform->disabledIf('plusfactor', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('plusfactor', 'gradetype', 'eq', GRADE_TYPE_TEXT);
        $mform->setType('plusfactor', PARAM_RAW);

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

        $default_gradedecimals = grade_get_setting($COURSE->id, 'decimalpoints', $CFG->grade_decimalpoints);
        $options = array(-1=>get_string('defaultprev', 'grades', $default_gradedecimals), 0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5);
        $mform->addElement('select', 'decimals', get_string('decimalpoints', 'grades'), $options);
        $mform->addHelpButton('decimals', 'decimalpoints', 'grades');
        $mform->setDefault('decimals', -1);
        $mform->disabledIf('decimals', 'display', 'eq', GRADE_DISPLAY_TYPE_LETTER);
        if ($default_gradedisplaytype == GRADE_DISPLAY_TYPE_LETTER) {
            $mform->disabledIf('decimals', 'display', "eq", GRADE_DISPLAY_TYPE_DEFAULT);
        }

        /// hiding
        if ($item->cancontrolvisibility) {
            // advcheckbox is not compatible with disabledIf!
            $mform->addElement('checkbox', 'hidden', get_string('hidden', 'grades'));
            $mform->addElement('date_time_selector', 'hiddenuntil', get_string('hiddenuntil', 'grades'), array('optional'=>true));
            $mform->disabledIf('hidden', 'hiddenuntil[off]', 'notchecked');
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

        $mform->addElement('text', 'aggregationcoef2', get_string('weight', 'grades'));
        $mform->addHelpButton('aggregationcoef2', 'weight', 'grades');
        $mform->setType('aggregationcoef2', PARAM_RAW);
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

/// mark advanced according to site settings
        if (isset($CFG->grade_item_advanced)) {
            $advanced = explode(',', $CFG->grade_item_advanced);
            foreach ($advanced as $el) {
                if ($mform->elementExists($el)) {
                    $mform->setAdvanced($el);
                }
            }
        }
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
            $grade_item = grade_item::fetch(array('id'=>$id));

            if (!$grade_item->is_raw_used()) {
                $mform->removeElement('plusfactor');
                $mform->removeElement('multfactor');
            }

            if ($grade_item->is_outcome_item()) {
                // we have to prevent incompatible modifications of outcomes if outcomes disabled
                $mform->removeElement('grademax');
                if ($mform->elementExists('grademin')) {
                    $mform->removeElement('grademin');
                }
                $mform->removeElement('gradetype');
                $mform->removeElement('display');
                $mform->removeElement('decimals');
                $mform->hardFreeze('scaleid');

            } else {
                if ($grade_item->is_external_item()) {
                    // following items are set up from modules and should not be overrided by user
                    if ($mform->elementExists('grademin')) {
                        // The site setting grade_report_showmin may have prevented grademin being added to the form.
                        $mform->hardFreeze('grademin');
                    }
                    $mform->hardFreeze('itemname,gradetype,grademax,scaleid');
                    if ($grade_item->itemnumber == 0) {
                        // the idnumber of grade itemnumber 0 is synced with course_modules
                        $mform->hardFreeze('idnumber');
                    }
                    //$mform->removeElement('calculation');
                }
            }

            // if we wanted to change parent of existing item - we would have to verify there are no circular references in parents!!!
            if ($mform->elementExists('parentcategory')) {
                $mform->hardFreeze('parentcategory');
            }

            $parent_category = $grade_item->get_parent_category();
            $parent_category->apply_forced_settings();

            if (!$parent_category->is_aggregationcoef_used()) {
                if ($mform->elementExists('aggregationcoef')) {
                    $mform->removeElement('aggregationcoef');
                }

            } else {
                $coefstring = $grade_item->get_coefstring();

                if ($coefstring !== '') {
                    if ($coefstring == 'aggregationcoefextrasum' || $coefstring == 'aggregationcoefextraweightsum') {
                        // advcheckbox is not compatible with disabledIf!
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
                $mform->disabledIf('aggregationcoef', 'parentcategory', 'eq', $parent_category->id);
            }

            // Remove fields used by natural weighting if the parent category is not using natural weighting.
            // Or if the item is a scale and scales are not used in aggregation.
            if ($parent_category->aggregation != GRADE_AGGREGATE_SUM
                    || (empty($CFG->grade_includescalesinaggregation) && $grade_item->gradetype == GRADE_TYPE_SCALE)) {
                if ($mform->elementExists('weightoverride')) {
                    $mform->removeElement('weightoverride');
                }
                if ($mform->elementExists('aggregationcoef2')) {
                    $mform->removeElement('aggregationcoef2');
                }
            }

            if ($category = $grade_item->get_item_category()) {
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

        } else {
            // all new items are manual, children of course category
            $mform->removeElement('plusfactor');
            $mform->removeElement('multfactor');
        }

        // no parent header for course category
        if (!$mform->elementExists('aggregationcoef') and !$mform->elementExists('parentcategory')) {
            $mform->removeElement('headerparent');
        }
    }

/// perform extra validation before submission
    function validation($data, $files) {
        global $COURSE;

        $errors = parent::validation($data, $files);

        if (array_key_exists('idnumber', $data)) {
            if ($data['id']) {
                $grade_item = new grade_item(array('id'=>$data['id'], 'courseid'=>$data['courseid']));
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

        if (array_key_exists('grademin', $data) and array_key_exists('grademax', $data)) {
            if ($data['grademax'] == $data['grademin'] or $data['grademax'] < $data['grademin']) {
                $errors['grademin'] = get_string('incorrectminmax', 'grades');
                $errors['grademax'] = get_string('incorrectminmax', 'grades');
            }
        }

        return $errors;
    }

}


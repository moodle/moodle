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

class edit_category_form extends moodleform {
    private $aggregation_options = array();

    function definition() {
        global $CFG, $COURSE, $DB;
        $mform =& $this->_form;

        $category = $this->_customdata['current'];

        $this->aggregation_options = array(GRADE_AGGREGATE_MEAN            =>get_string('aggregatemean', 'grades'),
                                           GRADE_AGGREGATE_WEIGHTED_MEAN   =>get_string('aggregateweightedmean', 'grades'),
                                           GRADE_AGGREGATE_WEIGHTED_MEAN2  =>get_string('aggregateweightedmean2', 'grades'),
                                           GRADE_AGGREGATE_EXTRACREDIT_MEAN=>get_string('aggregateextracreditmean', 'grades'),
                                           GRADE_AGGREGATE_MEDIAN          =>get_string('aggregatemedian', 'grades'),
                                           GRADE_AGGREGATE_MIN             =>get_string('aggregatemin', 'grades'),
                                           GRADE_AGGREGATE_MAX             =>get_string('aggregatemax', 'grades'),
                                           GRADE_AGGREGATE_MODE            =>get_string('aggregatemode', 'grades'),
                                           GRADE_AGGREGATE_SUM             =>get_string('aggregatesum', 'grades'));

        // visible elements
        $mform->addElement('header', 'headercategory', get_string('gradecategory', 'grades'));
        $mform->addElement('text', 'fullname', get_string('categoryname', 'grades'));
        $mform->addRule('fullname', null, 'required', null, 'client');

        $mform->addElement('select', 'aggregation', get_string('aggregation', 'grades'), $this->aggregation_options);
        $mform->addHelpButton('aggregation', 'aggregation', 'grades');

        if ((int)$CFG->grade_aggregation_flag & 2) {
            $mform->setAdvanced('aggregation');
        }

        $mform->addElement('checkbox', 'aggregateonlygraded', get_string('aggregateonlygraded', 'grades'));
        $mform->addHelpButton('aggregateonlygraded', 'aggregateonlygraded', 'grades');
        $mform->disabledIf('aggregateonlygraded', 'aggregation', 'eq', GRADE_AGGREGATE_SUM);

        if ((int)$CFG->grade_aggregateonlygraded_flag & 2) {
            $mform->setAdvanced('aggregateonlygraded');
        }

        if (empty($CFG->enableoutcomes)) {
            $mform->addElement('hidden', 'aggregateoutcomes');
            $mform->setType('aggregateoutcomes', PARAM_INT);
        } else {
            $mform->addElement('checkbox', 'aggregateoutcomes', get_string('aggregateoutcomes', 'grades'));
            $mform->addHelpButton('aggregateoutcomes', 'aggregateoutcomes', 'grades');
            if ((int)$CFG->grade_aggregateoutcomes_flag & 2) {
                $mform->setAdvanced('aggregateoutcomes');
            }
        }

        $mform->addElement('advcheckbox', 'aggregatesubcats', get_string('aggregatesubcats', 'grades'));
        $mform->addHelpButton('aggregatesubcats', 'aggregatesubcats', 'grades');

        if ((int)$CFG->grade_aggregatesubcats_flag & 2) {
            $mform->setAdvanced('aggregatesubcats');
        }

        $options = array(0 => get_string('none'));

        for ($i=1; $i<=20; $i++) {
            $options[$i] = $i;
        }

        $mform->addElement('select', 'keephigh', get_string('keephigh', 'grades'), $options);
        $mform->addHelpButton('keephigh', 'keephigh', 'grades');
        if ((int)$CFG->grade_keephigh_flag & 2) {
            $mform->setAdvanced('keephigh');
        }

        $mform->addElement('select', 'droplow', get_string('droplow', 'grades'), $options);
        $mform->addHelpButton('droplow', 'droplow', 'grades');
        $mform->disabledIf('droplow', 'keephigh', 'noteq', 0);
        if ((int)$CFG->grade_droplow_flag & 2) {
            $mform->setAdvanced('droplow');
        }

        $mform->disabledIf('keephigh', 'droplow', 'noteq', 0);
        $mform->disabledIf('droplow', 'keephigh', 'noteq', 0);

        // Grade item settings
        // Displayed as Category total to avoid confusion between grade items requiring marking and category totals
        $mform->addElement('header', 'general', get_string('categorytotal', 'grades'));

        $mform->addElement('text', 'grade_item_itemname', get_string('categorytotalname', 'grades'));
        $mform->setAdvanced('grade_item_itemname');

        $mform->addElement('text', 'grade_item_iteminfo', get_string('iteminfo', 'grades'));
        $mform->addHelpButton('grade_item_iteminfo', 'iteminfo', 'grades');

        $mform->addElement('text', 'grade_item_idnumber', get_string('idnumbermod'));
        $mform->addHelpButton('grade_item_idnumber', 'idnumbermod');

        $options = array(GRADE_TYPE_NONE=>get_string('typenone', 'grades'),
                         GRADE_TYPE_VALUE=>get_string('typevalue', 'grades'),
                         GRADE_TYPE_SCALE=>get_string('typescale', 'grades'),
                         GRADE_TYPE_TEXT=>get_string('typetext', 'grades'));

        $mform->addElement('select', 'grade_item_gradetype', get_string('gradetype', 'grades'), $options);
        $mform->addHelpButton('grade_item_gradetype', 'gradetype', 'grades');
        $mform->setDefault('grade_item_gradetype', GRADE_TYPE_VALUE);
        $mform->disabledIf('grade_item_gradetype', 'aggregation', 'eq', GRADE_AGGREGATE_SUM);

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
        if (!empty($category->grade_item_scaleid) and !isset($options[$category->grade_item_scaleid])) {
            if ($scale = grade_scale::fetch(array('id'=>$category->grade_item_scaleid))) {
                $options[$scale->id] = $scale->get_name().' '.get_string('incorrectcustomscale', 'grades');
            }
        }
        $mform->addElement('select', 'grade_item_scaleid', get_string('scale'), $options);
        $mform->addHelpButton('grade_item_scaleid', 'typescale', 'grades');
        $mform->disabledIf('grade_item_scaleid', 'grade_item_gradetype', 'noteq', GRADE_TYPE_SCALE);
        $mform->disabledIf('grade_item_scaleid', 'aggregation', 'eq', GRADE_AGGREGATE_SUM);

        $mform->addElement('text', 'grade_item_grademax', get_string('grademax', 'grades'));
        $mform->addHelpButton('grade_item_grademax', 'grademax', 'grades');
        $mform->disabledIf('grade_item_grademax', 'grade_item_gradetype', 'noteq', GRADE_TYPE_VALUE);
        $mform->disabledIf('grade_item_grademax', 'aggregation', 'eq', GRADE_AGGREGATE_SUM);

        $mform->addElement('text', 'grade_item_grademin', get_string('grademin', 'grades'));
        $mform->addHelpButton('grade_item_grademin', 'grademin', 'grades');
        $mform->disabledIf('grade_item_grademin', 'grade_item_gradetype', 'noteq', GRADE_TYPE_VALUE);
        $mform->disabledIf('grade_item_grademin', 'aggregation', 'eq', GRADE_AGGREGATE_SUM);

        $mform->addElement('text', 'grade_item_gradepass', get_string('gradepass', 'grades'));
        $mform->addHelpButton('grade_item_gradepass', 'gradepass', 'grades');
        $mform->disabledIf('grade_item_gradepass', 'grade_item_gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('grade_item_gradepass', 'grade_item_gradetype', 'eq', GRADE_TYPE_TEXT);

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
        $mform->addElement('select', 'grade_item_display', get_string('gradedisplaytype', 'grades'), $options);
        $mform->addHelpButton('grade_item_display', 'gradedisplaytype', 'grades');

        $default_gradedecimals = grade_get_setting($COURSE->id, 'decimalpoints', $CFG->grade_decimalpoints);
        $options = array(-1=>get_string('defaultprev', 'grades', $default_gradedecimals), 0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5);
        $mform->addElement('select', 'grade_item_decimals', get_string('decimalpoints', 'grades'), $options);
        $mform->addHelpButton('grade_item_decimals', 'decimalpoints', 'grades');
        $mform->setDefault('grade_item_decimals', -1);
        $mform->disabledIf('grade_item_decimals', 'grade_item_display', 'eq', GRADE_DISPLAY_TYPE_LETTER);

        if ($default_gradedisplaytype == GRADE_DISPLAY_TYPE_LETTER) {
            $mform->disabledIf('grade_item_decimals', 'grade_item_display', "eq", GRADE_DISPLAY_TYPE_DEFAULT);
        }

        /// hiding
        // advcheckbox is not compatible with disabledIf!
        $mform->addElement('checkbox', 'grade_item_hidden', get_string('hidden', 'grades'));
        $mform->addHelpButton('grade_item_hidden', 'hidden', 'grades');
        $mform->addElement('date_time_selector', 'grade_item_hiddenuntil', get_string('hiddenuntil', 'grades'), array('optional'=>true));
        $mform->disabledIf('grade_item_hidden', 'grade_item_hiddenuntil[off]', 'notchecked');

        /// locking
        $mform->addElement('checkbox', 'grade_item_locked', get_string('locked', 'grades'));
        $mform->addHelpButton('grade_item_locked', 'locked', 'grades');

        $mform->addElement('date_time_selector', 'grade_item_locktime', get_string('locktime', 'grades'), array('optional'=>true));
        $mform->disabledIf('grade_item_locktime', 'grade_item_gradetype', 'eq', GRADE_TYPE_NONE);

/// parent category related settings
        $mform->addElement('header', 'headerparent', get_string('parentcategory', 'grades'));

        $options = array();
        $default = '';
        $categories = grade_category::fetch_all(array('courseid'=>$COURSE->id));

        foreach ($categories as $cat) {
            $cat->apply_forced_settings();
            $options[$cat->id] = $cat->get_name();
            if ($cat->is_course_category()) {
                $default = $cat->id;
            }
        }

        if (count($categories) > 1) {
            $mform->addElement('select', 'parentcategory', get_string('parentcategory', 'grades'), $options);
            $mform->addElement('static', 'currentparentaggregation', get_string('currentparentaggregation', 'grades'));
        }

        // hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

/// add return tracking info
        $gpr = $this->_customdata['gpr'];
        $gpr->add_mform_elements($mform);

/// mark advanced according to site settings
        if (isset($CFG->grade_item_advanced)) {
            $advanced = explode(',', $CFG->grade_item_advanced);
            foreach ($advanced as $el) {
                $el = 'grade_item_'.$el;
                if ($mform->elementExists($el)) {
                    $mform->setAdvanced($el);
                }
            }
        }

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
//-------------------------------------------------------------------------------
        $this->set_data($category);
    }


/// tweak the form - depending on existing data
    function definition_after_data() {
        global $CFG, $COURSE;

        $mform =& $this->_form;

        $somecat = new grade_category();

        foreach ($somecat->forceable as $property) {
            if ((int)$CFG->{"grade_{$property}_flag"} & 1) {
                if ($mform->elementExists($property)) {
                    if (empty($CFG->grade_hideforcedsettings)) {
                        $mform->hardFreeze($property);
                    } else {
                        if ($mform->elementExists($property)) {
                            $mform->removeElement($property);
                        }
                    }
                }
            }
        }

        if ($CFG->grade_droplow > 0) {
            if ($mform->elementExists('keephigh')) {
                $mform->removeElement('keephigh');
            }
        } else if ($CFG->grade_keephigh > 0) {
            if ($mform->elementExists('droplow')) {
                $mform->removeElement('droplow');
            }
        }

        if ($id = $mform->getElementValue('id')) {
            $grade_category = grade_category::fetch(array('id'=>$id));
            $grade_item = $grade_category->load_grade_item();

            // remove agg coef if not used
            if ($grade_category->is_course_category()) {
                if ($mform->elementExists('parentcategory')) {
                    $mform->removeElement('parentcategory');
                }
                if ($mform->elementExists('currentparentaggregation')) {
                    $mform->removeElement('currentparentaggregation');
                }

            } else {
                // if we wanted to change parent of existing category - we would have to verify there are no circular references in parents!!!
                if ($mform->elementExists('parentcategory')) {
                    $mform->hardFreeze('parentcategory');
                }
                $parent_cat = $grade_category->get_parent_category();
                $mform->setDefault('currentparentaggregation', $this->aggregation_options[$parent_cat->aggregation]);

            }

            if ($grade_item->is_calculated()) {
                // following elements are ignored when calculation formula used
                if ($mform->elementExists('aggregation')) {
                    $mform->removeElement('aggregation');
                }
                if ($mform->elementExists('keephigh')) {
                    $mform->removeElement('keephigh');
                }
                if ($mform->elementExists('droplow')) {
                    $mform->removeElement('droplow');
                }
                if ($mform->elementExists('aggregateonlygraded')) {
                    $mform->removeElement('aggregateonlygraded');
                }
                if ($mform->elementExists('aggregateoutcomes')) {
                    $mform->removeElement('aggregateoutcomes');
                }
                if ($mform->elementExists('aggregatesubcats')) {
                    $mform->removeElement('aggregatesubcats');
                }
            }

            // If it is a course category, remove the "required" rule from the "fullname" element
            if ($grade_category->is_course_category()) {
                unset($mform->_rules['fullname']);
                $key = array_search('fullname', $mform->_required);
                unset($mform->_required[$key]);
            }

            // If it is a course category and its fullname is ?, show an empty field
            if ($grade_category->is_course_category() && $mform->getElementValue('fullname') == '?') {
                $mform->setDefault('fullname', '');
            }
            // remove unwanted aggregation options
            if ($mform->elementExists('aggregation')) {
                $allaggoptions = array_keys($this->aggregation_options);
                $agg_el =& $mform->getElement('aggregation');
                $visible = explode(',', $CFG->grade_aggregations_visible);
                if (!is_null($grade_category->aggregation)) {
                    // current type is always visible
                    $visible[] = $grade_category->aggregation;
                }
                foreach ($allaggoptions as $type) {
                    if (!in_array($type, $visible)) {
                        $agg_el->removeOption($type);
                    }
                }
            }

        } else {
            // adding new category
            if ($mform->elementExists('currentparentaggregation')) {
                $mform->removeElement('currentparentaggregation');
            }
            // remove unwanted aggregation options
            if ($mform->elementExists('aggregation')) {
                $allaggoptions = array_keys($this->aggregation_options);
                $agg_el =& $mform->getElement('aggregation');
                $visible = explode(',', $CFG->grade_aggregations_visible);
                foreach ($allaggoptions as $type) {
                    if (!in_array($type, $visible)) {
                        $agg_el->removeOption($type);
                    }
                }
            }
        }


        // no parent header for course category
        if (!$mform->elementExists('parentcategory')) {
            $mform->removeElement('headerparent');
        }

/// GRADE ITEM
        if ($id = $mform->getElementValue('id')) {
            $grade_category = grade_category::fetch(array('id'=>$id));
            $grade_item = $grade_category->load_grade_item();

            $mform->setDefault('grade_item_hidden', (int) $grade_item->hidden);

            if ($grade_item->is_outcome_item()) {
                // we have to prevent incompatible modifications of outcomes if outcomes disabled
                $mform->removeElement('grade_item_grademax');
                $mform->removeElement('grade_item_grademin');
                $mform->removeElement('grade_item_gradetype');
                $mform->removeElement('grade_item_display');
                $mform->removeElement('grade_item_decimals');
                $mform->hardFreeze('grade_item_scaleid');
            }

            //remove the aggregation coef element if not needed
            if ($grade_item->is_course_item()) {
                if ($mform->elementExists('grade_item_aggregationcoef')) {
                    $mform->removeElement('grade_item_aggregationcoef');
                }

            } else {
                if ($grade_item->is_category_item()) {
                    $category = $grade_item->get_item_category();
                    $parent_category = $category->get_parent_category();
                } else {
                    $parent_category = $grade_item->get_parent_category();
                }

                $parent_category->apply_forced_settings();

                if (!$parent_category->is_aggregationcoef_used()) {
                    if ($mform->elementExists('grade_item_aggregationcoef')) {
                        $mform->removeElement('grade_item_aggregationcoef');
                    }
                } else {

                    $coefstring = $grade_item->get_coefstring();

                    if ($coefstring == 'aggregationcoefextrasum') {
                        // advcheckbox is not compatible with disabledIf!
                        $element =& $mform->createElement('checkbox', 'grade_item_aggregationcoef', get_string($coefstring, 'grades'));
                    } else {
                        $element =& $mform->createElement('text', 'grade_item_aggregationcoef', get_string($coefstring, 'grades'));
                    }
                    $mform->insertElementBefore($element, 'parentcategory');
                    $mform->addHelpButton('grade_item_aggregationcoef', $coefstring, 'grades');
                }
            }
        }
    }

/// perform extra validation before submission
    function validation($data, $files) {
        global $COURSE;

        $errors = parent::validation($data, $files);

        if (array_key_exists('grade_item_gradetype', $data) and $data['grade_item_gradetype'] == GRADE_TYPE_SCALE) {
            if (empty($data['grade_item_scaleid'])) {
                $errors['grade_item_scaleid'] = get_string('missingscale', 'grades');
            }
        }
        if (array_key_exists('grade_item_grademin', $data) and array_key_exists('grade_item_grademax', $data)) {
            if (($data['grade_item_grademax'] != 0 OR $data['grade_item_grademin'] != 0) AND
                ($data['grade_item_grademax'] == $data['grade_item_grademin'] OR
                 $data['grade_item_grademax'] < $data['grade_item_grademin'])) {
                 $errors['grade_item_grademin'] = get_string('incorrectminmax', 'grades');
                 $errors['grade_item_grademax'] = get_string('incorrectminmax', 'grades');
             }
        }

        return $errors;
    }
}



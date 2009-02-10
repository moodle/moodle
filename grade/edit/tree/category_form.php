<?php  //$Id$

///////////////////////////////////////////////////////////////////////////
//                                                                       //
// NOTICE OF COPYRIGHT                                                   //
//                                                                       //
// Moodle - Modular Object-Oriented Dynamic Learning Environment         //
//          http://moodle.com                                            //
//                                                                       //
// Copyright (C) 1999 onwards  Martin Dougiamas  http://moodle.com       //
//                                                                       //
// This program is free software; you can redistribute it and/or modify  //
// it under the terms of the GNU General Public License as published by  //
// the Free Software Foundation; either version 2 of the License, or     //
// (at your option) any later version.                                   //
//                                                                       //
// This program is distributed in the hope that it will be useful,       //
// but WITHOUT ANY WARRANTY; without even the implied warranty of        //
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         //
// GNU General Public License for more details:                          //
//                                                                       //
//          http://www.gnu.org/copyleft/gpl.html                         //
//                                                                       //
///////////////////////////////////////////////////////////////////////////

require_once $CFG->libdir.'/formslib.php';

class edit_category_form extends moodleform {
    private $aggregation_options = array();

    function definition() {
        global $CFG, $COURSE, $DB;
        $mform =& $this->_form;

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
        $mform->setHelpButton('aggregation', array('aggregation', get_string('aggregation', 'grades'), 'grade'));
        if ((int)$CFG->grade_aggregation_flag & 2) {
            $mform->setAdvanced('aggregation');
        }

        $mform->addElement('checkbox', 'aggregateonlygraded', get_string('aggregateonlygraded', 'grades'));
        $mform->setHelpButton('aggregateonlygraded', array('aggregateonlygraded', get_string('aggregateonlygraded', 'grades'),'grade'), true);
        $mform->disabledIf('aggregateonlygraded', 'aggregation', 'eq', GRADE_AGGREGATE_SUM);
        if ((int)$CFG->grade_aggregateonlygraded_flag & 2) {
            $mform->setAdvanced('aggregateonlygraded');
        }

        if (empty($CFG->enableoutcomes)) {
            $mform->addElement('hidden', 'aggregateoutcomes');
            $mform->setType('aggregateoutcomes', PARAM_INT);
        } else {
            $mform->addElement('checkbox', 'aggregateoutcomes', get_string('aggregateoutcomes', 'grades'));
            $mform->setHelpButton('aggregateoutcomes', array('aggregateoutcomes', get_string('aggregateoutcomes', 'grades'), 'grade'), true);
            if ((int)$CFG->grade_aggregateoutcomes_flag & 2) {
                $mform->setAdvanced('aggregateoutcomes');
            }
        }

        $mform->addElement('advcheckbox', 'aggregatesubcats', get_string('aggregatesubcats', 'grades'));
        $mform->setHelpButton('aggregatesubcats', array('aggregatesubcats', get_string('aggregatesubcats', 'grades'), 'grade'), true);
        if ((int)$CFG->grade_aggregatesubcats_flag & 2) {
            $mform->setAdvanced('aggregatesubcats');
        }

        $options = array(0 => get_string('none'));
        for ($i=1; $i<=20; $i++) {
            $options[$i] = $i;
        }

        $mform->addElement('select', 'keephigh', get_string('keephigh', 'grades'), $options);
        $mform->setHelpButton('keephigh', array('keephigh', get_string('keephigh', 'grades'), 'grade'), true);
        if ((int)$CFG->grade_keephigh_flag & 2) {
            $mform->setAdvanced('keephigh');
        }

        $mform->addElement('select', 'droplow', get_string('droplow', 'grades'), $options);
        $mform->setHelpButton('droplow', array('droplow', get_string('droplow', 'grades'), 'grade'), true);
        $mform->disabledIf('droplow', 'keephigh', 'noteq', 0);
        if ((int)$CFG->grade_droplow_flag & 2) {
            $mform->setAdvanced('droplow');
        }

        $mform->disabledIf('keephigh', 'droplow', 'noteq', 0);
        $mform->disabledIf('droplow', 'keephigh', 'noteq', 0);

        // Grade item settings
        $mform->addElement('header', 'general', get_string('gradeitem', 'grades'));

        $mform->addElement('text', 'grade_item_itemname', get_string('itemname', 'grades'));
        $mform->addElement('text', 'grade_item_iteminfo', get_string('iteminfo', 'grades'));
        $mform->setHelpButton('grade_item_iteminfo', array('iteminfo', get_string('iteminfo', 'grades'), 'grade'), true);

        $mform->addElement('text', 'grade_item_idnumber', get_string('idnumbermod'));
        $mform->setHelpButton('grade_item_idnumber', array('idnumber', get_string('idnumber', 'grades'), 'grade'), true);

        $options = array(GRADE_TYPE_NONE=>get_string('typenone', 'grades'),
                         GRADE_TYPE_VALUE=>get_string('typevalue', 'grades'),
                         GRADE_TYPE_SCALE=>get_string('typescale', 'grades'),
                         GRADE_TYPE_TEXT=>get_string('typetext', 'grades'));

        $mform->addElement('select', 'grade_item_gradetype', get_string('gradetype', 'grades'), $options);
        $mform->setHelpButton('grade_item_gradetype', array('gradetype', get_string('gradetype', 'grades'), 'grade'), true);
        $mform->setDefault('grade_item_gradetype', GRADE_TYPE_VALUE);

        //$mform->addElement('text', 'calculation', get_string('calculation', 'grades'));
        //$mform->disabledIf('calculation', 'gradetype', 'eq', GRADE_TYPE_TEXT);
        //$mform->disabledIf('calculation', 'gradetype', 'eq', GRADE_TYPE_NONE);

        $options = array(0=>get_string('usenoscale', 'grades'));
        if ($scales = $DB->get_records('scale')) {
            foreach ($scales as $scale) {
                $options[$scale->id] = format_string($scale->name);
            }
        }
        $mform->addElement('select', 'grade_item_scaleid', get_string('scale'), $options);
        $mform->setHelpButton('grade_item_scaleid', array('scaleid', get_string('scaleid', 'grades'), 'grade'), true);
        $mform->disabledIf('grade_item_scaleid', 'grade_item_gradetype', 'noteq', GRADE_TYPE_SCALE);

        $mform->addElement('text', 'grade_item_grademax', get_string('grademax', 'grades'));
        $mform->setHelpButton('grade_item_grademax', array('grademax', get_string('grademax', 'grades'), 'grade'), true);
        $mform->disabledIf('grade_item_grademax', 'grade_item_gradetype', 'noteq', GRADE_TYPE_VALUE);

        $mform->addElement('text', 'grade_item_grademin', get_string('grademin', 'grades'));
        $mform->setHelpButton('grade_item_grademin', array('grademin', get_string('grademin', 'grades'), 'grade'), true);
        $mform->disabledIf('grade_item_grademin', 'grade_item_gradetype', 'noteq', GRADE_TYPE_VALUE);

        $mform->addElement('text', 'grade_item_gradepass', get_string('gradepass', 'grades'));
        $mform->setHelpButton('grade_item_gradepass', array('gradepass', get_string('gradepass', 'grades'), 'grade'), true);
        $mform->disabledIf('grade_item_gradepass', 'grade_item_gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('grade_item_gradepass', 'grade_item_gradetype', 'eq', GRADE_TYPE_TEXT);

        $mform->addElement('text', 'grade_item_multfactor', get_string('multfactor', 'grades'));
        $mform->setHelpButton('grade_item_multfactor', array('multfactor', get_string('multfactor', 'grades'), 'grade'), true);
        $mform->setAdvanced('grade_item_multfactor');
        $mform->disabledIf('grade_item_multfactor', 'grade_item_gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('grade_item_multfactor', 'grade_item_gradetype', 'eq', GRADE_TYPE_TEXT);

        $mform->addElement('text', 'grade_item_plusfactor', get_string('plusfactor', 'grades'));
        $mform->setHelpButton('grade_item_plusfactor', array('plusfactor', get_string('plusfactor', 'grades'), 'grade'), true);
        $mform->setAdvanced('grade_item_plusfactor');
        $mform->disabledIf('grade_item_plusfactor', 'grade_item_gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('grade_item_plusfactor', 'grade_item_gradetype', 'eq', GRADE_TYPE_TEXT);

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
        $mform->setHelpButton('grade_item_display', array('gradedisplaytype', get_string('gradedisplaytype', 'grades'), 'grade'), true);

        $default_gradedecimals = grade_get_setting($COURSE->id, 'decimalpoints', $CFG->grade_decimalpoints);
        $options = array(-1=>get_string('defaultprev', 'grades', $default_gradedecimals), 0=>0, 1=>1, 2=>2, 3=>3, 4=>4, 5=>5);
        $mform->addElement('select', 'grade_item_decimals', get_string('decimalpoints', 'grades'), $options);
        $mform->setHelpButton('grade_item_decimals', array('decimalpoints', get_string('decimalpoints', 'grades'), 'grade'), true);
        $mform->setDefault('grade_item_decimals', -1);
        $mform->disabledIf('grade_item_decimals', 'grade_item_display', 'eq', GRADE_DISPLAY_TYPE_LETTER);
        if ($default_gradedisplaytype == GRADE_DISPLAY_TYPE_LETTER) {
            $mform->disabledIf('grade_item_decimals', 'grade_item_display', "eq", GRADE_DISPLAY_TYPE_DEFAULT);
        }

        /// hiding
        // advcheckbox is not compatible with disabledIf!
        $mform->addElement('checkbox', 'grade_item_hidden', get_string('hidden', 'grades'));
        $mform->setHelpButton('grade_item_hidden', array('hidden', get_string('hidden', 'grades'), 'grade'));
        $mform->addElement('date_time_selector', 'grade_item_hiddenuntil', get_string('hiddenuntil', 'grades'), array('optional'=>true));
        $mform->setHelpButton('grade_item_hiddenuntil', array('hiddenuntil', get_string('hiddenuntil', 'grades'), 'grade'));
        $mform->disabledIf('grade_item_hidden', 'grade_item_hiddenuntil[off]', 'notchecked');

        /// locking
        $mform->addElement('advcheckbox', 'grade_item_locked', get_string('locked', 'grades'));
        $mform->setHelpButton('grade_item_locked', array('locked', get_string('locked', 'grades'), 'grade'));

        $mform->addElement('date_time_selector', 'grade_item_locktime', get_string('locktime', 'grades'), array('optional'=>true));
        $mform->setHelpButton('grade_item_locktime', array('lockedafter', get_string('locktime', 'grades'), 'grade'));
        $mform->disabledIf('grade_item_locktime', 'grade_item_gradetype', 'eq', GRADE_TYPE_NONE);

/// parent category related settings
        $mform->addElement('header', 'headerparent', get_string('parentcategory', 'grades'));

        $options = array();
        $default = '';
        $coefstring = '';
        $categories = grade_category::fetch_all(array('courseid'=>$COURSE->id));

        foreach ($categories as $cat) {
            $cat->apply_forced_settings();
            $options[$cat->id] = $cat->get_name();
            if ($cat->is_course_category()) {
                $default = $cat->id;
            }
            if ($cat->is_aggregationcoef_used()) {
                if ($cat->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN) {
                    $coefstring = ($coefstring=='' or $coefstring=='aggregationcoefweight') ? 'aggregationcoefweight' : 'aggregationcoef';

                } else if ($cat->aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN) {
                    $coefstring = ($coefstring=='' or $coefstring=='aggregationcoefextra') ? 'aggregationcoefextra' : 'aggregationcoef';

                } else {
                    $coefstring = 'aggregationcoef';
                }
            } else {
                $mform->disabledIf('grade_item_aggregationcoef', 'parentcategory', 'eq', $cat->id);
            }
        }
        if (count($categories) > 1) {
            $mform->addElement('select', 'parentcategory', get_string('parentcategory', 'grades'), $options);
            $mform->addElement('static', 'currentparentaggregation', get_string('currentparentaggregation', 'grades'));
        }

        if ($coefstring !== '') {
            $mform->addElement('text', 'grade_item_aggregationcoef', get_string($coefstring, 'grades'));
            $mform->setHelpButton('grade_item_aggregationcoef', array('aggregationcoefweight', get_string('aggregationcoef', 'grades'), 'grade'), true);
        }

/// user preferences
        $mform->addElement('header', 'headerpreferences', get_string('myreportpreferences', 'grades'));
        $options = array(GRADE_REPORT_PREFERENCE_DEFAULT => get_string('default', 'grades'),
                         GRADE_REPORT_AGGREGATION_VIEW_FULL => get_string('fullmode', 'grades'),
                         GRADE_REPORT_AGGREGATION_VIEW_AGGREGATES_ONLY => get_string('aggregatesonly', 'grades'),
                         GRADE_REPORT_AGGREGATION_VIEW_GRADES_ONLY => get_string('gradesonly', 'grades'));
        $label = get_string('aggregationview', 'grades') . ' (' . get_string('default', 'grades')
               . ': ' . $options[$CFG->grade_report_aggregationview] . ')';
        $mform->addElement('select', 'pref_aggregationview', $label, $options);
        $mform->setHelpButton('pref_aggregationview', array('aggregationview', get_string('aggregationview', 'grades'), 'grade'), true);
        $mform->setDefault('pref_aggregationview', GRADE_REPORT_PREFERENCE_DEFAULT);
        $mform->setAdvanced('pref_aggregationview');

        // hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

/// add return tracking info
        $gpr = $this->_customdata['gpr'];
        $gpr->add_mform_elements($mform);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
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
                if ($mform->elementExists('grade_item_aggregationcoef')) {
                    $mform->removeElement('grade_item_aggregationcoef');
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
        }

        // no parent header for course category
        if (!$mform->elementExists('aggregationcoef') and !$mform->elementExists('parentcategory')) {
            $mform->removeElement('headerparent');
        }

/// GRADE ITEM
        if ($id = $mform->getElementValue('id')) {
            $grade_category = grade_category::fetch(array('id'=>$id));
            $grade_item = $grade_category->load_grade_item();

            if (!$grade_item->is_raw_used()) {
                $mform->removeElement('grade_item_plusfactor');
                $mform->removeElement('grade_item_multfactor');
            }

            if ($grade_item->is_outcome_item()) {
                // we have to prevent incompatible modifications of outcomes if outcomes disabled
                $mform->removeElement('grade_item_grademax');
                $mform->removeElement('grade_item_grademin');
                $mform->removeElement('grade_item_gradetype');
                $mform->removeElement('grade_item_display');
                $mform->removeElement('grade_item_decimals');
                $mform->hardFreeze('grade_item_scaleid');

            } else {
                if ($grade_item->is_external_item()) {
                    // following items are set up from modules and should not be overrided by user
                    $mform->hardFreeze('grade_item_itemname,grade_item_idnumber,grade_item_gradetype,grade_item_grademax,grade_item_grademin,grade_item_scaleid');
                    //$mform->removeElement('calculation');
                }
            }

            //remove the aggregation coef element if not needed
            if ($grade_item->is_course_item()) {
                if ($mform->elementExists('grade_item_parentcategory')) {
                    $mform->removeElement('grade_item_parentcategory');
                }
                if ($mform->elementExists('grade_item_aggregationcoef')) {
                    $mform->removeElement('grade_item_aggregationcoef');
                }

            } else {
                // if we wanted to change parent of existing item - we would have to verify there are no circular references in parents!!!
                if ($mform->elementExists('grade_item_parentcategory')) {
                    $mform->hardFreeze('grade_item_parentcategory');
                }

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
                    //fix label if needed
                    $agg_el =& $mform->getElement('grade_item_aggregationcoef');
                    $aggcoef = '';
                    if ($parent_category->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN) {
                        $aggcoef = 'aggregationcoefweight';

                    } else if ($parent_category->aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN) {
                        $aggcoef = 'aggregationcoefextra';

                    } else if ($parent_category->aggregation == GRADE_AGGREGATE_SUM) {
                        $aggcoef = 'aggregationcoefextrasum';
                    }

                    if ($aggcoef !== '') {
                        $agg_el->setLabel(get_string($aggcoef, 'grades'));
                        $mform->setHelpButton('grade_item_aggregationcoef', array($aggcoef, get_string($aggcoef, 'grades'), 'grade'), true);
                    }
                }
            }

            if ($category = $grade_item->get_item_category()) {
                if ($category->aggregation == GRADE_AGGREGATE_SUM) {
                    if ($mform->elementExists('grade_item_gradetype')) {
                        $mform->hardFreeze('grade_item_gradetype');
                    }
                    if ($mform->elementExists('grade_item_grademin')) {
                        $mform->hardFreeze('grade_item_grademin');
                    }
                    if ($mform->elementExists('grade_item_grademax')) {
                        $mform->hardFreeze('grade_item_grademax');
                    }
                    if ($mform->elementExists('grade_item_scaleid')) {
                        $mform->removeElement('grade_item_scaleid');
                    }
                }
            }

        } else {
            // all new items are manual, children of course category
            $mform->removeElement('grade_item_plusfactor');
            $mform->removeElement('grade_item_multfactor');
        }
    }
}

?>

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

    function definition() {
        global $CFG, $COURSE;
        $mform =& $this->_form;

        $options = array(GRADE_AGGREGATE_MEAN            =>get_string('aggregatemean', 'grades'),
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

        $mform->addElement('select', 'aggregation', get_string('aggregation', 'grades'), $options);
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
                $mform->disabledIf('aggregationcoef', 'parentcategory', 'eq', $cat->id);
            }
        }
        if (count($categories) > 1) {
            $mform->addElement('select', 'parentcategory', get_string('gradecategory', 'grades'), $options);
        }

        if ($coefstring !== '') {
            $mform->addElement('text', 'aggregationcoef', get_string($coefstring, 'grades'));
            $mform->setHelpButton('aggregationcoef', array('aggregationcoef', get_string('aggregationcoef', 'grades'), 'grade'), true);
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
                if ($mform->elementExists('aggregationcoef')) {
                    $mform->removeElement('aggregationcoef');
                }

            } else {
                // if we wanted to change parent of existing category - we would have to verify there are no circular references in parents!!!
                if ($mform->elementExists('parentcategory')) {
                    $mform->hardFreeze('parentcategory');
                }

                $parent_category = $grade_category->get_parent_category();
                $parent_category->apply_forced_settings();
                if (!$parent_category->is_aggregationcoef_used()) {
                    if ($mform->elementExists('aggregationcoef')) {
                        $mform->removeElement('aggregationcoef');
                    }
                } else {
                    //fix label if needed
                    $agg_el =& $mform->getElement('aggregationcoef');
                    $aggcoef = '';
                    if ($parent_category->aggregation == GRADE_AGGREGATE_WEIGHTED_MEAN) {
                        $aggcoef = 'aggregationcoefweight';
                    } else if ($parent_category->aggregation == GRADE_AGGREGATE_EXTRACREDIT_MEAN) {
                        $aggcoef = 'aggregationcoefextra';
                    }
                    if ($aggcoef !== '') {
                        $agg_el->setLabel(get_string($aggcoef, 'grades'));
                        $mform->setHelpButton('aggregationcoef', array('aggregationcoef', get_string('aggregationcoef', 'grades'), 'grade'), true);
                    }
                }
                
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

    } 
}

?>

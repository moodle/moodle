<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_category_form extends moodleform {
    var $aggregation_types = array();
    var $keepdrop_options = array();

    function definition() {
        global $CFG;
        $mform =& $this->_form;

        $this->aggregation_types = array(GRADE_AGGREGATE_MEAN            =>get_string('aggregatemean', 'grades'),
                                         GRADE_AGGREGATE_MEDIAN          =>get_string('aggregatemedian', 'grades'),
                                         GRADE_AGGREGATE_MIN             =>get_string('aggregatemin', 'grades'),
                                         GRADE_AGGREGATE_MAX             =>get_string('aggregatemax', 'grades'),
                                         GRADE_AGGREGATE_MODE            =>get_string('aggregatemode', 'grades'),
                                         GRADE_AGGREGATE_WEIGHTED_MEAN   =>get_string('aggregateweightedmean', 'grades'),
                                         GRADE_AGGREGATE_EXTRACREDIT_MEAN=>get_string('aggregateextracreditmean', 'grades'));

        // visible elements
        $mform->addElement('header', 'general', get_string('gradecategory', 'grades'));
        $mform->addElement('text', 'fullname', get_string('categoryname', 'grades'));

        if ($CFG->grade_aggregation == -1) {

            $mform->addElement('select', 'aggregation', get_string('aggregation', 'grades'), $this->aggregation_types);
            $mform->setHelpButton('aggregation', array('aggregation', get_string('aggregation', 'grades'), 'grade'));
            $mform->setDefault('aggregation', GRADE_AGGREGATE_MEAN);
        } else {
            $mform->addElement('static', 'aggregation', get_string('aggregation', 'grades'));
        }

        if ($CFG->grade_aggregateonlygraded == -1) {
            $mform->addElement('advcheckbox', 'aggregateonlygraded', get_string('aggregateonlygraded', 'grades'));
            $mform->setHelpButton('aggregateonlygraded', array(false, get_string('aggregateonlygraded', 'grades'),
                              false, true, false, get_string('aggregateonlygradedhelp', 'grades')));
        } else {
            $mform->addElement('static', 'aggregateonlygraded', get_string('aggregateonlygraded', 'grades'));
        }

        if (!empty($CFG->enableoutcomes) && $CFG->grade_aggregateoutcomes == -1) {
            $mform->addElement('advcheckbox', 'aggregateoutcomes', get_string('aggregateoutcomes', 'grades'));
            $mform->setHelpButton('aggregateoutcomes', array(false, get_string('aggregateoutcomes', 'grades'),
                              false, true, false, get_string('aggregateoutcomeshelp', 'grades')));
        } else {
            $mform->addElement('static', 'aggregateoutcomes', get_string('aggregateoutcomes', 'grades'));
        }

        if ($CFG->grade_aggregatesubcats == -1) {
            $mform->addElement('advcheckbox', 'aggregatesubcats', get_string('aggregatesubcats', 'grades'));
            $mform->setHelpButton('aggregatesubcats', array(false, get_string('aggregatesubcats', 'grades'),
                              false, true, false, get_string('aggregatesubcatshelp', 'grades')));
        } else {
            $mform->addElement('static', 'aggregatesubcats', get_string('aggregatesubcats', 'grades'));
        }

        $this->keepdrop_options = array();
        $this->keepdrop_options[0] = get_string('none');
        for ($i=1; $i<=20; $i++) {
            $this->keepdrop_options[$i] = $i;
        }

        $keepdrop_present = 0;

        if ($CFG->grade_keephigh == -1) {
            $mform->addElement('select', 'keephigh', get_string('keephigh', 'grades'), $this->keepdrop_options);
            $mform->setHelpButton('keephigh', array(false, get_string('keephigh', 'grades'),
                              false, true, false, get_string('keephighhelp', 'grades')));
            $keepdrop_present++;
        } else {
            $mform->addElement('static', 'keephigh', get_string('keephigh', 'grades'));
        }

        if ($CFG->grade_droplow == -1) {
            $mform->addElement('select', 'droplow', get_string('droplow', 'grades'), $this->keepdrop_options);
            $mform->setHelpButton('droplow', array(false, get_string('droplow', 'grades'),
                              false, true, false, get_string('droplowhelp', 'grades')));
            $mform->disabledIf('droplow', 'keephigh', 'noteq', 0);
            $keepdrop_present++;
        } else {
            $mform->addElement('static', 'droplow', get_string('droplow', 'grades'));
        }

        if ($keepdrop_present == 2) {
            $mform->disabledIf('keephigh', 'droplow', 'noteq', 0);
            $mform->disabledIf('droplow', 'keephigh', 'noteq', 0);
        }

        // user preferences
        $mform->addElement('header', 'general', get_string('userpreferences', 'grades'));
        $options = array(GRADE_REPORT_PREFERENCE_DEFAULT => get_string('default', 'grades'),
                         GRADE_REPORT_AGGREGATION_VIEW_FULL => get_string('fullmode', 'grades'),
                         GRADE_REPORT_AGGREGATION_VIEW_AGGREGATES_ONLY => get_string('aggregatesonly', 'grades'),
                         GRADE_REPORT_AGGREGATION_VIEW_GRADES_ONLY => get_string('gradesonly', 'grades'));
        $label = get_string('aggregationview', 'grades') . ' (' . get_string('default', 'grades')
               . ': ' . $options[$CFG->grade_report_aggregationview] . ')';
        $mform->addElement('select', 'pref_aggregationview', $label, $options);
        $mform->setHelpButton('pref_aggregationview', array(false, get_string('aggregationview', 'grades'),
                              false, true, false, get_string('configaggregationview', 'grades')));
        $mform->setDefault('pref_aggregationview', GRADE_REPORT_PREFERENCE_DEFAULT);

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
        global $CFG;

        $mform =& $this->_form;

        $checkbox_values = array(get_string('no'), get_string('yes'));

        if ($CFG->grade_aggregation != -1) {
            $agg_el =& $mform->getElement('aggregation');
            $agg_el->setValue($this->aggregation_types[$CFG->grade_aggregation]);
        }

        if ($CFG->grade_aggregateonlygraded != -1) {
            $agg_el =& $mform->getElement('aggregateonlygraded');
            $agg_el->setValue($checkbox_values[$CFG->grade_aggregateonlygraded]);
        }

        if ($CFG->grade_aggregateoutcomes != -1) {
            $agg_el =& $mform->getElement('aggregateoutcomes');
            $agg_el->setValue($checkbox_values[$CFG->grade_aggregateoutcomes]);
        }

        if ($CFG->grade_aggregatesubcats != -1) {
            $agg_el =& $mform->getElement('aggregatesubcats');
            $agg_el->setValue($checkbox_values[$CFG->grade_aggregatesubcats]);
        }

        if ($CFG->grade_keephigh != -1) {
            $agg_el =& $mform->getElement('keephigh');
            $agg_el->setValue($this->keepdrop_options[$CFG->grade_keephigh]);
        }

        if ($CFG->grade_droplow != -1) {
            $agg_el =& $mform->getElement('droplow');
            $agg_el->setValue($this->keepdrop_options[$CFG->grade_droplow]);
        }

        if ($id = $mform->getElementValue('id')) {
            $grade_category = grade_category::fetch(array('id'=>$id));
            $grade_item = $grade_category->load_grade_item();

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
        }
    }

}

?>

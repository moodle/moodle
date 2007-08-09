<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_category_form extends moodleform {
    function definition() {
        global $CFG;
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('header', 'general', get_string('gradecategory', 'grades'));
        $mform->addElement('text', 'fullname', get_string('categoryname', 'grades'));

        $options = array(GRADE_AGGREGATE_MEAN_ALL               =>get_string('aggregatemeanall', 'grades'),
                         GRADE_AGGREGATE_MEAN_GRADED            =>get_string('aggregatemeangraded', 'grades'),
                         GRADE_AGGREGATE_MEDIAN_ALL             =>get_string('aggregatemedianall', 'grades'),
                         GRADE_AGGREGATE_MEDIAN_GRADED          =>get_string('aggregatemediangraded', 'grades'),
                         GRADE_AGGREGATE_MIN_ALL                =>get_string('aggregateminall', 'grades'),
                         GRADE_AGGREGATE_MIN_GRADED             =>get_string('aggregatemingraded', 'grades'),
                         GRADE_AGGREGATE_MAX_ALL                =>get_string('aggregatemaxall', 'grades'),
                         GRADE_AGGREGATE_MAX_GRADED             =>get_string('aggregatemaxgraded', 'grades'),
                         GRADE_AGGREGATE_MODE_ALL               =>get_string('aggregatemodeall', 'grades'),
                         GRADE_AGGREGATE_MODE_GRADED            =>get_string('aggregatemodegraded', 'grades'),
                         GRADE_AGGREGATE_WEIGHTED_MEAN_ALL      =>get_string('aggregateweightedmeanall', 'grades'),
                         GRADE_AGGREGATE_WEIGHTED_MEAN_GRADED   =>get_string('aggregateweightedmeangraded', 'grades'),
                         GRADE_AGGREGATE_EXTRACREDIT_MEAN_ALL   =>get_string('aggregateextracreditmeanall', 'grades'),
                         GRADE_AGGREGATE_EXTRACREDIT_MEAN_GRADED=>get_string('aggregateextracreditmeangraded', 'grades'));

        $mform->addElement('select', 'aggregation', get_string('aggregation', 'grades'), $options);
        $mform->setHelpButton('aggregation', array('aggregation', get_string('aggregation', 'grades'), 'grade'));
        $mform->setDefault('gradetype', GRADE_AGGREGATE_MEAN_ALL);

        if (!empty($CFG->enableoutcomes)) {
            $mform->addElement('advcheckbox', 'aggregateoutcomes', get_string('aggregateoutcomes', 'grades'));
            $mform->setHelpButton('aggregateoutcomes', array(false, get_string('aggregateoutcomes', 'grades'),
                              false, true, false, get_string("aggregateoutcomeshelp", 'grades')));
        }

        $options = array();
        $options[0] = get_string('none');
        for ($i=1; $i<=20; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', 'keephigh', get_string('keephigh', 'grades'), $options);
        $mform->setHelpButton('keephigh', array(false, get_string('keephigh', 'grades'),
                          false, true, false, get_string("keephighhelp", 'grades')));
        $mform->disabledIf('keephigh', 'droplow', 'noteq', 0);

        $mform->addElement('select', 'droplow', get_string('droplow', 'grades'), $options);
        $mform->setHelpButton('droplow', array(false, get_string('droplow', 'grades'),
                          false, true, false, get_string("droplowhelp", 'grades')));
        $mform->disabledIf('droplow', 'keephigh', 'noteq', 0);

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
                              false, true, false, get_string("configaggregationview", 'grades')));
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
            }
        }
    }

}

?>

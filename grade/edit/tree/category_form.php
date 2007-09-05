<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_category_form extends moodleform {
    function definition() {
        global $CFG;
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('header', 'general', get_string('gradecategory', 'grades'));
        $mform->addElement('text', 'fullname', get_string('categoryname', 'grades'));

        $options = array(GRADE_AGGREGATE_MEAN            =>get_string('aggregatemean', 'grades'),
                         GRADE_AGGREGATE_MEDIAN          =>get_string('aggregatemedian', 'grades'),
                         GRADE_AGGREGATE_MIN             =>get_string('aggregatemin', 'grades'),
                         GRADE_AGGREGATE_MAX             =>get_string('aggregatemax', 'grades'),
                         GRADE_AGGREGATE_MODE            =>get_string('aggregatemode', 'grades'),
                         GRADE_AGGREGATE_WEIGHTED_MEAN   =>get_string('aggregateweightedmean', 'grades'),
                         GRADE_AGGREGATE_EXTRACREDIT_MEAN=>get_string('aggregateextracreditmean', 'grades'));

        $mform->addElement('select', 'aggregation', get_string('aggregation', 'grades'), $options);
        $mform->setHelpButton('aggregation', array('aggregation', get_string('aggregation', 'grades'), 'grade'));
        $mform->setDefault('gradetype', GRADE_AGGREGATE_MEAN);

        $mform->addElement('advcheckbox', 'aggregateonlygraded', get_string('aggregateonlygraded', 'grades'));
        $mform->setHelpButton('aggregateonlygraded', array(false, get_string('aggregateonlygraded', 'grades'),
                          false, true, false, get_string('aggregateonlygradedhelp', 'grades')));

        if (!empty($CFG->enableoutcomes)) {
            $mform->addElement('advcheckbox', 'aggregateoutcomes', get_string('aggregateoutcomes', 'grades'));
            $mform->setHelpButton('aggregateoutcomes', array(false, get_string('aggregateoutcomes', 'grades'),
                              false, true, false, get_string('aggregateoutcomeshelp', 'grades')));
        }

        $mform->addElement('advcheckbox', 'aggregatesubcats', get_string('aggregatesubcats', 'grades'));
        $mform->setHelpButton('aggregatesubcats', array(false, get_string('aggregatesubcats', 'grades'),
                          false, true, false, get_string('aggregatesubcatshelp', 'grades')));

        $options = array();
        $options[0] = get_string('none');
        for ($i=1; $i<=20; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', 'keephigh', get_string('keephigh', 'grades'), $options);
        $mform->setHelpButton('keephigh', array(false, get_string('keephigh', 'grades'),
                          false, true, false, get_string('keephighhelp', 'grades')));
        $mform->disabledIf('keephigh', 'droplow', 'noteq', 0);

        $mform->addElement('select', 'droplow', get_string('droplow', 'grades'), $options);
        $mform->setHelpButton('droplow', array(false, get_string('droplow', 'grades'),
                          false, true, false, get_string('droplowhelp', 'grades')));
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

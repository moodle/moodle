<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_category_form extends moodleform {
    function definition() {
        $mform =& $this->_form;

        // visible elements
        $mform->addElement('text', 'fullname', get_string('categoryname', 'grades'));

        $options = array(GRADE_AGGREGATE_MEAN_ALL   =>get_string('aggregatemeanall', 'grades'),
                         GRADE_AGGREGATE_MEDIAN     =>get_string('aggregatemedian', 'grades'),
                         GRADE_AGGREGATE_MEAN_GRADED=>get_string('aggregatemeangraded', 'grades'),
                         GRADE_AGGREGATE_MIN        =>get_string('aggregatemin', 'grades'),
                         GRADE_AGGREGATE_MAX        =>get_string('aggregatemax', 'grades'),
                         GRADE_AGGREGATE_MODE       =>get_string('aggregatemode', 'grades'));
        $mform->addElement('select', 'aggregation', get_string('aggregation', 'grades'), $options);
        $mform->setDefault('gradetype', GRADE_AGGREGATE_MEAN_ALL);

        $options = array();
        $options[0] = get_string('none');
        for ($i=1; $i<=20; $i++) {
            $options[$i] = $i;
        }
        $mform->addElement('select', 'keephigh', get_string('keephigh', 'grades'), $options);
        $mform->disabledIf('keephigh', 'droplow', 'noteq', 0);

        $mform->addElement('select', 'droplow', get_string('droplow', 'grades'), $options);
        $mform->disabledIf('droplow', 'keephigh', 'noteq', 0);

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
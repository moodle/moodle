<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_outcomeitem_form extends moodleform {
    function definition() {
        global $COURSE, $CFG;

        $mform =& $this->_form;

/// visible elements
        $mform->addElement('header', 'general', get_string('gradeoutcomeitem', 'grades'));

        $mform->addElement('text', 'itemname', get_string('itemname', 'grades'));
        $mform->addRule('itemname', get_string('required'), 'required', null, 'client');

        $mform->addElement('text', 'iteminfo', get_string('iteminfo', 'grades'));

        $mform->addElement('text', 'idnumber', get_string('idnumber'));

        // allow setting of outcomes on module items too
        $options = array();
        if ($outcomes = grade_outcome::fetch_all_available($COURSE->id)) {
            foreach ($outcomes as $outcome) {
                $options[$outcome->id] = $outcome->get_name();
            }
        }
        $mform->addElement('select', 'outcomeid', get_string('outcome', 'grades'), $options);

        $options = array(0=>get_string('none'));
        if ($coursemods = get_course_mods($COURSE->id)) {
            foreach ($coursemods as $coursemod) {
                $mod = get_coursemodule_from_id($coursemod->modname, $coursemod->id);
                $options[$coursemod->id] = format_string($mod->name);
            }
        }
        $mform->addElement('select', 'cmid', get_string('linkedactivity', 'grades'), $options);
        $mform->setDefault('cmid', 0);


        $mform->addElement('text', 'calculation', get_string('calculation', 'grades'));

        $mform->addElement('text', 'aggregationcoef', get_string('aggregationcoef', 'grades'));
        $mform->setDefault('aggregationcoef', 0.0);

        $mform->addElement('advcheckbox', 'locked', get_string('locked', 'grades'));

        $mform->addElement('date_time_selector', 'locktime', get_string('locktime', 'grades'), array('optional'=>true));

/// hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', $COURSE->id);
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

        if ($id = $mform->getElementValue('id')) {
            $grade_item = grade_item::fetch(array('id'=>$id));

            //remove the aggregation coef element if not needed
            if ($grade_item->is_course_item()) {
                $mform->removeElement('aggregationcoef');

            } else if ($grade_item->is_category_item()) {
                $category = $grade_item->get_item_category();
                $parent_category = $category->get_parent_category();
                if (!$parent_category->is_aggregationcoef_used()) {
                    $mform->removeElement('aggregationcoef');
                }

            } else {
                $parent_category = $grade_item->get_parent_category();
                if (!$parent_category->is_aggregationcoef_used()) {
                    $mform->removeElement('aggregationcoef');
                }
            }

        } else {
            $course_category = grade_category::fetch_course_category($COURSE->id);
            if (!$course_category->is_aggregationcoef_used()) {
                $mform->removeElement('aggregationcoef');
            }
        }
    }


/// perform extra validation before submission
    function validation($data){
        $errors= array();

        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }
    }

}
?>

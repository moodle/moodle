<?php  //$Id$

require_once $CFG->libdir.'/formslib.php';

class edit_item_form extends moodleform {
    function definition() {
        global $COURSE;

        $mform =& $this->_form;

/// visible elements
        $mform->addElement('header', 'general', get_string('gradeitem', 'grades'));

        $mform->addElement('text', 'itemname', get_string('itemname', 'grades'));
        $mform->addElement('text', 'iteminfo', get_string('iteminfo', 'grades'));
        $mform->addElement('text', 'idnumber', get_string('idnumber'));

        // allow setting of outcomes on module items too
        $options = array(0=>get_string('usenooutcome', 'grades'));
        if ($outcomes = grade_outcome::fetch_all(array('courseid'=>$COURSE->id))) {
            foreach ($outcomes as $outcome) {
                $options[$scale->id] = $outcome->get_name();
            }
        }
        $mform->addElement('select', 'outcomeid', get_string('outcome', 'grades'), $options);

        $options = array(GRADE_TYPE_NONE=>get_string('typenone', 'grades'),
                          GRADE_TYPE_VALUE=>get_string('typevalue', 'grades'),
                          GRADE_TYPE_SCALE=>get_string('typescale', 'grades'),
                          GRADE_TYPE_TEXT=>get_string('typetext', 'grades'));
        $mform->addElement('select', 'gradetype', get_string('gradetype', 'grades'), $options);
        $mform->setDefault('gradetype', GRADE_TYPE_VALUE);

        $mform->addElement('text', 'calculation', get_string('calculation', 'grades'));
        $mform->disabledIf('calculation', 'gradetype', 'eq', GRADE_TYPE_TEXT);
        $mform->disabledIf('calculation', 'gradetype', 'eq', GRADE_TYPE_NONE);

        $options = array(0=>get_string('usenoscale', 'grades'));
        if ($scales = get_records('scale')) {
            foreach ($scales as $scale) {
                $options[$scale->id] = format_string($scale->name);
            }
        }
        $mform->addElement('select', 'scaleid', get_string('scale'), $options);
        $mform->disabledIf('scaleid', 'gradetype', 'noteq', GRADE_TYPE_SCALE);

        $mform->addElement('text', 'grademax', get_string('grademax', 'grades'));
        $mform->disabledIf('grademax', 'gradetype', 'noteq', GRADE_TYPE_VALUE);
        $mform->setDefault('grademax', 100);

        $mform->addElement('text', 'grademin', get_string('grademin', 'grades'));
        $mform->disabledIf('grademin', 'gradetype', 'noteq', GRADE_TYPE_VALUE);
        $mform->setDefault('grademin', 0);

        $mform->addElement('text', 'gradepass', get_string('gradepass', 'grades'));
        $mform->disabledIf('gradepass', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('gradepass', 'gradetype', 'eq', GRADE_TYPE_TEXT);
        $mform->setDefault('gradepass', 0);

        $mform->addElement('text', 'multfactor', get_string('multfactor', 'grades'));
        $mform->disabledIf('multfactor', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('multfactor', 'gradetype', 'eq', GRADE_TYPE_TEXT);
        $mform->setDefault('multfactor', 1);

        $mform->addElement('text', 'plusfactor', get_string('plusfactor', 'grades'));
        $mform->disabledIf('plusfactor', 'gradetype', 'eq', GRADE_TYPE_NONE);
        $mform->disabledIf('plusfactor', 'gradetype', 'eq', GRADE_TYPE_TEXT);
        $mform->setDefault('plusfactor', 0);

        $mform->addElement('advcheckbox', 'locked', get_string('locked', 'grades'));

        $mform->addElement('date_time_selector', 'locktime', get_string('locktime', 'grades'), array('optional'=>true));
        $mform->disabledIf('locktime', 'gradetype', 'eq', GRADE_TYPE_NONE);

/// user preferences
        $mform->addElement('header', 'general', get_string('userpreferences', 'grades'));

        $mform->addElement('select', 'pref_gradedisplaytype', get_string('gradedisplaytype', 'grades'),
                    array('default' => get_string('default', 'grades'),
                          GRADE_REPORT_GRADE_DISPLAY_TYPE_RAW => get_string('raw', 'grades'),
                          GRADE_REPORT_GRADE_DISPLAY_TYPE_PERCENTAGE => get_string('percentage', 'grades')));
        $mform->setHelpButton('pref_gradedisplaytype', array(false, get_string('gradedisplaytype', 'grades'),
                              false, true, false, get_string("config_gradedisplaytype", 'grades')));
        $mform->setDefault('pref_gradedisplaytype', 'default');

        $mform->addElement('select', 'pref_decimalpoints', get_string('decimalpoints', 'grades'),
                    array('default' => get_string('default', 'grades'), 0, 1, 2, 3, 4, 5));
        $mform->setHelpButton('pref_decimalpoints', array(false, get_string('decimalpoints', 'grades'),
                              false, true, false, get_string("config_decimalpoints", 'grades')));
        $mform->setDefault('pref_decimalpoints', 'default');

/// hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'itemtype', 0);
        $mform->setType('itemtype', PARAM_ALPHA);

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
            $grade_item = grade_item::fetch(array('id'=>$id));
            if ($grade_item->is_normal_item()) {
                // following items are set up from modules and should not be overrided by user
                $mform->hardFreeze('itemname,idnumber,calculation,gradetype,grademax,grademin,scaleid');
            }
            if ($grade_item->is_manual_item()) {
                // manual grade item does not use these - uses only final grades
                $mform->hardFreeze('plusfactor,multfactor');
            }
        }
    }


/// perform extra validation before submission
    function validation($data){
        $errors= array();

        if (array_key_exists('calculation', $data) and $data['calculation'] != '') {
            $grade_item = new grade_item(array('id'=>$data['id'], 'itemtype'=>$data['itemtype'], 'courseid'=>$data['courseid']));
            $result = $grade_item->validate_formula($data['calculation']);
            if ($result !== true) {
                $errors['calculation'] = $result;
            }
        }

        if (array_key_exists('grademin', $data) and array_key_exists('grademax', $data)) {
            if ($data['grademax'] == $data['grademin'] or $data['grademax'] < $data['grademin']) {
                $errors['grademin'] = get_String('incorrectminmax', 'grades');
                $errors['grademax'] = get_String('incorrectminmax', 'grades');
            }
        }

        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }
    }

}
?>

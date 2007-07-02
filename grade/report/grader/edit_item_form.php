<?php
class edit_item_form extends moodleform {
    function definition() {
        $mform =& $this->_form;
    
        if ($id = $this->_customdata['id']) { // grade item id, if known
            $item = get_record('grade_items', 'id', $id);
        } else {
            $item = NULL;
        }
              
        $mform->addElement('header', 'general', get_string('gradeitem', 'form'));
        // visible elements
        $mform->addElement('text', 'itemname', get_string('itemname', 'grades'));
        $mform->addElement('text', 'iteminfo', get_string('iteminfo', 'grades'));
        $mform->addElement('text', 'idnumber', get_string('idnumber'));
        $mform->addElement('text', 'grademax', get_string('grademax', 'grades'));
        $mform->addElement('text', 'grademin', get_string('grademin', 'grades'));
        $mform->addElement('text', 'gradepass', get_string('gradepass', 'grades'));
        $mform->addElement('text', 'multfactor', get_string('multfactor', 'grades'));
        $mform->addElement('text', 'plusfactor', get_string('plusfactor', 'grades'));
        $mform->addElement('checkbox', 'locked', get_string('locked', 'grades'));
        
        // new grade item, or existing manual grade item(?)
        if (!$id || (!empty($item->scaleid) && $item->type == 'manual')) {
            if ($scales = get_records('scale')) {
                $soptions = array(0=>get_string('usenoscale', 'grades'));
                foreach ($scales as $scale) {
                    $soptions[$scale->id] = $scale->name;
                }
                $mform->addElement('select', 'scaleid', get_string('scale'), $soptions);
            }
        }

        $mform->addElement('date_time_selector', 'locktime', get_string('locktime', 'grades'), array('optional'=>true));

        // TOOD: outcomeid/calculations (only for new/manual/category?)

        // hidden params
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'courseid', 0);
        $mform->setType('courseid', PARAM_INT);

        $mform->addElement('hidden', 'itemtype', 0);
        $mform->setType('itemtype', PARAM_ALPHA);

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }
}
?>
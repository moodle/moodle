<?php // $Id$
require_once $CFG->libdir.'/formslib.php';

class grade_export_txt_form extends moodleform {
    function definition (){
        global $CFG;
        include_once($CFG->libdir.'/pear/HTML/QuickForm/advcheckbox.php');
        $mform =& $this->_form;
        $mform->addElement('header', 'general', 'Gradeitems to be included'); // TODO: localize
        $id = $this->_customdata['id']; // course id
        $mform->addElement('hidden', 'id', $id);
        if ($grade_items = grade_get_items($id)) {
            foreach ($grade_items as $grade_item) {
                $element = new HTML_QuickForm_advcheckbox('itemids[]', null, $grade_item->itemname, array('selected'=>'selected'), array(0, $grade_item->id));
                $element->setChecked(1);
                $mform->addElement($element);
            }
        }
        
        include_once($CFG->libdir.'/pear/HTML/QuickForm/radio.php');          
        $radio = array();
        $radio[] = &MoodleQuickForm::createElement('radio', 'separator', null, get_string('septab', 'grades'), 'tab');
        $radio[] = &MoodleQuickForm::createElement('radio', 'separator', null, get_string('sepcomma', 'grades'), 'comma');
        $mform->addGroup($radio, 'separator', get_string('separator', 'grades'), ' ', false);
        $mform->setDefault('separator', 'comma');          

        $this->add_action_buttons(false, get_string('submit'));    
    }
}
?>
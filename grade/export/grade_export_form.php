<?php // $Id$
require_once $CFG->libdir.'/formslib.php';

class grade_export_form extends moodleform {
    function definition (){
        global $CFG;
        include_once($CFG->libdir.'/pear/HTML/QuickForm/advcheckbox.php');
        $mform =& $this->_form;
        $mform->addElement('header', 'general', get_string('gradeitemsinc', 'grades')); // TODO: localize
        $id = $this->_customdata['id']; // course id
        $mform->addElement('hidden', 'id', $id);
        if ($grade_items = grade_item::fetch_all(array('courseid'=>$id))) {
            foreach ($grade_items as $grade_item) {
                $element = new HTML_QuickForm_advcheckbox('itemids[]', null, $grade_item->itemname, array('selected'=>'selected'), array(0, $grade_item->id));
                $element->setChecked(1);
                $mform->addElement($element);
            }
        }
        $this->add_action_buttons(false, get_string('submit'));    
    }
}
?>
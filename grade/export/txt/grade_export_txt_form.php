<?php // $Id$
require_once $CFG->libdir.'/formslib.php';

class grade_export_txt_form extends moodleform {
    function definition (){
        global $CFG;
        include_once($CFG->libdir.'/pear/HTML/QuickForm/advcheckbox.php');
        $mform =& $this->_form;

        $mform->addElement('advcheckbox', 'export_letters', get_string('exportletters', 'grades'));
        $mform->setDefault('export_letters', 0);
        $mform->setHelpButton('export_letters', array(false, get_string('exportletters', 'grades'),
                          false, true, false, get_string("exportlettershelp", 'grades')));

        $mform->addElement('header', 'general', 'Gradeitems to be included'); // TODO: localize
        $id = $this->_customdata['id']; // course id
        $mform->addElement('hidden', 'id', $id);
        if ($grade_items = grade_item::fetch_all(array('courseid'=>$id))) {
            foreach ($grade_items as $grade_item) {
                $element = new HTML_QuickForm_advcheckbox('itemids[]', null, $grade_item->get_name(), array('selected'=>'selected'), array(0, $grade_item->id));
                $element->setChecked(1);
                $mform->addElement($element);
            }
        }
        $options = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', 'Preview rows', $options); // TODO: localize
        $mform->setType('previewrows', PARAM_INT);
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

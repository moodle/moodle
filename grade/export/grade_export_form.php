<?php // $Id$
require_once $CFG->libdir.'/formslib.php';

class grade_export_form extends moodleform {
    function definition (){
        global $CFG;
        include_once($CFG->libdir.'/pear/HTML/QuickForm/advcheckbox.php');
        $mform =& $this->_form;
        if (isset($this->_customdata['plugin'])) {
            $plugin = $this->_customdata['plugin'];
        } else {
            $plugin = 'unknown';
        }

        $mform->addElement('header', 'options', get_string('options'));

        $mform->addElement('advcheckbox', 'export_letters', get_string('exportletters', 'grades'));
        $mform->setDefault('export_letters', 0);
        $mform->setHelpButton('export_letters', array(false, get_string('exportletters', 'grades'), false, true, false, get_string("exportlettershelp", 'grades')));

        $mform->addElement('advcheckbox', 'publish', get_string('publish', 'grades'));
        $mform->setDefault('publish', 0);
        $mform->setHelpButton('publish', array(false, get_string('publish', 'grades'), false, true, false, get_string("publishhelp", 'grades')));

        $mform->addElement('textarea', 'iplist', get_string('iplist', 'grades'), array('cols' => 40, 'rows' => 5));
        $mform->setHelpButton('iplist', array(false, get_string('iplist', 'grades'), false, true, false, get_string("iplisthelp", 'grades')));

        $mform->addElement('password', 'password', get_string('password'));
        $mform->setHelpButton('password', array(false, get_string('password', 'grades'), false, true, false, get_string("passwordhelp", 'grades')));

        $mform->addElement('header', 'general', get_string('gradeitemsinc', 'grades')); // TODO: localize

        $id = $this->_customdata['id']; // course id
        $mform->addElement('hidden', 'id', $id);
        if ($grade_items = grade_item::fetch_all(array('courseid'=>$id))) {
            $noidnumber = false;
            foreach ($grade_items as $grade_item) {

                if ($plugin != 'xmlexport' || $grade_item->idnumber) {
                    $element = new HTML_QuickForm_advcheckbox('itemids[]', null, $grade_item->get_name(), array('selected'=>'selected'), array(0, $grade_item->id));
                    $element->setChecked(1);
                } else {
                    $noidnumber = true;
                    $element = new HTML_QuickForm_advcheckbox('itemids[]', null, $grade_item->get_name(), array('disabled'=>'disabled'), array(0, $grade_item->id));
                }

                $mform->addElement($element);
            }
        }

        if ($noidnumber) {
            $mform->addElement('static', 'noidnumber',  '', get_string('noidnumber'));
        }

        $options = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', 'Preview rows', $options); // TODO: localize
        $mform->setType('previewrows', PARAM_INT);
        $this->add_action_buttons(false, get_string('submit'));
    }
}
?>

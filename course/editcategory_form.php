<?php 
require_once ($CFG->dirroot.'/course/moodleform_mod.php');
class editcategory_form extends moodleform {

    // form definition
    function definition() {
        global $CFG;
        $mform =& $this->_form;
        
        // get list of categories to use as parents, with site as the first one
        $categories = get_categories();
        $options = array(get_string('top'));
        foreach ($categories as $catid => $cat) {
            $options[$catid] = $cat->name;
        }
        
        $mform->addElement('select', 'parent', get_string('parentcategory'), $options);
        $mform->addElement('text', 'name', get_string('categoryname'), array('size'=>'30'));    
        $mform->addRule('name', get_string('required'), 'required', null);        
        $mform->addElement('htmleditor', 'description', get_string('description'));
        $mform->setType('description', PARAM_RAW);
        if (!empty($CFG->allowcategorythemes)) {
            $themes=array();
            $themes[''] = get_string('forceno');
            $themes += get_list_of_themes();
            $mform->addElement('select', 'theme', get_string('forcetheme'), $themes);
        }
        $mform->setHelpButton('description', array('writing', 'richtext'), false, 'editorhelpbutton');
        
        $mform->addElement('hidden', 'id', null);
        $mform->addElement('hidden', 'categoryadd', 0);
        $mform->setType('id', PARAM_INT);
        $this->add_action_buttons(false, get_string('submit'));
    }
} 
?>

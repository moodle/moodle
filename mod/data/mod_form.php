<?php
require_once ($CFG->dirroot.'/course/moodleform_mod.php');

class mod_data_mod_form extends moodleform_mod {

    function definition() {

        global $CFG;
        $mform =& $this->_form;

//-------------------------------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));

        $mform->addElement('text', 'name', get_string('name'), array('size'=>'64'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEAN);
        }
        $mform->addRule('name', null, 'required', null, 'client');

        $mform->addElement('htmleditor', 'intro', get_string('intro', 'data'));
        $mform->setType('intro', PARAM_RAW);
        $mform->addRule('intro', null, 'required', null, 'client');
        $mform->setHelpButton('intro', array('writing', 'questions', 'richtext'), false, 'editorhelpbutton');

        $mform->addElement('date_selector', 'timeavailablefrom', get_string('availablefromdate', 'data'), array('optional'=>true));

        $mform->addElement('date_selector', 'timeavailableto', get_string('availabletodate', 'data'), array('optional'=>true));

        $mform->addElement('date_selector', 'timeviewfrom', get_string('viewfromdate', 'data'), array('optional'=>true));

        $mform->addElement('date_selector', 'timeviewto', get_string('viewtodate', 'data'), array('optional'=>true));


        $countoptions = array(0=>get_string('none'))+
                        (array_combine(range(1, DATA_MAX_ENTRIES),//keys
                                        range(1, DATA_MAX_ENTRIES)));//values
        $mform->addElement('select', 'requiredentries', get_string('requiredentries', 'data'), $countoptions);
        $mform->setHelpButton('requiredentries', array('requiredentries', get_string('requiredentries', 'data'), 'data'));

        $mform->addElement('select', 'requiredentriestoview', get_string('requiredentriestoview', 'data'), $countoptions);
        $mform->setHelpButton('requiredentriestoview', array('requiredentriestoview', get_string('requiredentriestoview', 'data'), 'data'));

        $mform->addElement('select', 'maxentries', get_string('maxentries', 'data'), $countoptions);
        $mform->setHelpButton('maxentries', array('maxentries', get_string('maxentries', 'data'), 'data'));

        $ynoptions = array(0 => get_string('no'), 1 => get_string('yes'));
        $mform->addElement('select', 'comments', get_string('comments', 'data'), $ynoptions);
        $mform->setHelpButton('comments', array('comments', get_string('allowcomments', 'data'), 'data'));

        $mform->addElement('select', 'approval', get_string('requireapproval', 'data'), $ynoptions);
        $mform->setHelpButton('approval', array('requireapproval', get_string('requireapproval', 'data'), 'data'));

        if($CFG->enablerssfeeds && $CFG->data_enablerssfeeds){
            $mform->addElement('select', 'rssarticles', get_string('numberrssarticles', 'data') , $countoptions);
        }

        $mform->addElement('checkbox', 'assessed', get_string('allowratings', 'data') , get_string('ratingsuse', 'data'));

        $mform->addElement('modgrade', 'scale', get_string('grade'), false);
        $mform->disabledIf('scale', 'assessed');


        $this->standard_coursemodule_elements(array('groups'=>true, 'groupings'=>true, 'groupmembersonly'=>true));

//-------------------------------------------------------------------------------
        // buttons
        $this->add_action_buttons();
    }

    function data_preprocessing(&$default_values){
        if (empty($default_values['scale'])){
            $default_values['assessed'] = 0;
        }        
    }

}
?>

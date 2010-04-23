<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_label_form extends feedback_item_form {
    var $type = "label";
    var $area;

    function definition() {
        global $CFG;
        
        $item = $this->_customdata['item'];
        $common = $this->_customdata['common'];
        $presentationoptions = $this->_customdata['presentationoptions'];
        $positionlist = $this->_customdata['positionlist'];
        $position = $this->_customdata['position'];
        
        $context = get_context_instance(CONTEXT_MODULE, $common['cmid']);

        $mform =& $this->_form;

        $mform->addElement('hidden', 'required', 0);
        $mform->setType('required', PARAM_INT);
        $mform->addElement('hidden', 'name', 'label');
        $mform->setType('template', PARAM_ALPHA);
        $mform->addElement('hidden', 'label', '-');
        $mform->setType('label', PARAM_ALPHA);
        
        
        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $mform->addElement('editor', 'presentation_editor', '', null, $presentationoptions);
        $mform->setType('presentation_editor', PARAM_CLEANHTML);
    
        
        ////////////////////////////////////////////////////////////////////////
        //the following is used in all itemforms
        ////////////////////////////////////////////////////////////////////////
        $position_select = $mform->addElement('select',
                                            'position',
                                            get_string('position', 'feedback').'&nbsp;',
                                            $positionlist);
        $position_select->setValue($position);
        

        $mform->addElement('hidden', 'cmid', $common['cmid']);
        $mform->setType('cmid', PARAM_INT);
        
        $mform->addElement('hidden', 'id', $common['id']);
        $mform->setType('id', PARAM_INT);
        
        $mform->addElement('hidden', 'feedback', $common['feedback']);
        $mform->setType('feedback', PARAM_INT);
        
        $mform->addElement('hidden', 'template', 0);
        $mform->setType('template', PARAM_INT);
        
        $mform->addElement('hidden', 'typ', $this->type);
        $mform->setType('typ', PARAM_ALPHA);

        $mform->addElement('hidden', 'hasvalue', 0);
        $mform->setType('hasvalue', PARAM_INT);


        $buttonarray = array();
        if(!empty($item->id)){
            $buttonarray[] = &$mform->createElement('submit', 'update_item', get_string('update_item', 'feedback'));
        }else{
            $buttonarray[] = &$mform->createElement('submit', 'save_item', get_string('save_item', 'feedback'));
        }
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '&nbsp;', array(' '), false);
        
        $this->set_data($item);

    }

}


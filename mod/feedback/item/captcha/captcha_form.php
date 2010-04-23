<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_captcha_form extends feedback_item_form {
    var $type = "captcha";

    function definition() {
        
        $item = $this->_customdata['item'];
        $common = $this->_customdata['common'];
        $positionlist = $this->_customdata['positionlist'];
        $position = $this->_customdata['position'];
        
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $mform->addElement('checkbox', 'required', get_string('required', 'feedback'));
        $mform->addElement('text', 'name', get_string('item_name', 'feedback'), array('size="'.FEEDBACK_ITEM_NAME_TEXTBOX_SIZE.'"','maxlength="255"'));
        $mform->addElement('text', 'label', get_string('item_label', 'feedback'), array('size="'.FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE.'"','maxlength="255"'));

        $mform->addElement('select',
                            'presentation',
                            get_string('count_of_nums', 'feedback').'&nbsp;',
                            array_slice(range(0,10),3,10,true));

        
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
        
        $mform->addElement('hidden', 'name', 'label');
        $mform->setType('template', PARAM_ALPHA);
        
        $mform->addElement('hidden', 'label', '-');
        $mform->setType('label', PARAM_ALPHA);
        
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


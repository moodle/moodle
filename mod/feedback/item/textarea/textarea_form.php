<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_textarea_form extends feedback_item_form {
    var $type = "textarea";
    var $requiredcheck;
    var $itemname;
    var $itemlabel;
    var $selectwidth;
    var $selectheight;

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $this->requiredcheck = $mform->addElement('checkbox', 'required', get_string('required', 'feedback'));

        $this->itemname = $mform->addElement('text', 'itemname', get_string('item_name', 'feedback'), array('size="'.FEEDBACK_ITEM_NAME_TEXTBOX_SIZE.'"','maxlength="255"'));
        $this->itemlabel = $mform->addElement('text', 'itemlabel', get_string('item_label', 'feedback'), array('size="'.FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE.'"','maxlength="255"'));

        $this->selectwidth = $mform->addElement('select',
                                            'itemwidth',
                                            get_string('textarea_width', 'feedback').'&nbsp;',
                                            array_slice(range(0,80),5,80,true));

        $this->selectheight = $mform->addElement('select',
                                            'itemheight',
                                            get_string('textarea_height', 'feedback').'&nbsp;',
                                            array_slice(range(0,40),5,40,true));

        
        ////////////////////////////////////////////////////////////////////////
        //the following is used in all itemforms
        ////////////////////////////////////////////////////////////////////////
        $item = $this->_customdata['item'];
        $common = $this->_customdata['common'];

        $mform->addElement('hidden', 'cmid', $common['cmid']);
        $mform->setType('cmid', PARAM_INT);
        $mform->addElement('hidden', 'id', $common['id']);
        $mform->setType('id', PARAM_INT);
        $mform->addElement('hidden', 'typ', $common['typ']);
        $mform->setType('typ', PARAM_ALPHA);
        $mform->addElement('hidden', 'feedbackid', $common['feedbackid']);
        $mform->setType('feedbackid', PARAM_INT);

        $position_select = $mform->addElement('select',
                                            'position',
                                            get_string('position', 'feedback').'&nbsp;',
                                            $this->_customdata['positionlist']);
        $position_select->setValue($this->_customdata['position']);
        

        $buttonarray = array();
        if(!empty($item->id)){
            $mform->addElement('hidden', 'updateitem', '1');
            $mform->setType('updateitem', PARAM_INT);
            // $i_form->addElement('submit', 'update_item', get_string('update_item', 'feedback'));
            $buttonarray[] = &$mform->createElement('submit', 'update_item', get_string('update_item', 'feedback'));
        }else{
            $mform->addElement('hidden', 'saveitem', '1');
            $mform->setType('saveitem', PARAM_INT);
            // $i_form->addElement('submit', 'save_item', get_string('save_item', 'feedback'));
            $buttonarray[] = &$mform->createElement('submit', 'save_item', get_string('save_item', 'feedback'));
        }
        // $i_form->addElement('cancel');
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '&nbsp;', array(' '), false);

    }
}


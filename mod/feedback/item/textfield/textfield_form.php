<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_textfield_form extends feedback_item_form {
    var $type = "textfield";
    var $requiredcheck;
    var $itemname;
    var $itemlabel;
    var $selectwith;
    var $selectheight;

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $this->requiredcheck = $mform->addElement('checkbox', 'required', get_string('required', 'feedback'));

        $this->itemname = $mform->addElement('text', 'itemname', get_string('item_name', 'feedback'), array('size="'.FEEDBACK_ITEM_NAME_TEXTBOX_SIZE.'"','maxlength="255"'));
        $this->itemlabel = $mform->addElement('text', 'itemlabel', get_string('item_label', 'feedback'), array('size="'.FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE.'"','maxlength="255"'));

        $this->selectwith = $mform->addElement('select',
                                            'itemsize',
                                            get_string('textfield_size', 'feedback').'&nbsp;',
                                            array_slice(range(0,255),5,255,true));

        $this->selectheight = $mform->addElement('select',
                                            'itemmaxlength',
                                            get_string('textfield_maxlength', 'feedback').'&nbsp;',
                                            array_slice(range(0,255),5,255,true));

        
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


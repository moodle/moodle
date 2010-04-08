<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_info_form extends feedback_item_form {
    var $type = "info";
    var $requiredcheck;
    var $itemname;
    var $itemlabel;
    var $infotype;

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));
        $this->requiredcheck = &$mform->addElement('hidden', 'required');
        $mform->setType('required', PARAM_INT);

        $this->itemname = &$mform->addElement('text', 'itemname', get_string('item_name', 'feedback'), array('size="'.FEEDBACK_ITEM_NAME_TEXTBOX_SIZE.'"','maxlength="255"'));
        $this->itemlabel = $mform->addElement('text', 'itemlabel', get_string('item_label', 'feedback'), array('size="'.FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE.'"','maxlength="255"'));

        $options=array();
        $options[1]  = get_string('responsetime', 'feedback');
        $options[2]  = get_string('coursename', 'feedback');
        $options[3]  = get_string('coursecategory', 'feedback');
        $this->infotype = &$mform->addElement('select', 'infotype', get_string('infotype', 'feedback'), $options);

        
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


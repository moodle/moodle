<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_label_form extends feedback_item_form {
    var $type = "label";
    var $area;

    function definition() {
        $mform =& $this->_form;

        $mform->addElement('header', 'general', get_string($this->type, 'feedback'));

        $mform->addElement('hidden', 'itemname', $this->type);
        $mform->setType('itemname', PARAM_INT);
        $mform->addElement('editor', 'presentation', '', null, null);
        $mform->setType('presentation', PARAM_CLEANHTML);
    
        
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

    function get_data() {
        $data = parent::get_data();
        if ($data) {
            $data->presentation = $data->presentation['text'];
        }
        return $data;
    }

    function set_data($data) {
        if(isset($data->presentation)) {
            $data->presentation = array('text'=>$data->presentation, 'format'=>FORMAT_HTML, 'itemid'=>0);
        }
        return parent::set_data($data);
    }
}


<?php

require_once($CFG->dirroot.'/mod/feedback/item/feedback_item_form_class.php');

class feedback_numeric_form extends feedback_item_form {
    var $type = "numeric";

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

        $mform->addElement('text', 'rangefrom', get_string('numeric_range_from', 'feedback'), array('size="10"','maxlength="10"'));

        $mform->addElement('text', 'rangeto', get_string('numeric_range_to', 'feedback'), array('size="10"','maxlength="10"'));

        
        ////////////////////////////////////////////////////////////////////////
        //the following is used in all itemforms
        ////////////////////////////////////////////////////////////////////////
        //itemdepending
        if($common['items']) {
            $mform->addElement('select',
                                'dependitem',
                                get_string('dependitem', 'feedback').'&nbsp;',
                                $common['items']
                                );
            $mform->addHelpButton('dependitem', 'depending', 'feedback');
            $mform->addElement('text', 'dependvalue', get_string('dependvalue', 'feedback'), array('size="'.FEEDBACK_ITEM_LABEL_TEXTBOX_SIZE.'"','maxlength="255"'));
        }else {
            $mform->addElement('hidden', 'dependitem', 0);
            $mform->setType('dependitem', PARAM_INT);
            $mform->addElement('hidden', 'dependvalue', '');
            $mform->setType('dependitem', PARAM_ALPHA);
        }

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
        
        $mform->setType('name', PARAM_RAW);
        $mform->setType('label', PARAM_ALPHANUM);
        
        $mform->addElement('hidden', 'typ', $this->type);
        $mform->setType('typ', PARAM_ALPHA);

        $mform->addElement('hidden', 'hasvalue', 0);
        $mform->setType('hasvalue', PARAM_INT);

        $mform->addElement('hidden', 'options', '');
        $mform->setType('options', PARAM_ALPHA);

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

    function get_data() {
        if(!$item = parent::get_data()) {
            return false;
        }
        
        $itemobj = new feedback_item_numeric();

        $num1 = str_replace($itemobj->sep_dec, FEEDBACK_DECIMAL, $item->rangefrom);
        if(is_numeric($num1)) {
            $num1 = floatval($num1);
        }else {
            $num1 = '-';
        }

        $num2 = str_replace($itemobj->sep_dec, FEEDBACK_DECIMAL, $item->rangeto);
        if(is_numeric($num2)) {
            $num2 = floatval($num2);
        }else {
            $num2 = '-';
        }

        if($num1 === '-' OR $num2 === '-') {
            $item->presentation = $num1 . '|'. $num2;
            return $item;
        }

        if($num1 > $num2) {
            $item->presentation =  $num2 . '|'. $num1;
        }else {
            $item->presentation = $num1 . '|'. $num2;
        }
        return $item;
    }

}


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
    }

    function get_data() {
        $data = parent::get_data();
        if ($data) {
            $data->presentation = $data->presentation['text'];
        }
        return $data;
    }

    function set_data($data) {
        $data->presentation = array('text'=>$data->presentation, 'format'=>FORMAT_HTML, 'itemid'=>0);
        return parent::set_data($data);
    }
}


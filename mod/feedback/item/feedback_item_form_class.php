<?php

require_once $CFG->libdir.'/formslib.php';

define('FEEDBACK_ITEM_NAME_TEXTBOX_SIZE', 80);
class feedback_item_form extends moodleform {
    
    function get_item_form() {
        return $this->_form;
    }
}
?>

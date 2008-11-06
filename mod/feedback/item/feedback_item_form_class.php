<?php

require_once $CFG->libdir.'/formslib.php';

class feedback_item_form extends moodleform {
    
    function get_item_form() {
        return $this->_form;
    }
}
?>

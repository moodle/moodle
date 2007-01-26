<?php //$Id$

class profile_field_menu extends profile_field_base {
    var $options;

    function profile_field_menu($fieldid) {
        //first call parent constructor
        $this->profile_field_base($fieldid);

        /// Param 1 for menu type is the options
        $options = explode("\n", $this->field->param1);
        $this->options = array();
        foreach($options as $option) {
            $this->options[] = format_string($option);//multilang formatting
        }

    }

    function display_field_add(&$mform) {
        /// Create the form field
        $mform->addElement('select', $this->inputname, format_string($this->field->name), $this->options);
    }

    /// Override base class method
    function display_field_default(&$mform) {
        $defaultkey = (int)array_search($field->defaultdata, $this->options);
        $mform->setDefault($this->inputname, $defaultkey);
    }
}

?>

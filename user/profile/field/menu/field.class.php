<?php //$Id$

class profile_field_menu extends profile_field_base {
    var $options;
    var $selected;

    function profile_field_menu($fieldid) {
        //first call parent constructor
        $this->profile_field_base($fieldid);

        /// Param 1 for menu type is the options
        $options = explode("\n", $this->field->param1);
        $this->options = array();
        foreach($options as $key => $option) {
            $this->options[$key] = format_string($option);//multilang formatting
            if ($option == $this->field->defaultdata) {
                $this->selected = $key;
            }
        }

    }

    function display_field_add(&$mform) {
        /// Create the form field
        $mform->addElement('select', $this->inputname, format_string($this->field->name), $this->options);
        $mform->setDefault($this->inputname, $this->selected);
    }

    /// Override base class method
    function display_field_default(&$mform) {
        $defaultkey = (int)array_search($field->defaultdata, $this->options);
        $mform->setDefault($this->inputname, $defaultkey);
    }
}

?>

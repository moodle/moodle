<?php //$Id$

class profile_field_menu extends profile_field_base {

    function init() {
        /// Param 1 for menu type is the options
        if (($options = explode("\n", $this->field->param1)) === false) {
            $options = array();
        }
        $this->options = $options;
    }

    function display_field_add(&$form) {
        /// Create the form field
        $form->addElement('select', $this->fieldname, $this->field->name, $this->options);
        $form->setType($this->fieldname, PARAM_INT);
    }

    /// Override base class method
    function display_field_default(&$form) {
        /// Default data is either what user has already set othewise the default value for the field othewise nothing
        if (!($default = get_field('user_info_data', 'data', 'userid', $this->userid, 'fieldid', $this->field->id))) {
            $default = (empty($this->field->defaultdata)) ? '' : $this->field->defaultdata;
        }
        if (($defaultkey = array_search($default, $this->options)) === NULL) {
            $defaultkey = 0;
        }
        $form->setDefault($this->fieldname, $defaultkey);
    }

    function set_data_type() {
        $this->datatype = 'menu';
    }

    function validate_data($data) {
        if ($data >= count($this->options)) {
            return get_string('invaliddata');
        } else {
            return '';
        }
    }

    function save_data_preprocess($data) {
        if (!isset($this->options[$data])) { /// validate_data should already have caught this
            return '';
        } else {
            return $this->options[$data];
        }
    }
}

?>

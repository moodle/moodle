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
            return get_string('profileinvaliddata', 'admin');
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

    function edit_field_specific(&$form) {
        /// Param 1 for menu type contains the options
        $form->addElement('textarea', 'param1', get_string('profilemenuoptions', 'admin'));
        $form->setType('param1', PARAM_MULTILANG);
        
        /// Default data
        $form->addElement('text', 'defaultdata', get_string('profiledefaultdata', 'admin'), 'size="30"');
        $form->setType('defaultdata', PARAM_MULTILANG);
    }

    function edit_validate_specific($data) {
        $err = array();
        
        /// Check that we have at least 2 options
        if (($options = explode("\n", $data->param1)) === false) {
            $err['param1'] = get_string('profilemenunooptions', 'admin');
        } elseif (count($options) < 2) {
            $err['param1'] = get_string('profilemenutoofewoptions', 'admin');
            
        /// Check the default data exists in the options
        } elseif (!empty($data->defaultdata) and !in_array($data->defaultdata, $options)) {
            $err['defaultdata'] = get_string('profilemenudefaultnotinoptions', 'admin');
        }
        return $err;        
    }
}

?>

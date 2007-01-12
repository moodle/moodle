<?php //$Id$

class profile_field_text extends profile_field_base {

    function display_field_add(&$form) {
        /// Param 1 for text type is the size of the field
        $size = (empty($this->field->param1)) ? '30' : $this->field->param1;

        /// Param 2 for text type is the maxlength of the field
        $maxlength = (empty($this->field->param2)) ? '2048' : $this->field->param2;

        /// Create the form field
        $form->addElement('text', $this->fieldname, $this->field->name, 'maxlength="'.$maxlength.'" size="'.$size.'" ');
        $form->setType($this->fieldname, PARAM_MULTILANG);
    }

    function set_data_type() {
        $this->datatype = 'text';
    }

    function edit_field_specific(&$form) {
        /// Default data
        $form->addElement('text', 'defaultdata', get_string('profiledefaultdata', 'admin'), 'size="30"');
        $form->setType('defaultdata', PARAM_MULTILANG);

        /// Param 1 for text type is the size of the field
        $form->addElement('text', 'param1', get_string('profilefieldsize', 'admin'), 'size="6"');
        $form->setType('param1', PARAM_INT);
        
        /// Param 2 for text type is the maxlength of the field
        $form->addElement('text', 'param2', get_string('profilefieldmaxlength', 'admin'), 'size="6"');
        $form->setType('param2', PARAM_INT);
    }

}

?>

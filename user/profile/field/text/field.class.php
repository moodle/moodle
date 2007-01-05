<?php //$Id$

class profile_field_text extends profile_field_base {

    function display_field_add(&$form) {
        /// Param 1 for text type is the size of the field
        $size = (empty($this->field->param1)) ? '30' : $this->field->param1;

        /// Param 2 for text type is the maxlength of the field
        $maxlength = (empty($this->field->param2)) ? '254' : $this->field->param2;

        /// Create the form field
        $form->addElement('text', $this->fieldname, $this->field->name, 'maxlength="'.$maxlength.'" size="'.$size.'" ');
        $form->setType($this->fieldname, PARAM_MULTILANG);
    }

    function set_data_type() {
        $this->datatype = 'text';
    }
}

?>

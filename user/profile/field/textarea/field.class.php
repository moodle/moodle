<?php

class profile_field_textarea extends profile_field_base {

    function edit_field_add(&$mform) {
        $cols = $this->field->param1;
        $rows = $this->field->param2;

        /// Create the form field
        $mform->addElement('editor', $this->inputname, format_string($this->field->name), null, null);
        $mform->setType($this->inputname, PARAM_RAW); // we MUST clean this before display!
    }

    /// Overwrite base class method, data in this field type is potentially too large to be
    /// included in the user object
    function is_user_object_data() {
        return false;
    }

    function edit_save_data_preprocess($data, &$datarecord) {
        if (is_array($data)) {
            $datarecord->dataformat = $data['format'];
            $data = $data['text'];
        }
        return $data;
    }

    function edit_load_user_data(&$user) {
        if ($this->data !== NULL) {
            $this->data = clean_text($this->data, $this->dataformat);
            $user->{$this->inputname} = array('text'=>$this->data, 'format'=>$this->dataformat);
        }
    }

    /**
     * Display the data for this field
     */
    function display_data() {
        return format_text($this->data, $this->dataformat, array('overflowdiv'=>true));
    }

}



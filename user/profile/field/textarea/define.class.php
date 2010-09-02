<?php

class profile_define_textarea extends profile_define_base {

    function define_form_specific(&$form) {
        /// Default data
        $form->addElement('editor', 'defaultdata', get_string('profiledefaultdata', 'admin'));
        $form->setType('defaultdata', PARAM_RAW); // we have to trust person with capability to edit this default description
    }

    function define_editors() {
        return array('defaultdata');
    }

}



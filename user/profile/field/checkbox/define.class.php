<?php  // $Id$

class profile_define_checkbox extends profile_define_base {

    function define_form_specific(&$form) {
        /// select whether or not this should be checked by default
        $form->addElement('selectyesno', 'defaultdata', get_string('profiledefaultchecked', 'admin'));
        $form->setDefault('defaultdata', 0); // defaults to 'no'
        $form->setType('defaultdata', PARAM_BOOL);
    }
}

?>

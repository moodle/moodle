<?php

// This is one "supplanter" form that generates
// one correct forgot_password.php request in
// order to get the mailout for 'restored' users
// working automatically without having to
// fill the form manually (the user already has
// filled the username and it has been detected
// as a 'restored' one. Surely, some day this will
// be out, with the forgot_password utility being
// part of each plugin, but now now. See MDL-20846
// for the rationale for this implementation.

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class login_forgot_password_form extends moodleform {

    function definition() {
        $mform    =& $this->_form;

        $username = $this->_customdata['username'];

        $mform->addElement('hidden', 'username', $username);
        $mform->setType('username', PARAM_RAW);

        $this->add_action_buttons(false, get_string('continue'));
    }

}

?>

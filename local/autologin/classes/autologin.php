<?php

namespace local_autologin;

defined('MOODLE_INTERNAL') || die();

class autologin {

    public static function attempt_autologin() {
        global $CFG, $DB, $USER;

        // Check if the request contains the idnumber parameter.
        $idnumber = optional_param('nin', '', PARAM_TEXT);

        //De-Obfuscate ID Number
        

        if (!empty($idnumber)) {
            // Attempt to find the user with the provided idnumber.
            $user = $DB->get_record('user', array('idnumber' => $idnumber));

            if ($user) {
                // Log in the user.
                complete_user_login($user);
                redirect($CFG->wwwroot);
            }
        }
    }
}

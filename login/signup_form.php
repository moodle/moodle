<?php  // $Id$

require_once($CFG->libdir.'/formslib.php');
require_once($CFG->dirroot.'/user/profile/lib.php');

class login_signup_form extends moodleform {
    function definition() {
        global $USER, $CFG;

        $mform =& $this->_form;

        $mform->addElement('header', '', get_string('createuserandpass'), '');


        $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="12"');
        $mform->setType('username', PARAM_NOTAGS);
        $mform->addRule('username', get_string('missingusername'), 'required', null, 'server');

        $mform->addElement('passwordunmask', 'password', get_string('password'), 'maxlength="32" size="12"');
        $mform->setType('password', PARAM_RAW);
        $mform->addRule('password', get_string('missingpassword'), 'required', null, 'server');

        $mform->addElement('header', '', get_string('supplyinfo'),'');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25"');
        $mform->setType('email', PARAM_NOTAGS);
        $mform->addRule('email', get_string('missingemail'), 'required', null, 'server');

        $mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" size="25"');
        $mform->setType('email2', PARAM_NOTAGS);
        $mform->addRule('email2', get_string('missingemail'), 'required', null, 'server');

        $nameordercheck = new object();
        $nameordercheck->firstname = 'a';
        $nameordercheck->lastname  = 'b';
        if (fullname($nameordercheck) == 'b a' ) {  // See MDL-4325
            $mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30"');
            $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
        } else {
            $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
            $mform->addElement('text', 'lastname',  get_string('lastname'),  'maxlength="100" size="30"');
        }

        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('missingfirstname'), 'required', null, 'server');

        $mform->setType('lastname', PARAM_TEXT);
        $mform->addRule('lastname', get_string('missinglastname'), 'required', null, 'server');

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="20" size="20"');
        $mform->setType('city', PARAM_TEXT);
        $mform->addRule('city', get_string('missingcity'), 'required', null, 'server');

        $country = get_list_of_countries();
        $default_country[''] = get_string('selectacountry');
        $country = array_merge($default_country, $country);
        $mform->addElement('select', 'country', get_string('country'), $country);
        $mform->addRule('country', get_string('missingcountry'), 'required', null, 'server');
        $mform->setDefault('country', '');

        profile_signup_fields($mform);

        if (!empty($CFG->sitepolicy)) {
            $mform->addElement('header', '', get_string('policyagreement'), '');
            $mform->addElement('static', 'policylink', '', '<a href="'.$CFG->sitepolicy.'" onclick="this.target=\'_blank\'">'.get_String('policyagreementclick').'</a>');
            $mform->addElement('checkbox', 'policyagreed', get_string('policyaccept'));
            $mform->addRule('policyagreed', get_string('policyagree'), 'required', null, 'server');
        }

        // buttons
        $this->add_action_buttons(true, get_string('createaccount'));

    }

    function definition_after_data(){
        $mform =& $this->_form;

        $mform->applyFilter('username', 'moodle_strtolower');
        $mform->applyFilter('username', 'trim');
    }

    function validation($data) {
        global $CFG;
        $errors = array();

        $authplugin = get_auth_plugin($CFG->registerauth);

        if (record_exists('user', 'username', $data['username'], 'mnethostid', $CFG->mnet_localhost_id)) {
            $errors['username'] = get_string('usernameexists');
        } else {
            if (empty($CFG->extendedusernamechars)) {
                $string = eregi_replace("[^(-\.[:alnum:])]", '', $data['username']);
                if (strcmp($data['username'], $string)) {
                    $errors['username'] = get_string('alphanumerical');
                }
            }
        }

        //check if user exists in external db
        //TODO: maybe we should check all enabled plugins instead
        if ($authplugin->user_exists($data['username'])) {
            $errors['username'] = get_string('usernameexists');
        }


        if (! validate_email($data['email'])) {
            $errors['email'] = get_string('invalidemail');

        } else if (record_exists('user', 'email', $data['email'])) {
            $errors['email'] = get_string('emailexists').' <a href="forgot_password.php">'.get_string('newpassword').'?</a>';
        }
        if (empty($data['email2'])) {
            $errors['email2'] = get_string('missingemail');

        } else if ($data['email2'] != $data['email']) {
            $errors['email2'] = get_string('invalidemail');
        }
        if (!isset($errors['email'])) {
            if ($err = email_is_not_allowed($data['email'])) {
                $errors['email'] = $err;
            }

        }

        if (!check_password_policy($data['password'], $errmsg)) {
            $errors['password'] = $errmsg;
        }

        if (0 == count($errors)){
            return true;
        } else {
            return $errors;
        }


    }
}

?>

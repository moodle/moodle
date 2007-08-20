<?php // $Id$
require_once $CFG->libdir.'/formslib.php';

class admin_uploaduser_form extends moodleform {
    function definition (){
        global $CFG;
        $templateuser = $this->_customdata;
        if(empty($templateuser)) {
            if (!$templateuser = get_admin()) {
                error('Could not find site admin');
            }
        }
        
        $mform =& $this->_form;

        $mform->addElement('file', 'userfile', get_string('file'));
        $mform->addRule('userfile', null, 'required');

        $mform->addElement('header', 'defaultheader', get_string('defaultvalues', 'admin'));
        $mform->addElement('text', 'username', get_string('username'), 'size="20"');

        $modules = get_list_of_plugins('auth');
        $auth_options = array();
        foreach ($modules as $module) {
            $auth_options[$module] = get_string("auth_$module"."title", "auth");
        }
        $mform->addElement('select', 'auth', get_string('chooseauthmethod','auth'), $auth_options);
        $mform->setDefault('auth', $templateuser->auth);
        $mform->setHelpButton('auth', array('authchange', get_string('chooseauthmethod','auth')));

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');
        $choices = array(
            get_string('emaildisplayno'),
            get_string('emaildisplayyes'),
            get_string('emaildisplaycourse'),
        );
        $mform->addElement('select', 'maildisplay', get_string('emaildisplay'), $choices);
        $mform->setDefault('maildisplay', 2);

        $choices = array(
            get_string('emailenable'),
            get_string('emaildisable'),
        );
        $mform->addElement('select', 'emailstop', get_string('emailactive'), $choices);

        $choices = array(
            get_string('textformat'),
            get_string('htmlformat'),
        );
        $mform->addElement('select', 'mailformat', get_string('emailformat'), $choices);
        $mform->setDefault('mailformat', 1);

        $choices = array(
            get_string('autosubscribeyes'),
            get_string('autosubscribeno'),
        );
        $mform->addElement('select', 'autosubscribe', get_string('autosubscribe'), $choices);
        $mform->setDefault('autosubscribe', 1);

        if ($CFG->htmleditor) {
            $choices = array(
                get_string('texteditor'),
                get_string('htmleditor'),
            );
            $mform->addElement('select', 'htmleditor', get_string('textediting'), $choices);
            $mform->setDefault('htmleditor', 1);
        }

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="100" size="25"');
        $mform->setType('city', PARAM_MULTILANG);
        $mform->setDefault('city', $templateuser->city);
        
        $mform->addElement('select', 'country', get_string('selectacountry'), get_list_of_countries());
        $mform->setDefault('country', $templateuser->country);

        $choices = get_list_of_timezones();
        $choices['99'] = get_string('serverlocaltime');
        $mform->addElement('select', 'timezone', get_string('timezone'), $choices);
        $mform->setDefault('timezone', $templateuser->timezone);

        $mform->addElement('select', 'lang', get_string('preferredlanguage'), get_list_of_languages());
        $mform->setDefault('lang', $templateuser->lang);
        
        $mform->addElement('htmleditor', 'description', get_string('userdescription'));
        $mform->setType('description', PARAM_CLEAN);
        $mform->setHelpButton('description', array('text', get_string('helptext')));

        $mform->addElement('text', 'url', get_string('webpage'), 'maxlength="255" size="50"');

        $mform->addElement('text', 'institution', get_string('institution'), 'maxlength="40" size="25"');
        $mform->setType('institution', PARAM_MULTILANG);
        $mform->setDefault('institution', $templateuser->institution);

        $mform->addElement('text', 'department', get_string('department'), 'maxlength="30" size="25"');
        $mform->setType('department', PARAM_MULTILANG);
        $mform->setDefault('department', $templateuser->department);

        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="25"');
        $mform->setType('phone1', PARAM_CLEAN);

        $mform->addElement('text', 'phone2', get_string('phone'), 'maxlength="20" size="25"');
        $mform->setType('phone2', PARAM_CLEAN);

        $mform->addElement('text', 'address', get_string('address'), 'maxlength="70" size="25"');
        $mform->setType('address', PARAM_MULTILANG);
        
        $mform->addElement('header', 'settingsheader', get_string('settings'));

        $choices = array(
            get_string('infilefield', 'auth'),
            get_string('createpasswordifneeded', 'auth'),
        );
        $mform->addElement('select', 'createpassword', get_string('passwordhandling', 'auth'), $choices);

        $mform->addElement('selectyesno', 'updateaccounts', get_string('updateaccounts', 'admin'));
        $mform->addElement('selectyesno', 'allowrenames', get_string('allowrenames', 'admin'));

        $choices = array(
            get_string('addcounter', 'admin'),
            get_string('skipuser', 'admin'),
        );
        $mform->addElement('select', 'duplicatehandling', get_string('newusernamehandling', 'admin'), $choices);

        $this->add_action_buttons(false, get_string('uploadusers'));
    }

    function get_userfile_name(){
        if ($this->is_submitted() and $this->is_validated()) {
            // return the temporary filename to process
            return $this->_upload_manager->files['userfile']['tmp_name'];
        }else{
            return  NULL;
        }
    }
}
?>

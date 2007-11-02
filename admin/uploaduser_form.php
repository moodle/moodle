<?php // $Id$
require_once $CFG->libdir.'/formslib.php';

class admin_uploaduser_form1 extends moodleform {
    function definition (){
        global $CFG, $USER;

        $this->set_upload_manager(new upload_manager('userfile', false, false, null, false, 0, true, true, false));

        $mform =& $this->_form;

        $mform->addElement('header', 'settingsheader', get_string('upload'));

        $mform->addElement('file', 'userfile', get_string('file'));
        $mform->addRule('userfile', null, 'required');

        $choices = array('comma'=>',', 'semicolon'=>';', 'colon'=>':', 'tab'=>'\\t');
        if (isset($CFG->CSV_DELIMITER) and !in_array($CFG->CSV_DELIMITER, $choices)) {
            $choices['cfg'] = $CFG->CSV_DELIMITER; 
        }
        $mform->addElement('select', 'separator', get_string('csvseparator', 'admin'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('separator', 'cfg');
        } else if (get_string('listsep') == ';') {
            $mform->setDefault('separator', 'semicolon');
        } else {
            $mform->setDefault('separator', 'comma');
        }

        $textlib = textlib_get_instance();
        $choices = $textlib->get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'admin'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        $choices = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'admin'), $choices);
        $mform->setType('previewrows', PARAM_INT);

        $this->add_action_buttons(false, get_string('uploadusers'));
    }
}

class admin_uploaduser_form2 extends moodleform {
    function definition (){
        global $CFG, $USER;

        $mform =& $this->_form;

        // I am the tamplate user
        $templateuser = $USER;

// upload settings and file
        $mform->addElement('header', 'settingsheader', get_string('settings'));

        $choices = array(0 => get_string('infilefield', 'auth'), 1 => get_string('createpasswordifneeded', 'auth'));
        $mform->addElement('select', 'createpassword', get_string('passwordhandling', 'auth'), $choices);

        $mform->addElement('selectyesno', 'updateaccounts', get_string('updateaccounts', 'admin'));
        $mform->addElement('selectyesno', 'allowrenames', get_string('allowrenames', 'admin'));

        $choices = array(0 => get_string('addcounter', 'admin'), 1 => get_string('skipuser', 'admin'));
        $mform->addElement('select', 'duplicatehandling', get_string('newusernamehandling', 'admin'), $choices);
        $mform->setDefault('duplicatehandling', 1); // better skip, bc and safer

// default values
        $mform->addElement('header', 'defaultheader', get_string('defaultvalues', 'admin'));
        $mform->addElement('text', 'username', get_string('username'), 'size="20"');

        // only enabled plugins
        $aplugins = get_enabled_auth_plugins();
        $auth_options = array();
        foreach ($aplugins as $module) {
            $auth_options[$module] = get_string('auth_'.$module.'title', 'auth');
        }
        $mform->addElement('select', 'auth', get_string('chooseauthmethod','auth'), $auth_options);
        $mform->setDefault('auth', 'manual'); // manual is a sensible backwards compatible default
        $mform->setHelpButton('auth', array('authchange', get_string('chooseauthmethod','auth')));
        $mform->setAdvanced('auth');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');

        $choices = array(0 => get_string('emaildisplayno'), 1 => get_string('emaildisplayyes'), 2 => get_string('emaildisplaycourse'));
        $mform->addElement('select', 'maildisplay', get_string('emaildisplay'), $choices);
        $mform->setDefault('maildisplay', 2);

        $choices = array(0 => get_string('emailenable'), 1 => get_string('emaildisable'));
        $mform->addElement('select', 'emailstop', get_string('emailactive'), $choices);

        $choices = array(0 => get_string('textformat'), 1 => get_string('htmlformat'));
        $mform->addElement('select', 'mailformat', get_string('emailformat'), $choices);
        $mform->setDefault('mailformat', 1);
        $mform->setAdvanced('mailformat');

        $choices = array(0 => get_string('autosubscribeyes'), 1 => get_string('autosubscribeno'));
        $mform->addElement('select', 'autosubscribe', get_string('autosubscribe'), $choices);
        $mform->setDefault('autosubscribe', 1);

        if ($CFG->htmleditor) {
            $choices = array(0 => get_string('texteditor'), 1 => get_string('htmleditor'));
            $mform->addElement('select', 'htmleditor', get_string('textediting'), $choices);
            $mform->setDefault('htmleditor', 1);
        } else {
            $mform->addElement('static', 'htmleditor', get_string('textediting'), get_string('texteditor'));
        }
        $mform->setAdvanced('htmleditor');

        if (empty($CFG->enableajax)) {
            $mform->addElement('static', 'ajax', get_string('ajaxuse'), get_string('ajaxno'));
        } else {
            $choices = array( 0 => get_string('ajaxno'), 1 => get_string('ajaxyes'));
            $mform->addElement('select', 'ajax', get_string('ajaxuse'), $choices);
            $mform->setDefault('ajax', 1);
        }
        $mform->setAdvanced('ajax');

        $mform->addElement('text', 'city', get_string('city'), 'maxlength="100" size="25"');
        $mform->setType('city', PARAM_MULTILANG);
        $mform->setDefault('city', $templateuser->city);

        $mform->addElement('select', 'country', get_string('selectacountry'), get_list_of_countries());
        $mform->setDefault('country', $templateuser->country);
        $mform->setAdvanced('country');

        $choices = get_list_of_timezones();
        $choices['99'] = get_string('serverlocaltime');
        $mform->addElement('select', 'timezone', get_string('timezone'), $choices);
        $mform->setDefault('timezone', $templateuser->timezone);
        $mform->setAdvanced('timezone');

        $mform->addElement('select', 'lang', get_string('preferredlanguage'), get_list_of_languages());
        $mform->setDefault('lang', $templateuser->lang);
        $mform->setAdvanced('lang');

        $mform->addElement('htmleditor', 'description', get_string('userdescription'));
        $mform->setType('description', PARAM_CLEAN);
        $mform->setHelpButton('description', array('text', get_string('helptext')));
        $mform->setAdvanced('description');

        $mform->addElement('text', 'url', get_string('webpage'), 'maxlength="255" size="50"');
        $mform->setAdvanced('url');

        $mform->addElement('text', 'idnumber', get_string('idnumber'), 'maxlength="64" size="25"');
        $mform->setType('idnumber', PARAM_CLEAN);

        $mform->addElement('text', 'institution', get_string('institution'), 'maxlength="40" size="25"');
        $mform->setType('institution', PARAM_MULTILANG);
        $mform->setDefault('institution', $templateuser->institution);

        $mform->addElement('text', 'department', get_string('department'), 'maxlength="30" size="25"');
        $mform->setType('department', PARAM_MULTILANG);
        $mform->setDefault('department', $templateuser->department);

        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="25"');
        $mform->setType('phone1', PARAM_CLEAN);
        $mform->setAdvanced('phone1');

        $mform->addElement('text', 'phone2', get_string('phone'), 'maxlength="20" size="25"');
        $mform->setType('phone2', PARAM_CLEAN);
        $mform->setAdvanced('phone2');

        $mform->addElement('text', 'address', get_string('address'), 'maxlength="70" size="25"');
        $mform->setType('address', PARAM_MULTILANG);
        $mform->setAdvanced('address');

// hidden fields
        $mform->addElement('hidden', 'uplid');
        $mform->setType('uplid', PARAM_FILE);

        $mform->addElement('hidden', 'separator');
        $mform->setType('separator', PARAM_ALPHA);

        $mform->addElement('hidden', 'previewrows');
        $mform->setType('previewrows', PARAM_ALPHA);

        $this->add_action_buttons(true, get_string('uploadusers'));
    }

    function definition_after_data() {
        $mform =& $this->_form;

        $separator = $mform->getElementValue('separator');
        $uplid     = $mform->getElementValue('uplid');
        
        if ($headers = get_uf_headers($uplid, $separator)) {
            foreach ($headers as $header) {
                if ($mform->elementExists($header)) {
                    $mform->removeElement($header);
                }
            }
        }
    }
}
?>

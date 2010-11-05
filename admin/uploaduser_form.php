<?php

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

require_once $CFG->libdir.'/formslib.php';

class admin_uploaduser_form1 extends moodleform {
    function definition (){
        global $CFG, $USER;

        $mform =& $this->_form;

        $mform->addElement('header', 'settingsheader', get_string('upload'));

        $mform->addElement('filepicker', 'userfile', get_string('file'));
        $mform->addRule('userfile', null, 'required');

        $choices = csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter_name', get_string('csvdelimiter', 'admin'), $choices);
        if (array_key_exists('cfg', $choices)) {
            $mform->setDefault('delimiter_name', 'cfg');
        } else if (get_string('listsep', 'langconfig') == ';') {
            $mform->setDefault('delimiter_name', 'semicolon');
        } else {
            $mform->setDefault('delimiter_name', 'comma');
        }

        $textlib = textlib_get_instance();
        $choices = $textlib->get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'admin'), $choices);
        $mform->setDefault('encoding', 'UTF-8');

        $choices = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', get_string('rowpreviewnum', 'admin'), $choices);
        $mform->setType('previewrows', PARAM_INT);

        $choices = array(UU_ADDNEW    => get_string('uuoptype_addnew', 'admin'),
                         UU_ADDINC    => get_string('uuoptype_addinc', 'admin'),
                         UU_ADD_UPDATE => get_string('uuoptype_addupdate', 'admin'),
                         UU_UPDATE     => get_string('uuoptype_update', 'admin'));
        $mform->addElement('select', 'uutype', get_string('uuoptype', 'admin'), $choices);

        $this->add_action_buttons(false, get_string('uploadusers', 'admin'));
    }
}

class admin_uploaduser_form2 extends moodleform {
    function definition (){
        global $CFG, $USER;

        $mform   =& $this->_form;
        $columns =& $this->_customdata;

        // I am the template user, why should it be the administrator? we have roles now, other ppl may use this script ;-)
        $templateuser = $USER;

        // upload settings and file
        $mform->addElement('header', 'settingsheader', get_string('settings'));
        $mform->addElement('static', 'uutypelabel', get_string('uuoptype', 'admin') );

        $choices = array(0 => get_string('infilefield', 'auth'), 1 => get_string('createpasswordifneeded', 'auth'));
        $mform->addElement('select', 'uupasswordnew', get_string('uupasswordnew', 'admin'), $choices);
        $mform->setDefault('uupasswordnew', 1);
        $mform->disabledIf('uupasswordnew', 'uutype', 'eq', UU_UPDATE);

        $choices = array(0 => get_string('nochanges', 'admin'),
                         1 => get_string('uuupdatefromfile', 'admin'),
                         2 => get_string('uuupdateall', 'admin'),
                         3 => get_string('uuupdatemissing', 'admin'));
        $mform->addElement('select', 'uuupdatetype', get_string('uuupdatetype', 'admin'), $choices);
        $mform->setDefault('uuupdatetype', 0);
        $mform->disabledIf('uuupdatetype', 'uutype', 'eq', UU_ADDNEW);
        $mform->disabledIf('uuupdatetype', 'uutype', 'eq', UU_ADDINC);

        $choices = array(0 => get_string('nochanges', 'admin'), 1 => get_string('update'));
        $mform->addElement('select', 'uupasswordold', get_string('uupasswordold', 'admin'), $choices);
        $mform->setDefault('uupasswordold', 0);
        $mform->disabledIf('uupasswordold', 'uutype', 'eq', UU_ADDNEW);
        $mform->disabledIf('uupasswordold', 'uutype', 'eq', UU_ADDINC);
        $mform->disabledIf('uupasswordold', 'uuupdatetype', 'eq', 0);
        $mform->disabledIf('uupasswordold', 'uuupdatetype', 'eq', 3);

        $mform->addElement('selectyesno', 'uuallowrenames', get_string('allowrenames', 'admin'));
        $mform->setDefault('uuallowrenames', 0);
        $mform->disabledIf('uuallowrenames', 'uutype', 'eq', UU_ADDNEW);
        $mform->disabledIf('uuallowrenames', 'uutype', 'eq', UU_ADDINC);

        $mform->addElement('selectyesno', 'uuallowdeletes', get_string('allowdeletes', 'admin'));
        $mform->setDefault('uuallowdeletes', 0);
        $mform->disabledIf('uuallowdeletes', 'uutype', 'eq', UU_ADDNEW);
        $mform->disabledIf('uuallowdeletes', 'uutype', 'eq', UU_ADDINC);

        $mform->addElement('selectyesno', 'uunoemailduplicates', get_string('uunoemailduplicates', 'admin'));
        $mform->setDefault('uunoemailduplicates', 1);

        $choices = array(0 => get_string('no'),
                         1 => get_string('uubulknew', 'admin'),
                         2 => get_string('uubulkupdated', 'admin'),
                         3 => get_string('uubulkall', 'admin'));
        $mform->addElement('select', 'uubulk', get_string('uubulk', 'admin'), $choices);
        $mform->setDefault('uubulk', 0);

        // roles selection
        $showroles = false;
        foreach ($columns as $column) {
            if (preg_match('/^type\d+$/', $column)) {
                $showroles = true;
                break;
            }
        }
        if ($showroles) {
            $mform->addElement('header', 'rolesheader', get_string('roles'));

            $choices = uu_allowed_roles(true);

            $mform->addElement('select', 'uulegacy1', get_string('uulegacy1role', 'admin'), $choices);
            if ($studentroles = get_archetype_roles('student')) {
                foreach ($studentroles as $role) {
                    if (isset($choices[$role->id])) {
                        $mform->setDefault('uulegacy1', $role->id);
                        break;
                    }
                }
                unset($studentroles);
            }

            $mform->addElement('select', 'uulegacy2', get_string('uulegacy2role', 'admin'), $choices);
            if ($editteacherroles = get_archetype_roles('editingteacher')) {
                foreach ($editteacherroles as $role) {
                    if (isset($choices[$role->id])) {
                        $mform->setDefault('uulegacy2', $role->id);
                        break;
                    }
                }
                unset($editteacherroles);
            }

            $mform->addElement('select', 'uulegacy3', get_string('uulegacy3role', 'admin'), $choices);
            if ($teacherroles = get_archetype_roles('teacher')) {
                foreach ($teacherroles as $role) {
                    if (isset($choices[$role->id])) {
                        $mform->setDefault('uulegacy3', $role->id);
                        break;
                    }
                }
                unset($teacherroles);
            }
        }

        // default values
        $mform->addElement('header', 'defaultheader', get_string('defaultvalues', 'admin'));

        $mform->addElement('text', 'username', get_string('username'), 'size="20"');
        $mform->addRule('username', get_string('requiredtemplate', 'admin'), 'required', null, 'client');

        // only enabled and known to work plugins
        $choices = uu_allowed_auths();
        $mform->addElement('select', 'auth', get_string('chooseauthmethod','auth'), $choices);
        $mform->setDefault('auth', 'manual'); // manual is a sensible backwards compatible default
        $mform->addHelpButton('auth', 'chooseauthmethod', 'auth');
        $mform->setAdvanced('auth');

        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="30"');

        $choices = array(0 => get_string('emaildisplayno'), 1 => get_string('emaildisplayyes'), 2 => get_string('emaildisplaycourse'));
        $mform->addElement('select', 'maildisplay', get_string('emaildisplay'), $choices);
        $mform->setDefault('maildisplay', 2);

        $choices = array(0 => get_string('textformat'), 1 => get_string('htmlformat'));
        $mform->addElement('select', 'mailformat', get_string('emailformat'), $choices);
        $mform->setDefault('mailformat', 1);
        $mform->setAdvanced('mailformat');

        $choices = array(0 => get_string('emaildigestoff'), 1 => get_string('emaildigestcomplete'), 2 => get_string('emaildigestsubjects'));
        $mform->addElement('select', 'maildigest', get_string('emaildigest'), $choices);
        $mform->setDefault('maildigest', 0);
        $mform->setAdvanced('maildigest');

        $choices = array(0 => get_string('autosubscribeyes'), 1 => get_string('autosubscribeno'));
        $mform->addElement('select', 'autosubscribe', get_string('autosubscribe'), $choices);
        $mform->setDefault('autosubscribe', 1);

        $editors = editors_get_enabled();
        if (count($editors) > 1) {
            $choices = array();
            $choices['0'] = get_string('texteditor');
            $choices['1'] = get_string('htmleditor');
            $mform->addElement('select', 'htmleditor', get_string('textediting'), $choices);
            $mform->setDefault('htmleditor', 1);
        } else {
            $mform->addElement('hidden', 'htmleditor');
            $mform->setDefault('htmleditor', 1);
            $mform->setType('htmleditor', PARAM_INT);
        }

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

        $mform->addElement('select', 'country', get_string('selectacountry'), get_string_manager()->get_list_of_countries());
        $mform->setDefault('country', $templateuser->country);
        $mform->setAdvanced('country');

        $choices = get_list_of_timezones();
        $choices['99'] = get_string('serverlocaltime');
        $mform->addElement('select', 'timezone', get_string('timezone'), $choices);
        $mform->setDefault('timezone', $templateuser->timezone);
        $mform->setAdvanced('timezone');

        $mform->addElement('select', 'lang', get_string('preferredlanguage'), get_string_manager()->get_list_of_translations());
        $mform->setDefault('lang', $templateuser->lang);
        $mform->setAdvanced('lang');

        $editoroptions = array('maxfiles'=>0, 'maxbytes'=>0, 'trusttext'=>false, 'forcehttps'=>false);
        $mform->addElement('editor', 'description', get_string('userdescription'), null, $editoroptions);
        $mform->setType('description', PARAM_CLEANHTML);
        $mform->addHelpButton('description', 'userdescription');
        $mform->setAdvanced('description');

        $mform->addElement('text', 'url', get_string('webpage'), 'maxlength="255" size="50"');
        $mform->setAdvanced('url');

        $mform->addElement('text', 'idnumber', get_string('idnumber'), 'maxlength="64" size="25"');
        $mform->setType('idnumber', PARAM_NOTAGS);

        $mform->addElement('text', 'institution', get_string('institution'), 'maxlength="40" size="25"');
        $mform->setType('institution', PARAM_MULTILANG);
        $mform->setDefault('institution', $templateuser->institution);

        $mform->addElement('text', 'department', get_string('department'), 'maxlength="30" size="25"');
        $mform->setType('department', PARAM_MULTILANG);
        $mform->setDefault('department', $templateuser->department);

        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="25"');
        $mform->setType('phone1', PARAM_NOTAGS);
        $mform->setAdvanced('phone1');

        $mform->addElement('text', 'phone2', get_string('phone2'), 'maxlength="20" size="25"');
        $mform->setType('phone2', PARAM_NOTAGS);
        $mform->setAdvanced('phone2');

        $mform->addElement('text', 'address', get_string('address'), 'maxlength="70" size="25"');
        $mform->setType('address', PARAM_MULTILANG);
        $mform->setAdvanced('address');

        // Next the profile defaults
        profile_definition($mform);

        // hidden fields
        $mform->addElement('hidden', 'iid');
        $mform->setType('iid', PARAM_INT);

        $mform->addElement('hidden', 'previewrows');
        $mform->setType('previewrows', PARAM_INT);

        $mform->addElement('hidden', 'readcount');
        $mform->setType('readcount', PARAM_INT);

        $mform->addElement('hidden', 'uutype');
        $mform->setType('uutype', PARAM_INT);

        $this->add_action_buttons(true, get_string('uploadusers', 'admin'));
    }

    /**
     * Form tweaks that depend on current data.
     */
    function definition_after_data() {
        $mform   =& $this->_form;
        $columns =& $this->_customdata;

        foreach ($columns as $column) {
            if ($mform->elementExists($column)) {
                $mform->removeElement($column);
            }
        }
    }

    /**
     * Server side validation.
     */
    function validation($data, $files) {
        $errors = parent::validation($data, $files);
        $columns =& $this->_customdata;
        $optype  = $data['uutype'];

        // detect if password column needed in file
        if (!in_array('password', $columns)) {
            switch ($optype) {
                case UU_UPDATE:
                    if (!empty($data['uupasswordold'])) {
                        $errors['uupasswordold'] = get_string('missingfield', 'error', 'password');
                    }
                    break;

                case UU_ADD_UPDATE:
                    if (empty($data['uupasswordnew'])) {
                        $errors['uupasswordnew'] = get_string('missingfield', 'error', 'password');
                    }
                    if  (!empty($data['uupasswordold'])) {
                        $errors['uupasswordold'] = get_string('missingfield', 'error', 'password');
                    }
                    break;

                case UU_ADDNEW:
                    if (empty($data['uupasswordnew'])) {
                        $errors['uupasswordnew'] = get_string('missingfield', 'error', 'password');
                    }
                    break;
                case UU_ADDINC:
                    if (empty($data['uupasswordnew'])) {
                        $errors['uupasswordnew'] = get_string('missingfield', 'error', 'password');
                    }
                    break;
             }
        }

        // look for other required data
        if ($optype != UU_UPDATE) {
            if (!in_array('firstname', $columns)) {
                $errors['uutype'] = get_string('missingfield', 'error', 'firstname');
            }

            if (!in_array('lastname', $columns)) {
                if (isset($errors['uutype'])) {
                    $errors['uutype'] = '';
                } else {
                    $errors['uutype'] = ' ';
                }
                $errors['uutype'] .= get_string('missingfield', 'error', 'lastname');
            }

            if (!in_array('email', $columns) and empty($data['email'])) {
                $errors['email'] = get_string('requiredtemplate', 'admin');
            }

            if (!in_array('city', $columns) and empty($data['city'])) {
                $errors['city'] = get_string('required');
            }
        }

        return $errors;
    }

    /**
     * Used to reformat the data from the editor component
     *
     * @return stdClass
     */
    function get_data() {
        $data = parent::get_data();

        if ($data !== null) {
            $data->descriptionformat = $data->description['format'];
            $data->description = $data->description['text'];
        }

        return $data;
    }
}

class admin_uploaduser_form3 extends moodleform {
    function definition (){
        global $CFG, $USER;
        $mform =& $this->_form;
        $this->add_action_buttons(false, get_string('uploadnewfile'));
    }
}

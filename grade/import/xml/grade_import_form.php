<?php // $Id$
require_once $CFG->libdir.'/formslib.php';

class grade_import_form extends moodleform {
    function definition () {
        global $COURSE, $USER, $CFG;

        $mform =& $this->_form;

        $this->set_upload_manager(new upload_manager('userfile', false, false, null, false, 0, true, true, false));

        // course id needs to be passed for auth purposes
        $mform->addElement('hidden', 'id', optional_param('id'));
        $mform->setType('id', PARAM_INT);
        $mform->addElement('header', 'general', get_string('importfile', 'grades'));
        $mform->disabledIf('url', 'userfile', 'noteq', '');

        // file upload
        $mform->addElement('file', 'userfile', get_string('file'));
        $mform->setType('userfile', PARAM_FILE);
        $mform->disabledIf('userfile', 'url', 'noteq', '');

        $mform->addElement('text', 'url', get_string('fileurl', 'gradeimport_xml'), 'size="80"');

        if (!empty($CFG->gradepublishing)) {
            $mform->addElement('header', 'publishing', get_string('publishing', 'grades'));
            $options = array(get_string('nopublish', 'grades'), get_string('createnewkey', 'userkey'));
            if ($keys = get_records_select('user_private_key', "script='grade/import' AND instance={$COURSE->id} AND userid={$USER->id}")) {
                foreach ($keys as $key) {
                    $options[$key->value] = $key->value; // TODO: add more details - ip restriction, valid until ??
                }
            }
            $mform->addElement('select', 'key', get_string('userkey', 'userkey'), $options);
            $mform->setHelpButton('key', array(false, get_string('userkey', 'userkey'),
                    false, true, false, get_string("userkeyhelp", 'grades')));
            $mform->addElement('static', 'keymanagerlink', get_string('keymanager', 'userkey'),
                    '<a href="'.$CFG->wwwroot.'/grade/import/keymanager.php?id='.$COURSE->id.'">'.get_string('keymanager', 'userkey').'</a>');

            $mform->addElement('text', 'iprestriction', get_string('keyiprestriction', 'userkey'), array('size'=>80));
            $mform->setHelpButton('iprestriction', array(false, get_string('keyiprestriction', 'userkey'),
                    false, true, false, get_string("keyiprestrictionhelp", 'userkey')));

            $mform->addElement('date_time_selector', 'validuntil', get_string('keyvaliduntil', 'userkey'), array('optional'=>true));
            $mform->setHelpButton('validuntil', array(false, get_string('keyvaliduntil', 'userkey'),
                    false, true, false, get_string("keyvaliduntilhelp", 'userkey')));

            $mform->disabledIf('iprestriction', 'key', 'noteq', 1);
            $mform->disabledIf('validuntil', 'key', 'noteq', 1);

            $mform->disabledIf('iprestriction', 'url', 'eq', '');
            $mform->disabledIf('validuntil', 'url', 'eq', '');
            $mform->disabledIf('key', 'url', 'eq', '');
        }

        $this->add_action_buttons(false, get_string('uploadgrades', 'grades'));
    }

    function validation($data, $files) {
        $err = array();
        if (empty($data['url']) and empty($files['userfile'])) {
            if (array_key_exists('url', $data)) {
                $err['url'] = get_string('required');
            }
            if (array_key_exists('userfile', $data)) {
                $err['userfile'] = get_string('required');
            }

        } else if (array_key_exists('url', $data) and $data['url'] != clean_param($data['url'], PARAM_URL)) {
            $err['url'] = get_string('error');
        }

        if (count($err) == 0){
            return true;
        } else {
            return $err;
        }
    }
}
?>

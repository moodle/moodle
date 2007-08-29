<?php // $Id$
require_once $CFG->libdir.'/formslib.php';

class grade_export_form extends moodleform {
    function definition() {
        global $CFG, $COURSE, $USER;

        $mform =& $this->_form;
        if (isset($this->_customdata['plugin'])) {  // hardcoding plugin names here is hacky
            $plugin = $this->_customdata['plugin'];
        } else {
            $plugin = 'unknown';
        }

        $mform->addElement('header', 'options', get_string('options', 'grades'));

        $mform->addElement('advcheckbox', 'export_letters', get_string('exportletters', 'grades'));
        $mform->setDefault('export_letters', 0);
        $mform->setHelpButton('export_letters', array(false, get_string('exportletters', 'grades'),
                false, true, false, get_string("exportlettershelp", 'grades')));

        $mform->addElement('header', 'publishing', get_string('publishing', 'grades'));
        $options = array(get_string('nopublish', 'grades'), get_string('createnewkey', 'userkey'));
        if ($keys = get_records_select('user_private_key', "script='grade/export' AND instance={$COURSE->id} AND userid={$USER->id}")) {
            foreach ($keys as $key) {
                $options[$key->value] = $key->value; // TODO: add ip, date, etc.??
            }
        }
        $mform->addElement('select', 'key', get_string('userkey', 'userkey'), $options);
        $mform->setHelpButton('key', array(false, get_string('userkey', 'userkey'),
                false, true, false, get_string("userkeyhelp", 'grades')));
        $mform->addElement('static', 'keymanagerlink', get_string('keymanager', 'userkey'),
                '<a href="'.$CFG->wwwroot.'/grade/export/keymanager.php?id='.$COURSE->id.'">'.get_string('keymanager', 'userkey').'</a>');

        $mform->addElement('text', 'iprestriction', get_string('keyiprestriction', 'userkey'), array('size'=>80));
        $mform->setHelpButton('iprestriction', array(false, get_string('keyiprestriction', 'userkey'),
                false, true, false, get_string("keyiprestrictionhelp", 'userkey')));

        $mform->addElement('date_time_selector', 'validuntil', get_string('keyvaliduntil', 'userkey'), array('optional'=>true));
        $mform->setHelpButton('validuntil', array(false, get_string('keyvaliduntil', 'userkey'),
                false, true, false, get_string("keyvaliduntilhelp", 'userkey')));
        $mform->disabledIf('iprestriction', 'key', get_string('createnewkey', 'userkey'));
        $mform->disabledIf('validuntil', 'key', get_string('createnewkey', 'userkey'));

        $mform->addElement('header', 'general', get_string('gradeitemsinc', 'grades')); // TODO: localize

        $mform->addElement('hidden', 'id', $COURSE->id);

        if ($grade_items = grade_item::fetch_all(array('courseid'=>$COURSE->id))) {
            foreach ($grade_items as $grade_item) {
                if ($plugin != 'xmlexport' || $grade_item->idnumber) {
                    $mform->addElement('advcheckbox', 'itemids['.$grade_item->id.']', $grade_item->get_name());
                    $mform->setDefault('itemids['.$grade_item->id.']', 1);

                } else {
                    $mform->addElement('advcheckbox', 'itemids['.$grade_item->id.']', $grade_item->get_name(), get_string('noidnumber'));
                    $mform->hardFreeze('itemids['.$grade_item->id.']');
                    $noidnumber = true;
                }
            }
        }

        $options = array('10'=>10, '20'=>20, '100'=>100, '1000'=>1000, '100000'=>100000);
        $mform->addElement('select', 'previewrows', 'Preview rows', $options); // TODO: localize
        $mform->setType('previewrows', PARAM_INT);
        $this->add_action_buttons(false, get_string('submit'));
    }
}
?>

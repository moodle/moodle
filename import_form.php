<?php

require_once($CFG->libdir.'/formslib.php');

class book_import_form extends moodleform {

    function definition() {
        global $CFG;
        $mform =& $this->_form;
        $cm    = $this->_customdata;

        $mform->addElement('header', 'general', get_string('import'));

        $group = array();
        $group[0] =& MoodleQuickForm::createElement('text', 'reference', get_string('fileordir', 'book'), array('size'=>'48'));
        $group[1] =& MoodleQuickForm::createElement('button', 'popup', get_string('chooseafile', 'resource') .' ...');

        $options = 'menubar=0,location=0,scrollbars,resizable,width=600,height=400';
        $url = '/mod/book/coursefiles.php?choose=id_reference&id='.$cm->course;
        $buttonattributes = array('title'=>get_string('chooseafile', 'resource'), 'onclick'=>"return openpopup('$url', '".$group[1]->getName()."', '$options', 0);");
        $group[1]->updateAttributes($buttonattributes);

        $mform->addGroup($group, 'choosesomething', get_string('fileordir', 'book'), array(''), false);

        $mform->addElement('checkbox', 'subchapter', get_string('subchapter', 'book'));
        $mform->addElement('static', 'importfileinfo', get_string('help'), get_string('importinfo', 'book'));

        $mform->addElement('hidden', 'id', $cm->id);
        $mform->setType('id', PARAM_INT);

        $this->add_action_buttons(true, get_string('import', 'book'));
    }

    function validation($data, $files) {
        global $CFG;
        $cm = $this->_customdata;

        $errors = parent::validation($data, $files);
        $reference = stripslashes($data['reference']);

        if ($reference != '') { //null path is root
            $reference = book_prepare_link($reference);
            if ($reference == '') { //evil characters in $ref!
                $errors['choosesomething'] = get_string('error');
            } else {
                $coursebase = $CFG->dataroot.'/'.$cm->course;

                if ($reference == '') {
                    $base = $coursebase;
                } else {
                    $base = $coursebase.'/'.$reference;
                }
                if (!is_dir($base) and !is_file($base)) {
                    $errors['choosesomething'] = get_string('error');
                }
            }
        }

        return $errors;
    }
}

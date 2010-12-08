<?php
// This file is part of Book module for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Book import form
 *
 * @package    mod
 * @subpackage book
 * @copyright  2004-2010 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once($CFG->libdir.'/formslib.php');

class book_import_form extends moodleform {

    function definition() {
        global $CFG;
        $mform = $this->_form;
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
        $reference = $data['reference'];

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

<?php
// This file is part of Moodle - http://moodle.org/
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
 * @package    booktool_importhtml
 * @copyright  2004-2011 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/formslib.php');

class booktool_importhtml_form extends moodleform {

    function definition() {
        $mform = $this->_form;
        $data  = $this->_customdata;

        $mform->addElement('header', 'general', get_string('import'));

        $options = array(
                // '0'=>get_string('typeonefile', 'booktool_importhtml'),
                '1'=>get_string('typezipdirs', 'booktool_importhtml'),
                '2'=>get_string('typezipfiles', 'booktool_importhtml'),
        );
        $mform->addElement('select', 'type', get_string('type', 'booktool_importhtml'), $options);
        $mform->setDefault('type', 2);

        $mform->addElement('filepicker', 'importfile', get_string('ziparchive', 'booktool_importhtml'));
        $mform->addHelpButton('importfile', 'ziparchive', 'booktool_importhtml');
        $mform->addRule('importfile', null, 'required');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'chapterid');
        $mform->setType('chapterid', PARAM_INT);

        $this->add_action_buttons(true, get_string('doimport', 'booktool_importhtml'));

        $this->set_data($data);
    }

    function validation($data, $files) {
        global $USER;

        if ($errors = parent::validation($data, $files)) {
            return $errors;
        }

        $usercontext = context_user::instance($USER->id);
        $fs = get_file_storage();

        if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data['importfile'], 'id', false)) {
            $errors['importfile'] = get_string('required');
            return $errors;
        } else {
            $file = reset($files);
            if ($file->get_mimetype() != 'application/zip') {
                $errors['importfile'] = get_string('invalidfiletype', 'error', $file->get_filename());
                // better delete current file, it is not usable anyway
                $fs->delete_area_files($usercontext->id, 'user', 'draft', $data['importfile']);
            } else {
                if (!$chpterfiles = toolbook_importhtml_get_chapter_files($file, $data['type'])) {
                    $errors['importfile'] = get_string('errornochapters', 'booktool_importhtml');
                }
            }
        }

        return $errors;
    }
}

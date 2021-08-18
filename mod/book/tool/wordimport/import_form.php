<?php
// This file is part of Moodle - http://moodle.org/
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Import Microsoft Word file form.
 *
 * @package    booktool_wordimport
 * @copyright  2016 Eoin Campbell
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/* This file contains code based on mod/book/tool/importhtml/import_form.php
 * (copyright 2004-2011 Petr Skoda) from Moodle 2.4. */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir . DIRECTORY_SEPARATOR . 'formslib.php');

/**
 * Importer for Microsoft Word books.
 *
 * @copyright 2016 Eoin Campbell
 * @author Eoin Campbell
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later (5)
 */
class booktool_wordimport_form extends moodleform {

    /**
     * Define Word import form
     *
     * @return void
     */
    public function definition() {
        $mform = $this->_form;
        $data  = $this->_customdata;

        $mform->addElement('header', 'general', get_string('importchapters', 'booktool_wordimport'));

        // Word files are automatically split into book chapters based on Heading 1 styles.
        // Does user want to split Word file into book subchapters based on Heading 2 styles (default to yes)?
        $mform->addElement('checkbox', 'splitonsubheadings', '', get_string('splitonsubheadings', 'booktool_wordimport'));
        $mform->addHelpButton('splitonsubheadings', 'splitonsubheadings', 'booktool_wordimport');
        $mform->setDefault('splitonsubheadings', 0);

        // User can select 1 and only 1 Word file which must have a .docx suffix (not .docm or .doc).
        $mform->addElement('filepicker', 'importfile', get_string('wordfile', 'booktool_wordimport'), null,
                           array('subdirs' => 0, 'accepted_types' => array('.docx')));
        $mform->addHelpButton('importfile', 'wordfile', 'booktool_wordimport');
        $mform->addRule('importfile', null, 'required');

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'chapterid');
        $mform->setType('chapterid', PARAM_INT);

        $this->add_action_buttons(true, get_string('import'));

        $this->set_data($data);
    }

    /**
     * Define Word import form validation
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
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
            $mimetype = $file->get_mimetype();
            if ($mimetype != 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                $errors['importfile'] = get_string('invalidfiletype', 'error', $file->get_filename());
                $fs->delete_area_files($usercontext->id, 'user', 'draft', $data['importfile']);
            }
        }

        return $errors;
    }
}

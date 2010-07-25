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
 * Form used to select a file and file format for the import
 *
 * @package    mod
 * @subpackage lesson
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 **/

defined('MOODLE_INTERNAL') || die();

/**
 * Form used to select a file and file format for the import
 * @copyright  2009 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class lesson_import_form extends moodleform {

    public function definition() {

        $mform = $this->_form;

        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        $mform->addElement('hidden', 'pageid');
        $mform->setType('pageid', PARAM_INT);

        $mform->addElement('select', 'format', get_string('fileformat', 'lesson'), $this->_customdata['formats']);
        $mform->setDefault('format', 'gift');
        $mform->setType('format', 'text');
        $mform->addRule('format', null, 'required');

        $mform->addElement('file', 'newfile', get_string('upload'), array('size'=>'50'));
        $mform->addRule('newfile', null, 'required');

        $this->add_action_buttons(null, get_string("uploadthisfile"));

    }

    public function get_importfile_name(){
        if ($this->is_submitted() and $this->is_validated()) {
            // return the temporary filename to process
            return $_FILES['newfile']['tmp_name'];
        }else{
            return  NULL;
        }
    }

    public function get_importfile_realname(){
        if ($this->is_submitted() and $this->is_validated()) {
            // return the temporary filename to process
            // TODO change this to use the files API properly.
            return $_FILES['newfile']['name'];
        }else{
            return  NULL;
        }
    }

}
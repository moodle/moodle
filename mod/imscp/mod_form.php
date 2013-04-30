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
 * IMS CP configuration form
 *
 * @package    mod
 * @subpackage imscp
 * @copyright  2009 Petr Skoda  {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->libdir.'/filelib.php');

class mod_imscp_mod_form extends moodleform_mod {
    function definition() {
        global $CFG, $DB;
        $mform = $this->_form;

        $config = get_config('imscp');

        //-------------------------------------------------------
        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), array('size'=>'48'));
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');
        $this->add_intro_editor($config->requiremodintro);

        //-------------------------------------------------------
        $mform->addElement('header', 'content', get_string('contentheader', 'imscp'));
        $mform->setExpanded('content', true);
        $mform->addElement('filepicker', 'package', get_string('packagefile', 'imscp'));

        $options = array('-1'=>get_string('all'), '0'=>get_string('no'), '1'=>'1', '2'=>'2', '5'=>'5', '10'=>'10', '20'=>'20');
        $mform->addElement('select', 'keepold', get_string('keepold', 'imscp'), $options);
        $mform->setDefault('keepold', $config->keepold);
        $mform->setAdvanced('keepold', $config->keepold_adv);

        //-------------------------------------------------------
        $this->standard_coursemodule_elements();

        //-------------------------------------------------------
        $this->add_action_buttons();
    }

    function validation($data, $files) {
        global $USER;

        if ($errors = parent::validation($data, $files)) {
            return $errors;
        }

        $usercontext = context_user::instance($USER->id);
        $fs = get_file_storage();

        if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data['package'], 'id', false)) {
            if (!$this->current->instance) {
                $errors['package'] = get_string('required');
                return $errors;
            }
        } else {
            $file = reset($files);
            if ($file->get_mimetype() != 'application/zip') {
                $errors['package'] = get_string('invalidfiletype', 'error', '', $file);
                // better delete current file, it is not usable anyway
                $fs->delete_area_files($usercontext->id, 'user', 'draft', $data['package']);
            }
        }

        return $errors;
    }
}

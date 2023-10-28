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
 * @package   mod_pdfannotator
 * @copyright 2018 RWTH Aachen (see README.md)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */
if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    // It must be included from a Moodle page.
}

require_once($CFG->dirroot . '/course/moodleform_mod.php');
require_once($CFG->dirroot . '/mod/pdfannotator/lib.php');
require_once($CFG->libdir . '/filelib.php');

class mod_pdfannotator_mod_form extends moodleform_mod {

    public function definition() {

        global $CFG, $USER, $COURSE;
        $mform =& $this->_form;
        $config = get_config('mod_pdfannotator');

        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->setType('general', PARAM_TEXT);
        $mform->addElement('text', 'name', get_string('setting_alternative_name', 'pdfannotator'), array('size' => '48'));
        $mform->addHelpButton('name', 'setting_alternative_name', 'pdfannotator');
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        // Description.
        $this->standard_intro_elements();

        $element = $mform->getElement('introeditor');
        $attributes = $element->getAttributes();
        $attributes['rows'] = 5;
        $element->setAttributes($attributes);

        // Add a filemanager for drag-and-drop file upload.
        // $fileoptions = array('subdirs' => 0, 'maxbytes' => 0, 'areamaxbytes' => 10485760, 'maxfiles' => 1,
        // 'accepted_types' => '.pdf', 'return_types' => 1 | 2);
        // FILE_INTERNAL | FILE_EXTERNAL was replaced by 1|2, because moodle doesnt't identify FILE_INTERNAL, FILE_EXTERNAL here.
        $filemanageroptions = array();
        $filemanageroptions['accepted_types'] = '.pdf';
        $filemanageroptions['maxbytes'] = 0;
        $filemanageroptions['maxfiles'] = 1; // Upload only one file.
        $filemanageroptions['mainfile'] = true;

        $mform->addElement('filemanager', 'files', get_string('setting_fileupload', 'pdfannotator'), null,
            $filemanageroptions); // Params: 1. type of the element, 2. (html) elementname, 3. label.
        $mform->addHelpButton('files', 'setting_fileupload', 'pdfannotator');

        $mform->addElement('advcheckbox', 'usevotes', get_string('setting_usevotes', 'pdfannotator'),
            get_string('usevotes', 'pdfannotator'), null, array(0, 1));
        $mform->setType('usevotes', PARAM_BOOL);
        $mform->setDefault('usevotes', $config->usevotes);
        $mform->addHelpButton('usevotes', 'setting_usevotes', 'pdfannotator');

        $mform->addElement('advcheckbox', 'use_studenttextbox', get_string('setting_use_studenttextbox', 'pdfannotator'),
                get_string('use_studenttextbox', 'pdfannotator'), null, array(0, 1));
        $mform->setType('use_studenttextbox', PARAM_BOOL);
        $mform->setDefault('use_studenttextbox', $config->use_studenttextbox);
        $mform->addHelpButton('use_studenttextbox', 'setting_use_studenttextbox', 'pdfannotator');

        $mform->addElement('advcheckbox', 'use_studentdrawing', get_string('setting_use_studentdrawing', 'pdfannotator'),
                get_string('use_studentdrawing', 'pdfannotator'), null, array(0, 1));
        $mform->setType('use_studentdrawing', PARAM_BOOL);
        $mform->setDefault('use_studentdrawing', $config->use_studentdrawing);
        $mform->addHelpButton('use_studentdrawing', 'setting_use_studentdrawing', 'pdfannotator');

        // XXX second checkbox or change to select.
        $mform->addElement('advcheckbox', 'useprint', get_string('setting_useprint_document', 'pdfannotator'),
            get_string('useprint', 'pdfannotator'), null, array(0, 1));
        $mform->setType('useprint', PARAM_BOOL);
        $mform->setDefault('useprint', $config->useprint);
        $mform->addHelpButton('useprint', 'setting_useprint_document', 'pdfannotator');

        $mform->addElement('advcheckbox', 'useprintcomments', get_string('setting_useprint_comments', 'pdfannotator'),
            get_string('useprint_comments', 'pdfannotator'), null, array(0, 1));
        $mform->setType('useprintcomments', PARAM_BOOL);
        $mform->setDefault('useprintcomments', $config->useprintcomments);
        $mform->addHelpButton('useprintcomments', 'setting_useprint_comments', 'pdfannotator');

        $mform->addElement('advcheckbox', 'useprivatecomments', get_string('setting_use_private_comments', 'pdfannotator'),
            get_string('use_private_comments', 'pdfannotator'), null, array(0, 1));
        $mform->setType('useprivatecomments', PARAM_BOOL);
        $mform->setDefault('useprivatecomments', $config->use_private_comments);
        $mform->addHelpButton('useprivatecomments', 'setting_use_private_comments', 'pdfannotator');

        $mform->addElement('advcheckbox', 'useprotectedcomments', get_string('setting_use_protected_comments', 'pdfannotator'),
            get_string('use_protected_comments', 'pdfannotator'), null, array(0, 1));
        $mform->setType('useprotectedcomments', PARAM_BOOL);
        $mform->setDefault('useprotectedcomments', $config->use_protected_comments);
        $mform->addHelpButton('useprotectedcomments', 'setting_use_protected_comments', 'pdfannotator');

        // Add legacy files flag only if used.
        if (isset($this->current->legacyfiles) and $this->current->legacyfiles != RESOURCELIB_LEGACYFILES_NO) {
            $options = array(RESOURCELIB_LEGACYFILES_DONE => get_string('legacyfilesdone', 'pdfannotator'),
                RESOURCELIB_LEGACYFILES_ACTIVE => get_string('legacyfilesactive', 'pdfannotator'));
            $mform->addElement('select', 'legacyfiles', get_string('legacyfiles', 'pdfannotator'), $options);
        }

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
        // -------------------------------------------------------
        $mform->addElement('hidden', 'revision'); // Hard-coded as 1; should be changed if version becomes important.
        $mform->setType('revision', PARAM_INT);
        $mform->setDefault('revision', 1);
    }

    // Loads the old file in the filemanager.
    public function data_preprocessing(&$defaultvalues) {
        if ($this->current->instance) {
            $contextid = $this->context->id;
            $draftitemid = file_get_submitted_draft_itemid('files');
            file_prepare_draft_area($draftitemid, $contextid, 'mod_pdfannotator', 'content', 0, array('subdirs' => true));
            $defaultvalues['files'] = $draftitemid;
            $this->_form->disabledIf('files', 'update', 'notchecked', 2);
        }
    }

    public function validation($data, $files) {
        global $USER;

        $errors = parent::validation($data, $files);

        $usercontext = context_user::instance($USER->id);
        $fs = get_file_storage();
        if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data['files'], 'sortorder, id', false)) {
            $errors['files'] = get_string('required');
            return $errors;
        }
        if (count($files) == 1) {
            // No need to select main file if only one picked.
            return $errors;
        } else if (count($files) > 1) {
            $mainfile = false;
            foreach ($files as $file) {
                if ($file->get_sortorder() == 1) {
                    $mainfile = true;
                    break;
                }
            }
            // Set a default main file.
            if (!$mainfile) {
                $file = reset($files);
                file_set_sortorder($file->get_contextid(), $file->get_component(), $file->get_filearea(), $file->get_itemid(),
                                   $file->get_filepath(), $file->get_filename(), 1);
            }
        }
        return $errors;
    }

}

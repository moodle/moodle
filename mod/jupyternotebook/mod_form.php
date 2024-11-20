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
 * jupyternotebook configuration form
 *
 * @package   mod_jupyternotebook
 * @copyright 2021 DNE - Ministere de l'Education Nationale 
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;

require_once ($CFG->dirroot.'/course/moodleform_mod.php');
require_once($CFG->dirroot.'/mod/url/locallib.php');
require_once($CFG->dirroot.'/mod/jupyternotebook/github/JupyterCommit.php');

/**
 * Module instance settings form.
 *
 * @package     mod_jupyternotebook
 * @category    form
 * @copyright   2021 DNE - Ministere de l'Education Nationale 
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_jupyternotebook_mod_form extends moodleform_mod {

    /**
     * Defines forms elements.
     */
    function definition() {
        global $CFG, $PAGE;
        $mform = $this->_form;

        $config = get_config('url');

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

        $this->standard_intro_elements();

        $filemanager_options = array();
        $filemanager_options['accepted_types'] = 'ipynb';
        $filemanager_options['maxbytes'] = 0;
        $filemanager_options['maxfiles'] = 1;
        $filemanager_options['mainfile'] = true;


        $mform->addElement('filemanager', 'files', get_string('selectfile', 'mod_jupyternotebook'), null, $filemanager_options);

        $serverurldisabled = get_config('mod_jupyternotebook', 'canediturl') ? '' : 'disabled';
        $mform->addElement('text', 'serverurl', get_string('serverurl', 'mod_jupyternotebook'), array('size'=>'100', $serverurldisabled));
        $mform->setType('serverurl', PARAM_URL);
        $mform->setDefault('serverurl', get_config('mod_jupyternotebook', 'defaultserverurl'));

        $mform->addElement('hidden', 'jpcourseid');
        $mform->setType('jpcourseid', PARAM_TEXT);
        $mform->setDefault('jpcourseid',$PAGE->course->id );

        $mform->addElement('hidden', 'jpnotebookid');
        $mform->setType('jpnotebookid', PARAM_TEXT);
        $mform->addElement('header', 'optionssection', get_string('appearance'));

        if ($this->current->instance) {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions), $this->current->displayoptions);
        } else {
            $options = resourcelib_get_displayoptions(explode(',', $config->displayoptions));
        }

        unset($options[RESOURCELIB_DISPLAY_AUTO]);
        unset($options[RESOURCELIB_DISPLAY_POPUP]);
        unset($options[RESOURCELIB_DISPLAY_DOWNLOAD]);
        unset($options[RESOURCELIB_DISPLAY_FRAME]);
        unset($options[RESOURCELIB_DISPLAY_DOWNLOAD]);

        if (count($options) == 1) {
            $mform->addElement('hidden', 'display');
            $mform->setType('display', PARAM_INT);
            reset($options);
            $mform->setDefault('display', key($options));
        } else {
            $mform->addElement('select', 'displayoptions', get_string('displayselect', 'url'), $options);
            $mform->setDefault('displayoptions', $config->display);
            $mform->addHelpButton('displayoptions', 'displayselect', 'url');
        }

        $mform->addElement('text' ,'iframeheight', get_string('iframeweightlabel', 'mod_jupyternotebook'));
        $mform->setType('iframeheight', PARAM_INT);
        $mform->setDefault('iframeheight', 1000);

        $this->standard_coursemodule_elements();
        $this->add_action_buttons();
    }

    /**
     * Set up the draft file which isn't part of standard data.
     *
     * @param array $default_values
     */
    function data_preprocessing(&$default_values) {
        if ($this->current->instance) {
            $draftitemid = file_get_submitted_draft_itemid('files');
            file_prepare_draft_area($draftitemid, $this->context->id, 'mod_jupyternotebook', 'content', 0, array('subdirs'=>true));
            $default_values['files'] = $draftitemid;
        }
    }

    /**
     * data post processing
     * @param stdClass $data
     */
    public function data_postprocessing($data) {
        parent::data_postprocessing($data);
        global $USER;
        $fs = get_file_storage();
        $usercontext = context_user::instance($USER->id);
        if ($files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data->files, 'sortorder, id', false)) {
            $file = $files[array_keys($files)[0]];
            $filename = str_replace(" ", "_", $file->get_filename());
            $data->jpnotebookid= substr($filename, 0, strrpos($filename, "."));;
        }
    }

    /**
     * Definition after data
     */
    function definition_after_data() {
        if ($this->current->instance) {
            // resource not migrated yet
            return;
        }
        parent::definition_after_data();
    }

    /**
     * Enforce validation rules here
     *
     * @param object $data Post data to validate
     * @param array $files
     * @return array
     **/
    function validation($data, $files) {
        global $USER, $PAGE;

        $errors = parent::validation($data, $files);

        $usercontext = context_user::instance($USER->id);
        $fs = get_file_storage();
        if (!$files = $fs->get_area_files($usercontext->id, 'user', 'draft', $data['files'], 'sortorder, id', false)) {
            $errors['files'] = get_string('required');
            return $errors;
        }

        if (count($files) !== 1) {
            $errors['files'] = get_string('required');
            return $errors;
        }

        $file = $files[array_keys($files)[0]];
        $filename = str_replace(" ", "_", $file->get_filename());

        $jupytercommit = new JupyterCommit();
        $repositorybasedirectory = '';
        if ($repositorybasedirectoryConf = get_config('mod_jupyternotebook','repositorybasedirectory')) {
            $repositorybasedirectory = $repositorybasedirectoryConf."/";
        }
        $jupytercommit->commitandpush($repositorybasedirectory.$PAGE->course->id."/".$filename,$file->get_content(),get_string('commitmessage', 'jupyternotebook', ['name' => $filename, 'activity' => $data["name"],'course' => $PAGE->course->fullname]));

        return $errors;
    }

}

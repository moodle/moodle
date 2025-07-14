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

namespace mod_data\form;

use context;
use core\notification;
use moodle_exception;
use moodle_url;
use core_form\dynamic_form;
use mod_data\manager;
use mod_data\preset;

/**
 * Save database as preset form.
 *
 * @package    mod_data
 * @copyright  2021 Mihail Geshoski <paulh@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class save_as_preset extends dynamic_form {

    /**
     * Form definition
     */
    protected function definition() {

        $this->_form->addElement('hidden', 'd');
        $this->_form->setType('d', PARAM_INT);
        $this->_form->addElement('hidden', 'action', 'save');
        $this->_form->setType('action', PARAM_ALPHANUM);
        $this->_form->addElement('hidden', 'oldpresetname', '');
        $this->_form->setType('oldpresetname', PARAM_FILE);

        $this->_form->addElement('text', 'name', get_string('name'), ['size' => 60]);
        $this->_form->setType('name', PARAM_FILE);
        $this->_form->addRule('name', null, 'required');

        // Overwrite checkbox will be hidden by default. It will only appear if there is an error when saving the preset.
        $this->_form->addElement('checkbox', 'overwrite', '', get_string('overrwritedesc', 'data'), ['class' => 'hidden']);

        $this->_form->addElement('textarea', 'description', get_string('description'), ['rows' => 5, 'cols' => 60]);
        $this->_form->setType('name', PARAM_TEXT);
    }

    /**
     * Return form context
     *
     * @return context
     */
    protected function get_context_for_dynamic_submission(): context {
        global $DB;

        $d = $this->optional_param('d', null, PARAM_INT);
        $data = $DB->get_record('data', array('id' => $d), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $data->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('data', $data->id, $course->id, null, MUST_EXIST);

        return \context_module::instance($cm->id, MUST_EXIST);
    }

    /**
     * Perform some validation.
     *
     * @param array $formdata
     * @param array $files
     * @return array
     */
    public function validation($formdata, $files): array {

        $errors = parent::validation($formdata, $files);
        $context = $this->get_context_for_dynamic_submission();
        $cm = get_coursemodule_from_id('', $context->instanceid, 0, false, MUST_EXIST);
        $manager = manager::create_from_coursemodule($cm);

        if (!empty($formdata['overwrite'])) {
            $presets = $manager->get_available_presets();
            $selectedpreset = new \stdClass();
            foreach ($presets as $preset) {
                if ($preset->name == $formdata['name']) {
                    $selectedpreset = $preset;
                    break;
                }
            }
            if (!$selectedpreset instanceof preset || !$selectedpreset->can_manage()) {
                $errors['name'] = get_string('cannotoverwritepreset', 'data');
            }
        } else if ($formdata['action'] == 'saveaspreset' || $formdata['oldpresetname'] != $formdata['name']) {

            // If the preset exists when a new preset is saved or name has changed, then we need to throw an error.
            $sitepresets = $manager->get_available_saved_presets();
            $usercandelete = false;
            foreach ($sitepresets as $preset) {
                if ($formdata['name'] == $preset->name) {
                    if ($preset->can_manage()) {
                        $errors['name'] = get_string('errorpresetexists', 'data');
                        $usercandelete = true;
                    } else {
                        $errors['name'] = get_string('errorpresetexistsbutnotoverwrite', 'data');
                    }
                    break;
                }
            }
            // If there are some errors, the checkbox should be displayed, to let users overwrite the preset.
            if (!empty($errors) && $usercandelete) {
                $this->_form->getElement('overwrite')->removeAttribute('class');
            }
        }

        return $errors;
    }

    /**
     * Check if current user has access to this form, otherwise throw exception.
     *
     * @return void
     * @throws moodle_exception
     */
    protected function check_access_for_dynamic_submission(): void {
        global $DB;

        if (!has_capability('mod/data:managetemplates', $this->get_context_for_dynamic_submission())) {
            throw new moodle_exception('saveaspresetmissingcapability', 'data');
        }

        $action = $this->optional_param('action', '', PARAM_ALPHANUM);
        if ($action == 'saveaspreset') {
            // For saving it as a new preset, some fields need to be created; otherwise, an exception will be raised.
            $instanceid = $this->optional_param('d', null, PARAM_INT);
            $hasfields = $DB->record_exists('data_fields', ['dataid' => $instanceid]);

            if (!$hasfields) {
                throw new moodle_exception('nofieldindatabase', 'data');
            }
        }
    }

    /**
     * Process the form submission, used if form was submitted via AJAX.
     *
     * @return array
     */
    public function process_dynamic_submission(): array {
        global $DB, $CFG;
        require_once($CFG->dirroot . '/mod/data/lib.php');

        $formdata = $this->get_data();
        $result = false;
        $errors = [];
        $data = $DB->get_record('data', array('id' => $formdata->d), '*', MUST_EXIST);
        $course = $DB->get_record('course', array('id' => $data->course), '*', MUST_EXIST);
        $cm = get_coursemodule_from_instance('data', $data->id, $course->id, null, MUST_EXIST);
        $context = \context_module::instance($cm->id, MUST_EXIST);

        try {
            $manager = manager::create_from_instance($data);
            if (!empty($formdata->overwrite)) {
                $presets = $manager->get_available_presets();
                $selectedpreset = new \stdClass();
                foreach ($presets as $preset) {
                    if ($preset->name == $formdata->name) {
                        $selectedpreset = $preset;
                        break;
                    }
                }
                if ($selectedpreset instanceof preset && $selectedpreset->can_manage()) {
                    $selectedpreset->delete();
                }
            }
            $presetname = $formdata->name;
            if (!empty($formdata->oldpresetname)) {
                $presetname = $formdata->oldpresetname;
            }
            $preset = preset::create_from_instance($manager, $presetname, $formdata->description);
            if (!empty($formdata->oldpresetname)) {
                // Update the name and the description, to save the new data.
                $preset->name = $formdata->name;
                $preset->description = $formdata->description;
            }
            $result = $preset->save();

            if ($result) {
                // Add notification in the session to be shown when the page is reloaded on the JS side.
                $previewurl = new moodle_url(
                    '/mod/data/preset.php',
                    ['id' => $cm->id, 'fullname' => $preset->get_fullname(), 'action' => 'preview']
                );
                notification::success(get_string('savesuccess', 'mod_data', (object)['url' => $previewurl->out()]));
            }
        } catch (\Exception $exception) {
            $errors[] = $exception->getMessage();
        }

        return [
            'result' => $result,
            'errors' => $errors,
        ];
    }

    /**
     * Load in existing data as form defaults.
     *
     * @return void
     */
    public function set_data_for_dynamic_submission(): void {
        $data = (object)[
            'd' => $this->optional_param('d', 0, PARAM_INT),
            'action' => $this->optional_param('action', '', PARAM_ALPHANUM),
            'oldpresetname' => $this->optional_param('presetname', '', PARAM_FILE),
            'name' => $this->optional_param('presetname', '', PARAM_FILE),
            'description' => $this->optional_param('presetdescription', '', PARAM_TEXT),
        ];
        $this->set_data($data);
    }

    /**
     * Returns url to set in $PAGE->set_url() when form is being rendered or submitted via AJAX
     *
     * @return moodle_url
     */
    protected function get_page_url_for_dynamic_submission(): moodle_url {
        $d = $this->optional_param('d', null, PARAM_INT);

        return new moodle_url('/user/field.php', ['d' => $d]);
    }
}

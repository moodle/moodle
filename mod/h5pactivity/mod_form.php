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
 * The main mod_h5pactivity configuration form.
 *
 * @package     mod_h5pactivity
 * @copyright   2020 Ferran Recio <ferran@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/course/moodleform_mod.php');

/**
 * Module instance settings form.
 *
 * @package    mod_h5pactivity
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_h5pactivity_mod_form extends moodleform_mod {

    /**
     * Defines forms elements
     */
    public function definition(): void {
        global $CFG;

        $mform = $this->_form;

        // Adding the "general" fieldset, where all the common settings are shown.
        $mform->addElement('header', 'general', get_string('general', 'form'));

        // Adding the standard "name" field.
        $mform->addElement('text', 'name', get_string('name'), ['size' => '64']);

        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }

        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 255), 'maxlength', 255, 'client');

        $this->standard_intro_elements();

        // Adding the rest of mod_h5pactivity settings, spreading all them into this fieldset.
        $options = [];
        $options['accepted_types'] = ['.h5p'];
        $options['maxbytes'] = 0;
        $options['maxfiles'] = 1;
        $options['subdirs'] = 0;

        $mform->addElement('filemanager', 'packagefile', get_string('package', 'mod_h5pactivity'), null, $options);
        $mform->addHelpButton('packagefile', 'package', 'mod_h5pactivity');

        // H5P displaying options.
        $factory = new \core_h5p\factory();
        $core = $factory->get_core();
        $displayoptions = (array) \core_h5p\helper::decode_display_options($core);
        $mform->addElement('header', 'h5pdisplay', get_string('h5pdisplay', 'mod_h5pactivity'));
        foreach ($displayoptions as $key => $value) {
            $name = get_string('display'.$key, 'mod_h5pactivity');
            $fieldname = "displayopt[$key]";
            $mform->addElement('checkbox', $fieldname, $name);
            $mform->setType($fieldname, PARAM_BOOL);
        }

        // Add standard grading elements.
        $this->standard_grading_coursemodule_elements();

        // Add standard elements.
        $this->standard_coursemodule_elements();

        // Add standard buttons.
        $this->add_action_buttons();
    }

    /**
     * Enforce validation rules here
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array
     **/
    public function validation($data, $files) {
        global $USER;
        $errors = parent::validation($data, $files);

        if (empty($data['packagefile'])) {
            $errors['packagefile'] = get_string('required');

        } else {
            $draftitemid = file_get_submitted_draft_itemid('packagefile');

            file_prepare_draft_area($draftitemid, $this->context->id, 'mod_h5pactivity', 'packagefilecheck', null,
                ['subdirs' => 0, 'maxfiles' => 1]);

            // Get file from users draft area.
            $usercontext = context_user::instance($USER->id);
            $fs = get_file_storage();
            $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id', false);

            if (count($files) < 1) {
                $errors['packagefile'] = get_string('required');
                return $errors;
            }
            $file = reset($files);
            if (!$file->is_external_file() && !empty($data['updatefreq'])) {
                // Make sure updatefreq is not set if using normal local file.
                $errors['updatefreq'] = get_string('updatefreq_error', 'mod_h5pactivity');
            }
        }

        return $errors;
    }

    /**
     * Enforce defaults here.
     *
     * @param array $defaultvalues Form defaults
     * @return void
     **/
    public function data_preprocessing(&$defaultvalues) {
        // H5P file.
        $draftitemid = file_get_submitted_draft_itemid('packagefile');
        file_prepare_draft_area($draftitemid, $this->context->id, 'mod_h5pactivity',
                'package', 0, ['subdirs' => 0, 'maxfiles' => 1]);
        $defaultvalues['packagefile'] = $draftitemid;

        // H5P display options.
        $factory = new \core_h5p\factory();
        $core = $factory->get_core();
        if (isset($defaultvalues['displayoptions'])) {
            $currentdisplay = $defaultvalues['displayoptions'];
            $displayoptions = (array) \core_h5p\helper::decode_display_options($core, $currentdisplay);
        } else {
            $displayoptions = (array) \core_h5p\helper::decode_display_options($core);
        }
        foreach ($displayoptions as $key => $value) {
            $fieldname = "displayopt[$key]";
            $defaultvalues[$fieldname] = $value;
        }
    }

    /**
     * Allows modules to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data passed by reference
     */
    public function data_postprocessing($data) {
        parent::data_postprocessing($data);

        $factory = new \core_h5p\factory();
        $core = $factory->get_core();
        if (isset($data->displayopt)) {
            $config = (object) $data->displayopt;
        } else {
            $config = \core_h5p\helper::decode_display_options($core);
        }
        $data->displayoptions = \core_h5p\helper::get_display_options($core, $config);
    }
}

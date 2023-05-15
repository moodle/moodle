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
 * Form for creating new H5P Content
 *
 * @package    mod_hvp
 * @copyright  2016 Joubel AS <contact@joubel.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/course/moodleform_mod.php');

class mod_hvp_mod_form extends moodleform_mod {

    public function definition() {
        global $CFG, $COURSE, $PAGE;

        $mform =& $this->_form;

        // Name.
        $mform->addElement('hidden', 'name', '');
        $mform->setType('name', PARAM_TEXT);

        // Intro.
        if (method_exists($this, 'standard_intro_elements')) {
            $this->standard_intro_elements();
        } else {
            $this->add_intro_editor(false, get_string('intro', 'hvp'));
        }

        // Action.
        $h5paction = array();
        $h5paction[] = $mform->createElement('radio', 'h5paction', '', get_string('upload', 'hvp'), 'upload');
        $h5paction[] = $mform->createElement('radio', 'h5paction', '', get_string('create', 'hvp'), 'create');
        $mform->addGroup($h5paction, 'h5pactiongroup', get_string('action', 'hvp'), array('<br/>'), false);
        $mform->setDefault('h5paction', 'create');

        // Upload.
        $mform->addElement('filepicker', 'h5pfile', get_string('h5pfile', 'hvp'), null,
            array('maxbytes' => $COURSE->maxbytes, 'accepted_types' => '*'));

        // Editor placeholder.
        if ($CFG->theme == 'boost' || in_array('boost', $PAGE->theme->parents)) {
            $h5peditor   = [];
            $h5peditor[] = $mform->createElement('html',
                                                 '<div class="h5p-editor">' . get_string('javascriptloading', 'hvp') . '</div>');
            $mform->addGroup($h5peditor, 'h5peditor', get_string('editor', 'hvp'));
        } else {
            $mform->addElement('static', 'h5peditor', get_string('editor', 'hvp'),
                               '<div class="h5p-editor">' . get_string('javascriptloading', 'hvp') . '</div>');
        }

        // Hidden fields.
        $mform->addElement('hidden', 'h5plibrary', '');
        $mform->setType('h5plibrary', PARAM_RAW);
        $mform->addElement('hidden', 'h5pparams', '');
        $mform->setType('h5pparams', PARAM_RAW);
        $mform->addElement('hidden', 'h5pmaxscore', '');
        $mform->setType('h5pmaxscore', PARAM_INT);

        $core = \mod_hvp\framework::instance();
        $displayoptions = $core->getDisplayOptionsForEdit();
        if (isset($displayoptions[H5PCore::DISPLAY_OPTION_FRAME])) {
            // Display options group.
            $mform->addElement('header', 'displayoptions', get_string('displayoptions', 'hvp'));

            $mform->addElement('checkbox', H5PCore::DISPLAY_OPTION_FRAME, get_string('enableframe', 'hvp'));
            $mform->setType(H5PCore::DISPLAY_OPTION_FRAME, PARAM_BOOL);
            $mform->setDefault(H5PCore::DISPLAY_OPTION_FRAME, true);

            if (isset($displayoptions[H5PCore::DISPLAY_OPTION_DOWNLOAD])) {
                $mform->addElement('checkbox', H5PCore::DISPLAY_OPTION_DOWNLOAD, get_string('enabledownload', 'hvp'));
                $mform->setType(H5PCore::DISPLAY_OPTION_DOWNLOAD, PARAM_BOOL);
                $mform->setDefault(H5PCore::DISPLAY_OPTION_DOWNLOAD, $displayoptions[H5PCore::DISPLAY_OPTION_DOWNLOAD]);
                $mform->disabledIf(H5PCore::DISPLAY_OPTION_DOWNLOAD, 'frame');
            }

            if (isset($displayoptions[H5PCore::DISPLAY_OPTION_EMBED])) {
                $mform->addElement('checkbox', H5PCore::DISPLAY_OPTION_EMBED, get_string('enableembed', 'hvp'));
                $mform->setType(H5PCore::DISPLAY_OPTION_EMBED, PARAM_BOOL);
                $mform->setDefault(H5PCore::DISPLAY_OPTION_EMBED, $displayoptions[H5PCore::DISPLAY_OPTION_EMBED]);
                $mform->disabledIf(H5PCore::DISPLAY_OPTION_EMBED, 'frame');
            }

            if (isset($displayoptions[H5PCore::DISPLAY_OPTION_COPYRIGHT])) {
                $mform->addElement('checkbox', H5PCore::DISPLAY_OPTION_COPYRIGHT, get_string('enablecopyright', 'hvp'));
                $mform->setType(H5PCore::DISPLAY_OPTION_COPYRIGHT, PARAM_BOOL);
                $mform->setDefault(H5PCore::DISPLAY_OPTION_COPYRIGHT, $displayoptions[H5PCore::DISPLAY_OPTION_COPYRIGHT]);
                $mform->disabledIf(H5PCore::DISPLAY_OPTION_COPYRIGHT, 'frame');
            }
        }

        // Grade settings.
        $this->standard_grading_coursemodule_elements();
        $mform->removeElement('grade');

        // Max grade.
        $mform->addElement('text', 'maximumgrade', get_string('maximumgrade', 'hvp'));
        $mform->setType('maximumgrade', PARAM_INT);
        $mform->setDefault('maximumgrade', 10);

        // Standard course module settings.
        $this->standard_coursemodule_elements();

        $this->add_action_buttons();
    }

    /**
     * Sets display options within default values
     *
     * @param $defaultvalues
     */
    private function set_display_options(&$defaultvalues) {
        // Individual display options are not stored, must be extracted from disable.
        if (isset($defaultvalues['disable'])) {
            $h5pcore = \mod_hvp\framework::instance('core');
            $displayoptions = $h5pcore->getDisplayOptionsForEdit($defaultvalues['disable']);
            if (isset ($displayoptions[H5PCore::DISPLAY_OPTION_FRAME])) {
                $defaultvalues[H5PCore::DISPLAY_OPTION_FRAME] = $displayoptions[H5PCore::DISPLAY_OPTION_FRAME];
            }
            if (isset($displayoptions[H5PCore::DISPLAY_OPTION_DOWNLOAD])) {
                $defaultvalues[H5PCore::DISPLAY_OPTION_DOWNLOAD] = $displayoptions[H5PCore::DISPLAY_OPTION_DOWNLOAD];
            }
            if (isset($displayoptions[H5PCore::DISPLAY_OPTION_EMBED])) {
                $defaultvalues[H5PCore::DISPLAY_OPTION_EMBED] = $displayoptions[H5PCore::DISPLAY_OPTION_EMBED];
            }
            if (isset($displayoptions[H5PCore::DISPLAY_OPTION_COPYRIGHT])) {
                $defaultvalues[H5PCore::DISPLAY_OPTION_COPYRIGHT] = $displayoptions[H5PCore::DISPLAY_OPTION_COPYRIGHT];
            }
        }
    }

    /**
     * Sets max grade in default values from grade item
     *
     * @param $content
     * @param $defaultvalues
     */
    private function set_max_grade($content, &$defaultvalues) {
        // Set default maxgrade.
        if (isset($content) && isset($content['id'])
            && isset($defaultvalues) && isset($defaultvalues['course'])) {

            // Get the gradeitem and set maxgrade.
            $gradeitem = grade_item::fetch(array(
                'itemtype' => 'mod',
                'itemmodule' => 'hvp',
                'iteminstance' => $content['id'],
                'courseid' => $defaultvalues['course']
            ));

            if (isset($gradeitem) && isset($gradeitem->grademax)) {
                $defaultvalues['maximumgrade'] = $gradeitem->grademax;
            }
        }
    }

    public function data_preprocessing(&$defaultvalues) {
        global $DB;
        $core = \mod_hvp\framework::instance();

        $content = null;
        if (!empty($defaultvalues['id'])) {
            // Load Content.
            $content = $core->loadContent($defaultvalues['id']);
        }

        $this->set_max_grade($content, $defaultvalues);

        // Aaah.. we meet again h5pfile!
        $draftitemid = file_get_submitted_draft_itemid('h5pfile');
        file_prepare_draft_area($draftitemid, $this->context->id, 'mod_hvp', 'package', 0);
        $defaultvalues['h5pfile'] = $draftitemid;
        $this->set_display_options($defaultvalues);

        // Determine default action.
        if (!get_config('mod_hvp', 'hub_is_enabled') && $content === null &&
            $DB->get_field_sql("SELECT id FROM {hvp_libraries} WHERE runnable = 1", null, IGNORE_MULTIPLE) === false) {
            $defaultvalues['h5paction'] = 'upload';
        }

        // Set editor defaults.
        $defaultvalues['h5plibrary'] = ($content === null ? 0 : H5PCore::libraryToString($content['library']));

        // Combine params and metadata in one JSON object.
        $params = ($content === null ? '{}' : $core->filterParameters($content));
        $maincontentdata = array('params' => json_decode($params));
        if (isset($content['metadata'])) {
            $maincontentdata['metadata'] = $content['metadata'];
        }
        $defaultvalues['h5pparams'] = json_encode($maincontentdata, true);

        // Completion settings check.
        if (empty($defaultvalues['completionusegrade'])) {
            $defaultvalues['completionpass'] = 0; // Forced unchecked.
        }

        // Add required editor assets.
        require_once('locallib.php');
        $mformid = $this->_form->getAttribute('id');
        \hvp_add_editor_assets($content === null ? null : $defaultvalues['id'], $mformid);
    }

    /**
     * Validate uploaded H5P
     *
     * @param $data
     * @param $errors
     */
    private function validate_upload($data, &$errors) {
        global $CFG;

        if (empty($data['h5pfile'])) {
            // Field missing.
            $errors['h5pfile'] = get_string('required');
        } else {
            $files = $this->get_draft_files('h5pfile');
            if (count($files) < 1) {
                // No file uploaded.
                $errors['h5pfile'] = get_string('required');
            } else {
                // Prepare to validate package.
                $file = reset($files);
                $interface = \mod_hvp\framework::instance('interface');

                $path = $CFG->tempdir . uniqid('/hvp-');
                $interface->getUploadedH5pFolderPath($path);
                $path .= '.h5p';
                $interface->getUploadedH5pPath($path);
                $file->copy_content_to($path);

                $h5pvalidator = \mod_hvp\framework::instance('validator');
                if (! $h5pvalidator->isValidPackage()) {
                    // Errors while validating the package.
                    $errors = array_map(function ($message) {
                        return $message->message;
                    }, \mod_hvp\framework::messages('error'));

                    $messages = array_merge(\mod_hvp\framework::messages('info'), $errors);
                    $errors['h5pfile'] = implode('<br/>', $messages);
                } else {
                    foreach ($h5pvalidator->h5pC->mainJsonData['preloadedDependencies'] as $dep) {
                        if ($dep['machineName'] === $h5pvalidator->h5pC->mainJsonData['mainLibrary']) {
                            if ($h5pvalidator->h5pF->libraryHasUpgrade($dep)) {
                                // We do not allow storing old content due to security concerns.
                                $errors['h5pfile'] = get_string('olduploadoldcontent', 'hvp');
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Validate new H5P
     *
     * @param $data
     */
    private function validate_created(&$data, &$errors) {
        // Validate library and params used in editor.
        $core = \mod_hvp\framework::instance();

        // Get library array from string.
        $library = H5PCore::libraryFromString($data['h5plibrary']);

        if (!$library) {
            $errors['h5peditor'] = get_string('librarynotselected', 'hvp');
        } else {
            // Check that library exists.
            $library['libraryId'] = $core->h5pF->getLibraryId($library['machineName'],
                $library['majorVersion'],
                $library['minorVersion']);
            if (!$library['libraryId']) {
                $errors['h5peditor'] = get_string('nosuchlibrary', 'hvp');
            } else {
                $data['h5plibrary'] = $library;

                if ($core->h5pF->libraryHasUpgrade($library)) {
                    // We do not allow storing old content due to security concerns.
                    $errors['h5peditor'] = get_string('anunexpectedsave', 'hvp');
                } else {
                    // Verify that parameters are valid.
                    if (empty($data['h5pparams'])) {
                        $errors['h5peditor'] = get_string('noparameters', 'hvp');
                    } else {
                        $params = json_decode($data['h5pparams']);
                        if ($params === null) {
                            $errors['h5peditor'] = get_string('invalidparameters', 'hvp');
                        } else {
                            $data['h5pparams'] = $params;
                        }
                    }
                }
            }
        }
    }

    /**
     * Validates editor form
     *
     * @param array $data
     * @param array $files
     *
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate max grade as a non-negative numeric value.
        if (!is_numeric($data['maximumgrade']) || $data['maximumgrade'] < 0) {
            $errors['maximumgrade'] = get_string('maximumgradeerror', 'hvp');
        }

        if ($data['h5paction'] === 'upload') {
            // Validate uploaded H5P file.
            unset($errors['name']); // Will be set in data_postprocessing().
            $this->validate_upload($data, $errors);
        } else {
            $this->validate_created($data, $errors);
        }

        if (array_key_exists('completion', $data) && $data['completion'] == COMPLETION_TRACKING_AUTOMATIC) {
            $completionpass = isset($data['completionpass']) ? $data['completionpass'] : $this->current->completionpass;
            // Show an error if require passing grade was selected and the grade to pass was set to 0.
            if ($completionpass && (empty($data['gradepass']) || grade_floatval($data['gradepass']) == 0)) {
                if (isset($data['completionpass'])) {
                    $errors['completionpassgroup'] = get_string('gradetopassnotset', 'hvp');
                } else {
                    $errors['gradepass'] = get_string('gradetopassmustbeset', 'hvp');
                }
            }
        }

        return $errors;
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
        // Determine disabled content features.
        $options = array(
            H5PCore::DISPLAY_OPTION_FRAME     => isset($data->frame) ? $data->frame : 0,
            H5PCore::DISPLAY_OPTION_DOWNLOAD  => isset($data->export) ? $data->export : 0,
            H5PCore::DISPLAY_OPTION_EMBED     => isset($data->embed) ? $data->embed : 0,
            H5PCore::DISPLAY_OPTION_COPYRIGHT => isset($data->copyright) ? $data->copyright : 0,
        );
        $core          = \mod_hvp\framework::instance();
        $data->disable = $core->getStorableDisplayOptions($options, 0);

        if (isset($data->h5pparams)) {
            // Remove metadata wrapper from form data.
            $params = json_decode($data->h5pparams);
            if ($params !== null) {
                $data->params = json_encode($params->params);
                if (isset($params->metadata)) {
                    $data->metadata = $params->metadata;
                }
            }
            // Cleanup.
            unset($data->h5pparams);
        }

        if (isset($data->h5paction)  && $data->h5paction === 'upload') {
            if (empty($data->metadata)) {
                $data->metadata = new stdClass();
            }

            if (empty($data->metadata->title)) {
                // Fix for legacy content upload to work.
                // Fetch title from h5p.json or use a default string if not available.
                $h5pvalidator = \mod_hvp\framework::instance('validator');
                $data->metadata->title = empty($h5pvalidator->h5pC->mainJsonData['title'])
                    ? 'Uploaded Content'
                    : $h5pvalidator->h5pC->mainJsonData['title'];
            }
            $data->name = $data->metadata->title; // Sort of a hack,
            // but there is no JavaScript that sets the value when there is no editor...
        }
    }

    /**
     * This should not be overridden, but we have to in order to support Moodle <3.2
     * and older Totara sites.
     *
     * Moodle 3.1 LTS is supported until May 2019, after that this can be dropped.
     * (could cause issues for new features if they add more to this in Core)
     *
     * @return object submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data() {
        $data = parent::get_data();

        if ($data) {
            // Check if moodleform_mod class has already taken care of the data for us.
            // If not this is an older Moodle or Totara site that we need to treat differently.

            $class = new ReflectionClass('moodleform_mod');
            $method = $class->getMethod('get_data');
            if ($method->class !== 'moodleform_mod') {
                // Moodleform_mod class doesn't override get_data so we need to convert it ourselves.

                // Convert the grade pass value - we may be using a language which uses commas,
                // rather than decimal points, in numbers. These need to be converted so that
                // they can be added to the DB.
                if (isset($data->gradepass)) {
                    $data->gradepass = unformat_float($data->gradepass);
                }
                $this->data_postprocessing($data);
            }
        }
        return $data;
    }

    public function add_completion_rules() {
        $mform   =& $this->_form;
        $items   = array();
        $group   = array();
        $group[] = $mform->createElement('advcheckbox', 'completionpass', null, get_string('completionpass', 'hvp'),
            array('group' => 'cpass'));
        $mform->disabledIf('completionpass', 'completionusegrade', 'notchecked');
        $mform->addGroup($group, 'completionpassgroup', get_string('completionpass', 'hvp'), ' &nbsp; ', false);
        $mform->addHelpButton('completionpassgroup', 'completionpass', 'hvp');
        $items[] = 'completionpassgroup';

        return $items;
    }
}

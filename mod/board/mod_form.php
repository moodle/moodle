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

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/course/moodleform_mod.php');

use mod_board\board;
use mod_board\local\note;

/**
 * The mod form.
 * @package     mod_board
 * @author      Karen Holland <karen@brickfieldlabs.ie>
 * @copyright   2021 Brickfield Education Labs <https://www.brickfield.ie/>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class mod_board_mod_form extends moodleform_mod {
    /**
     * The definition function.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'general', get_string('general', 'form'));
        $mform->addElement('text', 'name', get_string('name'), ['size' => '50']);
        if (!empty($CFG->formatstringstriptags)) {
            $mform->setType('name', PARAM_TEXT);
        } else {
            $mform->setType('name', PARAM_CLEANHTML);
        }
        $mform->addRule('name', null, 'required', null, 'client');
        $mform->addRule('name', get_string('maximumchars', '', 100), 'maxlength', 100, 'client');
        $this->standard_intro_elements();

        $mform->addElement('header', 'board', get_string('boardsettings', 'mod_board'));
        if (!$this->current->id) {
            $templates = \mod_board\local\template::get_applicable_templates($this->context);
            if ($templates) {
                $templates = ['' => get_string('choosedots')] + $templates;
                $mform->addElement('select', 'templateid', get_string('template', 'mod_board'), $templates);
            }
        }

        $mform->addElement('text', 'background_color', get_string('background_color', 'mod_board'), ['size' => '50']);
        $mform->setType('background_color', PARAM_TEXT);
        $mform->addRule('background_color', get_string('maximumchars', '', 9), 'maxlength', 9, 'client');
        $mform->addHelpButton('background_color', 'background_color', 'mod_board');

        $mform->addElement(
            'filemanager',
            'background_image',
            get_string('background_image', 'mod_board'),
            null,
            board::get_background_picker_options()
        );

        $mform->addElement(
            'select',
            'addrating',
            get_string('addrating', 'mod_board'),
            [
                board::RATINGDISABLED => get_string('addrating_none', 'mod_board'),
                board::RATINGBYSTUDENTS => get_string('addrating_students', 'mod_board'),
                board::RATINGBYTEACHERS => get_string('addrating_teachers', 'mod_board'),
                board::RATINGBYALL => get_string('addrating_all', 'mod_board'),
            ]
        );
        $mform->setType('addrating', PARAM_INT);

        $mform->addElement('checkbox', 'hideheaders', get_string('hideheaders', 'mod_board'));
        $mform->setType('hideheaders', PARAM_INT);

        $mform->addElement(
            'select',
            'sortby',
            get_string('sortby', 'mod_board'),
            [
                board::SORTBYNONE => get_string('sortbynone', 'mod_board'),
                board::SORTBYDATE => get_string('sortbydate', 'mod_board'),
                board::SORTBYRATING => get_string('sortbyrating', 'mod_board'),
            ]
        );
        $mform->setType('sortby', PARAM_INT);

        $boardhasnotes = (!empty($this->_cm) && board::board_has_notes($this->_cm->instance));
        if ($boardhasnotes) {
            $mform->addElement('html', '<div class="alert alert-info">' . get_string('boardhasnotes', 'mod_board') . '</div>');
        }
        [$allowprivate, $allowpublic] = str_split(get_config('mod_board', 'allowed_singleuser_modes'));
        $modesallow = [
            board::SINGLEUSER_PRIVATE => $allowprivate,
            board::SINGLEUSER_PUBLIC => $allowpublic,
            board::SINGLEUSER_DISABLED => "1",
        ];
        $allowedsumodes = array_filter([
            board::SINGLEUSER_DISABLED => get_string('singleusermodenone', 'mod_board'),
            board::SINGLEUSER_PRIVATE => get_string('singleusermodeprivate', 'mod_board'),
            board::SINGLEUSER_PUBLIC => get_string('singleusermodepublic', 'mod_board'),
            ], function ($mode) use ($modesallow) {
                return $modesallow[$mode];
            }, ARRAY_FILTER_USE_KEY);
        if (count($allowedsumodes) > 1) {
            $mform->addElement('select', 'singleusermode', get_string('singleusermode', 'mod_board'), $allowedsumodes);
        }
        $mform->setType('singleusermode', PARAM_INT);
        if ($boardhasnotes) {
            $mform->addElement('hidden', 'hasnotes', $boardhasnotes);
            $mform->setType('hasnotes', PARAM_BOOL);
            $mform->disabledIf('singleusermode', 'hasnotes', 'gt', 0);
        }

        $mform->addElement('checkbox', 'postbyenabled', get_string('postbyenabled', 'mod_board'));
        $mform->addElement('date_time_selector', 'postby', get_string('postbydate', 'mod_board'));
        $mform->hideIf('postby', 'postbyenabled', 'notchecked');

        $mform->addElement('advcheckbox', 'userscanedit', get_string('userscanedit', 'mod_board'));

        $mform->addElement('advcheckbox', 'enableblanktarget', get_string('enableblanktarget', 'mod_board'));
        $mform->addHelpButton('enableblanktarget', 'enableblanktarget', 'mod_board');

        // Only add the embed setting, if embedding is allowed globally.
        if (get_config('mod_board', 'embed_allowed')) {
            // Embed board on the course, rather then give a link to it.
            $mform->addElement('advcheckbox', 'embed', get_string('embedboard', 'mod_board'));

            $mform->addElement('advcheckbox', 'hidename', get_string('hidename', 'mod_board'));
        }

        $this->standard_coursemodule_elements();

        $this->add_action_buttons();

        $mform->addElement('hidden', 'revision');
        $mform->setType('revision', PARAM_INT);
        $mform->setDefault('revision', 1);
    }

    /**
     * Preprocess the data.
     * @param array $defaultvalues
     */
    public function data_preprocessing(&$defaultvalues) {
        $draftitemid = file_get_submitted_draft_itemid('background_image');
        file_prepare_draft_area(
            $draftitemid,
            $this->context->id,
            'mod_board',
            'background',
            0,
            board::get_background_picker_options()
        );
        $defaultvalues['background_image'] = $draftitemid;

        $defaultvalues['postbyenabled'] = !empty($defaultvalues['postby']);

        $defaultvalues['completionnotesenabled'] = !empty($defaultvalues['completionnotes']) ? 1 : 0;
    }

    /**
     * Validate the data.
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (get_config('mod_board', 'embed_allowed')) {
            if (($data['embed'] == 1) && ($data['singleusermode'] != board::SINGLEUSER_DISABLED)) {
                $errors['embed'] = get_string('singleusermodenotembed', 'mod_board');
            }
        }

        return $errors;
    }

    /**
     * Add custom completion rules.
     *
     * @return array Array of string IDs of added items, empty array if none
     */
    public function add_completion_rules() {
        global $CFG;

        $mform = $this->_form;

        // Changes for Moodle 4.3 - MDL-78516.
        if ($CFG->branch < 403) {
            $suffix = '';
        } else {
            $suffix = $this->get_suffix();
        }

        $group = [];
        $group[] = $mform->createElement(
            'checkbox',
            'completionnotesenabled' . $suffix,
            '',
            get_string('completionnotes', 'mod_board')
        );
        $group[] = $mform->createElement('text', 'completionnotes' . $suffix, '', ['size' => 3]);
        $mform->setType('completionnotes' . $suffix, PARAM_INT);
        $mform->addGroup($group, 'completionnotesgroup' . $suffix, get_string('completionnotesgroup', 'mod_board'), [' '], false);
        $mform->disabledIf('completionnotes' . $suffix, 'completionnotesenabled', 'notchecked');

        return ['completionnotesgroup' . $suffix];
    }

    /**
     * Determines if completion is enabled for this module.
     *
     * @param array $data
     * @return bool
     */
    public function completion_rule_enabled($data) {
        return (!empty($data['completionnotesenabled']) && $data['completionnotes'] != 0);
    }

    /**
     * Allows module to modify the data returned by form get_data().
     * This method is also called in the bulk activity completion form.
     *
     * Only available on moodleform_mod.
     *
     * @param stdClass $data the form data to be modified.
     */
    public function data_postprocessing($data) {
        parent::data_postprocessing($data);
        // Turn off completion settings if the checkboxes aren't ticked.
        if (!empty($data->completionunlocked)) {
            $autocompletion = !empty($data->completion) && $data->completion == COMPLETION_TRACKING_AUTOMATIC;
            if (empty($data->completionnotesenabled) || !$autocompletion) {
                $data->completionnotes = 0;
            }
        }
    }
}

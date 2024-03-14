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
 * Form for editing steps.
 *
 * @package    tool_usertours
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_usertours\local\forms;

use stdClass;
use tool_usertours\helper;
use tool_usertours\step;

defined('MOODLE_INTERNAL') || die('Direct access to this script is forbidden.');

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing steps.
 *
 * @copyright  2016 Andrew Nicols <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editstep extends \moodleform {
    /**
     * @var tool_usertours\step $step
     */
    protected $step;

    /**
     * @var int Display the step's content by using Moodle language string.
     */
    private const CONTENTTYPE_LANGSTRING = 0;

    /**
     * @var int Display the step's content by entering it manually.
     */
    private const CONTENTTYPE_MANUAL = 1;

    /**
     * Create the edit step form.
     *
     * @param   string      $target     The target of the form.
     * @param   step        $step       The step being editted.
     */
    public function __construct($target, \tool_usertours\step $step) {
        $this->step = $step;

        parent::__construct($target);
    }

    /**
     * Form definition.
     */
    public function definition() {
        global $CFG;

        $mform = $this->_form;

        $mform->addElement('header', 'heading_target', get_string('target_heading', 'tool_usertours'));
        $types = [];
        foreach (\tool_usertours\target::get_target_types() as $value => $type) {
            $types[$value] = get_string('target_' . $type, 'tool_usertours');
        }
        $mform->addElement('select', 'targettype', get_string('targettype', 'tool_usertours'), $types);
        $mform->addHelpButton('targettype', 'targettype', 'tool_usertours');

        // The target configuration.
        foreach (\tool_usertours\target::get_target_types() as $value => $type) {
            $targetclass = \tool_usertours\target::get_classname($type);
            $targetclass::add_config_to_form($mform);
        }

        // Content of the step.
        $mform->addElement('header', 'heading_content', get_string('content_heading', 'tool_usertours'));
        $mform->addElement('textarea', 'title', get_string('title', 'tool_usertours'));
        $mform->addRule('title', get_string('required'), 'required', null, 'client');
        $mform->setType('title', PARAM_TEXT);
        $mform->addHelpButton('title', 'title', 'tool_usertours');

        // Content type.
        $typeoptions = [
            static::CONTENTTYPE_LANGSTRING => get_string('content_type_langstring', 'tool_usertours'),
            static::CONTENTTYPE_MANUAL => get_string('content_type_manual', 'tool_usertours'),
        ];
        $mform->addElement('select', 'contenttype', get_string('content_type', 'tool_usertours'), $typeoptions);
        $mform->addHelpButton('contenttype', 'content_type', 'tool_usertours');
        $mform->setDefault('contenttype', static::CONTENTTYPE_MANUAL);

        // Language identifier.
        $mform->addElement('textarea', 'contentlangstring', get_string('moodle_language_identifier', 'tool_usertours'));
        $mform->setType('contentlangstring', PARAM_TEXT);
        $mform->hideIf('contentlangstring', 'contenttype', 'eq', static::CONTENTTYPE_MANUAL);

        $editoroptions = [
            'subdirs' => 1,
            'maxbytes' => $CFG->maxbytes,
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'changeformat' => 1,
            'trusttext' => true,
        ];
        $objs = $mform->createElement('editor', 'content', get_string('content', 'tool_usertours'), null, $editoroptions);
        // TODO: MDL-68540 We need to add the editor to a group element because editor element will not work with hideIf.
        $mform->addElement('group', 'contenthtmlgrp', get_string('content', 'tool_usertours'), [$objs], ' ', false);
        $mform->addHelpButton('contenthtmlgrp', 'content', 'tool_usertours');
        $mform->hideIf('contenthtmlgrp', 'contenttype', 'eq', static::CONTENTTYPE_LANGSTRING);

        // Add the step configuration.
        $mform->addElement('header', 'heading_options', get_string('options_heading', 'tool_usertours'));
        // All step configuration is defined in the step.
        $this->step->add_config_to_form($mform);

        // And apply any form constraints.
        foreach (\tool_usertours\target::get_target_types() as $value => $type) {
            $targetclass = \tool_usertours\target::get_classname($type);
            $targetclass::add_disabled_constraints_to_form($mform);
        }

        $this->add_action_buttons();
    }

    /**
     * Validate the database on the submitted content type.
     *
     * @param array $data array of ("fieldname"=>value) of submitted data
     * @param array $files array of uploaded files "element_name"=>tmp_file_path
     * @return array of "element_name"=>"error_description" if there are errors,
     *         or an empty array if everything is OK (true allowed for backwards compatibility too).
     */
    public function validation($data, $files): array {
        $errors = parent::validation($data, $files);

        if ($data['contenttype'] == static::CONTENTTYPE_LANGSTRING) {
            if (!isset($data['contentlangstring']) || trim($data['contentlangstring']) == '') {
                $errors['contentlangstring'] = get_string('required');
            } else {
                $splitted = explode(',', trim($data['contentlangstring']), 2);
                $langid = $splitted[0];
                $langcomponent = $splitted[1];
                if (!get_string_manager()->string_exists($langid, $langcomponent)) {
                    $errors['contentlangstring'] = get_string('invalid_lang_id', 'tool_usertours');
                }
            }
        }

        // Validate manually entered text content. Validation logic derived from \MoodleQuickForm_Rule_Required::validate()
        // without the checking of the "strictformsrequired" admin setting.
        if ($data['contenttype'] == static::CONTENTTYPE_MANUAL) {
            $value = $data['content']['text'] ?? '';

            // All tags except img, canvas and hr, plus all forms of whitespaces.
            $stripvalues = [
                '#</?(?!img|canvas|hr).*?>#im',
                '#(\xc2\xa0|\s|&nbsp;)#',
            ];
            $value = preg_replace($stripvalues, '', (string)$value);
            if (empty($value)) {
                $errors['contenthtmlgrp'] = get_string('required');
            }
        }

        return $errors;
    }

    /**
     * Load in existing data as form defaults. Usually new entry defaults are stored directly in
     * form definition (new entry form); this function is used to load in data where values
     * already exist and data is being edited (edit entry form).
     *
     * @param stdClass|array $data object or array of default values
     */
    public function set_data($data): void {
        $data = (object) $data;
        if (!isset($data->contenttype)) {
            if (!empty($data->content['text']) && helper::is_language_string_from_input($data->content['text'])) {
                $data->contenttype = static::CONTENTTYPE_LANGSTRING;
                $data->contentlangstring = $data->content['text'];

                // Empty the editor content.
                $data->content = ['text' => ''];
            } else {
                $data->contenttype = static::CONTENTTYPE_MANUAL;
            }
        }
        parent::set_data($data);
    }

    /**
     * Return submitted data if properly submitted or returns NULL if validation fails or
     * if there is no submitted data.
     *
     * @return object|null submitted data; NULL if not valid or not submitted or cancelled
     */
    public function get_data(): ?object {
        $data = parent::get_data();
        if ($data) {
            if ($data->contenttype == static::CONTENTTYPE_LANGSTRING) {
                $data->content = [
                    'text' => $data->contentlangstring,
                    'format' => FORMAT_MOODLE,
                ];
            }
        }
        return $data;
    }
}

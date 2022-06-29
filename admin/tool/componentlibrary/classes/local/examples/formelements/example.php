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
 * Example form to showcase the rendering of form fields.
 *
 * @package    tool_componentlibrary
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_componentlibrary\local\examples\formelements;

/**
 * Example form to showcase the rendering of form fields.
 *
 * @copyright  2021 Bas Brands <bas@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class example extends \moodleform {
    /**
     * Elements of the test form.
     */
    public function definition() {
        $mform = $this->_form;

        $required = optional_param('required', false, PARAM_BOOL);
        $help = optional_param('help', false, PARAM_BOOL);
        $mixed = optional_param('mixed', false, PARAM_BOOL);

        // Text.
        $mform->addElement('text', 'textelement', 'Text');
        $mform->setType('textelement', 'text');
        if ($required) {
            $mform->addRule('textelement', null, 'required', null, 'client');
        }
        if ($help && !$mixed) {
            $mform->addHelpButton('textelement', 'summary');
        }
        $mform->setAdvanced('textelement', true);

        // Text with a long label.
        $mform->addElement('text', 'textelementtwo', 'Text element with a long label that can span multiple lines.
            The next field has no label. ');
        $mform->setType('textelementtwo', 'text');
        if ($required) {
            $mform->addRule('textelementtwo', 'This element is required', 'required', null, 'client');
        }
        if ($help) {
            $mform->addHelpButton('textelementtwo', 'summary');
        }
        $mform->setAdvanced('textelementtwo', true);

        // Text without label.
        $mform->addElement('text', 'textelementhree', '', '');
        $mform->setType('textelementhree', 'text');
        if ($required && !$mixed) {
            $mform->addRule('textelementhree', 'This element is required', 'required', null, 'client');
        }
        if ($help && !$mixed) {
            $mform->addHelpButton('textelementhree', 'summary');
        }
        $mform->setAdvanced('textelementhree', true);

        // Button.
        $mform->addElement('button', 'buttonelement', 'Button');
        if ($required) {
            $mform->addRule('buttonelement', 'This element is required', 'required', null, 'client');
        }
        if ($help) {
            $mform->addHelpButton('buttonelement', 'summary');
        }
        $mform->setAdvanced('buttonelement', true);

        // Date.
        $mform->addElement('date_selector', 'date', 'Date selector');
        if ($required && !$mixed) {
            $mform->addRule('date', 'This element is required', 'required', null, 'client');
        }
        if ($help) {
            $mform->addHelpButton('date', 'summary');
        }
        $mform->setAdvanced('date', true);

        // Date time.
        $mform->addElement('date_time_selector', 'datetimesel', 'Date time selector');
        if ($required) {
            $mform->addRule('datetimesel', 'This element is required', 'required', null, 'client');
        }
        if ($help && !$mixed) {
            $mform->addHelpButton('datetimesel', 'summary');
        }
        $mform->setAdvanced('datetimesel', true);

        // Duration (does not support required form fields).
        $mform->addElement('duration', 'duration', 'Duration');
        if ($help) {
            $mform->addHelpButton('duration', 'summary');
        }

        // Editor.
        $mform->addElement('editor', 'editor', 'Editor');
        $mform->setType('editor', PARAM_RAW);
        if ($required) {
            $mform->addRule('editor', 'This element is required', 'required', null, 'client');
        }
        if ($help && !$mixed) {
            $mform->addHelpButton('editor', 'summary');
        }
        $mform->setAdvanced('editor', true);

        // Filepicker.
        $mform->addElement('filepicker', 'userfile', 'Filepicker', null, ['maxbytes' => 100, 'accepted_types' => '*']);
        if ($required) {
            $mform->addRule('userfile', 'This element is required', 'required', null, 'client');
        }
        if ($help) {
            $mform->addHelpButton('userfile', 'summary');
        }
        $mform->setAdvanced('userfile', true);

        // Html.
        $mform->addElement('html', '<div class="text-success h2 ">The HTML only formfield</div>');

        // Passwords.
        $mform->addElement('passwordunmask', 'passwordunmask', 'Passwordunmask');
        if ($required && !$mixed) {
            $mform->addRule('passwordunmask', 'This element is required', 'required', null, 'client');
        }
        if ($help && !$mixed) {
            $mform->addHelpButton('passwordunmask', 'summary');
        }
        $mform->setAdvanced('passwordunmask', true);

        // Radio.
        $mform->addElement('radio', 'radio', 'Radio', 'Radio label', 'choice_value');
        if ($required) {
            $mform->addRule('radio', 'This element is required', 'required', null, 'client');
        }
        if ($help && !$mixed) {
            $mform->addHelpButton('radio', 'summary');
        }
        $mform->setAdvanced('radio', true);

        // Checkbox.
        $mform->addElement('checkbox', 'checkbox', 'Checkbox', 'Checkbox Text');
        if ($required) {
            $mform->addRule('checkbox', 'This element is required', 'required', null, 'client');
        }
        if ($help) {
            $mform->addHelpButton('checkbox', 'summary');
        }
        $mform->setAdvanced('checkbox', true);

        // Select.
        $mform->addElement('select', 'auth', 'Select', ['cow', 'crow', 'dog', 'cat']);
        if ($required && !$mixed) {
            $mform->addRule('auth', 'This element is required', 'required', null, 'client');
        }
        if ($help) {
            $mform->addHelpButton('auth', 'summary');
        }
        $mform->setAdvanced('auth', true);

        // Yes No.
        $mform->addElement('selectyesno', 'selectyesno', 'Selectyesno');
        if ($required && !$mixed) {
            $mform->addRule('selectyesno', 'This element is required', 'required', null, 'client');
        }
        if ($help) {
            $mform->addHelpButton('selectyesno', 'summary');
        }
        $mform->setAdvanced('selectyesno', true);

        // Static.
        $mform->addElement('static', 'static', 'Static', 'static description');

        // Float.
        $mform->addElement('float', 'float', 'Floating number');
        if ($required) {
            $mform->addRule('float', 'This element is required', 'required', null, 'client');
        }
        if ($help) {
            $mform->addHelpButton('float', 'summary');
        }
        $mform->setAdvanced('float', true);

        // Textarea.
        $mform->addElement('textarea', 'textarea', 'Text area', 'wrap="virtual" rows="20" cols="50"');
        if ($required && !$mixed) {
            $mform->addRule('textarea', 'This element is required', 'required', null, 'client');
        }
        if ($help && !$mixed) {
            $mform->addHelpButton('textarea', 'summary');
        }
        $mform->setAdvanced('textarea', true);

        // Recaptcha. (does not support required).
        $mform->addElement('recaptcha', 'recaptcha', 'Recaptcha');
        if ($help) {
            $mform->addHelpButton('recaptcha', 'summary');
        }

        // Tags.
        $mform->addElement('tags', 'tags', 'Tags', ['itemtype' => 'course_modules', 'component' => 'core']);
        if ($required && !$mixed) {
            $mform->addRule('tags', 'This element is required', 'required', null, 'client');
        }
        if ($help && !$mixed) {
            $mform->addHelpButton('tags', 'summary');
        }
        $mform->setAdvanced('tags', true);

        // Filetypes. (does not support required).
        $mform->addElement('filetypes', 'filetypes', 'Allowedfiletypes', ['onlytypes' => ['document', 'image'],
            'allowunknown' => true]);
        if ($help) {
            $mform->addHelpButton('filetypes', 'summary');
        }
        $mform->setAdvanced('filetypes', true);

        // Advanced checkbox.
        $mform->addElement('advcheckbox', 'advcheckbox', 'Advanced checkbox', 'Advanced checkbox name', ['group' => 1],
            [0, 1]);
        if ($required) {
            $mform->addRule('advcheckbox', 'This element is required', 'required', null, 'client');
        }
        if ($help) {
            $mform->addHelpButton('advcheckbox', 'summary');
        }
        $mform->setAdvanced('advcheckbox', true);

        // Autocomplete.
        $searchareas = \core_search\manager::get_search_areas_list(true);
        $areanames = [];
        foreach ($searchareas as $areaid => $searcharea) {
            $areanames[$areaid] = $searcharea->get_visible_name();
        }
        $options = [
            'multiple' => true,
            'noselectionstring' => get_string('allareas', 'search'),
        ];
        $mform->addElement('autocomplete', 'autocomplete', get_string('searcharea', 'search'), $areanames, $options);
        if ($required) {
            $mform->addRule('autocomplete', 'This element is required', 'required', null, 'client');
        }
        if ($help && !$mixed) {
            $mform->addHelpButton('autocomplete', 'summary');
        }
        $mform->setAdvanced('autocomplete', true);

        // Group.
        $radiogrp = [
            $mform->createElement('text', 'rtext', 'Text'),
            $mform->createElement('radio', 'rradio', 'Radio label', 'After one ', 1),
            $mform->createElement('checkbox', 'rchecbox', 'Checkbox label', 'After two ', 2)
        ];
        $mform->setType('rtext', PARAM_RAW);
        $mform->addGroup($radiogrp, 'group', 'Group', ' ', false);
        if ($required) {
            $mform->addRule('group', 'This element is required', 'required', null, 'client');
        }
        if ($help) {
            $mform->addHelpButton('group', 'summary');
        }
        $mform->setAdvanced('group', true);

        $group = $mform->getElement('group');

        // Group of groups.
        $group = [];
        $group[] = $mform->createElement('select', 'profilefield', '', [0 => 'Username', 1 => 'Email']);
        $elements = [];
        $elements[] = $mform->createElement('select', 'operator', null, [0 => 'equal', 1 => 'not equal']);
        $elements[] = $mform->createElement('text', 'value', null);
        $elements[] = $mform->createElement('static', 'desc', 'Just a static text', 'Just a static text');
        $mform->setType('value', PARAM_RAW);
        $group[] = $mform->createElement('group', 'fieldvalue', '', $elements, '', false);
        $mform->addGroup($group, 'fieldsgroup', 'Group containing another group', '', false);
        if ($required) {
            $mform->addRule('fieldsgroup', 'This element is required', 'required', null, 'client');
        }
        if ($help) {
            $mform->addHelpButton('fieldsgroup', 'summary');
        }

        $this->add_action_buttons();
    }
}

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

namespace core_cache\form;

use core_cache\administration_helper;
use core_cache\definition;
use moodleform;

/**
 * Form to set definition sharing option
 *
 * @package    core_cache
 * @category   cache
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_definition_sharing_form extends moodleform {
    /**
     * The definition of the form
     */
    final protected function definition() {
        $definition = $this->_customdata['definition'];
        $sharingoptions = $this->_customdata['sharingoptions'];
        $form = $this->_form;

        $form->addElement('hidden', 'definition', $definition);
        $form->setType('definition', PARAM_SAFEPATH);
        $form->addElement('hidden', 'action', 'editdefinitionsharing');
        $form->setType('action', PARAM_ALPHA);

        // We use a group here for validation.
        $count = 0;
        $group = [];
        foreach ($sharingoptions as $value => $text) {
            $count++;
            $group[] = $form->createElement('checkbox', $value, null, $text);
        }
        $form->addGroup($group, 'sharing', get_string('sharing', 'cache'), '<br />');
        $form->setType('sharing', PARAM_INT);

        $form->addElement('text', 'userinputsharingkey', get_string('userinputsharingkey', 'cache'));
        $form->addHelpButton('userinputsharingkey', 'userinputsharingkey', 'cache');
        $form->disabledIf('userinputsharingkey', 'sharing[' . definition::SHARING_INPUT . ']', 'notchecked');
        $form->setType('userinputsharingkey', PARAM_ALPHANUMEXT);

        $values = array_keys($sharingoptions);
        if (in_array(definition::SHARING_ALL, $values)) {
            // If you share with all thenthe other options don't really make sense.
            foreach ($values as $value) {
                $form->disabledIf('sharing[' . $value . ']', 'sharing[' . definition::SHARING_ALL . ']', 'checked');
            }
            $form->disabledIf('userinputsharingkey', 'sharing[' . definition::SHARING_ALL . ']', 'checked');
        }

        $this->add_action_buttons();
    }

    /**
     * Sets the data for this form.
     *
     * @param array $data
     */
    public function set_data($data) {
        if (!isset($data['sharing'])) {
            // Set the default value here. mforms doesn't handle defaults very nicely.
            $data['sharing'] = administration_helper::get_definition_sharing_options(definition::SHARING_DEFAULT);
        }
        parent::set_data($data);
    }

    /**
     * Validates this form
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (count($errors) === 0 && !isset($data['sharing'])) {
            // They must select at least one sharing option.
            $errors['sharing'] = get_string('sharingrequired', 'cache');
        }
        return $errors;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(cache_definition_sharing_form::class, \cache_definition_sharing_form::class);

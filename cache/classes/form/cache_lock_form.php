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

use core_cache\config as cache_config;
use moodleform;

/**
 * Form to add a cache lock instance.
 *
 * All cache lock plugins that wish to have custom configuration should override
 * this form, and more explicitly the plugin_definition and plugin_validation methods.
 *
 * @package    core_cache
 * @category   cache
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_lock_form extends moodleform {
    /**
     * Defines this form.
     */
    final public function definition() {
        $plugin = $this->_customdata['lock'];

        $this->_form->addElement('hidden', 'action', 'newlockinstance');
        $this->_form->setType('action', PARAM_ALPHANUMEXT);
        $this->_form->addElement('hidden', 'lock', $plugin);
        $this->_form->setType('lock', PARAM_COMPONENT);
        $this->_form->addElement('text', 'name', get_string('lockname', 'cache'));
        $this->_form->setType('name', PARAM_ALPHANUMEXT);
        $this->_form->addRule('name', get_string('required'), 'required');
        $this->_form->addElement('static', 'namedesc', '', get_string('locknamedesc', 'cache'));

        $this->plugin_definition();

        $this->add_action_buttons();
    }

    /**
     * Validates this form.
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    final public function validation($data, $files) {
        $errors = parent::validation($data, $files);
        if (!isset($errors['name'])) {
            $config = cache_config::instance();
            if (in_array($data['name'], array_keys($config->get_locks()))) {
                $errors['name'] = get_string('locknamenotunique', 'cache');
            }
        }
        $errors = $this->plugin_validation($data, $files, $errors);
        return $errors;
    }

    /**
     * Plugin specific definition.
     */
    public function plugin_definition() {
        // No custom validation going on here.
    }

    /**
     * Plugin specific validation.
     *
     * @param array $data
     * @param array $files
     * @param array $errors
     * @return array
     */
    public function plugin_validation($data, $files, array $errors) {
        return $errors;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(cache_lock_form::class, \cache_lock_form::class);

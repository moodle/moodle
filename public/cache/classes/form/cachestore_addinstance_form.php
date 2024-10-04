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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/lib/formslib.php');

use core_cache\administration_helper;
use moodleform;

/**
 * Add store instance form.
 *
 * @package    core_cache
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_addinstance_form extends moodleform {
    #[\Override]
    final protected function definition() {
        $form = $this->_form;
        $store = $this->_customdata['store'];
        $plugin = $this->_customdata['plugin'];
        $locks = $this->_customdata['locks'];

        $form->addElement('hidden', 'plugin', $plugin);
        $form->setType('plugin', PARAM_PLUGIN);
        $form->addElement('hidden', 'editing', !empty($this->_customdata['store']));
        $form->setType('editing', PARAM_BOOL);

        if (!$store) {
            $form->addElement('text', 'name', get_string('storename', 'cache'));
            $form->addHelpButton('name', 'storename', 'cache');
            $form->addRule('name', get_string('required'), 'required');
            $form->setType('name', PARAM_NOTAGS);
        } else {
            $form->addElement('hidden', 'name', $store);
            $form->addElement('static', 'name-value', get_string('storename', 'cache'), $store);
            $form->setType('name', PARAM_NOTAGS);
        }

        if (is_array($locks)) {
            $form->addElement('select', 'lock', get_string('locking', 'cache'), $locks);
            $form->addHelpButton('lock', 'locking', 'cache');
            $form->setType('lock', PARAM_ALPHANUMEXT);
        } else {
            $form->addElement('hidden', 'lock', '');
            $form->setType('lock', PARAM_ALPHANUMEXT);
            $form->addElement(
                'static',
                'lock-value',
                get_string('locking', 'cache'),
                '<em>' . get_string('nativelocking', 'cache') . '</em>'
            );
        }

        if (method_exists($this, 'configuration_definition')) {
            $form->addElement('header', 'storeconfiguration', get_string('storeconfiguration', 'cache'));
            $this->configuration_definition();
        }

        $this->add_action_buttons();
    }

    #[\Override]
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!array_key_exists('name', $errors)) {
            if (!preg_match('#^[a-zA-Z0-9\-_ ]+$#', $data['name'])) {
                $errors['name'] = get_string('storenameinvalid', 'cache');
            } else if (empty($this->_customdata['store'])) {
                $stores = administration_helper::get_store_instance_summaries();
                if (array_key_exists($data['name'], $stores)) {
                    $errors['name'] = get_string('storenamealreadyused', 'cache');
                }
            }
        }

        if (method_exists($this, 'configuration_validation')) {
            $newerrors = $this->configuration_validation($data, $files, $errors);
            // We need to selectiviliy merge here.
            foreach ($newerrors as $element => $error) {
                if (!array_key_exists($element, $errors)) {
                    $errors[$element] = $error;
                }
            }
        }

        return $errors;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(cachestore_addinstance_form::class, \cachestore_addinstance_form::class);

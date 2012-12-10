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
 * Forms used for the administration and managemement of the cache setup.
 *
 * This file is part of Moodle's cache API, affectionately called MUC.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/lib/formslib.php');

/**
 * Add store instance form.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_addinstance_form extends moodleform {

    /**
     * The definition of the add instance form
     */
    protected final function definition() {
        $form = $this->_form;
        $store = $this->_customdata['store'];
        $plugin = $this->_customdata['plugin'];
        $locks = $this->_customdata['locks'];

        $form->addElement('hidden', 'plugin', $plugin);
        $form->addElement('hidden', 'editing', !empty($this->_customdata['store']));

        if (!$store) {
            $form->addElement('text', 'name', get_string('storename', 'cache'));
            $form->addHelpButton('name', 'storename', 'cache');
            $form->addRule('name', get_string('required'), 'required');
            $form->setType('name', PARAM_TEXT);
        } else {
            $form->addElement('hidden', 'name', $store);
            $form->addElement('static', 'name-value', get_string('storename', 'cache'), $store);
        }

        if (is_array($locks)) {
            $form->addElement('select', 'lock', get_string('lockmethod', 'cache'), $locks);
            $form->addHelpButton('lock', 'lockmethod', 'cache');
            $form->setType('lock', PARAM_TEXT);
        } else {
            $form->addElement('hidden', 'lock', '');
            $form->addElement('static', 'lock-value', get_string('lockmethod', 'cache'),
                    '<em>'.get_string('nativelocking', 'cache').'</em>');
        }

        if (method_exists($this, 'configuration_definition')) {
            $form->addElement('header', 'storeconfiguration', get_string('storeconfiguration', 'cache'));
            $this->configuration_definition();
        }

        $this->add_action_buttons();
    }

    /**
     * Validates the add instance form data
     *
     * @param array $data
     * @param array $files
     * @return array
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (!array_key_exists('name', $errors)) {
            if (!preg_match('#^[a-zA-Z0-9\-_ ]+$#', $data['name'])) {
                $errors['name'] = get_string('storenameinvalid', 'cache');
            } else if (empty($this->_customdata['store'])) {
                $stores = cache_administration_helper::get_store_instance_summaries();
                if (array_key_exists($data['name'], $stores)) {
                    $errors['name'] = get_string('storenamealreadyused', 'cache');
                }
            }
        }

        if (method_exists($this, 'configuration_validation')) {
            $newerrors = $this->configuration_validation($data, $files, $errors);
            // We need to selectiviliy merge here
            foreach ($newerrors as $element => $error) {
                if (!array_key_exists($element, $errors)) {
                    $errors[$element] = $error;
                }
            }
        }

        return $errors;
    }
}

/**
 * Form to set definition mappings
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_definition_mappings_form extends moodleform {

    /**
     * The definition of the form
     */
    protected final function definition() {
        $definition = $this->_customdata['definition'];
        $form = $this->_form;

        list($component, $area) = explode('/', $definition, 2);
        list($currentstores, $storeoptions, $defaults) =
                cache_administration_helper::get_definition_store_options($component, $area);

        $form->addElement('hidden', 'definition', $definition);
        $form->addElement('hidden', 'action', 'editdefinitionmapping');

        $requiredoptions = max(3, count($currentstores)+1);
        $requiredoptions = min($requiredoptions, count($storeoptions));

        $options = array('' => get_string('none'));
        foreach ($storeoptions as $option => $def) {
            $options[$option] = $option;
            if ($def['default']) {
                $options[$option] .= ' '.get_string('mappingdefault', 'cache');
            }
        }

        for ($i = 0; $i < $requiredoptions; $i++) {
            $title = '...';
            if ($i === 0) {
                $title = get_string('mappingprimary', 'cache');
            } else if ($i === $requiredoptions-1) {
                $title = get_string('mappingfinal', 'cache');
            }
            $form->addElement('select', 'mappings['.$i.']', $title, $options);
        }
        $i = 0;
        foreach ($currentstores as $store => $def) {
            $form->setDefault('mappings['.$i.']', $store);
            $i++;
        }

        if (!empty($defaults)) {
            $form->addElement('static', 'defaults', get_string('defaultmappings', 'cache'),
                    html_writer::tag('strong', join(', ', $defaults)));
            $form->addHelpButton('defaults', 'defaultmappings', 'cache');
        }

        $this->add_action_buttons();
    }
}

/**
 * Form to set the mappings for a mode.
 *
 * @package    core
 * @category   cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_mode_mappings_form extends moodleform {
    /**
     * The definition of the form
     */
    protected function definition() {
        $form = $this->_form;
        $stores = $this->_customdata;

        $options = array(
            cache_store::MODE_APPLICATION => array(),
            cache_store::MODE_SESSION => array(),
            cache_store::MODE_REQUEST => array()
        );
        foreach ($stores as $storename => $store) {
            foreach ($store['modes'] as $mode => $enabled) {
                if ($enabled) {
                    if (empty($store['default'])) {
                        $options[$mode][$storename] = $store['name'];
                    } else {
                        $options[$mode][$storename] = get_string('store_'.$store['name'], 'cache');
                    }
                }
            }
        }

        $form->addElement('hidden', 'action', 'editmodemappings');
        foreach ($options as $mode => $optionset) {
            $form->addElement('select', 'mode_'.$mode, get_string('mode_'.$mode, 'cache'), $optionset);
        }

        $this->add_action_buttons();
    }
}
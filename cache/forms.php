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
            $form->addElement('static', 'lock-value', get_string('locking', 'cache'),
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
        $form->setType('definition', PARAM_SAFEPATH);
        $form->addElement('hidden', 'action', 'editdefinitionmapping');
        $form->setType('action', PARAM_ALPHA);

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
 * Form to set definition sharing option
 *
 * @package    core
 * @category   cache
 * @copyright  2013 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cache_definition_sharing_form extends moodleform {
    /**
     * The definition of the form
     */
    protected final function definition() {
        $definition = $this->_customdata['definition'];
        $sharingoptions = $this->_customdata['sharingoptions'];
        $form = $this->_form;

        $form->addElement('hidden', 'definition', $definition);
        $form->setType('definition', PARAM_SAFEPATH);
        $form->addElement('hidden', 'action', 'editdefinitionsharing');
        $form->setType('action', PARAM_ALPHA);

        // We use a group here for validation.
        $count = 0;
        $group = array();
        foreach ($sharingoptions as $value => $text) {
            $count++;
            $group[] = $form->createElement('checkbox', $value, null, $text);
        }
        $form->addGroup($group, 'sharing', get_string('sharing', 'cache'), '<br />');
        $form->setType('sharing', PARAM_INT);

        $form->addElement('text', 'userinputsharingkey', get_string('userinputsharingkey', 'cache'));
        $form->addHelpButton('userinputsharingkey', 'userinputsharingkey', 'cache');
        $form->disabledIf('userinputsharingkey', 'sharing['.cache_definition::SHARING_INPUT.']', 'notchecked');
        $form->setType('userinputsharingkey', PARAM_ALPHANUMEXT);

        $values = array_keys($sharingoptions);
        if (in_array(cache_definition::SHARING_ALL, $values)) {
            // If you share with all thenthe other options don't really make sense.
            foreach ($values as $value) {
                $form->disabledIf('sharing['.$value.']', 'sharing['.cache_definition::SHARING_ALL.']', 'checked');
            }
            $form->disabledIf('userinputsharingkey', 'sharing['.cache_definition::SHARING_ALL.']', 'checked');
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
            $data['sharing'] = cache_administration_helper::get_definition_sharing_options(cache_definition::SHARING_DEFAULT);
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
                if ($enabled && ($mode !== cache_store::MODE_SESSION || $store['supports']['searchable'])) {
                    if (empty($store['default'])) {
                        $options[$mode][$storename] = $store['name'];
                    } else {
                        $options[$mode][$storename] = get_string('store_'.$store['name'], 'cache');
                    }
                }
            }
        }

        $form->addElement('hidden', 'action', 'editmodemappings');
        $form->setType('action', PARAM_ALPHA);
        foreach ($options as $mode => $optionset) {
            $form->addElement('select', 'mode_'.$mode, get_string('mode_'.$mode, 'cache'), $optionset);
        }

        $this->add_action_buttons();
    }
}

/**
 * Form to add a cache lock instance.
 *
 * All cache lock plugins that wish to have custom configuration should override
 * this form, and more explicitly the plugin_definition and plugin_validation methods.
 *
 * @package    core
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
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
 * Form for adding a apcu instance.
 *
 * @copyright  2014 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_apcu_addinstance_form extends cachestore_addinstance_form {
    /**
     * Add the desired form elements.
     */
    protected function configuration_definition() {
        global $CFG;
        $form = $this->_form;
        $form->addElement('text', 'prefix', get_string('prefix', 'cachestore_apcu'),
            array('maxlength' => 5, 'size' => 5));
        $form->addHelpButton('prefix', 'prefix', 'cachestore_apcu');
        $form->setType('prefix', PARAM_TEXT); // We set to text but we have a rule to limit to alphanumext.
        $form->setDefault('prefix', $CFG->prefix);
        $form->addRule('prefix', get_string('prefixinvalid', 'cachestore_apcu'), 'regex', '#^[a-zA-Z0-9\-_]+$#');
        $form->addElement('header', 'apc_notice', get_string('notice', 'cachestore_apcu'));
        $form->setExpanded('apc_notice');
        $link = get_docs_url('Caching#APC');
        $form->addElement('html', nl2br(get_string('clusternotice', 'cachestore_apcu', $link)));
    }

    /**
     * Validates the configuration data.
     *
     * We need to check that prefix is unique.
     *
     * @param array $data
     * @param array $files
     * @param array $errors
     * @return array
     * @throws coding_exception
     */
    public function configuration_validation($data, $files, array $errors) {
        if (empty($errors['prefix'])) {
            $factory = cache_factory::instance();
            $config = $factory->create_config_instance();
            foreach ($config->get_all_stores() as $store) {
                if ($store['plugin'] === 'apcu') {
                    if (isset($store['configuration']['prefix'])) {
                        if ($data['prefix'] === $store['configuration']['prefix']) {
                            // The new store has the same prefix as an existing store, thats a problem.
                            $errors['prefix'] = get_string('prefixnotunique', 'cachestore_apcu');
                            break;
                        }
                    } else if (empty($data['prefix'])) {
                        // The existing store hasn't got a prefix and neither does the new store, that's a problem.
                        $errors['prefix'] = get_string('prefixnotunique', 'cachestore_apcu');
                        break;
                    }
                }
            }
        }
        return $errors;
    }
}

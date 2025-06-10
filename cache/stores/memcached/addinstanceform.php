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
 * The library file for the memcached cache store.
 *
 * This file is part of the memcached cache store, it contains the API for interacting with an instance of the store.
 *
 * @package    cachestore_memcached
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/cache/forms.php');
require_once($CFG->dirroot.'/cache/stores/memcached/lib.php');

/**
 * Form for adding a memcached instance.
 *
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_memcached_addinstance_form extends cachestore_addinstance_form {

    /**
     * Adds the desired form elements.
     */
    protected function configuration_definition() {
        global $OUTPUT;

        $form = $this->_form;
        $version = phpversion('memcached');
        $hasrequiredversion = ($version || version_compare($version, cachestore_memcached::REQUIRED_VERSION, '>='));

        if (!$hasrequiredversion) {
            $notify = new \core\output\notification(nl2br(get_string('upgrade200recommended', 'cachestore_memcached')),
                \core\output\notification::NOTIFY_WARNING);
            $form->addElement('html', $OUTPUT->render($notify));
        }

        $form->addElement('textarea', 'servers', get_string('servers', 'cachestore_memcached'), array('cols' => 75, 'rows' => 5));
        $form->addHelpButton('servers', 'servers', 'cachestore_memcached');
        $form->addRule('servers', get_string('required'), 'required');
        $form->setType('servers', PARAM_RAW);

        $form->addElement('selectyesno', 'compression', get_string('usecompression', 'cachestore_memcached'));
        $form->addHelpButton('compression', 'usecompression', 'cachestore_memcached');
        $form->setDefault('compression', 1);
        $form->setType('compression', PARAM_BOOL);

        $serialiseroptions = cachestore_memcached::config_get_serialiser_options();
        $form->addElement('select', 'serialiser', get_string('useserialiser', 'cachestore_memcached'), $serialiseroptions);
        $form->addHelpButton('serialiser', 'useserialiser', 'cachestore_memcached');
        $form->setDefault('serialiser', Memcached::SERIALIZER_PHP);
        $form->setType('serialiser', PARAM_INT);

        $form->addElement('text', 'prefix', get_string('prefix', 'cachestore_memcached'), array('size' => 16));
        $form->setType('prefix', PARAM_TEXT); // We set to text but we have a rule to limit to alphanumext.
        $form->addHelpButton('prefix', 'prefix', 'cachestore_memcached');
        $form->addRule('prefix', get_string('prefixinvalid', 'cachestore_memcached'), 'regex', '#^[a-zA-Z0-9\-_]+$#');
        $form->setForceLtr('prefix');

        $hashoptions = cachestore_memcached::config_get_hash_options();
        $form->addElement('select', 'hash', get_string('hash', 'cachestore_memcached'), $hashoptions);
        $form->addHelpButton('hash', 'hash', 'cachestore_memcached');
        $form->setDefault('serialiser', Memcached::HASH_DEFAULT);
        $form->setType('serialiser', PARAM_INT);

        $form->addElement('selectyesno', 'bufferwrites', get_string('bufferwrites', 'cachestore_memcached'));
        $form->addHelpButton('bufferwrites', 'bufferwrites', 'cachestore_memcached');
        $form->setDefault('bufferwrites', 0);
        $form->setType('bufferwrites', PARAM_BOOL);

        if ($hasrequiredversion) {
            // Only show this option if we have the required version of memcache extension installed.
            // If it's not installed then this option does nothing, so there is no point in displaying it.
            $form->addElement('selectyesno', 'isshared', get_string('isshared', 'cachestore_memcached'));
            $form->addHelpButton('isshared', 'isshared', 'cachestore_memcached');
            $form->setDefault('isshared', 0);
            $form->setType('isshared', PARAM_BOOL);
        }

        $form->addElement('header', 'clusteredheader', get_string('clustered', 'cachestore_memcached'));

        $form->addElement('checkbox', 'clustered', get_string('clustered', 'cachestore_memcached'));
        $form->setDefault('checkbox', false);
        $form->addHelpButton('clustered', 'clustered', 'cachestore_memcached');

        $form->addElement('textarea', 'setservers', get_string('setservers', 'cachestore_memcached'),
                array('cols' => 75, 'rows' => 5));
        $form->addHelpButton('setservers', 'setservers', 'cachestore_memcached');
        $form->disabledIf('setservers', 'clustered');
        $form->setType('setservers', PARAM_RAW);
    }

    /**
     * Perform minimal validation on the settings form.
     *
     * @param array $data
     * @param array $files
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if (isset($data['clustered']) && ($data['clustered'] == 1)) {
            // Set servers is required with in cluster mode.
            if (!isset($data['setservers'])) {
                $errors['setservers'] = get_string('required');
            } else {
                $trimmed = trim($data['setservers']);
                if (empty($trimmed)) {
                    $errors['setservers'] = get_string('required');
                }
            }

            $validservers = false;
            if (isset($data['servers'])) {
                $servers = trim($data['servers']);
                $servers = explode("\n", $servers);
                if (count($servers) === 1) {
                    $validservers = true;
                }
            }

            if (!$validservers) {
                $errors['servers'] = get_string('serversclusterinvalid', 'cachestore_memcached');
            }
        }

        return $errors;
    }
}

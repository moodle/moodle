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
        $form = $this->_form;

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

        $hashoptions = cachestore_memcached::config_get_hash_options();
        $form->addElement('select', 'hash', get_string('hash', 'cachestore_memcached'), $hashoptions);
        $form->addHelpButton('hash', 'hash', 'cachestore_memcached');
        $form->setDefault('serialiser', Memcached::HASH_DEFAULT);
        $form->setType('serialiser', PARAM_INT);

        $form->addElement('selectyesno', 'bufferwrites', get_string('bufferwrites', 'cachestore_memcached'));
        $form->addHelpButton('bufferwrites', 'bufferwrites', 'cachestore_memcached');
        $form->setDefault('bufferwrites', 0);
        $form->setType('bufferwrites', PARAM_BOOL);
    }
}

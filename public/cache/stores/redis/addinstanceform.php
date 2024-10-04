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
 * Form for adding instance of Redis Cache Store.
 *
 * @copyright   2013 Adam Durana
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_redis_addinstance_form extends cachestore_addinstance_form {
    /**
     * Builds the form for creating an instance.
     */
    protected function configuration_definition() {
        $form = $this->_form;

        $form->addElement('advcheckbox', 'clustermode', get_string('clustermode', 'cachestore_redis'), '',
            cache_helper::is_cluster_available() ? '' : 'disabled');
        $form->addHelpButton('clustermode', 'clustermode', 'cachestore_redis');
        $form->setType('clustermode', PARAM_BOOL);

        $form->addElement('textarea', 'server', get_string('server', 'cachestore_redis'), ['cols' => 6, 'rows' => 10]);
        $form->setType('server', PARAM_TEXT);
        $form->addHelpButton('server', 'server', 'cachestore_redis');
        $form->addRule('server', get_string('required'), 'required');

        $form->addElement('advcheckbox', 'encryption', get_string('encrypt_connection', 'cachestore_redis'));
        $form->setType('encryption', PARAM_BOOL);
        $form->addHelpButton('encryption', 'encrypt_connection', 'cachestore_redis');

        $form->addElement('text', 'cafile', get_string('ca_file', 'cachestore_redis'));
        $form->setType('cafile', PARAM_TEXT);
        $form->addHelpButton('cafile', 'ca_file', 'cachestore_redis');

        $form->addElement('passwordunmask', 'password', get_string('password', 'cachestore_redis'));
        $form->setType('password', PARAM_RAW);
        $form->addHelpButton('password', 'password', 'cachestore_redis');

        $form->addElement('text', 'prefix', get_string('prefix', 'cachestore_redis'), array('size' => 16));
        $form->setType('prefix', PARAM_TEXT); // We set to text but we have a rule to limit to alphanumext.
        $form->addHelpButton('prefix', 'prefix', 'cachestore_redis');
        $form->addRule('prefix', get_string('prefixinvalid', 'cachestore_redis'), 'regex', '#^[a-zA-Z0-9\-_]+$#');

        $serializeroptions = cachestore_redis::config_get_serializer_options();
        $form->addElement('select', 'serializer', get_string('useserializer', 'cachestore_redis'), $serializeroptions);
        $form->addHelpButton('serializer', 'useserializer', 'cachestore_redis');
        $form->setDefault('serializer', Redis::SERIALIZER_PHP);
        $form->setType('serializer', PARAM_INT);

        $compressoroptions = cachestore_redis::config_get_compressor_options();
        $form->addElement('select', 'compressor', get_string('usecompressor', 'cachestore_redis'), $compressoroptions);
        $form->addHelpButton('compressor', 'usecompressor', 'cachestore_redis');
        $form->setDefault('compressor', cachestore_redis::COMPRESSOR_NONE);
        $form->setType('compressor', PARAM_INT);

        $form->addElement('text', 'connectiontimeout', get_string('connectiontimeout', 'cachestore_redis'));
        $form->addHelpButton('connectiontimeout', 'connectiontimeout', 'cachestore_redis');
        $form->setDefault('connectiontimeout', cachestore_redis::CONNECTION_TIMEOUT);
        $form->setType('connectiontimeout', PARAM_INT);
    }
}

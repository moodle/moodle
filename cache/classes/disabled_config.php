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

namespace core_cache;

use core\exception\coding_exception;
use core_cache\exception\cache_exception;
use cachestore_static;
use cachestore_session;
use cachestore_file;

/**
 * The cache config class used when the Cache has been disabled.
 *
 * @package core_cache
 * @copyright  2012 Sam Hemelryk
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class disabled_config extends config_writer {
    /**
     * Returns an instance of the configuration writer.
     *
     * @return disabled_config
     */
    public static function instance() {
        $factory = factory::instance();
        return $factory->create_config_instance(true);
    }

    /**
     * Saves the current configuration.
     */
    protected function config_save() {
        // Nothing to do here.
    }

    /**
     * Generates a configuration array suitable to be written to the config file.
     *
     * @return array
     */
    protected function generate_configuration_array() {
        $configuration = [];
        $configuration['stores'] = $this->configstores;
        $configuration['modemappings'] = $this->configmodemappings;
        $configuration['definitions'] = $this->configdefinitions;
        $configuration['definitionmappings'] = $this->configdefinitionmappings;
        $configuration['locks'] = $this->configlocks;
        return $configuration;
    }

    /**
     * Adds a plugin instance.
     *
     * @param string $name Unused.
     * @param string $plugin Unused.
     * @param array $configuration Unused.
     * @return bool
     * @throws cache_exception
     */
    public function add_store_instance($name, $plugin, array $configuration = []) {
        return false;
    }

    /**
     * Sets the mode mappings.
     *
     * @param array $modemappings Unused.
     * @return bool
     * @throws cache_exception
     */
    public function set_mode_mappings(array $modemappings) {
        return false;
    }

    /**
     * Edits a give plugin instance.
     *
     * @param string $name Unused.
     * @param string $plugin Unused.
     * @param array $configuration Unused.
     * @return bool
     * @throws cache_exception
     */
    public function edit_store_instance($name, $plugin, $configuration) {
        return false;
    }

    /**
     * Deletes a store instance.
     *
     * @param string $name Unused.
     * @return bool
     * @throws cache_exception
     */
    public function delete_store_instance($name) {
        return false;
    }

    /**
     * Creates the default configuration and saves it.
     *
     * @param bool $forcesave Ignored because we are disabled!
     * @return array
     */
    public static function create_default_configuration($forcesave = false) {
        global $CFG;

        // HACK ALERT.
        // We probably need to come up with a better way to create the default stores, or at least ensure 100% that the
        // default store plugins are protected from deletion.
        require_once($CFG->dirroot . '/cache/stores/file/lib.php');
        require_once($CFG->dirroot . '/cache/stores/session/lib.php');
        require_once($CFG->dirroot . '/cache/stores/static/lib.php');

        $writer = new self();
        $writer->configstores = [
            'default_application' => [
                'name' => 'default_application',
                'plugin' => 'file',
                'configuration' => [],
                'features' => cachestore_file::get_supported_features(),
                'modes' => store::MODE_APPLICATION,
                'default' => true,
            ],
            'default_session' => [
                'name' => 'default_session',
                'plugin' => 'session',
                'configuration' => [],
                'features' => cachestore_session::get_supported_features(),
                'modes' => store::MODE_SESSION,
                'default' => true,
            ],
            'default_request' => [
                'name' => 'default_request',
                'plugin' => 'static',
                'configuration' => [],
                'features' => cachestore_static::get_supported_features(),
                'modes' => store::MODE_REQUEST,
                'default' => true,
            ],
        ];
        $writer->configdefinitions = [];
        $writer->configmodemappings = [
            [
                'mode' => store::MODE_APPLICATION,
                'store' => 'default_application',
                'sort' => -1,
            ],
            [
                'mode' => store::MODE_SESSION,
                'store' => 'default_session',
                'sort' => -1,
            ],
            [
                'mode' => store::MODE_REQUEST,
                'store' => 'default_request',
                'sort' => -1,
            ],
        ];
        $writer->configlocks = [
            'default_file_lock' => [
                'name' => 'cachelock_file_default',
                'type' => 'cachelock_file',
                'dir' => 'filelocks',
                'default' => true,
            ],
        ];

        return $writer->generate_configuration_array();
    }

    /**
     * Updates the definition in the configuration from those found in the cache files.
     *
     * @param bool $coreonly Unused.
     */
    public static function update_definitions($coreonly = false) {
        // Nothing to do here.
    }

    /**
     * Locates all of the definition files.
     *
     * @param bool $coreonly Unused.
     * @return array
     */
    protected static function locate_definitions($coreonly = false) {
        return [];
    }

    /**
     * Sets the mappings for a given definition.
     *
     * @param string $definition Unused.
     * @param array $mappings Unused.
     * @throws coding_exception
     */
    public function set_definition_mappings($definition, $mappings) {
        // Nothing to do here.
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(disabled_config::class, \cache_config_disabled::class);

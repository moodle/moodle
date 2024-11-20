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

use core_component;
use core\output\pix_icon;
use lang_string;

/**
 * Administration helper base class.
 *
 * Defines abstract methods for a subclass to define the admin page.
 *
 * @package     core_cache
 * @category    cache
 * @author      Peter Burnett <peterburnett@catalyst-au.net>
 * @copyright   2020 Catalyst IT
 * @copyright  2012 Sam Hemelryk
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class administration_helper extends helper {
    /**
     * Returns an array containing all of the information about stores a renderer needs.
     * @return array
     */
    public static function get_store_instance_summaries(): array {
        $return = [];
        $default = [];
        $instance = config::instance();
        $stores = $instance->get_all_stores();
        $locks = $instance->get_locks();
        foreach ($stores as $name => $details) {
            $class = $details['class'];
            $store = false;
            if ($class::are_requirements_met()) {
                $store = new $class($details['name'], $details['configuration']);
            }
            $lock = (isset($details['lock'])) ? $locks[$details['lock']] : $instance->get_default_lock();
            $record = [
                'name' => $name,
                'plugin' => $details['plugin'],
                'default' => $details['default'],
                'isready' => $store ? $store->is_ready() : false,
                'requirementsmet' => $class::are_requirements_met(),
                'mappings' => 0,
                'lock' => $lock,
                'modes' => [
                    store::MODE_APPLICATION =>
                        ($class::get_supported_modes($return) & store::MODE_APPLICATION) == store::MODE_APPLICATION,
                    store::MODE_SESSION =>
                        ($class::get_supported_modes($return) & store::MODE_SESSION) == store::MODE_SESSION,
                    store::MODE_REQUEST =>
                        ($class::get_supported_modes($return) & store::MODE_REQUEST) == store::MODE_REQUEST,
                ],
                'supports' => [
                    'multipleidentifiers' => $store ? $store->supports_multiple_identifiers() : false,
                    'dataguarantee' => $store ? $store->supports_data_guarantee() : false,
                    'nativettl' => $store ? $store->supports_native_ttl() : false,
                    'nativelocking' => ($store instanceof lockable_cache_interface),
                    'keyawareness' => ($store instanceof key_aware_cache_interface),
                    'searchable' => ($store instanceof searchable_cache_interface),
                ],
                'warnings' => $store ? $store->get_warnings() : [],
            ];
            if (empty($details['default'])) {
                $return[$name] = $record;
            } else {
                $default[$name] = $record;
            }
        }

        ksort($return);
        ksort($default);
        $return = $return + $default;

        $mappings = $instance->get_definition_mappings();
        foreach ($mappings as $mapping) {
            if (!array_key_exists($mapping['store'], $return)) {
                continue;
            }
            $return[$mapping['store']]['mappings']++;
        }

        // Now get all definitions, and if not mapped, increment the defaults for the mode.
        $modemappings = $instance->get_mode_mappings();
        foreach ($instance->get_definitions() as $definition) {
            // Construct the definition name to search for.
            $defname = $definition['component'] . '/' . $definition['area'];
            // Skip if definition is already mapped.
            if (array_search($defname, array_column($mappings, 'definition')) !== false) {
                continue;
            }

            $mode = $definition['mode'];
            // Get the store name of the default mapping from the mode.
            $index = array_search($mode, array_column($modemappings, 'mode'));
            $store = $modemappings[$index]['store'];
            $return[$store]['mappings']++;
        }

        return $return;
    }

    /**
     * Returns an array of information about plugins, everything a renderer needs.
     *
     * @return array for each store, an array containing various information about each store.
     *     See the code below for details
     */
    public static function get_store_plugin_summaries(): array {
        $return = [];
        $plugins = \core_component::get_plugin_list_with_file('cachestore', 'lib.php', true);
        foreach ($plugins as $plugin => $path) {
            $class = 'cachestore_' . $plugin;
            $return[$plugin] = [
                'name' => get_string('pluginname', 'cachestore_' . $plugin),
                'requirementsmet' => $class::are_requirements_met(),
                'instances' => 0,
                'modes' => [
                    store::MODE_APPLICATION => ($class::get_supported_modes() & store::MODE_APPLICATION),
                    store::MODE_SESSION => ($class::get_supported_modes() & store::MODE_SESSION),
                    store::MODE_REQUEST => ($class::get_supported_modes() & store::MODE_REQUEST),
                ],
                'supports' => [
                    'multipleidentifiers' => ($class::get_supported_features() & store::SUPPORTS_MULTIPLE_IDENTIFIERS),
                    'dataguarantee' => ($class::get_supported_features() & store::SUPPORTS_DATA_GUARANTEE),
                    'nativettl' => ($class::get_supported_features() & store::SUPPORTS_NATIVE_TTL),
                    'nativelocking' => (in_array(lockable_cache_interface::class, class_implements($class))),
                    'keyawareness' => (array_key_exists(key_aware_cache_interface::class, class_implements($class))),
                ],
                'canaddinstance' => ($class::can_add_instance() && $class::are_requirements_met()),
            ];
        }

        $instance = config::instance();
        $stores = $instance->get_all_stores();
        foreach ($stores as $store) {
            $plugin = $store['plugin'];
            if (array_key_exists($plugin, $return)) {
                $return[$plugin]['instances']++;
            }
        }

        return $return;
    }

    /**
     * Returns an array about the definitions. All the information a renderer needs.
     *
     * @return array for each store, an array containing various information about each store.
     *     See the code below for details
     */
    public static function get_definition_summaries(): array {
        $factory = factory::instance();
        $config = $factory->create_config_instance();
        $storenames = [];
        foreach ($config->get_all_stores() as $key => $store) {
            if (!empty($store['default'])) {
                $storenames[$key] = new lang_string('store_' . $key, 'cache');
            } else {
                $storenames[$store['name']] = $store['name'];
            }
        }
        /* @var definition[] $definitions */
        $definitions = [];
        $return = [];
        foreach ($config->get_definitions() as $key => $definition) {
            $definitions[$key] = definition::load($definition['component'] . '/' . $definition['area'], $definition);
        }
        foreach ($definitions as $id => $definition) {
            $mappings = [];
            foreach (helper::get_stores_suitable_for_definition($definition) as $store) {
                $mappings[] = $storenames[$store->my_name()];
            }
            $return[$id] = [
                'id' => $id,
                'name' => $definition->get_name(),
                'mode' => $definition->get_mode(),
                'component' => $definition->get_component(),
                'area' => $definition->get_area(),
                'mappings' => $mappings,
                'canuselocalstore' => $definition->can_use_localstore(),
                'sharingoptions' => self::get_definition_sharing_options($definition->get_sharing_options(), false),
                'selectedsharingoption' => self::get_definition_sharing_options($definition->get_selected_sharing_option(), true),
                'userinputsharingkey' => $definition->get_user_input_sharing_key(),
            ];
        }
        return $return;
    }

    /**
     * Get the default stores for all modes.
     *
     * @return array An array containing sub-arrays, one for each mode.
     */
    public static function get_default_mode_stores(): array {
        global $OUTPUT;
        $instance = config::instance();
        $adequatestores = helper::get_stores_suitable_for_mode_default();
        $icon = new pix_icon('i/warning', new lang_string('inadequatestoreformapping', 'cache'));
        $storenames = [];
        foreach ($instance->get_all_stores() as $key => $store) {
            if (!empty($store['default'])) {
                $storenames[$key] = new lang_string('store_' . $key, 'cache');
            }
        }
        $modemappings = [
            store::MODE_APPLICATION => [],
            store::MODE_SESSION => [],
            store::MODE_REQUEST => [],
        ];
        foreach ($instance->get_mode_mappings() as $mapping) {
            $mode = $mapping['mode'];
            if (!array_key_exists($mode, $modemappings)) {
                debugging('Unknown mode in cache store mode mappings', DEBUG_DEVELOPER);
                continue;
            }
            if (array_key_exists($mapping['store'], $storenames)) {
                $modemappings[$mode][$mapping['store']] = $storenames[$mapping['store']];
            } else {
                $modemappings[$mode][$mapping['store']] = $mapping['store'];
            }
            if (!array_key_exists($mapping['store'], $adequatestores)) {
                $modemappings[$mode][$mapping['store']] = $modemappings[$mode][$mapping['store']] . ' ' . $OUTPUT->render($icon);
            }
        }
        return $modemappings;
    }

    /**
     * Returns an array summarising the locks available in the system.
     *
     * @return array array of lock summaries.
     */
    public static function get_lock_summaries(): array {
        $locks = [];
        $instance = config::instance();
        $stores = $instance->get_all_stores();
        foreach ($instance->get_locks() as $lock) {
            $default = !empty($lock['default']);
            if ($default) {
                $name = new lang_string($lock['name'], 'cache');
            } else {
                $name = $lock['name'];
            }
            $uses = 0;
            foreach ($stores as $store) {
                if (!empty($store['lock']) && $store['lock'] === $lock['name']) {
                    $uses++;
                }
            }
            $lockdata = [
                'name' => $name,
                'default' => $default,
                'uses' => $uses,
                'type' => get_string('pluginname', $lock['type']),
            ];
            $locks[$lock['name']] = $lockdata;
        }
        return $locks;
    }

    /**
     * Given a sharing option hash this function returns an array of strings that can be used to describe it.
     *
     * @param int $sharingoption The sharing option hash to get strings for.
     * @param bool $isselectedoptions Set to true if the strings will be used to view the selected options.
     * @return array An array of lang_string's.
     */
    public static function get_definition_sharing_options(int $sharingoption, bool $isselectedoptions = true): array {
        $options = [];
        $prefix = ($isselectedoptions) ? 'sharingselected' : 'sharing';
        if ($sharingoption & definition::SHARING_ALL) {
            $options[definition::SHARING_ALL] = new lang_string($prefix . '_all', 'cache');
        }
        if ($sharingoption & definition::SHARING_SITEID) {
            $options[definition::SHARING_SITEID] = new lang_string($prefix . '_siteid', 'cache');
        }
        if ($sharingoption & definition::SHARING_VERSION) {
            $options[definition::SHARING_VERSION] = new lang_string($prefix . '_version', 'cache');
        }
        if ($sharingoption & definition::SHARING_INPUT) {
            $options[definition::SHARING_INPUT] = new lang_string($prefix . '_input', 'cache');
        }
        return $options;
    }

    /**
     * Get an array of stores that are suitable to be used for a given definition.
     *
     * @param string $component
     * @param string $area
     * @return array Array containing 3 elements
     *      1. An array of currently used stores
     *      2. An array of suitable stores
     *      3. An array of default stores
     */
    public static function get_definition_store_options(string $component, string $area): array {
        $factory = factory::instance();
        $definition = $factory->create_definition($component, $area);
        $config = config::instance();
        $currentstores = $config->get_stores_for_definition($definition);
        $possiblestores = $config->get_stores($definition->get_mode(), $definition->get_requirements_bin());

        $defaults = [];
        foreach ($currentstores as $key => $store) {
            if (!empty($store['default'])) {
                $defaults[] = $key;
                unset($currentstores[$key]);
            }
        }
        foreach ($possiblestores as $key => $store) {
            if ($store['default']) {
                unset($possiblestores[$key]);
                $possiblestores[$key] = $store;
            }
        }
        return [$currentstores, $possiblestores, $defaults];
    }

    /**
     * This function must be implemented to display options for store plugins.
     *
     * @param string $name the name of the store plugin.
     * @param array $plugindetails array of store plugin details.
     * @return array array of actions.
     */
    public function get_store_plugin_actions(string $name, array $plugindetails): array {
        return [];
    }

    /**
     * This function must be implemented to display options for store instances.
     *
     * @param string $name the store instance name.
     * @param array $storedetails array of store instance details.
     * @return array array of actions.
     */
    public function get_store_instance_actions(string $name, array $storedetails): array {
        return [];
    }

    /**
     * This function must be implemented to display options for definition mappings.
     *
     * @param context $context the context for the definition.
     * @param array $definitionsummary the definition summary.
     * @return array array of actions.
     */
    public function get_definition_actions(\context $context, array $definitionsummary): array {
        return [];
    }

    /**
     * This function must be implemented to get addable locks.
     *
     * @return array array of locks that are addable.
     */
    public function get_addable_lock_options(): array {
        return [];
    }

    /**
     * This function must be implemented to perform any page actions by a child class.
     *
     * @param string $action the action to perform.
     * @param array $forminfo empty array to be set by actions.
     * @return array array of form info.
     */
    abstract public function perform_cache_actions(string $action, array $forminfo): array;

    /**
     * This function must be implemented to display the cache admin page.
     *
     * @param \core_cache\output\renderer $renderer the renderer used to generate the page.
     * @return string the HTML for the page.
     */
    abstract public function generate_admin_page(\core_cache\output\renderer $renderer): string;

    /**
     * Gets usage information about the whole cache system.
     *
     * This is a slow function and should only be used on an admin information page.
     *
     * The returned array lists all cache definitions with fields 'cacheid' and 'stores'. For
     * each store, the following fields are available:
     *
     * - name (store name)
     * - class (e.g. cachestore_redis)
     * - supported (true if we have any information)
     * - items (number of items stored)
     * - mean (mean size of item)
     * - sd (standard deviation for item sizes)
     * - margin (margin of error for mean at 95% confidence)
     * - storetotal (total usage for store if known, otherwise null)
     *
     * The storetotal field will be the same for every cache that uses the same store.
     *
     * @param int $samplekeys Number of keys to sample when checking size of large caches
     * @return array Details of cache usage
     */
    abstract public function get_usage(int $samplekeys): array;
}

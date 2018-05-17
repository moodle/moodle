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
 * Class containing helper methods for processing data requests.
 *
 * @package    tool_dataprivacy
 * @copyright  2018 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_dataprivacy;

defined('MOODLE_INTERNAL') || die();

/**
 * Class containing helper methods for processing data requests.
 *
 * @copyright  2018 Adrian Greeve
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class metadata_registry {

    /**
     * Returns plugin types / plugins and the user data that it stores in a format that can be sent to a template.
     *
     * @return array An array with all of the plugin types / plugins and the user data they store.
     */
    public function get_registry_metadata() {
        $manager = new \core_privacy\manager();
        $manager->set_observer(new \tool_dataprivacy\manager_observer());

        $pluginman = \core_plugin_manager::instance();
        $contributedplugins = $this->get_contrib_list();
        $metadata = $manager->get_metadata_for_components();
        $fullyrichtree = $this->get_full_component_list();
        foreach ($fullyrichtree as $branch => $leaves) {
            $plugintype = $leaves['plugin_type'];
            $plugins = array_map(function($component) use ($manager, $metadata, $contributedplugins, $plugintype, $pluginman) {
                // Use the plugin name for the plugins, ignore for core subsystems.
                $internaldata = ($plugintype == 'core') ? ['component' => $component] :
                        ['component' => $pluginman->plugin_name($component)];
                $internaldata['raw_component'] = $component;
                if ($manager->component_is_compliant($component)) {
                    $internaldata['compliant'] = true;
                    if (isset($metadata[$component])) {
                        $collection = $metadata[$component]->get_collection();
                        $internaldata = $this->format_metadata($collection, $component, $internaldata);
                    } else if ($manager->is_empty_subsystem($component)) {
                        // This is an unused subsystem.
                        // Use the generic string.
                        $internaldata['nullprovider'] = get_string('privacy:subsystem:empty', 'core_privacy');
                    } else {
                        // Call get_reason for null provider.
                        $internaldata['nullprovider'] = get_string($manager->get_null_provider_reason($component), $component);
                    }
                } else {
                    $internaldata['compliant'] = false;
                }
                // Check to see if we are an external plugin.
                $componentshortname = explode('_', $component);
                $shortname = array_pop($componentshortname);
                if (isset($contributedplugins[$plugintype][$shortname])) {
                    $internaldata['external'] = true;
                }
                return $internaldata;
            }, $leaves['plugins']);
            $fullyrichtree[$branch]['plugin_type_raw'] = $plugintype;
            // We're done using the plugin type. Convert it to a readable string.
            $fullyrichtree[$branch]['plugin_type'] = $pluginman->plugintype_name($plugintype);
            $fullyrichtree[$branch]['plugins'] = $plugins;
        }
        return $fullyrichtree;
    }

    /**
     * Formats the metadata for use with a template.
     *
     * @param  array $collection The collection associated with the component that we want to expand and format.
     * @param  string $component The component that we are dealing in
     * @param  array $internaldata The array to add the formatted metadata to.
     * @return array The internal data array with the formatted metadata.
     */
    protected function format_metadata($collection, $component, $internaldata) {
        foreach ($collection as $collectioninfo) {
            $privacyfields = $collectioninfo->get_privacy_fields();
            $fields = '';
            if (!empty($privacyfields)) {
                $fields = array_map(function($key, $field) use ($component) {
                    return [
                        'field_name' => $key,
                        'field_summary' => get_string($field, $component)
                    ];
                }, array_keys($privacyfields), $privacyfields);
            }
            // Can the metadata types be located somewhere else besides core?
            $items = explode('\\', get_class($collectioninfo));
            $type = array_pop($items);
            $typedata = [
                'name' => $collectioninfo->get_name(),
                'type' => $type,
                'fields' => $fields,
                'summary' => get_string($collectioninfo->get_summary(), $component)
            ];
            if (strpos($type, 'subsystem_link') === 0 || strpos($type, 'plugintype_link') === 0) {
                $typedata['link'] = true;
            }
            $internaldata['metadata'][] = $typedata;
        }
        return $internaldata;
    }

    /**
     * Return the full list of components.
     *
     * @return array An array of plugin types which contain plugin data.
     */
    protected function get_full_component_list() {
        global $CFG;

        $list = \core_component::get_component_list();
        $list['core']['core'] = "{$CFG->dirroot}/lib";
        $formattedlist = [];
        foreach ($list as $plugintype => $plugin) {
            $formattedlist[] = ['plugin_type' => $plugintype, 'plugins' => array_keys($plugin)];
        }

        return $formattedlist;
    }

    /**
     * Returns a list of contributed plugins installed on the system.
     *
     * @return array A list of contributed plugins installed.
     */
    protected function get_contrib_list() {
        return array_map(function($plugins) {
            return array_filter($plugins, function($plugindata) {
                return !$plugindata->is_standard();
            });
        }, \core_plugin_manager::instance()->get_plugins());
    }
}

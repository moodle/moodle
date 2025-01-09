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

namespace core_message\external;

use context_system;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_value;

/**
 * Webservice to enable or disable the default notification.
 *
 * @package    core_message
 * @copyright  2024 Raquel Ortega <raquel.ortega@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class message_set_default_notification extends external_api {
    /**
     * Returns description of method parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'preference' => new external_value(
                PARAM_TEXT,
                'The name of the preference',
                VALUE_REQUIRED,
            ),
            'state' => new external_value(
                PARAM_INT,
                'The target state',
                VALUE_REQUIRED,
            ),
        ]);
    }
    /**
     * Set the default notification action state.
     *
     * @param string $preference The name of the preference.
     * @param int $state The target state.
     * @return array
     */
    public static function execute(
        string $preference,
        int $state,
    ): array {

        global $DB;
        // Parameter validation.
        [
            'preference' => $preference,
            'state' => $state,
        ] = self::validate_parameters(self::execute_parameters(), [
            'preference' => $preference,
            'state' => $state,
        ]);

        $context = context_system::instance();
        self::validate_context($context);
        require_capability('moodle/site:config', $context);

        // Fetch all message processors and filter out disabled ones.
        $allprocessors = get_message_processors();
        $processors = array_filter($allprocessors, function($processor) {
            return $processor->enabled;
        });
        $preferences = get_message_output_default_preferences();

        // Get the provider name and provider component from the given preference.
        $providers = get_message_providers();
        $providername = '';
        $providercomponent = '';
        foreach ($providers as $provider) {
            if (str_contains($preference, $provider->component . '_' . $provider->name)) {
                $providername = $provider->name;
                $providercomponent = $provider->component;
                break;
            }
        }
        // Initialize variables to store new preferences.
        $preferencename = '';
        $value = 0;
        $successmessage = '';
        if (!empty($providername) && !empty($providercomponent)) {
            $componentproviderbase = $providercomponent.'_'.$providername;
            $providerdisplayname = get_string('messageprovider:'.$providername, $providercomponent);

            // Enabled / disabled toggle for providers.
            if ($preference === $componentproviderbase . '_disable') {
                // If $state is true, set the preference to disabled; otherwise, enable it.
                $preferencename = $preference;
                $value = $state ? 0 : 1;
                $successmessage = get_string('successproviderupdate', 'message', $providerdisplayname);

            } else {
                // Prepare data to use it for the component settings.
                $currentprocessorname = '';
                $componentprovidersetting = '';
                foreach ($processors as $processor) {
                    if (str_contains($preference, '[' . $processor->name . ']')) {
                        $currentprocessorname = $processor->name;
                        $componentprovidersetting = str_replace('[' . $processor->name . ']', '', $preference);
                        $labelparams = [
                            'provider'  => $providerdisplayname,
                            'processor' => get_string('pluginname', 'message_' . $processor->name),
                        ];
                        break;
                    }
                }

                // Locked toggle.
                if ($preference === $componentproviderbase.'_locked' . '[' . $currentprocessorname . ']') {
                    $preferencename = $currentprocessorname.'_provider_'.$componentproviderbase . '_locked';
                    $value = $state ? 1 : 0;
                    if ($value === 1) {
                        $successmessage = get_string('successproviderlocked', 'message', $labelparams);
                    } else {
                        $successmessage = get_string('successproviderunlocked', 'message', $labelparams);
                    }
                }
                // Enabled toggle.
                if ($preference === $componentproviderbase.'_enabled' . '[' . $currentprocessorname . ']') {
                    // Fetch the default message output preferences.
                    $preferencename = 'message_provider_'.$componentprovidersetting;
                    $successmessage = get_string('successproviderenabled', 'message', $labelparams);
                    $newsettings = [];
                    if (!empty($state)) {
                        $newsettings[] = $currentprocessorname;
                    }
                    // Check if the property exists within the preferences to maintain existing values.
                    if (property_exists($preferences, $preferencename)) {
                        $newsettings = array_merge($newsettings, explode(',', $preferences->$preferencename));

                        if (in_array($currentprocessorname, $newsettings) && empty($state)) {
                            if (($key = array_search($currentprocessorname, $newsettings)) !== false) {
                                unset($newsettings[$key]);
                            }
                        }
                    }
                    $value = join(',', $newsettings);
                    if (empty($value)) {
                        $value = null;
                    }
                }
            }
        }
        // Update preferences.
        if (!empty($preferencename)) {
            $transaction = $DB->start_delegated_transaction();
            $old = isset($preferences->$preferencename) ? $preferences->$preferencename : '';

            if ($old != $value) {
                add_to_config_log($preferencename, $old, $value, 'message');
            }

            set_config($preferencename, $value, 'message');
            $transaction->allow_commit();

            \core_plugin_manager::reset_caches();
        }

        return [
            'successmessage' => $successmessage,
        ];
    }

    /**
     * Describe the return structure of the external service.
     *
     * @return external_function_parameters
     */
    public static function execute_returns(): external_function_parameters {
        return new external_function_parameters([
            'successmessage' => new external_value(PARAM_TEXT, 'Success notification message.'),
        ]);
    }
}

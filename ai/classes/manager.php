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

namespace core_ai;

use core\exception\coding_exception;
use core_ai\aiactions\base;
use core_ai\aiactions\responses;

/**
 * AI subsystem manager.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /**
     * Get communication provider class name from the plugin name.
     *
     * @param string $plugin The component name.
     * @return string The class name of the provider.
     */
    private static function get_ai_plugin_classname(string $plugin): string {
        if (str_starts_with($plugin, 'aiprovider_')) {
            return "{$plugin}\\provider";
        } else if (str_starts_with($plugin, 'aiplacement_')) {
            return "{$plugin}\\placement";
        } else {
            // Explode if neither.
            throw new coding_exception("Plugin name does not start with 'aiprovider_' or 'aiplacement_': {$plugin}");
        }
    }

    /**
     * Get the list of actions that this provider or placement supports,
     * given the name of the plugin.
     *
     * @param string $pluginname The name of the plugin to get the actions for.
     * @return array An array of action class names.
     */
    public static function get_supported_actions(string $pluginname): array {
        $pluginclassname = static::get_ai_plugin_classname($pluginname);
        $plugin = new $pluginclassname();
        return $plugin->get_action_list();
    }

    /**
     * Given a list of actions get the provider plugins that support them.
     *
     * Will return an array of arrays, indexed by action name.
     *
     * @param array $actions An array of fully qualified action class names.
     * @param bool $enabledonly If true, only return enabled providers.
     * @return array An array of provider instances indexed by action name.
     */
    public static function get_providers_for_actions(array $actions, bool $enabledonly = false): array {
        $providers = [];
        $plugins = \core_plugin_manager::instance()->get_plugins_of_type('aiprovider');
        foreach ($actions as $action) {
            $providers[$action] = [];
            foreach ($plugins as $plugin) {
                $pluginclassname = static::get_ai_plugin_classname($plugin->component);
                $provider = new $pluginclassname();
                // Check the plugin is enabled and the provider is configured before making the action available.
                if ($enabledonly && (!$plugin->is_enabled() || !static::is_action_enabled($plugin->component, $action)) ||
                        $enabledonly && !$provider->is_provider_configured()) {
                    continue;
                }
                if (in_array($action, $provider->get_action_list())) {
                    $providers[$action][] = $provider;
                }
            }
        }
        return $providers;
    }

    /**
     * Call the action provider.
     *
     * The named provider will process the action and return the result.
     *
     * @param provider $provider The provider to call.
     * @param base $action The action to process.
     * @return responses\response_base The result of the action.
     */
    protected function call_action_provider(provider $provider, base $action): responses\response_base {
        $classname = 'process_' . $action->get_basename();
        $classpath = substr($provider::class, 0, strpos($provider::class, '\\') + 1);
        $processclass = $classpath . $classname;
        $processor = new $processclass($provider, $action);

        return $processor->process($action);
    }

    /**
     * Process an action.
     *
     * This is the entry point for processing an action.
     *
     * @param base $action The action to process. Action must be configured.
     * @return responses\response_base The result of the action.
     */
    public function process_action(base $action): responses\response_base {
        // Get the action response_base name.
        $actionname = $action::class;
        $responseclassname = 'core_ai\\aiactions\\responses\\response_' . $action->get_basename();

        // Get the providers that support the action.
        $providers = self::get_providers_for_actions([$actionname], true);

        // Loop through the providers and process the action.
        foreach ($providers[$actionname] as $provider) {
            $result = $this->call_action_provider($provider, $action);

            // Store the result (success or failure).
            $this->store_action_result($provider, $action, $result);

            // If the result is successful, return the result.
            // No need to keep looping.
            if ($result->get_success()) {
                return $result;
            }
        }

        // If we get here we've all available providers have failed.
        // Return the result if we have one.
        if (isset($result)) {
            return $result;
        }

        // Response if there are no providers available.
        return new $responseclassname(
            success: false,
            errorcode: -1,
            errormessage: 'No providers available to process the action.');
    }

    /**
     * Store the action result.
     *
     * @param provider $provider The provider that processed the action.
     * @param base $action The action that was processed.
     * @param responses\response_base $response The result of the action.
     * @return int The id of the stored action result.
     */
    private function store_action_result(
        provider $provider,
        base $action,
        responses\response_base $response,
    ): int {
        global $DB;
        // Store the action result.
        $record = (object) [
            'actionname' => $action->get_basename(),
            'success' => $response->get_success(),
            'userid' => $action->get_configuration('userid'),
            'contextid' => $action->get_configuration('contextid'),
            'provider' => $provider->get_name(),
            'errorcode' => $response->get_errorcode(),
            'errormessage' => $response->get_errormessage(),
            'timecreated' => $action->get_configuration('timecreated'),
            'timecompleted' => $response->get_timecreated(),
        ];

        try {
            // Do everything in a transaction.
            $transaction = $DB->start_delegated_transaction();

            // Create the record for the action result.
            $record->actionid = $action->store($response);
            $recordid = $DB->insert_record('ai_action_register', $record);

            // Commit the transaction.
            $transaction->allow_commit();
        } catch (\Exception $e) {
            // Rollback the transaction.
            $transaction->rollback($e);
            // Re throw the exception.
            throw $e;
        }

        return $recordid;
    }

    /**
     * Set the policy acceptance for a given user.
     *
     * @param int $userid The user id.
     * @param int $contextid The context id the policy was accepted in.
     * @return bool True if the policy was set, false otherwise.
     */
    public static function user_policy_accepted(int $userid, int $contextid): bool {
        global $DB;

        $record = (object) [
            'userid' => $userid,
            'contextid' => $contextid,
            'timeaccepted' => \core\di::get(\core\clock::class)->time(),
        ];

        if ($DB->insert_record('ai_policy_register', $record)) {
            $policycache = \cache::make('core', 'ai_policy');
            return $policycache->set($userid, true);
        } else {
            return false;
        }
    }

    /**
     * Get the user policy.
     *
     * @param int $userid The user id.
     * @return bool True if the policy was accepted, false otherwise.
     */
    public static function get_user_policy_status(int $userid): bool {
        $policycache = \cache::make('core', 'ai_policy');
        return $policycache->get($userid);
    }

    /**
     * Set the action state for a given plugin.
     *
     * @param string $plugin The name of the plugin.
     * @param string $actionbasename The action to be set.
     * @param int $enabled The state to be set (e.g., enabled or disabled).
     * @return bool Returns true if the configuration was successfully set, false otherwise.
     */
    public static function set_action_state(string $plugin, string $actionbasename, int $enabled): bool {
        $actionclass = 'core_ai\\aiactions\\' . $actionbasename;
        $oldvalue = static::is_action_enabled($plugin, $actionclass);
        // Only set value if there is no config setting or if the value is different from the previous one.
        if ($oldvalue !== $enabled) {
            set_config($actionbasename, $enabled, $plugin);
            add_to_config_log('disabled', !$oldvalue, !$enabled, $plugin);
            \core_plugin_manager::reset_caches();
            return true;
        }
        return false;
    }

    /**
     * Check if an action is enabled for a given plugin.
     *
     * @param string $plugin The name of the plugin.
     * @param string $actionclass The fully qualified action class name to be checked.
     * @return mixed Returns the configuration value of the action for the given plugin.
     */
    public static function is_action_enabled(string $plugin, string $actionclass): bool {
        $value = get_config($plugin, $actionclass::get_basename());
        // If not exist in DB, set it to true (enabled).
        if ($value === false) {
            return true;
        }
        return (bool) $value;
    }

    /**
     * Check if an action is available.
     * Action is available if it is enabled for at least one enabled provider.
     *
     * @param string $actionclass The fully qualified action class name to be checked.
     * @return bool
     */
    public static function is_action_available(string $actionclass): bool {
        $providers = self::get_providers_for_actions([$actionclass], true);
        // Check if the requested action is enabled for at least one provider.
        foreach ($providers as $provideractions) {
            foreach ($provideractions as $provider) {
                $classnamearray = explode('\\', $provider::class);
                $pluginname = reset($classnamearray);
                if (self::is_action_enabled($pluginname, $actionclass)) {
                    return true;
                }
            }
        }
        // There are no providers with this action enabled.
        return false;
    }
}

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
use core\plugininfo\aiprovider as aiproviderplugin;
/**
 * AI subsystem manager.
 *
 * @package    core_ai
 * @copyright  2024 Matt Porritt <matt.porritt@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /**
     * Create a new AI manager.
     *
     * @param \moodle_database $db
     */
    public function __construct(
        /** @var \moodle_database The database instance */
        protected readonly \moodle_database $db,
    ) {
    }

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
        return $pluginclassname::get_action_list();
    }

    /**
     * Given a list of actions get the provider instances that support them.
     *
     * Will return an array of arrays, indexed by action name.
     *
     * @param array $actions An array of fully qualified action class names.
     * @param bool $enabledonly If true, only return enabled providers.
     * @return array An array of provider instances indexed by action name.
     */
    public function get_providers_for_actions(array $actions, bool $enabledonly = false): array {
        $providers = [];
        $instances = $this->get_sorted_providers();
        foreach ($actions as $action) {
            $providers[$action] = [];
            foreach ($instances as $instance) {
                // Check the plugin is enabled and the provider is configured before making the action available.
                if ($enabledonly && (!$instance->enabled
                        || !$this->is_action_enabled($instance->provider, $action, $instance->id))
                        || $enabledonly && !$instance->is_provider_configured()) {
                    continue;
                }
                if (in_array($action, $instance->get_action_list())) {
                    $providers[$action][] = $instance;
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
        $providers = $this->get_providers_for_actions([$actionname], true);

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
            'model' => $response->get_model_used(),
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
     * @param int $instanceid The instance id of the instance.
     * @return bool Returns true if the configuration was successfully set, false otherwise.
     */
    public function set_action_state(
        string $plugin,
        string $actionbasename,
        int $enabled,
        int $instanceid = 0
    ): bool {
        $actionclass = 'core_ai\\aiactions\\' . $actionbasename;
        $oldvalue = $this->is_action_enabled($plugin, $actionclass, $instanceid);

        // Check if we are setting an action for a provider or placement.
        if (str_contains($plugin, 'aiprovider')) {
            // Handle provider actions.
            $providers = $this->get_provider_instances(['id' => $instanceid]);
            $provider = reset($providers);

            // Update the enabled state of the action.
            $actionconfig = $provider->actionconfig;
            $actionconfig[$actionclass]['enabled'] = (bool)$enabled;

            return $this->update_provider_instance(
                provider: $provider,
                actionconfig: $actionconfig)->actionconfig[$actionclass]['enabled'];

        } else {
            // Handle placement actions.
            // Only set value if there is no config setting or if the value is different from the previous one.
            if ($oldvalue !== (bool)$enabled) {
                set_config($actionbasename, $enabled, $plugin);
                add_to_config_log('disabled', !$oldvalue, !$enabled, $plugin);
                \core_plugin_manager::reset_caches();
                return true;
            }
            return false;
        }
    }

    /**
     * Check if an action is enabled for a given provider.
     *
     * @param string $plugin The name of the plugin.
     * @param string $actionclass The fully qualified action class name to be checked.
     * @param int $instanceid The instance id of the plugin.
     * @return bool Returns the configuration value of the action for the given plugin.
     */
    private function is_provider_action_enabled(string $plugin, string $actionclass, int $instanceid): bool {
        // If there is no instance id, we are checking the provider itself.
        // So get the defaults.
        if ($instanceid === 0) {
            // Get the defaults for this provider type.
            $classname = "\\{$plugin}\\provider";
            $defaultconfig = $classname::initialise_action_settings();

            // Return the default value.
            return array_key_exists($actionclass, $defaultconfig) && $defaultconfig[$actionclass]['enabled'];

        } else {
            // Get the provider instance.
            $providers = $this->get_provider_instances(['id' => $instanceid]);
            $provider = reset($providers);
            return array_key_exists($actionclass, $provider->actionconfig) && $provider->actionconfig[$actionclass]['enabled'];
        }
    }

    /**
     * Check if an action is enabled for a given plugin.
     *
     * @param string $plugin The name of the plugin.
     * @param string $actionclass The fully qualified action class name to be checked.
     * @param int $instanceid The instance id of the plugin.
     * @return bool Returns the configuration value of the action for the given plugin.
     */
    public function is_action_enabled(string $plugin, string $actionclass, int $instanceid = 0): bool {
        if (str_contains($plugin, 'aiprovider')) {
            // Handle provider actions.
            return $this->is_provider_action_enabled($plugin, $actionclass, $instanceid);
        } else {
            // Handle placement actions.
            $value = get_config($plugin, $actionclass::get_basename());
            // If not exist in DB, set it to true (enabled).
            if ($value === false) {
                return true;
            }
            return (bool) $value;
        }
    }

    /**
     * Check if an action is available.
     * Action is available if it is enabled for at least one enabled provider.
     *
     * @param string $actionclass The fully qualified action class name to be checked.
     * @return bool
     */
    public function is_action_available(string $actionclass): bool {
        $providers = $this->get_providers_for_actions([$actionclass], true);
        // Check if the requested action is enabled for at least one provider.
        foreach ($providers as $provideractions) {
            foreach ($provideractions as $provider) {
                $classnamearray = explode('\\', $provider::class);
                $pluginname = reset($classnamearray);
                if ($this->is_action_enabled($pluginname, $actionclass)) {
                    return true;
                }
            }
        }
        // There are no providers with this action enabled.
        return false;
    }

    /**
     * Create a new provider instance.
     *
     * @param string $classname Classname of the provider.
     * @param string $name The name of the provider config.
     * @param bool $enabled The enabled state of the provider.
     * @param array|null $config The config json.
     * @param array|null $actionconfig The action config json.
     * @return provider
     */
    public function create_provider_instance(
        string $classname,
        string $name,
        bool $enabled = false,
        ?array $config = null,
        ?array $actionconfig = null,
    ): provider {
        if (!class_exists($classname) || !is_a($classname, provider::class, true)) {
            throw new \coding_exception("Provider class not valid: {$classname}");
        }
        $provider = new $classname(
            enabled: $enabled,
            name: $name,
            config: json_encode($config ?? []),
            actionconfig: $actionconfig ? json_encode($actionconfig) : '',
        );

        $id = $this->db->insert_record('ai_providers', $provider->to_record());

        // Ensure the provider instance order config gets updated if the provider is enabled.
        if ($enabled) {
            $this->update_provider_order($id, \core\plugininfo\aiprovider::ENABLE);
        }

        return $provider->with(id: $id);
    }

    /**
     * Get single provider record according to the filter
     *
     * @param array $filter The filterable elements to get the record from
     * @param int $strictness
     * @return \stdClass|false
     */
    public function get_provider_record(array $filter = [], int $strictness = IGNORE_MISSING): \stdClass|false {
        return $this->db->get_record(
            table: 'ai_providers',
            conditions: $filter,
            strictness: $strictness,
        );
    }

    /**
     * Get the provider records according to the filter.
     *
     * @param array|null $filter The filterable elements to get the records from.
     * @return array
     */
    public function get_provider_records(?array $filter = null): array {
        return $this->db->get_records(
            table: 'ai_providers',
            conditions: $filter,
        );
    }

    /**
     * Get a list of all provider instances.
     *
     * This method retrieves provider records from the database, attempts to instantiate
     * each provider class, and returns an array of provider instances. It filters out
     * any records where the provider class does not exist.
     *
     * @param null|array $filter The database filter to apply when fetching provider records.
     * @return array An array of instantiated provider objects.
     */
    public function get_provider_instances(?array $filter = null): array {
        // Filter out any null values from the array (providers that couldn't be instantiated).
        return array_filter(
            // Apply a callback function to each provider record to instantiate the provider.
            array_map(
                function ($record): ?provider {
                    // Check if the provider class specified in the record exists.
                    if (!class_exists($record->provider)) {
                        // Log a debugging message if the provider class is not found.
                        debugging(
                            "Unable to find a provider class for {$record->provider}",
                            DEBUG_DEVELOPER,
                        );
                        // Return null to indicate that the provider could not be instantiated.
                        return null;
                    }

                    // Instantiate the provider class with the record's data.
                    return new $record->provider(
                        enabled: $record->enabled,
                        id: $record->id,
                        name: $record->name,
                        config: $record->config,
                        actionconfig: $record->actionconfig,
                    );
                },
                // Retrieve the provider records from the database with the optional filter.
                $this->get_provider_records($filter),
            )
        );
    }

    /**
     * Update provider instance.
     *
     * @param provider $provider The provider instance.
     * @param array|null $config the configuration of the provider instance to be updated.
     * @param array|null $actionconfig the action configuration of the provider instance to be updated.
     * @return provider
     */
    public function update_provider_instance(
        provider $provider,
        ?array $config = null,
        ?array $actionconfig = null
    ): provider {
        $provider = $provider->with(
            name: $provider->name,
            config: $config ?? $provider->config,
            actionconfig: $actionconfig ?? $provider->actionconfig,
        );
        $this->db->update_record('ai_providers', $provider->to_record());
        return $provider;
    }

    /**
     * Delete the provider instance.
     *
     * @param provider $provider The provider instance.
     * @return bool
     */
    public function delete_provider_instance(provider $provider): bool {
        // Dispatch the hook before deleting the record.
        $hook = new \core_ai\hook\before_provider_deleted(
            provider: $provider,
        );
        $hookmanager = \core\di::get(\core\hook\manager::class)->dispatch($hook);
        if ($hookmanager->isPropagationStopped()) {
            $deleted = false;
        } else {
            $deleted = $this->db->delete_records('ai_providers', ['id' => $provider->id]);
        }
        return $deleted;
    }

    /**
     * Enable a provider instance.
     *
     * @param provider $provider
     * @return provider
     */
    public function enable_provider_instance(provider $provider): provider {
        if (!$provider->enabled) {
            $provider = $provider->with(enabled: true);
            $this->db->update_record('ai_providers', $provider->to_record());
            $this->update_provider_order($provider->id, aiproviderplugin::ENABLE);
        }

        return $provider;
    }

    /**
     * Disable a provider.
     *
     * @param provider $provider
     * @return provider
     */
    public function disable_provider_instance(provider $provider): provider {
        if ($provider->enabled) {
            $hook = new \core_ai\hook\before_provider_disabled(
                provider: $provider,
            );
            $hookmanager = \core\di::get(\core\hook\manager::class)->dispatch($hook);
            if (!$hookmanager->isPropagationStopped()) {
                $provider = $provider->with(enabled: false);
                $this->db->update_record('ai_providers', $provider->to_record());
            }
            $this->update_provider_order($provider->id, aiproviderplugin::DISABLE);
        }

        return $provider;
    }

    /**
     * Sorts provider instances by configured order.
     *
     * @param array $unsorted of provider instance objects
     * @return array of provider instance objects
     */
    public static function sort_providers_by_order(array $unsorted): array {
        $sorted = [];
        $orderarray = explode(',', get_config('core_ai', 'provider_order'));

        foreach ($orderarray as $notused => $providerid) {
            foreach ($unsorted as $key => $provider) {
                if ($provider->id == $providerid) {
                    $sorted[] = $provider;
                    unset($unsorted[$key]);
                }
            }
        }

        return array_merge($sorted, $unsorted);
    }

    /**
     * Get the configured ai providers from the manager.
     *
     * @return array
     */
    public function get_sorted_providers(): array {
        $unsorted = $this->get_provider_instances();
        $orders = $this->sort_providers_by_order($unsorted);
        $sortedplugins = [];

        foreach ($orders as $order) {
            $sortedplugins[$order->id] = $unsorted[$order->id];
        }

        return $sortedplugins;
    }

    /**
     * Change the order of the provider instance relative to other provider instances.
     *
     * When possible, the change will be stored into the config_log table, to let admins check when/who has modified it.
     *
     * @param int $providerid The provider ID.
     * @param int $direction The direction to move the provider instance. Negative numbers mean up, Positive mean down.
     * @return bool Whether the provider has been updated or not.
     */
    public function change_provider_order(int $providerid, int $direction): bool {
        $activefactors = array_keys($this->get_sorted_providers());
        $key = array_search($providerid, $activefactors);

        if ($key === false) {
            return false;
        }

        $movedown = ($direction === aiproviderplugin::MOVE_DOWN && $key < count($activefactors) - 1);
        $moveup = ($direction === aiproviderplugin::MOVE_UP && $key >= 1);
        if ($movedown || $moveup) {
            $this->update_provider_order($providerid, $direction);
            return true;
        }

        return false;
    }

    /**
     * Update the provider instance order configuration.
     *
     * @param int $providerid The provider ID.
     * @param string|int $action
     *
     * @throws dml_exception
     */
    public function update_provider_order(int $providerid, string|int $action): void {
        $order = explode(',', get_config('core_ai', 'provider_order'));
        $key = array_search($providerid, $order);

        switch ($action) {
            case aiproviderplugin::MOVE_UP:
                if ($key >= 1) {
                    $fsave = $order[$key];
                    $order[$key] = $order[$key - 1];
                    $order[$key - 1] = $fsave;
                }
                break;

            case aiproviderplugin::MOVE_DOWN:
                if ($key < (count($order) - 1)) {
                    $fsave = $order[$key];
                    $order[$key] = $order[$key + 1];
                    $order[$key + 1] = $fsave;
                }
                break;

            case aiproviderplugin::ENABLE:
                if (!$key) {
                    $order[] = $providerid;
                }
                break;

            case aiproviderplugin::DISABLE:
                if ($key) {
                    unset($order[$key]);
                }
                break;
        }

        $this->set_provider_config(['provider_order' => implode(',', $order)], 'core_ai');

        \core\session\manager::gc(); // Remove stale sessions.
        \core_plugin_manager::reset_caches();
    }

    /**
     * Sets config variable for given provider instance.
     *
     * @param array $data The data to set.
     * @param string $plugin The plugin name.
     *
     * @return bool true or exception.
     * @throws dml_exception
     */
    public function set_provider_config(array $data, string $plugin): bool|dml_exception {
        $providerconf = get_config($plugin);
        foreach ($data as $key => $newvalue) {
            if (empty($providerconf->$key)) {
                add_to_config_log($key, null, $newvalue, $plugin);
                set_config($key, $newvalue, $plugin);
            } else if ($providerconf->$key != $newvalue) {
                add_to_config_log($key, $providerconf->$key, $newvalue, $plugin);
                set_config($key, $newvalue, $plugin);
            }
        }
        return true;
    }
}

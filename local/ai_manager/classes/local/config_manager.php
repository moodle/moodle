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

namespace local_ai_manager\local;

use local_ai_manager\base_purpose;

/**
 * Class for managing the configuration of tenants.
 *
 * @package    local_ai_manager
 * @copyright  2024 ISB Bayern
 * @author     Philipp Memmel
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class config_manager {

    /** @var array $config the array which stores the configuration of the current tenant */
    private array $config = [];

    /**
     * Constructor for the config manager.
     *
     * @param tenant $tenant the tenant for which the config manager should manage the configuration
     */
    public function __construct(
        /** @var tenant $tenant the tenant for which the config manager should manage the configuration */
            private readonly tenant $tenant
    ) {
        $this->load_config();
    }

    /**
     * Returns all the keys which must not be retrieved directly, but need to be accessed by a separate getter function.
     *
     * @return array array of keys which are not allowed to be read directly
     */
    private function get_separate_getter_config_keys(): array {
        $keys = [];
        foreach (base_purpose::get_all_purposes() as $purpose) {
            $keys[] = $purpose . '_max_requests_basic';
            $keys[] = $purpose . '_max_requests_extended';
        }
        $keys[] = 'max_requests_period';
        $keys[] = 'tenantenabled';
        return $keys;
    }

    /**
     * Helper function to load the current config of the current tenant from database into the config manager object.
     */
    private function load_config(): void {
        global $DB;
        if (empty($this->tenant->get_identifier())) {
            $this->config = [];
            return;
        }
        $records = $DB->get_records('local_ai_manager_config', ['tenant' => $this->tenant->get_identifier()]);
        foreach ($records as $record) {
            $this->config[$record->configkey] = $record->configvalue;
        }
    }

    /**
     * Common getter for config keys.
     *
     * @param string $configkey the key for which the config value should be retrieved
     * @return false|string false if there is no value for the specified key, the value as string otherwise
     * @throws \coding_exception if you must not access the config key directly but must use a separate getter
     */
    public function get_config(string $configkey): false|string {
        if (in_array($configkey, $this->get_separate_getter_config_keys())) {
            throw new \coding_exception('You must not access this config key directly. Please use the separate getter function.');
        }
        if (!array_key_exists($configkey, $this->config)) {
            return false;
        }
        return $this->config[$configkey];
    }

    /**
     * Remove config with specified config key for the current tenant.
     *
     * @param string $configkey The config key for the entry which should be removed
     */
    public function unset_config(string $configkey): void {
        global $DB;
        if (empty($this->tenant->get_identifier())) {
            return;
        }
        $DB->delete_records('local_ai_manager_config',
                [
                        'tenant' => $this->tenant->get_identifier(),
                        'configkey' => $configkey,
                ]
        );
    }

    /**
     * Retrieve the purpose config for this tenant.
     *
     * @param int $role the local_ai_manager internal role for which the purpose config should be calculated
     * @return array Returns array of the form ['purposename' => 3, 'purpose2name' => null, ...].
     *  Value null means that purpose is not configured for the tenant, integer value is the id of the configured connector instance
     */
    public function get_purpose_config(int $role): array {
        $purposeconfig = [];
        foreach (base_purpose::get_all_purposes() as $purpose) {
            if (array_key_exists(base_purpose::get_purpose_tool_config_key($purpose, $role), $this->config)) {
                $purposeconfig[$purpose] = $this->config[base_purpose::get_purpose_tool_config_key($purpose, $role)];
            } else {
                $purposeconfig[$purpose] = null;
            }
        }
        return $purposeconfig;
    }

    /**
     * Set a config key-value-pair.
     *
     * @param string $configkey the key
     * @param string $configvalue the value
     */
    public function set_config(string $configkey, string $configvalue): void {
        global $DB;
        // phpcs:disable moodle.Commenting.TodoComment.MissingInfoInline
        // TODO Eventually do a validation of which config keys are allowed.
        // phpcs:enable moodle.Commenting.TodoComment.MissingInfoInline
        $configrecord = $DB->get_record('local_ai_manager_config',
                ['configkey' => $configkey, 'tenant' => $this->tenant->get_identifier()]);
        if ($configrecord) {
            $configrecord->configvalue = $configvalue;
            $DB->update_record('local_ai_manager_config', $configrecord);
        } else {
            $configrecord = new \stdClass();
            $configrecord->configkey = $configkey;
            $configrecord->configvalue = $configvalue;
            $configrecord->tenant = $this->tenant->get_identifier();
            $DB->insert_record('local_ai_manager_config', $configrecord);
        }
        $this->load_config();
    }

    /**
     * Standard getter.
     *
     * @return tenant the tenant object
     */
    public function get_tenant(): tenant {
        return $this->tenant;
    }

    /**
     * Getter for the max requests config option for a purpose and a given role.
     *
     * @param base_purpose $purpose the purpose for which the max requests should be returned
     * @param int $role the role for which the max requests should be returned
     * @return int amount of max requests, or {@see userusage::UNLIMITED_REQUESTS_PER_USER} if no maximum
     */
    public function get_max_requests(base_purpose $purpose, int $role): int {
        $maxrequests = false;
        switch ($role) {
            case userinfo::ROLE_BASIC:
                $maxrequests = $this->get_max_requests_raw($purpose, userinfo::ROLE_BASIC);
                if ($maxrequests === false) {
                    $maxrequests = userusage::MAX_REQUESTS_DEFAULT_ROLE_BASE;
                }
                break;
            case userinfo::ROLE_EXTENDED:
                $maxrequests = $this->get_max_requests_raw($purpose, userinfo::ROLE_EXTENDED);
                if ($maxrequests === false) {
                    $maxrequests = userusage::MAX_REQUESTS_DEFAULT_ROLE_EXTENDED;
                }
                break;
            case userinfo::ROLE_UNLIMITED:
                $maxrequests = userusage::UNLIMITED_REQUESTS_PER_USER;
                break;
        }
        return $maxrequests;
    }

    /**
     * Getter for the unmanipulated max requests for a given purpose and role.
     *
     * @param base_purpose $purpose the purpose for which the max requests should be returned
     * @param int $role the role for which the max requests should be returned
     * @return int|false the maximum amount of requests or false if no config is set
     */
    public function get_max_requests_raw(base_purpose $purpose, int $role): int|false {
        $rolesuffix = '';
        switch ($role) {
            case userinfo::ROLE_BASIC:
                $rolesuffix = 'basic';
                break;
            case userinfo::ROLE_EXTENDED:
                $rolesuffix = 'extended';
        }
        $configkey = $purpose->get_plugin_name() . '_max_requests_' . $rolesuffix;
        if (!array_key_exists($configkey, $this->config)) {
            return false;
        }
        return intval($this->config[$configkey]);
    }

    /**
     * Getter for the max requests period config.
     *
     * @return int the max requests period
     */
    public function get_max_requests_period(): int {
        if (!array_key_exists('max_requests_period', $this->config)) {
            return userusage::MAX_REQUESTS_DEFAULT_PERIOD;
        }
        return $this->config['max_requests_period'];
    }

    /**
     * Returns if the tenant has been enabled by a tenant manager.
     *
     * @return bool true if the tenant is enabled
     */
    public function is_tenant_enabled(): bool {
        if (!array_key_exists('tenantenabled', $this->config)) {
            return false;
        }
        if (!$this->tenant->is_tenant_allowed()) {
            return false;
        }
        return $this->config['tenantenabled'];
    }
}

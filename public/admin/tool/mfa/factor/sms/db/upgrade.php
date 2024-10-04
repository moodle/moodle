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
 * factor_sms upgrade library.
 *
 * @package    factor_sms
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Factor sms upgrade helper.
 *
 * @param int $oldversion Previous version of the plugin.
 * @return bool
 */
function xmldb_factor_sms_upgrade(int $oldversion): bool {
    if ($oldversion < 2024082200) {
        $config = get_config('factor_sms');
        // If the sms factor is enabled, then do the migration to the sms subsystem.
        if ((int)$config->enabled === 1) {
            // SMS gateway configs.
            $smsconfig = new stdClass();
            $smsconfig->countrycode = $config->countrycode;
            $smsconfig->gateway = $config->gateway;
            $smsconfig->usecredchain = $config->usecredchain;
            $smsconfig->api_key = $config->api_key;
            $smsconfig->api_secret = $config->api_secret;
            $smsconfig->api_region = $config->api_region;
            // Now insert the record.
            $manager = \core\di::get(\core_sms\manager::class);
            $gateway = $manager->create_gateway_instance(
                classname: \smsgateway_aws\gateway::class,
                name: 'MFA AWS',
                enabled: $config->enabled,
                config: $smsconfig,
            );
            // Set the mfa config for the sms gateway.
            set_config('smsgateway', $gateway->id, 'factor_sms');

            // Now add the task to send notification to admins about this migration.
            $task = new \factor_sms\task\sms_gateway_migration_notification();
            \core\task\manager::queue_adhoc_task($task, true);
        }
        // MFA savepoint reached.
        upgrade_plugin_savepoint(true, 2024082200, 'factor', 'sms');
    }

    if ($oldversion < 2024082201) {
        // Unset the removed admin settings.
        unset_config('countrycode', 'factor_sms');
        unset_config('gateway', 'factor_sms');
        unset_config('usecredchain', 'factor_sms');
        unset_config('api_key', 'factor_sms');
        unset_config('api_secret', 'factor_sms');
        unset_config('api_region', 'factor_sms');

        // MFA savepoint reached.
        upgrade_plugin_savepoint(true, 2024082201, 'factor', 'sms');
    }

    // Automatically generated Moodle v4.5.0 release upgrade line.
    // Put any upgrade step following this.

    if ($oldversion < 2025040700) {
        // Ensure default values are applied for the MFA SMS factor when upgrading.
        $config = get_config('factor_sms');

        // Set the weight to the default value (100) if it is misconfigured (e.g. set to 0).
        $weight = $config->weight ?? null;
        if (isset($weight) && (int)$weight <= 0) {
            set_config('weight', 100, 'factor_sms');
        }

        // Set the duration to the default value (30 minutes) if it is misconfigured (e.g. set to 0).
        $duration = $config->duration ?? null;
        if (isset($duration) && (int)$duration <= 0) {
            set_config('duration', 30 * MINSECS, 'factor_sms');
        }

        // MFA savepoint reached.
        upgrade_plugin_savepoint(true, 2025040700, 'factor', 'sms');
    }

    // Automatically generated Moodle v5.0.0 release upgrade line.
    // Put any upgrade step following this.

    return true;
}

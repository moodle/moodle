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
    if ($oldversion < 2024050300) {
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
            $manager->create_gateway_instance(
                classname: \smsgateway_aws\gateway::class,
                enabled: $config->enabled,
                config: $smsconfig,
            );
        }
        // MFA savepoint reached.
        upgrade_plugin_savepoint(true, 2024050300, 'factor', 'sms');
    }

    return true;
}

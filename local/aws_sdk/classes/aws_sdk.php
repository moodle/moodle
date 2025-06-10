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
 * Utility to help use the AWS SDK.
 *
 * @package
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_aws_sdk;

/**
 * Utility to help use the AWS SDK.
 *
 * @package   local_aws_sdk
 * @copyright Copyright (c) 2016 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class aws_sdk {
    /**
     * Required AWS config names.
     */
    const REQUIRED_CONFIGS = ['region'];

    /**
     * Autoload the AWS SDK classes.
     */
    public static function autoload() {
        require_once(__DIR__.'/../vendor/autoload.php');
    }

    /**
     * Extract region, key and secret from CFG global.
     *
     * @param string $name The CFG config name
     * @return array
     */
    public static function config_from_cfg($name) {
        global $CFG;

        if (empty($CFG->$name)) {
            throw new \coding_exception('The $CFG->'.$name.' must be set');
        }
        if (!is_array($CFG->$name)) {
            throw new \coding_exception('The $CFG->'.$name.' must be set to an array');
        }
        $cfg = $CFG->$name;
        foreach (self::REQUIRED_CONFIGS as $key) {
            if (!array_key_exists($key, $cfg)) {
                throw new \coding_exception('The $CFG->'.$name.' is missing the \''.$key.'\' option.  '.
                    'Required configs: '.implode(', ', self::REQUIRED_CONFIGS));
            }
        }

        $config = ['region' => $cfg['region']];

        $credentials = self::resolve_credentials($name, $cfg);
        if (!empty($credentials)) {
            $config['credentials'] = $credentials;
        }

        return $config;
    }

    /**
     * Extract AWS credentials from CFG value.
     *
     * @param string $name The CFG name.
     * @param array $cfg The Moodle config value.
     * @return mixed
     */
    private static function resolve_credentials($name, array $cfg) {
        // First check for key/secret credentials.
        if (array_key_exists('key', $cfg) && array_key_exists('secret', $cfg)) {
            return [
                'key'    => $cfg['key'],
                'secret' => $cfg['secret']
            ];
        }

        // With no credentials set, then the AWS SDK will fallback to various means for acquiring credentials.
        // See http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/configuration.html#credentials for details.

        // When using IAM roles, we can set credentials to a cache so the SDK doesn't have to ping the metadata service every time.
        // See http://docs.aws.amazon.com/aws-sdk-php/v3/guide/guide/credentials.html#instance-profile-credentials for details.
        if (array_key_exists('credentials_cache', $cfg)) {
            // Ensure we are loading as caches require this.
            self::autoload();

            $type = $cfg['credentials_cache'];
            if ($type === 'apcu') {
                if (aws_apcu_cache::are_requirements_met()) {
                    return new aws_apcu_cache();
                }
            } else {
                throw new \coding_exception('The $CFG->'.$name.'[\'credentials_cache\'] value is unknown: '.$type);
            }
        }

        return [];
    }
}

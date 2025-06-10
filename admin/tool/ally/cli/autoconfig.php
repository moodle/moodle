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
 * CLI script for configuring the Ally integration.
 *
 * @package   tool_ally
 * @author    Sam Chaffee
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

use tool_ally\auto_config;
use tool_ally\auto_config_resolver;
use tool_ally\auto_configurator;

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');

list($options, $unrecognized) = cli_get_params(
    [
        'help'      => false,
        'configs'   => '',
        'quiet'     => false,
    ],
    [
        'h' => 'help',
        'c' => 'configs',
        'q' => 'quiet',
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}


if (!empty($options['help'])) {
    echo "Ally auto configuration.

Configures the Ally integration. First, it sets the following Ally Moodle configs:
* tool_ally/key
* tool_ally/secret
* tool_ally/adminurl
* tool_ally/pushurl
* tool_ally/clientid

Then the script runs the Ally web service auto configuration.

Options:
-h, --help Print out this help
-c, --configs JSON encoded string of a hash of key/value pairs for the Ally Moodle configs
-q, --quiet Suppresses all output except for printing the site url and web service token

Example:
$ sudo -u www-data /usr/bin/php admin/tool/ally/cli/autoconfig.php -c='{\"key\": \"mykey\", \"secret\": \"mysecret\"}'" . PHP_EOL;

    die;
}

$quiet = !empty($options['quiet']);

$configurator = new auto_configurator();

!$quiet && cli_write('Configuring the tool_ally plugin settings...');
// Resolve where to get the configurations are coming from.
$configresolver = new auto_config_resolver((string) $options['configs']);
try {
    $configurator->configure_settings($configresolver);
} catch (\Exception $e) {
    cli_error($e->getMessage());
}
!$quiet && cli_write('Settings successfully set' . PHP_EOL);
!$quiet && cli_write('Configuring the tool_ally web services...');

// Run the web service autoconfig.
try {
    $wsconfig = new auto_config();
    $configurator->configure_webservices($wsconfig);
} catch (\Exception $e) {
    cli_error($e->getMessage());
}
!$quiet && cli_write('Web services successfully configured' . PHP_EOL);
cli_write($CFG->wwwroot . ',' . $wsconfig->token . PHP_EOL);




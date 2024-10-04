<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * All in one init script - PHP version.
 *
 * @package    tool_phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_SERVER['REMOTE_ADDR'])) {
    die; // No access from web!
}

// Force OPcache reset if used, we do not want any stale caches
// when preparing test environment.
if (function_exists('opcache_reset')) {
    opcache_reset();
}

define('IGNORE_COMPONENT_CACHE', true);

// It makes no sense to use BEHAT_CLI for this script (you cannot initialise PHPunit starting from
// the Behat environment), so in case user has set tne environment variable, disable it.
putenv('BEHAT_CLI=0');

require_once(__DIR__.'/../../../../lib/clilib.php');
require_once(__DIR__.'/../../../../lib/phpunit/bootstraplib.php');
require_once(__DIR__.'/../../../../lib/testing/lib.php');

list($options, $unrecognized) = cli_get_params(
    [
        'help'                 => false,
        'disable-composer'     => false,
        'composer-upgrade'     => true,
        'composer-self-update' => true,
    ],
    [
        'h' => 'help',
    ]
);

$help = "
Utilities to initialise the PHPUnit test site.

Usage:
  php init.php [--no-composer-self-update] [--no-composer-upgrade]
               [--help]

--no-composer-self-update
                    Prevent upgrade of the composer utility using its self-update command

--no-composer-upgrade
                    Prevent update development dependencies using composer

--disable-composer
                    A shortcut to disable composer self-update and dependency update
                    Note: Installation of composer and/or dependencies will still happen as required

-h, --help          Print out this help

Example from Moodle root directory:
\$ php admin/tool/phpunit/cli/init.php
";

if (!empty($options['help'])) {
    echo $help;
    exit(0);
}

echo "Initialising Moodle PHPUnit test environment...\n";

if ($options['disable-composer']) {
    // Disable self-update and upgrade easily.
    // Note: Installation will still occur regardless of this setting.
    $options['composer-self-update'] = false;
    $options['composer-upgrade'] = false;
}

// Install and update composer and dependencies as required.
testing_update_composer_dependencies($options['composer-self-update'], $options['composer-upgrade']);

$output = null;
exec('php --version', $output, $code);
if ($code != 0) {
    phpunit_bootstrap_error(1, 'Can not execute \'php\' binary.');
}

chdir(__DIR__);
$output = null;
exec("php util.php --diag", $output, $code);
if ($code == PHPUNIT_EXITCODE_INSTALL) {
    passthru("php util.php --install", $code);
    if ($code != 0) {
        exit($code);
    }

} else if ($code == PHPUNIT_EXITCODE_REINSTALL) {
    passthru("php util.php --drop", $code);
    passthru("php util.php --install", $code);
    if ($code != 0) {
        exit($code);
    }

} else if ($code != 0) {
    echo implode("\n", $output)."\n";
    exit($code);
}

passthru("php util.php --buildconfig", $code);

echo "\n";
echo "PHPUnit test environment setup complete.\n";
exit(0);

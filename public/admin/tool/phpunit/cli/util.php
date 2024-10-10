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
 * PHPUnit related utilities.
 *
 * Exit codes: {@see phpunit_bootstrap_error()}
 *
 * @package    tool_phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_SERVER['REMOTE_ADDR'])) {
    die; // No access from web!
}

define('IGNORE_COMPONENT_CACHE', true);

// It makes no sense to use BEHAT_CLI for this script (you cannot initialise PHPunit starting from
// the Behat environment), so in case user has set tne environment variable, disable it.
putenv('BEHAT_CLI=0');

require_once(__DIR__.'/../../../../lib/clilib.php');
require_once(__DIR__.'/../../../../lib/phpunit/bootstraplib.php');
require_once(__DIR__.'/../../../../lib/testing/lib.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
    [
        'drop'                  => false,
        'install'               => false,
        'buildconfig'           => false,
        'buildcomponentconfigs' => false,
        'diag'                  => false,
        'run'                   => false,
        'help'                  => false,
    ],
    [
        'h' => 'help',
    ]
);

// Basic check to see if phpunit is installed.
if (!file_exists(__DIR__.'/../../../../../vendor/phpunit/phpunit/composer.json') ||
        !file_exists(__DIR__.'/../../../../../vendor/bin/phpunit') ||
        !file_exists(__DIR__.'/../../../../../vendor/autoload.php')) {
    phpunit_bootstrap_error(PHPUNIT_EXITCODE_PHPUNITMISSING);
}

if ($options['install'] || $options['drop']) {
    define('CACHE_DISABLE_ALL', true);
}

if ($options['run']) {
    unset($options);
    unset($unrecognized);

    foreach ($_SERVER['argv'] as $k => $v) {
        if (strpos($v, '--run') === 0) {
            unset($_SERVER['argv'][$k]);
            $_SERVER['argc'] = $_SERVER['argc'] - 1;
        }
    }
    $_SERVER['argv'] = array_values($_SERVER['argv']);
    require(__DIR__ . '/../../../../../vendor/bin/phpunit');
    exit(0);
}

define('PHPUNIT_UTIL', true);

require(__DIR__.'/../../../../../vendor/autoload.php');
require(__DIR__ . '/../../../../lib/phpunit/bootstrap.php');

// From now on this is a regular moodle CLI_SCRIPT.

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/upgradelib.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/installlib.php');

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$diag = $options['diag'];
$drop = $options['drop'];
$install = $options['install'];
$buildconfig = $options['buildconfig'];
$buildcomponentconfigs = $options['buildcomponentconfigs'];

if ($options['help'] || (!$drop && !$install && !$buildconfig && !$buildcomponentconfigs && !$diag)) {
    $help = "Various PHPUnit utility functions

Options:
--drop         Drop database and dataroot
--install      Install database
--diag         Diagnose installation and return error code only
--run          Execute PHPUnit tests (alternative for standard phpunit binary)
--buildconfig  Build /phpunit.xml from /phpunit.xml.dist that runs all tests
--buildcomponentconfigs
               Build distributed phpunit.xml files for each component

-h, --help     Print out this help

Example:
\$ php ".testing_cli_argument_path('/public/admin/tool/phpunit/cli/util.php')." --install
";
    echo $help;
    exit(0);
}

if ($diag) {
    list($errorcode, $message) = phpunit_util::testing_ready_problem();
    if ($errorcode) {
        phpunit_bootstrap_error($errorcode, $message);
    }
    exit(0);

} else if ($buildconfig) {
    if (phpunit_util::build_config_file()) {
        exit(0);
    } else {
        phpunit_bootstrap_error(
            PHPUNIT_EXITCODE_CONFIGWARNING,
            'Can not create main /phpunit.xml configuration file, verify dirroot permissions'
        );
    }

} else if ($buildcomponentconfigs) {
    phpunit_util::build_component_config_files();
    exit(0);

} else if ($drop) {
    // Make sure tests do not run in parallel.
    test_lock::acquire('phpunit');
    phpunit_util::drop_site(true);
    // Note: we must stop here because $CFG is messed up and we can not reinstall, sorry.
    exit(0);

} else if ($install) {
    phpunit_util::install_site();
    exit(0);
}

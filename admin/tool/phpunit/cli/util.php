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
 * PHPUnit related utilities.
 *
 * Exit codes: {@see phpunit_bootstrap_error()}
 *
 * @package    tool_phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_SERVER['REMOTE_ADDR'])) {
    die; // no access from web!
}

require_once(__DIR__.'/../../../../lib/clilib.php');
require_once(__DIR__.'/../../../../lib/phpunit/bootstraplib.php');
require_once(__DIR__.'/../../../../lib/testing/lib.php');

// now get cli options
list($options, $unrecognized) = cli_get_params(
    array(
        'drop'                  => false,
        'install'               => false,
        'buildconfig'           => false,
        'buildcomponentconfigs' => false,
        'diag'                  => false,
        'run'                   => false,
        'help'                  => false,
    ),
    array(
        'h' => 'help'
    )
);

if (file_exists(__DIR__.'/../../../../vendor/phpunit/phpunit/PHPUnit/Autoload.php')) {
    // Composer packages present.
    require_once(__DIR__.'/../../../../vendor/autoload.php');
    require_once(__DIR__.'/../../../../vendor/phpunit/phpunit/PHPUnit/Autoload.php');

} else {
    // Verify PHPUnit PEAR libs can be loaded.
    if (!include('PHPUnit/Autoload.php')) {
        phpunit_bootstrap_error(PHPUNIT_EXITCODE_PHPUNITMISSING);
    }
}

if ($options['install'] or $options['drop']) {
    define('CACHE_DISABLE_ALL', true);
}

if ($options['run']) {
    unset($options);
    unset($unrecognized);

    foreach ($_SERVER['argv'] as $k=>$v) {
        if (strpos($v, '--run') === 0) {
            unset($_SERVER['argv'][$k]);
            $_SERVER['argc'] = $_SERVER['argc'] - 1;
        }
    }
    $_SERVER['argv'] = array_values($_SERVER['argv']);
    PHPUnit_TextUI_Command::main();
    exit(0);
}

define('PHPUNIT_UTIL', true);

require(__DIR__ . '/../../../../lib/phpunit/bootstrap.php');

// from now on this is a regular moodle CLI_SCRIPT

require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/upgradelib.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/pluginlib.php');
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

if ($options['help'] or (!$drop and !$install and !$buildconfig and !$buildcomponentconfigs and !$diag)) {
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
\$ php ".testing_cli_argument_path('/admin/tool/phpunit/cli/util.php')." --install
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
        phpunit_bootstrap_error(PHPUNIT_EXITCODE_CONFIGWARNING, 'Can not create main /phpunit.xml configuration file, verify dirroot permissions');
    }

} else if ($buildcomponentconfigs) {
    phpunit_util::build_component_config_files();
    exit(0);

} else if ($drop) {
    // make sure tests do not run in parallel
    test_lock::acquire('phpunit');
    phpunit_util::drop_site(true);
    // note: we must stop here because $CFG is messed up and we can not reinstall, sorry
    exit(0);

} else if ($install) {
    phpunit_util::install_site();
    exit(0);
}

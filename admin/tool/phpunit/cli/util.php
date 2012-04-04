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
 * Exit codes:
 *  0   - success
 *  1   - general error
 *  130 - missing PHPUnit error
 *  131 - configuration problem
 *  132 - install new test database
 *  133 - drop existing data before installing
 *  134 - can not create main phpunit.xml
 *
 * @package    tool_phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('PHPUNIT_UTIL', true);

require_once(__DIR__ . '/../../../../lib/phpunit/bootstraplib.php');

// verify PHPUnit installation
if (!@include_once('PHPUnit/Autoload.php')) {
    phpunit_bootstrap_error(130);
}

require(__DIR__ . '/../../../../lib/phpunit/bootstrap.php');
require_once($CFG->libdir.'/phpunit/lib.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/upgradelib.php');
require_once($CFG->libdir.'/clilib.php');
require_once($CFG->libdir.'/pluginlib.php');
require_once($CFG->libdir.'/installlib.php');

// now get cli options
list($options, $unrecognized) = cli_get_params(
    array(
        'drop'        => false,
        'install'     => false,
        'buildconfig' => false,
        'diag'        => false,
        'help'        => false,
    ),
    array(
        'h' => 'help'
    )
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$diag = $options['diag'];
$drop = $options['drop'];
$install = $options['install'];
$buildconfig = $options['buildconfig'];

if ($options['help'] or (!$drop and !$install and !$buildconfig and !$diag)) {
    $help = "Various PHPUnit utility functions

Options:
--drop                Drop database and dataroot
--install             Install database
--buildconfig         Build /phpunit.xml from /phpunit.xml.dist that includes suites for all plugins and core
--diag                Diagnose installation and return error code only

-h, --help            Print out this help

Example:
\$/usr/bin/php lib/phpunit/tool.php
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
        phpunit_bootstrap_error(134);
    }


} else if ($drop) {
    phpunit_util::drop_site();
    // note: we must stop here because $CFG is messed up and we can not reinstall, sorry
    exit(0);

} else if ($install) {
    phpunit_util::install_site();
    exit(0);
}

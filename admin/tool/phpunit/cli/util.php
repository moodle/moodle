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
 *  130 - coding error
 *  131 - configuration problem
 *  133 - drop existing data before installing
 *
 * @package    tool_phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('PHPUNIT_CLI_UTIL', true);

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

$drop = $options['drop'];
$install = $options['install'];
$buildconfig = $options['buildconfig'];

if ($options['help'] or (!$drop and !$install and !$buildconfig)) {
    $help = "Various PHPUnit utility functions

Options:
--drop                Drop database and dataroot
--install             Install database
--buildconfig         Build /phpunit.xml from /phpunit.xml.dist that includes suites for all plugins and core

-h, --help            Print out this help

Example:
\$/usr/bin/php lib/phpunit/tool.php
";
    echo $help;
    die;
}

if ($buildconfig) {
    phpunit_util::build_config_file();
    exit(0);

} else if ($drop) {
    phpunit_util::drop_site();
    // note: we must stop here because $CFG is messed up and we can not reinstall, sorry
    exit(0);

} else if ($install) {
    phpunit_util::install_site();
    exit(0);
}

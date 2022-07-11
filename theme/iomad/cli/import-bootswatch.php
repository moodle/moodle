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
 * Used to convert a bootswatch file from https://bootswatch.com/ to a Moodle preset.
 *
 * @package    theme_iomad
 * @subpackage cli
 * @copyright  2022 Derick Turner
 * @author    Derick Turner
 * @based on theme_boost by Damyon Wiese
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');

$usage = "
Utility to convert a Bootswatch theme to a Moodle preset compatible with Bootstrap 4.

Download _variables.scss and _bootswatch.scss files from https://bootswatch.com/
Run this script. It will generate a new file 'preset.scss' which can be used as
a Moodle preset.

Usage:
    # php import-bootswatch.php [--help|-h]
    # php import-bootswatch.php --variables=<path> --bootswatch=<path> --preset=<path>

Options:
    -h --help               Print this help.
    --variables=<path>      Path to the input variables file, defaults to _variables.scss
    --bootswatch=<path>     Path to the input bootswatch file, defauls to _bootswatch.scss
    --preset=<path>         Path to the output preset file, defaults to preset.scss
";

list($options, $unrecognised) = cli_get_params([
    'help' => false,
    'variables' => '_variables.scss',
    'bootswatch' => '_bootswatch.scss',
    'preset' => 'preset.scss',
], [
    'h' => 'help',
]);

if ($unrecognised) {
    $unrecognised = implode(PHP_EOL.'  ', $unrecognised);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
}

if ($options['help']) {
    cli_writeln($usage);
    exit(2);
}

if (is_readable($options['variables'])) {
    $sourcevariables = file_get_contents($options['variables']);
} else {
    cli_writeln($usage);
    cli_error('Error reading the variables file: '.$options['variables']);
}


if (is_readable($options['bootswatch'])) {
    $sourcebootswatch = file_get_contents($options['bootswatch']);
} else {
    cli_writeln($usage);
    cli_error('Error reading the bootswatch file: '.$options['bootswatch']);
}

// Write the preset file.
$out = fopen($options['preset'], 'w');

if (!$out) {
    cli_error('Error writing to the preset file');
}

fwrite($out, $sourcevariables);

fwrite($out, '
// Import FontAwesome.
@import "fontawesome";

// Import All of Bootstrap
@import "bootstrap";

// Import Core moodle CSS
@import "moodle";
');

// Add the bootswatch file.
fwrite($out, $sourcebootswatch);

fclose($out);

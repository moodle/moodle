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
 * @package    theme_boost
 * @subpackage cli
 * @copyright  2016 Damyon Wiese
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

/**
 * Local helper function replacing only the first occurrence of a substring.
 *
 * @param string $needle Substring to be searched for
 * @param string $replace New text replacing the old substring
 * @param string $haystack The text where the replacement happens
 * @return string
 */
function str_replace_one($needle, $replace, $haystack) {
    $pos = strpos($haystack, $needle);
    if ($pos !== false) {
        return substr_replace($haystack, $replace, $pos, strlen($needle));
    } else {
        return $haystack;
    }
}

// Now start tweaking the variables strings.
$workingvariables = $sourcevariables;

// Insert a lightest grey colour.
$newrule = '$gray-lightest:          lighten($gray-lighter, 13.5%);';
$nextline = '$brand-primary';
$workingvariables = str_replace_one($nextline, "$newrule\n\n$nextline", $workingvariables);

// Set body-color to text-color.
$newrule = '$body-color: $text-color;';
$nextline = '//** Global textual link color.';
$workingvariables = str_replace_one($nextline, "$newrule\n\n$nextline", $workingvariables);

// Add a font-size-root the same as font-size-base.
$newrule = '$font-size-root: $font-size-base;';
$nextline = '$font-size-large';
$workingvariables = str_replace_one($nextline, "$newrule\n\n$nextline", $workingvariables);

// Replace all 'large' with 'lg'.
$workingvariables = str_replace('large', 'lg', $workingvariables);
// Replace all 'small' with 'sm'.
$workingvariables = str_replace('small', 'sm', $workingvariables);
// Replace all 'vertical' with 'y'.
$workingvariables = str_replace('vertical', 'y', $workingvariables);
// Replace all 'horizontal' with 'x'.
$workingvariables = str_replace('horizontal', 'x', $workingvariables);
// Replace all 'border-radius-base' with 'border-radius'.
$workingvariables = str_replace('border-radius-base', 'border-radius', $workingvariables);
// Replace all 'condensed-cell' with 'sm-cell'.
$workingvariables = str_replace('condensed-cell', 'sm-cell', $workingvariables);

// Add styles for btn-secondary.
$newrule = '$btn-secondary-color: $btn-default-color;
$btn-secondary-bg: $btn-default-bg;
$btn-secondary-border: $btn-default-border;

';
$nextline = '$btn-primary-color';
$workingvariables = str_replace_one($nextline, "$newrule\n\n$nextline", $workingvariables);

// Add a input-border rule matching input-border-color.
$newrule = '$input-border-color: $input-border;';
$nextline = '$input-border-radius:';
$workingvariables = str_replace_one($nextline, "$newrule\n\n$nextline", $workingvariables);
// Replace all 'input-height-base:' with 'input-height:'.
$workingvariables = str_replace('input-height-base:', 'input-height:', $workingvariables);

// Replace all '-default-' with '-light-'.
$workingvariables = str_replace('navbar-default-', 'navbar-light-', $workingvariables);

// Replace all '-inverse-' with '-dark-'.
$workingvariables = str_replace('navbar-inverse-', 'navbar-dark-', $workingvariables);

// Add a pagination-border-color rule matching pagination-border.
$newrule = '$pagination-border-color: $pagination-border;';
$nextline = '$pagination-hover-color';
$workingvariables = str_replace_one($nextline, "$newrule\n\n$nextline", $workingvariables);

// Replace all 'label-' with 'tag-'.
$workingvariables = str_replace('label-', 'tag-', $workingvariables);

// Replace all 'panel-' with 'card-'.
$workingvariables = str_replace('panel-', 'card-', $workingvariables);

// Write the preset file.
$out = fopen($options['preset'], 'w');

if (!$out) {
    cli_error('Error writing to the preset file');
}

fwrite($out, $workingvariables);

fwrite($out, '
@import "moodle";
');

// Now replacements on the bootswatch.
$workingbootswatch = $sourcebootswatch;

$mixins = <<<EOD

@mixin placeholder(\$text) {
    placeholder: \$text;
}
@mixin box-shadow(\$text) {
    box-shadow: \$text;
}
@mixin transform(\$transforms) {
    transform: \$transforms;
}
@mixin rotate (\$deg) {
    @include transform(rotate(#{\$deg}deg));
}
@mixin scale(\$scale) {
    @include transform(scale(\$scale));
}
@mixin translate (\$x, \$y) {
    @include transform(translate(\$x, \$y));
}
@mixin skew (\$x, \$y) {
    @include transform(skew(#{\$x}deg, #{\$y}deg));
}
@mixin transform-origin (\$origin) {
    transform-origin: \$origin;
}

EOD;
// Prepend some mixins.
$workingbootswatch = $mixins . $workingbootswatch;

// Replace all 'large' with 'lg'.
$workingbootswatch = str_replace('large', 'lg', $workingbootswatch);
// Replace all 'small' with 'sm'.
$workingbootswatch = str_replace('small', 'sm', $workingbootswatch);
// Replace all 'vertical' with 'y'.
$workingbootswatch = str_replace('vertical', 'y', $workingbootswatch);
// Replace all 'horizontal' with 'x'.
$workingbootswatch = str_replace('horizontal', 'x', $workingbootswatch);
// Replace all 'border-radius-base' with 'border-radius'.
$workingbootswatch = str_replace('border-radius-base', 'border-radius', $workingbootswatch);
// Replace all 'condensed-cell' with 'sm-cell'.
$workingbootswatch = str_replace('condensed-cell', 'sm-cell', $workingbootswatch);

// Replace all 'input-height-base:' with 'input-height:'.
$workingbootswatch = str_replace('input-height-base:', 'input-height:', $workingbootswatch);

// Replace all '-default-' with '-light-'.
$workingbootswatch = str_replace('navbar-default-', 'navbar-light-', $workingbootswatch);

// Replace all '-inverse-' with '-dark-'.
$workingbootswatch = str_replace('navbar-inverse-', 'navbar-dark-', $workingbootswatch);

// Replace all 'label-' with 'tag-'.
$workingbootswatch = str_replace('label-', 'tag-', $workingbootswatch);

// Replace all 'panel-' with 'card-'.
$workingbootswatch = str_replace('panel-', 'card-', $workingbootswatch);

fwrite($out, $workingbootswatch);

fclose($out);

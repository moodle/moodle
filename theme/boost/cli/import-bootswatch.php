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


// Now get cli options.
list($options, $unrecognized) = cli_get_params(array('help' => false),
    array('h' => 'help', 'v' => 'variables', 'b' => 'bootswatch', 'p' => 'preset'));

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if (!isset($options['variables'])) {
    $options['variables'] = '_variables.scss';
}
if (!isset($options['bootswatch'])) {
    $options['bootswatch'] = '_bootswatch.scss';
}
if (!isset($options['preset'])) {
    $options['preset'] = 'preset.scss';
}

if ($options['help']) {
    $help = "Convert a Bootswatch file from Bootstrap 3 to a Moodle preset file compatible with bootstrap 4.

        This scripts takes the scss files from a Bootstrap 3 Bootswatch and produces a Moodle compatible preset file.

        Options:
        -h, --help            Print out this help
        -v, --variables=<variables file>
        -b, --bootswatch=<bootswatch file>
        -p, --preset=<preset file>

        Example:
        \$import-bootswatch.php -v=_variables.scss -b=_bootswatch.scss -p=preset-paper.scss
        ";

    echo $help;
    die;
}

cli_heading('Convert a Bootswatch file from Bootstrap 3 to a Moodle preset file compatible with bootstrap 4.');
$variablesfile = $options['variables'];
$bootswatchfile = $options['bootswatch'];
$presetfile = $options['preset'];

$sourcevariables = @file_get_contents($variablesfile);
if (!$sourcevariables) {
    die('Could not read variables file: ' . $variablesfile . "\n");
}
$sourcebootswatch = @file_get_contents($bootswatchfile);
if (!$sourcebootswatch) {
    die('Could not read bootswatch file: ' . $bootswatchfile . "\n");
}

function str_replace_one($needle, $replace, $haystack) {
    $pos = strpos($haystack, $needle);
    if ($pos !== false) {
        $newstring = substr_replace($haystack, $replace, $pos, strlen($needle));
    }
    return $newstring;
}

$out = @fopen($presetfile, "w");

if (!$out) {
    die('Could not open preset file for writing: ' . $presetfile . "\n");
}

// Write the license (MIT).

$license = <<<EOD
//
// The MIT License (MIT)
//
// Copyright (c) 2013 Thomas Park
//
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to deal
// in the Software without restriction, including without limitation the rights
// to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
// copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
// OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
// THE SOFTWARE.
//

EOD;

fwrite($out, $license);

$workingvariables = $sourcevariables;
// Now start tweaking the variables strings.

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


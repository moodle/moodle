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
 * Build and store theme CSS.
 *
 * @package    core
 * @subpackage cli
 * @copyright  2017 Ryan Wyllie <ryan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once("$CFG->libdir/clilib.php");
require_once("$CFG->libdir/csslib.php");
require_once("$CFG->libdir/outputlib.php");

$longparams = [
    'themes'    => null,
    'direction' => null,
    'help'      => false,
    'verbose'   => false
];

$shortmappings = [
    't' => 'themes',
    'd' => 'direction',
    'h' => 'help',
    'v' => 'verbose'
];

// Get CLI params.
list($options, $unrecognized) = cli_get_params($longparams, $shortmappings);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    echo
"Compile the CSS for one or more installed themes.
Existing CSS caches will replaced.
By default all themes will be recompiled unless otherwise specified.

Options:
-t, --themes    A comma separated list of themes to be compiled
-d, --direction Only compile a single direction (either ltr or rtl)
-v, --verbose   Print info comments to stdout
-h, --help      Print out this help

Example:
\$ sudo -u www-data /usr/bin/php admin/cli/build_theme_css.php --themes=boost --direction=ltr
";

    die;
}

if (empty($options['verbose'])) {
    $trace = new null_progress_trace();
} else {
    $trace = new text_progress_trace();
}

cli_heading('Build theme css');

// Determine which themes we need to build.
$themenames = [];
if (is_null($options['themes'])) {
    $trace->output('No themes specified. Finding all installed themes.');
    $themenames = array_keys(core_component::get_plugin_list('theme'));
} else {
    if (is_string($options['themes'])) {
        $themenames = explode(',', $options['themes']);
    } else {
        cli_error('--themes must be a comma separated list of theme names');
    }
}

$trace->output('Checking that each theme is correctly installed...');
$themeconfigs = [];
foreach ($themenames as $themename) {
    if (is_null(theme_get_config_file_path($themename))) {
        cli_error("Unable to find theme config for {$themename}");
    }

    // Load the config for the theme.
    $themeconfigs[] = theme_config::load($themename);
}

$directions = ['ltr', 'rtl'];

if (!is_null($options['direction'])) {
    if (!in_array($options['direction'], $directions)) {
         cli_error("--direction must be either ltr or rtl");
    }

    $directions = [$options['direction']];
}

$trace->output('Building CSS for themes: ' . implode(', ', $themenames));
theme_build_css_for_themes($themeconfigs, $directions);

exit(0);

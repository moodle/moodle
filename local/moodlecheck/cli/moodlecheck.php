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
 * Command-line script to Moodle PHP code check
 *
 * @package    local_moodlecheck
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(dirname(dirname(dirname(dirname(__FILE__)))).'/config.php');
require_once($CFG->libdir.'/clilib.php');      // CLI only functions.
require_once($CFG->dirroot. '/local/moodlecheck/locallib.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
        array('help' => false, 'path' => '', 'format' => 'xml', 'exclude' => '', 'rules' => 'all', 'componentsfile' => ''),
        array('h' => 'help', 'p' => 'path', 'f' => 'format', 'e' => 'exclude', 'r' => 'rules', 'c' => 'componentsfile')
    );

$rules = preg_split('/\s*[\n,;]\s*/', trim($options['rules']), -1, PREG_SPLIT_NO_EMPTY);
$paths = preg_split('/\s*[\n,;]\s*/', trim($options['path']), -1, PREG_SPLIT_NO_EMPTY);
$exclude = preg_split('/\s*[\n,;]\s*/', trim($options['exclude']), -1, PREG_SPLIT_NO_EMPTY);
if (!in_array($options['format'], array('xml', 'html', 'text'))) {
    unset($options['format']);
}

if ($options['help'] || !isset($options['format']) || !count($paths)) {
    $help = "Perform Moodle PHP code check.

This script checks all files found in the specified paths against defined rules

Options:
-h, --help            Print out this help
-p, --path            Path(s) to check. Specify paths from the root directory,
                      separate multiple paths with comman, semicolon or newline
-e, --exclude         Path(s) or files to be excluded. Non-php files are
                      automatically excluded
-r, --rules           List rules to check against. Default 'all'
-f, --format          Output format: html, xml, text. Default 'xml'
-c, --componentsfile  Path to one file contaning the list of valid components in format: type, name, fullpath

Example:
\$sudo -u www-data /usr/bin/php local/moodlecheck/cli/moodlecheck.php -p=local/moodlecheck
";

    echo $help;
    die;
}

// Include all files from rules directory.
if ($dh = opendir($CFG->dirroot. '/local/moodlecheck/rules')) {
    while (($file = readdir($dh)) !== false) {
        if ($file != '.' && $file != '..') {
            $pathinfo = pathinfo($file);
            if (isset($pathinfo['extension']) && $pathinfo['extension'] == 'php') {
                require_once($CFG->dirroot. '/local/moodlecheck/rules/'. $file);
            }
        }
    }
    closedir($dh);
}

$output = $PAGE->get_renderer('local_moodlecheck');

if (count($rules) && !in_array('all', $rules)) {
    foreach ($rules as $code) {
        local_moodlecheck_registry::enable_rule($code);
    }
} else {
    local_moodlecheck_registry::enable_all_rules();
}
foreach ($paths as $filename) {
    $path = new local_moodlecheck_path($filename, $exclude);
    local_moodlecheck_path::get_components($options['componentsfile']);
    echo $output->display_path($path, $options['format']);
}

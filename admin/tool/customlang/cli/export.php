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
 * Export custom language strings to zip files.
 *
 * @package    tool_customlang
 * @subpackage customlang
 * @copyright  2020 Thomas Wedekind <thomas.wedekind@univie.ac.at>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once("$CFG->libdir/clilib.php");
require_once("$CFG->dirroot/$CFG->admin/tool/customlang/locallib.php");

$usage = <<<EOF
"Export custom language files to a target folder.
Useful for uploading custom langstrings to AMOS or importing or syncing them to other moodle instances.

Options:
-l, --lang              Comma seperated language ids to export, default: all
-c, --components        Comma seperated components to export, default: all
-t, --target            Target to copy the zip files to, default: $CFG->tempdir/customlang
-o, --overwrite         Overwrite existing files in the target folder.
                            Note: If the target is not set, the files are always overwritten!
-h, --help              Print out this help

Examples:
Export all custom language files to the default folder:
\$ sudo -u www-data /usr/bin/php admin/tool/customlang/cli/export.php

Export just the english files of moodle core and the activity 'quiz' in a subfolder in my home folder:
\$ sudo -u www-data /usr/bin/php admin/tool/customlang/cli/export.php --lang='en' --components='moodle,quiz' --target='~/customdir'

EOF;

$dafaulttarget = "$CFG->tempdir/customlang/";

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
    [
        'lang' => '',
        'components' => '',
        'target' => $dafaulttarget,
        'overwrite' => false,
        'help' => false,
    ],
    ['h' => 'help', 'c' => 'components', 't' => 'target', 'o' => 'overwrite']
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    echo $usage;
    die;
}
if (!file_exists($options['target'])) {
    mkdir($options['target'], 0777, true);
}

cli_writeln(get_string('cliexportheading', 'tool_customlang'));
$langs = [];
if ($options['lang']) {
    $langs = explode(',', $options['lang']);
} else {
    // No language set. We export all installed languages.
    $langs = array_keys(get_string_manager()->get_list_of_translations(true));
}

foreach ($langs as $lang) {
    $filename = $options['target'] . get_string('exportzipfilename', 'tool_customlang', ['lang' => $lang]);
    // If the file exists and we are not using the temp folder it requires an ovewrite.
    if ($options['target'] != $dafaulttarget && file_exists($filename) && !$options['overwrite']) {
        cli_problem(get_string('cliexportfileexists', 'tool_customlang', $lang));
        continue;
    }
    cli_heading(get_string('cliexportstartexport', 'tool_customlang', $lang));
    $langdir = tool_customlang_utils::get_localpack_location($lang);
    if (!file_exists($langdir)) {
        // No custom files set for this language set.
        cli_writeln(get_string('cliexportnofilefoundforlang', 'tool_customlang', ['lang' => $lang]));
        continue;
    }
    $zipper = get_file_packer();
    $tempzip = tempnam($CFG->tempdir . '/', 'tool_customlang_export');
    $filelist = [];
    if ($options['components']) {
        $components = explode(',', $options['components']);
        foreach ($components as $component) {
            $filepath = "$langdir/$component.php";
            if (file_exists($filepath)) {
                $filelist["$component.php"] = $filepath;
            } else {
                cli_problem(
                    get_string('cliexportfilenotfoundforcomponent', 'tool_customlang', ['lang' => $lang, 'file' => $filepath])
                );
            }
        }
    } else {
        $langfiles = scandir($langdir);
        foreach ($langfiles as $file) {
            if (substr($file, 0, 1) != '.') {
                $filelist[$file] = "$langdir/$file";
            }
        }
    }
    if (empty($filelist)) {
        cli_problem(get_string('cliexportnofilefoundforlang', 'tool_customlang', ['lang' => $lang]));
        continue;
    }
    if ($zipper->archive_to_pathname($filelist, $filename)) {
        cli_writeln(get_string('cliexportzipdone', 'tool_customlang', $filename));
    } else {
        cli_problem(get_string('cliexportzipfail', 'tool_customlang', $filename));
    }
}

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
 * CLI customlang import tool.
 *
 * @package    tool_customlang
 * @copyright  2020 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_customlang\local\importer;
use core\output\notification;

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/customlang/locallib.php');
require_once("$CFG->libdir/clilib.php");

$usage =
"Import lang customization.

It can get a single file or a folder.
If no lang is provided it will try to infere from the filename

Options:
--lang                  The target language (will get from filename if not provided)
--source=path           File or folder of the custom lang files (zip or php files)
--mode                  What string should be imported. Options are:
                            - all: all string will be imported (default)
                            - new: only string with no previous customisation
                            - update: only strings already modified
--checkin               Save strings to the language pack
-h, --help              Print out this help

Examples:
\$ sudo -u www-data /usr/bin/php admin/tool/customlang/cli/import.php --lang=en --source=customlangs.zip

\$ sudo -u www-data /usr/bin/php admin/tool/customlang/cli/import.php --source=/tmp/customlangs --checkin

\$ sudo -u www-data /usr/bin/php admin/tool/customlang/cli/import.php --lang=en --source=/tmp/customlangs

";

list($options, $unrecognized) = cli_get_params(
    [
        'help' => false,
        'lang' => false,
        'source' => false,
        'mode' => 'all',
        'checkin' => false,
    ],
    ['h' => 'help']
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    cli_write($usage);
    exit(0);
}

$source = $options['source'] ?? null;
$lang = $options['lang'] ?? null;
$modeparam = $options['mode'] ?? 'all';
$checkin = $options['checkin'] ?? false;

$modes = [
    'all' => importer::IMPORTALL,
    'update' => importer::IMPORTUPDATE,
    'new' => importer::IMPORTNEW,
];
if (!isset($modes[$modeparam])) {
    cli_error(get_string('climissingmode', 'tool_customlang'));
}
$mode = $modes[$modeparam];

if (empty($source)) {
    $source = $CFG->dataroot.'/temp/customlang';
}

if (!file_exists($source)) {
    cli_error(get_string('climissingsource', 'tool_customlang'));
}

// Emulate normal session - we use admin account by default.
\core\cron::setup_user();

// Get the file list.
$files = [];
$langfiles = [];

if (is_file($source)) {
    $files[] = $source;
}
if (is_dir($source)) {
    $filelist = glob("$source/*");
    foreach ($filelist as $filename) {
        $files[] = "$filename";
    }
}

$countfiles = 0;
foreach ($files as $filepath) {
    // Try to get the lang.
    $filelang = $lang;
    // Get component from filename.
    $pathparts = pathinfo($filepath);
    $filename = $pathparts['filename'];
    $extension = $pathparts['extension'];
    if ($extension == 'zip') {
        if (!$filelang) {
            // Try to get the lang from the filename.
            if (strrpos($filename, 'customlang_') === 0) {
                $parts = explode('_', $filename);
                if (!empty($parts[1])) {
                    $filelang = $parts[1];
                }
            }
        }
    } else if ($extension != 'php') {
        // Ignore any other file extension.
        continue;
    }
    if (empty($filelang)) {
        cli_error(get_string('climissinglang', 'tool_customlang'));
    }
    if (!isset($langfiles[$filelang])) {
        $langfiles[$filelang] = [];
    }
    $langfiles[$filelang][] = $filepath;
    $countfiles ++;
}

if (!$countfiles) {
    cli_error(get_string('climissingfiles', 'tool_customlang'));
}

foreach ($langfiles as $lng => $files) {
    $importer = new importer($lng, $mode);
    $storedfiles = [];
    $fs = get_file_storage();

    cli_heading(get_string('clifiles', 'tool_customlang', $lng));

    // If we intend to check in any changes, we must first check them out.
    if ($checkin) {
        cli_writeln(get_string('checkout', 'tool_customlang'));

        $progressbar = new progress_bar();
        $progressbar->create();

        tool_customlang_utils::checkout($lng, $progressbar);
    }

    foreach ($files as $file) {
        // Generate a valid stored_file from this file.
        $record = (object)[
            'filearea' => 'draft',
            'component' => 'user',
            'filepath' => '/',
            'itemid'   => file_get_unused_draft_itemid(),
            'license'  => $CFG->sitedefaultlicense,
            'author'   => '',
            'filename' => clean_param(basename($file), PARAM_FILE),
            'contextid' => \context_user::instance($USER->id)->id,
            'userid' => $USER->id,
        ];
        cli_writeln($file);
        $storedfiles[] = $fs->create_file_from_pathname($record, $file);
    }
    cli_writeln("");

    // Import files.
    cli_heading(get_string('cliimporting', 'tool_customlang', $modeparam));
    $importer->import($storedfiles);
    // Display logs.
    $log = $importer->get_log();
    if (empty($log)) {
        cli_problem(get_string('clinolog', 'tool_customlang', $lng));
    }
    foreach ($log as $message) {
        if ($message->errorlevel == notification::NOTIFY_ERROR) {
            cli_problem($message->get_message());
        } else {
            cli_writeln($message->get_message());
        }
    }
    // Do the checkin if necessary.
    if ($checkin) {
        tool_customlang_utils::checkin($lng);
        cli_writeln(get_string('savecheckin', 'tool_customlang'));
    }
    cli_writeln("");
}

exit(0);

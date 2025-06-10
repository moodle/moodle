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
 * CLI script for getting the valid files list within a time range.
 *
 * @package   tool_ally
 * @author    David Castro
 * @copyright Copyright (c) 2018 Open LMS (https://www.openlms.net) / 2023 Anthology Inc. and its affiliates
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

use tool_ally\local_file;
use tool_ally\local;

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');

list($options, $unrecognized) = cli_get_params(
    [
        'help'        => false,
        'since'       => 0,
        'component'   => false,
        'filearea'    => false,
        'itemid'      => false,
        'mimetype'    => false,
        'omitvalid'   => false,
    ],
    [
        'h' => 'help',
        's' => 'since',
        'c' => 'component',
        'f' => 'filearea',
        'i' => 'itemid',
        'm' => 'mimetype',
        'o' => 'omitvalid'
    ]
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}


if (!empty($options['help'])) {
    echo "Ally valid files.

Outputs a json array with:
* id
* courseid
* name
* mimetype
* contenthash
* timemodified

For files targeted for Ally usage.

Options:
-h, --help      Print out this help
-s, --since     Start timestamp for file modification filtering
-c, --component Component identifier
-f, --filearea  File area identifier
-i, --itemid    Item id
-m, --mimetype  MIME type
-o, --omitvalid If we should omit validation of Ally files, this would show all files

Example:
$ sudo -u www-data /usr/bin/php admin/tool/ally/cli/queryfiles.php -s=1000000000 > /tmp/allyvalidfiles.json" . PHP_EOL;

    die;
}

$files = local_file::iterator();
if (!empty($options['since'])) {
    $files->since($options['since']);
}
if (!empty($options['component'])) {
    $files->with_component($options['component']);
}
if (!empty($options['filearea'])) {
    $files->with_filearea($options['filearea']);
}
if (!empty($options['itemid'])) {
    $files->with_itemid($options['itemid']);
}
if (!empty($options['mimetype'])) {
    $files->with_mimetype($options['mimetype']);
}
$files->with_valid_filter(empty($options['omitvalid']));

$files->sort_by('timemodified');

$files->rewind();

// JSON is written line by line to avoid having to buffer output.
cli_write('[' . PHP_EOL);
while ($files->valid()) {
    $file = $files->current();
    $response = [
        'id'           => $file->get_pathnamehash(),
        'courseid'     => local_file::courseid($file),
        'name'         => $file->get_filename(),
        'mimetype'     => $file->get_mimetype(),
        'contenthash'  => $file->get_contenthash(),
        'timemodified' => local::iso_8601($file->get_timemodified()),
        'component'    => $file->get_component(),
        'filearea'     => $file->get_filearea(),
    ];
    cli_write(json_encode($response));

    $files->next();
    if ($files->valid()) {
        cli_write(', ');
    }
    cli_write(PHP_EOL);
}
cli_write(']' . PHP_EOL);

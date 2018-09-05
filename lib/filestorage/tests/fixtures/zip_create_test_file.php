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
 * This debug script is used during zip support development.
 *
 * @package    core_files
 * @copyright  2012 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');

$help =
    "Create sample zip file for testing
Example:
  \$php zip_create_test_file.php test.zip
";

if (count($_SERVER['argv']) != 2 or file_exists($_SERVER['argv'][1])) {
    echo $help;
    exit(0);
}

$archive = $_SERVER['argv'][1];

$packer = get_file_packer('application/zip');

$file = __DIR__.'/test.txt';
$files = array(
    'test.test' => $file,
    'testíček.txt' => $file,
    'Prüfung.txt' => $file,
    '测试.txt' => $file,
    '試験.txt' => $file,
    'Žluťoučký/Koníček.txt' => $file,
);

$packer->archive_to_pathname($files, $archive);

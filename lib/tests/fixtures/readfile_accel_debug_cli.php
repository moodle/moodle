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
 * Cli script for testing readfile_accel function.
 *
 * @package    core
 * @subpackage fixtures
 * @copyright  2025 Catalyst IT
 * @author     Trisha Milan <trishamilan@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/filelib.php');

if (!defined('PHPUNIT_READFILE_ACCEL_TEST')) {
    echo 'This script is only intended to be run via PHPUnit.';
    exit(1);
}

$testdb = moodle_database::get_driver_instance($CFG->dbtype, $CFG->dblibrary);
$testdb->connect($CFG->dbhost, $CFG->dbuser, $CFG->dbpass, $CFG->dbname, $CFG->phpunit_prefix);
$DB = $testdb;

set_debugging(DEBUG_DEVELOPER, true);
$CFG->tempdir = '/tmp';

/**
 * Runs readfile_accel() with a file path or a stored_file to trigger the buffer check.
 *
 * @param string|stored_file $input
 * @param string $mimetype
 * @param bool $accelerate
 */
function run_readfile_accel_test(string|stored_file $input, string $mimetype, bool $accelerate): void {
    try {
        ob_start();
        echo "test text";
        $_SERVER['REQUEST_METHOD'] = 'GET';
        readfile_accel($input, $mimetype, $accelerate);
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }
}

try {
    // Prepare test file.
    $filename = "readfile_accel.txt";
    // Generate temporary local file for testing.
    $path = "$CFG->tempdir/$filename";
    file_put_contents($path, "\nMoodle test data\n");

    // Populate {files} table.
    $fs = get_file_storage();
    $filerecord = [
        'contextid' => context_system::instance()->id,
        'component' => 'test',
        'filearea' => 'readfile',
        'itemid' => 0,
        'filepath' => '/',
        'filename' => $filename,
    ];
    $storedfile = null;
    $filerecord['filename'] = $fs->get_unused_filename(
        $filerecord['contextid'],
        $filerecord['component'],
        $filerecord['filearea'],
        $filerecord['itemid'],
        $filerecord['filepath'],
        $filerecord['filename']
    );
    $storedfile = $fs->create_file_from_pathname($filerecord, $path);
    $mimetype = get_mimetype_for_sending($storedfile->get_filename());
    $accelerate = true;

    // Run the test with direct path.
    run_readfile_accel_test($path, $mimetype, $accelerate);

    // Run the test with direct stored_file.
    run_readfile_accel_test($storedfile, $mimetype, $accelerate);
} finally {
    // Clean up {files} table.
    if (!is_null($fs)) {
        @$fs->delete_area_files(
            $filerecord['contextid'],
            $filerecord['component'],
            $filerecord['filearea'],
            $filerecord['itemid']
        );
    }

    // Clean up testing file.
    if ($path !== "") {
        @unlink($path);
    }
}

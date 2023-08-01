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
 * Generates a secure key for the current server (presuming it does not already exist).
 *
 * @package core_admin
 * @copyright 2020 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use \core\encryption;

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/clilib.php');

// Get cli options.
[$options, $unrecognized] = cli_get_params(
        ['help' => false, 'method' => null],
        ['h' => 'help']);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

if ($options['help']) {
    echo "Generate secure key

This script manually creates a secure key within the secret data root folder (configured in
config.php as \$CFG->secretdataroot). You must run it using an account with access to write
to that folder.

In normal use Moodle automatically creates the key; this script is intended when setting up
a new Moodle system, for cases where the secure folder is not on shared storage and the key
may be manually installed on multiple servers.

Options:
-h, --help         Print out this help
--method <method>  Generate key for specified encryption method instead of default (sodium)

Example:
php admin/cli/generate_key.php
";
    exit;
}

$method = $options['method'];

if (encryption::key_exists($method)) {
    echo 'Key already exists: ' . encryption::get_key_file($method) . "\n";
    exit;
}

// Creates key with default permissions (no chmod).
echo "Generating key...\n";
encryption::create_key($method, false);

echo "\nKey created: " . encryption::get_key_file($method) . "\n\n";
echo "If the key folder is not shared storage, then key files should be copied to all servers.\n";

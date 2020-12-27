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
 * This hack is intended for clustered sites that do not want
 * to use shared cachedir for component cache.
 *
 * This file needs to be called after any change in PHP files in dataroot,
 * that is before upgrade and install.
 *
 * @package   core
 * @copyright 2013 Petr Skoda (skodak)  {@link http://skodak.org}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


define('CLI_SCRIPT', true);
define('ABORT_AFTER_CONFIG', true); // We need just the values from config.php.
define('CACHE_DISABLE_ALL', true); // This prevents reading of existing caches.
define('IGNORE_COMPONENT_CACHE', true);

require(__DIR__.'/../../config.php');
require_once($CFG->libdir.'/clilib.php');

// Now get cli options.
list($options, $unrecognized) = cli_get_params(
    array(
        'file'    => false,
        'rebuild' => false,
        'print'   => false,
        'help'    => false
    ),
    array(
        'h' => 'help'
    )
);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized), 2);
}

if (!$options['rebuild'] and !$options['file'] and !$options['print']) {
    $help =
"Create alternative component cache file

Options:
-h, --help            Print out this help
--rebuild             Rebuild \$CFG->alternative_component_cache file
--file=filepath       Save component cache to file
--print               Print component cache file content

Example:
\$ php admin/cli/rebuild_alternative_component_cache.php --rebuild
";

    echo $help;
    exit(0);
}

error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', 1);

$content = core_component::get_cache_content();

if ($options['print']) {
    echo $content;
    exit(0);
}

if ($options['rebuild']) {
    if (empty($CFG->alternative_component_cache)) {
        fwrite(STDERR, 'config.php does not contain $CFG->alternative_component_cache setting');
        fwrite(STDERR, "\n");
        exit(2);
    }
    $target = $CFG->alternative_component_cache;
} else {
    $target = $options['file'];
}

if (!$target) {
    fwrite(STDERR, "Invalid target file $target");
    fwrite(STDERR, "\n");
    exit(1);
}

$bytes = file_put_contents($target, $content);

if (!$bytes) {
    fwrite(STDERR, "Error writing to $target");
    fwrite(STDERR, "\n");
    exit(1);
}

// Success.
echo "File $target was updated\n";
exit(0);

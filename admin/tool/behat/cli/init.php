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
 * CLI script to set up all the behat test environment.
 *
 * @package    tool_behat
 * @copyright  2013 David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_SERVER['REMOTE_ADDR'])) {
    die(); // No access from web!
}

// Force OPcache reset if used, we do not want any stale caches
// when preparing test environment.
if (function_exists('opcache_reset')) {
    opcache_reset();
}

// Is not really necessary but adding it as is a CLI_SCRIPT.
define('CLI_SCRIPT', true);
define('CACHE_DISABLE_ALL', true);

// Basic functions.
require_once(__DIR__ . '/../../../../lib/clilib.php');
require_once(__DIR__ . '/../../../../lib/behat/lib.php');


list($options, $unrecognized) = cli_get_params(
    array(
        'parallel' => 0,
        'suffix' => '',
    )
);


$nproc = (int) preg_filter('#.*(\d+).*#', '$1', $options['parallel']);
$suffixarg = $options['suffix'] ? "--suffix={$options['suffix']} --parallel=$nproc" : '';


if ($nproc && !$suffixarg) {
    foreach ((array)glob(__DIR__."/../../../../behat*") as $dir) {
        if (file_exists($dir) && is_link($dir) && preg_match('#/behat\d+$#', $dir)) {
            unlink($dir);
        }
    }
    $cmds = array();
    for ($i = 1; $i <= $nproc; $i++) {
        $cmds[] = "php ".__FILE__." --suffix=$i --parallel=$nproc 2>&1";
    }
    // This is intensive compared to behat itself so halve the parallelism.
    foreach (array_chunk($cmds, max(1, floor($nproc/2)), true) as $chunk) {
        ns_parallel_popen($chunk, true);
    }
    exit(0);
}


// Changing the cwd to admin/tool/behat/cli.
chdir(__DIR__);
$output = null;
exec("php util.php --diag $suffixarg", $output, $code);
if ($code == 0) {
    echo "Behat test environment already installed\n";

} else if ($code == BEHAT_EXITCODE_INSTALL) {

    testing_update_composer_dependencies();

    // Behat and dependencies are installed and we need to install the test site.
    chdir(__DIR__);
    passthru("php util.php --install $suffixarg", $code);
    if ($code != 0) {
        exit($code);
    }

} else if ($code == BEHAT_EXITCODE_REINSTALL) {

    testing_update_composer_dependencies();

    // Test site data is outdated.
    chdir(__DIR__);
    passthru("php util.php --drop $suffixarg", $code);
    if ($code != 0) {
        exit($code);
    }

    passthru("php util.php --install $suffixarg", $code);
    if ($code != 0) {
        exit($code);
    }

} else if ($code == BEHAT_EXITCODE_COMPOSER) {
    // Missing Behat dependencies.

    testing_update_composer_dependencies();

    // Returning to admin/tool/behat/cli.
    chdir(__DIR__);
    passthru("php util.php --install $suffixarg", $code);
    if ($code != 0) {
        exit($code);
    }

} else {
    // Generic error, we just output it.
    echo implode("\n", $output)."\n";
    exit($code);
}

// Enable editing mode according to config.php vars.
passthru("php util.php --enable $suffixarg", $code);
if ($code != 0) {
    exit($code);
}

exit(0);

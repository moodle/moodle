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
 * All in one init script - PHP version.
 *
 * @package    tool_phpunit
 * @copyright  2012 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

if (isset($_SERVER['REMOTE_ADDR'])) {
    die; // no access from web!
}

require_once(__DIR__.'/../../../../lib/clilib.php');
require_once(__DIR__.'/../../../../lib/phpunit/bootstraplib.php');
require_once(__DIR__.'/../../../../lib/testing/lib.php');

echo "Initialising Moodle PHPUnit test environment...\n";

$output = null;
exec('php --version', $output, $code);
if ($code != 0) {
    phpunit_bootstrap_error(1, 'Can not execute \'php\' binary.');
}

chdir(__DIR__);
$output = null;
exec("php util.php --diag", $output, $code);
if ($code == 0) {
    // everything is ready

} else if ($code == PHPUNIT_EXITCODE_INSTALL) {
    passthru("php util.php --install", $code);
    if ($code != 0) {
        exit($code);
    }

} else if ($code == PHPUNIT_EXITCODE_REINSTALL) {
    passthru("php util.php --drop", $code);
    passthru("php util.php --install", $code);
    if ($code != 0) {
        exit($code);
    }

} else {
    echo implode("\n", $output)."\n";
    exit($code);
}

passthru("php util.php --buildconfig", $code);

exit(0);

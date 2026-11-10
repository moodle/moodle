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
 * Install Rector's dependencies when installing Composer development dependencies.
 *
 * @package    core
 * @copyright  2026 Andrew Nicols <andrew@nicols.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// phpcs:disable moodle.Files.MoodleInternal.MoodleInternalGlobalState

if (getenv('COMPOSER_DEV_MODE') !== '1') {
    exit(0);
}

$binary = getenv('COMPOSER_BINARY');
$command = (substr($binary, -5) === '.phar' ? escapeshellarg(PHP_BINARY) . ' ' : '') . escapeshellarg($binary);
passthru($command . ' bin rector install --ansi', $exitcode);
exit($exitcode);

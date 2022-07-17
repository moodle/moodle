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
 * Tool_pluginskel internal functions.
 *
 * @package     tool_pluginskel
 * @copyright   2016 Alexandru Elisei <alexandru.elisei@gmail.com>, David Mudrák <david@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Expand the path for $path.
 *
 * @param string $path The path to be expanded.
 * @return string | false The expanded path, false on failure.
 */
function tool_pluginskel_expand_path(string $path) {

    if ($path === '') {
        return false;
    }

    if ($path[0] === '~') {
        $homedir = getenv('HOME');
        if ($homedir === false) {
            return false;
        }

        $path = $homedir.substr($path, 1);
    }

    if ($path[0] !== DIRECTORY_SEPARATOR) {
        // We cannot use getcwd() here because moodle's setup.php calls chdir() to the script location.
        $cwd = getenv('PWD');
        if ($cwd === false) {
            return false;
        }

        $path = $cwd . DIRECTORY_SEPARATOR . $path;
    }

    $path = realpath($path);
    if ($path === false) {
        return false;
    }

    return $path;
}

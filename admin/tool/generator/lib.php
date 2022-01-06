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
 * Generator tool functions.
 *
 * @package    tool_generator
 * @copyright  David Monllaó
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Files support.
 *
 * Exits if the required permissions are not satisfied.
 *
 * @param stdClass $course course object
 * @param stdClass $cm
 * @param stdClass $context context object
 * @param string $filearea file area
 * @param array $args extra arguments
 * @param bool $forcedownload whether or not force download
 * @param array $options additional options affecting the file serving
 * @return void The file is sent along with it's headers
 */
function tool_generator_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array()) {

    // Only for admins or CLI.
    if (!defined('CLI_SCRIPT') && !is_siteadmin()) {
        die;
    }

    if ($context->contextlevel != CONTEXT_SYSTEM) {
        send_file_not_found();
    }

    $fs = get_file_storage();
    $file = $fs->get_file($context->id, 'tool_generator', $filearea, $args[0], '/', $args[1]);

    // Send the file, always forcing download, we don't want options.
    \core\session\manager::write_close();
    send_stored_file($file, 0, 0, true);
}


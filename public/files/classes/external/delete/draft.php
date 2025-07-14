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
 * This is the external method for deleting draft files.
 *
 * @package    core_files
 * @since      Moodle 3.10
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_files\external\delete;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/filelib.php');

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use context_user;

/**
 * This is the external method for deleting draft files.
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class draft extends external_api {

    /**
     * Describes the parameters for execute.
     *
     * @return external_function_parameters
     * @since Moodle 3.10
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters (
            [
                'draftitemid' => new external_value(PARAM_INT, 'Item id of the draft file area'),
                'files' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'filepath'  => new external_value(PARAM_PATH, 'Path to the file or directory to delete.'),
                            'filename'  => new external_value(PARAM_FILE, 'Name of the file to delete.'),
                        ]
                    ), 'Files or directories to be deleted.'
                ),
            ]
        );
    }

    /**
     * Delete the indicated files (or directories) from a user draft file area.
     *
     * @param  int    $draftitemid item id of the draft file area
     * @param  array  $files       files to be deleted
     * @return array of warnings and parent paths of the files deleted
     * @since Moodle 3.10
     */
    public static function execute(int $draftitemid, array $files): array {
        global $CFG, $USER;
        require_once($CFG->dirroot . '/repository/lib.php');

        $params = self::validate_parameters(self::execute_parameters(), compact('draftitemid', 'files'));
        [$draftitemid, $files] = array_values($params);

        $usercontext = context_user::instance($USER->id);
        self::validate_context($usercontext);

        $files = array_map(function($file) {
            return (object) $file;
        }, $files);
        $parentpaths = repository_delete_selected_files($usercontext, 'user', 'draft', $draftitemid, $files);

        return [
            'parentpaths' => array_keys($parentpaths),
            'warnings' => [],
        ];
    }

    /**
     * Describes the execute return value.
     *
     * @return external_single_structure
     * @since Moodle 3.10
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure(
            [
                'parentpaths' => new external_multiple_structure(
                    new external_value(PARAM_PATH, 'Path to parent directory of the deleted files.')
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }
}

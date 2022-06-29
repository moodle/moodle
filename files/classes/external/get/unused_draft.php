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
 * Generate a new draft itemid for the current user.
 *
 * @package    core_files
 * @since      Moodle 3.11
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_files\external\get;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->libdir . '/filelib.php');

use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use external_warnings;
use context_user;

/**
 * Generate a new draft itemid for the current user.
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class unused_draft extends external_api {

    /**
     * Describes the parameters for execute.
     *
     * @return external_function_parameters
     * @since Moodle 3.11
     */
    public static function execute_parameters() : external_function_parameters {
        return new external_function_parameters ([]);
    }

    /**
     * Generate a new draft itemid for the current user.
     *
     * @return array of information containing the draft item area and possible warnings.
     * @since Moodle 3.11
     */
    public static function execute() : array {
        global $USER;

        $usercontext = context_user::instance($USER->id);
        self::validate_context($usercontext);

        return [
            'component' => 'user',
            'contextid' => $usercontext->id,
            'userid' => $USER->id,
            'filearea' => 'draft',
            'itemid' => file_get_unused_draft_itemid(),
            'warnings' => [],
        ];
    }

    /**
     * Describes the execute return value.
     *
     * @return external_single_structure
     * @since Moodle 3.11
     */
    public static function execute_returns() : external_single_structure {
        return new external_single_structure(
            [
                'component' => new external_value(PARAM_COMPONENT, 'File area component.'),
                'contextid' => new external_value(PARAM_INT, 'File area context.'),
                'userid' => new external_value(PARAM_INT, 'File area user id.'),
                'filearea' => new external_value(PARAM_ALPHA, 'File area name.'),
                'itemid' => new external_value(PARAM_INT, 'File are item id.'),
                'warnings' => new external_warnings(),
            ]
        );
    }
}

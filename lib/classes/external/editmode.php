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
 * A web service to load the mapping of moodle pix names to fontawesome icon names.
 *
 * @package    core
 * @category   external
 * @copyright  2021 Bas Brands <bas@sonsbeekmedia.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core\external;

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;

/**
 * Web service to change the edit mode.
 *
 * @package    core
 * @copyright  2021 Bas Brands <bas@sonsbeekmedia.nl>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class editmode extends external_api {

    /**
     * Description of the parameters suitable for the `change_editmode` function.
     *
     * @return external_function_parameters
     */
    public static function change_editmode_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'setmode' => new external_value(PARAM_BOOL, 'Set edit mode to'),
                'context' => new external_value(PARAM_INT, 'Page context id')
            ]
        );
    }

    /**
     * Save the image and return any warnings and the new image url
     *
     * @param bool $setmode the current edit mode
     * @param int $contextid the current page context id
     * @return array the new edit mode.
     */
    public static function change_editmode(bool $setmode, int $contextid): array {
        global $USER, $PAGE;

        $params = self::validate_parameters(
            self::change_editmode_parameters(),
            [
                'setmode' => $setmode,
                'context' => $contextid
            ]
        );

        $context = \context_helper::instance_by_id($params['context']);
        self::validate_context($context);
        $PAGE->set_context($context);

        if ($context->id === \context_user::instance($USER->id)->id) {
            $PAGE->set_blocks_editing_capability('moodle/my:manageblocks');
        }

        $success = false;
        if ($PAGE->user_allowed_editing()) {
            $USER->editing = $setmode;
            $success = true;
        }

        return ['success' => $success];
    }

    /**
     * Description of the return value for the `change_editmode` function.
     *
     * @return external_single_structure
     */
    public static function change_editmode_returns(): external_single_structure {
        $keys = [
            'success' => new external_value(PARAM_BOOL, 'The edit mode was changed', VALUE_REQUIRED),
        ];

        return new external_single_structure($keys, 'editmode');
    }
}

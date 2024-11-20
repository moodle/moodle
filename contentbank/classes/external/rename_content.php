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

namespace core_contentbank\external;

use core_contentbank\contentbank;
use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

/**
 * This is the external method for renaming a content.
 *
 * @package    core_contentbank
 * @copyright  2020 Amaia Anabitarte <amaia@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class rename_content extends external_api {
    /**
     * rename_content parameters.
     *
     * @since  Moodle 3.9
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'contentid' => new external_value(PARAM_INT, 'The content id to rename', VALUE_REQUIRED),
                'name' => new external_value(PARAM_RAW, 'The new name for the content', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * Rename content from the contentbank.
     *
     * @since  Moodle 3.9
     * @param  int $contentid The content id to rename.
     * @param  string $name The new name.
     * @return array True if the content has been renamed; false and the warning, otherwise.
     */
    public static function execute(int $contentid, string $name): array {
        global $DB;

        $result = false;
        $warnings = [];

        $params = self::validate_parameters(self::execute_parameters(), [
            'contentid' => $contentid,
            'name' => $name,
        ]);
        $params['name'] = clean_param($params['name'], PARAM_TEXT);

        // If name is empty don't try to rename and return a more detailed message.
        if (trim($params['name']) === '') {
            $warnings[] = [
                'item' => $params['contentid'],
                'warningcode' => 'emptynamenotallowed',
                'message' => get_string('emptynamenotallowed', 'core_contentbank')
            ];
        } else {
            try {
                $record = $DB->get_record('contentbank_content', ['id' => $params['contentid']], '*', MUST_EXIST);
                $cb = new contentbank();
                $content = $cb->get_content_from_id($record->id);
                $contenttype = $content->get_content_type_instance();
                $context = \context::instance_by_id($record->contextid, MUST_EXIST);
                self::validate_context($context);
                // Check capability.
                if ($contenttype->can_manage($content)) {
                    // This content can be renamed.
                    if ($contenttype->rename_content($content, $params['name'])) {
                        $result = true;
                    } else {
                        $warnings[] = [
                            'item' => $params['contentid'],
                            'warningcode' => 'contentnotrenamed',
                            'message' => get_string('contentnotrenamed', 'core_contentbank')
                        ];
                    }
                } else {
                    // The user has no permission to manage this content.
                    $warnings[] = [
                        'item' => $params['contentid'],
                        'warningcode' => 'nopermissiontomanage',
                        'message' => get_string('nopermissiontomanage', 'core_contentbank')
                    ];
                }
            } catch (\moodle_exception $e) {
                // The content or the context don't exist.
                $warnings[] = [
                    'item' => $params['contentid'],
                    'warningcode' => 'exception',
                    'message' => $e->getMessage()
                ];
            }
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * rename_content return.
     *
     * @since  Moodle 3.9
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }
}

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
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

/**
 * This is the external method for deleting a content.
 *
 * @package    core_contentbank
 * @since      Moodle 3.9
 * @copyright  2020 Sara Arjona <sara@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_content extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'contentids' => new external_multiple_structure(
                new external_value(PARAM_INT, 'The content id to delete', VALUE_REQUIRED)
            )
        ]);
    }

    /**
     * Delete content from the contentbank.
     *
     * @param  array $contentids List of content ids to delete.
     * @return array True if the content has been deleted; false and the warning, otherwise.
     */
    public static function execute(array $contentids): array {
        global $DB;

        $result = false;
        $warnings = [];

        $params = self::validate_parameters(self::execute_parameters(), ['contentids' => $contentids]);
        $cb = new contentbank();
        foreach ($params['contentids'] as $contentid) {
            try {
                $record = $DB->get_record('contentbank_content', ['id' => $contentid], '*', MUST_EXIST);
                $content = $cb->get_content_from_id($record->id);
                $contenttype = $content->get_content_type_instance();
                $context = \context::instance_by_id($record->contextid, MUST_EXIST);
                self::validate_context($context);
                // Check capability.
                if ($contenttype->can_delete($content)) {
                    // This content can be deleted.
                    if (!$contenttype->delete_content($content)) {
                        $warnings[] = [
                            'item' => $contentid,
                            'warningcode' => 'contentnotdeleted',
                            'message' => get_string('contentnotdeleted', 'core_contentbank')
                        ];
                    }
                } else {
                    // The user has no permission to delete this content.
                    $warnings[] = [
                        'item' => $contentid,
                        'warningcode' => 'nopermissiontodelete',
                        'message' => get_string('nopermissiontodelete', 'core_contentbank')
                    ];
                }
            } catch (\moodle_exception $e) {
                // The content or the context don't exist.
                $warnings[] = [
                    'item' => $contentid,
                    'warningcode' => 'exception',
                    'message' => $e->getMessage()
                ];
            }
        }

        if (empty($warnings)) {
            $result = true;
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * Return.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }
}

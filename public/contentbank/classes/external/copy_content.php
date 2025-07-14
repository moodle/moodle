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
 * This is the external method for copying a content.
 *
 * @package    core_contentbank
 * @copyright  2023 Daniel Neis Araujo
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class copy_content extends external_api {

    /**
     * copy_content parameters.
     *
     * @since  Moodle 4.3
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'contentid' => new external_value(PARAM_INT, 'The content id to copy', VALUE_REQUIRED),
                'name' => new external_value(PARAM_RAW, 'The new name for the content', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * Copy content from the contentbank.
     *
     * @since  Moodle 4.3
     * @param  int $contentid The content id to copy.
     * @param  string $name The new name.
     * @return array Id of the new content; false and the warning, otherwise.
     */
    public static function execute(int $contentid, string $name): array {
        global $DB;

        $id = 0;
        $warnings = [];

        $params = self::validate_parameters(self::execute_parameters(), [
            'contentid' => $contentid,
            'name' => $name,
        ]);
        $params['name'] = clean_param($params['name'], PARAM_TEXT);

        // If name is empty don't try to copy and return a more detailed message.
        if (trim($params['name']) === '') {
            $warnings[] = [
                'item' => $params['contentid'],
                'warningcode' => 'emptynamenotallowed',
                'message' => get_string('emptynamenotallowed', 'core_contentbank'),
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
                if ($contenttype->can_copy($content)) {

                    // This content can be copied.
                    $crecord = $content->get_content();
                    unset($crecord->id);
                    $crecord->name = $params['name'];

                    if ($content = $contenttype->create_content($crecord)) {

                        $handler = \core_contentbank\customfield\content_handler::create();
                        $handler->instance_form_before_set_data($record);
                        $record->id = $content->get_id();
                        $handler->instance_form_save($record);

                        $fs = get_file_storage();
                        $files = $fs->get_area_files($context->id, 'contentbank', 'public', $params['contentid'], 'itemid, filepath,
                            filename', false);
                        if (!empty($files)) {
                            $file = reset($files);
                            $content->import_file($file);
                        }
                        $id = $content->get_id();
                    } else {
                        $warnings[] = [
                            'item' => $params['contentid'],
                            'warningcode' => 'contentnotcopied',
                            'message' => get_string('contentnotcopied', 'core_contentbank'),
                        ];
                    }
                } else {
                    // The user has no permission to manage this content.
                    $warnings[] = [
                        'item' => $params['contentid'],
                        'warningcode' => 'nopermissiontomanage',
                        'message' => get_string('nopermissiontocopy', 'core_contentbank'),
                    ];
                }
            } catch (\moodle_exception $e) {
                // The content or the context don't exist.
                $warnings[] = [
                    'item' => $params['contentid'],
                    'warningcode' => 'exception',
                    'message' => $e->getMessage(),
                ];
            }
        }

        return [
            'id' => $id,
            'warnings' => $warnings,
        ];
    }

    /**
     * copy_content return.
     *
     * @since  Moodle 4.3
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'id' => new external_value(PARAM_INT, 'The id of the new content'),
            'warnings' => new external_warnings(),
        ]);
    }
}

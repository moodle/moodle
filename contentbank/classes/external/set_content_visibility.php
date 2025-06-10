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

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');

use external_api;
use external_function_parameters;
use external_single_structure;
use external_value;
use external_warnings;

/**
 * External API to set the visibility of content bank content.
 *
 * @package    core_contentbank
 * @copyright  2020 François Moreau
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class set_content_visibility extends external_api {
    /**
     * set_content_visibility parameters.
     *
     * @since  Moodle 3.11
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'contentid' => new external_value(PARAM_INT, 'The content id to rename', VALUE_REQUIRED),
                'visibility' => new external_value(PARAM_INT, 'The new visibility for the content', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * Set visibility of a content from the contentbank.
     *
     * @since  Moodle 3.11
     * @param  int $contentid The content id to rename.
     * @param  int $visibility The new visibility.
     * @return array
     */
    public static function execute(int $contentid, int $visibility): array {
        global $DB;

        $result = false;
        $warnings = [];

        $params = self::validate_parameters(self::execute_parameters(), [
            'contentid' => $contentid,
            'visibility' => $visibility,
        ]);

        try {
            $record = $DB->get_record('contentbank_content', ['id' => $params['contentid']], '*', MUST_EXIST);
            $contenttypeclass = "\\$record->contenttype\\contenttype";
            if (class_exists($contenttypeclass)) {
                $context = \context::instance_by_id($record->contextid, MUST_EXIST);
                self::validate_context($context);
                $contenttype = new $contenttypeclass($context);
                $contentclass = "\\$record->contenttype\\content";
                $content = new $contentclass($record);
                // Check capability.
                if ($contenttype->can_manage($content)) {
                    // This content's visibility can be changed.
                    if ($content->set_visibility($params['visibility'])) {
                        $result = true;
                    } else {
                        $warnings[] = [
                            'item' => $params['contentid'],
                            'warningcode' => 'contentvisibilitynotset',
                            'message' => get_string('contentvisibilitynotset', 'core_contentbank')
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
            }
        } catch (\moodle_exception $e) {
            // The content or the context don't exist.
            $warnings[] = [
                'item' => $params['contentid'],
                'warningcode' => 'exception',
                'message' => $e->getMessage()
            ];
        }

        return [
            'result' => $result,
            'warnings' => $warnings
        ];
    }

    /**
     * set_content_visibility return.
     *
     * @since  Moodle 3.11
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }
}

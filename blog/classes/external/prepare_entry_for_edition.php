<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_blog\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use context_system;
use context_course;
use moodle_exception;

/**
 * This is the external method for preparing a blog entry to be edited.
 *
 * @package    core_blog
 * @copyright  2024 Juan Leyva <juan@moodle.com>
 * @category   external
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prepare_entry_for_edition extends external_api {

    /**
     * Describes the external function parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
                'entryid' => new external_value(PARAM_INT, 'The entry id to edit.'),
            ]
        );
    }

    /**
     * Prepare a draft area for editing a blog entry.
     *
     * @param int $entryid The entry id to edit.
     * @throws moodle_exception;
     * @return array draft area information
     */
    public static function execute(int $entryid): array {
        global $USER, $CFG;

        require_once($CFG->dirroot . '/blog/lib.php');
        require_once($CFG->dirroot . '/blog/locallib.php');

        $params = self::validate_parameters(
            self::execute_parameters(),
            [
                'entryid' => $entryid,
            ]
        );

        if (empty($CFG->enableblogs)) {
            throw new moodle_exception('blogdisable', 'blog');
        }

        if (!$entry = new \blog_entry($params['entryid'])) {
            throw new moodle_exception('wrongentryid', 'blog');
        }

        $courseid = !empty($entry->courseid) ? $entry->courseid : SITEID;
        $context = context_course::instance($courseid);
        $sitecontext = context_system::instance();

        self::validate_context($context);

        if (!blog_user_can_edit_entry($entry)) {
            throw new \moodle_exception('cannoteditentryorblog', 'blog');
        }

        [$summaryoptions, $attachmentoptions] = blog_get_editor_options($entry);

        $entry = file_prepare_standard_editor($entry, 'summary', $summaryoptions, $sitecontext, 'blog', 'post', $entry->id);
        $entry = file_prepare_standard_filemanager($entry, 'attachment', $attachmentoptions, $sitecontext,
            'blog', 'attachment', $entry->id);

        // Just get a structure compatible with external API.
        array_walk($attachmentoptions, function(&$item, $key) use(&$attachmentoptions) {
            if (!is_scalar($item)) {
                unset($attachmentoptions[$key]);
                return;
            }
            $item = ['name' => $key, 'value' => $item];
        });
        array_walk($summaryoptions, function(&$item, $key) use(&$summaryoptions) {
            if (!is_scalar($item)) {
                unset($summaryoptions[$key]);
                return;
            }
            $item = ['name' => $key, 'value' => $item];
        });

        $result = [
            'inlineattachmentsid' => $entry->summary_editor['itemid'],
            'attachmentsid' => $entry->attachment_filemanager,
            'areas' => [
                [
                    'area' => 'summary',
                    'options' => $summaryoptions,
                ],
                [
                    'area' => 'attachment',
                    'options' => $attachmentoptions,
                ],
            ],
            'warnings' => [],
        ];
        return $result;
    }

    /**
     * Return.
     *
     * @return external_single_structure
     */
    public static function execute_returns() : external_single_structure {
        return new external_single_structure(
            [
                'inlineattachmentsid' => new external_value(PARAM_INT, 'Draft item id for the text editor.'),
                'attachmentsid' => new external_value(PARAM_INT, 'Draft item id for the file manager.'),
                'areas' => new external_multiple_structure(
                    new external_single_structure(
                        [
                            'area' => new external_value(PARAM_ALPHA, 'File area name.'),
                            'options' => new external_multiple_structure(
                                new external_single_structure(
                                    [
                                        'name' => new external_value(PARAM_RAW, 'Name of option.'),
                                        'value' => new external_value(PARAM_RAW, 'Value of option.'),
                                    ]
                                ), 'Draft file area options.'
                            ),
                        ]
                    ), 'File areas including options'
                ),
                'warnings' => new external_warnings(),
            ]
        );
    }
}

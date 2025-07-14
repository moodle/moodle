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
 * This is the external method for preparing a entry for edition.
 *
 * @package    mod_glossary
 * @since      Moodle 3.10
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_glossary\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/mod/glossary/lib.php');

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;

/**
 * This is the external method for preparing a entry for edition.
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class prepare_entry extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'entryid' => new external_value(PARAM_INT, 'Glossary entry id to update'),
        ]);
    }

    /**
     * Prepare for update the indicated entry from the glossary.
     *
     * @param  int $entryid The entry to update
     * @return array with result and warnings
     * @throws moodle_exception
     */
    public static function execute(int $entryid): array {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), compact('entryid'));
        $id = $params['entryid'];

        // Get and validate the glossary.
        $entry = $DB->get_record('glossary_entries', ['id' => $id], '*', MUST_EXIST);
        list($glossary, $context, $course, $cm) = \mod_glossary_external::validate_glossary($entry->glossaryid);

        // Check permissions.
        mod_glossary_can_update_entry($entry, $glossary, $context, $cm, false);

        list($definitionoptions, $attachmentoptions) = glossary_get_editor_and_attachment_options($course, $context, $entry);

        $entry->aliases = '';
        $entry->categories = [];
        $entry = mod_glossary_prepare_entry_for_edition($entry);
        $entry = file_prepare_standard_editor($entry, 'definition', $definitionoptions, $context, 'mod_glossary', 'entry',
            $entry->id);
        $entry = file_prepare_standard_filemanager($entry, 'attachment', $attachmentoptions, $context, 'mod_glossary', 'attachment',
            $entry->id);

        // Just get a structure compatible with external API.
        array_walk($definitionoptions, function(&$item, $key) use (&$definitionoptions) {
            if (!is_scalar($item)) {
                unset($definitionoptions[$key]);
                return;
            }
            $item = ['name' => $key, 'value' => $item];
        });

        array_walk($attachmentoptions, function(&$item, $key) use (&$attachmentoptions) {
            if (!is_scalar($item)) {
                unset($attachmentoptions[$key]);
                return;
            }
            $item = ['name' => $key, 'value' => $item];
        });

        return [
            'inlineattachmentsid' => $entry->definition_editor['itemid'],
            'attachmentsid' => $entry->attachment_filemanager,
            'areas' => [
                [
                    'area' => 'definition',
                    'options' => $definitionoptions,
                ],
                [
                    'area' => 'attachment',
                    'options' => $attachmentoptions,
                ],
            ],
            'aliases' => explode("\n", trim($entry->aliases)),
            'categories' => $entry->categories,
        ];
    }

    /**
     * Return.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
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
                        )
                    ]
                ), 'File areas including options'
            ),
            'aliases' => new external_multiple_structure(new external_value(PARAM_RAW, 'Alias name.')),
            'categories' => new external_multiple_structure(new external_value(PARAM_INT, 'Category id')),
            'warnings' => new external_warnings(),
        ]);
    }
}

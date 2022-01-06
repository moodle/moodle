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
 * This is the external method for updating a glossary entry.
 *
 * @package    mod_glossary
 * @since      Moodle 3.10
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_glossary\external;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->libdir . '/externallib.php');
require_once($CFG->dirroot . '/mod/glossary/lib.php');

use external_api;
use external_function_parameters;
use external_multiple_structure;
use external_single_structure;
use external_value;
use external_format_value;
use external_warnings;
use core_text;
use moodle_exception;

/**
 * This is the external method for updating a glossary entry.
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class update_entry extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'entryid' => new external_value(PARAM_INT, 'Glossary entry id to update'),
            'concept' => new external_value(PARAM_TEXT, 'Glossary concept'),
            'definition' => new external_value(PARAM_RAW, 'Glossary concept definition'),
            'definitionformat' => new external_format_value('definition'),
            'options' => new external_multiple_structure (
                new external_single_structure(
                    [
                        'name' => new external_value(PARAM_ALPHANUM,
                            'The allowed keys (value format) are:
                            inlineattachmentsid (int); the draft file area id for inline attachments
                            attachmentsid (int); the draft file area id for attachments
                            categories (comma separated int); comma separated category ids
                            aliases (comma separated str); comma separated aliases
                            usedynalink (bool); whether the entry should be automatically linked.
                            casesensitive (bool); whether the entry is case sensitive.
                            fullmatch (bool); whether to match whole words only.'),
                        'value' => new external_value(PARAM_RAW, 'the value of the option (validated inside the function)')
                    ]
                ), 'Optional settings', VALUE_DEFAULT, []
            )
        ]);
    }

    /**
     * Update the indicated glossary entry.
     *
     * @param  int $entryid The entry to update
     * @param string $concept    the glossary concept
     * @param string $definition the concept definition
     * @param int $definitionformat the concept definition format
     * @param array  $options    additional settings
     * @return array with result and warnings
     * @throws moodle_exception
     */
    public static function execute(int $entryid, string $concept, string $definition, int $definitionformat,
            array $options = []): array {

        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), compact('entryid', 'concept', 'definition',
            'definitionformat', 'options'));
        $id = $params['entryid'];

        // Get and validate the glossary entry.
        $entry = $DB->get_record('glossary_entries', ['id' => $id], '*', MUST_EXIST);
        list($glossary, $context, $course, $cm) = \mod_glossary_external::validate_glossary($entry->glossaryid);

        // Check if the user can update the entry.
        mod_glossary_can_update_entry($entry, $glossary, $context, $cm, false);

        // Check for duplicates if the concept changes.
        if (!$glossary->allowduplicatedentries &&
                core_text::strtolower($entry->concept) != core_text::strtolower(trim($params['concept']))) {

            if (glossary_concept_exists($glossary, $params['concept'])) {
                throw new moodle_exception('errconceptalreadyexists', 'glossary');
            }
        }

        // Prepare the entry object.
        $entry->aliases = '';
        $entry = mod_glossary_prepare_entry_for_edition($entry);
        $entry->concept = $params['concept'];
        $entry->definition_editor = [
            'text' => $params['definition'],
            'format' => $params['definitionformat'],
        ];
        // Options.
        foreach ($params['options'] as $option) {
            $name = trim($option['name']);
            switch ($name) {
                case 'inlineattachmentsid':
                    $entry->definition_editor['itemid'] = clean_param($option['value'], PARAM_INT);
                    break;
                case 'attachmentsid':
                    $entry->attachment_filemanager = clean_param($option['value'], PARAM_INT);
                    break;
                case 'categories':
                    $entry->categories = clean_param($option['value'], PARAM_SEQUENCE);
                    $entry->categories = explode(',', $entry->categories);
                    break;
                case 'aliases':
                    $entry->aliases = clean_param($option['value'], PARAM_NOTAGS);
                    // Convert to the expected format.
                    $entry->aliases = str_replace(",", "\n", $entry->aliases);
                    break;
                case 'usedynalink':
                case 'casesensitive':
                case 'fullmatch':
                    // Only allow if linking is enabled.
                    if ($glossary->usedynalink) {
                        $entry->{$name} = clean_param($option['value'], PARAM_BOOL);
                    }
                    break;
                default:
                    throw new moodle_exception('errorinvalidparam', 'webservice', '', $name);
            }
        }

        $entry = glossary_edit_entry($entry, $course, $cm, $glossary, $context);

        return [
            'result' => true,
            'warnings' => [],
        ];
    }

    /**
     * Return.
     *
     * @return external_single_structure
     */
    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'result' => new external_value(PARAM_BOOL, 'The update result'),
            'warnings' => new external_warnings()
        ]);
    }
}

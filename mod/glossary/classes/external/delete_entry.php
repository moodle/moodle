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
 * This is the external method for deleting a content.
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
use external_warnings;

/**
 * This is the external method for deleting a content.
 *
 * @copyright  2020 Juan Leyva <juan@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete_entry extends external_api {
    /**
     * Parameters.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'entryid' => new external_value(PARAM_INT, 'Glossary entry id to delete'),
        ]);
    }

    /**
     * Delete the indicated entry from the glossary.
     *
     * @param  int $entryid The entry to delete
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

        // Check and delete.
        mod_glossary_can_delete_entry($entry, $glossary, $context, false);
        mod_glossary_delete_entry($entry, $glossary, $cm, $context, $course);

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
            'result' => new external_value(PARAM_BOOL, 'The processing result'),
            'warnings' => new external_warnings()
        ]);
    }
}

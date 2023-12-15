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
 * Callback and other methods for viewquestionname plugin.
 *
 * @package    qbank_viewquestionname
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_external\external_api;
use core_external\external_description;
use core_external\external_files;
use core_external\external_format_value;
use core_external\external_function_parameters;
use core_external\external_multiple_structure;
use core_external\external_settings;
use core_external\external_single_structure;
use core_external\external_value;
use core_external\external_warnings;
use core_external\restricted_context_exception;
use core_external\util;
use core_external\util as external_util;

/**
 * In place editing callback for question name.
 *
 * @param string $itemtype type of the item, questionname for this instance
 * @param int $itemid question id to change the title
 * @param string $newvalue the changed question title
 * @return \core\output\inplace_editable
 */
function qbank_viewquestionname_inplace_editable ($itemtype, $itemid, $newvalue) : \core\output\inplace_editable {
    if ($itemtype === 'questionname') {
        global $CFG, $DB;
        require_once($CFG->libdir . '/questionlib.php');
        // Get the question data and to confirm any invalud itemid is not passed.
        $record = $DB->get_record('question', ['id' => $itemid], '*', MUST_EXIST);

        // Load question data from question engine.
        $question = question_bank::load_question($record->id);
        question_require_capability_on($question, 'edit');

        // Context validation.
        external_api::validate_context(context::instance_by_id($question->contextid));

        // Now update the question data.
        $record->name = $newvalue;
        $DB->update_record('question', $record);

        // Trigger events.
        question_bank::notify_question_edited($record->id);
        $event = \core\event\question_updated::create_from_question_instance($question);
        $event->trigger();

        // Prepare the element for the output.
        return new \qbank_viewquestionname\output\questionname($record);
    }
}

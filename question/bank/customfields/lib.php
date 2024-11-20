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
 * Helper functions and callbacks.
 *
 * @package    qbank_customfields
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Comment content for callbacks.
 *
 * @param question_definition $question
 * @param int $courseid
 * @return string
 */
function qbank_customfields_preview_display(question_definition $question, int $courseid): string {
    // Prepare custom fields data.
    $customfieldhandler = qbank_customfields\customfield\question_handler::create();
    $catfielddata = $customfieldhandler->get_categories_fields_data($question->id);
    return $customfieldhandler->display_custom_categories_fields($catfielddata);
}

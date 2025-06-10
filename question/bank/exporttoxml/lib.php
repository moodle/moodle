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
 * @package    qbank_exporttoxml
 * @copyright  2023 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core\plugininfo\qbank;
use qbank_exporttoxml\helper;

/**
 * Callback to add content to the question preview screen.
 *
 * @param question_definition $question the question being previewed.
 * @param int $courseid the course id.
 * @return string HTML to add to the question preview screen.
 */
function qbank_exporttoxml_preview_display(question_definition $question, int $courseid): string {
    if (!qbank::is_plugin_enabled('qbank_exporttoxml')) {
        return '';
    }

    if (!question_has_capability_on($question, 'view')) {
        return '';
    }

    $exporturl = helper::question_get_export_single_question_url($question);
    return html_writer::div(html_writer::link($exporturl, get_string('exportonequestion', 'question')));
}

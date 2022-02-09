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
 * @package    qbank_viewcreator
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Edit page callback for information.
 *
 * @param object $question
 * @return string
 */
function qbank_viewcreator_edit_form_display($question): string {
    global $DB, $PAGE;
    $versiondata = [];
    $questionversion = $DB->get_record('question_versions', ['questionid' => $question->id])->version;
    $versiondata['versionnumber'] = $questionversion;
    if (!empty($question->createdby)) {
        $a = new stdClass();
        $a->time = userdate($question->timecreated);
        $a->user = fullname($DB->get_record('user', ['id' => $question->createdby]));
        $versiondata['createdby'] = get_string('created', 'question') . ' ' .
                                    get_string('byandon', 'question', $a);
    }
    return $PAGE->get_renderer('qbank_viewcreator')->render_version_info($versiondata);

}

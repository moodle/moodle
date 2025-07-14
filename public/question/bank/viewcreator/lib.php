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
    global $DB, $PAGE, $OUTPUT;
    $question = question_bank::load_question($question->id);

    $versiondata = [];
    $versioninfo = new \core_question\output\question_version_info($question, true);
    $versiondata['versionnumber'] = $versioninfo->export_for_template($OUTPUT)['versioninfo'];

    // Currently the history only display the question versions for just only default category.
    // To display question in the other category.
    // So we need to add filter param so that we can display the question in different category.
    $filterparam = json_encode([
        'category' => [
            'jointype' => 1,
            'values' => [$question->category],
            'filteroptions' => ['includesubcategories' => false],
        ],
    ]);
    // We need a return url param so that click close button on history page should redirect back to edit question page.
    // Set params filter to returnurl so that when we use the move feature  It will not cause any error.
    $returnurl = $PAGE->url;
    $returnurl->param('filter', $filterparam);
    $versiondata['historyurl'] = new moodle_url('/question/bank/history/history.php', [
        'entryid' => $question->questionbankentryid,
        'returnurl' => $returnurl,
        'courseid' => $PAGE->course->id,
        'filter' => $filterparam,
        'cmid' => $PAGE->url->param('cmid'),
    ]);

    if (!empty($question->createdby)) {
        $a = new stdClass();
        $a->time = userdate($question->timecreated);
        $a->user = fullname($DB->get_record('user', ['id' => $question->createdby]));
        $versiondata['createdby'] = get_string('created', 'question') . ' ' .
                                    get_string('byandon', 'question', $a);
    }
    return $PAGE->get_renderer('qbank_viewcreator')->render_version_info($versiondata);

}

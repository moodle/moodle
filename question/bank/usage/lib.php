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
 * @package    qbank_usage
 * @copyright  2021 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Question usage fragment callback.
 *
 * @param array $args
 * @return string rendered output
 */
function qbank_usage_output_fragment_question_usage(array $args): string {
    global $USER, $PAGE, $CFG, $DB;
    require_once($CFG->dirroot . '/question/engine/bank.php');
    $displaydata = [];

    $question = question_bank::load_question($args['questionid']);
    $quba = question_engine::make_questions_usage_by_activity('core_question_preview', context_user::instance($USER->id));

    $options = new \qbank_previewquestion\question_preview_options($question);
    $quba->set_preferred_behaviour($options->behaviour);
    $slot = $quba->add_question($question, $options->maxmark);
    $quba->start_question($slot, $options->variant);
    $transaction = $DB->start_delegated_transaction();
    question_engine::save_questions_usage_by_activity($quba);
    $transaction->allow_commit();
    $displaydata['question'] = $quba->render_question($slot, $options, '1');

    $specificversion = clean_param($args['specificversion'] ?? false, PARAM_BOOL);
    $questionusagetable = new \qbank_usage\tables\question_usage_table('question_usage_table', $question, $specificversion);
    $questionusagetable->baseurl = new moodle_url('');
    if (isset($args['querystring'])) {
        $querystring = preg_replace('/^\?/', '', $args['querystring']);
        $params = [];
        parse_str($querystring, $params);
        if (isset($params['page'])) {
            $questionusagetable->currpage = $params['page'];
        }
    }
    $displaydata['tablesql'] = $questionusagetable->export_for_fragment();
    $selector = \core_question\output\question_version_selection::make_for_question('question_usage_version_dropdown',
        $args['questionid']);
    $qbankrenderer = $PAGE->get_renderer('core_question', 'bank');
    $displaydata['versionselection'] = $selector->export_for_template($qbankrenderer);

    return $PAGE->get_renderer('qbank_usage')->render_usage_fragment($displaydata);
}

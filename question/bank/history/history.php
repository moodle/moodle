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
 * Question history preview.
 *
 * @package    qbank_history
 * @copyright  2022 Catalyst IT Australia Pty Ltd
 * @author     Safat Shahin <safatshahin@catalyst-au.net>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->dirroot . '/question/editlib.php');

require_login();
core_question\local\bank\helper::require_plugin_enabled('qbank_history');

$entryid = required_param('entryid', PARAM_INT);
$returnurl = required_param('returnurl', PARAM_LOCALURL);

list($thispageurl, $contexts, $cmid, $cm, $module, $pagevars) =
        question_edit_setup('questions', '/question/bank/history/history.php');
$pagevars['entryid'] = $entryid;
$pagevars['returnurl'] = $returnurl;
$url = new moodle_url($thispageurl, ['entryid' => $entryid, 'returnurl' => $returnurl]);
$PAGE->set_url($url);

// Additional param to differentiate with other question bank view.
$extraparams['entryid'] = $entryid;
$extraparams['returnurl'] = $returnurl;
$questionbank = new \qbank_history\question_history_view($contexts, $url, $COURSE, null, $pagevars, $extraparams);

$streditingquestions = get_string('history_header', 'qbank_history');
$PAGE->set_title($streditingquestions);
$PAGE->set_heading($streditingquestions);
$context = $contexts->lowest();
$PAGE->set_context($context);
$PAGE->navbar->add(get_string('question'), new moodle_url($returnurl));
$PAGE->navbar->add($streditingquestions, $url);

echo $OUTPUT->header();
// Print the question area.
$questionbank->display();
echo $OUTPUT->footer();

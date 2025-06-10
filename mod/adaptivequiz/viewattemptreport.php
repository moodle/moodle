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
 * Adaptive quiz view attempt report script.
 *
 * @copyright  2013 onwards Remote-Learner {@link http://www.remote-learner.ca/}
 * @copyright  2022 onwards Vitaly Potenko <potenkov@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->dirroot.'/mod/adaptivequiz/locallib.php');

use mod_adaptivequiz\local\report\individual_user_attempts\filter as user_attempts_table;
use mod_adaptivequiz\local\report\questions_difficulty_range;
use mod_adaptivequiz\local\report\individual_user_attempts\table as individual_user_attempts_table;

$id = required_param('cmid', PARAM_INT);
$userid = required_param('userid', PARAM_INT);

$cm = get_coursemodule_from_id('adaptivequiz', $id, 0, false, MUST_EXIST);
$adaptivequiz = $DB->get_record('adaptivequiz', ['id' => $cm->instance], '*', MUST_EXIST);
$course = $DB->get_record('course', ['id' => $cm->course], '*', MUST_EXIST);
$user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);

require_login($course, true, $cm);

$context = context_module::instance($cm->id);

require_capability('mod/adaptivequiz:viewreport', $context);

$PAGE->set_context($context);
$PAGE->set_url('/mod/adaptivequiz/viewattemptreport.php', ['cmid' => $cm->id, 'userid' => $user->id]);

$a = new stdClass();
$a->quizname = format_string($adaptivequiz->name);
$a->username = fullname($user);
$title = get_string('reportindividualuserattemptpageheading', 'adaptivequiz', $a);
$PAGE->set_title($title);

$PAGE->set_heading(format_string($course->fullname));

/** @var mod_adaptivequiz_renderer $renderer */
$renderer = $PAGE->get_renderer('mod_adaptivequiz');

$header = $renderer->print_header();
$footer = $renderer->print_footer();

echo $header;
echo $renderer->heading($title);

$attemptstable = new individual_user_attempts_table(
    $renderer,
    user_attempts_table::from_vars($cm->instance, $user->id),
    $PAGE->url,
    questions_difficulty_range::from_activity_instance($adaptivequiz),
    $cm->id
);
$attemptstable->out(20, false);

echo $footer;

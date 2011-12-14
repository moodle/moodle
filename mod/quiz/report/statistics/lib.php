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
 * Standard plugin entry points of the quiz statistics report.
 *
 * @package    quiz
 * @subpackage statistics
 * @copyright  2011 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * Serve questiontext files in the question text when they are displayed in this report.
 * @param context $context the context
 * @param int $questionid the question id
 * @param array $args remaining file args
 * @param bool $forcedownload
 */
function quiz_statistics_questiontext_preview_pluginfile($context, $questionid, $args, $forcedownload) {
    global $CFG;
    require_once($CFG->dirroot . '/mod/quiz/locallib.php');

    list($context, $course, $cm) = get_context_info_array($context->id);
    require_login($course, false, $cm);

    // Assume only trusted people can see this report. There is no real way to
    // validate questionid, becuase of the complexity of random quetsions.
    require_capability('quiz/statistics:view', $context);

    question_send_questiontext_file($questionid, $args, $forcedownload);
}

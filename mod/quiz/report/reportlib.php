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
 * Helper functions for the quiz reports.
 *
 * @package    mod
 * @subpackage quiz
 * @copyright  2008 Jamie Pratt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/mod/quiz/lib.php');
require_once($CFG->libdir . '/filelib.php');

define('QUIZ_REPORT_DEFAULT_PAGE_SIZE', 30);
define('QUIZ_REPORT_DEFAULT_GRADING_PAGE_SIZE', 10);

define('QUIZ_REPORT_ATTEMPTS_ALL', 0);
define('QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH_NO', 1);
define('QUIZ_REPORT_ATTEMPTS_STUDENTS_WITH', 2);
define('QUIZ_REPORT_ATTEMPTS_ALL_STUDENTS', 3);

/**
 * Takes an array of objects and constructs a multidimensional array keyed by
 * the keys it finds on the object.
 * @param array $datum an array of objects with properties on the object
 * including the keys passed as the next param.
 * @param array $keys Array of strings with the names of the properties on the
 * objects in datum that you want to index the multidimensional array by.
 * @param bool $keysunique If there is not only one object for each
 * combination of keys you are using you should set $keysunique to true.
 * Otherwise all the object will be added to a zero based array. So the array
 * returned will have count($keys) + 1 indexs.
 * @return array multidimensional array properly indexed.
 */
function quiz_report_index_by_keys($datum, $keys, $keysunique = true) {
    if (!$datum) {
        return array();
    }
    $key = array_shift($keys);
    $datumkeyed = array();
    foreach ($datum as $data) {
        if ($keys || !$keysunique) {
            $datumkeyed[$data->{$key}][]= $data;
        } else {
            $datumkeyed[$data->{$key}]= $data;
        }
    }
    if ($keys) {
        foreach ($datumkeyed as $datakey => $datakeyed) {
            $datumkeyed[$datakey] = quiz_report_index_by_keys($datakeyed, $keys, $keysunique);
        }
    }
    return $datumkeyed;
}

function quiz_report_unindex($datum) {
    if (!$datum) {
        return $datum;
    }
    $datumunkeyed = array();
    foreach ($datum as $value) {
        if (is_array($value)) {
            $datumunkeyed = array_merge($datumunkeyed, quiz_report_unindex($value));
        } else {
            $datumunkeyed[] = $value;
        }
    }
    return $datumunkeyed;
}

/**
 * Get the slots of real questions (not descriptions) in this quiz, in order.
 * @param object $quiz the quiz.
 * @return array of slot => $question object with fields
 *      ->slot, ->id, ->maxmark, ->number, ->length.
 */
function quiz_report_get_significant_questions($quiz) {
    global $DB;

    $questionids = quiz_questions_in_quiz($quiz->questions);
    if (empty($questionids)) {
        return array();
    }

    list($usql, $params) = $DB->get_in_or_equal(explode(',', $questionids));
    $params[] = $quiz->id;
    $questions = $DB->get_records_sql("
SELECT
    q.id,
    q.length,
    qqi.grade AS maxmark

FROM {question} q
JOIN {quiz_question_instances} qqi ON qqi.question = q.id

WHERE
    q.id $usql AND
    qqi.quiz = ? AND
    length > 0", $params);

    $qsbyslot = array();
    $number = 1;
    foreach (explode(',', $questionids) as $key => $id) {
        if (!array_key_exists($id, $questions)) {
            continue;
        }

        $slot = $key + 1;
        $question = $questions[$id];
        $question->slot = $slot;
        $question->number = $number;

        $qsbyslot[$slot] = $question;

        $number += $question->length;
    }

    return $qsbyslot;
}

/**
 * Given the quiz grading method return sub select sql to find the id of the
 * one attempt that will be graded for each user. Or return
 * empty string if all attempts contribute to final grade.
 */
function quiz_report_qm_filter_select($quiz, $quizattemptsalias = 'quiza') {
    if ($quiz->attempts == 1) { // Only one attempt allowed on this quiz
        return '';
    }

    switch ($quiz->grademethod) {
        case QUIZ_GRADEHIGHEST :
            return "$quizattemptsalias.id = (
                    SELECT MIN(qa2.id)
                    FROM {quiz_attempts} qa2
                    WHERE qa2.quiz = $quizattemptsalias.quiz AND
                        qa2.userid = $quizattemptsalias.userid AND
                        COALESCE(qa2.sumgrades, 0) = (
                            SELECT MAX(COALESCE(qa3.sumgrades, 0))
                            FROM {quiz_attempts} qa3
                            WHERE qa3.quiz = $quizattemptsalias.quiz AND
                                qa3.userid = $quizattemptsalias.userid
                        )
                    )";

        case QUIZ_GRADEAVERAGE :
            return '';

        case QUIZ_ATTEMPTFIRST :
            return "$quizattemptsalias.id = (
                    SELECT MIN(qa2.id)
                    FROM {quiz_attempts} qa2
                    WHERE qa2.quiz = $quizattemptsalias.quiz AND
                        qa2.userid = $quizattemptsalias.userid)";

        case QUIZ_ATTEMPTLAST :
            return "$quizattemptsalias.id = (
                    SELECT MAX(qa2.id)
                    FROM {quiz_attempts} qa2
                    WHERE qa2.quiz = $quizattemptsalias.quiz AND
                        qa2.userid = $quizattemptsalias.userid)";
    }
}

/**
 * Get the nuber of students whose score was in a particular band for this quiz.
 * @param number $bandwidth the width of each band.
 * @param int $bands the number of bands
 * @param int $quizid the quiz id.
 * @param array $userids list of user ids.
 * @return array band number => number of users with scores in that band.
 */
function quiz_report_grade_bands($bandwidth, $bands, $quizid, $userids = array()) {
    global $DB;

    if ($userids) {
        list($usql, $params) = $DB->get_in_or_equal($userids, SQL_PARAMS_NAMED, 'u');
        $usql = "qg.userid $usql AND";
    } else {
        $usql = '';
        $params = array();
    }
    $sql = "
SELECT band, COUNT(1)

FROM (
    SELECT FLOOR(qg.grade / :bandwidth) AS band
      FROM {quiz_grades} qg
     WHERE $usql qg.quiz = :quizid
) subquery

GROUP BY
    band

ORDER BY
    band";

    $params['quizid'] = $quizid;
    $params['bandwidth'] = $bandwidth;

    $data = $DB->get_records_sql_menu($sql, $params);

    //need to create array elements with values 0 at indexes where there is no element
    $data =  $data + array_fill(0, $bands+1, 0);
    ksort($data);

    //place the maximum (prefect grade) into the last band i.e. make last
    //band for example 9 <= g <=10 (where 10 is the perfect grade) rather than
    //just 9 <= g <10.
    $data[$bands - 1] += $data[$bands];
    unset($data[$bands]);

    return $data;
}

function quiz_report_highlighting_grading_method($quiz, $qmsubselect, $qmfilter) {
    if ($quiz->attempts == 1) {
        return '<p>' . get_string('onlyoneattemptallowed', 'quiz_overview') . '</p>';

    } else if (!$qmsubselect) {
        return '<p>' . get_string('allattemptscontributetograde', 'quiz_overview') . '</p>';

    } else if ($qmfilter) {
        return '<p>' . get_string('showinggraded', 'quiz_overview') . '</p>';

    } else {
        return '<p>' . get_string('showinggradedandungraded', 'quiz_overview',
                '<span class="gradedattempt">' . quiz_get_grading_option_name($quiz->grademethod) .
                '</span>') . '</p>';
    }
}

/**
 * Get the feedback text for a grade on this quiz. The feedback is
 * processed ready for display.
 *
 * @param float $grade a grade on this quiz.
 * @param int $quizid the id of the quiz object.
 * @return string the comment that corresponds to this grade (empty string if there is not one.
 */
function quiz_report_feedback_for_grade($grade, $quizid, $context) {
    global $DB;

    static $feedbackcache = array();

    if (!isset($feedbackcache[$quizid])) {
        $feedbackcache[$quizid] = $DB->get_records('quiz_feedback', array('quizid' => $quizid));
    }

    $feedbacks = $feedbackcache[$quizid];
    $feedbackid = 0;
    $feedbacktext = '';
    $feedbacktextformat = FORMAT_MOODLE;
    foreach ($feedbacks as $feedback) {
        if ($feedback->mingrade <= $grade && $grade < $feedback->maxgrade) {
            $feedbackid = $feedback->id;
            $feedbacktext = $feedback->feedbacktext;
            $feedbacktextformat = $feedback->feedbacktextformat;
            break;
        }
    }

    // Clean the text, ready for display.
    $formatoptions = new stdClass();
    $formatoptions->noclean = true;
    $feedbacktext = file_rewrite_pluginfile_urls($feedbacktext, 'pluginfile.php',
            $context->id, 'mod_quiz', 'feedback', $feedbackid);
    $feedbacktext = format_text($feedbacktext, $feedbacktextformat, $formatoptions);

    return $feedbacktext;
}

/**
 * Format a number as a percentage out of $quiz->sumgrades
 * @param number $rawgrade the mark to format.
 * @param object $quiz the quiz settings
 * @param bool $round whether to round the results ot $quiz->decimalpoints.
 */
function quiz_report_scale_summarks_as_percentage($rawmark, $quiz, $round = true) {
    if ($quiz->sumgrades == 0) {
        return '';
    }
    if (!is_numeric($rawmark)) {
        return $rawmark;
    }

    $mark = $rawmark * 100 / $quiz->sumgrades;
    if ($round) {
        $mark = quiz_format_grade($quiz, $mark);
    }
    return $mark . '%';
}

/**
 * Returns an array of reports to which the current user has access to.
 * @return array reports are ordered as they should be for display in tabs.
 */
function quiz_report_list($context) {
    global $DB;
    static $reportlist = null;
    if (!is_null($reportlist)) {
        return $reportlist;
    }

    $reports = $DB->get_records('quiz_reports', null, 'displayorder DESC', 'name, capability');
    $reportdirs = get_plugin_list('quiz');

    // Order the reports tab in descending order of displayorder
    $reportcaps = array();
    foreach ($reports as $key => $report) {
        if (array_key_exists($report->name, $reportdirs)) {
            $reportcaps[$report->name] = $report->capability;
        }
    }

    // Add any other reports, which are on disc but not in the DB, on the end
    foreach ($reportdirs as $reportname => $notused) {
        if (!isset($reportcaps[$reportname])) {
            $reportcaps[$reportname] = null;
        }
    }
    $reportlist = array();
    foreach ($reportcaps as $name => $capability) {
        if (empty($capability)) {
            $capability = 'mod/quiz:viewreports';
        }
        if (has_capability($capability, $context)) {
            $reportlist[] = $name;
        }
    }
    return $reportlist;
}

/**
 * Create a filename for use when downloading data from a quiz report. It is
 * expected that this will be passed to flexible_table::is_downloading, which
 * cleans the filename of bad characters and adds the file extension.
 * @param string $report the type of report.
 * @param string $courseshortname the course shortname.
 * @param string $quizname the quiz name.
 * @return string the filename.
 */
function quiz_report_download_filename($report, $courseshortname, $quizname) {
    return $courseshortname . '-' . format_string($quizname, true) . '-' . $report;
}

/**
 * Get the default report for the current user.
 * @param object $context the quiz context.
 */
function quiz_report_default_report($context) {
    return reset(quiz_report_list($context));
}

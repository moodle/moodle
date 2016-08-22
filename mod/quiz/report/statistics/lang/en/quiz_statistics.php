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
 * Strings for component 'quiz_statistics', language 'en', branch 'MOODLE_20_STABLE'
 *
 * @package   quiz_statistics
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['actualresponse'] = 'Actual response';
$string['allattempts'] = 'all attempts';
$string['allattemptsavg'] = 'Average grade of all attempts';
$string['allattemptscount'] = 'Total number of complete graded attempts';
$string['analysisnameonly'] = '"{$a->name}"';
$string['analysisno'] = '({$a->number}) "{$a->name}"';
$string['analysisnovariant'] = '({$a->number}) "{$a->name}" variant {$a->variant}';
$string['analysisofresponses'] = 'Analysis of responses';
$string['analysisofresponsesfor'] = 'Analysis of responses for {$a}';
$string['analysisvariant'] = '"{$a->name}" variant {$a->variant}';
$string['attempts'] = 'Attempts';
$string['attemptsall'] = 'all attempts';
$string['attemptsfirst'] = 'first attempt';
$string['backtoquizreport'] = 'Back to main statistics report page.';
$string['calculatefrom'] = 'Calculate statistics from';
$string['calculatingallstats'] = 'Calculating statistics for quiz, questions and analysing response data';
$string['cic'] = 'Coefficient of internal consistency (for {$a})';
$string['completestatsfilename'] = 'completestats';
$string['count'] = 'Count';
$string['counttryno'] = 'Count Try {$a}';
$string['coursename'] = 'Course name';
$string['detailedanalysis'] = 'More detailed analysis of the responses to this question';
$string['detailedanalysisforvariant'] = 'More detailed analysis of the responses to variant {$a} of this question';
$string['discrimination_index'] = 'Discrimination index';
$string['discriminative_efficiency'] = 'Discriminative efficiency';
$string['downloadeverything'] = 'Download full report as';
$string['duration'] = 'Open for';
$string['effective_weight'] = 'Effective weight';
$string['errordeleting'] = 'Error deleting old {$a} records.';
$string['errormedian'] = 'Error fetching median';
$string['errorpowerquestions'] = 'Error fetching data to calculate variance for question grades';
$string['errorpowers'] = 'Error fetching data to calculate variance for quiz grades';
$string['errorrandom'] = 'Error getting sub item data';
$string['errorratio'] = 'Error ratio (for {$a})';
$string['errorstatisticsquestions'] = 'Error fetching data to calculate statistics for question grades';
$string['facility'] = 'Facility index';
$string['firstattempts'] = 'first attempts';
$string['firstattemptsavg'] = 'Average grade of first attempts';
$string['firstattemptscount'] = 'Number of complete graded first attempts';
$string['frequency'] = 'Frequency';
$string['highestattempts'] = 'highest graded attempt';
$string['highestattemptsavg'] = 'Average grade of highest graded attempts';
$string['intended_weight'] = 'Intended weight';
$string['kurtosis'] = 'Score distribution kurtosis (for {$a})';
$string['lastattempts'] = 'last attempt';
$string['lastattemptsavg'] = 'Average grade of last attempts';
$string['lastcalculated'] = 'Last calculated {$a->lastcalculated} ago there have been {$a->count} attempts since then.';
$string['maximumfacility'] = 'Maximum facility';
$string['median'] = 'Median grade (for {$a})';
$string['medianfacility'] = 'Median facility';
$string['minimumfacility'] = 'Minimum facility';
$string['modelresponse'] = 'Model response';
$string['nameforvariant'] = 'Variant {$a->variant} of {$a->name}';
$string['negcovar'] = 'Negative covariance of grade with total attempt grade';
$string['negcovar_help'] = 'This question\'s grade for this set of attempts on the quiz varies in an opposite way to the overall attempt grade. This means overall attempt grade tends to be below average when the grade for this question is above average and vice-versa.

Our equation for effective question weight cannot be calculated in this case. The calculations for effective question weight for other questions in this quiz are the effective question weight for these questions if the highlighted questions with a negative covariance are given a maximum grade of zero.

If you edit a quiz and give these question(s) with negative covariance a max grade of zero then the effective question weight of these questions will be zero and the real effective question weight of other questions will be as calculated now.';
$string['nogradedattempts'] = 'No attempts have been made at this quiz, or all attempts have questions that need manual grading.';
$string['nostudentsingroup'] = 'There are no students in this group yet';
$string['optiongrade'] = 'Partial credit';
$string['partofquestion'] = 'Part of question';
$string['pluginname'] = 'Statistics';
$string['position'] = 'Position';
$string['positions'] = 'Position(s)';
$string['questioninformation'] = 'Question information';
$string['questionname'] = 'Question name';
$string['questionnumber'] = 'Q#';
$string['questionstatistics'] = 'Question statistics';
$string['questionstatsfilename'] = 'questionstats';
$string['questiontype'] = 'Question type';
$string['quizinformation'] = 'Quiz information';
$string['quizname'] = 'Quiz name';
$string['quizoverallstatistics'] = 'Quiz overall statistics';
$string['quizstructureanalysis'] = 'Quiz structure analysis';
$string['random_guess_score'] = 'Random guess score';
$string['recalculatenow'] = 'Recalculate now';
$string['reportsettings'] = 'Statistics calculation settings';
$string['response'] = 'Response';
$string['slotstructureanalysis'] = 'Structural analysis for question number {$a}';
$string['skewness'] = 'Score distribution skewness (for {$a})';
$string['standarddeviation'] = 'Standard deviation (for {$a})';
$string['standarddeviationq'] = 'Standard deviation';
$string['standarderror'] = 'Standard error (for {$a})';
$string['statistics'] = 'Statistics';
$string['statistics:componentname'] = 'Quiz statistics report';
$string['statisticsreport'] = 'Statistics report';
$string['statisticsreportgraph'] = 'Statistics for question positions';
$string['statistics:view'] = 'View statistics report';
$string['statsfor'] = 'Quiz statistics (for {$a})';
$string['variant'] = 'Variant';
$string['whichtries'] = 'Analyze responses for';


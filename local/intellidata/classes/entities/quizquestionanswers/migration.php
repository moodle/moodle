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
 * Class for migration Quiz question answers.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\quizquestionanswers;
use local_intellidata\helpers\DBHelper;

/**
 * Class for migration Quiz question answers.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migration extends \local_intellidata\entities\migration {
    /** @var string */
    public $entity = '\local_intellidata\entities\quizquestionanswers\quizquestionanswer';
    /** @var string */
    public $table = 'quiz_attempts';
    /** @var string */
    public $tablealias  = 'qa';

    /**
     * Prepare SQL query to get data.
     *
     * @param false $count
     * @param null $condition
     * @param array $conditionparams
     * @param null $timestart
     * @return array
     */
    public function get_sql($count = false, $condition = null, $conditionparams = [], $timestart = null) {

        list($rownumber, $rownumberselect) = DBHelper::get_row_number();

        $sqlwhere1 = $sqlwhere2 = '';
        if ($timestart > 0) {
            $sqlwhere1 = " AND qas.timecreated > $timestart ";
            $sqlwhere2 = " AND qa.timemodified > $timestart ";
        }

        if ($count) {
            $sql = "SELECT COUNT(qa.id) as recordscount
                    FROM {quiz_attempts} qa
                        JOIN {question_attempts} qua ON qua.questionusageid = qa.uniqueid
                        JOIN {question_attempt_steps} qas ON qas.questionattemptid = qua.id
                    WHERE qas.fraction IS NOT NULL $sqlwhere2";
            $params = [];
        } else {
            $sql = "SELECT $rownumber AS uid, qa.id, qa.id AS attemptid, qua.questionid,
                           qas.state, a.value, qas.fraction, qa.timemodified
                FROM $rownumberselect {quiz_attempts} qa
                JOIN {question_attempts} qua ON qua.questionusageid = qa.uniqueid
                JOIN {question_attempt_steps} qas ON qas.questionattemptid = qua.id
           LEFT JOIN (SELECT qas.questionattemptid, qasd.value
                        FROM {question_attempt_step_data} qasd
                        JOIN {question_attempt_steps} qas ON qas.id = qasd.attemptstepid
                        WHERE qasd.name=:name $sqlwhere1) a ON a.questionattemptid = qas.questionattemptid
               WHERE qas.fraction IS NOT NULL $sqlwhere2";

            $params = [
                'name' => 'answer',
            ];
        }

        if ($condition) {
            $sql .= " AND " . $condition;
            $params += $conditionparams;
        }

        if (!$count) {
            $sql .= " ORDER BY qa.id";
        }

        return [$sql, $params];
    }
}

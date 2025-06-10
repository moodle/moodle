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
 * Class for migration Quiz question attempts migration.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\quizquestionanswers;

/**
 * Class for migration Quiz question attempts migration.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quamigration extends \local_intellidata\entities\migration {
    /** @var string */
    public $entity = '\local_intellidata\entities\quizquestionanswers\quizquestionattempts';
    /** @var string */
    public $table = 'question_attempts';
    /** @var string */
    public $tablealias = 'qua';

    /**
     * Prepare SQL query to get data from DB.
     *
     * @param false $count
     * @param null $condition
     * @param array $conditionparams
     * @param null $timestart
     * @return array
     */
    public function get_sql($count = false, $condition = null, $conditionparams = [], $timestart = null) {

        $alias = $this->tablealias;
        $select = ($count) ?
            "SELECT COUNT($alias.id) as recordscount" :
            "SELECT $alias.id, qa.id AS attemptid, $alias.questionid, qa.uniqueid,
                    qa.timemodified, $alias.maxmark, $alias.slot, $alias.responsesummary";

        $sql = "$select
                FROM {" . $this->table . "} $alias
           LEFT JOIN {quiz_attempts} qa ON qa.uniqueid = $alias.questionusageid";

        return $this->set_condition($condition, $conditionparams, $sql, []);
    }
}

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
 * Class for migration Assignments Submissions.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\assignments;

use local_intellidata\helpers\DBManagerHelper;

/**
 * Class for migration Assignments Submissions.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migration extends \local_intellidata\entities\migration {
    /** @var string */
    public $entity = '\local_intellidata\entities\assignments\submission';
    /** @var string */
    public $eventname = '\mod_assign\event\submission_created';
    /** @var string */
    public $table = 'assign_submission';
    /** @var string */
    public $tablealias  = 's';

    /**
     * Prepare SQL query to get data from DB.
     *
     * @param false $count
     * @param null $condition
     * @param array $conditionparams
     * @return array
     */
    public function get_sql($count = false, $condition = null, $conditionparams = []) {
        $where = 's.id > 0';
        $xmltables = DBManagerHelper::get_install_xml_tables();

        $select = $join = $plugins = [];
        foreach ($xmltables as $xmltable) {
            if (isset($xmltable['plugintype']) && !in_array($xmltable['plugintype'], $plugins)
                && $xmltable['plugintype'] == 'assignsubmission') {
                $select[] = "CASE WHEN MAX({$xmltable['name']}.id) IS NOT NULL THEN '{$xmltable['plugin']}' ELSE '' END";
                $join[] = "LEFT JOIN {{$xmltable['name']}} {$xmltable['name']} on {$xmltable['name']}.submission = s.id";

                $plugins[] = $xmltable['plugintype'];
            }
        }

        if (!empty($select)) {
            $select = implode(",',',", $select);
            $join = implode(' ', $join);
            $innerwhere = " WHERE $where ";

            if ($condition) {
                $innerwhere .= " AND " . $this->apply_tablealias($condition);
                foreach ($conditionparams as $key => $value) {
                    $newkey = $key . '_inner';
                    $innerwhere = str_replace(':' . $key, ':' . $newkey, $innerwhere);
                    $conditionparams[$newkey] = $value;
                }
            }

            $submissionssql = "SELECT
                        s.id AS submission_id,
                        CONCAT($select, '') AS submission_type
                    FROM {assign_submission} s
                         $join
                    $innerwhere
                    GROUP BY s.id";
        } else {
            $submissionssql = "SELECT NULL AS submission_id, '' AS submission_type";
        }

        $select = ($count) ?
            "SELECT COUNT(s.id) as recordscount" :
            "SELECT s.id, s.assignment, s.userid, s.timemodified, s.status, s.attemptnumber,
                    ag.grade, ag.timemodified as feedback_at, ag.grader as feedback_by,
                    sс.commenttext as feedback, subt.submission_type";

        $sql = "$select
                  FROM {assign_submission} s
             LEFT JOIN {assign_grades} ag ON ag.assignment = s.assignment
                   AND ag.userid = s.userid AND ag.attemptnumber = s.attemptnumber
             LEFT JOIN {assignfeedback_comments} sс ON sс.assignment = ag.assignment
                   AND sс.grade = ag.id
             LEFT JOIN ($submissionssql) subt ON subt.submission_id = s.id
                 WHERE $where";

        if ($condition) {
            $sql .= " AND " . $this->apply_tablealias($condition);
        }

        return [$sql, $conditionparams];
    }
}

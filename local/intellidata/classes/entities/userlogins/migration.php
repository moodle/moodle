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
 * Class for migration User logins.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2021 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\userlogins;

use local_intellidata\helpers\DBHelper;
use local_intellidata\helpers\ParamsHelper;

/**
 * Class for migration User logins.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2021 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migration extends \local_intellidata\entities\migration {
    /** @var string */
    public $entity = '\local_intellidata\entities\userlogins\userlogin';
    /** @var string */
    public $eventname = '\core\event\user_loggedin';
    /** @var string */
    public $table = 'logstore_standard_log';

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
        $release4 = ParamsHelper::compare_release('4.0.0');
        $sqlwhere = ''; $sqlparams = [];

        if ($timestart > 0) {
            $sqlwhere = " AND timecreated > :timecreated ";
            $sqlparams['timecreated'] = $timestart;
        } else if ($condition) {
            $sqlwhere .= " AND " . $this->apply_tablealias($condition);
            $sqlparams = array_merge($sqlparams, $conditionparams);
        }

        $additionalfromsql = '';
        if ($release4 && DBHelper::is_mysql_type()) {
            $additionalfromsql = 'USE INDEX({logsstanlog_con_ix})';
        }

        if ($count) {
            $sql = "SELECT
                        COUNT(DISTINCT userid) as recordscount
                      FROM {logstore_standard_log} $additionalfromsql
                     WHERE contextid = 1 AND eventname = '\\\\core\\\\event\\\\user_loggedin' $sqlwhere";
        } else {
            $sql = "SELECT
                        userid AS id,
                        COUNT(userid) AS logins
                      FROM {logstore_standard_log} $additionalfromsql
                     WHERE contextid = 1 AND eventname = '\\\\core\\\\event\\\\user_loggedin' $sqlwhere
                  GROUP BY userid
                  ORDER BY userid";
        }

        return [$sql, $sqlparams];
    }

    /**
     * Get records count.
     *
     * @param null $condition
     * @param array $sqlparams
     * @return int
     * @throws \dml_exception
     */
    public function get_records_count($lastrecordid = null) {
        global $DB;

        $condition = null; $conditionparams = [];

        if ($lastrecordid) {
            $condition = "userid <= :lastrecid";
            $conditionparams = ['lastrecid' => $lastrecordid];
        }

        list($sql, $sqlparams) = $this->get_sql(true, $condition, $conditionparams);

        return $DB->count_records_sql($sql, $sqlparams);
    }
}

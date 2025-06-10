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
 * Class for migration Participations.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace local_intellidata\entities\participations;

use local_intellidata\helpers\DBHelper;

/**
 * Class for migration Participation.
 *
 * @package    local_intellidata
 * @author     IntelliBoard
 * @copyright  2020 intelliboard.net
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class migration extends \local_intellidata\entities\migration {
    /** @var string */
    public $entity = '\local_intellidata\entities\participations\participation';
    /** @var string */
    public $eventname = '\generated\new_participation';
    /** @var string */
    public $table = 'logstore_standard_log';

    /**
     * Prepare SQL query to get data from DB.
     *
     * @param false $count
     * @param null $condition
     * @param array $sqlparams
     * @return array
     */
    public function get_sql($count = false, $condition = null, $conditionparams = []) {
        global $DB, $CFG;

        list($insql, $params) = $DB->get_in_or_equal([CONTEXT_COURSE, CONTEXT_MODULE], SQL_PARAMS_NAMED);
        $where = "AND contextlevel $insql";

        if ($condition) {
            $where .= " AND " . $condition;
            $params += $conditionparams;
        }

        if ($count) {
            $dbtype = $CFG->dbtype == DBHelper::OCI_TYPE ? DBHelper::OCI_TYPE : DBHelper::MYSQL_TYPE;
            $concat = DBHelper::get_operator('CONCAT', "'_'", ['userid', 'contextinstanceid', 'contextlevel'], $dbtype);
            $sql = "SELECT
                        COUNT(DISTINCT $concat) as recordscount
                    FROM {logstore_standard_log}
                   WHERE crud IN('c', 'u') AND userid > 0 AND contextinstanceid > 0 $where";
        } else {
            $sql = "SELECT
                    MAX(id) as id,
                    userid,
                    contextlevel,
                    contextinstanceid,
                    COUNT(contextinstanceid) AS participations,
                    MAX(timecreated) AS last_participation
                FROM {logstore_standard_log}
               WHERE crud IN('c', 'u') AND userid > 0 AND contextinstanceid > 0 $where
            GROUP BY userid, contextinstanceid, contextlevel";
        }

        return [$sql, $params];
    }

    /**
     * Prepare records for export.
     *
     * @param $records
     * @return \Generator
     * @throws \coding_exception
     */
    public function prepare_records_iterable($records) {
        foreach ($records as $record) {
            $record->type = ($record->contextlevel == CONTEXT_MODULE) ? 'activity' : 'course';
            $record->objectid = (int)$record->contextinstanceid;

            $entity = new $this->entity($record);
            $userdata = $entity->export();

            yield $userdata;
        }
    }
}

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
 * @package tool
 * @subpackage mergeusers
 * @author Jordi Pujol-Ahulló <jordi.pujol@urv.cat>
 * @copyright 2013 Servei de Recursos Educatius (http://www.sre.urv.cat)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once __DIR__ . '/../../../../config.php';

global $CFG;

require_once $CFG->dirroot .'/lib/clilib.php';

/**
 * Class to manage logging actions for this tool.
 * General log table cannot be used for log.info field length restrictions.
 */
class tool_mergeusers_logger {

    /**
     * Adds a merging action log into tool log.
     * @param int $touserid user.id where all data from $fromuserid will be merged into.
     * @param int $fromuserid user.id moving all data into $touserid.
     * @param bool $success true if merging action was ok; false otherwise.
     * @param array $log list of actions performed for a successful merging;
     * or a problem description if merging failed.
     */
    public function log($touserid, $fromuserid, $success, $log) {
        global $DB;

        $record = new stdClass();
        $record->touserid = $touserid;
        $record->fromuserid = $fromuserid;
        $record->timemodified = time();
        $record->success = (int)$success;
        $record->log = json_encode($log); //to get it

        try {
            return $DB->insert_record('tool_mergeusers', $record, true); //exception is thrown on any error
        } catch (Exception $e) {
            $msg = __METHOD__ . ' : Cannot insert new record on log. Reason: "' . $DB->get_last_error() .
                    '". Message: "' . $e->getMessage() . '". Trace' . $e->getTraceAsString();
            if (CLI_SCRIPT) {
                cli_error($msg);
            } else {
                print_error($msg, null, new moodle_url('/admin/tool/mergeusers/index.php'));
            }
        }
    }

    /**
     * Gets the merging logs and stores on to and from attributes the related user records.
     * @param array $filter associative array with conditions to match for getting results.
     * If empty, this will return all logs.
     * @param int $limitfrom starting number of record to get. 0 to get all.
     * @param int $limitnum maximum number of records to get. 0 to get all.
     * @param string $order SQL ordering, defaults to "timemodified DESC"
     */
    public function get($filter = null, $limitfrom=0, $limitnum=0, $sort = "timemodified DESC") {
        global $DB;
        $logs = $DB->get_records('tool_mergeusers', $filter, $sort, 'id, touserid, fromuserid, success, timemodified', $limitfrom, $limitnum);
        if (!$logs) {
            return $logs;
        }
        foreach ($logs as $id => &$log) {
            $log->to = $DB->get_record('user', array('id' => $log->touserid));
            $log->from = $DB->get_record('user', array('id' => $log->fromuserid));
        }
        return $logs;
    }

    /**
     * Get the whole detail of a log id.
     * @param int $logid
     * @return stdClass the whole record related to the $logid
     */
    public function getDetail($logid) {
        global $DB;
        $log = $DB->get_record('tool_mergeusers', array('id' => $logid), '*', MUST_EXIST);
        $log->log = json_decode($log->log);
        return $log;
    }
}

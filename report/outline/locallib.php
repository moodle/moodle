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
 * This file contains functions used by the outline reports
 *
 * @package    report
 * @subpackage outline
 * @copyright  1999 onwards Martin Dougiamas (http://dougiamas.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once(__DIR__.'/lib.php');
require_once($CFG->dirroot.'/course/lib.php');

function report_outline_print_row($mod, $instance, $result) {
    global $OUTPUT, $CFG;

    $image = $OUTPUT->image_icon('icon', $mod->modfullname, $mod->modname);

    echo "<tr>";
    echo "<td valign=\"top\">$image</td>";
    echo "<td valign=\"top\" style=\"width:300\">";
    echo "   <a title=\"$mod->modfullname\"";
    echo "   href=\"$CFG->wwwroot/mod/$mod->modname/view.php?id=$mod->id\">".format_string($instance->name,true)."</a></td>";
    echo "<td>&nbsp;&nbsp;&nbsp;</td>";
    echo "<td valign=\"top\">";
    if (isset($result->info)) {
        echo "$result->info";
    } else {
        echo "<p style=\"text-align:center\">-</p>";
    }
    echo "</td>";
    echo "<td>&nbsp;&nbsp;&nbsp;</td>";
    if (!empty($result->time)) {
        $timeago = format_time(time() - $result->time);
        echo "<td valign=\"top\" style=\"white-space: nowrap\">".userdate($result->time)." ($timeago)</td>";
    }
    echo "</tr>";
}

/**
 * Returns an array of the commonly used log variables by the outline report.
 *
 * @return array the array of variables used
 */
function report_outline_get_common_log_variables() {
    global $DB;

    static $uselegacyreader;
    static $useinternalreader;
    static $minloginternalreader;
    static $logtable = null;

    if (isset($uselegacyreader) && isset($useinternalreader) && isset($minloginternalreader)) {
        return array($uselegacyreader, $useinternalreader, $minloginternalreader, $logtable);
    }

    $uselegacyreader = false; // Flag to determine if we should use the legacy reader.
    $useinternalreader = false; // Flag to determine if we should use the internal reader.
    $minloginternalreader = 0; // Set this to 0 for now.

    // Get list of readers.
    $logmanager = get_log_manager();
    $readers = $logmanager->get_readers();

    // Get preferred reader.
    if (!empty($readers)) {
        foreach ($readers as $readerpluginname => $reader) {
            // If legacy reader is preferred reader.
            if ($readerpluginname == 'logstore_legacy') {
                $uselegacyreader = true;
            }

            // If sql_internal_table_reader is preferred reader.
            if ($reader instanceof \core\log\sql_internal_table_reader) {
                $useinternalreader = true;
                $logtable = $reader->get_internal_log_table_name();
                $minloginternalreader = $DB->get_field_sql('SELECT min(timecreated) FROM {' . $logtable . '}');
            }
        }
    }

    return array($uselegacyreader, $useinternalreader, $minloginternalreader, $logtable);
}

/**
 * Return the most commonly used user outline information.
 *
 * @param int $userid the id of the user
 * @param int $cmid the course module id
 * @param string $module the name of the module (eg. 'book')
 * @param int $instanceid (eg. the 'id' in the 'book' table)
 * @return stdClass|null if any information is found then a stdClass containing
 *  this info is returned, else null is.
 */
function report_outline_user_outline($userid, $cmid, $module, $instanceid) {
    global $DB;

    list($uselegacyreader, $useinternalreader, $minloginternalreader, $logtable) = report_outline_get_common_log_variables();

    // If using legacy log then get users from old table.
    if ($uselegacyreader) {
        // Create the params for the query.
        $params = array('userid' => $userid, 'module' => $module, 'action' => 'view', 'info' => $instanceid);
        // If we are going to use the internal (not legacy) log table, we should only get records
        // from the legacy table that exist before we started adding logs to the new table.
        $limittime = '';
        if (!empty($minloginternalreader)) {
            $limittime = ' AND time < :timeto ';
            $params['timeto'] = $minloginternalreader;
        }
        $select = "SELECT COUNT(id) ";
        $from = "FROM {log} ";
        $where = "WHERE userid = :userid
                    AND module = :module
                    AND action = :action
                    AND info = :info ";
        if ($legacylogcount = $DB->count_records_sql($select . $from . $where . $limittime, $params)) {
            $numviews = $legacylogcount;

            // Get the time for the last log.
            $select = "SELECT MAX(time) ";
            $lastlogtime = $DB->get_field_sql($select . $from . $where, $params);

            $result = new stdClass();
            $result->info = get_string('numviews', '', $numviews);
            $result->time = $lastlogtime;
        }
    }

    // Get record from sql_internal_table_reader and combine with the number of views from the legacy log table (if needed).
    if ($useinternalreader) {
        $params = array('userid' => $userid, 'contextlevel' => CONTEXT_MODULE, 'contextinstanceid' => $cmid, 'crud' => 'r',
            'edulevel1' => core\event\base::LEVEL_PARTICIPATING, 'edulevel2' => core\event\base::LEVEL_TEACHING,
            'edulevel3' => core\event\base::LEVEL_OTHER, 'anonymous' => 0);
        $select = "SELECT COUNT(*) as count ";
        $from = "FROM {" . $logtable . "} ";
        $where = "WHERE userid = :userid
                    AND contextlevel = :contextlevel
                    AND contextinstanceid = :contextinstanceid
                    AND crud = :crud
                    AND edulevel IN (:edulevel1, :edulevel2, :edulevel3)
                    AND anonymous = :anonymous";
        if ($internalreadercount = $DB->count_records_sql($select . $from . $where, $params)) {
            if (!empty($numviews)) {
                $numviews = $numviews + $internalreadercount;
            } else {
                $numviews = $internalreadercount;
            }

            // Get the time for the last log.
            $select = "SELECT MAX(timecreated) ";
            $lastlogtime = $DB->get_field_sql($select . $from . $where, $params);

            $result = new stdClass();
            $result->info = get_string('numviews', '', $numviews);
            $result->time = $lastlogtime;
        }
    }

    if (!empty($result)) {
        return $result;
    }

    return null;
}

/**
 * Display the most commonly used user complete information.
 *
 * @param int $userid the id of the user
 * @param int $cmid the course module id
 * @param string $module the name of the module (eg. 'book')
 * @param int $instanceid (eg. the 'id' in the 'book' table)
 * @return string
 */
function report_outline_user_complete($userid, $cmid, $module, $instanceid) {
    global $DB;

    list($uselegacyreader, $useinternalreader, $minloginternalreader, $logtable) = report_outline_get_common_log_variables();

    // If using legacy log then get users from old table.
    if ($uselegacyreader) {
        // Create the params for the query.
        $params = array('userid' => $userid, 'module' => $module, 'action' => 'view', 'info' => $instanceid);
        // If we are going to use the internal (not legacy) log table, we should only get records
        // from the legacy table that exist before we started adding logs to the new table.
        $limittime = '';
        if (!empty($minloginternalreader)) {
            $limittime = ' AND time < :timeto ';
            $params['timeto'] = $minloginternalreader;
        }
        $select = "SELECT COUNT(id) ";
        $from = "FROM {log} ";
        $where = "WHERE userid = :userid
                    AND module = :module
                    AND action = :action
                    AND info = :info ";
        if ($legacylogcount = $DB->count_records_sql($select . $from . $where . $limittime, $params)) {
            $numviews = $legacylogcount;

            // Get the time for the last log.
            $select = "SELECT MAX(time) ";
            $lastlogtime = $DB->get_field_sql($select . $from . $where, $params);

            $strnumviews = get_string('numviews', '', $numviews);
        }
    }

    // Get record from sql_internal_table_reader and combine with the number of views from the legacy log table (if needed).
    if ($useinternalreader) {
        $params = array('userid' => $userid, 'contextlevel' => CONTEXT_MODULE, 'contextinstanceid' => $cmid, 'crud' => 'r',
            'edulevel1' => core\event\base::LEVEL_PARTICIPATING, 'edulevel2' => core\event\base::LEVEL_TEACHING,
            'edulevel3' => core\event\base::LEVEL_OTHER, 'anonymous' => 0);
        $select = "SELECT COUNT(*) as count ";
        $from = "FROM {" . $logtable . "} ";
        $where = "WHERE userid = :userid
                    AND contextlevel = :contextlevel
                    AND contextinstanceid = :contextinstanceid
                    AND crud = :crud
                    AND edulevel IN (:edulevel1, :edulevel2, :edulevel3)
                    AND anonymous = :anonymous";
        if ($internalreadercount = $DB->count_records_sql($select . $from . $where, $params)) {
            if (!empty($numviews)) {
                $numviews = $numviews + $internalreadercount;
            } else {
                $numviews = $internalreadercount;
            }

            // Get the time for the last log.
            $select = "SELECT MAX(timecreated) ";
            $lastlogtime = $DB->get_field_sql($select . $from . $where, $params);

            $strnumviews = get_string('numviews', '', $numviews);
        }
    }

    if (!empty($strnumviews) && (!empty($lastlogtime))) {
        return $strnumviews . ' - ' . get_string('mostrecently') . ' ' . userdate($lastlogtime);
    } else {
        return get_string('neverseen', 'report_outline');
    }
}

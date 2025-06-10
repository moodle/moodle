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
 *
 * @package    block_ues_logs
 * @copyright  2014 Louisiana State University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/enrol/ues/publiclib.php');
ues::require_daos();

interface ues_log_types {
    const ADD = 'AD';
    const DROP = 'DR';
}

class ues_log extends ues_external implements ues_log_types {
    public $userid;
    public $sectionid;
    public $action;
    public $timestamp;

    public static function add($uesuser) {
        return self::make(self::ADD, $uesuser);
    }

    public static function drop($uesuser) {
        return self::make(self::DROP, $uesuser);
    }

    public static function get_by_special($params, $order = 'timestamp DESC') {
        global $DB;

        $toflatten = function($key, $value) {
            $safevalue = addslashes($value);

            if (preg_match('/name$/', $key)) {
                $filter = " LIKE '$safevalue%'";
            } else {
                $filter = " = '$safevalue'";
            }

            return "$key $filter";
        };

        $f = array_map($toflatten, array_keys($params), array_values($params));

        $where = (!empty($f) ? ' AND ' : '') . implode(' AND ', $f);

	$userfields = \core_user\fields::for_name()->get_required_fields();

        $altnamefields = implode(',u.', $userfields);

        $sql = "SELECT l.id, l.userid, $altnamefields, u.email, l.action, l.timestamp
                FROM {enrol_ues_logs} l,
                    {user} u
                WHERE u.id = l.userid $where ORDER BY $order";
        $upgraded = function($log) {
            return ues_log::upgrade($log);
        };

        return array_map($upgraded, $DB->get_records_sql($sql));
    }

    private static function make($type, $uesuser) {
        $log = new ues_log();
        $log->action = $type;
        $log->timestamp = time();
        $log->userid = $uesuser->userid;
        $log->sectionid = $uesuser->sectionid;

        return $log;
    }
}

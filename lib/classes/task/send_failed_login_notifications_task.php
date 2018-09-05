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
 * Scheduled task class.
 *
 * @package    core
 * @copyright  2013 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace core\task;

/**
 * Simple task to send notifications about failed login attempts.
 */
class send_failed_login_notifications_task extends scheduled_task {

    /** The maximum time period to look back (30 days = 30 * 24 * 3600) */
    const NOTIFY_MAXIMUM_TIME = 2592000;

    /**
     * Get a descriptive name for this task (shown to admins).
     *
     * @return string
     */
    public function get_name() {
        return get_string('tasksendfailedloginnotifications', 'admin');
    }

    /**
     * Do the job.
     * Throw exceptions on errors (the job will be retried).
     */
    public function execute() {
        global $CFG, $DB;

        if (empty($CFG->notifyloginfailures)) {
            return;
        }

        $recip = get_users_from_config($CFG->notifyloginfailures, 'moodle/site:config');

        // Do not look back more than 1 month to avoid crashes due to huge number of records.
        $maximumlastnotifytime = time() - self::NOTIFY_MAXIMUM_TIME;
        if (empty($CFG->lastnotifyfailure) || ($CFG->lastnotifyfailure < $maximumlastnotifytime)) {
            $CFG->lastnotifyfailure = $maximumlastnotifytime;
        }

        // If it has been less than an hour, or if there are no recipients, don't execute.
        if (((time() - HOURSECS) < $CFG->lastnotifyfailure) || !is_array($recip) || count($recip) <= 0) {
            return;
        }

        // We need to deal with the threshold stuff first.
        if (empty($CFG->notifyloginthreshold)) {
            $CFG->notifyloginthreshold = 10; // Default to something sensible.
        }

        // Get all the IPs with more than notifyloginthreshold failures since lastnotifyfailure
        // and insert them into the cache_flags temp table.
        $logmang = get_log_manager();
        $readers = $logmang->get_readers('\core\log\sql_internal_table_reader');
        $reader = reset($readers);
        $readername = key($readers);
        if (empty($reader) || empty($readername)) {
            // No readers, no processing.
            return true;
        }
        $logtable = $reader->get_internal_log_table_name();

        $sql = "SELECT ip, COUNT(*)
                  FROM {" . $logtable . "}
                 WHERE eventname = ?
                       AND timecreated > ?
               GROUP BY ip
                 HAVING COUNT(*) >= ?";
        $params = array('\core\event\user_login_failed', $CFG->lastnotifyfailure, $CFG->notifyloginthreshold);
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $iprec) {
            if (!empty($iprec->ip)) {
                set_cache_flag('login_failure_by_ip', $iprec->ip, '1', 0);
            }
        }
        $rs->close();

        // Get all the INFOs with more than notifyloginthreshold failures since lastnotifyfailure
        // and insert them into the cache_flags temp table.
        $sql = "SELECT userid, count(*)
                  FROM {" . $logtable . "}
                 WHERE eventname = ?
                       AND timecreated > ?
              GROUP BY userid
                HAVING count(*) >= ?";
        $params = array('\core\event\user_login_failed', $CFG->lastnotifyfailure, $CFG->notifyloginthreshold);
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $inforec) {
            if (!empty($inforec->info)) {
                set_cache_flag('login_failure_by_id', $inforec->userid, '1', 0);
            }
        }
        $rs->close();

        // Now, select all the login error logged records belonging to the ips and infos
        // since lastnotifyfailure, that we have stored in the cache_flags table.
        $namefields = get_all_user_name_fields(true, 'u');
        $sql = "SELECT * FROM (
                        SELECT l.*, u.username, $namefields
                          FROM {" . $logtable . "} l
                          JOIN {cache_flags} cf ON l.ip = cf.name
                     LEFT JOIN {user} u         ON l.userid = u.id
                         WHERE l.eventname = ?
                               AND l.timecreated > ?
                               AND cf.flagtype = 'login_failure_by_ip'
                    UNION ALL
                        SELECT l.*, u.username, $namefields
                          FROM {" . $logtable . "} l
                          JOIN {cache_flags} cf ON l.userid = " . $DB->sql_cast_char2int('cf.name') . "
                     LEFT JOIN {user} u         ON l.userid = u.id
                         WHERE l.eventname = ?
                               AND l.timecreated > ?
                               AND cf.flagtype = 'login_failure_by_info') t
             ORDER BY t.timecreated DESC";
        $params = array('\core\event\user_login_failed', $CFG->lastnotifyfailure, '\core\event\user_login_failed', $CFG->lastnotifyfailure);

        // Init some variables.
        $count = 0;
        $messages = '';
        // Iterate over the logs recordset.
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $log) {
            $a = new \stdClass();
            $a->time = userdate($log->timecreated);
            if (empty($log->username)) {
                // Entries with no valid username. We get attempted username from the event's other field.
                $other = unserialize($log->other);
                $a->info = empty($other['username']) ? '' : $other['username'];
                $a->name = get_string('unknownuser');
            } else {
                $a->info = $log->username;
                $a->name = fullname($log);
            }
            $a->ip = $log->ip;
            $messages .= get_string('notifyloginfailuresmessage', '', $a)."\n";
            $count++;
        }
        $rs->close();

        // If we have something useful to report.
        if ($count > 0) {
            $site = get_site();
            $subject = get_string('notifyloginfailuressubject', '', format_string($site->fullname));
            // Calculate the complete body of notification (start + messages + end).
            $params = array('id' => 0, 'modid' => 'site_errors', 'chooselog' => '1', 'logreader' => $readername);
            $url = new \moodle_url('/report/log/index.php', $params);
            $body = get_string('notifyloginfailuresmessagestart', '', $CFG->wwwroot) .
                    (($CFG->lastnotifyfailure != 0) ? '('.userdate($CFG->lastnotifyfailure).')' : '')."\n\n" .
                    $messages .
                    "\n\n".get_string('notifyloginfailuresmessageend', '',  $url->out(false).' ')."\n\n";

            // For each destination, send mail.
            mtrace('Emailing admins about '. $count .' failed login attempts');
            foreach ($recip as $admin) {
                // Emailing the admins directly rather than putting these through the messaging system.
                email_to_user($admin, \core_user::get_noreply_user(), $subject, $body);
            }
        }

        // Update lastnotifyfailure with current time.
        set_config('lastnotifyfailure', time());

        // Finally, delete all the temp records we have created in cache_flags.
        $DB->delete_records_select('cache_flags', "flagtype IN ('login_failure_by_ip', 'login_failure_by_info')");

    }
}

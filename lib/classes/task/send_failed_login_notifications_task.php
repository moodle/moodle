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
        global $CFG, $DB, $OUTPUT;

        if (empty($CFG->notifyloginfailures)) {
            return;
        }

        $recip = get_users_from_config($CFG->notifyloginfailures, 'moodle/site:config');

        if (empty($CFG->lastnotifyfailure)) {
            $CFG->lastnotifyfailure = 0;
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
        $sql = "SELECT ip, COUNT(*)
                  FROM {log}
                 WHERE module = 'login' AND action = 'error'
                       AND time > ?
              GROUP BY ip
                HAVING COUNT(*) >= ?";
        $params = array($CFG->lastnotifyfailure, $CFG->notifyloginthreshold);
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $iprec) {
            if (!empty($iprec->ip)) {
                set_cache_flag('login_failure_by_ip', $iprec->ip, '1', 0);
            }
        }
        $rs->close();

        // Get all the INFOs with more than notifyloginthreshold failures since lastnotifyfailure
        // and insert them into the cache_flags temp table.
        $sql = "SELECT info, count(*)
                  FROM {log}
                 WHERE module = 'login' AND action = 'error'
                       AND time > ?
              GROUP BY info
                HAVING count(*) >= ?";
        $params = array($CFG->lastnotifyfailure, $CFG->notifyloginthreshold);
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $inforec) {
            if (!empty($inforec->info)) {
                set_cache_flag('login_failure_by_info', $inforec->info, '1', 0);
            }
        }
        $rs->close();

        // Now, select all the login error logged records belonging to the ips and infos
        // since lastnotifyfailure, that we have stored in the cache_flags table.
        $sql = "SELECT * FROM (
            SELECT l.*, u.firstname, u.lastname
                  FROM {log} l
                  JOIN {cache_flags} cf ON l.ip = cf.name
             LEFT JOIN {user} u         ON l.userid = u.id
                 WHERE l.module = 'login' AND l.action = 'error'
                       AND l.time > ?
                       AND cf.flagtype = 'login_failure_by_ip'
            UNION ALL
                SELECT l.*, u.firstname, u.lastname
                  FROM {log} l
                  JOIN {cache_flags} cf ON l.info = cf.name
             LEFT JOIN {user} u         ON l.userid = u.id
                 WHERE l.module = 'login' AND l.action = 'error'
                       AND l.time > ?
                       AND cf.flagtype = 'login_failure_by_info') t
            ORDER BY t.time DESC";
        $params = array($CFG->lastnotifyfailure, $CFG->lastnotifyfailure);

        // Init some variables.
        $count = 0;
        $messages = '';
        // Iterate over the logs recordset.
        $rs = $DB->get_recordset_sql($sql, $params);
        foreach ($rs as $log) {
            $log->time = userdate($log->time);
            $messages .= get_string('notifyloginfailuresmessage', '', $log) . "\n";
            $count++;
        }
        $rs->close();

        // If we have something useful to report.
        if ($count > 0) {
            $site = get_site();
            $subject = get_string('notifyloginfailuressubject', '', format_string($site->fullname));
            // Calculate the complete body of notification (start + messages + end).
            $body = get_string('notifyloginfailuresmessagestart', '', $CFG->wwwroot) .
                    (($CFG->lastnotifyfailure != 0) ? '('.userdate($CFG->lastnotifyfailure).')' : '')."\n\n" .
                    $messages .
                    "\n\n" . get_string('notifyloginfailuresmessageend', '', $CFG->wwwroot) . "\n\n";

            // For each destination, send mail.
            mtrace('Emailing admins about '. $count .' failed login attempts');
            foreach ($recip as $admin) {
                // Emailing the admins directly rather than putting these through the messaging system.
                email_to_user($admin, \core_user::get_support_user(), $subject, $body);
            }
        }

        // Update lastnotifyfailure with current time.
        set_config('lastnotifyfailure', time());

        // Finally, delete all the temp records we have created in cache_flags.
        $DB->delete_records_select('cache_flags', "flagtype IN ('login_failure_by_ip', 'login_failure_by_info')");

    }
}

<?php
namespace tool_bruteforce;

// Event observer class for tool_bruteforce.

defined('MOODLE_INTERNAL') || die();

class observer {
    /**
     * Record a login attempt.
     *
     * @param int|null $userid User identifier.
     * @param string $ip Remote IP.
     * @param string $status success|fail.
     * @return void
     */
    protected static function log_attempt(?int $userid, string $ip, string $status): void {
        global $DB;
        $record = (object) [
            'userid' => $userid,
            'ip' => $ip,
            'status' => $status,
            'timecreated' => time(),
        ];
        $DB->insert_record('tool_bruteforce_log', $record);
    }

    /**
     * Create or extend a block entry.
     *
     * @param string $type user|ip
     * @param string $value Identifier to block.
     * @param int $duration Block duration in minutes.
     * @return void
     */
    protected static function block(string $type, string $value, int $duration): void {
        global $DB;
        $timerelease = time() + ($duration * 60);
        if ($existing = $DB->get_record('tool_bruteforce_block', ['type' => $type, 'value' => $value])) {
            if ($timerelease > $existing->timerelease) {
                $existing->timerelease = $timerelease;
                $DB->update_record('tool_bruteforce_block', $existing);
            }
        } else {
            $record = (object) [
                'type' => $type,
                'value' => $value,
                'reason' => 'auto',
                'timecreated' => time(),
                'timerelease' => $timerelease,
            ];
            $DB->insert_record('tool_bruteforce_block', $record);
        }
    }

    /**
     * Handle failed login events.
     *
     * @param \core\event\user_login_failed $event Event data.
     * @return void
     */
    public static function user_login_failed(\core\event\user_login_failed $event): void {
        global $DB;

        $userid = $event->userid ?: null;
        $ip = $event->other['ip'] ?? \core\ip_utils::get_ip_address();

        self::log_attempt($userid, $ip, 'fail');

        // Skip blocking for privileged or whitelisted entries.
        $syscontext = \context_system::instance();
        if (($userid && is_siteadmin($userid)) ||
            ($userid && has_capability('tool/bruteforce:exempt', $syscontext, $userid)) ||
            ($userid && api::is_whitelisted('user', (string) $userid)) ||
            api::is_whitelisted('ip', $ip)) {
            return;
        }

        // Immediate block for blacklisted users or IPs.
        $dayduration = (int) get_config('tool_bruteforce', 'dayblockduration');
        if (($userid && api::is_blacklisted('user', (string) $userid))) {
            self::block('user', (string) $userid, $dayduration);
            return;
        }
        if (api::is_blacklisted('ip', $ip)) {
            self::block('ip', $ip, $dayduration);
            return;
        }

        // User based blocking.
        if ($userid && get_config('tool_bruteforce', 'enableuserlock')) {
            $since = time() - ((int) get_config('tool_bruteforce', 'userfailwindow') * 60);
            $threshold = (int) get_config('tool_bruteforce', 'userfailthreshold');
            $count = $DB->count_records_select('tool_bruteforce_log', 'userid = ? AND status = ? AND timecreated > ?',
                [$userid, 'fail', $since]);
            if ($count >= $threshold) {
                $duration = (int) get_config('tool_bruteforce', 'userblockduration');
                self::block('user', (string) $userid, $duration);
            }
        }

        // IP based blocking.
        if (get_config('tool_bruteforce', 'enableiplock')) {
            $since = time() - ((int) get_config('tool_bruteforce', 'ipfailwindow') * 60);
            $threshold = (int) get_config('tool_bruteforce', 'ipfailthreshold');
            $count = $DB->count_records_select('tool_bruteforce_log', 'ip = ? AND status = ? AND timecreated > ?',
                [$ip, 'fail', $since]);
            if ($count >= $threshold) {
                $duration = (int) get_config('tool_bruteforce', 'ipblockduration');
                self::block('ip', $ip, $duration);
            }
        }

        // Daily limit by IP.
        $daythreshold = (int) get_config('tool_bruteforce', 'dayfailthreshold');
        if ($daythreshold > 0) {
            $since = time() - DAYSECS;
            $count = $DB->count_records_select('tool_bruteforce_log', 'ip = ? AND status = ? AND timecreated > ?',
                [$ip, 'fail', $since]);
            if ($count >= $daythreshold) {
                $duration = (int) get_config('tool_bruteforce', 'dayblockduration');
                self::block('ip', $ip, $duration);
            }
        }
    }

    /**
     * Handle successful login events.
     *
     * @param \core\event\user_loggedin $event Event data.
     * @return void
     */
    public static function user_loggedin(\core\event\user_loggedin $event): void {
        global $DB;
        $userid = $event->userid;
        $ip = $event->other['ip'] ?? \core\ip_utils::get_ip_address();

        self::log_attempt($userid, $ip, 'success');

        // Remove user block on successful login.
        $DB->delete_records('tool_bruteforce_block', ['type' => 'user', 'value' => $userid]);
    }
}
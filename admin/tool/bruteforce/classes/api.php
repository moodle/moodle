<?php
namespace tool_bruteforce;

// Public API for tool_bruteforce.

defined('MOODLE_INTERNAL') || die();

/**
 * Helper functions to query block status and lists.
 */
class api {
    /**
     * Check if a given identifier is currently blocked.
     *
     * @param string $type 'user' or 'ip'
     * @param string $value User ID or IP address
     * @return bool
     */
    public static function is_blocked(string $type, string $value): bool {
        global $DB;
        return $DB->record_exists_select(
            'tool_bruteforce_block',
            'type = ? AND value = ? AND timerelease > ?',
            [$type, $value, time()]
        );
    }

    /**
     * Check if a user is blocked.
     *
     * @param int $userid
     * @return bool
     */
    public static function is_user_blocked(int $userid): bool {
        return self::is_blocked('user', (string) $userid);
    }

    /**
     * Check if an IP address is blocked.
     *
     * @param string $ip
     * @return bool
     */
    public static function is_ip_blocked(string $ip): bool {
        return self::is_blocked('ip', $ip);
    }

    /**
     * Check whitelist.
     *
     * @param string $type 'user' or 'ip'
     * @param string $value
     * @return bool
     */
    public static function is_whitelisted(string $type, string $value): bool {
        global $DB;
        if ($type === 'ip') {
            return self::ip_list_match('white', $value);
        }
        return $DB->record_exists('tool_bruteforce_list', ['listtype' => 'white', 'type' => $type, 'value' => $value]);
    }

    /**
     * Check blacklist.
     *
     * @param string $type
     * @param string $value
     * @return bool
     */
    public static function is_blacklisted(string $type, string $value): bool {
        global $DB;
        if ($type === 'ip') {
            return self::ip_list_match('black', $value);
        }
        return $DB->record_exists('tool_bruteforce_list', ['listtype' => 'black', 'type' => $type, 'value' => $value]);
    }

    /**
     * Check if IP is within list entries.
     *
     * @param string $listtype
     * @param string $ip
     * @return bool
     */
    protected static function ip_list_match(string $listtype, string $ip): bool {
        global $DB;
        $records = $DB->get_records('tool_bruteforce_list', ['listtype' => $listtype, 'type' => 'ip']);
        foreach ($records as $record) {
            if (self::ip_matches($ip, $record->value)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if an IP matches an entry value (exact or CIDR).
     *
     * @param string $ip
     * @param string $value
     * @return bool
     */
    protected static function ip_matches(string $ip, string $value): bool {
        if ($ip === $value) {
            return true;
        }
        if (strpos($value, '/') !== false) {
            return self::cidr_match($ip, $value);
        }
        return false;
    }

    /**
     * Compare an IP against a CIDR range.
     *
     * @param string $ip
     * @param string $cidr
     * @return bool
     */
    protected static function cidr_match(string $ip, string $cidr): bool {
        list($subnet, $mask) = explode('/', $cidr, 2);
        $mask = (int) $mask;
        $ipbin = @inet_pton($ip);
        $subnetbin = @inet_pton($subnet);
        if ($ipbin === false || $subnetbin === false) {
            return false;
        }
        $ipbytes = unpack('C*', $ipbin);
        $subnetbytes = unpack('C*', $subnetbin);
        $bits = $mask;
        for ($i = 1; $bits > 0; $i++) {
            $shift = $bits >= 8 ? 0 : 8 - $bits;
            $maskbyte = $bits >= 8 ? 0xff : (~((1 << $shift) - 1) & 0xff);
            if (($ipbytes[$i] & $maskbyte) !== ($subnetbytes[$i] & $maskbyte)) {
                return false;
            }
            $bits -= 8;
        }
        return true;
    }

    /**
     * Add an entry to white/black list if it does not already exist.
     *
     * @param string $listtype 'white' or 'black'
     * @param string $type 'user' or 'ip'
     * @param string $value Value to store
     * @param string|null $comment Optional note
     * @param int|null $userid User performing the action
     * @return int New record id or existing id
     */
    public static function add_list_entry(string $listtype, string $type, string $value,
            ?string $comment = null, ?int $userid = null): int {
        global $DB;
        if ($existing = $DB->get_record('tool_bruteforce_list',
                ['listtype' => $listtype, 'type' => $type, 'value' => $value], 'id')) {
            return (int)$existing->id;
        }
        $record = (object) [
            'listtype' => $listtype,
            'type' => $type,
            'value' => $value,
            'comment' => $comment,
            'userid' => $userid,
            'timecreated' => time(),
        ];
        return (int) $DB->insert_record('tool_bruteforce_list', $record);
    }

    /**
     * Remove a list entry by id.
     *
     * @param int $id
     * @return void
     */
    public static function remove_list_entry(int $id): void {
        global $DB;
        $DB->delete_records('tool_bruteforce_list', ['id' => $id]);
    }

    /**
     * Retrieve entries for a given list type.
     *
     * @param string $listtype 'white' or 'black'
     * @return array
     */
    public static function get_list_entries(string $listtype): array {
        global $DB;
        return $DB->get_records('tool_bruteforce_list', ['listtype' => $listtype], 'timecreated DESC');
    }

    /**
     * Remove a block and log the action.
     *
     * @param string $type 'user' or 'ip'
     * @param string $value Identifier to unblock
     * @param int $actorid User performing the action
     * @param string|null $reason Optional reason
     * @return void
     */
    public static function unblock(string $type, string $value, int $actorid, ?string $reason = null): void {
        global $DB;
        $DB->delete_records('tool_bruteforce_block', ['type' => $type, 'value' => $value]);
        self::log_audit($actorid, $type, $value, 'unblock', $reason);
    }

    /**
     * Log an audit action.
     *
     * @param int $actorid
     * @param string $targettype
     * @param string $targetvalue
     * @param string $action
     * @param string|null $reason
     * @return void
     */
    public static function log_audit(int $actorid, string $targettype, string $targetvalue,
            string $action, ?string $reason = null): void {
        global $DB;
        $record = (object) [
            'actorid' => $actorid,
            'targettype' => $targettype,
            'targetvalue' => $targetvalue,
            'action' => $action,
            'reason' => $reason,
            'timecreated' => time(),
        ];
        $DB->insert_record('tool_bruteforce_audit', $record);
    }
}

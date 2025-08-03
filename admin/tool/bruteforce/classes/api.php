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
        return $DB->record_exists(
            'tool_bruteforce_list',
            ['listtype' => 'white', 'type' => $type, 'value' => $value]
        );
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
        return $DB->record_exists(
            'tool_bruteforce_list',
            ['listtype' => 'black', 'type' => $type, 'value' => $value]
        );
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
}

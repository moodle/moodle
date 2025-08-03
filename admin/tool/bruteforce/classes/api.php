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
}

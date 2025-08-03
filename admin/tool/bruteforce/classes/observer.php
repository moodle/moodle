<?php
namespace tool_bruteforce;

// Event observer class.

defined('MOODLE_INTERNAL') || die();

class observer {
    /**
     * Handle failed login events.
     *
     * @param \core\event\user_login_failed $event
     * @return void
     */
    public static function user_login_failed(\core\event\user_login_failed $event): void {
        global $DB;
        // TODO: Increment counters and apply blocking logic.
    }

    /**
     * Handle successful login events.
     *
     * @param \core\event\user_loggedin $event
     * @return void
     */
    public static function user_loggedin(\core\event\user_loggedin $event): void {
        global $DB;
        // TODO: Reset counters after successful login.
    }
}

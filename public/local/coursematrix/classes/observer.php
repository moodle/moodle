<?php
/**
 * Event observer for local_coursematrix.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_coursematrix;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/coursematrix/lib.php');

/**
 * Event observer class.
 *
 * @package    local_coursematrix
 * @copyright  2024 Author Name
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class observer {

    /**
     * Triggered when a user is created.
     *
     * @param \core\event\user_created $event The event object
     */
    public static function user_created(\core\event\user_created $event) {
        local_coursematrix_enrol_user($event->objectid);
    }

    /**
     * Triggered when a user is updated.
     *
     * @param \core\event\user_updated $event The event object
     */
    public static function user_updated(\core\event\user_updated $event) {
        local_coursematrix_enrol_user($event->objectid);
    }
}


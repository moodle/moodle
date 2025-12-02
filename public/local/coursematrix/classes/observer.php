<?php
namespace local_coursematrix;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/local/coursematrix/lib.php');

class observer {
    public static function user_created(\core\event\user_created $event) {
        local_coursematrix_enrol_user($event->objectid);
    }

    public static function user_updated(\core\event\user_updated $event) {
        local_coursematrix_enrol_user($event->objectid);
    }
}

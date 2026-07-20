<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Block access to this plugin unless the site is running on localhost.
 * Allowed hosts: localhost, 127.0.0.1, ::1
 */
function local_courseanalytics_ensure_local() {
    global $CFG;
    if (empty($CFG->wwwroot)) {
        return;
    }
    $host = parse_url($CFG->wwwroot, PHP_URL_HOST);
    $allowed = ['localhost', '127.0.0.1', '::1'];
    if (!in_array($host, $allowed, true)) {
        require_once($CFG->libdir . '/outputlib.php');
        redirect(new moodle_url('/'), get_string('local_only_message', 'local_courseanalytics'), null,
            \core\output\notification::NOTIFY_ERROR);
        exit;
    }
}

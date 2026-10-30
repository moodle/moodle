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

namespace local_launchpad;

/**
 * Honest empty state when a signed-in fellow has no LaunchPad course yet.
 *
 * Does not invent contact details, fees, or enrolment self-service instructions.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class empty_state {

    /** Marker used in automated and manual checks. */
    public const DATA_REGION = 'local-launchpad-empty-state';

    /**
     * Whether the current request/user should see the empty-state notice.
     */
    public static function should_show(): bool {
        global $USER, $PAGE;

        if (during_initial_install() || (defined('CLI_SCRIPT') && CLI_SCRIPT)
                || (defined('AJAX_SCRIPT') && AJAX_SCRIPT)
                || (defined('WS_SERVER') && WS_SERVER)) {
            return false;
        }
        if (!isloggedin() || isguestuser()) {
            return false;
        }
        if (empty($USER->id) || is_siteadmin($USER)) {
            // Admins manage structure elsewhere; do not show fellow empty-state copy.
            return false;
        }
        // Employee / Mentor use persona homes — not Fellowship empty-state copy.
        if (account_persona::is_employee((int) $USER->id) || account_persona::is_mentor((int) $USER->id)) {
            return false;
        }

        $path = '';
        try {
            if ($PAGE->url) {
                $path = $PAGE->url->out_as_local_url(false);
            }
        } catch (\Throwable $e) {
            $path = '';
        }
        $pagetype = (string) ($PAGE->pagetype ?? '');
        $onmy = str_starts_with($path, '/my')
            || $pagetype === 'my-index'
            || str_contains($pagetype, 'my-courses')
            || str_contains($pagetype, 'my-');
        if (!$onmy) {
            return false;
        }

        return !self::user_has_launchpad_enrolment((int) $USER->id);
    }

    /**
     * True when the user is enrolled in any LaunchPad course (idnumber lp-*).
     */
    public static function user_has_launchpad_enrolment(int $userid): bool {
        global $CFG, $DB;

        if ($userid <= 0) {
            return false;
        }

        require_once($CFG->libdir . '/enrollib.php');

        $sql = "SELECT ue.id
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                  JOIN {course} c ON c.id = e.courseid
                 WHERE ue.userid = :userid
                   AND ue.status = :uestatus
                   AND e.status = :estatus
                   AND " . $DB->sql_like('c.idnumber', ':idn', false);
        return $DB->record_exists_sql($sql, [
            'userid' => $userid,
            'uestatus' => ENROL_USER_ACTIVE,
            'estatus' => ENROL_INSTANCE_ENABLED,
            'idn' => $DB->sql_like_escape('lp-') . '%',
        ]);
    }

    /**
     * Notice HTML (Bootstrap info alert; no invented contacts).
     *
     * Placed with top margin so it clears Boost's fixed navbar (injection
     * point is before the nav in the DOM).
     */
    public static function notice_html(): string {
        $message = get_string('emptystate_no_course', 'local_launchpad');
        return \html_writer::div(
            \html_writer::tag('p', $message, ['class' => 'mb-0']),
            'local-launchpad-empty-state alert alert-info mx-3',
            [
                'role' => 'status',
                'data-region' => self::DATA_REGION,
                // Boost fixed-top navbar ~60px; keep banner visible underneath.
                'style' => 'position:relative;z-index:1020;margin-top:4.75rem;',
            ]
        );
    }
}

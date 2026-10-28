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
 * Read-only ToBa account persona classifier.
 *
 * Derives Fellowship / Mentor / Admin solely from Moodle enrolments, role
 * assignments and native admin checks. Never reads request parameters, profile
 * fields, usernames or client state. Persona labels are informational only —
 * they must not authorize actions; callers still use require_login(),
 * require_capability() and enrolment checks.
 *
 * Employee is cohort membership ({@see self::EMPLOYEE_COHORT_IDNUMBER}) only —
 * it never implies Fellowship, Mentor, or Admin.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class account_persona {

    /** Fellow enrolled as student on a LaunchPad course. */
    public const FELLOWSHIP = 'fellowship';

    /** Mentor with teacher/editingteacher on a LaunchPad course. */
    public const MENTOR = 'mentor';

    /** Site administrator (or holder of {@see self::ADMIN_CAPABILITY} at system). */
    public const ADMIN = 'admin';

    /** Employee via system cohort membership (not a course role). */
    public const EMPLOYEE = 'employee';

    /** @deprecated Kept for older probes; Employee is cohort-based now. */
    public const EMPLOYEE_STATUS_UNRESOLVED = 'unresolved';

    /** System cohort idnumber for Employee access. */
    public const EMPLOYEE_COHORT_IDNUMBER = 'lp-employee';

    /**
     * LaunchPad course idnumber prefix (matches empty_state / structure CLI).
     */
    public const COURSE_IDNUMBER_PREFIX = 'lp-';

    /**
     * System-context capability used (with {@see is_siteadmin()}) for Admin persona.
     *
     * Detection only — this class never assigns the capability. Site admins match
     * via is_siteadmin(); has_capability also recognises the same native admin path.
     */
    public const ADMIN_CAPABILITY = 'moodle/site:config';

    /**
     * Personas currently implemented for detection.
     *
     * @return list<string>
     */
    public static function implemented_personas(): array {
        return [self::FELLOWSHIP, self::MENTOR, self::ADMIN, self::EMPLOYEE];
    }

    /**
     * Whether Employee persona detection/access exists.
     */
    public static function employee_implemented(): bool {
        return true;
    }

    /**
     * Employee product status string.
     */
    public static function employee_status(): string {
        return 'cohort:' . self::EMPLOYEE_COHORT_IDNUMBER;
    }

    /**
     * All personas that apply to this user.
     *
     * Multiple personas are allowed (e.g. Admin who is also enrolled as a fellow).
     * Optional $courseid scopes Fellowship/Mentor to that LaunchPad course only;
     * Admin and Employee remain site-wide. Invalid / non-LaunchPad course ids yield no
     * course-scoped personas.
     *
     * @param int $userid Moodle user id
     * @param int|null $courseid Optional course id for course-scoped personas
     * @return list<string> Stable order: fellowship, mentor, employee, admin (subset)
     */
    public static function for_user(int $userid, ?int $courseid = null): array {
        if ($userid <= 0) {
            return [];
        }

        $found = [];
        if (self::is_fellowship($userid, $courseid)) {
            $found[] = self::FELLOWSHIP;
        }
        if (self::is_mentor($userid, $courseid)) {
            $found[] = self::MENTOR;
        }
        if (self::is_employee($userid)) {
            $found[] = self::EMPLOYEE;
        }
        if (self::is_admin($userid)) {
            $found[] = self::ADMIN;
        }
        return $found;
    }

    /**
     * True when the user currently has the given implemented persona.
     *
     * @param int $userid
     * @param string $persona One of the persona constants
     * @param int|null $courseid Optional course scope for fellowship/mentor
     */
    public static function has(int $userid, string $persona, ?int $courseid = null): bool {
        if (!in_array($persona, self::implemented_personas(), true)) {
            return false;
        }
        return in_array($persona, self::for_user($userid, $courseid), true);
    }

    /**
     * Employee cohort membership (site-wide; not course-scoped).
     *
     * @param int $userid
     */
    public static function is_employee(int $userid): bool {
        global $DB, $CFG;

        if ($userid <= 0) {
            return false;
        }
        require_once($CFG->dirroot . '/cohort/lib.php');

        $cohort = $DB->get_record('cohort', [
            'idnumber' => self::EMPLOYEE_COHORT_IDNUMBER,
            'contextid' => \context_system::instance()->id,
        ], 'id', IGNORE_MISSING);
        if (!$cohort) {
            return false;
        }
        return cohort_is_member((int) $cohort->id, $userid);
    }

    /**
     * Site admin persona (site-wide; not course-scoped).
     *
     * @param int $userid
     */
    public static function is_admin(int $userid): bool {
        if ($userid <= 0) {
            return false;
        }
        if (is_siteadmin($userid)) {
            return true;
        }
        return has_capability(self::ADMIN_CAPABILITY, \context_system::instance(), $userid);
    }

    /**
     * Active student enrolment (+ student role) on a relevant LaunchPad course.
     *
     * @param int $userid
     * @param int|null $courseid When set, that course only (must be lp-*)
     */
    public static function is_fellowship(int $userid, ?int $courseid = null): bool {
        return self::has_launchpad_role($userid, ['student'], $courseid, true);
    }

    /**
     * Teacher or editingteacher role assignment on a relevant LaunchPad course.
     *
     * @param int $userid
     * @param int|null $courseid When set, that course only (must be lp-*)
     */
    public static function is_mentor(int $userid, ?int $courseid = null): bool {
        return self::has_launchpad_role($userid, ['teacher', 'editingteacher'], $courseid, false);
    }

    /**
     * Shared LaunchPad course + role detector.
     *
     * @param int $userid
     * @param string[] $archetypes Role archetypes to match
     * @param int|null $courseid Optional single course
     * @param bool $requireactiveenrol When true, also require active user_enrolments
     */
    private static function has_launchpad_role(
        int $userid,
        array $archetypes,
        ?int $courseid,
        bool $requireactiveenrol
    ): bool {
        global $CFG, $DB;

        if ($userid <= 0 || $archetypes === []) {
            return false;
        }
        if ($courseid !== null && $courseid <= 0) {
            return false;
        }

        require_once($CFG->libdir . '/enrollib.php');
        require_once($CFG->libdir . '/accesslib.php');

        [$archsql, $archparams] = $DB->get_in_or_equal($archetypes, SQL_PARAMS_NAMED, 'arch');

        $params = [
            'userid' => $userid,
            'ctxlevel' => CONTEXT_COURSE,
            'idn' => $DB->sql_like_escape(self::COURSE_IDNUMBER_PREFIX) . '%',
        ] + $archparams;

        $coursescope = '';
        if ($courseid !== null) {
            $coursescope = ' AND c.id = :courseid ';
            $params['courseid'] = $courseid;
        }

        if ($requireactiveenrol) {
            $sql = "SELECT ra.id
                      FROM {role_assignments} ra
                      JOIN {role} r ON r.id = ra.roleid
                      JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :ctxlevel
                      JOIN {course} c ON c.id = ctx.instanceid
                      JOIN {enrol} e ON e.courseid = c.id AND e.status = :estatus
                      JOIN {user_enrolments} ue ON ue.enrolid = e.id
                                                AND ue.userid = ra.userid
                                                AND ue.status = :uestatus
                     WHERE ra.userid = :userid
                       AND r.archetype $archsql
                       AND " . $DB->sql_like('c.idnumber', ':idn', false) . "
                       $coursescope";
            $params['estatus'] = ENROL_INSTANCE_ENABLED;
            $params['uestatus'] = ENROL_USER_ACTIVE;
        } else {
            $sql = "SELECT ra.id
                      FROM {role_assignments} ra
                      JOIN {role} r ON r.id = ra.roleid
                      JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :ctxlevel
                      JOIN {course} c ON c.id = ctx.instanceid
                     WHERE ra.userid = :userid
                       AND r.archetype $archsql
                       AND " . $DB->sql_like('c.idnumber', ':idn', false) . "
                       $coursescope";
        }

        return $DB->record_exists_sql($sql, $params);
    }
}

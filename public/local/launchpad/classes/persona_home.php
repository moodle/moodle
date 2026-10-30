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
 * Persona post-login / My home routing.
 *
 * Priority: Admin (no override) → Mentor → Fellowship → Employee-only → Moodle /my/.
 * Deep wantsurl targets are never rewritten.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class persona_home {

    /**
     * Moodle path for the mentor home stub.
     */
    public const MENTOR_PATH = '/local/launchpad/mentor.php';

    /**
     * Moodle path for the employee area stub.
     */
    public const EMPLOYEE_PATH = '/local/launchpad/employee.php';

    /**
     * Moodle path for the Fellowship dashboard shell.
     */
    public const FELLOW_PATH = '/local/launchpad/fellow.php';

    /**
     * Persona home URL for this user, or null to keep Moodle default (/my/).
     *
     * Site admins always return null (no forced persona redirect).
     * Mentors → mentor stub. Fellows → Fellowship shell.
     * Employees without Fellowship → employee stub.
     *
     * @param int $userid
     * @return \moodle_url|null
     */
    public static function home_url_for(int $userid): ?\moodle_url {
        if ($userid <= 0) {
            return null;
        }
        if (account_persona::is_admin($userid) || is_siteadmin($userid)) {
            return null;
        }
        if (account_persona::is_mentor($userid)) {
            return new \moodle_url(self::MENTOR_PATH);
        }
        if (account_persona::is_fellowship($userid)) {
            return new \moodle_url(self::FELLOW_PATH);
        }
        if (account_persona::is_employee($userid)) {
            return new \moodle_url(self::EMPLOYEE_PATH);
        }
        return null;
    }

    /**
     * Whether wantsurl is empty or a default home (/ , /my/ , /my/courses.php).
     *
     * @param string|null $wantsurl Absolute or site-relative URL
     */
    public static function should_override_default_home(?string $wantsurl): bool {
        global $CFG;

        if ($wantsurl === null || trim($wantsurl) === '') {
            return true;
        }

        $raw = trim($wantsurl);
        $path = self::path_of($raw);
        if ($path === null) {
            // External or unparseable — do not override.
            return false;
        }

        $path = rtrim($path, '/') ?: '/';
        $defaults = ['/', '/my', '/my/courses.php'];
        foreach ($defaults as $default) {
            $d = rtrim($default, '/') ?: '/';
            if ($path === $d) {
                return true;
            }
        }
        // Also match wwwroot alone when passed as absolute without trailing slash quirks.
        $www = rtrim((string) $CFG->wwwroot, '/');
        if ($raw === $www || $raw === $www . '/') {
            return true;
        }
        return false;
    }

    /**
     * LaunchPad courses where the user holds teacher/editingteacher.
     *
     * @param int $userid
     * @return list<\stdClass> course records (id, fullname, shortname, idnumber)
     */
    public static function list_mentor_courses(int $userid): array {
        global $CFG, $DB;

        if ($userid <= 0) {
            return [];
        }

        require_once($CFG->libdir . '/accesslib.php');

        $archetypes = ['teacher', 'editingteacher'];
        [$archsql, $archparams] = $DB->get_in_or_equal($archetypes, SQL_PARAMS_NAMED, 'arch');
        $params = [
            'userid' => $userid,
            'ctxlevel' => CONTEXT_COURSE,
            'idn' => $DB->sql_like_escape(account_persona::COURSE_IDNUMBER_PREFIX) . '%',
        ] + $archparams;

        $sql = "SELECT DISTINCT c.id, c.fullname, c.shortname, c.idnumber
                  FROM {role_assignments} ra
                  JOIN {role} r ON r.id = ra.roleid
                  JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.contextlevel = :ctxlevel
                  JOIN {course} c ON c.id = ctx.instanceid
                 WHERE ra.userid = :userid
                   AND r.archetype $archsql
                   AND " . $DB->sql_like('c.idnumber', ':idn', false) . "
              ORDER BY c.fullname ASC";

        return array_values($DB->get_records_sql($sql, $params));
    }

    /**
     * Apply persona home to SESSION wantsurl when it is still a default home.
     *
     * @param int $userid
     * @return bool True when wantsurl was changed
     */
    public static function maybe_set_wantsurl(int $userid): bool {
        global $SESSION;

        $home = self::home_url_for($userid);
        if ($home === null) {
            return false;
        }

        $current = isset($SESSION->wantsurl) ? (string) $SESSION->wantsurl : '';
        if (!self::should_override_default_home($current === '' ? null : $current)) {
            return false;
        }

        $SESSION->wantsurl = $home->out(false);
        return true;
    }

    /**
     * Whether the current PAGE is My home / My courses (for live redirect).
     */
    public static function is_my_home_page(): bool {
        global $PAGE;

        $pagetype = (string) ($PAGE->pagetype ?? '');
        if ($pagetype === 'my-index' || str_contains($pagetype, 'my-courses')) {
            return true;
        }

        $path = '';
        try {
            if ($PAGE->url) {
                $path = $PAGE->url->out_as_local_url(false);
            }
        } catch (\Throwable $e) {
            $path = '';
        }
        $path = rtrim(strtok($path, '?') ?: '', '/') ?: '';
        return $path === '/my' || $path === '/my/index.php' || $path === '/my/courses.php';
    }

    /**
     * Redirect from /my/ to persona home when applicable (no loop).
     */
    public static function maybe_redirect_from_my(): void {
        global $USER;

        if (during_initial_install() || (defined('CLI_SCRIPT') && CLI_SCRIPT)
                || (defined('AJAX_SCRIPT') && AJAX_SCRIPT)
                || (defined('WS_SERVER') && WS_SERVER)) {
            return;
        }
        if (!isloggedin() || isguestuser() || empty($USER->id)) {
            return;
        }
        if (!self::is_my_home_page()) {
            return;
        }

        $home = self::home_url_for((int) $USER->id);
        if ($home === null) {
            return;
        }
        redirect($home);
    }

    /**
     * Extract path from an absolute wwwroot URL or a local path.
     *
     * @param string $url
     * @return string|null Path starting with /, or null if external
     */
    private static function path_of(string $url): ?string {
        global $CFG;

        if (str_starts_with($url, '/')) {
            return parse_url($url, PHP_URL_PATH) ?: $url;
        }

        $www = rtrim((string) $CFG->wwwroot, '/');
        $https = preg_replace('#^http://#', 'https://', $www);
        $http = preg_replace('#^https://#', 'http://', $www);

        foreach ([$www, $https, $http] as $root) {
            if ($root && (str_starts_with($url, $root . '/') || $url === $root)) {
                $path = substr($url, strlen($root)) ?: '/';
                $parsed = parse_url($path, PHP_URL_PATH);
                return $parsed !== null && $parsed !== '' ? $parsed : '/';
            }
        }
        return null;
    }
}

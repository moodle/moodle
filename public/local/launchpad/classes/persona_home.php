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
 * Priority: Admin (no override) → Mentor → Employee → Fellowship → Moodle /my/.
 * Employee (cohort lp-employee) wins over Fellowship so employer learners enrolled
 * on lp-* courses still get the Employer shell — not fellow.php.
 * Deep course/mod wantsurl targets are never rewritten. Stale persona-shell
 * wantsurls (wrong shell for this user) are corrected on login.
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

    /** @var array<int, \moodle_url|null> Request-scoped home URL cache. */
    private static array $homecache = [];

    /**
     * Persona home URL for this user, or null to keep Moodle default (/my/).
     *
     * Site admins always return null (no forced persona redirect).
     * Mentors → mentor stub. Employees → Employer portal.
     * Fellows (without employee cohort) → Fellowship shell.
     *
     * @param int $userid
     * @return \moodle_url|null
     */
    public static function home_url_for(int $userid): ?\moodle_url {
        if ($userid <= 0) {
            return null;
        }
        if (array_key_exists($userid, self::$homecache)) {
            return self::$homecache[$userid];
        }

        $home = null;
        // is_admin() already covers is_siteadmin() — avoid a second native check.
        if (account_persona::is_admin($userid)) {
            $home = null;
        } else if (account_persona::is_mentor($userid)) {
            $home = new \moodle_url(self::MENTOR_PATH);
        } else if (account_persona::is_employee($userid)) {
            // Employer cohort before Fellowship: lp-* student enrolments are normal
            // for employee upskilling and must not force the Fellowship shell.
            $home = new \moodle_url(self::EMPLOYEE_PATH);
        } else if (account_persona::is_fellowship($userid)) {
            $home = new \moodle_url(self::FELLOW_PATH);
        }

        self::$homecache[$userid] = $home;
        return $home;
    }

    /**
     * Clear request cache (tests / rare mid-request persona changes).
     */
    public static function reset_home_cache(): void {
        self::$homecache = [];
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
        $defaults = ['/', '/my', '/my/index.php', '/my/courses.php'];
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
     * Whether login should rewrite wantsurl for this user.
     *
     * Overrides default homes and wrong persona shells (e.g. employee still
     * holding fellow.php from an older priority bug). Keeps same-shell deep
     * links (fellow.php?view=track) and all non-shell Moodle URLs.
     *
     * @param int $userid
     * @param string|null $wantsurl
     */
    public static function should_rewrite_wantsurl(int $userid, ?string $wantsurl): bool {
        if (self::should_override_default_home($wantsurl)) {
            return true;
        }
        if ($wantsurl === null || trim($wantsurl) === '') {
            return true;
        }

        $home = self::home_url_for($userid);
        if ($home === null) {
            return false;
        }

        $path = self::path_of(trim($wantsurl));
        if ($path === null) {
            return false;
        }
        $path = rtrim($path, '/') ?: '/';
        if (!self::is_persona_shell_path($path)) {
            return false;
        }

        $homepath = rtrim((string) $home->get_path(), '/') ?: '/';
        // Same shell (possibly with ?view=) — keep the deep link.
        return $path !== $homepath;
    }

    /**
     * Paths that are LaunchPad persona shells.
     *
     * @param string $path Normalised path starting with /
     */
    public static function is_persona_shell_path(string $path): bool {
        $path = rtrim($path, '/') ?: '/';
        foreach ([self::MENTOR_PATH, self::EMPLOYEE_PATH, self::FELLOW_PATH] as $shell) {
            $shell = rtrim($shell, '/') ?: '/';
            if ($path === $shell) {
                return true;
            }
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
     * Apply persona home to SESSION wantsurl when it is still a default home
     * or a wrong persona shell.
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
        if (!self::should_rewrite_wantsurl($userid, $current === '' ? null : $current)) {
            return false;
        }

        $next = $home->out(false);
        if ($current === $next) {
            return false;
        }
        $SESSION->wantsurl = $next;
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
     * Whether ?noredirect=1 may keep the stock Moodle /my/ page for this home.
     *
     * Employees never: Employer portal is the canonical dashboard (PR #15 parity
     * with Fellowship — persona shell replaces /my/, not a parallel page).
     * Admins (null home), mentors, and fellows may still opt out.
     *
     * @param \moodle_url|null $home From home_url_for()
     */
    public static function should_honor_noredirect(?\moodle_url $home): bool {
        if ($home === null) {
            return true;
        }
        $path = rtrim((string) $home->get_path(), '/') ?: '/';
        $employee = rtrim(self::EMPLOYEE_PATH, '/') ?: '/';
        return $path !== $employee;
    }

    /**
     * Redirect from /my/ to persona home when applicable (no loop).
     *
     * Pass ?noredirect=1 to keep stock Moodle /my/ for mentors/fellows/admins.
     * Employees always redirect to employee.php (noredirect cannot bypass).
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
        // Opt-out only when this persona still allows stock /my/.
        if (!empty(optional_param('noredirect', 0, PARAM_BOOL))
                && self::should_honor_noredirect($home)) {
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

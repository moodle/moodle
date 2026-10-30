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

defined('MOODLE_INTERNAL') || die();

/**
 * Employer portal dashboard context (cohort-gated learner shell).
 *
 * Modern LaunchPad layout over real Moodle data:
 * enrolments, course sections, activities, completion, notifications.
 * No invented curriculum / XP / grades — those stay in Moodle core pages.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class employer_dashboard {

    /** Marker on the outer shell for CSS / tests. */
    public const DATA_REGION = 'local-launchpad-employer-dashboard';

    /**
     * Build template context for the signed-in employer learner.
     *
     * @param \stdClass $user Moodle user record ($USER)
     * @return array Mustache context
     */
    public static function export_for_template(\stdClass $user): array {
        global $CFG;

        require_once($CFG->libdir . '/enrollib.php');
        require_once($CFG->libdir . '/completionlib.php');

        $userid = (int) $user->id;
        $firstname = self::display_firstname($user);
        $fullname = fullname($user);
        $courses = self::assigned_courses($userid);
        $hascourses = $courses !== [];
        $primary = $hascourses ? $courses[0] : null;

        $sections = [];
        $activities = [];
        $nextactivity = null;
        $sectiondone = 0;
        $sectiontotal = 0;
        $heroring = null;

        if ($primary) {
            $courseview = self::course_workspace((int) $primary['id'], $userid);
            $sections = $courseview['sections'];
            $activities = $courseview['activities'];
            $nextactivity = $courseview['next'];
            $sectiondone = $courseview['done'];
            $sectiontotal = $courseview['total'];
            if (!empty($primary['hasprogress'])) {
                $heroring = (int) $primary['progress'];
            } else if ($sectiontotal > 0) {
                $heroring = (int) round(($sectiondone / $sectiontotal) * 100);
            }
        }

        $unread = self::unread_notification_count($userid);
        $urls = self::core_urls($primary);
        $pluginbase = $CFG->wwwroot . '/local/launchpad';

        $jumplist = self::jump_targets($courses, $activities, $urls);

        return [
            'dataregion' => self::DATA_REGION,
            'assets' => $pluginbase . '/assets/img',
            'pluginbase' => $pluginbase,
            'firstname' => $firstname,
            'fullname' => $fullname,
            'initials' => self::initials($fullname),
            'email' => (string) ($user->email ?? ''),
            'rolelabel' => get_string('employer_role_label', 'local_launchpad'),
            'portallabel' => get_string('employer_portal_label', 'local_launchpad'),
            'hascourses' => $hascourses,
            'coursecount' => count($courses),
            'courses' => $courses,
            'primarycourse' => $primary,
            'hasprimary' => $primary !== null,
            'sections' => $sections,
            'hassections' => $sections !== [],
            'activities' => $activities,
            'hasactivities' => $activities !== [],
            'activitydone' => $sectiondone,
            'activitytotal' => $sectiontotal,
            'activitypct' => $sectiontotal > 0 ? (int) round(($sectiondone / $sectiontotal) * 100) : 0,
            'nextactivity' => $nextactivity,
            'hasnext' => $nextactivity !== null,
            'heroring' => $heroring,
            'hasheroring' => $heroring !== null,
            'tiles' => self::tiles($courses, $hascourses, $sectiondone, $sectiontotal, $unread),
            'gates' => self::programme_gates($sections),
            'journey' => self::journey_from_sections($sections, $sectiondone, $sectiontotal),
            'bot_url' => $pluginbase . '/assets/img/bot.webp',
            'navgroups' => self::nav_groups($urls, $primary, $unread),
            'jumplist' => $jumplist,
            'unread' => $unread,
            'hasunread' => $unread > 0,
            'urls' => $urls,
            'empty' => [
                'title' => get_string('employer_empty_title', 'local_launchpad'),
                'body' => get_string('employer_empty_body', 'local_launchpad'),
            ],
            'strings' => self::strings($firstname),
        ];
    }

    /**
     * @param string $firstname
     * @return array<string, string>
     */
    private static function strings(string $firstname): array {
        return [
            'welcome' => get_string('employer_welcome', 'local_launchpad', $firstname),
            'eyebrow' => get_string('employer_eyebrow', 'local_launchpad'),
            'nextquest' => get_string('employer_next_quest', 'local_launchpad'),
            'opencourse' => get_string('employer_open_course', 'local_launchpad'),
            'openactivity' => get_string('employer_open_activity', 'local_launchpad'),
            'mycourses' => get_string('employer_my_courses', 'local_launchpad'),
            'journey' => get_string('employer_journey_title', 'local_launchpad'),
            'journeyhelp' => get_string('employer_journey_help', 'local_launchpad'),
            'opentrack' => get_string('employer_open_track', 'local_launchpad'),
            'assigned' => get_string('employer_assigned_title', 'local_launchpad'),
            'assignedhelp' => get_string('employer_assigned_help', 'local_launchpad'),
            'activities' => get_string('employer_activities_title', 'local_launchpad'),
            'activitieshelp' => get_string('employer_activities_help', 'local_launchpad'),
            'corelinks' => get_string('employer_core_links_title', 'local_launchpad'),
            'corehelp' => get_string('employer_core_links_help', 'local_launchpad'),
            'signout' => get_string('employer_sign_out', 'local_launchpad'),
            'publicsite' => get_string('employer_public_site', 'local_launchpad'),
            'menu' => get_string('employer_menu', 'local_launchpad'),
            'skiptomain' => get_string('employer_skip_main', 'local_launchpad'),
            'search' => get_string('employer_search_placeholder', 'local_launchpad'),
            'notifications' => get_string('employer_nav_notifications', 'local_launchpad'),
            'linkdashboard' => get_string('employer_link_dashboard', 'local_launchpad'),
            'linkgrades' => get_string('employer_link_grades', 'local_launchpad'),
            'linkcalendar' => get_string('employer_link_calendar', 'local_launchpad'),
            'linkmessages' => get_string('employer_link_messages', 'local_launchpad'),
            'linkprofile' => get_string('employer_link_profile', 'local_launchpad'),
            'tip' => get_string('employer_tip', 'local_launchpad'),
            'noactivities' => get_string('employer_no_activities', 'local_launchpad'),
        ];
    }

    /**
     * Moodle core destinations used by the shell.
     *
     * @param array<string, mixed>|null $primary
     * @return array<string, string>
     */
    public static function core_urls(?array $primary = null): array {
        // Keep My courses on this portal (employees are redirected away from /my/).
        // Moodle dashboard uses noredirect so the core page stays reachable.
        $urls = [
            'home' => (new \moodle_url('/local/launchpad/employee.php'))->out(false),
            'mycourses' => (new \moodle_url('/local/launchpad/employee.php', [], 'assigned'))->out(false),
            'mydashboard' => (new \moodle_url('/my/index.php', ['noredirect' => 1]))->out(false),
            'grades' => (new \moodle_url('/grade/report/overview/index.php'))->out(false),
            'calendar' => (new \moodle_url('/calendar/view.php', ['view' => 'month']))->out(false),
            'messages' => (new \moodle_url('/message/index.php'))->out(false),
            'profile' => (new \moodle_url('/user/profile.php'))->out(false),
            'preferences' => (new \moodle_url('/user/preferences.php'))->out(false),
            'notifications' => (new \moodle_url('/message/output/popup/notifications.php'))->out(false),
            'public' => (new \moodle_url('/'))->out(false),
            'logout' => (new \moodle_url('/login/logout.php', ['sesskey' => sesskey()]))->out(false),
            'course' => '',
            'coursegrades' => '',
        ];
        if ($primary) {
            $cid = (int) $primary['id'];
            $urls['course'] = (new \moodle_url('/course/view.php', ['id' => $cid]))->out(false);
            $urls['coursegrades'] = (new \moodle_url('/grade/report/user/index.php', ['id' => $cid]))->out(false);
        }
        return $urls;
    }

    /**
     * Enrolled courses (excludes site). Progress from Moodle completion only.
     *
     * @param int $userid
     * @return list<array<string, mixed>>
     */
    public static function assigned_courses(int $userid): array {
        global $CFG;

        if ($userid <= 0) {
            return [];
        }

        require_once($CFG->libdir . '/enrollib.php');

        $raw = enrol_get_users_courses($userid, true, 'id, fullname, shortname, idnumber, visible');
        $out = [];

        foreach ($raw as $course) {
            if ((int) $course->id === (int) SITEID) {
                continue;
            }
            $ctx = \context_course::instance((int) $course->id);
            if (empty($course->visible) && !has_capability('moodle/course:viewhiddencourses', $ctx, $userid)) {
                continue;
            }

            $idnumber = (string) ($course->idnumber ?? '');
            $islaunchpad = str_starts_with($idnumber, account_persona::COURSE_IDNUMBER_PREFIX);
            $progress = self::course_progress_percent((int) $course->id, $userid);
            $url = (new \moodle_url('/course/view.php', ['id' => (int) $course->id]))->out(false);
            $gradesurl = (new \moodle_url('/grade/report/user/index.php', ['id' => (int) $course->id]))->out(false);

            $out[] = [
                'id' => (int) $course->id,
                'name' => format_string($course->fullname, true, ['context' => $ctx]),
                'shortname' => format_string($course->shortname),
                'idnumber' => $idnumber,
                'islaunchpad' => $islaunchpad,
                'url' => $url,
                'gradesurl' => $gradesurl,
                'hasprogress' => $progress !== null,
                'progress' => $progress ?? 0,
                'progresslabel' => $progress !== null
                    ? get_string('employer_progress_pct', 'local_launchpad', $progress)
                    : get_string('employer_progress_na', 'local_launchpad'),
                'badge' => $islaunchpad
                    ? get_string('employer_badge_track', 'local_launchpad')
                    : get_string('employer_badge_course', 'local_launchpad'),
            ];
        }

        usort($out, static function (array $a, array $b): int {
            if ($a['islaunchpad'] !== $b['islaunchpad']) {
                return $a['islaunchpad'] ? -1 : 1;
            }
            return strcasecmp((string) $a['name'], (string) $b['name']);
        });

        return $out;
    }

    /**
     * Visible Moodle sections + activities for a course the user can access.
     *
     * @param int $courseid
     * @param int $userid
     * @return array{
     *   sections: list<array<string,mixed>>,
     *   activities: list<array<string,mixed>>,
     *   next: ?array<string,mixed>,
     *   done: int,
     *   total: int
     * }
     */
    public static function course_workspace(int $courseid, int $userid): array {
        $empty = ['sections' => [], 'activities' => [], 'next' => null, 'done' => 0, 'total' => 0];
        if ($courseid <= 0 || $userid <= 0) {
            return $empty;
        }

        try {
            $course = get_course($courseid);
            $modinfo = get_fast_modinfo($course, $userid);
            $completion = new \completion_info($course);
            $completionon = $completion->is_enabled();
        } catch (\Throwable $e) {
            return $empty;
        }

        $sections = [];
        $activities = [];
        $done = 0;
        $total = 0;
        $next = null;
        $phaseindex = 0;

        foreach ($modinfo->get_section_info_all() as $section) {
            if ((int) $section->section === 0) {
                // General section: collect activities only (not shown as a programme gate).
                $cms = $modinfo->sections[0] ?? [];
            } else {
                if (!$section->uservisible) {
                    $phaseindex++;
                    $sections[] = [
                        'index' => $phaseindex,
                        'name' => get_section_name($course, $section),
                        'visible' => false,
                        'locked' => true,
                        'chip' => get_string('employer_section_locked', 'local_launchpad'),
                        'chiptype' => 'line',
                        'url' => (new \moodle_url('/course/view.php', [
                            'id' => $courseid,
                            'section' => (int) $section->section,
                        ]))->out(false),
                    ];
                    continue;
                }
                $phaseindex++;
                $sections[] = [
                    'index' => $phaseindex,
                    'name' => get_section_name($course, $section),
                    'visible' => true,
                    'locked' => false,
                    'chip' => get_string('employer_section_open', 'local_launchpad'),
                    'chiptype' => 'ok',
                    'url' => (new \moodle_url('/course/view.php', [
                        'id' => $courseid,
                        'section' => (int) $section->section,
                    ]))->out(false),
                ];
                $cms = $modinfo->sections[$section->section] ?? [];
            }

            foreach ($cms as $cmid) {
                $cm = $modinfo->cms[$cmid] ?? null;
                if (!$cm || !$cm->uservisible || $cm->modname === 'label') {
                    continue;
                }

                $complete = false;
                if ($completionon && $completion->is_enabled($cm)) {
                    $data = $completion->get_data($cm, false, $userid);
                    $complete = ((int) $data->completionstate === COMPLETION_COMPLETE
                        || (int) $data->completionstate === COMPLETION_COMPLETE_PASS);
                }

                $total++;
                if ($complete) {
                    $done++;
                }

                $modname = method_exists($cm, 'get_module_type_name')
                    ? (string) $cm->get_module_type_name()
                    : get_string('modulename', $cm->modname);
                $item = [
                    'id' => (int) $cm->id,
                    'name' => $cm->get_formatted_name(),
                    'modname' => $modname,
                    'modtype' => $cm->modname,
                    'url' => $cm->url ? $cm->url->out(false) : (new \moodle_url('/mod/' . $cm->modname . '/view.php', [
                        'id' => (int) $cm->id,
                    ]))->out(false),
                    'complete' => $complete,
                    'section' => (int) $section->section,
                ];
                $activities[] = $item;
                if (!$complete && $next === null) {
                    $next = $item;
                }
            }
        }

        // Cap activity list on home for readability; full course is in Moodle.
        $activities = array_slice($activities, 0, 12);

        return [
            'sections' => $sections,
            'activities' => $activities,
            'next' => $next,
            'done' => $done,
            'total' => $total,
        ];
    }

    /**
     * Unread popup notifications (Moodle core).
     *
     * @param int $userid
     */
    public static function unread_notification_count(int $userid): int {
        global $DB, $CFG;

        if ($userid <= 0) {
            return 0;
        }
        try {
            if (class_exists('\core_message\api')) {
                // Prefer message API unread conversations when available.
            }
            // Popup notifications table (core).
            if ($DB->get_manager()->table_exists('notifications')) {
                return (int) $DB->count_records_select(
                    'notifications',
                    'useridto = :uid AND timeread IS NULL',
                    ['uid' => $userid]
                );
            }
        } catch (\Throwable $e) {
            debugging('employer_dashboard notifications: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }
        // Fallback: unread messages.
        try {
            require_once($CFG->dirroot . '/message/lib.php');
            if (function_exists('message_count_unread_messages')) {
                return (int) message_count_unread_messages(\core_user::get_user($userid));
            }
        } catch (\Throwable $e) {
            // Ignore.
        }
        return 0;
    }

    /**
     * Jump-to list for topbar search (courses + visible activities + core pages).
     *
     * @param list<array<string,mixed>> $courses
     * @param list<array<string,mixed>> $activities
     * @param array<string,string> $urls
     * @return list<array{label:string,url:string,group:string}>
     */
    private static function jump_targets(array $courses, array $activities, array $urls): array {
        $items = [
            ['label' => get_string('employer_nav_home', 'local_launchpad'), 'url' => $urls['home'], 'group' => 'Portal'],
            ['label' => get_string('employer_nav_mycourses', 'local_launchpad'), 'url' => $urls['mycourses'], 'group' => 'Moodle'],
            ['label' => get_string('employer_nav_grades', 'local_launchpad'), 'url' => $urls['grades'], 'group' => 'Moodle'],
            ['label' => get_string('employer_nav_calendar', 'local_launchpad'), 'url' => $urls['calendar'], 'group' => 'Moodle'],
            ['label' => get_string('employer_nav_messages', 'local_launchpad'), 'url' => $urls['messages'], 'group' => 'Moodle'],
            ['label' => get_string('employer_nav_profile', 'local_launchpad'), 'url' => $urls['profile'], 'group' => 'Moodle'],
            ['label' => get_string('employer_nav_preferences', 'local_launchpad'), 'url' => $urls['preferences'], 'group' => 'Moodle'],
        ];
        foreach ($courses as $c) {
            $items[] = [
                'label' => (string) $c['name'],
                'url' => (string) $c['url'],
                'group' => get_string('employer_jump_courses', 'local_launchpad'),
            ];
        }
        foreach ($activities as $a) {
            $items[] = [
                'label' => $a['modname'] . ' · ' . $a['name'],
                'url' => (string) $a['url'],
                'group' => get_string('employer_jump_activities', 'local_launchpad'),
            ];
        }
        return $items;
    }

    /**
     * @param int $courseid
     * @param int $userid
     */
    public static function course_progress_percent(int $courseid, int $userid): ?int {
        global $CFG;

        try {
            $course = get_course($courseid);
            $info = new \completion_info($course);
            if (!$info->is_enabled()) {
                return null;
            }
            require_once($CFG->dirroot . '/completion/classes/progress.php');
            $pct = \core_completion\progress::get_course_progress_percentage($course, $userid);
            if ($pct === null) {
                return null;
            }
            return (int) round((float) $pct);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * @param list<array<string,mixed>> $courses
     * @param bool $hascourses
     * @param int $actdone
     * @param int $acttotal
     * @param int $unread
     * @return list<array{label:string,value:string,sub:string,icon:string}>
     */
    private static function tiles(
        array $courses,
        bool $hascourses,
        int $actdone,
        int $acttotal,
        int $unread
    ): array {
        if (!$hascourses) {
            return [
                [
                    'label' => get_string('employer_tile_assigned', 'local_launchpad'),
                    'value' => '0',
                    'sub' => get_string('employer_tile_assigned_empty', 'local_launchpad'),
                    'icon' => 'courses',
                ],
                [
                    'label' => get_string('employer_tile_status', 'local_launchpad'),
                    'value' => get_string('employer_tile_waiting', 'local_launchpad'),
                    'sub' => get_string('employer_tile_waiting_sub', 'local_launchpad'),
                    'icon' => 'clock',
                ],
                [
                    'label' => get_string('employer_nav_notifications', 'local_launchpad'),
                    'value' => (string) $unread,
                    'sub' => get_string('employer_tile_notif_sub', 'local_launchpad'),
                    'icon' => 'bell',
                ],
            ];
        }

        $avg = null;
        $sum = 0;
        $n = 0;
        foreach ($courses as $c) {
            if (!empty($c['hasprogress'])) {
                $sum += (int) $c['progress'];
                $n++;
            }
        }
        if ($n > 0) {
            $avg = (int) round($sum / $n);
        }

        return [
            [
                'label' => get_string('employer_tile_assigned', 'local_launchpad'),
                'value' => (string) count($courses),
                'sub' => get_string('employer_tile_assigned_sub', 'local_launchpad'),
                'icon' => 'courses',
            ],
            [
                'label' => get_string('employer_tile_activities', 'local_launchpad'),
                'value' => $acttotal > 0 ? $actdone . '/' . $acttotal : '—',
                'sub' => get_string('employer_tile_activities_sub', 'local_launchpad'),
                'icon' => 'check',
            ],
            [
                'label' => get_string('employer_tile_avg', 'local_launchpad'),
                'value' => $avg !== null ? $avg . '%' : ($acttotal > 0 ? (int) round(($actdone / $acttotal) * 100) . '%' : '—'),
                'sub' => get_string('employer_tile_avg_sub', 'local_launchpad'),
                'icon' => 'chart',
            ],
            [
                'label' => get_string('employer_nav_notifications', 'local_launchpad'),
                'value' => (string) $unread,
                'sub' => get_string('employer_tile_notif_sub', 'local_launchpad'),
                'icon' => 'bell',
            ],
        ];
    }

    /**
     * "Your journey" SVG — same geometry / status rules as Fellowship home.
     *
     * Uses up to four Moodle sections (or blueprint spine when none yet).
     *
     * @param list<array<string,mixed>> $sections
     * @param int $done Completed activities
     * @param int $total Trackable activities
     * @param bool $compact Home card compact height
     * @return array<string, mixed>
     */
    private static function journey_from_sections(
        array $sections,
        int $done,
        int $total,
        bool $compact = true
    ): array {
        $spine = [];
        if ($sections !== []) {
            foreach (array_slice($sections, 0, 4) as $s) {
                $name = (string) ($s['name'] ?? '');
                $spine[] = [
                    'name' => $name,
                    'short' => \core_text::strlen($name) > 18
                        ? \core_text::substr($name, 0, 16) . '…'
                        : $name,
                    'locked' => !empty($s['locked']),
                    'done' => empty($s['locked']) && (($s['chiptype'] ?? '') === 'ok') && $done > 0
                        && $total > 0 && $done >= $total,
                ];
            }
        }
        while (count($spine) < 4) {
            $phases = blueprint::phase_sections();
            $nums = array_keys($phases);
            $idx = count($spine);
            $num = $nums[$idx] ?? ($idx + 1);
            $name = $phases[$num] ?? ('Phase ' . ($idx + 1));
            $spine[] = [
                'name' => $name,
                'short' => \core_text::strlen($name) > 18
                    ? \core_text::substr($name, 0, 16) . '…'
                    : $name,
                'locked' => true,
                'done' => false,
            ];
        }

        // First unlocked incomplete = current; prior unlocked with full course done = done.
        // Prefer sequential lock: advance current only after earlier gates complete.
        $gates = [];
        $foundcurrent = false;
        $phasedone = [];
        foreach ($spine as $i => $meta) {
            // Gate done when not locked and activity progress covers this phase index.
            $phasedone[$i] = !empty($meta['done']);
            if (!$phasedone[$i] && empty($meta['locked']) && $total > 0) {
                // Heuristic: distribute completion across gates for path fill.
                $phasedone[$i] = ($done / max(1, $total)) > (($i + 0.85) / 4);
            }
        }
        // Ensure sequential: a gate cannot be done if a prior one is not.
        for ($i = 1; $i < 4; $i++) {
            if (empty($phasedone[$i - 1])) {
                $phasedone[$i] = false;
            }
        }

        foreach ($spine as $i => $meta) {
            $isdone = !empty($phasedone[$i]);
            $status = 'lock';
            $mark = '🔒';
            if ($isdone) {
                $status = 'done';
                $mark = '✓';
            } else if (!$foundcurrent) {
                $status = 'cur';
                $mark = (string) ($i + 1);
                $foundcurrent = true;
            }
            $gates[] = [
                'name' => $meta['name'],
                'short' => $meta['short'],
                'status' => $status,
                'mark' => $mark,
                'cx' => 0,
                'cy' => 0,
                'show_sublabel' => false,
                'sublabel' => '',
                'ring_r' => $compact ? 24 : 30,
                'core_r' => $compact ? 17 : 22,
                'mark_y' => $compact ? 6 : 7,
                'label_y' => $compact ? 44 : 54,
            ];
        }

        $viewh = $compact ? 150 : 210;
        $pts = [
            [90.0, $viewh * 0.62],
            [360.0, $viewh * 0.30],
            [640.0, $viewh * 0.66],
            [910.0, $viewh * 0.32],
        ];
        $pathd = sprintf(
            'M%s,%s C230,%s 240,%s %s,%s S520,%s %s,%s S800,%s %s,%s',
            $pts[0][0],
            $pts[0][1],
            $viewh * 0.05,
            $viewh * 0.85,
            $pts[1][0],
            $pts[1][1],
            $viewh * 0.98,
            $pts[2][0],
            $pts[2][1],
            $viewh * 0.02,
            $pts[3][0],
            $pts[3][1]
        );
        foreach ($gates as $idx => &$gate) {
            $gate['cx'] = (int) round($pts[$idx][0]);
            $gate['cy'] = (int) round($pts[$idx][1]);
        }
        unset($gate);

        if (!empty($phasedone[0])) {
            $pathprogress = 0.36;
            $extradone = 0;
            foreach ([1, 2, 3] as $n) {
                if (!empty($phasedone[$n])) {
                    $extradone++;
                }
            }
            $pathprogress = min(1.0, 0.36 + ($extradone * 0.21));
        } else if ($total > 0) {
            $pathprogress = min(0.30, ($done / $total) * 0.30);
        } else {
            $pathprogress = 0.0;
        }

        $botlift = $compact ? 62 : 78;
        return [
            'has_gates' => true,
            'compact' => $compact,
            'gates' => $gates,
            'path_d' => $pathd,
            'dash_offset' => number_format(1.0 - $pathprogress, 3, '.', ''),
            'view_h' => $viewh,
            'bot_left' => round(($pts[0][0] / 1000) * 100, 2) . '%',
            'bot_top' => round((($pts[0][1] - $botlift) / $viewh) * 100, 2) . '%',
        ];
    }

    /**
     * Prefer real Moodle sections; fall back to blueprint gate labels.
     *
     * @param list<array<string,mixed>> $sections
     * @return list<array{index:int,name:string,short:string,locked:bool,chiptype:string,chip:string,url:string}>
     */
    private static function programme_gates(array $sections): array {
        if ($sections !== []) {
            $gates = [];
            foreach ($sections as $s) {
                $gates[] = [
                    'index' => (int) $s['index'],
                    'name' => (string) $s['name'],
                    'short' => (string) $s['name'],
                    'locked' => !empty($s['locked']),
                    'chiptype' => (string) ($s['chiptype'] ?? 'line'),
                    'chip' => (string) ($s['chip'] ?? ''),
                    'url' => (string) ($s['url'] ?? '#'),
                    'hasurl' => !empty($s['url']),
                ];
            }
            return $gates;
        }

        $phases = blueprint::phase_sections();
        $gates = [];
        foreach ($phases as $i => $name) {
            $gates[] = [
                'index' => (int) $i,
                'name' => $name,
                'short' => $name,
                'locked' => true,
                'chiptype' => 'line',
                'chip' => get_string('employer_section_locked', 'local_launchpad'),
                'url' => '',
                'hasurl' => false,
            ];
        }
        return $gates;
    }

    /**
     * @param array<string, string> $urls
     * @param array<string, mixed>|null $primary
     * @param int $unread
     * @return list<array{label:string,items:list<array<string,mixed>>}>
     */
    private static function nav_groups(array $urls, ?array $primary, int $unread): array {
        $icon = static function (string $paths): string {
            return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">'
                . $paths . '</svg>';
        };

        $learn = [
            [
                'key' => 'home',
                'label' => get_string('employer_nav_home', 'local_launchpad'),
                'url' => $urls['home'],
                'current' => true,
                'icon_svg' => $icon('<path d="M3 11.5L12 4l9 7.5"/><path d="M6 10.5V20h12v-9.5"/>'),
                'badge' => '',
                'hasbadge' => false,
            ],
            [
                'key' => 'mycourses',
                'label' => get_string('employer_nav_mycourses', 'local_launchpad'),
                'url' => $urls['mycourses'],
                'current' => false,
                'icon_svg' => $icon('<path d="M4 6h16M4 12h10M4 18h13"/><circle cx="18" cy="12" r="2"/><circle cx="19" cy="18" r="2"/>'),
                'badge' => '',
                'hasbadge' => false,
            ],
        ];
        if ($primary) {
            $learn[] = [
                'key' => 'course',
                'label' => get_string('employer_nav_track', 'local_launchpad'),
                'url' => $urls['course'],
                'current' => false,
                'icon_svg' => $icon('<path d="M4 5h16v14H4z"/><path d="M8 9h8"/><path d="M8 13h5"/>'),
                'badge' => '',
                'hasbadge' => false,
            ];
        }

        $record = [
            [
                'key' => 'grades',
                'label' => get_string('employer_nav_grades', 'local_launchpad'),
                'url' => $primary ? $urls['coursegrades'] : $urls['grades'],
                'current' => false,
                'icon_svg' => $icon('<path d="M4 19V5"/><path d="M4 19h16"/><path d="M8 15v-4"/><path d="M12 15V8"/><path d="M16 15v-6"/>'),
                'badge' => '',
                'hasbadge' => false,
            ],
            [
                'key' => 'calendar',
                'label' => get_string('employer_nav_calendar', 'local_launchpad'),
                'url' => $urls['calendar'],
                'current' => false,
                'icon_svg' => $icon('<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M8 3v4M16 3v4M4 10h16"/>'),
                'badge' => '',
                'hasbadge' => false,
            ],
            [
                'key' => 'messages',
                'label' => get_string('employer_nav_messages', 'local_launchpad'),
                'url' => $urls['messages'],
                'current' => false,
                'icon_svg' => $icon('<path d="M5 5h14v10H9l-4 4V5z"/>'),
                'badge' => '',
                'hasbadge' => false,
            ],
            [
                'key' => 'notifications',
                'label' => get_string('employer_nav_notifications', 'local_launchpad'),
                'url' => $urls['notifications'],
                'current' => false,
                'icon_svg' => $icon('<path d="M6 9a6 6 0 0112 0c0 7 3 7 3 9H3c0-2 3-2 3-9"/><path d="M10 20a2 2 0 004 0"/>'),
                'badge' => $unread > 0 ? (string) $unread : '',
                'hasbadge' => $unread > 0,
            ],
        ];

        $you = [
            [
                'key' => 'profile',
                'label' => get_string('employer_nav_profile', 'local_launchpad'),
                'url' => $urls['profile'],
                'current' => false,
                'icon_svg' => $icon('<circle cx="12" cy="8" r="3.5"/><path d="M5 19c1.5-3 4-4.5 7-4.5s5.5 1.5 7 4.5"/>'),
                'badge' => '',
                'hasbadge' => false,
            ],
            [
                'key' => 'preferences',
                'label' => get_string('employer_nav_preferences', 'local_launchpad'),
                'url' => $urls['preferences'],
                'current' => false,
                'icon_svg' => $icon('<circle cx="12" cy="12" r="3"/><path d="M12 3v2M12 19v2M3 12h2M19 12h2M5.6 5.6l1.4 1.4M17 17l1.4 1.4M5.6 18.4l1.4-1.4M17 7l1.4-1.4"/>'),
                'badge' => '',
                'hasbadge' => false,
            ],
        ];

        return [
            [
                'label' => get_string('employer_navgroup_learn', 'local_launchpad'),
                'items' => $learn,
            ],
            [
                'label' => get_string('employer_navgroup_record', 'local_launchpad'),
                'items' => $record,
            ],
            [
                'label' => get_string('employer_navgroup_you', 'local_launchpad'),
                'items' => $you,
            ],
        ];
    }

    /**
     * @param \stdClass $user
     */
    private static function display_firstname(\stdClass $user): string {
        $name = trim((string) ($user->firstname ?? ''));
        if ($name !== '') {
            return $name;
        }
        return fullname($user);
    }

    /**
     * @param string $fullname
     */
    private static function initials(string $fullname): string {
        $parts = preg_split('/\s+/', trim($fullname)) ?: [];
        $letters = '';
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $letters .= \core_text::strtoupper(\core_text::substr($part, 0, 1));
            if (\core_text::strlen($letters) >= 2) {
                break;
            }
        }
        return $letters !== '' ? $letters : 'U';
    }
}

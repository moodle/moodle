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
     * @param string $view home|courses|course
     * @param int $courseid Course id when $view === course
     * @return array Mustache context
     */
    public static function export_for_template(\stdClass $user, string $view = 'home', int $courseid = 0): array {
        global $CFG;

        require_once($CFG->libdir . '/enrollib.php');
        require_once($CFG->libdir . '/completionlib.php');

        $view = in_array($view, ['home', 'courses', 'course'], true) ? $view : 'home';
        $userid = (int) $user->id;
        $firstname = self::display_firstname($user);
        $fullname = fullname($user);
        $courses = self::assigned_courses($userid);
        $hascourses = $courses !== [];
        $primary = self::pick_primary_course($courses);

        // Selected enrolment for course detail (or optional home focus via ?id=).
        $detail = null;
        $focused = null;
        if ($courseid > 0 && ($view === 'course' || $view === 'home')) {
            foreach ($courses as $c) {
                if ((int) $c['id'] === $courseid) {
                    $focused = $c;
                    if ($view === 'course') {
                        $detail = $c;
                    }
                    break;
                }
            }
        }
        $workspacecourse = $detail ?? $focused ?? $primary;

        $sections = [];
        $activities = [];
        $nextactivity = null;
        $sectiondone = 0;
        $sectiontotal = 0;
        $heroring = null;

        if ($workspacecourse) {
            // Home keeps a short list; course detail shows the full Moodle outline.
            $actlimit = ($view === 'course') ? 0 : 12;
            $courseview = self::course_workspace((int) $workspacecourse['id'], $userid, $actlimit);
            $sections = $courseview['sections'];
            $activities = $courseview['activities'];
            $nextactivity = $courseview['next'];
            $sectiondone = $courseview['done'];
            $sectiontotal = $courseview['total'];
            if (!empty($workspacecourse['hasprogress'])) {
                $heroring = (int) $workspacecourse['progress'];
            } else if ($sectiontotal > 0) {
                $heroring = (int) round(($sectiondone / $sectiontotal) * 100);
            }
        }

        $unread = self::unread_notification_count($userid);
        $urls = self::core_urls($workspacecourse ?? $primary);
        $pluginbase = $CFG->wwwroot . '/local/launchpad';
        $strings = self::strings($firstname);
        $sidecard = self::side_card($urls, $heroring, count($courses));

        $activitypct = $sectiontotal > 0 ? (int) round(($sectiondone / $sectiontotal) * 100) : 0;
        $progresspct = $heroring !== null ? $heroring : $activitypct;
        $hasprogress = $heroring !== null || $sectiontotal > 0;
        $progresslabel = $hasprogress
            ? self::str('employer_progress_pct', $progresspct, '{$a}% complete')
            : self::str('employer_progress_na', null, 'Open course for activities');

        $gates = ($workspacecourse !== null)
            ? self::programme_gates($sections, $sectiondone, $sectiontotal)
            : [];
        $journey = ($workspacecourse !== null && $sections !== [])
            ? self::journey_from_sections($sections, $sectiondone, $sectiontotal, true)
            : ['has_gates' => false];
        $badges = self::moodle_badges_summary($userid, 6);
        $homesummary = self::home_summary(
            $gates,
            $activities,
            $badges,
            $sectiondone,
            $sectiontotal,
            $heroring,
            $hascourses
        );

        // Portal-first URL contract (stay in Employer shell):
        // - detail_url  → employee.php?view=course&id=
        // - continue    → next activity URL when learning (Moodle mod; unavoidable for quizzes)
        // - No course/view.php as primary CTA — portal owns the course overview UI.
        $opencourse = self::str('employer_open_course', null, 'Open course');
        $continuebtn = self::str('employer_cta_continue', null, 'Continue');
        $resume = self::str('employer_cta_resume', null, 'Resume learning');

        $homecard = self::mycourse_card(
            $primary,
            $nextactivity,
            // Home resume: next activity if any, else portal course page.
            $nextactivity['url'] ?? (string) ($primary['detail_url'] ?? ''),
            $nextactivity ? $resume : $opencourse,
            false
        );
        $coursecards = [];
        foreach ($courses as $c) {
            // List: open the portal course page only.
            $coursecards[] = self::mycourse_card(
                $c,
                null,
                (string) ($c['detail_url'] ?? ''),
                $opencourse,
                false
            );
        }
        $detailcard = [];
        $coursephases = [];
        $courseswitch = [];
        if ($detail !== null) {
            $portaldetail = (string) ($detail['detail_url'] ?? '');
            if ($nextactivity !== null) {
                $detailcard = self::mycourse_card(
                    $detail,
                    $nextactivity,
                    (string) ($nextactivity['url'] ?? $portaldetail),
                    $continuebtn,
                    false
                );
            } else {
                $detailcard = self::mycourse_card(
                    $detail,
                    null,
                    $portaldetail,
                    $opencourse,
                    false
                );
            }
            $coursephases = self::course_phases($sections, $activities, $gates);
            // Journey / gates stay on this portal page (section anchors).
            foreach ($gates as $gi => $gate) {
                $sec = (int) ($gate['sectionnum'] ?? 0);
                if ($sec > 0) {
                    $gates[$gi]['url'] = '#section-' . $sec;
                    $gates[$gi]['hasurl'] = true;
                }
            }
            if (!empty($journey['gates']) && is_array($journey['gates'])) {
                foreach ($journey['gates'] as $ji => $jgate) {
                    $sec = (int) ($jgate['sectionnum'] ?? $jgate['index'] ?? 0);
                    // Prefer sectionnum from matching programme gate.
                    foreach ($gates as $gate) {
                        if ((int) ($gate['index'] ?? 0) === (int) ($jgate['index'] ?? -1)) {
                            $sec = (int) ($gate['sectionnum'] ?? $sec);
                            break;
                        }
                    }
                    if ($sec > 0) {
                        $journey['gates'][$ji]['url'] = '#section-' . $sec;
                    }
                }
            }
            foreach ($courses as $c) {
                $cid = (int) ($c['id'] ?? 0);
                $courseswitch[] = [
                    'id' => $cid,
                    'fullname' => (string) ($c['name'] ?? ''),
                    'url' => (string) ($c['detail_url'] ?? ''),
                    'selected' => $cid === (int) ($detail['id'] ?? 0),
                ];
            }
        }

        // Decorate activity rows when listed flat (home) or as fallback.
        if ($activities !== []) {
            $activities = array_map([self::class, 'decorate_activity_row'], $activities);
        }

        $pagetitles = [
            'home' => self::str('employer_nav_home', null, 'Home'),
            'courses' => self::str('employer_my_courses', null, 'My courses'),
            'course' => $detail
                ? (string) $detail['name']
                : self::str('employer_my_courses', null, 'My courses'),
        ];

        return array_merge([
            'dataregion' => self::DATA_REGION,
            'assets' => $pluginbase . '/assets/img',
            'pluginbase' => $pluginbase,
            'firstname' => $firstname,
            'fullname' => $fullname,
            'displayname' => $fullname,
            'initials' => self::initials($fullname),
            'email' => (string) ($user->email ?? ''),
            'rolelabel' => self::str('employer_role_label', null, 'Employee · Upskilling'),
            'portallabel' => self::str('employer_portal_label', null, 'Employer portal'),
            'pagetitle' => $pagetitles[$view] ?? $pagetitles['home'],
            'greeting' => self::greeting_for($firstname),
            // Figma hero: "Welcome back, {Name}" with name tinted.
            'welcome_html' => self::str('employer_welcome_prefix', null, 'Welcome back,')
                . ' <span class="lp-hero-name">' . s($firstname) . '</span>',
            'hero_image' => $pluginbase . '/assets/img/employer-hero-desk.jpg',
            'hasquest' => $nextactivity !== null || $primary !== null,
            'quest' => self::hero_quest($nextactivity, $primary, $progresspct, $hasprogress),
            'view' => $view,
            'is_home' => $view === 'home',
            'is_courses' => $view === 'courses',
            'is_course' => $view === 'course',
            'hascourses' => $hascourses,
            'coursecount' => count($courses),
            'coursescountlabel' => self::str(
                'employer_courses_count',
                count($courses),
                '{$a} enrolled'
            ),
            'courses' => $courses,
            'coursecards' => $coursecards,
            'primarycourse' => $primary,
            'hasprimary' => $primary !== null,
            'mycourse' => $homecard,
            'hasmycourse' => $primary !== null,
            'homecourses' => $courses,
            'hashomecourses' => $hascourses,
            'detail' => $detail ?? [],
            'hasdetail' => $detail !== null,
            'detailcard' => $detailcard,
            'course_phases' => $coursephases,
            'has_course_phases' => $coursephases !== [],
            'first_section_anchor' => $coursephases !== []
                ? (string) ($coursephases[0]['anchor'] ?? '')
                : '',
            'has_first_section' => $coursephases !== [] && !empty($coursephases[0]['anchor']),
            'course_switch' => $courseswitch,
            'multi_course' => count($courseswitch) > 1,
            'sections' => $sections,
            'hassections' => $sections !== [],
            'activities' => $activities,
            'hasactivities' => $activities !== [],
            'activitydone' => $sectiondone,
            'activitytotal' => $sectiontotal,
            'activitypct' => $progresspct,
            'progresslabel' => $progresslabel,
            'nextactivity' => $nextactivity,
            'hasnext' => $nextactivity !== null,
            'heroring' => $homesummary['progresspct'] ?? $progresspct,
            'hasheroring' => $hascourses,
            'sidecard' => $sidecard,
            'gates' => $gates,
            'hasgates' => $gates !== [],
            'journey' => $journey,
            'badges' => $badges['items'],
            'hasbadges' => $badges['hasbadges'],
            'badges_total' => $badges['total'],
            'bot_url' => $pluginbase . '/assets/img/bot.webp',
            'orbit_tip' => self::orbit_tip_for($view, $hascourses),
            'nav_groups' => self::nav_groups($urls, $primary, $unread, $view),
            'unread' => $unread,
            'hasunread' => $unread > 0,
            'notifications_label' => $unread > 0
                ? self::str('employer_notifications_unread', $unread, 'Notifications, {$a} unread')
                : self::str('employer_nav_notifications', null, 'Notifications'),
            'urls' => $urls,
            'empty' => [
                'title' => self::str('employer_empty_title', null, 'No courses assigned yet'),
                'body' => self::str('employer_empty_body', null, 'When your organisation enrols you, your courses will show here.'),
            ],
            'strings' => $strings,
        ], $homesummary);
    }

    /**
     * @param string $firstname
     * @return array<string, string>
     */
    private static function strings(string $firstname): array {
        return [
            'welcome' => self::str('employer_welcome', $firstname, 'Welcome back, {$a}'),
            'eyebrow' => self::str('employer_eyebrow', null, 'Employer upskilling'),
            'nextquest' => self::str('employer_next_quest', null, 'Continue learning'),
            'opencourse' => self::str('employer_open_course', null, 'Open course'),
            'openinmoodle' => self::str('employer_open_in_moodle', null, 'Open in Moodle'),
            'opencoursehelp' => self::str(
                'employer_open_course_help',
                null,
                'Open in Moodle loads the real Moodle course page. Quizzes, assigns, files, and grades stay in Moodle.'
            ),
            'courseeyebrow' => self::str('employer_course_eyebrow', null, 'My course'),
            'courselead' => self::str(
                'employer_course_lead_default',
                null,
                'Work through your assigned Moodle course. Activities open in Moodle. Sections follow Moodle visibility.'
            ),
            'nativnote' => self::str(
                'employer_native_activities_note',
                null,
                'Activities open in Moodle. Completion and grades stay on Moodle pages.'
            ),
            'moodletoolshelp' => self::str(
                'employer_moodle_tools_help',
                null,
                'Jump to standard Moodle tools for this course. LaunchPad does not replace them.'
            ),
            'moodlefunctions' => self::str('employer_moodle_functions', null, 'Moodle functions'),
            'moodlefunctionsheader' => self::str(
                'employer_moodle_functions_help',
                null,
                'These open Moodle pages. This portal only summarises enrolments, sections, and completion.'
            ),
            'phasesheading' => self::str('employer_phases_heading', null, 'Sections & activities'),
            'phaseshelp' => self::str(
                'employer_phases_help',
                null,
                'Complete the activities in order to build your skills.'
            ),
            'linkbadges' => self::str('employer_nav_badges', null, 'Badges'),
            'openactivity' => self::str('employer_open_activity', null, 'Open activity'),
            'openactivitymoodle' => self::str(
                'employer_open_activity_moodle',
                null,
                'Opens this activity'
            ),
            'backtocourse' => self::str('employer_back_to_course', null, 'Back to course'),
            'openlink' => self::str('employer_open_link', null, 'Open link'),
            'activityempty' => self::str(
                'employer_activity_empty',
                null,
                'No content has been added to this activity yet.'
            ),
            'contentcomingsoon' => self::str(
                'employer_content_coming_soon',
                null,
                'Content coming soon'
            ),
            'activityenginehelp' => self::str(
                'employer_activity_engine_help',
                null,
                'This activity uses Moodle tools (quiz, assignment, forum). Continue to complete it.'
            ),
            'opensectionmoodle' => self::str(
                'employer_open_section_moodle',
                null,
                'Opens this section in the Moodle course'
            ),
            'resume' => self::str('employer_cta_resume', null, 'Resume learning'),
            'viewcourse' => self::str('employer_cta_view_course', null, 'View course'),
            'mycourses' => self::str('employer_my_courses', null, 'My courses'),
            'journey' => self::str('employer_journey_title', null, 'Your course sections'),
            'journeyhelp' => self::str('employer_journey_help', null, ''),
            'opentrack' => self::str('employer_open_track', null, 'Open course'),
            'assigned' => self::str('employer_my_courses', null, 'My courses'),
            'assignedhelp' => self::str('employer_assigned_help', null, ''),
            'courseshelp' => self::str(
                'employer_courses_help',
                null,
                'View details stays in this portal. Open in Moodle opens the Moodle course page for real learning work.'
            ),
            'courseslegend_details' => self::str(
                'employer_courses_legend_details',
                null,
                'Portal overview: sections, activity list, Moodle links'
            ),
            'courseslegend_moodle' => self::str(
                'employer_courses_legend_moodle',
                null,
                'Moodle course home (same as Fellowship “Open in Moodle”)'
            ),
            'courseseyebrow' => self::str('employer_courses_eyebrow', null, 'Learning'),
            'continuebtn' => self::str('employer_cta_continue', null, 'Continue'),
            'viewdetails' => self::str('employer_cta_view_details', null, 'View details'),
            'nextprefix' => self::str('employer_next_prefix', null, 'Next:'),
            'navhome' => self::str('employer_nav_home', null, 'Home'),
            'coursenotfound' => self::str('employer_course_not_found', null, 'Course not found'),
            'coursenotfoundhelp' => self::str('employer_course_not_found_help', null, 'That course is not in your enrolments.'),
            'seeallcourses' => self::str('employer_see_all_courses', null, 'See all courses'),
            'activities' => self::str('employer_activities_title', null, 'Activities'),
            'activitieshelp' => self::str(
                'employer_activities_help',
                null,
                'Each row opens the Moodle activity page. Completion ticks come from Moodle when tracking is on.'
            ),
            'corelinks' => self::str('employer_core_links_title', null, 'Moodle tools'),
            'corehelp' => self::str(
                'employer_core_links_help',
                null,
                'These links open standard Moodle pages. LaunchPad does not replace grades, calendar, or messages.'
            ),
            'signout' => self::str('employer_sign_out', null, 'Sign out'),
            'publicsite' => self::str('employer_public_site', null, 'Public site'),
            'menu' => self::str('employer_menu', null, 'Menu'),
            'skiptomain' => self::str('employer_skip_main', null, 'Skip to main content'),
            'navaria' => self::str('employer_nav_aria', null, 'Employer portal navigation'),
            'notifications' => self::str('employer_nav_notifications', null, 'Notifications'),
            'linkdashboard' => self::str('employer_link_dashboard', null, 'Dashboard'),
            'linkgrades' => self::str('employer_link_grades', null, 'Grades'),
            'linkcalendar' => self::str('employer_link_calendar', null, 'Calendar'),
            'linkmessages' => self::str('employer_link_messages', null, 'Messages'),
            'linkprofile' => self::str('employer_link_profile', null, 'Profile'),
            'tip' => self::str('employer_tip', null, ''),
            'noactivities' => self::str('employer_no_activities', null, 'No activities yet'),
            'sections' => self::str('employer_sections_title', null, 'Course sections'),
            'sectionshelp' => self::str('employer_sections_help', null, ''),
            'overview' => self::str('employer_overview_title', null, 'At a glance'),
            'alldone' => self::str('employer_all_tracked_done', null, 'All tracked activities complete'),
            'continue' => self::str('employer_continue_title', null, 'Continue learning'),
            'continuehelp' => self::str('employer_continue_help', null, ''),
            'progresslabel' => self::str('employer_ring_label', null, 'Progress'),
            'badges' => self::str('employer_badges_title', null, 'Your badges'),
            'badgeshelp' => self::str('employer_badges_help', null, ''),
            'badgesempty' => self::str('employer_badges_empty', null, 'No badges earned yet.'),
            'badgesemptyhint' => self::str('employer_badges_empty_hint', null, 'Complete course criteria in Moodle to unlock them.'),
            'badgesall' => self::str('employer_badges_all', null, 'My badges'),
            'recentbadges' => self::str('employer_recent_badges', null, 'Recent badges'),
            'sectioncurrent' => self::str('employer_section_current', null, 'Current'),
            'statbadges' => self::str('employer_stat_badges', null, 'Badges earned'),
            'statrequired' => self::str('employer_stat_required', null, 'Activities done'),
            'statgate' => self::str('employer_stat_gate', null, 'Current section'),
            'complete' => self::str('employer_activity_complete', null, 'Complete'),
            'incomplete' => self::str('employer_activity_incomplete', null, 'To do'),
            'nottracked' => self::str('employer_activity_not_tracked', null, 'Not tracked'),
            'homelead' => self::str('employer_home_lead', null, 'Continue from where you left off in your assigned Moodle courses.'),
            'yourcourses' => self::str('employer_your_courses', null, 'Your courses'),
            'yourcourseshelp' => self::str(
                'employer_your_courses_help',
                null,
                'Continue learning where you left off'
            ),
            'sectionshelpshort' => self::str('employer_sections_help_short', null, 'Your learning journey'),
            'badgeshelpshort' => self::str(
                'employer_badges_help_short',
                null,
                'Earn badges by completing course activities'
            ),
            'openfullcourse' => self::str('employer_open_full_course', null, 'Open full course in Moodle'),
            'currentsectionbadge' => self::str('employer_section_current_badge', null, 'Current section'),
            'orbithide' => self::str('employer_orbit_hide', null, 'Hide'),
            'orbitdismiss' => self::str('employer_orbit_dismiss', null, 'Dismiss'),
            'orbitaria' => self::str('employer_orbit_aria', null, 'LaunchPad guide'),
        ];
    }

    /**
     * Recent Moodle badges for the home strip (core badges only — no invented XP).
     *
     * @param int $userid
     * @param int $limit
     * @return array{enabled:bool,hasbadges:bool,earned:int,total:int,items:list<array<string,mixed>>}
     */
    public static function moodle_badges_summary(int $userid, int $limit = 6): array {
        global $CFG;

        $empty = [
            'enabled' => false,
            'hasbadges' => false,
            'earned' => 0,
            'total' => 0,
            'items' => [],
        ];
        if ($userid <= 0 || empty($CFG->enablebadges)) {
            return $empty;
        }

        try {
            require_once($CFG->libdir . '/badgeslib.php');
            $all = badges_get_user_badges($userid, 0, 0, 0);
            $total = count($all);
            $slice = array_slice(array_values($all), 0, max(1, $limit));
            $items = [];
            foreach ($slice as $badge) {
                $context = ((int) $badge->type === BADGE_TYPE_SITE)
                    ? \context_system::instance()
                    : \context_course::instance((int) $badge->courseid);
                $image = \moodle_url::make_pluginfile_url(
                    $context->id,
                    'badges',
                    'badgeimage',
                    (int) $badge->id,
                    '/',
                    'f2'
                )->out(false);
                $hash = (string) ($badge->uniquehash ?? '');
                $items[] = [
                    'id' => (int) $badge->id,
                    'name' => self::plain_text(format_string($badge->name)),
                    'detail' => !empty($badge->dateissued)
                        ? userdate((int) $badge->dateissued, get_string('strftimedate', 'langconfig'))
                        : self::str('employer_badge_earned', null, 'Earned'),
                    'imageurl' => $image,
                    'url' => $hash !== ''
                        ? (new \moodle_url('/badges/badge.php', ['hash' => $hash]))->out(false)
                        : (new \moodle_url('/badges/mybadges.php'))->out(false),
                    'css' => 'on',
                ];
            }
            return [
                'enabled' => true,
                'hasbadges' => $items !== [],
                'earned' => $total,
                'total' => $total,
                'items' => $items,
            ];
        } catch (\Throwable $e) {
            debugging('employer_dashboard badges: ' . $e->getMessage(), DEBUG_DEVELOPER);
            return $empty;
        }
    }

    /**
     * Decode Moodle format_string / HTML entities for Mustache {{ }} (avoids &amp;).
     */
    private static function plain_text(string $value): string {
        $decoded = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        return trim(strip_tags($decoded));
    }

    /**
     * Resolve a local_launchpad string; never return raw [[identifier]] placeholders.
     *
     * @param string $identifier
     * @param string|object|array|null $a
     * @param string $fallback Plain text if the lang string is missing / uncached
     */
    private static function str(string $identifier, $a = null, string $fallback = ''): string {
        if (get_string_manager()->string_exists($identifier, 'local_launchpad')) {
            $out = get_string($identifier, 'local_launchpad', $a);
            // Stale lang cache can still emit [[key]] — never show that in UI.
            if (is_string($out) && $out !== '' && strpos($out, '[[') === false) {
                return $out;
            }
        }
        if ($fallback !== '') {
            if (is_object($a) || is_array($a)) {
                $vars = (array) $a;
                return preg_replace_callback('/\{\$a(?:->(\w+))?\}/', static function (array $m) use ($vars): string {
                    if ($m[1] ?? '') {
                        return (string) ($vars[$m[1]] ?? '');
                    }
                    return '';
                }, $fallback) ?? $fallback;
            }
            if ($a !== null && strpos($fallback, '{$a}') !== false) {
                return str_replace('{$a}', (string) $a, $fallback);
            }
            return $fallback;
        }
        return is_scalar($a) ? (string) $a : '';
    }

    /**
     * Glass "Continue learning" strip for the Figma employer hero.
     *
     * @param array<string,mixed>|null $next
     * @param array<string,mixed>|null $primary
     * @return array<string,mixed>
     */
    private static function hero_quest(?array $next, ?array $primary, int $progresspct, bool $hasprogress): array {
        if ($next !== null) {
            $meta = self::plain_text((string) ($next['modname'] ?? ''));
            if ($primary !== null && !empty($primary['name'])) {
                $meta .= ($meta !== '' ? ' · ' : '') . (string) $primary['name'];
            }
            return [
                'title' => self::plain_text((string) ($next['name'] ?? '')),
                'meta' => $meta,
                'url' => (string) ($next['url'] ?? ''),
                'cta' => self::str('employer_cta_resume', null, 'Resume learning'),
                'hasprogress' => $hasprogress,
                'progress' => max(0, min(100, $progresspct)),
                'progresslabel' => $hasprogress
                    ? self::str('employer_progress_pct', $progresspct, '{$a}% complete')
                    : self::str('employer_progress_na', null, 'Open course for activities'),
            ];
        }
        if ($primary === null) {
            return [];
        }
        return [
            'title' => (string) $primary['name'],
            'meta' => (string) ($primary['status'] ?? ''),
            'url' => (string) ($primary['detail_url'] ?? $primary['url'] ?? ''),
            'cta' => self::str('employer_cta_view_details', null, 'View details'),
            'hasprogress' => !empty($primary['hasprogress']) || $hasprogress,
            'progress' => !empty($primary['hasprogress'])
                ? (int) $primary['progress']
                : max(0, min(100, $progresspct)),
            'progresslabel' => !empty($primary['hasprogress'])
                ? self::str('employer_progress_pct', (int) $primary['progress'], '{$a}% complete')
                : ($hasprogress
                    ? self::str('employer_progress_pct', $progresspct, '{$a}% complete')
                    : self::str('employer_progress_na', null, 'Open course for activities')),
        ];
    }

    /**
     * Time-of-day greeting with the learner's first name (never a lang identifier).
     *
     * @param string $firstname
     */
    private static function greeting_for(string $firstname): string {
        $name = trim($firstname) !== '' ? $firstname : get_string('user');
        $parts = usergetdate(time());
        $hour = (int) ($parts['hours'] ?? 12);
        if ($hour < 12) {
            return self::str('employer_greeting_morning', $name, 'Good morning, {$a}');
        }
        if ($hour < 17) {
            return self::str('employer_greeting_afternoon', $name, 'Good afternoon, {$a}');
        }
        return self::str('employer_greeting_evening', $name, 'Good evening, {$a}');
    }

    /**
     * Featured continue-learning card (Moodle enrolment + completion only).
     *
     * @param array<string,mixed>|null $primary
     * @param array<string,mixed>|null $next
     * @param list<array<string,mixed>> $gates
     * @param int $done
     * @param int $total
     * @param int|null $heroring
     * @return array<string,mixed>
     */
    private static function continue_card(
        ?array $primary,
        ?array $next,
        array $gates,
        int $done,
        int $total,
        ?int $heroring
    ): array {
        if (!$primary) {
            return ['hascontent' => false];
        }

        $pct = $heroring;
        if ($pct === null && $total > 0) {
            $pct = (int) round(($done / $total) * 100);
        }
        $pct = $pct !== null ? max(0, min(100, $pct)) : null;

        return [
            'hascontent' => true,
            'course_name' => (string) $primary['name'],
            'course_url' => (string) $primary['url'],
            'badge' => (string) $primary['badge'],
            'hasnext' => $next !== null,
            'next_name' => $next ? (string) $next['name'] : '',
            'next_mod' => $next ? (string) $next['modname'] : '',
            'next_url' => $next ? (string) $next['url'] : (string) $primary['url'],
            'progress' => $pct ?? 0,
            'hasprogress' => $pct !== null,
            'progresslabel' => $pct !== null
                ? get_string('employer_progress_pct', 'local_launchpad', $pct)
                : get_string('employer_progress_na', 'local_launchpad'),
            'activitylabel' => $total > 0
                ? get_string('employer_continue_activity_meta', 'local_launchpad', (object) [
                    'done' => $done,
                    'total' => $total,
                ])
                : get_string('employer_progress_na', 'local_launchpad'),
            'primary_label' => $next
                ? get_string('employer_cta_resume', 'local_launchpad')
                : get_string('employer_open_course', 'local_launchpad'),
            'secondary_label' => get_string('employer_cta_view_course', 'local_launchpad'),
            'hasgates' => $gates !== [],
        ];
    }

    /**
     * SVG progress ring geometry (completion %, not XP).
     *
     * @param int $pct
     * @param int $size
     * @return array<string,mixed>
     */
    private static function ring_geometry(int $pct, int $size = 112): array {
        $pct = max(0, min(100, $pct));
        $radius = ($size / 2) - 10;
        $circ = 2 * M_PI * $radius;
        $dashoffset = $circ * (1 - ($pct / 100));
        return [
            'pct' => $pct,
            'size' => $size,
            'radius' => round($radius, 2),
            'cx' => $size / 2,
            'cy' => $size / 2,
            'circ' => round($circ, 2),
            'dashoffset' => number_format($dashoffset, 2, '.', ''),
        ];
    }

    /**
     * Horizontal section stepper for the continue card.
     *
     * @param list<array<string,mixed>> $gates
     * @return list<array<string,mixed>>
     */
    private static function stepper_from_gates(array $gates): array {
        $out = [];
        foreach (array_slice($gates, 0, 5) as $g) {
            $out[] = [
                'index' => (int) $g['index'],
                'name' => (string) $g['name'],
                'short' => \core_text::strlen((string) $g['name']) > 16
                    ? \core_text::substr((string) $g['name'], 0, 14) . '…'
                    : (string) $g['name'],
                'status' => (string) ($g['step'] ?? 'lock'),
                'url' => (string) ($g['url'] ?? '#'),
                'hasurl' => !empty($g['hasurl']),
                'isdone' => ($g['step'] ?? '') === 'done',
                'iscur' => ($g['step'] ?? '') === 'cur',
                'islock' => ($g['step'] ?? '') === 'lock',
            ];
        }
        return $out;
    }

    /**
     * Sidebar progress card (lp-lvcard chrome).
     * Shows Moodle completion % for assigned courses.
     *
     * @param array<string, string> $urls
     * @param int|null $heroring
     * @param int $coursecount
     * @return array<string, mixed>
     */
    private static function side_card(array $urls, ?int $heroring, int $coursecount): array {
        $pct = $heroring !== null ? max(0, min(100, $heroring)) : 0;
        // Sidebar = primary-course progress (bar + %). Topbar chip = enrolment count.
        if ($coursecount > 0) {
            $meta = $heroring !== null
                ? self::str(
                    'employer_side_progress_meta',
                    (object) ['pct' => $pct, 'count' => $coursecount],
                    '{$a->pct}% · {$a->count} assigned'
                )
                : self::str('employer_side_courses_meta', $coursecount, '{$a} assigned · open for activities');
            return [
                'url' => $urls['mycourses'] ?? $urls['home'],
                'title' => self::str('employer_side_progress', null, 'Course progress'),
                'badge' => $heroring !== null ? (string) $pct . '%' : (string) $coursecount,
                'color' => 'var(--lp-blue)',
                'name' => self::str('employer_side_progress', null, 'Course progress'),
                'meta' => $meta,
                'pct' => $pct,
                'chip_value' => (string) $coursecount,
                'chip_label' => self::str('employer_my_courses', null, 'My courses'),
            ];
        }
        return [
            'url' => $urls['mycourses'] ?? $urls['home'],
            'title' => self::str('employer_my_courses', null, 'My courses'),
            'badge' => '0',
            'color' => 'var(--lp-blue)',
            'name' => self::str('employer_side_waiting', null, 'Waiting'),
            'meta' => self::str('employer_side_waiting_meta', null, 'Courses appear after enrolment'),
            'pct' => 0,
            'chip_value' => '0',
            'chip_label' => self::str('employer_my_courses', null, 'My courses'),
        ];
    }

    /**
     * Prefer an in-progress LaunchPad course, else first enrolment (assigned_courses sort).
     *
     * @param list<array<string,mixed>> $courses
     * @return array<string,mixed>|null
     */
    private static function pick_primary_course(array $courses): ?array {
        if ($courses === []) {
            return null;
        }
        foreach ($courses as $c) {
            if (!empty($c['hasprogress']) && (int) ($c['progress'] ?? 0) < 100) {
                return $c;
            }
        }
        return $courses[0];
    }

    /**
     * Per-view orbit tip (Fellowship shell pattern).
     */
    private static function orbit_tip_for(string $view, bool $hascourses): string {
        if (!$hascourses) {
            return self::str('employer_orbit_tip_empty', null, 'Tip: ask your organisation to assign a course.');
        }
        return match ($view) {
            'courses' => self::str(
                'employer_orbit_tip_courses',
                null,
                'Tip: open a course card for sections, activities, and Moodle links.'
            ),
            'course' => self::str(
                'employer_orbit_tip_course',
                null,
                'Tip: Continue jumps to your next Moodle activity when one is ready.'
            ),
            default => self::str(
                'employer_orbit_tip_home',
                null,
                'Tip: open your course to keep learning in Moodle.'
            ),
        };
    }

    /**
     * Moodle core destinations used by the shell.
     *
     * @param array<string, mixed>|null $primary
     * @return array<string, string>
     */
    public static function core_urls(?array $primary = null): array {
        // Keep My courses on this portal (employees are redirected away from /my/).
        // mydashboard aliases home — Employer shell is the canonical dashboard.
        $home = (new \moodle_url('/local/launchpad/employee.php'))->out(false);
        $mycourses = (new \moodle_url('/local/launchpad/employee.php', ['view' => 'courses']))->out(false);
        $urls = [
            'home' => $home,
            'mycourses' => $mycourses,
            'mydashboard' => $home,
            'grades' => (new \moodle_url('/grade/report/overview/index.php'))->out(false),
            'calendar' => (new \moodle_url('/calendar/view.php', ['view' => 'month']))->out(false),
            'messages' => (new \moodle_url('/message/index.php'))->out(false),
            'profile' => (new \moodle_url('/user/profile.php'))->out(false),
            'preferences' => (new \moodle_url('/user/preferences.php'))->out(false),
            'notifications' => (new \moodle_url('/message/output/popup/notifications.php'))->out(false),
            'badges' => (new \moodle_url('/badges/mybadges.php'))->out(false),
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

        $raw = enrol_get_users_courses(
            $userid,
            true,
            'id, fullname, shortname, idnumber, visible, summary, summaryformat'
        );
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
            [$status, $statustype] = self::course_status_chip($progress);
            $name = self::plain_text(format_string($course->fullname, true, ['context' => $ctx]));
            $shortname = self::plain_text(format_string($course->shortname));
            $summary = '';
            if (!empty($course->summary)) {
                $summary = self::plain_text(format_text(
                    $course->summary,
                    (int) ($course->summaryformat ?? FORMAT_HTML),
                    ['context' => $ctx, 'filter' => false]
                ));
                if (\core_text::strlen($summary) > 110) {
                    $summary = \core_text::substr($summary, 0, 108) . '…';
                }
            }

            $cid = (int) $course->id;
            $out[] = [
                'id' => $cid,
                'name' => $name,
                'shortname' => $shortname,
                'summary' => $summary,
                'hassummary' => $summary !== '',
                'media_label' => self::course_media_label($shortname, $name, $idnumber),
                'idnumber' => $idnumber,
                'islaunchpad' => $islaunchpad,
                'url' => $url,
                'gradesurl' => $gradesurl,
                'detail_url' => (new \moodle_url('/local/launchpad/employee.php', [
                    'view' => 'course',
                    'id' => $cid,
                ]))->out(false),
                'hasprogress' => $progress !== null,
                'progress' => $progress ?? 0,
                'progresslabel' => $progress !== null
                    ? self::str('employer_progress_pct', $progress, '{$a}% complete')
                    : self::str('employer_progress_na', null, 'Open course for activities'),
                'badge' => $islaunchpad
                    ? self::str('employer_badge_track', null, 'Assigned track')
                    : self::str('employer_badge_course', null, 'Assigned course'),
                'status' => $status,
                'statustype' => $statustype,
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
     * @param int $actlimit Max activities to return (0 = no cap; home uses 12)
     * @return array{
     *   sections: list<array<string,mixed>>,
     *   activities: list<array<string,mixed>>,
     *   next: ?array<string,mixed>,
     *   done: int,
     *   total: int
     * }
     */
    public static function course_workspace(int $courseid, int $userid, int $actlimit = 12): array {
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
                    'sectionnum' => (int) $section->section,
                    'name' => self::plain_text(get_section_name($course, $section)),
                    'summary' => self::plain_text((string) ($section->summary ?? '')),
                    'visible' => false,
                    'locked' => true,
                    'chip' => self::str('employer_section_locked', null, 'Not available'),
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
                    'sectionnum' => (int) $section->section,
                    'name' => self::plain_text(get_section_name($course, $section)),
                    'summary' => self::plain_text((string) ($section->summary ?? '')),
                    'visible' => true,
                    'locked' => false,
                    'chip' => self::str('employer_section_open', null, 'Available'),
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

                $tracked = false;
                $complete = false;
                if ($completionon && $completion->is_enabled($cm) != COMPLETION_TRACKING_NONE) {
                    $tracked = true;
                    $data = $completion->get_data($cm, false, $userid);
                    $complete = ((int) $data->completionstate === COMPLETION_COMPLETE
                        || (int) $data->completionstate === COMPLETION_COMPLETE_PASS);
                    $total++;
                    if ($complete) {
                        $done++;
                    }
                }

                $modname = method_exists($cm, 'get_module_type_name')
                    ? (string) $cm->get_module_type_name()
                    : get_string('modulename', $cm->modname);
                // Portal activity shell for Employer management UI (page/url/resource + interstitial).
                $item = activity_portal::rewrite_row([
                    'id' => (int) $cm->id,
                    'name' => self::plain_text($cm->get_formatted_name()),
                    'modname' => self::plain_text($modname),
                    'modtype' => $cm->modname,
                    'url' => $cm->url ? $cm->url->out(false) : (new \moodle_url('/mod/' . $cm->modname . '/view.php', [
                        'id' => (int) $cm->id,
                    ]))->out(false),
                    'complete' => $complete,
                    'tracked' => $tracked,
                    'section' => (int) $section->section,
                ]);
                $activities[] = $item;
                if ((!$tracked || !$complete) && $next === null) {
                    $next = $item;
                }
            }
        }

        // Cap only when requested (home). Detail passes 0 for the full outline.
        if ($actlimit > 0 && count($activities) > $actlimit) {
            $activities = array_slice($activities, 0, $actlimit);
        }

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
     * Map tile semantic key → Hugeicons name (Iconify).
     *
     * @param string $key
     */
    private static function tile_hugeicon(string $key): string {
        return match ($key) {
            'courses' => 'book-open-01',
            'check' => 'checkmark-circle-02',
            'chart' => 'chart-line-data-01',
            'badge' => 'medal-01',
            'bell' => 'notification-01',
            'clock' => 'clock-01',
            default => 'circle',
        };
    }

    /**
     * Status chip from Moodle completion percent (no invented due dates).
     *
     * @param int|null $progress
     * @return array{0:string,1:string} label, chiptype
     */
    private static function course_status_chip(?int $progress): array {
        if ($progress !== null && $progress >= 100) {
            return [
                self::str('employer_status_done', null, 'Completed'),
                'ok',
            ];
        }
        if ($progress !== null && $progress > 0) {
            return [
                self::str('employer_status_progress', null, 'In progress'),
                'run',
            ];
        }
        return [
            self::str('employer_status_assigned', null, 'Assigned'),
            'line',
        ];
    }

    /**
     * Short media strip label for the My Course card (from Moodle shortname / name).
     */
    private static function course_media_label(string $shortname, string $fullname, string $idnumber): string {
        $src = $shortname !== '' ? $shortname : ($idnumber !== '' ? $idnumber : $fullname);
        $src = preg_replace('/^lp[-_]?/i', '', $src) ?? $src;
        $src = preg_replace('/[^A-Za-z0-9]+/', ' ', $src) ?? $src;
        $parts = preg_split('/\s+/', trim($src)) ?: [];
        $word = strtoupper((string) ($parts[0] ?? 'COURSE'));
        if (\core_text::strlen($word) > 10) {
            $word = \core_text::substr($word, 0, 10);
        }
        return $word !== '' ? $word : 'COURSE';
    }

    /**
     * Group Moodle sections + activities into Fellowship-style phase accordions.
     * No Capstone / XP / invented weights — only Moodle visibility + completion.
     *
     * @param list<array<string,mixed>> $sections From course_workspace
     * @param list<array<string,mixed>> $activities From course_workspace
     * @param list<array<string,mixed>> $gates From programme_gates
     * @return list<array<string,mixed>>
     */
    private static function course_phases(array $sections, array $activities, array $gates): array {
        $bysection = [];
        foreach ($activities as $activity) {
            $sec = (int) ($activity['section'] ?? 0);
            if ($sec <= 0) {
                continue;
            }
            $bysection[$sec][] = self::decorate_activity_row($activity);
        }

        $gatestep = [];
        foreach ($gates as $gate) {
            $gatestep[(int) ($gate['sectionnum'] ?? 0)] = $gate;
        }

        $phases = [];
        $firstopen = true;
        foreach ($sections as $section) {
            $sec = (int) ($section['sectionnum'] ?? 0);
            if ($sec <= 0) {
                continue;
            }
            $gate = $gatestep[$sec] ?? null;
            $step = (string) ($gate['step'] ?? '');
            $locked = !empty($section['locked']) || $step === 'lock';
            $acts = $bysection[$sec] ?? [];
            $released = !$locked;
            $iscurrent = $step === 'cur' || ($released && $firstopen && $step !== 'done');
            $isopen = $iscurrent;
            if ($released) {
                $firstopen = false;
            }

            $count = count($acts);
            $countlabel = $count === 1
                ? self::str('employer_activity_one', null, '1 activity')
                : self::str('employer_activities_count', $count, '{$a} activities');
            $summary = trim((string) ($section['summary'] ?? ''));
            if ($released) {
                $blurb = $summary !== '' ? $countlabel . ' • ' . $summary : $countlabel;
            } else {
                $blurb = $summary !== ''
                    ? $countlabel . ' • ' . $summary
                    : self::str('employer_section_locked_help', null, 'Not available yet');
            }

            if ($iscurrent) {
                $status = self::str('employer_section_current_badge', null, 'Current section');
                $chiptype = 'current';
                $showlock = false;
            } else if ($step === 'done') {
                $status = self::str('employer_status_done', null, 'Completed');
                $chiptype = 'ok';
                $showlock = false;
            } else if ($locked) {
                $status = self::str('employer_section_locked', null, 'Not available');
                $chiptype = 'line';
                $showlock = true;
            } else {
                $status = self::str('employer_section_open', null, 'Available');
                $chiptype = 'line';
                $showlock = true;
            }

            $index = (int) ($section['index'] ?? $sec);
            if ($iscurrent || $index === 1) {
                $icon = 'book-open-01';
                $icontone = 'blue';
            } else if ($locked) {
                $icon = 'square-lock-02';
                $icontone = 'slate';
            } else {
                $icon = 'briefcase-01';
                $icontone = 'violet';
            }

            $phases[] = [
                'sectionnum' => $sec,
                'index' => $index,
                'name' => (string) ($section['name'] ?? ''),
                'blurb' => $blurb,
                'released' => $released,
                'is_open' => $isopen,
                'is_current' => $iscurrent,
                'locked' => $locked,
                'status' => $status,
                'chip' => $chiptype,
                'has_chip_class' => $chiptype !== '',
                'show_lock' => $showlock,
                'icon' => $icon,
                'icon_tone' => $icontone,
                'activities' => $acts,
                'has_activities' => $acts !== [],
                'anchor' => 'section-' . $sec,
                'activity_count' => $count,
                'locked_note' => self::str(
                    'employer_section_locked_help',
                    null,
                    'Not available yet'
                ),
            ];
        }
        return $phases;
    }

    /**
     * Figma-style My Course card (Moodle data only — no invented XP / due dates).
     *
     * URL roles (same as Fellowship track):
     * - detail_url: Employer portal course overview
     * - url / moodle_url: Moodle course/view.php (Open in Moodle)
     * - continue_url: primary CTA (portal detail, next mod, or Moodle course)
     *
     * @param array<string,mixed>|null $course
     * @param array<string,mixed>|null $next
     * @param string $primarycta URL for the solid CTA (detail page or next activity)
     * @param string $ctalabel
     * @param bool $showmoodlelink Show secondary “Open in Moodle” when primary is not already Moodle course
     * @return array<string,mixed>
     */
    private static function mycourse_card(
        ?array $course,
        ?array $next,
        string $primarycta = '',
        string $ctalabel = '',
        bool $showmoodlelink = false
    ): array {
        if ($course === null) {
            return [];
        }
        $progress = !empty($course['hasprogress']) ? (int) $course['progress'] : null;
        $detailurl = (string) ($course['detail_url'] ?? '');
        $moodleurl = (string) ($course['url'] ?? '');
        $cta = $primarycta !== '' ? $primarycta : ($detailurl !== '' ? $detailurl : $moodleurl);
        $nextline = '';
        if ($next !== null && !empty($next['name'])) {
            $nextline = self::str('employer_next_prefix', null, 'Next:') . ' '
                . self::plain_text((string) $next['name']);
        }
        if ($ctalabel === '') {
            $ctalabel = self::str('employer_cta_view_details', null, 'View details');
        }
        // Avoid two identical Moodle course buttons when primary CTA is already Open course.
        $primaryismooodlecourse = ($moodleurl !== '' && $cta === $moodleurl);
        return [
            'id' => (int) ($course['id'] ?? 0),
            'name' => (string) ($course['name'] ?? ''),
            'summary' => (string) ($course['summary'] ?? ''),
            'hassummary' => !empty($course['hassummary']),
            'media_label' => (string) ($course['media_label'] ?? 'COURSE'),
            'badge' => (string) ($course['badge'] ?? ''),
            'status' => (string) ($course['status'] ?? ''),
            'statustype' => (string) ($course['statustype'] ?? 'line'),
            'url' => $moodleurl,
            'moodle_url' => $moodleurl,
            'detail_url' => $detailurl !== '' ? $detailurl : $cta,
            'continue_url' => $cta,
            'cta_label' => $ctalabel,
            'moodle_label' => self::str('employer_open_course', null, 'Open course'),
            'has_moodle_link' => false,
            'hasprogress' => $progress !== null,
            'progress' => $progress ?? 0,
            'progress_display' => $progress !== null ? $progress . '%' : '',
            'progress_hint' => $progress !== null
                ? self::str('employer_progress_from_moodle', null, 'Course completion')
                : self::str('employer_progress_na', null, 'Open course for activities'),
            'nextline' => $nextline,
            'hasnextline' => $nextline !== '',
            'shortname' => (string) ($course['shortname'] ?? ''),
            'hasshortname' => false,
            'gradesurl' => (string) ($course['gradesurl'] ?? ''),
            'has_grades' => false,
            'grades_label' => self::str('employer_link_grades', null, 'Grades'),
        ];
    }

    /**
     * @param list<array<string,mixed>> $courses
     * @param bool $hascourses
     * @param int $actdone
     * @param int $acttotal
     * @param int $unread
     * @param array<string,string> $urls
     * @param int $badgesearned
     * @param bool $badgesenabled
     * @param string $badgesurl
     * @return list<array{label:string,value:string,sub:string,icon:string,icon_hi:string,url:string,hasurl:bool}>
     */
    private static function tiles(
        array $courses,
        bool $hascourses,
        int $actdone,
        int $acttotal,
        int $unread,
        array $urls = [],
        int $badgesearned = 0,
        bool $badgesenabled = false,
        string $badgesurl = ''
    ): array {
        $tile = static function (
            string $label,
            string $value,
            string $sub,
            string $icon,
            string $url = ''
        ): array {
            return [
                'label' => $label,
                'value' => $value,
                'sub' => $sub,
                'icon' => $icon,
                'icon_hi' => self::tile_hugeicon($icon),
                'url' => $url,
                'hasurl' => $url !== '',
            ];
        };

        $mycourses = $urls['mycourses'] ?? '';
        $notifications = $urls['notifications'] ?? '';
        $course = $urls['course'] ?? $mycourses;

        if (!$hascourses) {
            return [
                $tile(
                    get_string('employer_tile_assigned', 'local_launchpad'),
                    '0',
                    get_string('employer_tile_assigned_empty', 'local_launchpad'),
                    'courses',
                    $mycourses
                ),
                $tile(
                    get_string('employer_tile_status', 'local_launchpad'),
                    get_string('employer_tile_waiting', 'local_launchpad'),
                    get_string('employer_tile_waiting_sub', 'local_launchpad'),
                    'clock'
                ),
                $tile(
                    get_string('employer_nav_notifications', 'local_launchpad'),
                    (string) $unread,
                    get_string('employer_tile_notif_sub', 'local_launchpad'),
                    'bell',
                    $notifications
                ),
            ];
        }

        $out = [
            $tile(
                get_string('employer_tile_assigned', 'local_launchpad'),
                (string) count($courses),
                get_string('employer_tile_assigned_sub', 'local_launchpad'),
                'courses',
                $mycourses
            ),
            $tile(
                get_string('employer_tile_activities', 'local_launchpad'),
                $acttotal > 0 ? $actdone . '/' . $acttotal : '0',
                get_string('employer_tile_activities_sub', 'local_launchpad'),
                'check',
                $course
            ),
        ];
        if ($badgesenabled) {
            $out[] = $tile(
                get_string('employer_tile_badges', 'local_launchpad'),
                (string) $badgesearned,
                get_string('employer_tile_badges_sub', 'local_launchpad'),
                'badge',
                $badgesurl
            );
        }
        $out[] = $tile(
            get_string('employer_nav_notifications', 'local_launchpad'),
            (string) $unread,
            get_string('employer_tile_notif_sub', 'local_launchpad'),
            'bell',
            $notifications
        );
        return $out;
    }

    /**
     * Course-section path for Employer home.
     *
     * Uses real Moodle sections only — never Fellowship blueprint phase names
     * (Foundations / Demo day / etc.).
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
        if ($sections === []) {
            return ['has_gates' => false];
        }

        $spine = [];
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

        $n = count($spine);
        // First unlocked incomplete = current; prior unlocked with full course done = done.
        $gates = [];
        $foundcurrent = false;
        $phasedone = [];
        foreach ($spine as $i => $meta) {
            $phasedone[$i] = !empty($meta['done']);
            if (!$phasedone[$i] && empty($meta['locked']) && $total > 0) {
                $phasedone[$i] = ($done / max(1, $total)) > (($i + 0.85) / max(1, $n));
            }
        }
        for ($i = 1; $i < $n; $i++) {
            if (empty($phasedone[$i - 1])) {
                $phasedone[$i] = false;
            }
        }

        foreach ($spine as $i => $meta) {
            $isdone = !empty($phasedone[$i]);
            $status = 'lock';
            $mark = '🔒';
            $sublabel = '';
            $showsub = false;
            if ($isdone) {
                $status = 'done';
                $mark = '✓';
            } else if (!$foundcurrent) {
                $status = 'cur';
                $mark = (string) ($i + 1);
                $foundcurrent = true;
                $sublabel = self::str('employer_section_current_badge', null, 'Current section');
                $showsub = true;
            }
            $gates[] = [
                'name' => $meta['name'],
                'short' => $meta['short'],
                'status' => $status,
                'mark' => $mark,
                'cx' => 0,
                'cy' => 0,
                'show_sublabel' => $showsub,
                'sublabel' => $sublabel,
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
            for ($i = 1; $i < $n; $i++) {
                if (!empty($phasedone[$i])) {
                    $extradone++;
                }
            }
            $step = $n > 1 ? (0.64 / ($n - 1)) : 0.64;
            $pathprogress = min(1.0, 0.36 + ($extradone * $step));
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
     * Real Moodle course sections only (no Fellowship blueprint placeholders).
     *
     * @param list<array<string,mixed>> $sections
     * @param int $done
     * @param int $total
     * @return list<array{index:int,name:string,short:string,locked:bool,chiptype:string,chip:string,url:string,hasurl:bool,step:string}>
     */
    private static function programme_gates(array $sections, int $done = 0, int $total = 0): array {
        $n = count($sections);
        $phasedone = [];
        foreach ($sections as $i => $s) {
            $phasedone[$i] = false;
            if (!empty($s['locked']) || $total <= 0) {
                continue;
            }
            if ($done >= $total) {
                $phasedone[$i] = true;
            } else {
                $phasedone[$i] = ($done / $total) > (($i + 0.85) / max(1, $n));
            }
        }
        for ($i = 1; $i < $n; $i++) {
            if (empty($phasedone[$i - 1])) {
                $phasedone[$i] = false;
            }
        }

        $gates = [];
        $foundcurrent = false;
        foreach ($sections as $i => $s) {
            $locked = !empty($s['locked']);
            if (!empty($phasedone[$i])) {
                $step = 'done';
                $chip = self::str('employer_status_done', null, 'Completed');
                $chiptype = 'ok';
            } else if (!$locked && !$foundcurrent) {
                $step = 'cur';
                $foundcurrent = true;
                $chip = self::str('employer_section_current', null, 'Current');
                $chiptype = 'run';
            } else if ($locked) {
                $step = 'lock';
                $chip = self::str('employer_section_locked', null, 'Not available');
                $chiptype = 'line';
            } else {
                $step = 'open';
                $chip = self::str('employer_section_open', null, 'Available');
                $chiptype = 'line';
            }
            $fullname = self::plain_text((string) $s['name']);
            $short = \core_text::strlen($fullname) > 18
                ? \core_text::substr($fullname, 0, 16) . '…'
                : $fullname;
            $gates[] = [
                'index' => (int) $s['index'],
                'sectionnum' => (int) ($s['sectionnum'] ?? 0),
                'name' => $fullname,
                'short' => $short,
                'locked' => $step === 'lock',
                'chiptype' => $chiptype,
                'chip' => $chip,
                'url' => (string) ($s['url'] ?? '#'),
                'hasurl' => !empty($s['url']) && $step !== 'lock',
                'step' => $step,
                'is_current' => $step === 'cur',
                'is_done' => $step === 'done',
            ];
        }
        return $gates;
    }

    /**
     * Home hero ring, KPI strip, scoped section activities, recent badges.
     * Mirrors Fellowship home_summary — Moodle data only, no invented XP.
     *
     * @param list<array<string,mixed>> $gates
     * @param list<array<string,mixed>> $activities
     * @param array{enabled:bool,hasbadges:bool,earned:int,total:int,items:list<array>} $badges
     * @param int $done
     * @param int $total
     * @param int|null $coursepct Course completion % when available
     * @param bool $hascourses
     * @return array<string,mixed>
     */
    public static function home_summary(
        array $gates,
        array $activities,
        array $badges,
        int $done,
        int $total,
        ?int $coursepct,
        bool $hascourses
    ): array {
        $hascoursepercent = $coursepct !== null;
        $currentgate = self::str('employer_gate_none', null, 'Not started');
        $currentshort = $currentgate;
        $currentsection = 0;
        foreach ($gates as $gate) {
            if (!empty($gate['is_current'])) {
                $currentgate = (string) ($gate['name'] ?? $currentgate);
                $currentshort = (string) ($gate['short'] ?? $currentgate);
                $currentsection = (int) ($gate['sectionnum'] ?? 0);
                break;
            }
            if (!empty($gate['is_done'])) {
                $currentgate = (string) ($gate['name'] ?? $currentgate);
                $currentshort = (string) ($gate['short'] ?? $currentgate);
                $currentsection = (int) ($gate['sectionnum'] ?? 0);
            }
        }

        $ringpct = $hascoursepercent ? max(0, min(100, (int) $coursepct)) : 0;
        $gatecompleted = 0;
        $gatetrackable = 0;
        if (!$hascoursepercent && $currentsection > 0) {
            foreach ($activities as $activity) {
                if ((int) ($activity['section'] ?? 0) !== $currentsection) {
                    continue;
                }
                if (empty($activity['tracked'])) {
                    continue;
                }
                $gatetrackable++;
                if (!empty($activity['complete'])) {
                    $gatecompleted++;
                }
            }
            if ($gatetrackable > 0) {
                $ringpct = (int) round(($gatecompleted / $gatetrackable) * 100);
            } else if ($total > 0) {
                $ringpct = (int) round(($done / $total) * 100);
            }
        } else if (!$hascoursepercent && $total > 0) {
            $ringpct = (int) round(($done / $total) * 100);
        }

        $alldone = $total > 0 && $done >= $total;
        if ($alldone) {
            $currentshort = self::str('employer_gate_complete', null, 'Complete');
            $currentgate = $currentshort;
        }

        $ring = self::ring_geometry($ringpct);

        $requiredvalue = self::str('employer_activity_not_tracked', null, 'Not tracked');
        if ($total > 0) {
            $requiredvalue = $done . '/' . $total;
        } else if ($gatetrackable > 0) {
            $requiredvalue = $gatecompleted . '/' . $gatetrackable;
        }

        // Scoped list: current section activities (Fellowship Foundations pattern).
        $scoped = [];
        $scopedname = $currentgate;
        if ($currentsection > 0) {
            foreach ($activities as $activity) {
                if ((int) ($activity['section'] ?? 0) === $currentsection) {
                    $scoped[] = $activity;
                }
            }
        }
        if ($scoped === [] && $activities !== []) {
            // Fall back to first Moodle section that has activities.
            $firstsec = (int) ($activities[0]['section'] ?? 0);
            foreach ($gates as $gate) {
                if ((int) ($gate['sectionnum'] ?? 0) === $firstsec) {
                    $scopedname = (string) ($gate['name'] ?? $scopedname);
                    break;
                }
            }
            foreach ($activities as $activity) {
                if ((int) ($activity['section'] ?? 0) === $firstsec) {
                    $scoped[] = $activity;
                }
            }
        }
        $scoped = array_slice($scoped, 0, 8);
        $scopedname = self::plain_text($scopedname);
        $scoped = array_map([self::class, 'decorate_activity_row'], $scoped);

        // Activity counts per section for journey legend cards.
        $seccounts = [];
        foreach ($activities as $activity) {
            $sec = (int) ($activity['section'] ?? 0);
            if ($sec <= 0) {
                continue;
            }
            $seccounts[$sec] = ($seccounts[$sec] ?? 0) + 1;
        }
        $gatelegend = [];
        foreach ($gates as $gate) {
            $sec = (int) ($gate['sectionnum'] ?? 0);
            $count = $seccounts[$sec] ?? 0;
            $gatelegend[] = array_merge($gate, [
                'activity_count' => $count,
                'activity_meta' => $count > 0
                    ? self::str('employer_activities_count', $count, '{$a} activities')
                    : '',
                'has_activity_meta' => $count > 0 && empty($gate['is_current']),
                'legend_icon' => self::section_legend_icon((string) ($gate['step'] ?? '')),
            ]);
        }

        $earned = (int) ($badges['earned'] ?? 0);
        $badgestotal = (int) ($badges['total'] ?? 0);
        $badgesenabled = !empty($badges['enabled']);
        $recent = array_slice($badges['items'] ?? [], 0, 4);
        $badgesvalue = '0';
        if ($badgesenabled) {
            $badgesvalue = $badgestotal > 0
                ? $earned . '/' . $badgestotal
                : (string) $earned;
        }

        $statbadgeslabel = self::str('employer_stat_badges', null, 'Badges earned');
        $statreqlabel = self::str('employer_stat_required', null, 'Activities done');
        $statgatelabel = self::str('employer_stat_gate', null, 'Current section');

        return [
            // Always show the ring when enrolled (0% is honest — matches Fellowship home).
            'show_hero_ring' => $hascourses,
            'has_percent' => $hascoursepercent || $total > 0 || $activities !== [],
            'progresspct' => $ringpct,
            'hasprogress' => $hascourses,
            'ring' => $ring,
            'hasring' => $hascourses,
            'ring_label' => self::plain_text($currentshort),
            'stat_badges_value' => $badgesvalue,
            'stat_required_value' => $requiredvalue,
            'stat_gate_value' => self::plain_text($currentgate),
            'has_stats' => $hascourses,
            'stat_cards' => $hascourses ? [
                [
                    'url' => (new \moodle_url('/badges/mybadges.php'))->out(false),
                    'icon' => 'champion',
                    'tone' => 'blue',
                    'value' => $badgesenabled ? (string) $earned : '0',
                    'label' => $statbadgeslabel,
                ],
                [
                    'url' => '#employer-scoped',
                    'icon' => 'checkmark-circle-02',
                    'tone' => 'violet',
                    'value' => $requiredvalue,
                    'label' => $statreqlabel,
                ],
                [
                    'url' => '#employer-journey',
                    'icon' => 'book-open-01',
                    'tone' => 'green',
                    'value' => self::plain_text($currentgate),
                    'label' => $statgatelabel,
                    'is_text' => true,
                ],
            ] : [],
            'scoped_activities' => $scoped,
            'has_scoped_activities' => $scoped !== [],
            'scoped_section_name' => $scopedname,
            'scoped_section_help' => self::str(
                'employer_scoped_help',
                null,
                'Complete the activities below to keep moving through this section.'
            ),
            'gate_legend' => $gatelegend,
            'has_gate_legend' => $gatelegend !== [],
            'recent_badges' => $recent,
            'has_recent_badges' => $recent !== [],
            'badges_enabled' => $badgesenabled,
            'badges_earned' => $earned,
            'progressmeta' => $total > 0
                ? self::str(
                    'employer_continue_activity_meta',
                    (object) ['done' => $done, 'total' => $total],
                    '{$a->done} of {$a->total} tracked activities'
                )
                : self::str('employer_tile_avg_sub', null, 'Moodle completion / activity ticks'),
        ];
    }

    /**
     * Iconify row chrome for home activity list (Moodle modname only — no invented times).
     *
     * @param array<string,mixed> $activity
     * @return array<string,mixed>
     */
    private static function decorate_activity_row(array $activity): array {
        $mod = (string) ($activity['modtype'] ?? '');
        $icon = 'book-open-01';
        $tone = 'blue';
        if (in_array($mod, ['quiz'], true)) {
            $icon = 'help-circle';
            $tone = 'violet';
        } else if (in_array($mod, ['assign', 'workshop'], true)) {
            $icon = 'task-01';
            $tone = 'teal';
        } else if (in_array($mod, ['page', 'resource', 'url', 'book', 'folder', 'file'], true)) {
            $icon = 'file-01';
            $tone = 'blue';
        } else if (in_array($mod, ['forum'], true)) {
            $icon = 'message-01';
            $tone = 'amber';
        }
        $tracked = !empty($activity['tracked']);
        $complete = !empty($activity['complete']);
        if ($tracked && $complete) {
            $status = self::str('employer_activity_complete', null, 'Complete');
            $statustone = 'ok';
        } else if ($tracked) {
            $status = self::str('employer_activity_incomplete', null, 'To do');
            $statustone = 'line';
        } else {
            $status = self::str('employer_activity_not_tracked', null, 'Not tracked');
            $statustone = 'line';
        }
        $modlabel = self::plain_text((string) ($activity['modname'] ?? ''));
        $activity['icon'] = $icon;
        $activity['tone'] = $tone;
        $activity['status_label'] = $status;
        $activity['statustone'] = $statustone;
        $activity['modname'] = $modlabel !== '' ? \core_text::strtoupper($modlabel) : $modlabel;
        $activity['meta'] = $modlabel;
        // Moodle-backed meta only — no invented duration / difficulty.
        $activity['meta_items'] = [];
        $activity['has_meta_items'] = false;
        return $activity;
    }

    /**
     * Legend icon for a journey gate step.
     */
    private static function section_legend_icon(string $step): string {
        return match ($step) {
            'done' => 'checkmark-circle-02',
            'cur' => 'flag-01',
            'lock' => 'square-lock-02',
            default => 'file-01',
        };
    }

    /**
     * Sidebar nav groups: Learn / My tools / You.
     *
     * @param array<string, string> $urls
     * @param array<string, mixed>|null $primary
     * @param int $unread
     * @param string $view
     * @return list<array{group:string,items:list<array<string,mixed>>}>
     */
    private static function nav_groups(array $urls, ?array $primary, int $unread, string $view = 'home'): array {
        // Hugeicons (Iconify) — same stroke-rounded set as LaunchPad landing.
        $icon = static function (string $name, int $size = 18): string {
            $safe = preg_replace('/[^a-z0-9-]/', '', $name) ?? 'circle';
            return '<iconify-icon icon="hugeicons:' . $safe . '" width="' . $size . '" height="' . $size
                . '" aria-hidden="true"></iconify-icon>';
        };
        $item = static function (
            string $key,
            string $label,
            string $url,
            string $iconname,
            bool $current = false,
            string $badge = ''
        ) use ($icon): array {
            return [
                'key' => $key,
                'label' => $label,
                'url' => $url,
                'current' => $current,
                'icon_svg' => $icon($iconname),
                'badge' => $badge,
                'has_badge' => $badge !== '',
            ];
        };

        // Home + My courses list (cards → course detail).
        $learn = [
            $item(
                'home',
                self::str('employer_nav_home', null, 'Home'),
                $urls['home'],
                'home-01',
                $view === 'home'
            ),
            $item(
                'courses',
                self::str('employer_my_courses', null, 'My courses'),
                (string) ($urls['mycourses'] ?? $urls['home']),
                'book-open-01',
                $view === 'courses' || $view === 'course'
            ),
        ];

        $record = [
            $item('grades', self::str('employer_nav_grades', null, 'Grades'),
                $primary ? $urls['coursegrades'] : $urls['grades'],
                'chart-histogram'),
            $item(
                'badges',
                self::str('employer_nav_badges', null, 'Badges'),
                (string) ($urls['badges'] ?? $urls['home']),
                'award-01'
            ),
            $item('calendar', self::str('employer_nav_calendar', null, 'Calendar'), $urls['calendar'],
                'calendar-03'),
            $item('messages', self::str('employer_nav_messages', null, 'Messages'), $urls['messages'],
                'message-01'),
            $item(
                'notifications',
                self::str('employer_nav_notifications', null, 'Notifications'),
                $urls['notifications'],
                'notification-01',
                false,
                $unread > 0 ? (string) $unread : ''
            ),
        ];

        // Profile also in sidebar footer (lp-me); keep discoverable under You like Fellowship.
        $you = [
            $item('profile', self::str('employer_nav_profile', null, 'Profile'), $urls['profile'],
                'user-circle'),
            $item('preferences', self::str('employer_nav_preferences', null, 'Preferences'), $urls['preferences'],
                'settings-01'),
            $item('public', self::str('employer_public_site', null, 'Public site'), $urls['public'],
                'globe-02'),
        ];

        return [
            [
                'group' => self::str('employer_navgroup_learn', null, 'Learn'),
                'items' => $learn,
            ],
            [
                'group' => self::str('employer_navgroup_record', null, 'My tools'),
                'items' => $record,
            ],
            [
                'group' => self::str('employer_navgroup_you', null, 'You'),
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

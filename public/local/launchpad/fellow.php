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

/**
 * Fellowship dashboard (assigned lp-* courses; native Moodle activities).
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require(__DIR__ . '/../../config.php');

use local_launchpad\account_persona;
use local_launchpad\fellowship_dashboard;
use local_launchpad\persona_home;

require_login();

$userid = (int) $USER->id;

// Employer cohort never uses the Fellowship shell (even with lp-* enrolments).
if (account_persona::is_employee($userid) && !is_siteadmin()) {
    redirect(new moodle_url(persona_home::EMPLOYEE_PATH));
}

if (!fellowship_dashboard::can_access($userid)) {
    throw new \moodle_exception('fellowship_access_denied', 'local_launchpad');
}

$view = optional_param('view', 'home', PARAM_ALPHA);
$courseid = optional_param('id', 0, PARAM_INT);

$courses = fellowship_dashboard::list_courses($userid);
$courseids = array_map(static fn($c) => (int) $c->id, $courses);

// Refuse mentor-only / non-fellow visitors (admins may preview).
if (!account_persona::is_fellowship($userid) && !is_siteadmin()) {
    throw new \moodle_exception('fellowship_access_denied', 'local_launchpad');
}

$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url(persona_home::FELLOW_PATH, array_filter([
    'view' => $view !== 'home' ? $view : null,
    'id' => $courseid > 0 ? $courseid : null,
])));
$PAGE->set_pagelayout('embedded');
$PAGE->set_title(get_string('fellowship_area_title', 'local_launchpad'));
$PAGE->set_heading(get_string('fellowship_area_heading', 'local_launchpad'));

$pluginbase = $CFG->wwwroot . '/local/launchpad';
$assetv = (string) (get_config('local_launchpad', 'version') ?: '1');
$cssurl = $pluginbase . '/styles_fellowship.css?v=' . rawurlencode($assetv);
$jsurl = $pluginbase . '/fellowship.js?v=' . rawurlencode($assetv);
$logouturl = (new moodle_url('/login/logout.php', ['sesskey' => sesskey()]))->out(false);
$profileurl = (new moodle_url('/user/profile.php', ['id' => $userid]))->out(false);
$homeurl = (new moodle_url(persona_home::FELLOW_PATH))->out(false);
// Legacy Progress tab redirects to Phase progress; keep URL for shell/home links.
$progressurl = (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'phases']))->out(false);
$notificationsurl = (new moodle_url('/message/index.php'))->out(false);

$firstname = format_string($USER->firstname);
$displayname = fullname($USER);

$icon = static function (string $paths): string {
    return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">'
        . $paths . '</svg>';
};

$nav = [
    [
        'group' => get_string('fellowship_nav_learn', 'local_launchpad'),
        'items' => [
            [
                'label' => get_string('fellowship_nav_home', 'local_launchpad'),
                'url' => $homeurl,
                'current' => $view === 'home',
                'key' => 'home',
                'icon_svg' => $icon('<path d="M3 11.5L12 4l9 7.5"/><path d="M6 10.5V20h12v-9.5"/>'),
            ],
            [
                'label' => get_string('fellowship_nav_track', 'local_launchpad'),
                'url' => (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'track']))->out(false),
                'current' => $view === 'track',
                'key' => 'track',
                'icon_svg' => $icon('<path d="M4 6h16M4 12h10M4 18h13"/><circle cx="18" cy="12" r="2"/><circle cx="19" cy="18" r="2"/>'),
            ],
            [
                'label' => get_string('fellowship_nav_achievements', 'local_launchpad'),
                'url' => (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'achievements']))->out(false),
                'current' => $view === 'achievements',
                'key' => 'achievements',
                'icon_svg' => $icon('<path d="M8 21h8"/><path d="M12 17v4"/><path d="M7 4h10v5a5 5 0 01-10 0V4z"/><path d="M7 8H4a2 2 0 000 4c2 0 3-1 3-2"/><path d="M17 8h3a2 2 0 010 4c-2 0-3-1-3-2"/>'),
            ],
        ],
    ],
    [
        'group' => get_string('fellowship_nav_record', 'local_launchpad'),
        'items' => [
            [
                'label' => get_string('fellowship_nav_feedback', 'local_launchpad'),
                'url' => (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'feedback']))->out(false),
                'current' => $view === 'feedback',
                'key' => 'feedback',
                'icon_svg' => $icon('<path d="M5 5h14v10H9l-4 4V5z"/>'),
            ],
            [
                'label' => get_string('fellowship_nav_phases', 'local_launchpad'),
                'url' => (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'phases']))->out(false),
                'current' => $view === 'phases',
                'key' => 'phases',
                'icon_svg' => $icon('<path d="M4 19V5"/><path d="M4 19h16"/><path d="M7 15h3v4H7z"/><path d="M12 10h3v9h-3z"/><path d="M17 7h3v12h-3z"/>'),
            ],
            [
                'label' => get_string('fellowship_nav_completion', 'local_launchpad'),
                'url' => (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'completion']))->out(false),
                'current' => $view === 'completion',
                'key' => 'completion',
                'icon_svg' => $icon('<path d="M4 5h16v14H4z"/><path d="M8 9h8"/><path d="M8 13h5"/><path d="M15 16l2 2 4-4"/>'),
            ],
        ],
    ],
    [
        'group' => get_string('fellowship_nav_you', 'local_launchpad'),
        'items' => [
            [
                'label' => get_string('fellowship_nav_news', 'local_launchpad'),
                'url' => (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'news']))->out(false),
                'current' => $view === 'news',
                'key' => 'news',
                'icon_svg' => $icon('<path d="M4 5h12v14H4z"/><path d="M16 8h4v11h-4"/><path d="M7 9h6"/><path d="M7 13h6"/>'),
            ],
            [
                'label' => get_string('fellowship_nav_calendar', 'local_launchpad'),
                'url' => (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'calendar']))->out(false),
                'current' => $view === 'calendar',
                'key' => 'calendar',
                'icon_svg' => $icon('<rect x="4" y="5" width="16" height="15" rx="2"/><path d="M4 9h16"/><path d="M8 3v4"/><path d="M16 3v4"/>'),
            ],
            [
                'label' => get_string('fellowship_nav_portfolio', 'local_launchpad'),
                'url' => (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'portfolio']))->out(false),
                'current' => $view === 'portfolio',
                'key' => 'portfolio',
                'icon_svg' => $icon('<path d="M4 8h16v12H4z"/><path d="M9 8V6a2 2 0 012-2h2a2 2 0 012 2v2"/>'),
            ],
            [
                'label' => get_string('fellowship_nav_help', 'local_launchpad'),
                'url' => (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'help']))->out(false),
                'current' => $view === 'help',
                'key' => 'help',
                'icon_svg' => $icon('<circle cx="12" cy="12" r="9"/><path d="M12 16v-1"/><path d="M12 13a2.5 2.5 0 10-1-4.8"/>'),
            ],
        ],
    ],
];
$achievementsurl = (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'achievements']))->out(false);

$crumbhome = [
    'label' => get_string('fellowship_nav_home', 'local_launchpad'),
    'url' => $homeurl,
];

$selected = null;
if ($courseid > 0) {
    if (!in_array($courseid, $courseids, true) && !is_siteadmin()) {
        throw new \moodle_exception('fellowship_course_denied', 'local_launchpad');
    }
    foreach ($courses as $c) {
        if ((int) $c->id === $courseid) {
            $selected = $c;
            break;
        }
    }
    if ($selected === null && is_siteadmin()) {
        $selected = get_course($courseid);
    }
} else if (count($courses) === 1) {
    $selected = $courses[0];
    $courseid = (int) $selected->id;
}

$outline = null;
$coursecards = [];
foreach ($courses as $c) {
    $curl = new moodle_url(persona_home::FELLOW_PATH, ['view' => 'track', 'id' => $c->id]);
    $moodlecourseurl = new moodle_url('/course/view.php', ['id' => $c->id]);
    $card = [
        'id' => (int) $c->id,
        'fullname' => format_string($c->fullname),
        'idnumber' => s($c->idnumber),
        'url' => $curl->out(false),
        'moodle_url' => $moodlecourseurl->out(false),
        'selected' => $selected && (int) $c->id === (int) $selected->id,
    ];
    if ($selected && (int) $c->id === (int) $selected->id) {
        $outline = fellowship_dashboard::course_outline($c, $userid);
        $card['percent'] = $outline['percent'];
        $card['completed'] = $outline['completed'];
        $card['trackable'] = $outline['trackable'];
        $card['completion_enabled'] = $outline['completion_enabled'];
    }
    $coursecards[] = $card;
}

if ($selected && $outline === null) {
    $outline = fellowship_dashboard::course_outline($selected, $userid);
}

$content = '';
$pagetitle = get_string('fellowship_area_heading', 'local_launchpad');

switch ($view) {
    case 'track':
        $pagetitle = get_string('fellowship_nav_track', 'local_launchpad');
        if ($selected === null) {
            $content = $OUTPUT->render_from_template('local_launchpad/fellowship_empty_courses', [
                'message' => get_string('fellowship_no_courses', 'local_launchpad'),
            ]);
        } else {
            $trackdata = fellowship_dashboard::track_for_user($userid, $selected);
            $content = $OUTPUT->render_from_template('local_launchpad/fellowship_track', array_merge($trackdata, [
                'moodle_course_url' => (new moodle_url('/course/view.php', ['id' => $selected->id]))->out(false),
                'bot_url' => $pluginbase . '/assets/img/bot.webp',
                'courses' => $coursecards,
                'multi_course' => count($coursecards) > 1,
                'has_crumbs' => true,
                'crumbs' => [
                    $crumbhome,
                    [
                        'label' => get_string('fellowship_nav_track', 'local_launchpad'),
                        'url' => '',
                    ],
                ],
            ]));
        }
        break;

    case 'progress':
        // Sidebar Progress removed — Phase progress is the programme record view.
        redirect(new moodle_url(persona_home::FELLOW_PATH, ['view' => 'phases']));
        break;

    case 'news':
        $pagetitle = get_string('fellowship_nav_news', 'local_launchpad');
        $newsdata = fellowship_dashboard::list_announcements($userid, $courses);
        $content = $OUTPUT->render_from_template('local_launchpad/fellowship_news', array_merge($newsdata, [
            'has_crumbs' => true,
            'crumbs' => [
                $crumbhome,
                [
                    'label' => get_string('fellowship_nav_news', 'local_launchpad'),
                    'url' => '',
                ],
            ],
        ]));
        break;

    case 'calendar':
        $pagetitle = get_string('fellowship_nav_calendar', 'local_launchpad');
        $caldata = fellowship_dashboard::list_calendar_events($userid, $courses);
        $content = $OUTPUT->render_from_template('local_launchpad/fellowship_calendar', array_merge($caldata, [
            'has_crumbs' => true,
            'crumbs' => [
                $crumbhome,
                [
                    'label' => get_string('fellowship_nav_calendar', 'local_launchpad'),
                    'url' => '',
                ],
            ],
        ]));
        break;

    case 'portfolio':
        $pagetitle = get_string('fellowship_nav_portfolio', 'local_launchpad');
        $portdata = fellowship_dashboard::list_portfolio_evidence($userid, $courses);
        $content = $OUTPUT->render_from_template('local_launchpad/fellowship_portfolio', array_merge($portdata, [
            'has_crumbs' => true,
            'crumbs' => [
                $crumbhome,
                [
                    'label' => get_string('fellowship_nav_portfolio', 'local_launchpad'),
                    'url' => '',
                ],
            ],
        ]));
        break;

    case 'achievements':
        $pagetitle = get_string('fellowship_nav_achievements', 'local_launchpad');
        // Content filled after live_achievements() so we compute Moodle XP/badges once.
        $content = '';
        break;

    case 'feedback':
        $pagetitle = get_string('fellowship_nav_feedback', 'local_launchpad');
        // Content filled after list_assignment_feedback() (shared with nav badge).
        $content = '';
        break;

    case 'phases':
        $pagetitle = get_string('fellowship_nav_phases', 'local_launchpad');
        $phasedata = fellowship_dashboard::phase_progress_for_user($userid, $courses, $selected);
        $content = $OUTPUT->render_from_template('local_launchpad/fellowship_phases', array_merge($phasedata, [
            'bot_url' => $pluginbase . '/assets/img/bot.webp',
            'empty_message' => get_string('fellowship_no_courses', 'local_launchpad'),
            'has_crumbs' => true,
            'crumbs' => [
                $crumbhome,
                [
                    'label' => get_string('fellowship_nav_phases', 'local_launchpad'),
                    'url' => '',
                ],
            ],
        ]));
        break;

    case 'completion':
        $pagetitle = get_string('fellowship_nav_completion', 'local_launchpad');
        $completiondata = fellowship_dashboard::completion_for_user($userid, $courses, $selected);
        $content = $OUTPUT->render_from_template('local_launchpad/fellowship_completion', array_merge($completiondata, [
            'prof_url' => $pluginbase . '/assets/img/prof.webp',
            'cert_message' => get_string('fellowship_completion_cert_body', 'local_launchpad'),
            'empty_message' => get_string('fellowship_no_courses', 'local_launchpad'),
            'has_crumbs' => true,
            'crumbs' => [
                $crumbhome,
                [
                    'label' => get_string('fellowship_nav_completion', 'local_launchpad'),
                    'url' => '',
                ],
            ],
        ]));
        break;

    case 'help':
        $pagetitle = get_string('fellowship_nav_help', 'local_launchpad');
        $content = $OUTPUT->render_from_template('local_launchpad/fellowship_help', [
            'has_crumbs' => true,
            'crumbs' => [
                $crumbhome,
                [
                    'label' => get_string('fellowship_nav_help', 'local_launchpad'),
                    'url' => '',
                ],
            ],
            'faqs' => [
                [
                    'q' => get_string('fellowship_faq_internship_q', 'local_launchpad'),
                    'a' => get_string('fellowship_faq_internship_a', 'local_launchpad'),
                ],
                [
                    'q' => get_string('fellowship_faq_track_q', 'local_launchpad'),
                    'a' => get_string('fellowship_faq_track_a', 'local_launchpad'),
                ],
                [
                    'q' => get_string('fellowship_faq_grade_q', 'local_launchpad'),
                    'a' => get_string('fellowship_faq_grade_a', 'local_launchpad'),
                ],
                [
                    'q' => get_string('fellowship_faq_quiz_q', 'local_launchpad'),
                    'a' => get_string('fellowship_faq_quiz_a', 'local_launchpad'),
                ],
            ],
        ]);
        break;

    case 'home':
    default:
        $view = 'home';
        $pagetitle = get_string('fellowship_nav_home', 'local_launchpad');
        // Content filled after live_achievements() (hero ring + recent badges).
        $content = '';
        break;
}

// Live XP + badges once per request (sidebar, topbar, Achievements, Home).
$live = fellowship_dashboard::live_achievements($userid, $courses);
$ach = fellowship_dashboard::achievements_context_from_live($live);
$xptotal = (int) $ach['xp_total'];
$badgesearned = (int) $ach['badges_earned'];
$badgestotal = (int) $ach['badges_total'];

if ($view === 'home') {
    $homeoutline = $outline;
    if ($homeoutline === null && $courses !== []) {
        $homecourse = $selected ?? $courses[0];
        $homeoutline = fellowship_dashboard::course_outline($homecourse, $userid);
        if ($selected === null) {
            $selected = $homecourse;
            $courseid = (int) $homecourse->id;
        }
    }
    if ($homeoutline === null) {
        $homeoutline = [
            'sections' => [],
            'completion_enabled' => false,
            'completed' => 0,
            'trackable' => 0,
            'percent' => null,
            'next' => null,
        ];
    }
    $next = $homeoutline['next'] ?? null;
    $journey = fellowship_dashboard::journey_from_outline($homeoutline);
    $homesummary = fellowship_dashboard::home_summary($homeoutline, $journey, $live);
    $content = $OUTPUT->render_from_template('local_launchpad/fellowship_home', array_merge([
        'firstname' => $firstname,
        'welcome_heading' => get_string('fellowship_welcome_named', 'local_launchpad', $firstname),
        'welcome_back' => get_string('fellowship_welcome_back', 'local_launchpad', $firstname),
        'courses' => $coursecards,
        'has_courses' => $coursecards !== [],
        'empty_message' => get_string('fellowship_no_courses', 'local_launchpad'),
        'next' => $next,
        'has_next' => $next !== null,
        'selected_course_name' => $selected ? format_string($selected->fullname) : '',
        'completion_enabled' => $homeoutline['completion_enabled'] ?? false,
        'journey' => $journey,
        'bot_url' => $pluginbase . '/assets/img/bot.webp',
        'track_url' => (new moodle_url(persona_home::FELLOW_PATH, array_filter([
            'view' => 'track',
            'id' => $courseid > 0 ? $courseid : null,
        ])))->out(false),
        'progress_url' => $progressurl,
        'phases_url' => $progressurl,
    ], $homesummary));
}

if ($view === 'achievements') {
    $content = $OUTPUT->render_from_template('local_launchpad/fellowship_achievements', array_merge($ach, [
        'has_crumbs' => true,
        'crumbs' => [
            $crumbhome,
            [
                'label' => get_string('fellowship_nav_achievements', 'local_launchpad'),
                'url' => '',
            ],
        ],
    ]));
}

$feedbackdata = fellowship_dashboard::list_assignment_feedback($userid, $courses);
$feedbackattention = (int) ($feedbackdata['attention_count'] ?? 0);
if ($view === 'feedback') {
    $content = $OUTPUT->render_from_template('local_launchpad/fellowship_feedback', array_merge($feedbackdata, [
        'has_courses' => $courses !== [],
        'empty_message' => get_string('fellowship_no_courses', 'local_launchpad'),
        'track_url' => (new moodle_url(persona_home::FELLOW_PATH, ['view' => 'track']))->out(false),
        'empty_avatar_url' => $pluginbase . '/assets/img/prof.webp',
        'has_crumbs' => true,
        'crumbs' => [
            $crumbhome,
            [
                'label' => get_string('fellowship_nav_feedback', 'local_launchpad'),
                'url' => '',
            ],
        ],
    ]));
}

// Mark current nav + Achievements earned/total + Feedback attention.
foreach ($nav as &$group) {
    foreach ($group['items'] as &$item) {
        $item['current'] = ($item['key'] === $view);
        if (($item['key'] ?? '') === 'achievements') {
            $item['has_badge'] = true;
            $item['badge'] = $badgesearned . '/' . $badgestotal;
        }
        if (($item['key'] ?? '') === 'feedback' && $feedbackattention > 0) {
            $item['has_badge'] = true;
            $item['badge'] = (string) $feedbackattention;
        }
    }
}
unset($group, $item);

$unread = 0;
if (class_exists('\message_popup\api')) {
    try {
        $unread = (int) \message_popup\api::count_unread_popup_notifications($userid);
    } catch (\Throwable $e) {
        $unread = 0;
    }
}
$notificationslabel = $unread > 0
    ? get_string('fellowship_notifications_unread', 'local_launchpad', $unread)
    : get_string('fellowship_notifications', 'local_launchpad');

$orbittips = [
    'home' => get_string('fellowship_orbit_tip_home', 'local_launchpad'),
    'track' => get_string('fellowship_orbit_tip_track', 'local_launchpad'),
    'progress' => get_string('fellowship_orbit_tip_phases', 'local_launchpad'),
    'achievements' => get_string('fellowship_orbit_tip_achievements', 'local_launchpad'),
    'feedback' => get_string('fellowship_orbit_tip_feedback', 'local_launchpad'),
    'phases' => get_string('fellowship_orbit_tip_phases', 'local_launchpad'),
    'completion' => get_string('fellowship_orbit_tip_completion', 'local_launchpad'),
    'help' => get_string('fellowship_orbit_tip_help', 'local_launchpad'),
    'news' => get_string('fellowship_orbit_tip_news', 'local_launchpad'),
    'calendar' => get_string('fellowship_orbit_tip_calendar', 'local_launchpad'),
    'portfolio' => get_string('fellowship_orbit_tip_portfolio', 'local_launchpad'),
];
$orbittip = $orbittips[$view] ?? $orbittips['home'];

$shell = $OUTPUT->render_from_template('local_launchpad/fellowship_shell', [
    'data_region' => fellowship_dashboard::DATA_REGION,
    'displayname' => $displayname,
    'firstname' => $firstname,
    'role_label' => get_string('fellowship_role_label', 'local_launchpad'),
    'nav_groups' => $nav,
    'content' => $content,
    'pagetitle' => $pagetitle,
    'home_url' => $homeurl,
    'logout_url' => $logouturl,
    'profile_url' => $profileurl,
    'progress_url' => $progressurl,
    'achievements_url' => $achievementsurl,
    'notifications_url' => $notificationsurl,
    'notifications_label' => $notificationslabel,
    'unread_notifications' => $unread,
    'has_unread_notifications' => $unread > 0,
    'xp_total' => $xptotal,
    'level_name' => $ach['level_name'],
    'level_index' => $ach['level_index'],
    'level_color' => $ach['level_color'],
    'has_next_level' => $ach['has_next_level'],
    'next_level_name' => $ach['next_level_name'],
    'xp_to_next' => $ach['xp_to_next'],
    'pct_to_next' => $ach['pct_to_next'],
    'initials' => fellowship_dashboard::initials($USER),
    'bot_url' => $pluginbase . '/assets/img/bot.webp',
    'orbit_tip' => $orbittip,
]);

header('Content-Type: text/html; charset=utf-8');
echo '<!doctype html>' . "\n";
echo '<html lang="' . s(current_language()) . '">' . "\n";
echo '<head>' . "\n";
echo '<meta charset="utf-8">' . "\n";
echo '<meta name="viewport" content="width=device-width, initial-scale=1">' . "\n";
echo '<title>' . s(get_string('fellowship_area_title', 'local_launchpad')) . '</title>' . "\n";
echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
echo '<link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&family=Syne:wght@600;700&display=swap" rel="stylesheet">' . "\n";
echo '<link rel="stylesheet" href="' . s($cssurl) . '">' . "\n";
echo '</head>' . "\n";
echo '<body class="local-launchpad-fellowship">' . "\n";
echo $shell;
echo '<script src="' . s($jsurl) . '" defer></script>' . "\n";
echo '</body>' . "\n";
echo '</html>';

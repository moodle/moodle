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
 * Fellowship dashboard data (enrolments, activities, completion).
 *
 * Read-only helpers for the Fellow shell. Never invents XP, grades, or
 * unlocks — only Moodle enrolments, visibility, and completion_info.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class fellowship_dashboard {

    /** Marker for automated checks. */
    public const DATA_REGION = 'local-launchpad-fellowship';

    /**
     * LaunchPad courses where the user is an active student.
     *
     * @param int $userid
     * @return list<\stdClass> id, fullname, shortname, idnumber, summary
     */
    public static function list_courses(int $userid): array {
        global $CFG, $DB;

        if ($userid <= 0) {
            return [];
        }

        require_once($CFG->libdir . '/enrollib.php');

        $sql = "SELECT DISTINCT c.id, c.fullname, c.shortname, c.idnumber, c.summary, c.summaryformat
                  FROM {user_enrolments} ue
                  JOIN {enrol} e ON e.id = ue.enrolid
                  JOIN {course} c ON c.id = e.courseid
                  JOIN {context} ctx ON ctx.instanceid = c.id AND ctx.contextlevel = :ctxlevel
                  JOIN {role_assignments} ra ON ra.contextid = ctx.id AND ra.userid = ue.userid
                  JOIN {role} r ON r.id = ra.roleid AND r.archetype = :arch
                 WHERE ue.userid = :userid
                   AND ue.status = :uestatus
                   AND e.status = :estatus
                   AND " . $DB->sql_like('c.idnumber', ':idn', false) . "
              ORDER BY c.fullname ASC";

        return array_values($DB->get_records_sql($sql, [
            'userid' => $userid,
            'uestatus' => ENROL_USER_ACTIVE,
            'estatus' => ENROL_INSTANCE_ENABLED,
            'ctxlevel' => CONTEXT_COURSE,
            'arch' => 'student',
            'idn' => $DB->sql_like_escape(account_persona::COURSE_IDNUMBER_PREFIX) . '%',
        ]));
    }

    /**
     * Visible course modules for a fellow, grouped by section, with completion.
     *
     * @param \stdClass $course
     * @param int $userid
     * @return array{
     *   sections: list<array{name: string, activities: list<array>}>,
     *   completion_enabled: bool,
     *   completed: int,
     *   trackable: int,
     *   percent: int|null,
     *   next: ?array
     * }
     */
    public static function course_outline(\stdClass $course, int $userid): array {
        global $CFG;

        require_once($CFG->libdir . '/completionlib.php');

        $modinfo = get_fast_modinfo($course, $userid);
        $completion = new \completion_info($course);
        $completionenabled = $completion->is_enabled();

        $sections = [];
        $completed = 0;
        $trackable = 0;
        $next = null;

        foreach ($modinfo->get_section_info_all() as $section) {
            if ($section->section <= 0 && empty($section->sequence)) {
                continue;
            }
            $cms = $modinfo->get_cms();
            $activities = [];
            $sectioncms = $modinfo->get_sections()[$section->section] ?? [];
            foreach ($sectioncms as $cmid) {
                if (!isset($cms[$cmid])) {
                    continue;
                }
                $cm = $cms[$cmid];
                if (!$cm->uservisible || $cm->deletioninprogress) {
                    continue;
                }
                // Skip labels (no dedicated learner destination).
                if ($cm->modname === 'label') {
                    continue;
                }

                $iscomplete = false;
                $tracked = false;
                if ($completionenabled && $completion->is_enabled($cm) != COMPLETION_TRACKING_NONE) {
                    $tracked = true;
                    $trackable++;
                    $data = $completion->get_data($cm, false, $userid);
                    $state = (int) $data->completionstate;
                    $iscomplete = ($state === COMPLETION_COMPLETE || $state === COMPLETION_COMPLETE_PASS);
                    if ($iscomplete) {
                        $completed++;
                    }
                }

                $url = $cm->url ? $cm->url->out(false) : (new \moodle_url('/mod/' . $cm->modname . '/view.php', [
                    'id' => $cm->id,
                ]))->out(false);

                $item = [
                    'cmid' => (int) $cm->id,
                    'name' => $cm->get_formatted_name(),
                    'modname' => $cm->modname,
                    'modname_display' => get_string('modulename', 'mod_' . $cm->modname),
                    'url' => $url,
                    'tracked' => $tracked,
                    'complete' => $iscomplete,
                    'icon_class' => self::mod_icon_class($cm->modname),
                ];
                $activities[] = $item;

                if ($next === null && (!$tracked || !$iscomplete)) {
                    $next = $item;
                }
            }

            if ($activities === [] && $section->section === 0) {
                continue;
            }

            if ($activities === []) {
                continue;
            }

            $secname = get_section_name($course, $section);
            if (trim((string) $secname) === '') {
                $secname = $section->section === 0
                    ? get_string('fellowship_section_general', 'local_launchpad')
                    : get_string('fellowship_section_numbered', 'local_launchpad', $section->section);
            }

            $sections[] = [
                'name' => $secname,
                'sectionnum' => (int) $section->section,
                'activities' => $activities,
                'has_activities' => true,
            ];
        }

        // Drop empty trailing sections with no activities.
        $sections = array_values(array_filter($sections, static function (array $s): bool {
            return !empty($s['has_activities']);
        }));

        $percent = null;
        if ($completionenabled && $trackable > 0) {
            $percent = (int) round(($completed / $trackable) * 100);
        }

        return [
            'sections' => $sections,
            'completion_enabled' => $completionenabled,
            'completed' => $completed,
            'trackable' => $trackable,
            'percent' => $percent,
            'next' => $next,
        ];
    }

    /**
     * Simple icon hint by module name (CSS class suffix).
     */
    public static function mod_icon_class(string $modname): string {
        return match ($modname) {
            'quiz' => 'quiz',
            'assign' => 'assign',
            'page' => 'page',
            'resource', 'folder', 'url' => 'resource',
            'forum' => 'forum',
            default => 'activity',
        };
    }

    /**
     * Programme phase weights (HTML PHW[]) — share of result per gate.
     *
     * @return array<int, int> sectionnum => weight percent
     */
    public static function phase_weights(): array {
        return [
            1 => 20,
            2 => 25,
            3 => 20,
            4 => 35,
        ];
    }

    /**
     * "Your journey" — same four gates / path as the HTML portal.
     *
     * Always renders Foundations → Guided project → Simulation → Demo day
     * (programme spine). Moodle section completion only drives status/progress;
     * empty later sections are still shown (locked), not dropped.
     *
     * @param array $outline Return value of {@see course_outline()}
     * @param bool $compact Home card uses compact; Phase progress uses full height + sublabels
     * @param array<int, string>|null $phasestatus Optional sectionnum => status label for sublabels
     * @return array{
     *   has_gates: bool,
     *   compact: bool,
     *   show_sublabels: bool,
     *   gates: list<array>,
     *   path_d: string,
     *   dash_offset: string,
     *   view_h: int,
     *   ring_r: int,
     *   core_r: int,
     *   mark_y: int,
     *   label_y: int,
     *   bot_left: string,
     *   bot_top: string
     * }
     */
    public static function journey_from_outline(array $outline, bool $compact = true, ?array $phasestatus = null): array {
        // Short labels match HTML PHS[]; full names match blueprint / HTML PH[].
        $spine = [
            1 => ['short' => 'Foundations', 'name' => 'Foundations'],
            2 => ['short' => 'Guided project', 'name' => 'Guided project'],
            3 => ['short' => 'Simulation', 'name' => 'Industry simulation'],
            4 => ['short' => 'Demo day', 'name' => 'Demo day and certification'],
        ];
        foreach (blueprint::phase_sections() as $num => $fullname) {
            if (isset($spine[$num])) {
                $spine[$num]['name'] = $fullname;
            }
        }
        $weights = self::phase_weights();

        $bysection = [];
        foreach ($outline['sections'] ?? [] as $section) {
            $num = (int) ($section['sectionnum'] ?? 0);
            if ($num > 0) {
                $bysection[$num] = $section;
            }
        }

        $phasedone = [];
        foreach ($spine as $num => $meta) {
            $section = $bysection[$num] ?? null;
            $tracked = 0;
            $done = 0;
            if ($section !== null) {
                foreach ($section['activities'] ?? [] as $activity) {
                    if (!empty($activity['tracked'])) {
                        $tracked++;
                        if (!empty($activity['complete'])) {
                            $done++;
                        }
                    }
                }
            }
            $phasedone[$num] = ($tracked > 0 && $done >= $tracked);
        }

        // HTML home journey: gate 0 is done|cur; later gates stay lock until earlier work is met.
        // We advance "current" when prior phases are complete (still show all four).
        $gates = [];
        $foundcurrent = false;
        $i = 0;
        foreach ($spine as $num => $meta) {
            $isdone = !empty($phasedone[$num]);
            $status = 'lock';
            $isdoneflag = false;
            $iscurrent = false;
            $islock = true;
            $mark = '🔒';
            if ($isdone) {
                $status = 'done';
                $isdoneflag = true;
                $islock = false;
                $mark = '✓';
            } else if (!$foundcurrent) {
                $status = 'cur';
                $iscurrent = true;
                $islock = false;
                $mark = (string) ($i + 1);
                $foundcurrent = true;
            }
            $statustext = $phasestatus[$num] ?? ($isdone ? 'Met' : ($iscurrent ? 'In progress' : 'Not released'));
            $weight = (int) ($weights[$num] ?? 0);
            $gates[] = [
                'name' => $meta['name'],
                'short' => $meta['short'],
                'sectionnum' => $num,
                'status' => $status,
                'is_done' => $isdoneflag,
                'is_current' => $iscurrent,
                'is_lock' => $islock,
                'mark' => $mark,
                'cx' => 0,
                'cy' => 0,
                'show_sublabel' => !$compact,
                'sublabel' => $weight . '% · ' . $statustext,
                // Duplicated on each gate so Mustache nest context can read sizes.
                'ring_r' => $compact ? 24 : 30,
                'core_r' => $compact ? 17 : 22,
                'mark_y' => $compact ? 6 : 7,
                'label_y' => $compact ? 44 : 54,
            ];
            $i++;
        }

        // Path geometry from ToBa-Learn-LaunchPad.html journey(compact).
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

        // HTML: foundations Met → 0.36; else scale activity progress within first ~30% of path.
        $foundationsmet = !empty($phasedone[1]);
        $completed = (int) ($outline['completed'] ?? 0);
        $trackable = (int) ($outline['trackable'] ?? 0);
        if ($foundationsmet) {
            $pathprogress = 0.36;
            // Nudge further when later phases complete.
            $extradone = 0;
            foreach ([2, 3, 4] as $n) {
                if (!empty($phasedone[$n])) {
                    $extradone++;
                }
            }
            $pathprogress = min(1.0, 0.36 + ($extradone * 0.21));
        } else if ($trackable > 0) {
            $pathprogress = min(0.30, ($completed / $trackable) * 0.30);
        } else {
            $pathprogress = 0.0;
        }
        $dashoffset = number_format(1.0 - $pathprogress, 3, '.', '');

        // Bot sits on the first gate (HTML: offset 62 compact / 78 full).
        $botlift = $compact ? 62 : 78;
        $botleft = round(($pts[0][0] / 1000) * 100, 2) . '%';
        $bottop = round((($pts[0][1] - $botlift) / $viewh) * 100, 2) . '%';

        return [
            'has_gates' => true,
            'compact' => $compact,
            'show_sublabels' => !$compact,
            'gates' => $gates,
            'path_d' => $pathd,
            'dash_offset' => $dashoffset,
            'view_h' => $viewh,
            'ring_r' => $compact ? 24 : 30,
            'core_r' => $compact ? 17 : 22,
            'mark_y' => $compact ? 6 : 7,
            'label_y' => $compact ? 44 : 54,
            'bot_left' => $botleft,
            'bot_top' => $bottop,
        ];
    }

    /**
     * Phase progress tab: journey + per-gate weight/score/status from Moodle assigns.
     *
     * Pass at 70/100 on a released assign grade in that section (HTML rule). Empty
     * later sections stay "Not released". Never invents marks.
     *
     * @param list<\stdClass> $courses
     * @param \stdClass|null $selected Preferred lp-* course
     * @return array
     */
    public static function phase_progress_for_user(int $userid, array $courses, ?\stdClass $selected = null): array {
        global $CFG, $DB;

        require_once($CFG->libdir . '/gradelib.php');

        $course = $selected;
        if ($course === null && $courses !== []) {
            $course = $courses[0];
        }

        $weights = self::phase_weights();
        $hints = [
            1 => get_string('fellowship_phase_hint_foundations', 'local_launchpad'),
            2 => get_string('fellowship_phase_hint_submission', 'local_launchpad'),
            3 => get_string('fellowship_phase_hint_submission', 'local_launchpad'),
            4 => get_string('fellowship_phase_hint_capstone', 'local_launchpad'),
        ];
        $names = blueprint::phase_sections();
        $shorts = [
            1 => 'Foundations',
            2 => 'Guided project',
            3 => 'Simulation',
            4 => 'Demo day',
        ];

        $outline = [
            'sections' => [],
            'completion_enabled' => false,
            'completed' => 0,
            'trackable' => 0,
            'percent' => null,
            'next' => null,
        ];
        $modinfo = null;
        if ($course !== null) {
            $outline = self::course_outline($course, $userid);
            $modinfo = get_fast_modinfo($course, $userid);
        }

        $bysection = [];
        foreach ($outline['sections'] ?? [] as $section) {
            $num = (int) ($section['sectionnum'] ?? 0);
            if ($num > 0) {
                $bysection[$num] = $section;
            }
        }

        $phases = [];
        $statusmap = [];
        foreach ($names as $num => $fullname) {
            $weight = (int) ($weights[$num] ?? 0);
            $section = $bysection[$num] ?? null;
            $activities = $section['activities'] ?? [];
            $visiblecount = count($activities);

            $status = 'Not released';
            $chip = 'line';
            $score = null;
            $scored = false;
            $ico = (string) $num;
            $icodim = true;

            if ($visiblecount > 0 && $course !== null && $modinfo !== null) {
                $icodim = false;
                $bestpct = null;
                $hasgrade = false;
                $submitted = false;
                $revising = false;

                foreach ($activities as $activity) {
                    $cmid = (int) ($activity['cmid'] ?? 0);
                    if ($cmid <= 0 || !isset($modinfo->cms[$cmid])) {
                        continue;
                    }
                    $cm = $modinfo->cms[$cmid];
                    if ($cm->modname !== 'assign' || !$cm->uservisible || $cm->deletioninprogress) {
                        continue;
                    }
                    $instanceid = (int) $cm->instance;

                    $latest = $DB->get_record('assign_submission', [
                        'assignment' => $instanceid,
                        'userid' => $userid,
                        'latest' => 1,
                    ], '*', IGNORE_MISSING);
                    if ($latest && $latest->status === 'submitted') {
                        $submitted = true;
                    } else if ($latest && in_array($latest->status, ['draft', 'reopened', 'new'], true)) {
                        $subcount = (int) $DB->count_records('assign_submission', [
                            'assignment' => $instanceid,
                            'userid' => $userid,
                            'status' => 'submitted',
                        ]);
                        if ($subcount > 0) {
                            $revising = true;
                        }
                    }

                    $gradeinfo = grade_get_grades($course->id, 'mod', 'assign', $instanceid, $userid);
                    $gg = $gradeinfo->items[0]->grades[$userid] ?? null;
                    $grademax = isset($gradeinfo->items[0]->grademax) ? (float) $gradeinfo->items[0]->grademax : 0.0;
                    if ($gg && $gg->grade !== null && $grademax > 0) {
                        $hasgrade = true;
                        $pct = (((float) $gg->grade) / $grademax) * 100;
                        if ($bestpct === null || $pct > $bestpct) {
                            $bestpct = $pct;
                        }
                    }
                }

                if ($hasgrade && $bestpct !== null) {
                    $score = (int) round($bestpct);
                    $scored = true;
                    if ($bestpct >= 70) {
                        $status = 'Met';
                        $chip = 'ok';
                        $ico = '✓';
                    } else {
                        $status = 'Not yet met';
                        $chip = 'warn';
                    }
                } else if ($submitted) {
                    $status = 'Review pending';
                    $chip = '';
                } else if ($revising) {
                    $status = 'Revising';
                    $chip = 'line';
                } else {
                    $status = 'In progress';
                    $chip = 'line';
                }
            }

            $statusmap[$num] = $status;
            $phases[] = [
                'sectionnum' => $num,
                'name' => $fullname,
                'short' => $shorts[$num] ?? $fullname,
                'weight' => $weight,
                'weight_label' => $weight . '%',
                'hint' => $hints[$num] ?? '',
                'status' => $status,
                'chip' => $chip,
                'has_chip_class' => $chip !== '',
                'score' => $score,
                'has_score' => $scored,
                'score_display' => $scored ? ($score . '/100') : '—',
                'ico' => $ico,
                'ico_dim' => $icodim,
                'is_met' => $status === 'Met',
                'is_not_released' => $status === 'Not released',
            ];
        }

        $journey = self::journey_from_outline($outline, false, $statusmap);

        return [
            'has_course' => $course !== null,
            'course_name' => $course ? format_string($course->fullname) : '',
            'course_idnumber' => $course ? s($course->idnumber) : '',
            'phases' => $phases,
            'has_phases' => $phases !== [],
            'journey' => $journey,
            'outline' => $outline,
        ];
    }

    /**
     * My track page context — HTML layout with live Moodle sections/activities.
     *
     * Full journey + phase accordions + Capstone / meta / How completion works.
     * Capstone + device/tools copy is programme-honest (not demo track fiction).
     * Mentor names come from course teachers when enrolled.
     *
     * @param \stdClass $course
     * @return array
     */
    public static function track_for_user(int $userid, \stdClass $course): array {
        $progress = self::phase_progress_for_user($userid, [$course], $course);
        $outline = $progress['outline'] ?? self::course_outline($course, $userid);
        $bysection = [];
        foreach ($outline['sections'] ?? [] as $section) {
            $num = (int) ($section['sectionnum'] ?? 0);
            if ($num > 0) {
                $bysection[$num] = $section;
            }
        }

        $blurbs = [
            1 => get_string('fellowship_track_blurb_1', 'local_launchpad'),
            2 => get_string('fellowship_track_blurb_2', 'local_launchpad'),
            3 => get_string('fellowship_track_blurb_3', 'local_launchpad'),
            4 => get_string('fellowship_track_blurb_4', 'local_launchpad'),
        ];

        $trackphases = [];
        $firstopen = true;
        foreach ($progress['phases'] ?? [] as $phase) {
            $num = (int) $phase['sectionnum'];
            $section = $bysection[$num] ?? null;
            $activities = $section['activities'] ?? [];
            $released = $activities !== [];
            $isopen = $released && $firstopen;
            if ($released) {
                $firstopen = false;
            }
            $trackphases[] = array_merge($phase, [
                'blurb' => $blurbs[$num] ?? '',
                'released' => $released,
                'is_open' => $isopen,
                'activities' => $activities,
                'has_activities' => $activities !== [],
                'locked_note' => get_string('fellowship_track_phase_locked', 'local_launchpad', $phase['weight']),
            ]);
        }

        $weights = [];
        $maxw = max(1, max(self::phase_weights()));
        foreach (self::phase_weights() as $num => $weight) {
            $weights[] = [
                'name' => blueprint::phase_sections()[$num] ?? ('Phase ' . $num),
                'weight' => $weight,
                'weight_label' => $weight . '%',
                'bar_pct' => (int) round(($weight / $maxw) * 100),
            ];
        }

        $summary = trim(format_string($course->summary ?? ''));
        $hasrealsummary = $summary !== '' && stripos($summary, 'placeholder') === false;

        $mentors = self::course_mentor_names($course);
        $mentorlabel = $mentors !== []
            ? implode(', ', $mentors)
            : get_string('fellowship_track_mentor_tba', 'local_launchpad');

        return [
            'course_name' => format_string($course->fullname),
            'course_idnumber' => s($course->idnumber),
            'course_lead' => $hasrealsummary
                ? $summary
                : get_string('fellowship_track_lead_default', 'local_launchpad'),
            'journey' => $progress['journey'],
            'track_phases' => $trackphases,
            'has_track_phases' => $trackphases !== [],
            'capstone_body' => get_string('fellowship_track_capstone_body', 'local_launchpad'),
            'prerequisite' => get_string('fellowship_track_prerequisite', 'local_launchpad'),
            'device' => get_string('fellowship_track_device', 'local_launchpad'),
            'tools' => get_string('fellowship_track_tools', 'local_launchpad'),
            'mentor' => $mentorlabel,
            'weight_rows' => $weights,
            'completion_enabled' => !empty($outline['completion_enabled']),
            'completed' => (int) ($outline['completed'] ?? 0),
            'trackable' => (int) ($outline['trackable'] ?? 0),
            'percent' => $outline['percent'] ?? null,
            'has_percent' => isset($outline['percent']) && $outline['percent'] !== null,
        ];
    }

    /**
     * Display names of teachers / editing teachers on a course (Mentors card).
     *
     * @return list<string>
     */
    public static function course_mentor_names(\stdClass $course): array {
        global $CFG, $DB;

        require_once($CFG->libdir . '/enrollib.php');

        $context = \context_course::instance((int) $course->id);
        $roleids = $DB->get_fieldset_select(
            'role',
            'id',
            'shortname IN (?, ?)',
            ['editingteacher', 'teacher']
        );
        if ($roleids === []) {
            return [];
        }

        list($insql, $params) = $DB->get_in_or_equal($roleids, SQL_PARAMS_NAMED);
        $params['ctx'] = $context->id;
        $params['active'] = ENROL_USER_ACTIVE;
        $params['enabled'] = ENROL_INSTANCE_ENABLED;

        $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.firstnamephonetic, u.lastnamephonetic,
                       u.middlename, u.alternatename
                  FROM {role_assignments} ra
                  JOIN {user} u ON u.id = ra.userid AND u.deleted = 0
                  JOIN {user_enrolments} ue ON ue.userid = u.id AND ue.status = :active
                  JOIN {enrol} e ON e.id = ue.enrolid AND e.courseid = :courseid AND e.status = :enabled
                 WHERE ra.contextid = :ctx AND ra.roleid {$insql}
              ORDER BY u.lastname ASC, u.firstname ASC";
        $params['courseid'] = (int) $course->id;

        $names = [];
        foreach ($DB->get_records_sql($sql, $params) as $user) {
            $names[] = fullname($user);
        }
        return array_values($names);
    }

    /**
     * Completion tab: gates passed + requirements table + locked certificate.
     *
     * Reuses {@see phase_progress_for_user()} status. Weighted result only when
     * every phase has a released score. Certificate stays locked (tool not live).
     *
     * @param list<\stdClass> $courses
     * @param \stdClass|null $selected
     * @return array
     */
    public static function completion_for_user(int $userid, array $courses, ?\stdClass $selected = null): array {
        $base = self::phase_progress_for_user($userid, $courses, $selected);
        $phases = $base['phases'] ?? [];
        $met = 0;
        $outstanding = [];
        $allscored = $phases !== [];
        $weightsum = 0.0;

        foreach ($phases as $phase) {
            if (!empty($phase['is_met'])) {
                $met++;
            } else {
                $outstanding[] = [
                    'label' => $phase['name'] . ': ' . \core_text::strtolower($phase['status']),
                ];
            }
            if (empty($phase['has_score'])) {
                $allscored = false;
            } else {
                $weightsum += ((float) $phase['score']) * ((int) $phase['weight']) / 100.0;
            }
        }

        $allmet = $phases !== [] && $met === count($phases);
        $gates = max(1, count($phases));
        $ringpct = (int) round(($met / $gates) * 100);

        // SVG ring geometry (size 120, matching HTML completion ring).
        $size = 120;
        $radius = ($size / 2) - 10;
        $circ = 2 * M_PI * $radius;
        $dashoffset = $circ * (1 - ($ringpct / 100));

        return array_merge($base, [
            'gates_met' => $met,
            'gates_total' => count($phases),
            'gates_label' => get_string('fellowship_completion_gates', 'local_launchpad', [
                'met' => $met,
                'total' => count($phases),
            ]),
            'all_met' => $allmet,
            'status_chip' => $allmet ? 'ok' : 'line',
            'status_label' => $allmet
                ? get_string('fellowship_completion_met', 'local_launchpad')
                : get_string('fellowship_completion_outstanding', 'local_launchpad'),
            'ring_pct' => $ringpct,
            'ring_size' => $size,
            'ring_radius' => round($radius, 2),
            'ring_cx' => $size / 2,
            'ring_cy' => $size / 2,
            'ring_circ' => round($circ, 2),
            'ring_dashoffset' => number_format($dashoffset, 2, '.', ''),
            'has_outstanding' => $outstanding !== [],
            'outstanding' => $outstanding,
            'has_weighted' => $allscored,
            'weighted_display' => $allscored ? (string) (int) round($weightsum) : '—',
            'weighted_chip' => $allscored ? ($weightsum >= 70 ? 'ok' : 'warn') : 'line',
            'weighted_status' => $allscored
                ? ($weightsum >= 70
                    ? get_string('fellowship_completion_weighted_ok', 'local_launchpad')
                    : get_string('fellowship_completion_weighted_low', 'local_launchpad'))
                : get_string('fellowship_completion_weighted_pending', 'local_launchpad'),
            'certificate_locked' => true,
        ]);
    }

    /**
     * Live XP total from Moodle activity records (same rules as Achievements log).
     *
     * @param list<\stdClass> $courses
     */
    public static function provisional_xp_total(array $courses, int $userid): int {
        $live = self::live_achievements($userid, $courses);
        return (int) $live['xp_total'];
    }

    /**
     * Level ladder from the HTML Fellow portal (thresholds + colours).
     *
     * @return list<array{name: string, xp: int, color: string}>
     */
    public static function achievement_levels(): array {
        return [
            ['name' => 'Explorer', 'xp' => 0, 'color' => '#8FB3FF'],
            ['name' => 'Builder', 'xp' => 200, 'color' => '#5B8DF6'],
            ['name' => 'Practitioner', 'xp' => 450, 'color' => '#1A61F2'],
            ['name' => 'Specialist', 'xp' => 800, 'color' => '#0F4FD6'],
            ['name' => 'Fellow', 'xp' => 1200, 'color' => '#0D2461'],
        ];
    }

    /**
     * Index into {@see achievement_levels()} for the given XP.
     */
    public static function level_index_for_xp(int $xp): int {
        $levels = self::achievement_levels();
        $index = 0;
        foreach ($levels as $i => $level) {
            if ($xp >= (int) $level['xp']) {
                $index = $i;
            }
        }
        return $index;
    }

    /**
     * Achievements template context from live Moodle progress for this fellow.
     *
     * @param list<\stdClass> $courses
     * @return array
     */
    public static function achievements_for_user(int $userid, array $courses): array {
        $live = self::live_achievements($userid, $courses);
        return self::achievements_context_from_live($live);
    }

    /**
     * @deprecated Use {@see achievements_for_user()}; kept for callers that only have XP.
     */
    public static function achievements_context(int $xp): array {
        return self::achievements_context_from_live([
            'xp_total' => $xp,
            'xp_log' => [],
            'badges' => self::badge_catalogue_empty(),
            'badges_earned' => 0,
        ]);
    }

    /**
     * Build Achievements view context from a {@see live_achievements()} result.
     *
     * @param array $live
     * @return array
     */
    public static function achievements_context_from_live(array $live): array {
        $xp = (int) ($live['xp_total'] ?? 0);
        $levels = self::achievement_levels();
        $li = self::level_index_for_xp($xp);
        $ladder = [];
        foreach ($levels as $i => $level) {
            $ispast = $i < $li;
            $isnow = $i === $li;
            $ladder[] = [
                'name' => $level['name'],
                'xp' => $level['xp'],
                'index' => $i + 1,
                'mark' => $ispast ? '✓' : (string) ($i + 1),
                'is_past' => $ispast,
                'is_now' => $isnow,
                'css' => $ispast ? 'past' : ($isnow ? 'now' : ''),
                // Past = green done; current keeps level colour; locked = grey.
                'badge_color' => $ispast ? '#2E9E4F' : ($isnow ? $level['color'] : '#D6DEEC'),
            ];
        }

        $current = $levels[$li];
        $next = $levels[$li + 1] ?? null;
        $tonext = null;
        $pcttonext = 100;
        if ($next !== null) {
            $span = max(1, (int) $next['xp'] - (int) $current['xp']);
            $into = max(0, $xp - (int) $current['xp']);
            $tonext = max(0, (int) $next['xp'] - $xp);
            $pcttonext = (int) min(100, round(($into / $span) * 100));
            // Exactly at a level threshold → 0% into the next band looks "broken"; show a floor fill.
            if ($pcttonext === 0 && $xp >= (int) $current['xp']) {
                $pcttonext = 6;
            }
        }

        $badges = $live['badges'] ?? self::badge_catalogue_empty();
        $xplog = $live['xp_log'] ?? [];

        $xprules = [
            ['label' => 'Finish a lesson', 'xp' => 50],
            ['label' => 'Submit a quiz attempt', 'xp' => 40],
            ['label' => 'Each correct answer', 'xp' => 10],
            ['label' => 'Submit an assignment', 'xp' => 120],
            ['label' => 'Resubmit after feedback', 'xp' => 80],
            ['label' => 'Pass a gate', 'xp' => 200],
            ['label' => 'Save a portfolio case study', 'xp' => 60],
        ];

        return [
            'xp_total' => $xp,
            'level_name' => $current['name'],
            'level_index' => $li + 1,
            'level_color' => $current['color'],
            'has_next_level' => $next !== null,
            'next_level_name' => $next['name'] ?? '',
            'xp_to_next' => $tonext,
            'pct_to_next' => $pcttonext,
            'ladder' => $ladder,
            'badges' => $badges,
            'badges_earned' => (int) ($live['badges_earned'] ?? 0),
            'badges_total' => count($badges),
            'has_xp_log' => $xplog !== [],
            'xp_log' => $xplog,
            'xp_rules' => $xprules,
        ];
    }

    /**
     * Derive XP log + badge state from Moodle completion, quiz grades, and assign submissions.
     *
     * No local unlock tables — recomputed from Moodle records each request.
     *
     * @param list<\stdClass> $courses
     * @return array{xp_total: int, xp_log: list<array>, badges: list<array>, badges_earned: int}
     */
    public static function live_achievements(int $userid, array $courses): array {
        global $CFG, $DB, $USER;

        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->libdir . '/gradelib.php');

        $xplog = [];
        $pagesdone = 0;
        $pagesdones1 = 0;
        $pagestotals1 = 0;
        $pagecompletebyname = [];
        $quizperfect = false;
        $quizperfectat = 0;
        $assignsubmitted = false;
        $assignsubmittedat = 0;
        $assignresubmit = false;
        $assignresubmitat = 0;
        $foundationscomplete = false;
        $foundationsgateat = 0;
        $foundationsgradeok = false;

        foreach ($courses as $course) {
            $modinfo = get_fast_modinfo($course, $userid);
            $completion = new \completion_info($course);
            $completionenabled = $completion->is_enabled();

            $s1tracked = 0;
            $s1done = 0;

            foreach ($modinfo->get_cms() as $cm) {
                if (!$cm->uservisible || $cm->deletioninprogress || $cm->modname === 'label') {
                    continue;
                }

                $sectionnum = 0;
                try {
                    $sectionnum = (int) $cm->get_section_info()->section;
                } catch (\Throwable $e) {
                    $sectionnum = 0;
                }

                $name = $cm->get_formatted_name();
                $namelower = \core_text::strtolower($name);
                $iscomplete = false;
                $completedtime = 0;
                if ($completionenabled && $completion->is_enabled($cm) != COMPLETION_TRACKING_NONE) {
                    $data = $completion->get_data($cm, false, $userid);
                    $state = (int) $data->completionstate;
                    $iscomplete = ($state === COMPLETION_COMPLETE || $state === COMPLETION_COMPLETE_PASS);
                    $completedtime = (int) ($data->timemodified ?? 0);
                }

                if ($cm->modname === 'page' || $cm->modname === 'resource' || $cm->modname === 'url') {
                    if ($sectionnum === 1) {
                        $pagestotals1++;
                        if ($iscomplete) {
                            $pagesdones1++;
                        }
                    }
                    if ($iscomplete) {
                        $pagesdone++;
                        $pagecompletebyname[] = $namelower;
                        $xplog[] = self::xp_log_entry(
                            $completedtime,
                            get_string('fellowship_xp_log_lesson', 'local_launchpad', $name),
                            50,
                            'page-' . $cm->id
                        );
                    }
                }

                if ($sectionnum === 1 && $completionenabled && $completion->is_enabled($cm) != COMPLETION_TRACKING_NONE) {
                    $s1tracked++;
                    if ($iscomplete) {
                        $s1done++;
                        $foundationsgateat = max($foundationsgateat, $completedtime);
                    }
                }

                if ($cm->modname === 'quiz') {
                    $attempts = $DB->get_records('quiz_attempts', [
                        'userid' => $userid,
                        'quiz' => (int) $cm->instance,
                        'state' => 'finished',
                    ], 'attempt DESC', '*', 0, 1);
                    $attempt = $attempts ? reset($attempts) : false;
                    if ($attempt) {
                        $at = (int) ($attempt->timefinish ?: $attempt->timestart);
                        $xplog[] = self::xp_log_entry(
                            $at,
                            get_string('fellowship_xp_log_quiz', 'local_launchpad', $name),
                            40,
                            'quiz-' . $cm->id
                        );
                        $quiz = $DB->get_record('quiz', ['id' => (int) $cm->instance], 'id,sumgrades,grade');
                        $fraction = null;
                        if ($quiz && (float) $quiz->sumgrades > 0 && $attempt->sumgrades !== null) {
                            $fraction = ((float) $attempt->sumgrades) / ((float) $quiz->sumgrades);
                        } else {
                            $grades = grade_get_grades($course->id, 'mod', 'quiz', (int) $cm->instance, $userid);
                            $gg = $grades->items[0]->grades[$userid] ?? null;
                            if ($gg && $gg->grade !== null && !empty($grades->items[0]->grademax)) {
                                $fraction = ((float) $gg->grade) / ((float) $grades->items[0]->grademax);
                            }
                        }
                        if ($fraction !== null && $fraction >= 0.999) {
                            $quizperfect = true;
                            $quizperfectat = max($quizperfectat, $at);
                            // HTML: +10 per correct (Foundations check = 4). Award 40 when perfect.
                            $xplog[] = self::xp_log_entry(
                                $at,
                                get_string('fellowship_xp_log_quiz_perfect', 'local_launchpad', $name),
                                40,
                                'quiz-perfect-' . $cm->id
                            );
                        } else if ($fraction !== null && $fraction > 0) {
                            $correctxp = (int) round($fraction * 4) * 10;
                            if ($correctxp > 0) {
                                $xplog[] = self::xp_log_entry(
                                    $at,
                                    get_string('fellowship_xp_log_quiz_correct', 'local_launchpad', $name),
                                    $correctxp,
                                    'quiz-correct-' . $cm->id
                                );
                            }
                        }
                    }
                }

                if ($cm->modname === 'assign') {
                    $allsubs = $DB->get_records('assign_submission', [
                        'assignment' => (int) $cm->instance,
                        'userid' => $userid,
                        'status' => 'submitted',
                    ], 'attemptnumber ASC');
                    if ($allsubs) {
                        $first = true;
                        foreach ($allsubs as $sub) {
                            $at = (int) ($sub->timemodified ?: $sub->timecreated);
                            if ($first) {
                                $assignsubmitted = true;
                                $assignsubmittedat = max($assignsubmittedat, $at);
                                $xplog[] = self::xp_log_entry(
                                    $at,
                                    get_string('fellowship_xp_log_assign', 'local_launchpad', $name),
                                    120,
                                    'assign-' . $cm->id
                                );
                                $first = false;
                            } else {
                                $assignresubmit = true;
                                $assignresubmitat = max($assignresubmitat, $at);
                                $xplog[] = self::xp_log_entry(
                                    $at,
                                    get_string('fellowship_xp_log_resubmit', 'local_launchpad', $name),
                                    80,
                                    'assign-re-' . $cm->id . '-' . $sub->attemptnumber
                                );
                            }
                        }
                    }

                    $grades = grade_get_grades($course->id, 'mod', 'assign', (int) $cm->instance, $userid);
                    $gg = $grades->items[0]->grades[$userid] ?? null;
                    if ($gg && $gg->grade !== null && !empty($grades->items[0]->grademax)) {
                        $pct = (((float) $gg->grade) / ((float) $grades->items[0]->grademax)) * 100;
                        if ($sectionnum === 1 && $pct >= 70) {
                            $foundationsgradeok = true;
                            $foundationsgateat = max($foundationsgateat, (int) ($gg->dategraded ?: time()));
                        }
                    }
                }
            }

            if ($s1tracked > 0 && $s1done >= $s1tracked) {
                $foundationscomplete = true;
            }
        }

        $gatecleared = $foundationscomplete || $foundationsgradeok;
        if ($gatecleared) {
            $xplog[] = self::xp_log_entry(
                $foundationsgateat ?: time(),
                get_string('fellowship_xp_log_gate', 'local_launchpad'),
                200,
                'gate-foundations'
            );
        }

        // Newest first for the history table.
        usort($xplog, static function (array $a, array $b): int {
            return ($b['ts'] <=> $a['ts']) ?: strcmp($a['key'], $b['key']);
        });
        // Dedupe by key (gate / activities).
        $seen = [];
        $deduped = [];
        foreach ($xplog as $row) {
            if (isset($seen[$row['key']])) {
                continue;
            }
            $seen[$row['key']] = true;
            $deduped[] = $row;
        }
        $xplog = $deduped;

        $xptotal = 0;
        foreach ($xplog as $row) {
            $xptotal += (int) $row['xp'];
        }

        $evidence = false;
        $evidenceat = 0;
        foreach ($pagecompletebyname as $n) {
            if (str_contains($n, 'evidence') || str_contains($n, 'assumption')) {
                $evidence = true;
            }
        }
        if (!$evidence && $pagesdone >= 2) {
            $evidence = true;
        }

        $alllessons = ($pagestotals1 > 0 && $pagesdones1 >= $pagestotals1)
            || ($pagestotals1 === 0 && $pagesdone >= 3);

        $profiledone = false;
        $profileat = 0;
        $user = ($USER && (int) $USER->id === $userid) ? $USER : \core_user::get_user($userid);
        if ($user) {
            $headline = trim(strip_tags((string) ($user->description ?? '')));
            $city = trim((string) ($user->city ?? ''));
            if ($headline !== '' || $city !== '') {
                $profiledone = true;
                $profileat = (int) ($user->timemodified ?? time());
            }
        }

        $badgedefs = [
            'first' => [
                'name' => 'First step',
                'icon' => '🌱',
                'how' => 'Finish your first lesson',
                'earned' => $pagesdone >= 1,
                'at' => $pagesdone >= 1 ? self::first_log_ts($xplog, 'page-') : 0,
            ],
            'evidence' => [
                'name' => 'Evidence hunter',
                'icon' => '🔍',
                'how' => 'Finish Lesson 2: Evidence versus assumptions',
                'earned' => $evidence,
                'at' => $evidence ? self::first_log_ts($xplog, 'page-') : 0,
            ],
            'allLessons' => [
                'name' => 'Foundations reader',
                'icon' => '📚',
                'how' => 'Finish all Foundations lessons in your track',
                'earned' => $alllessons,
                'at' => $alllessons ? self::first_log_ts($xplog, 'page-') : 0,
            ],
            'ace' => [
                'name' => 'Quiz ace',
                'icon' => '🎯',
                'how' => 'Score full marks on the Foundations check',
                'earned' => $quizperfect,
                'at' => $quizperfectat,
            ],
            'handin' => [
                'name' => 'Hand-in hero',
                'icon' => '📨',
                'how' => 'Submit your first assignment',
                'earned' => $assignsubmitted,
                'at' => $assignsubmittedat,
            ],
            'loop' => [
                'name' => 'Feedback loop',
                'icon' => '🔄',
                'how' => 'Resubmit after your mentor requests changes',
                'earned' => $assignresubmit,
                'at' => $assignresubmitat,
            ],
            'gate' => [
                'name' => 'Gate one cleared',
                'icon' => '🏁',
                'how' => 'Pass Foundations (completion or grade ≥ 70)',
                'earned' => $gatecleared,
                'at' => $foundationsgateat,
            ],
            'portfolio' => [
                'name' => 'Portfolio starter',
                'icon' => '🗂️',
                'how' => 'Save your first portfolio case study',
                'earned' => false,
                'at' => 0,
            ],
            'profile' => [
                'name' => 'Introduced',
                'icon' => '👋',
                'how' => 'Add a city or description on your Moodle profile',
                'earned' => $profiledone,
                'at' => $profileat,
            ],
        ];

        $badges = [];
        $earnedcount = 0;
        foreach ($badgedefs as $id => $def) {
            $earned = !empty($def['earned']);
            if ($earned) {
                $earnedcount++;
            }
            $at = (int) ($def['at'] ?? 0);
            $badges[] = [
                'id' => $id,
                'name' => $def['name'],
                'icon' => $def['icon'],
                'how' => $def['how'],
                'earned' => $earned,
                'at' => $at,
                'display_icon' => $earned ? $def['icon'] : '🔒',
                'detail' => $earned
                    ? get_string('fellowship_badge_earned_on', 'local_launchpad', $at ? userdate($at, get_string('strftimedate', 'langconfig')) : get_string('fellowship_badge_earned_recent', 'local_launchpad'))
                    : $def['how'],
                'css' => $earned ? 'on' : '',
            ];
        }

        return [
            'xp_total' => $xptotal,
            'xp_log' => array_map(static function (array $row): array {
                return [
                    'at' => $row['at'],
                    'title' => $row['title'],
                    'xp' => $row['xp'],
                ];
            }, $xplog),
            'badges' => $badges,
            'badges_earned' => $earnedcount,
        ];
    }

    /**
     * Home hero ring, KPI strip, and recent badges (newest earned first).
     *
     * @param array $outline From {@see course_outline()}
     * @param array $journey From {@see journey_from_outline()}
     * @param array $live From {@see live_achievements()}
     * @param int $limit Max recent badges to show
     * @return array Mustache extras for fellowship_home
     */
    public static function home_summary(array $outline, array $journey, array $live, int $limit = 4): array {
        $percent = $outline['percent'] ?? null;
        $completed = (int) ($outline['completed'] ?? 0);
        $trackable = (int) ($outline['trackable'] ?? 0);
        $hascoursepercent = $percent !== null;

        $currentgate = get_string('fellowship_gate_none', 'local_launchpad');
        $currentshort = $currentgate;
        $currentsection = 0;
        foreach ($journey['gates'] ?? [] as $gate) {
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

        // Prefer course-wide activity completion %. If nothing is trackable yet,
        // fall back to the current gate section (still 0 when that section has
        // no completion criteria). Always show the hero ring when enrolled.
        $ringpct = $hascoursepercent ? (int) $percent : 0;
        $gatecompleted = 0;
        $gatetrackable = 0;
        if (!$hascoursepercent && $currentsection > 0) {
            foreach ($outline['sections'] ?? [] as $section) {
                if ((int) ($section['sectionnum'] ?? 0) !== $currentsection) {
                    continue;
                }
                foreach ($section['activities'] ?? [] as $activity) {
                    if (empty($activity['tracked'])) {
                        continue;
                    }
                    $gatetrackable++;
                    if (!empty($activity['complete'])) {
                        $gatecompleted++;
                    }
                }
            }
            if ($gatetrackable > 0) {
                $ringpct = (int) round(($gatecompleted / $gatetrackable) * 100);
            }
        }

        $alldone = $hascoursepercent && $trackable > 0 && $completed >= $trackable;
        if ($alldone) {
            $currentshort = get_string('fellowship_gate_complete', 'local_launchpad');
            $currentgate = $currentshort;
        }

        $size = 112;
        $radius = ($size / 2) - 10;
        $circ = 2 * M_PI * $radius;
        $dashoffset = $circ * (1 - ($ringpct / 100));

        $badges = $live['badges'] ?? [];
        $earned = [];
        foreach ($badges as $badge) {
            if (empty($badge['earned'])) {
                continue;
            }
            $earned[] = $badge;
        }
        usort($earned, static function (array $a, array $b): int {
            $at = ((int) ($b['at'] ?? 0)) <=> ((int) ($a['at'] ?? 0));
            if ($at !== 0) {
                return $at;
            }
            return strcmp((string) ($a['name'] ?? ''), (string) ($b['name'] ?? ''));
        });
        $recent = array_slice($earned, 0, max(1, $limit));

        $badgesearned = (int) ($live['badges_earned'] ?? count($earned));
        $badgestotal = count($badges);

        $requiredvalue = '—';
        if ($hascoursepercent) {
            $requiredvalue = $completed . '/' . $trackable;
        } else if ($gatetrackable > 0) {
            $requiredvalue = $gatecompleted . '/' . $gatetrackable;
        }

        $foundations = self::section_activities_from_outline($outline, 1);

        return [
            // Always render the hero ring for enrolled fellows (0% is honest).
            'show_hero_ring' => true,
            'has_percent' => $hascoursepercent,
            'percent' => $hascoursepercent ? (int) $percent : null,
            'completed' => $completed,
            'trackable' => $trackable,
            'ring_pct' => $ringpct,
            'ring_label' => $currentshort,
            'ring_size' => $size,
            'ring_radius' => round($radius, 2),
            'ring_cx' => $size / 2,
            'ring_cy' => $size / 2,
            'ring_circ' => round($circ, 2),
            'ring_dashoffset' => number_format($dashoffset, 2, '.', ''),
            'stat_badges_value' => $badgesearned . '/' . $badgestotal,
            'stat_required_value' => $requiredvalue,
            'stat_gate_value' => $currentgate,
            'recent_badges' => $recent,
            'has_recent_badges' => $recent !== [],
            'achievements_url' => (new \moodle_url(persona_home::FELLOW_PATH, ['view' => 'achievements']))->out(false),
            'foundations_activities' => $foundations,
            'has_foundations_activities' => $foundations !== [],
        ];
    }

    /**
     * Activities for one course section from a course_outline() result.
     *
     * @param array $outline
     * @param int $sectionnum
     * @return list<array>
     */
    public static function section_activities_from_outline(array $outline, int $sectionnum): array {
        foreach ($outline['sections'] ?? [] as $section) {
            if ((int) ($section['sectionnum'] ?? -1) === $sectionnum) {
                return array_values($section['activities'] ?? []);
            }
        }
        return [];
    }

    /**
     * Course news-forum posts for enrolled lp-* courses (newest first).
     *
     * @param list<\stdClass> $courses
     * @return array{items: list<array>, has_items: bool, moodle_news_url: string}
     */
    public static function list_announcements(int $userid, array $courses): array {
        global $DB;

        // Read-only: never call forum_get_course_forum() — it creates a news forum if missing.
        $items = [];
        $firstforumurl = (new \moodle_url('/my/'))->out(false);

        foreach ($courses as $course) {
            $modinfo = get_fast_modinfo($course, $userid);
            foreach ($modinfo->get_cms() as $cm) {
                if ($cm->modname !== 'forum' || !$cm->uservisible || $cm->deletioninprogress) {
                    continue;
                }
                $forum = $DB->get_record('forum', ['id' => (int) $cm->instance], 'id,type', IGNORE_MISSING);
                if (!$forum || $forum->type !== 'news') {
                    continue;
                }

                $forumurl = (new \moodle_url('/mod/forum/view.php', ['id' => $cm->id]))->out(false);
                if ($firstforumurl === (new \moodle_url('/my/'))->out(false)) {
                    $firstforumurl = $forumurl;
                }

                $discussions = $DB->get_records('forum_discussions', [
                    'forum' => (int) $forum->id,
                ], 'timemodified DESC', '*', 0, 20);
                foreach ($discussions as $discussion) {
                    $post = $DB->get_record('forum_posts', [
                        'discussion' => (int) $discussion->id,
                        'parent' => 0,
                    ]);
                    if (!$post) {
                        continue;
                    }
                    $author = $DB->get_record('user', ['id' => (int) $post->userid], 'id,firstname,lastname');
                    $items[] = [
                        'id' => (int) $discussion->id,
                        'subject' => format_string($discussion->name),
                        'excerpt' => shorten_text(format_text(
                            $post->message,
                            (int) $post->messageformat,
                            ['context' => \context_module::instance((int) $cm->id), 'para' => false]
                        ), 180),
                        'author' => $author ? fullname($author) : '',
                        'when' => userdate((int) $discussion->timemodified, get_string('strftimedatetimeshort', 'langconfig')),
                        'timemodified' => (int) $discussion->timemodified,
                        'url' => (new \moodle_url('/mod/forum/discuss.php', ['d' => $discussion->id]))->out(false),
                        'course_name' => format_string($course->fullname),
                    ];
                }
            }
        }

        usort($items, static fn(array $a, array $b): int => ($b['timemodified'] <=> $a['timemodified']));
        $items = array_slice($items, 0, 15);

        return [
            'items' => $items,
            'has_items' => $items !== [],
            'moodle_news_url' => $firstforumurl,
        ];
    }

    /**
     * Upcoming calendar events for the fellow across enrolled courses + user events.
     *
     * @param list<\stdClass> $courses
     * @return array{items: list<array>, has_items: bool, moodle_calendar_url: string}
     */
    public static function list_calendar_events(int $userid, array $courses): array {
        global $CFG;

        require_once($CFG->dirroot . '/calendar/lib.php');

        $courseids = array_map(static fn($c) => (int) $c->id, $courses);
        $tstart = time() - (1 * DAYSECS);
        $tend = time() + (90 * DAYSECS);
        $raw = calendar_get_legacy_events(
            $tstart,
            $tend,
            $userid,
            true,
            $courseids === [] ? true : $courseids,
            true,
            true
        );

        $coursenames = [];
        foreach ($courses as $c) {
            $coursenames[(int) $c->id] = format_string($c->fullname);
        }

        $items = [];
        foreach ($raw as $event) {
            $courseid = (int) ($event->courseid ?? 0);
            $items[] = [
                'id' => (int) $event->id,
                'name' => format_string($event->name),
                'when' => userdate((int) $event->timestart, get_string('strftimedatetimeshort', 'langconfig')),
                'timestart' => (int) $event->timestart,
                'course_name' => $courseid > 0 ? ($coursenames[$courseid] ?? '') : get_string('fellowship_calendar_personal', 'local_launchpad'),
                'url' => (new \moodle_url('/calendar/view.php', [
                    'view' => 'day',
                    'time' => (int) $event->timestart,
                ]))->out(false),
            ];
        }
        usort($items, static fn(array $a, array $b): int => ($a['timestart'] <=> $b['timestart']));

        return [
            'items' => $items,
            'has_items' => $items !== [],
            'moodle_calendar_url' => (new \moodle_url('/calendar/view.php', ['view' => 'month']))->out(false),
        ];
    }

    /**
     * Portfolio = assign evidence from enrolled tracks (submissions + grades), not invented files.
     *
     * @param list<\stdClass> $courses
     * @return array{items: list<array>, has_items: bool}
     */
    public static function list_portfolio_evidence(int $userid, array $courses): array {
        $feedback = self::list_assignment_feedback($userid, $courses);
        $items = [];
        foreach ($feedback['items'] ?? [] as $row) {
            $when = (string) ($row['graded_at'] ?? '');
            if ($when === '') {
                $when = (string) ($row['submitted_at'] ?? '');
            }
            $items[] = [
                'name' => $row['name'],
                'course_name' => $row['course_name'] ?? '',
                'status' => $row['status_label'] ?? '',
                'chip' => $row['chip'] ?? 'line',
                'when' => $when,
                'url' => $row['url'],
                'has_comment' => !empty($row['has_comment']),
            ];
        }
        return [
            'items' => $items,
            'has_items' => $items !== [],
        ];
    }

    /**
     * @return list<array>
     */
    private static function badge_catalogue_empty(): array {
        $live = [
            'badges' => [],
            'badges_earned' => 0,
        ];
        // Reuse definitions with nothing earned via a zero-user path is heavy; inline locked catalogue.
        foreach ([
            ['first', 'First step', '🌱', 'Finish your first lesson'],
            ['evidence', 'Evidence hunter', '🔍', 'Finish Lesson 2: Evidence versus assumptions'],
            ['allLessons', 'Foundations reader', '📚', 'Finish all Foundations lessons in your track'],
            ['ace', 'Quiz ace', '🎯', 'Score full marks on the Foundations check'],
            ['handin', 'Hand-in hero', '📨', 'Submit your first assignment'],
            ['loop', 'Feedback loop', '🔄', 'Resubmit after your mentor requests changes'],
            ['gate', 'Gate one cleared', '🏁', 'Pass Foundations (completion or grade ≥ 70)'],
            ['portfolio', 'Portfolio starter', '🗂️', 'Save your first portfolio case study'],
            ['profile', 'Introduced', '👋', 'Add a city or description on your Moodle profile'],
        ] as $row) {
            $live['badges'][] = [
                'id' => $row[0],
                'name' => $row[1],
                'icon' => $row[2],
                'how' => $row[3],
                'earned' => false,
                'display_icon' => '🔒',
                'detail' => $row[3],
                'css' => '',
            ];
        }
        return $live['badges'];
    }

    /**
     * @return array{ts: int, at: string, title: string, xp: int, key: string}
     */
    private static function xp_log_entry(int $ts, string $title, int $xp, string $key): array {
        if ($ts <= 0) {
            $ts = time();
        }
        return [
            'ts' => $ts,
            'at' => userdate($ts, get_string('strftimedatetimeshort', 'langconfig')),
            'title' => $title,
            'xp' => $xp,
            'key' => $key,
        ];
    }

    /**
     * @param list<array{key: string, ts: int}> $xplog
     */
    private static function first_log_ts(array $xplog, string $prefix): int {
        $best = 0;
        foreach ($xplog as $row) {
            if (str_starts_with((string) $row['key'], $prefix)) {
                $best = max($best, (int) $row['ts']);
            }
        }
        return $best;
    }

    /**
     * Assignment feedback cards for the Fellowship Feedback tab (Moodle assign + gradebook).
     *
     * Statuses: not_submitted | pending | reviewed | changes
     * "changes" = graded and attempt reopened for resubmit, or latest submission is reopened/draft after a grade.
     *
     * @param list<\stdClass> $courses
     * @return array{
     *   items: list<array>,
     *   has_items: bool,
     *   has_assigns: bool,
     *   attention_count: int,
     *   has_attention: bool
     * }
     */
    public static function list_assignment_feedback(int $userid, array $courses): array {
        global $CFG, $DB;

        require_once($CFG->libdir . '/gradelib.php');

        $items = [];
        $attention = 0;
        $assigncount = 0;

        foreach ($courses as $course) {
            $modinfo = get_fast_modinfo($course, $userid);
            foreach ($modinfo->get_cms() as $cm) {
                if ($cm->modname !== 'assign' || !$cm->uservisible || $cm->deletioninprogress) {
                    continue;
                }
                $assigncount++;
                $instanceid = (int) $cm->instance;
                $name = $cm->get_formatted_name();
                $url = (new \moodle_url('/mod/assign/view.php', ['id' => $cm->id]))->out(false);

                $latest = $DB->get_record('assign_submission', [
                    'assignment' => $instanceid,
                    'userid' => $userid,
                    'latest' => 1,
                ]);
                $submittedcount = (int) $DB->count_records('assign_submission', [
                    'assignment' => $instanceid,
                    'userid' => $userid,
                    'status' => 'submitted',
                ]);

                $agrades = $DB->get_records_select(
                    'assign_grades',
                    'assignment = :assignment AND userid = :userid AND grade >= 0',
                    ['assignment' => $instanceid, 'userid' => $userid],
                    'attemptnumber DESC, timemodified DESC',
                    '*',
                    0,
                    1
                );
                $agrade = $agrades ? reset($agrades) : false;

                $commenttext = '';
                $gradedat = 0;
                $gradepct = null;
                $gradestr = '';
                $hasgrade = false;

                if ($agrade) {
                    $gradedat = (int) ($agrade->timemodified ?: 0);
                    // Moodle uses -1 for "no grade" on assign_grades.
                    if ($agrade->grade !== null && (float) $agrade->grade >= 0) {
                        $hasgrade = true;
                        $assignmax = $DB->get_field('assign', 'grade', ['id' => $instanceid]);
                        if ($assignmax !== false && (float) $assignmax > 0) {
                            $gradepct = (((float) $agrade->grade) / ((float) $assignmax)) * 100;
                            $gradestr = format_float($gradepct, 0) . '%';
                        } else {
                            $gradestr = format_float((float) $agrade->grade, 2);
                        }
                    }
                    $fc = $DB->get_record('assignfeedback_comments', [
                        'assignment' => $instanceid,
                        'grade' => (int) $agrade->id,
                    ]);
                    if ($fc && trim((string) $fc->commenttext) !== '') {
                        $hasgrade = true; // Comment released counts as feedback even if mark pending.
                        $commenttext = format_text(
                            $fc->commenttext,
                            (int) $fc->commentformat,
                            ['context' => \context_module::instance($cm->id), 'para' => false]
                        );
                    }
                }

                $gradeinfo = grade_get_grades($course->id, 'mod', 'assign', $instanceid, $userid);
                $gg = $gradeinfo->items[0]->grades[$userid] ?? null;
                $grademax = isset($gradeinfo->items[0]->grademax) ? (float) $gradeinfo->items[0]->grademax : 0.0;
                if ($gg && $gg->grade !== null && $grademax > 0) {
                    $hasgrade = true;
                    $gradepct = (((float) $gg->grade) / $grademax) * 100;
                    $gradestr = format_float($gradepct, 0) . '%';
                    if (!empty($gg->dategraded)) {
                        $gradedat = max($gradedat, (int) $gg->dategraded);
                    }
                    if ($commenttext === '' && !empty($gg->feedback)) {
                        $commenttext = format_text(
                            $gg->feedback,
                            (int) ($gg->feedbackformat ?? FORMAT_MOODLE),
                            ['context' => \context_module::instance($cm->id), 'para' => false]
                        );
                    }
                }

                $substatus = $latest ? (string) $latest->status : '';
                $attempt = $latest ? ((int) $latest->attemptnumber + 1) : 0;
                $submittedat = 0;
                if ($latest && ($substatus === 'submitted' || $substatus === 'reopened')) {
                    $submittedat = (int) ($latest->timemodified ?: $latest->timecreated);
                } else if ($submittedcount > 0) {
                    $lastsub = $DB->get_records('assign_submission', [
                        'assignment' => $instanceid,
                        'userid' => $userid,
                        'status' => 'submitted',
                    ], 'timemodified DESC', '*', 0, 1);
                    if ($lastsub) {
                        $s = reset($lastsub);
                        $submittedat = (int) ($s->timemodified ?: $s->timecreated);
                        if ($attempt <= 0) {
                            $attempt = (int) $s->attemptnumber + 1;
                        }
                    }
                }

                // Classify.
                $status = 'not_submitted';
                $chip = 'line';
                $needsattention = false;
                if ($hasgrade && ($substatus === 'reopened' || $substatus === 'draft' || $substatus === 'new')) {
                    $status = 'changes';
                    $chip = 'warn';
                    $needsattention = true;
                } else if ($hasgrade) {
                    $status = 'reviewed';
                    $chip = ($gradepct !== null && $gradepct < 70) ? 'warn' : 'ok';
                    if ($gradepct !== null && $gradepct < 70) {
                        $needsattention = true;
                    }
                } else if ($substatus === 'submitted' || $submittedcount > 0) {
                    $status = 'pending';
                    $chip = '';
                } else {
                    $status = 'not_submitted';
                    $chip = 'line';
                }

                if ($needsattention) {
                    $attention++;
                }

                $statuslabel = match ($status) {
                    'pending' => get_string('fellowship_feedback_status_pending', 'local_launchpad'),
                    'reviewed' => get_string('fellowship_feedback_status_reviewed', 'local_launchpad'),
                    'changes' => get_string('fellowship_feedback_status_changes', 'local_launchpad'),
                    default => get_string('fellowship_feedback_status_none', 'local_launchpad'),
                };

                $summary = match ($status) {
                    'pending' => get_string('fellowship_feedback_summary_pending', 'local_launchpad'),
                    'reviewed' => get_string('fellowship_feedback_summary_reviewed', 'local_launchpad'),
                    'changes' => get_string('fellowship_feedback_summary_changes', 'local_launchpad'),
                    default => get_string('fellowship_feedback_summary_none', 'local_launchpad'),
                };

                $items[] = [
                    'cmid' => (int) $cm->id,
                    'name' => $name,
                    'course_name' => format_string($course->fullname),
                    'course_idnumber' => s($course->idnumber),
                    'url' => $url,
                    'status' => $status,
                    'status_label' => $statuslabel,
                    'chip' => $chip,
                    'summary' => $summary,
                    'has_grade' => $hasgrade && $gradestr !== '',
                    'grade_display' => $gradestr,
                    'has_comment' => $commenttext !== '',
                    'comment_html' => $commenttext,
                    'has_attempt' => $attempt > 0,
                    'attempt' => $attempt,
                    'submitted_at' => $submittedat ? userdate($submittedat, get_string('strftimedatetimeshort', 'langconfig')) : '',
                    'graded_at' => $gradedat ? userdate($gradedat, get_string('strftimedatetimeshort', 'langconfig')) : '',
                    'needs_attention' => $needsattention,
                    'cta_label' => match ($status) {
                        'not_submitted' => get_string('fellowship_feedback_cta_open', 'local_launchpad'),
                        'pending' => get_string('fellowship_feedback_cta_view', 'local_launchpad'),
                        'changes' => get_string('fellowship_feedback_cta_revise', 'local_launchpad'),
                        default => get_string('fellowship_feedback_cta_view', 'local_launchpad'),
                    },
                ];
            }
        }

        $released = [];
        $pending = [];
        $notsubmitted = [];
        foreach ($items as $item) {
            if ($item['status'] === 'reviewed' || $item['status'] === 'changes') {
                $released[] = $item;
            } else if ($item['status'] === 'pending') {
                $pending[] = $item;
            } else {
                $notsubmitted[] = $item;
            }
        }

        // HTML-style empty: no *released* mentor feedback yet.
        $hasreleased = $released !== [];
        $emptyctaurl = '';
        $emptyctalabel = get_string('fellowship_feedback_cta_open', 'local_launchpad');
        $emptybody = get_string('fellowship_feedback_empty_submit', 'local_launchpad');
        if ($pending !== []) {
            $emptybody = get_string('fellowship_feedback_empty_waiting', 'local_launchpad');
            $emptyctaurl = $pending[0]['url'];
            $emptyctalabel = get_string('fellowship_feedback_cta_receipt', 'local_launchpad');
        } else if ($notsubmitted !== []) {
            $emptybody = get_string('fellowship_feedback_empty_submit', 'local_launchpad');
            $emptyctaurl = $notsubmitted[0]['url'];
            $emptyctalabel = get_string('fellowship_feedback_cta_open', 'local_launchpad');
        } else if ($items !== []) {
            $emptyctaurl = $items[0]['url'];
        }

        return [
            'items' => $items,
            'released_items' => $released,
            'pending_items' => $pending,
            'open_items' => array_values(array_merge($pending, $notsubmitted)),
            'has_released' => $hasreleased,
            'has_open_items' => ($pending !== [] || $notsubmitted !== []),
            'has_items' => $items !== [],
            'has_assigns' => $assigncount > 0,
            'attention_count' => $attention,
            'has_attention' => $attention > 0,
            // Banner only — cards below still update from Moodle (not a static forever page).
            'show_empty_released' => $assigncount > 0 && !$hasreleased,
            'empty_title' => get_string('fellowship_feedback_empty_title', 'local_launchpad'),
            'empty_body' => $emptybody,
            'empty_cta_url' => $emptyctaurl,
            'empty_cta_label' => $emptyctalabel,
            'has_empty_cta' => $emptyctaurl !== '',
        ];
    }

    /**
     * Whether the user may open the Fellowship shell.
     */
    public static function can_access(int $userid): bool {
        if ($userid <= 0) {
            return false;
        }
        if (is_siteadmin($userid)) {
            // Admins may preview; persona_home still does not force them here.
            return true;
        }
        return account_persona::is_fellowship($userid);
    }

    /**
     * Two-letter initials for the avatar chip.
     *
     * @param \stdClass $user
     */
    public static function initials(\stdClass $user): string {
        $a = self::first_letter((string) ($user->firstname ?? ''));
        $b = self::first_letter((string) ($user->lastname ?? ''));
        $out = $a . $b;
        return $out !== '' ? $out : '?';
    }

    /**
     * Uppercase first character without requiring Moodle bootstrap in smoke tests.
     */
    private static function first_letter(string $value): string {
        $value = trim($value);
        if ($value === '') {
            return '';
        }
        if (class_exists(\core_text::class, false)) {
            return \core_text::strtoupper(\core_text::substr($value, 0, 1));
        }
        if (function_exists('mb_substr') && function_exists('mb_strtoupper')) {
            return mb_strtoupper(mb_substr($value, 0, 1));
        }
        return strtoupper(substr($value, 0, 1));
    }
}

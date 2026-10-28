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

require_once($CFG->dirroot . '/course/lib.php');
require_once($CFG->dirroot . '/course/modlib.php');
require_once($CFG->libdir . '/enrollib.php');
require_once($CFG->libdir . '/resourcelib.php');
require_once($CFG->libdir . '/gradelib.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

/**
 * Creates LaunchPad categories, hidden courses, sections, and placeholders.
 *
 * Additive and idempotent by category/course/activity idnumber. Does not
 * modify unrelated existing courses. Existing LaunchPad objects keep admin
 * visibility / name / placement (see reconcile_policy); only creates are hidden.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class setup {
    /** @var bool */
    private bool $dryrun;

    /** @var string */
    private string $cohort;

    /** @var list<string> */
    private array $log = [];

    /**
     * @throws cohort_exception when --cohort / generated identifiers are invalid
     */
    public function __construct(string $cohort, bool $dryrun = true) {
        cohort::validate($cohort);
        $this->cohort = trim($cohort);
        $this->dryrun = $dryrun;
    }

    /**
     * @return list<string>
     */
    public function get_log(): array {
        return $this->log;
    }

    /**
     * Run structure creation / verification.
     */
    public function execute(): void {
        $this->line($this->dryrun ? 'DRY-RUN (no database writes)' : 'APPLY mode');
        $this->line('Cohort label: ' . $this->cohort);

        $root = $this->ensure_category(blueprint::ROOT_CATEGORY, blueprint::ROOT_IDNUMBER, 0);
        $categories = [blueprint::ROOT_IDNUMBER => $root];
        foreach (blueprint::track_categories() as $idnumber => $name) {
            $categories[$idnumber] = $this->ensure_category($name, $idnumber, $root->id);
        }

        // Pre-MVP blueprint created an Orientation category; do not delete it.
        $this->report_legacy_orientation_category();

        foreach (blueprint::courses($this->cohort) as $coursedef) {
            $cat = $categories[$coursedef['category_idnumber']];
            $course = $this->ensure_course($coursedef, $cat->id);
            // Four named phases apply to track courses only (MVP §4 / §10).
            if ($coursedef['kind'] === 'track') {
                $this->ensure_sections($course);
            } else {
                $this->line('  SECTIONS: orientation keeps general section only (no four-phase track outline)');
            }
            $this->ensure_manual_enrol($course);
            foreach (blueprint::activities_for($coursedef['kind']) as $act) {
                $this->ensure_activity($course, $act);
            }
        }

        $this->line('Done.');
    }

    /**
     * Run a DB-facing step; wrap unexpected errors with operation + target context.
     *
     * @template T
     * @param callable(): T $fn
     * @return T
     * @throws operation_failure
     */
    private function run_op(string $operation, string $target, callable $fn): mixed {
        try {
            return $fn();
        } catch (operation_failure $e) {
            throw $e;
        } catch (cohort_exception $e) {
            throw $e;
        } catch (\Throwable $e) {
            throw new operation_failure($operation, $target, $e);
        }
    }

    /**
     * Warn if a legacy Orientation category remains (not in MVP track list).
     * Additive CLI never deletes categories.
     */
    private function report_legacy_orientation_category(): void {
        global $DB;

        $legacy = $DB->get_record('course_categories', ['idnumber' => 'launchpad-orientation']);
        if (!$legacy) {
            return;
        }
        $this->line(
            'NOTE: legacy category still present [launchpad-orientation] id=' . $legacy->id
            . ' — MVP places Orientation as a course under the fellowship root; CLI will not delete this category'
        );
    }

    private function line(string $message): void {
        $this->log[] = $message;
        cli_writeln($message);
    }

    /**
     * @return \stdClass category record-like with ->id
     */
    private function ensure_category(string $name, string $idnumber, int $parentid): \stdClass {
        global $DB;

        $existing = $DB->get_record('course_categories', ['idnumber' => $idnumber]);
        if ($existing) {
            $this->line("CATEGORY exists: {$name} [{$idnumber}] id={$existing->id}");
            // Never force-hide on re-apply: category hide cascades to child cats/courses
            // (core_course_category) and would undo deliberate MVP "show ready" releases.
            if (!reconcile_policy::may_force_hide_existing() && (int) $existing->visible !== 0) {
                $cascade = reconcile_policy::category_hide_cascades_to_courses()
                    ? '; category hide would cascade to courses' : '';
                $this->line(
                    "  CATEGORY visibility preserved: [{$idnumber}] visible="
                    . $existing->visible . ' (not auto-hidden' . $cascade . ')'
                );
            }
            if (!reconcile_policy::may_move_existing()
                    && (int) $existing->parent !== (int) $parentid
                    && $parentid >= 0) {
                $this->line(
                    "  CATEGORY parent drift (not moved): [{$idnumber}] parent="
                    . $existing->parent . " blueprint_parent={$parentid}"
                );
            }
            if (!reconcile_policy::may_rename_existing() && $existing->name !== $name) {
                $this->line(
                    "  CATEGORY name drift (not renamed): [{$idnumber}] '{$existing->name}' vs blueprint '{$name}'"
                );
            }
            return $existing;
        }

        $this->line("CATEGORY create (hidden): {$name} [{$idnumber}] parent={$parentid}");
        if ($this->dryrun) {
            return (object) ['id' => 0, 'idnumber' => $idnumber, 'name' => $name, 'visible' => 0];
        }

        return $this->run_op('create_category', $idnumber, function () use ($DB, $name, $idnumber, $parentid) {
            $created = \core_course_category::create((object) [
                'name' => $name,
                'idnumber' => $idnumber,
                'parent' => $parentid,
                'visible' => 0,
                'description' => 'LaunchPad structure — managed by local_launchpad CLI.',
                'descriptionformat' => FORMAT_PLAIN,
            ]);
            return $DB->get_record('course_categories', ['id' => $created->id], '*', MUST_EXIST);
        });
    }

    /**
     * @param array $coursedef
     */
    private function ensure_course(array $coursedef, int $categoryid): \stdClass {
        global $DB;

        $existing = $DB->get_record('course', ['idnumber' => $coursedef['idnumber']]);
        if ($existing) {
            $this->line("COURSE exists: {$coursedef['fullname']} [{$coursedef['idnumber']}] id={$existing->id}");
            // Preserve deliberate visibility (MVP show/hide of ready courses).
            if (!reconcile_policy::may_force_hide_existing() && (int) $existing->visible !== 0) {
                $this->line(
                    "  COURSE visibility preserved: [{$coursedef['idnumber']}] visible="
                    . $existing->visible . ' (not auto-hidden)'
                );
            }
            if (!reconcile_policy::may_move_existing()
                    && $categoryid > 0
                    && (int) $existing->category !== (int) $categoryid) {
                $this->line(
                    "  COURSE category drift (not moved): [{$coursedef['idnumber']}] category="
                    . $existing->category . " blueprint_category={$categoryid}"
                );
            }
            if (!reconcile_policy::may_rename_existing()
                    && $existing->fullname !== $coursedef['fullname']) {
                $this->line(
                    "  COURSE name drift (not renamed): [{$coursedef['idnumber']}] "
                    . "'{$existing->fullname}' vs blueprint '{$coursedef['fullname']}'"
                );
            }
            return $existing;
        }

        // Dry-run may have category id 0 — still report intent.
        $this->line("COURSE create (hidden): {$coursedef['fullname']} [{$coursedef['idnumber']}]");
        if ($this->dryrun) {
            return (object) [
                'id' => 0,
                'idnumber' => $coursedef['idnumber'],
                'shortname' => $coursedef['shortname'],
                'fullname' => $coursedef['fullname'],
            ];
        }

        $numsections = ($coursedef['kind'] === 'track') ? 4 : 0;
        $data = (object) [
            'category' => $categoryid,
            'fullname' => $coursedef['fullname'],
            'shortname' => $coursedef['shortname'],
            'idnumber' => $coursedef['idnumber'],
            'summary' => $coursedef['summary'],
            'summaryformat' => FORMAT_PLAIN,
            'format' => 'topics',
            'numsections' => $numsections,
            'startdate' => time(),
            'visible' => 0,
            'newsitems' => 0,
            'showgrades' => 1,
            'enablecompletion' => 1,
        ];

        return $this->run_op('create_course', $coursedef['idnumber'], static function () use ($data) {
            return create_course($data);
        });
    }

    private function ensure_sections(\stdClass $course): void {
        if ($this->dryrun || empty($course->id)) {
            foreach (blueprint::phase_sections() as $num => $name) {
                $this->line("  SECTION {$num}: {$name}");
            }
            return;
        }

        $this->run_op('ensure_sections', $course->idnumber, function () use ($course) {
            course_create_sections_if_missing($course->id, range(0, 4));
            $modinfo = get_fast_modinfo($course->id);
            foreach (blueprint::phase_sections() as $num => $name) {
                $section = $modinfo->get_section_info($num);
                if ($section->name !== $name) {
                    course_update_section($course, $section, ['name' => $name]);
                    $this->line("  SECTION {$num} named: {$name}");
                } else {
                    $this->line("  SECTION {$num} ok: {$name}");
                }
            }
        });
    }

    /**
     * Ensure a manual enrolment instance exists and is enabled.
     * Other enabled methods are reported and left unchanged.
     */
    private function ensure_manual_enrol(\stdClass $course): void {
        if (empty($course->id)) {
            $this->line('  ENROL: would ensure manual instance on create');
            return;
        }

        $instances = enrol_get_instances($course->id, false);
        $rows = [];
        foreach ($instances as $instance) {
            $rows[] = [
                'enrol' => (string) $instance->enrol,
                'status' => (int) $instance->status,
            ];
        }
        $plan = enrol_plan::analyse($rows);

        if ($this->dryrun) {
            foreach ($plan['lines'] as $msg) {
                $this->line('  ENROL: ' . $msg . ' (dry-run)');
            }
            return;
        }

        $this->run_op('ensure_manual_enrol', $course->idnumber, function () use ($course, $plan, $instances) {
            if ($plan['create_manual']) {
                $plugin = enrol_get_plugin('manual');
                if (!$plugin) {
                    throw new \RuntimeException('enrol_manual plugin is not available');
                }
                $plugin->add_default_instance($course);
                $this->line('  ENROL: created manual instance');
            } else if ($plan['enable_manual']) {
                foreach ($instances as $instance) {
                    if ($instance->enrol !== 'manual') {
                        continue;
                    }
                    $plugin = enrol_get_plugin('manual');
                    if (!$plugin) {
                        throw new \RuntimeException('enrol_manual plugin is not available');
                    }
                    $plugin->update_status($instance, ENROL_INSTANCE_ENABLED);
                    $this->line('  ENROL: enabled manual instance');
                    break;
                }
            } else {
                $this->line('  ENROL: manual already enabled');
            }

            foreach ($plan['other_enabled'] as $enrolname) {
                $this->line('  ENROL NOTE: other enabled method left unchanged: ' . $enrolname);
            }
        });
    }

    /**
     * @param array{section:int, modulename:string, name:string, idnumber:string} $act
     */
    private function ensure_activity(\stdClass $course, array $act): void {
        global $DB;

        $cmidnumber = $course->idnumber . '-' . $act['idnumber'];

        if (!empty($course->id)) {
            $existing = $DB->get_record('course_modules', [
                'course' => $course->id,
                'idnumber' => $cmidnumber,
            ], 'id');
            if ($existing) {
                $this->line("  ACTIVITY exists: {$act['name']} [{$cmidnumber}]");
                return;
            }
        }

        if ($this->dryrun || empty($course->id)) {
            $this->line("  ACTIVITY create {$act['modulename']} s{$act['section']}: {$act['name']} [{$cmidnumber}]");
            return;
        }

        $moduleinfo = $this->build_moduleinfo($course, $act, $cmidnumber);
        $target = $course->idnumber . ' / ' . $act['modulename'] . ' / ' . $cmidnumber;
        $this->run_op('create_activity', $target, static function () use ($moduleinfo) {
            create_module($moduleinfo);
        });
        $this->line("  ACTIVITY created (hidden): {$act['name']} [{$cmidnumber}]");
    }

    /**
     * @param array{section:int, modulename:string, name:string, idnumber:string} $act
     */
    private function build_moduleinfo(\stdClass $course, array $act, string $cmidnumber): \stdClass {
        $moduleinfo = new \stdClass();
        $moduleinfo->modulename = $act['modulename'];
        $moduleinfo->course = $course->id;
        $moduleinfo->section = $act['section'];
        $moduleinfo->visible = 0; // Hidden placeholders until content approved.
        $moduleinfo->visibleoncoursepage = 1;
        $moduleinfo->cmidnumber = $cmidnumber;
        $moduleinfo->name = $act['name'];
        $moduleinfo->introeditor = [
            'text' => '<p>Placeholder only. Curriculum content is not loaded by this tool.</p>',
            'format' => FORMAT_HTML,
            'itemid' => 0,
        ];
        $moduleinfo->completion = COMPLETION_TRACKING_NONE;

        switch ($act['modulename']) {
            case 'page':
                $moduleinfo->content = '<p>Placeholder page. Replace after lesson approval.</p>';
                $moduleinfo->contentformat = FORMAT_HTML;
                $moduleinfo->display = RESOURCELIB_DISPLAY_OPEN;
                $moduleinfo->printintro = 0;
                $moduleinfo->printlastmodified = 0;
                break;
            case 'quiz':
                $moduleinfo->timeopen = 0;
                $moduleinfo->timeclose = 0;
                $moduleinfo->timelimit = 0;
                $moduleinfo->overduehandling = 'autosubmit';
                $moduleinfo->graceperiod = 0;
                $moduleinfo->preferredbehaviour = 'deferredfeedback';
                $moduleinfo->canredoquestions = 0;
                $moduleinfo->attempts = 0;
                $moduleinfo->attemptonlast = 0;
                $moduleinfo->grademethod = QUIZ_GRADEHIGHEST;
                $moduleinfo->decimalpoints = 2;
                $moduleinfo->questiondecimalpoints = -1;
                $moduleinfo->reviewattempt = 0;
                $moduleinfo->reviewcorrectness = 0;
                $moduleinfo->reviewmaxmarks = 0;
                $moduleinfo->reviewmarks = 0;
                $moduleinfo->reviewspecificfeedback = 0;
                $moduleinfo->reviewgeneralfeedback = 0;
                $moduleinfo->reviewrightanswer = 0;
                $moduleinfo->reviewoverallfeedback = 0;
                $moduleinfo->questionsperpage = 1;
                $moduleinfo->navmethod = 'free';
                $moduleinfo->shuffleanswers = 1;
                $moduleinfo->sumgrades = 0;
                $moduleinfo->grade = 100;
                $moduleinfo->timecreated = time();
                $moduleinfo->timemodified = time();
                // Form field name is quizpassword; quiz_process_options copies it to password.
                $moduleinfo->quizpassword = '';
                $moduleinfo->subnet = '';
                $moduleinfo->browsersecurity = '-';
                $moduleinfo->delay1 = 0;
                $moduleinfo->delay2 = 0;
                $moduleinfo->showuserpicture = 0;
                $moduleinfo->showblocks = 0;
                $moduleinfo->completionattemptsexhausted = 0;
                $moduleinfo->completionminattempts = 0;
                $moduleinfo->allowofflineattempts = 0;
                break;
            case 'assign':
                $moduleinfo->alwaysshowdescription = 1;
                $moduleinfo->submissiondrafts = 1;
                $moduleinfo->requiresubmissionstatement = 0;
                $moduleinfo->sendnotifications = 0;
                $moduleinfo->sendlatenotifications = 0;
                $moduleinfo->sendstudentnotifications = 1;
                $moduleinfo->duedate = 0;
                $moduleinfo->cutoffdate = 0;
                $moduleinfo->gradingduedate = 0;
                $moduleinfo->allowsubmissionsfromdate = 0;
                $moduleinfo->grade = 100;
                $moduleinfo->teamsubmission = 0;
                $moduleinfo->requireallteammemberssubmit = 0;
                $moduleinfo->teamsubmissiongroupingid = 0;
                $moduleinfo->blindmarking = 0;
                $moduleinfo->hidegrader = 0;
                $moduleinfo->attemptreopenmethod = 'untilpass';
                $moduleinfo->maxattempts = 1;
                $moduleinfo->markingworkflow = 0;
                $moduleinfo->markingallocation = 0;
                $moduleinfo->markinganonymous = 0;
                $moduleinfo->preventsubmissionnotingroup = 0;
                $moduleinfo->timelimit = 0;
                $moduleinfo->submissionattachments = 0;
                $moduleinfo->gradepenalty = 0;
                $moduleinfo->assignsubmission_onlinetext_enabled = 1;
                $moduleinfo->assignsubmission_file_enabled = 1;
                $moduleinfo->assignsubmission_file_maxfiles = 20;
                $moduleinfo->assignsubmission_file_maxsizebytes = 0;
                break;
            default:
                throw new \coding_exception('Unsupported module: ' . $act['modulename']);
        }

        return $moduleinfo;
    }
}

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

namespace local_videowatch;

/**
 * Watched-time rule for an uploaded video.
 *
 * Opening the activity used to call set_module_viewed() before the file
 * played, so closing the page still counted as complete. Progress is accepted
 * only from the start of the file, and only about as fast as the clock.
 * A jump to the end is clamped back to the watched position.
 *
 * @package    local_videowatch
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class policy {
    /** Fraction of the file that must be watched. */
    public const THRESHOLD = 0.9;

    /** First report may cover at most this much, and never half of the requirement. */
    private const FIRST_CREDIT_MS = 1000;

    /** One report cannot bank more than this, even after a long idle gap. */
    private const MAX_CREDIT_MS = 2500;

    /** Reports closer together than this do not move the watched position. */
    private const MIN_GAP_MS = 250;

    /**
     * True when this activity is a video whose completion must wait for playback.
     *
     * @param \stdClass|\cm_info $cm
     * @param \context $context
     */
    public static function defers_view_completion($cm, $context): bool {
        if (self::skip()) {
            return false;
        }
        $file = self::main_file((int) $context->id);
        if (!$file || !self::is_video($file->get_mimetype())) {
            return false;
        }
        if ((int) $cm->completion === COMPLETION_TRACKING_NONE) {
            return false;
        }
        self::lock_rules($cm);
        return true;
    }

    /**
     * True when the video must stay on the activity page instead of the raw file.
     *
     * @param \stdClass|\cm_info $cm
     * @param \stored_file $file
     */
    public static function must_play_in_page($cm, \stored_file $file): bool {
        if (self::skip()) {
            return false;
        }
        if (!self::is_video($file->get_mimetype())) {
            return false;
        }
        return (int) $cm->completion !== COMPLETION_TRACKING_NONE;
    }

    /**
     * Apply one playback report for the current user.
     *
     * @return array{ok:bool,frontier:float,duration:float,required:float,completed:bool,error?:string}
     */
    public static function report(int $cmid, float $position): array {
        global $DB, $USER;

        $failed = static function (string $error): array {
            return [
                'ok' => false,
                'frontier' => 0.0,
                'duration' => 0.0,
                'required' => 0.0,
                'completed' => false,
                'error' => $error,
            ];
        };

        if (self::skip() || !isloggedin() || isguestuser()) {
            return $failed('unavailable');
        }

        $cm = get_coursemodule_from_id('resource', $cmid, 0, false, IGNORE_MISSING);
        if (!$cm) {
            return $failed('missing');
        }
        $context = \context_module::instance($cm->id);
        $file = self::main_file((int) $context->id);
        if (!$file || !self::is_video($file->get_mimetype())) {
            return $failed('not-video');
        }
        if ((int) $cm->completion === COMPLETION_TRACKING_NONE) {
            return $failed('completion-off');
        }
        self::lock_rules($cm);
        require_capability('mod/resource:view', $context);

        $seconds = duration::from_file($file);
        if ($seconds === null || $seconds <= 0) {
            return $failed('duration-unknown');
        }

        $durationms = (int) round($seconds * 1000);
        $requiredms = (int) floor($durationms * self::THRESHOLD);
        $reported = (int) round(max(0, $position) * 1000);
        if ($reported > $durationms) {
            $reported = $durationms;
        }

        $now = (int) round(microtime(true) * 1000);
        $row = $DB->get_record('local_videowatch', ['userid' => $USER->id, 'cmid' => $cm->id]);
        if (!$row) {
            $row = (object) [
                'userid' => $USER->id,
                'cmid' => $cm->id,
                'contenthash' => $file->get_contenthash(),
                'duration' => $durationms,
                'frontier' => 0,
                'startedat' => 0,
                'lastping' => 0,
            ];
            $row->id = $DB->insert_record('local_videowatch', $row);
        } else if ($row->contenthash !== $file->get_contenthash() || (int) $row->duration !== $durationms) {
            $row->contenthash = $file->get_contenthash();
            $row->duration = $durationms;
            $row->frontier = 0;
            $row->startedat = 0;
            $row->lastping = 0;
        }

        $first = (int) $row->startedat === 0;
        $elapsed = $first ? 0 : max(0, $now - (int) $row->lastping);
        $frontier = self::advance((int) $row->frontier, $reported, $elapsed, $first, $requiredms);
        if ($frontier > (int) $row->frontier) {
            if ($first) {
                $row->startedat = $now;
            }
            $row->lastping = $now;
            $row->frontier = $frontier;
            $DB->update_record('local_videowatch', $row);
        }

        $completed = (int) $row->frontier >= $requiredms && $requiredms > 0;
        if ($completed) {
            $course = get_course($cm->course);
            $fresh = get_coursemodule_from_id('resource', $cm->id, 0, false, MUST_EXIST);
            $completion = new \completion_info($course);
            if ($completion->is_enabled($fresh)) {
                $completion->set_module_viewed($fresh);
            }
        }

        return [
            'ok' => true,
            'frontier' => ((int) $row->frontier) / 1000,
            'duration' => $durationms / 1000,
            'required' => $requiredms / 1000,
            'completed' => $completed,
        ];
    }

    /**
     * Next watched position. A report cannot jump past the clock.
     */
    public static function advance(int $frontier, int $reported, int $elapsedms, bool $first, int $required): int {
        if ($reported <= $frontier) {
            return $frontier;
        }
        if ($first) {
            $credit = min(self::FIRST_CREDIT_MS, intdiv(max(0, $required), 2));
        } else if ($elapsedms < self::MIN_GAP_MS) {
            return $frontier;
        } else {
            $credit = min(self::MAX_CREDIT_MS, $elapsedms);
        }
        return (int) min($reported, $frontier + max(0, $credit));
    }

    /**
     * Watched seconds already stored for this user, used to resume the player.
     */
    public static function frontier_seconds(int $cmid, int $userid): float {
        global $DB;
        $frontier = $DB->get_field('local_videowatch', 'frontier', ['cmid' => $cmid, 'userid' => $userid]);
        if ($frontier === false) {
            return 0.0;
        }
        return ((int) $frontier) / 1000;
    }

    /**
     * @param \stdClass|\cm_info $cm
     */
    private static function lock_rules($cm): void {
        global $DB;
        $automatic = (int) $cm->completion === COMPLETION_TRACKING_AUTOMATIC;
        $view = (int) $cm->completionview === COMPLETION_VIEW_REQUIRED;
        if ($automatic && $view) {
            return;
        }
        $DB->set_field('course_modules', 'completion', COMPLETION_TRACKING_AUTOMATIC, ['id' => $cm->id]);
        $DB->set_field('course_modules', 'completionview', COMPLETION_VIEW_REQUIRED, ['id' => $cm->id]);
        $cm->completion = COMPLETION_TRACKING_AUTOMATIC;
        $cm->completionview = COMPLETION_VIEW_REQUIRED;
        if (!empty($cm->course)) {
            rebuild_course_cache((int) $cm->course, true);
        }
    }

    private static function main_file(int $contextid): ?\stored_file {
        $fs = get_file_storage();
        $files = $fs->get_area_files($contextid, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);
        if (!$files) {
            return null;
        }
        $file = reset($files);
        return $file instanceof \stored_file ? $file : null;
    }

    private static function is_video(string $mimetype): bool {
        return str_starts_with($mimetype, 'video/');
    }

    /**
     * Moodle's own test sites keep the upstream "complete on view" behaviour.
     */
    private static function skip(): bool {
        if (during_initial_install()) {
            return true;
        }
        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
            return true;
        }
        if (defined('BEHAT_SITE_RUNNING') && BEHAT_SITE_RUNNING) {
            return true;
        }
        return false;
    }
}

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
 * Contains class mod_h5pactivity\output\reportlink
 *
 * @package   mod_h5pactivity
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_h5pactivity\output;

defined('MOODLE_INTERNAL') || die();

use mod_h5pactivity\local\attempt as activity_attempt;
use renderable;
use templatable;
use renderer_base;
use moodle_url;
use user_picture;
use stdClass;

/**
 * Class to help display report link in mod_h5pactivity.
 *
 * @copyright 2020 Ferran Recio
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class attempt implements renderable, templatable {

    /** @var activity_attempt attempt */
    public $attempt;

    /** @var stdClass user record */
    public $user;

    /** @var int courseid necesary to present user picture */
    public $courseid;

    /**
     * Constructor.
     *
     * @param activity_attempt $attempt the attempt object
     * @param stdClass $user a user record (default null).
     * @param int $courseid optional course id (default null).
     */
    public function __construct(activity_attempt $attempt, ?stdClass $user = null, ?int $courseid = null) {
        $this->attempt = $attempt;
        $this->user = $user;
        $this->courseid = $courseid;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param renderer_base $output
     * @return stdClass
     */
    public function export_for_template(renderer_base $output) {
        $attempt = $this->attempt;

        $data = (object)[
            'id' => $attempt->get_id(),
            'h5pactivityid' => $attempt->get_h5pactivityid(),
            'userid' => $attempt->get_userid(),
            'timecreated' => $attempt->get_timecreated(),
            'timemodified' => $attempt->get_timemodified(),
            'attempt' => $attempt->get_attempt(),
            'rawscore' => $attempt->get_rawscore(),
            'maxscore' => $attempt->get_maxscore(),
            'duration' => '-',
            'durationcompact' => '-',
            'completion' => $attempt->get_completion(),
            'completionicon' => $this->completion_icon($output, $attempt->get_completion()),
            'completiontext' => $this->completion_icon($output, $attempt->get_completion(), true),
            'success' => $attempt->get_success(),
            'successicon' => $this->success_icon($output, $attempt->get_success()),
            'successtext' => $this->success_icon($output, $attempt->get_success(), true),
            'scaled' => $attempt->get_scaled(),
            'reporturl' => new moodle_url('/mod/h5pactivity/report.php', [
                'a' => $attempt->get_h5pactivityid(), 'attemptid' => $attempt->get_id()
            ]),
        ];
        if ($attempt->get_duration() !== null) {
            $data->durationvalue = $attempt->get_duration();
            $duration = $this->extract_duration($data->durationvalue);
            $data->duration = $this->format_duration($duration);
            $data->durationcompact = $this->format_duration_short($duration);
        }

        if (!empty($data->maxscore)) {
            $data->score = get_string('score_out_of', 'mod_h5pactivity', $data);
        }
        if ($this->user) {
            $data->user = $this->user;
            $userpicture = new user_picture($this->user);
            $userpicture->courseid = $this->courseid;
            $data->user->picture = $output->render($userpicture);
            $data->user->fullname = fullname($this->user);
        }
        return $data;
    }

    /**
     * Return a completion icon HTML.
     *
     * @param renderer_base $output the renderer base object
     * @param int|null $completion the current completion value
     * @param bool $showtext if the icon must have a text or only icon
     * @return string icon HTML
     */
    private function completion_icon(renderer_base $output, ?int $completion = null, bool $showtext = false): string {
        if ($completion === null) {
            return '';
        }
        if ($completion) {
            $alt = get_string('attempt_completion_yes', 'mod_h5pactivity');
            $icon = 'i/completion-auto-y';
        } else {
            $alt = get_string('attempt_completion_no', 'mod_h5pactivity');
            $icon = 'i/completion-auto-n';
        }
        $text = '';
        if ($showtext) {
            $text = $alt;
            $alt = '';
        }
        return $output->pix_icon($icon, $alt).$text;
    }

    /**
     * Return a success icon
     * @param renderer_base $output the renderer base object
     * @param int|null $success the current success value
     * @param bool $showtext if the icon must have a text or only icon
     * @return string icon HTML
     */
    private function success_icon(renderer_base $output, ?int $success = null, bool $showtext = false): string {
        if ($success === null) {
            $alt = get_string('attempt_success_unknown', 'mod_h5pactivity');
            if ($showtext) {
                return $alt;
            }
            $icon = 'i/empty';
        } else if ($success) {
            $alt = get_string('attempt_success_pass', 'mod_h5pactivity');
            $icon = 'i/checkedcircle';
        } else {
            $alt = get_string('attempt_success_fail', 'mod_h5pactivity');
            $icon = 'i/uncheckedcircle';
        }
        $text = '';
        if ($showtext) {
            $text = $alt;
            $alt = '';
        }
        return $output->pix_icon($icon, $alt).$text;
    }

    /**
     * Return the duration in long format (localized)
     *
     * @param stdClass $duration object with (h)hours, (m)minutes and (s)seconds
     * @return string the long format duration
     */
    private function format_duration(stdClass $duration): string {
        $result = [];
        if ($duration->h) {
            $result[] = get_string('numhours', 'moodle', $duration->h);
        }
        if ($duration->m) {
            $result[] = get_string('numminutes', 'moodle', $duration->m);
        }
        if ($duration->s) {
            $result[] = get_string('numseconds', 'moodle', $duration->s);
        }
        return implode(' ', $result);
    }

    /**
     * Return the duration en short format (for example: 145' 43'')
     *
     * Note: this method is used to make duration responsive.
     *
     * @param stdClass $duration object with (h)hours, (m)minutes and (s)seconds
     * @return string the short format duration
     */
    private function format_duration_short(stdClass $duration): string {
        $result = [];
        if ($duration->h || $duration->m) {
            $result[] = ($duration->h * 60 + $duration->m)."'";
        }
        if ($duration->s) {
            $result[] = $duration->s."''";
        }
        return implode(' ', $result);
    }

    /**
     * Extract hours and minutes from second duration.
     *
     * Note: this function is used to generate the param for format_duration
     * and format_duration_short
     *
     * @param int $seconds number of second
     * @return stdClass with (h)hours, (m)minutes and (s)seconds
     */
    private function extract_duration(int $seconds): stdClass {
        $h = floor($seconds / 3600);
        $m = floor(($seconds - $h * 3600) / 60);
        $s = $seconds - ($h * 3600 + $m * 60);
        return (object)['h' => $h, 'm' => $m, 's' => $s];
    }
}

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
 * Adds the playback guard to a video activity page.
 *
 * @package    local_videowatch
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class hook_callbacks {
    /**
     * @param \core\hook\output\before_footer_html_generation $hook
     */
    public static function before_footer(\core\hook\output\before_footer_html_generation $hook): void {
        global $PAGE, $USER;

        $cm = $PAGE->cm ?? null;
        if (empty($cm) && $PAGE->context && (int) $PAGE->context->contextlevel === CONTEXT_MODULE) {
            $cm = get_coursemodule_from_id('resource', (int) $PAGE->context->instanceid, 0, false, IGNORE_MISSING);
        }
        if (!$cm || ($cm->modname ?? '') !== 'resource') {
            return;
        }
        $path = $PAGE->url ? $PAGE->url->get_path() : '';
        if (!str_contains($path, '/mod/resource/view.php')) {
            return;
        }
        $context = $PAGE->context;
        if (!$context || !policy::defers_view_completion($cm, $context)) {
            return;
        }

        $frontier = policy::frontier_seconds((int) $cm->id, (int) $USER->id);
        $url = (new \moodle_url('/local/videowatch/guard.js'))->out(false);
        $progress = (new \moodle_url('/local/videowatch/progress.php'))->out(false);
        $hook->add_html(
            \html_writer::div(get_string('watchnotice', 'local_videowatch'), 'local-videowatch-note alert alert-info')
            . \html_writer::div('', 'local-videowatch', [
                'id' => 'local-videowatch',
                'data-cmid' => (int) $cm->id,
                'data-sesskey' => sesskey(),
                'data-url' => $progress,
                'data-frontier' => $frontier,
            ])
            . \html_writer::tag('script', '', ['src' => $url])
        );
    }
}

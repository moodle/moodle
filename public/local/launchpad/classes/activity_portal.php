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
 * Employer portal activity viewer — Moodle CM data in LaunchPad UI.
 *
 * Page / URL / resource render inside the portal shell. Interactive modules
 * (quiz, assign, …) keep Moodle engines but open from this consistent chrome.
 *
 * @package    local_launchpad
 * @copyright  2026 IntelliVerse-X
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
final class activity_portal {

    /** Mods rendered fully inside the Employer shell. */
    private const PORTAL_RENDER = ['page', 'url', 'resource'];

    /**
     * Portal activity URL for a course module (Employer shell).
     */
    public static function url(int $cmid): string {
        return (new \moodle_url('/local/launchpad/activity.php', ['id' => $cmid]))->out(false);
    }

    /**
     * Whether this modname is rendered inside the portal (not Moodle chrome).
     */
    public static function is_portal_renderable(string $modname): bool {
        return in_array($modname, self::PORTAL_RENDER, true);
    }

    /**
     * Rewrite a workspace activity row URL to the portal activity page.
     *
     * @param array<string,mixed> $activity
     * @return array<string,mixed>
     */
    public static function rewrite_row(array $activity): array {
        $cmid = (int) ($activity['id'] ?? 0);
        if ($cmid > 0) {
            $activity['url'] = self::url($cmid);
            $activity['portal'] = true;
        }
        return $activity;
    }

    /**
     * Build Mustache context for one CM inside the Employer shell.
     *
     * @param \stdClass $user
     * @param int $cmid
     * @return array<string,mixed>
     */
    public static function export(\stdClass $user, int $cmid): array {
        global $CFG, $DB;

        require_once($CFG->libdir . '/completionlib.php');
        require_once($CFG->libdir . '/filelib.php');

        $cmrecord = get_coursemodule_from_id('', $cmid, 0, false, MUST_EXIST);
        $course = get_course((int) $cmrecord->course);
        require_course_login($course, true, $cmrecord);

        $modinfo = get_fast_modinfo($course, (int) $user->id);
        $cm = $modinfo->get_cm($cmid);
        if (!$cm->uservisible) {
            throw new \moodle_exception('activityiscurrentlyhidden');
        }

        $modname = $cm->modname;
        $context = \context_module::instance($cm->id);
        $courseurl = (new \moodle_url('/local/launchpad/employee.php', [
            'view' => 'course',
            'id' => (int) $course->id,
        ]))->out(false);
        $moodleurl = $cm->url
            ? $cm->url->out(false)
            : (new \moodle_url('/mod/' . $modname . '/view.php', ['id' => $cm->id]))->out(false);

        $completion = new \completion_info($course);
        $tracked = false;
        $complete = false;
        if ($completion->is_enabled($cm) != COMPLETION_TRACKING_NONE) {
            $tracked = true;
            $data = $completion->get_data($cm, false, (int) $user->id);
            $complete = ((int) $data->completionstate === COMPLETION_COMPLETE
                || (int) $data->completionstate === COMPLETION_COMPLETE_PASS);
        }

        $typename = method_exists($cm, 'get_module_type_name')
            ? (string) $cm->get_module_type_name()
            : get_string('modulename', $modname);
        $title = $cm->get_formatted_name();
        // Figma card: primary title + grey subtitle in brackets.
        $titleplain = trim(html_entity_decode(strip_tags($title), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
        $titlemain = $titleplain;
        $titlesub = '';
        if (preg_match('/^(.*?)\s*(\[[^\]]+\])\s*$/u', $titleplain, $m)) {
            $titlemain = trim($m[1]);
            $titlesub = trim($m[2]);
        }

        $bodyhtml = '';
        $introhtml = '';
        $hasintro = false;
        $externalurl = '';
        $hasexternal = false;
        $files = [];
        $hasfiles = false;
        $isinportal = self::is_portal_renderable($modname);
        $needsengine = !$isinportal;
        $callouttext = '';

        if ($modname === 'page') {
            require_capability('mod/page:view', $context);
            require_once($CFG->dirroot . '/mod/page/lib.php');
            $page = $DB->get_record('page', ['id' => $cm->instance], '*', MUST_EXIST);
            // Same completion / events as mod/page/view.php.
            page_view($page, $course, $cm, $context);

            $content = file_rewrite_pluginfile_urls(
                $page->content,
                'pluginfile.php',
                $context->id,
                'mod_page',
                'content',
                $page->revision
            );
            $formatoptions = new \stdClass();
            $formatoptions->noclean = true;
            $formatoptions->overflowdiv = true;
            $formatoptions->context = $context;
            $bodyhtml = format_text($content, (int) $page->contentformat, $formatoptions);
            $callouttext = trim(html_entity_decode(strip_tags($bodyhtml), ENT_QUOTES | ENT_HTML5, 'UTF-8'));

            if (!empty($page->intro)) {
                $introhtml = format_module_intro('page', $page, $cm->id);
                $hasintro = trim(strip_tags($introhtml)) !== '';
            }
        } else if ($modname === 'url') {
            require_capability('mod/url:view', $context);
            require_once($CFG->dirroot . '/mod/url/lib.php');
            $urlmod = $DB->get_record('url', ['id' => $cm->instance], '*', MUST_EXIST);
            if (function_exists('url_view')) {
                url_view($urlmod, $course, $cm, $context);
            } else {
                $completion->set_module_viewed($cm);
            }
            $externalurl = $urlmod->externalurl ?? '';
            $hasexternal = $externalurl !== '';
            if (!empty($urlmod->intro)) {
                $introhtml = format_module_intro('url', $urlmod, $cm->id);
                $hasintro = trim(strip_tags($introhtml)) !== '';
            }
        } else if ($modname === 'resource') {
            require_capability('mod/resource:view', $context);
            require_once($CFG->dirroot . '/mod/resource/lib.php');
            $resource = $DB->get_record('resource', ['id' => $cm->instance], '*', MUST_EXIST);
            if (function_exists('resource_view')) {
                resource_view($resource, $course, $cm, $context);
            } else {
                $completion->set_module_viewed($cm);
            }
            if (!empty($resource->intro)) {
                $introhtml = format_module_intro('resource', $resource, $cm->id);
                $hasintro = trim(strip_tags($introhtml)) !== '';
            }
            $fs = get_file_storage();
            $stored = $fs->get_area_files($context->id, 'mod_resource', 'content', 0, 'sortorder DESC, id ASC', false);
            foreach ($stored as $file) {
                if ($file->is_directory()) {
                    continue;
                }
                $files[] = [
                    'filename' => $file->get_filename(),
                    'url' => \moodle_url::make_pluginfile_url(
                        $context->id,
                        'mod_resource',
                        'content',
                        $file->get_itemid(),
                        $file->get_filepath(),
                        $file->get_filename()
                    )->out(false),
                    'size' => display_size($file->get_filesize()),
                ];
            }
            $hasfiles = $files !== [];
        } else {
            // Interactive Moodle engines — open native mod URL from this shell.
            $completion->set_module_viewed($cm);
            $needsengine = true;
            $isinportal = false;
        }

        if ($tracked && $complete) {
            $status = get_string('employer_activity_complete', 'local_launchpad');
            $statustone = 'ok';
        } else if ($tracked) {
            $status = get_string('employer_activity_incomplete', 'local_launchpad');
            $statustone = 'run';
        } else {
            $status = get_string('employer_activity_not_tracked', 'local_launchpad');
            $statustone = 'line';
        }

        $sectionname = '';
        $sectionnum = (int) $cm->sectionnum;
        if ($sectionnum > 0) {
            $sectionname = get_section_name($course, $sectionnum);
        }

        $hasbody = trim(strip_tags($bodyhtml)) !== '';
        $isplaceholder = $titlesub !== ''
            || ($callouttext !== '' && stripos($callouttext, 'placeholder') !== false)
            || !$hasbody;

        return [
            'is_activity' => true,
            'is_home' => false,
            'is_courses' => false,
            'is_course' => false,
            'pagetitle' => format_string($title),
            'activity' => [
                'cmid' => (int) $cm->id,
                'name' => format_string($title),
                'title_main' => s($titlemain !== '' ? $titlemain : $titleplain),
                'title_sub' => s($titlesub),
                'hastitlesub' => $titlesub !== '',
                'modname' => format_string($typename),
                'modtype' => $modname,
                'mod_icon' => self::mod_icon($modname),
                'courseid' => (int) $course->id,
                'coursename' => format_string($course->fullname),
                'courseurl' => $courseurl,
                'sectionname' => format_string($sectionname),
                'hassection' => $sectionname !== '',
                'section_anchor' => $sectionnum > 0 ? 'section-' . $sectionnum : '',
                'moodleurl' => $moodleurl,
                'status' => $status,
                'statustone' => $statustone,
                'tracked' => $tracked,
                'complete' => $complete,
                'bodyhtml' => $bodyhtml,
                'hasbody' => $hasbody && !$isplaceholder,
                'callouttext' => s($callouttext),
                'hascallout' => $isplaceholder && $callouttext !== '',
                'isplaceholder' => $isplaceholder,
                'introhtml' => $introhtml,
                'hasintro' => $hasintro && !$isplaceholder,
                'externalurl' => $externalurl,
                'hasexternal' => $hasexternal,
                'files' => $files,
                'hasfiles' => $hasfiles,
                'isinportal' => $isinportal,
                'needsengine' => $needsengine,
            ],
        ];
    }

    /**
     * Hugeicons name for activity type badge.
     */
    private static function mod_icon(string $modname): string {
        return match ($modname) {
            'page' => 'file-01',
            'url' => 'link-01',
            'resource' => 'file-02',
            'quiz' => 'help-circle',
            'assign' => 'task-01',
            'forum' => 'message-01',
            default => 'book-open-01',
        };
    }

    /**
     * Redirect Employer learners from stock mod/page (and friends) into the portal.
     */
    public static function maybe_redirect_from_mod(): void {
        global $PAGE, $USER;

        if (!isloggedin() || isguestuser()) {
            return;
        }
        if (during_initial_install() || CLI_SCRIPT || AJAX_SCRIPT || WS_SERVER) {
            return;
        }
        if (!account_persona::is_employee((int) $USER->id) && !is_siteadmin()) {
            return;
        }

        $path = $PAGE->url ? $PAGE->url->get_path() : '';
        if ($path === '') {
            $path = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH) ?? '');
        }
        if (!preg_match('#/mod/(page|url|resource)/view\.php$#', $path)) {
            return;
        }

        $cmid = optional_param('id', 0, PARAM_INT);
        if ($cmid <= 0) {
            // mod/page also accepts ?p={page instance id}.
            $pageid = optional_param('p', 0, PARAM_INT);
            if ($pageid > 0 && str_ends_with($path, '/mod/page/view.php')) {
                $cm = get_coursemodule_from_instance('page', $pageid, 0, false, IGNORE_MISSING);
                $cmid = $cm ? (int) $cm->id : 0;
            }
        }
        if ($cmid <= 0) {
            return;
        }

        redirect(new \moodle_url('/local/launchpad/activity.php', ['id' => $cmid]));
    }
}

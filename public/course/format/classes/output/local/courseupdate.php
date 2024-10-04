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

namespace core_courseformat\output\local;

use core_courseformat\base as course_format;
use core\output\renderer_base;
use core\output\single_button;
use core\url;
use stdClass;

/**
 * Support UIs for non-ajax course updates alternatives.
 *
 * This class is used from course/format/update.php to provide confirmation
 * dialogs for specific actions that require user confirmation.
 *
 * All protected methods has the same parameters as the core_courseformat\stateactions
 * even if they are not used for a specific action.
 *
 * @package    core_courseformat
 * @copyright  2024 Ferran Recio <ferran@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class courseupdate {
    use courseformat_named_templatable;

    /**
     * Constructor.
     *
     * @param course_format $format the course format class.
     * @param url $actionurl the current action url.
     * @param url $returnurl the return url if the user cancel the action.
     */
    public function __construct(
        /** @var course_format the course format class */
        protected course_format $format,
        /** @var url the current action url */
        protected url $actionurl,
        /** @var url the return url if the user cancel the action */
        protected url $returnurl,
    ) {
    }

    /**
     * Check if a specific action requires confirmation.
     *
     * Format plugins can override this method to provide confirmation
     * dialogs for specific actions.
     *
     * @param string $action the action name
     * @return bool
     */
    public function is_confirmation_required(
        string $action,
    ): bool {
        $methodname = $action . '_confirmation_dialog';
        return method_exists($this, $methodname);
    }

    /**
     * Get the confirmation dialog for a specific action.
     *
     * Format plugins can override this method to provide confirmation
     * dialogs for specific actions.
     *
     * @param renderer_base $output the course renderer
     * @param stdClass $course
     * @param string $action the state action name to execute
     * @param array $ids the section or cm ids.
     * @param int|null $targetsectionid the optional target section id
     * @param int|null $targetcmid the optional target cm id
     * @return string the HTML output
     */
    public function get_confirmation_dialog(
        renderer_base $output,
        stdClass $course,
        string $action,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null,
    ): string {
        $methodname = $action . '_confirmation_dialog';
        if (method_exists($this, $methodname)) {
            return $this->$methodname(
                output: $output,
                course: $course,
                ids: $ids,
                targetsectionid: $targetsectionid,
                targetcmid: $targetcmid,
            );
        }
        return '';
    }

    /**
     * Render the section delete confirmation dialog.
     *
     * @param renderer_base $output the course renderer
     * @param stdClass $course
     * @param array $ids the action ids.
     * @param int|null $targetsectionid the target section id (not used)
     * @param int|null $targetcmid the target cm id (not used)
     * @return string the HTML output
     */
    protected function section_delete_confirmation_dialog(
        renderer_base $output,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null,
    ): string {
        if (count($ids) == 1) {
            $modinfo = $this->format->get_modinfo();
            $section = $modinfo->get_section_info_by_id($ids[0]);
            $title = get_string('sectiondelete_title', 'core_courseformat');
            $message = get_string(
                'sectiondelete_info',
                'core_courseformat',
                ['name' => $this->format->get_section_name($section)]
            );
        } else {
            $title = get_string('sectionsdelete_title', 'core_courseformat');
            $message = get_string('sectionsdelete_info', 'core_courseformat', ['count' => count($ids)]);
        }

        return $output->confirm(
            message: $message,
            cancel: $this->returnurl,
            continue: new url($this->actionurl, ['confirm' => 1]),
            displayoptions: [
                'confirmtitle' => $title,
                'type' => single_button::BUTTON_DANGER,
                'continuestr' => get_string('delete'),
            ]
        );
    }

    /**
     * Render the cm delete confirmation dialog.
     *
     * @param renderer_base $output the course renderer
     * @param stdClass $course
     * @param array $ids the action ids.
     * @param int|null $targetsectionid the target section id (not used)
     * @param int|null $targetcmid the target cm id (not used)
     * @return string the HTML output
     */
    protected function cm_delete_confirmation_dialog(
        renderer_base $output,
        stdClass $course,
        array $ids = [],
        ?int $targetsectionid = null,
        ?int $targetcmid = null,
    ): string {

        if (count($ids) == 1) {
            $modinfo = $this->format->get_modinfo();
            $cm = $modinfo->get_cm($ids[0]);

            if ($cm->get_delegated_section_info()) {
                $title = get_string('cmdelete_subsectiontitle', 'core_courseformat');
                $meesagestr = 'sectiondelete_info';
            } else {
                $title = get_string('cmdelete_title', 'core_courseformat');
                $meesagestr = 'cmdelete_info';
            }

            $message = get_string(
                $meesagestr,
                'core_courseformat',
                (object) [
                    'type' => get_string('pluginname', 'mod_' . $cm->modname),
                    'name' => $cm->name,
                ],
            );
        } else {
            $title = get_string('cmsdelete_title', 'core_courseformat');
            $message = get_string('cmsdelete_info', 'core_courseformat', ['count' => count($ids)]);
        }

        return $output->confirm(
            message: $message,
            cancel: $this->returnurl,
            continue: new url($this->actionurl, ['confirm' => 1]),
            displayoptions: [
                'confirmtitle' => $title,
                'type' => single_button::BUTTON_DANGER,
                'continuestr' => get_string('delete'),
            ]
        );
    }
}

<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

namespace core_courseformat\output\local\linearnavigation;

use core\output\named_templatable;
use core\output\renderable;
use core\output\renderer_base;
use core\router\util;
use core_course\route\controller\course_navigation;
use cm_info;

/**
 * Sticky footer class for linear navigation in course format.
 *
 * @package    core_courseformat
 * @copyright  2025 Laurent David <laurent.david@moodle.com>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class footer_content implements named_templatable, renderable {
    /**
     * Constructor.
     *
     * @param cm_info $cminfo The course module information.
     * @param int|null $userid The user ID to use for the completion controls.
     */
    public function __construct(
        /** @var cm_info The course module information. */
        private cm_info $cminfo,
        /** @var int|null The user ID to use for the completion controls. */
        private ?int $userid = null,
    ) {
    }

    #[\Override]
    public function export_for_template(renderer_base $output) {
        $data = ['completion' => $this->export_completion($output)];
        $navigation = new course_navigation();
        $modinfo = $this->cminfo->get_modinfo();
        $section = $navigation->get_section($this->cminfo);
        $allsectioncms = $navigation->get_all_section_cms($modinfo, $section);

        $isfirst = $navigation->is_first_navigable($this->cminfo, $modinfo, $allsectioncms);
        $previousurl = util::get_path_for_callable(
            [course_navigation::class, 'cm_previous_element'],
            ['cm' => $this->cminfo->id],
        );
        if (!$isfirst) {
            $data['previousurl'] = $previousurl->out(false);
        }

        $islast = $navigation->is_last_navigable($this->cminfo, $modinfo, $allsectioncms);
        $nexturl = util::get_path_for_callable(
            [course_navigation::class, 'cm_next_element'],
            ['cm' => $this->cminfo->id],
        );
        if ($islast) {
            $data['backtocourseurl'] = $nexturl->out(false);
        } else {
            $data['nexturl'] = $nexturl->out(false);
        }

        return $data;
    }

    /**
     * Export the activity completion control to display in the footer.
     *
     * @param renderer_base $output The renderer.
     * @return string The rendered completion control, or an empty string when there is none to show.
     */
    private function export_completion(renderer_base $output): string {
        global $USER;

        $userid = $this->userid ?? $USER->id;
        $details = \core_completion\cm_completion_details::get_instance($this->cminfo, $userid);
        $data = (array) (new \core_course\output\activity_completion($this->cminfo, $details, smallbutton: true))
            ->export_for_template($output);
        if (empty($data['uservisible'])) {
            return '';
        }
        if (!empty($data['showmanualcompletion'])) {
            return $output->render_from_template('core_course/completion_manual', $data);
        } else if (!empty($data['hascompletion']) && !empty($data['isautomatic'])) {
            if (!$data['istrackeduser'] || !$data['overallcomplete']) {
                return ''; // In this case the template would be an empty span.
            }
            return $output->render_from_template('core_course/completion_status', $data);
        }
        return '';
    }

    #[\Override]
    public function get_template_name(\renderer_base $renderer): string {
        return 'core_courseformat/local/linearnavigation/footer_content';
    }
}

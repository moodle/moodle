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

namespace mod_quiz\output;

use moodle_url;
use templatable;
use renderable;

/**
 * Renderable class for the basic action bar in the quiz report pages.
 *
 * @package    mod_quiz
 * @copyright  2024 The Open University
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class quiz_basic_action_bar implements templatable, renderable {

    /** @var string $reportmode The quiz report mode. */
    protected string $reportmode;
    /** @var \context $context The context object. */
    protected $context;
    /** @var \cm_info $cm The cm object. */
    protected $cm;
    /** @var array The params of the report url. */
    protected $params;

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     * @param string $reportmode The quiz report type.
     * @param \cm_info $cm The cm object.
     * @param array $params The params of the report url.
     */
    public function __construct(\context $context, string $reportmode, \cm_info $cm, array $params) {
        $this->context = $context;
        $this->reportmode = $reportmode;
        $this->cm = $cm;
        $this->params = $params;
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     * @throws \moodle_exception
     */
    public function export_for_template(\renderer_base $output): array {
        global $USER, $PAGE;
        $cm = $this->cm;
        $course = $cm->get_course();
        // Get the data used to output the general navigation selector.
        $generalnavselector = new navigation_action_bar($this->context);
        $data = $generalnavselector->export_for_template($output);
        $grouprenderer = $PAGE->get_renderer('core_group');
        $groupbar = new \core_group\output\group_selector($course, $cm);
        $data['groupselector'] = $grouprenderer->render_group_bar($groupbar);

        if ($course->groupmode == VISIBLEGROUPS || has_capability('moodle/site:accessallgroups', $this->context)) {
            $allowedgroups = groups_get_all_groups($course->id, 0, $course->defaultgroupingid);
        } else {
            $allowedgroups = groups_get_all_groups($course->id, $USER->id, $course->defaultgroupingid);
        }

        if (groups_get_course_group($course, true, $allowedgroups)) {
            $resetparam = array_merge($this->params, [
                'group' => 0,
            ]);

            $reset = new moodle_url('/mod/quiz/report.php', $resetparam);
            $data['pagereset'] = $reset->out(false);
        }

        return $data;
    }
}

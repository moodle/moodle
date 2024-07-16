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
 * Output the grading actionbar for this activity.
 *
 * @package   mod_assign
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_assign\output;

use assign;
use context_module;
use templatable;
use renderable;
use moodle_url;

/**
 * Output the grading actionbar for this activity.
 *
 * @package   mod_assign
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grading_actionmenu implements templatable, renderable {

    /** @var int Course module ID. */
    protected $cmid;
    /** @var bool If any submission plugins are enabled. */
    protected $submissionpluginenabled;
    /** @var int The number of submissions made. */
    protected $submissioncount;


    /**
     * Constructor for this object.
     *
     * @param int $cmid Course module ID.
     * @param bool $submissionpluginenabled If any submission plugins are enabled.
     * @param int $submissioncount The number of submissions made.
     */
    public function __construct(int $cmid, bool $submissionpluginenabled = false, int $submissioncount = 0) {
        $this->cmid = $cmid;
        $this->submissionpluginenabled = $submissionpluginenabled;
        $this->submissioncount = $submissioncount;

    }

    /**
     * Data to render in a template.
     *
     * @param \renderer_base $output renderer base output.
     * @return array Data to render.
     */
    public function export_for_template(\renderer_base $output): array {
        global $PAGE;

        $course = $PAGE->course;
        $data = [];

        $context = context_module::instance($this->cmid);
        $assign = new assign($context, null, null);
        $assignid = $assign->get_instance()->id;

        if ($this->submissionpluginenabled && $this->submissioncount) {
            $data['downloadall'] = (
                new moodle_url('/mod/assign/view.php', ['id' => $this->cmid, 'action' => 'downloadall'])
            )->out(false);
        }

        $userid = optional_param('userid', null, PARAM_INT);
        // If the user ID is set, it indicates that a user has been selected. In this case, override the user search
        // string with the full name of the selected user.
        $usersearch = $userid ? fullname(\core_user::get_user($userid)) : optional_param('search', '', PARAM_NOTAGS);

        $actionbarrenderer = $PAGE->get_renderer('core_course', 'actionbar');
        $resetlink = new moodle_url('/mod/assign/view.php', ['id' => $this->cmid, 'action' => 'grading']);
        $groupid = groups_get_course_group($course, true);
        $userselector = new \core_course\output\actionbar\user_selector(
            course: $course,
            resetlink: $resetlink,
            userid: $userid,
            groupid: $groupid,
            usersearch: $usersearch,
            instanceid: $assignid
        );
        $data['userselector'] = $actionbarrenderer->render($userselector);

        if ($course->groupmode) {
            $data['groupselector'] = $actionbarrenderer->render(new \core_course\output\actionbar\group_selector($course));
        }

        if (groups_get_course_group($course)) {
            $reset = new moodle_url('/mod/assign/view.php', [
                'id' => $this->cmid,
                'action' => 'grading',
                'group' => 0,
            ]);
            $data['pagereset'] = $reset->out(false);
        }

        return $data;
    }
}

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
    protected int $cmid;
    /** @var bool If any submission plugins are enabled. */
    protected bool $submissionpluginenabled;
    /** @var int The number of submissions made. */
    protected int $submissioncount;
    /** @var assign The assign instance. */
    protected assign $assign;

    /**
     * Constructor for this object.
     *
     * @param int $cmid Course module ID.
     * @param null|bool $submissionpluginenabled This parameter has been deprecated since 4.5 and should not be used anymore.
     * @param null|int $submissioncount This parameter has been deprecated since 4.5 and should not be used anymore.
     * @param assign|null $assign The assign instance. If not provided, it will be loaded based on the cmid.
     */
    public function __construct(
        int $cmid,
        ?bool $submissionpluginenabled = null,
        ?int $submissioncount = null,
        assign $assign = null
    ) {
        $this->cmid = $cmid;
        if (!$assign) {
            $context = context_module::instance($cmid);
            $assign = new assign($context, null, null);
        }
        $this->assign = $assign;
    }

    /**
     * Data to render in a template.
     *
     * @param \renderer_base $output renderer base output.
     * @return array Data to render.
     */
    public function export_for_template(\renderer_base $output): array {
        global $PAGE;

        $course = $this->assign->get_course();
        $actionbarrenderer = $PAGE->get_renderer('core_course', 'actionbar');
        $data = [];

        $userid = optional_param('userid', null, PARAM_INT);
        // If the user ID is set, it indicates that a user has been selected. In this case, override the user search
        // string with the full name of the selected user.
        $usersearch = $userid ? fullname(\core_user::get_user($userid)) : optional_param('search', '', PARAM_NOTAGS);

        $resetlink = new moodle_url('/mod/assign/view.php', ['id' => $this->cmid, 'action' => 'grading']);
        $groupid = groups_get_course_group($course, true);
        $userselector = new \core_course\output\actionbar\user_selector(
            course: $course,
            resetlink: $resetlink,
            userid: $userid,
            groupid: $groupid,
            usersearch: $usersearch,
            instanceid: $this->assign->get_instance()->id
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

        $actions = $this->get_actions();
        if ($actions) {
            $menu = new \action_menu();
            $menu->set_menu_trigger(get_string('actions'), 'btn btn-outline-primary');
            foreach ($actions as $groupkey => $actiongroup) {
                foreach ($actiongroup as $label => $url) {
                    $menu->add(new \action_menu_link_secondary(new \moodle_url($url), null, $label));
                }
                if ($groupkey !== array_key_last($actions)) {
                    $divider = new \action_menu_filler();
                    $divider->primary = false;
                    $menu->add($divider);
                }
            }

            $renderer = $PAGE->get_renderer('core');
            $data['actions'] = $renderer->render($menu);
        }

        return $data;
    }

    /**
     * Get the actions for the grading action menu.
     *
     * @return array A 2D array of actions grouped by a key in the form of key => label => URL.
     */
    private function get_actions() {
        $actions = [];
        if (
            has_capability('gradereport/grader:view', $this->assign->get_course_context())
            && has_capability('moodle/grade:viewall', $this->assign->get_course_context())
        ) {
            $url = new moodle_url('/grade/report/grader/index.php', ['id' => $this->assign->get_course()->id]);
            $actions['gradebook'][get_string('viewgradebook', 'assign')] = $url->out(false);
        }
        if ($this->assign->is_blind_marking() && has_capability('mod/assign:revealidentities', $this->assign->get_context())) {
            $url = new moodle_url('/mod/assign/view.php', [
                'id' => $this->assign->get_course_module()->id,
                'action' => 'revealidentities',
            ]);
            $actions['blindmarking'][get_string('revealidentities', 'assign')] = $url->out(false);
        }
        foreach ($this->assign->get_feedback_plugins() as $plugin) {
            if ($plugin->is_enabled() && $plugin->is_visible()) {
                foreach ($plugin->get_grading_actions() as $action => $description) {
                    $url = new moodle_url('/mod/assign/view.php', [
                        'id' => $this->assign->get_course_module()->id,
                        'plugin' => $plugin->get_type(),
                        'pluginsubtype' => 'assignfeedback',
                        'action' => 'viewpluginpage',
                        'pluginaction' => $action,
                    ]);
                    $actions['assignfeedback_' . $plugin->get_type()][$description] = $url->out(false);
                }
            }
        }
        if ($this->assign->is_any_submission_plugin_enabled() && $this->assign->count_submissions()) {
            $url = new moodle_url('/mod/assign/view.php', [
                'id' => $this->assign->get_course_module()->id,
                'action' => 'downloadall',
            ]);
            $actions['downloadall'][get_string('downloadall', 'mod_assign')] = $url->out(false);
        }

        return $actions;
    }
}

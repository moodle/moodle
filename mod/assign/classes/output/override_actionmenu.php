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
 * Output the override actionbar for this activity.
 *
 * @package   mod_assign
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_assign\output;

use core_availability\info_module;
use moodle_url;
use templatable;
use renderable;
use url_select;
use single_button;

/**
 * Output the override actionbar for this activity.
 *
 * @package   mod_assign
 * @copyright 2021 Adrian Greeve <adrian@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class override_actionmenu implements templatable, renderable {

    /** @var moodle_url The current url for this page. */
    protected $currenturl;
    /** @var \cm_info course module information */
    protected $cm;
    /** @var bool Can all groups be accessed */
    protected $canaccessallgroups;
    /** @var array Groups related to this activity */
    protected $groups;

    /**
     * Constructor for this action menu.
     *
     * @param moodle_url $currenturl The current url for this page.
     * @param \cm_info $cm course module information.
     */
    public function __construct(moodle_url $currenturl, \cm_info $cm) {
        $this->currenturl = $currenturl;
        $this->cm = $cm;
        $groupmode = groups_get_activity_groupmode($this->cm);
        $this->canaccessallgroups = ($groupmode === NOGROUPS) ||
                has_capability('moodle/site:accessallgroups', $this->cm->context);
        $this->groups = $this->canaccessallgroups ? groups_get_all_groups($this->cm->course) :
                groups_get_activity_allowed_groups($this->cm);
    }

    /**
     * Create a select menu for overrides.
     *
     * @return url_select A url select object.
     */
    protected function get_select_menu(): url_select {
        $userlink = new moodle_url('/mod/assign/overrides.php', ['cmid' => $this->cm->id, 'mode' => 'user']);
        $grouplink = new moodle_url('/mod/assign/overrides.php', ['cmid' => $this->cm->id, 'mode' => 'group']);
        $menu = [
            $userlink->out(false) => get_string('useroverrides', 'mod_assign'),
            $grouplink->out(false) => get_string('groupoverrides', 'mod_assign'),
        ];
        return new url_select($menu, $this->currenturl->out(false), null, 'mod_assign_override_select');
    }

    /**
     * Whether to show groups or not. Assignments can be have group overrides if there are groups available in the course.
     * There is no restriction related to the assignment group setting.
     *
     * @return bool
     */
    protected function show_groups(): bool {
        if ($this->canaccessallgroups) {
            $groups = groups_get_all_groups($this->cm->course);
        } else {
            $groups = groups_get_activity_allowed_groups($this->cm);
        }
        return !(empty($groups));
    }

    /**
     * Whether to enable/disable user override button or not.
     *
     * @return bool
     */
    protected function show_useroverride(): bool {
        global $DB;
        $users = [];
        $context = $this->cm->context;
        if ($this->canaccessallgroups) {
            $users = get_enrolled_users($context, '', 0, 'u.id');
        } else if ($this->groups) {
            $enrolledjoin = get_enrolled_join($context, 'u.id');
            list($ingroupsql, $ingroupparams) = $DB->get_in_or_equal(array_keys($this->groups), SQL_PARAMS_NAMED);
            $params = $enrolledjoin->params + $ingroupparams;
            $sql = "SELECT u.id
                      FROM {user} u
                      JOIN {groups_members} gm ON gm.userid = u.id
                           {$enrolledjoin->joins}
                     WHERE gm.groupid $ingroupsql
                       AND {$enrolledjoin->wheres}";
            $users = $DB->get_records_sql($sql, $params);
        }

        $info = new info_module($this->cm);
        $users = $info->filter_user_list($users);

        return !empty($users);
    }

    /**
     * Data to be used in a template.
     *
     * @param \renderer_base $output renderer base output.
     * @return array The data to be used in a template.
     */
    public function export_for_template(\renderer_base $output): array {

        $type = $this->currenturl->get_param('mode');
        if ($type == 'user') {
            $text = get_string('addnewuseroverride', 'mod_assign');
        } else {
            $text = get_string('addnewgroupoverride', 'mod_assign');
        }
        $action = ($type == 'user') ? 'adduser' : 'addgroup';

        $params = ['cmid' => $this->currenturl->get_param('cmid'), 'action' => $action];
        $url = new moodle_url('/mod/assign/overrideedit.php', $params);

        $options = [];
        if ($action == 'addgroup' && !$this->show_groups()) {
            $options = ['disabled' => 'true'];
        } else if ($action === 'adduser' && !$this->show_useroverride()) {
            $options = ['disabled' => 'true'];
        }
        $overridebutton = new single_button($url, $text, 'post', true, $options);

        $urlselect = $this->get_select_menu();
        return [
            'addoverride' => $overridebutton->export_for_template($output),
            'urlselect' => $urlselect->export_for_template($output)
        ];
    }
}

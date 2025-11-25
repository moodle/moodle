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

namespace gradereport_user\output;

use moodle_url;
use core_grades\output\general_action_bar;

/**
 * Renderable class for the action bar elements in the user report page.
 *
 * @package    gradereport_user
 * @copyright  2022 Mihail Geshoski <mihail@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class action_bar extends \core_grades\output\action_bar {

    /** @var int|null $userid The user ID. */
    protected $userid;

    /** @var int $userview The user report view mode. */
    protected $userview;

    /** @var int|null $currentgroupid The user report view mode. */
    protected $currentgroupid;

    /** @var string $usersearch String to search matching users. */
    protected $usersearch;

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     * @param int $userview The user report view mode.
     * @param int|null $userid The user ID or 0 if displaying all users.
     * @param int|null $currentgroupid The ID of the current group.
     * @param string $usersearch String to search matching user.
     */
    public function __construct(
        \context $context,
        int $userview,
        ?int $userid = null,
        ?int $currentgroupid = null,
        string $usersearch = ''
    ) {
        parent::__construct($context);
        $this->userview = $userview;
        $this->userid = $userid;
        $this->currentgroupid = $currentgroupid;
        $this->usersearch = $usersearch;
    }

    /**
     * Returns the template for the action bar.
     *
     * @return string
     */
    public function get_template(): string {
        return 'gradereport_user/action_bar';
    }

    /**
     * Export the data for the mustache template.
     *
     * @param \renderer_base $output renderer to be used to render the action bar elements.
     * @return array
     */
    public function export_for_template(\renderer_base $output): array {
        global $PAGE, $USER;

        // If in the course context, we should display the general navigation selector in gradebook.
        $courseid = $this->context->instanceid;
        // Get the data used to output the general navigation selector.
        $generalnavselector = new general_action_bar($this->context,
            new moodle_url('/grade/report/user/index.php', ['id' => $courseid]), 'gradereport', 'user');
        $data = $generalnavselector->export_for_template($output);

        // If the user has the capability to view all grades, display the group selector (if applicable), the user selector
        // and the view mode selector (if applicable).
        if (has_capability('moodle/grade:viewall', $this->context)) {
            $course = get_course($courseid);
            if ($course->groupmode) {
                $groupselector = new \core_course\output\actionbar\group_selector($this->context);
                $data['groupselector'] = $groupselector->export_for_template($output);
            }

            $resetlink = new moodle_url('/grade/report/user/index.php', ['id' => $courseid, 'group' => 0, 'reset' => 1]);
            $baseurl = new moodle_url('/grade/report/user/index.php', ['id' => $courseid]);
            $PAGE->requires->js_call_amd('gradereport_user/user', 'init', [$baseurl->out(false)]);

            $userselector = new \core_course\output\actionbar\user_selector(
                course: $course,
                resetlink: $resetlink,
                userid: $this->userid,
                groupid: $this->currentgroupid,
                usersearch: $this->usersearch
            );
            $data['userselector'] = [
                'courseid' => $courseid,
                'content' => $userselector->export_for_template($output),
            ];

            // Do not output the 'view mode' selector when in zero state or when the current user is viewing its own report.
            if (!is_null($this->userid) && $USER->id != $this->userid) {
                $viewasotheruser = new moodle_url('/grade/report/user/index.php',
                    ['id' => $courseid, 'userid' => $this->userid, 'userview' => GRADE_REPORT_USER_VIEW_USER]);
                $viewasmyself = new moodle_url('/grade/report/user/index.php',
                    ['id' => $courseid, 'userid' => $this->userid, 'userview' => GRADE_REPORT_USER_VIEW_SELF]);

                $selectoroptions = [
                    $viewasotheruser->out(false) => get_string('otheruser', 'core_grades'),
                    $viewasmyself->out(false) => get_string('myself', 'core_grades')
                ];

                $selectoractiveurl = $this->userview === GRADE_REPORT_USER_VIEW_USER ? $viewasotheruser : $viewasmyself;

                $viewasselect = new \core\output\select_menu('viewas', $selectoroptions, $selectoractiveurl->out(false));
                $viewasselect->set_label(get_string('viewas', 'core_grades'));

                $data['viewasselector'] = $viewasselect->export_for_template($output);
            }
        }

        return $data;
    }
}

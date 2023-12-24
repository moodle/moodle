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

    /**
     * The class constructor.
     *
     * @param \context $context The context object.
     * @param int $userview The user report view mode.
     * @param int|null $userid The user ID or 0 if displaying all users.
     * @param int|null $currentgroupid The ID of the current group.
     */
    public function __construct(\context $context, int $userview, ?int $userid = null, ?int $currentgroupid = null) {
        parent::__construct($context);
        $this->userview = $userview;
        $this->userid = $userid;
        $this->currentgroupid = $currentgroupid;
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
            $userreportrenderer = $PAGE->get_renderer('gradereport_user');
            $data['groupselector'] = $PAGE->get_renderer('core_grades')->group_selector(get_course($courseid));
            $data['userselector'] = [
                'courseid' => $courseid,
                'content' => $userreportrenderer->users_selector(get_course($courseid), $this->userid, $this->currentgroupid)
            ];

            // Do not output the 'view mode' selector when in zero state or when the current user is viewing its own report.
            if (!is_null($this->userid) && $USER->id != $this->userid) {
                $data['viewasselector'] = $userreportrenderer->view_mode_selector($this->userid, $this->userview, $courseid);
            }
        }

        return $data;
    }
}

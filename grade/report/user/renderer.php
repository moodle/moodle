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
 * Renderer for the grade user report
 *
 * @package   gradereport_user
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Custom renderer for the user grade report
 *
 * To get an instance of this use the following code:
 * $renderer = $PAGE->get_renderer('gradereport_user');
 *
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class gradereport_user_renderer extends plugin_renderer_base {

    /**
     * Small rendering function that helps with outputting the relevant user selector.
     *
     * @param string $report
     * @param stdClass $course
     * @param int $userid
     * @param null|int $groupid
     * @param bool $includeall
     * @return string The raw HTML to render.
     * @throws coding_exception
     */
    public function graded_users_selector(string $report, stdClass $course, int $userid, ?int $groupid, bool $includeall): string {

        $select = grade_get_graded_users_select($report, $course, $userid, $groupid, $includeall);
        $output = html_writer::tag('div', $this->output->render($select), ['id' => 'graded_users_selector']);
        $output .= html_writer::tag('p', '', ['style' => 'page-break-after: always;']);

        return $output;
    }

    /**
     * Creates and renders the single select box for the user view.
     *
     * @param int $userid The selected userid
     * @param int $userview The current view user setting constant
     * @return string
     */
    public function view_user_selector(int $userid, int $userview): string {
        global $USER;
        $url = $this->page->url;
        if ($userid != $USER->id) {
            $url->param('userid', $userid);
        }

        $options = [
            GRADE_REPORT_USER_VIEW_USER => get_string('otheruser', 'gradereport_user'),
            GRADE_REPORT_USER_VIEW_SELF => get_string('myself', 'gradereport_user')
        ];
        $select = new single_select($url, 'userview', $options, $userview, null);

        $select->label = get_string('viewas', 'gradereport_user');

        $output = html_writer::tag('div', $this->output->render($select), ['class' => 'view_users_selector']);

        return $output;
    }

}

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

namespace gradereport_singleview\local\screen;

/**
 * The grade search screen.
 *
 * @package   gradereport_singleview
 * @copyright 2022 Mathew May <mathew.solutions>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class grade_select extends screen {
    public function init($selfitemisempty = false) {
    }
    /**
     * Return the HTML for the page.
     *
     * @return string
     */
    public function html(): string {
        global $OUTPUT, $COURSE, $USER;

        $userlink = new \moodle_url('/grade/report/singleview/index.php', ['id' => $COURSE->id, 'item' => 'user_select']);
        $gradelink = new \moodle_url('/grade/report/singleview/index.php', ['id' => $COURSE->id, 'item' => 'grade_select']);
        $gpr = new \grade_plugin_return(['type' => 'report', 'plugin' => 'singleview', 'courseid' => $COURSE->id,
            'userid' => $USER->id]);
        $context = [
            'courseid' => $gpr->courseid,
            'imglink' => $OUTPUT->image_url('zero_state_grade', 'gradereport_singleview'),
            'userzerolink' => $userlink->out(false),
            'userselectactive' => false,
            'gradezerolink' => $gradelink->out(false),
            'gradeselectactive' => true,
            'displaylabel' => true,
            'groupmodeenabled' => $COURSE->groupmode,
            'groupactionbaseurl' => 'index.php?item=grade_select',
            'groupid' => $gpr->groupid
        ];
        return $OUTPUT->render_from_template('gradereport_singleview/zero_state_grade', $context);
    }

    public function item_type(): ?string {
        return false;
    }

    /**
     * Should we show the base singlereport group selector?
     * @return bool
     */
    public function display_group_selector(): bool {
        return false;
    }

    /**
     * Get the heading for the screen.
     *
     * @return string
     */
    public function heading(): string {
        return ' ';
    }

    /**
     * Does this screen support paging?
     *
     * @return bool
     */
    public function supports_paging(): bool {
        return false;
    }
}

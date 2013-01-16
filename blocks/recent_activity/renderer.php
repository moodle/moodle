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
 * Renderer for block recent_activity
 *
 * @package    block_recent_activity
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

/**
 * recent_activity block rendrer
 *
 * @package    block_recent_activity
 * @copyright  2012 Marina Glancy
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_recent_activity_renderer extends plugin_renderer_base {

    /**
     * Renders HTML to display recent_activity block
     *
     * @param stdClass $course
     * @param int $timestart
     * @param array $recentenrolments array of changes in enrolments
     * @param array $structuralchanges array of changes in course structure
     * @param array $modulesrecentactivity array of changes in modules (provided by modules)
     * @return string
     */
    public function recent_activity($course, $timestart, $recentenrolments, $structuralchanges,
            $modulesrecentactivity) {

        $output = html_writer::tag('div',
                get_string('activitysince', '', userdate($timestart)),
                array('class' => 'activityhead'));

        $output .= html_writer::tag('div',
                html_writer::link(new moodle_url('/course/recent.php', array('id' => $course->id)),
                    get_string('recentactivityreport')),
                array('class' => 'activityhead'));

        $content = false;

        // Firstly, have there been any new enrolments?
        if ($recentenrolments) {
            $content = true;
            $context = context_course::instance($course->id);
            $viewfullnames = has_capability('moodle/site:viewfullnames', $context);
            $output .= html_writer::start_tag('div', array('class' => 'newusers'));
            $output .= $this->heading(get_string("newusers").':', 3);
            //Accessibility: new users now appear in an <OL> list.
            $output .= html_writer::start_tag('ol', array('class' => 'list'));
            foreach ($recentenrolments as $user) {
                $output .= html_writer::tag('li',
                        html_writer::link(new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $course->id)),
                                fullname($user, $viewfullnames)),
                        array('class' => 'name'));
            }
            $output .= html_writer::end_tag('ol');
            $output .= html_writer::end_tag('div');
        }

        // Next, have there been any modifications to the course structure?
        if (!empty($structuralchanges)) {
            $content = true;
            $output .= $this->heading(get_string("courseupdates").':', 3);
            foreach ($structuralchanges as $changeinfo => $change) {
                $output .= $this->structural_change($change);
            }
        }

        // Now display new things from each module
        foreach ($modulesrecentactivity as $modname => $moduleactivity) {
            $content = true;
            $output .= $moduleactivity;
        }

        if (! $content) {
            $output .= html_writer::tag('p', get_string('nothingnew'), array('class' => 'message'));
        }
        return $output;
    }

    /**
     * Renders HTML for one change in course structure
     *
     * @see block_recent_activity::get_structural_changes()
     * @param array $change array containing attributes
     *    'action' - one of: 'add mod', 'update mod', 'delete mod'
     *    'module' - instance of cm_info (for 'delete mod' it is an object with attributes modname and modfullname)
     * @return string
     */
    protected function structural_change($change) {
        $cm = $change['module'];
        switch ($change['action']) {
            case 'delete mod':
                $text = get_string('deletedactivity', 'moodle', $cm->modfullname);
                break;
            case 'add mod':
                $text = get_string('added', 'moodle', $cm->modfullname). '<br />'.
                    html_writer::link($cm->get_url(), format_string($cm->name, true));
                break;
            case 'update mod':
                $text = get_string('updated', 'moodle', $cm->modfullname). '<br />'.
                    html_writer::link($cm->get_url(), format_string($cm->name, true));
                break;
            default:
                return '';
        }
        return html_writer::tag('p', $text, array('class' => 'activity'));
    }
}

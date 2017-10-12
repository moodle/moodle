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
 * Handles displaying the calendar upcoming events block.
 *
 * @package    block_calendar_upcoming
 * @copyright  2004 Eloy Lafuente (stronk7) {@link http://stronk7.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class block_calendar_upcoming extends block_base {

    /**
     * Initialise the block.
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_calendar_upcoming');
    }

    /**
     * Return the content of this block.
     *
     * @return stdClass the content
     */
    public function get_content() {
        global $CFG;

        require_once($CFG->dirroot.'/calendar/lib.php');

        if ($this->content !== null) {
            return $this->content;
        }
        $this->content = new stdClass;
        $this->content->text = '';

        $renderer = $this->page->get_renderer('core_calendar');
        $courseid = $this->page->course->id;
        $issite = ($courseid == SITEID);

        if ($issite) {
            // Being displayed at site level. This will cause the filter to fall back to auto-detecting
            // the list of courses it will be grabbing events from.
            $course = get_site();
            $courses = calendar_get_default_courses();
        } else {
            // Forcibly filter events to include only those from the particular course we are in.
            $course = $this->page->course;
            $courses = [$course->id => $course];
        }
        $calendar = new calendar_information(0, 0, 0, time());
        $calendar->set_sources($course, $courses);

        list($data, $template) = calendar_get_view($calendar, 'upcoming_mini');

        if (empty($data->events)) {
            $this->content->text = '<div class="post">'. get_string('noupcomingevents', 'calendar').'</div>';
        } else {
            $this->content->text .= $renderer->render_from_template($template, $data);
        }

        $this->content->footer = '<div class="gotocal">
                <a href="'.$CFG->wwwroot.'/calendar/view.php?view=upcoming&amp;course='.$courseid.'">'.
                get_string('gotocalendar', 'calendar').'</a>...</div>';

        return $this->content;
    }
}



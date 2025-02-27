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
        $this->content->footer = '';

        $courseid = $this->page->course->id;
        $categoryid = ($this->page->context->contextlevel === CONTEXT_COURSECAT) ? $this->page->category->id : null;
        $calendar = \calendar_information::create(time(), $courseid, $categoryid);
        list($data, $template) = calendar_get_view($calendar, 'upcoming_mini');

        $renderer = $this->page->get_renderer('core_calendar');
        $this->content->text .= $renderer->render_from_template($template, $data);

        $url = new \moodle_url('/calendar/view.php', ['view' => 'upcoming']);
        if ($courseid != SITEID) {
            $url->param('course', $this->page->course->id);
        } else if (!empty($categoryid)) {
            $url->param('category', $this->page->category->id);
        }

        $this->content->footer = html_writer::div(
            html_writer::link($url, get_string('gotocalendar', 'block_calendar_upcoming')),
            'gotocal'
        );

        return $this->content;
    }
}

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

    /**
     * Get the upcoming event block content.
     *
     * @param array $events list of events
     * @param \moodle_url|string $linkhref link to event referer
     * @param boolean $showcourselink whether links to courses should be shown
     * @return string|null $content html block content
     * @deprecated since 3.4
     */
    public static function get_upcoming_content($events, $linkhref = null, $showcourselink = false) {
        debugging(
                'get_upcoming_content() is deprecated. ' .
                'Please see block_calendar_upcoming::get_content() for the correct API usage.',
                DEBUG_DEVELOPER
            );

        $content = '';
        $lines = count($events);

        if (!$lines) {
            return $content;
        }

        for ($i = 0; $i < $lines; ++$i) {
            if (!isset($events[$i]->time)) {
                continue;
            }
            $events[$i] = calendar_add_event_metadata($events[$i]);
            $content .= '<div class="event"><span class="icon c0">' . $events[$i]->icon . '</span>';
            if (!empty($events[$i]->referer)) {
                // That's an activity event, so let's provide the hyperlink.
                $content .= $events[$i]->referer;
            } else {
                if (!empty($linkhref)) {
                    $href = calendar_get_link_href(new \moodle_url(CALENDAR_URL . $linkhref), 0, 0, 0,
                        $events[$i]->timestart);
                    $href->set_anchor('event_' . $events[$i]->id);
                    $content .= \html_writer::link($href, $events[$i]->name);
                } else {
                    $content .= $events[$i]->name;
                }
            }
            $events[$i]->time = str_replace('&raquo;', '<br />&raquo;', $events[$i]->time);
            if ($showcourselink && !empty($events[$i]->courselink)) {
                $content .= \html_writer::div($events[$i]->courselink, 'course');
            }
            $content .= '<div class="date">' . $events[$i]->time . '</div></div>';
            if ($i < $lines - 1) {
                $content .= '<hr />';
            }
        }

        return $content;
    }
}

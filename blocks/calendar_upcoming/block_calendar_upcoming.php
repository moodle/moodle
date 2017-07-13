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

        $filtercourse    = array();
        if (empty($this->instance)) { // Overrides: use no course at all.
            $courseshown = false;
            $this->content->footer = '';

        } else {
            $courseshown = $this->page->course->id;
            $this->content->footer = '<div class="gotocal"><a href="'.$CFG->wwwroot.
                                     '/calendar/view.php?view=upcoming&amp;course='.$courseshown.'">'.
                                      get_string('gotocalendar', 'calendar').'</a>...</div>';
            $context = context_course::instance($courseshown);
            if (has_any_capability(array('moodle/calendar:manageentries', 'moodle/calendar:manageownentries'), $context)) {
                $this->content->footer .= '<div class="newevent"><a href="'.$CFG->wwwroot.
                                          '/calendar/event.php?action=new&amp;course='.$courseshown.'">'.
                                           get_string('newevent', 'calendar').'</a>...</div>';
            }
            if ($courseshown == SITEID) {
                // Being displayed at site level. This will cause the filter to fall back to auto-detecting
                // the list of courses it will be grabbing events from.
                $filtercourse = calendar_get_default_courses();
            } else {
                // Forcibly filter events to include only those from the particular course we are in.
                $filtercourse = array($courseshown => $this->page->course);
            }
        }

        list($courses, $group, $user) = calendar_set_filters($filtercourse);

        $defaultlookahead = CALENDAR_DEFAULT_UPCOMING_LOOKAHEAD;
        if (isset($CFG->calendar_lookahead)) {
            $defaultlookahead = intval($CFG->calendar_lookahead);
        }
        $lookahead = get_user_preferences('calendar_lookahead', $defaultlookahead);

        $defaultmaxevents = CALENDAR_DEFAULT_UPCOMING_MAXEVENTS;
        if (isset($CFG->calendar_maxevents)) {
            $defaultmaxevents = intval($CFG->calendar_maxevents);
        }
        $maxevents = get_user_preferences('calendar_maxevents', $defaultmaxevents);
        $events = calendar_get_upcoming($courses, $group, $user, $lookahead, $maxevents);

        if (!empty($this->instance)) {
            $link = 'view.php?view=day&amp;course='.$courseshown.'&amp;';
            $showcourselink = ($this->page->course->id == SITEID);
            $this->content->text = self::get_upcoming_content($events, $link, $showcourselink);
        }

        if (empty($this->content->text)) {
            $this->content->text = '<div class="post">'. get_string('noupcomingevents', 'calendar').'</div>';
        }

        return $this->content;
    }

    /**
     * Get the upcoming event block content.
     *
     * @param array $events list of events
     * @param \moodle_url|string $linkhref link to event referer
     * @param boolean $showcourselink whether links to courses should be shown
     * @return string|null $content html block content
     */
    public static function get_upcoming_content($events, $linkhref = null, $showcourselink = false) {
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



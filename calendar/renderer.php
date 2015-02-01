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
 * This file contains the renderers for the calendar within Moodle
 *
 * @copyright 2010 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package calendar
 */

if (!defined('MOODLE_INTERNAL')) {
    die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
}

/**
 * The primary renderer for the calendar.
 */
class core_calendar_renderer extends plugin_renderer_base {

    /**
     * Starts the standard layout for the page
     *
     * @return string
     */
    public function start_layout() {
        return html_writer::start_tag('div', array('class'=>'maincalendar'));
    }

    /**
     * Creates the remainder of the layout
     *
     * @return string
     */
    public function complete_layout() {
        return html_writer::end_tag('div');
    }

    /**
     * Produces the content for the filters block (pretend block)
     *
     * @param int $courseid
     * @param int $day
     * @param int $month
     * @param int $year
     * @param int $view
     * @param int $courses
     * @return string
     */
    public function fake_block_filters($courseid, $day, $month, $year, $view, $courses) {
        $returnurl = $this->page->url;
        $returnurl->param('course', $courseid);
        return html_writer::tag('div', calendar_filter_controls($returnurl), array('class'=>'calendar_filters filters'));
    }

    /**
     * Produces the content for the three months block (pretend block)
     *
     * This includes the previous month, the current month, and the next month
     *
     * @param calendar_information $calendar
     * @return string
     */
    public function fake_block_threemonths(calendar_information $calendar) {
        // Get the calendar type we are using.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        $date = $calendartype->timestamp_to_date_array($calendar->time);

        $prevmonth = calendar_sub_month($date['mon'], $date['year']);
        $prevmonthtime = $calendartype->convert_to_gregorian($prevmonth[1], $prevmonth[0], 1);
        $prevmonthtime = make_timestamp($prevmonthtime['year'], $prevmonthtime['month'], $prevmonthtime['day'],
            $prevmonthtime['hour'], $prevmonthtime['minute']);

        $nextmonth = calendar_add_month($date['mon'], $date['year']);
        $nextmonthtime = $calendartype->convert_to_gregorian($nextmonth[1], $nextmonth[0], 1);
        $nextmonthtime = make_timestamp($nextmonthtime['year'], $nextmonthtime['month'], $nextmonthtime['day'],
            $nextmonthtime['hour'], $nextmonthtime['minute']);

        $content  = html_writer::start_tag('div', array('class' => 'minicalendarblock'));
        $content .= calendar_get_mini($calendar->courses, $calendar->groups, $calendar->users, false, false, 'display', $calendar->courseid, $prevmonthtime);
        $content .= html_writer::end_tag('div');
        $content .= html_writer::start_tag('div', array('class' => 'minicalendarblock'));
        $content .= calendar_get_mini($calendar->courses, $calendar->groups, $calendar->users, false, false, 'display', $calendar->courseid, $calendar->time);
        $content .= html_writer::end_tag('div');
        $content .= html_writer::start_tag('div', array('class' => 'minicalendarblock'));
        $content .= calendar_get_mini($calendar->courses, $calendar->groups, $calendar->users, false, false, 'display', $calendar->courseid, $nextmonthtime);
        $content .= html_writer::end_tag('div');
        return $content;
    }

    /**
     * Adds a pretent calendar block
     *
     * @param block_contents $bc
     * @param mixed $pos BLOCK_POS_RIGHT | BLOCK_POS_LEFT
     */
    public function add_pretend_calendar_block(block_contents $bc, $pos=BLOCK_POS_RIGHT) {
        $this->page->blocks->add_fake_block($bc, $pos);
    }

    /**
     * Creates a button to add a new event
     *
     * @param int $courseid
     * @param int $day
     * @param int $month
     * @param int $year
     * @param int $time the unixtime, used for multiple calendar support. The values $day,
     *     $month and $year are kept for backwards compatibility.
     * @return string
     */
    protected function add_event_button($courseid, $day = null, $month = null, $year = null, $time = null) {
        // If a day, month and year were passed then convert it to a timestamp. If these were passed
        // then we can assume the day, month and year are passed as Gregorian, as no where in core
        // should we be passing these values rather than the time. This is done for BC.
        if (!empty($day) && !empty($month) && !empty($year)) {
            if (checkdate($month, $day, $year)) {
                $time = make_timestamp($year, $month, $day);
            } else {
                $time = time();
            }
        } else if (empty($time)) {
            $time = time();
        }

        $output = html_writer::start_tag('div', array('class'=>'buttons'));
        $output .= html_writer::start_tag('form', array('action' => CALENDAR_URL . 'event.php', 'method' => 'get'));
        $output .= html_writer::start_tag('div');
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name' => 'action', 'value' => 'new'));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name' => 'course', 'value' => $courseid));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name' => 'time', 'value' => $time));
        $output .= html_writer::empty_tag('input', array('type'=>'submit', 'value' => get_string('newevent', 'calendar')));
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('form');
        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Displays the calendar for a single day
     *
     * @param calendar_information $calendar
     * @return string
     */
    public function show_day(calendar_information $calendar, moodle_url $returnurl = null) {

        if ($returnurl === null) {
            $returnurl = $this->page->url;
        }

        $events = calendar_get_upcoming($calendar->courses, $calendar->groups, $calendar->users, 1, 100, $calendar->timestamp_today());

        $output  = html_writer::start_tag('div', array('class'=>'header'));
        $output .= $this->course_filter_selector($returnurl, get_string('dayviewfor', 'calendar'));
        if (calendar_user_can_add_event($calendar->course)) {
            $output .= $this->add_event_button($calendar->course->id, 0, 0, 0, $calendar->time);
        }
        $output .= html_writer::end_tag('div');
        // Controls
        $output .= html_writer::tag('div', calendar_top_controls('day', array('id' => $calendar->courseid, 'time' => $calendar->time)), array('class'=>'controls'));

        if (empty($events)) {
            // There is nothing to display today.
            $output .= html_writer::span(get_string('daywithnoevents', 'calendar'), 'calendar-information calendar-no-results');
        } else {
            $output .= html_writer::start_tag('div', array('class' => 'eventlist'));
            $underway = array();
            // First, print details about events that start today
            foreach ($events as $event) {
                $event = new calendar_event($event);
                $event->calendarcourseid = $calendar->courseid;
                if ($event->timestart >= $calendar->timestamp_today() && $event->timestart <= $calendar->timestamp_tomorrow()-1) {  // Print it now
                    $event->time = calendar_format_event_time($event, time(), null, false, $calendar->timestamp_today());
                    $output .= $this->event($event);
                } else {                                                                 // Save this for later
                    $underway[] = $event;
                }
            }

            // Then, show a list of all events that just span this day
            if (!empty($underway)) {
                $output .= html_writer::span(get_string('spanningevents', 'calendar'),
                    'calendar-information calendar-span-multiple-days');
                foreach ($underway as $event) {
                    $event->time = calendar_format_event_time($event, time(), null, false, $calendar->timestamp_today());
                    $output .= $this->event($event);
                }
            }

            $output .= html_writer::end_tag('div');
        }

        return $output;
    }

    /**
     * Displays an event
     *
     * @param calendar_event $event
     * @param bool $showactions
     * @return string
     */
    public function event(calendar_event $event, $showactions=true) {
        global $CFG;

        $event = calendar_add_event_metadata($event);
        $context = $event->context;
        $output = '';

        if (!empty($event->icon)) {
            $output .= $event->icon;
        } else {
            $output .= $this->output->spacer(array('height' => 16, 'width' => 16));
        }

        if (!empty($event->referer)) {
            $output .= $this->output->heading($event->referer, 3, array('class' => 'referer'));
        } else {
            $output .= $this->output->heading(
                format_string($event->name, false, array('context' => $context)),
                3,
                array('class' => 'name')
            );
        }
        if (!empty($event->courselink)) {
            $output .= html_writer::tag('div', $event->courselink, array('class' => 'course'));
        }
        // Show subscription source if needed.
        if (!empty($event->subscription) && $CFG->calendar_showicalsource) {
            if (!empty($event->subscription->url)) {
                $source = html_writer::link($event->subscription->url, get_string('subsource', 'calendar', $event->subscription));
            } else {
                // File based ical.
                $source = get_string('subsource', 'calendar', $event->subscription);
            }
            $output .= html_writer::tag('div', $source, array('class' => 'subscription'));
        }
        if (!empty($event->time)) {
            $output .= html_writer::tag('span', $event->time, array('class' => 'date'));
        } else {
            $output .= html_writer::tag('span', calendar_time_representation($event->timestart), array('class' => 'date'));
        }

        $eventdetailshtml = '';
        $eventdetailsclasses = '';

        $eventdetailshtml .= format_text($event->description, $event->format, array('context' => $context));
        $eventdetailsclasses .= 'description';
        if (isset($event->cssclass)) {
            $eventdetailsclasses .= ' '.$event->cssclass;
        }

        $output .= html_writer::tag('div', $eventdetailshtml, array('class' => $eventdetailsclasses));

        if (calendar_edit_event_allowed($event) && $showactions) {
            if (empty($event->cmid)) {
                $editlink = new moodle_url(CALENDAR_URL.'event.php', array('action'=>'edit', 'id'=>$event->id));
                $deletelink = new moodle_url(CALENDAR_URL.'delete.php', array('id'=>$event->id));
                if (!empty($event->calendarcourseid)) {
                    $editlink->param('course', $event->calendarcourseid);
                    $deletelink->param('course', $event->calendarcourseid);
                }
            } else {
                $editlink = new moodle_url('/course/mod.php', array('update'=>$event->cmid, 'return'=>true, 'sesskey'=>sesskey()));
                $deletelink = null;
            }

            $commands  = html_writer::start_tag('div', array('class'=>'commands'));
            $commands .= html_writer::start_tag('a', array('href'=>$editlink));
            $commands .= html_writer::empty_tag('img', array('src'=>$this->output->pix_url('t/edit'), 'alt'=>get_string('tt_editevent', 'calendar'), 'title'=>get_string('tt_editevent', 'calendar')));
            $commands .= html_writer::end_tag('a');
            if ($deletelink != null) {
                $commands .= html_writer::start_tag('a', array('href'=>$deletelink));
                $commands .= html_writer::empty_tag('img', array('src'=>$this->output->pix_url('t/delete'), 'alt'=>get_string('tt_deleteevent', 'calendar'), 'title'=>get_string('tt_deleteevent', 'calendar')));
                $commands .= html_writer::end_tag('a');
            }
            $commands .= html_writer::end_tag('div');
            $output .= $commands;
        }
        return html_writer::tag('div', $output , array('class' => 'event', 'id' => 'event_' . $event->id));
    }

    /**
     * Displays a month in detail
     *
     * @param calendar_information $calendar
     * @param moodle_url $returnurl the url to return to
     * @return string
     */
    public function show_month_detailed(calendar_information $calendar, moodle_url $returnurl  = null) {
        global $CFG;

        if (empty($returnurl)) {
            $returnurl = $this->page->url;
        }

        // Get the calendar type we are using.
        $calendartype = \core_calendar\type_factory::get_calendar_instance();

        // Store the display settings.
        $display = new stdClass;
        $display->thismonth = false;

        // Get the specified date in the calendar type being used.
        $date = $calendartype->timestamp_to_date_array($calendar->time);
        $thisdate = $calendartype->timestamp_to_date_array(time());
        if ($date['mon'] == $thisdate['mon'] && $date['year'] == $thisdate['year']) {
            $display->thismonth = true;
            $date = $thisdate;
            $calendar->time = time();
        }

        // Get Gregorian date for the start of the month.
        $gregoriandate = $calendartype->convert_to_gregorian($date['year'], $date['mon'], 1);
        // Store the gregorian date values to be used later.
        list($gy, $gm, $gd, $gh, $gmin) = array($gregoriandate['year'], $gregoriandate['month'], $gregoriandate['day'],
            $gregoriandate['hour'], $gregoriandate['minute']);

        // Get the starting week day for this month.
        $startwday = dayofweek(1, $date['mon'], $date['year']);
        // Get the days in a week.
        $daynames = calendar_get_days();
        // Store the number of days in a week.
        $numberofdaysinweek = $calendartype->get_num_weekdays();

        $display->minwday = calendar_get_starting_weekday();
        $display->maxwday = $display->minwday + ($numberofdaysinweek - 1);
        $display->maxdays = calendar_days_in_month($date['mon'], $date['year']);

        // These are used for DB queries, so we want unixtime, so we need to use Gregorian dates.
        $display->tstart = make_timestamp($gy, $gm, $gd, $gh, $gmin, 0);
        $display->tend = $display->tstart + ($display->maxdays * DAYSECS) - 1;

        // Align the starting weekday to fall in our display range
        // This is simple, not foolproof.
        if ($startwday < $display->minwday) {
            $startwday += $numberofdaysinweek;
        }

        // Get events from database
        $events = calendar_get_events($display->tstart, $display->tend, $calendar->users, $calendar->groups, $calendar->courses);
        if (!empty($events)) {
            foreach($events as $eventid => $event) {
                $event = new calendar_event($event);
                if (!empty($event->modulename)) {
                    $cm = get_coursemodule_from_instance($event->modulename, $event->instance);
                    if (!\core_availability\info_module::is_user_visible($cm, 0, false)) {
                        unset($events[$eventid]);
                    }
                }
            }
        }

        // Extract information: events vs. time
        calendar_events_by_day($events, $date['mon'], $date['year'], $eventsbyday, $durationbyday, $typesbyday, $calendar->courses);

        $output  = html_writer::start_tag('div', array('class'=>'header'));
        $output .= $this->course_filter_selector($returnurl, get_string('detailedmonthviewfor', 'calendar'));
        if (calendar_user_can_add_event($calendar->course)) {
            $output .= $this->add_event_button($calendar->course->id, 0, 0, 0, $calendar->time);
        }
        $output .= html_writer::end_tag('div', array('class'=>'header'));
        // Controls
        $output .= html_writer::tag('div', calendar_top_controls('month', array('id' => $calendar->courseid, 'time' => $calendar->time)), array('class' => 'controls'));

        $table = new html_table();
        $table->attributes = array('class'=>'calendarmonth calendartable');
        $table->summary = get_string('calendarheading', 'calendar', userdate($calendar->time, get_string('strftimemonthyear')));
        $table->data = array();

        // Get the day names as the header.
        $header = array();
        for($i = $display->minwday; $i <= $display->maxwday; ++$i) {
            $header[] = $daynames[$i % $numberofdaysinweek]['shortname'];
        }
        $table->head = $header;

        // For the table display. $week is the row; $dayweek is the column.
        $week = 1;
        $dayweek = $startwday;

        $row = new html_table_row(array());

        // Paddding (the first week may have blank days in the beginning)
        for($i = $display->minwday; $i < $startwday; ++$i) {
            $cell = new html_table_cell('&nbsp;');
            $cell->attributes = array('class'=>'nottoday dayblank');
            $row->cells[] = $cell;
        }

        // Now display all the calendar
        $weekend = CALENDAR_DEFAULT_WEEKEND;
        if (isset($CFG->calendar_weekend)) {
            $weekend = intval($CFG->calendar_weekend);
        }

        $daytime = strtotime('-1 day', $display->tstart);
        for ($day = 1; $day <= $display->maxdays; ++$day, ++$dayweek) {
            $daytime = strtotime('+1 day', $daytime);
            if($dayweek > $display->maxwday) {
                // We need to change week (table row)
                $table->data[] = $row;
                $row = new html_table_row(array());
                $dayweek = $display->minwday;
                ++$week;
            }

            // Reset vars
            $cell = new html_table_cell();
            $dayhref = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', array('view' => 'day', 'course' => $calendar->courseid)), 0, 0, 0, $daytime);

            $cellclasses = array();

            if ($weekend & (1 << ($dayweek % $numberofdaysinweek))) {
                // Weekend. This is true no matter what the exact range is.
                $cellclasses[] = 'weekend';
            }

            // Special visual fx if an event is defined
            if (isset($eventsbyday[$day])) {
                if(count($eventsbyday[$day]) == 1) {
                    $title = get_string('oneevent', 'calendar');
                } else {
                    $title = get_string('manyevents', 'calendar', count($eventsbyday[$day]));
                }
                $cell->text = html_writer::tag('div', html_writer::link($dayhref, $day, array('title'=>$title)), array('class'=>'day'));
            } else {
                $cell->text = html_writer::tag('div', $day, array('class'=>'day'));
            }

            // Special visual fx if an event spans many days
            $durationclass = false;
            if (isset($typesbyday[$day]['durationglobal'])) {
                $durationclass = 'duration_global';
            } else if (isset($typesbyday[$day]['durationcourse'])) {
                $durationclass = 'duration_course';
            } else if (isset($typesbyday[$day]['durationgroup'])) {
                $durationclass = 'duration_group';
            } else if (isset($typesbyday[$day]['durationuser'])) {
                $durationclass = 'duration_user';
            }
            if ($durationclass) {
                $cellclasses[] = 'duration';
                $cellclasses[] = $durationclass;
            }

            // Special visual fx for today
            if ($display->thismonth && $day == $date['mday']) {
                $cellclasses[] = 'day today';
            } else {
                $cellclasses[] = 'day nottoday';
            }
            $cell->attributes = array('class'=>join(' ',$cellclasses));

            if (isset($eventsbyday[$day])) {
                $cell->text .= html_writer::start_tag('ul', array('class'=>'events-new'));
                foreach($eventsbyday[$day] as $eventindex) {
                    // If event has a class set then add it to the event <li> tag
                    $attributes = array();
                    if (!empty($events[$eventindex]->class)) {
                        $attributes['class'] = $events[$eventindex]->class;
                    }
                    $dayhref->set_anchor('event_'.$events[$eventindex]->id);
                    $link = html_writer::link($dayhref, format_string($events[$eventindex]->name, true));
                    $cell->text .= html_writer::tag('li', $link, $attributes);
                }
                $cell->text .= html_writer::end_tag('ul');
            }
            if (isset($durationbyday[$day])) {
                $cell->text .= html_writer::start_tag('ul', array('class'=>'events-underway'));
                foreach($durationbyday[$day] as $eventindex) {
                    $cell->text .= html_writer::tag('li', '['.format_string($events[$eventindex]->name,true).']', array('class'=>'events-underway'));
                }
                $cell->text .= html_writer::end_tag('ul');
            }
            $row->cells[] = $cell;
        }

        // Paddding (the last week may have blank days at the end)
        for($i = $dayweek; $i <= $display->maxwday; ++$i) {
            $cell = new html_table_cell('&nbsp;');
            $cell->attributes = array('class'=>'nottoday dayblank');
            $row->cells[] = $cell;
        }
        $table->data[] = $row;
        $output .= html_writer::table($table);

        return $output;
    }

    /**
     * Displays upcoming events
     *
     * @param calendar_information $calendar
     * @param int $futuredays
     * @param int $maxevents
     * @return string
     */
    public function show_upcoming_events(calendar_information $calendar, $futuredays, $maxevents, moodle_url $returnurl = null) {

        if ($returnurl === null) {
            $returnurl = $this->page->url;
        }

        $events = calendar_get_upcoming($calendar->courses, $calendar->groups, $calendar->users, $futuredays, $maxevents);

        $output  = html_writer::start_tag('div', array('class'=>'header'));
        $output .= $this->course_filter_selector($returnurl, get_string('upcomingeventsfor', 'calendar'));
        if (calendar_user_can_add_event($calendar->course)) {
            $output .= $this->add_event_button($calendar->course->id);
        }
        $output .= html_writer::end_tag('div');

        if ($events) {
            $output .= html_writer::start_tag('div', array('class' => 'eventlist'));
            foreach ($events as $event) {
                // Convert to calendar_event object so that we transform description
                // accordingly
                $event = new calendar_event($event);
                $event->calendarcourseid = $calendar->courseid;
                $output .= $this->event($event);
            }
            $output .= html_writer::end_tag('div');
        } else {
            $output .= html_writer::span(get_string('noupcomingevents', 'calendar'), 'calendar-information calendar-no-results');
        }

        return $output;
    }

    /**
     * Displays a course filter selector
     *
     * @param moodle_url $returnurl The URL that the user should be taken too upon selecting a course.
     * @param string $label The label to use for the course select.
     * @return string
     */
    protected function course_filter_selector(moodle_url $returnurl, $label=null) {
        global $USER, $SESSION, $CFG;

        if (!isloggedin() or isguestuser()) {
            return '';
        }

        if (has_capability('moodle/calendar:manageentries', context_system::instance()) && !empty($CFG->calendar_adminseesall)) {
            $courses = get_courses('all', 'c.shortname','c.id,c.shortname');
        } else {
            $courses = enrol_get_my_courses();
        }

        unset($courses[SITEID]);

        $courseoptions = array();
        $courseoptions[SITEID] = get_string('fulllistofcourses');
        foreach ($courses as $course) {
            $coursecontext = context_course::instance($course->id);
            $courseoptions[$course->id] = format_string($course->shortname, true, array('context' => $coursecontext));
        }

        if ($this->page->course->id !== SITEID) {
            $selected = $this->page->course->id;
        } else {
            $selected = '';
        }
        $courseurl = new moodle_url($returnurl);
        $courseurl->remove_params('course');
        $select = new single_select($courseurl, 'course', $courseoptions, $selected, null);
        $select->class = 'cal_courses_flt';
        if ($label !== null) {
            $select->set_label($label);
        } else {
            $select->set_label(get_string('listofcourses'), array('class' => 'accesshide'));
        }
        return $this->output->render($select);
    }

    /**
     * Renders a table containing information about calendar subscriptions.
     *
     * @param int $courseid
     * @param array $subscriptions
     * @param string $importresults
     * @return string
     */
    public function subscription_details($courseid, $subscriptions, $importresults = '') {
        $table = new html_table();
        $table->head  = array(
            get_string('colcalendar', 'calendar'),
            get_string('collastupdated', 'calendar'),
            get_string('eventkind', 'calendar'),
            get_string('colpoll', 'calendar'),
            get_string('colactions', 'calendar')
        );
        $table->align = array('left', 'left', 'left', 'center');
        $table->width = '100%';
        $table->data  = array();

        if (empty($subscriptions)) {
            $cell = new html_table_cell(get_string('nocalendarsubscriptions', 'calendar'));
            $cell->colspan = 4;
            $table->data[] = new html_table_row(array($cell));
        }
        $strnever = new lang_string('never', 'calendar');
        foreach ($subscriptions as $sub) {
            $label = $sub->name;
            if (!empty($sub->url)) {
                $label = html_writer::link($sub->url, $label);
            }
            if (empty($sub->lastupdated)) {
                $lastupdated = $strnever->out();
            } else {
                $lastupdated = userdate($sub->lastupdated, get_string('strftimedatetimeshort', 'langconfig'));
            }

            $cell = new html_table_cell($this->subscription_action_form($sub, $courseid));
            $cell->colspan = 2;
            $type = $sub->eventtype . 'events';

            $table->data[] = new html_table_row(array(
                new html_table_cell($label),
                new html_table_cell($lastupdated),
                new html_table_cell(get_string($type, 'calendar')),
                $cell
            ));
        }

        $out  = $this->output->box_start('generalbox calendarsubs');

        $out .= $importresults;
        $out .= html_writer::table($table);
        $out .= $this->output->box_end();
        return $out;
    }

    /**
     * Creates a form to perform actions on a given subscription.
     *
     * @param stdClass $subscription
     * @param int $courseid
     * @return string
     */
    protected function subscription_action_form($subscription, $courseid) {
        // Assemble form for the subscription row.
        $html = html_writer::start_tag('form', array('action' => new moodle_url('/calendar/managesubscriptions.php'), 'method' => 'post'));
        if (empty($subscription->url)) {
            // Don't update an iCal file, which has no URL.
            $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'pollinterval', 'value' => '0'));
        } else {
            // Assemble pollinterval control.
            $html .= html_writer::start_tag('div', array('style' => 'float:left;'));
            $html .= html_writer::start_tag('select', array('name' => 'pollinterval'));
            foreach (calendar_get_pollinterval_choices() as $k => $v) {
                $attributes = array();
                if ($k == $subscription->pollinterval) {
                    $attributes['selected'] = 'selected';
                }
                $attributes['value'] = $k;
                $html .= html_writer::tag('option', $v, $attributes);
            }
            $html .= html_writer::end_tag('select');
            $html .= html_writer::end_tag('div');
        }
        $html .= html_writer::start_tag('div', array('style' => 'float:right;'));
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'course', 'value' => $courseid));
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'id', 'value' => $subscription->id));
        if (!empty($subscription->url)) {
            $html .= html_writer::tag('button', get_string('update'), array('type'  => 'submit', 'name' => 'action',
                                                                            'value' => CALENDAR_SUBSCRIPTION_UPDATE));
        }
        $html .= html_writer::tag('button', get_string('remove'), array('type'  => 'submit', 'name' => 'action',
                                                                        'value' => CALENDAR_SUBSCRIPTION_REMOVE));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('form');
        return $html;
    }
}

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
     * Creates a basic export form
     *
     * @param bool $allowthisweek
     * @param bool $allownextweek
     * @param bool $allownextmonth
     * @param int $userid
     * @param string $authtoken
     * @return string
     */
    public function basic_export_form($allowthisweek, $allownextweek, $allownextmonth, $userid, $authtoken) {

        $output  = html_writer::tag('div', get_string('export', 'calendar'), array('class'=>'header'));
        $output .= html_writer::start_tag('fieldset');
        $output .= html_writer::tag('legend', get_string('commontasks', 'calendar'));
        $output .= html_writer::start_tag('form', array('action'=>new moodle_url('/calendar/export_execute.php'), 'method'=>'get'));

        $output .= html_writer::tag('div', get_string('iwanttoexport', 'calendar'));

        $output .= html_writer::start_tag('div', array('class'=>'indent'));
        $output .= html_writer::empty_tag('input', array('type'=>'radio', 'name'=>'preset_what', 'id'=>'pw_all', 'value'=>'all', 'checked'=>'checked'));
        $output .= html_writer::tag('label', get_string('eventsall', 'calendar'), array('for'=>'pw_all'));
        $output .= html_writer::empty_tag('br');
        $output .= html_writer::empty_tag('input', array('type'=>'radio', 'name'=>'preset_what', 'id'=>'pw_course', 'value'=>'courses'));
        $output .= html_writer::tag('label', get_string('eventsrelatedtocourses', 'calendar'), array('for'=>'pw_course'));
        $output .= html_writer::empty_tag('br');
        $output .= html_writer::end_tag('div');

        $output .= html_writer::tag('div', get_string('for', 'calendar').':');

        $output .= html_writer::start_tag('div', array('class'=>'indent'));
        if ($allowthisweek) {
            $output .= html_writer::empty_tag('input', array('type'=>'radio', 'name'=>'preset_time', 'id'=>'pt_wknow', 'value'=>'weeknow', 'checked'=>'checked'));
            $output .= html_writer::tag('label', get_string('weekthis', 'calendar'), array('for'=>'pt_wknow'));
            $output .= html_writer::empty_tag('br');
        }
        if ($allownextweek) {
            $output .= html_writer::empty_tag('input', array('type'=>'radio', 'name'=>'preset_time', 'id'=>'pt_wknext', 'value'=>'weeknext'));
            $output .= html_writer::tag('label', get_string('weeknext', 'calendar'), array('for'=>'pt_wknext'));
            $output .= html_writer::empty_tag('br');
        }
        $output .= html_writer::empty_tag('input', array('type'=>'radio', 'name'=>'preset_time', 'id'=>'pt_monnow', 'value'=>'monthnow'));
        $output .= html_writer::tag('label', get_string('monththis', 'calendar'), array('for'=>'pt_monnow'));
        $output .= html_writer::empty_tag('br');
        if ($allownextmonth) {
            $output .= html_writer::empty_tag('input', array('type'=>'radio', 'name'=>'preset_time', 'id'=>'pt_monnext', 'value'=>'monthnext'));
            $output .= html_writer::tag('label', get_string('monthnext', 'calendar'), array('for'=>'pt_monnext'));
            $output .= html_writer::empty_tag('br');
        }
        $output .= html_writer::empty_tag('input', array('type'=>'radio', 'name'=>'preset_time', 'id'=>'pt_recupc', 'value'=>'recentupcoming'));
        $output .= html_writer::tag('label', get_string('recentupcoming', 'calendar'), array('for'=>'pt_recupc'));
        $output .= html_writer::empty_tag('br');
        $output .= html_writer::end_tag('div');

        $output .= html_writer::start_tag('div', array('class'=>'rightalign'));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'cal_d', 'value'=>''));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'cal_m', 'value'=>''));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'cal_y', 'value'=>''));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'userid', 'value'=>$userid));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'authtoken', 'value'=>$authtoken));

        $output .= html_writer::empty_tag('input', array('type'=>'submit', 'name' => 'generateurl', 'id'=>'generateurl', 'value'=>get_string('generateurlbutton', 'calendar')));
        $output .= html_writer::empty_tag('input', array('type'=>'submit', 'value'=>get_string('exportbutton', 'calendar')));

        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('form');
        $output .= html_writer::end_tag('fieldset');

        $output .= html_writer::start_tag('div', array('id'=>'urlbox', 'style'=>'display:none;'));
        $output .= html_writer::tag('p', get_string('urlforical', 'calendar'));
        $output .= html_writer::tag('div', '', array('id'=>'url', 'style'=>'overflow:scroll;width:650px;'));
        $output .= html_writer::end_tag('div');

        return $output;
    }

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

        list($prevmon, $prevyr) = calendar_sub_month($calendar->month, $calendar->year);
        list($nextmon, $nextyr) = calendar_add_month($calendar->month, $calendar->year);

        $content  = html_writer::start_tag('div', array('class'=>'minicalendarblock'));
        $content .= calendar_top_controls('display', array('id' => $calendar->courseid, 'm' => $prevmon, 'y' => $prevyr));
        $content .= calendar_get_mini($calendar->courses, $calendar->groups, $calendar->users, $prevmon, $prevyr);
        $content .= html_writer::end_tag('div');
        $content .= html_writer::start_tag('div', array('class'=>'minicalendarblock'));
        $content .= calendar_top_controls('display', array('id' => $calendar->courseid, 'm' => $calendar->month, 'y' => $calendar->year));
        $content .= calendar_get_mini($calendar->courses, $calendar->groups, $calendar->users, $calendar->month, $calendar->year);
        $content .= html_writer::end_tag('div');
        $content .= html_writer::start_tag('div', array('class'=>'minicalendarblock'));
        $content .= calendar_top_controls('display', array('id' => $calendar->courseid, 'm' => $nextmon, 'y' => $nextyr));
        $content .= calendar_get_mini($calendar->courses, $calendar->groups, $calendar->users, $nextmon, $nextyr);
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
     * @return string
     */
    protected function add_event_button($courseid, $day=null, $month=null, $year=null) {
        $output = html_writer::start_tag('div', array('class'=>'buttons'));
        $output .= html_writer::start_tag('form', array('action'=>CALENDAR_URL.'event.php', 'method'=>'get'));
        $output .= html_writer::start_tag('div');
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'action', 'value'=>'new'));
        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'course', 'value'=>$courseid));
        if ($day !== null) {
            $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'cal_d', 'value'=>$day));
        }
        if ($month !== null) {
            $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'cal_m', 'value'=>$month));
        }
        if ($year !== null) {
            $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'cal_y', 'value'=>$year));
        }
        $output .= html_writer::empty_tag('input', array('type'=>'submit', 'value'=>get_string('newevent', 'calendar')));
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

        $calendar->checkdate();
        $events = calendar_get_upcoming($calendar->courses, $calendar->groups, $calendar->users, 1, 100, $calendar->timestamp_today());

        $output  = html_writer::start_tag('div', array('class'=>'header'));
        if (calendar_user_can_add_event($calendar->course)) {
            $output .= $this->add_event_button($calendar->course->id, $calendar->day, $calendar->month, $calendar->year);
        }
        //$output .= html_writer::tag('label', get_string('dayview', 'calendar'), array('for'=>'cal_course_flt_jump'));
        $output .= $this->course_filter_selector($returnurl, get_string('dayview', 'calendar'));
        $output .= html_writer::end_tag('div');
        // Controls
        $output .= html_writer::tag('div', calendar_top_controls('day', array('id' => $calendar->courseid, 'd' => $calendar->day, 'm' => $calendar->month, 'y' => $calendar->year)), array('class'=>'controls'));

        if (empty($events)) {
            // There is nothing to display today.
            $output .= $this->output->heading(get_string('daywithnoevents', 'calendar'), 3);
        } else {
            $output .= html_writer::start_tag('div', array('class'=>'eventlist'));
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
                $output .= $this->output->heading(get_string('spanningevents', 'calendar'), 3);
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
        $event = calendar_add_event_metadata($event);

        $anchor  = html_writer::tag('a', '', array('name'=>'event_'.$event->id));

        $table = new html_table();
        $table->attributes = array('class'=>'event', 'cellspacing'=>'0');
        $table->data = array(
            0 => new html_table_row(),
            1 => new html_table_row(),
        );

        if (!empty($event->icon)) {
            $table->data[0]->cells[0] = new html_table_cell($anchor.$event->icon);
        } else {
            $table->data[0]->cells[0] = new html_table_cell($anchor.$this->output->spacer(array('height'=>16, 'width'=>16, 'br'=>true)));
        }
        $table->data[0]->cells[0]->attributes['class'] .= ' picture';

        $table->data[0]->cells[1] = new html_table_cell();
        $table->data[0]->cells[1]->attributes['class'] .= ' topic';
        if (!empty($event->referer)) {
            $table->data[0]->cells[1]->text .= html_writer::tag('div', $event->referer, array('class'=>'referer'));
        } else {
            $table->data[0]->cells[1]->text .= html_writer::tag('div', $event->name, array('class'=>'name'));
        }
        if (!empty($event->courselink)) {
            $table->data[0]->cells[1]->text .= html_writer::tag('div', $event->courselink, array('class'=>'course'));
        }
        if (!empty($event->time)) {
            $table->data[0]->cells[1]->text .= html_writer::tag('span', $event->time, array('class'=>'date'));
        } else {
            $table->data[0]->cells[1]->text .= html_writer::tag('span', calendar_time_representation($event->timestart), array('class'=>'date'));
        }

        $table->data[1]->cells[0] = new html_table_cell('&nbsp;');
        $table->data[1]->cells[0]->attributes['class'] .= 'side';

        $table->data[1]->cells[1] = new html_table_cell($event->description);
        $table->data[1]->cells[1]->attributes['class'] .= ' description';
        if (isset($event->cssclass)) {
            $table->data[1]->cells[1]->attributes['class'] .= ' '.$event->cssclass;
        }

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
            $table->data[1]->cells[1]->text .= $commands;
        }
        return html_writer::table($table);
    }

    /**
     * Displays a month in detail
     *
     * @param calendar_information $calendar
     * @return string
     */
    public function show_month_detailed(calendar_information $calendar, moodle_url $returnurl  = null) {
        global $CFG;

        if (empty($returnurl)) {
            $returnurl = $this->page->url;
        }

        $date = usergetdate(time());

        $display = new stdClass;
        $display->minwday = get_user_preferences('calendar_startwday', calendar_get_starting_weekday());
        $display->maxwday = $display->minwday + 6;
        $display->thismonth = ($date['mon'] == $calendar->month);
        $display->maxdays = calendar_days_in_month($calendar->month, $calendar->year);

        $startwday = 0;
        if (get_user_timezone_offset() < 99) {
            // We 'll keep these values as GMT here, and offset them when the time comes to query the db
            $display->tstart = gmmktime(0, 0, 0, $calendar->month, 1, $calendar->year); // This is GMT
            $display->tend = gmmktime(23, 59, 59, $calendar->month, $display->maxdays, $calendar->year); // GMT
            $startwday = gmdate('w', $display->tstart); // $display->tstart is already GMT, so don't use date(): messes with server's TZ
        } else {
            // no timezone info specified
            $display->tstart = mktime(0, 0, 0, $calendar->month, 1, $calendar->year);
            $display->tend = mktime(23, 59, 59, $calendar->month, $display->maxdays, $calendar->year);
            $startwday = date('w', $display->tstart); // $display->tstart not necessarily GMT, so use date()
        }

        // Align the starting weekday to fall in our display range
        if ($startwday < $display->minwday) {
            $startwday += 7;
        }

        // Get events from database
        $events = calendar_get_events(usertime($display->tstart), usertime($display->tend), $calendar->users, $calendar->groups, $calendar->courses);
        if (!empty($events)) {
            foreach($events as $eventid => $event) {
                $event = new calendar_event($event);
                if (!empty($event->modulename)) {
                    $cm = get_coursemodule_from_instance($event->modulename, $event->instance);
                    if (!groups_course_module_visible($cm)) {
                        unset($events[$eventid]);
                    }
                }
            }
        }

        // Extract information: events vs. time
        calendar_events_by_day($events, $calendar->month, $calendar->year, $eventsbyday, $durationbyday, $typesbyday, $calendar->courses);

        $output  = html_writer::start_tag('div', array('class'=>'header'));
        if (calendar_user_can_add_event($calendar->course)) {
            $output .= $this->add_event_button($calendar->course->id, null, $calendar->month, $calendar->year);
        }
        $output .= get_string('detailedmonthview', 'calendar').': '.$this->course_filter_selector($returnurl);
        $output .= html_writer::end_tag('div', array('class'=>'header'));
        // Controls
        $output .= html_writer::tag('div', calendar_top_controls('month', array('id' => $calendar->courseid, 'm' => $calendar->month, 'y' => $calendar->year)), array('class'=>'controls'));

        $days = calendar_get_days();

        $table = new html_table();
        $table->attributes = array('class'=>'calendarmonth calendartable');
        $time = make_timestamp($calendar->year, $calendar->month);
        $table->summary = get_string('calendarheading', 'calendar', userdate($time, get_string('strftimemonthyear')));
        $table->data = array();

        $header = new html_table_row();
        $header->attributes = array('class'=>'weekdays');
        $header->cells = array();
        for($i = $display->minwday; $i <= $display->maxwday; ++$i) {
            // This uses the % operator to get the correct weekday no matter what shift we have
            // applied to the $display->minwday : $display->maxwday range from the default 0 : 6
            $cell = new html_table_cell(get_string($days[$i % 7], 'calendar'));
            $cell->header = true;
            $header->cells[] = $cell;
        }

        // For the table display. $week is the row; $dayweek is the column.
        $week = 1;
        $dayweek = $startwday;

        // Create an array of all the week days.
        $wdays = array(0 => '<strong>'. get_string('sunday', 'calendar'). '</strong>',
                       1 => '<strong>'. get_string('monday', 'calendar'). '</strong>',
                       2 => '<strong>'. get_string('tuesday', 'calendar'). '</strong>',
                       3 => '<strong>'. get_string('wednesday', 'calendar'). '</strong>',
                       4 => '<strong>'. get_string('thursday', 'calendar'). '</strong>',
                       5 => '<strong>'. get_string('friday', 'calendar'). '</strong>',
                       6 => '<strong>'. get_string('saturday', 'calendar'). '</strong>');

        // Loop only if the day offset is greater than 0.
        // This loop involves shifting the days around until the desired start day
        // is at the start of the array.
        $daycount = 0;
        while ($display->minwday > $daycount++) {
            $wdays_end = array_shift($wdays);
            array_push($wdays, $wdays_end);
        }

        // Now we set the (modified) array to the table header to be displayed.
        $table->head = $wdays;

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

        for ($calendar->day = 1; $calendar->day <= $display->maxdays; ++$calendar->day, ++$dayweek) {
            if($dayweek > $display->maxwday) {
                // We need to change week (table row)
                $table->data[] = $row;
                $row = new html_table_row(array());
                $dayweek = $display->minwday;
                ++$week;
            }

            // Reset vars
            $cell = new html_table_cell();
            $dayhref = calendar_get_link_href(new moodle_url(CALENDAR_URL.'view.php', array('view'=>'day', 'course'=>$calendar->courseid)), $calendar->day, $calendar->month, $calendar->year);

            $cellclasses = array();

            if ($weekend & (1 << ($dayweek % 7))) {
                // Weekend. This is true no matter what the exact range is.
                $cellclasses[] = 'weekend';
            }

            // Special visual fx if an event is defined
            if (isset($eventsbyday[$calendar->day])) {
                if(count($eventsbyday[$calendar->day]) == 1) {
                    $title = get_string('oneevent', 'calendar');
                } else {
                    $title = get_string('manyevents', 'calendar', count($eventsbyday[$calendar->day]));
                }
                $cell->text = html_writer::tag('div', html_writer::link($dayhref, $calendar->day, array('title'=>$title)), array('class'=>'day'));
            } else {
                $cell->text = html_writer::tag('div', $calendar->day, array('class'=>'day'));
            }

            // Special visual fx if an event spans many days
            $durationclass = false;
            if (isset($typesbyday[$calendar->day]['durationglobal'])) {
                $durationclass = 'duration_global';
            } else if (isset($typesbyday[$calendar->day]['durationcourse'])) {
                $durationclass = 'duration_course';
            } else if (isset($typesbyday[$calendar->day]['durationgroup'])) {
                $durationclass = 'duration_group';
            } else if (isset($typesbyday[$calendar->day]['durationuser'])) {
                $durationclass = 'duration_user';
            }
            if ($durationclass) {
                $cellclasses[] = 'duration';
                $cellclasses[] = $durationclass;
            }

            // Special visual fx for today
            if ($display->thismonth && $calendar->day == $date['mday']) {
                $cellclasses[] = 'day today';
            } else {
                $cellclasses[] = 'day nottoday';
            }
            $cell->attributes = array('class'=>join(' ',$cellclasses));

            if (isset($eventsbyday[$calendar->day])) {
                $cell->text .= html_writer::start_tag('ul', array('class'=>'events-new'));
                foreach($eventsbyday[$calendar->day] as $eventindex) {
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
            if (isset($durationbyday[$calendar->day])) {
                $cell->text .= html_writer::start_tag('ul', array('class'=>'events-underway'));
                foreach($durationbyday[$calendar->day] as $eventindex) {
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
     * Displays a filter selection table
     *
     * @param calendar_information $calendar
     * @return string
     * @deprecated since Moodle 2.4 MDL-32309
     * @see calendar_filter_controls()
     */
    protected function filter_selection_table(calendar_information $calendar, moodle_url $returnurl = null) {
        global $SESSION;
        debugging('Method core_calendar_renderer::filter_selection_table() is deprecated, please use '.
                'calendar_filter_controls() instead', DEBUG_DEVELOPER);

        if ($returnurl === null) {
            $returnurl = $this->page->url;
        }

        $output  = html_writer::start_tag('div', array('class'=>'filters'));
        $output .= html_writer::start_tag('table');
        $output .= html_writer::start_tag('tr');

        // Global events
        $link = new moodle_url(CALENDAR_URL.'set.php', array('var' => 'showglobal', 'return' => base64_encode($returnurl->out(false)), 'sesskey'=>sesskey()));
        $strglobalevents = get_string('globalevents', 'calendar');
        if (calendar_show_event_type(CALENDAR_EVENT_GLOBAL)) {
            $output .= html_writer::tag('td', '', array('class'=>'calendar_event_global', 'style'=>'width:8px;'));
            $output .= html_writer::tag('td', html_writer::tag('strong', $strglobalevents).' '.get_string('shown', 'calendar').' ('.html_writer::link($link, get_string('clickhide', 'calendar').'<span class="accesshide">'.$strglobalevents.'</span>').')');
        } else {
            $output .= html_writer::tag('td', '', array('style'=>'width:8px;'));
            $output .= html_writer::tag('td', html_writer::tag('strong', $strglobalevents).' '.get_string('hidden', 'calendar').' ('.html_writer::link($link, get_string('clickshow', 'calendar').'<span class="accesshide">'.$strglobalevents.'</span>').')');
        }

        // Course events
        $link = new moodle_url(CALENDAR_URL.'set.php', array('var'=>'showcourses', 'return' => base64_encode($returnurl->out(false)), 'sesskey'=>sesskey()));
        $strcourseevents = get_string('courseevents', 'calendar');
        if (calendar_show_event_type(CALENDAR_EVENT_COURSE)) {
            $output .= html_writer::tag('td', '', array('class'=>'calendar_event_course', 'style'=>'width:8px;'));
            $output .= html_writer::tag('td', html_writer::tag('strong', $strcourseevents).' '.get_string('shown', 'calendar').' ('.html_writer::link($link, get_string('clickhide', 'calendar').'<span class="accesshide">'.$strcourseevents.'</span>').')');
        } else {
            $output .= html_writer::tag('td', '', array('style'=>'width:8px;'));
            $output .= html_writer::tag('td', html_writer::tag('strong', $strcourseevents).' '.get_string('hidden', 'calendar').' ('.html_writer::link($link, get_string('clickshow', 'calendar').'<span class="accesshide">'.$strcourseevents.'</span>').')');
        }
        $output .= html_writer::end_tag('tr');

        if(isloggedin() && !isguestuser()) {
            $output .= html_writer::start_tag('tr');
            // Group events
            $link = new moodle_url(CALENDAR_URL.'set.php', array('var'=>'showgroups', 'return' => base64_encode($returnurl->out(false)), 'sesskey'=>sesskey()));
            $strgroupevents = get_string('groupevents', 'calendar');
            if (calendar_show_event_type(CALENDAR_EVENT_GROUP)) {
                $output .= html_writer::tag('td', '', array('class'=>'calendar_event_group', 'style'=>'width:8px;'));
                $output .= html_writer::tag('td', html_writer::tag('strong', $strgroupevents).' '.get_string('shown', 'calendar').' ('.html_writer::link($link, get_string('clickhide', 'calendar').'<span class="accesshide">'.$strgroupevents.'</span>').')');
            } else {
                $output .= html_writer::tag('td', '', array('style'=>'width:8px;'));
                $output .= html_writer::tag('td', html_writer::tag('strong', $strgroupevents).' '.get_string('hidden', 'calendar').' ('.html_writer::link($link, get_string('clickshow', 'calendar').'<span class="accesshide">'.$strgroupevents.'</span>').')');
            }
            // User events
            $link = new moodle_url(CALENDAR_URL.'set.php', array('var'=>'showuser', 'return' => base64_encode($returnurl->out(false)), 'sesskey'=>sesskey()));
            $struserevents = get_string('userevents', 'calendar');
            if (calendar_show_event_type(CALENDAR_EVENT_USER)) {
                $output .= html_writer::tag('td', '', array('class'=>'calendar_event_user', 'style'=>'width:8px;'));
                $output .= html_writer::tag('td', html_writer::tag('strong', $struserevents).' '.get_string('shown', 'calendar').' ('.html_writer::link($link, get_string('clickhide', 'calendar').'<span class="accesshide">'.$struserevents.'</span>').')');
            } else {
                $output .= html_writer::tag('td', '', array('style'=>'width:8px;'));
                $output .= html_writer::tag('td', html_writer::tag('strong', $struserevents).' '.get_string('hidden', 'calendar').' ('.html_writer::link($link, get_string('clickshow', 'calendar').'<span class="accesshide">'.$struserevents.'</span>').')');
            }
            $output .= html_writer::end_tag('tr');
        }
        $output .= html_writer::end_tag('table');
        $output .= html_writer::end_tag('div');
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
        if (calendar_user_can_add_event($calendar->course)) {
            $output .= $this->add_event_button($calendar->course->id);
        }
        $output .= html_writer::tag('label', get_string('upcomingevents', 'calendar'), array('for'=>'cal_course_flt_jump'));
        $output .= $this->course_filter_selector($returnurl);
        $output .= html_writer::end_tag('div');

        if ($events) {
            $output .= html_writer::start_tag('div', array('class'=>'eventlist'));
            foreach ($events as $event) {
                // Convert to calendar_event object so that we transform description
                // accordingly
                $event = new calendar_event($event);
                $event->calendarcourseid = $calendar->courseid;
                $output .= $this->event($event);
            }
            $output .= html_writer::end_tag('div');
        } else {
            $output .= $this->output->heading(get_string('noupcomingevents', 'calendar'));
        }

        return $output;
    }

    /**
     * Displays a course filter selector
     *
     * @param array $getvars
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
        $select = new single_select(new moodle_url(CALENDAR_URL.'set.php', array('return' => base64_encode($returnurl->out(false)), 'var' => 'setcourse', 'sesskey'=>sesskey())), 'id', $courseoptions, $selected, null);
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
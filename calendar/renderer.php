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
        return html_writer::start_tag('div', ['data-region' => 'calendar', 'class' => 'maincalendar']);
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
        $time = $calendartype->timestamp_to_date_array($calendar->time);

        $current = $calendar->time;
        $prevmonthyear = $calendartype->get_prev_month($time['year'], $time['mon']);
        $prev = $calendartype->convert_to_timestamp(
                $prevmonthyear[1],
                $prevmonthyear[0],
                1
            );
        $nextmonthyear = $calendartype->get_next_month($time['year'], $time['mon']);
        $next = $calendartype->convert_to_timestamp(
                $nextmonthyear[1],
                $nextmonthyear[0],
                1
            );

        $content = '';

        // Previous.
        $calendar->set_time($prev);
        list($previousmonth, ) = calendar_get_view($calendar, 'minithree', false, true);

        // Current month.
        $calendar->set_time($current);
        list($currentmonth, ) = calendar_get_view($calendar, 'minithree', false, true);

        // Next month.
        $calendar->set_time($next);
        list($nextmonth, ) = calendar_get_view($calendar, 'minithree', false, true);

        // Reset the time back.
        $calendar->set_time($current);

        $data = (object) [
            'previousmonth' => $previousmonth,
            'currentmonth' => $currentmonth,
            'nextmonth' => $nextmonth,
        ];

        $template = 'core_calendar/calendar_threemonth';
        $content .= $this->render_from_template($template, $data);
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
     * Creates a button to add a new event.
     *
     * @param int $courseid
     * @param int $unused1
     * @param int $unused2
     * @param int $unused3
     * @param int $unused4
     * @return string
     */
    public function add_event_button($courseid, $unused1 = null, $unused2 = null, $unused3 = null, $unused4 = null) {
        $data = [
            'contextid' => (\context_course::instance($courseid))->id,
        ];
        return $this->render_from_template('core_calendar/add_event_button', $data);
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

        $output .= $this->output->box_start('card-header clearfix');
        if (calendar_edit_event_allowed($event) && $showactions) {
            if (calendar_delete_event_allowed($event)) {
                $editlink = new moodle_url(CALENDAR_URL.'event.php', array('action' => 'edit', 'id' => $event->id));
                $deletelink = new moodle_url(CALENDAR_URL.'delete.php', array('id' => $event->id));
                if (!empty($event->calendarcourseid)) {
                    $editlink->param('course', $event->calendarcourseid);
                    $deletelink->param('course', $event->calendarcourseid);
                }
            } else {
                $params = array('update' => $event->cmid, 'return' => true, 'sesskey' => sesskey());
                $editlink = new moodle_url('/course/mod.php', $params);
                $deletelink = null;
            }

            $commands  = html_writer::start_tag('div', array('class' => 'commands float-sm-right'));
            $commands .= html_writer::start_tag('a', array('href' => $editlink));
            $str = get_string('tt_editevent', 'calendar');
            $commands .= $this->output->pix_icon('t/edit', $str);
            $commands .= html_writer::end_tag('a');
            if ($deletelink != null) {
                $commands .= html_writer::start_tag('a', array('href' => $deletelink));
                $str = get_string('tt_deleteevent', 'calendar');
                $commands .= $this->output->pix_icon('t/delete', $str);
                $commands .= html_writer::end_tag('a');
            }
            $commands .= html_writer::end_tag('div');
            $output .= $commands;
        }
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
                array('class' => 'name d-inline-block')
            );
        }
        // Show subscription source if needed.
        if (!empty($event->subscription) && $CFG->calendar_showicalsource) {
            if (!empty($event->subscription->url)) {
                $source = html_writer::link($event->subscription->url,
                        get_string('subscriptionsource', 'calendar', $event->subscription->name));
            } else {
                // File based ical.
                $source = get_string('subscriptionsource', 'calendar', $event->subscription->name);
            }
            $output .= html_writer::tag('div', $source, array('class' => 'subscription'));
        }
        if (!empty($event->courselink)) {
            $output .= html_writer::tag('div', $event->courselink);
        }
        if (!empty($event->time)) {
            $output .= html_writer::tag('span', $event->time, array('class' => 'date float-sm-right mr-1'));
        } else {
            $attrs = array('class' => 'date float-sm-right mr-1');
            $output .= html_writer::tag('span', calendar_time_representation($event->timestart), $attrs);
        }

        if (!empty($event->actionurl)) {
            $actionlink = html_writer::link(new moodle_url($event->actionurl), $event->actionname);
            $output .= html_writer::tag('div', $actionlink, ['class' => 'action']);
        }

        $output .= $this->output->box_end();
        $eventdetailshtml = '';
        $eventdetailsclasses = '';

        $eventdetailshtml .= format_text($event->description, $event->format, array('context' => $context));
        $eventdetailsclasses .= 'description card-block';
        if (isset($event->cssclass)) {
            $eventdetailsclasses .= ' '.$event->cssclass;
        }

        if (!empty($eventdetailshtml)) {
            $output .= html_writer::tag('div', $eventdetailshtml, array('class' => $eventdetailsclasses));
        }

        $eventhtml = html_writer::tag('div', $output, array('class' => 'card'));
        return html_writer::tag('div', $eventhtml, array('class' => 'event', 'id' => 'event_' . $event->id));
    }

    /**
     * Displays a course filter selector
     *
     * @param moodle_url $returnurl The URL that the user should be taken too upon selecting a course.
     * @param string $label The label to use for the course select.
     * @param int $courseid The id of the course to be selected.
     * @return string
     */
    public function course_filter_selector(moodle_url $returnurl, $label = null, $courseid = null) {
        global $CFG, $DB;

        if (!isloggedin() or isguestuser()) {
            return '';
        }

        $contextrecords = [];
        $courses = calendar_get_default_courses($courseid, 'id, shortname');

        if (!empty($courses) && count($courses) > CONTEXT_CACHE_MAX_SIZE) {
            // We need to pull the context records from the DB to preload them
            // below. The calendar_get_default_courses code will actually preload
            // the contexts itself however the context cache is capped to a certain
            // amount before it starts recycling. Unfortunately that starts to happen
            // quite a bit if a user has access to a large number of courses (e.g. admin).
            // So in order to avoid hitting the DB for each context as we loop below we
            // can load all of the context records and add them to the cache just in time.
            $courseids = array_map(function($c) {
                return $c->id;
            }, $courses);
            list($insql, $params) = $DB->get_in_or_equal($courseids);
            $contextsql = "SELECT ctx.instanceid, " . context_helper::get_preload_record_columns_sql('ctx') .
                          " FROM {context} ctx WHERE ctx.contextlevel = ? AND ctx.instanceid $insql";
            array_unshift($params, CONTEXT_COURSE);
            $contextrecords = $DB->get_records_sql($contextsql, $params);
        }

        unset($courses[SITEID]);

        $courseoptions = array();
        $courseoptions[SITEID] = get_string('fulllistofcourses');
        foreach ($courses as $course) {
            if (isset($contextrecords[$course->id])) {
                context_helper::preload_from_record($contextrecords[$course->id]);
            }
            $coursecontext = context_course::instance($course->id);
            $courseoptions[$course->id] = format_string($course->shortname, true, array('context' => $coursecontext));
        }

        if ($courseid) {
            $selected = $courseid;
        } else if ($this->page->course->id !== SITEID) {
            $selected = $this->page->course->id;
        } else {
            $selected = '';
        }
        $courseurl = new moodle_url($returnurl);
        $courseurl->remove_params('course');

        if ($label === null) {
            $label = get_string('listofcourses');
        }

        $select = html_writer::label($label, 'course', false, ['class' => 'mr-1']);
        $select .= html_writer::select($courseoptions, 'course', $selected, false,
                ['class' => 'cal_courses_flt mr-auto']);

        return $select;
    }

    /**
     * Renders a table containing information about calendar subscriptions.
     *
     * @param int $unused
     * @param array $subscriptions
     * @param string $importresults
     * @return string
     */
    public function subscription_details($unused = null, $subscriptions, $importresults = '') {
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
            $cell->colspan = 5;
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

            $cell = new html_table_cell($this->subscription_action_form($sub));
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
     * @return string
     */
    protected function subscription_action_form($subscription) {
        // Assemble form for the subscription row.
        $html = html_writer::start_tag('form', array('action' => new moodle_url('/calendar/managesubscriptions.php'), 'method' => 'post'));
        if (empty($subscription->url)) {
            // Don't update an iCal file, which has no URL.
            $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'pollinterval', 'value' => '0'));
        } else {
            // Assemble pollinterval control.
            $html .= html_writer::start_tag('div', array('style' => 'float:left;'));
            $html .= html_writer::start_tag('select', array('name' => 'pollinterval', 'class' => 'custom-select'));
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
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()));
        $html .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'id', 'value' => $subscription->id));
        $html .= html_writer::start_tag('div', array('class' => 'btn-group float-right'));
        if (!empty($subscription->url)) {
            $html .= html_writer::tag('button', get_string('update'), array('type'  => 'submit', 'name' => 'action',
                                                                            'class' => 'btn btn-secondary',
                                                                            'value' => CALENDAR_SUBSCRIPTION_UPDATE));
        }
        $html .= html_writer::tag('button', get_string('remove'), array('type'  => 'submit', 'name' => 'action',
                                                                        'class' => 'btn btn-secondary',
                                                                        'value' => CALENDAR_SUBSCRIPTION_REMOVE));
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_tag('form');
        return $html;
    }

    /**
     * Render the event filter region.
     *
     * @return  string
     */
    public function event_filter() {
        $data = [
            'eventtypes' => calendar_get_filter_types(),
        ];
        return $this->render_from_template('core_calendar/event_filter', $data);
    }
}

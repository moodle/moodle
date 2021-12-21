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
     * @deprecated since 4.0 MDL-72810.
     * @todo MDL-73117 This will be deleted in Moodle 4.4.
     *
     * @param calendar_information $calendar
     * @return string
     */
    public function fake_block_threemonths(calendar_information $calendar) {
        debugging('This method is no longer used as the three month calendar block has been removed', DEBUG_DEVELOPER);

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
     * @deprecated since 3.9
     *
     * @param calendar_event $event
     * @param bool $showactions
     * @return string
     */
    public function event(calendar_event $event, $showactions=true) {
        global $CFG;
        debugging('This function is no longer used', DEBUG_DEVELOPER);

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
     * @param int|null $calendarinstanceid The instance ID of the calendar we're generating this course filter for.
     * @return string
     */
    public function course_filter_selector(moodle_url $returnurl, $label = null, $courseid = null, int $calendarinstanceid = null) {
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

        $labelattributes = [];
        if (empty($label)) {
            $label = get_string('listofcourses');
            $labelattributes['class'] = 'sr-only';
        }

        $filterid = 'calendar-course-filter';
        if ($calendarinstanceid) {
            $filterid .= "-$calendarinstanceid";
        }
        $select = html_writer::label($label, $filterid, false, $labelattributes);
        $select .= html_writer::select($courseoptions, 'course', $selected, false,
                ['class' => 'cal_courses_flt ml-1 mr-auto', 'id' => $filterid]);

        return $select;
    }

    /**
     * Render the subscriptions header
     *
     * @return string
     */
    public function render_subscriptions_header(): string {
        $importcalendarbutton = new single_button(new moodle_url('/calendar/import.php', calendar_get_export_import_link_params()),
                get_string('importcalendar', 'calendar'), 'get', true);
        $importcalendarbutton->class .= ' float-sm-right float-right';
        $exportcalendarbutton = new single_button(new moodle_url('/calendar/export.php', calendar_get_export_import_link_params()),
                get_string('exportcalendar', 'calendar'), 'get', true);
        $exportcalendarbutton->class .= ' float-sm-right float-right';
        $output = $this->output->heading(get_string('managesubscriptions', 'calendar'));
        $output .= html_writer::start_div('header d-flex flex-wrap mt-5');
        $headerattr = [
            'class' => 'mr-auto',
            'aria-describedby' => 'subscription_details_table',
        ];
        $output .= html_writer::tag('h3', get_string('yoursubscriptions', 'calendar'), $headerattr);
        $output .= $this->output->render($importcalendarbutton);
        $output .= $this->output->render($exportcalendarbutton);
        $output .= html_writer::end_div();

        return $output;
    }

    /**
     * Render the subscriptions blank state appearance
     *
     * @return string
     */
    public function render_no_calendar_subscriptions(): string {
        $output = html_writer::start_div('mt-5');
        $importlink = html_writer::link((new moodle_url('/calendar/import.php', calendar_get_export_import_link_params()))->out(),
                get_string('importcalendarexternal', 'calendar'));
        $output .= get_string('nocalendarsubscriptions', 'calendar', $importlink);
        $output .= html_writer::end_div();

        return $output;
    }

    /**
     * Renders a table containing information about calendar subscriptions.
     *
     * @param int $unused
     * @param array $subscriptions
     * @param string $unused2
     * @return string
     */
    public function subscription_details($unused, $subscriptions, $unused2 = '') {
        $table = new html_table();
        $table->head  = array(
            get_string('colcalendar', 'calendar'),
            get_string('collastupdated', 'calendar'),
            get_string('eventkind', 'calendar'),
            get_string('colpoll', 'calendar'),
            get_string('colactions', 'calendar')
        );
        $table->data  = array();
        $table->id = 'subscription_details_table';

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

            $type = $sub->eventtype . 'events';
            $calendarname = new html_table_cell($label);
            $calendarname->header = true;

            $tablerow = new html_table_row(array(
                $calendarname,
                new html_table_cell($lastupdated),
                new html_table_cell(get_string($type, 'calendar')),
                new html_table_cell($this->render_subscription_update_interval($sub)),
                new html_table_cell($this->subscription_action_links())
            ));
            $tablerow->attributes += [
                'data-subid' => $sub->id,
                'data-subname' => $sub->name
            ];
            $table->data[] = $tablerow;
        }

        $out  = $this->output->box_start('generalbox calendarsubs');

        $out .= html_writer::table($table);
        $out .= $this->output->box_end();

        $this->page->requires->js_call_amd('core_calendar/manage_subscriptions', 'init');
        return $out;
    }

    /**
     * Render subscription update interval form.
     *
     * @param stdClass $subscription
     * @return string
     */
    protected function render_subscription_update_interval(stdClass $subscription): string {
        if (empty($subscription->url)) {
            return '';
        }

        $tmpl = new \core_calendar\output\refreshintervalcollection($subscription);
        return $this->output->render_from_template('core/inplace_editable', $tmpl->export_for_template($this->output));
    }

    /**
     * Creates a form to perform actions on a given subscription.
     *
     * @return string
     */
    protected function subscription_action_links(): string {
        $html = html_writer::start_tag('div', array('class' => 'btn-group float-left'));
        $html .= html_writer::span(html_writer::link('#', get_string('delete'),
            ['data-action' => 'delete-subscription']), '');
        $html .= html_writer::end_tag('div');
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

    /**
     * Render the calendar import result.
     *
     * @param array $result Import result
     * @return string|null
     */
    public function render_import_result(array $result): ?string {
        $data = [
            'eventsimported' => $result['eventsimported'],
            'eventsskipped' => $result['eventsskipped'],
            'eventsupdated' => $result['eventsupdated'],
            'eventsdeleted' => $result['eventsdeleted'],
            'haserror' => $result['haserror'],
            'errors' => $result['errors']
        ];

        return $this->render_from_template('core_calendar/subscription_update_result', $data);
    }
}

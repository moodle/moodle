<?php

class block_calendar_month extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_calendar_month');
    }

    function preferred_width() {
        return 210;
    }

    function get_content() {
        global $USER, $CFG, $SESSION;
        $cal_m = optional_param( 'cal_m', 0, PARAM_INT );
        $cal_y = optional_param( 'cal_y', 0, PARAM_INT );

        require_once($CFG->dirroot.'/calendar/lib.php');

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        // [pj] To me it looks like this if would never be needed, but Penny added it
        // when committing the /my/ stuff. Reminder to discuss and learn what it's about.
        // It definitely needs SOME comment here!
        $courseid = $this->page->course->id;
        $issite = ($courseid == SITEID);

        if ($issite) {
            // Being displayed at site level. This will cause the filter to fall back to auto-detecting
            // the list of courses it will be grabbing events from.
            $filtercourse = calendar_get_default_courses();
        } else {
            // Forcibly filter events to include only those from the particular course we are in.
            $filtercourse = array($courseid => $this->page->course);
        }

        list($courses, $group, $user) = calendar_set_filters($filtercourse);
        if ($issite) {
            // For the front page
            $this->content->text .= calendar_top_controls('frontpage', array('id' => $courseid, 'm' => $cal_m, 'y' => $cal_y));
            $this->content->text .= calendar_get_mini($courses, $group, $user, $cal_m, $cal_y);
            // No filters for now
        } else {
            // For any other course
            $this->content->text .= calendar_top_controls('course', array('id' => $courseid, 'm' => $cal_m, 'y' => $cal_y));
            $this->content->text .= calendar_get_mini($courses, $group, $user, $cal_m, $cal_y);
            $this->content->text .= '<h3 class="eventskey">'.get_string('eventskey', 'calendar').'</h3>';
            $this->content->text .= '<div class="filters">'.calendar_filter_controls($this->page->url).'</div>';
        }

        return $this->content;
    }
}



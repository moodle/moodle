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
        // Reset the session variables
        calendar_session_vars($this->page->course);

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        // [pj] To me it looks like this if would never be needed, but Penny added it
        // when committing the /my/ stuff. Reminder to discuss and learn what it's about.
        // It definitely needs SOME comment here!
        $courseshown = $this->page->course->id;

        if ($courseshown == SITEID) {
            // Being displayed at site level. This will cause the filter to fall back to auto-detecting
            // the list of courses it will be grabbing events from.
            $filtercourse    = NULL;
            $groupeventsfrom = NULL;
            $SESSION->cal_courses_shown = calendar_get_default_courses(true);
            calendar_set_referring_course(0);

        } else {
            //MDL-14693: fix calendar on resource page
            $courseshown =  optional_param( 'id', $this->page->course->id, PARAM_INT );
            // Forcibly filter events to include only those from the particular course we are in.
            $filtercourse    = array($courseshown => $this->page->course);
            $groupeventsfrom = array($courseshown => 1);
        }

        // We 'll need this later
        calendar_set_referring_course($courseshown);

        // MDL-9059, set to show this course when admins go into a course, then unset it.
        if ($this->page->course->id != SITEID && !isset($SESSION->cal_courses_shown[$this->page->course->id]) && has_capability('moodle/calendar:manageentries', get_context_instance(CONTEXT_SYSTEM))) {
            $courseset = true;
            $SESSION->cal_courses_shown[$this->page->course->id] = $this->page->course;
        }

        // Be VERY careful with the format for default courses arguments!
        // Correct formatting is [courseid] => 1 to be concise with moodlelib.php functions.
        calendar_set_filters($courses, $group, $user, $filtercourse, $groupeventsfrom, false);
        if ($courseshown == SITEID) {
            // For the front page
            $this->content->text .= calendar_top_controls('frontpage', array('id' => $courseshown, 'm' => $cal_m, 'y' => $cal_y));
            $this->content->text .= calendar_get_mini($courses, $group, $user, $cal_m, $cal_y);
            // No filters for now

        } else {
            // For any other course
            $this->content->text .= calendar_top_controls('course', array('id' => $courseshown, 'm' => $cal_m, 'y' => $cal_y));
            $this->content->text .= calendar_get_mini($courses, $group, $user, $cal_m, $cal_y);
            $this->content->text .= '<h3 class="eventskey">'.get_string('eventskey', 'calendar').'</h3>';
            $this->content->text .= '<div class="filters">'.calendar_filter_controls('course', '', $this->page->course).'</div>';

        }

        // MDL-9059, unset this so that it doesn't stay in session
        if (!empty($courseset)) {
            unset($SESSION->cal_courses_shown[$this->page->course->id]);
        }

        return $this->content;
    }
}



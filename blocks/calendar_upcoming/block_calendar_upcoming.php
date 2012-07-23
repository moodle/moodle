<?php

class block_calendar_upcoming extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_calendar_upcoming');
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

        $filtercourse    = array();
        if (empty($this->instance)) { // Overrides: use no course at all
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
            $this->content->text = calendar_get_block_upcoming($events, 'view.php?view=day&amp;course='.$courseshown.'&amp;');
        }

        if (empty($this->content->text)) {
            $this->content->text = '<div class="post">'. get_string('noupcomingevents', 'calendar').'</div>';
        }

        return $this->content;
    }
}



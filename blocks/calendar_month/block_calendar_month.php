<?php

class block_calendar_month extends block_base {
    function init() {
        $this->title = get_string('pluginname', 'block_calendar_month');
    }

    function preferred_width() {
        return 210;
    }

    function get_content() {
        global $USER, $CFG, $SESSION, $COURSE, $OUTPUT;
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
            $this->content->text .= calendar_get_mini($courses, $group, $user, $cal_m, $cal_y, 'frontpage', $courseid);
            // No filters for now
        } else {
            // For any other course
            $this->content->text .= calendar_get_mini($courses, $group, $user, $cal_m, $cal_y, 'course', $courseid);
            $this->content->text .= '<h3 class="eventskey">'.get_string('eventskey', 'calendar').'</h3>';
            $this->content->text .= '<div class="filters calendar_filters">'.calendar_filter_controls($this->page->url).'</div>';
        }

        // MDL-18375, Multi-Calendar Support
        if (empty($COURSE->calendarsystem)) {
            // the course has not a forced calendarsystem
            // so user can change it.
            $url = $CFG->wwwroot . (!empty($COURSE->id) && ($COURSE->id!= SITEID) ? "/course/view.php?id={$COURSE->id}" : '/index.php');
            $url = new moodle_url($url);

            $calendarselect = new single_select($url, 'calendarsystem', get_list_of_calendars(), current_calendarsystem_plugin(), false, 'choosecalendar');
            $calendarselect->set_label('<span style="font-weight: normal;">'.get_string('system', 'calendarsystem').'</span>');

            $this->content->text .= '
            <div id="changecalendarlink" style="display: none; visibility: hidden;">
                <a style="font-weight: normal;" onclick="return toggleCalendarVisibility(document.getElementById(\'choosecalendar\'))" href="#">
                ' . get_string('changecalendar', 'calendarsystem') . '
                </a>
            </div>
            ' . $OUTPUT->render($calendarselect) . '
            <script language="JavaScript">
                function toggleCalendarVisibility (choosecalendar) {
                    if (choosecalendar.style.visibility != "visible") {
                        choosecalendar.style.display = "block";
                        choosecalendar.style.visibility = "visible";
                    } else {
                        choosecalendar.style.display = "none";
                        choosecalendar.style.visibility = "hidden";
                    }

                    return false;
                }

                document.getElementById ( "choosecalendar" ).style.display = "none";
                document.getElementById ( "choosecalendar" ).style.visibility = "hidden";

                document.getElementById ( "changecalendarlink" ).style.display = "inline";
                document.getElementById ( "changecalendarlink" ).style.visibility = "visible";
            </script>
             ';
        }

        return $this->content;
    }
}



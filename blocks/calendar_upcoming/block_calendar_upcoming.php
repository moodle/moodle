<?PHP //$Id$

class CourseBlock_calendar_upcoming extends MoodleBlock {
    function CourseBlock_calendar_upcoming ($course) {
        $this->title = get_string('upcomingevents', 'calendar');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004052000;
    }

    function get_content() {
        global $USER, $CFG, $SESSION;
        optional_variable($_GET['cal_m']);
        optional_variable($_GET['cal_y']);

        require_once($CFG->dirroot.'/calendar/lib.php');

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New object;
        $this->content->text = '';
        $this->content->footer = '<br /><a href="'.$CFG->wwwroot.'/calendar/view.php?view=month">'.get_string('gotocalendar', 'calendar').'</a>...';
        $this->content->footer .= '<br /><a href="'.$CFG->wwwroot.'/calendar/event.php?action=new">'.get_string('newevent', 'calendar').'</a>...';

        if($this->course === NULL) {
            // Overrides: use no course at all
            $courseshown = false;
            $defaultcourses = NULL;
        }
        else {
            $courseshown = $this->course->id;
            $defaultcourses = array($courseshown => 1);
        }

        // We 'll need this later
        calendar_set_referring_course($courseshown);

        if($courseshown !== false && $SESSION->cal_show_course !== false) {
            // By default, the course filter will show this course only
            $SESSION->cal_show_course = $courseshown;
        }

        // [pj] Let's leave this in, the above may not be the final solution
        /*
        if($courseshown !== false && is_int($SESSION->cal_show_course) && $SESSION->cal_show_course != $courseshown) {
            // There is a filter in action that shows events from a course other than the current
            // Change it to show only the current course
            $SESSION->cal_show_course = $courseshown;
        }
        else if($courseshown !== false && is_array($SESSION->cal_show_course) && !in_array($courseshown, $SESSION->cal_show_course)) {
            // Same as above, only there are many courses being shown. Unfortunately, not this one.
            // Change it to show only the current course
            $SESSION->cal_show_course = $courseshown;
        }
        */

        // Be VERY careful with the format for default courses arguments!
        // Correct formatting is [courseid] => 1 to be concise with moodlelib.php functions.

        calendar_set_filters($courses, $group, $user, $defaultcourses, $defaultcourses);

        $this->content->text = calendar_get_sideblock_upcoming($courses, $group, $user, get_user_preferences('calendar_lookahead', CALENDAR_UPCOMING_DAYS), get_user_preferences('calendar_maxevents', CALENDAR_UPCOMING_MAXEVENTS));

        if(empty($this->content->text)) {
            $this->content->text = '<div style="font-size: 0.8em; text-align: center;">'.get_string('noupcomingevents', 'calendar').'</div>';
        }

        return $this->content;
    }
}

?>

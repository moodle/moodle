<?PHP //$Id$

class CourseBlock_calendar_upcoming extends MoodleBlock {
    function CourseBlock_calendar_upcoming ($course) {
        $this->title = get_string('upcomingevents', 'calendar');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004052600;
    }

    function get_content() {
        global $USER, $CFG, $SESSION;
        optional_variable($_GET['cal_m']);
        optional_variable($_GET['cal_y']);

        require_once($CFG->dirroot.'/calendar/lib.php');

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New object;
        $this->content->text = '';

        if (empty($this->course)) { // Overrides: use no course at all
        
            $courseshown = false;
            $filtercourse = array();
            $this->content->footer = '';

        } else {

            $courseshown = $this->course->id;
            $this->content->footer = '<br /><a href="'.$CFG->wwwroot.
                                     '/calendar/view.php?view=upcoming&amp;course='.$this->course->id.'">'.
                                      get_string('gotocalendar', 'calendar').'</a>...';
            $this->content->footer .= '<br /><a href="'.$CFG->wwwroot.
                                      '/calendar/event.php?action=new&amp;course='.$this->course->id.'">'.
                                       get_string('newevent', 'calendar').'</a>...';
            
            if($this->course->id == SITEID) {
                // Being displayed at site level. This will cause the filter to fall back to auto-detecting
                // the list of courses it will be grabbing events from.
                $filtercourse = NULL;
            }
            else {
                // Forcibly filter events to include only those from the particular course we are in.
                $filtercourse = array($courseshown => 1);
            }
        }

        // We 'll need this later
        calendar_set_referring_course($courseshown);

        // Be VERY careful with the format for default courses arguments!
        // Correct formatting is [courseid] => 1 to be concise with moodlelib.php functions.

        calendar_set_filters($courses, $group, $user, $filtercourse, $filtercourse, false);
        $events = calendar_get_upcoming($courses, $group, $user, get_user_preferences('calendar_lookahead', CALENDAR_UPCOMING_DAYS), get_user_preferences('calendar_maxevents', CALENDAR_UPCOMING_MAXEVENTS));

        $this->content->text = calendar_get_sideblock_upcoming($events, 'view.php?view=day&amp;course='.$this->course->id.'&amp;');

        if(empty($this->content->text)) {
            $this->content->text = '<div style="font-size: 0.8em; text-align: center;">'.get_string('noupcomingevents', 'calendar').'</div>';
        }

        return $this->content;
    }
}

?>

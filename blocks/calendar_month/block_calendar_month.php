<?PHP //$Id$

class CourseBlock_calendar_month extends MoodleBlock {
    function CourseBlock_calendar_month($course) {
        $this->title = get_string('calendar', 'calendar');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004041000;
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
        $this->content->footer = '';

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

        if($courseshown !== false && is_int($SESSION->cal_show_course) && $SESSION->cal_show_course != $courseshown) {
            // There is a filter in action that shows events from a course other than the current
            // Obviously we have to cut it out
            $SESSION->cal_show_course = true;
        }
       else if($courseshown !== false && is_array($SESSION->cal_show_course) && !in_array($courseshown, $SESSION->cal_show_course)) {
            // Same as above, only there are many courses being shown. Unfortunately, not this one.
            $SESSION->cal_show_course = true;
        }

        // Be VERY careful with the format for default courses arguments!
        // Correct formatting is [courseid] => 1 to be concise with moodlelib.php functions.

        calendar_set_filters($courses, $group, $user, $defaultcourses, $defaultcourses);

        if($courseshown == 1) {
            // For the front page
            $this->content->text .= calendar_overlib_html();
            $this->content->text .= calendar_top_controls('frontpage', array('m' => $_GET['cal_m'], 'y' => $_GET['cal_y']));
            $this->content->text.= calendar_get_mini($courses, $group, $user, $_GET['cal_m'], $_GET['cal_y']);
            // No filters for now
        }
        else {
            // For any other course
            $this->content->text .= calendar_overlib_html();
            $this->content->text .= calendar_top_controls('course', array('id' => $courseshown, 'm' => $_GET['cal_m'], 'y' => $_GET['cal_y']));
            $this->content->text .= calendar_get_mini($courses, $group, $user, $_GET['cal_m'], $_GET['cal_y']);
            $this->content->text .= calendar_filter_controls('course');
        }

        return $this->content;
    }
}

?>

<?PHP //$Id$

class CourseBlock_section_links extends MoodleBlock {

    function CourseBlock_section_links ($course) {
        if ($course->format == 'topics') {
            $this->title = get_string('topics', 'block_section_links');
        }
        else if ($course->format == 'weeks') {
            $this->title = get_string('weeks', 'block_section_links');
        }
        else {
            $this->title = get_string('blockname', 'block_section_links');
        }
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->course = $course;
        $this->version = 2004052800;
    }

    function applicable_formats() {
        return (COURSE_FORMAT_WEEKS | COURSE_FORMAT_TOPICS);
    }

    function get_content() {
        global $CFG, $USER;

        $highlight = 0;

        if($this->content !== NULL) {
            return $this->content;
        }

        if ($this->course->format == 'weeks') {
            $highlight = ceil((time()-$this->course->startdate)/604800);
            $linktext = get_string('jumptocurrentweek', 'block_section_links');
            $sectionname = 'week';
        }
        else if ($this->course->format == 'topics') {
            $highlight = $this->course->marker;
            $linktext = get_string('jumptocurrenttopic', 'block_section_links');
            $sectionname = 'topic';
        }
        $inc = 1;
        if ($this->course->numsections > 22) {
            $inc = 2;
        }
        if ($this->course->numsections > 40) {
            $inc = 5;
        }
        $courseid = $this->course->id;
        if ($display = get_field('course_display', 'display', 'course', $courseid, 'userid', $USER->id)) {
            $link = "$CFG->wwwroot/course/view.php?id=$courseid&amp;$sectionname=";
        } else {
            $link = '#';
        }
        $text = '<font size=-1>';
        for ($i = $inc; $i <= $this->course->numsections; $i += $inc) {
            $isvisible = get_field('course_sections', 'visible', 'course', $this->course->id, 'section', $i);
            if (!$isvisible and !isteacher($this->course->id)) {
                continue;
            }
            $style = ($isvisible) ? '' : ' class="dimmed"';
            if ($i == $highlight) {
                $text .= "<a href=\"$link$i\"$style><b>$i</b></a> ";
            } else {
                $text .= "<a href=\"$link$i\"$style>$i</a> ";
            }
        }
        if ($highlight) {
            $isvisible = get_field('course_sections', 'visible', 'course', $this->course->id, 'section', $highlight);
            if ($isvisible or isteacher($this->course->id)) {
                $style = ($isvisible) ? '' : ' class="dimmed"';
                $text .= "<br><a href=\"$link$highlight\"$style>$linktext</a>";
            }
        }

        $this->content = New stdClass;
        $this->content->footer = '';
        $this->content->text = $text;
        return $this->content;
    }
}
?>

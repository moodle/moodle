<?PHP //$Id$

class block_section_links extends block_base {

    function init() {
        $this->title = get_string('blockname', 'block_section_links');
        $this->version = 2004052800;
    }

    function instance_config($instance) {
        parent::instance_config($instance);
        $course = get_record('course', 'id', $this->instance->pageid);
        if (isset($course->format)) {
            if ($course->format == 'topics') {
                $this->title = get_string('topics', 'block_section_links');
            } else if ($course->format == 'weeks') {
                $this->title = get_string('weeks', 'block_section_links');
            } else {
                $this->title = get_string('blockname', 'block_section_links');
            }
        }
    }

    function applicable_formats() {
        return (array('course-view-weeks' => true, 'course-view-topics' => true));
    }

    function get_content() {
        global $CFG, $USER;

        $highlight = 0;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New stdClass;
        $this->content->footer = '';
        $this->content->text   = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $course = get_record('course', 'id', $this->instance->pageid);

        if ($course->format == 'weeks') {
            $highlight = ceil((time()-$course->startdate)/604800);
            $linktext = get_string('jumptocurrentweek', 'block_section_links');
            $sectionname = 'week';
        }
        else if ($course->format == 'topics') {
            $highlight = $course->marker;
            $linktext = get_string('jumptocurrenttopic', 'block_section_links');
            $sectionname = 'topic';
        }
        $inc = 1;
        if ($course->numsections > 22) {
            $inc = 2;
        }
        if ($course->numsections > 40) {
            $inc = 5;
        }

        if (!empty($USER->id)) {
            $display = get_field('course_display', 'display', 'course', $this->instance->pageid, 'userid', $USER->id);
        }
        if (!empty($display)) {
            $link = $CFG->wwwroot.'/course/view.php?id='.$this->instance->pageid.'&amp;'.$sectionname.'=';
        } else {
            $link = '#';
        }
        $text = '';
        for ($i = $inc; $i <= $course->numsections; $i += $inc) {
            $isvisible = get_field('course_sections', 'visible', 'course', $this->instance->pageid, 'section', $i);
            if (!$isvisible and !isteacher($this->instance->pageid)) {
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
            $isvisible = get_field('course_sections', 'visible', 'course', $this->instance->pageid, 'section', $highlight);
            if ($isvisible or isteacher($this->instance->pageid)) {
                $style = ($isvisible) ? '' : ' class="dimmed"';
                $text .= "<br /><a href=\"$link$highlight\"$style>$linktext</a>";
            }
        }

        $this->content = New stdClass;
        $this->content->footer = '';
        $this->content->text = $text;
        return $this->content;
    }
}
?>

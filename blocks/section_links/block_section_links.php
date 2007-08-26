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
        return (array('course-view-weeks' => true, 'course-view-topics' => true, 'course-edit-weeks' => true, 'course-edit-topics' => true));
    }

    function get_content() {
        global $CFG, $USER, $COURSE;

        $highlight = 0;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->footer = '';
        $this->content->text   = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if ($this->instance->pageid == $COURSE->id) {
            $course = $COURSE;
        } else {
            $course = get_record('course', 'id', $this->instance->pageid);
        }
        $context = get_context_instance(CONTEXT_COURSE, $course->id);

        if ($course->format == 'weeks' or $course->format == 'weekscss') {
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
            $link = '#section-';
        }
        $text = '<ol class="inline-list">';
        for ($i = $inc; $i <= $course->numsections; $i += $inc) {
            $isvisible = get_field('course_sections', 'visible', 'course', $this->instance->pageid, 'section', $i);
            if (!$isvisible and !has_capability('moodle/course:update', $context)) {
                continue;
            }
            $style = ($isvisible) ? '' : ' class="dimmed"';
            if ($i == $highlight) {
                $text .= "<li><a href=\"$link$i\"$style><strong>$i</strong></a></li>\n";
            } else {
                $text .= "<li><a href=\"$link$i\"$style>$i</a></li>\n";
            }
        }
        $text .= '</ol>';
        if ($highlight) {
            $isvisible = get_field('course_sections', 'visible', 'course', $this->instance->pageid, 'section', $highlight);
            if ($isvisible or has_capability('moodle/course:update', $context)) {
                $style = ($isvisible) ? '' : ' class="dimmed"';
                $text .= "\n<a href=\"$link$highlight\"$style>$linktext</a>";
            }
        }

        $this->content->text = $text;
        return $this->content;
    }
}

?>

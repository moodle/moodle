<?php //$Id$

class block_activity_modules extends block_list {
    function init() {
        $this->title = get_string('activities');
        $this->version = 2007101509;
    }

    function get_content() {
        global $CFG, $COURSE;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if ($COURSE->id == $this->instance->pageid) {
            $course = $COURSE;
        } else {
            $course = get_record('course', 'id', $this->instance->pageid);
        }

        if (empty($course)) {
            return '';
        }

        require_once($CFG->dirroot.'/course/lib.php');

        $modinfo = get_fast_modinfo($course);
        $modfullnames = array();

        foreach($modinfo->cms as $cm) {
            if (!$cm->uservisible) {
                continue;
            }
            $modfullnames[$cm->modname] = $cm->modplural;
        }

        asort($modfullnames, SORT_LOCALE_STRING);

        foreach ($modfullnames as $modname => $modfullname) {
            if ($modname != 'label') {
                $this->content->items[] = '<a href="'.$CFG->wwwroot.'/mod/'.$modname.'/index.php?id='.$this->instance->pageid.'">'.$modfullname.'</a>';
                $this->content->icons[] = '<img src="'.$CFG->modpixpath.'/'.$modname.'/icon.gif" class="icon" alt="" />';
            }
        }

        return $this->content;
    }

    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'my' => false, 'admin' => false,
                     'tag' => false);
    }
}

?>

<?PHP //$Id$

class block_latest_activities extends block_base {
    function init() {
        $this->title = get_string('blockname','block_latest_activities');
        $this->version = 2005031400;
    }

    function has_config() {return true;}

    function applicable_formats() {
        return (array('course-view-weeks' => true, 'course-view-topics' => true));
    }

    function get_content() {
        global $CFG, $USER;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        $course = get_record('course', 'id', $this->instance->pageid);

        $activitiestosee = 5; //Activities default
        if (isset($CFG->block_latest_activities_number)) {
            $activitiestosee = $CFG->block_latest_activities_number;
        }

        $activities = get_records_sql("SELECT l.id, l.cmid, l.module, l.time
                                       FROM {$CFG->prefix}course_modules m,
                                            {$CFG->prefix}log l
                                       WHERE l.cmid = m.id AND
                                             l.userid = $USER->id AND
                                             l.course = $course->id AND
                                             m.visible = 1
                                       ORDER BY l.time DESC ".
                                       sql_paging_limit(0,$activitiestosee));

        $modinfo = unserialize($course->modinfo);

        if (!empty($activities) && !empty($modinfo)) {
            foreach ($activities as $activity) {
                $activity->name = urldecode($modinfo[$activity->cmid]->name);
                $activity->timeago = format_time(time() - $activity->time);
                $this->content->text .= '<div style="text-align: left; font-size: 0.75em; padding-top: 5px;">';
                $this->content->text .= '<a href="'.$CFG->wwwroot.'/mod/'.$activity->module.'/view.php?id='.$activity->cmid.'" title="'.$activity->timeago.'">'.$activity->name.'</a>';
                $this->content->text .= '</div>';
            }
        }
        return $this->content;
    }
}

?>

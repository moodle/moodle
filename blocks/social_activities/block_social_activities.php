<?php //$Id$

class CourseBlock_social_activities extends MoodleBlock {
    function CourseBlock_social_activities ($course) {
        $this->title = get_string('blockname','block_social_activities');
        $this->content_type = BLOCK_TYPE_LIST;
        $this->course = $course;
        $this->version = 2004041800;
    }

    function applicable_formats() {
        return COURSE_FORMAT_SOCIAL;
    }

    function get_content() {
        global $USER, $CFG;

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New object;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        // To make our day, we start with an ugly hack
        global $sections, $mods, $modnames;

        $section = $sections[0];
        // That wasn't so bad, was it?

        $groupbuttons = $this->course->groupmode;
        $groupbuttonslink = (!$this->course->groupmodeforce);
        $isteacher = isteacher($this->course->id);
        $isediting = isediting($this->course->id);
        $ismoving = ismoving($this->course->id);
        if ($ismoving) {
            $strmovehere = get_string('movehere');
            $strmovefull = strip_tags(get_string('movefull', '', "'$USER->activitycopyname'"));
            $strcancel= get_string('cancel');
            $stractivityclipboard = $USER->activitycopyname;
        }

        $modinfo = unserialize($this->course->modinfo);
        $editbuttons = '';

        if ($ismoving) {
            $this->content->items[] = '&nbsp;<img align="bottom" src="'.$CFG->pixpath.'/t/move.gif" height="11" width="11">';
            $this->content->icons[] = $USER->activitycopyname.'&nbsp;(<a href="'.$CFG->wwwroot.'/course/mod.php?cancelcopy=true">'.$strcancel.'</a>)';
        }

        if (!empty($section->sequence)) {
            $sectionmods = explode(',', $section->sequence);
            foreach ($sectionmods as $modnumber) {
                if (empty($mods[$modnumber])) {
                    continue;
                }
                $mod = $mods[$modnumber];
                if ($isediting && !$ismoving) {
                    if ($groupbuttons) {
                        if (! $mod->groupmodelink = $groupbuttonslink) {
                            $mod->groupmode = $this->course->groupmode;
                        }

                    } else {
                        $mod->groupmode = false;
                    }
                    $editbuttons = '<br />'.make_editing_buttons($mod, true, true);
                } else {
                    $editbuttons = '';
                }
                if ($mod->visible || $isteacher) {
                    if ($ismoving) {
                        if ($mod->id == $USER->activitycopy) {
                            continue;
                        }
                        $this->content->items[] = '<a title="'.$strmovefull.'" href="'.$CFG->wwwroot.'/course/mod.php?moveto='.$mod->id.'">'.
                            '<img height="16" width="80" src="'.$CFG->pixpath.'/movehere.gif" alt="'.$strmovehere.'" border="0"></a>';
                        $this->content->icons[] = '';
                    }
                    $instancename = urldecode($modinfo[$modnumber]->name);
                    if (!empty($CFG->filterall)) {
                        $instancename = filter_text('<nolink>'.$instancename.'</nolink>', $this->course->id);
                    }
                    $linkcss = $mod->visible ? '' : ' class="dimmed" ';
                    if (!empty($modinfo[$modnumber]->extra)) {
                        $extra = urldecode($modinfo[$modnumber]->extra);
                    } else {
                        $extra = '';
                    }

                    if ($mod->modname == 'label') {
                        $this->content->items[] = format_text($extra, FORMAT_HTML).$editbuttons;
                        $this->content->icons[] = '';
                    } else {
                        $this->content->items[] = '<a title="'.$mod->modfullname.'" '.$linkcss.' '.$extra.
                            ' href="'.$CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$mod->id.'">'.$instancename.'</a>'.$editbuttons;
                        $this->content->icons[] = '<img src="'.$CFG->modpixpath.'/'.$mod->modname.'/icon.gif" height="16" width="16" alt="'.$mod->modfullname.'">';
                    }
                }
            }
        }

        if ($ismoving) {
            $this->content->items[] = '<a title="'.$strmovefull.'" href="'.$CFG->wwwroot.'/course/mod.php?movetosection='.$section->id.'">'.
                                      '<img height="16" width="80" src="'.$CFG->pixpath.'/movehere.gif" alt="'.$strmovehere.'" border="0"></a>';
            $this->content->icons[] = '';
        }

        if ($isediting && $modnames) {
            $this->content->footer = '<div style="text-align: right;">'.
                popup_form($CFG->wwwroot.'/course/mod.php?id='.$this->course->id.'&amp;section='.$section->section.'&amp;add=',
                           $modnames, 'section0', '', get_string('add').'...', 'mods', get_string('activities'), true) . '</div>';
        } else {
            $this->content->footer = '';
        }

        return $this->content;
    }
}

?>

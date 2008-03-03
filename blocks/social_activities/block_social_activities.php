<?php //$Id$

class block_social_activities extends block_list {
    function init(){
        $this->title = get_string('blockname', 'block_social_activities');
        $this->version = 2007101509;
    }

    function applicable_formats() {
        return array('course-view-social' => true);
    }

    function get_content() {
        global $USER, $CFG, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new object();
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }

        if ($COURSE->id == $this->instance->pageid) {
            $course = $COURSE;
        } else {
            $course = get_record('course', 'id', $this->instance->pageid);
        }

        require_once($CFG->dirroot.'/course/lib.php');

        $context = get_context_instance(CONTEXT_COURSE, $course->id);
        $isediting = isediting($this->instance->pageid) && has_capability('moodle/course:manageactivities', $context);
        $modinfo = get_fast_modinfo($course);

/// extra fast view mode
        if (!$isediting) {
            if (!empty($modinfo->sections[0])) {
                foreach($modinfo->sections[0] as $cmid) {
                    $cm = $modinfo->cms[$cmid];
                    if (!$cm->uservisible) {
                        continue;
                    }
                    if ($cm->modname == 'label') {
                        $this->content->items[] = format_text($cm->extra, FORMAT_HTML);
                        $this->content->icons[] = '';
                    } else {
                        $linkcss = $cm->visible ? '' : ' class="dimmed" ';
                        $instancename = format_string($cm->name, true, $course->id);
                        $this->content->items[] = '<a title="'.$cm->modplural.'" '.$linkcss.' '.$cm->extra.
                            ' href="'.$CFG->wwwroot.'/mod/'.$cm->modname.'/view.php?id='.$cm->id.'">'.$instancename.'</a>';
                        //Accessibility: incidental image - should be empty Alt text
                        if (!empty($cm->icon)) {
                            $icon = $CFG->pixpath.'/'.$cm->icon;
                        } else {
                            $icon = $CFG->modpixpath.'/'.$cm->modname.'/icon.gif';
                        }
                        $this->content->icons[] = '<img src="'.$icon.'" class="icon" alt="" />';
                    }
                }
            }
            return $this->content;
        }


/// slow & hacky editing mode
        $ismoving = ismoving($this->instance->pageid);
        $sections = get_all_sections($this->instance->pageid);

        if(!empty($sections) && isset($sections[0])) {
            $section = $sections[0];
        }

        if (!empty($section)) {
            get_all_mods($this->instance->pageid, $mods, $modnames, $modnamesplural, $modnamesused);
        }

        $groupbuttons = $course->groupmode;
        $groupbuttonslink = (!$course->groupmodeforce);

        if ($ismoving) {
            $strmovehere = get_string('movehere');
            $strmovefull = strip_tags(get_string('movefull', '', "'$USER->activitycopyname'"));
            $strcancel= get_string('cancel');
            $stractivityclipboard = $USER->activitycopyname;
        }
    /// Casting $course->modinfo to string prevents one notice when the field is null
        $editbuttons = '';

        if ($ismoving) {
            $this->content->icons[] = '&nbsp;<img align="bottom" src="'.$CFG->pixpath.'/t/move.gif" class="iconsmall" alt="" />';
            $this->content->items[] = $USER->activitycopyname.'&nbsp;(<a href="'.$CFG->wwwroot.'/course/mod.php?cancelcopy=true&amp;sesskey='.$USER->sesskey.'">'.$strcancel.'</a>)';
        }

        if (!empty($section) && !empty($section->sequence)) {
            $sectionmods = explode(',', $section->sequence);
            foreach ($sectionmods as $modnumber) {
                if (empty($mods[$modnumber])) {
                    continue;
                }
                $mod = $mods[$modnumber];
                if (!$ismoving) {
                    if ($groupbuttons) {
                        if (! $mod->groupmodelink = $groupbuttonslink) {
                            $mod->groupmode = $course->groupmode;
                        }

                    } else {
                        $mod->groupmode = false;
                    }
                    $editbuttons = '<br />'.make_editing_buttons($mod, true, true);
                } else {
                    $editbuttons = '';
                }
                if ($mod->visible || has_capability('moodle/course:viewhiddenactivities', $context)) {
                    if ($ismoving) {
                        if ($mod->id == $USER->activitycopy) {
                            continue;
                        }
                        $this->content->items[] = '<a title="'.$strmovefull.'" href="'.$CFG->wwwroot.'/course/mod.php?moveto='.$mod->id.'&amp;sesskey='.$USER->sesskey.'">'.
                            '<img style="height:16px; width:80px; border:0px" src="'.$CFG->pixpath.'/movehere.gif" alt="'.$strmovehere.'" /></a>';
                        $this->content->icons[] = '';
                    }
                    $instancename = $modinfo->cms[$modnumber]->name;
                    $instancename = format_string($instancename, true, $this->instance->pageid);
                    $linkcss = $mod->visible ? '' : ' class="dimmed" ';
                    if (!empty($modinfo->cms[$modnumber]->extra)) {
                        $extra = $modinfo->cms[$modnumber]->extra;
                    } else {
                        $extra = '';
                    }
                    if (!empty($modinfo->cms[$modnumber]->icon)) {
                        $icon = $CFG->pixpath.'/'.$modinfo->cms[$modnumber]->icon;
                    } else {
                        $icon = $CFG->modpixpath.'/'.$mod->modname.'/icon.gif';
                    }

                    if ($mod->modname == 'label') {
                        $this->content->items[] = format_text($extra, FORMAT_HTML).$editbuttons;
                        $this->content->icons[] = '';
                    } else {
                        $this->content->items[] = '<a title="'.$mod->modfullname.'" '.$linkcss.' '.$extra.
                            ' href="'.$CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$mod->id.'">'.$instancename.'</a>'.$editbuttons;
                        //Accessibility: incidental image - should be empty Alt text
                        $this->content->icons[] = '<img src="'.$icon.'" class="icon" alt="" />';
                    }
                }
            }
        }

        if ($ismoving) {
            $this->content->items[] = '<a title="'.$strmovefull.'" href="'.$CFG->wwwroot.'/course/mod.php?movetosection='.$section->id.'&amp;sesskey='.$USER->sesskey.'">'.
                                      '<img style="height:16px; width:80px; border:0px" src="'.$CFG->pixpath.'/movehere.gif" alt="'.$strmovehere.'" /></a>';
            $this->content->icons[] = '';
        }

        if ($modnames) {
            $this->content->footer = print_section_add_menus($course, 0, $modnames, true, true);
        } else {
            $this->content->footer = '';
        }

        return $this->content;
    }
}

?>

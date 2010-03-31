<?php

class block_admin extends block_list {
    function init() {
        $this->title = get_string('administration');
        $this->version = 2007101509;
    }

    function get_content() {
        global $CFG, $USER, $DB, $OUTPUT;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        $course = $this->page->course;

        if (empty($CFG->loginhttps)) {
            $securewwwroot = $CFG->wwwroot;
        } else {
            $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
        }

        $isenrolled = is_enrolled($this->page->context);
        $isviewing = is_viewing($this->page->context);

    /// Course editing on/off
        if ($course->id !== SITEID and has_capability('moodle/course:update', $this->page->context)) {
            $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/edit') . '" class="icon" alt="" />';
            if ($this->page->user_is_editing()) {
                $this->content->items[]='<a href="view.php?id='.$course->id.'&amp;edit=off&amp;sesskey='.sesskey().'">'.get_string('turneditingoff').'</a>';
            } else {
                $this->content->items[]='<a href="view.php?id='.$course->id.'&amp;edit=on&amp;sesskey='.sesskey().'">'.get_string('turneditingon').'</a>';
            }

            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/edit.php?id='.$course->id.'">'.get_string('settings').'</a>';
            $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/settings') . '" class="icon" alt="" />';
        }

    /// Assign roles to the course
        if ($course->id != SITEID) {
            if (has_capability('moodle/role:assign', $this->page->context)) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$this->page->context->id.'">'.get_string('assignroles', 'role').'</a>';
                $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/roles') . '" class="icon" alt="" />';
            } else if (get_overridable_roles($this->page->context, ROLENAME_ORIGINAL)) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/override.php?contextid='.$this->page->context->id.'">'.get_string('overridepermissions', 'role').'</a>';
                $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/roles') . '" class="icon" alt="" />';
            }
        }

    /// View course grades (or just your own grades, same link)
    /// find all accessible reports
        if ($course->id !== SITEID and ($isenrolled or $isviewing)) {
            $reportavailable = false;
            if (has_capability('moodle/grade:viewall', $this->page->context)) {
                $reportavailable = true;
            } else if (!empty($course->showgrades)) {
                if ($reports = get_plugin_list('gradereport')) {     // Get all installed reports
                    arsort($reports); // user is last, we want to test it first
                    foreach ($reports as $plugin => $plugindir) {
                        if (has_capability('gradereport/'.$plugin.':view', $this->page->context)) {
                            //stop when the first visible plugin is found
                            $reportavailable = true;
                            break;
                        }
                    }
                }
            }

            if ($reportavailable) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/grade/report/index.php?id='.$course->id.'">'.get_string('grades').'</a>';
                $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/grades') . '" class="icon" alt="" />';
            }
        }

    /// Course outcomes (to help give it more prominence because it's important)
        if (!empty($CFG->enableoutcomes)) {
            if ($course->id!==SITEID and has_capability('moodle/course:update', $this->page->context)) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/grade/edit/outcome/course.php?id='.$course->id.'">'.get_string('outcomes', 'grades').'</a>';
                $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/outcomes') . '" class="icon" alt="" />';
            }
        }

    /// Manage metacourses
        if ($course->metacourse) {
            if (has_capability('moodle/course:managemetacourse', $this->page->context)) {
                $strchildcourses = get_string('childcourses');
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/importstudents.php?id='.$course->id.'">'.$strchildcourses.'</a>';
                $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/course') . '" class="icon" alt="" />';
            } else if (has_capability('moodle/role:assign', $this->page->context)) {
                $strchildcourses = get_string('childcourses');
                $this->content->items[]='<span class="dimmed_text">'.$strchildcourses.'</span>';
                $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/course') . '" class="icon" alt="" />';
            }
        }


    /// Manage groups in this course
        if (($course->id!==SITEID) && ($course->groupmode || !$course->groupmodeforce) && has_capability('moodle/course:managegroups', $this->page->context)) {
            $strgroups = get_string('groups');
            $this->content->items[]='<a title="'.$strgroups.'" href="'.$CFG->wwwroot.'/group/index.php?id='.$course->id.'">'.$strgroups.'</a>';
            $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/group') . '" class="icon" alt="" />';
        }

    /// Backup this course
        if ($course->id!==SITEID and has_capability('moodle/backup:backupcourse', $this->page->context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/backup/backup.php?id='.$course->id.'">'.get_string('backup').'</a>';
            $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/backup') . '" class="icon" alt="" />';
        }

    /// Restore to this course
        if ($course->id !== SITEID and has_capability('moodle/restore:restorecourse', $this->page->context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/files/index.php?id='.$course->id.'&amp;wdir=/backupdata">'.get_string('restore').'</a>';
            $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/restore') . '" class="icon" alt="" />';
        }

    /// Import data from other courses
        if ($course->id !== SITEID and has_capability('moodle/restore:restoretargetimport', $this->page->context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/import.php?id='.$course->id.'">'.get_string('import').'</a>';
            $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/restore') . '" class="icon" alt="" />';
        }

    /// Reset this course
        if ($course->id!==SITEID and has_capability('moodle/course:reset', $this->page->context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/reset.php?id='.$course->id.'">'.get_string('reset').'</a>';
            $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/return') . '" class="icon" alt="" />';
        }

    /// View course reports
        if ($course->id !== SITEID and has_capability('moodle/site:viewreports', $this->page->context)) { // basic capability for listing of reports
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/report.php?id='.$course->id.'">'.get_string('reports').'</a>';
            $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/stats') . '" class="icon" alt="" />';
        }

    /// Manage questions
        if ($course->id !== SITEID){
            $questionlink = '';
            $questioncaps = array(
                                    'moodle/question:add',
                                    'moodle/question:editmine',
                                    'moodle/question:editall',
                                    'moodle/question:viewmine',
                                    'moodle/question:viewall',
                                    'moodle/question:movemine',
                                    'moodle/question:moveall');
            foreach ($questioncaps as $questioncap){
                if (has_capability($questioncap, $this->page->context)){
                    $questionlink = 'edit.php';
                    break;
                }
            }
            if (!$questionlink && has_capability('moodle/question:managecategory', $this->page->context)) {
               $questionlink = 'category.php';
            }
            if ($questionlink) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/question/'.$questionlink.
                        '?courseid='.$course->id.'">'.get_string('questions', 'quiz').'</a>';
                $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/questions') . '" class="icon" alt="" />';
            }
        }

    /// Repository Instances
        require_once($CFG->dirroot.'/repository/lib.php');
        $editabletypes = repository::get_editable_types($this->page->context);
        if ($course->id !== SITEID && has_capability('moodle/course:update', $this->page->context) && !empty($editabletypes)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/repository/manage_instances.php?contextid='.$this->page->context->id.'">'.get_string('repositories').'</a>';
            $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/repository') . '" alt=""/>';
        }

    /// Manage files
        if ($course->id !== SITEID and has_capability('moodle/course:managefiles', $this->page->context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/files/index.php?id='.$course->id.'">'.get_string('files').'</a>';
            $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/files') . '" class="icon" alt=""/>';
        }

    /// Authorize hooks
        if ($course->enrol == 'authorize' || (empty($course->enrol) && $CFG->enrol == 'authorize') && ($course->id!==SITEID)) {
            require_once($CFG->dirroot.'/enrol/authorize/const.php');
            $paymenturl = '<a href="'.$CFG->wwwroot.'/enrol/authorize/index.php?course='.$course->id.'">'.get_string('payments').'</a> ';
            if (has_capability('enrol/authorize:managepayments', $this->page->context)) {
                if ($cnt = $DB->count_records('enrol_authorize', array('status'=>AN_STATUS_AUTH, 'courseid'=>$course->id))) {
                    $paymenturl .= '<a href="'.$CFG->wwwroot.'/enrol/authorize/index.php?status='.AN_STATUS_AUTH.'&amp;course='.$course->id.'">'.get_string('paymentpending', 'moodle', $cnt).'</a>';
                }
            }
            $this->content->items[] = $paymenturl;
            $this->content->icons[] = '<img src="'.$OUTPUT->pix_url('i/payment') . '" class="icon" alt="" />';
        }

    /// Unenrol link
        if (empty($course->metacourse) && ($course->id!==SITEID)) {
            if ($isenrolled) {
                if (has_capability('moodle/role:unassignself', $this->page->context, NULL, false) and get_user_roles($this->page->context, $USER->id, false)) {  // Have some role
                    $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/unenrol.php?id='.$course->id.'">'.get_string('unenrolme', '', format_string($course->shortname)).'</a>';
                    $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/user') . '" class="icon" alt="" />';
                }
                
            } else if ($isviewing) {
                // inspector, manager, etc. - do not show anything
            } else {
                // access because otherwise they would not get into this course at all
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/enrol.php?id='.$course->id.'">'.get_string('enrolme', '', format_string($course->shortname)).'</a>';
                $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/user') . '" class="icon" alt="" />';
            }
        }

    /// Link to the user own profile if they are enrolled
        if ($isenrolled) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/user/view.php?id='.$USER->id.'&amp;course='.$course->id.'">'.get_string('profile').'</a>';
            $this->content->icons[]='<img src="'.$OUTPUT->pix_url('i/user') . '" alt="" />';
        }

        return $this->content;
    }

    function applicable_formats() {
        return array('course' => true);   // Not needed on site
    }
}



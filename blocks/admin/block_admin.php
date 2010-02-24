<?php //$Id$

class block_admin extends block_list {
    function init() {
        $this->title = get_string('administration');
        $this->version = 2007101509;
    }

    function get_content() {

        global $CFG, $USER, $SITE, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (empty($this->instance->pageid)) { // sticky
            if (!empty($COURSE)) {
                $this->instance->pageid = $COURSE->id;
            }
        }

        if (empty($this->instance)) {
            return $this->content = '';
        } else if ($this->instance->pageid == SITEID) {
            // return $this->content = '';
        }

        if (!empty($this->instance->pageid)) {
            $context = get_context_instance(CONTEXT_COURSE, $this->instance->pageid);
            if ($COURSE->id == $this->instance->pageid) {
                $course = $COURSE;
            } else {
                $course = get_record('course', 'id', $this->instance->pageid);
            }
        } else {
            $context = get_context_instance(CONTEXT_SYSTEM);
            $course = $SITE;
        }

        if (!has_capability('moodle/course:view', $context)) {  // Just return
            return $this->content;
        }

        if (empty($CFG->loginhttps)) {
            $securewwwroot = $CFG->wwwroot;
        } else {
            $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
        }

    /// Course editing on/off

        if ($course->id !== SITEID and has_capability('moodle/course:update', $context)) {
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/edit.gif" class="icon" alt="" />';
            if (isediting($this->instance->pageid)) {
                $this->content->items[]='<a href="view.php?id='.$this->instance->pageid.'&amp;edit=off&amp;sesskey='.sesskey().'">'.get_string('turneditingoff').'</a>';
            } else {
                $this->content->items[]='<a href="view.php?id='.$this->instance->pageid.'&amp;edit=on&amp;sesskey='.sesskey().'">'.get_string('turneditingon').'</a>';
            }

            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/edit.php?id='.$this->instance->pageid.'">'.get_string('settings').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/settings.gif" class="icon" alt="" />';
        }

    /// Assign roles to the course

        if ($course->id != SITEID) {
            if (has_capability('moodle/role:assign', $context)) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/assign.php?contextid='.$context->id.'">'.get_string('assignroles', 'role').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/roles.gif" class="icon" alt="" />';
            } else if (get_overridable_roles($context, 'name', ROLENAME_ORIGINAL)) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/roles/override.php?contextid='.$context->id.'">'.get_string('overridepermissions', 'role').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/roles.gif" class="icon" alt="" />';
            }
        }

    /// View course grades (or just your own grades, same link)
    /// find all accessible reports
        if ($course->id !== SITEID) {
            $reportavailable = false;
            if (has_capability('moodle/grade:viewall', $context)) {
                $reportavailable = true;
            } else if (!empty($course->showgrades)) {
                if ($reports = get_list_of_plugins('grade/report')) {     // Get all installed reports
                    arsort($reports); // user is last, we want to test it first
                    foreach ($reports as $plugin) {
                        if (has_capability('gradereport/'.$plugin.':view', $context)) {
                            //stop when the first visible plugin is found
                            $reportavailable = true;
                            break;
                        }
                    }
                }
            }

            if ($reportavailable) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/grade/report/index.php?id='.$this->instance->pageid.'">'.get_string('grades').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/grades.gif" class="icon" alt="" />';
            }
        }

    /// Course outcomes (to help give it more prominence because it's important)
        if (!empty($CFG->enableoutcomes)) {
            if ($course->id!==SITEID and has_capability('moodle/course:update', $context)) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/grade/edit/outcome/course.php?id='.$this->instance->pageid.'">'.get_string('outcomes', 'grades').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/outcomes.gif" class="icon" alt="" />';
            }
        }

    /// Manage metacourses
        if ($course->metacourse) {
            if (has_capability('moodle/course:managemetacourse', $context)) {
                $strchildcourses = get_string('childcourses');
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/importstudents.php?id='.$this->instance->pageid.'">'.$strchildcourses.'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/course.gif" class="icon" alt="" />';
            } else if (has_capability('moodle/role:assign', $context)) {
                $strchildcourses = get_string('childcourses');
                $this->content->items[]='<span class="dimmed_text">'.$strchildcourses.'</span>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/course.gif" class="icon" alt="" />';
            }
        }


    /// Manage groups in this course

        if (($course->id!==SITEID) && ($course->groupmode || !$course->groupmodeforce) && has_capability('moodle/course:managegroups', $context)) {
            $strgroups = get_string('groups');
            $this->content->items[]='<a title="'.$strgroups.'" href="'.$CFG->wwwroot.'/group/index.php?id='.$this->instance->pageid.'">'.$strgroups.'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/group.gif" class="icon" alt="" />';
        }

    /// Backup this course

        if ($course->id!==SITEID and has_capability('moodle/site:backup', $context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/backup/backup.php?id='.$this->instance->pageid.'">'.get_string('backup').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/backup.gif" class="icon" alt="" />';
        }

    /// Restore to this course
        if ($course->id !== SITEID and has_capability('moodle/site:restore', $context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/files/index.php?id='.$this->instance->pageid.'&amp;wdir=/backupdata">'.get_string('restore').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/restore.gif" class="icon" alt="" />';
        }

    /// Import data from other courses
        if ($course->id !== SITEID and has_capability('moodle/site:import', $context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/import.php?id='.$this->instance->pageid.'">'.get_string('import').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/restore.gif" class="icon" alt="" />';
        }

    /// Reset this course
        if ($course->id!==SITEID and has_capability('moodle/course:reset', $context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/reset.php?id='.$this->instance->pageid.'">'.get_string('reset').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/return.gif" class="icon" alt="" />';
        }

    /// View course reports
        if ($course->id !== SITEID and has_capability('moodle/site:viewreports', $context)) { // basic capability for listing of reports
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/report.php?id='.$this->instance->pageid.'">'.get_string('reports').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/stats.gif" class="icon" alt="" />';
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
                if (has_capability($questioncap, $context)){
                    $questionlink = 'edit.php';
                    break;
                }
            }
            if (!$questionlink && has_capability('moodle/question:managecategory', $context)) {
               $questionlink = 'category.php';
            }
            if ($questionlink) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/question/'.$questionlink.
                        '?courseid='.$this->instance->pageid.'">'.get_string('questions', 'quiz').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/questions.gif" class="icon" alt="" />';
            }
        }


    /// Manage files
        if ($course->id !== SITEID and has_capability('moodle/course:managefiles', $context)) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/files/index.php?id='.$this->instance->pageid.'">'.get_string('files').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/files.gif" class="icon" alt="" />';
        }

    /// Authorize hooks
        if ($course->enrol == 'authorize' || (empty($course->enrol) && $CFG->enrol == 'authorize') && ($course->id!==SITEID)) {
            require_once($CFG->dirroot.'/enrol/authorize/const.php');
            $paymenturl = '<a href="'.$CFG->wwwroot.'/enrol/authorize/index.php?course='.$course->id.'">'.get_string('payments').'</a> ';
            if (has_capability('enrol/authorize:managepayments', $context)) {
                if ($cnt = count_records('enrol_authorize', 'status', AN_STATUS_AUTH, 'courseid', $course->id)) {
                    $paymenturl .= '<a href="'.$CFG->wwwroot.'/enrol/authorize/index.php?status='.AN_STATUS_AUTH.'&amp;course='.$course->id.'">'.get_string('paymentpending', 'moodle', $cnt).'</a>';
                }
            }
            $this->content->items[] = $paymenturl;
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/payment.gif" class="icon" alt="" />';
        }

    /// Unenrol link
        if (empty($course->metacourse) && ($course->id!==SITEID)) {
            if (has_capability('moodle/legacy:guest', $context, NULL, false)) {   // Are a guest now
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/enrol.php?id='.$this->instance->pageid.'">'.get_string('enrolme', '', format_string($course->shortname)).'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" class="icon" alt="" />';
            } else if (has_capability('moodle/role:unassignself', $context, NULL, false) and get_user_roles($context, $USER->id, false)) {  // Have some role
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/unenrol.php?id='.$this->instance->pageid.'">'.get_string('unenrolme', '', format_string($course->shortname)).'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" class="icon" alt="" />';
            }
        }

    /// Link to the user own profile (except guests)
        if (!isguestuser() and isloggedin()) {
            $this->content->items[]='<a href="'.$CFG->wwwroot.'/user/view.php?id='.$USER->id.'&amp;course='.$course->id.'">'.get_string('profile').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" alt="" />';
        }

        return $this->content;
    }

    function applicable_formats() {
        return array('course' => true);   // Not needed on site
    }
}

?>

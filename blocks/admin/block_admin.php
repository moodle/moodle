<?php //$Id$

class block_admin extends block_list {
    function init() {
        $this->title = get_string('administration');
        $this->version = 2004081200;
    }

    function get_content() {

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (empty($this->instance)) {
            $this->content = '';
        } else if ($this->instance->pageid == SITEID) {
            $this->load_content_for_site();
        } else {
            $this->load_content_for_course();
        }

        return $this->content;
    }


    function load_content_for_site() {
        global $CFG, $USER;

        if (isadmin()) {
            $this->content->items[] = '<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/configure.php">'.get_string('configuration').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/admin.gif" alt="" />';

            $this->content->items[] = '<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/users.php">'.get_string('users').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/users.gif" alt="" />';

            $this->content->items[]='<a href="'.$CFG->wwwroot.'/backup/backup.php?id='.SITEID.'">'.get_string('backup').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/backup.gif" alt="" />';

            $this->content->items[]='<a href="'.$CFG->wwwroot.'/files/index.php?id='.SITEID.'&amp;wdir=/backupdata">'.get_string('restore').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/restore.gif" alt="" />';
        }

        if (iscreator()) {
            $this->content->items[] = '<a href="'.$CFG->wwwroot.'/course/index.php?edit=on&amp;sesskey='.sesskey().'">'.get_string('courses').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/course.gif" alt="" />';
        }

        if (isadmin()) {
            $this->content->items[] = '<a href="'.$CFG->wwwroot.'/course/report/log/index.php?id='.SITEID.'">'.get_string('logs').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/log.gif" alt="" />';


            $this->content->items[] = '<a href="'.$CFG->wwwroot.'/admin/report.php">'.get_string('reports').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/stats.gif" alt="" />';

            $this->content->items[] = '<a href="'.$CFG->wwwroot.'/files/index.php?id='.SITEID.'">'.get_string('sitefiles').'</a>';
            $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/files.gif" alt="" />';

            if (file_exists($CFG->dirroot.'/'.$CFG->admin.'/'.$CFG->dbtype)) {
                $this->content->items[] = '<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/'.$CFG->dbtype.'/frame.php">'.get_string('managedatabase').'</a>';
                $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/db.gif" alt="" />';
            }

            if ($CFG->enrol == 'authorize') {
                require_once($CFG->dirroot.'/enrol/authorize/const.php');
                $paymenturl = '<a href="'.$CFG->wwwroot.'/enrol/authorize/index.php">'.get_string('payments').'</a> ';
                if ($cnt = count_records('enrol_authorize', 'status', AN_STATUS_AUTH)) {
                    $paymenturl .= '<a href="'.$CFG->wwwroot.'/enrol/authorize/index.php?status='.AN_STATUS_AUTH.'">'.get_string('paymentpending', 'moodle', $cnt).'</a>';
                }
                $this->content->items[] = $paymenturl;
                $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/payment.gif" alt="" />';
            }

            $this->content->footer = '<a href="'.$CFG->wwwroot.'/'.$CFG->admin.'/">'.get_string('admin').'...</a>';
        }
    }

    function load_content_for_course() {
        global $CFG, $USER;

        require_once($CFG->dirroot.'/mod/forum/lib.php');

        if (isguest()) {
            return $this->content;
        }

        if(empty($CFG->loginhttps)) {
            $securewwwroot = $CFG->wwwroot;
        } else {
            $securewwwroot = str_replace('http:','https:',$CFG->wwwroot);
        }

        $course = get_record('course', 'id', $this->instance->pageid);

        if (isteacher($this->instance->pageid)) {

            $isteacheredit = isteacheredit($this->instance->pageid);

            if ($isteacheredit) {
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/edit.gif" alt="" />';
                if (isediting($this->instance->pageid)) {
                    $this->content->items[]='<a href="view.php?id='.$this->instance->pageid.'&amp;edit=off&amp;sesskey='.sesskey().'">'.get_string('turneditingoff').'</a>';
                } else {
                    $this->content->items[]='<a href="view.php?id='.$this->instance->pageid.'&amp;edit=on&amp;sesskey='.sesskey().'">'.get_string('turneditingon').'</a>';
                }
                
                $this->content->items[]='<a href="edit.php?id='.$this->instance->pageid.'">'.get_string('settings').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/settings.gif" alt="" />';


            $fullname = fullname($USER, true);
            $editmyprofile = '<a title="'.$fullname.'" href="'.$securewwwroot.'/user/edit.php?id='.$USER->id.'&amp;course='.$this->instance->pageid.'">'.get_string('editmyprofile').'</a>';
            if (empty($USER->description)) {
                //Accessibility: replace non-standard <blink> with CSS (<a> makes title visible in IE).
                $text = get_string('profile').' '.get_string('missingdescription');
                $this->content->items[]= $editmyprofile.' <a title="'.$text.'" class="useredit blink">*<span class="accesshide">'.$text.'</span></a>';
            } else {
                $this->content->items[]= $editmyprofile;
            }
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" alt="" />';


                if (iscreator() || !empty($CFG->teacherassignteachers)) {
                    if (!$course->teachers) {
                        $course->teachers = get_string('defaultcourseteachers');
                    }
                    $this->content->items[]='<a href="teacher.php?id='.$this->instance->pageid.'">'.$course->teachers.'</a>';
                    $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/users.gif" alt="" />';
                }

                if (!$course->students) {
                    $course->students = get_string('defaultcoursestudents');
                } 
                if (!$course->metacourse) {
                    $this->content->items[]='<a href="student.php?id='.$this->instance->pageid.'">'.$course->students.'</a>';
                    $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/users.gif" alt="" />';
                } else {
                    $strchildcourses = get_string('childcourses');
                    $this->content->items[]='<a href="importstudents.php?id='.$this->instance->pageid.'">'.$strchildcourses.'</a>';
                    $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/course.gif" alt="" />';
                }
                if ($course->groupmode || !$course->groupmodeforce) {
                    $strgroups = get_string('groups');
                    $this->content->items[]='<a title="'.$strgroups.'" href="'.$CFG->wwwroot.'/course/groups.php?id='.$this->instance->pageid.'">'.$strgroups.'</a>';
                    $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/group.gif" alt="" />';
                }

                $this->content->items[]='<a href="'.$CFG->wwwroot.'/backup/backup.php?id='.$this->instance->pageid.'">'.get_string('backup').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/backup.gif" alt="" />';

                $this->content->items[]='<a href="'.$CFG->wwwroot.'/files/index.php?id='.$this->instance->pageid.'&amp;wdir=/backupdata">'.get_string('restore').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/restore.gif" alt="" />';

                $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/import.php?id='.$this->instance->pageid.'">'.get_string('import').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/restore.gif" alt="" />';
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/reset.php?id='.$this->instance->pageid.'">'.get_string('reset').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/return.gif" alt="" />';
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/course/report.php?id='.$this->instance->pageid.'">'.get_string('reports').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/stats.gif" alt="" />';

                $this->content->items[]='<a href="'.$CFG->wwwroot.'/question/edit.php?courseid='.$this->instance->pageid.'&amp;clean=true">'.get_string('questions', 'quiz').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/questions.gif" alt="" />';

                $this->content->items[]='<a href="scales.php?id='.$this->instance->pageid.'">'.get_string('scales').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/scales.gif" alt="" />';

            }

            $this->content->items[]='<a href="'.$CFG->wwwroot.'/grade/index.php?id='.$this->instance->pageid.'">'.get_string('grades').'</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/grades.gif" alt="" />';

            if ($isteacheredit) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/files/index.php?id='.$this->instance->pageid.'">'.get_string('files').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/files.gif" alt="" />';
            }

            if ($course->enrol == 'authorize' || (empty($course->enrol) && $CFG->enrol == 'authorize')) {
                require_once($CFG->dirroot.'/enrol/authorize/const.php');
                $paymenturl = '<a href="'.$CFG->wwwroot.'/enrol/authorize/index.php?course='.$course->id.'">'.get_string('payments').'</a> ';
                if ($cnt = count_records('enrol_authorize', 'status', AN_STATUS_AUTH, 'courseid', $course->id)) {
                    $paymenturl .= '<a href="'.$CFG->wwwroot.'/enrol/authorize/index.php?status='.AN_STATUS_AUTH.'&amp;course='.$course->id.'">'.get_string('paymentpending', 'moodle', $cnt).'</a>';
                }
                $this->content->items[] = $paymenturl;
                $this->content->icons[] = '<img src="'.$CFG->pixpath.'/i/payment.gif" alt="" />';
            }

            $lang = str_replace('_utf8', '', current_language());

            $this->content->items[]='<a href="http://docs.moodle.org/'.$lang.'/Teacher_documentation">'.get_string('help').'</a>';
            $this->content->icons[]='<img src="'.$CFG->modpixpath.'/resource/icon.gif" alt="" />';

            if ($teacherforum = forum_get_course_forum($this->instance->pageid, 'teacher')) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/mod/forum/view.php?f='.$teacherforum->id.'">'.get_string('nameteacher', 'forum').'</a>';
                $this->content->icons[]='<img src="'.$CFG->modpixpath.'/forum/icon.gif" alt="" />';
            }

        } else if (!isguest()) {  // Students menu

            if ($course->showgrades) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/grade/index.php?id='.$this->instance->pageid.'">'.get_string('grades').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/grades.gif" alt="" />';
            }
            if ($course->showreports) {
                $this->content->items[]='<a href="user.php?id='.$this->instance->pageid.'&amp;user='.$USER->id.'">'.get_string('activityreport').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/report.gif" alt="" />';
            }

            $fullname = fullname($USER, true);
            $editmyprofile = '<a title="'.$fullname.'" href="'.$securewwwroot.'/user/edit.php?id='.
                             $USER->id.'&amp;course='.$this->instance->pageid.'">'.get_string('editmyprofile').'</a>';
            if (empty($USER->description)) {
                //Accessibility: replace non-standard <blink> with CSS (<a> makes title visible in IE).
                $text = get_string('profile').' '.get_string('missingdescription');
                $this->content->items[]= $editmyprofile.' <a title="'.$text.'" class="useredit blink">*<span class="accesshide">'.$text.'</span></a>';
            } else {
                $this->content->items[]= $editmyprofile;
            }
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" alt="" />';

            if (is_internal_auth() && !is_restricted_user($USER->username)) {
                $this->content->items[]='<a href="'.$securewwwroot.'/login/change_password.php?id='.$this->instance->pageid.'">'.get_string('changepassword').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" alt="" />';
            } else if ($CFG->changepassword && !is_restricted_user($USER->username)) {
                $this->content->items[]='<a href="'.$CFG->changepassword.'">'.get_string('changepassword').'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" alt="" />';
            }
            if ($CFG->allowunenroll && !$course->metacourse) {
                $this->content->items[]='<a href="unenrol.php?id='.$this->instance->pageid.'">'.get_string('unenrolme', '', $course->shortname).'</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" alt="" />';
            }
        }
    }

    function applicable_formats() {
        return array('all' => true, 'mod' => false, 'my' => false);
    }
}

?>

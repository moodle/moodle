<?PHP //$Id$

class CourseBlock_admin extends MoodleBlock {
    function CourseBlock_admin($course) {
        $this->title = get_string('administration');
        $this->content_type = BLOCK_TYPE_LIST;
        $this->course = $course;
        $this->version = 2004041000;
    }
    function get_content() {
        global $USER, $CFG, $THEME;

        require_once($CFG->dirroot.'/mod/forum/lib.php');

        if($this->content !== NULL) {
            return $this->content;
        }

        $this->content = New object;
        $this->content->items = array();
        $this->content->icons = array();
        $this->content->footer = '';

        if (isguest()) {
            return $this->content;
        }

        if (isteacher($this->course->id)) {

            $isteacheredit = isteacheredit($this->course->id);

            if ($isteacheredit) {
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/edit.gif" height="16" width="16" alt="">';
                if (isediting($this->course->id)) {
                    $this->content->items[]='<a href="view.php?id='.$this->course->id.'&amp;edit=off">'.get_string('turneditingoff').'</a>';
                } else {
                    $this->content->items[]='<a href="view.php?id='.$this->course->id.'&amp;edit=on">'.get_string('turneditingon').'</a>';
                }
                $this->content->items[]='<a href="edit.php?id='.$this->course->id.'">'.get_string('settings').'...</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/settings.gif" height="16" width="16" alt="">';

                if (iscreator() or !empty($CFG->teacherassignteachers)) {
                    if (!$this->course->teachers) {
                        $this->course->teachers = get_string('defaultcourseteachers');
                    }
                    $this->content->items[]='<a href="teacher.php?id='.$this->course->id.'">'.$this->course->teachers.'...</a>';
                    $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/users.gif" height="16" width="16" alt="">';
                }

                if (!$this->course->students) {
                    $this->course->students = get_string('defaultcoursestudents');
                }
                $this->content->items[]='<a href="student.php?id='.$this->course->id.'">'.$this->course->students.'...</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/users.gif" height="16" width="16" alt="">';

                $this->content->items[]='<a href="'.$CFG->wwwroot.'/backup/backup.php?id='.$this->course->id.'">'.get_string('backup').'...</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/backup.gif" height="16" width="16" alt="">';

                $this->content->items[]='<a href="'.$CFG->wwwroot.'/files/index.php?id='.$this->course->id.'&amp;wdir=/backupdata">'.get_string('restore').'...</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/restore.gif" height="16" width="16" alt="">';
                $this->content->items[]='<a href="scales.php?id='.$this->course->id.'">'.get_string('scales').'...</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/scales.gif" height="16" width="16" alt="">';
            }

            $this->content->items[]='<a href="grades.php?id='.$this->course->id.'">'.get_string('grades').'...</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/grades.gif" height="16" width="16" alt="">';

            $this->content->items[]='<a href="log.php?id='.$this->course->id.'">'.get_string('logs').'...</a>';
            $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/log.gif" height="16" width="16" alt="">';

            if ($isteacheredit) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/files/index.php?id='.$this->course->id.'">'.get_string('files').'...</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/files.gif" height="16" width="16" alt="">';
            }

            $this->content->items[]='<a href="'.$CFG->wwwroot.'/doc/view.php?id='.$this->course->id.'&amp;file=teacher.html">'.get_string('help').'...</a>';
            $this->content->icons[]='<img src="'.$CFG->modpixpath.'/resource/icon.gif" height="16" width="16" alt="">';

            if ($teacherforum = forum_get_course_forum($this->course->id, 'teacher')) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/mod/forum/view.php?f='.$teacherforum->id.'">'.get_string('nameteacher', 'forum').'</a>';
                $this->content->icons[]='<img src="'.$CFG->modpixpath.'/forum/icon.gif" height="16" width="16" alt="">';
            }

        } else if (!isguest()) {  // Students menu
            if ($this->course->showgrades) {
                $this->content->items[]='<a href="grade.php?id='.$this->course->id.'">'.get_string('grades').'...</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/grades.gif" height="16" width="16" alt="">';
            }
            if ($this->course->showreports) {
                $this->content->items[]='<a href="user.php?id='.$this->course->id.'&amp;user='.$USER->id.'">'.get_string('activityreport').'...</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/report.gif" height="16" width="16" alt="">';
            }
            if (is_internal_auth()) {
                $this->content->items[]='<a href="'.$CFG->wwwroot.'/login/change_password.php?id='.$this->course->id.'">'.get_string('changepassword').'...</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" height="16" width="16" alt="">';
            } else if ($CFG->changepassword) {
                $this->content->items[]='<a href="'.$CFG->changepassword.'">'.get_string('changepassword').'...</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" height="16" width="16" alt="">';
            }
            if ($CFG->allowunenroll) {
                $this->content->items[]='<a href="unenrol.php?id='.$this->course->id.'">'.get_string('unenrolme', '', $this->course->shortname).'...</a>';
                $this->content->icons[]='<img src="'.$CFG->pixpath.'/i/user.gif" height="16" width="16" alt="">';
            }
        }

        return $this->content;
    }
}

?>

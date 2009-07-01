<?php //$Id$

require_once($CFG->dirroot .'/blog/lib.php');
require_once($CFG->dirroot .'/course/lib.php');

class block_blog_menu extends block_base {

    function init() {
        $this->title = get_string('blockmenutitle', 'blog');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2007101509;
    }

    function get_content() {
        global $CFG, $USER, $COURSE, $DB, $PAGE;

        if (empty($CFG->bloglevel)) {
            $this->content->text = '';
            return $this->content;
        }

        // don't display menu block if block is set at site level, and user is not logged in
        if ($CFG->bloglevel < BLOG_GLOBAL_LEVEL && !(isloggedin() && !isguest())) {
            $this->content->text = '';
            return $this->content;
        }

        if (!isset($userBlog)) {
            $userBlog ->userid = 0;
        }

        if (!empty($USER->id)) {
            $userBlog->userid = $USER->id;
        }   //what is $userBlog anyway
        if($this->content !== NULL) {
            return $this->content;
        }

        $output = '';

        $this->content = new stdClass;
        $this->content->footer = '';

        if (empty($this->instance)) {
            // Either we're being asked for content without
            // an associated instance of the Blog module has never been installed.
            $this->content->text = $output;
            return $this->content;
        }
        //discover context for pre-loaded associations
        $basefilters = array();
        if(!empty($COURSE)) $courseid = $COURSE->id;
        switch($PAGE->pagetype) {
            case PAGE_COURSE_VIEW:
                $courseid = $PAGE->courserecord->id;
                $basefilters['courseid'] = $courseid;
                break;
            case PAGE_BLOG_VIEW:
                $basefilters = $PAGE->filters;
                if(!empty($PAGE->filters['course']))
                    $courseid = $PAGE->filters['course'];
                if(!empty($PAGE->filters['mod']))
                    $modid = $PAGE->filters['mod'];
                if(!empty($PAGE->filters['user']))
                    $userid = $PAGE->filters['user'];
                if(!empty($PAGE->filters['group']))
                    $groupid = $PAGE->filters['group'];
                if(!empty($PAGE->filters['tag']))
                    $tagid = $PAGE->filters['tag'];
                break;
        }

            $addentrylink = '';
        $blogprefslink = '';
        $myviewlink = '';
        $siteviewlink = '';
        $courseviewlink = '';
        $modviewlink = '';
        $groupviewlink = '';
        $userviewlink = '';
        $tagviewlink = '';
        $coursepopuplink = '';
        $modpopuplink = '';
        $grouppopuplink = '';
        $userspopuplink = '';

        //create basic blog preference links
        $coursearg = '';
            $sitecontext = get_context_instance(CONTEXT_SYSTEM);

            if ($this->page->course->id != SITEID) {
                $incoursecontext = true;
                $curcontext = get_context_instance(CONTEXT_COURSE, $this->page->course->id);
            } else {
                $incoursecontext = false;
                $curcontext = $sitecontext;
            }
            $canviewblogs = has_capability('moodle/blog:view', $curcontext);
            if ( (isloggedin() && !isguest()) && $incoursecontext
                    && $CFG->bloglevel >= BLOG_SITE_LEVEL && $canviewblogs) {

                $coursearg = '&amp;courseid='.$this->page->course->id;
                if(!empty($modid)) $coursearg .= '&amp;modid='.$modid;

                // a course is specified

                $courseviewlink = '<li><a href="'. $CFG->wwwroot .'/blog/index.php?filtertype=course&amp;filterselect='. $this->page->course->id .'">';
                $courseviewlink .= get_string('viewcourseentries', 'blog') ."</a></li>\n";
            }
            $blogmodon = false;
            if ( (isloggedin() && !isguest())
                        && (!$blogmodon || ($blogmodon && $coursearg != ''))
                        && $CFG->bloglevel >= BLOG_USER_LEVEL ) {
            // create the Add entry link
                if (has_capability('moodle/blog:create', $sitecontext)) {
                    $addentrylink = '<li><a href="'. $CFG->wwwroot. '/blog/edit.php?action=add'
                                   .$coursearg.'">'.get_string('addnewentry', 'blog') ."</a></li>\n";
                }
            // create the link to manage blog prefs
            $blogprefslink = '<li><a href="'. $CFG->wwwroot. '/blog/preferences.php?userid='.
                                 $userBlog->userid . $coursearg .'">'.
                                 get_string('blogpreferences', 'blog')."</a></li>\n";
            // create the View my entries link
            $myviewlink = '<li><a href="'.blog_get_blogs_url(array('user'=>$USER->id)).'">'.
                          //'<img src="'.$CFG->pixpath.'/i/user.gif" class="icon" alt="" />'.fullname($USER).
                          get_string('viewmyentries', 'blog').
                          "</a></li>\n";
            }

        // create the View site entries link
            if ($CFG->bloglevel >= BLOG_SITE_LEVEL && $canviewblogs) {
            $siteviewlink .= '<li><a href="'.blog_get_blogs_url(array()).'">'.
                             get_string('viewsiteentries', 'blog').
                             //$DB->get_field('course', 'shortname', array('format'=>'site')).
                             "</a></li>\n";
            }

        //create 'view blogs for course' link
        if($incoursecontext and (!empty($modid) or !empty($userid) or !empty($tagid) or !empty($groupid) or
                                 $PAGE->pagetype == PAGE_COURSE_VIEW)
           and $CFG->bloglevel >= BLOG_SITE_LEVEL and $canviewblogs) {
            $courseviewlink = '<li><a href="'. blog_get_blogs_url(array('course'=>$courseid)) .'">'
                             .'<img src="'.$CFG->pixpath.'/i/course.gif" class="icon" alt="" />'.
                              $DB->get_field('course', 'shortname', array('id'=>$courseid)) ."</a></li>\n";
        }

        //create 'view blogs for user' link
        if(!empty($userid) and $userid != $USER->id and (!empty($modid) or !empty($courseid) or !empty($tagid) or !empty($groupid))
           and  $canviewblogs) {
            $userviewlink = '<li><a href="'. blog_get_blogs_url(array('user'=>$userid)) .'">'.
                            '<img src="'.$CFG->pixpath.'/i/user.gif" class="icon" alt="" />'.
                            $DB->get_field('user', 'username', array('id'=>$userid)).
                            "</a></li>\n";
            }

        //create 'view blogs for mod' link
        if(!empty($modid) and (!empty($groupid) or !empty($userid) or !empty($tagid))
           and $CFG->bloglevel >= BLOG_SITE_LEVEL and $canviewblogs) {
           $filtercontext = get_context_instance(CONTEXT_MODULE, $modid);
           $modinfo = $DB->get_record('course_modules', array('id' => $filtercontext->instanceid));
           $modname = $DB->get_field('modules', 'name', array('id' => $modinfo->module));
           $modviewlink = '<li><a href="'. blog_get_blogs_url(array('mod'=>$modid)) .'">'.
                          '<img src="'.$CFG->wwwroot.'/mod/'.$modname.'/icon.gif" border=0 alt="">'.
                          $DB->get_field($modname, 'name', array('id' => $modinfo->instance)).
                          "</a></li>\n";
        }

        //create 'view blogs for group' link
        if(!empty($groupid) and (!empty($modid) or !empty($tagid) or !empty($userid))
           and $CFG->bloglevel >= BLOG_SITE_LEVEL and $canviewblogs) {
            $groupviewlink = '<li><a href="'. blog_get_blogs_url(array('group'=>$groupid)) .'">'.
                             '<img src="'.$CFG->pixpath.'/i/group.gif" class="icon" alt="" />'.
                             $DB->get_field('groups', 'name', array('id'=>$groupid)) ."</a></li>\n";
        }

        //create 'view blogs for tag' link
        if(!empty($tagid) && (!empty($modid) or !empty($userid) or !empty($courseid) or !empty($groupid)) and $canviewblogs) {
            $tagviewlink = '<li>'.get_string('tag', 'tag').': <a href="'. blog_get_blogs_url(array('tag'=>$tagid)) .'">'.
                           $DB->get_field('tag', 'name', array('id'=>$tagid))."</a></li>\n";
        }

        //create 'view blogs for this site's courses' link
        if($canviewblogs and $CFG->bloglevel >= BLOG_SITE_LEVEL) {
            $courseoptions = array();
            if(!empty($courseid)) {
                if($courseid != SITEID) {
                    $newfilters = $basefilters;
                    unset($newfilters['course']);
                    unset($newfilters['mod']);
                    unset($newfilters['group']);
                    $courseoptions = array(blog_get_blogs_url($newfilters) => 'none');
                }
            }

            foreach($DB->get_records('course', array()) as $course) {
                if($course->id != SITEID) {
                    $newfilters = $basefilters;
                    if(!empty($courseid)) {
                        if($course->id != $courseid) {
                            unset($newfilters['mod']);
                        }
                    }
                    $newfilters['course'] = $course->id;
                    $courseoptions[blog_get_blogs_url($newfilters)] = $course->shortname;
                }
            }
            $coursepopuplink = '<li>'.popup_form('', $courseoptions, 'view_course_blogs', blog_get_blogs_url($basefilters),
                                                 get_string('course'),'', '', true) . "</li>\n";
        }

        //create 'view blogs for this course's mods' link
        if(!empty($courseid) and $canviewblogs and $CFG->bloglevel >= BLOG_SITE_LEVEL) {
            $modnames = array();
            $modnamesplural = array();
            $modnamesused = array();
            $modoptions = array();
            if(!empty($modid)) {
                $newfilters = $basefilters;
                unset($newfilters['mod']);
                $modoptions = array(blog_get_blogs_url($newfilters) => 'none');
            }
            get_all_mods($courseid, $mods, $modnames, $modnamesplural, $modnamesused);
            foreach($mods as $i => $mod) {
                $newfilters = $basefilters;
                $newfilters['mod'] = $mod->id;
                $modoptions[blog_get_blogs_url($newfilters)] = $DB->get_field($mod->modname, 'name', array('id' => $mod->instance));
            }
            $modpopuplink = '<li>'.popup_form('', $modoptions, 'view_mod_blogs', blog_get_blogs_url($basefilters),
                                              get_string('resource').'/'.get_string('activity'),
                                              '', '', true)."</li>\n";
        }

        //create 'view blogs for this course's groups link
        if($incoursecontext and $canviewblogs and $CFG->bloglevel >= BLOG_SITE_LEVEL) {
            $groupoptions = array();
            if(!empty($groupid)) {
                $newfilters = $basefilters;
                unset($newfilters['group']);
                $groupoptions = array(blog_get_blogs_url($newfilters) => 'none');
            }

            foreach($DB->get_records('groups', array('courseid'=>$courseid)) as $group) {
                $newfilters = $basefilters;
                $newfilters['group'] = $group->id;
                $groupoptions[blog_get_blogs_url($newfilters)] = $group->name;
            }
            $grouppopuplink = '<li>'.popup_form('', $groupoptions, 'view_group_blogs', blog_get_blogs_url($basefilters),
                                                get_string('group'),'', '', true)."</li>\n";
        }

        //create 'view blogs for this course/group's users link
        if(!empty($courseid) and $canviewblogs and $CFG->bloglevel >= BLOG_SITE_LEVEL) {
            $useroptions = array();
            if(!empty($userid)) {
                $newfilters = $basefilters;
                unset($newfilters['user']);
                $useroptions = array(blog_get_blogs_url($newfilters) => 'none');
            }
            if(!empty($groupid)) {
                $members = $DB->get_records('groups_members', array('groupid'=>$groupid));
            } else {
                $coursecontext = get_context_instance(CONTEXT_COURSE, $courseid);
                $members = $DB->get_records('role_assignments', array('contextid'=>$coursecontext->id));
            }
            foreach($members as $member) {
                $newfilters = $basefilters;
                $newfilters['user'] = $member->userid;
                $useroptions[blog_get_blogs_url($newfilters)] = $DB->get_field('user', 'username', array('id'=>$member->userid));
            }
            $userspopuplink = '<li>'.popup_form('', $useroptions, 'view_user_blogs', blog_get_blogs_url($basefilters),
                                                get_string('user'),'', '', true)."</li>\n";

            }


        $this->content->text = '<ul class="list">' . $addentrylink.$blogprefslink.$myviewlink.$siteviewlink;
        if($courseviewlink or $modviewlink or $groupviewlink or $userviewlink or $tagviewlink) {
            $this->content->text .= '<ul class="list">'.get_string('viewblogsfor', 'blog') .
                                    $courseviewlink.$modviewlink.$groupviewlink.$userviewlink.$tagviewlink.'</ul>';
        }
        if($PAGE->pagetype != PAGE_COURSE_VIEW and ($coursepopuplink or $modpopuplink or $grouppopuplink or $userspopuplink)) {
            $this->content->text .= '<ul class="list">'.get_string('filterblogsby', 'blog') .
                                    $coursepopuplink.$modpopuplink.$grouppopuplink.$userspopuplink.'</ul>';
        }
        $this->content->text .= "</ul>\n";

        return $this->content;
    }
}

?>

<?php //$Id$

require_once($CFG->dirroot .'/blog/lib.php');

class block_blog_menu extends block_base {
    
    function init() {
        $this->title = get_string('blockmenutitle', 'blog');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2004112000;
    }
    
    function get_content() {
        global $CFG, $course;

        if (!isset($course)) {
            $course = SITEID;
        }

        if ($CFG->bloglevel < BLOG_USER_LEVEL) {
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

        global $CFG, $USER, $course;
        if (!empty($USER->id)) {
            $userBlog->userid = $USER->id;
        }   //what is $userBlog anyway
        if($this->content !== NULL) {
            return $this->content;
        }

        $output = '';

        $this->content = new stdClass;
        $this->content->footer = '';
        if (empty($this->instance) /*|| empty($CFG->blog_version)*/) {
            // Either we're being asked for content without
            // an associated instance of the Blog module has never been installed.
            $this->content->text = $output;
            return $this->content;
        }

        //if ( blog_isLoggedIn() && !isguest() ) {
            $courseviewlink = '';
            $addentrylink = '';
                            
            $coursearg = '';
            if((isloggedin() && !isguest()) && isset($course) && isset($course->id) && $course->id !=0 && $course->id!=SITEID && $CFG->bloglevel >= BLOG_COURSE_LEVEL) {
                $coursearg = '&amp;courseid='. $course->id;
                // a course is specified
                
                $courseviewlink = '<a href="'. $CFG->wwwroot .'/blog/index.php?filtertype=course&amp;filterselect='. $course->id .'">';
                $courseviewlink .= get_string('viewcourseentries', 'blog') .'</a><br />';
            }
                
            $blogmodon = false;

                if ((isloggedin() && !isguest()) && (isadmin() || !$blogmodon || ($blogmodon && $coursearg != '')) && $CFG->bloglevel >= BLOG_USER_LEVEL) {

                    // show Add entry link - user is not admin, moderation is off, or moderation is on and the user is viewing the block within the context of a course
                    $addentrylink = '<a href="'. $CFG->wwwroot. '/blog/edit.php?userid='. $userBlog->userid . $coursearg .'">'. get_string('addnewentry', 'blog') .'</a><br />';

                    // show View my entries link
                    $addentrylink .= '<a href="'. $CFG->wwwroot .'/blog/index.php?userid='. $userBlog->userid.'">';
                    $addentrylink .= get_string('viewmyentries', 'blog') .'</a><br />';
                    // show link to manage blog prefs
                    $addentrylink .= '<a href="'. $CFG->wwwroot. '/blog/preferences.php?userid='. $userBlog->userid . $coursearg .'">'. get_string('blogpreferences', 'blog') .'</a><br />';

                    $output = $addentrylink;
                    $output .= $courseviewlink;

            }

            // show View site entries link
            if ($CFG->bloglevel >= BLOG_SITE_LEVEL) {
                $output .= '<a href="'. $CFG->wwwroot .'/blog/index.php?filtertype=site&amp;">';
                $output .= get_string('viewsiteentries', 'blog') .'</a><br />';
            }
            
            if (isloggedin() && (!isguest())) {
                $output .= link_to_popup_window("/blog/tags.php",'popup',get_string('tagmanagement'), 400, 500, 'Popup window', 'none', true);
            }
            
            // show Help with blogging link
            //$output .= '<a href="'. $CFG->wwwroot .'/help.php?module=blog&amp;file=user.html">';
            //$output .= get_string('helpblogging', 'blog') .'</a>';
        //} else {
        //    $output = ''; //guest users and users who are not logged in do not get menus
        //}

        $this->content->text = $output;
        return $this->content;
    }
}
?>

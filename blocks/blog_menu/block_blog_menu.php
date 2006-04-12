<?php //$Id$

class block_blog_menu extends block_base {
    
    function init() {
        $this->title = get_string('blockmenutitle', 'blog');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2004112000;
    }
    
    function get_content() {
        global $CFG, $course;
        
        if ($CFG->bloglevel < 1) {
            $this->content->text = '';
            return $this->content;
        }
        
        if ($CFG->bloglevel < 5 && !isstudent($course->id) && !isteacher($course->id)) {
            $this->content->text = '';
            return $this->content;
        }

        if (!isset($userBlog)) {
            $userBlog ->userid = 0;
        }

        global $CFG, $USER, $course;
        if (isset($USER->id)) {
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
        
        require_once($CFG->dirroot .'/blog/lib.php');

        //if ( blog_isLoggedIn() && !isguest() ) {
            $courseviewlink = '';
            $addentrylink = '';
                            
            $coursearg = '';
            if(blog_isLoggedIn() && isset($course) && isset($course->id) && $course->id !=0 && $course->id!=SITEID && $CFG->bloglevel >=3 ) {
                $coursearg = '&amp;courseid='. $course->id;
                // a course is specified
                
                $courseviewlink = '<a href="'. $CFG->wwwroot .'/blog/index.php?filtertype=course&amp;filterselect='. $course->id .'">';
                $courseviewlink .= get_string('viewcourseentries', 'blog') .'</a><br />';
            }
                
            $blogmodon = false;

                if (blog_isLoggedIn() && (isadmin() || !$blogmodon || ($blogmodon && $coursearg != '')) && $CFG->bloglevel >= 1) {

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
            if ($CFG->bloglevel >= 4) {
                $output .= '<a href="'. $CFG->wwwroot .'/blog/index.php?filtertype=site&amp;">';
                $output .= get_string('viewsiteentries', 'blog') .'</a><br />';
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

<?php //$Id$

require_once($CFG->dirroot .'/blog/lib.php');

class block_blog_menu extends block_base {

    function init() {
        $this->title = get_string('blockmenutitle', 'blog');
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->version = 2007101509;
    }

    function get_content() {
        global $CFG, $USER, $COURSE;

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

            $sitecontext = get_context_instance(CONTEXT_SYSTEM);

            if ($COURSE->id != SITEID) {

                $incoursecontext = true;
                $curcontext = get_context_instance(CONTEXT_COURSE, $COURSE->id);
            } else {
                $incoursecontext = false;
                $curcontext = $sitecontext;
            }

            $canviewblogs = has_capability('moodle/blog:view', $curcontext);

            /// Accessibility: markup as a list.

            if ( (isloggedin() && !isguest()) && $incoursecontext
                    && $CFG->bloglevel >= BLOG_COURSE_LEVEL && $canviewblogs) {

                $coursearg = '&amp;courseid='.$COURSE->id;
                // a course is specified

                $courseviewlink = '<li><a href="'. $CFG->wwwroot .'/blog/index.php?filtertype=course&amp;filterselect='. $COURSE->id .'">';
                $courseviewlink .= get_string('viewcourseentries', 'blog') ."</a></li>\n";
            }

            $blogmodon = false;

            if ( (isloggedin() && !isguest())
                        && (!$blogmodon || ($blogmodon && $coursearg != ''))
                        && $CFG->bloglevel >= BLOG_USER_LEVEL ) {

                // show Add entry link
                if (has_capability('moodle/blog:create', $sitecontext)) {
                    $addentrylink = '<li><a href="'. $CFG->wwwroot. '/blog/edit.php?action=add'
                                   .$coursearg.'">'.get_string('addnewentry', 'blog') ."</a></li>\n";
                }
                // show View my entries link
                $addentrylink .= '<li><a href="'. $CFG->wwwroot .'/blog/index.php?userid='.
                                 $userBlog->userid.'">'.get_string('viewmyentries', 'blog').
                                 "</a></li>\n";

                // show link to manage blog prefs
                $addentrylink .= '<li><a href="'. $CFG->wwwroot. '/blog/preferences.php?userid='.
                                 $userBlog->userid . $coursearg .'">'.
                                 get_string('blogpreferences', 'blog')."</a></li>\n";

                $output = $addentrylink;
                $output .= $courseviewlink;
            }

            // show View site entries link
            if ($CFG->bloglevel >= BLOG_SITE_LEVEL && $canviewblogs) {
                $output .= '<li><a href="'. $CFG->wwwroot .'/blog/index.php?filtertype=site&amp;">';
                $output .= get_string('viewsiteentries', 'blog')."</a></li>\n";
            }

            // took out tag management interface/link, should use tag/manage.php

            // show Help with blogging link
            //$output .= '<li><a href="'. $CFG->wwwroot .'/help.php?module=blog&amp;file=user.html">';
            //$output .= get_string('helpblogging', 'blog') ."</a></li>\n";
        //} else {
        //    $output = ''; //guest users and users who are not logged in do not get menus
        //}

        $this->content->text = '<ul class="list">'. $output ."</ul>\n";
        return $this->content;
    }
}

?>

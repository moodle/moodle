<?PHP //$Id$

class block_news_items extends block_base {
    function init() {
        $this->title = get_string('latestnews');
        $this->version = 2005030800;
    }

    function get_content() {
        global $CFG;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }


        $course = get_record('course', 'id', $this->instance->pageid);

        if ($course->newsitems) {   // Create a nice listing of recent postings

            require_once($CFG->dirroot.'/mod/forum/lib.php');   // We'll need this
           
            $text = '';

            if (!$forum = forum_get_course_forum($course->id, 'news')) {
                return $this->content;
            }

        /// First work out whether we can post to this group and if so, include a link

            if (isteacheredit($course->id)) {     /// Teachers can always post
                $visiblegroups = -1; 

                $text .= '<div align="center" class="newlink"><a href="'.$CFG->wwwroot.'/mod/forum/post.php?forum='.$forum->id.'">'.
                          get_string('addanewtopic', 'forum').'</a>...</div>';

            } else {                              /// Check the group situation
                $currentgroup = get_current_group($course->id);

                if (forum_user_can_post_discussion($forum, $currentgroup)) {
                    $text .= '<div align="center" class="newlink"><a href="'.$CFG->wwwroot.'/mod/forum/post.php?forum='.$forum->id.'">'.
                              get_string('addanewtopic', 'forum').'</a>...</div>';
                }

                if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $course->id)) {
                    $this->content->text = $text;
                    return $this->content;
                }
    
                $groupmode = groupmode($course, $cm);
    
                /// Decides if current user is allowed to see ALL the current discussions or not
    
                if (!$currentgroup and ($groupmode != SEPARATEGROUPS) ) {
                    $visiblegroups = -1;
                } else {
                    $visiblegroups = $currentgroup;
                }
            }

        /// Get all the recent discussions we're allowed to see

            if (! $discussions = forum_get_discussions($forum->id, 'p.modified DESC', 0, false, 
                                                       $visiblegroups, $course->newsitems) ) {
                $text .= '('.get_string('nonews', 'forum').')';
                $this->content->text = $text;
                return $this->content;
            }

        /// Actually create the listing now

            $strftimerecent = get_string('strftimerecent');
            $strmore = get_string('more', 'forum');

            foreach ($discussions as $discussion) {

                $discussion->subject = $discussion->name;

                if (!empty($CFG->filterall)) {
                    $discussion->subject = filter_text($discussion->subject, $forum->course);
                }

                $text .= '<p class="post">'.
                         '<span class="head">'.userdate($discussion->modified, $strftimerecent).'<br />'.
                                               fullname($discussion).'</span><br />'.
                         '<span class="info">'.$discussion->subject.' '.
                         '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion->discussion.'">'.
                         $strmore.'...</a></span>'.
                         '</p>';
            }

            $this->content->text = $text;

            $this->content->footer = '<a href="'.$CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id.'">'.
                                      get_string('oldertopics', 'forum').'</a> ...';

        }

        return $this->content;
    }
}

?>

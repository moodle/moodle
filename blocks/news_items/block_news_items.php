<?PHP //$Id$

class block_news_items extends block_base {
    function init() {
        $this->title = get_string('latestnews');
        $this->version = 2005030800;
    }

    function get_content() {
        global $CFG, $USER, $COURSE;

        if ($this->content !== NULL) {
            return $this->content;
        }

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        if (empty($this->instance)) {
            return $this->content;
        }


        if ($COURSE->newsitems) {   // Create a nice listing of recent postings

            require_once($CFG->dirroot.'/mod/forum/lib.php');   // We'll need this

            $text = '';

            if (!$forum = forum_get_course_forum($COURSE->id, 'news')) {
                return '';
            }

            if (!$cm = get_coursemodule_from_instance('forum', $forum->id, $COURSE->id)) {
                return '';
            }

            $context = get_context_instance(CONTEXT_MODULE, $cm->id);

        /// First work out whether we can post to this group and if so, include a link
            $groupmode    = groupmode($COURSE, $cm);
            $currentgroup = get_and_set_current_group($COURSE, $groupmode);
            

            if (forum_user_can_post_discussion($forum, $currentgroup, $groupmode, $cm, $context)) {
                $text .= '<div class="newlink"><a href="'.$CFG->wwwroot.'/mod/forum/post.php?forum='.$forum->id.'">'.
                          get_string('addanewtopic', 'forum').'</a>...</div>';
            }

        /// Get all the recent discussions we're allowed to see

            if (! $discussions = forum_get_discussions($forum->id, 'p.modified DESC', 0, false, 
                                                       $currentgroup, $COURSE->newsitems) ) {
                $text .= '('.get_string('nonews', 'forum').')';
                $this->content->text = $text;
                return $this->content;
            }

        /// Actually create the listing now

            $strftimerecent = get_string('strftimerecent');
            $strmore = get_string('more', 'forum');

        /// Accessibility: markup as a list.
            $text .= "\n<ul class='unlist'>\n";
            foreach ($discussions as $discussion) {

                $discussion->subject = $discussion->name;

                $discussion->subject = format_string($discussion->subject, true, $forum->course);

                $text .= '<li class="post">'.
                         '<div class="head">'.
                         '<div class="date">'.userdate($discussion->modified, $strftimerecent).'</div>'.
                         '<div class="name">'.fullname($discussion).'</div></div>'.
                         '<div class="info">'.$discussion->subject.' '.
                         '<a href="'.$CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion->discussion.'">'.
                         $strmore.'...</a></div>'.
                         "</li>\n";
            }
            $text .= "</ul>\n";

            $this->content->text = $text;

            $this->content->footer = '<a href="'.$CFG->wwwroot.'/mod/forum/view.php?f='.$forum->id.'">'.
                                      get_string('oldertopics', 'forum').'</a> ...';

        /// If RSS is activated at site and forum level and this forum has rss defined, show link
            if (isset($CFG->enablerssfeeds) && isset($CFG->forum_enablerssfeeds) &&
                $CFG->enablerssfeeds && $CFG->forum_enablerssfeeds && $forum->rsstype && $forum->rssarticles) {
                require_once($CFG->dirroot.'/lib/rsslib.php');   // We'll need this
                if ($forum->rsstype == 1) {
                    $tooltiptext = get_string('rsssubscriberssdiscussions','forum',format_string($forum->name));
                } else {
                    $tooltiptext = get_string('rsssubscriberssposts','forum',format_string($forum->name));
                }
                if (empty($USER->id)) {
                    $userid = 0;
                } else {
                    $userid = $USER->id;
                }
                $this->content->footer .= '<br />'.rss_get_link($COURSE->id, $userid, 'forum', $forum->id, $tooltiptext);
            }

        }

        return $this->content;
    }
}

?>

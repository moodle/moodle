<?php

class block_new_forum_replies extends block_base {
    function init() {
		$this->title = get_string('blockname_replies', 'block_new_forum_replies');
        $this->version = 2008070900;
    }

    function specialization() {
    	if(empty($this->config->style)) {
        	// set the default and save it
        	$this->config->style = 'replies';
            $this->instance_config_commit();
        }
    	if(!empty($this->config->title)) {
        	$this->title = $this->config->title;
        }
        else {
	        if($this->config->style=='newposts') {
                $this->title = get_string('blockname_newposts', 'block_new_forum_replies');
	        }
	        elseif($this->config->style=='threads') {
	            $this->title = get_string('blockname_threads', 'block_new_forum_replies');
	        }
	        else {
                $this->title = get_string('blockname_replies', 'block_new_forum_replies');
	        }
        }
    }

    function get_content() {
        if ($this->content !== NULL) {
            return $this->content;
        }

        global $CFG, $USER, $course, $isteacher, $timestart;

        $this->content = new stdClass;
        $this->content->text = '';
        $this->content->footer = '';

        $strby = get_string('by', 'block_new_forum_replies');
        $strnewpost = get_string('newpost', 'block_new_forum_replies');
        $strnewposts = get_string('newposts', 'block_new_forum_replies');


        $timestart = time() - COURSE_MAX_RECENT_PERIOD;
	    $course = get_record('course', 'id', $this->instance->pageid);
	    $context = get_context_instance(CONTEXT_COURSE, $course->id);

	    if (!has_capability('moodle/legacy:guest', $context, NULL, false)) {
	        if (!empty($USER->lastcourseaccess[$course->id])) {
	            if ($USER->lastcourseaccess[$course->id] > $timestart) {
	                $timestart = $USER->lastcourseaccess[$course->id];
	            }
	        }
	    }

		if($this->config->style=='replies') {
            $sql = "SELECT * FROM {$CFG->prefix}forum_posts WHERE
            	parent IN (SELECT id FROM {$CFG->prefix}forum_posts WHERE userid = '$USER->id')
                AND discussion IN (SELECT id FROM {$CFG->prefix}forum_discussions WHERE course = {$course->id})
                AND modified > '$timestart' ORDER BY created DESC";
		}
    	elseif($this->config->style=='newposts') {
		    $sql = "SELECT * FROM {$CFG->prefix}forum_posts WHERE
            			discussion IN (SELECT DISTINCT discussion FROM {$CFG->prefix}forum_posts WHERE userid = '$USER->id')
                        AND discussion IN (SELECT id FROM {$CFG->prefix}forum_discussions WHERE course = {$course->id})
                        AND userid != '$USER->id'
                        AND modified > '$timestart' ORDER BY created DESC";
        }
        else
        {
        	$sql = "SELECT * FROM {$CFG->prefix}forum_discussions WHERE id IN (SELECT DISTINCT discussion
						FROM {$CFG->prefix}forum_posts WHERE userid = '$USER->id') AND id IN (
						SELECT discussion FROM {$CFG->prefix}forum_posts WHERE userid != '$USER->id'
                        AND modified > '$timestart') AND course = {$course->id}";
        }
       	//echo "<pre>$sql</pre>";

	    $instances = get_records_sql($sql);
        $displayedinstances = 0;
        if($instances !== false) {
        	if($this->config->style!='threads') {
	            foreach ($instances as $post) {

	                $tempmod = new object;
	                $tempmod->course = $course->id;
	                $discussion = get_record('forum_discussions', 'id', $post->discussion);
	                $poster = get_record('user', 'id', $post->userid);
	                $tempmod->id = $discussion->forum;

	                $visible = instance_is_visible('forum', $tempmod)
	                                || has_capability('moodle/course:viewhiddenactivities', $context);
	                if($visible) {

	                    $date = userdate($post->modified);

	                    //echo "<pre>"; print_r($post); echo "</pre>";
	                    $link = $CFG->wwwroot.'/mod/forum/discuss.php?d='.$post->discussion.'&parent='.$post->parent.'#p'.$post->id;
	                    $this->content->text .= '<div>'.$date.'<br/><a href="' . $link . '">' . $post->subject . '</a><br/>';
	                    $this->content->text .= '<div class="name">'.$strby.' '.fullname($poster, has_capability('moodle/site:viewfullnames', $context)).'<hr/></div></div>';
	                    $displayedinstances++;
	                }
	            }
            }
            else {
	            foreach ($instances as $discussion) {
	                $tempmod = new object;
	                $tempmod->course = $course->id;
	                $tempmod->id = $discussion->forum;
	                $visible = instance_is_visible('forum', $tempmod)
	                                || has_capability('moodle/course:viewhiddenactivities', $context);
	                if($visible) {
                    	$sql = "SELECT COUNT(discussion) AS count FROM {$CFG->prefix}forum_posts WHERE userid != '$USER->id'
			                        AND modified > '$timestart' AND discussion = {$discussion->id}";
       					//echo "<pre>$sql</pre>";
                    	$count = get_field_sql($sql);
	                    $link = $CFG->wwwroot.'/mod/forum/discuss.php?d='.$discussion->id;
	                    $this->content->text .= '<div><a href="' . $link . '">' . $discussion->name . '</a><br/>';
                        if($count == 1) {
                            $this->content->text .= $count . ' ' . $strnewpost . '<hr/></div>';
                        }
                        else {
                            $this->content->text .= $count . ' ' . $strnewposts . '<hr/></div>';
                        }
	                    $displayedinstances++;
	                }
	            }
            }
        }
        if($displayedinstances == 0) {
        	$this->content->text = get_string('nonewreplies', 'block_new_forum_replies');
        }

        return $this->content;
    }

    function instance_allow_multiple() {
    	return true;
    }

    function instance_allow_config() {
	    return true;
    }

}

?>
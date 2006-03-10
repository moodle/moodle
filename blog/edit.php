<?php //$Id$

require_once('../config.php');
include_once('lib.php');
include_once('class.BlogInfo.php');
require_login();
// detemine where the user is coming from in case we need to send them back there
if (isset($_SERVER['HTTP_REFERER'])) {
    $referrer = $_SERVER['HTTP_REFERER'];
} else {
    $referrer = $CFG->wwwroot;
}

//first verify that user is not a guest
if (isguest()) {
    error(get_string('noguestpost', 'forum'), $referrer);
}

optional_variable($userid, 0);
optional_variable($editid, '');
optional_variable($sendpingbacks, 0);
optional_variable($sendtrackbacks, 0);

global $USER, $CFG;

//check to see if there is a requested blog to edit
if (!empty($userid) && $userid != 0) {
    if (blog_isLoggedIn() && $userid == $USER->id ) {
        ; // Daryl Hawes note: is this a placeholder for missing functionality?
    }
} else if ( blog_isLoggedIn() ) {
    //the user is logged in and have not specified a blog - so they will be editing their own
    $tempBlogInfo = blog_user_bloginfo();
    $userid = $tempBlogInfo->userid;
    unset($tempBlogInfo); //free memory from temp object - bloginfo will be created again in the included header
} else {
    error(get_string('noblogspecified', 'blog') .'<a href="'. $CFG->blog_blogurl .'">' .get_string('viewentries', 'blog') .'</a>');
}

$pageNavigation = 'edit';

include($CFG->dirroot .'/blog/header.php');

if (!empty($course)) {
    $courseid = $course->id;
} else if (!isadmin() && $CFG->blog_enable_moderation) {
    // the user is not an admin, blog moderation is on and there is no course association
    //Daryl Hawes note: possible bug here if editing a personal post that existed before blog moderation was enabled for the site.
    error('Blog moderation is enabled. Your entries must be associated with a course.');
}

//print_object($PAGE->bloginfo); //debug

//check if user is in blog's acl
if ( !blog_user_has_rights($PAGE->bloginfo) ) {
    if ($editid != '') {
        $blogEntry = $PAGE->bloginfo->get_blog_entry_by_id($editid);
        if (! (isteacher($blogEntry->$entryCourseId)) ) {
//            error( get_string('notallowedtoedit'.' You do not teach in this course.', 'blog'), $CFG->wwwroot .'/login/index.php');
            error( get_string('notallowedtoedit', 'blog'), $CFG->wwwroot .'/login/index.php');
        }
    } else {
        error( get_string('notallowedtoedit', 'blog'), $CFG->wwwroot .'/login/index.php');
    }
}

//////////// SECURITY AND SETUP COMPLETE - NOW PAGE LOGIC ///////////////////

if (isset($act) && $act == 'del')
{
    require_variable($postid);
    do_delete($PAGE->bloginfo, $postid);
}
if ($usehtmleditor = can_use_richtext_editor()) {
    $defaultformat = FORMAT_HTML;
    $onsubmit = '';
} else {
    $defaultformat = FORMAT_MOODLE;
    $onsubmit = '';
}

if ($post = data_submitted( get_referer() ) ) {
    if (!empty($post->editform)) { //make sure we're processing the edit form here
        //print_object($post); //debug

        ///these varaibles needs to be changed because of the javascript hack
        ///post->courseid
        ///post->groupid
        $post->courseid = $post->realcourse;   //might not need either, if javascript re-written
        $post->groupid = $post->realgroup;   //might not need
        $courseid = $post->realcourse;
        //end of yu's code
        
        if (!$post->etitle or !$post->body) {
            $post->error = get_string('emptymessage', 'forum');
        }
        if ($post->act == 'save') {
            do_save($post, $PAGE->bloginfo, $sendpingbacks, $sendtrackbacks);
        } else if ($post->act == 'update') {
            do_update($post, $PAGE->bloginfo, $sendpingbacks, $sendtrackbacks);
        } else if ($post->act == 'del') {
            require_variable($postid);
            do_delete($PAGE->bloginfo, $postid);
        }
    }
} else {

    //no post data yet, so load up the post array with default information
    $post->etitle = '';
    $post->userid = $USER->id;
    $post->body = '';
    $post->extendedbody = '';
    $post->useextendedbody = $PAGE->bloginfo->get_blog_use_extended_body();
    $post->format = $defaultformat;
    $post->categoryid = array(1);
    $post->publishstate = 'draft';
    $post->courseid  = $courseid;
    

}

if ($editid != '') {  // User is editing a post
    // ensure that editing is allowed first - admin users can edit any posts
    if (!isadmin() && $CFG->blog_enable_moderation && $blogEntry->entryPublishState != 'draft') {
        error('You are not allowed to modify a published entry. A teacher must first change this post back to draft status.'); //Daryl Hawes note: localize this line
    }
    $blogEntry = $PAGE->bloginfo->get_blog_entry_by_id($editid);

    //using an unformatted entry body here so that extra formatting information is not stored in the db
    $post->body = $blogEntry->get_unformatted_entry_body();
    $post->extendedbody = $blogEntry->get_unformatted_entry_extended_body();
    $post->useextendedbody = $PAGE->bloginfo->get_blog_use_extended_body();
    $post->etitle = $blogEntry->entryTitle;    
    $post->postid = $editid;
    $post->userid = $PAGE->bloginfo->userid;
    $post->categoryid = $blogEntry->entryCategoryIds;
    $post->format = $blogEntry->entryFormat;
    $post->publishstate = $blogEntry->entryPublishState;
    $post->courseid  = $blogEntry->entryCourseId;
    $post->groupid = (int)$blogEntry->entryGroupId;
}

if (isset($post->postid) && ($post->postid != -1) ) {
    $formHeading = get_string('updateentrywithid', 'blog', $post->postid);
} else {
    $formHeading = get_string('addnewentry', 'blog');
}

if (isset($post->error)) {
    notify($post->error);
}

print_simple_box_start("center");
require('edit.html');
print_simple_box_end();

    // Janne comment: Let's move this in here
    // so IE gets more time to load the
    // Page.
    if ($usehtmleditor) {
        // Janne comment: there are two text fields in form
        // so lets try to replace them both with
        // HTMLArea editors
        use_html_editor();
    }

include($CFG->dirroot .'/blog/footer.php');


/*****************************   edit.php functions  ***************************/
/*
* do_delete
* takes $bloginfo_arg argument as reference to a blogInfo object.
* also takes the postid - the id of the entry to be removed
*/
function do_delete(&$bloginfo_arg, $postid) {
    global $CFG;
    // make sure this user is authorized to delete this entry.
    // cannot use $post->pid because it may not have been initialized yet. Also the pid may be in get format rather than post.
    if ($bloginfo_arg->delete_blog_entry_by_id($postid)) {
        //echo "bloginfo_arg:"; //debug
        print_object($bloginfo_arg); //debug
        //echo "pid to delete:".$postid; //debug
        delete_records('blog_tag_instance', 'entryid', $postid);
        print '<strong>'. get_string('entrydeleted', 'blog') .'</strong><p>';

        //record a log message of this entry deletion
        if ($site = get_site()) {
            add_to_log($site->id, 'blog', 'delete', 'index.php?userid='. $bloginfo_arg->userid, 'deleted blog entry with entry id# '. $postid);
        }
    } else {
        error(get_string('entryerrornotyours', 'blog'));
    }

    //comment out this redirect to debug the deletion of entries
    redirect($CFG->wwwroot .'/blog/index.php?userid='. $bloginfo_arg->userid);
}

/**
*  do_save
*
* @param object $post argument is a reference to the post object which is used to store information for the form
* @param object $bloginfo_arg argument is reference to a blogInfo object.
*/
function do_save(&$post, &$bloginfo_arg, $sendpingbacks, $sendtrackbacks) {
    global $USER, $CFG;
//    echo 'Debug: Post object in do_save function of edit.php<br />'; //debug
//    print_object($post); //debug

    if ($post->body == '') {
        $post->error =  get_string('nomessagebodyerror', 'blog');
    } else {

        //initialize courseid and groupid if specified
        if (isset($post->courseid)) {
            $courseid = $post->courseid;
        } else {
            $courseid = 1;
        }
        if (isset($post->groupid)) {
            $groupid = $post->groupid;
        } else {
            $groupid = '';
        }
       
/*     
        //group pseudocode 
        if ($groupid != '') {
            if (! ismember($post->groupid) ) {
                error('You are not a member of the specified group. Group with id#('.$groupid.')'); //Daryl Hawes note: LOCALIZATION NEEDED FOR THIS LINE
            }
        }*/

        // Insert the new blog entry.
        $entryID = $bloginfo_arg->insert_blog_entry($post->etitle, $post->body, $post->extendedbody, $USER->id, $post->format, $post->publishstate, $courseid, $groupid);

//        print 'Debug: created a new entry - entryId = '.$entryID.'<br />'; //debug
//        echo 'Debug: do_save() in edit.php calling blog_do_*back_pings<br />'."\n"; //debug
        $otags = optional_param('otags');
        $ptags = optional_param('ptags');
        // Add tags information
        foreach ($otags as $otag) {
            $tag->entryid = $entryID;
            $tag->tagid = $otag;
            $tag->groupid = $groupid;
            $tag->courseid = $courseid;
            $tag->userid = $USER->id;

            insert_record('blog_tag_instance',$tag);
        }
        
        foreach ($ptags as $ptag) {
            $tag->entryid = $entryID;
            $tag->tagid = $ptag;
            $tag->groupid = $groupid;
            $tag->courseid = $courseid;
            $tag->userid = $USER->id;

            insert_record('blog_tag_instance',$tag);
        }

        print '<strong>'. get_string('entrysaved', 'blog') .'</strong><br />';
        //record a log message of this entry addition
        if ($site = get_site()) {
            add_to_log($site->id, 'blog', 'add', 'archive.php?userid='. $bloginfo_arg->userid .'&postid='. $entryID, 'created new blog entry with entry id# '. $entryID);
        }
        //to debug this save function comment out the following redirect code
        if ($courseid == 1 || $courseid == 0 || $courseid == '') {
            redirect($CFG->wwwroot .'/blog/index.php?userid='. $bloginfo_arg->userid);
        } else {
            redirect($CFG->wwwroot .'/course/view.php?id='. $courseid);
        }
    }
}

/**
 * @param . $post argument is a reference to the post object which is used to store information for the form
 * @param . $bloginfo_arg argument is reference to a blogInfo object.
 * @todo complete documenting this function. enable trackback and pingback between entries on the same server
 */
function do_update(&$post, &$bloginfo, $sendpingbacks, $sendtrackbacks) {

    global $CFG, $USER;
    
    //initialize courseid and groupid if specified
    if (isset($post->courseid)) {
        $courseid = $post->courseid;
    } else {
        $courseid = 1;
    }
    if (isset($post->groupid)) {
        $groupid = $post->groupid;
    } else {
        $groupid = '';
    }

/*
    //pseudocode for handling groups
    if ($groupid != '') {
        if (! ismember($groupid) ) {
            error('You are not a member of the specified group. Group with id#('. $groupid .')'); //Daryl Hawes note: LOCALIZATION NEEDED FOR THIS LINE
        }
    }*/
    
    $blogentry = $bloginfo->get_blog_entry_by_id($post->postid);
    echo "id id ".$post->postid;
//  print_object($blogentry);  //debug

    $blogentry->set_title($post->etitle);
    $blogentry->set_body($post->body);
    if (isset($post->extendedbody)) {
        $blogentry->set_extendedbody($post->extendedbody);
    }
    $blogentry->set_format($post->format);
    $blogentry->set_publishstate($post->publishstate); //we don't care about the return value here
    $blogentry->set_courseid($courseid);
    $blogentry->set_groupid($groupid);

    if ( !$error = $blogentry->save() ) {
//        echo 'Debug: do_update in edit.php calling do_pings<br />'."\n"; //debug
        delete_records('blog_tag_instance', 'entryid', $blogentry->entryId);

        $otags = optional_param('otags');
        $ptags = optional_param('ptags');
        // Add tags information
        foreach ($otags as $otag) {
            $tag->entryid = $blogentry->entryId;
            $tag->tagid = $otag;
            $tag->groupid = $groupid;
            $tag->courseid = $courseid;
            $tag->userid = $USER->id;

            insert_record('blog_tag_instance',$tag);
        }

        foreach ($ptags as $ptag) {
            $tag->entryid = $blogentry->entryId;
            $tag->tagid = $ptag;
            $tag->groupid = $groupid;
            $tag->courseid = $courseid;
            $tag->userid = $USER->id;

            insert_record('blog_tag_instance',$tag);
        }
        // only do pings if the entry is published to the world
        // Daryl Hawes note - eventually should check if it's on the same server
        // and if so allow pb/tb as well - especially now that moderation is in place
        print '<strong>'. get_string('entryupdated', 'blog') .'</strong><p>';

        //record a log message of this entry update action
        if ($site = get_site()) {
            add_to_log($site->id, 'blog', 'update', 'archive.php?userid='. $bloginfo->userid .'&postid='. $post->postid, 'updated existing blog entry with entry id# '. $post->postid);
        }

        redirect($CFG->wwwroot .'/blog/index.php?userid='. $bloginfo->userid);
    } else {
//        get_string('', 'blog') //Daryl Hawes note: localize this line
        $post->error =  'There was an error updating this post in the database: '. $error;
    }
}
?>

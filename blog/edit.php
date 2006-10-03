<?php //$Id$

require_once('../config.php');
include_once('lib.php');
require_login();

$courseid = optional_param('courseid', SITEID, PARAM_INT);
$act = optional_param('act','',PARAM_ALPHA);

if (empty($CFG->bloglevel)) {
    error('Blogging is disabled!');
}

// detemine where the user is coming from in case we need to send them back there
if (!$referrer = optional_param('referrer','', PARAM_URL)) {
    if (isset($_SERVER['HTTP_REFERER'])) {
        $referrer = $_SERVER['HTTP_REFERER'];
    } else {
        $referrer = $CFG->wwwroot;
    }
}


$context = get_context_instance(CONTEXT_SYSTEM, SITEID);
if (!has_capability('moodle/blog:view', $context)) {
    error(get_string('nopost', 'blog'), $referrer);
}


// Make sure that the person trying to edit have access right
if ($editid = optional_param('editid', 0, PARAM_INT)) {

    $blogEntry = get_record('post', 'id', $editid);

    if (!blog_user_can_edit_post($blogEntry, $context)) {
        error( get_string('notallowedtoedit', 'blog'), $CFG->wwwroot .'/login/index.php');
    }
}

// Check to see if there is a requested blog to edit
if (isloggedin() && !isguest()) {
    $userid = $USER->id;
} else {
    error(get_string('noblogspecified', 'blog') .'<a href="'. $CFG->blog_blogurl .'">' .get_string('viewentries', 'blog') .'</a>');
}

// If we are trying to delete an non-existing blog entry
if (isset($act) && ($act == 'del') && (empty($blogEntry))) {
    error ('the entry you are trying to delete does not exist');
}


$pageNavigation = 'edit';
include($CFG->dirroot .'/blog/header.php');

//////////// SECURITY AND SETUP COMPLETE - NOW PAGE LOGIC ///////////////////

if (isset($act) && ($act == 'del') && confirm_sesskey())
{
    $postid = required_param('editid', PARAM_INT);
    if (optional_param('confirm',0,PARAM_INT)) {
        do_delete($postid, $context);
    } else {

    /// prints blog entry and what confirmation form
        echo '<div align="center"><form method="GET" action="edit.php">';
        echo '<input type="hidden" name="act" value="del" />';
        echo '<input type="hidden" name="confirm" value="1" />';
        echo '<input type="hidden" name="editid" value="'.$postid.'" />';
        echo '<input type="hidden" name="sesskey" value="'.sesskey().'" />';

        print_string('blogdeleteconfirm', 'blog');
        blog_print_entry($blogEntry);

        echo '<br />';
        echo '<input type="submit" value="'.get_string('delete').'" /> ';
        echo ' <input type="button" value="'.get_string('cancel').'" onclick="javascript:history.go(-1)" />';
        echo '</form></div>';
        print_footer($course);
        exit;
    }
}

if ($usehtmleditor = can_use_richtext_editor()) {
    $defaultformat = FORMAT_HTML;
    $onsubmit = '';
} else {
    $defaultformat = FORMAT_MOODLE;
    $onsubmit = '';
}

if (($post = data_submitted( get_referer() )) && confirm_sesskey()) {
    if (!empty($post->editform)) { //make sure we're processing the edit form here
        //print_object($post); //debug

        if (!$post->etitle or !$post->body) {
            $post->error = get_string('emptymessage', 'forum');
        }
        if ($post->act == 'save') {
            do_save($post);
        } else if ($post->act == 'update') {
            do_update($post);
        } else if ($post->act == 'del') {
            $postid = required_param('postid', PARAM_INT);
            do_delete($postid, $context);
        }
    }
} else {

    //no post data yet, so load up the post array with default information
    $post->etitle = '';
    $post->userid = $USER->id;
    $post->body = '';
    $post->format = $defaultformat;
    $post->publishstate = 'draft';
}

if ($editid) {  // User is editing a post
    // ensure that editing is allowed first - admin users can edit any posts

    $blogEntry = get_record('post','id',$editid);

    //using an unformatted entry body here so that extra formatting information is not stored in the db
    $post->body = stripslashes_safe($blogEntry->summary);
    $post->etitle = stripslashes_safe($blogEntry->subject);
    $post->postid = $editid;
    $post->userid = $blogEntry->userid;
    $post->format = $blogEntry->format;
    $post->publishstate = $blogEntry->publishstate;
}

if (isset($post->postid) && ($post->postid != -1) ) {
    $formHeading = get_string('updateentrywithid', 'blog');
} else {
    $formHeading = get_string('addnewentry', 'blog');
}

if (isset($post->error)) {
    notify($post->error);
}

print_simple_box_start("center");
require('edit.html');
print_simple_box_end();

include($CFG->dirroot .'/blog/footer.php');


/*****************************   edit.php functions  ***************************/
/*
* do_delete
* takes $bloginfo_arg argument as reference to a blogInfo object.
* also takes the postid - the id of the entry to be removed
*/
function do_delete($postid, $context) {
    global $CFG, $USER, $referrer;
    // make sure this user is authorized to delete this entry.
    // cannot use $post->pid because it may not have been initialized yet. Also the pid may be in get format rather than post.
    // check ownership
    $blogEntry = get_record('post', 'id', $postid);

    if (blog_user_can_edit_post($blogEntry, $context)) {
        if (delete_records('post', 'id', $postid)) {
            //echo "bloginfo_arg:"; //debug
            //print_object($bloginfo_arg); //debug
            //echo "pid to delete:".$postid; //debug
            delete_records('blog_tag_instance', 'entryid', $postid);
            print '<strong>'. get_string('entrydeleted', 'blog') .'</strong><p>';

            //record a log message of this entry deletion
            if ($site = get_site()) {
                add_to_log($site->id, 'blog', 'delete', 'index.php?userid='. $blogEntry->userid, 'deleted blog entry with entry id# '. $postid);
            }
        }
    }
    else {
        error(get_string('entryerrornotyours', 'blog'));
    }

    //comment out this redirect to debug the deletion of entries

    redirect($CFG->wwwroot .'/blog/index.php?userid='. $blogEntry->userid);
}

/**
*  do_save
*
* @param object $post argument is a reference to the post object which is used to store information for the form
* @param object $bloginfo_arg argument is reference to a blogInfo object.
*/
function do_save($post) {
    global $USER, $CFG, $referrer;
//    echo 'Debug: Post object in do_save function of edit.php<br />'; //debug
//    print_object($post); //debug

    if ($post->body == '') {
        $post->error =  get_string('nomessagebodyerror', 'blog');
    } else {

        /// Write a blog entry into database
        $blogEntry = new object;
        $blogEntry->subject = addslashes($post->etitle);
        $blogEntry->summary = addslashes($post->body);
        $blogEntry->module = 'blog';
        $blogEntry->userid = $USER->id;
        $blogEntry->format = $post->format;
        $blogEntry->publishstate = $post->publishstate;
        $blogEntry->lastmodified = time();
        $blogEntry->created = time();

        // Insert the new blog entry.
        $entryID = insert_record('post',$blogEntry);

//        print 'Debug: created a new entry - entryId = '.$entryID.'<br />'; //debug
//        echo 'Debug: do_save() in edit.php calling blog_do_*back_pings<br />'."\n"; //debug
        if ($entryID) {

            /// Creates a unique hash. I don't know what this is for (Yu)
            $dataobject = new object;
            $dataobject->uniquehash = md5($blogEntry->userid.$CFG->wwwroot.$entryID);
            update_record('post', $dataobject);

            /// Associate tags with entries
            
            $tag = NULL;
            $tag->entryid = $entryID;
            $tag->userid = $USER->id;
            $tag->timemodified = time();

            /// Add tags information
            if ($otags = optional_param('otags','', PARAM_INT)) {
                foreach ($otags as $otag) {
                    $tag->tagid = $otag;
                    insert_record('blog_tag_instance',$tag);
                }
            }

            if ($ptags = optional_param('ptags','', PARAM_INT)) {
                foreach ($ptags as $ptag) {
                    $tag->tagid = $ptag;
                    insert_record('blog_tag_instance',$tag);
                }
            }

            print '<strong>'. get_string('entrysaved', 'blog') .'</strong><br />';
        }
        //record a log message of this entry addition
        if ($site = get_site()) {
            add_to_log($site->id, 'blog', 'add', 'index.php?userid='. $blogEntry->userid .'&postid='. $entryID, $blogEntry->subject);
        }
        
        redirect($referrer);
        /*
        //to debug this save function comment out the following redirect code
        if ($courseid == SITEID || $courseid == 0 || $courseid == '') {
            redirect($CFG->wwwroot .'/blog/index.php?userid='. $blogEntry->userid);
        } else {
            redirect($CFG->wwwroot .'/course/view.php?id='. $courseid);
        }*/
    }
}

/**
 * @param . $post argument is a reference to the post object which is used to store information for the form
 * @param . $bloginfo_arg argument is reference to a blogInfo object.
 * @todo complete documenting this function. enable trackback and pingback between entries on the same server
 */
function do_update($post) {
    // here post = data_submitted();
    global $CFG, $USER, $referrer;
    $blogEntry = get_record('post','id',$post->postid);
//  echo "id id ".$post->postid;
//  print_object($blogentry);  //debug

    $blogEntry->subject = addslashes($post->etitle);
    $blogEntry->summary = addslashes($post->body);
    if ($blogEntry->summary == '<br />') {
        $blogEntry->summary = '';
    }
    $blogEntry->format = $post->format;
    $blogEntry->publishstate = $post->publishstate; //we don't care about the return value here

    if ( update_record('post',$blogEntry)) {
        delete_records('blog_tag_instance', 'entryid', $blogEntry->id);

        $tag = NULL;
        $tag->entryid = $blogEntry->id;
        $tag->userid = $USER->id;
        $tag->timemodified = time();
        
        /// Add tags information
        if ($otags = optional_param('otags','', PARAM_INT)) {
            foreach ($otags as $otag) {
                $tag->tagid = $otag;
                insert_record('blog_tag_instance',$tag);
            }
        }

        if ($ptags = optional_param('ptags','', PARAM_INT)) {
            foreach ($ptags as $ptag) {
                $tag->tagid = $ptag;
                insert_record('blog_tag_instance',$tag);
            }
        }
        
        // only do pings if the entry is published to the world
        // Daryl Hawes note - eventually should check if it's on the same server
        // and if so allow pb/tb as well - especially now that moderation is in place
        print '<strong>'. get_string('entryupdated', 'blog') .'</strong><p>';

        //record a log message of this entry update action
        if ($site = get_site()) {
            add_to_log($site->id, 'blog', 'update', 'index.php?userid='. $blogEntry->userid .'&postid='. $post->postid, $blogEntry->subject);
        }
        
        redirect($referrer);
        //to debug this save function comment out the following redirect code
/*
        if ($courseid == SITEID || $courseid == 0 || $courseid == '') {
            redirect($CFG->wwwroot .'/blog/index.php?userid='. $blogEntry->userid);
        } else {
            redirect($CFG->wwwroot .'/course/view.php?id='. $courseid);
        }*/
    } else {
//        get_string('', 'blog') //Daryl Hawes note: localize this line
        $post->error =  'There was an error updating this post in the database';
    }
}
?>
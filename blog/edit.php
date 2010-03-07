<?php //$Id$

require_once('../config.php');
include_once('lib.php');
include_once($CFG->dirroot.'/tag/lib.php');

$action   = required_param('action', PARAM_ALPHA);
$id       = optional_param('id', 0, PARAM_INT);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);
$courseid = optional_param('courseid', 0, PARAM_INT); // needed for user tab - does nothing here

require_login($courseid);

if (empty($CFG->bloglevel)) {
    error('Blogging is disabled!');
}

if (isguest()) {
    print_error('noguestpost', 'blog');
}

$sitecontext = get_context_instance(CONTEXT_SYSTEM);
if (!has_capability('moodle/blog:create', $sitecontext) and !has_capability('moodle/blog:manageentries', $sitecontext)) {
    error('You can not post or edit blogs.');
}

// Make sure that the person trying to edit have access right
if ($id) {
    if (!$existing = get_record('post', 'id', $id)) {
        error('Wrong blog post id');
    }

    if (!blog_user_can_edit_post($existing)) {
        print_error('notallowedtoedit', 'blog');
    }
    $userid    = $existing->userid;
    $returnurl = $CFG->wwwroot.'/blog/index.php?userid='.$existing->userid;
} else {
    if (!has_capability('moodle/blog:create', $sitecontext)) {
        print_error('nopost', 'blog'); // manageentries is not enough for adding
    }
    $existing  = false;
    $userid    = $USER->id;
    $returnurl = 'index.php?userid='.$USER->id;
}
if (!empty($courseid)) {
    $returnurl .= '&amp;courseid='.$courseid;
}


$strblogs = get_string('blogs','blog');

if ($action=='delete'){
    if (!$existing) {
        error('Incorrect blog post id');
    }
    if (data_submitted() and $confirm and confirm_sesskey()) {
        do_delete($existing);
        redirect($returnurl);
    } else {
        $optionsyes = array('id'=>$id, 'action'=>'delete', 'confirm'=>1, 'sesskey'=>sesskey(), 'courseid'=>$courseid);
        $optionsno = array('userid'=>$existing->userid, 'courseid'=>$courseid);
        print_header("$SITE->shortname: $strblogs", $SITE->fullname);
        blog_print_entry($existing);
        echo '<br />';
        notice_yesno(get_string('blogdeleteconfirm', 'blog'), 'edit.php', 'index.php', $optionsyes, $optionsno, 'post', 'get');
        print_footer();
        die;
    }
}

require_once('edit_form.php');
$blogeditform = new blog_edit_form(null, compact('existing', 'sitecontext'));

if ($blogeditform->is_cancelled()){
    redirect($returnurl);
} else if ($fromform = $blogeditform->get_data()){
    //save stuff in db
    switch ($action) {
        case 'add':
            do_add($fromform, $blogeditform);
        break;

        case 'edit':
            if (!$existing) {
                error('Incorrect blog post id');
            }
            do_edit($fromform, $blogeditform);
        break;
        default :
            error('Unknown action!');
    }
    redirect($returnurl);
}


// gui setup
switch ($action) {
    case 'add':
        // prepare new empty form
        $post->publishstate = 'site';
        $strformheading = get_string('addnewentry', 'blog');
        $post->action       = $action;
    break;

    case 'edit':
        if (!$existing) {
            error('Incorrect blog post id');
        }
        $post->id           = $existing->id;
        $post->subject      = clean_text($existing->subject);
        $post->summary      = clean_text($existing->summary, $existing->format);
        $post->publishstate = $existing->publishstate;
        $post->format       = $existing->format;
        $post->action       = $action;
        $strformheading = get_string('updateentrywithid', 'blog');

        if ($itemptags = tag_get_tags_csv('post', $post->id, TAG_RETURN_TEXT, 'default')) {
            $post->ptags = $itemptags;
        }
        
        if ($itemotags = tag_get_tags_array('post', $post->id, 'official')) {
            $post->otags = array_keys($itemotags);
        }
    break;
    default :
        error('Unknown action!');
}

// done here in order to allow deleting of posts with wrong user id above
if (!$user = get_record('user', 'id', $userid)) {
    error('Incorrect user id');
}
$navlinks = array();
$navlinks[] = array('name' => fullname($user), 'link' => "$CFG->wwwroot/user/view.php?id=$userid", 'type' => 'misc');
$navlinks[] = array('name' => $strblogs, 'link' => "$CFG->wwwroot/blog/index.php?userid=$userid", 'type' => 'misc');
$navlinks[] = array('name' => $strformheading, 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

print_header("$SITE->shortname: $strblogs", $SITE->fullname, $navigation,'','',true);
$blogeditform->set_data($post);
$blogeditform->display();


print_footer();


die;

/*****************************   edit.php functions  ***************************/

/*
* Delete blog post from database
*/
function do_delete($post) {
    global $returnurl;

    $status = delete_records('post', 'id', $post->id);
    //$status = delete_records('blog_tag_instance', 'entryid', $post->id) and $status;
    tag_set('post', $post->id, array());
    
    blog_delete_old_attachments($post);

    add_to_log(SITEID, 'blog', 'delete', 'index.php?userid='. $post->userid, 'deleted blog entry with entry id# '. $post->id);

    if (!$status) {
        error('Error occured while deleting post', $returnurl);
    }
}

/**
 * Write a new blog entry into database
 */
function do_add($post, $blogeditform) {
    global $CFG, $USER, $returnurl;

    $post->module       = 'blog';
    $post->userid       = $USER->id;
    $post->lastmodified = time();
    $post->created      = time();

    // Insert the new blog entry.
    if ($id = insert_record('post', $post)) {
        $post->id = $id;
        // add blog attachment
        $dir = blog_file_area_name($post);
        if ($blogeditform->save_files($dir) and $newfilename = $blogeditform->get_new_filename()) {
            set_field("post", "attachment", $newfilename, "id", $post->id);
        }
        add_tags_info($post->id);
        add_to_log(SITEID, 'blog', 'add', 'index.php?userid='.$post->userid.'&postid='.$post->id, $post->subject);

    } else {
        error('There was an error adding this post in the database', $returnurl);
    }

}

/**
 * @param . $post argument is a reference to the post object which is used to store information for the form
 * @param . $bloginfo_arg argument is reference to a blogInfo object.
 * @todo complete documenting this function. enable trackback and pingback between entries on the same server
 */
function do_edit($post, $blogeditform) {

    global $CFG, $USER, $returnurl;


    $post->lastmodified = time();

    $dir = blog_file_area_name($post);
    if ($blogeditform->save_files($dir) and $newfilename = $blogeditform->get_new_filename()) {
        $post->attachment = $newfilename;
    }

    // update record
    if (update_record('post', $post)) {
        // delete all tags associated with this entry
        
        //delete_records('blog_tag_instance', 'entryid', $post->id);
        //delete_records('tag_instance', 'itemid', $post->id, 'itemtype', 'blog');
        //untag_an_item('post', $post->id);
        // add them back
        add_tags_info($post->id);

        add_to_log(SITEID, 'blog', 'update', 'index.php?userid='.$USER->id.'&postid='.$post->id, $post->subject);

    } else {
        error('There was an error updating this post in the database', $returnurl);
    }
}

/**
 * function to attach tags into a post
 * @param int postid - id of the blog
 */
function add_tags_info($postid) {
    
    $tags = array();
    if ($otags = optional_param('otags', '', PARAM_INT)) {
        foreach ($otags as $tagid) {
            // TODO : make this use the tag name in the form
            if ($tag = tag_get('id', $tagid)) {
                $tags[] = $tag->name;
            }
        }
    }

    $manual_tags = optional_param('ptags', '', PARAM_NOTAGS);
    $tags = array_merge($tags, explode(',', $manual_tags));
    
    tag_set('post', $postid, $tags);
}
?>

<?php //$Id$

require_once('../config.php');
include_once('lib.php');

$action   = required_param('action', PARAM_ALPHA);
$id       = optional_param('id', 0, PARAM_INT);
$confirm  = optional_param('confirm', 0, PARAM_BOOL);
$courseid = optional_param('courseid', 0, PARAM_INT); // needed for user tab - does nothing here

require_login();

if (empty($CFG->bloglevel)) {
    error('Blogging is disabled!');
}

if (isguest()) {
    error(get_string('noguestpost', 'blog'));
}

$sitecontext = get_context_instance(CONTEXT_SYSTEM, SITEID);
if (!has_capability('moodle/blog:create', $sitecontext) and !has_capability('moodle/blog:manageentries', $sitecontext)) {
    error('You can not post or edit blogs.');
}

// Make sure that the person trying to edit have access right
if ($id) {
    if (!$existing = get_record('post', 'id', $id)) {
        error('Wrong blog post id');
    }

    if (!blog_user_can_edit_post($existing)) {
        error(get_string('notallowedtoedit', 'blog'));
    }
    $userid    = $existing->userid;
    $returnurl = $CFG->wwwroot.'/blog/index.php?userid='.$existing->userid;
} else {
    if (!has_capability('moodle/blog:create', $sitecontext)) {
        error(get_string('nopost', 'blog')); // manageentries is not enough for adding
    }
    $existing  = false;
    $userid    = $USER->id;
    $returnurl = 'index.php?userid='.$USER->id;
}
if (!empty($courseid)) {
    $returnurl .= '&amp;courseid='.$courseid;
}

$errors = array();
$post = new object(); // editing form data

$usehtmleditor = can_use_richtext_editor();
$strblogs = get_string('blogs','blog');

/// Main switch for processing blog entry
switch ($action) {

    case 'add':
        if (data_submitted() and confirm_sesskey()) {
            do_add($post, $errors);
            if (empty($errors)) {
                redirect($returnurl);
            }
            $post = stripslashes_safe($post); // no db access after this!!
            // print form again
        } else {
            // prepare new empty form
            $post->subject      = '';
            $post->summary      = '';
            $post->publishstate = 'draft';
            $post->format       = $usehtmleditor ? FORMAT_HTML : FORMAT_MOODLE;

        }
        $strformheading = get_string('addnewentry', 'blog');
    break;

    case 'edit':
        if (!$existing) {
            error('Incorrect blog post id');
        }
        if (data_submitted() and confirm_sesskey()) {
            do_edit($post, $errors);
            if (empty($errors)) {
                redirect($returnurl);
            }
            $post = stripslashes_safe($post); // no db access after this!!
            // print form again
        } else {
            $post->id           = $existing->id;
            $post->subject      = $existing->subject;
            $post->summary      = $existing->summary;
            $post->publishstate = $existing->publishstate;
            $post->format       = $existing->format;
        }
        $strformheading = get_string('updateentrywithid', 'blog');
    break;

    case 'delete':
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
    break;

    default:
        error('Unknown action!');
    break;
}

// gui setup

// done here in order to allow deleting of posts with wrong user id above
if (!$user = get_record('user', 'id', $userid)) {
    error('Incorrect user id');
}

print_header("$SITE->shortname: $strblogs", $SITE->fullname,
                '<a href="'.$CFG->wwwroot.'/user/view.php?id='.$userid.'">'.fullname($user).'</a> ->
                <a href="'.$CFG->wwwroot.'/blog/index.php?userid='.$userid.'">'.$strblogs.'</a> -> '.$strformheading,'','',true);

echo '<br />';
print_simple_box_start('center');
require('edit.html');
print_simple_box_end();

if ($usehtmleditor) {
    use_html_editor();
}

print_footer();

die;

/*****************************   edit.php functions  ***************************/
/*
* Delete blog post from database
*/
function do_delete($post) {
    global $returnurl;

    $status = delete_records('post', 'id', $post->id);
    $status = delete_records('blog_tag_instance', 'entryid', $post->id) and $status;

    add_to_log(SITEID, 'blog', 'delete', 'index.php?userid='. $post->userid, 'deleted blog entry with entry id# '. $post->id);

    if (!$status) {
        error('Error occured while deleting post', $returnurl);
    }
}

/**
 * Write a new blog entry into database
 */
function do_add(&$post, &$errors) {
    global $CFG, $USER, $returnurl;

    $post->subject      = required_param('subject', PARAM_MULTILANG);
    $post->summary      = required_param('summary', PARAM_RAW);
    $post->format       = required_param('format', PARAM_INT);
    $post->publishstate = required_param('publishstate', PARAM_ALPHA);;

    if ($post->summary == '<br />') {
        $post->summary = '';
    }

    if ($post->subject == '') {
        $errors['subject'] = get_string('emptytitle', 'blog');
    }
    if ($post->summary == '') {
        $errors['summary'] = get_string('emptybody', 'blog');
    }

    if (!empty($errors)) {
        return; // no saving
    }

    $post->module       = 'blog';
    $post->userid       = $USER->id;
    $post->lastmodified = time();
    $post->created      = time();

    // Insert the new blog entry.
    if ($id = insert_record('post', $post)) {
        $post->id = $id;
        add_tags_info($post->id);
        add_to_log(SITEID, 'blog', 'add', 'index.php?userid='.$post->userid.'&postid='.$posz->id, $post->subject);

    } else {
        error('There was an error adding this post in the database', $returnurl);
    }

}

/**
 * @param . $post argument is a reference to the post object which is used to store information for the form
 * @param . $bloginfo_arg argument is reference to a blogInfo object.
 * @todo complete documenting this function. enable trackback and pingback between entries on the same server
 */
function do_edit(&$post, &$errors) {

    global $CFG, $USER, $returnurl;

    $post->id           = required_param('id', PARAM_INT);
    $post->subject      = required_param('subject', PARAM_MULTILANG);
    $post->summary      = required_param('summary', PARAM_RAW);
    $post->format       = required_param('format', PARAM_INT);
    $post->publishstate = required_param('publishstate', PARAM_ALPHA);;

    if ($post->summary == '<br />') {
        $post->summary = '';
    }

    if ($post->subject == '') {
        $errors['subject'] = get_string('emptytitle', 'blog');
    }
    if ($post->summary == '') {
        $errors['summary'] = get_string('emptybody', 'blog');
    }

    if (!empty($errors)) {
        return; // no saving
    }

    $post->lastmodified = time();

    // update record
    if (update_record('post', $post)) {
        // delete all tags associated with this entry
        delete_records('blog_tag_instance', 'entryid', $post->id);
        // add them back
        add_tags_info($post->id);
        add_to_log(SITEID, 'blog', 'update', 'index.php?userid='.$post->userid.'&postid='.$post->id, $post->subject);

    } else {
        error('There was an error updating this post in the database', $returnurl);
    }
    
}

/**
 * function to attach tags into a post
 * @param int postid - id of the blog
 */
function add_tags_info($postid) {
    
    global $USER;
    
    $post = get_record('post', 'id', $postid);

    $tag = new object();
    $tag->entryid = $post->id;
    $tag->userid = $post->userid;
    $tag->timemodified = time();
        
    /// Attach official tags
    if ($otags = optional_param('otags','', PARAM_INT)) {
        foreach ($otags as $otag) {
            $tag->tagid = $otag;
            insert_record('blog_tag_instance', $tag);
        }
    }

    /// Attach Personal Tags
    if ($ptags = optional_param('ptags','', PARAM_NOTAGS)) {
        $ptags = explode(',',$ptags);
        foreach ($ptags as $ptag) {
            $ptag = trim($ptag);
            // check for existance
            // it does not matter whether it is an offical tag or personal tag
            // we do not want to have 1 copy of offical tag and 1 copy of personal tag (for the same tag)
            if ($ctag = get_record('tags', 'text', $ptag)) {
                $tag->tagid = $ctag->id;
                insert_record('blog_tag_instance', $tag);
            } else { // create a personal tag
                $ctag = new object;
                $ctag->userid = $USER->id;
                $ctag->text = $ptag;
                $ctag->type = 'personal';
                if ($tagid = insert_record('tags', $ctag)) {
                    $tag->tagid = $tagid;
                    insert_record('blog_tag_instance', $tag);
                }         
            }
        }
    }
}
?>
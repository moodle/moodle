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
    print_error('blogdisable', 'blog');
}

if (isguest()) {
    print_error('noguestpost', 'blog');
}

$sitecontext = get_context_instance(CONTEXT_SYSTEM);
if (!has_capability('moodle/blog:create', $sitecontext) and !has_capability('moodle/blog:manageentries', $sitecontext)) {
    print_error('cannoteditpostorblog');
}

// Make sure that the person trying to edit have access right
if ($id) {
    if (!$existing = $DB->get_record('post', array('id'=>$id))) {
        print_error('wrongpostid', 'blog');
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

if ($action === 'delete'){
    if (!$existing) {
        print_error('wrongpostid', 'blog');
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
        echo $OUTPUT->confirm(get_string('blogdeleteconfirm', 'blog'), new moodle_url('edit.php', $optionsyes),new moodle_url( 'index.php', $optionsno));
        echo $OUTPUT->footer();
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
                print_error('wrongpostid', 'blog');
            }
            do_edit($fromform, $blogeditform);
        break;
        default :
            print_error('invalidaction');
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
            print_error('wrongpostid', 'blog');
        }
        $post->id           = $existing->id;
        $post->subject      = $existing->subject;
        $post->summary      = $existing->summary;
        $post->publishstate = $existing->publishstate;
        $post->format       = $existing->format;
        $post->tags = tag_get_tags_array('post', $post->id);
        $post->action       = $action;
        $strformheading = get_string('updateentrywithid', 'blog');

    break;
    default :
        print_error('unknowaction');
}

// done here in order to allow deleting of posts with wrong user id above
if (!$user = $DB->get_record('user', array('id'=>$userid))) {
    print_error('invaliduserid');
}
$navlinks = array();
$navlinks[] = array('name' => fullname($user), 'link' => "$CFG->wwwroot/user/view.php?id=$userid", 'type' => 'misc');
$navlinks[] = array('name' => $strblogs, 'link' => "$CFG->wwwroot/blog/index.php?userid=$userid", 'type' => 'misc');
$navlinks[] = array('name' => $strformheading, 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

print_header("$SITE->shortname: $strblogs", $SITE->fullname, $navigation,'','',true);
$blogeditform->set_data($post);
$blogeditform->display();


echo $OUTPUT->footer();


die;

/*****************************   edit.php functions  ***************************/

/**
* Delete blog post from database
*/
function do_delete($post) {
    global $returnurl, $DB;

    blog_delete_attachments($post);

    $status = $DB->delete_records('post', array('id'=>$post->id));
    tag_set('post', $post->id, array());
    
    add_to_log(SITEID, 'blog', 'delete', 'index.php?userid='. $post->userid, 'deleted blog entry with entry id# '. $post->id);
}

/**
 * Write a new blog entry into database
 */
function do_add($post, $blogeditform) {
    global $CFG, $USER, $returnurl, $DB;

    $post->module       = 'blog';
    $post->userid       = $USER->id;
    $post->lastmodified = time();
    $post->created      = time();

    // Insert the new blog entry.
    $post->id = $DB->insert_record('post', $post);
    // Add blog attachment
    if ($blogeditform->get_new_filename('attachment')) {
        if ($blogeditform->save_stored_file('attachment', SYSCONTEXTID, 'blog', $post->id, '/', false, $USER->id)) {
            $DB->set_field("post", "attachment", 1, array("id"=>$post->id));
        }
    }

    // Update tags.
    tag_set('post', $post->id, $post->tags);

    add_to_log(SITEID, 'blog', 'add', 'index.php?userid='.$post->userid.'&postid='.$post->id, $post->subject);
}

/**
 * @param . $post argument is a reference to the post object which is used to store information for the form
 * @param . $bloginfo_arg argument is reference to a blogInfo object.
 * @todo complete documenting this function. enable trackback and pingback between entries on the same server
 */
function do_edit($post, $blogeditform) {
    global $CFG, $USER, $returnurl, $DB;

    $post->lastmodified = time();

    if ($blogeditform->get_new_filename('attachment')) {
        blog_delete_attachments($post);
        if ($blogeditform->save_stored_file('attachment', SYSCONTEXTID, 'blog', $post->id, '/', false, $USER->id)) {
            $post->attachment = 1;
        } else {
            $post->attachment = 1;
        }
    }

    // Update record
    $DB->update_record('post', $post);
    tag_set('post', $post->id, $post->tags);

    add_to_log(SITEID, 'blog', 'update', 'index.php?userid='.$USER->id.'&postid='.$post->id, $post->subject);
}

?>

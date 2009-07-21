<?php // $Id$

require_once('../config.php');
require_once('lib.php');
require_once('locallib.php');
require_once($CFG->dirroot.'/lib/weblib.php');
require_once($CFG->dirroot.'/blog/lib.php');

require_login();

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

$tagid       = optional_param('id', 0, PARAM_INT); // tag id
$tagname     = optional_param('tag', '', PARAM_TAG); // tag

$edit        = optional_param('edit', -1, PARAM_BOOL);
$userpage    = optional_param('userpage', 0, PARAM_INT); // which page to show
$perpage     = optional_param('perpage', 24, PARAM_INT);

$systemcontext   = get_context_instance(CONTEXT_SYSTEM);

if ($tagname) {
    $tag = tag_get('name', $tagname, '*');
} else if ($tagid) {
    $tag = tag_get('id', $tagid, '*');
}

if (empty($tag)) {
    redirect($CFG->wwwroot.'/tag/search.php');
}

$PAGE->set_url('tag/index.php', array('id' => $tag->id));
$PAGE->set_subpage($tag->id);
$PAGE->set_context($systemcontext);
$PAGE->set_blocks_editing_capability('moodle/tag:editblocks');

if (($edit != -1) and $PAGE->user_allowed_editing()) {
    $USER->editing = $edit;
}

$tagname = tag_display_name($tag);

$navlinks = array();
$navlinks[] = array('name' => get_string('tags', 'tag'), 'link' => "{$CFG->wwwroot}/tag/search.php", 'type' => '');
$navlinks[] = array('name' => $tagname, 'link' => '', 'type' => '');

$navigation = build_navigation($navlinks);
$title = get_string('tag', 'tag') .' - '. $tagname;

$button = '';
if ($PAGE->user_allowed_editing() ) {
    $button = update_tag_button($tag->id);
}
print_header_simple($title, '', $navigation, '', '', '', $button);

// Manage all tags links
if (has_capability('moodle/tag:manage', $systemcontext)) {
    echo '<div class="managelink"><a href="'. $CFG->wwwroot .'/tag/manage.php">'. get_string('managetags', 'tag') .'</a></div>' ;
}

$tagname  = tag_display_name($tag);

if ($tag->flag > 0 && has_capability('moodle/tag:manage', $systemcontext)) {
    $tagname =  '<span class="flagged-tag">' . $tagname . '</span>';
}

print_heading($tagname, '', 2, 'headingblock header tag-heading');
tag_print_management_box($tag);
tag_print_description_box($tag);

echo '<div style="text-align: center "><p><a href="#course">'.get_string('courses').
 '</a> | <a href="#blog">'.get_string('relatedblogs', 'tag').
 '</a> | <a href="#user">'.get_string('users').'</a></p></div>';

// Display courses tagged with the tag
require_once($CFG->dirroot.'/tag/coursetagslib.php');
if ($courses = coursetag_get_tagged_courses($tag->id)) {

    $totalcount = count( $courses );
    print_box_start('generalbox', 'tag-blogs'); //could use an id separate from tag-blogs, but would have to copy the css style to make it look the same

    $heading = get_string('courses') . ' ' . get_string('taggedwith', 'tag', $tagname) .': '. $totalcount;
    echo "<a name='course'></a>";
    print_heading($heading, '', 3);

    foreach ($courses as $course) {
        print_course($course);
	}

    print_box_end();
}

// Print up to 10 previous blogs entries

// I was not able to use get_items_tagged_with() because it automatically
// tries to join on 'blog' table, since the itemtype is 'blog'. However blogs
// uses the post table so this would not really work.    - Yu 29/8/07
if (has_capability('moodle/blog:view', $systemcontext)) {  // You have to see blogs obviously

    $count = 10;
    if ($blogs = blog_fetch_entries('', $count, 0, 'site', '', $tag->id)) {

        print_box_start('generalbox', 'tag-blogs');
        $heading = get_string('relatedblogs', 'tag', $tagname). ' ' . get_string('taggedwith', 'tag', $tagname);
        echo "<a name='blog'></a>";
        print_heading($heading, '', 3);

        echo '<ul id="tagblogentries">';
        foreach ($blogs as $blog) {
            if ($blog->publishstate == 'draft') {
                $class = 'class="dimmed"';
            } else {
                $class = '';
            }
            echo '<li '.$class.'>';
            echo '<a '.$class.' href="'.$CFG->wwwroot.'/blog/index.php?postid='.$blog->id.'">';
            echo format_string($blog->subject);
            echo '</a>';
            echo ' - ';
            echo '<a '.$class.' href="'.$CFG->wwwroot.'/user/view.php?id='.$blog->userid.'">';
            echo fullname($blog);
            echo '</a>';
            echo ', '. userdate($blog->lastmodified);
            echo '</li>';
        }
        echo '</ul>';

        echo '<p class="moreblogs"><a href="'.$CFG->wwwroot.'/blog/index.php?filtertype=site&amp;filterselect=0&amp;tagid='.$tag->id.'">'.get_string('seeallblogs', 'tag', $tagname).'</a></p>';

        print_box_end();
    }
}

$usercount = tag_record_count('user', $tag->id);
if ($usercount > 0) {

    //user table box
    print_box_start('generalbox', 'tag-user-table');

    $heading = get_string('users'). ' ' . get_string('taggedwith', 'tag', $tagname) . ': ' . $usercount;
    echo "<a name='user'></a>";
    print_heading($heading, '', 3);

    $baseurl = $CFG->wwwroot.'/tag/index.php?id=' . $tag->id;

    print_paging_bar($usercount, $userpage, $perpage, $baseurl.'&amp;', 'userpage');
    tag_print_tagged_users_table($tag, $userpage * $perpage, $perpage);
    print_box_end();
}

print_footer();
?>

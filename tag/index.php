<?php // $Id$

require_once('../config.php');
require_once('lib.php');
require_once('pagelib.php');
require_once($CFG->dirroot.'/lib/weblib.php');
require_once($CFG->dirroot.'/blog/lib.php');

require_login();

if (empty($CFG->usetags)) {
    error(get_string('tagsaredisabled', 'tag'));
}

$tagid       = optional_param('id',     0,      PARAM_INT);   // tag id
$edit        = optional_param('edit', -1, PARAM_BOOL);
$userpage    = optional_param('userpage', 0, PARAM_INT);      // which page to show
$perpage     = optional_param('perpage', 24, PARAM_INT);

$tag      = tag_by_id($tagid);

if (!$tag) {
    redirect($CFG->wwwroot.'/tag/search.php');
}

//create a new page_tag object, defined in pagelib.php
$PAGE = page_create_object(PAGE_TAG_INDEX, $tag->id);
$pageblocks = blocks_setup($PAGE,BLOCKS_PINNED_BOTH);
$PAGE->tag_object = $tag;

if (($edit != -1) and $PAGE->user_allowed_editing()) {
    $USER->editing = $edit;
}


$PAGE->print_header();


echo '<table border="0" cellpadding="3" cellspacing="0" width="100%" id="layout-table">';
echo '<tr valign="top">';

//----------------- left column -----------------

$blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_LEFT]), 210);

if (blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing()) {
    echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="left-column">';
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    echo '</td>';
}



//----------------- middle column -----------------

echo '<td valign="top" id="middle-column">';

$tagname  = tag_display_name($tag);

$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
if ($tag->flag > 0 && has_capability('moodle/tag:manage', $systemcontext)) {
    $tagname =  '<span class="flagged-tag">' . $tagname . '</span>';
}

print_heading($tagname, '', 2, 'headingblock header tag-heading');

print_tag_management_box($tag);

print_tag_description_box($tag);


$usercount = count_items_tagged_with($tag->id,'user');

if ($usercount > 0) {

    //user table box
    print_box_start('generalbox', 'tag-user-table');

    $heading = get_string('userstaggedwith', 'tag', $tagname) . ': ' . $usercount;
    print_heading($heading, '', 3);

    $baseurl = $CFG->wwwroot.'/tag/index.php?id=' . $tag->id;

    print_paging_bar($usercount, $userpage, $perpage, $baseurl.'&amp;', 'userpage');

    print_tagged_users_table($tag, $userpage * $perpage, $perpage);

    print_box_end();

}




// Print last 10 blogs

// I was not able to use get_items_tagged_with() because it automatically 
// tries to join on 'blog' table, since the itemtype is 'blog'. However blogs
// uses the post table so this would not really work.    - Yu 29/8/07
if ($blogs = fetch_entries('', 10, 0, 'site', '', $tag->id)) {

    print_box_start('generalbox', 'tag-blogs');

    print_heading(get_string('relatedblogs', 'tag'), '', 3);

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

    echo '<p class="moreblogs"><a href="'.$CFG->wwwroot.'/blog/index.php?filtertype=site&filterselect=0&tagid='.$tag->id.'">'.get_string('seeallblogs', 'tag').'</a>...</p>';

    print_box_end();
}


echo '</td>';


//----------------- right column -----------------

$blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 210);

if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $PAGE->user_is_editing()) {
    echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="right-column">';
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
    echo '</td>';
}

/// Finish the page
echo '</tr></table>';



$PAGE->print_footer();


?>

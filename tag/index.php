<?php // $Id$

require_once('../config.php');
require_once('lib.php');
require_once('pagelib.php');
require_once($CFG->dirroot.'/lib/weblib.php');
if (!empty($THEME->customcorners)) {
    require_once($CFG->dirroot.'/lib/custom_corners_lib.php');
}

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

echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="left-column">';
if(blocks_have_content($pageblocks, BLOCK_POS_LEFT) || $PAGE->user_is_editing()) {
    if (!empty($THEME->customcorners)) print_custom_corners_start();
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_LEFT);
    if (!empty($THEME->customcorners)) print_custom_corners_end();
}
echo '</td>';



//----------------- middle column -----------------

echo '<td valign="top" id="middle-column">';

if (!empty($THEME->customcorners)) print_custom_corners_start(TRUE);


$tagname  = tag_display_name($tag);

$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
if ($tag->flag > 0 && has_capability('moodle/tag:manage', $systemcontext)) {
    $tagname =  '<span class="flagged-tag">' . $tagname . '</span>';
}

print_heading($tagname, '', 2, 'headingblock header tag-heading');

print_tag_description_box($tag);


$usercount = count_items_tagged_with($tag->id,'user');

if ($usercount > 0) {

    $heading = get_string('userstaggedwith', 'tag', $tagname) . ': ' . $usercount;
    print_heading($heading, '', 3);

    $baseurl = $CFG->wwwroot.'/tag/index.php?id=' . $tag->id;

    print_paging_bar($usercount, $userpage, $perpage, $baseurl.'&amp;', 'userpage');

    print_tagged_users_table($tag, $userpage * $perpage, $perpage);

}

if (!empty($THEME->customcorners)) print_custom_corners_end();
echo '</td>';



//----------------- right column -----------------

$blocks_preferred_width = bounded_number(180, blocks_preferred_width($pageblocks[BLOCK_POS_RIGHT]), 210);

echo '<td style="vertical-align: top; width: '.$blocks_preferred_width.'px;" id="right-column">';
if (blocks_have_content($pageblocks, BLOCK_POS_RIGHT) || $PAGE->user_is_editing()) {
    if (!empty($THEME->customcorners)) print_custom_corners_start();
    blocks_print_group($PAGE, $pageblocks, BLOCK_POS_RIGHT);
    if (!empty($THEME->customcorners)) print_custom_corners_end();
}
echo '</td>';

/// Finish the page
echo '</tr></table>';



$PAGE->print_footer();


?>

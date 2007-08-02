<?php // $Id$

require_once('../config.php');
require_once('lib.php');
require_once($CFG->dirroot.'/lib/weblib.php');

require_login();

if (empty($CFG->usetags)) {
    error(get_string('tagsaredisabled', 'tag'));
}

$tagid       = optional_param('id',     0,      PARAM_INT);   // tag id
$userpage    = optional_param('userpage', 0, PARAM_INT);      // which page to show
$perpage     = optional_param('perpage', 20, PARAM_INT);

$tag      = tag_by_id($tagid);

if (!$tag) {
    redirect($CFG->wwwroot.'/tag/search.php');
}

$tagname  = tag_display_name($tag);

$navlinks = array();
$navlinks[] = array('name' => get_string('tags', 'tag'), 'link' => "{$CFG->wwwroot}/tag/search.php", 'type' => '');
$navlinks[] = array('name' => $tagname, 'link' => '', 'type' => '');

$navigation = build_navigation($navlinks);
print_header_simple(get_string('tag', 'tag') . ' - ' . $tagname, '', $navigation);

$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
if ($tag->flag > 0 && has_capability('moodle/tag:manage', $systemcontext)) {
    $tagname =  '<span class="flagged-tag">' . $tagname . '</span>';
}

print_heading($tagname, '', 2);

print_tag_management_box($tag);

print_tag_description_box($tag);


$usercount = count_items_tagged_with($tagid,'user');

if ($usercount > 0) {

    print_heading(get_string('userstaggedwith', 'tag', $tagname) . ': ' . $usercount, '', 3);

    $baseurl = $CFG->wwwroot.'/tag/index.php?id=' . $tagid;
    
    print_paging_bar($usercount, $userpage, $perpage, $baseurl.'&amp;', 'userpage');
    
    print_tagged_users_table($tag, $userpage * $perpage, $perpage);

}

print_footer();




?>

<?php

require_once('../config.php');
require_once('lib.php');
require_once($CFG->dirroot.'/lib/weblib.php');

global $CFG;
require_login();

if( empty($CFG->usetags)) {
    error(get_string('tagsaredisabled', 'tag'));
}

$query      = optional_param('query', '', PARAM_TEXT);
$page        = optional_param('page', 0, PARAM_INT);      // which page to show
$perpage     = optional_param('perpage', 18, PARAM_INT);

$navlinks = array();
$navlinks[] = array('name' => get_string('tags', 'tag'), 'link' => "{$CFG->wwwroot}/tag/search.php", 'type' => '');
$navigation = build_navigation($navlinks);

$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
if ( has_capability('moodle/tag:manage',$systemcontext) ) {
    $manage_link =  "<a href=\"{$CFG->wwwroot}/tag/manage.php\">" . get_string('managetags', 'tag') . "</a>" ;
}

print_header_simple(get_string('tags', 'tag'), '', $navigation, '', '', '', $manage_link);

print_heading(get_string('searchtags', 'tag'), '', 2);

print_tag_search_box();

if(!empty($query)) {
     print_tag_search_results($query, $page, $perpage);
}

echo '<br/><br/>';

print_box_start('generalbox', 'big-tag-cloud-box');
print_tag_cloud(popular_tags_count(60), true, 170,70);
print_box_end();

print_footer();

?>

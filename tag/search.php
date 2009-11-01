<?php

require_once('../config.php');
require_once('lib.php');
require_once('locallib.php');

require_login();

if (empty($CFG->usetags)) {
    print_error('tagsaredisabled', 'tag');
}

$query   = optional_param('query', '', PARAM_RAW);
$page    = optional_param('page', 0, PARAM_INT); // which page to show
$perpage = optional_param('perpage', 18, PARAM_INT);

$params = array();
if ($query) {
    $params['query'] = $query;
}
if ($page) {
    $params['page'] = $page;
}
if ($perpage) {
    $params['perpage'] = $perpage;
}
$PAGE->set_url('tag/search.php', $params);
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));

$systemcontext = get_context_instance(CONTEXT_SYSTEM);
$manage_link = '&nbsp;';

$PAGE->navbar->add(get_string('tags', 'tag'), new moodle_url($CFG->wwwroot.'/tag/search.php'));
$PAGE->set_title(get_string('tags', 'tag'));
echo $OUTPUT->header();

if ( has_capability('moodle/tag:manage',$systemcontext) ) {
    echo '<div class="managelink"><a href="'. $CFG->wwwroot .'/tag/manage.php">' . get_string('managetags', 'tag') . '</a></div>' ;
}

echo $OUTPUT->heading(get_string('searchtags', 'tag'), 2);

tag_print_search_box();

if(!empty($query)) {
     tag_print_search_results($query, $page, $perpage);
}

echo '<br/><br/>';

echo $OUTPUT->box_start('generalbox', 'big-tag-cloud-box');
tag_print_cloud(150);
echo $OUTPUT->box_end();

echo $OUTPUT->footer();

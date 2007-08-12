<?php // $Id$

require_once('../config.php');
require_once('lib.php');
require_once($CFG->dirroot.'/lib/weblib.php');

require_login();

if( empty($CFG->usetags)) {
    error(get_string('tagsaredisabled', 'tag'));
}

//managing tags requires moodle/tag:manage capability
$systemcontext   = get_context_instance(CONTEXT_SYSTEM);
require_capability('moodle/tag:manage', $systemcontext);

$tagschecked    = optional_param('tagschecked', array());
$action         = optional_param('action', '', PARAM_ALPHA);

$navlinks = array();
$navlinks[] = array('name' => get_string('tags', 'tag'), 'link' => "{$CFG->wwwroot}/tag/search.php", 'type' => '');
$navlinks[] = array('name' => get_string('managetags', 'tag'), 'link' => '', 'type' => '');

$navigation = build_navigation($navlinks);
print_header_simple(get_string('managetags', 'tag'), '', $navigation);

switch($action) {
    
    case 'delete':
        
        $notice = tag_name_from_string(implode($tagschecked, ', '));
        $notice = str_replace(',', ', ', $notice);
        $notice .= ' --  ' . get_string('deleted','tag');
        notify($notice , 'green');
        
        tag_delete(implode($tagschecked, ','));
        break;
    case 'reset':
        
        $notice = tag_name_from_string(implode($tagschecked, ', '));
        $notice = str_replace(',', ', ', $notice);
        $notice .= ' -- ' . get_string('reset','tag');
        notify($notice , 'green');        
        
        tag_flag_reset(implode($tagschecked, ','));
        break;
}

echo '<br/>';

print_tag_management_list();


echo '<br/>';

print_footer();

?>

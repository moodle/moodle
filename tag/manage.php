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
$newnames       = optional_param('newname', array());
$action         = optional_param('action', '', PARAM_ALPHA);

$navlinks = array();
$navlinks[] = array('name' => get_string('tags', 'tag'), 'link' => "{$CFG->wwwroot}/tag/search.php", 'type' => '');
$navlinks[] = array('name' => get_string('managetags', 'tag'), 'link' => '', 'type' => '');

$navigation = build_navigation($navlinks);
print_header_simple(get_string('managetags', 'tag'), '', $navigation);

$notice = tag_name_from_string(implode($tagschecked, ', '));
$notice = str_replace(',', ', ', $notice);

switch($action) {

    case 'delete':

        $notice .= ' --  ' . get_string('deleted','tag');

        tag_delete(implode($tagschecked, ','));
        
        break;
        
    case 'reset':

        $notice .= ' -- ' . get_string('reset','tag');
        
        tag_flag_reset(implode($tagschecked, ','));
        
        break;
        
    case 'changename':
        
        $normalized_new_names_csv = tag_normalize( str_replace(',,','',implode($newnames, ',')) );
        
        //tag names entered might already exist
        $existing_tags = tags_id( $normalized_new_names_csv );
        
        //notice to warn that names already exist
        $err_notice = '';
        foreach ($existing_tags as $name => $tag){
            $err_notice .= $name . ', ';
        }
        if(!empty($err_notice)){
            $err_notice .= '-- ' . get_string('namesalreadybeeingused','tag');
        }
                
        
        //update tag names with the names passed in $newnames
        $tags_names_changed = array();
        foreach ($tagschecked as $tag_id){
            $tags_names_changed[$tag_id] = str_replace(',','',$newnames[$tag_id]) ;
        }

        $tags_names_updated = tag_update_name($tags_names_changed);
        
        //notice to inform what tags had their names effectively updated
        $notice = implode($tags_names_updated, ', ');
        if(!empty($notice)){
            $notice .= ' -- ' . get_string('updated','tag');
        }

        break;
}

echo '<br/>';

notify($err_notice, 'red');
notify($notice , 'green');

print_tag_management_list();


echo '<br/>';

print_footer();

?>

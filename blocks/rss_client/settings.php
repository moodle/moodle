<?php  //$Id$

require_once($CFG->libdir.'/rsslib.php');

$settings->add(new admin_setting_configtext('block_rss_client_num_entries', get_string('numentries', 'block_rss_client'),
                   get_string('clientnumentries', 'block_rss_client'), 5, PARAM_INT));

$settings->add(new admin_setting_configtext('block_rss_client_timeout', get_string('timeout2', 'block_rss_client'),
                   get_string('timeout', 'block_rss_client'), 30, PARAM_INT));

$options = array (SUBMITTERS_ALL_ACCOUNT_HOLDERS => get_string('everybody'),
                  SUBMITTERS_ADMIN_ONLY => get_string('administrators'),
                  SUBMITTERS_ADMIN_AND_TEACHER => get_string('administratorsandteachers'));
$settings->add(new admin_setting_configselect('block_rss_client_submitters', get_string('submitters2', 'block_rss_client'),
                   get_string('submitters', 'block_rss_client'), SUBMITTERS_ADMIN_ONLY, $options));

$link ='<a href="'.$CFG->wwwroot.'/blocks/rss_client/block_rss_client_action.php">'.get_string('feedsaddedit', 'block_rss_client').'</a>';
$settings->add(new admin_setting_heading('block_rss_addheading', '', $link));


?>

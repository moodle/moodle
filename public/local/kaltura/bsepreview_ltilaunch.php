<?php
require_once(dirname(__FILE__).'/../../config.php');
require_once($CFG->dirroot.'/local/kaltura/locallib.php');

require_login();

global $PAGE;

$playurl = required_param('playurl', PARAM_URL);

$launch = array();
$launch['id'] = 1;
$launch['cmid'] = 0;
$launch['title'] = 'Browse and Embed - Preview Entry';
$launch['module'] = KAF_BROWSE_EMBED_MODULE;
$launch['course'] = $PAGE->course;
$launch['width'] = '300';
$launch['height'] = '300';
$launch['custom_publishdata'] = '';
$launch['source'] = $playurl;
echo local_kaltura_request_lti_launch($launch, false);

?>
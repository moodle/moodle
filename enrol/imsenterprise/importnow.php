<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

if (!$site = get_site()) {
    redirect("index.php");
}

/// get language strings
$str = get_strings(array('enrolments', 'users', 'administration', 'settings'));
$navlinks = array();
$navlinks[] = array('name' => $str->administration, 'link' => "../../$CFG->admin/index.php", 'type' => 'misc');
$navlinks[] = array('name' => $str->enrolments, 'link' => null, 'type' => 'misc');
$navlinks[] = array('name' => 'IMS import', 'link' => null, 'type' => 'misc');
$navigation = build_navigation($navlinks);

print_header("$site->shortname: $str->enrolments", $site->fullname, $navigation);

require_once('enrol.php');

//echo "Creating the IMS Enterprise enroller object\n";
$enrol = new enrolment_plugin_imsenterprise();

?>
<p>Launching the IMS Enterprise "cron" function. The import log will appear below (giving details of any
problems that might require attention).</p>
<pre style="margin:10px; padding: 2px; border: 1px solid black; background-color: white; color: black;"><?php
//error_reporting(E_ALL);
$enrol->cron();
?></pre><?php
print_footer();

exit;
?>

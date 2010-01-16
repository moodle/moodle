<?php
require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_login();
require_capability('moodle/site:config', get_context_instance(CONTEXT_SYSTEM));

$site = get_site();

/// get language strings
$str = get_strings(array('enrolments', 'users', 'administration', 'settings'));

$PAGE->set_url('/enrol/imsenterprise/importnow.php');
$PAGE->set_title("$site->shortname: $str->enrolments");
$PAGE->set_heading($site->fullname);
$PAGE->navbar->add($str->administration, new moodle_url('/admin/index.php'));
$PAGE->navbar->add($str->enrolments);
$PAGE->navbar->add('IMS import');
echo $OUTPUT->header();

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
echo $OUTPUT->footer();

exit;
?>

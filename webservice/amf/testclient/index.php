<?php
require "../../../config.php";
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('externalservice');

//page nav bar
$PAGE->set_url('/webservice/amf/testclient/index.php');
$node = $PAGE->settingsnav->find('testclient', navigation_node::TYPE_SETTING);
if ($node) {
    $node->make_active();
}
$PAGE->navbar->add(get_string('amftestclient', 'webservice'));

$PAGE->set_title('Test Client');
$PAGE->set_heading('Test Client');
echo $OUTPUT->header();

$url = $CFG->wwwroot.'/webservice/amf/testclient/AMFTester.swf';
$output = <<<OET
<div id="moodletestclient">
<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="100%" height="1000">
<param name="movie" value="$url" />
<param name="base" value="." />
<param name="allowscriptaccess" value="sameDomain" />
<param name="FlashVars" value="rooturl=$CFG->wwwroot" />
<!--[if !IE]>-->
<object type="application/x-shockwave-flash" data="$url" width="100%" height="1000">
  <param name="base" value="." />
  <param name="allowscriptaccess" value="sameDomain" />
  <param name="FlashVars" value="rooturl=$CFG->wwwroot" />
<!--<![endif]-->
<p>You need to install Flash 9.0</p>
<!--[if !IE]>-->
</object>
<!--<![endif]-->
</object>
</span>
OET;

echo $output;

echo $OUTPUT->footer();

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

$flashvars = new stdClass();
$flashvars->rooturl =$CFG->wwwroot;


$PAGE->requires->js('/lib/swfobject/swfobject.js', true);

$PAGE->requires->js_function_call('swfobject.embedSWF',
                array($CFG->wwwroot.'/webservice/amf/testclient/AMFTester.swf', //movie
                    'moodletestclient', // div id
                    '100%', // width
                    '1000', // height
                    '9.0', // version
                    false,//no express install swf
                    $flashvars), //flash vars
                true
            );

$PAGE->set_title('Test Client');
$PAGE->set_heading('Test Client');
echo $OUTPUT->header();
echo '<div id="moodletestclient">
      <p>You need to install Flash 9.0</p>
    </div>';
echo $OUTPUT->footer();

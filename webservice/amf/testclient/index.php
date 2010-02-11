<?php
require "../../../config.php";

$PAGE->set_url('/webservice/amf/testclient/index.php');

$flashvars = new object();
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

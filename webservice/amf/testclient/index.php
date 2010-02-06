<?php
require "../../../config.php";

die('TODO');

$args['movie'] = $CFG->wwwroot.'/webservice/amf/testclient/moodleclient.swf';
$args['width'] = '100%';
$args['height'] = 500;
$args['majorversion'] = 9;
$args['build'] = 0;
$args['allowscriptaccess'] = 'never';
$args['quality'] = 'high';
$args['flashvars'] = 'amfurl='.$CFG->wwwroot.'/webservice/amf/server.php';
$args['setcontainercss'] = 'true';

$PAGE->requires->js('/lib/ufo.js');
$PAGE->requires->js_function_call('M.util.create_UFO_object', array('moodletestclient', $args));

$PAGE->set_title('Test Client');
$PAGE->set_heading('Test Client');
echo $OUTPUT->header();
echo '<div id="moodletestclient">
      <p>You need to install Flash 9.0</p>
    </div>';
echo $OUTPUT->footer();

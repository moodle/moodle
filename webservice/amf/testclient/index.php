<?php
require "../../../config.php";


$args['movie'] = $CFG->wwwroot.'/webservice/amf/testclient/moodleclient.swf';
$args['width'] = '100%';
$args['height'] = 500;
$args['majorversion'] = 9;
$args['build'] = 0;
$args['allowscriptaccess'] = 'never';
$args['quality'] = 'high';
$args['flashvars'] = 'amfurl='.$CFG->wwwroot.'/webservice/amf/server.php';
$args['setcontainercss'] = 'true';


$PAGE->requires->js('lib/ufo.js')->in_head();
$PAGE->requires->data_for_js('FO', $args);
$PAGE->requires->js_function_call('create_UFO_object', Array('moodletestclient'));

print_header_simple('Test Client', 'Test Client');
echo '<div id="moodletestclient">
      <p>You need to install Flash 9.0</p>
    </div>';
echo $OUTPUT->footer();

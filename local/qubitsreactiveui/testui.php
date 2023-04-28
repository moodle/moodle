<?php
require_once('../../config.php');


$PAGE->set_pagelayout('base');
$domelementid = "tractui";
$PAGE->requires->js_call_amd('local_qubitsreactiveui/main', 'init', [$domelementid]);

echo $OUTPUT->header();

echo "<h1>Reactive UI</h1>";

echo $OUTPUT->footer();
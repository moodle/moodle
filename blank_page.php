<?php
require_once(dirname(__FILE__) . '/config.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('standard');
$PAGE->set_heading('Blank Page');
echo $OUTPUT->header();
?>

<?php
echo $OUTPUT->footer();
?>
<?php
require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir.'/adminlib.php');

//admin_externalpage_setup('local_lsucli');

$context = context_system::instance();

$PAGE->set_context($context);
$PAGE->set_url('/local/lsucli/index.php');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('lsucli', 'local_lsucli'));

$cliscripts = array_diff(scandir($CFG->dirroot . '/admin/cli'), array('..', '.'));

$table = new html_table();
$table->head = array(get_string('scriptname', 'local_lsucli'), get_string('schedule'));

foreach ($cliscripts as $script) {
    if (substr($script, -4) !== '.php') {
        continue;
    }

    $schedule_url = new moodle_url('/local/lsucli/schedule.php', array('script' => $script));
    $schedule_button = new single_button($schedule_url, get_string('schedule'));
    $row = new html_table_row(array($script, $OUTPUT->render($schedule_button)));
    $table->data[] = $row;
}

echo html_writer::table($table);

echo $OUTPUT->footer();

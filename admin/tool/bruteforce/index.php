<?php
// Admin dashboard for tool_bruteforce.

require(__DIR__ . '/../../../config.php');

require_login();
$context = context_system::instance();
require_capability('tool/bruteforce:viewreports', $context);

$PAGE->set_url(new moodle_url('/admin/tool/bruteforce/index.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('pluginname', 'tool_bruteforce'));
$PAGE->set_heading(get_string('pluginname', 'tool_bruteforce'));

echo $OUTPUT->header();

$blocks = $DB->get_records('tool_bruteforce_blocks');
if ($blocks) {
    $table = new html_table();
    $table->head = [
        get_string('userid'),
        get_string('ip', 'tool_bruteforce'),
        get_string('expires', 'tool_bruteforce'),
    ];
    foreach ($blocks as $block) {
        $table->data[] = [$block->userid, $block->ip, userdate($block->expires)];
    }
    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('noblocks', 'tool_bruteforce'), 'notifymessage');
}

echo $OUTPUT->footer();

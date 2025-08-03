<?php
// Minimal dashboard for tool_bruteforce showing active blocks.

require(__DIR__.'/../../../config.php');
require_login();
require_capability('tool/bruteforce:manage', context_system::instance());

$PAGE->set_url(new moodle_url('/admin/tool/bruteforce/index.php'));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('pluginname', 'tool_bruteforce'));
$PAGE->set_heading(get_string('pluginname', 'tool_bruteforce'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('activeblocks', 'tool_bruteforce'));

echo html_writer::div(
    html_writer::link(new moodle_url('/admin/tool/bruteforce/lists.php', ['list' => 'white']),
        get_string('whitelist', 'tool_bruteforce')) . ' | ' .
    html_writer::link(new moodle_url('/admin/tool/bruteforce/lists.php', ['list' => 'black']),
        get_string('blacklist', 'tool_bruteforce')),
    'mb-3'
);

$blocks = $DB->get_records('tool_bruteforce_block');

if (empty($blocks)) {
    echo $OUTPUT->notification(get_string('noblocks', 'tool_bruteforce'), 'info');
} else {
    $table = new html_table();
    $table->head = [
        get_string('type', 'tool_bruteforce'),
        get_string('value', 'tool_bruteforce'),
        get_string('timerelease', 'tool_bruteforce'),
    ];
    foreach ($blocks as $block) {
        $table->data[] = [
            s($block->type),
            s($block->value),
            userdate($block->timerelease),
        ];
    }
    echo html_writer::table($table);
}

echo $OUTPUT->footer();

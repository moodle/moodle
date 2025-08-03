<?php
// Manual unblock page for tool_bruteforce.

require(__DIR__.'/../../../config.php');
require_login();
require_capability('tool/bruteforce:manage', context_system::instance());

$id = required_param('id', PARAM_INT);
$block = $DB->get_record('tool_bruteforce_block', ['id' => $id], '*', MUST_EXIST);

$mform = new \tool_bruteforce\form\unblock(null, [
    'id' => $block->id,
    'type' => $block->type,
    'value' => $block->value,
]);

$PAGE->set_url(new moodle_url('/admin/tool/bruteforce/unblock.php', ['id' => $id]));
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('unblock', 'tool_bruteforce'));
$PAGE->set_heading(get_string('unblock', 'tool_bruteforce'));

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/admin/tool/bruteforce/index.php'));
} else if ($data = $mform->get_data()) {
    \tool_bruteforce\api::unblock($block->type, $block->value, $USER->id, $data->reason ?? null);
    redirect(new moodle_url('/admin/tool/bruteforce/index.php'), get_string('unblocked', 'tool_bruteforce'));
}

echo $OUTPUT->header();
$mform->display();
echo $OUTPUT->footer();

<?php
// Management page for whitelist and blacklist entries.

require(__DIR__ . '/../../../config.php');
require_login();
require_capability('tool/bruteforce:manage', context_system::instance());

$list = optional_param('list', 'white', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);

$baseurl = new moodle_url('/admin/tool/bruteforce/lists.php', ['list' => $list]);
$PAGE->set_url($baseurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string($list . 'list', 'tool_bruteforce'));
$PAGE->set_heading(get_string($list . 'list', 'tool_bruteforce'));

require_once(__DIR__ . '/classes/form/listentry.php');

if ($delete && confirm_sesskey()) {
    \tool_bruteforce\api::remove_list_entry($delete);
    redirect($baseurl);
}

$record = null;
if ($id) {
    $record = $DB->get_record('tool_bruteforce_list', ['id' => $id, 'listtype' => $list]);
}
$mform = new \tool_bruteforce\form\listentry(null, ['list' => $list]);
if ($record) {
    $mform->set_data($record);
}

if ($mform->is_cancelled()) {
    redirect($baseurl);
} else if ($data = $mform->get_data()) {
    if (!empty($data->id)) {
        $update = (object) [
            'id' => $data->id,
            'type' => $data->type,
            'value' => $data->value,
            'comment' => $data->comment,
        ];
        $DB->update_record('tool_bruteforce_list', $update);
    } else {
        \tool_bruteforce\api::add_list_entry($list, $data->type, $data->value, $data->comment, $USER->id);
    }
    redirect($baseurl);
}

$otherlist = $list === 'white' ? 'black' : 'white';
$otherurl = new moodle_url('/admin/tool/bruteforce/lists.php', ['list' => $otherlist]);

echo $OUTPUT->header();
$tabs = [new tabobject('white', new moodle_url('/admin/tool/bruteforce/lists.php', ['list' => 'white']), get_string('whitelist', 'tool_bruteforce')),
         new tabobject('black', new moodle_url('/admin/tool/bruteforce/lists.php', ['list' => 'black']), get_string('blacklist', 'tool_bruteforce'))];
print_tabs([$tabs], $list);

$entries = \tool_bruteforce\api::get_list_entries($list);
if ($entries) {
    $table = new html_table();
    $table->head = [get_string('type', 'tool_bruteforce'), get_string('value', 'tool_bruteforce'),
        get_string('comment', 'tool_bruteforce'), get_string('addedby', 'tool_bruteforce'),
        get_string('timecreated', 'tool_bruteforce'), get_string('actions')];
    foreach ($entries as $entry) {
        $user = $entry->userid ? core_user::get_user($entry->userid, '*', MUST_EXIST) : null;
        $deleteurl = new moodle_url($baseurl, ['delete' => $entry->id, 'sesskey' => sesskey()]);
        $table->data[] = [s($entry->type), s($entry->value), s($entry->comment ?? ''),
            $user ? fullname($user) : '-', userdate($entry->timecreated),
            html_writer::link($deleteurl, get_string('delete'))];
    }
    echo html_writer::table($table);
} else {
    echo $OUTPUT->notification(get_string('nolistentries', 'tool_bruteforce'), 'info');
}

$mform->display();

echo $OUTPUT->footer();

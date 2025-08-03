<?php
// CLI management for whitelist and blacklist entries.

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

list($options, $unrecognized) = cli_get_params([
    'list' => '',
    'action' => '',
    'type' => '',
    'value' => '',
    'comment' => '',
    'id' => 0,
    'help' => false,
], [
    'l' => 'list',
    'a' => 'action',
    't' => 'type',
    'v' => 'value',
    'c' => 'comment',
    'i' => 'id',
    'h' => 'help',
]);

if (!empty($options['help']) || empty($options['action'])) {
    $help = <<<'HELP'
Manage whitelist/blacklist entries.

Options:
--list=white|black       List to manage (default white)
--action=add|delete|show Action to perform
--type=user|ip           Entry type (required for add)
--value=VALUE            Entry value (required for add)
--comment=TEXT           Optional comment for add
--id=ID                  Entry id (required for delete)
-h, --help               Show this help
HELP;
    cli_writeln($help);
    exit(0);
}

$list = $options['list'] ?: 'white';
$action = $options['action'];

if ($action === 'add') {
    if (empty($options['type']) || empty($options['value'])) {
        cli_error('Type and value required');
    }
    \tool_bruteforce\api::add_list_entry($list, $options['type'], $options['value'], $options['comment'] ?? null);
    cli_writeln(get_string('cli_entry_added', 'tool_bruteforce'));
    exit(0);
}

if ($action === 'delete') {
    if (empty($options['id'])) {
        cli_error('id required');
    }
    \tool_bruteforce\api::remove_list_entry((int)$options['id']);
    cli_writeln(get_string('cli_entry_deleted', 'tool_bruteforce'));
    exit(0);
}

if ($action === 'show') {
    $entries = \tool_bruteforce\api::get_list_entries($list);
    if (empty($entries)) {
        cli_writeln(get_string('cli_no_entries', 'tool_bruteforce'));
        exit(0);
    }
    cli_writeln(get_string('cli_listentries_header', 'tool_bruteforce'));
    foreach ($entries as $entry) {
        cli_writeln($entry->id . ' | ' . $entry->type . ' | ' . $entry->value . ' | ' . ($entry->comment ?? ''));
    }
    exit(0);
}

cli_error('Unknown action');

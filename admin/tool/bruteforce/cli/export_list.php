<?php
// CLI script to export whitelist/blacklist entries to CSV.

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'list' => null,
    'file' => null,
], [
    'h' => 'help',
]);

if (!empty($options['help']) || empty($options['list']) || empty($options['file'])) {
    $help = <<<'EOT'
Exports whitelist or blacklist entries to a CSV file.

Options:
--list=white|black   Source list.
--file=PATH          Output CSV file.
-h, --help           Show this help.
EOT;
    cli_write($help);
    exit(0);
}

$list = $options['list'];
$file = $options['file'];

$entries = \tool_bruteforce\api::get_list_entries($list);
$handle = fopen($file, 'w');
foreach ($entries as $entry) {
    fputcsv($handle, [$entry->type, $entry->value, $entry->comment]);
}
cli_writeln(get_string('cli_exportcount', 'tool_bruteforce', count($entries)));

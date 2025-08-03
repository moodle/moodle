<?php
// CLI script to import whitelist/blacklist entries from CSV.

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
Imports entries into whitelist or blacklist from a CSV file.

Options:
--list=white|black   Target list.
--file=PATH          CSV file with columns: type,value,comment
-h, --help           Show this help.
EOT;
    cli_write($help);
    exit(0);
}

$list = $options['list'];
$file = $options['file'];

if (!is_readable($file)) {
    cli_error("Cannot read file {$file}");
}

$handle = fopen($file, 'r');
$count = 0;
while (($data = fgetcsv($handle)) !== false) {
    $type = $data[0] ?? '';
    $value = $data[1] ?? '';
    $comment = $data[2] ?? null;
    \tool_bruteforce\api::add_list_entry($list, $type, $value, $comment, 0);
    $count++;
}
cli_writeln(get_string('cli_importcount', 'tool_bruteforce', $count));

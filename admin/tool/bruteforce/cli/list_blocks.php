<?php
// CLI script to list active brute force blocks.

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

list($options, $unrecognized) = cli_get_params([
    'help' => false,
], [
    'h' => 'help',
]);

if (!empty($options['help'])) {
    $help = <<<'EOT'
Lists active brute force blocks.

Options:
-h, --help          Show this help.
EOT;
    cli_write($help);
    exit(0);
}

$blocks = $DB->get_records_select('tool_bruteforce_block', 'timerelease > ?', [time()], 'timecreated DESC');

if (empty($blocks)) {
    cli_writeln(get_string('cli_no_blocks', 'tool_bruteforce'));
    exit(0);
}

cli_writeln(get_string('cli_listblocks_header', 'tool_bruteforce'));
foreach ($blocks as $block) {
    cli_writeln($block->type . ' | ' . $block->value . ' | ' . userdate($block->timerelease));
}

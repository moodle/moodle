<?php
// CLI script to clear expired brute force blocks.

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../../config.php');
require_once($CFG->libdir.'/clilib.php');

list($options, $unrecognized) = cli_get_params([
    'help' => false,
], [
    'h' => 'help',
]);

if (!empty($options['help'])) {
    $help = <<<'EOT'
Clears expired brute force blocks.

Options:
-h, --help          Show this help.
EOT;
    cli_write($help);
    exit(0);
}

$DB->delete_records_select('tool_bruteforce_block', 'timerelease < ?', [time()]);
cli_writeln('Expired blocks cleared.');

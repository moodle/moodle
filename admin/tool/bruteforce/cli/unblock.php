<?php
// CLI script to remove a block with audit logging.

define('CLI_SCRIPT', true);

require(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/clilib.php');

list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'user' => null,
    'ip' => null,
    'reason' => null,
    'actorid' => null,
], [
    'h' => 'help',
]);

if ($unrecognized) {
    $unrecognized = implode('\n  ', $unrecognized);
    cli_error(get_string('cliunknowoption', 'core', $unrecognized));
}

if ($options['help'] || (!$options['user'] && !$options['ip'])) {
    $help = "Unblock a user or IP with audit logging.\n\n".
        "--user=ID       User ID to unblock\n".
        "--ip=IP        IP address to unblock\n".
        "--actorid=ID   Acting user ID\n".
        "--reason=TEXT  Optional reason\n";
    cli_writeln($help);
    exit(0);
}

if (!$options['actorid']) {
    cli_error('actorid is required');
}

$actorid = (int)$options['actorid'];
if ($options['user']) {
    \tool_bruteforce\api::unblock('user', (string)$options['user'], $actorid, $options['reason']);
} else if ($options['ip']) {
\tool_bruteforce\api::unblock('ip', $options['ip'], $actorid, $options['reason']);
}
cli_writeln(get_string('unblocked', 'tool_bruteforce'));


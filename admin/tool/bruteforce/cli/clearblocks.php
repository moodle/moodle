<?php
// CLI script to list and clear brute force blocks.

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
require_once($CFG->libdir . '/clilib.php');

list($options) = cli_get_params([
    'list' => false,
    'clear' => false,
]);

if ($options['list']) {
    $blocks = $DB->get_records('tool_bruteforce_blocks');
    foreach ($blocks as $block) {
        cli_writeln("User: {$block->userid} IP: {$block->ip} Expires: " . userdate($block->expires));
    }
}

if ($options['clear']) {
    $DB->delete_records('tool_bruteforce_blocks');
    cli_writeln('All blocks cleared.');
}

if (!$options['list'] && !$options['clear']) {
    cli_writeln("Options:\n --list  List blocks\n --clear Clear all blocks");
}

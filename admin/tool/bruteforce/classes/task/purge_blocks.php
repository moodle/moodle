<?php
namespace tool_bruteforce\task;

// Scheduled task to remove expired blocks.

defined('MOODLE_INTERNAL') || die();

class purge_blocks extends \core\task\scheduled_task {
    /**
     * Return the task name.
     *
     * @return string
     */
    public function get_name(): string {
        return get_string('taskpurge', 'tool_bruteforce');
    }

    /**
     * Execute the task.
     *
     * @return void
     */
    public function execute(): void {
        global $DB;
        // TODO: Delete expired blocks from tool_bruteforce_blocks table.
    }
}

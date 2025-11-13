<?php
namespace local_lsucli\task;

defined('MOODLE_INTERNAL') || die();

class run_cli_script extends \core\task\adhoc_task {
    /**
     * Execute the task.
     */
    public function execute() {
        global $CFG;

        $script_name = $this->get_custom_data()->script_name;
        $script_path = $CFG->dirroot . '/cli/' . $script_name;

        if (file_exists($script_path)) {
            $php_executable = \core_php_bin::get_php_binary_path();
            $cmd = $php_executable . ' ' . escapeshellarg($script_path);
            proc_open($cmd, [], $pipes);
        }
    }
}

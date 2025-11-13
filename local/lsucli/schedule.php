<?php
require_once(__DIR__ . '/../../config.php');

$script = required_param('script', PARAM_FILE);

$task = new \local_lsucli\task\run_cli_script();
$task->set_custom_data(['script_name' => $script]);
\core\task\manager::queue_adhoc_task($task);

redirect(new moodle_url('/local/lsucli/index.php'));

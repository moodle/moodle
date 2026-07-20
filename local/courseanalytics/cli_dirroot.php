<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');
echo "CFG->dirroot=" . (isset($CFG->dirroot) ? $CFG->dirroot : '(not set)') . PHP_EOL;
echo "CFG->wwwroot=" . (isset($CFG->wwwroot) ? $CFG->wwwroot : '(not set)') . PHP_EOL;
?>
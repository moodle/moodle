<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');
// Check plugin in mdl_plugins and mdl_config_plugins
$installed = $DB->record_exists('plugin_versions', ['plugin'=>'local_courseanalytics']);
$configs = $DB->get_records('config_plugins', ['plugin'=>'local_courseanalytics']);
if ($installed) {
    echo "plugin_registered=1\n";
} else {
    echo "plugin_registered=0\n";
}
if (!empty($configs)) {
    foreach ($configs as $c) {
        echo "config:" . $c->name . "=" . $c->value . "\n";
    }
} else {
    echo "no_config_entries\n";
}
?>
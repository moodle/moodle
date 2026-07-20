<?php
define('CLI_SCRIPT', true);
require(__DIR__ . '/../../config.php');
try {
    $rows = $DB->get_records_sql("SELECT plugin, version FROM {plugin_versions} WHERE plugin LIKE 'local_%'");
    if (empty($rows)) {
        echo "no_local_plugins\n";
    } else {
        foreach ($rows as $r) {
            echo $r->plugin . " => " . $r->version . "\n";
        }
    }
} catch (Exception $e) {
    echo "exception: " . $e->getMessage() . "\n";
}
?>
<?php
// define('CLI_SCRIPT', true); // Pretend to be CLI to avoid redirect to login
require('config.php');

echo "Updating debug settings...\n";
try {
    set_config('debug', 32767); // E_ALL | E_STRICT
    set_config('debugdisplay', 1);
    echo "Debug settings updated.\n";
    
    purge_all_caches();
    echo "Caches purged.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

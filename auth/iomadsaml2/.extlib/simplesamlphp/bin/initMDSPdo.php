#!/usr/bin/env php
<?php

// This is the base directory of the SimpleSAMLphp installation
$baseDir = dirname(dirname(__FILE__));

// Add library autoloader and configuration
require_once $baseDir . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . '_autoload.php';
require_once \SimpleSAML\Utils\Config::getConfigDir() . DIRECTORY_SEPARATOR . 'config.php';

echo "Initializing Metadata Database..." . PHP_EOL;

# Iterate through configured metadata sources and ensure
# that a PDO source exists.
foreach ($config['metadata.sources'] as $source) {
    # If pdo is configured, create the new handler and initialize the DB.
    if ($source['type'] === "pdo") {
        $metadataStorageHandler = new \SimpleSAML\Metadata\MetaDataStorageHandlerPdo($source);
        $result = $metadataStorageHandler->initDatabase();

        if ($result === false) {
            echo "Failed to initialize metadata database." . PHP_EOL;
        } else {
            echo "Successfully initialized metadata database." . PHP_EOL;
        }
    }
}

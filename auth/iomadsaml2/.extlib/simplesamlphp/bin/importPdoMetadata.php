#!/usr/bin/env php
<?php

$baseDir = dirname(dirname(__FILE__));

require_once $baseDir . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . '_autoload.php';
require_once \SimpleSAML\Utils\Config::getConfigDir() . DIRECTORY_SEPARATOR . 'config.php';

# Iterate through configured metadata sources and ensure
# that a PDO source exists.
foreach ($config['metadata.sources'] as $s) {
    # If pdo is configured, create the new handler and add in the metadata sets.
    if ($s['type'] === "pdo") {
        $mdshp = new \SimpleSAML\Metadata\MetaDataStorageHandlerPdo($s);
        $mdshp->initDatabase();

        $metadataDir = rtrim(\SimpleSAML\Configuration::getInstance()->getString('metadatadir'), '/');
        foreach (glob("{$metadataDir}/*.php") as $filename) {
            $metadata = [];
            require_once $filename;
            $set = basename($filename, ".php");
            echo "importing set '$set'..." . PHP_EOL;

            foreach ($metadata as $k => $v) {
                echo "\t$k" . PHP_EOL;
                $mdshp->addEntry($k, $set, $v);
            }
        }
    }
}

#!/usr/bin/env php
<?php

// Check that the memcache library is enabled
if (!class_exists('Memcache') && !class_exists('Memcached')) {
    echo "Error: the memcached (or memcache) PHP extension appears to be unavailable.\n";
    echo "\n";
    echo "This is most likely because PHP doesn't load it for the command line\n";
    echo "version. You probably need to enable it somehow.\n";
    echo "\n";
    if (is_executable('/usr/sbin/phpenmod')) {
        echo "It is possible that running one of the following commands as root will fix it:\n";
        echo " phpenmod -s cli memcached\n";
        echo " phpenmod -s cli memcache\n";
    }

    exit(1);
}

// This is the base directory of the SimpleSAMLphp installation
$baseDir = dirname(dirname(__FILE__));

// Add library autoloader
require_once($baseDir . '/lib/_autoload.php');

// Initialize the configuration
$configdir = SimpleSAML\Utils\Config::getConfigDir();
\SimpleSAML\Configuration::setConfigDir($configdir);

// Things we should warn the user about
$warnServerDown = 0;
$warnBigSlab = 0;

// We use the stats interface to determine which servers exists
$stats = \SimpleSAML\Memcache::getRawStats();

$keys = [];
foreach ($stats as $group) {
    foreach ($group as $server => $state) {
        if ($state === false) {
            echo "WARNING: Server " . $server . " is down.\n";
            $warnServerDown++;
            continue;
        }

        $items = $state['curr_items'];
        echo "Server " . $server . " has " . $items . " items.\n";
        $serverKeys = getServerKeys($server);
        $keys = array_merge($keys, $serverKeys);
    }
}

echo "Total number of keys: " . count($keys) . "\n";
$keys = array_unique($keys);
echo "Total number of unique keys: " . count($keys) . "\n";

echo "Starting synchronization.\n" ;

$skipped = 0;
$sync = 0;
foreach ($keys as $key) {
    $res = \SimpleSAML\Memcache::get($key);
    if ($res === null) {
        $skipped += 1;
    } else {
        $sync += 1;
    }
}


echo "Synchronization done.\n";
echo $sync . " keys in sync.\n";
if ($skipped > 0) {
    echo $skipped . " keys skipped.\n";
    echo "Keys are skipped because they are either expired, or are of a type unknown\n";
    echo "to SimpleSAMLphp.\n";
}

if ($warnServerDown > 0) {
    echo "WARNING: " . $warnServerDown . " server(s) down. Not all servers are synchronized.\n";
}

if ($warnBigSlab > 0) {
    echo "WARNING: " . $warnBigSlab . " slab(s) may have contained more keys than we were told about.\n";
}

/**
 * Fetch all keys available in an server.
 *
 * @param string $server The server, as a string with <hostname>:<port>.
 *
 * @return array An array with all the keys available on the server.
 */
function getServerKeys(string $server): array
{
    $server = explode(':', $server);
    $host = $server[0];
    $port = (int) $server[1];

    echo "Connecting to: " . $host . ":" . $port . "\n";
    $socket = fsockopen($host, $port);
    echo "Connected. Finding keys.\n";

    if (fwrite($socket, "stats slabs\r\n") === false) {
        echo "Error requesting slab dump from server.\n";
        exit(1);
    }

    // Read list of slabs
    $slabs = [];
    while (($line = fgets($socket)) !== false) {
        $line = rtrim($line);
        if ($line === 'END') {
            break;
        }

        if (preg_match('/^STAT (\d+):/', $line, $matches)) {
            $slab = (int) $matches[1];
            if (!in_array($slab, $slabs, true)) {
                $slabs[] = $slab;
            }
        }
    }

    // Dump keys in slabs
    $keys = [];
    foreach ($slabs as $slab) {
        if (fwrite($socket, "stats cachedump " . $slab . " 1000000\r\n") === false) {
            echo "Error requesting cache dump from server.\n";
            exit(1);
        }

        /* We keep track of the result size, to be able to warn the user if it is
         * so big that keys may have been lost.
         */
        $resultSize = 0;

        while (($line = fgets($socket)) !== false) {
            $resultSize += strlen($line);

            $line = rtrim($line);
            if ($line === 'END') {
                break;
            }

            if (preg_match('/^ITEM (.*) \[\d+ b; \d+ s\]/', $line, $matches)) {
                $keys[] = $matches[1];
            } else {
                echo "Unknown result from cache dump: " . $line . "\n";
            }
        }
        if ($resultSize > 1900000 || count($keys) >= 1000000) {
            echo "WARNING: Slab " . $slab . " on server " . $host . ":" . $port .
                " may have contained more keys than we were told about.\n";
            $GLOBALS['warnBigSlab'] += 1;
        }
    }

    echo "Found " . count($keys) . " key(s).\n";
    fclose($socket);

    return $keys;
}

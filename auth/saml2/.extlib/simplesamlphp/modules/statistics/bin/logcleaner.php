#!/usr/bin/env php
<?php

// This is the base directory of the SimpleSAMLphp installation
$baseDir = dirname(dirname(dirname(dirname(__FILE__))));

// Add library autoloader.
require_once($baseDir . '/lib/_autoload.php');

// Initialize the configuration.
$configdir = \SimpleSAML\Utils\Config::getConfigDir();
\SimpleSAML\Configuration::setConfigDir($configdir);

$progName = array_shift($argv);
$debug = false;
$dryrun = false;
$output = '/tmp/simplesamlphp-new.log';
$infile = null;

foreach ($argv as $a) {
    if (strlen($a) === 0) {
        continue;
    }
    if (strpos($a, '=') !== false) {
        $p = strpos($a, '=');
        $v = substr($a, $p + 1);
        $a = substr($a, 0, $p);
    } else {
        $v = null;
    }

    // Map short options to long options.
    $shortOptMap = ['-d' => '--debug'];
    if (array_key_exists($a, $shortOptMap)) {
        $a = $shortOptMap[$a];
    }

    switch ($a) {
        case '--help':
            printHelp();
            exit(0);
        case '--debug':
            $debug = true;
            break;
        case '--dry-run':
            $dryrun = true;
            break;
        case '--infile':
            $infile = $v;
            break;
        case '--outfile':
            $output = $v;
            break;
        default:
            echo 'Unknown option: ' . $a . "\n";
            echo 'Please run `' . $progName . ' --help` for usage information.' . "\n";
            exit(1);
    }
}

$cleaner = new \SimpleSAML\Module\statistics\LogCleaner($infile);
$cleaner->dumpConfig();
$todelete = $cleaner->clean($debug);

echo "Cleaning these trackIDs: " . join(', ', $todelete) . "\n";

if (!$dryrun) {
    $cleaner->store($todelete, $output);
}

/**
 * This function prints the help output.
 * @return void
 */
function printHelp()
{
    global $progName;

    echo <<<END
Usage: $progName [options]

This program cleans logs. This script is experimental. Do not run it unless you have talked to Andreas about it. 
The script deletes log lines related to sessions that produce more than 200 lines.

Options:
	-d, --debug			Used when configuring the log file syntax. See doc.
	--dry-run			Aggregate but do not store the results.
	--infile			File input.
	--outfile			File to output the results.

END;
}

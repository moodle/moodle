<?php

namespace OpenSpout\Autoloader;

require_once 'Psr4Autoloader.php';

/**
 * @var string
 *             Full path to "src/Spout" which is what we want "OpenSpout" to map to
 */
$srcBaseDirectory = \dirname(__DIR__);

$loader = new Psr4Autoloader();
$loader->register();
$loader->addNamespace('OpenSpout', $srcBaseDirectory);

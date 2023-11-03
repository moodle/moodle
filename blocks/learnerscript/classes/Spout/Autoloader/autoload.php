<?php

namespace block_learnerscript\Spout\Autoloader;

require_once 'Psr4Autoloader.php';

/**
 * @var string $srcBaseDirectory
 * Full path to "src/Spout" which is what we want "block_learnerscript\Spout" to map to.
 */
$srcBaseDirectory = dirname(dirname(__FILE__));

$loader = new Psr4Autoloader();
$loader->register();
$loader->addNamespace('block_learnerscript\Spout', $srcBaseDirectory);

<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__);
require_once($projectRoot . '/vendor/autoload.php');

// Current SSP autoloader can't resolve classes from the tests folder.
include($projectRoot . '/tests/Utils/ClearStateTestCase.php');

<?php

/**
 * The _include script registers a autoloader for the SimpleSAMLphp libraries. It also
 * initializes the SimpleSAMLphp config class with the correct path.
 */

namespace SimpleSAML\Module\cron;

use SimpleSAML\Configuration;
use SimpleSAML\Session;

$config = Configuration::getInstance();
$session = Session::getSessionFromRequest();

$controller = new Controller\Cron($config, $session);
$response = $controller->info();
$response->show();

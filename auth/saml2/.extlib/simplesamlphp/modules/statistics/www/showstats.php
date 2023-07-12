<?php

namespace SimpleSAML\Module\statistics;

use SimpleSAML\Configuration;
use SimpleSAML\Session;
use Symfony\Component\HttpFoundation\Request;

$config = Configuration::getInstance();
$session = Session::getSessionFromRequest();
$request = Request::createFromGlobals();

$controller = new StatisticsController($config, $session);
$t = $controller->main($request);
$t->show();

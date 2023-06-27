<?php

if (isset($_REQUEST['retryURL'])) {
    $retryURL = (string) $_REQUEST['retryURL'];
    $retryURL = \SimpleSAML\Utils\HTTP::checkURLAllowed($retryURL);
} else {
    $retryURL = null;
}

$globalConfig = \SimpleSAML\Configuration::getInstance();
$t = new \SimpleSAML\XHTML\Template($globalConfig, 'core:no_cookie.tpl.php');
$translator = $t->getTranslator();

/** @var string $header */
$header = $translator->t('{core:no_cookie:header}');
/** @var string $desc */
$desc = $translator->t('{core:no_cookie:description}');
/** @var string $retry */
$retry = $translator->t('{core:no_cookie:retry}');

$t->data['header'] = htmlspecialchars($header);
$t->data['description'] = htmlspecialchars($desc);
$t->data['retry'] = htmlspecialchars($retry);
$t->data['retryURL'] = $retryURL;
$t->show();

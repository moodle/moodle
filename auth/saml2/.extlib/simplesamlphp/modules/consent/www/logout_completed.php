<?php
/**
 * This is the handler for logout completed from the consent page.
 *
 * @package SimpleSAMLphp
 */

$globalConfig = \SimpleSAML\Configuration::getInstance();
$t = new \SimpleSAML\XHTML\Template($globalConfig, 'consent:logout_completed.php');
$t->show();

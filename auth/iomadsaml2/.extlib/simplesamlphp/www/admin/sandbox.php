<?php

require_once('../_include.php');

// Load SimpleSAMLphp configuration
$config = \SimpleSAML\Configuration::getInstance()->toArray();
$config['usenewui'] = true;
$config = \SimpleSAML\Configuration::loadFromArray($config, '[ARRAY]', 'simplesaml');
$session = \SimpleSAML\Session::getSessionFromRequest();

$template = new \SimpleSAML\XHTML\Template($config, 'sandbox.php');
$template->data['pagetitle'] = 'Sandbox';
$template->data['sometext'] = 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus.' .
    ' Sed sit amet ipsum mauris. Maecenas congue ligula ac quam viverra nec consectetur ante hendrerit.' .
    ' Donec et mollis dolor. Praesent et diam eget libero egestas mattis sit amet vitae augue. ' .
    'Nam tincidunt congue enim, ut porta lorem lacinia consectetur.';
$template->data['remaining'] = $session->getAuthData('admin', 'Expire') - time();
$template->data['logout'] = null;

$template->send();

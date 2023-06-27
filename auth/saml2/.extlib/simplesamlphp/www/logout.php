<?php

require_once('_include.php');

$config = \SimpleSAML\Configuration::getInstance();

if (array_key_exists('link_href', $_REQUEST)) {
    $link = \SimpleSAML\Utils\HTTP::checkURLAllowed($_REQUEST['link_href']);
} else {
    $link = 'index.php';
}

if (array_key_exists('link_text', $_REQUEST)) {
    $text = $_REQUEST['link_text'];
} else {
    $text = '{logout:default_link_text}';
}

$t = new \SimpleSAML\XHTML\Template($config, 'logout.php');
$t->data['link'] = $link;
$t->data['text'] = $text;
$t->show();
exit();

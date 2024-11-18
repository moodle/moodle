<?php

/**
 * An AJAX handler to retrieve a list of disco tabs from the session.
 * This allows us to dynamically update the tab list without inline javascript.
 *
 * @author Guy Halse, http://orcid.org/0000-0002-9388-8592
 * @package SimpleSAMLphp
 */

$session = \SimpleSAML\Session::getSessionFromRequest();
$tabs = $session->getData('discopower:tabList', 'tabs');
$faventry = $session->getData('discopower:tabList', 'faventry');
$defaulttab = $session->getData('discopower:tabList', 'defaulttab');

if (!is_array($tabs)) {
    throw new \SimpleSAML\Error\Exception('Could not get tab list from session');
}

// handle JSON vs JSONP requests
if (isset($_REQUEST['callback'])) {
    if (!preg_match('/^[a-z0-9_]+$/i', $_REQUEST['callback'], $matches)) {
        throw new \SimpleSAML\Error\Exception('Unsafe JSONP callback function name "' . $matches[0] . '"');
    }
    $jsonp = true;
    header('Content-Type: application/javascript');
    echo addslashes($matches[0]) . '(';
} else {
    $jsonp = false;
    header('Content-Type: application/json');
}

echo json_encode(
    [
        'faventry' => $faventry,
        'default' => $defaulttab,
        'tabs' => $tabs,
    ]
);

if ($jsonp) {
    echo ');';
}

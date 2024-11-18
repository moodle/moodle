<?php

/**
 * This page serves as a dummy login page.
 *
 * Note that we don't actually validate the user in this example. This page
 * just serves to make the example work out of the box.
 *
 * @package SimpleSAMLphp
 */

if (!isset($_REQUEST['ReturnTo'])) {
    die('Missing ReturnTo parameter.');
}

$returnTo = \SimpleSAML\Utils\HTTP::checkURLAllowed($_REQUEST['ReturnTo']);

/**
 * The following piece of code would never be found in a real authentication page. Its
 * purpose in this example is to make this example safer in the case where the
 * administrator of the IdP leaves the exampleauth-module enabled in a production
 * environment.
 *
 * What we do here is to extract the $state-array identifier, and check that it belongs to
 * the exampleauth:External process.
 */
if (!preg_match('@State=(.*)@', $returnTo, $matches)) {
    die('Invalid ReturnTo URL for this example.');
}

/**
 * The loadState-function will not return if the second parameter does not
 * match the parameter passed to saveState, so by now we know that we arrived here
 * through the exampleauth:External authentication page.
 */
\SimpleSAML\Auth\State::loadState(urldecode($matches[1]), 'exampleauth:External');

// our list of users.
$users = [
    'student' => [
        'password' => 'student',
        'uid' => 'student',
        'name' => 'Student Name',
        'mail' => 'somestudent@example.org',
        'type' => 'student',
    ],
    'admin' => [
        'password' => 'admin',
        'uid' => 'admin',
        'name' => 'Admin Name',
        'mail' => 'someadmin@example.org',
        'type' => 'employee',
    ],
];

// time to handle login responses; since this is a dummy example, we accept any data
$badUserPass = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = (string) $_REQUEST['username'];
    $password = (string) $_REQUEST['password'];

    if (!isset($users[$username]) || $users[$username]['password'] !== $password) {
        $badUserPass = true;
    } else {
        $user = $users[$username];

        if (!session_id()) {
            // session_start not called before. Do it here.
            session_start();
        }

        $_SESSION['uid'] = $user['uid'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['mail'] = $user['mail'];
        $_SESSION['type'] = $user['type'];

        \SimpleSAML\Utils\HTTP::redirectTrustedURL($returnTo);
    }
}

// if we get this far, we need to show the login page to the user
$config = \SimpleSAML\Configuration::getInstance();
$t = new \SimpleSAML\XHTML\Template($config, 'exampleauth:authenticate.tpl.php');
$t->data['badUserPass'] = $badUserPass;
$t->data['returnTo'] = $returnTo;
$t->show();

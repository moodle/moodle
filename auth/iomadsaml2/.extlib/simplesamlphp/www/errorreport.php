<?php

require_once('_include.php');

$config = \SimpleSAML\Configuration::getInstance();

// this page will redirect to itself after processing a POST request and sending the email
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // the message has been sent. Show error report page

    $t = new \SimpleSAML\XHTML\Template($config, 'errorreport.php', 'errors');
    $t->show();
    exit;
}

$reportId = $_REQUEST['reportId'];
$email = $_REQUEST['email'];
$text = $_REQUEST['text'];

if (!preg_match('/^[0-9a-f]{8}$/', $reportId)) {
    throw new \SimpleSAML\Error\Exception('Invalid reportID');
}

$data = null;
try {
    $session = \SimpleSAML\Session::getSessionFromRequest();
    $data = $session->getData('core:errorreport', $reportId);
} catch (\Exception $e) {
    \SimpleSAML\Logger::error('Error loading error report data: ' . var_export($e->getMessage(), true));
}

if ($data === null) {
    $data = [
        'exceptionMsg'   => 'not set',
        'exceptionTrace' => 'not set',
        'trackId'        => 'not set',
        'url'            => 'not set',
        'referer'        => 'not set',
    ];

    if (isset($session)) {
        $data['trackId'] = $session->getTrackID();
    }
}

$data['reportId'] = $reportId;
$data['version'] = $config->getVersion();
$data['hostname'] = php_uname('n');
$data['directory'] = dirname(dirname(__FILE__));

if ($config->getBoolean('errorreporting', true)) {
    $mail = new SimpleSAML\Utils\EMail('SimpleSAMLphp error report from ' . $email);
    $mail->setData($data);
    if (filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_REQUIRE_SCALAR)) {
        $mail->addReplyTo($email);
    }
    $mail->setText($text);
    $mail->send();
    SimpleSAML\Logger::error('Report with id ' . $reportId . ' sent');
}

// redirect the user back to this page to clear the POST request
\SimpleSAML\Utils\HTTP::redirectTrustedURL(\SimpleSAML\Utils\HTTP::getSelfURLNoQuery());

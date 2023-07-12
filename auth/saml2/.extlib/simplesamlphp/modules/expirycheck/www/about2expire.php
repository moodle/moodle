<?php

/**
 * about2expire.php
 *
 * @package SimpleSAMLphp
 */

\SimpleSAML\Logger::info('expirycheck - User has been warned that NetID is near to expirational date.');

if (!array_key_exists('StateId', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest('Missing required StateId query parameter.');
}
$id = $_REQUEST['StateId'];
$state = \SimpleSAML\Auth\State::loadState($id, 'expirywarning:about2expire');

if (array_key_exists('yes', $_REQUEST)) {
    // The user has pressed the yes-button
    \SimpleSAML\Auth\ProcessingChain::resumeProcessing($state);
}

$globalConfig = \SimpleSAML\Configuration::getInstance();

$daysleft = $state['daysleft'];

$t = new \SimpleSAML\XHTML\Template($globalConfig, 'expirycheck:about2expire.php');
$t->data['autofocus'] = 'yesbutton';
$t->data['yesTarget'] = \SimpleSAML\Module::getModuleURL('expirycheck/about2expire.php');
$t->data['yesData'] = ['StateId' => $id];
$t->data['expireOnDate'] = $state['expireOnDate'];
$t->data['netId'] = $state['netId'];

if ($daysleft == 0) {
    # netid will expire today
    $t->data['header'] = $t->t('{expirycheck:expwarning:warning_header_today}', [
                                '%NETID%' => htmlspecialchars($t->data['netId'])
                        ]);
    $t->data['warning'] = $t->t('{expirycheck:expwarning:warning_today}', [
                                '%NETID%' => htmlspecialchars($t->data['netId'])
                        ]);
} elseif ($daysleft == 1) {
    # netid will expire in one day

    $t->data['header'] = $t->t('{expirycheck:expwarning:warning_header}', [
                                '%NETID%' => htmlspecialchars($t->data['netId']),
                                '%DAYS%' => $t->t('{expirycheck:expwarning:day}'),
                                '%DAYSLEFT%' => htmlspecialchars($daysleft),
                        ]);
    $t->data['warning'] = $t->t('{expirycheck:expwarning:warning}', [
                                '%NETID%' => htmlspecialchars($t->data['netId']),
                                '%DAYS%' => $t->t('{expirycheck:expwarning:day}'),
                                '%DAYSLEFT%' => htmlspecialchars($daysleft),
                        ]);
} else {
    # netid will expire in next <daysleft> days
    $t->data['header'] = $t->t('{expirycheck:expwarning:warning_header}', [
                                '%NETID%' => htmlspecialchars($t->data['netId']),
                                '%DAYS%' => $t->t('{expirycheck:expwarning:days}'),
                                '%DAYSLEFT%' => htmlspecialchars($daysleft),
                        ]);
    $t->data['warning'] = $t->t('{expirycheck:expwarning:warning}', [
                                '%NETID%' => htmlspecialchars($t->data['netId']),
                                '%DAYS%' => $t->t('{expirycheck:expwarning:days}'),
                                '%DAYSLEFT%' => htmlspecialchars($daysleft),
                        ]);
}

$t->show();

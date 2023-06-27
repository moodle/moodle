<?php

/**
 * This is the page the user lands on when choosing "no" in the consent form.
 *
 * @package SimpleSAMLphp
 */

if (!array_key_exists('StateId', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest(
        'Missing required StateId query parameter.'
    );
}

$id = $_REQUEST['StateId'];
$state = \SimpleSAML\Auth\State::loadState($id, 'consent:request');

$resumeFrom = \SimpleSAML\Module::getModuleURL(
    'consent/getconsent.php',
    ['StateId' => $id]
);

$logoutLink = \SimpleSAML\Module::getModuleURL(
    'consent/logout.php',
    ['StateId' => $id]
);

$aboutService = null;
if (!isset($state['consent:showNoConsentAboutService']) || $state['consent:showNoConsentAboutService']) {
    if (isset($state['Destination']['url.about'])) {
        $aboutService = $state['Destination']['url.about'];
    }
}

$statsInfo = [];
if (isset($state['Destination']['entityid'])) {
    $statsInfo['spEntityID'] = $state['Destination']['entityid'];
}
\SimpleSAML\Stats::log('consent:reject', $statsInfo);

if (array_key_exists('name', $state['Destination'])) {
    $dstName = $state['Destination']['name'];
} elseif (array_key_exists('OrganizationDisplayName', $state['Destination'])) {
    $dstName = $state['Destination']['OrganizationDisplayName'];
} else {
    $dstName = $state['Destination']['entityid'];
}

$globalConfig = \SimpleSAML\Configuration::getInstance();

$t = new \SimpleSAML\XHTML\Template($globalConfig, 'consent:noconsent.php');
$translator = $t->getTranslator();
$t->data['dstMetadata'] = $state['Destination'];
$t->data['resumeFrom'] = $resumeFrom;
$t->data['aboutService'] = $aboutService;
$t->data['logoutLink'] = $logoutLink;

$dstName = htmlspecialchars(is_array($dstName) ? $translator->t($dstName) : $dstName);

$t->data['noconsent_text'] = $translator->t('{consent:consent:noconsent_text}', ['SPNAME' => $dstName]);
$t->data['noconsent_abort'] = $translator->t('{consent:consent:abort}', ['SPNAME' => $dstName]);

$t->show();

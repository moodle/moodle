<?php

use Webmozart\Assert\Assert;

use \SimpleSAML\Module\cas\Auth\Source\CAS;

/**
 * Handle linkback() response from CAS.
 */

if (!isset($_GET['stateID'])) {
    throw new \SimpleSAML\Error\BadRequest('Missing stateID parameter.');
}
$state = \SimpleSAML\Auth\State::loadState($_GET['stateID'], CAS::STAGE_INIT);

if (!isset($_GET['ticket'])) {
    throw new \SimpleSAML\Error\BadRequest('Missing ticket parameter.');
}
$state['cas:ticket'] = (string) $_GET['ticket'];

// Find authentication source
Assert::keyExists($state, CAS::AUTHID);
$sourceId = $state[CAS::AUTHID];

/** @var \SimpleSAML\Module\cas\Auth\Source\CAS|null $source */
$source = \SimpleSAML\Auth\Source::getById($sourceId);
if ($source === null) {
    throw new \Exception('Could not find authentication source with id '.$sourceId);
}

$source->finalStep($state);

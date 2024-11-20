<?php

/**
 * Request handler for redirect filter test.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */

if (!array_key_exists('StateId', $_REQUEST)) {
    throw new \SimpleSAML\Error\BadRequest('Missing required StateId query parameter.');
}

/** @var array $state */
$state = \SimpleSAML\Auth\State::loadState($_REQUEST['StateId'], 'exampleauth:redirectfilter-test');

$state['Attributes']['RedirectTest2'] = ['OK'];

\SimpleSAML\Auth\ProcessingChain::resumeProcessing($state);

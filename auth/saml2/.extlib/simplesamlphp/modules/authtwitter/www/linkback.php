<?php

/**
 * Handle linkback() response from Twitter.
 */

if (!array_key_exists('AuthState', $_REQUEST) || empty($_REQUEST['AuthState'])) {
    throw new \SimpleSAML\Error\BadRequest('Missing state parameter on twitter linkback endpoint.');
}
$state = \SimpleSAML\Auth\State::loadState(
    $_REQUEST['AuthState'],
    \SimpleSAML\Module\authtwitter\Auth\Source\Twitter::STAGE_INIT
);

// Find authentication source
if (is_null($state) || !array_key_exists(\SimpleSAML\Module\authtwitter\Auth\Source\Twitter::AUTHID, $state)) {
    throw new \SimpleSAML\Error\BadRequest(
        'No data in state for '.\SimpleSAML\Module\authtwitter\Auth\Source\Twitter::AUTHID
    );
}
$sourceId = $state[\SimpleSAML\Module\authtwitter\Auth\Source\Twitter::AUTHID];

/** @var \SimpleSAML\Module\authtwitter\Auth\Source\Twitter|null $source */
$source = \SimpleSAML\Auth\Source::getById($sourceId);
if ($source === null) {
    throw new \SimpleSAML\Error\BadRequest(
        'Could not find authentication source with id '.var_export($sourceId, true)
    );
}

try {
    if (array_key_exists('denied', $_REQUEST)) {
        throw new \SimpleSAML\Error\UserAborted();
    }
    $source->finalStep($state);
} catch (\SimpleSAML\Error\Exception $e) {
    \SimpleSAML\Auth\State::throwException($state, $e);
} catch (\Exception $e) {
    \SimpleSAML\Auth\State::throwException(
        $state,
        new \SimpleSAML\Error\AuthSource($sourceId, 'Error on authtwitter linkback endpoint.', $e)
    );
}

\SimpleSAML\Auth\Source::completeAuth($state);

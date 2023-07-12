<?php

/**
 * Handle linkback() response from Windows Live ID.
 */

if (!array_key_exists('state', $_REQUEST)) {
    throw new \Exception('Lost OAuth Client State');
}
$state = \SimpleSAML\Auth\State::loadState(
    $_REQUEST['state'],
    \SimpleSAML\Module\authwindowslive\Auth\Source\LiveID::STAGE_INIT
);

// http://msdn.microsoft.com/en-us/library/ff749771.aspx
if (array_key_exists('code', $_REQUEST)) {
    // good
    $state['authwindowslive:verification_code'] = $_REQUEST['code'];

    if (array_key_exists('exp', $_REQUEST)) {
        $state['authwindowslive:exp'] = $_REQUEST['exp'];
    }
} else {
    // In the OAuth WRAP service, error_reason = 'user_denied' means user chose
    // not to login with LiveID. It isn't clear that this is still true in the
    // newer API, but the parameter name has changed to error. It doesn't hurt
    // to preserve support for this, so this is left in as a placeholder.
    // redirect them to their original page so they can choose another auth mechanism
    if (($_REQUEST['error'] === 'user_denied') && ($state !== null)) {
        $e = new \SimpleSAML\Error\UserAborted();
        \SimpleSAML\Auth\State::throwException($state, $e);
    }

    // error
    throw new \Exception('Authentication failed: ['.$_REQUEST['error'].'] '.$_REQUEST['error_description']);
}

assert(array_key_exists(\SimpleSAML\Module\authwindowslive\Auth\Source\LiveID::AUTHID, $state));

// find authentication source
$sourceId = $state[\SimpleSAML\Module\authwindowslive\Auth\Source\LiveID::AUTHID];

/** @var \SimpleSAML\Module\authwindowslive\Auth\Source\LiveID|null $source */
$source = \SimpleSAML\Auth\Source::getById($sourceId);
if ($source === null) {
    throw new \Exception('Could not find authentication source with id '.$sourceId);
}

$source->finalStep($state);

\SimpleSAML\Auth\Source::completeAuth($state);

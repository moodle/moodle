<?php

/**
 * Logout endpoint handler for SAML SP authentication client.
 *
 * This endpoint handles both logout requests and logout responses.
 */

if (!array_key_exists('PATH_INFO', $_SERVER)) {
    throw new \SimpleSAML\Error\BadRequest('Missing authentication source ID in logout URL');
}

$sourceId = substr($_SERVER['PATH_INFO'], 1);

/** @var \SimpleSAML\Module\saml\Auth\Source\SP $source */
$source = \SimpleSAML\Auth\Source::getById($sourceId);
if ($source === null) {
    throw new \Exception('Could not find authentication source with id ' . $sourceId);
} elseif (!($source instanceof \SimpleSAML\Module\saml\Auth\Source\SP)) {
    throw new \SimpleSAML\Error\Exception('Source type changed?');
}

try {
    $binding = \SAML2\Binding::getCurrentBinding();
} catch (\Exception $e) {
    // TODO: look for a specific exception
    // This is dirty. Instead of checking the message of the exception, \SAML2\Binding::getCurrentBinding() should throw
    // an specific exception when the binding is unknown, and we should capture that here
    if ($e->getMessage() === 'Unable to find the current binding.') {
        throw new \SimpleSAML\Error\Error('SLOSERVICEPARAMS', $e, 400);
    } else {
        throw $e; // do not ignore other exceptions!
    }
}
$message = $binding->receive();

$issuer = $message->getIssuer();
if ($issuer instanceof \SAML2\XML\saml\Issuer) {
    $idpEntityId = $issuer->getValue();
} else {
    $idpEntityId = $issuer;
}

if ($idpEntityId === null) {
    // Without an issuer we have no way to respond to the message.
    throw new \SimpleSAML\Error\BadRequest('Received message on logout endpoint without issuer.');
}

/** @var \SimpleSAML\Module\saml\Auth\Source\SP $source */
$spEntityId = $source->getEntityId();

$metadata = \SimpleSAML\Metadata\MetaDataStorageHandler::getMetadataHandler();
$idpMetadata = $source->getIdPMetadata($idpEntityId);
$spMetadata = $source->getMetadata();

\SimpleSAML\Module\saml\Message::validateMessage($idpMetadata, $spMetadata, $message);

$destination = $message->getDestination();
if ($destination !== null && $destination !== \SimpleSAML\Utils\HTTP::getSelfURLNoQuery()) {
    throw new \SimpleSAML\Error\Exception('Destination in logout message is wrong.');
}

if ($message instanceof \SAML2\LogoutResponse) {
    $relayState = $message->getRelayState();
    if ($relayState === null) {
        // Somehow, our RelayState has been lost.
        throw new \SimpleSAML\Error\BadRequest('Missing RelayState in logout response.');
    }

    if (!$message->isSuccess()) {
        \SimpleSAML\Logger::warning(
            'Unsuccessful logout. Status was: ' . \SimpleSAML\Module\saml\Message::getResponseError($message)
        );
    }

    $state = \SimpleSAML\Auth\State::loadState($relayState, 'saml:slosent');
    $state['saml:sp:LogoutStatus'] = $message->getStatus();
    \SimpleSAML\Auth\Source::completeLogout($state);
} elseif ($message instanceof \SAML2\LogoutRequest) {
    \SimpleSAML\Logger::debug('module/saml2/sp/logout: Request from ' . $idpEntityId);
    \SimpleSAML\Logger::stats('saml20-idp-SLO idpinit ' . $spEntityId . ' ' . $idpEntityId);

    if ($message->isNameIdEncrypted()) {
        try {
            $keys = \SimpleSAML\Module\saml\Message::getDecryptionKeys($idpMetadata, $spMetadata);
        } catch (\Exception $e) {
            throw new \SimpleSAML\Error\Exception('Error decrypting NameID: ' . $e->getMessage());
        }

        $blacklist = \SimpleSAML\Module\saml\Message::getBlacklistedAlgorithms($idpMetadata, $spMetadata);

        $lastException = null;
        foreach ($keys as $i => $key) {
            try {
                $message->decryptNameId($key, $blacklist);
                \SimpleSAML\Logger::debug('Decryption with key #' . $i . ' succeeded.');
                $lastException = null;
                break;
            } catch (\Exception $e) {
                \SimpleSAML\Logger::debug('Decryption with key #' . $i . ' failed with exception: ' . $e->getMessage());
                $lastException = $e;
            }
        }
        if ($lastException !== null) {
            throw $lastException;
        }
    }

    $nameId = $message->getNameId();
    $sessionIndexes = $message->getSessionIndexes();

    /** @psalm-suppress PossiblyNullArgument  This will be fixed in saml2 5.0 */
    $numLoggedOut = \SimpleSAML\Module\saml\SP\LogoutStore::logoutSessions($sourceId, $nameId, $sessionIndexes);
    if ($numLoggedOut === false) {
        // This type of logout was unsupported. Use the old method
        $source->handleLogout($idpEntityId);
        $numLoggedOut = count($sessionIndexes);
    }

    // Create and send response
    $lr = \SimpleSAML\Module\saml\Message::buildLogoutResponse($spMetadata, $idpMetadata);
    $lr->setRelayState($message->getRelayState());
    $lr->setInResponseTo($message->getId());

    if ($numLoggedOut < count($sessionIndexes)) {
        \SimpleSAML\Logger::warning('Logged out of ' . $numLoggedOut . ' of ' . count($sessionIndexes) . ' sessions.');
    }

    /** @var array $dst */
    $dst = $idpMetadata->getEndpointPrioritizedByBinding(
        'SingleLogoutService',
        [
            \SAML2\Constants::BINDING_HTTP_REDIRECT,
            \SAML2\Constants::BINDING_HTTP_POST
        ]
    );

    if (!($binding instanceof \SAML2\SOAP)) {
        $binding = \SAML2\Binding::getBinding($dst['Binding']);
        if (isset($dst['ResponseLocation'])) {
            $dst = $dst['ResponseLocation'];
        } else {
            $dst = $dst['Location'];
        }
        $binding->setDestination($dst);
    }
    $lr->setDestination($dst);

    $binding->send($lr);
} else {
    throw new \SimpleSAML\Error\BadRequest('Unknown message received on logout endpoint: ' . get_class($message));
}

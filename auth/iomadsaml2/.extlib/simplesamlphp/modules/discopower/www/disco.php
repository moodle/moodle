<?php

try {
    $discoHandler = new \SimpleSAML\Module\discopower\PowerIdPDisco(
        ['saml20-idp-remote', 'shib13-idp-remote'],
        'poweridpdisco'
    );
} catch (\Exception $exception) {
    // An error here should be caused by invalid query parameters
    throw new \SimpleSAML\Error\Error('DISCOPARAMS', $exception);
}

try {
    $discoHandler->handleRequest();
} catch (\Exception $exception) {
    // An error here should be caused by metadata
    throw new \SimpleSAML\Error\Error('METADATA', $exception);
}

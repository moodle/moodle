<?php

/**
 * SAML 1.1 remote SP metadata for SimpleSAMLphp.
 *
 * Note that SAML 1.1 support has been deprecated and will be removed in SimpleSAMLphp 2.0.
 *
 * See: https://simplesamlphp.org/docs/stable/simplesamlphp-reference-sp-remote
 */

/*
 * This is just an example:
 */
$metadata['https://sp.shiblab.feide.no'] = [
    'AssertionConsumerService' => 'http://sp.shiblab.feide.no/Shibboleth.sso/SAML/POST',
    'audience' => 'urn:mace:feide:shiblab',
    'base64attributes' => false,
];

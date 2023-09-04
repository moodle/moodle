<?php

declare(strict_types=1);

$metadata = [];

$metadata['entityA'] = [
    'entityid' => 'entityA',
    'name' =>
        [
            'en' => 'entityA SP from source1',
        ],
    'metadata-set' => 'saml20-sp-remote',
    'AssertionConsumerService' =>
        [
            0 =>
                [
                    'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                    'Location' => 'https://entityA.example.org/Shibboleth.sso/SAML2/POST',
                    'index' => 1,
                    'isDefault' => true,
                ],
        ]
];

$metadata['entityInBoth'] = [
    'entityid' => 'entityInBoth',
    'name' =>
        [
            'en' => 'entityInBoth SP from source1',
        ],
    'metadata-set' => 'saml20-sp-remote',
    'AssertionConsumerService' =>
        [
            0 =>
                [
                    'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                    'Location' => 'https://entityInBoth.example.org/Shibboleth.sso/SAML2/POST',
                    'index' => 1,
                    'isDefault' => true,
                ],
        ]
];

$metadata['expiredInSrc1InSrc2'] = [
    'entityid' => 'expiredInSrc1InSrc2',
    // This entity is expired in src1 but unexpired in src2
    'expire' => 1,
    'name' =>
        [
            'en' => 'expiredInSrc1InSrc2 SP from source1',
        ],
    'metadata-set' => 'saml20-sp-remote',
    'AssertionConsumerService' =>
        [
            0 =>
                [
                    'Binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
                    'Location' => 'https://expiredInSrc1InSrc2.example.org/Shibboleth.sso/SAML2/POST',
                    'index' => 1,
                    'isDefault' => true,
                ],
        ]
];

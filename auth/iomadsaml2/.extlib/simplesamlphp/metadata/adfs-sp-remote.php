<?php

$metadata['urn:federation:localhost'] = [
    'prp' => 'https://localhost/adfs/ls/',
    'simplesaml.nameidattribute' => 'uid',
    'authproc' => [
        50 => [
            'class' => 'core:AttributeLimit',
            'cn', 'mail', 'uid', 'eduPersonAffiliation',
        ],
    ],
];

<?php

$definitions = array(
    // Default cache for locking
    'locking' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'mappingsonly' => true,
    ),
    'string' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'component' => 'core',
        'area' => 'string',
        'persistent' => true,
        'persistentmaxsize' => 3
    ),
    'databasemeta' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'requireidentifiers' => array(
            'dbfamily'
        ),
        'persistent' => true,
        'persistentmaxsize' => 2
    ),
    'config' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'persistent' => true
    ),
    // Event invalidation cache
    'eventinvalidation' => array(
        'mode' => cache_store::MODE_APPLICATION,
        'persistent' => true,
        'requiredataguarantee' => true
    )
);

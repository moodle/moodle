<?php return array(
    'root' => array(
        'name' => '__root__',
        'pretty_version' => '1.0.0+no-version-set',
        'version' => '1.0.0.0',
        'reference' => NULL,
        'type' => 'library',
        'install_path' => __DIR__ . '/../../',
        'aliases' => array(),
        'dev' => true,
    ),
    'versions' => array(
        '__root__' => array(
            'pretty_version' => '1.0.0+no-version-set',
            'version' => '1.0.0.0',
            'reference' => NULL,
            'type' => 'library',
            'install_path' => __DIR__ . '/../../',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'apereo/phpcas' => array(
            'pretty_version' => '1.6.1',
            'version' => '1.6.1.0',
            'reference' => 'c129708154852656aabb13d8606cd5b12dbbabac',
            'type' => 'library',
            'install_path' => __DIR__ . '/../apereo/phpcas',
            'aliases' => array(),
            'dev_requirement' => false,
        ),
        'psr/log' => array(
            'dev_requirement' => false,
            'replaced' => array(
                0 => '*',
            ),
        ),
    ),
);

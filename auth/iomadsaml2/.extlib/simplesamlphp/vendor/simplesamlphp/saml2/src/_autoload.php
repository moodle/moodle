<?php

declare(strict_types=1);

/**
 * Temporary autoloader to ensure compatibility with old, non-PSR-4 compliant classes.
 *
 * @author Jaime Pérez Crespo <jaime.perez@uninett.no>
 * @package SimpleSAMLphp
 */

/**
 * Autoload function that looks for classes migrated to PSR-4.
 *
 * @param string $className Name of the class.
 * @return void
 */
function SAML2_autoload(string $className) : void
{
    // handle classes that have been renamed
    $renamed = [
        'SAML2_Const' => 'SAML2_Constants',
    ];
    $oldName = $className;
    if (array_key_exists($className, $renamed)) {
        $className = $renamed[$className];
    }

    $file = dirname(__FILE__).'/'.str_replace('_', '/', $className).'.php';
    if (file_exists($file)) {
        require_once($file);
        $newName = '\\'.str_replace('_', '\\', $className);
        class_alias($newName, $oldName);
    }
}

spl_autoload_register('SAML2_autoload');

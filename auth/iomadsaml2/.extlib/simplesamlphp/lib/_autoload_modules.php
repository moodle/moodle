<?php

declare(strict_types=1);

/**
 * This file registers an autoloader for SimpleSAMLphp modules.
 *
 * @author Boy Baukema, SURFnet
 * @author Jaime Perez <jaime.perez@uninett.no>, UNINETT
 * @package SimpleSAMLphp
 */

/**
 * This temporary autoloader allows loading classes with their old-style names (SimpleSAML_Path_Something) even if they
 * have been migrated to namespaces, by registering an alias for the new class. If the class has not yet been migrated,
 * the autoloader will then try to load it.
 *
 * @param string $class The full name of the class using underscores to separate the elements of the path, and starting
 * with 'SimpleSAML_'.
 * @deprecated This function will be removed in SSP 2.0.
 */
function temporaryLoader(string $class)
{
    // handle the upgrade to the latest version of XMLSecLibs using namespaces
    if (strstr($class, 'XMLSec') && !strstr($class, '\\RobRichards\\XMLSecLibs\\')) {
        $new = '\\RobRichards\\XMLSecLibs\\' . $class;
        if (class_exists($new, true)) {
            class_alias($new, $class);
            SimpleSAML\Logger::warning("The class '$class' is now using namespaces, please use '$new'.");
            return;
        }
    }

    if (!strstr($class, 'SimpleSAML_')) {
        return; // not a valid class name for old classes
    }
    $original = $class;

    // list of classes that have been renamed or moved
    $renamed = [
        'SimpleSAML_Metadata_MetaDataStorageHandlerMDX' => 'SimpleSAML_Metadata_Sources_MDQ',
        'SimpleSAML_Logger_LoggingHandlerSyslog' => 'SimpleSAML_Logger_SyslogLoggingHandler',
        'SimpleSAML_Logger_LoggingHandlerErrorLog' => 'SimpleSAML_Logger_ErrorLogLoggingHandler',
        'SimpleSAML_Logger_LoggingHandlerFile' => 'SimpleSAML_Logger_FileLoggingHandler',
        'SimpleSAML_Logger_LoggingHandler' => 'SimpleSAML_Logger_LoggingHandlerInterface',
        'SimpleSAML_IdP_LogoutHandler' => 'SimpleSAML_IdP_LogoutHandlerInterface',
        'SimpleSAML_IdP_LogoutIFrame' => 'SimpleSAML_IdP_IFrameLogoutHandler',
        'SimpleSAML_IdP_LogoutTraditional' => 'SimpleSAML_IdP_TraditionalLogoutHandler',
        'SimpleSAML_Auth_Default' => 'SimpleSAML_Auth_DefaultAuth',
        'SimpleSAML_Auth_LDAP' => 'SimpleSAML_Module_ldap_Auth_Ldap',
    ];
    if (array_key_exists($class, $renamed)) {
        // the class has been renamed, try to load it and create an alias
        $class = $renamed[$class];
    }

    // try to load it from the corresponding file
    $path = explode('_', $class);
    $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . join(DIRECTORY_SEPARATOR, $path) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }

    // it exists, so it's not yet migrated to namespaces
    if (class_exists($class, false) || interface_exists($class, false)) {
        return;
    }

    // it didn't exist, try to see if it was migrated to namespaces
    $new = join('\\', $path);
    if (class_exists($new, false) || interface_exists($new, false)) {
        // do not try to autoload it if it doesn't exist! It should!
        class_alias($new, $original);
        SimpleSAML\Logger::warning("The class or interface '$original' is now using namespaces, please use '$new'.");
    }
}


/**
 * Autoload function for SimpleSAMLphp modules following PSR-0.
 *
 * @param string $className Name of the class.
 *
 * @deprecated This method will be removed in SSP 2.0.
 *
 * TODO: this autoloader should be removed once everything has been migrated to namespaces.
 */
function sspmodAutoloadPSR0(string $className)
{
    $modulePrefixLength = strlen('sspmod_');
    $classPrefix = substr($className, 0, $modulePrefixLength);
    if ($classPrefix !== 'sspmod_') {
        return;
    }

    // list of classes that have been renamed or moved
    $renamed = [
        'sspmod_adfs_SAML2_XML_fed_Const' => [
            'module' => 'adfs',
            'path' => ['SAML2', 'XML', 'fed', 'Constants']
        ],
    ];
    if (array_key_exists($className, $renamed)) {
        // the class has been renamed, try to load it and create an alias
        $module = $renamed[$className]['module'];
        $path = $renamed[$className]['path'];
    } else {
        $modNameEnd = strpos($className, '_', $modulePrefixLength);
        $module = substr($className, $modulePrefixLength, $modNameEnd - $modulePrefixLength);
        $path = explode('_', substr($className, $modNameEnd + 1));
    }

    if (!\SimpleSAML\Module::isModuleEnabled($module)) {
        return;
    }

    $file = \SimpleSAML\Module::getModuleDir($module) . '/lib/' . join('/', $path) . '.php';
    if (!file_exists($file)) {
        return;
    }
    require_once($file);

    if (!class_exists($className, false) && !interface_exists($className, false)) {
        // the file exists, but the class is not defined. Is it using namespaces?
        $nspath = join('\\', $path);
        if (
            class_exists('SimpleSAML\\Module\\' . $module . '\\' . $nspath)
            || interface_exists('SimpleSAML\\Module\\' . $module . '\\' . $nspath)
        ) {
            // the class has been migrated, create an alias and warn about it
            \SimpleSAML\Logger::warning(
                "The class or interface '$className' is now using namespaces, please use 'SimpleSAML\\Module\\" .
                $module . "\\" . $nspath . "' instead."
            );
            class_alias("SimpleSAML\\Module\\$module\\$nspath", $className);
        }
    }
}


/**
 * Autoload function for SimpleSAMLphp modules following PSR-4.
 *
 * @param string $className Name of the class.
 */
function sspmodAutoloadPSR4(string $className)
{
    $elements = explode('\\', $className);
    if ($elements[0] === '') {
        // class name starting with /, ignore
        array_shift($elements);
    }
    if (count($elements) < 4) {
        return; // it can't be a module
    }
    if (array_shift($elements) !== 'SimpleSAML') {
        return; // the first element is not "SimpleSAML"
    }
    if (array_shift($elements) !== 'Module') {
        return; // the second element is not "module"
    }

    // this is a SimpleSAMLphp module following PSR-4
    $module = array_shift($elements);
    if (!\SimpleSAML\Module::isModuleEnabled($module)) {
        return; // module not enabled, avoid giving out any information at all
    }

    $file = \SimpleSAML\Module::getModuleDir($module) . '/lib/' . implode('/', $elements) . '.php';

    if (file_exists($file)) {
        require_once($file);
    }
}

spl_autoload_register("temporaryLoader");
spl_autoload_register('sspmodAutoloadPSR0');
spl_autoload_register('sspmodAutoloadPSR4');

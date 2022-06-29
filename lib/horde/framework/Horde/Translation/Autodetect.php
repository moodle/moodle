<?php
/**
 * Copyright 2010-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (LGPL). If you
 * did not receive this file, see http://www.horde.org/licenses/lgpl21.
 *
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Translation
 * @since     2.2.0
 */

/**
 * The Horde_Translation_Autodetect auto detects the locale directory location
 * for the class implementing it.
 *
 * @author    Jan Schneider <jan@horde.org>
 * @category  Horde
 * @copyright 2010-2017 Horde LLC
 * @license   http://www.horde.org/licenses/lgpl21 LGPL 2.1
 * @package   Translation
 * @since     2.2.0
 */
abstract class Horde_Translation_Autodetect extends Horde_Translation
{
    /**
     * The absolute PEAR path to the translations for the default gettext handler.
     *
     * This value is automatically set by PEAR Replace Tasks.
     *
     * @var string
     */
    protected static $_pearDirectory;

    /**
     * Auto detects the locale directory location.
     *
     * @param string $handlerClass  The name of a class implementing the
     *                              Horde_Translation_Handler interface.
     */
    public static function loadHandler($handlerClass)
    {
        if (!static::$_domain) {
            throw new Horde_Translation_Exception('The domain property must be set by the class that extends Horde_Translation_Autodetect.');
        }

        $directory = static::_searchLocaleDirectory();
        if (!$directory) {
            throw new Horde_Translation_Exception(sprintf('Could not found find any locale directory for %s domain.', static::$_domain));
        }

        static::$_directory = $directory;
        parent::loadHandler($handlerClass);
    }

    /**
     * Search for the locale directory for different installations methods (eg: PEAR, Composer).
     *
     * @var boolean|string The directory if found, or false when no valid directory is found
     */
    protected static function _searchLocaleDirectory()
    {
        if (static::$_pearDirectory !== '@data_dir@') {
            $directory = static::$_pearDirectory . '/' . static::$_domain . '/locale';
            if (is_dir($directory)) {
                return $directory;
            }
        }

        $directories = static::_getSearchDirectories();
        foreach ($directories as $directory) {
            if (is_dir($directory)) {
                return $directory;
            }
        }

        return false;
    }

    /**
     * Get potential locations for the locale directory.
     *
     * @var array List of directories
     */
    protected static function _getSearchDirectories()
    {
        $className = get_called_class();
        $class = new ReflectionClass($className);
        $basedir = dirname($class->getFilename());
        $depth = substr_count($className, '\\')
            ?: substr_count($className, '_');

        return array(
            /* Composer */
            $basedir . str_repeat('/..', $depth) . '/data/locale',
            /* Source */
            $basedir . str_repeat('/..', $depth + 1) . '/locale'
        );
    }

}

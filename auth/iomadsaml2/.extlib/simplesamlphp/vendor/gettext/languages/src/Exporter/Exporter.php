<?php

namespace Gettext\Languages\Exporter;

use Exception;

/**
 * Base class for all the exporters.
 */
abstract class Exporter
{
    /**
     * @var array
     */
    private static $exporters;

    /**
     * Return the list of all the available exporters. Keys are the exporter handles, values are the exporter class names.
     *
     * @param bool $onlyForPublicUse if true, internal exporters will be omitted
     *
     * @return string[]
     */
    final public static function getExporters($onlyForPublicUse = false)
    {
        if (!isset(self::$exporters)) {
            $exporters = array();
            $m = null;
            foreach (scandir(__DIR__) as $f) {
                if (preg_match('/^(\w+)\.php$/', $f, $m)) {
                    if ($f !== basename(__FILE__)) {
                        $exporters[strtolower($m[1])] = $m[1];
                    }
                }
            }
            self::$exporters = $exporters;
        }
        if ($onlyForPublicUse) {
            $result = array();
            foreach (self::$exporters as $handle => $class) {
                if (call_user_func(self::getExporterClassName($handle) . '::isForPublicUse') === true) {
                    $result[$handle] = $class;
                }
            }
        } else {
            $result = self::$exporters;
        }

        return $result;
    }

    /**
     * Return the description of a specific exporter.
     *
     * @param string $exporterHandle the handle of the exporter
     *
     * @throws \Exception throws an Exception if $exporterHandle is not valid
     *
     * @return string
     */
    final public static function getExporterDescription($exporterHandle)
    {
        $exporters = self::getExporters();
        if (!isset($exporters[$exporterHandle])) {
            throw new Exception("Invalid exporter handle: '{$exporterHandle}'");
        }

        return call_user_func(self::getExporterClassName($exporterHandle) . '::getDescription');
    }

    /**
     * Returns the fully qualified class name of a exporter given its handle.
     *
     * @param string $exporterHandle the exporter class handle
     *
     * @return string
     */
    final public static function getExporterClassName($exporterHandle)
    {
        return __NAMESPACE__ . '\\' . ucfirst(strtolower($exporterHandle));
    }

    /**
     * Convert a list of Language instances to string.
     *
     * @param \Gettext\Languages\Language[] $languages the Language instances to convert
     * @param array|null $options
     *
     * @return string
     */
    final public static function toString($languages, $options = null)
    {
        if (isset($options) && is_array($options)) {
            if (isset($options['us-ascii']) && $options['us-ascii']) {
                $asciiList = array();
                foreach ($languages as $language) {
                    $asciiList[] = $language->getUSAsciiClone();
                }
                $languages = $asciiList;
            }
        }

        return static::toStringDo($languages);
    }

    /**
     * Save the Language instances to a file.
     *
     * @param \Gettext\Languages\Language[] $languages the Language instances to convert
     * @param array|null $options
     *
     * @throws \Exception
     */
    final public static function toFile($languages, $filename, $options = null)
    {
        $data = self::toString($languages, $options);
        if (@file_put_contents($filename, $data) === false) {
            throw new Exception("Error writing data to '{$filename}'");
        }
    }

    /**
     * Is this exporter for public use?
     *
     * @return bool
     */
    public static function isForPublicUse()
    {
        return true;
    }

    /**
     * Return a short description of the exporter.
     *
     * @return string
     */
    public static function getDescription()
    {
        throw new Exception(get_called_class() . ' does not implement the method ' . __FUNCTION__);
    }

    /**
     * Convert a list of Language instances to string.
     *
     * @param \Gettext\Languages\Language[] $languages the Language instances to convert
     *
     * @return string
     */
    protected static function toStringDo($languages)
    {
        throw new Exception(get_called_class() . ' does not implement the method ' . __FUNCTION__);
    }
}

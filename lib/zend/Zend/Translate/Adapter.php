<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage Zend_Translate_Adapter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Locale
 */
require_once 'Zend/Locale.php';

/**
 * @see Zend_Translate_Plural
 */
require_once 'Zend/Translate/Plural.php';

/**
 * Basic adapter class for each translation source adapter
 *
 * @category   Zend
 * @package    Zend_Translate
 * @subpackage Zend_Translate_Adapter
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Translate_Adapter {
    /**
     * Shows if locale detection is in automatic level
     * @var boolean
     */
    private $_automatic = true;

    /**
     * Internal cache for all adapters
     * @var Zend_Cache_Core
     */
    protected static $_cache     = null;

    /**
     * Scans for the locale within the name of the directory
     * @constant integer
     */
    const LOCALE_DIRECTORY = 'directory';

    /**
     * Scans for the locale within the name of the file
     * @constant integer
     */
    const LOCALE_FILENAME  = 'filename';

    /**
     * Array with all options, each adapter can have own additional options
     *       'clear'  => clears already loaded data when adding new files
     *       'scan'   => searches for translation files using the LOCALE constants
     *       'locale' => the actual set locale to use
     * @var array
     */
    protected $_options = array(
        'clear'           => false,
        'disableNotices'  => false,
        'ignore'          => '.',
        'locale'          => 'auto',
        'log'             => null,
        'logMessage'      => "Untranslated message within '%locale%': %message%",
        'logUntranslated' => false,
        'scan'            => null
    );

    /**
     * Translation table
     * @var array
     */
    protected $_translate = array();

    /**
     * Generates the adapter
     *
     * @param  string|array       $data    Translation data or filename for this adapter
     * @param  string|Zend_Locale $locale  (optional) Locale/Language to set, identical with Locale
     *                                     identifiers see Zend_Locale for more information
     * @param  array              $options (optional) Options for the adaptor
     * @throws Zend_Translate_Exception
     * @return void
     */
    public function __construct($data, $locale = null, array $options = array())
    {
        if (isset(self::$_cache)) {
            $id = 'Zend_Translate_' . $this->toString() . '_Options';
            $result = self::$_cache->load($id);
            if ($result) {
                $this->_options   = unserialize($result);
            }
        }

        if (($locale === "auto") or ($locale === null)) {
            $this->_automatic = true;
        } else {
            $this->_automatic = false;
        }

        $this->addTranslation($data, $locale, $options);
        if ($this->getLocale() !== (string) $locale) {
            $this->setLocale($locale);
        }
    }

    /**
     * Add translation data
     *
     * It may be a new language or additional data for existing language
     * If $clear parameter is true, then translation data for specified
     * language is replaced and added otherwise
     *
     * @param  array|string       $data    Translation data
     * @param  string|Zend_Locale $locale  (optional) Locale/Language to add data for, identical
     *                                        with locale identifier, see Zend_Locale for more information
     * @param  array              $options (optional) Option for this Adapter
     * @throws Zend_Translate_Exception
     * @return Zend_Translate_Adapter Provides fluent interface
     */
    public function addTranslation($data, $locale = null, array $options = array())
    {
        try {
            $locale    = Zend_Locale::findLocale($locale);
        } catch (Zend_Locale_Exception $e) {
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception("The given Language '{$locale}' does not exist");
        }

        $originate = (string) $locale;

        $this->setOptions($options);
        if (is_string($data) and is_dir($data)) {
            $data = realpath($data);
            $prev = '';
            foreach (new RecursiveIteratorIterator(
                     new RecursiveDirectoryIterator($data, RecursiveDirectoryIterator::KEY_AS_PATHNAME),
                     RecursiveIteratorIterator::SELF_FIRST) as $directory => $info) {
                $file = $info->getFilename();
                if (strpos($directory, DIRECTORY_SEPARATOR . $this->_options['ignore']) !== false) {
                    // ignore files matching first characters from option 'ignore' and all files below
                    continue;
                }

                if ($info->isDir()) {
                    // pathname as locale
                    if (($this->_options['scan'] === self::LOCALE_DIRECTORY) and (Zend_Locale::isLocale($file, true, false))) {
                        if (strlen($prev) <= strlen($file)) {
                            $locale = $file;
                            $prev   = (string) $locale;
                        }
                    }
                } else if ($info->isFile()) {
                    // filename as locale
                    if ($this->_options['scan'] === self::LOCALE_FILENAME) {
                        $filename = explode('.', $file);
                        array_pop($filename);
                        $filename = implode('.', $filename);
                        if (Zend_Locale::isLocale((string) $filename, true, false)) {
                            $locale = (string) $filename;
                        } else {
                            $parts  = explode('.', $file);
                            $parts2 = array();
                            foreach($parts as $token) {
                                $parts2 += explode('_', $token);
                            }
                            $parts  = array_merge($parts, $parts2);
                            $parts2 = array();
                            foreach($parts as $token) {
                                $parts2 += explode('-', $token);
                            }
                            $parts = array_merge($parts, $parts2);
                            $parts = array_unique($parts);
                            $prev  = '';
                            foreach($parts as $token) {
                                if (Zend_Locale::isLocale($token, true, false)) {
                                    if (strlen($prev) <= strlen($token)) {
                                        $locale = $token;
                                        $prev   = $token;
                                    }
                                }
                            }
                        }
                    }
                    try {
                        $this->_addTranslationData($info->getPathname(), (string) $locale, $this->_options);
                        if ((isset($this->_translate[(string) $locale]) === true) and (count($this->_translate[(string) $locale]) > 0)) {
                            $this->setLocale($locale);
                        }
                    } catch (Zend_Translate_Exception $e) {
                        // ignore failed sources while scanning
                    }
                }
            }
        } else {
            $this->_addTranslationData($data, (string) $locale, $this->_options);
            if ((isset($this->_translate[(string) $locale]) === true) and (count($this->_translate[(string) $locale]) > 0)) {
                $this->setLocale($locale);
            }
        }

        if ((isset($this->_translate[$originate]) === true) and (count($this->_translate[$originate]) > 0) and ($originate !== (string) $locale)) {
            $this->setLocale($originate);
        }

        return $this;
    }

    /**
     * Sets new adapter options
     *
     * @param  array $options Adapter options
     * @throws Zend_Translate_Exception
     * @return Zend_Translate_Adapter Provides fluent interface
     */
    public function setOptions(array $options = array())
    {
        $change = false;
        $locale = null;
        foreach ($options as $key => $option) {
            if ($key == 'locale') {
                $locale = $option;
            } else if ((isset($this->_options[$key]) and ($this->_options[$key] != $option)) or
                    !isset($this->_options[$key])) {
                if (($key == 'log') && !($option instanceof Zend_Log)) {
                    require_once 'Zend/Translate/Exception.php';
                    throw new Zend_Translate_Exception('Instance of Zend_Log expected for option log');
                }

                $this->_options[$key] = $option;
                $change = true;
            }
        }

        if ($locale !== null) {
            $this->setLocale($locale);
        }

        if (isset(self::$_cache) and ($change == true)) {
            $id = 'Zend_Translate_' . $this->toString() . '_Options';
            self::$_cache->save( serialize($this->_options), $id, array('Zend_Translate'));
        }

        return $this;
    }

    /**
     * Returns the adapters name and it's options
     *
     * @param  string|null $optionKey String returns this option
     *                                null returns all options
     * @return integer|string|array|null
     */
    public function getOptions($optionKey = null)
    {
        if ($optionKey === null) {
            return $this->_options;
        }

        if (isset($this->_options[$optionKey]) === true) {
            return $this->_options[$optionKey];
        }

        return null;
    }

    /**
     * Gets locale
     *
     * @return Zend_Locale|string|null
     */
    public function getLocale()
    {
        return $this->_options['locale'];
    }

    /**
     * Sets locale
     *
     * @param  string|Zend_Locale $locale Locale to set
     * @throws Zend_Translate_Exception
     * @return Zend_Translate_Adapter Provides fluent interface
     */
    public function setLocale($locale)
    {
        if (($locale === "auto") or ($locale === null)) {
            $this->_automatic = true;
        } else {
            $this->_automatic = false;
        }

        try {
            $locale = Zend_Locale::findLocale($locale);
        } catch (Zend_Locale_Exception $e) {
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception("The given Language ({$locale}) does not exist");
        }

        if (!isset($this->_translate[$locale])) {
            $temp = explode('_', $locale);
            if (!isset($this->_translate[$temp[0]]) and !isset($this->_translate[$locale])) {
                if (!$this->_options['disableNotices']) {
                    if ($this->_options['log']) {
                        $this->_options['log']->notice("The language '{$locale}' has to be added before it can be used.");
                    } else {
                        trigger_error("The language '{$locale}' has to be added before it can be used.", E_USER_NOTICE);
                    }
                }
            }

            $locale = $temp[0];
        }

        if (empty($this->_translate[$locale])) {
            if (!$this->_options['disableNotices']) {
                if ($this->_options['log']) {
                    $this->_options['log']->notice("No translation for the language '{$locale}' available.");
                } else {
                    trigger_error("No translation for the language '{$locale}' available.", E_USER_NOTICE);
                }
            }
        }

        if ($this->_options['locale'] != $locale) {
            $this->_options['locale'] = $locale;

            if (isset(self::$_cache)) {
                $id = 'Zend_Translate_' . $this->toString() . '_Options';
                self::$_cache->save( serialize($this->_options), $id, array('Zend_Translate'));
            }
        }

        return $this;
    }

    /**
     * Returns the available languages from this adapter
     *
     * @return array
     */
    public function getList()
    {
        $list = array_keys($this->_translate);
        $result = null;
        foreach($list as $value) {
            if (!empty($this->_translate[$value])) {
                $result[$value] = $value;
            }
        }
        return $result;
    }

    /**
     * Returns all available message ids from this adapter
     * If no locale is given, the actual language will be used
     *
     * @param  string|Zend_Locale $locale (optional) Language to return the message ids from
     * @return array
     */
    public function getMessageIds($locale = null)
    {
        if (empty($locale) or !$this->isAvailable($locale)) {
            $locale = $this->_options['locale'];
        }

        return array_keys($this->_translate[(string) $locale]);
    }

    /**
     * Returns all available translations from this adapter
     * If no locale is given, the actual language will be used
     * If 'all' is given the complete translation dictionary will be returned
     *
     * @param  string|Zend_Locale $locale (optional) Language to return the messages from
     * @return array
     */
    public function getMessages($locale = null)
    {
        if ($locale === 'all') {
            return $this->_translate;
        }

        if ((empty($locale) === true) or ($this->isAvailable($locale) === false)) {
            $locale = $this->_options['locale'];
        }

        return $this->_translate[(string) $locale];
    }

    /**
     * Is the wished language available ?
     *
     * @see    Zend_Locale
     * @param  string|Zend_Locale $locale Language to search for, identical with locale identifier,
     *                                    @see Zend_Locale for more information
     * @return boolean
     */
    public function isAvailable($locale)
    {
        $return = isset($this->_translate[(string) $locale]);
        return $return;
    }

    /**
     * Load translation data
     *
     * @param  mixed              $data
     * @param  string|Zend_Locale $locale
     * @param  array              $options (optional)
     * @return array
     */
    abstract protected function _loadTranslationData($data, $locale, array $options = array());

    /**
     * Internal function for adding translation data
     *
     * It may be a new language or additional data for existing language
     * If $clear parameter is true, then translation data for specified
     * language is replaced and added otherwise
     *
     * @see    Zend_Locale
     * @param  array|string       $data    Translation data
     * @param  string|Zend_Locale $locale  Locale/Language to add data for, identical with locale identifier,
     *                                     @see Zend_Locale for more information
     * @param  array              $options (optional) Option for this Adapter
     * @throws Zend_Translate_Exception
     * @return Zend_Translate_Adapter Provides fluent interface
     */
    private function _addTranslationData($data, $locale, array $options = array())
    {
        try {
            $locale    = Zend_Locale::findLocale($locale);
        } catch (Zend_Locale_Exception $e) {
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception("The given Language '{$locale}' does not exist");
        }

        if ($options['clear'] || !isset($this->_translate[$locale])) {
            $this->_translate[$locale] = array();
        }

        $read = true;
        if (isset(self::$_cache)) {
            $id = 'Zend_Translate_' . md5(serialize($data)) . '_' . $this->toString();
            $result = self::$_cache->load($id);
            if ($result) {
                $temp = unserialize($result);
                $read = false;
            }
        }

        if ($read) {
            $temp = $this->_loadTranslationData($data, $locale, $options);
        }

        if (empty($temp)) {
            $temp = array();
        }

        $keys = array_keys($temp);
        foreach($keys as $key) {
            if (!isset($this->_translate[$key])) {
                $this->_translate[$key] = array();
            }

            $this->_translate[$key] = $temp[$key] + $this->_translate[$key];
        }

        if ($this->_automatic === true) {
            $find = new Zend_Locale($locale);
            $browser = $find->getEnvironment() + $find->getBrowser();
            arsort($browser);
            foreach($browser as $language => $quality) {
                if (isset($this->_translate[$language])) {
                    $this->_options['locale'] = $language;
                    break;
                }
            }
        }

        if (($read) and (isset(self::$_cache))) {
            $id = 'Zend_Translate_' . md5(serialize($data)) . '_' . $this->toString();
            self::$_cache->save( serialize($temp), $id, array('Zend_Translate'));
        }

        return $this;
    }

    /**
     * Translates the given string
     * returns the translation
     *
     * @see Zend_Locale
     * @param  string|array       $messageId Translation string, or Array for plural translations
     * @param  string|Zend_Locale $locale    (optional) Locale/Language to use, identical with
     *                                       locale identifier, @see Zend_Locale for more information
     * @return string
     */
    public function translate($messageId, $locale = null)
    {
        if ($locale === null) {
            $locale = $this->_options['locale'];
        }

        $plural = null;
        if (is_array($messageId)) {
            if (count($messageId) > 2) {
                $number    = array_pop($messageId);
                if (!is_numeric($number)) {
                    $plocale = $number;
                    $number       = array_pop($messageId);
                } else {
                    $plocale = 'en';
                }

                $plural    = $messageId;
                $messageId = $messageId[0];
            } else {
                $messageId = $messageId[0];
            }
        }

        if (!Zend_Locale::isLocale($locale, true, false)) {
            if (!Zend_Locale::isLocale($locale, false, false)) {
                // language does not exist, return original string
                $this->_log($messageId, $locale);
                if ($plural === null) {
                    return $messageId;
                }

                $rule = Zend_Translate_Plural::getPlural($number, $plocale);
                if (!isset($plural[$rule])) {
                    $rule = 0;
                }

                return $plural[$rule];
            }

            $locale = new Zend_Locale($locale);
        }

        $locale = (string) $locale;
        if (isset($this->_translate[$locale][$messageId])) {
            // return original translation
            if ($plural === null) {
                return $this->_translate[$locale][$messageId];
            }

            $rule = Zend_Translate_Plural::getPlural($number, $locale);
            if (isset($this->_translate[$locale][$plural[0]][$rule])) {
                return $this->_translate[$locale][$plural[0]][$rule];
            }
        } else if (strlen($locale) != 2) {
            // faster than creating a new locale and separate the leading part
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));

            if (isset($this->_translate[$locale][$messageId])) {
                // return regionless translation (en_US -> en)
                if ($plural === null) {
                    return $this->_translate[$locale][$messageId];
                }

                $rule = Zend_Translate_Plural::getPlural($number, $locale);
                if (isset($this->_translate[$locale][$plural[0]][$rule])) {
                    return $this->_translate[$locale][$plural[0]][$rule];
                }
            }
        }

        $this->_log($messageId, $locale);
        if ($plural === null) {
            return $messageId;
        }

        $rule = Zend_Translate_Plural::getPlural($number, $plocale);
        if (!isset($plural[$rule])) {
            $rule = 0;
        }

        return $plural[$rule];
    }

    /**
     * Translates the given string using plural notations
     * Returns the translated string
     *
     * @see Zend_Locale
     * @param  string             $singular Singular translation string
     * @param  string             $plural   Plural translation string
     * @param  integer            $number   Number for detecting the correct plural
     * @param  string|Zend_Locale $locale   (Optional) Locale/Language to use, identical with
     *                                      locale identifier, @see Zend_Locale for more information
     * @return string
     */
    public function plural($singular, $plural, $number, $locale = null)
    {
        return $this->translate(array($singular, $plural, $number), $locale);
    }

    /**
     * Logs a message when the log option is set
     *
     * @param string $message Message to log
     * @param String $locale  Locale to log
     */
    protected function _log($message, $locale) {
        if ($this->_options['logUntranslated']) {
            $message = str_replace('%message%', $message, $this->_options['logMessage']);
            $message = str_replace('%locale%', $locale, $message);
            if ($this->_options['log']) {
                $this->_options['log']->notice($message);
            } else {
                trigger_error($message, E_USER_NOTICE);
            }
        }
    }

    /**
     * Translates the given string
     * returns the translation
     *
     * @param  string             $messageId Translation string
     * @param  string|Zend_Locale $locale    (optional) Locale/Language to use, identical with locale
     *                                       identifier, @see Zend_Locale for more information
     * @return string
     */
    public function _($messageId, $locale = null)
    {
        return $this->translate($messageId, $locale);
    }

    /**
     * Checks if a string is translated within the source or not
     * returns boolean
     *
     * @param  string             $messageId Translation string
     * @param  boolean            $original  (optional) Allow translation only for original language
     *                                       when true, a translation for 'en_US' would give false when it can
     *                                       be translated with 'en' only
     * @param  string|Zend_Locale $locale    (optional) Locale/Language to use, identical with locale identifier,
     *                                       see Zend_Locale for more information
     * @return boolean
     */
    public function isTranslated($messageId, $original = false, $locale = null)
    {
        if (($original !== false) and ($original !== true)) {
            $locale = $original;
            $original = false;
        }

        if ($locale === null) {
            $locale = $this->_options['locale'];
        }

        if (!Zend_Locale::isLocale($locale, true, false)) {
            if (!Zend_Locale::isLocale($locale, false, false)) {
                // language does not exist, return original string
                $this->_log($messageId, $locale);
                return false;
            }

            $locale = new Zend_Locale($locale);
        }

        $locale = (string) $locale;
        if (isset($this->_translate[$locale][$messageId]) === true) {
            // return original translation
            return true;
        } else if ((strlen($locale) != 2) and ($original === false)) {
            // faster than creating a new locale and separate the leading part
            $locale = substr($locale, 0, -strlen(strrchr($locale, '_')));

            if (isset($this->_translate[$locale][$messageId]) === true) {
                // return regionless translation (en_US -> en)
                return true;
            }
        }

        // No translation found, return original
        $this->_log($messageId, $locale);
        return false;
    }

    /**
     * Returns the set cache
     *
     * @return Zend_Cache_Core The set cache
     */
    public static function getCache()
    {
        return self::$_cache;
    }

    /**
     * Sets a cache for all Zend_Translate_Adapters
     *
     * @param Zend_Cache_Core $cache Cache to store to
     */
    public static function setCache(Zend_Cache_Core $cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Returns true when a cache is set
     *
     * @return boolean
     */
    public static function hasCache()
    {
        if (self::$_cache !== null) {
            return true;
        }

        return false;
    }

    /**
     * Removes any set cache
     *
     * @return void
     */
    public static function removeCache()
    {
        self::$_cache = null;
    }

    /**
     * Clears all set cache data
     *
     * @return void
     */
    public static function clearCache()
    {
        require_once 'Zend/Cache.php';
        self::$_cache->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, array('Zend_Translate'));
    }

    /**
     * Returns the adapter name
     *
     * @return string
     */
    abstract public function toString();
}
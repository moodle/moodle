<?php

declare(strict_types=1);

namespace SimpleSAML;

use SAML2\Constants;
use SimpleSAML\Error;
use SimpleSAML\Utils;

/**
 * Configuration of SimpleSAMLphp
 *
 * @author Andreas Aakre Solberg, UNINETT AS. <andreas.solberg@uninett.no>
 * @package SimpleSAMLphp
 */
class Configuration implements Utils\ClearableState
{
    /**
     * The release version of this package
     */
    public const VERSION = '1.19.7';

    /**
     * A default value which means that the given option is required.
     *
     * @var string
     */
    const REQUIRED_OPTION = '___REQUIRED_OPTION___';


    /**
     * Associative array with mappings from instance-names to configuration objects.
     *
     * @var array
     */
    private static $instance = [];


    /**
     * Configuration directories.
     *
     * This associative array contains the mappings from configuration sets to
     * configuration directories.
     *
     * @var array
     */
    private static $configDirs = [];


    /**
     * Cache of loaded configuration files.
     *
     * The index in the array is the full path to the file.
     *
     * @var array
     */
    private static $loadedConfigs = [];


    /**
     * The configuration array.
     *
     * @var array
     */
    private $configuration;


    /**
     * The location which will be given when an error occurs.
     *
     * @var string
     */
    private $location;


    /**
     * The file this configuration was loaded from.
     *
     * @var string|null
     */
    private $filename = null;


    /**
     * Temporary property that tells if the deprecated getBaseURL() method has been called or not.
     *
     * @var bool
     */
    private $deprecated_base_url_used = false;


    /**
     * Initializes a configuration from the given array.
     *
     * @param array $config The configuration array.
     * @param string $location The location which will be given when an error occurs.
     */
    public function __construct($config, $location)
    {
        assert(is_array($config));
        assert(is_string($location));

        $this->configuration = $config;
        $this->location = $location;
    }

    /**
     * Load the given configuration file.
     *
     * @param string $filename The full path of the configuration file.
     * @param bool $required Whether the file is required.
     *
     * @return \SimpleSAML\Configuration The configuration file. An exception will be thrown if the
     *                                   configuration file is missing.
     *
     * @throws \Exception If the configuration file is invalid or missing.
     */
    private static function loadFromFile(string $filename, bool $required): Configuration
    {
        if (array_key_exists($filename, self::$loadedConfigs)) {
            return self::$loadedConfigs[$filename];
        }

        if (file_exists($filename)) {
            $config = 'UNINITIALIZED';

            // the file initializes a variable named '$config'
            ob_start();
            if (interface_exists('Throwable', false)) {
                try {
                    require($filename);
                } catch (\ParseError $e) {
                    self::$loadedConfigs[$filename] = self::loadFromArray([], '[ARRAY]', 'simplesaml');
                    throw new Error\ConfigurationError($e->getMessage(), $filename, []);
                }
            } else {
                require($filename);
            }

            $spurious_output = ob_get_length() > 0;
            ob_end_clean();

            // check that $config exists
            if (!isset($config)) {
                throw new Error\ConfigurationError(
                    '$config is not defined in the configuration file.',
                    $filename
                );
            }

            // check that $config is initialized to an array
            if (!is_array($config)) {
                throw new Error\ConfigurationError(
                    '$config is not an array.',
                    $filename
                );
            }

            // check that $config is not empty
            if (empty($config)) {
                throw new Error\ConfigurationError(
                    '$config is empty.',
                    $filename
                );
            }
        } elseif ($required) {
            // file does not exist, but is required
            throw new Error\ConfigurationError('Missing configuration file', $filename);
        } else {
            // file does not exist, but is optional, so return an empty configuration object without saving it
            $cfg = new Configuration([], $filename);
            $cfg->filename = $filename;
            return $cfg;
        }

        $cfg = new Configuration($config, $filename);
        $cfg->filename = $filename;

        self::$loadedConfigs[$filename] = $cfg;

        if ($spurious_output) {
            Logger::warning(
                "The configuration file '$filename' generates output. Please review your configuration."
            );
        }

        return $cfg;
    }


    /**
     * Set the directory for configuration files for the given configuration set.
     *
     * @param string $path The directory which contains the configuration files.
     * @param string $configSet The configuration set. Defaults to 'simplesaml'.
     * @return void
     */
    public static function setConfigDir($path, $configSet = 'simplesaml')
    {
        assert(is_string($path));
        assert(is_string($configSet));

        self::$configDirs[$configSet] = $path;
    }

    /**
     * Store a pre-initialized configuration.
     *
     * Allows consumers to create configuration objects without having them
     * loaded from a file.
     *
     * @param \SimpleSAML\Configuration $config  The configuration object to store
     * @param string $filename  The name of the configuration file.
     * @param string $configSet  The configuration set. Optional, defaults to 'simplesaml'.
     * @return void
     * @throws \Exception
     */
    public static function setPreLoadedConfig(
        Configuration $config,
        $filename = 'config.php',
        $configSet = 'simplesaml'
    ) {
        assert(is_string($filename));
        assert(is_string($configSet));

        if (!array_key_exists($configSet, self::$configDirs)) {
            if ($configSet !== 'simplesaml') {
                throw new \Exception('Configuration set \'' . $configSet . '\' not initialized.');
            } else {
                self::$configDirs['simplesaml'] = dirname(dirname(dirname(__FILE__))) . '/config';
            }
        }

        $dir = self::$configDirs[$configSet];
        $filePath = $dir . '/' . $filename;

        self::$loadedConfigs[$filePath] = $config;
    }


    /**
     * Load a configuration file from a configuration set.
     *
     * @param string $filename The name of the configuration file.
     * @param string $configSet The configuration set. Optional, defaults to 'simplesaml'.
     *
     * @return \SimpleSAML\Configuration The Configuration object.
     * @throws \Exception If the configuration set is not initialized.
     */
    public static function getConfig($filename = 'config.php', $configSet = 'simplesaml')
    {
        assert(is_string($filename));
        assert(is_string($configSet));

        if (!array_key_exists($configSet, self::$configDirs)) {
            if ($configSet !== 'simplesaml') {
                throw new \Exception('Configuration set \'' . $configSet . '\' not initialized.');
            } else {
                self::$configDirs['simplesaml'] = Utils\Config::getConfigDir();
            }
        }

        $dir = self::$configDirs[$configSet];
        $filePath = $dir . '/' . $filename;
        return self::loadFromFile($filePath, true);
    }


    /**
     * Load a configuration file from a configuration set.
     *
     * This function will return a configuration object even if the file does not exist.
     *
     * @param string $filename The name of the configuration file.
     * @param string $configSet The configuration set. Optional, defaults to 'simplesaml'.
     *
     * @return \SimpleSAML\Configuration A configuration object.
     * @throws \Exception If the configuration set is not initialized.
     */
    public static function getOptionalConfig($filename = 'config.php', $configSet = 'simplesaml')
    {
        assert(is_string($filename));
        assert(is_string($configSet));

        if (!array_key_exists($configSet, self::$configDirs)) {
            if ($configSet !== 'simplesaml') {
                throw new \Exception('Configuration set \'' . $configSet . '\' not initialized.');
            } else {
                self::$configDirs['simplesaml'] = Utils\Config::getConfigDir();
            }
        }

        $dir = self::$configDirs[$configSet];
        $filePath = $dir . '/' . $filename;
        return self::loadFromFile($filePath, false);
    }


    /**
     * Loads a configuration from the given array.
     *
     * @param array  $config The configuration array.
     * @param string $location The location which will be given when an error occurs. Optional.
     * @param string|null $instance The name of this instance. If specified, the configuration will be loaded and an
     * instance with that name will be kept for it to be retrieved later with getInstance($instance). If null, the
     * configuration will not be kept for later use. Defaults to null.
     *
     * @return \SimpleSAML\Configuration The configuration object.
     */
    public static function loadFromArray($config, $location = '[ARRAY]', $instance = null)
    {
        assert(is_array($config));
        assert(is_string($location));

        $c = new Configuration($config, $location);
        if ($instance !== null) {
            self::$instance[$instance] = $c;
        }
        return $c;
    }


    /**
     * Get a configuration file by its instance name.
     *
     * This function retrieves a configuration file by its instance name. The instance
     * name is initialized by the init function, or by copyFromBase function.
     *
     * If no configuration file with the given instance name is found, an exception will
     * be thrown.
     *
     * @param string $instancename The instance name of the configuration file. Deprecated.
     *
     * @return \SimpleSAML\Configuration The configuration object.
     *
     * @throws \Exception If the configuration with $instancename name is not initialized.
     */
    public static function getInstance($instancename = 'simplesaml')
    {
        assert(is_string($instancename));

        // check if the instance exists already
        if (array_key_exists($instancename, self::$instance)) {
            return self::$instance[$instancename];
        }

        if ($instancename === 'simplesaml') {
            try {
                return self::getConfig();
            } catch (Error\ConfigurationError $e) {
                throw Error\CriticalConfigurationError::fromException($e);
            }
        }

        throw new Error\CriticalConfigurationError(
            'Configuration with name ' . $instancename . ' is not initialized.'
        );
    }


    /**
     * Initialize a instance name with the given configuration file.
     *
     * TODO: remove.
     *
     * @param string $path
     * @param string $instancename
     * @param string $configfilename
     * @return \SimpleSAML\Configuration
     *
     * @see setConfigDir()
     * @deprecated This function is superseeded by the setConfigDir function.
     */
    public static function init($path, $instancename = 'simplesaml', $configfilename = 'config.php')
    {
        assert(is_string($path));
        assert(is_string($instancename));
        assert(is_string($configfilename));

        if ($instancename === 'simplesaml') {
            // for backwards compatibility
            self::setConfigDir($path, 'simplesaml');
        }

        // check if we already have loaded the given config - return the existing instance if we have
        if (array_key_exists($instancename, self::$instance)) {
            return self::$instance[$instancename];
        }

        self::$instance[$instancename] = self::loadFromFile($path . '/' . $configfilename, true);
        return self::$instance[$instancename];
    }


    /**
     * Load a configuration file which is located in the same directory as this configuration file.
     *
     * TODO: remove.
     *
     * @param string $instancename
     * @param string $filename
     * @return \SimpleSAML\Configuration
     *
     * @see getConfig()
     * @deprecated This function is superseeded by the getConfig() function.
     */
    public function copyFromBase($instancename, $filename)
    {
        assert(is_string($instancename));
        assert(is_string($filename));
        assert($this->filename !== null);

        // check if we already have loaded the given config - return the existing instance if we have
        if (array_key_exists($instancename, self::$instance)) {
            return self::$instance[$instancename];
        }

        $dir = dirname($this->filename);

        self::$instance[$instancename] = self::loadFromFile($dir . '/' . $filename, true);
        return self::$instance[$instancename];
    }


    /**
     * Retrieve the current version of SimpleSAMLphp.
     *
     * @return string
     */
    public function getVersion()
    {
        return self::VERSION;
    }


    /**
     * Retrieve a configuration option set in config.php.
     *
     * @param string $name Name of the configuration option.
     * @param mixed  $default Default value of the configuration option. This parameter will default to null if not
     *                        specified. This can be set to \SimpleSAML\Configuration::REQUIRED_OPTION, which will
     *                        cause an exception to be thrown if the option isn't found.
     *
     * @return mixed The configuration option with name $name, or $default if the option was not found.
     *
     * @throws \Exception If the required option cannot be retrieved.
     */
    public function getValue($name, $default = null)
    {
        // return the default value if the option is unset
        if (!array_key_exists($name, $this->configuration)) {
            if ($default === self::REQUIRED_OPTION) {
                throw new \Exception(
                    $this->location . ': Could not retrieve the required option ' .
                    var_export($name, true)
                );
            }
            return $default;
        }

        return $this->configuration[$name];
    }


    /**
     * Check whether a key in the configuration exists or not.
     *
     * @param string $name The key in the configuration to look for.
     *
     * @return boolean If the value is set in this configuration.
     */
    public function hasValue($name)
    {
        return array_key_exists($name, $this->configuration);
    }


    /**
     * Check whether any key of the set given exists in the configuration.
     *
     * @param array $names An array of options to look for.
     *
     * @return boolean If any of the keys in $names exist in the configuration
     */
    public function hasValueOneOf($names)
    {
        foreach ($names as $name) {
            if ($this->hasValue($name)) {
                return true;
            }
        }
        return false;
    }


    /**
     * Retrieve the absolute path of the SimpleSAMLphp installation, relative to the root of the website.
     *
     * For example: simplesaml/
     *
     * The path will always end with a '/' and never have a leading slash.
     *
     * @return string The absolute path relative to the root of the website.
     *
     * @throws \SimpleSAML\Error\CriticalConfigurationError If the format of 'baseurlpath' is incorrect.
     *
     * @deprecated This method will be removed in SimpleSAMLphp 2.0. Please use getBasePath() instead.
     */
    public function getBaseURL()
    {
        if (!$this->deprecated_base_url_used) {
            $this->deprecated_base_url_used = true;
            Logger::warning(
                "\SimpleSAML\Configuration::getBaseURL() is deprecated, please use getBasePath() instead."
            );
        }
        if (preg_match('/^\*(.*)$/D', $this->getString('baseurlpath', 'simplesaml/'), $matches)) {
            // deprecated behaviour, will be removed in the future
            return Utils\HTTP::getFirstPathElement(false) . $matches[1];
        }
        return ltrim($this->getBasePath(), '/');
    }


    /**
     * Retrieve the absolute path pointing to the SimpleSAMLphp installation.
     *
     * The path is guaranteed to start and end with a slash ('/'). E.g.: /simplesaml/
     *
     * @return string The absolute path where SimpleSAMLphp can be reached in the web server.
     *
     * @throws \SimpleSAML\Error\CriticalConfigurationError If the format of 'baseurlpath' is incorrect.
     */
    public function getBasePath()
    {
        $baseURL = $this->getString('baseurlpath', 'simplesaml/');

        if (preg_match('#^https?://[^/]*(?:/(.+/?)?)?$#', $baseURL, $matches)) {
            // we have a full url, we need to strip the path
            if (!array_key_exists(1, $matches)) {
                // absolute URL without path
                return '/';
            }
            return '/' . rtrim($matches[1], '/') . '/';
        } elseif ($baseURL === '' || $baseURL === '/') {
            // root directory of site
            return '/';
        } elseif (preg_match('#^/?((?:[^/\s]+/?)+)#', $baseURL, $matches)) {
            // local path only
            return '/' . rtrim($matches[1], '/') . '/';
        } else {
            /*
             * Invalid 'baseurlpath'. We cannot recover from this, so throw a critical exception and try to be graceful
             * with the configuration. Use a guessed base path instead of the one provided.
             */
            $c = $this->toArray();
            $c['baseurlpath'] = Utils\HTTP::guessBasePath();
            throw new Error\CriticalConfigurationError(
                'Incorrect format for option \'baseurlpath\'. Value is: "' .
                $this->getString('baseurlpath', 'simplesaml/') . '". Valid format is in the form' .
                ' [(http|https)://(hostname|fqdn)[:port]]/[path/to/simplesaml/].',
                $this->filename,
                $c
            );
        }
    }


    /**
     * This function resolves a path which may be relative to the SimpleSAMLphp base directory.
     *
     * The path will never end with a '/'.
     *
     * @param string|null $path The path we should resolve. This option may be null.
     *
     * @return string|null $path if $path is an absolute path, or $path prepended with the base directory of this
     * SimpleSAMLphp installation. We will return NULL if $path is null.
     */
    public function resolvePath($path)
    {
        if ($path === null) {
            return null;
        }

        assert(is_string($path));

        return Utils\System::resolvePath($path, $this->getBaseDir());
    }


    /**
     * Retrieve a path configuration option set in config.php.
     *
     * The function will always return an absolute path unless the option is not set. It will then return the default
     * value.
     *
     * It checks if the value starts with a slash, and prefixes it with the value from getBaseDir if it doesn't.
     *
     * @param string $name Name of the configuration option.
     * @param string|null $default Default value of the configuration option. This parameter will default to null if
     * not specified.
     *
     * @return string|null The path configuration option with name $name, or $default if the option was not found.
     */
    public function getPathValue($name, $default = null)
    {
        // return the default value if the option is unset
        if (!array_key_exists($name, $this->configuration)) {
            $path = $default;
        } else {
            $path = $this->configuration[$name];
        }

        $path = $this->resolvePath($path);
        if ($path === null) {
            return null;
        }

        return $path . '/';
    }


    /**
     * Retrieve the base directory for this SimpleSAMLphp installation.
     *
     * This function first checks the 'basedir' configuration option. If this option is undefined or null, then we
     * fall back to looking at the current filename.
     *
     * @return string The absolute path to the base directory for this SimpleSAMLphp installation. This path will
     * always end with a slash.
     */
    public function getBaseDir()
    {
        // check if a directory is configured in the configuration file
        $dir = $this->getString('basedir', null);
        if ($dir !== null) {
            // add trailing slash if it is missing
            if (substr($dir, -1) !== DIRECTORY_SEPARATOR) {
                $dir .= DIRECTORY_SEPARATOR;
            }

            return $dir;
        }

        // the directory wasn't set in the configuration file, path is <base directory>/lib/SimpleSAML/Configuration.php
        $dir = __FILE__;
        assert(basename($dir) === 'Configuration.php');

        $dir = dirname($dir);
        assert(basename($dir) === 'SimpleSAML');

        $dir = dirname($dir);
        assert(basename($dir) === 'lib');

        $dir = dirname($dir);

        // Add trailing directory separator
        $dir .= DIRECTORY_SEPARATOR;

        return $dir;
    }


    /**
     * This function retrieves a boolean configuration option.
     *
     * An exception will be thrown if this option isn't a boolean, or if this option isn't found, and no default value
     * is given.
     *
     * @param string $name The name of the option.
     * @param mixed  $default A default value which will be returned if the option isn't found. The option will be
     *                  required if this parameter isn't given. The default value can be any value, including
     *                  null.
     *
     * @return boolean|mixed The option with the given name, or $default if the option isn't found and $default is
     *     specified.
     *
     * @throws \Exception If the option is not boolean.
     */
    public function getBoolean($name, $default = self::REQUIRED_OPTION)
    {
        assert(is_string($name));

        $ret = $this->getValue($name, $default);

        if ($ret === $default) {
            // the option wasn't found, or it matches the default value. In any case, return this value
            return $ret;
        }

        if (!is_bool($ret)) {
            throw new \Exception(
                $this->location . ': The option ' . var_export($name, true) .
                ' is not a valid boolean value.'
            );
        }

        return $ret;
    }


    /**
     * This function retrieves a string configuration option.
     *
     * An exception will be thrown if this option isn't a string, or if this option isn't found, and no default value
     * is given.
     *
     * @param string $name The name of the option.
     * @param mixed  $default A default value which will be returned if the option isn't found. The option will be
     *                  required if this parameter isn't given. The default value can be any value, including
     *                  null.
     *
     * @return string|mixed The option with the given name, or $default if the option isn't found and $default is
     *     specified.
     *
     * @throws \Exception If the option is not a string.
     */
    public function getString($name, $default = self::REQUIRED_OPTION)
    {
        assert(is_string($name));

        $ret = $this->getValue($name, $default);

        if ($ret === $default) {
            // the option wasn't found, or it matches the default value. In any case, return this value
            return $ret;
        }

        if (!is_string($ret)) {
            throw new \Exception(
                $this->location . ': The option ' . var_export($name, true) .
                ' is not a valid string value.'
            );
        }

        return $ret;
    }


    /**
     * This function retrieves an integer configuration option.
     *
     * An exception will be thrown if this option isn't an integer, or if this option isn't found, and no default value
     * is given.
     *
     * @param string $name The name of the option.
     * @param mixed  $default A default value which will be returned if the option isn't found. The option will be
     *                  required if this parameter isn't given. The default value can be any value, including
     *                  null.
     *
     * @return int|mixed The option with the given name, or $default if the option isn't found and $default is
     * specified.
     *
     * @throws \Exception If the option is not an integer.
     */
    public function getInteger($name, $default = self::REQUIRED_OPTION)
    {
        assert(is_string($name));

        $ret = $this->getValue($name, $default);

        if ($ret === $default) {
            // the option wasn't found, or it matches the default value. In any case, return this value
            return $ret;
        }

        if (!is_int($ret)) {
            throw new \Exception(
                $this->location . ': The option ' . var_export($name, true) .
                ' is not a valid integer value.'
            );
        }

        return $ret;
    }


    /**
     * This function retrieves an integer configuration option where the value must be in the specified range.
     *
     * An exception will be thrown if:
     * - the option isn't an integer
     * - the option isn't found, and no default value is given
     * - the value is outside of the allowed range
     *
     * @param string $name The name of the option.
     * @param int    $minimum The smallest value which is allowed.
     * @param int    $maximum The largest value which is allowed.
     * @param mixed  $default A default value which will be returned if the option isn't found. The option will be
     *                  required if this parameter isn't given. The default value can be any value, including
     *                  null.
     *
     * @return int|mixed The option with the given name, or $default if the option isn't found and $default is
     *     specified.
     *
     * @throws \Exception If the option is not in the range specified.
     */
    public function getIntegerRange($name, $minimum, $maximum, $default = self::REQUIRED_OPTION)
    {
        assert(is_string($name));
        assert(is_int($minimum));
        assert(is_int($maximum));

        $ret = $this->getInteger($name, $default);

        if ($ret === $default) {
            // the option wasn't found, or it matches the default value. In any case, return this value
            return $ret;
        }

        if ($ret < $minimum || $ret > $maximum) {
            throw new \Exception(
                $this->location . ': Value of option ' . var_export($name, true) .
                ' is out of range. Value is ' . $ret . ', allowed range is ['
                . $minimum . ' - ' . $maximum . ']'
            );
        }

        return $ret;
    }


    /**
     * Retrieve a configuration option with one of the given values.
     *
     * This will check that the configuration option matches one of the given values. The match will use
     * strict comparison. An exception will be thrown if it does not match.
     *
     * The option can be mandatory or optional. If no default value is given, it will be considered to be
     * mandatory, and an exception will be thrown if it isn't provided. If a default value is given, it
     * is considered to be optional, and the default value is returned. The default value is automatically
     * included in the list of allowed values.
     *
     * @param string $name The name of the option.
     * @param array  $allowedValues The values the option is allowed to take, as an array.
     * @param mixed  $default The default value which will be returned if the option isn't found. If this parameter
     *                  isn't given, the option will be considered to be mandatory. The default value can be
     *                  any value, including null.
     *
     * @return mixed The option with the given name, or $default if the option isn't found and $default is given.
     *
     * @throws \Exception If the option does not have any of the allowed values.
     */
    public function getValueValidate($name, $allowedValues, $default = self::REQUIRED_OPTION)
    {
        assert(is_string($name));
        assert(is_array($allowedValues));

        $ret = $this->getValue($name, $default);
        if ($ret === $default) {
            // the option wasn't found, or it matches the default value. In any case, return this value
            return $ret;
        }

        if (!in_array($ret, $allowedValues, true)) {
            $strValues = [];
            foreach ($allowedValues as $av) {
                $strValues[] = var_export($av, true);
            }
            $strValues = implode(', ', $strValues);

            throw new \Exception(
                $this->location . ': Invalid value given for the option ' .
                var_export($name, true) . '. It should have one of the following values: ' .
                $strValues . '; but it had the following value: ' . var_export($ret, true)
            );
        }

        return $ret;
    }


    /**
     * This function retrieves an array configuration option.
     *
     * An exception will be thrown if this option isn't an array, or if this option isn't found, and no
     * default value is given.
     *
     * @param string $name The name of the option.
     * @param mixed  $default A default value which will be returned if the option isn't found. The option will be
     *                       required if this parameter isn't given. The default value can be any value, including
     *                       null.
     *
     * @return array|mixed The option with the given name, or $default if the option isn't found and $default is
     * specified.
     *
     * @throws \Exception If the option is not an array.
     */
    public function getArray($name, $default = self::REQUIRED_OPTION)
    {
        assert(is_string($name));

        $ret = $this->getValue($name, $default);

        if ($ret === $default) {
            // the option wasn't found, or it matches the default value. In any case, return this value
            return $ret;
        }

        if (!is_array($ret)) {
            throw new \Exception($this->location . ': The option ' . var_export($name, true) . ' is not an array.');
        }

        return $ret;
    }


    /**
     * This function retrieves an array configuration option.
     *
     * If the configuration option isn't an array, it will be converted to an array.
     *
     * @param string $name The name of the option.
     * @param mixed  $default A default value which will be returned if the option isn't found. The option will be
     *                       required if this parameter isn't given. The default value can be any value, including
     *                       null.
     *
     * @return array|mixed The option with the given name, or $default
     * if the option isn't found and $default is specified.
     */
    public function getArrayize($name, $default = self::REQUIRED_OPTION)
    {
        assert(is_string($name));

        $ret = $this->getValue($name, $default);

        if ($ret === $default) {
            // the option wasn't found, or it matches the default value. In any case, return this value
            return $ret;
        }

        if (!is_array($ret)) {
            $ret = [$ret];
        }

        return $ret;
    }


    /**
     * This function retrieves a configuration option with a string or an array of strings.
     *
     * If the configuration option is a string, it will be converted to an array with a single string
     *
     * @param string $name The name of the option.
     * @param mixed  $default A default value which will be returned if the option isn't found. The option will be
     *                       required if this parameter isn't given. The default value can be any value, including
     *                       null.
     *
     * @return array The option with the given name, or $default if the option isn't found and $default is specified.
     *
     * @throws \Exception If the option is not a string or an array of strings.
     */
    public function getArrayizeString($name, $default = self::REQUIRED_OPTION)
    {
        assert(is_string($name));

        $ret = $this->getArrayize($name, $default);

        if ($ret === $default) {
            // the option wasn't found, or it matches the default value. In any case, return this value
            return $ret;
        }

        foreach ($ret as $value) {
            if (!is_string($value)) {
                throw new \Exception(
                    $this->location . ': The option ' . var_export($name, true) .
                    ' must be a string or an array of strings.'
                );
            }
        }

        return $ret;
    }


    /**
     * Retrieve an array as a \SimpleSAML\Configuration object.
     *
     * This function will load the value of an option into a \SimpleSAML\Configuration object. The option must contain
     * an array.
     *
     * An exception will be thrown if this option isn't an array, or if this option isn't found, and no default value
     * is given.
     *
     * @param string $name The name of the option.
     * @param array|null $default A default value which will be used if the option isn't found. An empty Configuration
     *                        object will be returned if this parameter isn't given and the option doesn't exist.
     *                        This function will only return null if $default is set to null and the option
     *                        doesn't exist.
     *
     * @return mixed The option with the given name, or $default if the option isn't found and $default is specified.
     *
     * @throws \Exception If the option is not an array.
     */
    public function getConfigItem($name, $default = [])
    {
        assert(is_string($name));

        $ret = $this->getValue($name, $default);

        if ($ret === null) {
            // the option wasn't found, or it is explicitly null
            // do not instantiate a new Configuration instance, but just return null
            return null;
        }

        if (!is_array($ret)) {
            throw new \Exception(
                $this->location . ': The option ' . var_export($name, true) .
                ' is not an array.'
            );
        }

        return self::loadFromArray($ret, $this->location . '[' . var_export($name, true) . ']');
    }


    /**
     * Retrieve an array of arrays as an array of \SimpleSAML\Configuration objects.
     *
     * This function will retrieve an option containing an array of arrays, and create an array of
     * \SimpleSAML\Configuration objects from that array. The indexes in the new array will be the same as the original
     * indexes, but the values will be \SimpleSAML\Configuration objects.
     *
     * An exception will be thrown if this option isn't an array of arrays, or if this option isn't found, and no
     * default value is given.
     *
     * @param string $name The name of the option.
     *
     * @return array The array of \SimpleSAML\Configuration objects.
     *
     * @throws \Exception If the value of this element is not an array.
     *
     * @deprecated Very specific function, will be removed in a future release; use getConfigItem or getArray instead
     */
    public function getConfigList($name)
    {
        assert(is_string($name));

        $ret = $this->getValue($name, []);

        if (!is_array($ret)) {
            throw new \Exception(
                $this->location . ': The option ' . var_export($name, true) .
                ' is not an array.'
            );
        }

        $out = [];
        foreach ($ret as $index => $config) {
            $newLoc = $this->location . '[' . var_export($name, true) . '][' .
                var_export($index, true) . ']';
            if (!is_array($config)) {
                throw new \Exception($newLoc . ': The value of this element was expected to be an array.');
            }
            $out[$index] = self::loadFromArray($config, $newLoc);
        }

        return $out;
    }


    /**
     * Retrieve list of options.
     *
     * This function returns the name of all options which are defined in this
     * configuration file, as an array of strings.
     *
     * @return array Name of all options defined in this configuration file.
     */
    public function getOptions()
    {
        return array_keys($this->configuration);
    }


    /**
     * Convert this configuration object back to an array.
     *
     * @return array An associative array with all configuration options and values.
     */
    public function toArray()
    {
        return $this->configuration;
    }


    /**
     * Retrieve the default binding for the given endpoint type.
     *
     * This function combines the current metadata type (SAML 2 / SAML 1.1)
     * with the endpoint type to determine which binding is the default.
     *
     * @param string $endpointType The endpoint type.
     *
     * @return string The default binding.
     *
     * @throws \Exception If the default binding is missing for this endpoint type.
     */
    private function getDefaultBinding(string $endpointType): string
    {
        $set = $this->getString('metadata-set');
        switch ($set . ':' . $endpointType) {
            case 'saml20-idp-remote:SingleSignOnService':
            case 'saml20-idp-remote:SingleLogoutService':
            case 'saml20-sp-remote:SingleLogoutService':
                return Constants::BINDING_HTTP_REDIRECT;
            case 'saml20-sp-remote:AssertionConsumerService':
                return Constants::BINDING_HTTP_POST;
            case 'saml20-idp-remote:ArtifactResolutionService':
                return Constants::BINDING_SOAP;
            case 'shib13-idp-remote:SingleSignOnService':
                return 'urn:mace:shibboleth:1.0:profiles:AuthnRequest';
            case 'shib13-sp-remote:AssertionConsumerService':
                return 'urn:oasis:names:tc:SAML:1.0:profiles:browser-post';
            default:
                throw new \Exception('Missing default binding for ' . $endpointType . ' in ' . $set);
        }
    }


    /**
     * Helper function for dealing with metadata endpoints.
     *
     * @param string $endpointType The endpoint type.
     *
     * @return array Array of endpoints of the given type.
     *
     * @throws \Exception If any element of the configuration options for this endpoint type is incorrect.
     */
    public function getEndpoints($endpointType)
    {
        assert(is_string($endpointType));

        $loc = $this->location . '[' . var_export($endpointType, true) . ']:';

        if (!array_key_exists($endpointType, $this->configuration)) {
            // no endpoints of the given type
            return [];
        }


        $eps = $this->configuration[$endpointType];
        if (is_string($eps)) {
            // for backwards-compatibility
            $eps = [$eps];
        } elseif (!is_array($eps)) {
            throw new \Exception($loc . ': Expected array or string.');
        }


        foreach ($eps as $i => &$ep) {
            $iloc = $loc . '[' . var_export($i, true) . ']';

            if (is_string($ep)) {
                // for backwards-compatibility
                $ep = [
                    'Location' => $ep,
                    'Binding'  => $this->getDefaultBinding($endpointType),
                ];
                $responseLocation = $this->getString($endpointType . 'Response', null);
                if ($responseLocation !== null) {
                    $ep['ResponseLocation'] = $responseLocation;
                }
            } elseif (!is_array($ep)) {
                throw new \Exception($iloc . ': Expected a string or an array.');
            }

            if (!array_key_exists('Location', $ep)) {
                throw new \Exception($iloc . ': Missing Location.');
            }
            if (!is_string($ep['Location'])) {
                throw new \Exception($iloc . ': Location must be a string.');
            }

            if (!array_key_exists('Binding', $ep)) {
                throw new \Exception($iloc . ': Missing Binding.');
            }
            if (!is_string($ep['Binding'])) {
                throw new \Exception($iloc . ': Binding must be a string.');
            }

            if (array_key_exists('ResponseLocation', $ep)) {
                if (!is_string($ep['ResponseLocation'])) {
                    throw new \Exception($iloc . ': ResponseLocation must be a string.');
                }
            }

            if (array_key_exists('index', $ep)) {
                if (!is_int($ep['index'])) {
                    throw new \Exception($iloc . ': index must be an integer.');
                }
            }
        }

        return $eps;
    }


    /**
     * Find an endpoint of the given type, using a list of supported bindings as a way to prioritize.
     *
     * @param string $endpointType The endpoint type.
     * @param array  $bindings Sorted array of acceptable bindings.
     * @param mixed  $default The default value to return if no matching endpoint is found. If no default is provided,
     *     an exception will be thrown.
     *
     * @return array|null The default endpoint, or null if no acceptable endpoints are used.
     *
     * @throws \Exception If no supported endpoint is found.
     */
    public function getEndpointPrioritizedByBinding($endpointType, array $bindings, $default = self::REQUIRED_OPTION)
    {
        assert(is_string($endpointType));

        $endpoints = $this->getEndpoints($endpointType);

        foreach ($bindings as $binding) {
            foreach ($endpoints as $ep) {
                if ($ep['Binding'] === $binding) {
                    return $ep;
                }
            }
        }

        if ($default === self::REQUIRED_OPTION) {
            $loc = $this->location . '[' . var_export($endpointType, true) . ']:';
            throw new \Exception($loc . 'Could not find a supported ' . $endpointType . ' endpoint.');
        }

        return $default;
    }


    /**
     * Find the default endpoint of the given type.
     *
     * @param string $endpointType The endpoint type.
     * @param array  $bindings Array with acceptable bindings. Can be null if any binding is allowed.
     * @param mixed  $default The default value to return if no matching endpoint is found. If no default is provided,
     *     an exception will be thrown.
     *
     * @return mixed The default endpoint, or the $default parameter if no acceptable endpoints are used.
     *
     * @throws \Exception If no supported endpoint is found and no $default parameter is specified.
     */
    public function getDefaultEndpoint($endpointType, array $bindings = null, $default = self::REQUIRED_OPTION)
    {
        assert(is_string($endpointType));

        $endpoints = $this->getEndpoints($endpointType);

        $defaultEndpoint = Utils\Config\Metadata::getDefaultEndpoint($endpoints, $bindings);
        if ($defaultEndpoint !== null) {
            return $defaultEndpoint;
        }

        if ($default === self::REQUIRED_OPTION) {
            $loc = $this->location . '[' . var_export($endpointType, true) . ']:';
            throw new \Exception($loc . 'Could not find a supported ' . $endpointType . ' endpoint.');
        }

        return $default;
    }


    /**
     * Retrieve a string which may be localized into many languages.
     *
     * The default language returned is always 'en'.
     *
     * @param string $name The name of the option.
     * @param mixed  $default The default value. If no default is given, and the option isn't found, an exception will
     *     be thrown.
     *
     * @return mixed Associative array with language => string pairs, or the provided default value.
     *
     * @throws \Exception If the translation is not an array or a string, or its index or value are not strings.
     */
    public function getLocalizedString($name, $default = self::REQUIRED_OPTION)
    {
        assert(is_string($name));

        $ret = $this->getValue($name, $default);
        if ($ret === $default) {
            // the option wasn't found, or it matches the default value. In any case, return this value
            return $ret;
        }

        $loc = $this->location . '[' . var_export($name, true) . ']';

        if (is_string($ret)) {
            $ret = ['en' => $ret];
        }

        if (!is_array($ret)) {
            throw new \Exception($loc . ': Must be an array or a string.');
        }

        foreach ($ret as $k => $v) {
            if (!is_string($k)) {
                throw new \Exception($loc . ': Invalid language code: ' . var_export($k, true));
            }
            if (!is_string($v)) {
                throw new \Exception($loc . '[' . var_export($v, true) . ']: Must be a string.');
            }
        }

        return $ret;
    }


    /**
     * Get public key from metadata.
     *
     * @param string|null $use The purpose this key can be used for. (encryption or signing).
     * @param bool $required Whether the public key is required. If this is true, a
     *                       missing key will cause an exception. Default is false.
     * @param string $prefix The prefix which should be used when reading from the metadata
     *                       array. Defaults to ''.
     *
     * @return array Public key data, or empty array if no public key or was found.
     *
     * @throws \Exception If the certificate or public key cannot be loaded from a file.
     * @throws \SimpleSAML\Error\Exception If the file does not contain a valid PEM-encoded certificate, or there is no
     * certificate in the metadata.
     */
    public function getPublicKeys($use = null, $required = false, $prefix = '')
    {
        assert(is_bool($required));
        assert(is_string($prefix));

        if ($this->hasValue($prefix . 'keys')) {
            $ret = [];
            foreach ($this->getArray($prefix . 'keys') as $key) {
                if ($use !== null && isset($key[$use]) && !$key[$use]) {
                    continue;
                }
                if (isset($key['X509Certificate'])) {
                    // Strip whitespace from key
                    $key['X509Certificate'] = preg_replace('/\s+/', '', $key['X509Certificate']);
                }
                $ret[] = $key;
            }
            return $ret;
        } elseif ($this->hasValue($prefix . 'certData')) {
            $certData = $this->getString($prefix . 'certData');
            $certData = preg_replace('/\s+/', '', $certData);
            return [
                [
                    'encryption'      => true,
                    'signing'         => true,
                    'type'            => 'X509Certificate',
                    'X509Certificate' => $certData,
                ],
            ];
        } elseif ($this->hasValue($prefix . 'certificate')) {
            $file = $this->getString($prefix . 'certificate');
            $file = Utils\Config::getCertPath($file);
            $data = @file_get_contents($file);

            if ($data === false) {
                throw new \Exception(
                    $this->location . ': Unable to load certificate/public key from file "' . $file . '".'
                );
            }

            // extract certificate data (if this is a certificate)
            $pattern = '/^-----BEGIN CERTIFICATE-----([^-]*)^-----END CERTIFICATE-----/m';
            if (!preg_match($pattern, $data, $matches)) {
                throw new \SimpleSAML\Error\Exception(
                    $this->location . ': Could not find PEM encoded certificate in "' . $file . '".'
                );
            }
            $certData = preg_replace('/\s+/', '', $matches[1]);

            return [
                [
                    'encryption'      => true,
                    'signing'         => true,
                    'type'            => 'X509Certificate',
                    'X509Certificate' => $certData,
                ],
            ];
        } elseif ($required === true) {
            throw new \SimpleSAML\Error\Exception($this->location . ': Missing certificate in metadata.');
        } else {
            return [];
        }
    }

    /**
     * Clear any configuration information cached.
     * Allows for configuration files to be changed and reloaded during a given request. Most useful
     * when running phpunit tests and needing to alter config.php between test cases
     *
     * @return void
     */
    public static function clearInternalState()
    {
        self::$configDirs = [];
        self::$instance = [];
        self::$loadedConfigs = [];
    }
}

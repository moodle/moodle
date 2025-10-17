<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Components (core subsystems + plugins) related code.
 *
 * @package    core
 * @copyright  2013 Petr Skoda {@link http://skodak.org}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core;

use core\exception\coding_exception;
use core\output\theme_config;
use stdClass;
use ArrayIterator;
use DirectoryIterator;
use Exception;
use RegexIterator;

// Constants used in version.php files, these must exist when core_component executes.

// We make use of error_log as debugging is not always available.
// phpcs:disable moodle.PHP.ForbiddenFunctions.FoundWithAlternative
// We make use of empty if statements to make complex decisions clearer.
// phpcs:disable Generic.CodeAnalysis.EmptyStatement.DetectedIf

/** Software maturity level - internals can be tested using white box techniques. */
define('MATURITY_ALPHA', 50);
/** Software maturity level - feature complete, ready for preview and testing. */
define('MATURITY_BETA', 100);
/** Software maturity level - tested, will be released unless there are fatal bugs. */
define('MATURITY_RC', 150);
/** Software maturity level - ready for production deployment. */
define('MATURITY_STABLE', 200);
/** Any version - special value that can be used in $plugin->dependencies in version.php files. */
define('ANY_VERSION', 'any');

/**
 * Collection of components related methods.
 */
class component {
    /** @var array list of ignored directories in plugin type roots - watch out for auth/db exception */
    protected static $ignoreddirs = [
        'CVS' => true,
        '_vti_cnf' => true,
        'amd' => true,
        'classes' => true,
        'db' => true,
        'fonts' => true,
        'lang' => true,
        'pix' => true,
        'simpletest' => true,
        'templates' => true,
        'tests' => true,
        'yui' => true,
    ];
    /** @var array list plugin types that support subplugins, do not add more here unless absolutely necessary */
    protected static $supportsubplugins = ['mod', 'editor', 'tool', 'local'];

    /** @var object JSON source of the component data */
    protected static $componentsource = null;
    /** @var array cache of plugin types */
    protected static $plugintypes = null;
    /** @var array cache of plugin locations */
    protected static $plugins = null;
    /** @var array cache of core subsystems */
    protected static $subsystems = null;
    /** @var array subplugin type parents */
    protected static $parents = null;
    /** @var array subplugins */
    protected static $subplugins = null;
    /** @var array deprecated plugins  */
    protected static $deprecatedplugins = null;
    /** @var array deleted plugins */
    protected static $deletedplugins = null;
    /** @var array deprecated plugin types */
    protected static $deprecatedplugintypes = null;
    /** @var array deleted plugin types */
    protected static $deletedplugintypes = null;
    /** @var array deprecated sub plugins */
    protected static $deprecatedsubplugins = null;
    /** @var array deleted sub plugins */
    protected static $deletedsubplugins = null;
    /** @var array cache of core APIs */
    protected static $apis = null;
    /** @var array list of all known classes that can be autoloaded */
    protected static $classmap = null;
    /** @var array list of all classes that have been renamed to be autoloaded */
    protected static $classmaprenames = null;
    /** @var array list of some known files that can be included. */
    protected static $filemap = null;
    /** @var int|float core version. */
    protected static $version = null;
    /** @var array list of the files to map. */
    protected static $filestomap = ['lib.php', 'settings.php'];
    /** @var array associative array of PSR-0 namespaces and corresponding paths. */
    protected static $psr0namespaces = [
        'Mustache' => 'public/lib/mustache/src/Mustache',
    ];
    /** @var array<string|array<string>> associative array of PRS-4 namespaces and corresponding paths. */
    protected static $psr4namespaces = [
        \Aws::class => 'public/lib/aws-sdk/src',
        \CFPropertyList::class => 'public/lib/plist/src/CFPropertyList',
        \Complex::class => 'public/lib/phpspreadsheet/markbaker/complex/classes/src',
        \Composer\Pcre::class => 'public/lib/composer/pcre/src',
        \DI::class => 'public/lib/php-di/php-di/src',
        \GeoIp2::class => 'public/lib/maxmind/GeoIp2/src',
        \FastRoute::class => 'public/lib/nikic/fast-route/src',
        \Firebase\JWT::class => 'public/lib/php-jwt/src',
        \GuzzleHttp::class => 'public/lib/guzzlehttp/guzzle/src',
        \GuzzleHttp\Promise::class => 'public/lib/guzzlehttp/promises/src',
        \GuzzleHttp\Psr7::class => 'public/lib/guzzlehttp/psr7/src',
        \Html2Text::class => 'public/lib/html2text/src',
        \IMSGlobal\LTI::class => 'public/lib/ltiprovider/src',
        \Invoker::class => 'public/lib/php-di/invoker/src',
        \JmesPath::class => 'public/lib/jmespath/src',
        \Kevinrob\GuzzleCache::class => 'public/lib/guzzlehttp/kevinrob/guzzlecache/src',
        \Laravel\SerializableClosure::class => 'public/lib/laravel/serializable-closure/src',
        \lbuchs\WebAuthn::class => 'public/lib/webauthn/src',
        \libphonenumber::class => 'public/lib/giggsey/libphonenumber-for-php-lite/src',
        \Matrix::class => 'public/lib/phpspreadsheet/markbaker/matrix/classes/src',
        \MatthiasMullie\Minify::class => 'public/lib/minify/matthiasmullie-minify/src',
        \MatthiasMullie\PathConverter::class => 'public/lib/minify/matthiasmullie-pathconverter/src',
        \MaxMind\Db::class => 'public/lib/maxmind/MaxMind/src/MaxMind/Db',
        \Michelf::class => 'public/lib/markdown/Michelf',
        \MoodleHQ::class => [
            'public/lib/rtlcss/src/MoodleHQ',
        ],
        \OpenSpout::class => 'public/lib/openspout/src',
        \Packback\Lti1p3::class => 'public/lib/lti1p3/src',
        \PHPMailer\PHPMailer::class => 'public/lib/phpmailer/src',
        \PhpOffice\PhpSpreadsheet::class => 'public/lib/phpspreadsheet/phpspreadsheet/src/PhpSpreadsheet',
        \PhpXmlRpc::class => 'public/lib/phpxmlrpc/src',
        \Phpml::class => 'public/lib/mlbackend/php/phpml/src/Phpml',
        \Psr\Clock::class => 'public/lib/psr/clock/src',
        \Psr\Container::class => 'public/lib/psr/container/src',
        \Psr\EventDispatcher::class => 'public/lib/psr/event-dispatcher/src',
        \Psr\Http\Client::class => 'public/lib/psr/http-client/src',
        \Psr\Http\Message::class => [
            'public/lib/psr/http-factory/src',
            'public/lib/psr/http-message/src',
        ],
        \Psr\Http\Server::class => [
            "public/lib/psr/http-server-handler/src",
            "public/lib/psr/http-server-middleware/src",
        ],
        \Psr\Log::class => "public/lib/psr/log/src",
        \Psr\SimpleCache::class => 'public/lib/psr/simple-cache/src',
        \RedeyeVentures::class => 'public/lib/geopattern-php/src',
        \Sabberworm\CSS::class => 'public/lib/php-css-parser/src',
        \ScssPhp\ScssPhp::class => 'public/lib/scssphp/src',
        \SimplePie::class => 'public/lib/simplepie/src',
        \Slim::class => 'public/lib/slim/slim/Slim',
        \Spatie\Cloneable::class => 'public/lib/spatie/php-cloneable/src',
        \ZipStream::class => 'public/lib/zipstream/src',
    ];

    /**
     *  An array containing files which are normally in a package's composer/autoload.files section.
     *
     * PHP does not provide a mechanism for automatically including the files that methods are in.
     *
     * The Composer autoloader includes all files in this section of the composer.json file during the instantiation of the loader.
     *
     * @var array<string>
     */
    protected static $composerautoloadfiles = [
        'public/lib/aws-sdk/src/functions.php',
        'public/lib/guzzlehttp/guzzle/src/functions_include.php',
        'public/lib/jmespath/src/JmesPath.php',
        'public/lib/nikic/fast-route/src/functions.php',
        'public/lib/php-di/php-di/src/functions.php',
        'public/lib/ralouphie/getallheaders/src/getallheaders.php',
        'public/lib/symfony/deprecation-contracts/function.php',
    ];

    /**
     * Register the Moodle class autoloader.
     */
    public static function register_autoloader(): void {
        if (defined('COMPONENT_CLASSLOADER')) {
            spl_autoload_register(COMPONENT_CLASSLOADER);
        } else {
            spl_autoload_register([self::class, 'classloader']);
        }

        // Load any composer-driven autoload files.
        // This is intended to mimic the behaviour of the standard Composer Autoloader.
        foreach (static::$composerautoloadfiles as $file) {
            $path = dirname(__DIR__, 3) . '/' . $file;
            if (file_exists($path)) {
                require_once($path);
            }
        }
    }

    /**
     * Class loader for Frankenstyle named classes in standard locations.
     * Frankenstyle namespaces are supported.
     *
     * The expected location for core classes is:
     *    1/ core_xx_yy_zz ---> lib/classes/xx_yy_zz.php
     *    2/ \core\xx_yy_zz ---> lib/classes/xx_yy_zz.php
     *    3/ \core\xx\yy_zz ---> lib/classes/xx/yy_zz.php
     *
     * The expected location for plugin classes is:
     *    1/ mod_name_xx_yy_zz ---> mod/name/classes/xx_yy_zz.php
     *    2/ \mod_name\xx_yy_zz ---> mod/name/classes/xx_yy_zz.php
     *    3/ \mod_name\xx\yy_zz ---> mod/name/classes/xx/yy_zz.php
     *
     * @param string $classname
     */
    public static function classloader($classname) {
        self::init();

        if (isset(self::$classmap[$classname])) {
            // Global $CFG is expected in included scripts.
            global $CFG;
            // Function include would be faster, but for BC it is better to include only once.
            include_once(self::$classmap[$classname]);
            return;
        }
        if (isset(self::$classmaprenames[$classname]) && isset(self::$classmap[self::$classmaprenames[$classname]])) {
            $newclassname = self::$classmaprenames[$classname];
            $debugging = "Class '%s' has been renamed for the autoloader and is now deprecated. Please use '%s' instead.";
            debugging(sprintf($debugging, $classname, $newclassname), DEBUG_DEVELOPER);
            if (preg_match('#\\\null(\\\|$)#', $classname)) {
                throw new coding_exception("Cannot alias $classname to $newclassname");
            }
            class_alias($newclassname, $classname);
            return;
        }

        $file = self::psr_classloader($classname);
        // If the file is found, require it.
        if (!empty($file)) {
            require($file);
            return;
        }

        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
            // For unit tests we support classes in `\frankenstyle_component\tests\` to be loaded from
            // `path/to/frankenstyle/component/tests/classes` directory.
            // Note: We do *not* support the legacy `\frankenstyle_component_tests_style_classnames`.
            if ($component = self::get_component_from_classname($classname)) {
                $pathoptions = [
                    '/tests/classes' => "{$component}\\tests\\",
                    '/tests/behat' => "{$component}\\behat\\",
                ];
                foreach ($pathoptions as $path => $testnamespace) {
                    if (preg_match("#^" . preg_quote($testnamespace) . "#", $classname)) {
                        $path = self::get_component_directory($component) . $path;
                        $relativeclassname = str_replace(
                            $testnamespace,
                            '',
                            $classname,
                        );
                        $file = sprintf(
                            "%s/%s.php",
                            $path,
                            str_replace('\\', '/', $relativeclassname),
                        );
                        if (!empty($file) && file_exists($file)) {
                            require($file);
                            return;
                        }
                        break;
                    }
                }
            }
        }
    }

    /**
     * Return the path to a class from our defined PSR-0 or PSR-4 standard namespaces on
     * demand. Only returns paths to files that exist.
     *
     * Adapated from http://www.php-fig.org/psr/psr-4/examples/ and made PSR-0
     * compatible.
     *
     * @param string $class the name of the class.
     * @return string|bool The full path to the file defining the class. Or false if it could not be resolved or does not exist.
     */
    protected static function psr_classloader($class) {
        // Iterate through each PSR-4 namespace prefix.
        foreach (self::$psr4namespaces as $prefix => $paths) {
            if (!is_array($paths)) {
                $paths = [$paths];
            }
            foreach ($paths as $path) {
                $file = self::get_class_file($class, $prefix, $path, ['\\']);
                if (!empty($file) && file_exists($file)) {
                    return $file;
                }
            }
        }

        // Iterate through each PSR-0 namespace prefix.
        foreach (self::$psr0namespaces as $prefix => $path) {
            $file = self::get_class_file($class, $prefix, $path, ['\\', '_']);
            if (!empty($file) && file_exists($file)) {
                return $file;
            }
        }

        return false;
    }

    /**
     * Return the path to the class based on the given namespace prefix and path it corresponds to.
     *
     * Will return the path even if the file does not exist. Check the file esists before requiring.
     *
     * @param string $class the name of the class.
     * @param string $prefix The namespace prefix used to identify the base directory of the source files.
     * @param string $path The relative path to the base directory of the source files.
     * @param string[] $separators The characters that should be used for separating.
     * @return string|bool The full path to the file defining the class. Or false if it could not be resolved.
     */
    protected static function get_class_file($class, $prefix, $path, $separators) {
        global $CFG;

        // Does the class use the namespace prefix?
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            // No, move to the next prefix.
            return false;
        }

        $path = self::get_path($path);

        // Get the relative class name.
        $relativeclass = substr($class, $len);

        // Replace the namespace prefix with the base directory, replace namespace
        // separators with directory separators in the relative class name, append
        // with .php.
        $file = $path . str_replace($separators, '/', $relativeclass) . '.php';

        return $file;
    }

    /**
     * Initialise caches, always call before accessing self:: caches.
     */
    protected static function init() {
        global $CFG;

        // Init only once per request/CLI execution, we ignore changes done afterwards.
        if (isset(self::$plugintypes)) {
            return;
        }

        if (defined('IGNORE_COMPONENT_CACHE') && IGNORE_COMPONENT_CACHE) {
            self::fill_all_caches();
            return;
        }

        if (!empty($CFG->alternative_component_cache)) {
            // Hack for heavily clustered sites that want to manage component cache invalidation manually.
            $cachefile = $CFG->alternative_component_cache;

            if (file_exists($cachefile)) {
                if (CACHE_DISABLE_ALL) {
                    // Verify the cache state only on upgrade pages.
                    $content = self::get_cache_content();
                    if (sha1_file($cachefile) !== sha1($content)) {
                        die('Outdated component cache file defined in $CFG->alternative_component_cache, can not continue');
                    }
                    return;
                }
                $cache = [];
                include($cachefile);
                self::$plugintypes              = $cache['plugintypes'];
                self::$deprecatedplugintypes    = $cache['deprecatedplugintypes'];
                self::$deletedplugintypes       = $cache['deletedplugintypes'];
                self::$plugins                  = $cache['plugins'];
                self::$deprecatedplugins        = $cache['deprecatedplugins'];
                self::$deletedplugins           = $cache['deletedplugins'];
                self::$subsystems               = $cache['subsystems'];
                self::$parents                  = $cache['parents'];
                self::$subplugins               = $cache['subplugins'];
                self::$deprecatedsubplugins     = $cache['deprecatedsubplugins'];
                self::$deletedsubplugins        = $cache['deletedsubplugins'];
                self::$apis                     = $cache['apis'];
                self::$classmap                 = $cache['classmap'];
                self::$classmaprenames          = $cache['classmaprenames'];
                self::$filemap                  = $cache['filemap'];
                return;
            }

            if (!is_writable(dirname($cachefile))) {
                die(
                    'Can not create alternative component cache file defined in ' .
                    '$CFG->alternative_component_cache, can not continue'
                );
            }

            // Lets try to create the file, it might be in some writable directory or a local cache dir.
        } else {
            // Note: $CFG->cachedir MUST be shared by all servers in a cluster,
            // use $CFG->alternative_component_cache if you do not like it.
            $cachefile = "$CFG->cachedir/core_component.php";
        }

        if (!CACHE_DISABLE_ALL && !self::is_developer()) {
            // 1/ Use the cache only outside of install and upgrade.
            // 2/ Let developers add/remove classes in developer mode.
            if (is_readable($cachefile)) {
                $cache = false;
                include($cachefile);
                if (is_array($cache) && self::is_cache_valid($cache)) {
                    // The cache looks ok, let's use it.
                    self::$plugintypes              = $cache['plugintypes'];
                    self::$deprecatedplugintypes    = $cache['deprecatedplugintypes'];
                    self::$deletedplugintypes       = $cache['deletedplugintypes'];
                    self::$plugins                  = $cache['plugins'];
                    self::$deprecatedplugins        = $cache['deprecatedplugins'];
                    self::$deletedplugins           = $cache['deletedplugins'];
                    self::$subsystems               = $cache['subsystems'];
                    self::$parents                  = $cache['parents'];
                    self::$subplugins               = $cache['subplugins'];
                    self::$deprecatedsubplugins     = $cache['deprecatedsubplugins'];
                    self::$deletedsubplugins        = $cache['deletedsubplugins'];
                    self::$apis                     = $cache['apis'];
                    self::$classmap                 = $cache['classmap'];
                    self::$classmaprenames          = $cache['classmaprenames'];
                    self::$filemap                  = $cache['filemap'];
                    return;
                }
                // Note: we do not verify $CFG->admin here intentionally,
                // they must visit admin/index.php after any change.
            }
        }

        if (!isset(self::$plugintypes)) {
            // This needs to be atomic and self-fixing as much as possible.

            $content = self::get_cache_content();
            if (file_exists($cachefile)) {
                if (sha1_file($cachefile) === sha1($content)) {
                    return;
                }
                // Stale cache detected!
                unlink($cachefile);
            }

            // Permissions might not be setup properly in installers.
            $dirpermissions = !isset($CFG->directorypermissions) ? 02777 : $CFG->directorypermissions;
            $filepermissions = !isset($CFG->filepermissions) ? ($dirpermissions & 0666) : $CFG->filepermissions;

            clearstatcache();
            $cachedir = dirname($cachefile);
            if (!is_dir($cachedir)) {
                mkdir($cachedir, $dirpermissions, true);
            }

            if ($fp = @fopen($cachefile . '.tmp', 'xb')) {
                fwrite($fp, $content);
                fclose($fp);
                @rename($cachefile . '.tmp', $cachefile);
                @chmod($cachefile, $filepermissions);
            }
            @unlink($cachefile . '.tmp'); // Just in case anything fails (race condition).
            self::invalidate_opcode_php_cache($cachefile);
        }
    }

    /**
     * Reset the initialisation of the component utility.
     *
     * Note: It should not be necessary to call this in regular code.
     * Please only use it where strictly required.
     */
    public static function reset(
        bool $fullreset = false,
    ): void {
        // The autoloader will re-initialise if plugintypes is null.
        self::$plugintypes = null;

        // Reset all caches to ensure they are reloaded.
        self::$plugins = null;
        self::$subsystems = null;
        self::$parents = null;
        self::$subplugins = null;
        self::$deprecatedplugins = null;
        self::$deletedplugins = null;
        self::$deprecatedplugintypes = null;
        self::$deletedplugintypes = null;
        self::$deprecatedsubplugins = null;
        self::$deletedsubplugins = null;
        self::$apis = null;
        self::$classmap = null;
        self::$classmaprenames = null;
        self::$filemap = null;

        if ($fullreset) {
            self::$componentsource = null;
            self::$version = null;
            self::$supportsubplugins = ['mod', 'editor', 'tool', 'local'];
        }
    }

    /**
     * Check whether the cache content in the supplied cache is valid.
     *
     * @param array $cache The content being loaded
     * @return bool Whether it is valid
     */
    protected static function is_cache_valid(array $cache): bool {
        global $CFG;

        if (!isset($cache['version'])) {
            // Something is very wrong.
            return false;
        }

        if ((float) $cache['version'] !== (float) self::fetch_core_version()) {
            // Outdated cache. We trigger an error log to track an eventual repetitive failure of float comparison.
            error_log('Resetting core_component cache after core upgrade to version ' . self::fetch_core_version());
            return false;
        }

        if ($cache['plugintypes']['mod'] !== "$CFG->dirroot/mod") {
            // phpcs:ignore moodle.Commenting.InlineComment.NotCapital
            // $CFG->dirroot was changed.
            return false;
        }

        // Check for key classes which block access to the upgrade in some way.
        // Note: This list should be kept _extremely_ minimal and generally
        // when adding a newly discovered classes older ones should be removed.
        // Always keep moodle_exception in place.
        $keyclasses = [
            \core\exception\moodle_exception::class,
            \core\navigation\navbar::class,
            \core\navigation\navigation_node::class,
        ];
        foreach ($keyclasses as $classname) {
            if (!array_key_exists($classname, $cache['classmap'])) {
                // The cache is missing some key classes. This is likely before the upgrade has run.
                error_log(
                    "The '{$classname}' class was not found in the component class cache. Resetting the classmap.",
                );
                return false;
            }
        }

        return true;
    }

    /**
     * Are we in developer debug mode?
     *
     * Note: You need to set "$CFG->debug = (E_ALL);" in config.php,
     *       the reason is we need to use this before we setup DB connection or caches for CFG.
     *
     * @return bool
     */
    protected static function is_developer() {
        global $CFG;

        // Note we can not rely on $CFG->debug here because DB is not initialised yet.
        if (isset($CFG->config_php_settings['debug'])) {
            $debug = (int)$CFG->config_php_settings['debug'];
        } else {
            return false;
        }

        if ($debug & E_ALL) {
            return true;
        }

        return false;
    }

    /**
     * Create cache file content.
     *
     * @private this is intended for $CFG->alternative_component_cache only.
     *
     * @return string
     */
    public static function get_cache_content() {
        if (!isset(self::$plugintypes)) {
            self::fill_all_caches();
        }

        $cache = [
            'subsystems'            => self::$subsystems,
            'plugintypes'           => self::$plugintypes,
            'deprecatedplugintypes' => self::$deprecatedplugintypes,
            'deletedplugintypes'    => self::$deletedplugintypes,
            'plugins'               => self::$plugins,
            'deprecatedplugins'     => self::$deprecatedplugins,
            'deletedplugins'        => self::$deletedplugins,
            'subplugins'            => self::$subplugins,
            'deprecatedsubplugins'  => self::$deprecatedsubplugins,
            'deletedsubplugins'     => self::$deletedsubplugins,
            'parents'               => self::$parents,
            'apis'                  => self::$apis,
            'classmap'              => self::$classmap,
            'classmaprenames'       => self::$classmaprenames,
            'filemap'               => self::$filemap,
            'version'               => self::$version,
        ];

        return '<?php
$cache = ' . var_export($cache, true) . ';
';
    }

    /**
     * Fill all caches.
     */
    protected static function fill_all_caches() {
        self::$subsystems = self::fetch_subsystems();

        [
            'plugintypes' => self::$plugintypes,
            'parents' => self::$parents,
            'subplugins' => self::$subplugins,
            'deprecatedplugintypes' => self::$deprecatedplugintypes,
            'deletedplugintypes' => self::$deletedplugintypes,
            'deprecatedsubplugins' => self::$deprecatedsubplugins,
            'deletedsubplugin' => self::$deletedsubplugins
        ] = self::fetch_plugintypes();

        self::$plugins = [];
        foreach (self::$plugintypes as $type => $fulldir) {
            self::$plugins[$type] = self::fetch_plugins($type, $fulldir);
        }

        self::$deprecatedplugins = [];
        foreach (self::$deprecatedplugintypes as $type => $fulldir) {
            self::$deprecatedplugins[$type] = self::fetch_plugins($type, $fulldir);
        }

        self::$deletedplugins = [];
        foreach (self::$deletedplugintypes as $type => $fulldir) {
            self::$deletedplugins[$type] = self::fetch_plugins($type, $fulldir);
        }

        self::$apis = self::fetch_apis();

        self::fill_classmap_cache();
        self::fill_classmap_renames_cache();
        self::fill_filemap_cache();
        self::fetch_core_version();
    }

    /**
     * Get the core version.
     *
     * In order for this to work properly, opcache should be reset beforehand.
     *
     * @return float core version.
     */
    protected static function fetch_core_version() {
        global $CFG;
        if (self::$version === null) {
            $version = null; // Prevent IDE complaints.
            require($CFG->dirroot . '/version.php');
            self::$version = $version;
        }
        return self::$version;
    }

    /**
     * Returns list of core subsystems.
     * @return array
     */
    protected static function fetch_subsystems() {
        global $CFG;

        // NOTE: Any additions here must be verified to not collide with existing add-on modules and subplugins!!!
        $info = [];
        foreach (self::fetch_component_source('subsystems') as $subsystem => $path) {
            // Replace admin/ directory with the config setting.
            if ($CFG->admin !== 'admin') {
                if ($path === 'admin') {
                    $path = $CFG->admin;
                }
                if (strpos($path, 'admin/') === 0) {
                    $path = $CFG->admin . substr($path, 5);
                }
            }

            $info[$subsystem] = empty($path) ? null : self::get_path($path);
        }

        return $info;
    }

    /**
     * Returns list of core APIs.
     * @return stdClass[]
     */
    protected static function fetch_apis() {
        return (array) json_decode(file_get_contents(__DIR__ . '/../apis.json'));
    }

    /**
     * Returns list of known plugin types.
     * @return array
     */
    protected static function fetch_plugintypes() {
        global $CFG;

        // Top level plugin types.
        $plugintypesmap = [
            'plugintypes' => [],
            'deprecatedplugintypes' => [],
            'deletedplugintypes' => [],
        ];

        $subplugintypesmap = [
            'plugintypes' => [],
            'deprecatedplugintypes' => [],
            'deletedplugintypes' => [],
        ];

        $parents = [];

        foreach ($plugintypesmap as $sourcekey => $typesarr) {
            /** @var string $plugintype */
            foreach (self::fetch_component_source($sourcekey) as $plugintype => $path) {
                // Replace admin/ with the config setting.
                if ($CFG->admin !== 'admin' && strpos($path, 'admin/') === 0) {
                    $path = $CFG->admin . substr($path, 5);
                }
                $plugintypesmap[$sourcekey][$plugintype] = self::get_path($path);
            }
        }

        // Prevent deprecation of plugin types supporting subplugins (those in self::$supportsubplugins).
        foreach (['deprecatedplugintypes', 'deletedplugintypes'] as $key) {
            $illegaltypes = array_intersect(self::$supportsubplugins, array_keys($plugintypesmap[$key]));
            if (!empty($illegaltypes)) {
                debugging("Deprecation of a plugin type which supports subplugins is not supported. These plugin types will ".
                    "continue to be treated as active.", DEBUG_DEVELOPER);
                foreach ($illegaltypes as $plugintype) {
                    $plugintypesmap['plugintypes'][$plugintype] = $plugintypesmap[$key][$plugintype];
                    unset($plugintypesmap[$key][$plugintype]);
                }
            }
        }

        if (!empty($CFG->themedir) && is_dir($CFG->themedir)) {
            $plugintypesmap['plugintypes']['theme'] = $CFG->themedir;
        } else {
            $plugintypesmap['plugintypes']['theme'] = $CFG->dirroot . '/theme';
        }

        foreach (self::$supportsubplugins as $type) {
            if ($type === 'local') {
                // Local subplugins must be after local plugins.
                continue;
            }

            $plugins = self::fetch_plugins($type, $plugintypesmap['plugintypes'][$type]);
            foreach ($plugins as $plugin => $fulldir) {
                $allsubtypes = self::fetch_subtypes($fulldir);
                $subplugintypesdata = [
                    'plugintypes' => $allsubtypes['plugintypes'] ?? [],
                    'deprecatedplugintypes' => $allsubtypes['deprecatedplugintypes'] ?? [],
                    'deletedplugintypes' => $allsubtypes['deletedplugintypes'] ?? [],
                ];

                if (!$subplugintypesdata['plugintypes'] && !$subplugintypesdata['deprecatedplugintypes']
                        && !$subplugintypesdata['deletedplugintypes']) {
                    continue;
                }
                $subplugintypesmap['plugintypes'][$type . '_' . $plugin] = [];
                $subplugintypesmap['deprecatedplugintypes'][$type . '_' . $plugin] = [];
                $subplugintypesmap['deletedplugintypes'][$type . '_' . $plugin] = [];

                foreach ($subplugintypesdata as $key => $subplugintypes) {
                    foreach ($subplugintypes as $subtype => $subdir) {
                        if (isset($plugintypesmap['plugintypes'][$subtype])
                                || isset($plugintypesmap['deprecatedplugintypes'][$subtype])
                                || isset($plugintypesmap['deletedplugintypes'][$subtype])) {
                            error_log("Invalid subtype '$subtype', duplicate detected.");
                            continue;
                        }
                        $plugintypesmap[$key][$subtype] = $subdir;
                        $parents[$subtype] = $type . '_' . $plugin;
                        $subplugintypesmap[$key][$type . '_' . $plugin][$subtype] = array_keys(
                            self::fetch_plugins($subtype, $subdir)
                        );
                    }
                }
            }
        }
        // Local is always last!
        $plugintypesmap['plugintypes']['local'] = $CFG->dirroot . '/local';

        if (in_array('local', self::$supportsubplugins)) {
            $type = 'local';
            $plugins = self::fetch_plugins($type, $plugintypesmap['plugintypes'][$type]);
            foreach ($plugins as $plugin => $fulldir) {
                $allsubtypes = self::fetch_subtypes($fulldir);
                $subplugintypesdata = [
                    'plugintypes' => $allsubtypes['plugintypes'] ?? [],
                    'deprecatedplugintypes' => $allsubtypes['deprecatedplugintypes'] ?? [],
                    'deletedplugintypes' => $allsubtypes['deletedplugintypes'] ?? [],
                ];
                if (!$subplugintypesdata['plugintypes'] && !$subplugintypesdata['deprecatedplugintypes']
                        && !$subplugintypesdata['deletedplugintypes']) {
                    continue;
                }
                $subplugintypesmap['plugintypes'][$type . '_' . $plugin] = [];
                $subplugintypesmap['deprecatedplugintypes'][$type . '_' . $plugin] = [];
                $subplugintypesmap['deletedplugintypes'][$type . '_' . $plugin] = [];

                foreach ($subplugintypesdata as $key => $subplugintypes) {
                    foreach ($subplugintypes as $subtype => $subdir) {
                        if (isset($plugintypesmap['plugintypes'][$subtype])
                                || isset($plugintypesmap['deprecatedplugintypes'][$subtype])
                                || isset($plugintypesmap['deletedplugintypes'][$subtype])) {
                            error_log("Invalid subtype '$subtype', duplicate detected.");
                            continue;
                        }
                        $plugintypesmap[$key][$subtype] = $subdir;
                        $parents[$subtype] = $type . '_' . $plugin;
                        $subplugintypesmap[$key][$type . '_' . $plugin][$subtype] = array_keys(
                            self::fetch_plugins($subtype, $subdir)
                        );
                    }
                }
            }
        }

        return [
            'plugintypes' => $plugintypesmap['plugintypes'],
            'parents' => $parents,
            'subplugins' => $subplugintypesmap['plugintypes'],
            'deprecatedplugintypes' => $plugintypesmap['deprecatedplugintypes'],
            'deletedplugintypes' => $plugintypesmap['deletedplugintypes'],
            'deprecatedsubplugins' => $subplugintypesmap['deprecatedplugintypes'],
            'deletedsubplugin' => $subplugintypesmap['deletedplugintypes'],
        ];
    }

    /**
     * Returns the component source content as loaded from /lib/components.json.
     *
     * @return array
     */
    protected static function fetch_component_source(string $key) {
        if (null === self::$componentsource) {
            self::$componentsource = (array) json_decode(file_get_contents(dirname(__DIR__, 3) . '/lib/components.json'));
        }

        return !empty(self::$componentsource[$key]) ? (array) self::$componentsource[$key] : [];
    }

    /**
     * Returns list of subtypes.
     * @param string $ownerdir
     * @return array
     */
    protected static function fetch_subtypes($ownerdir) {
        global $CFG;

        $types = [];
        $subplugins = [];
        $root = self::get_path();
        if (str_contains($ownerdir, $root)) {
            $plugindir = substr($ownerdir, strlen($root) + 1);
        } else {
            $realownerdir = realpath($ownerdir);
            $realroot = realpath(dirname(__DIR__, 2));
            $plugindir = substr($realownerdir, strlen($realroot) + 1);
        }

        $subtypesregister = [
            'plugintypes' => [],
            'deprecatedplugintypes' => [],
            'deletedplugintypes' => [],
        ];
        if (file_exists("$ownerdir/db/subplugins.json")) {
            $subpluginpathformatter = fn (string $value): string => "{$plugindir}/{$value}";
            $subpluginsjson = json_decode(file_get_contents("$ownerdir/db/subplugins.json"));
            if (json_last_error() === JSON_ERROR_NONE) {
                $subplugins = [];
                if (!empty($subpluginsjson->subplugintypes)) {
                    // If the newer subplugintypes is defined, use it.
                    // The value here is relative to the plugin's owner directory.
                    $subplugins = array_map($subpluginpathformatter, (array) $subpluginsjson->subplugintypes);
                } else if (!empty($subpluginsjson->plugintypes)) {
                    error_log(
                        "No subplugintypes defined in $ownerdir/db/subplugins.json. " .
                        "Falling back to deprecated plugintypes value. " .
                        "See MDL-83705 for further information.",
                    );
                    $subplugins = (array) $subpluginsjson->plugintypes;
                    array_walk(
                        $subplugins,
                        fn (string &$path): string  => $path = str_starts_with($path, 'public/') ? $path : "public/{$path}",
                    );
                } else if (empty($subpluginjson->deprecatedplugintypes) && empty($subpluginsjson->deletedplugintypes)) {
                    error_log("No plugintypes defined in $ownerdir/db/subplugins.json");
                }

                $subtypesregister['plugintypes'] = $subplugins;

                // The deprecated and deleted subplugintypes are optional and are always relative to the plugin's root directory.
                $subtypesregister['deprecatedplugintypes'] = array_map(
                    $subpluginpathformatter,
                    (array) ($subpluginsjson->deprecatedsubplugintypes ?? []),
                );
                $subtypesregister['deletedplugintypes'] = array_map(
                    $subpluginpathformatter,
                    (array) ($subpluginsjson->deletedsubplugintypes ?? []),
                );
            } else {
                $jsonerror = json_last_error_msg();
                error_log("$ownerdir/db/subplugins.json is invalid ($jsonerror)");
            }

            if (function_exists('debugging') && debugging()) {
                if (property_exists($subpluginsjson, 'subplugintypes') && property_exists($subpluginsjson, 'plugintypes')) {
                    $subplugintypes = (array) $subpluginsjson->subplugintypes;
                    $plugintypes = (array) $subpluginsjson->plugintypes;
                    array_walk(
                        $plugintypes,
                        fn (string &$path): string  => $path = str_starts_with($path, 'public/') ? $path : "public/{$path}",
                    );
                    if (count($subplugintypes) !== count(($plugintypes))) {
                        error_log("Subplugintypes and plugintypes are not in sync in $ownerdir/db/subplugins.json");
                    }
                    foreach ($subplugintypes as $type => $path) {
                        if (!isset($plugintypes[$type])) {
                            error_log("Subplugintypes and plugintypes are not in sync for '$type' in $ownerdir/db/subplugins.json");

                            continue;
                        }

                        if ($plugintypes[$type] !== $subplugins[$type]) {
                            error_log("Subplugintypes and plugintypes are not in sync for '$type' in $ownerdir/db/subplugins.json");
                        }
                    }
                }
            }
        } else if (file_exists("$ownerdir/db/subplugins.php")) {
            throw new coding_exception(
                'Use of subplugins.php has been deprecated and is no longer supported. ' .
                "Please update your '$ownerdir' plugin to provide a subplugins.json file instead.",
            );
        }

        foreach ($subtypesregister as $key => $subtypes) {
            foreach ($subtypes as $subtype => $dir) {
                if (!preg_match('/^[a-z][a-z0-9]*$/', $subtype)) {
                    error_log("Invalid subtype '$subtype'' detected in '$ownerdir', invalid characters present.");
                    continue;
                }
                if (isset(self::$subsystems[$subtype])) {
                    error_log("Invalid subtype '$subtype'' detected in '$ownerdir', duplicates core subsystem.");
                    continue;
                }
                if ($CFG->admin !== 'admin' && strpos($dir, 'admin/') === 0) {
                    $dir = preg_replace('|^admin/|', "$CFG->admin/", $dir);
                }
                if (!is_dir(self::get_path($dir))) {
                    error_log("Invalid subtype directory '$dir' detected in '$ownerdir'.");
                    continue;
                }
                $types[$key][$subtype] = self::get_path($dir);
            }
        }

        return $types;
    }

    /**
     * Returns list of plugins of given type in given directory.
     * @param string $plugintype
     * @param string $fulldir
     * @return array
     */
    protected static function fetch_plugins($plugintype, $fulldir) {
        global $CFG;

        $fulldirs = (array)$fulldir;
        if ($plugintype === 'theme') {
            if (realpath($fulldir) !== realpath($CFG->dirroot . '/theme')) {
                // Include themes in standard location too.
                array_unshift($fulldirs, $CFG->dirroot . '/theme');
            }
        }

        $result = [];

        foreach ($fulldirs as $fulldir) {
            if (!is_dir($fulldir)) {
                continue;
            }
            $items = new DirectoryIterator($fulldir);
            foreach ($items as $item) {
                if ($item->isDot() || !$item->isDir()) {
                    continue;
                }
                $pluginname = $item->getFilename();
                if ($plugintype === 'auth' && $pluginname === 'db') {
                    // Special exception for this wrong plugin name.
                } else if (isset(self::$ignoreddirs[$pluginname])) {
                    continue;
                }
                if (!self::is_valid_plugin_name($plugintype, $pluginname)) {
                    // Always ignore plugins with problematic names here.
                    continue;
                }
                $result[$pluginname] = $fulldir . '/' . $pluginname;
                unset($item);
            }
            unset($items);
        }

        ksort($result);
        return $result;
    }

    /**
     * Find all classes that can be autoloaded including frankenstyle namespaces.
     */
    protected static function fill_classmap_cache() {
        global $CFG;

        self::$classmap = [];

        self::load_classes('core', "$CFG->dirroot/lib/classes");
        self::load_legacy_classes($CFG->libdir, true);

        foreach (self::$subsystems as $subsystem => $fulldir) {
            if (!$fulldir) {
                continue;
            }
            self::load_classes('core_' . $subsystem, "$fulldir/classes");
        }

        foreach (self::$plugins as $plugintype => $plugins) {
            foreach ($plugins as $pluginname => $fulldir) {
                self::load_classes($plugintype . '_' . $pluginname, "$fulldir/classes");
                self::load_legacy_classes($fulldir);
            }
        }

        // Include deprecated plugins in the classmap, to facilitate migration code which uses existing plugin classes.
        foreach (self::$deprecatedplugins as $plugintype => $plugins) {
            foreach ($plugins as $pluginname => $fulldir) {
                self::load_classes($plugintype . '_' . $pluginname, "$fulldir/classes");
            }
        }

        ksort(self::$classmap);
    }

    /**
     * Fills up the cache defining what plugins have certain files.
     *
     * @see self::get_plugin_list_with_file
     * @return void
     */
    protected static function fill_filemap_cache() {
        global $CFG;

        self::$filemap = [];

        foreach (self::$filestomap as $file) {
            if (!isset(self::$filemap[$file])) {
                self::$filemap[$file] = [];
            }
            foreach (self::$plugins as $plugintype => $plugins) {
                if (!isset(self::$filemap[$file][$plugintype])) {
                    self::$filemap[$file][$plugintype] = [];
                }
                foreach ($plugins as $pluginname => $fulldir) {
                    if (file_exists("$fulldir/$file")) {
                        self::$filemap[$file][$plugintype][$pluginname] = "$fulldir/$file";
                    }
                }
            }
        }
    }

    /**
     * Find classes in directory and recurse to subdirs.
     * @param string $component
     * @param string $fulldir
     * @param string $namespace
     */
    protected static function load_classes($component, $fulldir, $namespace = '') {
        if (!is_dir($fulldir)) {
            return;
        }

        if (!is_readable($fulldir)) {
            // TODO: MDL-51711 We should generate some diagnostic debugging information in this case
            // because its pretty likely to lead to a missing class error further down the line.
            // But our early setup code can't handle errors this early at the moment.
            return;
        }

        $items = new DirectoryIterator($fulldir);
        foreach ($items as $item) {
            if ($item->isDot()) {
                continue;
            }
            if ($item->isDir()) {
                $dirname = $item->getFilename();
                self::load_classes($component, "$fulldir/$dirname", $namespace . '\\' . $dirname);
                continue;
            }

            $filename = $item->getFilename();
            $classname = preg_replace('/\.php$/', '', $filename);

            if ($filename === $classname) {
                // Not a php file.
                continue;
            }
            if ($namespace === '') {
                // Legacy long frankenstyle class name.
                self::$classmap[$component . '_' . $classname] = "$fulldir/$filename";
            }
            // New namespaced classes.
            self::$classmap[$component . $namespace . '\\' . $classname] = "$fulldir/$filename";
        }
        unset($item);
        unset($items);
    }


    /**
     * List all core subsystems and their location
     *
     * This is a list of components that are part of the core and their
     * language strings are defined in /lang/en/<<subsystem>>.php. If a given
     * plugin is not listed here and it does not have proper plugintype prefix,
     * then it is considered as course activity module.
     *
     * The location is absolute file path to dir. NULL means there is no special
     * directory for this subsystem. If the location is set, the subsystem's
     * renderer.php is expected to be there.
     *
     * @return array of (string)name => (string|null)full dir location
     */
    public static function get_core_subsystems() {
        self::init();
        return self::$subsystems;
    }

    /**
     * List all core APIs and their attributes.
     *
     * This is a list of all the existing / allowed APIs in moodle, each one with the
     * following attributes:
     *   - component: the component, usually a subsystem or core, the API belongs to.
     *   - allowedlevel2: if the API is allowed as level2 namespace or no.
     *   - allowedspread: if the API can spread out from its component or no.
     *
     * @return stdClass[] array of APIs (as keys) with their attributes as object instances.
     */
    public static function get_core_apis() {
        self::init();
        return self::$apis;
    }

    /**
     * Get list of available plugin types together with their location.
     *
     * @return array as (string)plugintype => (string)fulldir
     */
    public static function get_plugin_types() {
        self::init();
        return self::$plugintypes;
    }

    /**
     * Get a list of deprecated plugin types and their locations.
     *
     * @return array as (string)plugintype => (string)fulldir
     */
    public static function get_deprecated_plugin_types(): array {
        self::init();
        return self::$deprecatedplugintypes;
    }

    /**
     * Get a list of all deleted plugin types and their locations.
     *
     * @return array as (string)plugintype => (string)fulldir
     */
    public static function get_deleted_plugin_types(): array {
        self::init();
        return self::$deletedplugintypes;
    }

    /**
     * Gets list of all plugin types, comprising available plugin types as well as any plugin types currently in deprecation.
     *
     * @return array as (string)plugintype => (string)fulldir
     */
    public static function get_all_plugin_types(): array {
        self::init();
        return array_merge(self::$plugintypes, self::$deprecatedplugintypes, self::$deletedplugintypes);
    }

    /**
     * Is the plugintype deprecated.
     *
     * @param string $plugintype
     * @return bool true if deprecated, false otherwise.
     */
    public static function is_deprecated_plugin_type(string $plugintype): bool {
        self::init();
        return array_key_exists($plugintype, self::$deprecatedplugintypes);
    }

    /**
     * Is the plugintype deleted.
     *
     * @param string $plugintype
     * @return bool true if deleted, false otherwise.
     */
    public static function is_deleted_plugin_type(string $plugintype): bool {
        self::init();
        return array_key_exists($plugintype, self::$deletedplugintypes);
    }

    /**
     * Is the plugintype in deprecation.
     *
     * @param string $plugintype
     * @return bool true if in either phase 1 (deprecated) or phase 2 (deleted) or deprecation.
     */
    public static function is_plugintype_in_deprecation(string $plugintype): bool {
        self::init();
        return array_key_exists($plugintype, array_merge(self::$deprecatedplugintypes, self::$deletedplugintypes));
    }

    /**
     * Get list of plugins of given type.
     *
     * @param string $plugintype
     * @return array as (string)pluginname => (string)fulldir
     */
    public static function get_plugin_list($plugintype) {
        self::init();

        if (!isset(self::$plugins[$plugintype])) {
            return [];
        }
        return self::$plugins[$plugintype];
    }

    /**
     * Get list of deprecated plugins of a given type.
     *
     * @param string $plugintype
     * @return array as (string)pluginname => (string)fulldir
     */
    public static function get_deprecated_plugin_list($plugintype): array {
        self::init();
        return self::$deprecatedplugins[$plugintype] ?? [];
    }

    /**
     * Get list of deleted plugins of a given type.
     *
     * @param string $plugintype
     * @return array as (string)pluginname => (string)fulldir
     */
    public static function get_deleted_plugin_list($plugintype): array {
        self::init();
        return self::$deletedplugins[$plugintype] ?? [];
    }

    /**
     * Get list of all plugins of a given type, comprising all available plugins as well as any plugins in deprecation.
     *
     * @param string $plugintype
     * @return array as (string)pluginname => (string)fulldir
     */
    public static function get_all_plugins_list(string $plugintype): array {
        self::init();
        return array_merge(
            self::$plugins[$plugintype] ?? [],
            self::$deprecatedplugins[$plugintype] ?? [],
            self::$deletedplugins[$plugintype] ?? []
        );
    }

    /**
     * Get a list of all the plugins of a given type that define a certain class
     * in a certain file. The plugin component names and class names are returned.
     *
     * @param string $plugintype the type of plugin, e.g. 'mod' or 'report'.
     * @param string $class the part of the name of the class after the
     *      frankenstyle prefix. e.g 'thing' if you are looking for classes with
     *      names like report_courselist_thing. If you are looking for classes with
     *      the same name as the plugin name (e.g. qtype_multichoice) then pass ''.
     *      Frankenstyle namespaces are also supported.
     * @param string $file the name of file within the plugin that defines the class.
     * @return array with frankenstyle plugin names as keys (e.g. 'report_courselist', 'mod_forum')
     *      and the class names as values (e.g. 'report_courselist_thing', 'qtype_multichoice').
     */
    public static function get_plugin_list_with_class($plugintype, $class, $file = null) {
        global $CFG; // Necessary in case it is referenced by included PHP scripts.

        if ($class) {
            $suffix = '_' . $class;
        } else {
            $suffix = '';
        }

        $pluginclasses = [];
        $plugins = self::get_plugin_list($plugintype);
        foreach ($plugins as $plugin => $fulldir) {
            // Try class in frankenstyle namespace.
            if ($class) {
                $classname = '\\' . $plugintype . '_' . $plugin . '\\' . $class;
                if (class_exists($classname, true)) {
                    $pluginclasses[$plugintype . '_' . $plugin] = $classname;
                    continue;
                }
            }

            // Try autoloading of class with frankenstyle prefix.
            $classname = $plugintype . '_' . $plugin . $suffix;
            if (class_exists($classname, true)) {
                $pluginclasses[$plugintype . '_' . $plugin] = $classname;
                continue;
            }

            // Fall back to old file location and class name.
            if ($file && file_exists("$fulldir/$file")) {
                include_once("$fulldir/$file");
                if (class_exists($classname, false)) {
                    $pluginclasses[$plugintype . '_' . $plugin] = $classname;
                    continue;
                }
            }
        }

        return $pluginclasses;
    }

    /**
     * Get a list of all the plugins of a given type that contain a particular file.
     *
     * @param string $plugintype the type of plugin, e.g. 'mod' or 'report'.
     * @param string $file the name of file that must be present in the plugin.
     *                     (e.g. 'view.php', 'db/install.xml').
     * @param bool $include if true (default false), the file will be include_once-ed if found.
     * @return array with plugin name as keys (e.g. 'forum', 'courselist') and the path
     *               to the file relative to dirroot as value (e.g. "$CFG->dirroot/mod/forum/view.php").
     */
    public static function get_plugin_list_with_file($plugintype, $file, $include = false) {
        global $CFG; // Necessary in case it is referenced by included PHP scripts.
        $pluginfiles = [];
        self::init();

        if (isset(self::$filemap[$file])) {
            // If the file was supposed to be mapped, then it should have been set in the array.
            if (isset(self::$filemap[$file][$plugintype])) {
                $pluginfiles = self::$filemap[$file][$plugintype];
            }
        } else {
            // Old-style search for non-cached files.
            $plugins = self::get_plugin_list($plugintype);
            foreach ($plugins as $plugin => $fulldir) {
                $path = $fulldir . '/' . $file;
                if (file_exists($path)) {
                    $pluginfiles[$plugin] = $path;
                }
            }
        }

        if ($include) {
            foreach ($pluginfiles as $path) {
                include_once($path);
            }
        }

        return $pluginfiles;
    }

    /**
     * Returns all classes in a component matching the provided namespace.
     *
     * It checks that the class exists.
     *
     * e.g. get_component_classes_in_namespace('mod_forum', 'event')
     *
     * @param string|null $component A valid moodle component (frankenstyle) or null if searching all components
     * @param string $namespace Namespace from the component name or empty string if all $component classes.
     * @return array The full class name as key and the class path as value, empty array if $component is `null`
     * and $namespace is empty.
     */
    public static function get_component_classes_in_namespace($component = null, $namespace = '') {

        $classes = [];
        self::init();

        // Only look for components if a component name is set or a namespace is set.
        if (isset($component) || !empty($namespace)) {
            // If a component parameter value is set we only want to look in that component.
            // Otherwise we want to check all components.
            $component = (isset($component)) ? self::normalize_componentname($component) : '\w+';
            if ($namespace) {
                // We will add them later.
                $namespace = trim($namespace, '\\');

                // We need add double backslashes as it is how classes are stored into self::$classmap.
                $namespace = implode('\\\\', explode('\\', $namespace));
                $namespace = $namespace . '\\\\';
            }
            $regex = '|^' . $component . '\\\\' . $namespace . '|';
            $it = new RegexIterator(new ArrayIterator(self::$classmap), $regex, RegexIterator::GET_MATCH, RegexIterator::USE_KEY);

            // We want to be sure that they exist.
            foreach ($it as $classname => $classpath) {
                if (class_exists($classname)) {
                    $classes[$classname] = $classpath;
                }
            }
        }

        return $classes;
    }

    /**
     * Returns the exact absolute path to plugin directory.
     *
     * @param string $plugintype type of plugin
     * @param string $pluginname name of the plugin
     * @return string full path to plugin directory; null if not found
     */
    public static function get_plugin_directory($plugintype, $pluginname) {
        if (empty($pluginname)) {
            // Invalid plugin name, sorry.
            return null;
        }

        self::init();

        if (!isset(self::$plugins[$plugintype][$pluginname]) && !isset(self::$deprecatedplugins[$plugintype][$pluginname])) {
            return null;
        }
        return self::$plugins[$plugintype][$pluginname] ?? self::$deprecatedplugins[$plugintype][$pluginname];
    }

    /**
     * Returns the exact absolute path to plugin directory.
     *
     * @param string $subsystem type of core subsystem
     * @return string full path to subsystem directory; null if not found
     */
    public static function get_subsystem_directory($subsystem) {
        self::init();

        if (!isset(self::$subsystems[$subsystem])) {
            return null;
        }
        return self::$subsystems[$subsystem];
    }

    /**
     * This method validates a plug name. It is much faster than calling clean_param.
     *
     * @param string $plugintype type of plugin
     * @param string $pluginname a string that might be a plugin name.
     * @return bool if this string is a valid plugin name.
     */
    public static function is_valid_plugin_name($plugintype, $pluginname) {
        if ($plugintype === 'mod') {
            // Modules must not have the same name as core subsystems.
            if (!isset(self::$subsystems)) {
                // Watch out, this is called from init!
                self::init();
            }
            if (isset(self::$subsystems[$pluginname])) {
                return false;
            }
            // Modules MUST NOT have any underscores,
            // component normalisation would break very badly otherwise!
            return !is_null($pluginname) && (bool) preg_match('/^[a-z][a-z0-9]*$/', $pluginname);
        } else {
            return !is_null($pluginname) && (bool) preg_match('/^[a-z](?:[a-z0-9_](?!__))*[a-z0-9]+$/', $pluginname);
        }
    }

    /**
     * Normalize the component name.
     *
     * Note: this does not verify the validity of the plugin or component.
     *
     * @param string $component
     * @return string
     */
    public static function normalize_componentname($componentname) {
        [$plugintype, $pluginname] = self::normalize_component($componentname);
        if ($plugintype === 'core' && is_null($pluginname)) {
            return $plugintype;
        }
        return $plugintype . '_' . $pluginname;
    }

    /**
     * Normalize the component name using the "frankenstyle" rules.
     *
     * Note: this does not verify the validity of plugin or type names.
     *
     * @param string $component
     * @return array two-items list of [(string)type, (string|null)name]
     */
    public static function normalize_component($component) {
        if ($component === 'moodle' || $component === 'core' || $component === '') {
            return ['core', null];
        }

        if (strpos($component, '_') === false) {
            self::init();
            if (array_key_exists($component, self::$subsystems)) {
                $type   = 'core';
                $plugin = $component;
            } else {
                // Everything else without underscore is a module.
                $type   = 'mod';
                $plugin = $component;
            }
        } else {
            [$type, $plugin] = explode('_', $component, 2);
            if ($type === 'moodle') {
                $type = 'core';
            }
            // Any unknown type must be a subplugin.
        }

        return [$type, $plugin];
    }

    /**
     * Fetch the component name from a Moodle PSR-like namespace.
     *
     * Note: Classnames in the flat underscore_class_name_format are not supported.
     *
     * @param string $classname
     * @return null|string The component name, or null if a matching component was not found
     */
    public static function get_component_from_classname(string $classname): ?string {
        $components = static::get_component_names(true, true);

        $classname = ltrim($classname, '\\');

        // Prefer PSR-4 classnames.
        $parts = explode('\\', $classname);
        if ($parts) {
            $component = array_shift($parts);
            if (array_search($component, $components) !== false) {
                return $component;
            }
        }

        // Note: Frankenstyle classnames are not supported as they lead to false positives, for example:
        // \core_typo\example => \core instead of \core_typo because it does not exist
        // Please *do not* add support for Frankenstyle classnames. They will break other things.

        return null;
    }

    /**
     * Return exact absolute path to a plugin directory.
     *
     * @param string $component name such as 'moodle', 'mod_forum'
     * @return string full path to component directory; NULL if not found
     */
    public static function get_component_directory($component) {
        global $CFG;

        [$type, $plugin] = self::normalize_component($component);

        if ($type === 'core') {
            if ($plugin === null) {
                return $path = $CFG->libdir;
            }
            return self::get_subsystem_directory($plugin);
        }

        return self::get_plugin_directory($type, $plugin);
    }

    /**
     * Returns list of plugin types that allow subplugins.
     * @return array as (string)plugintype => (string)fulldir
     */
    public static function get_plugin_types_with_subplugins() {
        self::init();

        $return = [];
        foreach (self::$supportsubplugins as $type) {
            $return[$type] = self::$plugintypes[$type];
        }
        return $return;
    }

    /**
     * Returns parent of this subplugin type.
     *
     * No filtering is done on deprecated/deleted subtypes. Calling code should check this if needed.
     *
     * @param string $type
     * @return string parent component or null
     */
    public static function get_subtype_parent($type) {
        self::init();

        if (isset(self::$parents[$type])) {
            return self::$parents[$type];
        }

        return null;
    }

    /**
     * Return all available subplugins of this component.
     * @param string $component.
     * @return array $subtype=>array($component, ..), null if no subtypes defined
     */
    public static function get_subplugins($component) {
        self::init();

        if (isset(self::$subplugins[$component])) {
            return self::$subplugins[$component];
        }

        return null;
    }

    /**
     * Return all subplugins for the component, comprising all available subplugins plus any in deprecation.
     *
     * @param string $component
     * @return array|null $subtype=>array($component, ..), null if no subtypes defined
     */
    public static function get_all_subplugins($component): ?array {
        self::init();
        $subplugins = array_merge(
            self::$subplugins[$component] ?? [],
            self::$deprecatedsubplugins[$component] ?? [],
            self::$deletedsubplugins[$component] ?? [],
        );
        return $subplugins ?: null;
    }

    /**
     * Returns hash of all versions including core and all plugins.
     *
     * This is relatively slow and not fully cached, use with care!
     *
     * @return string sha1 hash
     */
    public static function get_all_versions_hash() {
        return sha1(serialize(self::get_all_versions()));
    }

    /**
     * Returns hash of all versions including core and all plugins.
     *
     * This is relatively slow and not fully cached, use with care!
     *
     * @return array as (string)plugintype_pluginname => (int)version
     */
    public static function get_all_versions(): array {
        global $CFG;

        self::init();

        $versions = [];

        // Main version first.
        $versions['core'] = self::fetch_core_version();

        // The problem here is tha the component cache might be stable,
        // we want this to work also on frontpage without resetting the component cache.
        $usecache = false;
        if (CACHE_DISABLE_ALL || (defined('IGNORE_COMPONENT_CACHE') && IGNORE_COMPONENT_CACHE)) {
            $usecache = true;
        }

        // Now all plugins.
        $plugintypes = self::get_plugin_types();
        foreach ($plugintypes as $type => $typedir) {
            if ($usecache) {
                $plugs = self::get_plugin_list($type);
            } else {
                $plugs = self::fetch_plugins($type, $typedir);
            }
            foreach ($plugs as $plug => $fullplug) {
                $plugin = new stdClass();
                $plugin->version = null;
                $module = $plugin;
                include($fullplug . '/version.php');
                $versions[$type . '_' . $plug] = $plugin->version;
            }
        }

        return $versions;
    }

    /**
     * Returns hash of all core + plugin /db/ directories.
     *
     * This is relatively slow and not fully cached, use with care!
     *
     * @param array|null $components optional component directory => hash array to use. Only used in PHPUnit.
     * @return string sha1 hash.
     */
    public static function get_all_component_hash(?array $components = null): string {
        $tohash = $components ?? self::get_all_directory_hashes();
        return sha1(serialize($tohash));
    }

    /**
     * Get the hashes of all core + plugin /db/ directories.
     *
     * @param array|null $directories optional component directory array to hash. Only used in PHPUnit.
     * @return array of directory => hash.
     */
    public static function get_all_directory_hashes(?array $directories = null): array {
        global $CFG;

        self::init();

        // The problem here is that the component cache might be stale,
        // we want this to work also on frontpage without resetting the component cache.
        $usecache = false;
        if (CACHE_DISABLE_ALL || (defined('IGNORE_COMPONENT_CACHE') && IGNORE_COMPONENT_CACHE)) {
            $usecache = true;
        }

        if (empty($directories)) {
            $directories = [
                $CFG->libdir . '/db',
            ];
            // For all components, get the directory of the /db directory.
            $plugintypes = self::get_plugin_types();
            foreach ($plugintypes as $type => $typedir) {
                if ($usecache) {
                    $plugs = self::get_plugin_list($type);
                } else {
                    $plugs = self::fetch_plugins($type, $typedir);
                }
                foreach ($plugs as $plug) {
                    $directories[] = $plug . '/db';
                }
            }
        }

        // Create a mapping of directories to their hash.
        $hashes = [];
        foreach ($directories as $directory) {
            if (!is_dir($directory)) {
                // Just hash an empty string as the non-existing representation.
                $hashes[$directory] = sha1('');
                continue;
            }

            $scan = scandir($directory);
            if ($scan) {
                sort($scan);
            }
            $scanhashes = [];
            foreach ($scan as $file) {
                $file = $directory . '/' . $file;
                // Moodle ignores directories.
                if (!is_dir($file)) {
                    $scanhashes[] = hash_file('sha1', $file);
                }
            }
            // Finally we can serialize and hash the whole dir.
            $hashes[$directory] = sha1(serialize($scanhashes));
        }

        return $hashes;
    }

    /**
     * Invalidate opcode cache for given file, this is intended for
     * php files that are stored in dataroot.
     *
     * Note: we need it here because this class must be self-contained.
     *
     * @param string $file
     */
    public static function invalidate_opcode_php_cache($file) {
        if (function_exists('opcache_invalidate')) {
            if (!file_exists($file)) {
                return;
            }
            opcache_invalidate($file, true);
        }
    }

    /**
     * Return true if subsystemname is core subsystem.
     *
     * @param string $subsystemname name of the subsystem.
     * @return bool true if core subsystem.
     */
    public static function is_core_subsystem($subsystemname) {
        self::init();
        return isset(self::$subsystems[$subsystemname]);
    }

    /**
     * Return true if apiname is a core API.
     *
     * @param string $apiname name of the API.
     * @return bool true if core API.
     */
    public static function is_core_api($apiname) {
        self::init();
        return isset(self::$apis[$apiname]);
    }

    /**
     * Records all class renames that have been made to facilitate autoloading.
     */
    protected static function fill_classmap_renames_cache() {
        global $CFG;

        self::$classmaprenames = [];

        self::load_renamed_classes("$CFG->dirroot/lib/");

        foreach (self::$subsystems as $subsystem => $fulldir) {
            self::load_renamed_classes($fulldir);
        }

        foreach (self::$plugins as $plugintype => $plugins) {
            foreach ($plugins as $pluginname => $fulldir) {
                self::load_renamed_classes($fulldir);
            }
        }
    }

    /**
     * Loads the db/renamedclasses.php file from the given directory.
     *
     * The renamedclasses.php should contain a key => value array ($renamedclasses) where the key is old class name,
     * and the value is the new class name.
     * It is only included when we are populating the component cache. After that is not needed.
     *
     * @param string|null $fulldir The directory to the renamed classes.
     */
    protected static function load_renamed_classes(?string $fulldir) {
        if (is_null($fulldir)) {
            return;
        }

        $file = $fulldir . '/db/renamedclasses.php';
        if (is_readable($file)) {
            $renamedclasses = null;
            require($file);
            if (is_array($renamedclasses)) {
                foreach ($renamedclasses as $oldclass => $newclass) {
                    self::$classmaprenames[(string)$oldclass] = (string)$newclass;
                }
            }
        }
    }

    /**
     * Load legacy classes based upon the db/legacyclasses.php file.
     *
     * The legacyclasses.php should contain a key => value array ($legacyclasses) where the key is the class name,
     * and the value is the path to the class file within the relative ../classes/ directory.
     *
     * @param string|null $fulldir The directory to the legacy classes.
     * @param bool $allowsubsystems Whether to allow the specification of alternative subsystems for this path.
     */
    protected static function load_legacy_classes(
        ?string $fulldir,
        bool $allowsubsystems = false,
    ): void {
        if (is_null($fulldir)) {
            return;
        }

        $file = $fulldir . '/db/legacyclasses.php';
        if (is_readable($file)) {
            $legacyclasses = null;
            require($file);
            if (is_array($legacyclasses)) {
                foreach ($legacyclasses as $classname => $path) {
                    if (is_array($path)) {
                        if (!$allowsubsystems) {
                            throw new Exception(
                                "Invalid legacy classes path entry for {$classname}. " .
                                    "Only files within the component can be specified.",
                            );
                        }
                        if (count($path) !== 2) {
                            throw new Exception(
                                "Invalid legacy classes path entry for {$classname}. " .
                                    "Entries must be in the format [subsystem, path].",
                            );
                        }
                        [$subsystem, $path] = $path;
                        $subsystem = substr($subsystem, 5);
                        if (!array_key_exists($subsystem, self::$subsystems)) {
                            throw new Exception(
                                "Unknown subsystem '{$subsystem}' for legacy classes entry of '{$classname}'",
                            );
                        }

                        $subsystemfulldir = self::$subsystems[$subsystem];
                        self::$classmap[$classname] = "{$subsystemfulldir}/classes/{$path}";
                    } else {
                        self::$classmap[$classname] = "{$fulldir}/classes/{$path}";
                    }
                }
            }
        }
    }

    /**
     * Returns a list of frankenstyle component names and their paths, for all components (plugins and subsystems).
     *
     * E.g.
     *  [
     *      'mod' => [
     *          'mod_forum' => FORUM_PLUGIN_PATH,
     *          ...
     *      ],
     *      ...
     *      'core' => [
     *          'core_comment' => COMMENT_SUBSYSTEM_PATH,
     *          ...
     *      ]
     * ]
     *
     * @return array an associative array of components and their corresponding paths.
     */
    public static function get_component_list(): array {
        $components = [];
        // Get all plugins.
        foreach (self::get_plugin_types() as $plugintype => $typedir) {
            $components[$plugintype] = [];
            foreach (self::get_plugin_list($plugintype) as $pluginname => $plugindir) {
                $components[$plugintype][$plugintype . '_' . $pluginname] = $plugindir;
            }
        }
        // Get all subsystems.
        foreach (self::get_core_subsystems() as $subsystemname => $subsystempath) {
            $components['core']['core_' . $subsystemname] = $subsystempath;
        }
        return $components;
    }

    /**
     * Returns a list of frankenstyle component names, including all plugins, subplugins, and subsystems.
     *
     * Note: By default the 'core' subsystem is not included.
     *
     * @param bool $includecore Whether to include the 'core' subsystem
     * @param bool $includedeprecated Whether to include deprecated components
     * @return string[] the list of frankenstyle component names.
     */
    public static function get_component_names(
        bool $includecore = false,
        bool $includedeprecated = false
    ): array {
        $componentnames = [];
        // Get all plugins.
        foreach (self::get_plugin_types() as $plugintype => $typedir) {
            foreach (self::get_plugin_list($plugintype) as $pluginname => $plugindir) {
                $componentnames[] = $plugintype . '_' . $pluginname;
            }
        }
        // Get all subsystems.
        foreach (self::get_core_subsystems() as $subsystemname => $subsystempath) {
            $componentnames[] = 'core_' . $subsystemname;
        }

        if ($includecore) {
            $componentnames[] = 'core';
        }

        if ($includedeprecated) {
            foreach (self::get_deprecated_plugin_types() as $plugintype => $typedir) {
                foreach (self::get_deprecated_plugin_list($plugintype) as $pluginname => $plugindir) {
                    $componentnames[] = $plugintype . '_' . $pluginname;
                }
            }
        }

        return $componentnames;
    }

    /**
     * Returns the list of available API names.
     *
     * @return string[] the list of available API names.
     */
    public static function get_core_api_names(): array {
        return array_keys(self::get_core_apis());
    }

    /**
     * Checks for the presence of monologo icons within a plugin.
     *
     * Only checks monologo icons in PNG and SVG formats as they are
     * formats that can have transparent background.
     *
     * @param string $plugintype The plugin type.
     * @param string $pluginname The plugin name.
     * @return bool True if the plugin has a monologo icon
     */
    public static function has_monologo_icon(string $plugintype, string $pluginname): bool {
        global $PAGE;
        $plugindir = self::get_plugin_directory($plugintype, $pluginname);
        if ($plugindir === null) {
            return false;
        }
        $theme = theme_config::load($PAGE->theme->name);
        $component = self::normalize_componentname("{$plugintype}_{$pluginname}");
        $hassvgmonologo = $theme->resolve_image_location('monologo', $component, true) !== null;
        $haspngmonologo = $theme->resolve_image_location('monologo', $component) !== null;
        return $haspngmonologo || $hassvgmonologo;
    }

    /**
     * Returns a path relative to the Moodle root directory.
     *
     * @param string $path The child path
     * @return string The full path within the root directory.
     */
    protected static function get_path(string $path = ''): string {
        global $CFG;

        if (property_exists($CFG, 'root')) {
            // If the root property exists, use it.
            $root = $CFG->root;
        } else if (property_exists($CFG, 'dirroot')) {
            $root = dirname($CFG->dirroot);
        } else {
            throw new \RuntimeException(
                'The $CFG->root or $CFG->dirroot property must be set to use the component class.',
            );
        }

        if ($path === '') {
            // If no path is provided, return the root directory.
            return rtrim($root, '/');
        }

        return rtrim($root, '/') . '/' . ltrim($path, '/');
    }
}

// Alias this class to the old name.
// This should be kept here because we use this class in external tooling.
class_alias(component::class, \core_component::class);

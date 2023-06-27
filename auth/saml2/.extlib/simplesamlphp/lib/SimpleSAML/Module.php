<?php

declare(strict_types=1);

namespace SimpleSAML;

use SimpleSAML\Kernel;
use SimpleSAML\Utils;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Helper class for accessing information about modules.
 *
 * @author Olav Morken <olav.morken@uninett.no>, UNINETT AS.
 * @author Boy Baukema, SURFnet.
 * @author Jaime Perez <jaime.perez@uninett.no>, UNINETT AS.
 * @package SimpleSAMLphp
 */
class Module
{
    /**
     * Index pages: file names to attempt when accessing directories.
     *
     * @var array
     */
    public static $indexFiles = ['index.php', 'index.html', 'index.htm', 'index.txt'];

    /**
     * MIME Types
     *
     * The key is the file extension and the value the corresponding MIME type.
     *
     * @var array
     */
    public static $mimeTypes = [
        'bmp'   => 'image/x-ms-bmp',
        'css'   => 'text/css',
        'gif'   => 'image/gif',
        'htm'   => 'text/html',
        'html'  => 'text/html',
        'shtml' => 'text/html',
        'ico'   => 'image/vnd.microsoft.icon',
        'jpe'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'jpg'   => 'image/jpeg',
        'js'    => 'text/javascript',
        'pdf'   => 'application/pdf',
        'png'   => 'image/png',
        'svg'   => 'image/svg+xml',
        'svgz'  => 'image/svg+xml',
        'swf'   => 'application/x-shockwave-flash',
        'swfl'  => 'application/x-shockwave-flash',
        'txt'   => 'text/plain',
        'xht'   => 'application/xhtml+xml',
        'xhtml' => 'application/xhtml+xml',
    ];

    /**
     * A list containing the modules currently installed.
     *
     * @var array
     */
    public static $modules = [];

    /**
     * A cache containing specific information for modules, like whether they are enabled or not, or their hooks.
     *
     * @var array
     */
    public static $module_info = [];


    /**
     * Retrieve the base directory for a module.
     *
     * The returned path name will be an absolute path.
     *
     * @param string $module Name of the module
     *
     * @return string The base directory of a module.
     */
    public static function getModuleDir($module)
    {
        $baseDir = dirname(dirname(dirname(__FILE__))) . '/modules';
        $moduleDir = $baseDir . '/' . $module;

        return $moduleDir;
    }


    /**
     * Determine whether a module is enabled.
     *
     * Will return false if the given module doesn't exist.
     *
     * @param string $module Name of the module
     *
     * @return bool True if the given module is enabled, false otherwise.
     *
     * @throws \Exception If module.enable is set and is not boolean.
     */
    public static function isModuleEnabled($module)
    {
        $config = Configuration::getOptionalConfig();
        return self::isModuleEnabledWithConf($module, $config->getArray('module.enable', []));
    }


    /**
     * Handler for module requests.
     *
     * This controller receives requests for pages hosted by modules, and processes accordingly. Depending on the
     * configuration and the actual request, it will run a PHP script and exit, or return a Response produced either
     * by another controller or by a static file.
     *
     * @param Request|null $request The request to process. Defaults to the current one.
     *
     * @return Response|BinaryFileResponse Returns a Response object that can be sent to the browser.
     * @throws Error\BadRequest In case the request URI is malformed.
     * @throws Error\NotFound In case the request URI is invalid or the resource it points to cannot be found.
     */
    public static function process(Request $request = null)
    {
        if ($request === null) {
            $request = Request::createFromGlobals();
        }

        if ($request->server->get('PATH_INFO') === '/') {
            throw new Error\NotFound('No PATH_INFO to module.php');
        }

        $url = $request->server->get('PATH_INFO');
        assert(substr($url, 0, 1) === '/');

        /* clear the PATH_INFO option, so that a script can detect whether it is called with anything following the
         *'.php'-ending.
         */
        unset($_SERVER['PATH_INFO']);

        $modEnd = strpos($url, '/', 1);
        if ($modEnd === false) {
            $modEnd = strlen($url);
            $module = substr($url, 1);
            $url = '';
        } else {
            $module = substr($url, 1, $modEnd - 1);
            $url = substr($url, $modEnd + 1);
        }

        if (!self::isModuleEnabled($module)) {
            throw new Error\NotFound('The module \'' . $module . '\' was either not found, or wasn\'t enabled.');
        }

        /* Make sure that the request isn't suspicious (contains references to current directory or parent directory or
         * anything like that. Searching for './' in the URL will detect both '../' and './'. Searching for '\' will
         * detect attempts to use Windows-style paths.
         */
        if (strpos($url, '\\') !== false) {
            throw new Error\BadRequest('Requested URL contained a backslash.');
        } elseif (strpos($url, './') !== false) {
            throw new Error\BadRequest('Requested URL contained \'./\'.');
        }

        $config = Configuration::getInstance();

        // rebuild REQUEST_URI and SCRIPT_NAME just in case we need to.
        // This is needed for server aliases and rewrites
        $translated_uri = $config->getBasePath() . 'module.php/' . $module . '/' . $url;
        $request->server->set('REQUEST_URI', $translated_uri);
        $request->server->set('SCRIPT_NAME', $config->getBasePath() . 'module.php');
        // strip any NULL files (form file fields with nothing selected)
        // because initialize() will throw an error on them
        $request_files = array_filter(
            $request->files->all(),
            function ($val) {
                return !is_null($val);
            }
        );
        $request->initialize(
            $request->query->all(),
            $request->request->all(),
            $request->attributes->all(),
            $request->cookies->all(),
            $request_files,
            $request->server->all(),
            $request->getContent()
        );

        if ($config->getBoolean('usenewui', false) === true) {
            try {
                $kernel = new Kernel($module);
                $response = $kernel->handle($request);
                $kernel->terminate($request, $response);

                return $response;
            } catch (FileLocatorFileNotFoundException $e) {
                // no routes configured for this module, fall back to the old system
            } catch (NotFoundHttpException $e) {
                // this module has been migrated, but the route wasn't found
            }
        }

        $moduleDir = self::getModuleDir($module) . '/www/';

        // check for '.php/' in the path, the presence of which indicates that another php-script should handle the
        // request
        for ($phpPos = strpos($url, '.php/'); $phpPos !== false; $phpPos = strpos($url, '.php/', $phpPos + 1)) {
            $newURL = substr($url, 0, $phpPos + 4);
            $param = substr($url, $phpPos + 4);

            if (is_file($moduleDir . $newURL)) {
                /* $newPath points to a normal file. Point execution to that file, and save the remainder of the path
                 * in PATH_INFO.
                 */
                $url = $newURL;
                $request->server->set('PATH_INFO', $param);
                $_SERVER['PATH_INFO'] = $param;
                break;
            }
        }

        $path = $moduleDir . $url;

        if ($path[strlen($path) - 1] === '/') {
            // path ends with a slash - directory reference. Attempt to find index file in directory
            foreach (self::$indexFiles as $if) {
                if (file_exists($path . $if)) {
                    $path .= $if;
                    break;
                }
            }
        }

        if (is_dir($path)) {
            /* Path is a directory - maybe no index file was found in the previous step, or maybe the path didn't end
             * with a slash. Either way, we don't do directory listings.
             */
            throw new Error\NotFound('Directory listing not available.');
        }

        if (!file_exists($path)) {
            // file not found
            Logger::info('Could not find file \'' . $path . '\'.');
            throw new Error\NotFound('The URL wasn\'t found in the module.');
        }

        if (mb_strtolower(substr($path, -4), 'UTF-8') === '.php') {
            // PHP file - attempt to run it

            /* In some environments, $_SERVER['SCRIPT_NAME'] is already set with $_SERVER['PATH_INFO']. Check for that
             * case, and append script name only if necessary.
             *
             * Contributed by Travis Hegner.
             */
            $script = "/$module/$url";
            if (strpos($request->getScriptName(), $script) === false) {
                $request->server->set('SCRIPT_NAME', $request->getScriptName() . '/' . $module . '/' . $url);
            }

            require($path);
            exit();
        }

        // some other file type - attempt to serve it

        // find MIME type for file, based on extension
        $contentType = null;
        if (preg_match('#\.([^/\.]+)$#D', $path, $type)) {
            $type = strtolower($type[1]);
            if (array_key_exists($type, self::$mimeTypes)) {
                $contentType = self::$mimeTypes[$type];
            }
        }

        if ($contentType === null) {
            /* We were unable to determine the MIME type from the file extension. Fall back to mime_content_type (if it
             * exists).
             */
            if (function_exists('mime_content_type')) {
                $contentType = mime_content_type($path);
            } else {
                // mime_content_type doesn't exist. Return a default MIME type
                Logger::warning('Unable to determine mime content type of file: ' . $path);
                $contentType = 'application/octet-stream';
            }
        }

        /** @psalm-var \SimpleSAML\Configuration $assetConfig */
        $assetConfig = $config->getConfigItem('assets');
        /** @psalm-var \SimpleSAML\Configuration $cacheConfig */
        $cacheConfig = $assetConfig->getConfigItem('caching');
        $response = new BinaryFileResponse($path);
        $response->setCache([
            // "public" allows response caching even if the request was authenticated,
            // which is exactly what we want for static resources
            'public' => true,
            'max_age' => strval($cacheConfig->getInteger('max_age', 86400))
        ]);
        $response->setAutoLastModified();
        if ($cacheConfig->getBoolean('etag', false)) {
            $response->setAutoEtag();
        }
        $response->isNotModified($request);
        $response->headers->set('Content-Type', $contentType);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE);
        $response->prepare($request);
        return $response;
    }


    /**
     * @param string $module
     * @param array $mod_config
     * @return bool
     */
    private static function isModuleEnabledWithConf(string $module, array $mod_config): bool
    {
        if (isset(self::$module_info[$module]['enabled'])) {
            return self::$module_info[$module]['enabled'];
        }

        if (!empty(self::$modules) && !in_array($module, self::$modules, true)) {
            return false;
        }

        $moduleDir = self::getModuleDir($module);

        if (!is_dir($moduleDir)) {
            self::$module_info[$module]['enabled'] = false;
            return false;
        }

        if (isset($mod_config[$module])) {
            if (is_bool($mod_config[$module])) {
                self::$module_info[$module]['enabled'] = $mod_config[$module];
                return $mod_config[$module];
            }

            throw new \Exception("Invalid module.enable value for the '$module' module.");
        }

        if (
            assert_options(ASSERT_ACTIVE)
            && !file_exists($moduleDir . '/default-enable')
            && !file_exists($moduleDir . '/default-disable')
        ) {
            Logger::error("Missing default-enable or default-disable file for the module $module");
        }

        if (file_exists($moduleDir . '/enable')) {
            self::$module_info[$module]['enabled'] = true;
            return true;
        }

        if (!file_exists($moduleDir . '/disable') && file_exists($moduleDir . '/default-enable')) {
            self::$module_info[$module]['enabled'] = true;
            return true;
        }

        self::$module_info[$module]['enabled'] = false;
        return false;
    }


    /**
     * Get available modules.
     *
     * @return array One string for each module.
     *
     * @throws \Exception If we cannot open the module's directory.
     */
    public static function getModules()
    {
        if (!empty(self::$modules)) {
            return self::$modules;
        }

        $path = self::getModuleDir('.');

        $dh = scandir($path);
        if ($dh === false) {
            throw new \Exception('Unable to open module directory "' . $path . '".');
        }

        foreach ($dh as $f) {
            if ($f[0] === '.') {
                continue;
            }

            if (!is_dir($path . '/' . $f)) {
                continue;
            }

            self::$modules[] = $f;
        }

        return self::$modules;
    }


    /**
     * Resolve module class.
     *
     * This function takes a string on the form "<module>:<class>" and converts it to a class
     * name. It can also check that the given class is a subclass of a specific class. The
     * resolved classname will be "\SimleSAML\Module\<module>\<$type>\<class>.
     *
     * It is also possible to specify a full classname instead of <module>:<class>.
     *
     * An exception will be thrown if the class can't be resolved.
     *
     * @param string      $id The string we should resolve.
     * @param string      $type The type of the class.
     * @param string|null $subclass The class should be a subclass of this class. Optional.
     *
     * @return string The classname.
     *
     * @throws \Exception If the class cannot be resolved.
     */
    public static function resolveClass($id, $type, $subclass = null)
    {
        assert(is_string($id));
        assert(is_string($type));
        assert(is_string($subclass) || $subclass === null);

        $tmp = explode(':', $id, 2);
        if (count($tmp) === 1) {
            // no module involved
            $className = $tmp[0];
            if (!class_exists($className)) {
                throw new \Exception("Could not resolve '$id': no class named '$className'.");
            }
        } elseif (!in_array($tmp[0], self::getModules())) {
            // Module not installed
            throw new \Exception('No module named \'' . $tmp[0]. '\' has been installed.');
        } elseif (!self::isModuleEnabled($tmp[0])) {
            // Module installed, but not enabled
            throw new \Exception('The module \'' . $tmp[0]. '\' is not enabled.');
        } else {
            // should be a module
            // make sure empty types are handled correctly
            $type = (empty($type)) ? '\\' : '\\' . $type . '\\';

            $className = 'SimpleSAML\\Module\\' . $tmp[0] . $type . $tmp[1];
            if (!class_exists($className)) {
                // check for the old-style class names
                $type = str_replace('\\', '_', $type);
                $oldClassName = 'sspmod_' . $tmp[0] . $type . $tmp[1];

                if (!class_exists($oldClassName)) {
                    throw new \Exception("Could not resolve '$id': no class named '$className' or '$oldClassName'.");
                }
                $className = $oldClassName;
            }
        }

        if ($subclass !== null && !is_subclass_of($className, $subclass)) {
            throw new \Exception(
                'Could not resolve \'' . $id . '\': The class \'' . $className
                . '\' isn\'t a subclass of \'' . $subclass . '\'.'
            );
        }

        return $className;
    }


    /**
     * Get absolute URL to a specified module resource.
     *
     * This function creates an absolute URL to a resource stored under ".../modules/<module>/www/".
     *
     * @param string $resource Resource path, on the form "<module name>/<resource>"
     * @param array  $parameters Extra parameters which should be added to the URL. Optional.
     *
     * @return string The absolute URL to the given resource.
     */
    public static function getModuleURL($resource, array $parameters = [])
    {
        assert(is_string($resource));
        assert($resource[0] !== '/');

        $url = Utils\HTTP::getBaseURL() . 'module.php/' . $resource;
        if (!empty($parameters)) {
            $url = Utils\HTTP::addURLParameters($url, $parameters);
        }
        return $url;
    }


    /**
     * Get the available hooks for a given module.
     *
     * @param string $module The module where we should look for hooks.
     *
     * @return array An array with the hooks available for this module. Each element is an array with two keys: 'file'
     * points to the file that contains the hook, and 'func' contains the name of the function implementing that hook.
     * When there are no hooks defined, an empty array is returned.
     */
    public static function getModuleHooks($module)
    {
        if (isset(self::$modules[$module]['hooks'])) {
            return self::$modules[$module]['hooks'];
        }

        $hook_dir = self::getModuleDir($module) . '/hooks';
        if (!is_dir($hook_dir)) {
            return [];
        }

        $hooks = [];
        $files = scandir($hook_dir);
        foreach ($files as $file) {
            if ($file[0] === '.') {
                continue;
            }

            if (!preg_match('/hook_(\w+)\.php/', $file, $matches)) {
                continue;
            }
            $hook_name = $matches[1];
            $hook_func = $module . '_hook_' . $hook_name;
            $hooks[$hook_name] = ['file' => $hook_dir . '/' . $file, 'func' => $hook_func];
        }
        return $hooks;
    }


    /**
     * Call a hook in all enabled modules.
     *
     * This function iterates over all enabled modules and calls a hook in each module.
     *
     * @param string $hook The name of the hook.
     * @param mixed  &$data The data which should be passed to each hook. Will be passed as a reference.
     * @return void
     *
     * @throws \SimpleSAML\Error\Exception If an invalid hook is found in a module.
     */
    public static function callHooks($hook, &$data = null)
    {
        assert(is_string($hook));

        $modules = self::getModules();
        $config = Configuration::getOptionalConfig()->getArray('module.enable', []);
        sort($modules);
        foreach ($modules as $module) {
            if (!self::isModuleEnabledWithConf($module, $config)) {
                continue;
            }

            if (!isset(self::$module_info[$module]['hooks'])) {
                self::$module_info[$module]['hooks'] = self::getModuleHooks($module);
            }

            if (
                !isset(self::$module_info[$module]['hooks'][$hook])
                || empty(self::$module_info[$module]['hooks'][$hook])
            ) {
                continue;
            }

            require_once(self::$module_info[$module]['hooks'][$hook]['file']);

            if (!is_callable(self::$module_info[$module]['hooks'][$hook]['func'])) {
                throw new Error\Exception('Invalid hook \'' . $hook . '\' for module \'' . $module . '\'.');
            }

            $fn = self::$module_info[$module]['hooks'][$hook]['func'];
            $fn($data);
        }
    }


    /**
     * Handle a valid request for a module that lacks a trailing slash.
     *
     * This method add the trailing slash and redirects to the resulting URL.
     *
     * @param Request $request The request to process by this controller method.
     *
     * @return RedirectResponse A redirection to the URI specified in the request, but with a trailing slash.
     */
    public static function addTrailingSlash(Request $request)
    {
        // Must be of form /{module} - append a slash
        return new RedirectResponse($request->getRequestUri() . '/', 308);
    }


    /**
     * Handle a valid request that ends with a trailing slash.
     *
     * This method removes the trailing slash and redirects to the resulting URL.
     *
     * @param Request $request The request to process by this controller method.
     *
     * @return RedirectResponse A redirection to the URI specified in the request, but without the trailing slash.
     */
    public static function removeTrailingSlash(Request $request)
    {
        $pathInfo = $request->server->get('PATH_INFO');
        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $request->getRequestUri());
        return new RedirectResponse($url, 308);
    }
}

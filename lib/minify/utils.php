<?php
/**
 * Utility functions for generating group URIs in HTML files
 *
 * Before including this file, /min/lib must be in your include_path.
 * 
 * @package Minify
 */

require_once 'Minify/Build.php';


/**
 * Get a timestamped URI to a minified resource using the default Minify install
 *
 * <code>
 * <link rel="stylesheet" type="text/css" href="<?php echo Minify_groupUri('css'); ?>" />
 * <script type="text/javascript" src="<?php echo Minify_groupUri('js'); ?>"></script>
 * </code>
 *
 * If you do not want ampersands as HTML entities, set Minify_Build::$ampersand = "&" 
 * before using this function.
 *
 * @param string $group a key from groupsConfig.php
 * @param boolean $forceAmpersand (default false) Set to true if the RewriteRule
 * directives in .htaccess are functional. This will remove the "?" from URIs, making them
 * more cacheable by proxies.
 * @return string
 */ 
function Minify_groupUri($group, $forceAmpersand = false)
{
    $path = $forceAmpersand
        ? "/g={$group}"
        : "/?g={$group}";
    return _Minify_getBuild($group)->uri(
        '/' . basename(dirname(__FILE__)) . $path
        ,$forceAmpersand
    );
}


/**
 * Get the last modification time of the source js/css files used by Minify to
 * build the page.
 * 
 * If you're caching the output of Minify_groupUri(), you'll want to rebuild 
 * the cache if it's older than this timestamp.
 * 
 * <code>
 * // simplistic HTML cache system
 * $file = '/path/to/cache/file';
 * if (! file_exists($file) || filemtime($file) < Minify_groupsMtime(array('js', 'css'))) {
 *     // (re)build cache
 *     $page = buildPage(); // this calls Minify_groupUri() for js and css
 *     file_put_contents($file, $page);
 *     echo $page;
 *     exit();
 * }
 * readfile($file);
 * </code>
 *
 * @param array $groups an array of keys from groupsConfig.php
 * @return int Unix timestamp of the latest modification
 */ 
function Minify_groupsMtime($groups)
{
    $max = 0;
    foreach ((array)$groups as $group) {
        $max = max($max, _Minify_getBuild($group)->lastModified);
    }
    return $max;
}

/**
 * @param string $group a key from groupsConfig.php
 * @return Minify_Build
 * @private
 */
function _Minify_getBuild($group)
{
    static $builds = array();
    static $gc = false;
    if (false === $gc) {
        $gc = (require dirname(__FILE__) . '/groupsConfig.php');
    }
    if (! isset($builds[$group])) {
        $builds[$group] = new Minify_Build($gc[$group]);
    }
    return $builds[$group];
}

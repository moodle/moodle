<?php
/**
 * Configuration for default Minify application
 * @package Minify
 */

defined('MOODLE_INTERNAL') || die();

/**
 * In 'debug' mode, Minify can combine files with no minification and
 * add comments to indicate line #s of the original files.
 *
 * To allow debugging, set this option to true and add "&debug=1" to
 * a URI. E.g. /min/?f=script1.js,script2.js&debug=1
 */
$min_allowDebugFlag = ($CFG->debug);


/**
 * Set to true to log messages to FirePHP (Firefox Firebug addon).
 * Set to false for no error logging (Minify may be slightly faster).
 * @link http://www.firephp.org/
 *
 * If you want to use a custom error logger, set this to your logger
 * instance. Your object should have a method log(string $message).
 *
 * @todo cache system does not have error logging yet.
 */
$min_errorLogger = false;


/**
 * Allow use of the Minify URI Builder app. If you no longer need
 * this, set to false.
 **/
$min_enableBuilder = false;


/**
 * For best performance, specify your temp directory here. Otherwise Minify
 * will have to load extra code to guess. Some examples below:
 */
$min_cachePath = $CFG->dataroot.'/temp';


/**
 * Leave an empty string to use PHP's $_SERVER['DOCUMENT_ROOT'].
 *
 * On some servers, this value may be misconfigured or missing. If so, set this
 * to your full document root path with no trailing slash.
 * E.g. '/home/accountname/public_html' or 'c:\\xampp\\htdocs'
 *
 * If /min/ is directly inside your document root, just uncomment the
 * second line. The third line might work on some Apache servers.
 */
$min_documentRoot = $CFG->dirroot.'/lib/minify';
//$min_documentRoot = substr(__FILE__, 0, strlen(__FILE__) - 15);
//$min_documentRoot = $_SERVER['SUBDOMAIN_DOCUMENT_ROOT'];


/**
 * Cache file locking. Set to false if filesystem is NFS. On at least one
 * NFS system flock-ing attempts stalled PHP for 30 seconds!
 */
$min_cacheFileLocking = true;


/**
 * Combining multiple CSS files can place @import declarations after rules, which
 * is invalid. Minify will attempt to detect when this happens and place a
 * warning comment at the top of the CSS output. To resolve this you can either
 * move the @imports within your CSS files, or enable this option, which will
 * move all @imports to the top of the output. Note that moving @imports could
 * affect CSS values (which is why this option is disabled by default).
 */
$min_serveOptions['bubbleCssImports'] = false;


/**
 * Maximum age of browser cache in seconds. After this period, the browser
 * will send another conditional GET. Use a longer period for lower traffic
 * but you may want to shorten this before making changes if it's crucial
 * those changes are seen immediately.
 *
 * Note: Despite this setting, if you include a number at the end of the
 * querystring, maxAge will be set to one year. E.g. /min/f=hello.css&123456
 */
$min_serveOptions['maxAge'] = 1800;


/**
 * If you'd like to restrict the "f" option to files within/below
 * particular directories below DOCUMENT_ROOT, set this here.
 * You will still need to include the directory in the
 * f or b GET parameters.
 *
 * // = shortcut for DOCUMENT_ROOT
 */
//$min_serveOptions['minApp']['allowDirs'] = array('//js', '//css');

/**
 * Set to true to disable the "f" GET parameter for specifying files.
 * Only the "g" parameter will be considered.
 */
$min_serveOptions['minApp']['groupsOnly'] = true;

/**
 * Maximum # of files that can be specified in the "f" GET parameter
 */
$min_serveOptions['minApp']['maxFiles'] = 10;


/**
 * If you minify CSS files stored in symlink-ed directories, the URI rewriting
 * algorithm can fail. To prevent this, provide an array of link paths to
 * target paths, where the link paths are within the document root.
 *
 * Because paths need to be normalized for this to work, use "//" to substitute
 * the doc root in the link paths (the array keys). E.g.:
 * <code>
 * array('//symlink' => '/real/target/path') // unix
 * array('//static' => 'D:\\staticStorage')  // Windows
 * </code>
 */
$min_symlinks = array();


/**
 * If you upload files from Windows to a non-Windows server, Windows may report
 * incorrect mtimes for the files. This may cause Minify to keep serving stale
 * cache files when source file changes are made too frequently (e.g. more than
 * once an hour).
 *
 * Immediately after modifying and uploading a file, use the touch command to
 * update the mtime on the server. If the mtime jumps ahead by a number of hours,
 * set this variable to that number. If the mtime moves back, this should not be
 * needed.
 *
 * In the Windows SFTP client WinSCP, there's an option that may fix this
 * issue without changing the variable below. Under login > environment,
 * select the option "Adjust remote timestamp with DST".
 * @link http://winscp.net/eng/docs/ui_login_environment#daylight_saving_time
 */
$min_uploaderHoursBehind = 0;


/**
 * Path to Minify's lib folder. If you happen to move it, change
 * this accordingly.
 */
$min_libPath = $CFG->libdir . '/minify/lib';


// try to disable output_compression (may not have an effect)
ini_set('zlib.output_compression', '0');

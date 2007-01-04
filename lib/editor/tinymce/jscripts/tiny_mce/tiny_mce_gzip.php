<?php // $Id: tiny_mce_gzip.php,v 1.1 2006/03/04 15:57:32 julmis Exp $
/**
 * $RCSfile: tiny_mce_gzip.php,v $
 * $Revision: 1.1 $
 * $Date: 2006/03/04 15:57:32 $
 *
 * @version 1.07
 * @author Moxiecode
 * @copyright Copyright © 20052006, Moxiecode Systems AB, All rights reserved.
 *
 * This file compresses the TinyMCE JavaScript using GZip and
 * enables the browser to do two requests instead of one for each .js file.
 * Notice: This script defaults the button_tile_map option to true for extra performance.
 */

// General options
$suffix = "";                           // Set to "_src" to use source version
$expiresOffset = 3600 * 24 * 10;        // 10 days util client cache expires
$diskCache = false;                     // If you enable this option gzip files will be cached on disk.
$cacheDir = realpath(".");              // Absolute directory path to where cached gz files will be stored
$debug = false;                         // Enable this option if you need debuging info

// Headers
header("Content-type: text/javascript; charset: UTF-8");
// header("Cache-Control: must-revalidate");
header("Vary: Accept-Encoding"); // Handle proxies
header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expiresOffset) . " GMT");

// Get data to load
$theme = isset($_GET['theme']) ? TinyMCE_cleanInput($_GET['theme']) : "";
$language = isset($_GET['language']) ? TinyMCE_cleanInput($_GET['language']) : "";
$plugins = isset($_GET['plugins']) ? TinyMCE_cleanInput($_GET['plugins']) : "";
$lang = isset($_GET['lang']) ? TinyMCE_cleanInput($_GET['lang']) : "en";
$index = isset($_GET['index']) ? TinyMCE_cleanInput($_GET['index']) : -1;
$cacheKey = md5($theme . $language . $plugins . $lang . $index . $debug);
$cacheFile = $cacheDir == "" ? "" : $cacheDir . "/" . "tinymce_" .  $cacheKey . ".gz";
$cacheData = "";

// Patch older versions of PHP < 4.3.0
if (!function_exists('file_get_contents')) {
    function file_get_contents($filename) {
        $fd = fopen($filename, 'rb');
        $content = fread($fd, filesize($filename));
        fclose($fd);
        return $content;
    }
}

// Security check function, can only contain a-z 0-9 , _ - and whitespace.
function TinyMCE_cleanInput($str) {
    return preg_replace("/[^0-9a-z\-_,]+/i", "", $str); // Remove anything but 0-9,a-z,-_
}

function TinyMCE_echo($str) {
    global $cacheData, $diskCache;

    if ($diskCache)
        $cacheData .= $str;
    else
        echo $str;
}

// Only gzip the contents if clients and server support it
$encodings = array();

if (isset($_SERVER['HTTP_ACCEPT_ENCODING']))
    $encodings = explode(',', strtolower(preg_replace("/\s+/", "", $_SERVER['HTTP_ACCEPT_ENCODING'])));

// Check for gzip header or northon internet securities
if ((in_array('gzip', $encodings) || isset($_SERVER['---------------'])) && function_exists('ob_gzhandler') && !ini_get('zlib.output_compression')) {
    // Use cached file if it exists but not in debug mode
    if (file_exists($cacheFile) && !$debug) {
        header("Content-Encoding: gzip");
        echo file_get_contents($cacheFile);
        die;
    }

    if (!$diskCache)
        ob_start("ob_gzhandler");
} else
    $diskCache = false;

if ($index > -1) {
    // Write main script and patch some things
    if ($index == 0) {
        TinyMCE_echo(file_get_contents(realpath("tiny_mce" . $suffix . ".js")));
        TinyMCE_echo('TinyMCE.prototype.loadScript = function() {};var realTinyMCE = tinyMCE;');
    } else
        TinyMCE_echo('tinyMCE = realTinyMCE;');

    // Do init based on index
    TinyMCE_echo("tinyMCE.init(tinyMCECompressed.configs[" . $index . "]);");

    // Load theme, language pack and theme language packs
    if ($theme) {
        TinyMCE_echo(file_get_contents(realpath("themes/" . $theme . "/editor_template" . $suffix . ".js")));
        TinyMCE_echo(file_get_contents(realpath("themes/" . $theme . "/langs/" . $lang . ".js")));
    }

    if ($language)
        TinyMCE_echo(file_get_contents(realpath("langs/" . $language . ".js")));

    // Load all plugins and their language packs
    $plugins = explode(",", $plugins);
    foreach ($plugins as $plugin) {
        $pluginFile = realpath("plugins/" . $plugin . "/editor_plugin" . $suffix . ".js");
        $languageFile = realpath("plugins/" . $plugin . "/langs/" . $lang . ".js");

        if ($pluginFile)
            TinyMCE_echo(file_get_contents($pluginFile));

        if ($languageFile)
            TinyMCE_echo(file_get_contents($languageFile));
    }

    // Reset tinyMCE compressor engine
    TinyMCE_echo("tinyMCE = tinyMCECompressed;");

    // Write to cache
    if ($diskCache) {
        // Calculate compression ratio and debug target output path
        if ($debug) {
            $ratio = round(100 - strlen(gzencode($cacheData, 9, FORCE_GZIP)) / strlen($cacheData) * 100.0);
            TinyMCE_echo("alert('TinyMCE was compressed by " . $ratio . "%.\\nOutput cache file: " . $cacheFile . "');");
        }

        $cacheData = gzencode($cacheData, 9, FORCE_GZIP);

        // Write to file if possible
        $fp = @fopen($cacheFile, "wb");
        if ($fp) {
            fwrite($fp, $cacheData);
            fclose($fp);
        }

        // Output
        header("Content-Encoding: gzip");
        echo $cacheData;
    }

    die;
}
?>

function TinyMCECompressed() {
    this.configs = new Array();
    this.loadedFiles = new Array();
    this.loadAdded = false;
    this.isLoaded = false;
}

TinyMCECompressed.prototype.init = function(settings) {
    var elements = document.getElementsByTagName('script');
    var scriptURL = "";

    for (var i=0; i<elements.length; i++) {
        if (elements[i].src && elements[i].src.indexOf("tiny_mce_gzip.php") != -1) {
            scriptURL = elements[i].src;
            break;
        }
    }

    settings["theme"] = typeof(settings["theme"]) != "undefined" ? settings["theme"] : "default";
    settings["plugins"] = typeof(settings["plugins"]) != "undefined" ? settings["plugins"] : "";
    settings["language"] = typeof(settings["language"]) != "undefined" ? settings["language"] : "en";
    settings["button_tile_map"] = typeof(settings["button_tile_map"]) != "undefined" ? settings["button_tile_map"] : true;
    this.configs[this.configs.length] = settings;
    this.settings = settings;

    scriptURL += "?theme=" + escape(this.getOnce(settings["theme"])) + "&language=" + escape(this.getOnce(settings["language"])) + "&plugins=" + escape(this.getOnce(settings["plugins"])) + "&lang=" + settings["language"] + "&index=" + escape(this.configs.length-1);
    document.write('<sc'+'ript language="javascript" type="text/javascript" src="' + scriptURL + '"></script>');

    if (!this.loadAdded) {
        tinyMCE.addEvent(window, "DOMContentLoaded", TinyMCECompressed.prototype.onLoad);
        tinyMCE.addEvent(window, "load", TinyMCECompressed.prototype.onLoad);
        this.loadAdded = true;
    }
}

TinyMCECompressed.prototype.onLoad = function() {
    if (tinyMCE.isLoaded)
        return true;

    tinyMCE = realTinyMCE;
    TinyMCE_Engine.prototype.onLoad();
    tinyMCE._addUnloadEvents();
    tinyMCE.isLoaded = true;
}

TinyMCECompressed.prototype.addEvent = function(o, n, h) {
    if (o.attachEvent)
        o.attachEvent("on" + n, h);
    else
        o.addEventListener(n, h, false);
}

TinyMCECompressed.prototype.getOnce = function(str) {
    var ar = str.split(',');

    for (var i=0; i<ar.length; i++) {
        if (ar[i] == '')
            continue;

        // Skip load
        for (var x=0; x<this.loadedFiles.length; x++) {
            if (this.loadedFiles[x] == ar[i])
                ar[i] = null;
        }

        this.loadedFiles[this.loadedFiles.length] = ar[i];
    }

    // Glue
    str = "";
    for (var i=0; i<ar.length; i++) {
        if (ar[i] == null)
            continue;

        str += ar[i];

        if (i != ar.length-1)
            str += ",";
    }

    return str;
}

var tinyMCE = new TinyMCECompressed();
var tinyMCECompressed = tinyMCE;

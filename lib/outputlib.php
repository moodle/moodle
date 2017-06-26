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
 * Functions for generating the HTML that Moodle should output.
 *
 * Please see http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML
 * for an overview.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 * @category output
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/outputcomponents.php');
require_once($CFG->libdir.'/outputactions.php');
require_once($CFG->libdir.'/outputfactories.php');
require_once($CFG->libdir.'/outputrenderers.php');
require_once($CFG->libdir.'/outputrequirementslib.php');

/**
 * Invalidate all server and client side caches.
 *
 * This method deletes the physical directory that is used to cache the theme
 * files used for serving.
 * Because it deletes the main theme cache directory all themes are reset by
 * this function.
 */
function theme_reset_all_caches() {
    global $CFG, $PAGE;

    $next = time();
    if (isset($CFG->themerev) and $next <= $CFG->themerev and $CFG->themerev - $next < 60*60) {
        // This resolves problems when reset is requested repeatedly within 1s,
        // the < 1h condition prevents accidental switching to future dates
        // because we might not recover from it.
        $next = $CFG->themerev+1;
    }

    set_config('themerev', $next); // time is unique even when you reset/switch database

    if (!empty($CFG->themedesignermode)) {
        $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'core', 'themedesigner');
        $cache->purge();
    }

    // Purge compiled post processed css.
    cache::make('core', 'postprocessedcss')->purge();

    if ($PAGE) {
        $PAGE->reload_theme();
    }
}

/**
 * Enable or disable theme designer mode.
 *
 * @param bool $state
 */
function theme_set_designer_mod($state) {
    set_config('themedesignermode', (int)!empty($state));
    // Reset caches after switching mode so that any designer mode caches get purged too.
    theme_reset_all_caches();
}

/**
 * Returns current theme revision number.
 *
 * @return int
 */
function theme_get_revision() {
    global $CFG;

    if (empty($CFG->themedesignermode)) {
        if (empty($CFG->themerev)) {
            // This only happens during install. It doesn't matter what themerev we use as long as it's positive.
            return 1;
        } else {
            return $CFG->themerev;
        }

    } else {
        return -1;
    }
}

/**
 * Checks if the given device has a theme defined in config.php.
 *
 * @return bool
 */
function theme_is_device_locked($device) {
    global $CFG;
    $themeconfigname = core_useragent::get_device_type_cfg_var_name($device);
    return isset($CFG->config_php_settings[$themeconfigname]);
}

/**
 * Returns the theme named defined in config.php for the given device.
 *
 * @return string or null
 */
function theme_get_locked_theme_for_device($device) {
    global $CFG;

    if (!theme_is_device_locked($device)) {
        return null;
    }

    $themeconfigname = core_useragent::get_device_type_cfg_var_name($device);
    return $CFG->config_php_settings[$themeconfigname];
}

/**
 * This class represents the configuration variables of a Moodle theme.
 *
 * All the variables with access: public below (with a few exceptions that are marked)
 * are the properties you can set in your themes config.php file.
 *
 * There are also some methods and protected variables that are part of the inner
 * workings of Moodle's themes system. If you are just editing a themes config.php
 * file, you can just ignore those, and the following information for developers.
 *
 * Normally, to create an instance of this class, you should use the
 * {@link theme_config::load()} factory method to load a themes config.php file.
 * However, normally you don't need to bother, because moodle_page (that is, $PAGE)
 * will create one for you, accessible as $PAGE->theme.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class theme_config {

    /**
     * @var string Default theme, used when requested theme not found.
     */
    const DEFAULT_THEME = 'boost';

    /** The key under which the SCSS file is stored amongst the CSS files. */
    const SCSS_KEY = '__SCSS__';

    /**
     * @var array You can base your theme on other themes by linking to the other theme as
     * parents. This lets you use the CSS and layouts from the other themes
     * (see {@link theme_config::$layouts}).
     * That makes it easy to create a new theme that is similar to another one
     * but with a few changes. In this themes CSS you only need to override
     * those rules you want to change.
     */
    public $parents;

    /**
     * @var array The names of all the stylesheets from this theme that you would
     * like included, in order. Give the names of the files without .css.
     */
    public $sheets = array();

    /**
     * @var array The names of all the stylesheets from parents that should be excluded.
     * true value may be used to specify all parents or all themes from one parent.
     * If no value specified value from parent theme used.
     */
    public $parents_exclude_sheets = null;

    /**
     * @var array List of plugin sheets to be excluded.
     * If no value specified value from parent theme used.
     */
    public $plugins_exclude_sheets = null;

    /**
     * @var array List of style sheets that are included in the text editor bodies.
     * Sheets from parent themes are used automatically and can not be excluded.
     */
    public $editor_sheets = array();

    /**
     * @var array The names of all the javascript files this theme that you would
     * like included from head, in order. Give the names of the files without .js.
     */
    public $javascripts = array();

    /**
     * @var array The names of all the javascript files this theme that you would
     * like included from footer, in order. Give the names of the files without .js.
     */
    public $javascripts_footer = array();

    /**
     * @var array The names of all the javascript files from parents that should
     * be excluded. true value may be used to specify all parents or all themes
     * from one parent.
     * If no value specified value from parent theme used.
     */
    public $parents_exclude_javascripts = null;

    /**
     * @var array Which file to use for each page layout.
     *
     * This is an array of arrays. The keys of the outer array are the different layouts.
     * Pages in Moodle are using several different layouts like 'normal', 'course', 'home',
     * 'popup', 'form', .... The most reliable way to get a complete list is to look at
     * {@link http://cvs.moodle.org/moodle/theme/base/config.php?view=markup the base theme config.php file}.
     * That file also has a good example of how to set this setting.
     *
     * For each layout, the value in the outer array is an array that describes
     * how you want that type of page to look. For example
     * <pre>
     *   $THEME->layouts = array(
     *       // Most pages - if we encounter an unknown or a missing page type, this one is used.
     *       'standard' => array(
     *           'theme' = 'mytheme',
     *           'file' => 'normal.php',
     *           'regions' => array('side-pre', 'side-post'),
     *           'defaultregion' => 'side-post'
     *       ),
     *       // The site home page.
     *       'home' => array(
     *           'theme' = 'mytheme',
     *           'file' => 'home.php',
     *           'regions' => array('side-pre', 'side-post'),
     *           'defaultregion' => 'side-post'
     *       ),
     *       // ...
     *   );
     * </pre>
     *
     * 'theme' name of the theme where is the layout located
     * 'file' is the layout file to use for this type of page.
     * layout files are stored in layout subfolder
     * 'regions' This lists the regions on the page where blocks may appear. For
     * each region you list here, your layout file must include a call to
     * <pre>
     *   echo $OUTPUT->blocks_for_region($regionname);
     * </pre>
     * or equivalent so that the blocks are actually visible.
     *
     * 'defaultregion' If the list of regions is non-empty, then you must pick
     * one of the one of them as 'default'. This has two meanings. First, this is
     * where new blocks are added. Second, if there are any blocks associated with
     * the page, but in non-existent regions, they appear here. (Imaging, for example,
     * that someone added blocks using a different theme that used different region
     * names, and then switched to this theme.)
     */
    public $layouts = array();

    /**
     * @var string Name of the renderer factory class to use. Must implement the
     * {@link renderer_factory} interface.
     *
     * This is an advanced feature. Moodle output is generated by 'renderers',
     * you can customise the HTML that is output by writing custom renderers,
     * and then you need to specify 'renderer factory' so that Moodle can find
     * your renderers.
     *
     * There are some renderer factories supplied with Moodle. Please follow these
     * links to see what they do.
     * <ul>
     * <li>{@link standard_renderer_factory} - the default.</li>
     * <li>{@link theme_overridden_renderer_factory} - use this if you want to write
     *      your own custom renderers in a lib.php file in this theme (or the parent theme).</li>
     * </ul>
     */
    public $rendererfactory = 'standard_renderer_factory';

    /**
     * @var string Function to do custom CSS post-processing.
     *
     * This is an advanced feature. If you want to do custom post-processing on the
     * CSS before it is output (for example, to replace certain variable names
     * with particular values) you can give the name of a function here.
     */
    public $csspostprocess = null;

    /**
     * @var string Function to do custom CSS post-processing on a parsed CSS tree.
     *
     * This is an advanced feature. If you want to do custom post-processing on the
     * CSS before it is output, you can provide the name of the function here. The
     * function will receive a CSS tree document as first parameter, and the theme_config
     * object as second parameter. A return value is not required, the tree can
     * be edited in place.
     */
    public $csstreepostprocessor = null;

    /**
     * @var string Accessibility: Right arrow-like character is
     * used in the breadcrumb trail, course navigation menu
     * (previous/next activity), calendar, and search forum block.
     * If the theme does not set characters, appropriate defaults
     * are set automatically. Please DO NOT
     * use &lt; &gt; &raquo; - these are confusing for blind users.
     */
    public $rarrow = null;

    /**
     * @var string Accessibility: Left arrow-like character is
     * used in the breadcrumb trail, course navigation menu
     * (previous/next activity), calendar, and search forum block.
     * If the theme does not set characters, appropriate defaults
     * are set automatically. Please DO NOT
     * use &lt; &gt; &raquo; - these are confusing for blind users.
     */
    public $larrow = null;

    /**
     * @var string Accessibility: Up arrow-like character is used in
     * the book heirarchical navigation.
     * If the theme does not set characters, appropriate defaults
     * are set automatically. Please DO NOT
     * use ^ - this is confusing for blind users.
     */
    public $uarrow = null;

    /**
     * @var string Accessibility: Down arrow-like character.
     * If the theme does not set characters, appropriate defaults
     * are set automatically.
     */
    public $darrow = null;

    /**
     * @var bool Some themes may want to disable ajax course editing.
     */
    public $enablecourseajax = true;

    /**
     * @var string Determines served document types
     *  - 'html5' the only officially supported doctype in Moodle
     *  - 'xhtml5' may be used in development for validation (not intended for production servers!)
     *  - 'xhtml' XHTML 1.0 Strict for legacy themes only
     */
    public $doctype = 'html5';

    /**
     * @var string requiredblocks If set to a string, will list the block types that cannot be deleted. Defaults to
     *                                   navigation and settings.
     */
    public $requiredblocks = false;

    //==Following properties are not configurable from theme config.php==

    /**
     * @var string The name of this theme. Set automatically when this theme is
     * loaded. This can not be set in theme config.php
     */
    public $name;

    /**
     * @var string The folder where this themes files are stored. This is set
     * automatically. This can not be set in theme config.php
     */
    public $dir;

    /**
     * @var stdClass Theme settings stored in config_plugins table.
     * This can not be set in theme config.php
     */
    public $setting = null;

    /**
     * @var bool If set to true and the theme enables the dock then  blocks will be able
     * to be moved to the special dock
     */
    public $enable_dock = false;

    /**
     * @var bool If set to true then this theme will not be shown in the theme selector unless
     * theme designer mode is turned on.
     */
    public $hidefromselector = false;

    /**
     * @var array list of YUI CSS modules to be included on each page. This may be used
     * to remove cssreset and use cssnormalise module instead.
     */
    public $yuicssmodules = array('cssreset', 'cssfonts', 'cssgrids', 'cssbase');

    /**
     * An associative array of block manipulations that should be made if the user is using an rtl language.
     * The key is the original block region, and the value is the block region to change to.
     * This is used when displaying blocks for regions only.
     * @var array
     */
    public $blockrtlmanipulations = array();

    /**
     * @var renderer_factory Instance of the renderer_factory implementation
     * we are using. Implementation detail.
     */
    protected $rf = null;

    /**
     * @var array List of parent config objects.
     **/
    protected $parent_configs = array();

    /**
     * Used to determine whether we can serve SVG images or not.
     * @var bool
     */
    private $usesvg = null;

    /**
     * Whether in RTL mode or not.
     * @var bool
     */
    protected $rtlmode = false;

    /**
     * The LESS file to compile. When set, the theme will attempt to compile the file itself.
     * @var bool
     */
    public $lessfile = false;

    /**
     * The SCSS file to compile (without .scss), located in the scss/ folder of the theme.
     * Or a Closure, which receives the theme_config as argument and must
     * return the SCSS content. This setting takes precedence over self::$lessfile.
     * @var string|Closure
     */
    public $scss = false;

    /**
     * Local cache of the SCSS property.
     * @var false|array
     */
    protected $scsscache = null;

    /**
     * The name of the function to call to get the LESS code to inject.
     * @var string
     */
    public $extralesscallback = null;

    /**
     * The name of the function to call to get the SCSS code to inject.
     * @var string
     */
    public $extrascsscallback = null;

    /**
     * The name of the function to call to get extra LESS variables.
     * @var string
     */
    public $lessvariablescallback = null;

    /**
     * The name of the function to call to get SCSS to prepend.
     * @var string
     */
    public $prescsscallback = null;

    /**
     * Sets the render method that should be used for rendering custom block regions by scripts such as my/index.php
     * Defaults to {@link core_renderer::blocks_for_region()}
     * @var string
     */
    public $blockrendermethod = null;

    /**
     * Remember the results of icon remapping for the current page.
     * @var array
     */
    public $remapiconcache = [];

    /**
     * Load the config.php file for a particular theme, and return an instance
     * of this class. (That is, this is a factory method.)
     *
     * @param string $themename the name of the theme.
     * @return theme_config an instance of this class.
     */
    public static function load($themename) {
        global $CFG;

        // load theme settings from db
        try {
            $settings = get_config('theme_'.$themename);
        } catch (dml_exception $e) {
            // most probably moodle tables not created yet
            $settings = new stdClass();
        }

        if ($config = theme_config::find_theme_config($themename, $settings)) {
            return new theme_config($config);

        } else if ($themename == theme_config::DEFAULT_THEME) {
            throw new coding_exception('Default theme '.theme_config::DEFAULT_THEME.' not available or broken!');

        } else if ($config = theme_config::find_theme_config($CFG->theme, $settings)) {
            debugging('This page should be using theme ' . $themename .
                    ' which cannot be initialised. Falling back to the site theme ' . $CFG->theme, DEBUG_NORMAL);
            return new theme_config($config);

        } else {
            // bad luck, the requested theme has some problems - admin see details in theme config
            debugging('This page should be using theme ' . $themename .
                    ' which cannot be initialised. Nor can the site theme ' . $CFG->theme .
                    '. Falling back to ' . theme_config::DEFAULT_THEME, DEBUG_NORMAL);
            return new theme_config(theme_config::find_theme_config(theme_config::DEFAULT_THEME, $settings));
        }
    }

    /**
     * Theme diagnostic code. It is very problematic to send debug output
     * to the actual CSS file, instead this functions is supposed to
     * diagnose given theme and highlights all potential problems.
     * This information should be available from the theme selection page
     * or some other debug page for theme designers.
     *
     * @param string $themename
     * @return array description of problems
     */
    public static function diagnose($themename) {
        //TODO: MDL-21108
        return array();
    }

    /**
     * Private constructor, can be called only from the factory method.
     * @param stdClass $config
     */
    private function __construct($config) {
        global $CFG; //needed for included lib.php files

        $this->settings = $config->settings;
        $this->name     = $config->name;
        $this->dir      = $config->dir;

        if ($this->name != 'bootstrapbase') {
            $baseconfig = theme_config::find_theme_config('bootstrapbase', $this->settings);
        } else {
            $baseconfig = $config;
        }

        $configurable = array(
            'parents', 'sheets', 'parents_exclude_sheets', 'plugins_exclude_sheets',
            'javascripts', 'javascripts_footer', 'parents_exclude_javascripts',
            'layouts', 'enable_dock', 'enablecourseajax', 'requiredblocks',
            'rendererfactory', 'csspostprocess', 'editor_sheets', 'rarrow', 'larrow', 'uarrow', 'darrow',
            'hidefromselector', 'doctype', 'yuicssmodules', 'blockrtlmanipulations',
            'lessfile', 'extralesscallback', 'lessvariablescallback', 'blockrendermethod',
            'scss', 'extrascsscallback', 'prescsscallback', 'csstreepostprocessor', 'addblockposition', 'iconsystem');

        foreach ($config as $key=>$value) {
            if (in_array($key, $configurable)) {
                $this->$key = $value;
            }
        }

        // verify all parents and load configs and renderers
        foreach ($this->parents as $parent) {
            if (!$parent_config = theme_config::find_theme_config($parent, $this->settings)) {
                // this is not good - better exclude faulty parents
                continue;
            }
            $libfile = $parent_config->dir.'/lib.php';
            if (is_readable($libfile)) {
                // theme may store various function here
                include_once($libfile);
            }
            $renderersfile = $parent_config->dir.'/renderers.php';
            if (is_readable($renderersfile)) {
                // may contain core and plugin renderers and renderer factory
                include_once($renderersfile);
            }
            $this->parent_configs[$parent] = $parent_config;
        }
        $libfile = $this->dir.'/lib.php';
        if (is_readable($libfile)) {
            // theme may store various function here
            include_once($libfile);
        }
        $rendererfile = $this->dir.'/renderers.php';
        if (is_readable($rendererfile)) {
            // may contain core and plugin renderers and renderer factory
            include_once($rendererfile);
        } else {
            // check if renderers.php file is missnamed renderer.php
            if (is_readable($this->dir.'/renderer.php')) {
                debugging('Developer hint: '.$this->dir.'/renderer.php should be renamed to ' . $this->dir."/renderers.php.
                    See: http://docs.moodle.org/dev/Output_renderers#Theme_renderers.", DEBUG_DEVELOPER);
            }
        }

        // cascade all layouts properly
        foreach ($baseconfig->layouts as $layout=>$value) {
            if (!isset($this->layouts[$layout])) {
                foreach ($this->parent_configs as $parent_config) {
                    if (isset($parent_config->layouts[$layout])) {
                        $this->layouts[$layout] = $parent_config->layouts[$layout];
                        continue 2;
                    }
                }
                $this->layouts[$layout] = $value;
            }
        }

        //fix arrows if needed
        $this->check_theme_arrows();
    }

    /**
     * Let the theme initialise the page object (usually $PAGE).
     *
     * This may be used for example to request jQuery in add-ons.
     *
     * @param moodle_page $page
     */
    public function init_page(moodle_page $page) {
        $themeinitfunction = 'theme_'.$this->name.'_page_init';
        if (function_exists($themeinitfunction)) {
            $themeinitfunction($page);
        }
    }

    /**
     * Checks if arrows $THEME->rarrow, $THEME->larrow, $THEME->uarrow, $THEME->darrow have been set (theme/-/config.php).
     * If not it applies sensible defaults.
     *
     * Accessibility: right and left arrow Unicode characters for breadcrumb, calendar,
     * search forum block, etc. Important: these are 'silent' in a screen-reader
     * (unlike &gt; &raquo;), and must be accompanied by text.
     */
    private function check_theme_arrows() {
        if (!isset($this->rarrow) and !isset($this->larrow)) {
            // Default, looks good in Win XP/IE 6, Win/Firefox 1.5, Win/Netscape 8...
            // Also OK in Win 9x/2K/IE 5.x
            $this->rarrow = '&#x25BA;';
            $this->larrow = '&#x25C4;';
            $this->uarrow = '&#x25B2;';
            $this->darrow = '&#x25BC;';
            if (empty($_SERVER['HTTP_USER_AGENT'])) {
                $uagent = '';
            } else {
                $uagent = $_SERVER['HTTP_USER_AGENT'];
            }
            if (false !== strpos($uagent, 'Opera')
                || false !== strpos($uagent, 'Mac')) {
                // Looks good in Win XP/Mac/Opera 8/9, Mac/Firefox 2, Camino, Safari.
                // Not broken in Mac/IE 5, Mac/Netscape 7 (?).
                $this->rarrow = '&#x25B6;&#xFE0E;';
                $this->larrow = '&#x25C0;&#xFE0E;';
            }
            elseif ((false !== strpos($uagent, 'Konqueror'))
                || (false !== strpos($uagent, 'Android')))  {
                // The fonts on Android don't include the characters required for this to work as expected.
                // So we use the same ones Konqueror uses.
                $this->rarrow = '&rarr;';
                $this->larrow = '&larr;';
                $this->uarrow = '&uarr;';
                $this->darrow = '&darr;';
            }
            elseif (isset($_SERVER['HTTP_ACCEPT_CHARSET'])
                && false === stripos($_SERVER['HTTP_ACCEPT_CHARSET'], 'utf-8')) {
                // (Win/IE 5 doesn't set ACCEPT_CHARSET, but handles Unicode.)
                // To be safe, non-Unicode browsers!
                $this->rarrow = '&gt;';
                $this->larrow = '&lt;';
                $this->uarrow = '^';
                $this->darrow = 'v';
            }

            // RTL support - in RTL languages, swap r and l arrows
            if (right_to_left()) {
                $t = $this->rarrow;
                $this->rarrow = $this->larrow;
                $this->larrow = $t;
            }
        }
    }

    /**
     * Returns output renderer prefixes, these are used when looking
     * for the overridden renderers in themes.
     *
     * @return array
     */
    public function renderer_prefixes() {
        global $CFG; // just in case the included files need it

        $prefixes = array('theme_'.$this->name);

        foreach ($this->parent_configs as $parent) {
            $prefixes[] = 'theme_'.$parent->name;
        }

        return $prefixes;
    }

    /**
     * Returns the stylesheet URL of this editor content
     *
     * @param bool $encoded false means use & and true use &amp; in URLs
     * @return moodle_url
     */
    public function editor_css_url($encoded=true) {
        global $CFG;
        $rev = theme_get_revision();
        if ($rev > -1) {
            $url = new moodle_url("$CFG->httpswwwroot/theme/styles.php");
            if (!empty($CFG->slasharguments)) {
                $url->set_slashargument('/'.$this->name.'/'.$rev.'/editor', 'noparam', true);
            } else {
                $url->params(array('theme'=>$this->name,'rev'=>$rev, 'type'=>'editor'));
            }
        } else {
            $params = array('theme'=>$this->name, 'type'=>'editor');
            $url = new moodle_url($CFG->httpswwwroot.'/theme/styles_debug.php', $params);
        }
        return $url;
    }

    /**
     * Returns the content of the CSS to be used in editor content
     *
     * @return array
     */
    public function editor_css_files() {
        $files = array();

        // First editor plugins.
        $plugins = core_component::get_plugin_list('editor');
        foreach ($plugins as $plugin=>$fulldir) {
            $sheetfile = "$fulldir/editor_styles.css";
            if (is_readable($sheetfile)) {
                $files['plugin_'.$plugin] = $sheetfile;
            }
        }
        // Then parent themes - base first, the immediate parent last.
        foreach (array_reverse($this->parent_configs) as $parent_config) {
            if (empty($parent_config->editor_sheets)) {
                continue;
            }
            foreach ($parent_config->editor_sheets as $sheet) {
                $sheetfile = "$parent_config->dir/style/$sheet.css";
                if (is_readable($sheetfile)) {
                    $files['parent_'.$parent_config->name.'_'.$sheet] = $sheetfile;
                }
            }
        }
        // Finally this theme.
        if (!empty($this->editor_sheets)) {
            foreach ($this->editor_sheets as $sheet) {
                $sheetfile = "$this->dir/style/$sheet.css";
                if (is_readable($sheetfile)) {
                    $files['theme_'.$sheet] = $sheetfile;
                }
            }
        }

        return $files;
    }

    /**
     * Get the stylesheet URL of this theme.
     *
     * @param moodle_page $page Not used... deprecated?
     * @return moodle_url[]
     */
    public function css_urls(moodle_page $page) {
        global $CFG;

        $rev = theme_get_revision();

        $urls = array();

        $svg = $this->use_svg_icons();
        $separate = (core_useragent::is_ie() && !core_useragent::check_ie_version('10'));

        if ($rev > -1) {
            $filename = right_to_left() ? 'all-rtl' : 'all';
            $url = new moodle_url("$CFG->httpswwwroot/theme/styles.php");
            if (!empty($CFG->slasharguments)) {
                $slashargs = '';
                if (!$svg) {
                    // We add a simple /_s to the start of the path.
                    // The underscore is used to ensure that it isn't a valid theme name.
                    $slashargs .= '/_s'.$slashargs;
                }
                $slashargs .= '/'.$this->name.'/'.$rev.'/'.$filename;
                if ($separate) {
                    $slashargs .= '/chunk0';
                }
                $url->set_slashargument($slashargs, 'noparam', true);
            } else {
                $params = array('theme' => $this->name, 'rev' => $rev, 'type' => $filename);
                if (!$svg) {
                    // We add an SVG param so that we know not to serve SVG images.
                    // We do this because all modern browsers support SVG and this param will one day be removed.
                    $params['svg'] = '0';
                }
                if ($separate) {
                    $params['chunk'] = '0';
                }
                $url->params($params);
            }
            $urls[] = $url;

        } else {
            $baseurl = new moodle_url($CFG->httpswwwroot.'/theme/styles_debug.php');

            $css = $this->get_css_files(true);
            if (!$svg) {
                // We add an SVG param so that we know not to serve SVG images.
                // We do this because all modern browsers support SVG and this param will one day be removed.
                $baseurl->param('svg', '0');
            }
            if (right_to_left()) {
                $baseurl->param('rtl', 1);
            }
            if ($separate) {
                // We might need to chunk long files.
                $baseurl->param('chunk', '0');
            }
            if (core_useragent::is_ie()) {
                // Lalala, IE does not allow more than 31 linked CSS files from main document.
                $urls[] = new moodle_url($baseurl, array('theme'=>$this->name, 'type'=>'ie', 'subtype'=>'plugins'));
                foreach ($css['parents'] as $parent=>$sheets) {
                    // We need to serve parents individually otherwise we may easily exceed the style limit IE imposes (4096).
                    $urls[] = new moodle_url($baseurl, array('theme'=>$this->name,'type'=>'ie', 'subtype'=>'parents', 'sheet'=>$parent));
                }
                if ($this->get_scss_property()) {
                    // No need to define the type as IE here.
                    $urls[] = new moodle_url($baseurl, array('theme' => $this->name, 'type' => 'scss'));
                } else if (!empty($this->lessfile)) {
                    // No need to define the type as IE here.
                    $urls[] = new moodle_url($baseurl, array('theme' => $this->name, 'type' => 'less'));
                }
                $urls[] = new moodle_url($baseurl, array('theme'=>$this->name, 'type'=>'ie', 'subtype'=>'theme'));

            } else {
                foreach ($css['plugins'] as $plugin=>$unused) {
                    $urls[] = new moodle_url($baseurl, array('theme'=>$this->name,'type'=>'plugin', 'subtype'=>$plugin));
                }
                foreach ($css['parents'] as $parent=>$sheets) {
                    foreach ($sheets as $sheet=>$unused2) {
                        $urls[] = new moodle_url($baseurl, array('theme'=>$this->name,'type'=>'parent', 'subtype'=>$parent, 'sheet'=>$sheet));
                    }
                }
                foreach ($css['theme'] as $sheet => $filename) {
                    if ($sheet === self::SCSS_KEY) {
                        // This is the theme SCSS file.
                        $urls[] = new moodle_url($baseurl, array('theme' => $this->name, 'type' => 'scss'));
                    } else if ($sheet === $this->lessfile) {
                        // This is the theme LESS file.
                        $urls[] = new moodle_url($baseurl, array('theme' => $this->name, 'type' => 'less'));
                    } else {
                        // Sheet first in order to make long urls easier to read.
                        $urls[] = new moodle_url($baseurl, array('sheet'=>$sheet, 'theme'=>$this->name, 'type'=>'theme'));
                    }
                }
            }
        }

        return $urls;
    }

    /**
     * Get the whole css stylesheet for production mode.
     *
     * NOTE: this method is not expected to be used from any addons.
     *
     * @return string CSS markup compressed
     */
    public function get_css_content() {

        $csscontent = '';
        foreach ($this->get_css_files(false) as $type => $value) {
            foreach ($value as $identifier => $val) {
                if (is_array($val)) {
                    foreach ($val as $v) {
                        $csscontent .= file_get_contents($v) . "\n";
                    }
                } else {
                    if ($type === 'theme' && $identifier === self::SCSS_KEY) {
                        // We need the content from SCSS because this is the SCSS file from the theme.
                        $csscontent .= $this->get_css_content_from_scss(false);
                    } else if ($type === 'theme' && $identifier === $this->lessfile) {
                        // We need the content from LESS because this is the LESS file from the theme.
                        $csscontent .= $this->get_css_content_from_less(false);
                    } else {
                        $csscontent .= file_get_contents($val) . "\n";
                    }
                }
            }
        }
        $csscontent = $this->post_process($csscontent);
        $csscontent = core_minify::css($csscontent);

        return $csscontent;
    }
    /**
     * Set post processed CSS content cache.
     *
     * @param string $csscontent The post processed CSS content.
     * @return bool True if the content was successfully cached.
     */
    public function set_css_content_cache($csscontent) {

        $cache = cache::make('core', 'postprocessedcss');
        $key = $this->get_css_cache_key();

        return $cache->set($key, $csscontent);
    }

    /**
     * Return cached post processed CSS content.
     *
     * @return bool|string The cached css content or false if not found.
     */
    public function get_css_cached_content() {

        $key = $this->get_css_cache_key();
        $cache = cache::make('core', 'postprocessedcss');

        return $cache->get($key);
    }

    /**
     * Generate the css content cache key.
     *
     * @return string The post processed css cache key.
     */
    public function get_css_cache_key() {
        $nosvg = (!$this->use_svg_icons()) ? 'nosvg_' : '';
        $rtlmode = ($this->rtlmode == true) ? 'rtl' : 'ltr';

        return $nosvg . $this->name . '_' . $rtlmode;
    }

    /**
     * Get the theme designer css markup,
     * the parameters are coming from css_urls().
     *
     * NOTE: this method is not expected to be used from any addons.
     *
     * @param string $type
     * @param string $subtype
     * @param string $sheet
     * @return string CSS markup
     */
    public function get_css_content_debug($type, $subtype, $sheet) {

        if ($type === 'scss') {
            // The SCSS file of the theme is requested.
            $csscontent = $this->get_css_content_from_scss(true);
            if ($csscontent !== false) {
                return $this->post_process($csscontent);
            }
            return '';
        } else if ($type === 'less') {
            // The LESS file of the theme is requested.
            $csscontent = $this->get_css_content_from_less(true);
            if ($csscontent !== false) {
                return $this->post_process($csscontent);
            }
            return '';
        }

        $cssfiles = array();
        $css = $this->get_css_files(true);

        if ($type === 'ie') {
            // IE is a sloppy browser with weird limits, sorry.
            if ($subtype === 'plugins') {
                $cssfiles = $css['plugins'];

            } else if ($subtype === 'parents') {
                if (empty($sheet)) {
                    // Do not bother with the empty parent here.
                } else {
                    // Build up the CSS for that parent so we can serve it as one file.
                    foreach ($css[$subtype][$sheet] as $parent => $css) {
                        $cssfiles[] = $css;
                    }
                }
            } else if ($subtype === 'theme') {
                $cssfiles = $css['theme'];
                foreach ($cssfiles as $key => $value) {
                    if (in_array($key, [$this->lessfile, self::SCSS_KEY])) {
                        // Remove the LESS/SCSS file from the theme CSS files.
                        // The LESS/SCSS files use the type 'less' or 'scss', not 'ie'.
                        unset($cssfiles[$key]);
                    }
                }
            }

        } else if ($type === 'plugin') {
            if (isset($css['plugins'][$subtype])) {
                $cssfiles[] = $css['plugins'][$subtype];
            }

        } else if ($type === 'parent') {
            if (isset($css['parents'][$subtype][$sheet])) {
                $cssfiles[] = $css['parents'][$subtype][$sheet];
            }

        } else if ($type === 'theme') {
            if (isset($css['theme'][$sheet])) {
                $cssfiles[] = $css['theme'][$sheet];
            }
        }

        $csscontent = '';
        foreach ($cssfiles as $file) {
            $contents = file_get_contents($file);
            $contents = $this->post_process($contents);
            $comment = "/** Path: $type $subtype $sheet.' **/\n";
            $stats = '';
            $csscontent .= $comment.$stats.$contents."\n\n";
        }

        return $csscontent;
    }

    /**
     * Get the whole css stylesheet for editor iframe.
     *
     * NOTE: this method is not expected to be used from any addons.
     *
     * @return string CSS markup
     */
    public function get_css_content_editor() {
        // Do not bother to optimise anything here, just very basic stuff.
        $cssfiles = $this->editor_css_files();
        $css = '';
        foreach ($cssfiles as $file) {
            $css .= file_get_contents($file)."\n";
        }
        return $this->post_process($css);
    }

    /**
     * Returns an array of organised CSS files required for this output.
     *
     * @param bool $themedesigner
     * @return array nested array of file paths
     */
    protected function get_css_files($themedesigner) {
        global $CFG;

        $cache = null;
        $cachekey = 'cssfiles';
        if ($themedesigner) {
            require_once($CFG->dirroot.'/lib/csslib.php');
            // We need some kind of caching here because otherwise the page navigation becomes
            // way too slow in theme designer mode. Feel free to create full cache definition later...
            $cache = cache::make_from_params(cache_store::MODE_APPLICATION, 'core', 'themedesigner', array('theme' => $this->name));
            if ($files = $cache->get($cachekey)) {
                if ($files['created'] > time() - THEME_DESIGNER_CACHE_LIFETIME) {
                    unset($files['created']);
                    return $files;
                }
            }
        }

        $cssfiles = array('plugins'=>array(), 'parents'=>array(), 'theme'=>array());

        // Get all plugin sheets.
        $excludes = $this->resolve_excludes('plugins_exclude_sheets');
        if ($excludes !== true) {
            foreach (core_component::get_plugin_types() as $type=>$unused) {
                if ($type === 'theme' || (!empty($excludes[$type]) and $excludes[$type] === true)) {
                    continue;
                }
                $plugins = core_component::get_plugin_list($type);
                foreach ($plugins as $plugin=>$fulldir) {
                    if (!empty($excludes[$type]) and is_array($excludes[$type])
                            and in_array($plugin, $excludes[$type])) {
                        continue;
                    }

                    // Get the CSS from the plugin.
                    $sheetfile = "$fulldir/styles.css";
                    if (is_readable($sheetfile)) {
                        $cssfiles['plugins'][$type.'_'.$plugin] = $sheetfile;
                    }

                    // Create a list of candidate sheets from parents (direct parent last) and current theme.
                    $candidates = array();
                    foreach (array_reverse($this->parent_configs) as $parent_config) {
                        $candidates[] = $parent_config->name;
                    }
                    $candidates[] = $this->name;

                    // Add the sheets found.
                    foreach ($candidates as $candidate) {
                        $sheetthemefile = "$fulldir/styles_{$candidate}.css";
                        if (is_readable($sheetthemefile)) {
                            $cssfiles['plugins'][$type.'_'.$plugin.'_'.$candidate] = $sheetthemefile;
                        }
                    }
                }
            }
        }

        // Find out wanted parent sheets.
        $excludes = $this->resolve_excludes('parents_exclude_sheets');
        if ($excludes !== true) {
            foreach (array_reverse($this->parent_configs) as $parent_config) { // Base first, the immediate parent last.
                $parent = $parent_config->name;
                if (empty($parent_config->sheets) || (!empty($excludes[$parent]) and $excludes[$parent] === true)) {
                    continue;
                }
                foreach ($parent_config->sheets as $sheet) {
                    if (!empty($excludes[$parent]) && is_array($excludes[$parent])
                            && in_array($sheet, $excludes[$parent])) {
                        continue;
                    }

                    // We never refer to the parent LESS files.
                    $sheetfile = "$parent_config->dir/style/$sheet.css";
                    if (is_readable($sheetfile)) {
                        $cssfiles['parents'][$parent][$sheet] = $sheetfile;
                    }
                }
            }
        }


        // Current theme sheets and less file.
        // We first add the SCSS, or LESS file because we want the CSS ones to
        // be included after the SCSS/LESS code. However, if both the LESS file
        // and a CSS file share the same name, the CSS file is ignored.
        if ($this->get_scss_property()) {
            $cssfiles['theme'][self::SCSS_KEY] = true;
        } else if (!empty($this->lessfile)) {
            $sheetfile = "{$this->dir}/less/{$this->lessfile}.less";
            if (is_readable($sheetfile)) {
                $cssfiles['theme'][$this->lessfile] = $sheetfile;
            }
        }
        if (is_array($this->sheets)) {
            foreach ($this->sheets as $sheet) {
                $sheetfile = "$this->dir/style/$sheet.css";
                if (is_readable($sheetfile) && !isset($cssfiles['theme'][$sheet])) {
                    $cssfiles['theme'][$sheet] = $sheetfile;
                }
            }
        }

        if ($cache) {
            $files = $cssfiles;
            $files['created'] = time();
            $cache->set($cachekey, $files);
        }
        return $cssfiles;
    }

    /**
     * Return the CSS content generated from LESS the file.
     *
     * @param bool $themedesigner True if theme designer is enabled.
     * @return bool|string Return false when the compilation failed. Else the compiled string.
     */
    protected function get_css_content_from_less($themedesigner) {
        global $CFG;

        $lessfile = $this->lessfile;
        if (!$lessfile || !is_readable($this->dir . '/less/' . $lessfile . '.less')) {
            throw new coding_exception('The theme did not define a LESS file, or it is not readable.');
        }

        // We might need more memory/time to do this, so let's play safe.
        raise_memory_limit(MEMORY_EXTRA);
        core_php_time_limit::raise(300);

        // Files list.
        $files = $this->get_css_files($themedesigner);

        // Get the LESS file path.
        $themelessfile = $files['theme'][$lessfile];

        // Setup compiler options.
        $options = array(
            // We need to set the import directory to where $lessfile is.
            'import_dirs' => array(dirname($themelessfile) => '/'),
            // Always disable default caching.
            'cache_method' => false,
            // Disable the relative URLs, we have post_process() to handle that.
            'relativeUrls' => false,
        );

        if ($themedesigner) {
            // Add the sourceMap inline to ensure that it is atomically generated.
            $options['sourceMap'] = true;
            $options['sourceMapBasepath'] = $CFG->dirroot;
            $options['sourceMapRootpath'] = $CFG->wwwroot;
        }

        // Instantiate the compiler.
        $compiler = new core_lessc($options);

        try {
            $compiler->parse_file_content($themelessfile);

            // Get the callbacks.
            $compiler->parse($this->get_extra_less_code());
            $compiler->ModifyVars($this->get_less_variables());

            // Compile the CSS.
            $compiled = $compiler->getCss();

        } catch (Less_Exception_Parser $e) {
            $compiled = false;
            debugging('Error while compiling LESS ' . $lessfile . ' file: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }

        // Try to save memory.
        $compiler = null;
        unset($compiler);

        return $compiled;
    }

    /**
     * Return the CSS content generated from the SCSS file.
     *
     * @param bool $themedesigner True if theme designer is enabled.
     * @return bool|string Return false when the compilation failed. Else the compiled string.
     */
    protected function get_css_content_from_scss($themedesigner) {
        global $CFG;

        list($paths, $scss) = $this->get_scss_property();
        if (!$scss) {
            throw new coding_exception('The theme did not define a SCSS file, or it is not readable.');
        }

        // We might need more memory/time to do this, so let's play safe.
        raise_memory_limit(MEMORY_EXTRA);
        core_php_time_limit::raise(300);

        // Set-up the compiler.
        $compiler = new core_scss();
        $compiler->prepend_raw_scss($this->get_pre_scss_code());
        if (is_string($scss)) {
            $compiler->set_file($scss);
        } else {
            $compiler->append_raw_scss($scss($this));
            $compiler->setImportPaths($paths);
        }
        $compiler->append_raw_scss($this->get_extra_scss_code());

        try {
            // Compile!
            $compiled = $compiler->to_css();

        } catch (\Leafo\ScssPhp\Exception $e) {
            $compiled = false;
            debugging('Error while compiling SCSS: ' . $e->getMessage(), DEBUG_DEVELOPER);
        }

        // Try to save memory.
        $compiler = null;
        unset($compiler);

        return $compiled;
    }

    /**
     * Get the icon system to use.
     *
     * @return string
     */
    public function get_icon_system() {

        // Getting all the candidate functions.
        $system = false;
        if (isset($this->iconsystem) && \core\output\icon_system::is_valid_system($this->iconsystem)) {
            return $this->iconsystem;
        }
        foreach ($this->parent_configs as $parent_config) {
            if (isset($parent_config->iconsystem) && \core\output\icon_system::is_valid_system($parent_config->iconsystem)) {
                return $parent_config->iconsystem;
            }
        }
        return \core\output\icon_system::STANDARD;
    }

    /**
     * Return extra LESS variables to use when compiling.
     *
     * @return array Where keys are the variable names (omitting the @), and the values are the value.
     */
    protected function get_less_variables() {
        $variables = array();

        // Getting all the candidate functions.
        $candidates = array();
        foreach ($this->parent_configs as $parent_config) {
            if (!isset($parent_config->lessvariablescallback)) {
                continue;
            }
            $candidates[] = $parent_config->lessvariablescallback;
        }
        $candidates[] = $this->lessvariablescallback;

        // Calling the functions.
        foreach ($candidates as $function) {
            if (function_exists($function)) {
                $vars = $function($this);
                if (!is_array($vars)) {
                    debugging('Callback ' . $function . ' did not return an array() as expected', DEBUG_DEVELOPER);
                    continue;
                }
                $variables = array_merge($variables, $vars);
            }
        }

        return $variables;
    }

    /**
     * Return extra LESS code to add when compiling.
     *
     * This is intended to be used by themes to inject some LESS code
     * before it gets compiled. If you want to inject variables you
     * should use {@link self::get_less_variables()}.
     *
     * @return string The LESS code to inject.
     */
    protected function get_extra_less_code() {
        $content = '';

        // Getting all the candidate functions.
        $candidates = array();
        foreach ($this->parent_configs as $parent_config) {
            if (!isset($parent_config->extralesscallback)) {
                continue;
            }
            $candidates[] = $parent_config->extralesscallback;
        }
        $candidates[] = $this->extralesscallback;

        // Calling the functions.
        foreach ($candidates as $function) {
            if (function_exists($function)) {
                $content .= "\n/** Extra LESS from $function **/\n" . $function($this) . "\n";
            }
        }

        return $content;
    }

    /**
     * Return extra SCSS code to add when compiling.
     *
     * This is intended to be used by themes to inject some SCSS code
     * before it gets compiled. If you want to inject variables you
     * should use {@link self::get_scss_variables()}.
     *
     * @return string The SCSS code to inject.
     */
    protected function get_extra_scss_code() {
        $content = '';

        // Getting all the candidate functions.
        $candidates = array();
        foreach ($this->parent_configs as $parent_config) {
            if (!isset($parent_config->extrascsscallback)) {
                continue;
            }
            $candidates[] = $parent_config->extrascsscallback;
        }
        $candidates[] = $this->extrascsscallback;

        // Calling the functions.
        foreach ($candidates as $function) {
            if (function_exists($function)) {
                $content .= "\n/** Extra SCSS from $function **/\n" . $function($this) . "\n";
            }
        }

        return $content;
    }

    /**
     * SCSS code to prepend when compiling.
     *
     * This is intended to be used by themes to inject SCSS code before it gets compiled.
     *
     * @return string The SCSS code to inject.
     */
    protected function get_pre_scss_code() {
        $content = '';

        // Getting all the candidate functions.
        $candidates = array();
        foreach ($this->parent_configs as $parent_config) {
            if (!isset($parent_config->prescsscallback)) {
                continue;
            }
            $candidates[] = $parent_config->prescsscallback;
        }
        $candidates[] = $this->prescsscallback;

        // Calling the functions.
        foreach ($candidates as $function) {
            if (function_exists($function)) {
                $content .= "\n/** Pre-SCSS from $function **/\n" . $function($this) . "\n";
            }
        }

        return $content;
    }

    /**
     * Get the SCSS property.
     *
     * This resolves whether a SCSS file (or content) has to be used when generating
     * the stylesheet for the theme. It will look at parents themes and check the
     * SCSS properties there.
     *
     * @return False when SCSS is not used.
     *         An array with the import paths, and the path to the SCSS file or Closure as second.
     */
    public function get_scss_property() {
        if ($this->scsscache === null) {
            $configs = [$this] + $this->parent_configs;
            $scss = null;

            foreach ($configs as $config) {
                $path = "{$config->dir}/scss";

                // We collect the SCSS property until we've found one.
                if (empty($scss) && !empty($config->scss)) {
                    $candidate = is_string($config->scss) ? "{$path}/{$config->scss}.scss" : $config->scss;
                    if ($candidate instanceof Closure) {
                        $scss = $candidate;
                    } else if (is_string($candidate) && is_readable($candidate)) {
                        $scss = $candidate;
                    }
                }

                // We collect the import paths once we've found a SCSS property.
                if ($scss && is_dir($path)) {
                    $paths[] = $path;
                }

            }

            $this->scsscache = $scss !== null ? [$paths, $scss] : false;
        }

        return $this->scsscache;
    }

    /**
     * Generate a URL to the file that serves theme JavaScript files.
     *
     * If we determine that the theme has no relevant files, then we return
     * early with a null value.
     *
     * @param bool $inhead true means head url, false means footer
     * @return moodle_url|null
     */
    public function javascript_url($inhead) {
        global $CFG;

        $rev = theme_get_revision();
        $params = array('theme'=>$this->name,'rev'=>$rev);
        $params['type'] = $inhead ? 'head' : 'footer';

        // Return early if there are no files to serve
        if (count($this->javascript_files($params['type'])) === 0) {
            return null;
        }

        if (!empty($CFG->slasharguments) and $rev > 0) {
            $url = new moodle_url("$CFG->httpswwwroot/theme/javascript.php");
            $url->set_slashargument('/'.$this->name.'/'.$rev.'/'.$params['type'], 'noparam', true);
            return $url;
        } else {
            return new moodle_url($CFG->httpswwwroot.'/theme/javascript.php', $params);
        }
    }

    /**
     * Get the URL's for the JavaScript files used by this theme.
     * They won't be served directly, instead they'll be mediated through
     * theme/javascript.php.
     *
     * @param string $type Either javascripts_footer, or javascripts
     * @return array
     */
    public function javascript_files($type) {
        if ($type === 'footer') {
            $type = 'javascripts_footer';
        } else {
            $type = 'javascripts';
        }

        $js = array();
        // find out wanted parent javascripts
        $excludes = $this->resolve_excludes('parents_exclude_javascripts');
        if ($excludes !== true) {
            foreach (array_reverse($this->parent_configs) as $parent_config) { // base first, the immediate parent last
                $parent = $parent_config->name;
                if (empty($parent_config->$type)) {
                    continue;
                }
                if (!empty($excludes[$parent]) and $excludes[$parent] === true) {
                    continue;
                }
                foreach ($parent_config->$type as $javascript) {
                    if (!empty($excludes[$parent]) and is_array($excludes[$parent])
                        and in_array($javascript, $excludes[$parent])) {
                        continue;
                    }
                    $javascriptfile = "$parent_config->dir/javascript/$javascript.js";
                    if (is_readable($javascriptfile)) {
                        $js[] = $javascriptfile;
                    }
                }
            }
        }

        // current theme javascripts
        if (is_array($this->$type)) {
            foreach ($this->$type as $javascript) {
                $javascriptfile = "$this->dir/javascript/$javascript.js";
                if (is_readable($javascriptfile)) {
                    $js[] = $javascriptfile;
                }
            }
        }
        return $js;
    }

    /**
     * Resolves an exclude setting to the themes setting is applicable or the
     * setting of its closest parent.
     *
     * @param string $variable The name of the setting the exclude setting to resolve
     * @param string $default
     * @return mixed
     */
    protected function resolve_excludes($variable, $default = null) {
        $setting = $default;
        if (is_array($this->{$variable}) or $this->{$variable} === true) {
            $setting = $this->{$variable};
        } else {
            foreach ($this->parent_configs as $parent_config) { // the immediate parent first, base last
                if (!isset($parent_config->{$variable})) {
                    continue;
                }
                if (is_array($parent_config->{$variable}) or $parent_config->{$variable} === true) {
                    $setting = $parent_config->{$variable};
                    break;
                }
            }
        }
        return $setting;
    }

    /**
     * Returns the content of the one huge javascript file merged from all theme javascript files.
     *
     * @param bool $type
     * @return string
     */
    public function javascript_content($type) {
        $jsfiles = $this->javascript_files($type);
        $js = '';
        foreach ($jsfiles as $jsfile) {
            $js .= file_get_contents($jsfile)."\n";
        }
        return $js;
    }

    /**
     * Post processes CSS.
     *
     * This method post processes all of the CSS before it is served for this theme.
     * This is done so that things such as image URL's can be swapped in and to
     * run any specific CSS post process method the theme has requested.
     * This allows themes to use CSS settings.
     *
     * @param string $css The CSS to process.
     * @return string The processed CSS.
     */
    public function post_process($css) {
        // now resolve all image locations
        if (preg_match_all('/\[\[pix:([a-z0-9_]+\|)?([^\]]+)\]\]/', $css, $matches, PREG_SET_ORDER)) {
            $replaced = array();
            foreach ($matches as $match) {
                if (isset($replaced[$match[0]])) {
                    continue;
                }
                $replaced[$match[0]] = true;
                $imagename = $match[2];
                $component = rtrim($match[1], '|');
                $imageurl = $this->image_url($imagename, $component)->out(false);
                 // we do not need full url because the image.php is always in the same dir
                $imageurl = preg_replace('|^http.?://[^/]+|', '', $imageurl);
                $css = str_replace($match[0], $imageurl, $css);
            }
        }

        // Now resolve all font locations.
        if (preg_match_all('/\[\[font:([a-z0-9_]+\|)?([^\]]+)\]\]/', $css, $matches, PREG_SET_ORDER)) {
            $replaced = array();
            foreach ($matches as $match) {
                if (isset($replaced[$match[0]])) {
                    continue;
                }
                $replaced[$match[0]] = true;
                $fontname = $match[2];
                $component = rtrim($match[1], '|');
                $fonturl = $this->font_url($fontname, $component)->out(false);
                // We do not need full url because the font.php is always in the same dir.
                $fonturl = preg_replace('|^http.?://[^/]+|', '', $fonturl);
                $css = str_replace($match[0], $fonturl, $css);
            }
        }

        // Now resolve all theme settings or do any other postprocessing.
        // This needs to be done before calling core parser, since the parser strips [[settings]] tags.
        $csspostprocess = $this->csspostprocess;
        if (function_exists($csspostprocess)) {
            $css = $csspostprocess($css, $this);
        }

        // Post processing using an object representation of CSS.
        $treeprocessor = $this->get_css_tree_post_processor();
        $needsparsing = !empty($treeprocessor) || !empty($this->rtlmode);
        if ($needsparsing) {

            // We might need more memory/time to do this, so let's play safe.
            raise_memory_limit(MEMORY_EXTRA);
            core_php_time_limit::raise(300);

            $parser = new core_cssparser($css);
            $csstree = $parser->parse();
            unset($parser);

            if ($this->rtlmode) {
                $this->rtlize($csstree);
            }

            if ($treeprocessor) {
                $treeprocessor($csstree, $this);
            }

            $css = $csstree->render();
            unset($csstree);
        }

        return $css;
    }

    /**
     * Flip a stylesheet to RTL.
     *
     * @param Object $csstree The parsed CSS tree structure to flip.
     * @return void
     */
    protected function rtlize($csstree) {
        $rtlcss = new core_rtlcss($csstree);
        $rtlcss->flip();
    }

    /**
     * Return the direct URL for an image from the pix folder.
     *
     * Use this function sparingly and never for icons. For icons use pix_icon or the pix helper in a mustache template.
     *
     * @deprecated since Moodle 3.3
     * @param string $imagename the name of the icon.
     * @param string $component specification of one plugin like in get_string()
     * @return moodle_url
     */
    public function pix_url($imagename, $component) {
        debugging('pix_url is deprecated. Use image_url for images and pix_icon for icons.', DEBUG_DEVELOPER);
        return $this->image_url($imagename, $component);
    }

    /**
     * Return the direct URL for an image from the pix folder.
     *
     * Use this function sparingly and never for icons. For icons use pix_icon or the pix helper in a mustache template.
     *
     * @param string $imagename the name of the icon.
     * @param string $component specification of one plugin like in get_string()
     * @return moodle_url
     */
    public function image_url($imagename, $component) {
        global $CFG;

        $params = array('theme'=>$this->name);
        $svg = $this->use_svg_icons();

        if (empty($component) or $component === 'moodle' or $component === 'core') {
            $params['component'] = 'core';
        } else {
            $params['component'] = $component;
        }

        $rev = theme_get_revision();
        if ($rev != -1) {
            $params['rev'] = $rev;
        }

        $params['image'] = $imagename;

        $url = new moodle_url("$CFG->httpswwwroot/theme/image.php");
        if (!empty($CFG->slasharguments) and $rev > 0) {
            $path = '/'.$params['theme'].'/'.$params['component'].'/'.$params['rev'].'/'.$params['image'];
            if (!$svg) {
                // We add a simple /_s to the start of the path.
                // The underscore is used to ensure that it isn't a valid theme name.
                $path = '/_s'.$path;
            }
            $url->set_slashargument($path, 'noparam', true);
        } else {
            if (!$svg) {
                // We add an SVG param so that we know not to serve SVG images.
                // We do this because all modern browsers support SVG and this param will one day be removed.
                $params['svg'] = '0';
            }
            $url->params($params);
        }

        return $url;
    }

    /**
     * Return the URL for a font
     *
     * @param string $font the name of the font (including extension).
     * @param string $component specification of one plugin like in get_string()
     * @return moodle_url
     */
    public function font_url($font, $component) {
        global $CFG;

        $params = array('theme'=>$this->name);

        if (empty($component) or $component === 'moodle' or $component === 'core') {
            $params['component'] = 'core';
        } else {
            $params['component'] = $component;
        }

        $rev = theme_get_revision();
        if ($rev != -1) {
            $params['rev'] = $rev;
        }

        $params['font'] = $font;

        $url = new moodle_url("$CFG->httpswwwroot/theme/font.php");
        if (!empty($CFG->slasharguments) and $rev > 0) {
            $path = '/'.$params['theme'].'/'.$params['component'].'/'.$params['rev'].'/'.$params['font'];
            $url->set_slashargument($path, 'noparam', true);
        } else {
            $url->params($params);
        }

        return $url;
    }

    /**
     * Returns URL to the stored file via pluginfile.php.
     *
     * Note the theme must also implement pluginfile.php handler,
     * theme revision is used instead of the itemid.
     *
     * @param string $setting
     * @param string $filearea
     * @return string protocol relative URL or null if not present
     */
    public function setting_file_url($setting, $filearea) {
        global $CFG;

        if (empty($this->settings->$setting)) {
            return null;
        }

        $component = 'theme_'.$this->name;
        $itemid = theme_get_revision();
        $filepath = $this->settings->$setting;
        $syscontext = context_system::instance();

        $url = moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php", "/$syscontext->id/$component/$filearea/$itemid".$filepath);

        // Now this is tricky because the we can not hardcode http or https here, lets use the relative link.
        // Note: unfortunately moodle_url does not support //urls yet.

        $url = preg_replace('|^https?://|i', '//', $url->out(false));

        return $url;
    }

    /**
     * Serve the theme setting file.
     *
     * @param string $filearea
     * @param array $args
     * @param bool $forcedownload
     * @param array $options
     * @return bool may terminate if file not found or donotdie not specified
     */
    public function setting_file_serve($filearea, $args, $forcedownload, $options) {
        global $CFG;
        require_once("$CFG->libdir/filelib.php");

        $syscontext = context_system::instance();
        $component = 'theme_'.$this->name;

        $revision = array_shift($args);
        if ($revision < 0) {
            $lifetime = 0;
        } else {
            $lifetime = 60*60*24*60;
            // By default, theme files must be cache-able by both browsers and proxies.
            if (!array_key_exists('cacheability', $options)) {
                $options['cacheability'] = 'public';
            }
        }

        $fs = get_file_storage();
        $relativepath = implode('/', $args);

        $fullpath = "/{$syscontext->id}/{$component}/{$filearea}/0/{$relativepath}";
        $fullpath = rtrim($fullpath, '/');
        if ($file = $fs->get_file_by_hash(sha1($fullpath))) {
            send_stored_file($file, $lifetime, 0, $forcedownload, $options);
            return true;
        } else {
            send_file_not_found();
        }
    }

    /**
     * Resolves the real image location.
     *
     * $svg was introduced as an arg in 2.4. It is important because not all supported browsers support the use of SVG
     * and we need a way in which to turn it off.
     * By default SVG won't be used unless asked for. This is done for two reasons:
     *   1. It ensures that we don't serve svg images unless we really want to. The admin has selected to force them, of the users
     *      browser supports SVG.
     *   2. We only serve SVG images from locations we trust. This must NOT include any areas where the image may have been uploaded
     *      by the user due to security concerns.
     *
     * @param string $image name of image, may contain relative path
     * @param string $component
     * @param bool $svg|null Should SVG images also be looked for? If null, resorts to $CFG->svgicons if that is set; falls back to
     * auto-detection of browser support otherwise
     * @return string full file path
     */
    public function resolve_image_location($image, $component, $svg = false) {
        global $CFG;

        if (!is_bool($svg)) {
            // If $svg isn't a bool then we need to decide for ourselves.
            $svg = $this->use_svg_icons();
        }

        if ($component === 'moodle' or $component === 'core' or empty($component)) {
            if ($imagefile = $this->image_exists("$this->dir/pix_core/$image", $svg)) {
                return $imagefile;
            }
            foreach (array_reverse($this->parent_configs) as $parent_config) { // base first, the immediate parent last
                if ($imagefile = $this->image_exists("$parent_config->dir/pix_core/$image", $svg)) {
                    return $imagefile;
                }
            }
            if ($imagefile = $this->image_exists("$CFG->dataroot/pix/$image", $svg)) {
                return $imagefile;
            }
            if ($imagefile = $this->image_exists("$CFG->dirroot/pix/$image", $svg)) {
                return $imagefile;
            }
            return null;

        } else if ($component === 'theme') { //exception
            if ($image === 'favicon') {
                return "$this->dir/pix/favicon.ico";
            }
            if ($imagefile = $this->image_exists("$this->dir/pix/$image", $svg)) {
                return $imagefile;
            }
            foreach (array_reverse($this->parent_configs) as $parent_config) { // base first, the immediate parent last
                if ($imagefile = $this->image_exists("$parent_config->dir/pix/$image", $svg)) {
                    return $imagefile;
                }
            }
            return null;

        } else {
            if (strpos($component, '_') === false) {
                $component = 'mod_'.$component;
            }
            list($type, $plugin) = explode('_', $component, 2);

            if ($imagefile = $this->image_exists("$this->dir/pix_plugins/$type/$plugin/$image", $svg)) {
                return $imagefile;
            }
            foreach (array_reverse($this->parent_configs) as $parent_config) { // base first, the immediate parent last
                if ($imagefile = $this->image_exists("$parent_config->dir/pix_plugins/$type/$plugin/$image", $svg)) {
                    return $imagefile;
                }
            }
            if ($imagefile = $this->image_exists("$CFG->dataroot/pix_plugins/$type/$plugin/$image", $svg)) {
                return $imagefile;
            }
            $dir = core_component::get_plugin_directory($type, $plugin);
            if ($imagefile = $this->image_exists("$dir/pix/$image", $svg)) {
                return $imagefile;
            }
            return null;
        }
    }

    /**
     * Resolves the real font location.
     *
     * @param string $font name of font file
     * @param string $component
     * @return string full file path
     */
    public function resolve_font_location($font, $component) {
        global $CFG;

        if ($component === 'moodle' or $component === 'core' or empty($component)) {
            if (file_exists("$this->dir/fonts_core/$font")) {
                return "$this->dir/fonts_core/$font";
            }
            foreach (array_reverse($this->parent_configs) as $parent_config) { // Base first, the immediate parent last.
                if (file_exists("$parent_config->dir/fonts_core/$font")) {
                    return "$parent_config->dir/fonts_core/$font";
                }
            }
            if (file_exists("$CFG->dataroot/fonts/$font")) {
                return "$CFG->dataroot/fonts/$font";
            }
            if (file_exists("$CFG->dirroot/lib/fonts/$font")) {
                return "$CFG->dirroot/lib/fonts/$font";
            }
            return null;

        } else if ($component === 'theme') { // Exception.
            if (file_exists("$this->dir/fonts/$font")) {
                return "$this->dir/fonts/$font";
            }
            foreach (array_reverse($this->parent_configs) as $parent_config) { // Base first, the immediate parent last.
                if (file_exists("$parent_config->dir/fonts/$font")) {
                    return "$parent_config->dir/fonts/$font";
                }
            }
            return null;

        } else {
            if (strpos($component, '_') === false) {
                $component = 'mod_'.$component;
            }
            list($type, $plugin) = explode('_', $component, 2);

            if (file_exists("$this->dir/fonts_plugins/$type/$plugin/$font")) {
                return "$this->dir/fonts_plugins/$type/$plugin/$font";
            }
            foreach (array_reverse($this->parent_configs) as $parent_config) { // Base first, the immediate parent last.
                if (file_exists("$parent_config->dir/fonts_plugins/$type/$plugin/$font")) {
                    return "$parent_config->dir/fonts_plugins/$type/$plugin/$font";
                }
            }
            if (file_exists("$CFG->dataroot/fonts_plugins/$type/$plugin/$font")) {
                return "$CFG->dataroot/fonts_plugins/$type/$plugin/$font";
            }
            $dir = core_component::get_plugin_directory($type, $plugin);
            if (file_exists("$dir/fonts/$font")) {
                return "$dir/fonts/$font";
            }
            return null;
        }
    }

    /**
     * Return true if we should look for SVG images as well.
     *
     * @return bool
     */
    public function use_svg_icons() {
        global $CFG;
        if ($this->usesvg === null) {

            if (!isset($CFG->svgicons)) {
                $this->usesvg = core_useragent::supports_svg();
            } else {
                // Force them on/off depending upon the setting.
                $this->usesvg = (bool)$CFG->svgicons;
            }
        }
        return $this->usesvg;
    }

    /**
     * Forces the usesvg setting to either true or false, avoiding any decision making.
     *
     * This function should only ever be used when absolutely required, and before any generation of image URL's has occurred.
     * DO NOT ABUSE THIS FUNCTION... not that you'd want to right ;)
     *
     * @param bool $setting True to force the use of svg when available, null otherwise.
     */
    public function force_svg_use($setting) {
        $this->usesvg = (bool)$setting;
    }

    /**
     * Set to be in RTL mode.
     *
     * This will likely be used when post processing the CSS before serving it.
     *
     * @param bool $inrtl True when in RTL mode.
     */
    public function set_rtl_mode($inrtl = true) {
        $this->rtlmode = $inrtl;
    }

    /**
     * Checks if file with any image extension exists.
     *
     * The order to these images was adjusted prior to the release of 2.4
     * At that point the were the following image counts in Moodle core:
     *
     *     - png = 667 in pix dirs (1499 total)
     *     - gif = 385 in pix dirs (606 total)
     *     - jpg = 62  in pix dirs (74 total)
     *     - jpeg = 0  in pix dirs (1 total)
     *
     * There is work in progress to move towards SVG presently hence that has been prioritiesed.
     *
     * @param string $filepath
     * @param bool $svg If set to true SVG images will also be looked for.
     * @return string image name with extension
     */
    private static function image_exists($filepath, $svg = false) {
        if ($svg && file_exists("$filepath.svg")) {
            return "$filepath.svg";
        } else  if (file_exists("$filepath.png")) {
            return "$filepath.png";
        } else if (file_exists("$filepath.gif")) {
            return "$filepath.gif";
        } else  if (file_exists("$filepath.jpg")) {
            return "$filepath.jpg";
        } else  if (file_exists("$filepath.jpeg")) {
            return "$filepath.jpeg";
        } else {
            return false;
        }
    }

    /**
     * Loads the theme config from config.php file.
     *
     * @param string $themename
     * @param stdClass $settings from config_plugins table
     * @param boolean $parentscheck true to also check the parents.    .
     * @return stdClass The theme configuration
     */
    private static function find_theme_config($themename, $settings, $parentscheck = true) {
        // We have to use the variable name $THEME (upper case) because that
        // is what is used in theme config.php files.

        if (!$dir = theme_config::find_theme_location($themename)) {
            return null;
        }

        $THEME = new stdClass();
        $THEME->name     = $themename;
        $THEME->dir      = $dir;
        $THEME->settings = $settings;

        global $CFG; // just in case somebody tries to use $CFG in theme config
        include("$THEME->dir/config.php");

        // verify the theme configuration is OK
        if (!is_array($THEME->parents)) {
            // parents option is mandatory now
            return null;
        } else {
            // We use $parentscheck to only check the direct parents (avoid infinite loop).
            if ($parentscheck) {
                // Find all parent theme configs.
                foreach ($THEME->parents as $parent) {
                    $parentconfig = theme_config::find_theme_config($parent, $settings, false);
                    if (empty($parentconfig)) {
                        return null;
                    }
                }
            }
        }

        return $THEME;
    }

    /**
     * Finds the theme location and verifies the theme has all needed files
     * and is not obsoleted.
     *
     * @param string $themename
     * @return string full dir path or null if not found
     */
    private static function find_theme_location($themename) {
        global $CFG;

        if (file_exists("$CFG->dirroot/theme/$themename/config.php")) {
            $dir = "$CFG->dirroot/theme/$themename";

        } else if (!empty($CFG->themedir) and file_exists("$CFG->themedir/$themename/config.php")) {
            $dir = "$CFG->themedir/$themename";

        } else {
            return null;
        }

        if (file_exists("$dir/styles.php")) {
            //legacy theme - needs to be upgraded - upgrade info is displayed on the admin settings page
            return null;
        }

        return $dir;
    }

    /**
     * Get the renderer for a part of Moodle for this theme.
     *
     * @param moodle_page $page the page we are rendering
     * @param string $component the name of part of moodle. E.g. 'core', 'quiz', 'qtype_multichoice'.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @param string $target one of rendering target constants
     * @return renderer_base the requested renderer.
     */
    public function get_renderer(moodle_page $page, $component, $subtype = null, $target = null) {
        if (is_null($this->rf)) {
            $classname = $this->rendererfactory;
            $this->rf = new $classname($this);
        }

        return $this->rf->get_renderer($page, $component, $subtype, $target);
    }

    /**
     * Get the information from {@link $layouts} for this type of page.
     *
     * @param string $pagelayout the the page layout name.
     * @return array the appropriate part of {@link $layouts}.
     */
    protected function layout_info_for_page($pagelayout) {
        if (array_key_exists($pagelayout, $this->layouts)) {
            return $this->layouts[$pagelayout];
        } else {
            debugging('Invalid page layout specified: ' . $pagelayout);
            return $this->layouts['standard'];
        }
    }

    /**
     * Given the settings of this theme, and the page pagelayout, return the
     * full path of the page layout file to use.
     *
     * Used by {@link core_renderer::header()}.
     *
     * @param string $pagelayout the the page layout name.
     * @return string Full path to the lyout file to use
     */
    public function layout_file($pagelayout) {
        global $CFG;

        $layoutinfo = $this->layout_info_for_page($pagelayout);
        $layoutfile = $layoutinfo['file'];

        if (array_key_exists('theme', $layoutinfo)) {
            $themes = array($layoutinfo['theme']);
        } else {
            $themes = array_merge(array($this->name),$this->parents);
        }

        foreach ($themes as $theme) {
            if ($dir = $this->find_theme_location($theme)) {
                $path = "$dir/layout/$layoutfile";

                // Check the template exists, return general base theme template if not.
                if (is_readable($path)) {
                    return $path;
                }
            }
        }

        debugging('Can not find layout file for: ' . $pagelayout);
        // fallback to standard normal layout
        return "$CFG->dirroot/theme/base/layout/general.php";
    }

    /**
     * Returns auxiliary page layout options specified in layout configuration array.
     *
     * @param string $pagelayout
     * @return array
     */
    public function pagelayout_options($pagelayout) {
        $info = $this->layout_info_for_page($pagelayout);
        if (!empty($info['options'])) {
            return $info['options'];
        }
        return array();
    }

    /**
     * Inform a block_manager about the block regions this theme wants on this
     * page layout.
     *
     * @param string $pagelayout the general type of the page.
     * @param block_manager $blockmanager the block_manger to set up.
     */
    public function setup_blocks($pagelayout, $blockmanager) {
        $layoutinfo = $this->layout_info_for_page($pagelayout);
        if (!empty($layoutinfo['regions'])) {
            $blockmanager->add_regions($layoutinfo['regions'], false);
            $blockmanager->set_default_region($layoutinfo['defaultregion']);
        }
    }

    /**
     * Gets the visible name for the requested block region.
     *
     * @param string $region The region name to get
     * @param string $theme The theme the region belongs to (may come from the parent theme)
     * @return string
     */
    protected function get_region_name($region, $theme) {
        $regionstring = get_string('region-' . $region, 'theme_' . $theme);
        // A name exists in this theme, so use it
        if (substr($regionstring, 0, 1) != '[') {
            return $regionstring;
        }

        // Otherwise, try to find one elsewhere
        // Check parents, if any
        foreach ($this->parents as $parentthemename) {
            $regionstring = get_string('region-' . $region, 'theme_' . $parentthemename);
            if (substr($regionstring, 0, 1) != '[') {
                return $regionstring;
            }
        }

        // Last resort, try the bootstrapbase theme for names
        return get_string('region-' . $region, 'theme_bootstrapbase');
    }

    /**
     * Get the list of all block regions known to this theme in all templates.
     *
     * @return array internal region name => human readable name.
     */
    public function get_all_block_regions() {
        $regions = array();
        foreach ($this->layouts as $layoutinfo) {
            foreach ($layoutinfo['regions'] as $region) {
                $regions[$region] = $this->get_region_name($region, $this->name);
            }
        }
        return $regions;
    }

    /**
     * Returns the human readable name of the theme
     *
     * @return string
     */
    public function get_theme_name() {
        return get_string('pluginname', 'theme_'.$this->name);
    }

    /**
     * Returns the block render method.
     *
     * It is set by the theme via:
     *     $THEME->blockrendermethod = '...';
     *
     * It can be one of two values, blocks or blocks_for_region.
     * It should be set to the method being used by the theme layouts.
     *
     * @return string
     */
    public function get_block_render_method() {
        if ($this->blockrendermethod) {
            // Return the specified block render method.
            return $this->blockrendermethod;
        }
        // Its not explicitly set, check the parent theme configs.
        foreach ($this->parent_configs as $config) {
            if (isset($config->blockrendermethod)) {
                return $config->blockrendermethod;
            }
        }
        // Default it to blocks.
        return 'blocks';
    }

    /**
     * Get the callable for CSS tree post processing.
     *
     * @return string|null
     */
    public function get_css_tree_post_processor() {
        $configs = [$this] + $this->parent_configs;
        foreach ($configs as $config) {
            if (!empty($config->csstreepostprocessor) && is_callable($config->csstreepostprocessor)) {
                return $config->csstreepostprocessor;
            }
        }
        return null;
    }

}

/**
 * This class keeps track of which HTML tags are currently open.
 *
 * This makes it much easier to always generate well formed XHTML output, even
 * if execution terminates abruptly. Any time you output some opening HTML
 * without the matching closing HTML, you should push the necessary close tags
 * onto the stack.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class xhtml_container_stack {

    /**
     * @var array Stores the list of open containers.
     */
    protected $opencontainers = array();

    /**
     * @var array In developer debug mode, stores a stack trace of all opens and
     * closes, so we can output helpful error messages when there is a mismatch.
     */
    protected $log = array();

    /**
     * @var boolean Store whether we are developer debug mode. We need this in
     * several places including in the destructor where we may not have access to $CFG.
     */
    protected $isdebugging;

    /**
     * Constructor
     */
    public function __construct() {
        global $CFG;
        $this->isdebugging = $CFG->debugdeveloper;
    }

    /**
     * Push the close HTML for a recently opened container onto the stack.
     *
     * @param string $type The type of container. This is checked when {@link pop()}
     *      is called and must match, otherwise a developer debug warning is output.
     * @param string $closehtml The HTML required to close the container.
     */
    public function push($type, $closehtml) {
        $container = new stdClass;
        $container->type = $type;
        $container->closehtml = $closehtml;
        if ($this->isdebugging) {
            $this->log('Open', $type);
        }
        array_push($this->opencontainers, $container);
    }

    /**
     * Pop the HTML for the next closing container from the stack. The $type
     * must match the type passed when the container was opened, otherwise a
     * warning will be output.
     *
     * @param string $type The type of container.
     * @return string the HTML required to close the container.
     */
    public function pop($type) {
        if (empty($this->opencontainers)) {
            debugging('<p>There are no more open containers. This suggests there is a nesting problem.</p>' .
                    $this->output_log(), DEBUG_DEVELOPER);
            return;
        }

        $container = array_pop($this->opencontainers);
        if ($container->type != $type) {
            debugging('<p>The type of container to be closed (' . $container->type .
                    ') does not match the type of the next open container (' . $type .
                    '). This suggests there is a nesting problem.</p>' .
                    $this->output_log(), DEBUG_DEVELOPER);
        }
        if ($this->isdebugging) {
            $this->log('Close', $type);
        }
        return $container->closehtml;
    }

    /**
     * Close all but the last open container. This is useful in places like error
     * handling, where you want to close all the open containers (apart from <body>)
     * before outputting the error message.
     *
     * @param bool $shouldbenone assert that the stack should be empty now - causes a
     *      developer debug warning if it isn't.
     * @return string the HTML required to close any open containers inside <body>.
     */
    public function pop_all_but_last($shouldbenone = false) {
        if ($shouldbenone && count($this->opencontainers) != 1) {
            debugging('<p>Some HTML tags were opened in the body of the page but not closed.</p>' .
                    $this->output_log(), DEBUG_DEVELOPER);
        }
        $output = '';
        while (count($this->opencontainers) > 1) {
            $container = array_pop($this->opencontainers);
            $output .= $container->closehtml;
        }
        return $output;
    }

    /**
     * You can call this function if you want to throw away an instance of this
     * class without properly emptying the stack (for example, in a unit test).
     * Calling this method stops the destruct method from outputting a developer
     * debug warning. After calling this method, the instance can no longer be used.
     */
    public function discard() {
        $this->opencontainers = null;
    }

    /**
     * Adds an entry to the log.
     *
     * @param string $action The name of the action
     * @param string $type The type of action
     */
    protected function log($action, $type) {
        $this->log[] = '<li>' . $action . ' ' . $type . ' at:' .
                format_backtrace(debug_backtrace()) . '</li>';
    }

    /**
     * Outputs the log's contents as a HTML list.
     *
     * @return string HTML list of the log
     */
    protected function output_log() {
        return '<ul>' . implode("\n", $this->log) . '</ul>';
    }
}

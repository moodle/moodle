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
 * @package    core
 * @subpackage lib
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/outputcomponents.php');
require_once($CFG->libdir.'/outputactions.php');
require_once($CFG->libdir.'/outputfactories.php');
require_once($CFG->libdir.'/outputrenderers.php');
require_once($CFG->libdir.'/outputrequirementslib.php');

/**
 * Invalidate all server and client side caches.
 * @return void
 */
function theme_reset_all_caches() {
    global $CFG;
    require_once("$CFG->libdir/filelib.php");

    set_config('themerev', empty($CFG->themerev) ? 1 : $CFG->themerev+1);
    fulldelete("$CFG->dataroot/cache/theme");
}

/**
 * Enable or disable theme designer mode.
 * @param bool $state
 * @return void
 */
function theme_set_designer_mod($state) {
    theme_reset_all_caches();
    set_config('themedesignermode', (int)!empty($state));
}

/**
 * Returns current theme revision number.
 * @return int
 */
function theme_get_revision() {
    global $CFG;

    if (empty($CFG->themedesignermode)) {
        if (empty($CFG->themerev)) {
            return -1;
        } else {
            return $CFG->themerev;
        }

    } else {
        return -1;
    }
}


/**
 * This class represents the configuration variables of a Moodle theme.
 *
 * All the variables with access: public below (with a few exceptions that are marked)
 * are the properties you can set in your theme's config.php file.
 *
 * There are also some methods and protected variables that are part of the inner
 * workings of Moodle's themes system. If you are just editing a theme's config.php
 * file, you can just ignore those, and the following information for developers.
 *
 * Normally, to create an instance of this class, you should use the
 * {@link theme_config::load()} factory method to load a themes config.php file.
 * However, normally you don't need to bother, because moodle_page (that is, $PAGE)
 * will create one for you, accessible as $PAGE->theme.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class theme_config {
    /**
     * @var string default theme, used when requested theme not found
     */
    const DEFAULT_THEME = 'standard';

    /**
     * You can base your theme on other themes by linking to the other theme as
     * parents. This lets you use the CSS and layouts from the other themes
     * (see {@link $layouts}).
     * That makes it easy to create a new theme that is similar to another one
     * but with a few changes. In this theme's CSS you only need to override
     * those rules you want to change.
     *
     * @var array
     */
    public $parents;

    /**
     * The names of all the stylesheets from this theme that you would
     * like included, in order. Give the names of the files without .css.
     *
     * @var array
     */
    public $sheets = array();

    /**
     * The names of all the stylesheets from parents that should be excluded.
     * true value may be used to specify all parents or all themes from one parent.
     * If no value specified value from parent theme used.
     *
     * @var array or arrays, true means all, null means use value from parent
     */
    public $parents_exclude_sheets = null;

    /**
     * List of plugin sheets to be excluded.
     * If no value specified value from parent theme used.
     *
     * @var array of full plugin names, null means use value from parent
     */
    public $plugins_exclude_sheets = null;

    /**
     * List of style sheets that are included in the text editor bodies.
     * Sheets from parent themes are used automatically and can not be excluded.
     *
     * @var array
     */
    public $editor_sheets = array();

    /**
     * The names of all the javascript files this theme that you would
     * like included from head, in order. Give the names of the files without .js.
     *
     * @var array
     */
    public $javascripts = array();

    /**
     * The names of all the javascript files this theme that you would
     * like included from footer, in order. Give the names of the files without .js.
     *
     * @var array
     */
    public $javascripts_footer = array();

    /**
     * The names of all the javascript files from parents that should be excluded.
     * true value may be used to specify all parents or all themes from one parent.
     * If no value specified value from parent theme used.
     *
     * @var array or arrays, true means all, null means use value from parent
     */
    public $parents_exclude_javascripts = null;

    /**
     * Which file to use for each page layout.
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
     *
     * @var array
     */
    public $layouts = array();

    /**
     * Name of the renderer factory class to use.
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
     *
     * @var string name of a class implementing the {@link renderer_factory} interface.
     */
    public $rendererfactory = 'standard_renderer_factory';

    /**
     * Function to do custom CSS post-processing.
     *
     * This is an advanced feature. If you want to do custom post-processing on the
     * CSS before it is output (for example, to replace certain variable names
     * with particular values) you can give the name of a function here.
     *
     * @var string the name of a function.
     */
    public $csspostprocess = null;

    /**
     * Accessibility: Right arrow-like character is
     * used in the breadcrumb trail, course navigation menu
     * (previous/next activity), calendar, and search forum block.
     * If the theme does not set characters, appropriate defaults
     * are set automatically. Please DO NOT
     * use &lt; &gt; &raquo; - these are confusing for blind users.
     *
     * @var string
     */
    public $rarrow = null;

    /**
     * Accessibility: Right arrow-like character is
     * used in the breadcrumb trail, course navigation menu
     * (previous/next activity), calendar, and search forum block.
     * If the theme does not set characters, appropriate defaults
     * are set automatically. Please DO NOT
     * use &lt; &gt; &raquo; - these are confusing for blind users.
     *
     * @var string
     */
    public $larrow = null;

    /**
     * Some themes may want to disable ajax course editing.
     * @var bool
     */
    public $enablecourseajax = true;

    //==Following properties are not configurable from theme config.php==

    /**
     * The name of this theme. Set automatically when this theme is
     * loaded. This can not be set in theme config.php
     * @var string
     */
    public $name;

    /**
     * the folder where this themes files are stored. This is set
     * automatically. This can not be set in theme config.php
     * @var string
     */
    public $dir;

    /**
     * Theme settings stored in config_plugins table.
     * This can not be set in theme config.php
     * @var object
     */
    public $setting = null;

    /**
     * If set to true and the theme enables the dock then  blocks will be able
     * to be moved to the special dock
     * @var bool
     */
    public $enable_dock = false;

    /**
     * If set to true then this theme will not be shown in the theme selector unless
     * theme designer mode is turned on.
     * @var bool
     */
    public $hidefromselector = false;

    /**
     * Instance of the renderer_factory implementation
     * we are using. Implementation detail.
     * @var renderer_factory
     */
    protected $rf = null;

    /**
     * List of parent config objects.
     * @var array list of parent configs
     **/
    protected $parent_configs = array();

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

        } else {
            // bad luck, the requested theme has some problems - admin see details in theme config
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

        if ($this->name != 'base') {
            $baseconfig = theme_config::find_theme_config('base', $this->settings);
        } else {
            $baseconfig = $config;
        }

        $configurable = array('parents', 'sheets', 'parents_exclude_sheets', 'plugins_exclude_sheets', 'javascripts', 'javascripts_footer',
                              'parents_exclude_javascripts', 'layouts', 'enable_dock', 'enablecourseajax',
                              'rendererfactory', 'csspostprocess', 'editor_sheets', 'rarrow', 'larrow', 'hidefromselector');

        foreach ($config as $key=>$value) {
            if (in_array($key, $configurable)) {
                $this->$key = $value;
            }
        }

        // verify all parents and load configs and renderers
        foreach ($this->parents as $parent) {
            if ($parent == 'base') {
                $parent_config = $baseconfig;
            } else if (!$parent_config = theme_config::find_theme_config($parent, $this->settings)) {
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
            $rendererfile = $parent_config->dir.'/renderers.php';
            if (is_readable($rendererfile)) {
                 // may contain core and plugin renderers and renderer factory
                include_once($rendererfile);
            }
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

    /*
     * Checks if arrows $THEME->rarrow, $THEME->larrow have been set (theme/-/config.php).
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
            if (empty($_SERVER['HTTP_USER_AGENT'])) {
                $uagent = '';
            } else {
                $uagent = $_SERVER['HTTP_USER_AGENT'];
            }
            if (false !== strpos($uagent, 'Opera')
                || false !== strpos($uagent, 'Mac')) {
                // Looks good in Win XP/Mac/Opera 8/9, Mac/Firefox 2, Camino, Safari.
                // Not broken in Mac/IE 5, Mac/Netscape 7 (?).
                $this->rarrow = '&#x25B6;';
                $this->larrow = '&#x25C0;';
            }
            elseif (false !== strpos($uagent, 'Konqueror')) {
                $this->rarrow = '&rarr;';
                $this->larrow = '&larr;';
            }
            elseif (isset($_SERVER['HTTP_ACCEPT_CHARSET'])
                && false === stripos($_SERVER['HTTP_ACCEPT_CHARSET'], 'utf-8')) {
                // (Win/IE 5 doesn't set ACCEPT_CHARSET, but handles Unicode.)
                // To be safe, non-Unicode browsers!
                $this->rarrow = '&gt;';
                $this->larrow = '&lt;';
            }

        /// RTL support - in RTL languages, swap r and l arrows
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
     * @param bool $encoded false means use & and true use &amp; in URLs
     * @return string
     */
    public function editor_css_url($encoded=true) {
        global $CFG;

        $rev = theme_get_revision();

        if ($rev > -1) {
            $params = array('theme'=>$this->name,'rev'=>$rev, 'type'=>'editor');
            return new moodle_url($CFG->httpswwwroot.'/theme/styles.php', $params);
        } else {
            $params = array('theme'=>$this->name, 'type'=>'editor');
            return new moodle_url($CFG->httpswwwroot.'/theme/styles_debug.php', $params);
        }
    }

    /**
     * Returns the content of the CSS to be used in editor content
     * @return string
     */
    public function editor_css_files() {
        global $CFG;

        $files = array();

        // first editor plugins
        $plugins = get_plugin_list('editor');
        foreach ($plugins as $plugin=>$fulldir) {
            $sheetfile = "$fulldir/editor_styles.css";
            if (is_readable($sheetfile)) {
                $files['plugin_'.$plugin] = $sheetfile;
            }
        }
        // then parent themes
        foreach (array_reverse($this->parent_configs) as $parent_config) { // base first, the immediate parent last
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
        // finally this theme
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
     * Get the stylesheet URL of this theme
     * @param bool $encoded false means use & and true use &amp; in URLs
     * @return array of moodle_url
     */
    public function css_urls(moodle_page $page) {
        global $CFG;

        $rev = theme_get_revision();

        $urls = array();

        if ($rev > -1) {
            if (check_browser_version('MSIE', 5)) {
                // We need to split the CSS files for IE
                $urls[] = new moodle_url($CFG->httpswwwroot.'/theme/styles.php', array('theme'=>$this->name,'rev'=>$rev, 'type'=>'plugins'));
                $urls[] = new moodle_url($CFG->httpswwwroot.'/theme/styles.php', array('theme'=>$this->name,'rev'=>$rev, 'type'=>'parents'));
                $urls[] = new moodle_url($CFG->httpswwwroot.'/theme/styles.php', array('theme'=>$this->name,'rev'=>$rev, 'type'=>'theme'));
            } else {
                $urls[] = new moodle_url($CFG->httpswwwroot.'/theme/styles.php', array('theme'=>$this->name,'rev'=>$rev));
            }
        } else {
            // find out the current CSS and cache it now for 5 seconds
            // the point is to construct the CSS only once and pass it through the
            // dataroot to the script that actually serves the sheets
            if (!defined('THEME_DESIGNER_CACHE_LIFETIME')) {
                define('THEME_DESIGNER_CACHE_LIFETIME', 4); // this can be also set in config.php
            }
            $candidatesheet = "$CFG->dataroot/cache/theme/$this->name/designer.ser";
            if (!file_exists($candidatesheet)) {
                $css = $this->css_content();
                check_dir_exists(dirname($candidatesheet));
                file_put_contents($candidatesheet, serialize($css));

            } else if (filemtime($candidatesheet) > time() - THEME_DESIGNER_CACHE_LIFETIME) {
                if ($css = file_get_contents($candidatesheet)) {
                    $css = unserialize($css);
                } else {
                    unlink($candidatesheet);
                    $css = $this->css_content();
                }

            } else {
                unlink($candidatesheet);
                $css = $this->css_content();
                file_put_contents($candidatesheet, serialize($css));
            }

            $baseurl = $CFG->httpswwwroot.'/theme/styles_debug.php';

            if (check_browser_version('MSIE', 5)) {
                // lalala, IE does not allow more than 31 linked CSS files from main document
                $urls[] = new moodle_url($baseurl, array('theme'=>$this->name, 'type'=>'ie', 'subtype'=>'plugins'));
                foreach ($css['parents'] as $parent=>$sheets) {
                    // We need to serve parents individually otherwise we may easily exceed the style limit IE imposes (4096)
                    $urls[] = new moodle_url($baseurl, array('theme'=>$this->name,'type'=>'ie', 'subtype'=>'parents', 'sheet'=>$parent));
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
                foreach ($css['theme'] as $sheet=>$unused) {
                    $urls[] = new moodle_url($baseurl, array('sheet'=>$sheet, 'theme'=>$this->name, 'type'=>'theme')); // sheet first in order to make long urls easier to read
                }
            }
        }

        return $urls;
    }

    /**
     * Returns an array of organised CSS files required for this output
     * @return array
     */
    public function css_files() {
        $cssfiles = array('plugins'=>array(), 'parents'=>array(), 'theme'=>array());

        // get all plugin sheets
        $excludes = $this->resolve_excludes('plugins_exclude_sheets');
        if ($excludes !== true) {
            foreach (get_plugin_types() as $type=>$unused) {
                if ($type === 'theme' || (!empty($excludes[$type]) and $excludes[$type] === true)) {
                    continue;
                }
                $plugins = get_plugin_list($type);
                foreach ($plugins as $plugin=>$fulldir) {
                    if (!empty($excludes[$type]) and is_array($excludes[$type])
                        and in_array($plugin, $excludes[$type])) {
                        continue;
                    }

                    $plugincontent = '';
                    $sheetfile = "$fulldir/styles.css";
                    if (is_readable($sheetfile)) {
                        $cssfiles['plugins'][$type.'_'.$plugin] = $sheetfile;
                    }
                    $sheetthemefile = "$fulldir/styles_{$this->name}.css";
                    if (is_readable($sheetthemefile)) {
                        $cssfiles['plugins'][$type.'_'.$plugin.'_'.$this->name] = $sheetthemefile;
                    }
                    }
                }
            }

        // find out wanted parent sheets
        $excludes = $this->resolve_excludes('parents_exclude_sheets');
        if ($excludes !== true) {
            foreach (array_reverse($this->parent_configs) as $parent_config) { // base first, the immediate parent last
                $parent = $parent_config->name;
                if (empty($parent_config->sheets) || (!empty($excludes[$parent]) and $excludes[$parent] === true)) {
                    continue;
                }
                foreach ($parent_config->sheets as $sheet) {
                    if (!empty($excludes[$parent]) and is_array($excludes[$parent])
                        and in_array($sheet, $excludes[$parent])) {
                        continue;
                    }
                    $sheetfile = "$parent_config->dir/style/$sheet.css";
                    if (is_readable($sheetfile)) {
                        $cssfiles['parents'][$parent][$sheet] = $sheetfile;
                    }
                }
            }
        }

        // current theme sheets
        if (is_array($this->sheets)) {
            foreach ($this->sheets as $sheet) {
                $sheetfile = "$this->dir/style/$sheet.css";
                if (is_readable($sheetfile)) {
                    $cssfiles['theme'][$sheet] = $sheetfile;
                }
            }
        }

        return $cssfiles;
    }

    /**
     * Returns the content of the one huge CSS merged from all style sheets.
     * @return string
     */
    public function css_content() {
        $files = array_merge($this->css_files(), array('editor'=>$this->editor_css_files()));
        $css = $this->css_files_get_contents($files, array());
        return $css;
    }

    /**
     * Given an array of file paths or a single file path loads the contents of
     * the CSS file, processes it then returns it in the same structure it was given.
     *
     * Can be used recursively on the results of {@see css_files}
     *
     * @param array|string $file An array of file paths or a single file path
     * @param array $keys An array of previous array keys [recursive addition]
     * @return The converted array or the contents of the single file ($file type)
     */
    protected function css_files_get_contents($file, array $keys) {
        if (is_array($file)) {
            foreach ($file as $key=>$f) {
                $file[$key] = $this->css_files_get_contents($f, array_merge($keys, array($key)));
            }
            return $file;
        } else {
            $comment = '/** Path: '.implode(' ', $keys).' **/'."\n";
            return $comment.$this->post_process(file_get_contents($file));
        }
    }


    /**
     * Get the javascript URL of this theme
     * @param bool $inhead true means head url, false means footer
     * @return moodle_url
     */
    public function javascript_url($inhead) {
        global $CFG;

        $rev = theme_get_revision();
        $params = array('theme'=>$this->name,'rev'=>$rev);
        $params['type'] = $inhead ? 'head' : 'footer';

        return new moodle_url($CFG->httpswwwroot.'/theme/javascript.php', $params);
    }

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
     * Resolves an exclude setting to the theme's setting is applicable or the
     * setting of its closest parent.
     *
     * @param string $variable The name of the setting the exclude setting to resolve
     * @return mixed
     */
    protected function resolve_excludes($variable, $default=null) {
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
     * @param bool $inhead
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

    public function post_process($css) {
        global $CFG;

        // now resolve all image locations
        if (preg_match_all('/\[\[pix:([a-z_]+\|)?([^\]]+)\]\]/', $css, $matches, PREG_SET_ORDER)) {
            $replaced = array();
            foreach ($matches as $match) {
                if (isset($replaced[$match[0]])) {
                    continue;
                }
                $replaced[$match[0]] = true;
                $imagename = $match[2];
                $component = rtrim($match[1], '|');
                $imageurl = $this->pix_url($imagename, $component)->out(false);
                 // we do not need full url because the image.php is always in the same dir
                $imageurl = str_replace("$CFG->httpswwwroot/theme/", '', $imageurl);
                $css = str_replace($match[0], $imageurl, $css);
            }
        }

        // now resolve all theme settings or do any other postprocessing
        $csspostprocess = $this->csspostprocess;
        if (function_exists($csspostprocess)) {
            $css = $csspostprocess($css, $this);
        }

        return $css;
    }

    /**
     * Return the URL for an image
     *
     * @param string $imagename the name of the icon.
     * @param string $component, specification of one plugin like in get_string()
     * @return moodle_url
     */
    public function pix_url($imagename, $component) {
        global $CFG;

        $params = array('theme'=>$this->name, 'image'=>$imagename);

        $rev = theme_get_revision();
        if ($rev != -1) {
            $params['rev'] = $rev;
        }
        if (!empty($component) and $component !== 'moodle'and $component !== 'core') {
            $params['component'] = $component;
        }

        return new moodle_url("$CFG->httpswwwroot/theme/image.php", $params);
    }

    /**
     * Resolves the real image location.
     * @param string $image name of image, may contain relative path
     * @param string $component
     * @return string full file path
     */
    public function resolve_image_location($image, $component) {
        global $CFG;

        if ($component === 'moodle' or $component === 'core' or empty($component)) {
            if ($imagefile = $this->image_exists("$this->dir/pix_core/$image")) {
                return $imagefile;
            }
            foreach (array_reverse($this->parent_configs) as $parent_config) { // base first, the immediate parent last
                if ($imagefile = $this->image_exists("$parent_config->dir/pix_core/$image")) {
                    return $imagefile;
                }
            }
            if ($imagefile = $this->image_exists("$CFG->dirroot/pix/$image")) {
                return $imagefile;
            }
            return null;

        } else if ($component === 'theme') { //exception
            if ($image === 'favicon') {
                return "$this->dir/pix/favicon.ico";
            }
            if ($imagefile = $this->image_exists("$this->dir/pix/$image")) {
                return $imagefile;
            }
            foreach (array_reverse($this->parent_configs) as $parent_config) { // base first, the immediate parent last
                if ($imagefile = $this->image_exists("$parent_config->dir/pix/$image")) {
                    return $imagefile;
                }
            }
            return null;

        } else {
            if (strpos($component, '_') === false) {
                $component = 'mod_'.$component;
            }
            list($type, $plugin) = explode('_', $component, 2);

            if ($imagefile = $this->image_exists("$this->dir/pix_plugins/$type/$plugin/$image")) {
                return $imagefile;
            }
            foreach (array_reverse($this->parent_configs) as $parent_config) { // base first, the immediate parent last
                if ($imagefile = $this->image_exists("$parent_config->dir/pix_plugins/$type/$plugin/$image")) {
                    return $imagefile;
                }
            }
            $dir = get_plugin_directory($type, $plugin);
            if ($imagefile = $this->image_exists("$dir/pix/$image")) {
                return $imagefile;
            }
            return null;
        }
    }

    /**
     * Checks if file with any image extension exists.
     * @param string $filepath
     * @return string image name with extension
     */
    private static function image_exists($filepath) {
        if (file_exists("$filepath.gif")) {
            return "$filepath.gif";
        } else  if (file_exists("$filepath.png")) {
            return "$filepath.png";
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
     * @param string $themename
     * @param object $settings from config_plugins table
     * @return object
     */
    private static function find_theme_config($themename, $settings) {
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
        }

        return $THEME;
    }

    /**
     * Finds the theme location and verifies the theme has all needed files
     * and is not obsoleted.
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
     * @param moodle_page $page the page we are rendering
     * @param string $module the name of part of moodle. E.g. 'core', 'quiz', 'qtype_multichoice'.
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
     * @param string $pagelayout the general type of the page.
     * @param block_manager $blockmanager the block_manger to set up.
     * @return void
     */
    public function setup_blocks($pagelayout, $blockmanager) {
        $layoutinfo = $this->layout_info_for_page($pagelayout);
        if (!empty($layoutinfo['regions'])) {
            $blockmanager->add_regions($layoutinfo['regions']);
            $blockmanager->set_default_region($layoutinfo['defaultregion']);
        }
    }

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

        // Last resort, try the base theme for names
        return get_string('region-' . $region, 'theme_base');
    }

    /**
     * Get the list of all block regions known to this theme in all templates.
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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class xhtml_container_stack {
    /** @var array stores the list of open containers. */
    protected $opencontainers = array();
    /**
     * @var array in developer debug mode, stores a stack trace of all opens and
     * closes, so we can output helpful error messages when there is a mismatch.
     */
    protected $log = array();
    /**
     * Store whether we are developer debug mode. We need this in several places
     * including in the destructor where we may not have access to $CFG.
     * @var boolean
     */
    protected $isdebugging;

    public function __construct() {
        $this->isdebugging = debugging('', DEBUG_DEVELOPER);
    }

    /**
     * Push the close HTML for a recently opened container onto the stack.
     * @param string $type The type of container. This is checked when {@link pop()}
     *      is called and must match, otherwise a developer debug warning is output.
     * @param string $closehtml The HTML required to close the container.
     * @return void
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
     * @return void
     */
    public function discard() {
        $this->opencontainers = null;
    }

    /**
     * Adds an entry to the log.
     * @param string $action The name of the action
     * @param string $type The type of action
     * @return void
     */
    protected function log($action, $type) {
        $this->log[] = '<li>' . $action . ' ' . $type . ' at:' .
                format_backtrace(debug_backtrace()) . '</li>';
    }

    /**
     * Outputs the log's contents as a HTML list.
     * @return string HTML list of the log
     */
    protected function output_log() {
        return '<ul>' . implode("\n", $this->log) . '</ul>';
    }
}

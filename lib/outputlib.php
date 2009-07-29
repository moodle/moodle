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
 * This constant is used for html attributes which need to have an empty
 * value and still be output by the renderers (e.g. alt="");
 *
 * @constant @EMPTY@
 */
define('HTML_ATTR_EMPTY', '@EMPTY@');

/**
 * Functions for generating the HTML that Moodle should output.
 *
 * Please see http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML
 * for an overview.
 *
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * A renderer factory is just responsible for creating an appropriate renderer
 * for any given part of Moodle.
 *
 * Which renderer factory to use is chose by the current theme, and an instance
 * if created automatically when the theme is set up.
 *
 * A renderer factory must also have a constructor that takes a theme_config object.
 * (See {@link renderer_factory_base::__construct} for an example.)
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
interface renderer_factory {
    /**
     * Return the renderer for a particular part of Moodle.
     *
     * The renderer interfaces are defined by classes called moodle_{plugin}_renderer
     * where {plugin} is the name of the component. The renderers for core Moodle are
     * defined in lib/renderer.php. For plugins, they will be defined in a file
     * called renderer.php inside the plugin.
     *
     * Renderers will normally want to subclass the moodle_renderer_base class.
     * (However, if you really know what you are doing, you don't have to do that.)
     *
     * There is no separate interface definition for renderers. The default
     * moodle_{plugin}_renderer implementation also serves to define the API for
     * other implementations of the interface, whether or not they subclass it.
     * For example, {@link custom_corners_core_renderer} does subclass
     * {@link moodle_core_renderer}. On the other hand, if you are using
     * {@link template_renderer_factory} then you always get back an instance
     * of the {@link template_renderer} class, whatever type of renderer you ask
     * for. This uses the fact that PHP is a dynamic language.
     *
     * A particular plugin can define multiple renderers if it wishes, using the
     * $subtype parameter. For example moodle_mod_workshop_renderer,
     * moodle_mod_workshop_allocation_manual_renderer etc.
     *
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer($component, $page, $subtype=null);
}


/**
 * An icon finder is responsible for working out the correct URL for an icon.
 *
 * A icon finder must also have a constructor that takes a theme object.
 * (See {@link standard_icon_finder::__construct} for an example.)
 *
 * Note that we are planning to change the Moodle icon naming convention before
 * the Moodle 2.0 release. Therefore, this API will probably change.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
interface icon_finder {
    /**
     * Return the URL for an icon identified as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->pixpath/i/course.gif";
     * then old_icon_url('i/course'); will return the equivalent URL that is correct now.
     *
     * @param string $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname);

    /**
     * Return the URL for an icon identified as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->modpixpath/$mod/icon.gif";
     * then mod_icon_url('icon', $mod); will return the equivalent URL that is correct now.
     *
     * @param string $iconname the name of the icon.
     * @param string $module the module the icon belongs to.
     * @return string the URL for that icon.
     */
    public function mod_icon_url($iconname, $module);
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
     * @var string the name of this theme. Set automatically when this theme is
     * loaded. Please do not try to set this in your theme's config.php file.
     */
    public $name;

    /**
     * @var string the folder where this themes files are stored. This is set
     * automatically when the theme is loaded to $CFG->themedir . '/' . $this->name.
     * Please do not try to set this in your theme's config.php file.
     */
    public $dir;

    /**
     * @var array The names of all the stylesheets from this theme that you would
     * like included, in order. Give the names of the files without .css.
     */
    public $sheets = array('styles_layout', 'styles_fonts', 'styles_color');

    /**
     * You can base your theme on another theme by linking to the other theme as
     * a parent. This lets you use the CSS from the other theme
     * (see {@link $parentsheets}), or layout templates (see {@link $layouts}).
     * That makes it easy to create a new theme that is similar to another one
     * but with a few changes. In this theme's CSS you only need to override
     * those rules you want to change.
     */
    public $parent = null;

    /**
     * @var boolean|array Whether and which stylesheets from the parent theme
     * to use in this theme. (Ignored if parent is null)
     *
     * Possible values are:
     *      false - no parent theme CSS.
     *      true - include all the normal parent theme CSS. Currently this means
     *             array('styles_layout', 'styles_fonts', 'styles_color').
     *      array - include just the listed stylesheets. Give the files names
     *              without the .css, as in the above example.
     */
    public $parentsheets = false;

    /**
     * @var boolean|array Whether and which stylesheets from the standard theme
     * to use in this theme.
     *
     * The advantages of using the standard stylesheets in your theme is that
     * they give you a good basic layout, and when the Moodle core code is
     * updated with new features, the standard theme CSS will be updated to match
     * the changes in the code. Therefore, your theme is less likely to break
     * when you upgrade Moodle.
     *
     * Possible values are:
     *      false - no standard theme CSS.
     *      true - include all the main standard theme CSS. Currently this means
     *             array('styles_layout', 'styles_fonts', 'styles_color').
     *      array - include just the listed stylesheets. Give the files names
     *              without the .css, as in the above example.
     */
    public $standardsheets = true;

    /**
     * @var array use the CSS fragments from these types of plugins.
     *
     * All the plugins of the given types will be searched for a file called
     * styles.php and, if found, these will be included with the CSS for this theme.
     *
     * This allows modules to provide some basic CSS so they work out of the box.
     * You are strongly advised to leave this enabled, otherwise you will have to
     * provide styling in your theme for every installed block, activity, course
     * format, ... in your Moodle site.
     *
     * This setting is an array of plugin types, as in the {@link get_plugin_types()}
     * function. The default value has been chosen to be the same as Moodle 1.9.
     * This is not necessarily the best choice.
     *
     * The plugin CSS is included first, before any theme CSS. To be precise,
     * if $standardsheets is true, the plugin CSS is included with the
     * standard theme's CSS, otherwise if $parentsheets is true, the plugin CSS
     * will be included with the parent theme's CSS, otherwise the plugin CSS
     * will be include with this theme's CSS.
     */
    public $pluginsheets = array('mod', 'block', 'format', 'gradereport');

    /**
     * @var boolean When this is true then Moodle will try to include a file
     * meta.php from this theme into the <head></head> part of the page.
     */
    public $metainclude = false;

    /**
     * @var boolean When this is true, and when this theme has a parent, then
     * Moodle will try to include a file meta.php from the parent theme into the
     * <head></head> part of the page.
     */
    public $parentmetainclude = false;

    /**
     * @var boolean When this is true then Moodle will try to include the file
     * meta.php from the standard theme into the <head></head> part of the page.
     */
    public $standardmetainclude = true;

    /**
     * If true, then this theme must have a "pix" subdirectory that contains
     * copies of all files from the moodle/pix directory, plus a "pix/mod"
     * directory containing all the icons for all the activity modules.
     *
     * @var boolean
     */
    public $custompix = false;

    /**
     * Which template to use for each general type of page.
     *
     * This is an array of arrays. The keys of the outer array are the different
     * types of page. Pages in Moodle are categorised into one of a short list of
     * types like 'normal', 'home', 'popup', 'form', .... The most reliable way
     * to get a complete list is to look at
     * {@link http://cvs.moodle.org/moodle/theme/standard/config.php?view=markup the standard theme config.php file}.
     * That file also has a good example of how to set this setting.
     *
     * If Moodle encounters a general type of page that is not listed in your theme,
     * then it will use the first layout. Therefore, should probably put 'normal'
     * first in this array.
     *
     * For each page type, the value in the outer array is an array that describes
     * how you want that type of page to look. For example
     * <pre>
     *   $THEME->layouts = array(
     *       // Most pages. Put this first, so if we encounter an unknown page type, this is used.
     *       'normal' => array(
     *           'layout' => 'parent:layout.php',
     *           'regions' => array('side-pre', 'side-post'),
     *           'defaultregion' => 'side-post'
     *       ),
     *       // The site home page.
     *       'home' => array(
     *           'layout' => 'layout-home.php',
     *           'regions' => array('side-pre', 'side-post'),
     *           'defaultregion' => 'side-post'
     *       ),
     *       // ...
     *   );
     * </pre>
     *
     * 'layout' is the layout template to use for this type of page. You can
     * specify this in one of three ways:
     * <ol>
     * <li><b>filename</b> for example 'layout-home.php' as above. Use that file from this theme.</li>
     * <li><b>parent:filename</b> for example 'parent:layout.php' as above. Use the
     *      specified file from the parent theme. (Obviously, you can only do this
     *      if this theme has a parent!)</li>
     * <li><b>standard:filename</b> for example 'standard:layout-popup.php'. Use
     *      the specified file from the standard theme.</li>
     * </ol>
     * To promote consistency, you are encouraged to call your layout files
     * layout.php or layout-something.php.
     *
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
     * With this you can control the colours of the big MP3 player
     * that is used for MP3 resources.
     *
     * @var string
     */
    public $resource_mp3player_colors = 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&font=Arial&fontColour=3333FF&buffer=10&waitForPlay=no&autoPlay=yes';

    /**
     *  With this you can control the colours of the small MP3 player
     * that is used elsewhere
     *.
     * @var string
     */
    public $filter_mediaplugin_colors = 'bgColour=000000&btnColour=ffffff&btnBorderColour=cccccc&iconColour=000000&iconOverColour=00cc00&trackColour=cccccc&handleColour=ffffff&loaderColour=ffffff&waitForPlay=yes';

    /**
     *$THEME->rarrow = '&#x25BA;' //OR '&rarr;';
     *$THEME->larrow = '&#x25C4;' //OR '&larr;';
     *$CFG->block_search_button = link_arrow_right(get_string('search'), $url='', $accesshide=true);
     *
     * Accessibility: Right and left arrow-like characters are
     * used in the breadcrumb trail, course navigation menu
     * (previous/next activity), calendar, and search forum block.
     *
     * If the theme does not set characters, appropriate defaults
     * are set by (lib/weblib.php:check_theme_arrows). The suggestions
     * above are 'silent' in a screen-reader like JAWS. Please DO NOT
     * use &lt; &gt; &raquo; - these are confusing for blind users.
     */

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
     *      your own custom renderers in a renderers.php file in this theme (or the parent theme).</li>
     * <li>{@link template_renderer_factory} - highly experimental! Do not use (yet).</li>
     * </ul>
     *
     * @var string name of a class implementing the {@link renderer_factory} interface.
     */
    public $rendererfactory = 'standard_renderer_factory';

    /**
     * Name of the icon finder class to use.
     *
     * This is an advanced feature. controls how Moodle converts from the icon
     * names used in the code to URLs to embed in the HTML. You should not ever
     * need to change this.
     *
     * @var string name of a class implementing the {@link icon_finder} interface.
     */
    public $iconfinder = 'pix_icon_finder';

    /**
     * Function to do custom CSS processing.
     *
     * This is an advanced feature. If you want to do custom processing on the
     * CSS before it is output (for example, to replace certain variable names
     * with particular values) you can give the name of a function here.
     *
     * There are two functions available that you may wish to use (defined in lib/outputlib.php):
     * <ul>
     * <li>{@link output_css_replacing_constants}</li>
     * <li>{@link output_css_for_css_edit}</li>
     * </ul>
     *
     * If you wish to write your own function, look at those two as examples,
     * and it should be clear what you have to do.
     *
     * @var string the name of a function.
     */
    public $customcssoutputfunction = null;

    /**
     * You can use this to control the cutoff point for strings
     * in the navmenus (list of activities in popup menu etc)
     * Default is 50 characters wide.
     */
    public $navmenuwidth = 50;

    /**
     * By setting this to true, then you will have access to a
     * new variable in your header.html and footer.html called
     * $navmenulist ... this contains a simple XHTML menu of
     * all activities in the current course, mostly useful for
     * creating popup navigation menus and so on.
     */
    public $makenavmenulist = false;

    /**
     * @var renderer_factory Instance of the renderer_factory implementation
     * we are using. Implementation detail.
     */
    protected $rf = null;

    /**
     * @var renderer_factory Instance of the icon_finder implementation we are
     * using. Implementation detail.
     */
    protected $if = null;

    /**
     * Load the config.php file for a particular theme, and return an instance
     * of this class. (That is, this is a factory method.)
     *
     * @param string $themename the name of the theme.
     * @return theme_config an instance of this class.
     */
    public static function load($themename) {
        global $CFG;

        // We have to use the variable name $THEME (upper case) because that
        // is what is used in theme config.php files.

        // Set some other standard properties of the theme.
        $THEME = new theme_config;
        $THEME->name = $themename;
        $THEME->dir = $CFG->themedir . '/' . $themename;

        // Load up the theme config
        $configfile = $THEME->dir . '/config.php';
        if (!is_readable($configfile)) {
            throw new coding_exception('Cannot use theme ' . $themename .
                    '. The file ' . $configfile . ' does not exist or is not readable.');
        }
        include($configfile);

        $THEME->update_legacy_information();

        return $THEME;
    }

    /**
     * Get the renderer for a part of Moodle for this theme.
     * @param string $module the name of part of moodle. E.g. 'core', 'quiz', 'qtype_multichoice'.
     * @param moodle_page $page the page we are rendering
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return moodle_renderer_base the requested renderer.
     */
    public function get_renderer($module, $page, $subtype=null) {
        if (is_null($this->rf)) {
            if (CLI_SCRIPT) {
                $classname = 'cli_renderer_factory';
            } else {
                $classname = $this->rendererfactory;
            }
            $this->rf = new $classname($this);
        }

        return $this->rf->get_renderer($module, $page, $subtype);
    }

    /**
     * Get the renderer for a part of Moodle for this theme.
     * @return moodle_renderer_base the requested renderer.
     */
    protected function get_icon_finder() {
        if (is_null($this->if)) {
            $classname = $this->iconfinder;
            $this->if = new $classname($this);
        }
        return $this->if;
    }

    /**
     * Return the URL for an icon identified as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->pixpath/i/course.gif";
     * then old_icon_url('i/course'); will return the equivalent URL that is correct now.
     *
     * @param string $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname) {
        return $this->get_icon_finder()->old_icon_url($iconname);
    }

    /**
     * Return the URL for an icon identified as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->modpixpath/$mod/icon.gif";
     * then mod_icon_url('icon', $mod); will return the equivalent URL that is correct now.
     *
     * @param string $iconname the name of the icon.
     * @param string $module the module the icon belongs to.
     * @return string the URL for that icon.
     */
    public function mod_icon_url($iconname, $module) {
        return $this->get_icon_finder()->mod_icon_url($iconname, $module);
    }

    /**
     * Get the list of stylesheet URLs that need to go in the header for this theme.
     * @return array of URLs.
     */
    public function get_stylesheet_urls() {
        global $CFG;

        // We need to tell the CSS that is being included (for example the standard
        // theme CSS) which theme it is being included for. Prepare the necessary param.
        $param = '?for=' . $this->name;

        // Stylesheets, in order (standard, parent, this - some of which may be the same).
        $stylesheets = array();
        if ($this->name != 'standard' && $this->standardsheets) {
            $stylesheets[] = $CFG->httpsthemewww . '/standard/styles.php' . $param;
        }
        if (!empty($this->parent)) {
            $stylesheets[] = $CFG->httpsthemewww . '/' . $this->parent . '/styles.php' . $param;
        }
        $stylesheets[] = $CFG->httpsthemewww . '/' . $this->name . '/styles.php' . $param;

        // Additional styles for right-to-left languages, if applicable.
        if (right_to_left()) {
            $stylesheets[] = $CFG->httpsthemewww . '/standard/rtl.css';

            if (!empty($this->parent) && file_exists($CFG->themedir . '/' . $this->parent . '/rtl.css')) {
                $stylesheets[] = $CFG->httpsthemewww . '/' . $this->parent . '/rtl.css';
            }

            if (file_exists($this->dir . '/rtl.css')) {
                $stylesheets[] = $CFG->httpsthemewww . '/' . $this->name . '/rtl.css';
            }
        }

        // If the theme wants pluginsheets, get them included in the first (most
        // general) stylesheet we are including. That is, process them with the
        // standard CSS if we are using that, else with the parent CSS, else with
        // our own CSS.
        if (!empty($this->pluginsheets)) {
            $stylesheets[0] .= '&amp;pluginsheets=1';
        }

        return $stylesheets;
    }

    /**
     * Get the meta tags from one theme to got in the <head> of the HTML.
     * @param string $themename the name of the theme to get meta tags from.
     * @param string $page that page whose <head> is being output.
     * @return string HTML code.
     */
    protected function get_theme_meta_tags($themename, $page) {
        global $CFG;
        // At least one theme's meta.php expects to have $PAGE visible.
        $PAGE = $page;
        $filename = $CFG->themedir . '/' . $themename . '/meta.php';
        if (file_exists($filename)) {
            ob_start();
            include_once($filename);
            $metatags = ob_get_contents();
            ob_end_clean();
        }
        return $metatags;
    }

    /**
     * Get all the meta tags (from this theme, standard, parent) that this theme
     * wants in the <head> of the HTML.
     *
     * @param string $page that page whose <head> is being output.
     * @return string HTML code.
     */
    public function get_meta_tags($page) {
        $metatags = '';
        if ($this->standardmetainclude) {
            $metatags .= $this->get_theme_meta_tags('standard', $page);
        }
        if ($this->parent && $this->parentmetainclude) {
            $metatags .= $this->get_theme_meta_tags($this->parent, $page);
        }
        if ($this->metainclude) {
            $metatags .= $this->get_theme_meta_tags($this->name, $page);
        }
        return $metatags;
    }

    /**
     * Get the information from {@link $layouts} for this type of page.
     * @param string $generaltype the general type of the page.
     * @return array the appropriate part of {@link $layouts}.
     */
    protected function layout_info_for_page($generaltype) {
        if (array_key_exists($generaltype, $this->layouts)) {
            return $this->layouts[$generaltype];
        } else {
            return reset($this->layouts);
        }
    }

    /**
     * Given the settings of this theme, and the page generaltype, return the
     * full path of the page layout template to use.
     *
     * Used by {@link moodle_core_renderer::header()}. If an appropriate new-style
     * template cannot be found, returns false to signal that the old-style
     * header.html and footer.html files should be used.
     *
     * @param string $generaltype the general type of the page.
     * @return string Full path to the template to use, or false if a new-style
     * template cannot be found.
     */
    public function template_for_page($generaltype) {
        global $CFG;

        // Legacy fallback.
        if (empty($this->layouts)) {
            return false;
        }

        $layoutinfo = $this->layout_info_for_page($generaltype);
        $templatefile = $layoutinfo['layout'];

        // Parse the name that was found.
        if (strpos($templatefile, 'standard:') === 0) {
            $templatepath = $CFG->themedir . '/standard/' . substr($templatefile, 9);
        } else if (strpos($templatefile, 'parent:') === 0) {
            if (empty($this->parent)) {
                throw new coding_exception('This theme (' . $this->name .
                        ') does not have a parent. You cannot specify a layout template like ' .
                        $templatefile);
            }
            $templatepath = $CFG->themedir . '/' . $this->parent . '/' . substr($templatefile, 7);
        } else {
            $templatepath = $this->dir . '/' . $templatefile;
        }

        // Check the template exists.
        if (!is_readable($templatepath)) {
            throw new coding_exception('The template ' . $templatefile . ' (' . $templatepath .
                    ') for page type ' . $generaltype . ' cannot be found in this theme (' .
                    $this->name . ')');
        }

        return $templatepath;
    }

    /**
     * Inform a block_manager about the block regions this theme wants on this
     * type of page.
     * @param string $generaltype the general type of the page.
     * @param block_manager $blockmanager the block_manger to set up.
     * @return void
     */
    public function setup_blocks($generaltype, $blockmanager) {
        // Legacy fallback.
        if (empty($this->layouts)) {
            if (!in_array($generaltype, array('form', 'popup', 'maintenance'))) {
                $blockmanager->add_regions(array(BLOCK_POS_LEFT, BLOCK_POS_RIGHT));
                $blockmanager->set_default_region(BLOCK_POS_RIGHT);
            }
            return;
        }

        $layoutinfo = $this->layout_info_for_page($generaltype);
        if (!empty($layoutinfo['regions'])) {
            $blockmanager->add_regions($layoutinfo['regions']);
            $blockmanager->set_default_region($layoutinfo['defaultregion']);
        }
    }

    /**
     * Get the list of all block regions known to this theme in all templates.
     * @return array internal region name => human readable name.
     */
    public function get_all_block_regions() {
        // Legacy fallback.
        if (empty($this->layouts)) {
            return array(
                'side-pre' => get_string('region-side-pre', 'theme_standard'),
                'side-post' => get_string('region-side-post', 'theme_standard'),
            );
        }

        $regions = array();
        foreach ($this->layouts as $layoutinfo) {
            $ownertheme = $this->name;
            if (strpos($layoutinfo['layout'], 'standard:') === 0) {
                $ownertheme = 'standard';
            } else if (strpos($layoutinfo['layout'], 'parent:') === 0) {
                $ownertheme = $this->parent;
            }

            foreach ($layoutinfo['regions'] as $region) {
                $regions[$region] = get_string('region-' . $region, 'theme_' . $ownertheme);
            }
        }
        return $regions;
    }

    /**
     * Helper method used by {@link update_legacy_information()}. Update one entry
     * in the $this->pluginsheets array, based on the legacy $property propery.
     * @param string $plugintype e.g. 'mod'.
     * @param string $property e.g. 'modsheets'.
     * @return void
     */
    protected function update_legacy_plugin_sheets($plugintype, $property) {
        // In Moodle 1.9, modsheets etc. were ignored if standardsheets was false.
        if (!empty($this->standardsheets) && property_exists($this, $property)) {
            debugging('$THEME->' . $property . ' is deprecated. Please use the new $THEME->pluginsheets instead.', DEBUG_DEVELOPER);
            if (!empty($this->$property) && !in_array($plugintype, $this->pluginsheets)) {
                $this->pluginsheets[] = $plugintype;
            } else if (empty($this->$property) && in_array($plugintype, $this->pluginsheets)) {
                unset($this->pluginsheets[array_search($plugintype, $this->pluginsheets)]);
            }
        }
    }

    /**
     * This method looks a the settings that have been loaded, to see whether
     * any legacy things are being used, and outputs warning and tries to update
     * things to use equivalent newer settings.
     * @return void
     */
    protected function update_legacy_information() {
        global $CFG;

        $this->update_legacy_plugin_sheets('mod', 'modsheets');
        $this->update_legacy_plugin_sheets('block', 'blocksheets');
        $this->update_legacy_plugin_sheets('format', 'formatsheets');
        $this->update_legacy_plugin_sheets('gradereport', 'gradereportsheets');

        if (!empty($this->langsheets)) {
            debugging('$THEME->langsheets is no longer supported. No languages were ' .
                    'using it for anything, and it did not seem to serve any purpose.', DEBUG_DEVELOPER);
        }

        if (!empty($this->customcorners)) {
            // $THEME->customcorners is deprecated but we provide support for it via the
            // custom_corners_renderer_factory class in lib/deprecatedlib.php
            debugging('$THEME->customcorners is deprecated. Please use the new $THEME->rendererfactory ' .
                    'to control HTML generation. Please use $this->rendererfactory = \'custom_corners_renderer_factory\'; ' .
                    'in your config.php file instead.', DEBUG_DEVELOPER);
            $this->rendererfactory = 'custom_corners_renderer_factory';
        }

        if (!empty($this->cssconstants)) {
            debugging('$THEME->cssconstants is deprecated. Please use ' .
                    '$THEME->customcssoutputfunction = \'output_css_replacing_constants\'; ' .
                    'in your config.php file instead.', DEBUG_DEVELOPER);
            $this->customcssoutputfunction = 'output_css_replacing_constants';
        }

        if (!empty($this->CSSEdit)) {
            debugging('$THEME->CSSEdit is deprecated. Please use ' .
                    '$THEME->customcssoutputfunction = \'output_css_for_css_edit\'; ' .
                    'in your config.php file instead.', DEBUG_DEVELOPER);
            $this->customcssoutputfunction = 'output_css_for_css_edit';
        }

        if (!empty($CFG->smartpix)) {
            $this->iconfinder = 'smartpix_icon_finder';
        } else if ($this->custompix) {
            $this->iconfinder = 'theme_icon_finder';
        }
    }

    /**
     * Set the variable $CFG->pixpath and $CFG->modpixpath to be the right
     * ones for this theme. These should no longer be used, but legacy code
     * might still rely on them.
     * @return void
     */
    public function setup_legacy_pix_paths() {
        global $CFG;
        if (!empty($CFG->smartpix)) {
            if ($CFG->slasharguments) {
                // Use this method if possible for better caching
                $extra = '';
            } else {
                $extra = '?file=';
            }
            $CFG->pixpath = $CFG->httpswwwroot . '/pix/smartpix.php' . $extra . '/' . $this->name;
            $CFG->modpixpath = $CFG->httpswwwroot . '/pix/smartpix.php' . $extra . '/' . $this->name . '/mod';

        } else if (empty($THEME->custompix)) {
            $CFG->pixpath = $CFG->httpswwwroot . '/pix';
            $CFG->modpixpath = $CFG->httpswwwroot . '/mod';

        } else {
            $CFG->pixpath = $CFG->httpsthemewww . '/' . $this->name . '/pix';
            $CFG->modpixpath = $CFG->httpsthemewww . '/' . $this->name . '/pix/mod';
        }
    }
}


/**
 * This icon finder implements the old scheme that was used when themes that had
 * $THEME->custompix = false.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class pix_icon_finder implements icon_finder {
    /**
     * Constructor
     * @param theme_config $theme the theme we are finding icons for (which is irrelevant).
     */
    public function __construct($theme) {
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname) {
        global $CFG;
        if (file_exists($CFG->dirroot . '/pix/' . $iconname . '.png')) {
            return $CFG->httpswwwroot . '/pix/' . $iconname . '.png';
        } else {
            return $CFG->httpswwwroot . '/pix/' . $iconname . '.gif';
        }
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @param string $module the module the icon belongs to.
     * @return string the URL for that icon.
     */
    public function mod_icon_url($iconname, $module) {
        global $CFG;
        if (file_exists($CFG->dirroot . '/mod/' . $module . '/' . $iconname . '.png')) {
            return $CFG->httpswwwroot . '/mod/' . $module . '/' . $iconname . '.png';
        } else {
            return $CFG->httpswwwroot . '/mod/' . $module . '/' . $iconname . '.gif';
        }
    }
}


/**
 * This icon finder implements the old scheme that was used for themes that had
 * $THEME->custompix = true.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class theme_icon_finder implements icon_finder {
    protected $themename;
    /**
     * Constructor
     * @param theme_config $theme the theme we are finding icons for.
     */
    public function __construct($theme) {
        $this->themename = $theme->name;
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname) {
        global $CFG;
        if (file_exists($CFG->themedir . '/' . $this->themename . '/pix/' . $iconname . '.png')) {
            return $CFG->httpsthemewww . '/' . $this->themename . '/pix/' . $iconname . '.png';
        } else {
            return $CFG->httpsthemewww . '/' . $this->themename . '/pix/' . $iconname . '.gif';
        }
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @param string $module the module the icon belongs to.
     * @return string the URL for that icon.
     */
    public function mod_icon_url($iconname, $module) {
        global $CFG;
        if (file_exists($CFG->themedir . '/' . $this->themename . '/pix/mod/' . $module . '/' . $iconname . '.png')) {
            return $CFG->httpsthemewww . '/' . $this->themename . '/pix/mod/' . $module . '/' . $iconname . '.png';
        } else {
            return $CFG->httpsthemewww . '/' . $this->themename . '/pix/mod/' . $module . '/' . $iconname . '.gif';
        }
    }
}


/**
 * This icon finder implements the algorithm in pix/smartpix.php.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class smartpix_icon_finder extends pix_icon_finder {
    protected $places = array();

    /**
     * Constructor
     * @param theme_config $theme the theme we are finding icons for.
     */
    public function __construct($theme) {
        global $CFG;
        $this->places[$CFG->themedir . '/' . $theme->name . '/pix/'] =
                $CFG->httpsthemewww . '/' . $theme->name . '/pix/';
        if (!empty($theme->parent)) {
            $this->places[$CFG->themedir . '/' . $theme->parent . '/pix/'] =
                    $CFG->httpsthemewww . '/' . $theme->parent . '/pix/';
        }
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname) {
        foreach ($this->places as $dirroot => $urlroot) {
            if (file_exists($dirroot . $iconname . '.png')) {
                return $dirroot . $iconname . '.png';
            } else if (file_exists($dirroot . $iconname . '.gif')) {
                return $dirroot . $iconname . '.gif';
            }
        }
        return parent::old_icon_url($iconname);
    }

    /**
     * Implement interface method.
     * @param string $iconname the name of the icon.
     * @param string $module the module the icon belongs to.
     * @return string the URL for that icon.
     */
    public function mod_icon_url($iconname, $module) {
        foreach ($this->places as $dirroot => $urlroot) {
            if (file_exists($dirroot . 'mod/' . $iconname . '.png')) {
                return $dirroot . 'mod/' . $iconname . '.png';
            } else if (file_exists($dirroot . 'mod/' . $iconname . '.gif')) {
                return $dirroot . 'mod/' . $iconname . '.gif';
            }
        }
        return parent::old_icon_url($iconname, $module);
    }
}


/**
 * This is a base class to help you implement the renderer_factory interface.
 *
 * It keeps a cache of renderers that have been constructed, so you only need
 * to construct each one once in you subclass.
 *
 * It also has a method to get the name of, and include the renderer.php with
 * the definition of, the standard renderer class for a given module.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
abstract class renderer_factory_base implements renderer_factory {
    /** @var theme_config the theme we belong to. */
    protected $theme;

    /**
     * Constructor.
     * @param theme_config $theme the theme we belong to.
     */
    public function __construct($theme) {
        $this->theme = $theme;
    }
    /**
     * For a given module name, return the name of the standard renderer class
     * that defines the renderer interface for that module.
     *
     * Also, if it exists, include the renderer.php file for that module, so
     * the class definition of the default renderer has been loaded.
     *
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return string the name of the standard renderer class for that module.
     */
    protected function standard_renderer_class_for_module($component, $subtype=null) {
        if ($component != 'core') {
            $pluginrenderer = get_component_directory($component) . '/renderer.php';
            if (file_exists($pluginrenderer)) {
                include_once($pluginrenderer);
            }
        }
        if (is_null($subtype)) {
            $class = 'moodle_' . $component . '_renderer';
        } else {
            $class = 'moodle_' . $component . '_' . $subtype . '_renderer';
        }
        if (!class_exists($class)) {
            throw new coding_exception('Request for an unknown renderer class ' . $class);
        }
        return $class;
    }
}


/**
 * This is the default renderer factory for Moodle. It simply returns an instance
 * of the appropriate standard renderer class.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class standard_renderer_factory extends renderer_factory_base {
    /**
     * Implement the subclass method
     * @param string $module name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer($module, $page, $subtype=null) {
        if ($module == 'core') {
            return new moodle_core_renderer($page);
        } else {
            $class = $this->standard_renderer_class_for_module($module, $subtype);
            return new $class($page, $this->get_renderer('core', $page));
        }
    }
}


/**
 * This is a slight variation on the standard_renderer_factory used by CLI scripts.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class cli_renderer_factory extends standard_renderer_factory {
    /**
     * Implement the subclass method
     * @param string $module name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer($module, $page, $subtype=null) {
        if ($module == 'core') {
            return new cli_core_renderer($page);
        } else {
            parent::get_renderer($module, $page, $subtype);
        }
    }
}


/**
 * This is renderer factory allows themes to override the standard renderers using
 * php code.
 *
 * It will load any code from theme/mytheme/renderers.php and
 * theme/parenttheme/renderers.php, if then exist. Then whenever you ask for
 * a renderer for 'component', it will create a mytheme_component_renderer or a
 * parenttheme_component_renderer, instead of a moodle_component_renderer,
 * if either of those classes exist.
 *
 * This generates the slightly different HTML that the custom_corners theme expects.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class theme_overridden_renderer_factory extends standard_renderer_factory {
    protected $prefixes = array();

    /**
     * Constructor.
     * @param object $theme the theme we are rendering for.
     */
    public function __construct($theme) {
        global $CFG;
        parent::__construct($theme);

        // Initialise $this->prefixes.
        $renderersfile = $theme->dir . '/renderers.php';
        if (is_readable($renderersfile)) {
            include_once($renderersfile);
            $this->prefixes[] = $theme->name . '_';
        }
        if (!empty($theme->parent)) {
            $renderersfile = $CFG->themedir .'/'. $theme->parent . '/renderers.php';
            if (is_readable($renderersfile)) {
                include_once($renderersfile);
                $this->prefixes[] = $theme->parent . '_';
            }
        }
    }

    /**
     * Implement the subclass method
     * @param string $module name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer($module, $page, $subtype=null) {
        foreach ($this->prefixes as $prefix) {
            if (is_null($subtype)) {
                $classname = $prefix . $module . '_renderer';
            } else {
                $classname = $prefix . $module . '_' . $subtype . '_renderer';
            }
            if (class_exists($classname)) {
                if ($module == 'core') {
                    return new $classname($page);
                } else {
                    return new $classname($page, $this->get_renderer('core', $page));
                }
            }
        }
        return parent::get_renderer($module, $page, $subtype);
    }
}


/**
 * This is renderer factory that allows you to create templated themes.
 *
 * This should be considered an experimental proof of concept. In particular,
 * the performance is probably not very good. Do not try to use in on a busy site
 * without doing careful load testing first!
 *
 * This renderer factory returns instances of {@link template_renderer} class
 * which which implement the corresponding renderer interface in terms of
 * templates. To use this your theme must have a templates folder inside it.
 * Then suppose the method moodle_core_renderer::greeting($name = 'world');
 * exists. Then, a call to $OUTPUT->greeting() will cause the template
 * /theme/yourtheme/templates/core/greeting.php to be rendered, with the variable
 * $name available. The greeting.php template might contain
 *
 * <pre>
 * <h1>Hello <?php echo $name ?>!</h1>
 * </pre>
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class template_renderer_factory extends renderer_factory_base {
    /**
     * An array of paths of where to search for templates. Normally this theme,
     * the parent theme then the standardtemplate theme. (If some of these do
     * not exist, or are the same as each other, then the list will be shorter.
     */
    protected $searchpaths = array();

    /**
     * Constructor.
     * @param object $theme the theme we are rendering for.
     */
    public function __construct($theme) {
        global $CFG;
        parent::__construct($theme);

        // Initialise $this->searchpaths.
        if ($theme->name != 'standardtemplate') {
            $templatesdir = $theme->dir . '/templates';
            if (is_dir($templatesdir)) {
                $this->searchpaths[] = $templatesdir;
            }
        }
        if (!empty($theme->parent)) {
            $templatesdir = $CFG->themedir .'/'. $theme->parent . '/templates';
            if (is_dir($templatesdir)) {
                $this->searchpaths[] = $templatesdir;
            }
        }
        $this->searchpaths[] = $CFG->themedir .'/standardtemplate/templates';
    }

    /**
     * Implement the subclass method
     * @param string $module name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param moodle_page $page the page the renderer is outputting content for.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @return object an object implementing the requested renderer interface.
     */
    public function get_renderer($module, $page, $subtype=null) {
        // Refine the list of search paths for this module.
        $searchpaths = array();
        foreach ($this->searchpaths as $rootpath) {
            $path = $rootpath . '/' . $module;
            if (!is_null($subtype)) {
                $path .= '/' . $subtype;
            }
            if (is_dir($path)) {
                $searchpaths[] = $path;
            }
        }

        // Create a template_renderer that copies the API of the standard renderer.
        $copiedclass = $this->standard_renderer_class_for_module($module, $subtype);
        return new template_renderer($copiedclass, $searchpaths, $page);
    }
}


/**
 * Simple base class for Moodle renderers.
 *
 * Tracks the xhtml_container_stack to use, which is passed in in the constructor.
 *
 * Also has methods to facilitate generating HTML output.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_renderer_base {
    /** @var xhtml_container_stack the xhtml_container_stack to use. */
    protected $opencontainers;
    /** @var moodle_page the page we are rendering for. */
    protected $page;

    /**
     * Constructor
     * @param moodle_page $page the page we are doing output for.
     */
    public function __construct($page) {
        $this->opencontainers = $page->opencontainers;
        $this->page = $page;
    }

    /**
     * Have we started output yet?
     * @return boolean true if the header has been printed.
     */
    public function has_started() {
        return $this->page->state >= moodle_page::STATE_IN_BODY;
    }

    /**
     * Outputs a tag with attributes and contents
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @param string $contents What goes between the opening and closing tags
     * @return string HTML fragment
     */
    protected function output_tag($tagname, $attributes, $contents) {
        return $this->output_start_tag($tagname, $attributes) . $contents .
                $this->output_end_tag($tagname);
    }

    /**
     * Outputs an opening tag with attributes
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    protected function output_start_tag($tagname, $attributes) {
        return '<' . $tagname . $this->output_attributes($attributes) . '>';
    }

    /**
     * Outputs a closing tag
     * @param string $tagname The name of tag ('a', 'img', 'span' etc.)
     * @return string HTML fragment
     */
    protected function output_end_tag($tagname) {
        return '</' . $tagname . '>';
    }

    /**
     * Outputs an empty tag with attributes
     * @param string $tagname The name of tag ('input', 'img', 'br' etc.)
     * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    protected function output_empty_tag($tagname, $attributes) {
        return '<' . $tagname . $this->output_attributes($attributes) . ' />';
    }

    /**
     * Outputs a HTML attribute and value
     * @param string $name The name of the attribute ('src', 'href', 'class' etc.)
     * @param string $value The value of the attribute
     * @return string HTML fragment
     */
    protected function output_attribute($name, $value) {
        $value = trim($value);
        if ($value == HTML_ATTR_EMPTY) {
            return ' ' . $name . '=""';
        } else if ($value || is_numeric($value)) { // We want 0 to be output.
            return ' ' . $name . '="' . $value . '"';
        }
    }

    /**
     * Outputs a list of HTML attributes and values
     * @param array $attributes The tag attributes (array('src' => $url, 'class' => 'class1') etc.)
     * @return string HTML fragment
     */
    protected function output_attributes($attributes) {
        if (empty($attributes)) {
            $attributes = array();
        }
        $output = '';
        foreach ($attributes as $name => $value) {
            $output .= $this->output_attribute($name, $value);
        }
        return $output;
    }

    /**
     * Given an array or space-separated list of classes, prepares and returns the HTML class attribute value
     * @param mixed $classes Space-separated string or array of classes
     * @return string HTML class attribute value
     */
    public static function prepare_classes($classes) {
        if (is_array($classes)) {
            return implode(' ', array_unique($classes));
        }
        return $classes;
    }

    /**
     * Return the URL for an icon identified as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->pixpath/i/course.gif";
     * then old_icon_url('i/course'); will return the equivalent URL that is correct now.
     *
     * @param string $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname) {
        return $this->page->theme->old_icon_url($iconname);
    }

    /**
     * Return the URL for an icon identified as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->modpixpath/$mod/icon.gif";
     * then mod_icon_url('icon', $mod); will return the equivalent URL that is correct now.
     *
     * @param string $iconname the name of the icon.
     * @param string $module the module the icon belongs to.
     * @return string the URL for that icon.
     */
    public function mod_icon_url($iconname, $module) {
        return $this->page->theme->mod_icon_url($iconname, $module);
    }

    /**
     * A helper function that takes a moodle_html_component subclass as param.
     * If that component has an id attribute and an array of valid component_action objects,
     * it sets up the appropriate event handlers.
     *
     * @param moodle_html_component $component
     * @return void;
     */
    protected function prepare_event_handlers(&$component) {
        $actions = $component->get_actions();
        if (!empty($actions) && is_array($actions) && $actions[0] instanceof component_action) {
            foreach ($actions as $action) {
                if (!empty($action->jsfunction)) {
                    $this->page->requires->event_handler($component->id, $action->event, $action->jsfunction, $action->jsfunctionargs);
                }
            }
        }
    }

    /**
     * Given a moodle_html_component with height and/or width set, translates them
     * to appropriate CSS rules.
     *
     * @param moodle_html_component $component
     * @return string CSS rules
     */
    protected function prepare_legacy_width_and_height($component) {
        $output = '';
        if (!empty($component->height)) {
            // We need a more intelligent way to handle these warnings. If $component->height have come from
            // somewhere in deprecatedlib.php, then there is no point outputting a warning here.
            // debugging('Explicit height given to moodle_html_component leads to inline css. Use a proper CSS class instead.', DEBUG_DEVELOPER);
            $output .= "height: {$component->height}px;";
        }
        if (!empty($component->width)) {
            // debugging('Explicit width given to moodle_html_component leads to inline css. Use a proper CSS class instead.', DEBUG_DEVELOPER);
            $output .= "width: {$component->width}px;";
        }
        return $output;
    }
}


/**
 * This is the templated renderer which copies the API of another class, replacing
 * all methods calls with instantiation of a template.
 *
 * When the method method_name is called, this class will search for a template
 * called method_name.php in the folders in $searchpaths, taking the first one
 * that it finds. Then it will set up variables for each of the arguments of that
 * method, and render the template. This is implemented in the {@link __call()}
 * PHP magic method.
 *
 * Methods like print_box_start and print_box_end are handles specially, and
 * implemented in terms of the print_box.php method.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class template_renderer extends moodle_renderer_base {
    /** @var ReflectionClass information about the class whose API we are copying. */
    protected $copiedclass;
    /** @var array of places to search for templates. */
    protected $searchpaths;
    protected $rendererfactory;

    /**
     * Magic word used when breaking apart container templates to implement
     * _start and _end methods.
     */
    const CONTENTSTOKEN = '-@#-Contents-go-here-#@-';

    /**
     * Constructor
     * @param string $copiedclass the name of a class whose API we should be copying.
     * @param array $searchpaths a list of folders to search for templates in.
     * @param moodle_page $page the page we are doing output for.
     */
    public function __construct($copiedclass, $searchpaths, $page) {
        parent::__construct($page);
        $this->copiedclass = new ReflectionClass($copiedclass);
        $this->searchpaths = $searchpaths;
    }

    /**
     * PHP magic method implementation. Do not use this method directly.
     * @param string $method The method to call
     * @param array $arguments The arguments to pass to the method
     * @return mixed The return value of the called method
     */
    public function __call($method, $arguments) {
        if (substr($method, -6) == '_start') {
            return $this->process_start(substr($method, 0, -6), $arguments);
        } else if (substr($method, -4) == '_end') {
            return $this->process_end(substr($method, 0, -4), $arguments);
        } else {
            return $this->process_template($method, $arguments);
        }
    }

    /**
     * Render the template for a given method of the renderer class we are copying,
     * using the arguments passed.
     * @param string $method the method that was called.
     * @param array $arguments the arguments that were passed to it.
     * @return string the HTML to be output.
     */
    protected function process_template($method, $arguments) {
        if (!$this->copiedclass->hasMethod($method) ||
                !$this->copiedclass->getMethod($method)->isPublic()) {
            throw new coding_exception('Unknown method ' . $method);
        }

        // Find the template file for this method.
        $template = $this->find_template($method);

        // Use the reflection API to find out what variable names the arguments
        // should be stored in, and fill in any missing ones with the defaults.
        $namedarguments = array();
        $expectedparams = $this->copiedclass->getMethod($method)->getParameters();
        foreach ($expectedparams as $param) {
            $paramname = $param->getName();
            if (!empty($arguments)) {
                $namedarguments[$paramname] = array_shift($arguments);
            } else if ($param->isDefaultValueAvailable()) {
                $namedarguments[$paramname] = $param->getDefaultValue();
            } else {
                throw new coding_exception('Missing required argument ' . $paramname);
            }
        }

        // Actually render the template.
        return $this->render_template($template, $namedarguments);
    }

    /**
     * Actually do the work of rendering the template.
     * @param string $_template the full path to the template file.
     * @param array $_namedarguments an array variable name => value, the variables
     *      that should be available to the template.
     * @return string the HTML to be output.
     */
    protected function render_template($_template, $_namedarguments) {
        // Note, we intentionally break the coding guidelines with regards to
        // local variable names used in this function, so that they do not clash
        // with the names of any variables being passed to the template.

        global $CFG, $SITE, $THEME, $USER;
        // The next lines are a bit tricky. The point is, here we are in a method
        // of a renderer class, and this object may, or may not, be the same as
        // the global $OUTPUT object. When rendering the template, we want to use
        // this object. However, people writing Moodle code expect the current
        // renderer to be called $OUTPUT, not $this, so define a variable called
        // $OUTPUT pointing at $this. The same comment applies to $PAGE and $COURSE.
        $OUTPUT = $this;
        $PAGE = $this->page;
        $COURSE = $this->page->course;

        // And the parameters from the function call.
        extract($_namedarguments);

        // Include the template, capturing the output.
        ob_start();
        include($_template);
        $_result = ob_get_contents();
        ob_end_clean();

        return $_result;
    }

    /**
     * Searches the folders in {@link $searchpaths} to try to find a template for
     * this method name. Throws an exception if one cannot be found.
     * @param string $method the method name.
     * @return string the full path of the template to use.
     */
    protected function find_template($method) {
        foreach ($this->searchpaths as $path) {
            $filename = $path . '/' . $method . '.php';
            if (file_exists($filename)) {
                return $filename;
            }
        }
        throw new coding_exception('Cannot find template for ' . $this->copiedclass->getName() . '::' . $method);
    }

    /**
     * Handle methods like print_box_start by using the print_box template,
     * splitting the result, pushing the end onto the stack, then returning the start.
     * @param string $method the method that was called, with _start stripped off.
     * @param array $arguments the arguments that were passed to it.
     * @return string the HTML to be output.
     */
    protected function process_start($method, $arguments) {
        array_unshift($arguments, self::CONTENTSTOKEN);
        $html = $this->process_template($method, $arguments);
        list($start, $end) = explode(self::CONTENTSTOKEN, $html, 2);
        $this->opencontainers->push($method, $end);
        return $start;
    }

    /**
     * Handle methods like print_box_end, we just need to pop the end HTML from
     * the stack.
     * @param string $method the method that was called, with _end stripped off.
     * @param array $arguments not used. Assumed to be irrelevant.
     * @return string the HTML to be output.
     */
    protected function process_end($method, $arguments) {
        return $this->opencontainers->pop($method);
    }

    /**
     * @return array the list of paths where this class searches for templates.
     */
    public function get_search_paths() {
        return $this->searchpaths;
    }

    /**
     * @return string the name of the class whose API we are copying.
     */
    public function get_copied_class() {
        return $this->copiedclass->getName();
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
     * including in the destructor where we may no thave access to $CFG.
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
     * Emergency fallback. If we get to the end of processing and not all
     * containers have been closed, output the rest with a developer debug warning.
     * @return void
     */
    public function __destruct() {
        if (empty($this->opencontainers)) {
            return;
        }

        // It seems you cannot rely on $CFG, and hence the debugging function here,
        // becuase $CFG may be destroyed before this object is.
        if ($this->isdebugging) {
            echo '<div class="notifytiny"><p>Some containers were left open. This suggests there is a nesting problem.</p>' .
                    $this->output_log() . '</div>';
        }
        echo $this->pop_all_but_last();
        $container = array_pop($this->opencontainers);
        echo $container->closehtml;
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


/**
 * The standard implementation of the moodle_core_renderer interface.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_core_renderer extends moodle_renderer_base {
    /** @var string used in {@link header()}. */
    const PERFORMANCE_INFO_TOKEN = '%%PERFORMANCEINFO%%';
    /** @var string used in {@link header()}. */
    const END_HTML_TOKEN = '%%ENDHTML%%';
    /** @var string used in {@link header()}. */
    const MAIN_CONTENT_TOKEN = '[MAIN CONTENT GOES HERE]';
    /** @var string used to pass information from {@link doctype()} to {@link standard_head_html()}. */
    protected $contenttype;
    /** @var string used by {@link redirect_message()} method to communicate with {@link header()}. */
    protected $metarefreshtag = '';

    /**
     * Get the DOCTYPE declaration that should be used with this page. Designed to
     * be called in theme layout.php files.
     * @return string the DOCTYPE declaration (and any XML prologue) that should be used.
     */
    public function doctype() {
        global $CFG;

        $doctype = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n";
        $this->contenttype = 'text/html; charset=utf-8';

        if (empty($CFG->xmlstrictheaders)) {
            return $doctype;
        }

        // We want to serve the page with an XML content type, to force well-formedness errors to be reported.
        $prolog = '<?xml version="1.0" encoding="utf-8"?>' . "\n";
        if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/xhtml+xml') !== false) {
            // Firefox and other browsers that can cope natively with XHTML.
            $this->contenttype = 'application/xhtml+xml; charset=utf-8';

        } else if (preg_match('/MSIE.*Windows NT/', $_SERVER['HTTP_USER_AGENT'])) {
            // IE can't cope with application/xhtml+xml, but it will cope if we send application/xml with an XSL stylesheet.
            $this->contenttype = 'application/xml; charset=utf-8';
            $prolog .= '<?xml-stylesheet type="text/xsl" href="' . $CFG->httpswwwroot . '/lib/xhtml.xsl"?>' . "\n";

        } else {
            $prolog = '';
        }

        return $prolog . $doctype;
    }

    /**
     * The attributes that should be added to the <html> tag. Designed to
     * be called in theme layout.php files.
     * @return string HTML fragment.
     */
    public function htmlattributes() {
        return get_html_lang(true) . ' xmlns="http://www.w3.org/1999/xhtml"';
    }

    /**
     * The standard tags (meta tags, links to stylesheets and JavaScript, etc.)
     * that should be included in the <head> tag. Designed to be called in theme
     * layout.php files.
     * @return string HTML fragment.
     */
    public function standard_head_html() {
        global $CFG;
        $output = '';
        $output .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
        $output .= '<meta name="keywords" content="moodle, ' . $this->page->title . '" />' . "\n";
        if (!$this->page->cacheable) {
            $output .= '<meta http-equiv="pragma" content="no-cache" />' . "\n";
            $output .= '<meta http-equiv="expires" content="0" />' . "\n";
        }
        // This is only set by the {@link redirect()} method
        $output .= $this->metarefreshtag;

        // Check if a periodic refresh delay has been set and make sure we arn't
        // already meta refreshing
        if ($this->metarefreshtag=='' && $this->page->periodicrefreshdelay!==null) {
            $output .= '<meta http-equiv="refresh" content="'.$this->page->periodicrefreshdelay.';url='.$this->page->url->out().'" />';
        }

        $this->page->requires->js('lib/javascript-static.js')->in_head();
        $this->page->requires->js('lib/javascript-deprecated.js')->in_head();
        $this->page->requires->js('lib/javascript-mod.php')->in_head();
        $this->page->requires->js('lib/overlib/overlib.js')->in_head();
        $this->page->requires->js('lib/overlib/overlib_cssstyle.js')->in_head();
        $this->page->requires->js('lib/cookies.js')->in_head();
        $this->page->requires->js_function_call('setTimeout', Array('fix_column_widths()', 20));

        $focus = $this->page->focuscontrol;
        if (!empty($focus)) {
            if (preg_match("#forms\['([a-zA-Z0-9]+)'\].elements\['([a-zA-Z0-9]+)'\]#", $focus, $matches)) {
                // This is a horrifically bad way to handle focus but it is passed in
                // through messy formslib::moodleform
                $this->page->requires->js_function_call('old_onload_focus', Array($matches[1], $matches[2]));
            } else if (strpos($focus, '.')!==false) {
                // Old style of focus, bad way to do it
                debugging('This code is using the old style focus event, Please update this code to focus on an element id or the moodleform focus method.', DEBUG_DEVELOPER);
                $this->page->requires->js_function_call('old_onload_focus', explode('.', $focus, 2));
            } else {
                // Focus element with given id
                $this->page->requires->js_function_call('focuscontrol', Array($focus));
            }
        }

        // Add the meta tags from the themes if any were requested.
        $output .= $this->page->theme->get_meta_tags($this->page);

        // Get any HTML from the page_requirements_manager.
        $output .= $this->page->requires->get_head_code();

        // List alternate versions.
        foreach ($this->page->alternateversions as $type => $alt) {
            $output .= $this->output_empty_tag('link', array('rel' => 'alternate',
                    'type' => $type, 'title' => $alt->title, 'href' => $alt->url));
        }

        return $output;
    }

    /**
     * The standard tags (typically skip links) that should be output just inside
     * the start of the <body> tag. Designed to be called in theme layout.php files.
     * @return string HTML fragment.
     */
    public function standard_top_of_body_html() {
        return  $this->page->requires->get_top_of_body_code();
    }

    /**
     * The standard tags (typically performance information and validation links,
     * if we are in developer debug mode) that should be output in the footer area
     * of the page. Designed to be called in theme layout.php files.
     * @return string HTML fragment.
     */
    public function standard_footer_html() {
        global $CFG;

        // This function is normally called from a layout.php file in {@link header()}
        // but some of the content won't be known until later, so we return a placeholder
        // for now. This will be replaced with the real content in {@link footer()}.
        $output = self::PERFORMANCE_INFO_TOKEN;
        if (!empty($CFG->debugpageinfo)) {
            $output .= '<div class="performanceinfo">This page is: ' . $this->page->debug_summary() . '</div>';
        }
        if (!empty($CFG->debugvalidators)) {
            $output .= '<div class="validators"><ul>
              <li><a href="http://validator.w3.org/check?verbose=1&amp;ss=1&amp;uri=' . urlencode(qualified_me()) . '">Validate HTML</a></li>
              <li><a href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=-1&amp;url1=' . urlencode(qualified_me()) . '">Section 508 Check</a></li>
              <li><a href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=0&amp;warnp2n3e=1&amp;url1=' . urlencode(qualified_me()) . '">WCAG 1 (2,3) Check</a></li>
            </ul></div>';
        }
        return $output;
    }

    /**
     * The standard tags (typically script tags that are not needed earlier) that
     * should be output after everything else, . Designed to be called in theme layout.php files.
     * @return string HTML fragment.
     */
    public function standard_end_of_body_html() {
        // This function is normally called from a layout.php file in {@link header()}
        // but some of the content won't be known until later, so we return a placeholder
        // for now. This will be replaced with the real content in {@link footer()}.
        echo self::END_HTML_TOKEN;
    }

    /**
     * Return the standard string that says whether you are logged in (and switched
     * roles/logged in as another user).
     * @return string HTML fragment.
     */
    public function login_info() {
        global $USER;
        return user_login_string($this->page->course, $USER);
    }

    /**
     * Return the 'back' link that normally appears in the footer.
     * @return string HTML fragment.
     */
    public function home_link() {
        global $CFG, $SITE;

        if ($this->page->pagetype == 'site-index') {
            // Special case for site home page - please do not remove
            return '<div class="sitelink">' .
                   '<a title="Moodle ' . $CFG->release . '" href="http://moodle.org/">' .
                   '<img style="width:100px;height:30px" src="' . $CFG->httpswwwroot . '/pix/moodlelogo.gif" alt="moodlelogo" /></a></div>';

        } else if (!empty($CFG->target_release) && $CFG->target_release != $CFG->release) {
            // Special case for during install/upgrade.
            return '<div class="sitelink">'.
                   '<a title="Moodle ' . $CFG->target_release . '" href="http://docs.moodle.org/en/Administrator_documentation" onclick="this.target=\'_blank\'">' .
                   '<img style="width:100px;height:30px" src="' . $CFG->httpswwwroot . '/pix/moodlelogo.gif" alt="moodlelogo" /></a></div>';

        } else if ($this->page->course->id == $SITE->id || strpos($this->page->pagetype, 'course-view') === 0) {
            return '<div class="homelink"><a href="' . $CFG->wwwroot . '/">' .
                    get_string('home') . '</a></div>';

        } else {
            return '<div class="homelink"><a href="' . $CFG->wwwroot . '/course/view.php?id=' . $this->page->course->id . '">' .
                    format_string($this->page->course->shortname) . '</a></div>';
        }
    }

    /**
     * Redirects the user by any means possible given the current state
     *
     * This function should not be called directly, it should always be called using
     * the redirect function in lib/weblib.php
     *
     * The redirect function should really only be called before page output has started
     * however it will allow itself to be called during the state STATE_IN_BODY
     *
     * @param string $encodedurl The URL to send to encoded if required
     * @param string $message The message to display to the user if any
     * @param int $delay The delay before redirecting a user, if $message has been
     *         set this is a requirement and defaults to 3, set to 0 no delay
     * @param boolean $debugdisableredirect this redirect has been disabled for
     *         debugging purposes. Display a message that explains, and don't
     *         trigger the redirect.
     * @return string The HTML to display to the user before dying, may contain
     *         meta refresh, javascript refresh, and may have set header redirects
     */
    public function redirect_message($encodedurl, $message, $delay, $debugdisableredirect) {
        global $CFG;
        $url = str_replace('&amp;', '&', $encodedurl);

        switch ($this->page->state) {
            case moodle_page::STATE_BEFORE_HEADER :
                // No output yet it is safe to delivery the full arsenal of redirect methods
                if (!$debugdisableredirect) {
                    // Don't use exactly the same time here, it can cause problems when both redirects fire at the same time.
                    $this->metarefreshtag = '<meta http-equiv="refresh" content="'. $delay .'; url='. $encodedurl .'" />'."\n";
                    $this->page->requires->js_function_call('document.location.replace', array($url))->after_delay($delay + 3);
                }
                $output = $this->header();
                break;
            case moodle_page::STATE_PRINTING_HEADER :
                // We should hopefully never get here
                throw new coding_exception('You cannot redirect while printing the page header');
                break;
            case moodle_page::STATE_IN_BODY :
                // We really shouldn't be here but we can deal with this
                debugging("You should really redirect before you start page output");
                if (!$debugdisableredirect) {
                    $this->page->requires->js_function_call('document.location.replace', array($url))->after_delay($delay);
                }
                $output = $this->opencontainers->pop_all_but_last();
                break;
            case moodle_page::STATE_DONE :
                // Too late to be calling redirect now
                throw new coding_exception('You cannot redirect after the entire page has been generated');
                break;
        }
        $output .= $this->notification($message, 'redirectmessage');
        $output .= '<a href="'. $encodedurl .'">'. get_string('continue') .'</a>';
        if ($debugdisableredirect) {
            $output .= '<p><strong>Error output, so disabling automatic redirect.</strong></p>';
        }
        $output .= $this->footer();
        return $output;
    }

    /**
     * Start output by sending the HTTP headers, and printing the HTML <head>
     * and the start of the <body>.
     *
     * To control what is printed, you should set properties on $PAGE. If you
     * are familiar with the old {@link print_header()} function from Moodle 1.9
     * you will find that there are properties on $PAGE that correspond to most
     * of the old parameters to could be passed to print_header.
     *
     * Not that, in due course, the remaining $navigation, $menu parameters here
     * will be replaced by more properties of $PAGE, but that is still to do.
     *
     * @param string $navigation legacy, like the old parameter to print_header. Will be
     *      removed when there is a $PAGE->... replacement.
     * @param string $menu legacy, like the old parameter to print_header. Will be
     *      removed when there is a $PAGE->... replacement.
     * @return string HTML that you must output this, preferably immediately.
     */
    public function header($navigation = '', $menu='') {
        // TODO remove $navigation and $menu arguments - replace with $PAGE->navigation
        global $USER, $CFG;

        $this->page->set_state(moodle_page::STATE_PRINTING_HEADER);

        // Find the appropriate page template, based on $this->page->generaltype.
        $templatefile = $this->page->theme->template_for_page($this->page->generaltype);
        if ($templatefile) {
            // Render the template.
            $template = $this->render_page_template($templatefile, $menu, $navigation);
        } else {
            // New style template not found, fall back to using header.html and footer.html.
            $template = $this->handle_legacy_theme($navigation, $menu);
        }

        // Slice the template output into header and footer.
        $cutpos = strpos($template, self::MAIN_CONTENT_TOKEN);
        if ($cutpos === false) {
            throw new coding_exception('Layout template ' . $templatefile .
                    ' does not contain the string "' . self::MAIN_CONTENT_TOKEN . '".');
        }
        $header = substr($template, 0, $cutpos);
        $footer = substr($template, $cutpos + strlen(self::MAIN_CONTENT_TOKEN));

        if (empty($this->contenttype)) {
            debugging('The layout template did not call $OUTPUT->doctype()');
            $this->doctype();
        }

        send_headers($this->contenttype, $this->page->cacheable);
        $this->opencontainers->push('header/footer', $footer);
        $this->page->set_state(moodle_page::STATE_IN_BODY);
        return $header . $this->skip_link_target();
    }

    /**
     * Renders and outputs the page template.
     * @param string $templatefile The name of the template's file
     * @param array $menu The menu that will be used in the included file
     * @param array $navigation The navigation that will be used in the included file
     * @return string HTML code
     */
    protected function render_page_template($templatefile, $menu, $navigation) {
        global $CFG, $SITE, $THEME, $USER;
        // The next lines are a bit tricky. The point is, here we are in a method
        // of a renderer class, and this object may, or may not, be the same as
        // the global $OUTPUT object. When rendering the template, we want to use
        // this object. However, people writing Moodle code expect the current
        // renderer to be called $OUTPUT, not $this, so define a variable called
        // $OUTPUT pointing at $this. The same comment applies to $PAGE and $COURSE.
        $OUTPUT = $this;
        $PAGE = $this->page;
        $COURSE = $this->page->course;

        ob_start();
        include($templatefile);
        $template = ob_get_contents();
        ob_end_clean();
        return $template;
    }

    /**
     * Renders and outputs a legacy template.
     * @param array $navigation The navigation that will be used in the included file
     * @param array $menu The menu that will be used in the included file
     * @return string HTML code
     */
    protected function handle_legacy_theme($navigation, $menu) {
        global $CFG, $SITE, $USER;
        // Set a pretend global from the properties of this class.
        // See the comment in render_page_template for a fuller explanation.
        $COURSE = $this->page->course;
        $THEME = $this->page->theme;

        // Set up local variables that header.html expects.
        $direction = $this->htmlattributes();
        $title = $this->page->title;
        $heading = $this->page->heading;
        $focus = $this->page->focuscontrol;
        $button = $this->page->button;
        $pageid = $this->page->pagetype;
        $pageclass = $this->page->bodyclasses;
        $bodytags = ' class="' . $pageclass . '" id="' . $pageid . '"';
        $home = $this->page->generaltype == 'home';

        $meta = $this->standard_head_html();
        // The next line is a nasty hack. having set $meta to standard_head_html, we have already
        // got the contents of include($CFG->javascript). However, legacy themes are going to
        // include($CFG->javascript) again. We want to make sure that when they do, nothing is output.
        $CFG->javascript = $CFG->libdir . '/emptyfile.php';

        // Set up local variables that footer.html expects.
        $homelink = $this->home_link();
        $loggedinas = $this->login_info();
        $course = $this->page->course;
        $performanceinfo = self::PERFORMANCE_INFO_TOKEN;

        if (!$menu && $navigation) {
            $menu = $loggedinas;
        }

        if (!empty($this->page->theme->layouttable)) {
            $lt = $this->page->theme->layouttable;
        } else {
            $lt = array('left', 'middle', 'right');
        }

        if (!empty($this->page->theme->block_l_max_width)) {
            $preferredwidthleft = $this->page->theme->block_l_max_width;
        } else {
            $preferredwidthleft = 210;
        }
        if (!empty($this->page->theme->block_r_max_width)) {
            $preferredwidthright = $this->page->theme->block_r_max_width;
        } else {
            $preferredwidthright = 210;
        }

        ob_start();
        include($this->page->theme->dir . '/header.html');

        echo '<table id="layout-table"><tr>';
        foreach ($lt as $column) {
            if ($column == 'left' && $this->page->blocks->region_has_content(BLOCK_POS_LEFT, $this)) {
                echo '<td id="left-column" class="block-region" style="width: ' . $preferredwidthright . 'px; vertical-align: top;">';
                echo $this->container_start();
                echo $this->blocks_for_region(BLOCK_POS_LEFT);
                echo $this->container_end();
                echo '</td>';

            } else if ($column == 'middle') {
                echo '<td id="middle-column" style="vertical-align: top;">';
                echo $this->container_start();
                echo $this->skip_link_target();
                echo self::MAIN_CONTENT_TOKEN;
                echo $this->container_end();
                echo '</td>';

            } else if ($column == 'right' && $this->page->blocks->region_has_content(BLOCK_POS_RIGHT, $this)) {
                echo '<td id="right-column" class="block-region" style="width: ' . $preferredwidthright . 'px; vertical-align: top;">';
                echo $this->container_start();
                echo $this->blocks_for_region(BLOCK_POS_RIGHT);
                echo $this->container_end();
                echo '</td>';
            }
        }
        echo '</tr></table>';

        $menu = str_replace('navmenu', 'navmenufooter', $menu);
        include($THEME->dir . '/footer.html');

        $output = ob_get_contents();
        ob_end_clean();

        // Put in the start of body code. Bit of a hack, put it in before the first
        // <div or <table.
        $divpos = strpos($output, '<div');
        $tablepos = strpos($output, '<table');
        if ($divpos === false || ($tablepos !== false && $tablepos < $divpos)) {
            $pos = $tablepos;
        } else {
            $pos = $divpos;
        }
        $output = substr($output, 0, $divpos) . $this->standard_top_of_body_html() .
                substr($output, $divpos);

        // Put in the end token before the end of body.
        $output = str_replace('</body>', self::END_HTML_TOKEN . '</body>', $output);

        // Make sure we use the correct doctype.
        $output = preg_replace('/(<!DOCTYPE.+?>)/s', $this->doctype(), $output);

        return $output;
    }

    /**
     * Outputs the page's footer
     * @return string HTML fragment
     */
    public function footer() {
        $output = $this->opencontainers->pop_all_but_last(true);

        $footer = $this->opencontainers->pop('header/footer');

        // Provide some performance info if required
        $performanceinfo = '';
        if (defined('MDL_PERF') || (!empty($CFG->perfdebug) and $CFG->perfdebug > 7)) {
            $perf = get_performance_info();
            if (defined('MDL_PERFTOLOG') && !function_exists('register_shutdown_function')) {
                error_log("PERF: " . $perf['txt']);
            }
            if (defined('MDL_PERFTOFOOT') || debugging() || $CFG->perfdebug > 7) {
                $performanceinfo = $perf['html'];
            }
        }
        $footer = str_replace(self::PERFORMANCE_INFO_TOKEN, $performanceinfo, $footer);

        $footer = str_replace(self::END_HTML_TOKEN, $this->page->requires->get_end_code(), $footer);

        $this->page->set_state(moodle_page::STATE_DONE);


        return $output . $footer;
    }

    /**
     * Output the row of editing icons for a block, as defined by the controls array.
     * @param array $controls an array like {@link block_contents::$controls}.
     * @return HTML fragment.
     */
    public function block_controls($controls) {
        if (empty($controls)) {
            return '';
        }
        $controlshtml = array();
        foreach ($controls as $control) {
            $controlshtml[] = $this->output_tag('a', array('class' => 'icon',
                    'title' => $control['caption'], 'href' => $control['url']),
                    $this->output_empty_tag('img',  array('src' => $this->old_icon_url($control['icon']),
                    'alt' => $control['caption'])));
        }
        return $this->output_tag('div', array('class' => 'commands'), implode('', $controlshtml));
    }

    /**
     * Prints a nice side block with an optional header.
     *
     * The content is described
     * by a {@link block_contents} object.
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    function block($bc, $region) {
        $bc = clone($bc); // Avoid messing up the object passed in.
        $bc->prepare();

        $skiptitle = strip_tags($bc->title);
        if (empty($skiptitle)) {
            $output = '';
            $skipdest = '';
        } else {
            $output = $this->output_tag('a', array('href' => '#sb-' . $bc->skipid, 'class' => 'skip-block'),
                    get_string('skipa', 'access', $skiptitle));
            $skipdest = $this->output_tag('span', array('id' => 'sb-' . $bc->skipid, 'class' => 'skip-block-to'), '');
        }

        $bc->attributes['id'] = $bc->id;
        $bc->attributes['class'] = $bc->get_classes_string();
        $output .= $this->output_start_tag('div', $bc->attributes);

        $controlshtml = $this->block_controls($bc->controls);

        $title = '';
        if ($bc->title) {
            $title = $this->output_tag('h2', null, $bc->title);
        }

        if ($title || $controlshtml) {
            $output .= $this->output_tag('div', array('class' => 'header'),
                    $this->output_tag('div', array('class' => 'title'),
                    $title . $controlshtml));
        }

        $output .= $this->output_start_tag('div', array('class' => 'content'));
        $output .= $bc->content;

        if ($bc->footer) {
            $output .= $this->output_tag('div', array('class' => 'footer'), $bc->footer);
        }

        $output .= $this->output_end_tag('div');
        $output .= $this->output_end_tag('div');
        if ($bc->annotation) {
            $output .= $this->output_tag('div', array('class' => 'blockannotation'), $bc->annotation);
        }
        $output .= $skipdest;

        $this->init_block_hider_js($bc);
        return $output;
    }

    /**
     * Calls the JS require function to hide a block.
     * @param block_contents $bc A block_contents object
     * @return void
     */
    protected function init_block_hider_js($bc) {
        if ($bc->collapsible != block_contents::NOT_HIDEABLE) {
            $userpref = 'block' . $bc->blockinstanceid . 'hidden';
            user_preference_allow_ajax_update($userpref, PARAM_BOOL);
            $this->page->requires->yui_lib('dom');
            $this->page->requires->yui_lib('event');
            $plaintitle = strip_tags($bc->title);
            $this->page->requires->js_function_call('new block_hider', array($bc->id, $userpref,
                    get_string('hideblocka', 'access', $plaintitle), get_string('showblocka', 'access', $plaintitle),
                    $this->old_icon_url('t/switch_minus'), $this->old_icon_url('t/switch_plus')));
        }
    }

    /**
     * Render the contents of a block_list.
     * @param array $icons the icon for each item.
     * @param array $items the content of each item.
     * @return string HTML
     */
    public function list_block_contents($icons, $items) {
        $row = 0;
        $lis = array();
        foreach ($items as $key => $string) {
            $item = $this->output_start_tag('li', array('class' => 'r' . $row));
            if ($icons) {
                $item .= $this->output_tag('div', array('class' => 'icon column c0'), $icons[$key]);
            }
            $item .= $this->output_tag('div', array('class' => 'column c1'), $string);
            $item .= $this->output_end_tag('li');
            $lis[] = $item;
            $row = 1 - $row; // Flip even/odd.
        }
        return $this->output_tag('ul', array('class' => 'list'), implode("\n", $lis));
    }

    /**
     * Output all the blocks in a particular region.
     * @param string $region the name of a region on this page.
     * @return string the HTML to be output.
     */
    public function blocks_for_region($region) {
        $blockcontents = $this->page->blocks->get_content_for_region($region, $this);
        $output = '';
        foreach ($blockcontents as $bc) {
            $output .= $this->block($bc, $region);
        }
        return $output;
    }

    /**
     * Given a html_textarea object, outputs an <a> tag that uses the object's attributes.
     *
     * @param mixed $link A html_link object or a string URL (text param required in second case)
     * @param string $text A descriptive text for the link. If $link is a html_link, this is not required.
     * @return string HTML fragment
     */
    public function textarea($textarea) {

    }

    /**
     * Given a html_link object, outputs an <a> tag that uses the object's attributes.
     *
     * @param mixed $link A html_link object or a string URL (text param required in second case)
     * @param string $text A descriptive text for the link. If $link is a html_link, this is not required.
     * @return string HTML fragment
     */
    public function link($link, $text=null) {
        $attributes = array('href' => $link);

        if (is_a($link, 'html_link')) {
            $link->prepare();
            $attributes['href'] = prepare_url($link->url);
            $text = $link->text;

            $attributes['class'] = $link->get_classes_string();
            $attributes['title'] = $link->title;
            $attributes['id'] = $link->id;

        } else if (empty($text)) {
            throw new coding_exception('$OUTPUT->link() must have a string as second parameter if the first param ($link) is a string');
        }

        return $this->output_tag('a', $attributes, $text);
    }

   /**
    * Print a message along with button choices for Continue/Cancel. Labels default to Yes(Continue)/No(Cancel).
    * If a string or moodle_url is given instead of a html_button, method defaults to post and text to Yes/No
    * @param string $message The question to ask the user
    * @param mixed $continue The html_form component representing the Continue answer. Can also be a moodle_url or string URL
    * @param mixed $cancel The html_form component representing the Cancel answer. Can also be a moodle_url or string URL
    * @return string HTML fragment
    */
    public function confirm($message, $continue, $cancel) {
        if (!($continue instanceof html_form) && !is_object($continue)) {
            $continueform = new html_form();
            $continueform->url = new moodle_url($continue);
            $continue = $continueform;
        } else if (!is_object($continue)) {
            throw new coding_exception('The 2nd param to $OUTPUT->confirm must be either a URL (string/moodle_url) or a html_form object.');
        }

        if (!($cancel instanceof html_form) && !is_object($cancel)) {
            $cancelform = new html_form();
            $cancelform->url = new moodle_url($cancel);
            $cancel = $cancelform;
        } else if (!is_object($cancel)) {
            throw new coding_exception('The 3rd param to $OUTPUT->confirm must be either a URL (string/moodle_url) or a html_form object.');
        }

        if (empty($continue->button->text)) {
            $continue->button->text = get_string('yes');
        }
        if (empty($cancel->button->text)) {
            $cancel->button->text = get_string('no');
        }

        $output = $this->box_start('generalbox', 'notice');
        $output .= $this->output_tag('p', array(), $message);
        $output .= $this->output_tag('div', array('class' => 'buttons'), $this->button($continue) . $this->button($cancel));
        $output .= $this->box_end();
        return $output;
    }

    /**
     * Given a html_form object, outputs an <input> tag within a form that uses the object's attributes.
     *
     * @param html_form $form A html_form object
     * @return string HTML fragment
     */
    public function button($form) {
        if (empty($form->button) or !($form->button instanceof html_button)) {
            throw new coding_exception('$OUTPUT->button($form) requires $form to have a button (html_button) value');
        }

        $form->button->prepare();

        $this->prepare_event_handlers($form->button);

        $buttonattributes = array('class' => $form->button->get_classes_string(),
                                  'type' => 'submit',
                                  'value' => $form->button->text,
                                  'disabled' => $form->button->disabled,
                                  'id' => $form->button->id);

        $buttonoutput = $this->output_empty_tag('input', $buttonattributes);

        return $this->form($form, $buttonoutput);
    }

    /**
     * Given a html_form component and an optional rendered submit button,
     * outputs a HTML form with correct divs and inputs and a single submit button.
     * This doesn't render any other visible inputs. Use moodleforms for these.
     * @param html_form $form A html_form instance
     * @param string $contents HTML fragment to put inside the form. If given, must contain at least the submit button.
     * @return string HTML fragment
     */
    public function form($form, $contents=null) {
        $form->prepare();

        $this->prepare_event_handlers($form);

        if (empty($contents)) {
            $contents = $this->output_empty_tag('input', array('type' => 'submit', 'value' => get_string('ok')));
        }

        $hiddenoutput = '';

        foreach ($form->url->params() as $var => $val) {
            $hiddenoutput .= $this->output_empty_tag('input', array('type' => 'hidden', 'name' => $var, 'value' => $val));
        }

        $formattributes = array(
                'method' => $form->method,
                'action' => prepare_url($form->url, true),
                'id' => $form->id,
                'class' => $form->get_classes_string());

        $divoutput = $this->output_tag('div', array(), $hiddenoutput . $contents);
        $formoutput = $this->output_tag('form', $formattributes, $divoutput);
        $output = $this->output_tag('div', array('class' => 'singlebutton'), $formoutput);

        return $output;
    }

    /**
     * Given a action_icon object, outputs an image linking to an action (URL or AJAX).
     *
     * @param action_icon $icon An action_icon object
     * @return string HTML fragment
     */
    public function action_icon($icon) {

        $icon->prepare();

        $this->prepare_event_handlers($icon);

        $imageoutput = $this->image($icon->image);

        if ($icon->linktext) {
            $imageoutput .= $icon->linktext;
        }

        $icon->link->text = $imageoutput;

        return $this->link($icon->link);
    }

    /**
     * Print a help icon.
     *
     * @param help_icon $helpicon A help_icon object, subclass of html_link
     *
     * @return string  HTML fragment
     */
    public function help_icon($icon) {
        global $COURSE;

        $icon->prepare();

        $popup = new popup_action('click', $icon->link->url);
        $icon->link->add_action($popup);

        $image = null;

        if (!empty($icon->image)) {
            $image = $icon->image;
            $image->add_class('iconhelp');
        }

        return $this->output_tag('span', array('class' => 'helplink'), $this->link_to_popup($icon->link, $image));
    }

    /**
     * Creates and returns a button to a popup window
     *
     * @param html_link $link Subclass of moodle_html_component
     * @param moodle_popup $popup A moodle_popup object
     * @param html_image $image An optional image replacing the link text
     *
     * @return string HTML fragment
     */
    public function link_to_popup($link, $image=null) {
        $link->prepare();

        $this->prepare_event_handlers($link);

        if (empty($link->url)) {
            throw new coding_exception('Called $OUTPUT->link_to_popup($link) method without $link->url set.');
        }

        $linkurl = prepare_url($link->url);

        $tagoptions = array(
                'title' => $link->title,
                'id' => $link->id,
                'href' => ($linkurl) ? $linkurl : prepare_url($popup->url),
                'class' => $link->get_classes_string());

        // Use image if one is given
        if (!empty($image) && $image instanceof html_image) {

            if (empty($image->alt)) {
                $image->alt = $link->text;
            }

            $link->text = $this->image($image);

            if (!empty($link->linktext)) {
                $link->text = "$link->title &nbsp; $link->text";
            }
        }

        return $this->output_tag('a', $tagoptions, $link->text);
    }

    /**
     * Creates and returns a spacer image with optional line break.
     *
     * @param html_image $image Subclass of moodle_html_component
     *
     * @return string HTML fragment
     */
    public function spacer($image) {
        $image->prepare();
        $image->add_class('spacer');

        if (empty($image->src)) {
            $image->src = $this->old_icon_url('spacer');
        }

        $output = $this->image($image);

        return $output;
    }

    /**
     * Creates and returns an image.
     *
     * @param html_image $image Subclass of moodle_html_component
     *
     * @return string HTML fragment
     */
    public function image($image) {
        $image->prepare();

        $attributes = array('class' => $image->get_classes_string(),
                            'style' => $this->prepare_legacy_width_and_height($image),
                            'src' => prepare_url($image->src),
                            'alt' => $image->alt,
                            'title' => $image->title);

        return $this->output_empty_tag('img', $attributes);
    }

    /**
     * Print the specified user's avatar.
     *
     * This method can be used in two ways:
     * <pre>
     * // Option 1:
     * $userpic = new user_picture();
     * // Set properties of $userpic
     * $OUTPUT->user_picture($userpic);
     *
     * // Option 2: (shortcut for simple cases)
     * // $user has come from the DB and has fields id, picture, imagealt, firstname and lastname
     * $OUTPUT->user_picture($user, $COURSE->id);
     * </pre>
     *
     * @param object $userpic Object with at least fields id, picture, imagealt, firstname, lastname
     *     If any of these are missing, or if a userid is passed, the database is queried. Avoid this
     *     if at all possible, particularly for reports. It is very bad for performance.
     *     A user_picture object is a better parameter.
     * @param int $courseid courseid Used when constructing the link to the user's profile. Required if $userpic
     *     is not a user_picture object
     * @return string HTML fragment
     */
    public function user_picture($userpic, $courseid=null) {
        // Instantiate a user_picture object if $user is not already one
        if (!($userpic instanceof user_picture)) {
            if (empty($courseid)) {
                throw new coding_exception('Called $OUTPUT->user_picture with a $user object but no $courseid.');
            }

            $user = $userpic;
            $userpic = new user_picture();
            $userpic->user = $user;
            $userpic->courseid = $courseid;
        }

        $userpic->prepare();

        $output = $this->image($userpic->image);

        if (!empty($userpic->link) && !empty($userpic->url)) {
            $actions = $userpic->get_actions();
            if (!empty($actions)) {
                $link = new html_link();
                $link->url = $userpic->url;
                $link->text = fullname($userpic->user);
                $link->add_action($actions[0]);
                $output = $this->link_to_popup($link);
            } else {
                $output = $this->link(prepare_url($userpic->url), $output);
            }
        }

        return $output;
    }

    /**
     * Outputs a HTML nested list
     *
     * @param html_list $list A html_list object
     * @return string HTML structure
     */
    public function htmllist($list) {
        $list->prepare();

        $this->prepare_event_handlers($list);

        if ($list->type == 'ordered') {
            $tag = 'ol';
        } else if ($list->type == 'unordered') {
            $tag = 'ul';
        }

        $output = $this->output_start_tag($tag, array('class' => $list->get_classes_string()));

        foreach ($list->items as $listitem) {
            if ($listitem instanceof html_list) {
                $output .= $this->output_start_tag('li');
                $output .= $this->htmllist($listitem);
                $output .= $this->output_end_tag('li');
            } else if ($listitem instanceof html_list_item) {
                $listitem->prepare();
                $this->prepare_event_handlers($listitem);
                $output .= $this->output_tag('li', array('class' => $listitem->get_classes_string()), $listitem->value);
            }
        }

        return $output . $this->output_end_tag($tag);
    }

    public function close_window_button($buttontext = null, $reloadopener = false) {
        if (empty($buttontext)) {
            $buttontext = get_string('closewindow');
        }
        // TODO
    }

    public function close_window($delay = 0, $reloadopener = false) {
        // TODO
    }

    /**
     * Output a <select> menu.
     *
     * You can either call this function with a single moodle_select_menu argument
     * or, with a list of parameters, in which case those parameters are sent to
     * the moodle_select_menu constructor.
     *
     * @param moodle_select_menu $selectmenu a moodle_select_menu that describes
     *      the select menu you want output.
     * @return string the HTML for the <select>
     */
    public function select_menu($selectmenu) {
        $selectmenu = clone($selectmenu);
        $selectmenu->prepare();

        $this->prepare_event_handlers($selectmenu);

        if ($selectmenu->nothinglabel) {
            $selectmenu->options = array($selectmenu->nothingvalue => $selectmenu->nothinglabel) +
                    $selectmenu->options;
        }

        if (empty($selectmenu->id)) {
            $selectmenu->id = 'menu' . str_replace(array('[', ']'), '', $selectmenu->name);
        }

        $attributes = array(
            'name' => $selectmenu->name,
            'id' => $selectmenu->id,
            'class' => $selectmenu->get_classes_string()
        );
        if ($selectmenu->disabled) {
            $attributes['disabled'] = 'disabled';
        }
        if ($selectmenu->tabindex) {
            $attributes['tabindex'] = $tabindex;
        }

        if ($selectmenu->listbox) {
            if (is_integer($selectmenu->listbox)) {
                $size = $selectmenu->listbox;
            } else {
                $size = min($selectmenu->maxautosize, count($selectmenu->options));
            }
            $attributes['size'] = $size;
            if ($selectmenu->multiple) {
                $attributes['multiple'] = 'multiple';
            }
        }

        $html = '';

        if (!empty($selectmenu->label)) {
            $html .= $this->label($selectmenu->label);
        }

        $html .= $this->output_start_tag('select', $attributes) . "\n";

        if ($selectmenu->nested) {
            foreach ($selectmenu->options as $section => $values) {
                $html .= $this->output_start_tag('optgroup', array('label' => $section));
                if (!is_array($values)) {
                    var_dump($values);
                }
                foreach ($values as $value => $display) {
                    $attributes = array('value' => $value);

                    if ((string) $value == (string) $selectmenu->selectedvalue ||
                            (is_array($selectmenu->selectedvalue) && in_array($value, $selectmenu->selectedvalue))) {
                        $attributes['selected'] = 'selected';
                    }

                    $html .= $this->output_start_tag('option', $attributes);

                    if ($display === '') {
                        $html .= $value;
                    } else {
                        $html .= $display;
                    }

                    $html .= $this->output_end_tag('option');
                }
                $html .= $this->output_end_tag('optgroup');
            }
        } else {
            foreach ($selectmenu->options as $value => $display) {
                $attributes = array('value' => $value);
                if ((string) $value == (string) $selectmenu->selectedvalue ||
                        (is_array($selectmenu->selectedvalue) && in_array($value, $selectmenu->selectedvalue))) {
                    $attributes['selected'] = 'selected';
                }
                $html .= '    ' . $this->output_tag('option', $attributes, s($display)) . "\n";
            }
        }
        $html .= $this->output_end_tag('select') . "\n";

        return $html;
    }

    /**
     * Outputs a <label> element.
     * @param html_label $label A html_label object
     * @return HTML fragment
     */
    public function label($label) {
        $label->prepare();
        $this->prepare_event_handlers($label);
        return $this->output_tag('label', array('for' => $label->for, 'class' => $label->get_classes_string()), $label->text);
    }

    // TODO choose_from_radio

    /**
     * Output an error message. By default wraps the error message in <span class="error">.
     * If the error message is blank, nothing is output.
     * @param string $message the error message.
     * @return string the HTML to output.
     */
    public function error_text($message) {
        if (empty($message)) {
            return '';
        }
        return $this->output_tag('span', array('class' => 'error'), $message);
    }

    /**
     * Do not call this function directly.
     *
     * To terminate the current script with a fatal error, call the {@link print_error}
     * function, or throw an exception. Doing either of those things will then call this
     * function to display the error, before terminating the execution.
     *
     * @param string $message The message to output
     * @param string $moreinfourl URL where more info can be found about the error
     * @param string $link Link for the Continue button
     * @param array $backtrace The execution backtrace
     * @param string $debuginfo Debugging information
     * @param bool $showerrordebugwarning Whether or not to show a debugging warning
     * @return string the HTML to output.
     */
    public function fatal_error($message, $moreinfourl, $link, $backtrace,
                $debuginfo = null, $showerrordebugwarning = false) {

        $output = '';

        if ($this->has_started()) {
            $output .= $this->opencontainers->pop_all_but_last();
        } else {
            // Header not yet printed
            @header('HTTP/1.0 404 Not Found');
            $this->page->set_title(get_string('error'));
            $output .= $this->header();
        }

        $message = '<p class="errormessage">' . $message . '</p>'.
                '<p class="errorcode"><a href="' . $moreinfourl . '">' .
                get_string('moreinformation') . '</a></p>';
        $output .= $this->box($message, 'errorbox');

        if (debugging('', DEBUG_DEVELOPER)) {
            if ($showerrordebugwarning) {
                $output .= $this->notification('error() is a deprecated function. ' .
                        'Please call print_error() instead of error()', 'notifytiny');
            }
            if (!empty($debuginfo)) {
                $output .= $this->notification($debuginfo, 'notifytiny');
            }
            if (!empty($backtrace)) {
                $output .= $this->notification('Stack trace: ' .
                        format_backtrace($backtrace), 'notifytiny');
            }
        }

        if (!empty($link)) {
            $output .= $this->continue_button($link);
        }

        $output .= $this->footer();

        // Padding to encourage IE to display our error page, rather than its own.
        $output .= str_repeat(' ', 512);

        return $output;
    }

    /**
     * Output a notification (that is, a status message about something that has
     * just happened).
     *
     * @param string $message the message to print out
     * @param string $classes normally 'notifyproblem' or 'notifysuccess'.
     * @return string the HTML to output.
     */
    public function notification($message, $classes = 'notifyproblem') {
        return $this->output_tag('div', array('class' =>
                moodle_renderer_base::prepare_classes($classes)), clean_text($message));
    }

    /**
     * Print a continue button that goes to a particular URL.
     *
     * @param string|moodle_url $link The url the button goes to.
     * @return string the HTML to output.
     */
    public function continue_button($link) {
        if (!is_a($link, 'moodle_url')) {
            $link = new moodle_url($link);
        }
        $form = new html_form();
        $form->url = $link;
        $form->values = $link->params();
        $form->button->text = get_string('continue');
        $form->method = 'get';

        return $this->output_tag('div', array('class' => 'continuebutton') , $this->button($form));
    }

    /**
     * Prints a single paging bar to provide access to other pages  (usually in a search)
     *
     * @param string|moodle_url $link The url the button goes to.
     * @return string the HTML to output.
     */
    public function paging_bar($pagingbar) {
        $output = '';

        $pagingbar->prepare();

        if ($pagingbar->totalcount > $pagingbar->perpage) {
            $output .= get_string('page') . ':';

            if (!empty($pagingbar->previouslink)) {
                $output .= '&nbsp;(' . $this->link($pagingbar->previouslink) . ')&nbsp;';
            }

            if (!empty($pagingbar->firstlink)) {
                $output .= '&nbsp;' . $this->link($pagingbar->firstlink) . '&nbsp;...';
            }

            foreach ($pagingbar->pagelinks as $link) {
                if ($link instanceof html_link) {
                    $output .= '&nbsp;&nbsp;' . $this->link($link);
                } else {
                    $output .= "&nbsp;&nbsp;$link";
                }
            }

            if (!empty($pagingbar->lastlink)) {
                $output .= '&nbsp;...' . $this->link($pagingbar->lastlink) . '&nbsp;';
            }

            if (!empty($pagingbar->nextlink)) {
                $output .= '&nbsp;&nbsp;(' . $this->link($pagingbar->nextlink) . ')';
            }
        }

        return $this->output_tag('div', array('class' => 'paging'), $output);
    }

    /**
     * Render a HTML table
     *
     * @param object $table {@link html_table} instance containing all the information needed
     * @return string the HTML to output.
     */
    public function table(html_table $table) {
        $table->prepare();
        $attributes = array(
                'id'            => $table->id,
                'width'         => $table->width,
                'summary'       => $table->summary,
                'cellpadding'   => $table->cellpadding,
                'cellspacing'   => $table->cellspacing,
                'class'         => $table->get_classes_string());
        $output = $this->output_start_tag('table', $attributes) . "\n";

        $countcols = 0;

        if (!empty($table->head)) {
            $countcols = count($table->head);
            $output .= $this->output_start_tag('thead', array()) . "\n";
            $output .= $this->output_start_tag('tr', array()) . "\n";
            $keys = array_keys($table->head);
            $lastkey = end($keys);
            foreach ($table->head as $key => $heading) {
                $classes = array('header', 'c' . $key);
                if (isset($table->headspan[$key]) && $table->headspan[$key] > 1) {
                    $colspan = $table->headspan[$key];
                    $countcols += $table->headspan[$key] - 1;
                } else {
                    $colspan = '';
                }
                if ($key == $lastkey) {
                    $classes[] = 'lastcol';
                }
                if (isset($table->colclasses[$key])) {
                    $classes[] = $table->colclasses[$key];
                }
                if ($table->rotateheaders) {
                    // we need to wrap the heading content
                    $heading = $this->output_tag('span', '', $heading);
                }
                $attributes = array(
                        'style'     => $table->align[$key] . $table->size[$key] . 'white-space:nowrap;',
                        'class'     => moodle_renderer_base::prepare_classes($classes),
                        'scope'     => 'col',
                        'colspan'   => $colspan);
                $output .= $this->output_tag('th', $attributes, $heading) . "\n";
            }
            $output .= $this->output_end_tag('tr') . "\n";
            $output .= $this->output_end_tag('thead') . "\n";
        }

        if (!empty($table->data)) {
            $oddeven    = 1;
            $keys       = array_keys($table->data);
            $lastrowkey = end($keys);
            $output .= $this->output_start_tag('tbody', array()) . "\n";
            foreach ($table->data as $key => $row) {
                $oddeven = $oddeven ? 0 : 1;
                if (isset($table->rowclasses[$key])) {
                    $classes = array_unique(moodle_html_component::clean_classes($table->rowclasses[$key]));
                } else {
                    $classes = array();
                }
                $classes[] = 'r' . $oddeven;
                if ($key == $lastrowkey) {
                    $classes[] = 'lastrow';
                }
                $output .= $this->output_start_tag('tr', array('class' => moodle_renderer_base::prepare_classes($classes))) . "\n";
                if (($row === 'hr') && ($countcols)) {
                    $output .= $this->output_tag('td', array('colspan' => $countcols),
                                                 $this->output_tag('div', array('class' => 'tabledivider'), '')) . "\n";
                } else {  /// it's a normal row of data
                    $keys2 = array_keys($row);
                    $lastkey = end($keys2);
                    foreach ($row as $key => $item) {
                        if (isset($table->colclasses[$key])) {
                            $classes = array_unique(moodle_html_component::clean_classes($table->colclasses[$key]));
                        } else {
                            $classes = array();
                        }
                        $classes[] = 'cell';
                        $classes[] = 'c' . $key;
                        if ($key == $lastkey) {
                            $classes[] = 'lastcol';
                        }
                        $tdstyle = '';
                        $tdstyle .= isset($table->align[$key]) ? $table->align[$key] : '';
                        $tdstyle .= isset($table->size[$key]) ? $table->size[$key] : '';
                        $tdstyle .= isset($table->wrap[$key]) ? $table->wrap[$key] : '';
                        $output .= $this->output_tag('td',
                                                     array('style' => $tdstyle,
                                                           'class' => moodle_renderer_base::prepare_classes($classes)),
                                                     $item) . "\n";
                    }
                }
                $output .= $this->output_end_tag('tr') . "\n";
            }
            $output .= $this->output_end_tag('tbody') . "\n";
        }
        $output .= $this->output_end_tag('table') . "\n";

        if ($table->rotateheaders && can_use_rotated_text()) {
            $this->page->requires->yui_lib('event');
            $this->page->requires->js('course/report/progress/textrotate.js');
        }

        return $output;
    }

    /**
     * Output the place a skip link goes to.
     * @param string $id The target name from the corresponding $PAGE->requires->skip_link_to($target) call.
     * @return string the HTML to output.
     */
    public function skip_link_target($id = '') {
        return $this->output_tag('span', array('id' => $id), '');
    }

    /**
     * Outputs a heading
     * @param string $text The text of the heading
     * @param int $level The level of importance of the heading
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @return string the HTML to output.
     */
    public function heading($text, $level, $classes = 'main', $id = '') {
        $level = (integer) $level;
        if ($level < 1 or $level > 6) {
            throw new coding_exception('Heading level must be an integer between 1 and 6.');
        }
        return $this->output_tag('h' . $level,
                array('id' => $id, 'class' => moodle_renderer_base::prepare_classes($classes)), $text);
    }

    /**
     * Outputs a box.
     * @param string $contents The contents of the box
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @return string the HTML to output.
     */
    public function box($contents, $classes = 'generalbox', $id = '') {
        return $this->box_start($classes, $id) . $contents . $this->box_end();
    }

    /**
     * Outputs the opening section of a box.
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @return string the HTML to output.
     */
    public function box_start($classes = 'generalbox', $id = '') {
        $this->opencontainers->push('box', $this->output_end_tag('div'));
        return $this->output_start_tag('div', array('id' => $id,
                'class' => 'box ' . moodle_renderer_base::prepare_classes($classes)));
    }

    /**
     * Outputs the closing section of a box.
     * @return string the HTML to output.
     */
    public function box_end() {
        return $this->opencontainers->pop('box');
    }

    /**
     * Outputs a container.
     * @param string $contents The contents of the box
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @return string the HTML to output.
     */
    public function container($contents, $classes = '', $id = '') {
        return $this->container_start($classes, $id) . $contents . $this->container_end();
    }

    /**
     * Outputs the opening section of a container.
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @return string the HTML to output.
     */
    public function container_start($classes = '', $id = '') {
        $this->opencontainers->push('container', $this->output_end_tag('div'));
        return $this->output_start_tag('div', array('id' => $id,
                'class' => moodle_renderer_base::prepare_classes($classes)));
    }

    /**
     * Outputs the closing section of a container.
     * @return string the HTML to output.
     */
    public function container_end() {
        return $this->opencontainers->pop('container');
    }
}

/// COMPONENTS

/**
 * Base class for classes representing HTML elements, like moodle_select_menu.
 *
 * Handles the id and class attributes.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_html_component {
    /**
     * @var string value to use for the id attribute of this HTML tag.
     */
    public $id = '';
    /**
     * @var array class names to add to this HTML element.
     */
    public $classes = array();
    /**
     * @var string $title The title attributes applicable to any XHTML element
     */
    public $title = '';
    /**
     * An optional array of component_action objects handling the action part of this component.
     * @var array $actions
     */
    protected $actions = array();
    /**
     * This array of generated ids is kept static to avoid id collisions
     * @var array $generated_ids
     */
    public static $generated_ids = array();

    /**
     * Ensure some class names are an array.
     * @param mixed $classes either an array of class names or a space-separated
     *      string containing class names.
     * @return array the class names as an array.
     */
    public static function clean_classes($classes) {
        if (is_array($classes)) {
            return $classes;
        } else {
            return explode(' ', trim($classes));
        }
    }

    /**
     * Set the class name array.
     * @param mixed $classes either an array of class names or a space-separated
     *      string containing class names.
     * @return void
     */
    public function set_classes($classes) {
        $this->classes = self::clean_classes($classes);
    }

    /**
     * Add a class name to the class names array.
     * @param string $class the new class name to add.
     * @return void
     */
    public function add_class($class) {
        $this->classes[] = $class;
    }

    /**
     * Add a whole lot of class names to the class names array.
     * @param mixed $classes either an array of class names or a space-separated
     *      string containing class names.
     * @return void
     */
    public function add_classes($classes) {
        $this->classes += self::clean_classes($classes);
    }

    /**
     * Get the class names as a string.
     * @return string the class names as a space-separated string. Ready to be put in the class="" attribute.
     */
    public function get_classes_string() {
        return implode(' ', $this->classes);
    }

    /**
     * Perform any cleanup or final processing that should be done before an
     * instance of this class is output.
     * @return void
     */
    public function prepare() {
        $this->classes = array_unique(self::clean_classes($this->classes));
    }

    /**
     * This checks developer do not try to assign a property directly
     * if we have a setter for it. Otherwise, the property is set as expected.
     * @param string $name The name of the variable to set
     * @param mixed $value The value to assign to the variable
     * @return void
     */
    public function __set($name, $value) {
        if ($name == 'class') {
            debugging('this way of setting css class has been deprecated. use set_classes() method instead.');
            $this->set_classes($value);
        } else {
            $this->{$name} = $value;
        }
    }

    /**
     * Adds a JS action to this component.
     * Note: the JS function you write must have only two arguments: (string)event and (object|array)args
     * If you want to add an instantiated component_action (or one of its subclasses), give the object as the only parameter
     *
     * @param mixed  $event a DOM event (click, mouseover etc.) or a component_action object
     * @param string $jsfunction The name of the JS function to call. required if argument 1 is a string (event)
     * @param array  $jsfunctionargs An optional array of JS arguments to pass to the function
     * @return void
     */
    public function add_action($event, $jsfunction=null, $jsfunctionargs=array()) {
        if (empty($this->id) || in_array($this->id, moodle_html_component::$generated_ids)) {
            $this->generate_id();
        }

        if ($event instanceof component_action) {
            $this->actions[] = $event;
        } else {
            if (empty($jsfunction)) {
                throw new coding_exception('moodle_html_component::add_action requires a JS function argument if the first argument is a string event');
            }
            $this->actions[] = new component_action($event, $jsfunction, $jsfunctionargs);
        }
    }

    /**
     * Internal method for generating a unique ID for the purpose of event handlers.
     * @return void;
     */
    protected function generate_id() {
        $this->id = get_class($this) . '-' . substr(sha1(microtime() * rand(0, 500)), 0, 6);
        if (in_array($this->id, moodle_html_component::$generated_ids)) {
            $this->generate_id();
        } else {
            moodle_html_component::$generated_ids[] = $this->id;
        }
    }

    /**
     * Returns the array of component_actions.
     * @return array Component actions
     */
    public function get_actions() {
        return $this->actions;
    }
}


/**
 * This class hold all the information required to describe a <select> menu that
 * will be printed by {@link moodle_core_renderer::select_menu()}. (Or by an overridden
 * version of that method in a subclass.)
 *
 * This component can also hold enough metadata to be used as a popup form. It just
 * needs a bit more setting up than for a simple menu. See the shortcut methods for
 * developer-friendly usage.
 *
 * All the fields that are not set by the constructor have sensible defaults, so
 * you only need to set the properties where you want non-default behaviour.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_select_menu extends moodle_html_component {
    /**
     * @var array the choices to show in the menu. An array $value => $display.
     */
    public $options;
    /**
     * @var string $name the name of this form control. That is, the name of the GET/POST
     * variable that will be set if this select is submitted as part of a form.
     */
    public $name;
    /**
     * @var mixed $label The label for that component. String or html_label object
     */
    public $label;
    /**
     * @var string $selectedvalue the option to select initially. Should match one
     * of the $options array keys. Default none.
     */
    public $selectedvalue;
    /**
     * @var string The label for the 'nothing is selected' option.
     * Defaults to get_string('choosedots').
     * Set this to '' if you do not want a 'nothing is selected' option.
     */
    public $nothinglabel = null;
    /**
     * @var string The value returned by the 'nothing is selected' option. Defaults to 0.
     */
    public $nothingvalue = 0;
    /**
     * @var boolean set this to true if you want the control to appear disabled.
     */
    public $disabled = false;
    /**
     * @var integer if non-zero, sets the tabindex attribute on the <select> element. Default 0.
     */
    public $tabindex = 0;
    /**
     * @var mixed Defaults to false, which means display the select as a dropdown menu.
     * If true, display this select as a list box whose size is chosen automatically.
     * If an integer, display as list box of that size.
     */
    public $listbox = false;
    /**
     * @var integer if you are using $listbox === true to get an automatically
     * sized list box, the size of the list box will be the number of options,
     * or this number, whichever is smaller.
     */
    public $maxautosize = 10;
    /**
     * @var boolean if true, allow multiple selection. Only used if $listbox is true.
     */
    public $multiple = false;
    /**
     * @var boolean $nested if true, uses $options' keys as option headings (optgroup)
     */
    public $nested = false;

    /**
     * @see moodle_html_component::prepare()
     * @return void
     */
    public function prepare() {
        // name may contain [], which would make an invalid id. e.g. numeric question type editing form, assignment quickgrading
        if (empty($this->id)) {
            $this->id = 'menu' . str_replace(array('[', ']'), '', $this->name);
        }
        if (empty($this->classes)) {
            $this->set_classes(array('menu' . str_replace(array('[', ']'), '', $this->name)));
        }
        if (is_null($this->nothinglabel)) {
            $this->nothinglabel = get_string('choosedots');
        }

        // If nested is on, remove the default Choose option
        if ($this->nested) {
            $this->nothinglabel = '';
        }

        if (!($this->label instanceof html_label)) {
            $label = new html_label();
            $label->text = $this->label;
            $label->for = $this->name;
            $this->label = $label;
        }

        $this->add_class('select');

        parent::prepare();
    }

    /**
     * This is a shortcut for making a simple select menu. It lets you specify
     * the options, name and selected option in one line of code.
     * @param array $options used to initialise {@link $options}.
     * @param string $name used to initialise {@link $name}.
     * @param string $selected  used to initialise {@link $selected}.
     * @return moodle_select_menu A moodle_select_menu object with the three common fields initialised.
     */
    public static function make($options, $name, $selected = '') {
        $menu = new moodle_select_menu();
        $menu->options = $options;
        $menu->name = $name;
        $menu->selectedvalue = $selected;
        return $menu;
    }

    /**
     * This is a shortcut for making a yes/no select menu.
     * @param string $name used to initialise {@link $name}.
     * @param string $selected  used to initialise {@link $selected}.
     * @return moodle_select_menu A menu initialised with yes/no options.
     */
    public static function make_yes_no($name, $selected) {
        return self::make(array(0 => get_string('no'), 1 => get_string('yes')), $name, $selected);
    }

    /**
     * This is a shortcut for making an hour selector menu.
     * @param string $type The type of selector (years, months, days, hours, minutes)
     * @param string $name fieldname
     * @param int $currenttime A default timestamp in GMT
     * @param int $step minute spacing
     * @return moodle_select_menu A menu initialised with hour options.
     */
    public static function make_time_selector($type, $name, $currenttime=0, $step=5) {

        if (!$currenttime) {
            $currenttime = time();
        }
        $currentdate = usergetdate($currenttime);
        $userdatetype = $type;

        switch ($type) {
            case 'years':
                for ($i=1970; $i<=2020; $i++) {
                    $timeunits[$i] = $i;
                }
                $userdatetype = 'year';
                break;
            case 'months':
                for ($i=1; $i<=12; $i++) {
                    $timeunits[$i] = userdate(gmmktime(12,0,0,$i,15,2000), "%B");
                }
                $userdatetype = 'month';
                break;
            case 'days':
                for ($i=1; $i<=31; $i++) {
                    $timeunits[$i] = $i;
                }
                $userdatetype = 'mday';
                break;
            case 'hours':
                for ($i=0; $i<=23; $i++) {
                    $timeunits[$i] = sprintf("%02d",$i);
                }
                break;
            case 'minutes':
                if ($step != 1) {
                    $currentdate['minutes'] = ceil($currentdate['minutes']/$step)*$step;
                }

                for ($i=0; $i<=59; $i+=$step) {
                    $timeunits[$i] = sprintf("%02d",$i);
                }
                break;
            default:
                throw new coding_exception("Time type $type is not supported by moodle_select_menu::make_time_selector().");
        }

        $timerselector = self::make($timeunits, $name, $currentdate[$userdatetype]);
        $timerselector->label = new html_label();
        $timerselector->label->text = get_string(substr($type, -1), 'form');
        $timerselector->label->for = "menu$timerselector->name";
        $timerselector->label->add_class('accesshide');
        $timerselector->nothinglabel = '';

        return $timerselector;
    }
}

/**
 * This class represents how a block appears on a page.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_label extends moodle_html_component {
    /**
     * @var string $text The text to display in the label
     */
    public $text;
    /**
     * @var string $for The name of the form field this label is associated with
     */
    public $for;

    public function prepare() {
        parent::prepare();
    }
}

/**
 * This class represents how a block appears on a page.
 *
 * During output, each block instance is asked to return a block_contents object,
 * those are then passed to the $OUTPUT->block function for display.
 *
 * {@link $contents} should probably be generated using a moodle_block_..._renderer.
 *
 * Other block-like things that need to appear on the page, for example the
 * add new block UI, are also represented as block_contents objects.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class block_contents extends moodle_html_component {
    /** @var int used to set $skipid. */
    protected static $idcounter = 1;

    const NOT_HIDEABLE = 0;
    const VISIBLE = 1;
    const HIDDEN = 2;

    /**
     * @param integer $skipid All the blocks (or things that look like blocks)
     * printed on a page are given a unique number that can be used to construct
     * id="" attributes. This is set automatically be the {@link prepare()} method.
     * Do not try to set it manually.
     */
    public $skipid;

    /**
     * @var integer If this is the contents of a real block, this should be set to
     * the block_instance.id. Otherwise this should be set to 0.
     */
    public $blockinstanceid = 0;

    /**
     * @var integer if this is a real block instance, and there is a corresponding
     * block_position.id for the block on this page, this should be set to that id.
     * Otherwise it should be 0.
     */
    public $blockpositionid = 0;

    /**
     * @param array $attributes an array of attribute => value pairs that are put on the
     * outer div of this block. {@link $id} and {@link $classes} attributes should be set separately.
     */
    public $attributes = array();

    /**
     * @param string $title The title of this block. If this came from user input,
     * it should already have had format_string() processing done on it. This will
     * be output inside <h2> tags. Please do not cause invalid XHTML.
     */
    public $title = '';

    /**
     * @param string $content HTML for the content
     */
    public $content = '';

    /**
     * @param array $list an alternative to $content, it you want a list of things with optional icons.
     */
    public $footer = '';

    /**
     * Any small print that should appear under the block to explain to the
     * teacher about the block, for example 'This is a sticky block that was
     * added in the system context.'
     * @var string
     */
    public $annotation = '';

    /**
     * @var integer one of the constants NOT_HIDEABLE, VISIBLE, HIDDEN. Whether
     * the user can toggle whether this block is visible.
     */
    public $collapsible = self::NOT_HIDEABLE;

    /**
     * A (possibly empty) array of editing controls. Each element of this array
     * should be an array('url' => $url, 'icon' => $icon, 'caption' => $caption).
     * $icon is the icon name. Fed to $OUTPUT->old_icon_url.
     * @var array
     */
    public $controls = array();

    /**
     * @see moodle_html_component::prepare()
     * @return void
     */
    public function prepare() {
        $this->skipid = self::$idcounter;
        self::$idcounter += 1;
        $this->add_class('sideblock');
        if (empty($this->blockinstanceid) || !strip_tags($this->title)) {
            $this->collapsible = self::NOT_HIDEABLE;
        }
        if ($this->collapsible == self::HIDDEN) {
            $this->add_class('hidden');
        }
        if (!empty($this->controls)) {
            $this->add_class('block_with_controls');
        }
        parent::prepare();
    }
}


/**
 * Holds all the information required to render a <table> by
 * {@see moodle_core_renderer::table()} or by an overridden version of that
 * method in a subclass.
 *
 * Example of usage:
 * $t = new html_table();
 * ... // set various properties of the object $t as described below
 * echo $OUTPUT->table($t);
 *
 * @copyright 2009 David Mudrak <david.mudrak@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_table extends moodle_html_component {
    /**
     * @var array of headings. The n-th array item is used as a heading of the n-th column.
     *
     * Example of usage:
     * $t->head = array('Student', 'Grade');
     */
    public $head;
    /**
     * @var array can be used to make a heading span multiple columns
     *
     * Example of usage:
     * $t->headspan = array(2,1);
     *
     * In this example, {@see html_table:$data} is supposed to have three columns. For the first two columns,
     * the same heading is used. Therefore, {@see html_table::$head} should consist of two items.
     */
    public $headspan;
    /**
     * @var array of column alignments. The value is used as CSS 'text-align' property. Therefore, possible
     * values are 'left', 'right', 'center' and 'justify'. Specify 'right' or 'left' from the perspective
     * of a left-to-right (LTR) language. For RTL, the values are flipped automatically.
     *
     * Examples of usage:
     * $t->align = array(null, 'right');
     * or
     * $t->align[1] = 'right';
     *
     */
    public $align;
    /**
     * @var array of column sizes. The value is used as CSS 'size' property.
     *
     * Examples of usage:
     * $t->size = array('50%', '50%');
     * or
     * $t->size[1] = '120px';
     */
    public $size;
    /**
     * @var array of wrapping information. The only possible value is 'nowrap' that sets the
     * CSS property 'white-space' to the value 'nowrap' in the given column.
     *
     * Example of usage:
     * $t->wrap = array(null, 'nowrap');
     */
    public $wrap;
    /**
     * @var array of arrays containing the data. Alternatively, if you have
     * $head specified, the string 'hr' (for horizontal ruler) can be used
     * instead of an array of cells data resulting in a divider rendered.
     *
     * Example if usage:
     * $row1 = array('Harry Potter', '76 %');
     * $row2 = array('Hermione Granger', '100 %');
     * $t->data = array($row1, $row2);
     */
    public $data;
    /**
     * @var string width of the table, percentage of the page preferred. Defaults to 80% of the page width.
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     */
    public $width = null;
    /**
     * @var string alignment the whole table. Can be 'right', 'left' or 'center' (default).
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     */
    public $tablealign = null;
    /**
     * @var int padding on each cell, in pixels
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     */
    public $cellpadding = null;
    /**
     * @var int spacing between cells, in pixels
     * @deprecated since Moodle 2.0. Styling should be in the CSS.
     */
    public $cellspacing = null;
    /**
     * @var array classes to add to particular rows, space-separated string.
     * Classes 'r0' or 'r1' are added automatically for every odd or even row,
     * respectively. Class 'lastrow' is added automatically for the last row
     * in the table.
     *
     * Example of usage:
     * $t->rowclasses[9] = 'tenth'
     */
    public $rowclasses;
    /**
     * @var array classes to add to every cell in a particular column,
     * space-separated string. Class 'cell' is added automatically by the renderer.
     * Classes 'c0' or 'c1' are added automatically for every odd or even column,
     * respectively. Class 'lastcol' is added automatically for all last cells
     * in a row.
     *
     * Example of usage:
     * $t->colclasses = array(null, 'grade');
     */
    public $colclasses;
    /**
     * @var string description of the contents for screen readers.
     */
    public $summary;
    /**
     * @var bool true causes the contents of the heading cells to be rotated 90 degrees.
     */
    public $rotateheaders = false;

    /**
     * @see moodle_html_component::prepare()
     * @return void
     */
    public function prepare() {
        if (!empty($this->align)) {
            foreach ($this->align as $key => $aa) {
                if ($aa) {
                    $this->align[$key] = 'text-align:'. fix_align_rtl($aa) .';';  // Fix for RTL languages
                } else {
                    $this->align[$key] = '';
                }
            }
        }
        if (!empty($this->size)) {
            foreach ($this->size as $key => $ss) {
                if ($ss) {
                    $this->size[$key] = 'width:'. $ss .';';
                } else {
                    $this->size[$key] = '';
                }
            }
        }
        if (!empty($this->wrap)) {
            foreach ($this->wrap as $key => $ww) {
                if ($ww) {
                    $this->wrap[$key] = 'white-space:nowrap;';
                } else {
                    $this->wrap[$key] = '';
                }
            }
        }
        if (!empty($this->head)) {
            foreach ($this->head as $key => $val) {
                if (!isset($this->align[$key])) {
                    $this->align[$key] = '';
                }
                if (!isset($this->size[$key])) {
                    $this->size[$key] = '';
                }
                if (!isset($this->wrap[$key])) {
                    $this->wrap[$key] = '';
                }

            }
        }
        if (empty($this->classes)) { // must be done before align
            $this->set_classes(array('generaltable'));
        }
        if (!empty($this->tablealign)) {
            $this->add_class('boxalign' . $this->tablealign);
        }
        if (!empty($this->rotateheaders)) {
            $this->add_class('rotateheaders');
        } else {
            $this->rotateheaders = false; // Makes life easier later.
        }
        parent::prepare();
    }
    /**
     * @param string $name The name of the variable to set
     * @param mixed $value The value to assign to the variable
     * @return void
     */
    public function __set($name, $value) {
        if ($name == 'rowclass') {
            debugging('rowclass[] has been deprecated for html_table ' .
                      'and should be replaced with rowclasses[]. please fix the code.');
            $this->rowclasses = $value;
        } else {
            parent::__set($name, $value);
        }
    }
}

/**
 * Component representing a XHTML link.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_link extends moodle_html_component {
    /**
     * URL can be simple text or a moodle_url object
     * @var mixed $url
     */
    public $url;

    /**
     * @var string $text The text that will appear between the link tags
     */
    public $text;

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        // We can't accept an empty text value
        if (empty($this->text)) {
            throw new coding_exception('A html_link must have a descriptive text value!');
        }

        parent::prepare();
    }
}

/**
 * Component representing a help icon.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class help_icon extends moodle_html_component {
    /**
     * @var html_link $link A html_link object that will hold the URL info
     */
    public $link;
    /**
     * @var string $text A descriptive text
     */
    public $text;
    /**
     * @var string $page  The keyword that defines a help page
     */
    public $page;
    /**
     * @var string $module Which module is the page defined in
     */
    public $module = 'moodle';
    /**
     * @var boolean $linktext Whether or not to show text next to the icon
     */
    public $linktext = false;
    /**
     * @var mixed $image The help icon. Can be set to true (will use default help icon),
     *                   false (will not use any icon), the URL to an image, or a full
     *                   html_image object.
     */
    public $image;

    /**
     * Constructor: sets up the other components in case they are needed
     * @return void
     */
    public function __construct() {
        $this->link = new html_link();
        $this->image = new html_image();
    }

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        global $COURSE, $OUTPUT;

        if (empty($this->page)) {
            throw new coding_exception('A help_icon object requires a $page parameter');
        }

        if (empty($this->text)) {
            throw new coding_exception('A help_icon object requires a $text parameter');
        }

        $this->link->text = $this->text;

        // fix for MDL-7734
        $this->link->url = new moodle_url('/help.php', array('module' => $this->module, 'file' => $this->page .'.html'));

        // fix for MDL-7734
        if (!empty($COURSE->lang)) {
            $this->link->url->param('forcelang', $COURSE->lang);
        }

        // Catch references to the old text.html and emoticons.html help files that
        // were renamed in MDL-13233.
        if (in_array($this->page, array('text', 'emoticons', 'richtext'))) {
            $oldname = $this->page;
            $this->page .= '2';
            debugging("You are referring to the old help file '$oldname'. " .
                    "This was renamed to '$this->page' because of MDL-13233. " .
                    "Please update your code.", DEBUG_DEVELOPER);
        }

        if ($this->module == '') {
            $this->module = 'moodle';
        }

        // Warn users about new window for Accessibility
        $this->title = get_string('helpprefix2', '', trim($this->text, ". \t")) .' ('.get_string('newwindow').')';

        // Prepare image and linktext
        if ($this->image && !($this->image instanceof html_image)) {
            $image = fullclone($this->image);
            $this->image = new html_image();

            if ($image instanceof moodle_url) {
                $this->image->src = $image->out();
            } else if ($image === true) {
                $this->image->src = $OUTPUT->old_icon_url('help');
            } else if (is_string($image)) {
                $this->image->src = $image;
            }
            $this->image->alt = $this->text;

            if ($this->linktext) {
                $this->image->alt = get_string('helpwiththis');
            } else {
                $this->image->alt = $this->title;
            }
            $this->image->add_class('iconhelp');
        } else if (empty($this->image->src)) {
            $this->image->src = $OUTPUT->old_icon_url('help');
        }

        parent::prepare();
    }

    public static function make_scale_menu($courseid, $scale) {
        $helpbutton = new help_button();
        $strscales = get_string('scales');
        $helpbutton->image->alt = $scale->name;
        $helpbutton->link->url = new moodle_url('/course/scales.php', array('id' => $courseid, 'list' => true, 'scaleid' => $scale->id));
        $popupaction = new popup_action('click', $helpbutton->url, 'ratingscale', $popupparams);
        $popupaction->width = 500;
        $popupaction->height = 400;
        $helpbutton->link->add_action($popupaction);
        $helpbutton->link->title = $scale->name;
        return $helpbutton;
    }
}


/**
 * Component representing a XHTML button (input of type 'button').
 * The renderer will either output it as a button with an onclick event,
 * or as a form with hidden inputs.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_button extends moodle_html_component {
    /**
     * @var string $text
     */
    public $text;

    /**
     * @var boolean $disabled Whether or not this button is disabled
     */
    public $disabled = false;

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        $this->add_class('singlebutton');

        if (empty($this->text)) {
            throw new coding_exception('A html_button must have a text value!');
        }

        if ($this->disabled) {
            $this->disabled = 'disabled';
        }

        parent::prepare();
    }
}

/**
 * Component representing an icon linking to a Moodle page.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class action_icon extends moodle_html_component {
    /**
     * @var string $linktext Optional text to display next to the icon
     */
    public $linktext;
    /**
     * @var html_image $image The icon
     */
    public $image;
    /**
     * @var html_link $link The link
     */
    public $link;

    /**
     * Constructor: sets up the other components in case they are needed
     * @return void
     */
    public function __construct() {
        $this->image = new html_image();
        $this->link = new html_link();
    }

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        $this->image->add_class('action-icon');

        parent::prepare();

        if (empty($this->image->src)) {
            throw new coding_exception('action_icon->image->src must not be empty');
        }

        if (empty($this->image->alt) && !empty($this->linktext)) {
            $this->image->alt = $this->linktext;
        } else if (empty($this->image->alt)) {
            debugging('action_icon->image->alt should not be empty.', DEBUG_DEVELOPER);
        }
    }
}

/**
 * Component representing an image.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_image extends moodle_html_component {
    /**
     * @var string $alt A descriptive text
     */
    public $alt = HTML_ATTR_EMPTY;
    /**
     * @var string $src The path to the image being used
     */
    public $src;

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        $this->add_class('image');
        parent::prepare();
    }
}

/**
 * Component representing a user picture.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class user_picture extends moodle_html_component {
    /**
     * @var mixed $user A userid or a user object with at least fields id, picture, imagealrt, firstname and lastname set.
     */
    public $user;
    /**
     * @var int $courseid The course id. Used when constructing the link to the user's profile.
     */
    public $courseid;
    /**
     * @var html_image $image A custom image used as the user picture.
     */
    public $image;
    /**
     * @var mixed $url False: picture not enclosed in a link. True: default link. moodle_url: custom link.
     */
    public $url;
    /**
     * @var int $size Size in pixels. Special values are (true/1 = 100px) and (false/0 = 35px) for backward compatibility
     */
    public $size;
    /**
     * @var boolean $alttext add non-blank alt-text to the image. (Default true, set to false for purely
     */
    public $alttext = true;

    /**
     * Constructor: sets up the other components in case they are needed
     * @return void
     */
    public function __construct() {
        $this->image = new html_image();
    }

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        global $CFG, $DB, $OUTPUT;

        if (empty($this->user)) {
            throw new coding_exception('A user_picture object must have a $user object before being rendered.');
        }

        if (empty($this->courseid)) {
            throw new coding_exception('A user_picture object must have a courseid value before being rendered.');
        }

        if (!($this->image instanceof html_image)) {
            debugging('user_picture::image must be an instance of html_image', DEBUG_DEVELOPER);
        }

        $needrec = false;
        // only touch the DB if we are missing data...
        if (is_object($this->user)) {
            // Note - both picture and imagealt _can_ be empty
            // what we are trying to see here is if they have been fetched
            // from the DB. We should use isset() _except_ that some installs
            // have those fields as nullable, and isset() will return false
            // on null. The only safe thing is to ask array_key_exists()
            // which works on objects. property_exists() isn't quite
            // what we want here...
            if (! (array_key_exists('picture', $this->user)
                   && ($this->alttext && array_key_exists('imagealt', $this->user)
                       || (isset($this->user->firstname) && isset($this->user->lastname)))) ) {
                $needrec = true;
                $this->user = $this->user->id;
            }
        } else {
            if ($this->alttext) {
                // we need firstname, lastname, imagealt, can't escape...
                $needrec = true;
            } else {
                $userobj = new StdClass; // fake it to save DB traffic
                $userobj->id = $this->user;
                $userobj->picture = $this->image->src;
                $this->user = clone($userobj);
                unset($userobj);
            }
        }
        if ($needrec) {
            $this->user = $DB->get_record('user', array('id' => $this->user), 'id,firstname,lastname,imagealt');
        }

        if (!empty($this->link) && empty($this->url)) {
            $this->url = new moodle_url('/user/view.php', array('id' => $this->user->id, 'course' => $this->courseid));
        }

        if (empty($this->size)) {
            $file = 'f2';
            $this->size = 35;
        } else if ($this->size === true or $this->size == 1) {
            $file = 'f1';
            $this->size = 100;
        } else if ($this->size >= 50) {
            $file = 'f1';
        } else {
            $file = 'f2';
        }

        if (!empty($this->size)) {
            $this->image->width = $this->size;
            $this->image->height = $this->size;
        }

        $this->add_class('userpicture');

        if (empty($this->image->src) && !empty($this->user->picture)) {
            $this->image->src = $this->user->picture;
        }

        if (!empty($this->image->src)) {
            require_once($CFG->libdir.'/filelib.php');
            $this->image->src = new moodle_url(get_file_url($this->user->id.'/'.$file.'.jpg', null, 'user'));
        } else { // Print default user pictures (use theme version if available)
            $this->add_class('defaultuserpic');
            $this->image->src = $OUTPUT->old_icon_url('u/' . $file);
        }

        if ($this->alttext) {
            if (!empty($this->user->imagealt)) {
                $this->image->alt = $this->user->imagealt;
            } else {
                $this->image->alt = get_string('pictureof','',fullname($this->user));
            }
        }

        parent::prepare();
    }
}

/**
 * Component representing a textarea.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_textarea extends moodle_html_component {
    /**
     * @param string $name Name to use for the textarea element.
     */
    public $name;
    /**
     * @param string $value Initial content to display in the textarea.
     */
    public $value;
    /**
     * @param int $rows Number of rows to display  (minimum of 10 when $height is non-null)
     */
    public $rows;
    /**
     * @param int $cols Number of columns to display (minimum of 65 when $width is non-null)
     */
    public $cols;
    /**
     * @param bool $usehtmleditor Enables the use of the htmleditor for this field.
     */
    public $usehtmleditor;

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        $this->add_class('form-textarea');

        if (empty($this->id)) {
            $this->id = "edit-$this->name";
        }

        if ($this->usehtmleditor) {
            editors_head_setup();
            $editor = get_preferred_texteditor(FORMAT_HTML);
            $editor->use_editor($this->id, array('legacy'=>true));
            $this->value = htmlspecialchars($value);
        }

        parent::prepare();
    }
}

/**
 * Component representing a simple form wrapper. Its purpose is mainly to enclose
 * a submit input with the appropriate action and hidden inputs.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_form extends moodle_html_component {
    /**
     * @var string $method post or get
     */
    public $method = 'post';
    /**
     * If a string is given, it will be converted to a moodle_url during prepare()
     * @var mixed $url A moodle_url including params or a string
     */
    public $url;
    /**
     * @var array $params Optional array of parameters. Ignored if $url instanceof moodle_url
     */
    public $params = array();
    /**
     * @var boolean $showbutton If true, the submit button will always be shown even if JavaScript is available
     */
    public $showbutton = false;
    /**
     * @var string $targetwindow The name of the target page to open the linked page in.
     */
    public $targetwindow = 'self';
    /**
     * @var html_button $button A submit button
     */
    public $button;

    /**
     * Constructor: sets up the other components in case they are needed
     * @return void
     */
    public function __construct() {
        static $go;
        $this->button = new html_button();
        if (!isset($go)) {
            $go = get_string('go');
            $this->button->text = $go;
        }
    }

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {

        if (empty($this->url)) {
            throw new coding_exception('A html_form must have a $url value (string or moodle_url).');
        }

        if (!($this->url instanceof moodle_url)) {
            $this->url = new moodle_url($this->url, $this->params);
        }

        if ($this->method == 'post') {
            $this->url->param('sesskey', sesskey());
        }

        parent::prepare();
    }
}

/**
 * Component representing a paging bar.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class moodle_paging_bar extends moodle_html_component {
    /**
     * @var int $maxdisplay The maximum number of pagelinks to display
     */
    public $maxdisplay = 18;
    /**
     * @var int $totalcount post or get
     */
    public $totalcount;
    /**
     * @var int $page The page you are currently viewing
     */
    public $page;
    /**
     * @var int $perpage The number of entries that should be shown per page
     */
    public $perpage;
    /**
     * @var string $baseurl If this  is a string then it is the url which will be appended with $pagevar, an equals sign and the page number.
     *      If this is a moodle_url object then the pagevar param will be replaced by the page no, for each page.
     */
    public $baseurl;
    /**
     * @var string $pagevar This is the variable name that you use for the page number in your code (ie. 'tablepage', 'blogpage', etc)
     */
    public $pagevar = 'page';
    /**
     * @var bool $nocurr do not display the current page as a link
     */
    public $nocurr;
    /**
     * @var html_link $previouslink A HTML link representing the "previous" page
     */
    public $previouslink = null;
    /**
     * @var html_link $nextlink A HTML link representing the "next" page
     */
    public $nextlink = null;
    /**
     * @var html_link $firstlink A HTML link representing the first page
     */
    public $firstlink = null;
    /**
     * @var html_link $lastlink A HTML link representing the last page
     */
    public $lastlink = null;
    /**
     * @var array $pagelinks An array of html_links. One of them is just a string: the current page
     */
    public $pagelinks = array();

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        if (!($this->baseurl instanceof moodle_url)) {
            $this->baseurl = new moodle_url($this->baseurl);
        }

        if ($this->totalcount > $this->perpage) {
            $pagenum = $this->page - 1;

            if ($this->page > 0) {
                $this->previouslink = new html_link();
                $this->previouslink->add_class('previous');
                $this->previouslink->url = $this->baseurl->out(false, array($this->pagevar => $pagenum));
                $this->previouslink->text = get_string('previous');
            }

            if ($this->perpage > 0) {
                $lastpage = ceil($this->totalcount / $this->perpage);
            } else {
                $lastpage = 1;
            }

            if ($this->page > 15) {
                $startpage = $this->page - 10;

                $this->firstlink = new html_link();
                $this->firstlink->url = $this->baseurl->out(false, array($this->pagevar => 0));
                $this->firstlink->text = 1;
                $this->firstlink->add_class('first');
            } else {
                $startpage = 0;
            }

            $currpage = $startpage;
            $displaycount = $displaypage = 0;

            while ($displaycount < $this->maxdisplay and $currpage < $lastpage) {
                $displaypage = $currpage + 1;

                if ($this->page == $currpage && empty($this->nocurr)) {
                    $this->pagelinks[] = $displaypage;
                } else {
                    $pagelink = new html_link();
                    $pagelink->url = $this->baseurl->out(false, array($this->pagevar => $currpage));
                    $pagelink->text = $displaypage;
                    $this->pagelinks[] = $pagelink;
                }

                $displaycount++;
                $currpage++;
            }

            if ($currpage < $lastpage) {
                $lastpageactual = $lastpage - 1;
                $this->lastlink = new html_link();
                $this->lastlink->url = $this->baseurl->out(false, array($this->pagevar => $lastpageactual));
                $this->lastlink->text = $lastpage;
                $this->lastlink->add_class('last');
            }

            $pagenum = $this->page + 1;

            if ($pagenum != $displaypage) {
                $this->nextlink = new html_link();
                $this->nextlink->url = $this->baseurl->out(false, array($this->pagevar => $pagenum));
                $this->nextlink->text = get_string('next');
                $this->nextlink->add_class('next');
            }
        }
    }

}

/**
 * Component representing a list.
 *
 * The advantage of using this object instead of a flat array is that you can load it
 * with metadata (CSS classes, event handlers etc.) which can be used by the renderers.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_list extends moodle_html_component {

    /**
     * @var array $items An array of html_list_item or html_list objects
     */
    public $items = array();

    /**
     * @var string $type The type of list (ordered|unordered), definition type not yet supported
     */
    public $type = 'unordered';

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        parent::prepare();
    }

    /**
     * This function takes a nested array of data and maps it into this list's $items array
     * as proper html_list_item and html_list objects, with appropriate metadata.
     *
     * @param array $tree A nested array (array keys are ignored);
     * @param int $row Used in identifying the iteration level and in ul classes
     * @return void
     */
    public function load_data($tree, $level=0) {

        $this->add_class("list-$level");

        foreach ($tree as $key => $element) {
            if (is_array($element)) {
                $newhtmllist = new html_list();
                $newhtmllist->load_data($element, $level + 1);
                $this->items[] = $newhtmllist;
            } else {
                $listitem = new html_list_item();
                $listitem->value = $element;
                $listitem->add_class("list-item-$level-$key");
                $this->items[] = $listitem;
            }
        }
    }

    /**
     * Adds a html_list_item or html_list to this list.
     * If the param is a string, a html_list_item will be added.
     * @param mixed $item String, html_list or html_list_item object
     * @return void
     */
    public function add_item($item) {
        if ($item instanceof html_list_item || $item instanceof html_list) {
            $this->items[] = $item;
        } else {
            $listitem = new html_list_item();
            $listitem->value = $item;
            $this->items[] = $item;
        }
    }
}

/**
 * Component representing a list item.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class html_list_item extends moodle_html_component {
    /**
     * @var string $value The value of the list item
     */
    public $value;

    /**
     * @see lib/moodle_html_component#prepare()
     * @return void
     */
    public function prepare() {
        parent::prepare();
    }
}

/// ACTIONS

/**
 * Helper class used by other components that involve an action on the page (URL or JS).
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class component_action {

    /**
     * The DOM event that will trigger this action when caught
     * @var string $event DOM event
     */
    public $event;

    /**
     * The JS function you create must have two arguments:
     *      1. The event object
     *      2. An object/array of arguments ($jsfunctionargs)
     * @var string $jsfunction A function name to call when the button is clicked
     */
    public $jsfunction = false;

    /**
     * @var array $jsfunctionargs An array of arguments to pass to the JS function
     */
    public $jsfunctionargs = array();

    /**
     * Constructor
     * @param string $event DOM event
     * @param moodle_url $url A moodle_url object, required if no jsfunction is given
     * @param string $method 'post' or 'get'
     * @param string $jsfunction An optional JS function. Required if jsfunctionargs is given
     * @param array  $jsfunctionargs An array of arguments to pass to the jsfunction
     * @return void
     */
    public function __construct($event, $jsfunction, $jsfunctionargs=array()) {
        $this->event = $event;

        $this->jsfunction = $jsfunction;
        $this->jsfunctionargs = $jsfunctionargs;

        if (!empty($this->jsfunctionargs)) {
            if (empty($this->jsfunction)) {
                throw new coding_exception('The component_action object needs a jsfunction value to pass the jsfunctionargs to.');
            }
        }
    }
}

/**
 * Component action for a popup window.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class popup_action extends component_action {
    /**
     * @var array $params An array of parameters that will be passed to the openpopup JS function
     */
    public $params = array(
            'height' =>  400,
            'width' => 500,
            'top' => 0,
            'left' => 0,
            'menubar' => false,
            'location' => false,
            'scrollbars' => true,
            'resizable' => true,
            'toolbar' => true,
            'status' => true,
            'directories' => false,
            'fullscreen' => false,
            'dependent' => true);

    /**
     * Constructor
     * @param string $event DOM event
     * @param moodle_url $url A moodle_url object, required if no jsfunction is given
     * @param string $method 'post' or 'get'
     * @param array  $params An array of popup parameters
     * @return void
     */
    public function __construct($event, $url, $name='popup', $params=array()) {
        global $CFG;
        $this->name = $name;

        $url = new moodle_url($url);

        if ($this->name) {
            $_name = $this->name;
            if (($_name = preg_replace("/\s/", '_', $_name)) != $this->name) {
                throw new coding_exception('The $name of a popup window shouldn\'t contain spaces - string modified. '. $this->name .' changed to '. $_name);
                $this->name = $_name;
            }
        } else {
            $this->name = 'popup';
        }

        foreach ($this->params as $var => $val) {
            if (array_key_exists($var, $params)) {
                $this->params[$var] = $params[$var];
            }
        }
        parent::__construct($event, 'openpopup', array('url' => $url->out(false, array(), false), 'name' => $name, 'options' => $this->get_js_options($params)));
    }

    /**
     * Returns a string of concatenated option->value pairs used by JS to call the popup window,
     * based on this object's variables
     *
     * @return string String of option->value pairs for JS popup function.
     */
    public function get_js_options() {
        $jsoptions = '';

        foreach ($this->params as $var => $val) {
            if (is_string($val) || is_int($val)) {
                $jsoptions .= "$var=$val,";
            } elseif (is_bool($val)) {
                $jsoptions .= ($val) ? "$var," : "$var=0,";
            }
        }

        $jsoptions = substr($jsoptions, 0, strlen($jsoptions) - 1);

        return $jsoptions;
    }
}

/// RENDERERS

/**
 * A renderer that generates output for command-line scripts.
 *
 * The implementation of this renderer is probably incomplete.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class cli_core_renderer extends moodle_core_renderer {
    /**
     * Returns the page header.
     * @return string HTML fragment
     */
    public function header() {
        output_starting_hook();
        return $this->page->heading . "\n";
    }

    /**
     * Returns a template fragment representing a Heading.
     * @param string $text The text of the heading
     * @param int $level The level of importance of the heading
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @return string A template fragment for a heading
     */
    public function heading($text, $level, $classes = 'main', $id = '') {
        $text .= "\n";
        switch ($level) {
            case 1:
                return '=>' . $text;
            case 2:
                return '-->' . $text;
            default:
                return $text;
        }
    }

    /**
     * Returns a template fragment representing a fatal error.
     * @param string $message The message to output
     * @param string $moreinfourl URL where more info can be found about the error
     * @param string $link Link for the Continue button
     * @param array $backtrace The execution backtrace
     * @param string $debuginfo Debugging information
     * @param bool $showerrordebugwarning Whether or not to show a debugging warning
     * @return string A template fragment for a fatal error
     */
    public function fatal_error($message, $moreinfourl, $link, $backtrace,
                $debuginfo = null, $showerrordebugwarning = false) {
        $output = "!!! $message !!!\n";

        if (debugging('', DEBUG_DEVELOPER)) {
            if (!empty($debuginfo)) {
                $this->notification($debuginfo, 'notifytiny');
            }
            if (!empty($backtrace)) {
                $this->notification('Stack trace: ' . format_backtrace($backtrace, true), 'notifytiny');
            }
        }
    }

    /**
     * Returns a template fragment representing a notification.
     * @param string $message The message to include
     * @param string $classes A space-separated list of CSS classes
     * @return string A template fragment for a notification
     */
    public function notification($message, $classes = 'notifyproblem') {
        $message = clean_text($message);
        if ($classes === 'notifysuccess') {
            return "++ $message ++\n";
        }
        return "!! $message !!\n";
    }
}


/**
 * Output CSS while replacing constants/variables. See MDL-6798 for details
 *
 * Information from Urs Hunkler:
 *
 * This is an adaptation of Shaun Inman's "CSS Server-side Constants" for Moodle.
 * http://www.shauninman.com/post/heap/2005/08/09/css_constants
 *
 * To use, specify $THEME->customcssoutputfunction = 'output_css_replacing_constants';
 * in your theme's config.php file.
 *
 * The constant definitions are written into a separate CSS file named like
 * constants.css and loaded first in config.php. You can use constants for any
 * CSS properties. The constant definition looks like:
 * <code>
 * \@server constants {
 *   fontColor: #3a2830;
 *   aLink: #116699;
 *   aVisited: #AA2200;
 *   aHover: #779911;
 *   pageBackground: #FFFFFF;
 *   backgroundColor: #EEEEEE;
 *   backgroundSideblockHeader: #a8a4e9;
 *   fontcolorSideblockHeader: #222222;
 *   color1: #98818b;
 *   color2: #bd807b;
 *   color3: #f9d1d7;
 *   color4: #e8d4d8;
 * }
 * </code>
 *
 * The lines in the CSS files using CSS constants look like:
 * <code>
 * body {
 *   font-size: 100%;
 *   background-color: pageBackground;
 *   color: fontColor;
 *   font-family: 'Bitstream Vera Serif', georgia, times, serif;
 *   margin: 0;
 *   padding: 0;
 * }
 * div#page {
 *   margin: 0 10px;
 *   padding-top: 5px;
 *   border-top-width: 10px;
 *   border-top-style: solid;
 *   border-top-color: color3;
 * }
 * div.clearer {
 *   clear: both;
 * }
 * a:link {
 *   color: aLink;
 * }
 * </code>
 *
 * @param array $files an array of the CSS fields that need to be output.
 * @param array $toreplace for convenience. If you are going to output the names
 *      of the css files, for debugging purposes, then you should output
 *      str_replace($toreplace, '', $file); because it looks prettier.
 * @return void
 */
function output_css_replacing_constants($files, $toreplace) {
    // Get all the CSS.
    ob_start();
    foreach ($files as $file) {
        $shortname = str_replace($toreplace, '', $file);
        echo '/******* ' . $shortname . " start *******/\n\n";
        @include_once($file);
        echo '/******* ' . $shortname . " end *******/\n\n";
    }
    $css = ob_get_contents();
    ob_end_clean();

    if (preg_match_all("/@server\s+(?:variables|constants)\s*\{\s*([^\}]+)\s*\}\s*/i", $css, $matches)) {
        $variables = array();
        foreach ($matches[0] as $key => $server) {
            $css = str_replace($server, '', $css);
            preg_match_all("/([^:\}\s]+)\s*:\s*([^;\}]+);/", $matches[1][$key], $vars);
            foreach ($vars[1] as $var => $value) {
                $variables[$value] = $vars[2][$var];
            }
        }
        $css = str_replace(array_keys($variables), array_values($variables), $css);
    }
    echo $css;
}

/**
 * This CSS output function will link to CSS files rather than including them
 * inline.
 *
 * The single CSS files can then be edited and saved with interactive
 * CSS editors like CSSEdit. Any files that have a .php extension are still included
 * inline.
 *
 * @param array $files an array of the CSS fields that need to be output.
 * @param array $toreplace for convenience. If you are going to output the names
 *      of the css files, for debugging purposes, then you should output
 *      str_replace($toreplace, '', $file); because it looks prettier.
 * @return void
 */
function output_css_for_css_edit($files, $toreplace) {
    foreach ($files as $file) {
        $shortname = str_replace($toreplace, '', $file);
        echo '/* @group ' . $shortname . " */\n\n";
        if (strpos($file, '.css') !== false) {
            echo '@import url("' . $file . '");'."\n\n";
        } else {
            @include_once($file);
        }
        echo "/* @end */\n\n";
    }
}

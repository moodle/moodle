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
     * The renderer interfaces are defined by classes called moodle_..._renderer
     * where ... is the name of the module, which, will be defined in this file
     * for core parts of Moodle, and in a file called renderer.php for plugins.
     *
     * There is no separate interface definintion for renderers. Instead we
     * take advantage of PHP being a dynamic languages. The renderer returned
     * does not need to be a subclass of the moodle_..._renderer base class, it
     * just needs to impmenent the same interface. This is sometimes called
     * 'Duck typing'. For a tricky example, see {@link template_renderer} below.
     * renderer ob
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
 * the Moodle 2.0 relase. Therefore, this API will probably change.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
interface icon_finder {
    /**
     * Return the URL for an icon indentifed as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->pixpath/i/course.gif";
     * then old_icon_url('i/course'); will return the equivalent URL that is correct now.
     *
     * @param $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname);

    /**
     * Return the URL for an icon indentifed as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->modpixpath/$mod/icon.gif";
     * then mod_icon_url('icon', $mod); will return the equivalent URL that is correct now.
     *
     * @param $iconname the name of the icon.
     * @param $module the module the icon belongs to.
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
 * file, you cac just ingore those, and the following information for developers.
 *
 * Normally, to create an instance of this class, you should use the
 * {@link theme_config::load()} factory method to load a themes config.php file.
 * However, normally you don't need to bother, becuase moodle_page (that is, $PAGE)
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
     * Possbile values are:
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
     * Possbile values are:
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
     * if $standardsheets is true, the the plugin CSS is included with the
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
     * If Moodle encouters a general type of page that is not listed in your theme,
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
     * To promote conisitency, you are encouraged to call your layout files
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
     * the page, but in non-existant regions, they appear here. (Imaging, for example,
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
     * There are two functions avaiable that you may wish to use (defined in lib/outputlib.php):
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

        // We have to use the variable name $THEME (upper case) becuase that
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
     * @return moodle_renderer_base the requested renderer.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
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
     * Return the URL for an icon indentifed as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->pixpath/i/course.gif";
     * then old_icon_url('i/course'); will return the equivalent URL that is correct now.
     *
     * @param $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname) {
        return $this->get_icon_finder()->old_icon_url($iconname);
    }

    /**
     * Return the URL for an icon indentifed as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->modpixpath/$mod/icon.gif";
     * then mod_icon_url('icon', $mod); will return the equivalent URL that is correct now.
     *
     * @param $iconname the name of the icon.
     * @param $module the module the icon belongs to.
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
     * @param $themename the name of the theme to get meta tags from.
     * @param $page that page whose <head> is being output.
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
     * @param $page that page whose <head> is being output.
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

        // Check the the template exists.
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
     * Helper method used by {@link update_legacy_information()}. Update one entry
     * in the $this->pluginsheets array, based on the legacy $property propery.
     * @param $plugintype e.g. 'mod'.
     * @param $property e.g. 'modsheets'.
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

    /* Implement interface method. */
    public function old_icon_url($iconname) {
        global $CFG;
        if (file_exists($CFG->dirroot . '/pix/' . $iconname . '.png')) {
            return $CFG->httpswwwroot . '/pix/' . $iconname . '.png';
        } else {
            return $CFG->httpswwwroot . '/pix/' . $iconname . '.gif';
        }
    }

    /* Implement interface method. */
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

    /* Implement interface method. */
    public function old_icon_url($iconname) {
        global $CFG;
        if (file_exists($CFG->themedir . '/' . $this->themename . '/pix/' . $iconname . '.png')) {
            return $CFG->httpsthemewww . '/' . $this->themename . '/pix/' . $iconname . '.png';
        } else {
            return $CFG->httpsthemewww . '/' . $this->themename . '/pix/' . $iconname . '.gif';
        }
    }

    /* Implement interface method. */
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

    /* Implement interface method. */
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

    /* Implement interface method. */
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
    /* Implement the subclass method. */
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
    /* Implement the subclass method. */
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
     * @param moodle_page $page the page we are doing output for.
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

    /* Implement the interface method. */
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
     * @param moodle_page $page the page we are doing output for.
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

    /* Implement the subclass method. */
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
     * @param $opencontainers the xhtml_container_stack to use.
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

    protected function output_tag($tagname, $attributes, $contents) {
        return $this->output_start_tag($tagname, $attributes) . $contents .
                $this->output_end_tag($tagname);
    }
    protected function output_start_tag($tagname, $attributes) {
        return '<' . $tagname . $this->output_attributes($attributes) . '>';
    }
    protected function output_end_tag($tagname) {
        return '</' . $tagname . '>';
    }
    protected function output_empty_tag($tagname, $attributes) {
        return '<' . $tagname . $this->output_attributes($attributes) . ' />';
    }

    protected function output_attribute($name, $value) {
        $value = trim($value);
        if ($value || is_numeric($value)) { // We want 0 to be output.
            return ' ' . $name . '="' . $value . '"';
        }
    }
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
    public static function prepare_classes($classes) {
        if (is_array($classes)) {
            return implode(' ', array_unique($classes));
        }
        return $classes;
    }

    /**
     * Return the URL for an icon indentifed as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->pixpath/i/course.gif";
     * then old_icon_url('i/course'); will return the equivalent URL that is correct now.
     *
     * @param $iconname the name of the icon.
     * @return string the URL for that icon.
     */
    public function old_icon_url($iconname) {
        return $this->page->theme->old_icon_url($iconname);
    }

    /**
     * Return the URL for an icon indentifed as in pre-Moodle 2.0 code.
     *
     * Suppose you have old code like $url = "$CFG->modpixpath/$mod/icon.gif";
     * then mod_icon_url('icon', $mod); will return the equivalent URL that is correct now.
     *
     * @param $iconname the name of the icon.
     * @param $module the module the icon belongs to.
     * @return string the URL for that icon.
     */
    public function mod_icon_url($iconname, $module) {
        return $this->page->theme->mod_icon_url($iconname, $module);
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
    const contentstoken = '-@#-Contents-go-here-#@-';

    /**
     * Constructor
     * @param string $copiedclass the name of a class whose API we should be copying.
     * @param $searchpaths a list of folders to search for templates in.
     * @param $opencontainers the xhtml_container_stack to use.
     * @param moodle_page $page the page we are doing output for.
     */
    public function __construct($copiedclass, $searchpaths, $page) {
        parent::__construct($page);
        $this->copiedclass = new ReflectionClass($copiedclass);
        $this->searchpaths = $searchpaths;
    }

    /* PHP magic method implementation. */
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
     * @param $_template the full path to the template file.
     * @param $_namedarguments an array variable name => value, the variables
     *      that should be available to the template.
     * @return string the HTML to be output.
     */
    protected function render_template($_template, $_namedarguments) {
        // Note, we intentionally break the coding guidelines with regards to
        // local variable names used in this function, so that they do not clash
        // with the names of any variables being passed to the template.

        global $CFG, $SITE, $THEME, $USER;
        // The next lines are a bit tricky. The point is, here we are in a method
        // of a renderer class, and this object may, or may not, be the the same as
        // the global $OUTPUT object. When rendering the template, we want to use
        // this object. However, people writing Moodle code expect the current
        // rederer to be called $OUTPUT, not $this, so define a variable called
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
     * splitting the result, pusing the end onto the stack, then returning the start.
     * @param string $method the method that was called, with _start stripped off.
     * @param array $arguments the arguments that were passed to it.
     * @return string the HTML to be output.
     */
    protected function process_start($template, $arguments) {
        array_unshift($arguments, self::contentstoken);
        $html = $this->process_template($template, $arguments);
        list($start, $end) = explode(self::contentstoken, $html, 2);
        $this->opencontainers->push($template, $end);
        return $start;
    }

    /**
     * Handle methods like print_box_end, we just need to pop the end HTML from
     * the stack.
     * @param string $method the method that was called, with _end stripped off.
     * @param array $arguments not used. Assumed to be irrelevant.
     * @return string the HTML to be output.
     */
    protected function process_end($template, $arguments) {
        return $this->opencontainers->pop($template);
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
 * without the matching closing HTML, you should push the neccessary close tags
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
     * Push the close HTML for a recently opened container onto the stack.
     * @param string $type The type of container. This is checked when {@link pop()}
     *      is called and must match, otherwise a developer debug warning is output.
     * @param string $closehtml The HTML required to close the container.
     */
    public function push($type, $closehtml) {
        $container = new stdClass;
        $container->type = $type;
        $container->closehtml = $closehtml;
        if (debugging('', DEBUG_DEVELOPER)) {
            $this->log('Open', $type);
        }
        array_push($this->opencontainers, $container);
    }

    /**
     * Pop the HTML for the next closing container from the stack. The $type
     * must match the type passed when the container was opened, otherwise a
     * warning will be output.
     * @param string $type The type of container.
     * @return string the HTML requried to close the container.
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
        if (debugging('', DEBUG_DEVELOPER)) {
            $this->log('Close', $type);
        }
        return $container->closehtml;
    }

    /**
     * Close all but the last open container. This is useful in places like error
     * handling, where you want to close all the open containers (apart from <body>)
     * before outputting the error message.
     * @param $shouldbenone assert that the stack shold be empty now - causes a
     *      developer debug warning if it isn't.
     * @return string the HTML requried to close any open containers inside <body>.
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
     * Emergency fallback. If we get to the end of processing and not all
     * containers have been closed, output the rest with a developer debug warning.
     */
    public function __destruct() {
        if (empty($this->opencontainers)) {
            return;
        }

        debugging('<p>Some containers were left open. This suggests there is a nesting problem.</p>' .
                $this->output_log(), DEBUG_DEVELOPER);
        echo $this->pop_all_but_last();
        $container = array_pop($this->opencontainers);
        echo $container->closehtml;
    }

    protected function log($action, $type) {
        $this->log[] = '<li>' . $action . ' ' . $type . ' at:' .
                format_backtrace(debug_backtrace()) . '</li>';
    }

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
        $this->page->requires->js('lib/javascript-mod.php')->in_head();
        $this->page->requires->js('lib/overlib/overlib.js')->in_head();
        $this->page->requires->js('lib/overlib/overlib_cssstyle.js')->in_head();
        $this->page->requires->js('lib/cookies.js')->in_head();
        $this->page->requires->js_function_call('setTimeout', Array('fix_column_widths()', 20));

        $focus = $this->page->focuscontrol;
        if (!empty($focus)) {
            if(preg_match("#forms\['([a-zA-Z0-9]+)'\].elements\['([a-zA-Z0-9]+)'\]#", $focus, $matches)) {
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
     * if we are indevelope debug mode) that should be output in the footer area
     * of the page. Designed to be called in theme layout.php files.
     * @return string HTML fragment.
     */
    public function standard_footer_html() {
        // This function is normally called from a layout.php file in {@link header()}
        // but some of the content won't be known until later, so we return a placeholder
        // for now. This will be replaced with the real content in {@link footer()}.
        $output = self::PERFORMANCE_INFO_TOKEN;
        if (debugging()) {
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
     * @param string $messageclass The css class to put on the message that is
     *         being displayed to the user
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
                // No output yet it is safe to delivery the full arsenol of redirect methods
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
     * To control what is printed, you should set properies on $PAGE. If you
     * are familiar with the old {@link print_header()} function from Moodle 1.9
     * you will find that there are properties on $PAGE that correspond to most
     * of the old paramters to could be passed to print_header.
     *
     * Not that, in due course, the remaining $navigation, $menu paramters here
     * will be replaced by more properties of $PAGE, but that is still to do.
     *
     * @param $navigation legacy, like the old paramter to print_header. Will be
     *      removed when there is a $PAGE->... replacement.
     * @param $menu legacy, like the old paramter to print_header. Will be
     *      removed when there is a $PAGE->... replacement.
     * @return string HTML that you must output this, preferably immediately.
     */
    public function header($navigation = '', $menu='') {
    // TODO remove $navigation and $menu arguments - replace with $PAGE->navigation
        global $USER, $CFG;

        output_starting_hook();
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

    protected function render_page_template($templatefile, $menu, $navigation) {
        global $CFG, $SITE, $THEME, $USER;
        // The next lines are a bit tricky. The point is, here we are in a method
        // of a renderer class, and this object may, or may not, be the the same as
        // the global $OUTPUT object. When rendering the template, we want to use
        // this object. However, people writing Moodle code expect the current
        // rederer to be called $OUTPUT, not $this, so define a variable called
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
        $this->page->requires->get_top_of_body_code();

        echo '<table id="layout-table"><tr>';
        foreach ($lt as $column) {
            if ($column == 'left' && $this->page->blocks->region_has_content(BLOCK_POS_LEFT)) {
                echo '<td id="left-column" style="width: ' . $preferredwidthright . 'px; vertical-align: top;">';
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

            } else if ($column == 'right' && $this->page->blocks->region_has_content(BLOCK_POS_RIGHT)) {
                echo '<td id="right-column" style="width: ' . $preferredwidthright . 'px; vertical-align: top;">';
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

        $output = str_replace('</body>', self::END_HTML_TOKEN . '</body>', $output);
        // Make sure we use the correct doctype.
        $output = preg_replace('/(<!DOCTYPE.+?>)/s', $this->doctype(), $output);

        return $output;
    }

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
     * @param $controls an array like {@link block_contents::$controls}.
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
                    $this->output_empty_tag('img',  array('src' => $control['icon'],
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
     * @return string the HTML to be output.
     */
    function block($bc) {
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
     * @param $region the name of a region on this page.
     * @return string the HTML to be output.
     */
    public function blocks_for_region($region) {
        $blockcontents = $this->page->blocks->get_content_for_region($region, $this);
        $output = '';
        foreach ($blockcontents as $bc) {
            $output .= $this->block($bc);
        }
        return $output;
    }

    public function link_to_popup_window() {

    }

    public function button_to_popup_window() {

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
            'class' => $selectmenu->get_classes_string(),
            'onchange' => $selectmenu->script,
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

        $html = $this->output_start_tag('select', $attributes) . "\n";
        foreach ($selectmenu->options as $value => $label) {
            $attributes = array('value' => $value);
            if ((string)$value == (string)$selectmenu->selectedvalue ||
                    (is_array($selectmenu->selectedvalue) && in_array($value, $selectmenu->selectedvalue))) {
                $attributes['selected'] = 'selected';
            }
            $html .= '    ' . $this->output_tag('option', $attributes, s($label)) . "\n";
        }
        $html .= $this->output_end_tag('select') . "\n";

        return $html;
    }

    // TODO choose_from_menu_nested

    // TODO choose_from_radio

    /**
     * Output an error message. By default wraps the error message in <span class="error">.
     * If the error message is blank, nothing is output.
     * @param $message the error message.
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
     * funciton to display the error, before terminating the exection.
     *
     * @param string $message
     * @param string $moreinfourl
     * @param string $link
     * @param array $backtrace
     * @param string $debuginfo
     * @param bool $showerrordebugwarning
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
        return $this->output_tag('div', array('class' => 'continuebutton'),
                print_single_button($link->out(true), $link->params(), get_string('continue'), 'get', '', true));
    }

    /**
     * Output the place a skip link goes to.
     * @param $id The target name from the corresponding $PAGE->requires->skip_link_to($target) call.
     * @return string the HTML to output.
     */
    public function skip_link_target($id = '') {
        return $this->output_tag('span', array('id' => $id), '');
    }

    public function heading($text, $level, $classes = 'main', $id = '') {
        $level = (integer) $level;
        if ($level < 1 or $level > 6) {
            throw new coding_exception('Heading level must be an integer between 1 and 6.');
        }
        return $this->output_tag('h' . $level,
                array('id' => $id, 'class' => moodle_renderer_base::prepare_classes($classes)), $text);
    }

    public function box($contents, $classes = 'generalbox', $id = '') {
        return $this->box_start($classes, $id) . $contents . $this->box_end();
    }

    public function box_start($classes = 'generalbox', $id = '') {
        $this->opencontainers->push('box', $this->output_end_tag('div'));
        return $this->output_start_tag('div', array('id' => $id,
                'class' => 'box ' . moodle_renderer_base::prepare_classes($classes)));
    }

    public function box_end() {
        return $this->opencontainers->pop('box');
    }

    public function container($contents, $classes = '', $id = '') {
        return $this->container_start($classes, $id) . $contents . $this->container_end();
    }

    public function container_start($classes = '', $id = '') {
        $this->opencontainers->push('container', $this->output_end_tag('div'));
        return $this->output_start_tag('div', array('id' => $id,
                'class' => moodle_renderer_base::prepare_classes($classes)));
    }

    public function container_end() {
        return $this->opencontainers->pop('container');
    }
}


/**
 * Base class for classes representing HTML elements, like moodle_select_menu.
 *
 * Handles the id and class attribues.
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
     */
    public function set_classes($classes) {
        $this->classes = self::clean_classes($classes);
    }

    /**
     * Add a class name to the class names array.
     * @param string $class the new class name to add.
     */
    public function add_class($class) {
        $this->classes[] = $class;
    }

    /**
     * Add a whole lot of class names to the class names array.
     * @param mixed $classes either an array of class names or a space-separated
     *      string containing class names.
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
     */
    public function prepare() {
        $this->classes = array_unique(self::clean_classes($this->classes));
    }
}


/**
 * This class hold all the information required to describe a <select> menu that
 * will be printed by {@link moodle_core_renderer::select_menu()}. (Or by an overridden
 * version of that method in a subclass.)
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
     * @var array the choices to show in the menu. An array $value => $label.
     */
    public $options;
    /**
     * @var string the name of this form control. That is, the name of the GET/POST
     * variable that will be set if this select is submmitted as part of a form.
     */
    public $name;
    /**
     * @var string the option to select initially. Should match one
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
     * @deprecated
     * @var string JavaScript to add as an onchange attribute. Do not use this.
     * Use the YUI even library instead.
     */
    public $script = '';

    /* @see lib/moodle_html_component#prepare() */
    public function prepare() {
        if (empty($this->id)) {
            $this->id = 'menu' . str_replace(array('[', ']'), '', $this->name);
        }
        if (empty($this->classes)) {
            $this->set_classes(array('menu' . str_replace(array('[', ']'), '', $this->name)));
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
     * should be an array('url' => $url, 'icon' => $icon, 'caption' => $caption)
     * @var array
     */
    public $controls = array();

    /* @see lib/moodle_html_component#prepare() */
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
 * A renderer that generates output for commandlines scripts.
 *
 * The implementation of this renderer is probably incomplete.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since     Moodle 2.0
 */
class cli_core_renderer extends moodle_core_renderer {
    public function header() {
        output_starting_hook();
        return $this->page->heading . "\n";
    }

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
 * @param array $files an arry of the CSS fiels that need to be output.
 * @param array $toreplace for convenience. If you are going to output the names
 *      of the css files, for debugging purposes, then you should output
 *      str_replace($toreplace, '', $file); becuase it looks prettier.
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
 * @param array $files an arry of the CSS fiels that need to be output.
 * @param array $toreplace for convenience. If you are going to output the names
 *      of the css files, for debugging purposes, then you should output
 *      str_replace($toreplace, '', $file); becuase it looks prettier.
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

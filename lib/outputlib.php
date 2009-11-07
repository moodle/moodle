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

require_once($CFG->libdir.'/outputcomponents.php');
require_once($CFG->libdir.'/outputactions.php');
require_once($CFG->libdir.'/outputfactories.php');
require_once($CFG->libdir.'/outputrenderers.php');

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

    /*
     * Time in seconds to cache the CSS style sheets for the chosen theme
     *
     * @var integer
     */
    public $csslifetime = 1800;

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
        } else {
        	$blockmanager->set_default_region('');
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

        // TODO: MDL-20625 this looks dangerous and problematic because we never know
        //       the order of calling of constructors ==> the transaction warning will not be included

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

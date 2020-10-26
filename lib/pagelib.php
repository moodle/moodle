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
 * This file contains the moodle_page class. There is normally a single instance
 * of this class in the $PAGE global variable. This class is a central repository
 * of information about the page we are building up to send back to the user.
 *
 * @package core
 * @category page
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * $PAGE is a central store of information about the current page we are
 * generating in response to the user's request.
 *
 * It does not do very much itself
 * except keep track of information, however, it serves as the access point to
 * some more significant components like $PAGE->theme, $PAGE->requires,
 * $PAGE->blocks, etc.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category page
 *
 * The following properties are alphabetical. Please keep it that way so that its
 * easy to maintain.
 *
 * @property-read string $activityname The type of activity we are in, for example 'forum' or 'quiz'.
 *      Will be null if this page is not within a module.
 * @property-read stdClass $activityrecord The row from the activities own database table (for example
 *      the forum or quiz table) that this page belongs to. Will be null
 *      if this page is not within a module.
 * @property-read array $alternativeversions Mime type => object with ->url and ->title.
 * @property-read block_manager $blocks The blocks manager object for this page.
 * @property-read array $blockmanipulations
 * @property-read string $bodyclasses A string to use within the class attribute on the body tag.
 * @property-read string $bodyid A string to use as the id of the body tag.
 * @property-read string $button The HTML to go where the Turn editing on button normally goes.
 * @property-read bool $cacheable Defaults to true. Set to false to stop the page being cached at all.
 * @property-read array $categories An array of all the categories the page course belongs to,
 *      starting with the immediately containing category, and working out to
 *      the top-level category. This may be the empty array if we are in the
 *      front page course.
 * @property-read mixed $category The category that the page course belongs to.
 * @property-read cm_info $cm The course_module that this page belongs to. Will be null
 *      if this page is not within a module. This is a full cm object, as loaded
 *      by get_coursemodule_from_id or get_coursemodule_from_instance,
 *      so the extra modname and name fields are present.
 * @property-read context $context The main context to which this page belongs.
 * @property-read stdClass $course The current course that we are inside - a row from the
 *      course table. (Also available as $COURSE global.) If we are not inside
 *      an actual course, this will be the site course.
 * @property-read string $devicetypeinuse The name of the device type in use
 * @property-read string $docspath The path to the Moodle docs for this page.
 * @property-read string $focuscontrol The id of the HTML element to be focused when the page has loaded.
 * @property-read bool $headerprinted True if the page header has already been printed.
 * @property-read string $heading The main heading that should be displayed at the top of the <body>.
 * @property-read string $headingmenu The menu (or actions) to display in the heading
 * @property-read array $layout_options An arrays with options for the layout file.
 * @property-read array $legacythemeinuse True if the legacy browser theme is in use.
 * @property-read navbar $navbar The navbar object used to display the navbar
 * @property-read global_navigation $navigation The navigation structure for this page.
 * @property-read xhtml_container_stack $opencontainers Tracks XHTML tags on this page that have been opened but not closed.
 *      mainly for internal use by the rendering code.
 * @property-read string $pagelayout The general type of page this is. For example 'normal', 'popup', 'home'.
 *      Allows the theme to display things differently, if it wishes to.
 * @property-read string $pagetype The page type string, should be used as the id for the body tag in the theme.
 * @property-read int $periodicrefreshdelay The periodic refresh delay to use with meta refresh
 * @property-read page_requirements_manager $requires Tracks the JavaScript, CSS files, etc. required by this page.
 * @property-read string $requestip The IP address of the current request, null if unknown.
 * @property-read string $requestorigin The type of request 'web', 'ws', 'cli', 'restore', etc.
 * @property-read settings_navigation $settingsnav The settings navigation
 * @property-read int $state One of the STATE_... constants
 * @property-read string $subpage The subpage identifier, if any.
 * @property-read theme_config $theme The theme for this page.
 * @property-read string $title The title that should go in the <head> section of the HTML of this page.
 * @property-read moodle_url $url The moodle url object for this page.
 */
class moodle_page {

    /** The state of the page before it has printed the header **/
    const STATE_BEFORE_HEADER = 0;

    /** The state the page is in temporarily while the header is being printed **/
    const STATE_PRINTING_HEADER = 1;

    /** The state the page is in while content is presumably being printed **/
    const STATE_IN_BODY = 2;

    /**
     * The state the page is when the footer has been printed and its function is
     * complete.
     */
    const STATE_DONE = 3;

    /**
     * @var int The current state of the page. The state a page is within
     * determines what actions are possible for it.
     */
    protected $_state = self::STATE_BEFORE_HEADER;

    /**
     * @var stdClass The course currently associated with this page.
     * If not has been provided the front page course is used.
     */
    protected $_course = null;

    /**
     * @var cm_info If this page belongs to a module, this is the cm_info module
     * description object.
     */
    protected $_cm = null;

    /**
     * @var stdClass If $_cm is not null, then this will hold the corresponding
     * row from the modname table. For example, if $_cm->modname is 'quiz', this
     * will be a row from the quiz table.
     */
    protected $_module = null;

    /**
     * @var context The context that this page belongs to.
     */
    protected $_context = null;

    /**
     * @var array This holds any categories that $_course belongs to, starting with the
     * particular category it belongs to, and working out through any parent
     * categories to the top level. These are loaded progressively, if needed.
     * There are three states. $_categories = null initially when nothing is
     * loaded; $_categories = array($id => $cat, $parentid => null) when we have
     * loaded $_course->category, but not any parents; and a complete array once
     * everything is loaded.
     */
    protected $_categories = null;

    /**
     * @var array An array of CSS classes that should be added to the body tag in HTML.
     */
    protected $_bodyclasses = array();

    /**
     * @var string The title for the page. Used within the title tag in the HTML head.
     */
    protected $_title = '';

    /**
     * @var string The string to use as the heading of the page. Shown near the top of the
     * page within most themes.
     */
    protected $_heading = '';

    /**
     * @var string The pagetype is used to describe the page and defaults to a representation
     * of the physical path to the page e.g. my-index, mod-quiz-attempt
     */
    protected $_pagetype = null;

    /**
     * @var string The pagelayout to use when displaying this page. The
     * pagelayout needs to have been defined by the theme in use, or one of its
     * parents. By default base is used however standard is the more common layout.
     * Note that this gets automatically set by core during operations like
     * require_login.
     */
    protected $_pagelayout = 'base';

    /**
     * @var array List of theme layout options, these are ignored by core.
     * To be used in individual theme layout files only.
     */
    protected $_layout_options = null;

    /**
     * @var string An optional arbitrary parameter that can be set on pages where the context
     * and pagetype is not enough to identify the page.
     */
    protected $_subpage = '';

    /**
     * @var string Set a different path to use for the 'Moodle docs for this page' link.
     * By default, it uses the path of the file for instance mod/quiz/attempt.
     */
    protected $_docspath = null;

    /**
     * @var string A legacy class that will be added to the body tag
     */
    protected $_legacyclass = null;

    /**
     * @var moodle_url The URL for this page. This is mandatory and must be set
     * before output is started.
     */
    protected $_url = null;

    /**
     * @var array An array of links to alternative versions of this page.
     * Primarily used for RSS versions of the current page.
     */
    protected $_alternateversions = array();

    /**
     * @var block_manager The blocks manager for this page. It is responsible for
     * the blocks and there content on this page.
     */
    protected $_blocks = null;

    /**
     * @var page_requirements_manager Page requirements manager. It is responsible
     * for all JavaScript and CSS resources required by this page.
     */
    protected $_requires = null;

    /** @var page_requirements_manager Saves the requirement manager object used before switching to to fragments one. */
    protected $savedrequires = null;

    /**
     * @var string The capability required by the user in order to edit blocks
     * and block settings on this page.
     */
    protected $_blockseditingcap = 'moodle/site:manageblocks';

    /**
     * @var bool An internal flag to record when block actions have been processed.
     * Remember block actions occur on the current URL and it is important that
     * even they are never executed more than once.
     */
    protected $_block_actions_done = false;

    /**
     * @var array An array of any other capabilities the current user must have
     * in order to editing the page and/or its content (not just blocks).
     */
    protected $_othereditingcaps = array();

    /**
     * @var bool Sets whether this page should be cached by the browser or not.
     * If it is set to true (default) the page is served with caching headers.
     */
    protected $_cacheable = true;

    /**
     * @var string Can be set to the ID of an element on the page, if done that
     * element receives focus when the page loads.
     */
    protected $_focuscontrol = '';

    /**
     * @var string HTML to go where the turn on editing button is located. This
     * is nearly a legacy item and not used very often any more.
     */
    protected $_button = '';

    /**
     * @var theme_config The theme to use with this page. This has to be properly
     * initialised via {@link moodle_page::initialise_theme_and_output()} which
     * happens magically before any operation that requires it.
     */
    protected $_theme = null;

    /**
     * @var global_navigation Contains the global navigation structure.
     */
    protected $_navigation = null;

    /**
     * @var settings_navigation Contains the settings navigation structure.
     */
    protected $_settingsnav = null;

    /**
     * @var flat_navigation Contains a list of nav nodes, most closely related to the current page.
     */
    protected $_flatnav = null;

    /**
     * @var navbar Contains the navbar structure.
     */
    protected $_navbar = null;

    /**
     * @var string The menu (or actions) to display in the heading.
     */
    protected $_headingmenu = null;

    /**
     * @var array stack trace. Then the theme is initialised, we save the stack
     * trace, for use in error messages.
     */
    protected $_wherethemewasinitialised = null;

    /**
     * @var xhtml_container_stack Tracks XHTML tags on this page that have been
     * opened but not closed.
     */
    protected $_opencontainers;

    /**
     * @var int Sets the page to refresh after a given delay (in seconds) using
     * meta refresh in {@link standard_head_html()} in outputlib.php
     * If set to null(default) the page is not refreshed
     */
    protected $_periodicrefreshdelay = null;

    /**
     * @var array Associative array of browser shortnames (as used by check_browser_version)
     * and their minimum required versions
     */
    protected $_legacybrowsers = array('MSIE' => 6.0);

    /**
     * @var string Is set to the name of the device type in use.
     * This will we worked out when it is first used.
     */
    protected $_devicetypeinuse = null;

    /**
     * @var bool Used to determine if HTTPS should be required for login.
     */
    protected $_https_login_required = false;

    /**
     * @var bool Determines if popup notifications allowed on this page.
     * Code such as the quiz module disables popup notifications in situations
     * such as upgrading or completing a quiz.
     */
    protected $_popup_notification_allowed = true;

    /**
     * @var bool Is the settings menu being forced to display on this page (activities / resources only).
     * This is only used by themes that use the settings menu.
     */
    protected $_forcesettingsmenu = false;

    /**
     * @var array Array of header actions HTML to add to the page header actions menu.
     */
    protected $_headeractions = [];

    /**
     * @var bool Should the region main settings menu be rendered in the header.
     */
    protected $_regionmainsettingsinheader = false;

    /**
     * Force the settings menu to be displayed on this page. This will only force the
     * settings menu on an activity / resource page that is being displayed on a theme that
     * uses a settings menu.
     *
     * @param bool $forced default of true, can be sent false to turn off the force.
     */
    public function force_settings_menu($forced = true) {
        $this->_forcesettingsmenu = $forced;
    }

    /**
     * Check to see if the settings menu is forced to display on this activity / resource page.
     * This only applies to themes that use the settings menu.
     *
     * @return bool True if the settings menu is forced to display.
     */
    public function is_settings_menu_forced() {
        return $this->_forcesettingsmenu;
    }

    // Magic getter methods =============================================================
    // Due to the __get magic below, you normally do not call these as $PAGE->magic_get_x
    // methods, but instead use the $PAGE->x syntax.

    /**
     * Please do not call this method directly, use the ->state syntax. {@link moodle_page::__get()}.
     * @return integer one of the STATE_XXX constants. You should not normally need
     * to use this in your code. It is intended for internal use by this class
     * and its friends like print_header, to check that everything is working as
     * expected. Also accessible as $PAGE->state.
     */
    protected function magic_get_state() {
        return $this->_state;
    }

    /**
     * Please do not call this method directly, use the ->headerprinted syntax. {@link moodle_page::__get()}.
     * @return bool has the header already been printed?
     */
    protected function magic_get_headerprinted() {
        return $this->_state >= self::STATE_IN_BODY;
    }

    /**
     * Please do not call this method directly, use the ->course syntax. {@link moodle_page::__get()}.
     * @return stdClass the current course that we are inside - a row from the
     * course table. (Also available as $COURSE global.) If we are not inside
     * an actual course, this will be the site course.
     */
    protected function magic_get_course() {
        global $SITE;
        if (is_null($this->_course)) {
            return $SITE;
        }
        return $this->_course;
    }

    /**
     * Please do not call this method directly, use the ->cm syntax. {@link moodle_page::__get()}.
     * @return cm_info the course_module that this page belongs to. Will be null
     * if this page is not within a module. This is a full cm object, as loaded
     * by get_coursemodule_from_id or get_coursemodule_from_instance,
     * so the extra modname and name fields are present.
     */
    protected function magic_get_cm() {
        return $this->_cm;
    }

    /**
     * Please do not call this method directly, use the ->activityrecord syntax. {@link moodle_page::__get()}.
     * @return stdClass the row from the activities own database table (for example
     * the forum or quiz table) that this page belongs to. Will be null
     * if this page is not within a module.
     */
    protected function magic_get_activityrecord() {
        if (is_null($this->_module) && !is_null($this->_cm)) {
            $this->load_activity_record();
        }
        return $this->_module;
    }

    /**
     * Please do not call this method directly, use the ->activityname syntax. {@link moodle_page::__get()}.
     * @return string the The type of activity we are in, for example 'forum' or 'quiz'.
     * Will be null if this page is not within a module.
     */
    protected function magic_get_activityname() {
        if (is_null($this->_cm)) {
            return null;
        }
        return $this->_cm->modname;
    }

    /**
     * Please do not call this method directly, use the ->category syntax. {@link moodle_page::__get()}.
     * @return stdClass the category that the page course belongs to. If there isn't one
     * (that is, if this is the front page course) returns null.
     */
    protected function magic_get_category() {
        $this->ensure_category_loaded();
        if (!empty($this->_categories)) {
            return reset($this->_categories);
        } else {
            return null;
        }
    }

    /**
     * Please do not call this method directly, use the ->categories syntax. {@link moodle_page::__get()}.
     * @return array an array of all the categories the page course belongs to,
     * starting with the immediately containing category, and working out to
     * the top-level category. This may be the empty array if we are in the
     * front page course.
     */
    protected function magic_get_categories() {
        $this->ensure_categories_loaded();
        return $this->_categories;
    }

    /**
     * Please do not call this method directly, use the ->context syntax. {@link moodle_page::__get()}.
     * @return context the main context to which this page belongs.
     */
    protected function magic_get_context() {
        global $CFG;
        if (is_null($this->_context)) {
            if (CLI_SCRIPT or NO_MOODLE_COOKIES) {
                // Cli scripts work in system context, do not annoy devs with debug info.
                // Very few scripts do not use cookies, we can safely use system as default context there.
            } else if (AJAX_SCRIPT && $CFG->debugdeveloper) {
                // Throw exception inside AJAX script in developer mode, otherwise the debugging message may be missed.
                throw new coding_exception('$PAGE->context was not set. You may have forgotten '
                    .'to call require_login() or $PAGE->set_context()');
            } else {
                debugging('Coding problem: $PAGE->context was not set. You may have forgotten '
                    .'to call require_login() or $PAGE->set_context(). The page may not display '
                    .'correctly as a result');
            }
            $this->_context = context_system::instance();
        }
        return $this->_context;
    }

    /**
     * Please do not call this method directly, use the ->pagetype syntax. {@link moodle_page::__get()}.
     * @return string e.g. 'my-index' or 'mod-quiz-attempt'.
     */
    protected function magic_get_pagetype() {
        global $CFG;
        if (is_null($this->_pagetype) || isset($CFG->pagepath)) {
            $this->initialise_default_pagetype();
        }
        return $this->_pagetype;
    }

    /**
     * Please do not call this method directly, use the ->pagetype syntax. {@link moodle_page::__get()}.
     * @return string The id to use on the body tag, uses {@link magic_get_pagetype()}.
     */
    protected function magic_get_bodyid() {
        return 'page-'.$this->pagetype;
    }

    /**
     * Please do not call this method directly, use the ->pagelayout syntax. {@link moodle_page::__get()}.
     * @return string the general type of page this is. For example 'standard', 'popup', 'home'.
     *      Allows the theme to display things differently, if it wishes to.
     */
    protected function magic_get_pagelayout() {
        return $this->_pagelayout;
    }

    /**
     * Please do not call this method directly, use the ->layout_options syntax. {@link moodle_page::__get()}.
     * @return array returns arrays with options for layout file
     */
    protected function magic_get_layout_options() {
        if (!is_array($this->_layout_options)) {
            $this->_layout_options = $this->_theme->pagelayout_options($this->pagelayout);
        }
        return $this->_layout_options;
    }

    /**
     * Please do not call this method directly, use the ->subpage syntax. {@link moodle_page::__get()}.
     * @return string The subpage identifier, if any.
     */
    protected function magic_get_subpage() {
        return $this->_subpage;
    }

    /**
     * Please do not call this method directly, use the ->bodyclasses syntax. {@link moodle_page::__get()}.
     * @return string the class names to put on the body element in the HTML.
     */
    protected function magic_get_bodyclasses() {
        return implode(' ', array_keys($this->_bodyclasses));
    }

    /**
     * Please do not call this method directly, use the ->title syntax. {@link moodle_page::__get()}.
     * @return string the title that should go in the <head> section of the HTML of this page.
     */
    protected function magic_get_title() {
        return $this->_title;
    }

    /**
     * Please do not call this method directly, use the ->heading syntax. {@link moodle_page::__get()}.
     * @return string the main heading that should be displayed at the top of the <body>.
     */
    protected function magic_get_heading() {
        return $this->_heading;
    }

    /**
     * Please do not call this method directly, use the ->heading syntax. {@link moodle_page::__get()}.
     * @return string The menu (or actions) to display in the heading
     */
    protected function magic_get_headingmenu() {
        return $this->_headingmenu;
    }

    /**
     * Please do not call this method directly, use the ->docspath syntax. {@link moodle_page::__get()}.
     * @return string the path to the Moodle docs for this page.
     */
    protected function magic_get_docspath() {
        if (is_string($this->_docspath)) {
            return $this->_docspath;
        } else {
            return str_replace('-', '/', $this->pagetype);
        }
    }

    /**
     * Please do not call this method directly, use the ->url syntax. {@link moodle_page::__get()}.
     * @return moodle_url the clean URL required to load the current page. (You
     * should normally use this in preference to $ME or $FULLME.)
     */
    protected function magic_get_url() {
        global $FULLME;
        if (is_null($this->_url)) {
            debugging('This page did not call $PAGE->set_url(...). Using '.s($FULLME), DEBUG_DEVELOPER);
            $this->_url = new moodle_url($FULLME);
            // Make sure the guessed URL cannot lead to dangerous redirects.
            $this->_url->remove_params('sesskey');
        }
        return new moodle_url($this->_url); // Return a clone for safety.
    }

    /**
     * The list of alternate versions of this page.
     * @return array mime type => object with ->url and ->title.
     */
    protected function magic_get_alternateversions() {
        return $this->_alternateversions;
    }

    /**
     * Please do not call this method directly, use the ->blocks syntax. {@link moodle_page::__get()}.
     * @return block_manager the blocks manager object for this page.
     */
    protected function magic_get_blocks() {
        global $CFG;
        if (is_null($this->_blocks)) {
            if (!empty($CFG->blockmanagerclass)) {
                if (!empty($CFG->blockmanagerclassfile)) {
                    require_once($CFG->blockmanagerclassfile);
                }
                $classname = $CFG->blockmanagerclass;
            } else {
                $classname = 'block_manager';
            }
            $this->_blocks = new $classname($this);
        }
        return $this->_blocks;
    }

    /**
     * Please do not call this method directly, use the ->requires syntax. {@link moodle_page::__get()}.
     * @return page_requirements_manager tracks the JavaScript, CSS files, etc. required by this page.
     */
    protected function magic_get_requires() {
        if (is_null($this->_requires)) {
            $this->_requires = new page_requirements_manager();
        }
        return $this->_requires;
    }

    /**
     * Please do not call this method directly, use the ->cacheable syntax. {@link moodle_page::__get()}.
     * @return bool can this page be cached by the user's browser.
     */
    protected function magic_get_cacheable() {
        return $this->_cacheable;
    }

    /**
     * Please do not call this method directly, use the ->focuscontrol syntax. {@link moodle_page::__get()}.
     * @return string the id of the HTML element to be focused when the page has loaded.
     */
    protected function magic_get_focuscontrol() {
        return $this->_focuscontrol;
    }

    /**
     * Please do not call this method directly, use the ->button syntax. {@link moodle_page::__get()}.
     * @return string the HTML to go where the Turn editing on button normally goes.
     */
    protected function magic_get_button() {
        return $this->_button;
    }

    /**
     * Please do not call this method directly, use the ->theme syntax. {@link moodle_page::__get()}.
     * @return theme_config the initialised theme for this page.
     */
    protected function magic_get_theme() {
        if (is_null($this->_theme)) {
            $this->initialise_theme_and_output();
        }
        return $this->_theme;
    }

    /**
     * Returns an array of minipulations or false if there are none to make.
     *
     * @since Moodle 2.5.1 2.6
     * @return bool|array
     */
    protected function magic_get_blockmanipulations() {
        if (!right_to_left()) {
            return false;
        }
        if (is_null($this->_theme)) {
            $this->initialise_theme_and_output();
        }
        return $this->_theme->blockrtlmanipulations;
    }

    /**
     * Please do not call this method directly, use the ->devicetypeinuse syntax. {@link moodle_page::__get()}.
     * @return string The device type being used.
     */
    protected function magic_get_devicetypeinuse() {
        if (empty($this->_devicetypeinuse)) {
            $this->_devicetypeinuse = core_useragent::get_user_device_type();
        }
        return $this->_devicetypeinuse;
    }

    /**
     * Please do not call this method directly use the ->periodicrefreshdelay syntax
     * {@link moodle_page::__get()}
     * @return int The periodic refresh delay to use with meta refresh
     */
    protected function magic_get_periodicrefreshdelay() {
        return $this->_periodicrefreshdelay;
    }

    /**
     * Please do not call this method directly use the ->opencontainers syntax. {@link moodle_page::__get()}
     * @return xhtml_container_stack tracks XHTML tags on this page that have been opened but not closed.
     *      mainly for internal use by the rendering code.
     */
    protected function magic_get_opencontainers() {
        if (is_null($this->_opencontainers)) {
            $this->_opencontainers = new xhtml_container_stack();
        }
        return $this->_opencontainers;
    }

    /**
     * Return the navigation object
     * @return global_navigation
     */
    protected function magic_get_navigation() {
        if ($this->_navigation === null) {
            $this->_navigation = new global_navigation($this);
        }
        return $this->_navigation;
    }

    /**
     * Return a navbar object
     * @return navbar
     */
    protected function magic_get_navbar() {
        if ($this->_navbar === null) {
            $this->_navbar = new navbar($this);
        }
        return $this->_navbar;
    }

    /**
     * Returns the settings navigation object
     * @return settings_navigation
     */
    protected function magic_get_settingsnav() {
        if ($this->_settingsnav === null) {
            $this->_settingsnav = new settings_navigation($this);
            $this->_settingsnav->initialise();
        }
        return $this->_settingsnav;
    }

    /**
     * Returns the flat navigation object
     * @return flat_navigation
     */
    protected function magic_get_flatnav() {
        if ($this->_flatnav === null) {
            $this->_flatnav = new flat_navigation($this);
            $this->_flatnav->initialise();
        }
        return $this->_flatnav;
    }

    /**
     * Returns request IP address.
     *
     * @return string IP address or null if unknown
     */
    protected function magic_get_requestip() {
        return getremoteaddr(null);
    }

    /**
     * Returns the origin of current request.
     *
     * Note: constants are not required because we need to use these values in logging and reports.
     *
     * @return string 'web', 'ws', 'cli', 'restore', etc.
     */
    protected function magic_get_requestorigin() {
        if (class_exists('restore_controller', false) && restore_controller::is_executing()) {
            return 'restore';
        }

        if (WS_SERVER) {
            return 'ws';
        }

        if (CLI_SCRIPT) {
            return 'cli';
        }

        return 'web';
    }

    /**
     * PHP overloading magic to make the $PAGE->course syntax work by redirecting
     * it to the corresponding $PAGE->magic_get_course() method if there is one, and
     * throwing an exception if not.
     *
     * @param string $name property name
     * @return mixed
     * @throws coding_exception
     */
    public function __get($name) {
        $getmethod = 'magic_get_' . $name;
        if (method_exists($this, $getmethod)) {
            return $this->$getmethod();
        } else {
            throw new coding_exception('Unknown property ' . $name . ' of $PAGE.');
        }
    }

    /**
     * PHP overloading magic to catch obvious coding errors.
     *
     * This method has been created to catch obvious coding errors where the
     * developer has tried to set a page property using $PAGE->key = $value.
     * In the moodle_page class all properties must be set using the appropriate
     * $PAGE->set_something($value) method.
     *
     * @param string $name property name
     * @param mixed $value Value
     * @return void Throws exception if field not defined in page class
     * @throws coding_exception
     */
    public function __set($name, $value) {
        if (method_exists($this, 'set_' . $name)) {
            throw new coding_exception('Invalid attempt to modify page object', "Use \$PAGE->set_$name() instead.");
        } else {
            throw new coding_exception('Invalid attempt to modify page object', "Unknown property $name");
        }
    }

    // Other information getting methods ==========================================.

    /**
     * Returns instance of page renderer
     *
     * @param string $component name such as 'core', 'mod_forum' or 'qtype_multichoice'.
     * @param string $subtype optional subtype such as 'news' resulting to 'mod_forum_news'
     * @param string $target one of rendering target constants
     * @return renderer_base
     */
    public function get_renderer($component, $subtype = null, $target = null) {
        if ($this->pagelayout === 'maintenance') {
            // If the page is using the maintenance layout then we're going to force target to maintenance.
            // This leads to a special core renderer that is designed to block access to API's that are likely unavailable for this
            // page layout.
            $target = RENDERER_TARGET_MAINTENANCE;
        }
        return $this->magic_get_theme()->get_renderer($this, $component, $subtype, $target);
    }

    /**
     * Checks to see if there are any items on the navbar object
     *
     * @return bool true if there are, false if not
     */
    public function has_navbar() {
        if ($this->_navbar === null) {
            $this->_navbar = new navbar($this);
        }
        return $this->_navbar->has_items();
    }

    /**
     * Switches from the regular requirements manager to the fragment requirements manager to
     * capture all necessary JavaScript to display a chunk of HTML such as an mform. This is for use
     * by the get_fragment() web service and not for use elsewhere.
     */
    public function start_collecting_javascript_requirements() {
        global $CFG;
        require_once($CFG->libdir.'/outputfragmentrequirementslib.php');

        // Check that the requirements manager has not already been switched.
        if (get_class($this->_requires) == 'fragment_requirements_manager') {
            throw new coding_exception('JavaScript collection has already been started.');
        }
        // The header needs to have been called to flush out the generic JavaScript for the page. This allows only
        // JavaScript for the fragment to be collected. _wherethemewasinitialised is set when header() is called.
        if (!empty($this->_wherethemewasinitialised)) {
            // Change the current requirements manager over to the fragment manager to capture JS.
            $this->savedrequires = $this->_requires;
            $this->_requires = new fragment_requirements_manager();
        } else {
            throw new coding_exception('$OUTPUT->header() needs to be called before collecting JavaScript requirements.');
        }
    }

    /**
     * Switches back from collecting fragment JS requirement to the original requirement manager
     */
    public function end_collecting_javascript_requirements() {
        if ($this->savedrequires === null) {
            throw new coding_exception('JavaScript collection has not been started.');
        }
        $this->_requires = $this->savedrequires;
        $this->savedrequires = null;
    }

    /**
     * Should the current user see this page in editing mode.
     * That is, are they allowed to edit this page, and are they currently in
     * editing mode.
     * @return bool
     */
    public function user_is_editing() {
        global $USER;
        return !empty($USER->editing) && $this->user_allowed_editing();
    }

    /**
     * Does the user have permission to edit blocks on this page.
     * @return bool
     */
    public function user_can_edit_blocks() {
        return has_capability($this->_blockseditingcap, $this->_context);
    }

    /**
     * Does the user have permission to see this page in editing mode.
     * @return bool
     */
    public function user_allowed_editing() {
        return has_any_capability($this->all_editing_caps(), $this->_context);
    }

    /**
     * Get a description of this page. Normally displayed in the footer in developer debug mode.
     * @return string
     */
    public function debug_summary() {
        $summary = '';
        $summary .= 'General type: ' . $this->pagelayout . '. ';
        if (!during_initial_install()) {
            $summary .= 'Context ' . $this->context->get_context_name() . ' (context id ' . $this->_context->id . '). ';
        }
        $summary .= 'Page type ' . $this->pagetype .  '. ';
        if ($this->subpage) {
            $summary .= 'Sub-page ' . $this->subpage .  '. ';
        }
        return $summary;
    }

    // Setter methods =============================================================.

    /**
     * Set the state.
     *
     * The state must be one of that STATE_... constants, and the state is only allowed to advance one step at a time.
     *
     * @param int $state The new state.
     * @throws coding_exception
     */
    public function set_state($state) {
        if ($state != $this->_state + 1 || $state > self::STATE_DONE) {
            throw new coding_exception('Invalid state passed to moodle_page::set_state. We are in state ' .
                    $this->_state . ' and state ' . $state . ' was requested.');
        }

        if ($state == self::STATE_PRINTING_HEADER) {
            $this->starting_output();
        }

        $this->_state = $state;
    }

    /**
     * Set the current course. This sets both $PAGE->course and $COURSE. It also
     * sets the right theme and locale.
     *
     * Normally you don't need to call this function yourself, require_login will
     * call it for you if you pass a $course to it. You can use this function
     * on pages that do need to call require_login().
     *
     * Sets $PAGE->context to the course context, if it is not already set.
     *
     * @param stdClass $course the course to set as the global course.
     * @throws coding_exception
     */
    public function set_course($course) {
        global $COURSE, $PAGE, $CFG, $SITE;

        if (empty($course->id)) {
            throw new coding_exception('$course passed to moodle_page::set_course does not look like a proper course object.');
        }

        $this->ensure_theme_not_set();

        if (!empty($this->_course->id) && $this->_course->id != $course->id) {
            $this->_categories = null;
        }

        $this->_course = clone($course);

        if ($this === $PAGE) {
            $COURSE = $this->_course;
            moodle_setlocale();
        }

        if (!$this->_context) {
            $this->set_context(context_course::instance($this->_course->id));
        }

        // Notify course format that this page is set for the course.
        if ($this->_course->id != $SITE->id) {
            require_once($CFG->dirroot.'/course/lib.php');
            $courseformat = course_get_format($this->_course);
            $this->add_body_class('format-'. $courseformat->get_format());
            $courseformat->page_set_course($this);
        } else {
            $this->add_body_class('format-site');
        }
    }

    /**
     * Set the main context to which this page belongs.
     *
     * @param context $context a context object. You normally get this with context_xxxx::instance().
     */
    public function set_context($context) {
        if ($context === null) {
            // Extremely ugly hack which sets context to some value in order to prevent warnings,
            // use only for core error handling!!!!
            if (!$this->_context) {
                $this->_context = context_system::instance();
            }
            return;
        }
        // Ideally we should set context only once.
        if (isset($this->_context) && $context->id !== $this->_context->id) {
            $current = $this->_context->contextlevel;
            if ($current == CONTEXT_SYSTEM or $current == CONTEXT_COURSE) {
                // Hmm - not ideal, but it might produce too many warnings due to the design of require_login.
            } else if ($current == CONTEXT_MODULE and ($parentcontext = $context->get_parent_context()) and
                $this->_context->id == $parentcontext->id) {
                // Hmm - most probably somebody did require_login() and after that set the block context.
            } else {
                // We do not want devs to do weird switching of context levels on the fly because we might have used
                // the context already such as in text filter in page title.
                debugging("Coding problem: unsupported modification of PAGE->context from {$current} to {$context->contextlevel}");
            }
        }

        $this->_context = $context;
    }

    /**
     * The course module that this page belongs to (if it does belong to one).
     *
     * @param stdClass|cm_info $cm a record from course_modules table or cm_info from get_fast_modinfo().
     * @param stdClass $course
     * @param stdClass $module
     * @return void
     * @throws coding_exception
     */
    public function set_cm($cm, $course = null, $module = null) {
        global $DB, $CFG, $SITE;

        if (!isset($cm->id) || !isset($cm->course)) {
            throw new coding_exception('Invalid $cm. It has to be instance of cm_info or record from the course_modules table.');
        }

        if (!$this->_course || $this->_course->id != $cm->course) {
            if (!$course) {
                $course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
            }
            if ($course->id != $cm->course) {
                throw new coding_exception('The course you passed to $PAGE->set_cm does not correspond to the $cm.');
            }
            $this->set_course($course);
        }

        // Make sure we have a $cm from get_fast_modinfo as this contains activity access details.
        if (!($cm instanceof cm_info)) {
            $modinfo = get_fast_modinfo($this->_course);
            $cm = $modinfo->get_cm($cm->id);
        }
        $this->_cm = $cm;

        // Unfortunately the context setting is a mess.
        // Let's try to work around some common block problems and show some debug messages.
        if (empty($this->_context) or $this->_context->contextlevel != CONTEXT_BLOCK) {
            $context = context_module::instance($cm->id);
            $this->set_context($context);
        }

        if ($module) {
            $this->set_activity_record($module);
        }

        // Notify course format that this page is set for the course module.
        if ($this->_course->id != $SITE->id) {
            require_once($CFG->dirroot.'/course/lib.php');
            course_get_format($this->_course)->page_set_cm($this);
        }
    }

    /**
     * Sets the activity record. This could be a row from the main table for a
     * module. For instance if the current module (cm) is a forum this should be a row
     * from the forum table.
     *
     * @param stdClass $module A row from the main database table for the module that this page belongs to.
     * @throws coding_exception
     */
    public function set_activity_record($module) {
        if (is_null($this->_cm)) {
            throw new coding_exception('You cannot call $PAGE->set_activity_record until after $PAGE->cm has been set.');
        }
        if ($module->id != $this->_cm->instance || $module->course != $this->_course->id) {
            throw new coding_exception('The activity record does not seem to correspond to the cm that has been set.');
        }
        $this->_module = $module;
    }

    /**
     * Sets the pagetype to use for this page.
     *
     * Normally you do not need to set this manually, it is automatically created
     * from the script name. However, on some pages this is overridden.
     * For example the page type for course/view.php includes the course format,
     * for example 'course-view-weeks'. This gets used as the id attribute on
     * <body> and also for determining which blocks are displayed.
     *
     * @param string $pagetype e.g. 'my-index' or 'mod-quiz-attempt'.
     */
    public function set_pagetype($pagetype) {
        $this->_pagetype = $pagetype;
    }

    /**
     * Sets the layout to use for this page.
     *
     * The page layout determines how the page will be displayed, things such as
     * block regions, content areas, etc are controlled by the layout.
     * The theme in use for the page will determine that the layout contains.
     *
     * This properly defaults to 'base', so you only need to call this function if
     * you want something different. The exact range of supported layouts is specified
     * in the standard theme.
     *
     * For an idea of the common page layouts see
     * {@link http://docs.moodle.org/dev/Themes_2.0#The_different_layouts_as_of_August_17th.2C_2010}
     * But please keep in mind that it may be (and normally is) out of date.
     * The only place to find an accurate up-to-date list of the page layouts
     * available for your version of Moodle is {@link theme/base/config.php}
     *
     * @param string $pagelayout the page layout this is. For example 'popup', 'home'.
     */
    public function set_pagelayout($pagelayout) {
        global $SESSION;

        if (!empty($SESSION->forcepagelayout)) {
            $this->_pagelayout = $SESSION->forcepagelayout;
        } else {
            // Uncomment this to debug theme pagelayout issues like missing blocks.
            // if (!empty($this->_wherethemewasinitialised) && $pagelayout != $this->_pagelayout)
            //     debugging('Page layout has already been set and cannot be changed.', DEBUG_DEVELOPER);
            $this->_pagelayout = $pagelayout;
        }
    }

    /**
     * If context->id and pagetype are not enough to uniquely identify this page,
     * then you can set a subpage id as well. For example, the tags page sets
     *
     * @param string $subpage an arbitrary identifier that, along with context->id
     *      and pagetype, uniquely identifies this page.
     */
    public function set_subpage($subpage) {
        if (empty($subpage)) {
            $this->_subpage = '';
        } else {
            $this->_subpage = $subpage;
        }
    }

    /**
     * Adds a CSS class to the body tag of the page.
     *
     * @param string $class add this class name ot the class attribute on the body tag.
     * @throws coding_exception
     */
    public function add_body_class($class) {
        if ($this->_state > self::STATE_BEFORE_HEADER) {
            throw new coding_exception('Cannot call moodle_page::add_body_class after output has been started.');
        }
        $this->_bodyclasses[$class] = 1;
    }

    /**
     * Adds an array of body classes to the body tag of this page.
     *
     * @param array $classes this utility method calls add_body_class for each array element.
     */
    public function add_body_classes($classes) {
        foreach ($classes as $class) {
            $this->add_body_class($class);
        }
    }

    /**
     * Sets the title for the page.
     * This is normally used within the title tag in the head of the page.
     *
     * @param string $title the title that should go in the <head> section of the HTML of this page.
     */
    public function set_title($title) {
        $title = format_string($title);
        $title = strip_tags($title);
        $title = str_replace('"', '&quot;', $title);
        $this->_title = $title;
    }

    /**
     * Sets the heading to use for the page.
     * This is normally used as the main heading at the top of the content.
     *
     * @param string $heading the main heading that should be displayed at the top of the <body>.
     */
    public function set_heading($heading) {
        $this->_heading = format_string($heading);
    }

    /**
     * Sets some HTML to use next to the heading {@link moodle_page::set_heading()}
     *
     * @param string $menu The menu/content to show in the heading
     */
    public function set_headingmenu($menu) {
        $this->_headingmenu = $menu;
    }

    /**
     * Set the course category this page belongs to manually.
     *
     * This automatically sets $PAGE->course to be the site course. You cannot
     * use this method if you have already set $PAGE->course - in that case,
     * the category must be the one that the course belongs to. This also
     * automatically sets the page context to the category context.
     *
     * @param int $categoryid The id of the category to set.
     * @throws coding_exception
     */
    public function set_category_by_id($categoryid) {
        global $SITE;
        if (!is_null($this->_course)) {
            throw new coding_exception('Course has already been set. You cannot change the category now.');
        }
        if (is_array($this->_categories)) {
            throw new coding_exception('Course category has already been set. You cannot to change it now.');
        }
        $this->ensure_theme_not_set();
        $this->set_course($SITE);
        $this->load_category($categoryid);
        $this->set_context(context_coursecat::instance($categoryid));
    }

    /**
     * Set a different path to use for the 'Moodle docs for this page' link.
     *
     * By default, it uses the pagetype, which is normally the same as the
     * script name. So, for example, for mod/quiz/attempt.php, pagetype is
     * mod-quiz-attempt, and so docspath is mod/quiz/attempt.
     *
     * @param string $path the path to use at the end of the moodle docs URL.
     */
    public function set_docs_path($path) {
        $this->_docspath = $path;
    }

    /**
     * You should call this method from every page to set the URL that should be used to return to this page.
     *
     * Used, for example, by the blocks editing UI to know where to return the
     * user after an action.
     * For example, course/view.php does:
     *      $id = optional_param('id', 0, PARAM_INT);
     *      $PAGE->set_url('/course/view.php', array('id' => $id));
     *
     * @param moodle_url|string $url URL relative to $CFG->wwwroot or {@link moodle_url} instance
     * @param array $params parameters to add to the URL
     * @throws coding_exception
     */
    public function set_url($url, array $params = null) {
        global $CFG;

        if (is_string($url) && strpos($url, 'http') !== 0) {
            if (strpos($url, '/') === 0) {
                // Add the wwwroot to the relative url.
                $url = $CFG->wwwroot . $url;
            } else {
                throw new coding_exception('Invalid parameter $url, has to be full url or in shortened form starting with /.');
            }
        }

        $this->_url = new moodle_url($url, $params);

        $fullurl = $this->_url->out_omit_querystring();
        if (strpos($fullurl, "$CFG->wwwroot/") !== 0) {
            debugging('Most probably incorrect set_page() url argument, it does not match the wwwroot!');
        }
        $shorturl = str_replace("$CFG->wwwroot/", '', $fullurl);

        if (is_null($this->_pagetype)) {
            $this->initialise_default_pagetype($shorturl);
        }
    }

    /**
     * Make sure page URL does not contain the given URL parameter.
     *
     * This should not be necessary if the script has called set_url properly.
     * However, in some situations like the block editing actions; when the URL
     * has been guessed, it will contain dangerous block-related actions.
     * Therefore, the blocks code calls this function to clean up such parameters
     * before doing any redirect.
     *
     * @param string $param the name of the parameter to make sure is not in the
     * page URL.
     */
    public function ensure_param_not_in_url($param) {
        $this->_url->remove_params($param);
    }

    /**
     * Sets an alternative version of this page.
     *
     * There can be alternate versions of some pages (for example an RSS feed version).
     * Call this method for each alternative version available.
     * For each alternative version a link will be included in the <head> tag.
     *
     * @param string $title The title to give the alternate version.
     * @param string|moodle_url $url The URL of the alternate version.
     * @param string $mimetype The mime-type of the alternate version.
     * @throws coding_exception
     */
    public function add_alternate_version($title, $url, $mimetype) {
        if ($this->_state > self::STATE_BEFORE_HEADER) {
            throw new coding_exception('Cannot call moodle_page::add_alternate_version after output has been started.');
        }
        $alt = new stdClass;
        $alt->title = $title;
        $alt->url = $url;
        $this->_alternateversions[$mimetype] = $alt;
    }

    /**
     * Specify a form control should be focused when the page has loaded.
     *
     * @param string $controlid the id of the HTML element to be focused.
     */
    public function set_focuscontrol($controlid) {
        $this->_focuscontrol = $controlid;
    }

    /**
     * Specify a fragment of HTML that goes where the 'Turn editing on' button normally goes.
     *
     * @param string $html the HTML to display there.
     */
    public function set_button($html) {
        $this->_button = $html;
    }

    /**
     * Set the capability that allows users to edit blocks on this page.
     *
     * Normally the default of 'moodle/site:manageblocks' is used, but a few
     * pages like the My Moodle page need to use a different capability
     * like 'moodle/my:manageblocks'.
     *
     * @param string $capability a capability.
     */
    public function set_blocks_editing_capability($capability) {
        $this->_blockseditingcap = $capability;
    }

    /**
     * Some pages let you turn editing on for reasons other than editing blocks.
     * If that is the case, you can pass other capabilities that let the user
     * edit this page here.
     *
     * @param string|array $capability either a capability, or an array of capabilities.
     */
    public function set_other_editing_capability($capability) {
        if (is_array($capability)) {
            $this->_othereditingcaps = array_unique($this->_othereditingcaps + $capability);
        } else {
            $this->_othereditingcaps[] = $capability;
        }
    }

    /**
     * Sets whether the browser should cache this page or not.
     *
     * @param bool $cacheable can this page be cached by the user's browser.
     */
    public function set_cacheable($cacheable) {
        $this->_cacheable = $cacheable;
    }

    /**
     * Sets the page to periodically refresh
     *
     * This function must be called before $OUTPUT->header has been called or
     * a coding exception will be thrown.
     *
     * @param int $delay Sets the delay before refreshing the page, if set to null refresh is cancelled.
     * @throws coding_exception
     */
    public function set_periodic_refresh_delay($delay = null) {
        if ($this->_state > self::STATE_BEFORE_HEADER) {
            throw new coding_exception('You cannot set a periodic refresh delay after the header has been printed');
        }
        if ($delay === null) {
            $this->_periodicrefreshdelay = null;
        } else if (is_int($delay)) {
            $this->_periodicrefreshdelay = $delay;
        }
    }

    /**
     * Force this page to use a particular theme.
     *
     * Please use this cautiously.
     * It is only intended to be used by the themes selector admin page.
     *
     * @param string $themename the name of the theme to use.
     */
    public function force_theme($themename) {
        $this->ensure_theme_not_set();
        $this->_theme = theme_config::load($themename);
    }

    /**
     * Reload theme settings.
     *
     * This is used when we need to reset settings
     * because they are now double cached in theme.
     */
    public function reload_theme() {
        if (!is_null($this->_theme)) {
            $this->_theme = theme_config::load($this->_theme->name);
        }
    }

    /**
     * @deprecated since Moodle 3.4
     */
    public function https_required() {
        throw new coding_exception('https_required() cannot be used anymore.');
    }

    /**
     * @deprecated since Moodle 3.4
     */
    public function verify_https_required() {
        throw new coding_exception('verify_https_required() cannot be used anymore.');
    }

    // Initialisation methods =====================================================
    // These set various things up in a default way.

    /**
     * This method is called when the page first moves out of the STATE_BEFORE_HEADER
     * state. This is our last change to initialise things.
     */
    protected function starting_output() {
        global $CFG;

        if (!during_initial_install()) {
            $this->blocks->load_blocks();
            if (empty($this->_block_actions_done)) {
                $this->_block_actions_done = true;
                if ($this->blocks->process_url_actions($this)) {
                    redirect($this->url->out(false));
                }
            }
            $this->blocks->create_all_block_instances();
        }

        // If maintenance mode is on, change the page header.
        if (!empty($CFG->maintenance_enabled)) {
            $this->set_button('<a href="' . $CFG->wwwroot . '/' . $CFG->admin .
                    '/settings.php?section=maintenancemode">' . get_string('maintenancemode', 'admin') .
                    '</a> ' . $this->button);

            $title = $this->title;
            if ($title) {
                $title .= ' - ';
            }
            $this->set_title($title . get_string('maintenancemode', 'admin'));
        }

        $this->initialise_standard_body_classes();
    }

    /**
     * Method for use by Moodle core to set up the theme. Do not
     * use this in your own code.
     *
     * Make sure the right theme for this page is loaded. Tell our
     * blocks_manager about the theme block regions, and then, if
     * we are $PAGE, set up the global $OUTPUT.
     *
     * @return void
     */
    public function initialise_theme_and_output() {
        global $OUTPUT, $PAGE, $SITE, $CFG;

        if (!empty($this->_wherethemewasinitialised)) {
            return;
        }

        if (!during_initial_install()) {
            // Detect PAGE->context mess.
            $this->magic_get_context();
        }

        if (!$this->_course && !during_initial_install()) {
            $this->set_course($SITE);
        }

        if (is_null($this->_theme)) {
            $themename = $this->resolve_theme();
            $this->_theme = theme_config::load($themename);
        }

        $this->_theme->setup_blocks($this->pagelayout, $this->blocks);

        if ($this === $PAGE) {
            $target = null;
            if ($this->pagelayout === 'maintenance') {
                // If the page is using the maintenance layout then we're going to force target to maintenance.
                // This leads to a special core renderer that is designed to block access to API's that are likely unavailable for this
                // page layout.
                $target = RENDERER_TARGET_MAINTENANCE;
            }
            $OUTPUT = $this->get_renderer('core', null, $target);
        }

        if (!during_initial_install()) {
            $filtermanager = filter_manager::instance();
            $filtermanager->setup_page_for_globally_available_filters($this);
        }

        $this->_wherethemewasinitialised = debug_backtrace();
    }

    /**
     * For diagnostic/debugging purposes, find where the theme setup was triggered.
     *
     * @return null|array null if theme not yet setup. Stacktrace if it was.
     */
    public function get_where_theme_was_initialised() {
        return $this->_wherethemewasinitialised;
    }

    /**
     * Reset the theme and output for a new context. This only makes sense from
     * external::validate_context(). Do not cheat.
     *
     * @return string the name of the theme that should be used on this page.
     */
    public function reset_theme_and_output() {
        global $COURSE, $SITE;

        $COURSE = clone($SITE);
        $this->_theme = null;
        $this->_wherethemewasinitialised = null;
        $this->_course = null;
        $this->_cm = null;
        $this->_module = null;
        $this->_context = null;
    }

    /**
     * Work out the theme this page should use.
     *
     * This depends on numerous $CFG settings, and the properties of this page.
     *
     * @return string the name of the theme that should be used on this page.
     */
    protected function resolve_theme() {
        global $CFG, $USER, $SESSION;

        if (empty($CFG->themeorder)) {
            $themeorder = array('course', 'category', 'session', 'user', 'cohort', 'site');
        } else {
            $themeorder = $CFG->themeorder;
            // Just in case, make sure we always use the site theme if nothing else matched.
            $themeorder[] = 'site';
        }

        $mnetpeertheme = '';
        $mnetvarsok = isset($CFG->mnet_localhost_id) && isset($USER->mnethostid);
        if (isloggedin() and $mnetvarsok and $USER->mnethostid != $CFG->mnet_localhost_id) {
            require_once($CFG->dirroot.'/mnet/peer.php');
            $mnetpeer = new mnet_peer();
            $mnetpeer->set_id($USER->mnethostid);
            if ($mnetpeer->force_theme == 1 && $mnetpeer->theme != '') {
                $mnetpeertheme = $mnetpeer->theme;
            }
        }

        $devicetheme = core_useragent::get_device_type_theme($this->devicetypeinuse);

        // The user is using another device than default, and we have a theme for that, we should use it.
        $hascustomdevicetheme = core_useragent::DEVICETYPE_DEFAULT != $this->devicetypeinuse && !empty($devicetheme);

        foreach ($themeorder as $themetype) {

            switch ($themetype) {
                case 'course':
                    if (!empty($CFG->allowcoursethemes) && !empty($this->_course->theme) && !$hascustomdevicetheme) {
                        return $this->_course->theme;
                    }
                break;

                case 'category':
                    if (!empty($CFG->allowcategorythemes) && !$hascustomdevicetheme) {
                        $categories = $this->categories;
                        foreach ($categories as $category) {
                            if (!empty($category->theme)) {
                                return $category->theme;
                            }
                        }
                    }
                break;

                case 'session':
                    if (!empty($SESSION->theme)) {
                        return $SESSION->theme;
                    }
                break;

                case 'user':
                    if (!empty($CFG->allowuserthemes) && !empty($USER->theme) && !$hascustomdevicetheme) {
                        if ($mnetpeertheme) {
                            return $mnetpeertheme;
                        } else {
                            return $USER->theme;
                        }
                    }
                break;

                case 'cohort':
                    if (!empty($CFG->allowcohortthemes) && !empty($USER->cohorttheme) && !$hascustomdevicetheme) {
                        return $USER->cohorttheme;
                    }
                break;

                case 'site':
                    if ($mnetpeertheme) {
                        return $mnetpeertheme;
                    }
                    // First try for the device the user is using.
                    if (!empty($devicetheme)) {
                        return $devicetheme;
                    }
                    // Next try for the default device (as a fallback).
                    $devicetheme = core_useragent::get_device_type_theme(core_useragent::DEVICETYPE_DEFAULT);
                    if (!empty($devicetheme)) {
                        return $devicetheme;
                    }
                    // The default device theme isn't set up - use the overall default theme.
                    return theme_config::DEFAULT_THEME;
            }
        }

        // We should most certainly have resolved a theme by now. Something has gone wrong.
        debugging('Error resolving the theme to use for this page.', DEBUG_DEVELOPER);
        return theme_config::DEFAULT_THEME;
    }


    /**
     * Sets ->pagetype from the script name. For example, if the script that was
     * run is mod/quiz/view.php, ->pagetype will be set to 'mod-quiz-view'.
     *
     * @param string $script the path to the script that should be used to
     * initialise ->pagetype. If not passed the $SCRIPT global will be used.
     * If legacy code has set $CFG->pagepath that will be used instead, and a
     * developer warning issued.
     */
    protected function initialise_default_pagetype($script = null) {
        global $CFG, $SCRIPT;

        if (isset($CFG->pagepath)) {
            debugging('Some code appears to have set $CFG->pagepath. That was a horrible deprecated thing. ' .
                    'Don\'t do it! Try calling $PAGE->set_pagetype() instead.');
            $script = $CFG->pagepath;
            unset($CFG->pagepath);
        }

        if (is_null($script)) {
            $script = ltrim($SCRIPT, '/');
            $len = strlen($CFG->admin);
            if (substr($script, 0, $len) == $CFG->admin) {
                $script = 'admin' . substr($script, $len);
            }
        }

        $path = str_replace('.php', '', $script);
        if (substr($path, -1) == '/') {
            $path .= 'index';
        }

        if (empty($path) || $path == 'index') {
            $this->_pagetype = 'site-index';
        } else {
            $this->_pagetype = str_replace('/', '-', $path);
        }
    }

    /**
     * Initialises the CSS classes that will be added to body tag of the page.
     *
     * The function is responsible for adding all of the critical CSS classes
     * that describe the current page, and its state.
     * This includes classes that describe the following for example:
     *    - Current language
     *    - Language direction
     *    - YUI CSS initialisation
     *    - Pagelayout
     * These are commonly used in CSS to target specific types of pages.
     */
    protected function initialise_standard_body_classes() {
        global $CFG, $USER;

        $pagetype = $this->pagetype;
        if ($pagetype == 'site-index') {
            $this->_legacyclass = 'course';
        } else if (substr($pagetype, 0, 6) == 'admin-') {
            $this->_legacyclass = 'admin';
        }
        $this->add_body_class($this->_legacyclass);

        $pathbits = explode('-', trim($pagetype));
        for ($i = 1; $i < count($pathbits); $i++) {
            $this->add_body_class('path-' . join('-', array_slice($pathbits, 0, $i)));
        }

        $this->add_body_classes(core_useragent::get_browser_version_classes());
        $this->add_body_class('dir-' . get_string('thisdirection', 'langconfig'));
        $this->add_body_class('lang-' . current_language());
        $this->add_body_class('yui-skin-sam'); // Make YUI happy, if it is used.
        $this->add_body_class('yui3-skin-sam'); // Make YUI3 happy, if it is used.
        $this->add_body_class($this->url_to_class_name($CFG->wwwroot));

        // Extra class describing current page layout.
        $this->add_body_class('pagelayout-' . $this->_pagelayout);

        if (!during_initial_install()) {
            $this->add_body_class('course-' . $this->_course->id);
            $this->add_body_class('context-' . $this->_context->id);
        }

        if (!empty($this->_cm)) {
            $this->add_body_class('cmid-' . $this->_cm->id);
        }

        if (!empty($CFG->allowcategorythemes)) {
            $this->ensure_category_loaded();
            foreach ($this->_categories as $catid => $notused) {
                $this->add_body_class('category-' . $catid);
            }
        } else {
            $catid = 0;
            if (is_array($this->_categories)) {
                $catids = array_keys($this->_categories);
                $catid = reset($catids);
            } else if (!empty($this->_course->category)) {
                $catid = $this->_course->category;
            }
            if ($catid) {
                $this->add_body_class('category-' . $catid);
            }
        }

        if (!isloggedin()) {
            $this->add_body_class('notloggedin');
        }

        if ($this->user_is_editing()) {
            $this->add_body_class('editing');
            if (optional_param('bui_moveid', false, PARAM_INT)) {
                $this->add_body_class('blocks-moving');
            }
        }

        if (!empty($CFG->blocksdrag)) {
            $this->add_body_class('drag');
        }

        if ($this->_devicetypeinuse != 'default') {
            $this->add_body_class($this->_devicetypeinuse . 'theme');
        }

        // Add class for behat site to apply behat related fixes.
        if (defined('BEHAT_SITE_RUNNING')) {
            $this->add_body_class('behat-site');
        }
    }

    /**
     * Loads the activity record for the current CM object associated with this
     * page.
     *
     * This will load {@link moodle_page::$_module} with a row from the related
     * module table in the database.
     * For instance if {@link moodle_page::$_cm} is a forum then a row from the
     * forum table will be loaded.
     */
    protected function load_activity_record() {
        global $DB;
        if (is_null($this->_cm)) {
            return;
        }
        $this->_module = $DB->get_record($this->_cm->modname, array('id' => $this->_cm->instance));
    }

    /**
     * This function ensures that the category of the current course has been
     * loaded, and if not, the function loads it now.
     *
     * @return void
     * @throws coding_exception
     */
    protected function ensure_category_loaded() {
        if (is_array($this->_categories)) {
            return; // Already done.
        }
        if (is_null($this->_course)) {
            throw new coding_exception('Attempt to get the course category for this page before the course was set.');
        }
        if ($this->_course->category == 0) {
            $this->_categories = array();
        } else {
            $this->load_category($this->_course->category);
        }
    }

    /**
     * Loads the requested category into the pages categories array.
     *
     * @param int $categoryid
     * @throws moodle_exception
     */
    protected function load_category($categoryid) {
        global $DB;
        $category = $DB->get_record('course_categories', array('id' => $categoryid));
        if (!$category) {
            throw new moodle_exception('unknowncategory');
        }
        $this->_categories[$category->id] = $category;
        $parentcategoryids = explode('/', trim($category->path, '/'));
        array_pop($parentcategoryids);
        foreach (array_reverse($parentcategoryids) as $catid) {
            $this->_categories[$catid] = null;
        }
    }

    /**
     * Ensures that the category the current course is within, as well as all of
     * its parent categories, have been loaded.
     *
     * @return void
     */
    protected function ensure_categories_loaded() {
        global $DB;
        $this->ensure_category_loaded();
        if (!is_null(end($this->_categories))) {
            return; // Already done.
        }
        $idstoload = array_keys($this->_categories);
        array_shift($idstoload);
        $categories = $DB->get_records_list('course_categories', 'id', $idstoload);
        foreach ($idstoload as $catid) {
            $this->_categories[$catid] = $categories[$catid];
        }
    }

    /**
     * Ensure the theme has not been loaded yet. If it has an exception is thrown.
     *
     * @throws coding_exception
     */
    protected function ensure_theme_not_set() {
        // This is explicitly allowed for webservices though which may process many course contexts in a single request.
        if (WS_SERVER) {
            return;
        }

        if (!is_null($this->_theme)) {
            throw new coding_exception('The theme has already been set up for this page ready for output. ' .
                    'Therefore, you can no longer change the theme, or anything that might affect what ' .
                    'the current theme is, for example, the course.',
                    'Stack trace when the theme was set up: ' . format_backtrace($this->_wherethemewasinitialised));
        }
    }

    /**
     * Converts the provided URL into a CSS class that be used within the page.
     * This is primarily used to add the wwwroot to the body tag as a CSS class.
     *
     * @param string $url
     * @return string
     */
    protected function url_to_class_name($url) {
        $bits = parse_url($url);
        $class = str_replace('.', '-', $bits['host']);
        if (!empty($bits['port'])) {
            $class .= '--' . $bits['port'];
        }
        if (!empty($bits['path'])) {
            $path = trim($bits['path'], '/');
            if ($path) {
                $class .= '--' . str_replace('/', '-', $path);
            }
        }
        return $class;
    }

    /**
     * Combines all of the required editing caps for the page and returns them
     * as an array.
     *
     * @return array
     */
    protected function all_editing_caps() {
        $caps = $this->_othereditingcaps;
        $caps[] = $this->_blockseditingcap;
        return $caps;
    }

    /**
     * Returns true if the page URL has beem set.
     *
     * @return bool
     */
    public function has_set_url() {
        return ($this->_url!==null);
    }

    /**
     * Gets set when the block actions for the page have been processed.
     *
     * @param bool $setting
     */
    public function set_block_actions_done($setting = true) {
        $this->_block_actions_done = $setting;
    }

    /**
     * Are popup notifications allowed on this page?
     * Popup notifications may be disallowed in situations such as while upgrading or completing a quiz
     *
     * @return bool true if popup notifications may be displayed
     */
    public function get_popup_notification_allowed() {
        return $this->_popup_notification_allowed;
    }

    /**
     * Allow or disallow popup notifications on this page. Popups are allowed by default.
     *
     * @param bool $allowed true if notifications are allowed. False if not allowed. They are allowed by default.
     */
    public function set_popup_notification_allowed($allowed) {
        $this->_popup_notification_allowed = $allowed;
    }

    /**
     * Returns the block region having made any required theme manipulations.
     *
     * @since Moodle 2.5.1 2.6
     * @param string $region
     * @return string
     */
    public function apply_theme_region_manipulations($region) {
        if ($this->blockmanipulations && isset($this->blockmanipulations[$region])) {
            $regionwas = $region;
            $regionnow = $this->blockmanipulations[$region];
            if ($this->blocks->is_known_region($regionwas) && $this->blocks->is_known_region($regionnow)) {
                // Both the before and after regions are known so we can swap them over.
                return $regionnow;
            }
            // We didn't know about both, we won't swap them over.
            return $regionwas;
        }
        return $region;
    }

    /**
     * Add a report node and a specific report to the navigation.
     *
     * @param int $userid The user ID that we are looking to add this report node to.
     * @param array $nodeinfo Name and url of the final node that we are creating.
     */
    public function add_report_nodes($userid, $nodeinfo) {
        global $USER;
        // Try to find the specific user node.
        $newusernode = $this->navigation->find('user' . $userid, null);
        $reportnode = null;
        $navigationnodeerror =
                'Could not find the navigation node requested. Please check that the node you are looking for exists.';
        if ($userid != $USER->id) {
            // Check that we have a valid node.
            if (empty($newusernode)) {
                // Throw an error if we ever reach here.
                throw new coding_exception($navigationnodeerror);
            }
            // Add 'Reports' to the user node.
            $reportnode = $newusernode->add(get_string('reports'));
        } else {
            // We are looking at our own profile.
            $myprofilenode = $this->settingsnav->find('myprofile', null);
            // Check that we do end up with a valid node.
            if (empty($myprofilenode)) {
                // Throw an error if we ever reach here.
                throw new coding_exception($navigationnodeerror);
            }
            // Add 'Reports' to our node.
            $reportnode = $myprofilenode->add(get_string('reports'));
        }
        // Finally add the report to the navigation tree.
        $reportnode->add($nodeinfo['name'], $nodeinfo['url'], navigation_node::TYPE_CUSTOM, null, null,
            new pix_icon('i/report', $nodeinfo['name']));
    }

    /**
     * Add some HTML to the list of actions to render in the header actions menu.
     *
     * @param string $html The HTML to add.
     */
    public function add_header_action(string $html) : void {
        $this->_headeractions[] = $html;
    }

    /**
     * Get the list of HTML for actions to render in the header actions menu.
     *
     * @return string[]
     */
    public function get_header_actions() : array {
        return $this->_headeractions;
    }

    /**
     * Set the flag to indicate if the region main settings should be rendered as an action
     * in the header actions menu rather than at the top of the content.
     *
     * @param bool $value If the settings should be in the header.
     */
    public function set_include_region_main_settings_in_header_actions(bool $value) : void {
        $this->_regionmainsettingsinheader = $value;
    }

    /**
     * Check if the  region main settings should be rendered as an action in the header actions
     * menu rather than at the top of the content.
     *
     * @return bool
     */
    public function include_region_main_settings_in_header_actions() : bool {
        return $this->_regionmainsettingsinheader;
    }
}

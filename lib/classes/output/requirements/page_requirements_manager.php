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

namespace core\output\requirements;

use core_component;
use core\context\course as context_course;
use core\exception\coding_exception;
use core\output\core_renderer;
use core\output\js_writer;
use core\output\html_writer;
use core\output\renderer_base;
use lang_string;
use moodle_page;
use moodle_url;
use stdClass;

/**
 * This class tracks all the things that are needed by the current page.
 *
 * Normally, the only instance of this  class you will need to work with is the
 * one accessible via $PAGE->requires.
 *
 * Typical usage would be
 * <pre>
 *     $PAGE->requires->js_call_amd('mod_forum/view', 'init');
 * </pre>
 *
 * It also supports obsoleted coding style with/without YUI3 modules.
 * <pre>
 *     $PAGE->requires->js_init_call('M.mod_forum.init_view');
 *     $PAGE->requires->css('/mod/mymod/userstyles.php?id='.$id); // not overridable via themes!
 *     $PAGE->requires->js('/mod/mymod/script.js');
 *     $PAGE->requires->js('/mod/mymod/small_but_urgent.js', true);
 *     $PAGE->requires->js_function_call('init_mymod', array($data), true);
 * </pre>
 *
 * There are some natural restrictions on some methods. For example, {@see css()}
 * can only be called before the <head> tag is output. See the comments on the
 * individual methods for details.
 *
 * @copyright 2009 Tim Hunt, 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class page_requirements_manager {
    /**
     * @var array List of string available from JS
     */
    protected $stringsforjs = [];

    /**
     * @var array List of get_string $a parameters - used for validation only.
     */
    protected $stringsforjs_as = []; // phpcs:ignore moodle.NamingConventions.ValidVariableName.MemberNameUnderscore

    /**
     * @var array List of JS variables to be initialised
     */
    protected $jsinitvariables = ['head' => [], 'footer' => []];

    /**
     * @var array Included JS scripts
     */
    protected $jsincludes = ['head' => [], 'footer' => []];

    /**
     * @var array Inline scripts using RequireJS module loading.
     */
    protected $amdjscode = [''];

    /**
     * @var array List of needed function calls
     */
    protected $jscalls = ['normal' => [], 'ondomready' => []];

    /**
     * @var array List of skip links, those are needed for accessibility reasons
     */
    protected $skiplinks = [];

    /**
     * @var array Javascript code used for initialisation of page, it should
     * be relatively small
     */
    protected $jsinitcode = [];

    /**
     * @var array of moodle_url Theme sheets, initialised only from core_renderer
     */
    protected $cssthemeurls = [];

    /**
     * @var array of moodle_url List of custom theme sheets, these are strongly discouraged!
     * Useful mostly only for CSS submitted by teachers that is not part of the theme.
     */
    protected $cssurls = [];

    /**
     * @var array List of requested event handlers
     */
    protected $eventhandlers = [];

    /**
     * @var array Extra modules
     */
    protected $extramodules = [];

    /**
     * @var array trackes the names of bits of HTML that are only required once
     * per page. See {@see has_one_time_item_been_created()},
     * {@see set_one_time_item_created()} and {@see should_create_one_time_item_now()}.
     */
    protected $onetimeitemsoutput = [];

    /**
     * @var bool Flag indicated head stuff already printed
     */
    protected $headdone = false;

    /**
     * @var bool Flag indicating top of body already printed
     */
    protected $topofbodydone = false;

    /**
     * @var stdClass YUI PHPLoader instance responsible for YUI3 loading from PHP only
     */
    protected $yui3loader;

    /**
     * @var yui default YUI loader configuration
     */
    protected $YUI_config; // phpcs:ignore moodle.NamingConventions.ValidVariableName.MemberNameUnderscore

    /**
     * @var array $yuicssmodules
     */
    protected $yuicssmodules = [];

    /**
     * @var array Some config vars exposed in JS, please no secret stuff there
     */
    protected $M_cfg; // phpcs:ignore moodle.NamingConventions.ValidVariableName.MemberNameUnderscore

    /**
     * @var array list of requested jQuery plugins
     */
    protected $jqueryplugins = [];

    /**
     * @var array list of jQuery plugin overrides
     */
    protected $jquerypluginoverrides = [];

    /**
     * Page requirements constructor.
     */
    public function __construct() {
        global $CFG;

        // You may need to set up URL rewrite rule because oversized URLs might not be allowed by web server.
        $sep = empty($CFG->yuislasharguments) ? '?' : '/';

        $this->yui3loader = new stdClass();
        $this->YUI_config = new yui();

        // Set up some loader options.
        $this->yui3loader->local_base = $CFG->wwwroot . '/lib/yuilib/' . $CFG->yui3version . '/';
        $this->yui3loader->local_comboBase = $CFG->wwwroot . '/theme/yui_combo.php' . $sep;

        $this->yui3loader->base = $this->yui3loader->local_base;
        $this->yui3loader->comboBase = $this->yui3loader->local_comboBase;

        // Enable combo loader? This significantly helps with caching and performance!
        $this->yui3loader->combine = !empty($CFG->yuicomboloading);

        $jsrev = $this->get_jsrev();

        // Set up JS YUI loader helper object.
        $this->YUI_config->base         = $this->yui3loader->base;
        $this->YUI_config->comboBase    = $this->yui3loader->comboBase;
        $this->YUI_config->combine      = $this->yui3loader->combine;

        // If we've had to patch any YUI modules between releases, we must override the YUI configuration to include them.
        if (!empty($CFG->yuipatchedmodules) && !empty($CFG->yuipatchlevel)) {
            $this->YUI_config->define_patched_core_modules(
                $this->yui3loader->local_comboBase,
                $CFG->yui3version,
                $CFG->yuipatchlevel,
                $CFG->yuipatchedmodules
            );
        }

        $configname = $this->YUI_config->set_config_source('lib/yui/config/yui2.js');
        $this->YUI_config->add_group('yui2', [
            // Loader configuration for our 2in3.
            'base' => $CFG->wwwroot . '/lib/yuilib/2in3/' . $CFG->yui2version . '/build/',
            'comboBase' => $CFG->wwwroot . '/theme/yui_combo.php' . $sep,
            'combine' => $this->yui3loader->combine,
            'ext' => false,
            'root' => '2in3/' . $CFG->yui2version . '/build/',
            'patterns' => [
                'yui2-' => [
                    'group' => 'yui2',
                    'configFn' => $configname,
                ],
            ],
        ]);
        $configname = $this->YUI_config->set_config_source('lib/yui/config/moodle.js');
        $this->YUI_config->add_group('moodle', [
            'name' => 'moodle',
            'base' => $CFG->wwwroot . '/theme/yui_combo.php' . $sep . 'm/' . $jsrev . '/',
            'combine' => $this->yui3loader->combine,
            'comboBase' => $CFG->wwwroot . '/theme/yui_combo.php' . $sep,
            'ext' => false,
            'root' => 'm/' . $jsrev . '/', // Add the rev to the root path so that we can control caching.
            'patterns' => [
                'moodle-' => [
                    'group' => 'moodle',
                    'configFn' => $configname,
                ],
            ],
        ]);

        $this->YUI_config->add_group('gallery', [
            'name' => 'gallery',
            'base' => $CFG->wwwroot . '/lib/yuilib/gallery/',
            'combine' => $this->yui3loader->combine,
            'comboBase' => $CFG->wwwroot . '/theme/yui_combo.php' . $sep,
            'ext' => false,
            'root' => 'gallery/' . $jsrev . '/',
            'patterns' => [
                'gallery-' => [
                    'group' => 'gallery',
                ],
            ],
        ]);

        // Set some more loader options applying to groups too.
        if ($CFG->debugdeveloper) {
            // When debugging is enabled, we want to load the non-minified (RAW) versions of YUI library modules rather
            // than the DEBUG versions as these generally generate too much logging for our purposes.
            // However we do want the DEBUG versions of our Moodle-specific modules.
            // To debug a YUI-specific issue, change the yui3loader->filter value to DEBUG.
            $this->YUI_config->filter = 'RAW';
            $this->YUI_config->groups['moodle']['filter'] = 'DEBUG';

            // We use the yui3loader->filter setting when writing the YUI3 seed scripts into the header.
            $this->yui3loader->filter = $this->YUI_config->filter;
            $this->YUI_config->debug = true;
        } else {
            $this->yui3loader->filter = null;
            $this->YUI_config->groups['moodle']['filter'] = null;
            $this->YUI_config->debug = false;
        }

        // Include the YUI config log filters.
        if (!empty($CFG->yuilogexclude) && is_array($CFG->yuilogexclude)) {
            $this->YUI_config->logExclude = $CFG->yuilogexclude;
        }
        if (!empty($CFG->yuiloginclude) && is_array($CFG->yuiloginclude)) {
            $this->YUI_config->logInclude = $CFG->yuiloginclude;
        }
        if (!empty($CFG->yuiloglevel)) {
            $this->YUI_config->logLevel = $CFG->yuiloglevel;
        }

        // Add the moodle group's module data.
        $this->YUI_config->add_moodle_metadata();

        // Every page should include definition of following modules.
        $this->js_module($this->find_module('core_filepicker'));
        $this->js_module($this->find_module('core_comment'));
    }

    /**
     * Return the safe config values that get set for javascript in "M.cfg".
     *
     * @since 2.9
     * @param moodle_page $page The page to add JS to
     * @param renderer_base $renderer The renderer to use
     * @return array List of safe config values that are available to javascript.
     */
    public function get_config_for_javascript(moodle_page $page, renderer_base $renderer) {
        global $CFG, $USER;

        if (empty($this->M_cfg)) {
            $iconsystem = \core\output\icon_system::instance();

            // It is possible that the $page->context is null, so we can't use $page->context->id.
            $contextid = null;
            $contextinstanceid = null;
            if (!is_null($page->context)) {
                $contextid = $page->context->id;
                $contextinstanceid = $page->context->instanceid;
                $courseid = $page->course->id;
                $coursecontext = context_course::instance($courseid);
            }

            $this->M_cfg = [
                'wwwroot'               => $CFG->wwwroot,
                'apibase'               => $this->get_api_base(),
                'homeurl'               => $page->navigation->action,
                'sesskey'               => sesskey(),
                'sessiontimeout'        => $CFG->sessiontimeout,
                'sessiontimeoutwarning' => $CFG->sessiontimeoutwarning,
                'themerev'              => theme_get_revision(),
                'slasharguments'        => (int)(!empty($CFG->slasharguments)),
                'theme'                 => $page->theme->name,
                'iconsystemmodule'      => $iconsystem->get_amd_name(),
                'jsrev'                 => $this->get_jsrev(),
                'admin'                 => $CFG->admin,
                'svgicons'              => $page->theme->use_svg_icons(),
                'usertimezone'          => usertimezone(),
                'language'              => current_language(),
                'courseId'              => isset($courseid) ? (int) $courseid : 0,
                'courseContextId'       => isset($coursecontext) ? $coursecontext->id : 0,
                'contextid'             => $contextid,
                'contextInstanceId'     => (int) $contextinstanceid,
                'langrev'               => get_string_manager()->get_revision(),
                'templaterev'           => $this->get_templaterev(),
                'siteId'                => (int) SITEID,
                'userId'                => (int) $USER->id,
            ];
            if ($CFG->debugdeveloper) {
                $this->M_cfg['developerdebug'] = true;
            }
            if (defined('BEHAT_SITE_RUNNING')) {
                $this->M_cfg['behatsiterunning'] = true;
            }
        }
        return $this->M_cfg;
    }

    /**
     * Return the base URL for the API.
     *
     * If the router has been fully configured on the web server then we can use the shortened route, otherwise the r.php.
     *
     * @return string
     */
    protected function get_api_base(): string {
        global $CFG;

        if (!empty($CFG->router_configured)) {
            return sprintf(
                "%s/api",
                $CFG->wwwroot,
            );
        }

        return sprintf(
            "%s/r.php/api",
            $CFG->wwwroot,
        );
    }

    /**
     * Initialise with the bits of JavaScript that every Moodle page should have.
     *
     * @param moodle_page $page
     * @param core_renderer $renderer
     */
    protected function init_requirements_data(moodle_page $page, core_renderer $renderer) {
        global $CFG;

        // Init the js config.
        $this->get_config_for_javascript($page, $renderer);

        // Accessibility stuff.
        $this->skip_link_to('maincontent', get_string('tocontent', 'access'));

        // Add strings used on many pages.
        $this->string_for_js('confirmation', 'admin');
        $this->string_for_js('cancel', 'moodle');
        $this->string_for_js('yes', 'moodle');

        // Alter links in top frame to break out of frames.
        if ($page->pagelayout === 'frametop') {
            $this->js_init_call('M.util.init_frametop');
        }

        // Include block drag/drop if editing is on.
        if ($page->user_is_editing()) {
            $params = [
                'regions' => $page->blocks->get_regions(),
                'pagehash' => $page->get_edited_page_hash(),
            ];
            if (!empty($page->cm->id)) {
                $params['cmid'] = $page->cm->id;
            }
            // Strings for drag and drop.
            $this->strings_for_js(
                [
                    'movecontent',
                    'tocontent',
                    'emptydragdropregion',
                ],
                'moodle',
            );
            $page->requires->yui_module('moodle-core-blocks', 'M.core_blocks.init_dragdrop', [$params], null, true);
            $page->requires->js_call_amd('core_block/edit', 'init', ['pagehash' => $page->get_edited_page_hash()]);
        }

        // Include the YUI CSS Modules.
        $page->requires->set_yuicssmodules($page->theme->yuicssmodules);
    }

    /**
     * Determine the correct JS Revision to use for this load.
     *
     * @return int the jsrev to use.
     */
    public function get_jsrev() {
        global $CFG;

        if (empty($CFG->cachejs)) {
            $jsrev = -1;
        } else if (empty($CFG->jsrev)) {
            $jsrev = 1;
        } else {
            $jsrev = $CFG->jsrev;
        }

        return $jsrev;
    }

    /**
     * Determine the correct Template revision to use for this load.
     *
     * @return int the templaterev to use.
     */
    protected function get_templaterev() {
        global $CFG;

        if (empty($CFG->cachetemplates)) {
            $templaterev = -1;
        } else if (empty($CFG->templaterev)) {
            $templaterev = 1;
        } else {
            $templaterev = $CFG->templaterev;
        }

        return $templaterev;
    }

    /**
     * Ensure that the specified JavaScript file is linked to from this page.
     *
     * NOTE: This function is to be used in RARE CASES ONLY, please store your JS in module.js file
     * and use $PAGE->requires->js_init_call() instead or use /yui/ subdirectories for YUI modules.
     *
     * By default the link is put at the end of the page, since this gives best page-load performance.
     *
     * Even if a particular script is requested more than once, it will only be linked
     * to once.
     *
     * @param string|moodle_url $url The path to the .js file, relative to $CFG->dirroot / $CFG->wwwroot.
     *      For example '/mod/mymod/customscripts.js'; use moodle_url for external scripts
     * @param bool $inhead initialise in head
     */
    public function js($url, $inhead = false) {
        if ($url == '/question/qengine.js') {
            debugging('The question/qengine.js has been deprecated. ' .
                'Please use core_question/question_engine', DEBUG_DEVELOPER);
        }
        $url = $this->js_fix_url($url);
        $where = $inhead ? 'head' : 'footer';
        $this->jsincludes[$where][$url->out()] = $url;
    }

    /**
     * Request inclusion of jQuery library in the page.
     *
     * NOTE: this should not be used in official Moodle distribution!
     *
     * @link https://moodledev.io/docs/guides/javascript/jquery
     */
    public function jquery() {
        $this->jquery_plugin('jquery');
    }

    /**
     * Request inclusion of jQuery plugin.
     *
     * NOTE: this should not be used in official Moodle distribution!
     *
     * jQuery plugins are located in plugin/jquery/* subdirectory,
     * plugin/jquery/plugins.php lists all available plugins.
     *
     * Included core plugins:
     *   - jQuery UI
     *
     * Add-ons may include extra jQuery plugins in jquery/ directory,
     * plugins.php file defines the mapping between plugin names and
     * necessary page includes.
     *
     * Examples:
     * <code>
     *   // file: mod/xxx/view.php
     *   $PAGE->requires->jquery();
     *   $PAGE->requires->jquery_plugin('ui');
     *   $PAGE->requires->jquery_plugin('ui-css');
     * </code>
     *
     * <code>
     *   // file: theme/yyy/lib.php
     *   function theme_yyy_page_init(moodle_page $page) {
     *       $page->requires->jquery();
     *       $page->requires->jquery_plugin('ui');
     *       $page->requires->jquery_plugin('ui-css');
     *   }
     * </code>
     *
     * <code>
     *   // file: blocks/zzz/block_zzz.php
     *   public function get_required_javascript() {
     *       parent::get_required_javascript();
     *       $this->page->requires->jquery();
     *       $page->requires->jquery_plugin('ui');
     *       $page->requires->jquery_plugin('ui-css');
     *   }
     * </code>
     *
     * {@link https://moodledev.io/docs/guides/javascript/jquery}
     *
     * @param string $plugin name of the jQuery plugin as defined in jquery/plugins.php
     * @param string $component name of the component
     * @return bool success
     */
    public function jquery_plugin($plugin, $component = 'core') {
        global $CFG;

        if ($this->headdone) {
            debugging('Can not add jQuery plugins after starting page output!');
            return false;
        }

        if ($component !== 'core' && in_array($plugin, ['jquery', 'ui', 'ui-css'])) {
            debugging(
                "jQuery plugin '$plugin' is included in Moodle core, other components can not use the same name.",
                DEBUG_DEVELOPER,
            );
            $component = 'core';
        } else if ($component !== 'core' && strpos($component, '_') === false) {
            // Let's normalise the legacy activity names, Frankenstyle rulez!
            $component = 'mod_' . $component;
        }

        if (empty($this->jqueryplugins) && ($component !== 'core' || $plugin !== 'jquery')) {
            // Make sure the jQuery itself is always loaded first,
            // the order of all other plugins depends on order of $PAGE_>requires->.
            $this->jquery_plugin('jquery', 'core');
        }

        if (isset($this->jqueryplugins[$plugin])) {
            // No problem, we already have something, first Moodle plugin to register the jQuery plugin wins.
            return true;
        }

        $componentdir = core_component::get_component_directory($component);
        if (!file_exists($componentdir) || !file_exists("$componentdir/jquery/plugins.php")) {
            debugging("Can not load jQuery plugin '$plugin', missing plugins.php in component '$component'.", DEBUG_DEVELOPER);
            return false;
        }

        $plugins = [];
        require("$componentdir/jquery/plugins.php");

        if (!isset($plugins[$plugin])) {
            debugging("jQuery plugin '$plugin' can not be found in component '$component'.", DEBUG_DEVELOPER);
            return false;
        }

        $this->jqueryplugins[$plugin] = new stdClass();
        $this->jqueryplugins[$plugin]->plugin    = $plugin;
        $this->jqueryplugins[$plugin]->component = $component;
        $this->jqueryplugins[$plugin]->urls      = [];

        foreach ($plugins[$plugin]['files'] as $file) {
            if ($CFG->debugdeveloper) {
                if (!file_exists("$componentdir/jquery/$file")) {
                    debugging("Invalid file '$file' specified in jQuery plugin '$plugin' in component '$component'");
                    continue;
                }
                $file = str_replace('.min.css', '.css', $file);
                $file = str_replace('.min.js', '.js', $file);
            }
            if (!file_exists("$componentdir/jquery/$file")) {
                debugging("Invalid file '$file' specified in jQuery plugin '$plugin' in component '$component'");
                continue;
            }
            if (!empty($CFG->slasharguments)) {
                $url = new moodle_url("/theme/jquery.php");
                $url->set_slashargument("/$component/$file");
            } else {
                // This is not really good, we need slasharguments for relative links, this means no caching...
                $path = realpath("$componentdir/jquery/$file");
                if (strpos($path, $CFG->dirroot) === 0) {
                    $url = $CFG->wwwroot . preg_replace('/^' . preg_quote($CFG->dirroot, '/') . '/', '', $path);
                    // Replace all occurences of backslashes characters in url to forward slashes.
                    $url = str_replace('\\', '/', $url);
                    $url = new moodle_url($url);
                } else {
                    // Bad luck, fix your server!
                    debugging("Moodle jQuery integration requires 'slasharguments' setting to be enabled.");
                    continue;
                }
            }
            $this->jqueryplugins[$plugin]->urls[] = $url;
        }

        return true;
    }

    /**
     * Request replacement of one jQuery plugin by another.
     *
     * This is useful when themes want to replace the jQuery UI theme,
     * the problem is that theme can not prevent others from including the core ui-css plugin.
     *
     * Example:
     *  1/ generate new jQuery UI theme and place it into theme/yourtheme/jquery/
     *  2/ write theme/yourtheme/jquery/plugins.php
     *  3/ init jQuery from theme
     *
     * <code>
     *   // file theme/yourtheme/lib.php
     *   function theme_yourtheme_page_init($page) {
     *       $page->requires->jquery_plugin('yourtheme-ui-css', 'theme_yourtheme');
     *       $page->requires->jquery_override_plugin('ui-css', 'yourtheme-ui-css');
     *   }
     * </code>
     *
     * This code prevents loading of standard 'ui-css' which my be requested by other plugins,
     * the 'yourtheme-ui-css' gets loaded only if some other code requires jquery.
     *
     * @link https://moodledev.io/docs/guides/javascript/jquery
     *
     * @param string $oldplugin original plugin
     * @param string $newplugin the replacement
     */
    public function jquery_override_plugin($oldplugin, $newplugin) {
        if ($this->headdone) {
            debugging('Can not override jQuery plugins after starting page output!');
            return;
        }
        $this->jquerypluginoverrides[$oldplugin] = $newplugin;
    }

    /**
     * Return jQuery related markup for page start.
     * @return string
     */
    protected function get_jquery_headcode() {
        if (empty($this->jqueryplugins['jquery'])) {
            // If nobody requested jQuery then do not bother to load anything.
            // This may be useful for themes that want to override 'ui-css' only if requested by something else.
            return '';
        }

        $included = [];
        $urls = [];

        foreach ($this->jqueryplugins as $name => $unused) {
            if (isset($included[$name])) {
                continue;
            }
            if (array_key_exists($name, $this->jquerypluginoverrides)) {
                // The following loop tries to resolve the replacements,
                // use max 100 iterations to prevent infinite loop resulting
                // in blank page.
                $cyclic = true;
                $oldname = $name;
                for ($i = 0; $i < 100; $i++) {
                    $name = $this->jquerypluginoverrides[$name];
                    if (!array_key_exists($name, $this->jquerypluginoverrides)) {
                        $cyclic = false;
                        break;
                    }
                }
                if ($cyclic) {
                    // We can not do much with cyclic references here, let's use the old plugin.
                    $name = $oldname;
                    debugging("Cyclic overrides detected for jQuery plugin '$name'");
                } else if (empty($name)) {
                    // Developer requested removal of the plugin.
                    continue;
                } else if (!isset($this->jqueryplugins[$name])) {
                    debugging("Unknown jQuery override plugin '$name' detected");
                    $name = $oldname;
                } else if (isset($included[$name])) {
                    // The plugin was already included, easy.
                    continue;
                }
            }

            $plugin = $this->jqueryplugins[$name];
            $urls = array_merge($urls, $plugin->urls);
            $included[$name] = true;
        }

        $output = '';
        $attributes = ['rel' => 'stylesheet', 'type' => 'text/css'];
        foreach ($urls as $url) {
            if (preg_match('/\.js$/', $url)) {
                $output .= html_writer::script('', $url);
            } else if (preg_match('/\.css$/', $url)) {
                $attributes['href'] = $url;
                $output .= html_writer::empty_tag('link', $attributes) . "\n";
            }
        }

        return $output;
    }

    /**
     * Returns the actual url through which a JavaScript file is served.
     *
     * @param moodle_url|string $url full moodle url, or shortened path to script.
     * @throws coding_exception if the given $url isn't a shortened url starting with / or a moodle_url instance.
     * @return moodle_url
     */
    protected function js_fix_url($url) {
        global $CFG;

        if ($url instanceof moodle_url) {
            // If the URL is external to Moodle, it won't be handled by Moodle (!).
            if ($url->is_local_url()) {
                $localurl = $url->out_as_local_url();
                // Check if the URL points to a Moodle PHP resource.
                if (strpos($localurl, '.php') !== false) {
                    // It's a Moodle PHP resource e.g. a resource already served by the proper Moodle Handler.
                    return $url;
                }
                // It's a local resource: we need to further examine it.
                return $this->js_fix_url($url->out_as_local_url(false));
            }
            // The URL is not a Moodle resource.
            return $url;
        } else if (null !== $url && strpos($url, '/') === 0) {
            // Fix the admin links if needed.
            if ($CFG->admin !== 'admin') {
                if (strpos($url, "/admin/") === 0) {
                    $url = preg_replace("|^/admin/|", "/$CFG->admin/", $url);
                }
            }
            if (debugging()) {
                // Check file existence only when in debug mode.
                if (!file_exists($CFG->dirroot . strtok($url, '?'))) {
                    throw new coding_exception('Attempt to require a JavaScript file that does not exist.', $url);
                }
            }
            if (substr($url, -3) === '.js') {
                $jsrev = $this->get_jsrev();
                if (empty($CFG->slasharguments)) {
                    return new moodle_url('/lib/javascript.php', ['rev' => $jsrev, 'jsfile' => $url]);
                } else {
                    $returnurl = new moodle_url('/lib/javascript.php');
                    $returnurl->set_slashargument('/' . $jsrev . $url);
                    return $returnurl;
                }
            } else {
                return new moodle_url($url);
            }
        } else {
            throw new coding_exception('Invalid JS url, it has to be shortened url starting with / or moodle_url instance.', $url);
        }
    }

    /**
     * Find out if JS module present and return details.
     *
     * @param string $component name of component in frankenstyle, ex: core_group, mod_forum
     * @return array description of module or null if not found
     */
    protected function find_module($component) {
        global $CFG, $PAGE;

        $module = null;

        if (strpos($component, 'core_') === 0) {
            // Must be some core stuff - list here is not complete, this is just the stuff used from multiple places
            // so that we do nto have to repeat the definition of these modules over and over again.
            switch ($component) {
                case 'core_filepicker':
                    $module = [
                        'name' => 'core_filepicker',
                        'fullpath' => '/repository/filepicker.js',
                        'requires' => [
                            'base', 'node', 'node-event-simulate', 'json', 'async-queue', 'io-base', 'io-upload-iframe', 'io-form',
                            'yui2-treeview', 'panel', 'cookie', 'datatable', 'datatable-sort', 'resize-plugin', 'dd-plugin',
                            'escape', 'moodle-core_filepicker', 'moodle-core-notification-dialogue',
                        ],
                        'strings'  => [
                            ['lastmodified', 'moodle'],
                            ['name', 'moodle'],
                            ['type', 'repository'],
                            ['size', 'repository'],
                            ['invalidjson', 'repository'],
                            ['error', 'moodle'],
                            ['info', 'moodle'],
                            ['nofilesattached', 'repository'],
                            ['filepicker', 'repository'],
                            ['logout', 'repository'],
                            ['nofilesavailable', 'repository'],
                            ['norepositoriesavailable', 'repository'],
                            ['fileexistsdialogheader', 'repository'],
                            ['fileexistsdialog_editor', 'repository'],
                            ['fileexistsdialog_filemanager', 'repository'],
                            ['renameto', 'repository'],
                            ['referencesexist', 'repository'],
                            ['select', 'repository'],
                        ],
                    ];
                    break;
                case 'core_comment':
                    $module = [
                        'name' => 'core_comment',
                        'fullpath' => '/comment/comment.js',
                        'requires' => ['base', 'io-base', 'node', 'json', 'yui2-animation', 'overlay', 'escape'],
                        'strings' => [['confirmdeletecomments', 'admin'], ['yes', 'moodle'], ['no', 'moodle']],
                    ];
                    break;
                case 'core_role':
                    $module = [
                        'name' => 'core_role',
                        'fullpath' => '/admin/roles/module.js',
                        'requires' => ['node', 'cookie'],
                    ];
                    break;
                case 'core_completion':
                    break;
                case 'core_message':
                    $module = [
                        'name' => 'core_message',
                        'requires' => ['base', 'node', 'event', 'node-event-simulate'],
                        'fullpath' => '/message/module.js',
                    ];
                    break;
                case 'core_group':
                    $module = [
                        'name' => 'core_group',
                        'fullpath' => '/group/module.js',
                        'requires' => ['node', 'overlay', 'event-mouseenter'],
                    ];
                    break;
                case 'core_question_engine':
                    $module = [
                        'name' => 'core_question_engine',
                        'fullpath' => '/question/qengine.js',
                        'requires' => ['node', 'event'],
                    ];
                    break;
                case 'core_rating':
                    $module = [
                        'name' => 'core_rating',
                        'fullpath' => '/rating/module.js',
                        'requires' => ['node', 'event', 'overlay', 'io-base', 'json'],
                    ];
                    break;
                case 'core_dndupload':
                    $module = [
                        'name' => 'core_dndupload',
                        'fullpath' => '/lib/form/dndupload.js',
                        'requires' => ['node', 'event', 'json', 'core_filepicker'],
                        'strings'  => [
                            ['uploadformlimit', 'moodle'], ['droptoupload', 'moodle'], ['maxfilesreached', 'moodle'],
                            ['dndenabled_inbox', 'moodle'], ['fileexists', 'moodle'], ['maxbytesfile', 'error'],
                            ['sizegb', 'moodle'], ['sizemb', 'moodle'], ['sizekb', 'moodle'], ['sizeb', 'moodle'],
                            ['maxareabytesreached', 'moodle'], ['serverconnection', 'error'],
                            ['changesmadereallygoaway', 'moodle'], ['complete', 'moodle'],
                        ],
                    ];
                    break;
            }
        } else {
            if ($dir = core_component::get_component_directory($component)) {
                if (file_exists("$dir/module.js")) {
                    if (strpos($dir, $CFG->dirroot . '/') === 0) {
                        $dir = substr($dir, strlen($CFG->dirroot));
                        $module = ['name' => $component, 'fullpath' => "$dir/module.js", 'requires' => []];
                    }
                }
            }
        }

        return $module;
    }

    /**
     * Append YUI3 module to default YUI3 JS loader.
     * The structure of module array is described at {@link http://developer.yahoo.com/yui/3/yui/}
     *
     * @param string|array $module name of module (details are autodetected), or full module specification as array
     * @return void
     */
    public function js_module($module) {
        global $CFG;

        if (empty($module)) {
            throw new coding_exception('Missing YUI3 module name or full description.');
        }

        if (is_string($module)) {
            $module = $this->find_module($module);
        }

        if (empty($module) || empty($module['name']) || empty($module['fullpath'])) {
            throw new coding_exception('Missing YUI3 module details.');
        }

        $module['fullpath'] = $this->js_fix_url($module['fullpath'])->out(false);
        // Add all needed strings.
        if (!empty($module['strings'])) {
            foreach ($module['strings'] as $string) {
                $identifier = $string[0];
                $component = isset($string[1]) ? $string[1] : 'moodle';
                $a = isset($string[2]) ? $string[2] : null;
                $this->string_for_js($identifier, $component, $a);
            }
        }
        unset($module['strings']);

        // Process module requirements and attempt to load each. This allows
        // moodle modules to require each other.
        if (!empty($module['requires'])) {
            foreach ($module['requires'] as $requirement) {
                $rmodule = $this->find_module($requirement);
                if (is_array($rmodule)) {
                    $this->js_module($rmodule);
                }
            }
        }

        if ($this->headdone) {
            $this->extramodules[$module['name']] = $module;
        } else {
            $this->YUI_config->add_module_config($module['name'], $module);
        }
    }

    /**
     * Returns true if the module has already been loaded.
     *
     * @param string|array $module
     * @return bool True if the module has already been loaded
     */
    protected function js_module_loaded($module) {
        if (is_string($module)) {
            $modulename = $module;
        } else {
            $modulename = $module['name'];
        }
        return array_key_exists($modulename, $this->YUI_config->modules) ||
               array_key_exists($modulename, $this->extramodules);
    }

    /**
     * Ensure that the specified CSS file is linked to from this page.
     *
     * Because stylesheet links must go in the <head> part of the HTML, you must call
     * this function before {@see get_head_code()} is called. That normally means before
     * the call to print_header. If you call it when it is too late, an exception
     * will be thrown.
     *
     * Even if a particular style sheet is requested more than once, it will only
     * be linked to once.
     *
     * Please note use of this feature is strongly discouraged,
     * it is suitable only for places where CSS is submitted directly by teachers.
     * (Students must not be allowed to submit any external CSS because it may
     * contain embedded javascript!). Example of correct use is mod/data.
     *
     * @param string $stylesheet The path to the .css file, relative to $CFG->wwwroot.
     *   For example:
     *      $PAGE->requires->css('mod/data/css.php?d='.$data->id);
     */
    public function css($stylesheet) {
        global $CFG;

        if ($this->headdone) {
            throw new coding_exception('Cannot require a CSS file after &lt;head> has been printed.', $stylesheet);
        }

        // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedIf
        if ($stylesheet instanceof moodle_url) {
            // Ok.
        } else if (strpos($stylesheet, '/') === 0) {
            $stylesheet = new moodle_url($stylesheet);
        } else {
            throw new coding_exception('Invalid stylesheet parameter.', $stylesheet);
        }

        $this->cssurls[$stylesheet->out()] = $stylesheet;
    }

    /**
     * Add theme stylesheet to page - do not use from plugin code,
     * this should be called only from the core renderer!
     *
     * @param moodle_url $stylesheet
     * @return void
     */
    public function css_theme(moodle_url $stylesheet) {
        $this->cssthemeurls[] = $stylesheet;
    }

    /**
     * Ensure that a skip link to a given target is printed at the top of the <body>.
     *
     * You must call this function before {@see get_top_of_body_code()}, (if not, an exception
     * will be thrown). That normally means you must call this before the call to print_header.
     *
     * If you ask for a particular skip link to be printed, it is then your responsibility
     * to ensure that the appropriate <a name="..."> tag is printed in the body of the
     * page, so that the skip link goes somewhere.
     *
     * Even if a particular skip link is requested more than once, only one copy of it will be output.
     *
     * @param string $target the name of anchor this link should go to. For example 'maincontent'.
     * @param string $linktext The text to use for the skip link. Normally get_string('skipto', 'access', ...);
     */
    public function skip_link_to($target, $linktext) {
        if ($this->topofbodydone) {
            debugging('Page header already printed, can not add skip links any more, code needs to be fixed.');
            return;
        }
        $this->skiplinks[$target] = $linktext;
    }

    /**
     * !!!DEPRECATED!!! please use js_init_call() if possible
     * Ensure that the specified JavaScript function is called from an inline script
     * somewhere on this page.
     *
     * By default the call will be put in a script tag at the
     * end of the page after initialising Y instance, since this gives best page-load
     * performance and allows you to use YUI3 library.
     *
     * If you request that a particular function is called several times, then
     * that is what will happen (unlike linking to a CSS or JS file, where only
     * one link will be output).
     *
     * The main benefit of the method is the automatic encoding of all function parameters.
     *
     * @deprecated
     *
     * @param string $function the name of the JavaScritp function to call. Can
     *      be a compound name like 'Y.Event.purgeElement'. Can also be
     *      used to create and object by using a 'function name' like 'new user_selector'.
     * @param null|array $arguments and array of arguments to be passed to the function.
     *      When generating the function call, this will be escaped using json_encode,
     *      so passing objects and arrays should work.
     * @param bool $ondomready If tru the function is only called when the dom is
     *      ready for manipulation.
     * @param int $delay The delay before the function is called.
     */
    public function js_function_call(
        $function,
        ?array $arguments = null,
        $ondomready = false,
        $delay = 0,
    ) {
        $where = $ondomready ? 'ondomready' : 'normal';
        $this->jscalls[$where][] = [$function, $arguments, $delay];
    }

    /**
     * This function appends a block of code to the AMD specific javascript block executed
     * in the page footer, just after loading the requirejs library.
     *
     * The code passed here can rely on AMD module loading, e.g. require('jquery', function($) {...});
     *
     * @param string $code The JS code to append.
     */
    public function js_amd_inline($code) {
        $this->amdjscode[] = $code;
    }

    /**
     * Load an AMD module and eventually call its method.
     *
     * This function creates a minimal inline JS snippet that requires an AMD module and eventually calls a single
     * function from the module with given arguments. If it is called multiple times, it will be create multiple
     * snippets.
     *
     * @param string $fullmodule The name of the AMD module to load, formatted as <component name>/<module name>.
     * @param string $func Optional function from the module to call, defaults to just loading the AMD module.
     * @param array $params The params to pass to the function (will be serialized into JSON).
     */
    public function js_call_amd($fullmodule, $func = null, $params = []) {
        global $CFG;

        $modulepath = explode('/', $fullmodule);

        $modname = clean_param(array_shift($modulepath), PARAM_COMPONENT);
        foreach ($modulepath as $module) {
            $modname .= '/' . clean_param($module, PARAM_ALPHANUMEXT);
        }

        $functioncode = [];
        if ($func !== null) {
            $func = clean_param($func, PARAM_ALPHANUMEXT);

            $jsonparams = [];
            foreach ($params as $param) {
                $jsonparams[] = json_encode($param);
            }
            $strparams = implode(', ', $jsonparams);
            if ($CFG->debugdeveloper) {
                $toomanyparamslimit = 1024;
                if (strlen($strparams) > $toomanyparamslimit) {
                    debugging('Too much data passed as arguments to js_call_amd("' . $fullmodule . '", "' . $func .
                        '"). Generally there are better ways to pass lots of data from PHP to JavaScript, for example via Ajax, ' .
                        'data attributes, ... . This warning is triggered if the argument string becomes longer than ' .
                        $toomanyparamslimit . ' characters.', DEBUG_DEVELOPER);
                }
            }

            $functioncode[] = "amd.{$func}({$strparams});";
        }

        $functioncode[] = "M.util.js_complete('{$modname}');";

        $initcode = implode(' ', $functioncode);
        $js = "M.util.js_pending('{$modname}'); require(['{$modname}'], function(amd) {{$initcode}});";

        $this->js_amd_inline($js);
    }

    /**
     * Creates a JavaScript function call that requires one or more modules to be loaded.
     *
     * This function can be used to include all of the standard YUI module types within JavaScript:
     *     - YUI3 modules    [node, event, io]
     *     - YUI2 modules    [yui2-*]
     *     - Moodle modules  [moodle-*]
     *     - Gallery modules [gallery-*]
     *
     * Before writing new code that makes extensive use of YUI, you should consider it's replacement AMD/JQuery.
     * @see js_call_amd()
     *
     * @param array|string $modules One or more modules
     * @param string $function The function to call once modules have been loaded
     * @param null|array $arguments An array of arguments to pass to the function
     * @param null|string $galleryversion Deprecated: The gallery version to use
     * @param bool $ondomready
     */
    public function yui_module(
        $modules,
        $function,
        ?array $arguments = null,
        $galleryversion = null,
        $ondomready = false,
    ) {
        if (!is_array($modules)) {
            $modules = [$modules];
        }

        if ($galleryversion != null) {
            debugging('The galleryversion parameter to yui_module has been deprecated since Moodle 2.3.');
        }

        $jscode = 'Y.use(' .
            join(
                ',',
                array_map(
                    'json_encode',
                    convert_to_array($modules)
                )
            ) .
            ',function() {' . js_writer::function_call($function, $arguments) . '});';
        if ($ondomready) {
            $jscode = "Y.on('domready', function() { $jscode });";
        }
        $this->jsinitcode[] = $jscode;
    }

    /**
     * Set the CSS Modules to be included from YUI.
     *
     * @param array $modules The list of YUI CSS Modules to include.
     */
    public function set_yuicssmodules(array $modules = []) {
        $this->yuicssmodules = $modules;
    }

    /**
     * Ensure that the specified JavaScript function is called from an inline script
     * from page footer.
     *
     * @param string $function the name of the JavaScritp function to with init code,
     *      usually something like 'M.mod_mymodule.init'
     * @param null|array $extraarguments and array of arguments to be passed to the function.
     *      The first argument is always the YUI3 Y instance with all required dependencies
     *      already loaded.
     * @param bool $ondomready wait for dom ready (helps with some IE problems when modifying DOM)
     * @param null|array $module JS module specification array
     */
    public function js_init_call(
        $function,
        ?array $extraarguments = null,
        $ondomready = false,
        ?array $module = null,
    ) {
        $jscode = js_writer::function_call_with_Y($function, $extraarguments);
        if (!$module) {
            // Detect module automatically.
            if (preg_match('/M\.([a-z0-9]+_[^\.]+)/', $function, $matches)) {
                $module = $this->find_module($matches[1]);
            }
        }

        $this->js_init_code($jscode, $ondomready, $module);
    }

    /**
     * Add short static javascript code fragment to page footer.
     * This is intended primarily for loading of js modules and initialising page layout.
     * Ideally the JS code fragment should be stored in plugin renderer so that themes
     * may override it.
     *
     * @param string $jscode
     * @param bool $ondomready wait for dom ready (helps with some IE problems when modifying DOM)
     * @param null|array $module JS module specification array
     */
    public function js_init_code(
        $jscode,
        $ondomready = false,
        ?array $module = null,
    ) {
        $jscode = trim($jscode, " ;\n") . ';';

        $uniqid = html_writer::random_id();
        $startjs = " M.util.js_pending('" . $uniqid . "');";
        $endjs = " M.util.js_complete('" . $uniqid . "');";

        if ($module) {
            $this->js_module($module);
            $modulename = $module['name'];
            $jscode = "$startjs Y.use('$modulename', function(Y) { $jscode $endjs });";
        }

        if ($ondomready) {
            $jscode = "$startjs Y.on('domready', function() { $jscode $endjs });";
        }

        $this->jsinitcode[] = $jscode;
    }

    /**
     * Make a language string available to JavaScript.
     *
     * All the strings will be available in a M.str object in the global namespace.
     * So, for example, after a call to $PAGE->requires->string_for_js('course', 'moodle');
     * then the JavaScript variable M.str.moodle.course will be 'Course', or the
     * equivalent in the current language.
     *
     * The arguments to this function are just like the arguments to get_string
     * except that $component is not optional, and there are some aspects to consider
     * when the string contains {$a} placeholder.
     *
     * If the string does not contain any {$a} placeholder, you can simply use
     * M.str.component.identifier to obtain it. If you prefer, you can call
     * M.util.get_string(identifier, component) to get the same result.
     *
     * If you need to use {$a} placeholders, there are two options. Either the
     * placeholder should be substituted in PHP on server side or it should
     * be substituted in Javascript at client side.
     *
     * To substitute the placeholder at server side, just provide the required
     * value for the placeholder when you require the string. Because each string
     * is only stored once in the JavaScript (based on $identifier and $module)
     * you cannot get the same string with two different values of $a. If you try,
     * an exception will be thrown. Once the placeholder is substituted, you can
     * use M.str or M.util.get_string() as shown above:
     *
     *     // Require the string in PHP and replace the placeholder.
     *     $PAGE->requires->string_for_js('fullnamedisplay', 'moodle', $USER);
     *     // Use the result of the substitution in Javascript.
     *     alert(M.str.moodle.fullnamedisplay);
     *
     * To substitute the placeholder at client side, use M.util.get_string()
     * function. It implements the same logic as {@see get_string()}:
     *
     *     // Require the string in PHP but keep {$a} as it is.
     *     $PAGE->requires->string_for_js('fullnamedisplay', 'moodle');
     *     // Provide the values on the fly in Javascript.
     *     user = { firstname : 'Harry', lastname : 'Potter' }
     *     alert(M.util.get_string('fullnamedisplay', 'moodle', user);
     *
     * If you do need the same string expanded with different $a values in PHP
     * on server side, then the solution is to put them in your own data structure
     * (e.g. and array) that you pass to JavaScript with {@see data_for_js()}.
     *
     * @param string $identifier the desired string.
     * @param string $component the language file to look in.
     * @param mixed $a any extra data to add into the string (optional).
     */
    public function string_for_js($identifier, $component, $a = null) {
        if (!$component) {
            throw new coding_exception('The $component parameter is required for page_requirements_manager::string_for_js().');
        }
        if (isset($this->stringsforjs_as[$component][$identifier]) && $this->stringsforjs_as[$component][$identifier] !== $a) {
            throw new coding_exception(
                "Attempt to re-define already required string '$identifier' " .
                    "from lang file '$component' with different \$a parameter?",
            );
        }
        if (!isset($this->stringsforjs[$component][$identifier])) {
            $this->stringsforjs[$component][$identifier] = new lang_string($identifier, $component, $a);
            $this->stringsforjs_as[$component][$identifier] = $a;
        }
    }

    /**
     * Make an array of language strings available for JS.
     *
     * This function calls the above function {@see string_for_js()} for each requested
     * string in the $identifiers array that is passed to the argument for a single module
     * passed in $module.
     *
     * <code>
     * $PAGE->requires->strings_for_js(array('one', 'two', 'three'), 'mymod', array('a', null, 3));
     *
     * // The above is identical to calling:
     *
     * $PAGE->requires->string_for_js('one', 'mymod', 'a');
     * $PAGE->requires->string_for_js('two', 'mymod');
     * $PAGE->requires->string_for_js('three', 'mymod', 3);
     * </code>
     *
     * @param array $identifiers An array of desired strings
     * @param string $component The module to load for
     * @param mixed $a This can either be a single variable that gets passed as extra
     *         information for every string or it can be an array of mixed data where the
     *         key for the data matches that of the identifier it is meant for.
     *
     */
    public function strings_for_js($identifiers, $component, $a = null) {
        foreach ($identifiers as $key => $identifier) {
            if (is_array($a) && array_key_exists($key, $a)) {
                $extra = $a[$key];
            } else {
                $extra = $a;
            }
            $this->string_for_js($identifier, $component, $extra);
        }
    }

    /**
     * !!!!!!DEPRECATED!!!!!! please use js_init_call() for everything now.
     *
     * Make some data from PHP available to JavaScript code.
     *
     * For example, if you call
     * <pre>
     *      $PAGE->requires->data_for_js('mydata', array('name' => 'Moodle'));
     * </pre>
     * then in JavsScript mydata.name will be 'Moodle'.
     *
     * @deprecated
     * @param string $variable the the name of the JavaScript variable to assign the data to.
     *      Will probably work if you use a compound name like 'mybuttons.button[1]', but this
     *      should be considered an experimental feature.
     * @param mixed $data The data to pass to JavaScript. This will be escaped using json_encode,
     *      so passing objects and arrays should work.
     * @param bool $inhead initialise in head
     * @return void
     */
    public function data_for_js($variable, $data, $inhead = false) {
        $where = $inhead ? 'head' : 'footer';
        $this->jsinitvariables[$where][] = [$variable, $data];
    }

    /**
     * Creates a YUI event handler.
     *
     * @param mixed $selector standard YUI selector for elements, may be array or string, element id is in the form "#idvalue"
     * @param string $event A valid DOM event (click, mousedown, change etc.)
     * @param string $function The name of the function to call
     * @param null|array $arguments An optional array of argument parameters to pass to the function
     */
    public function event_handler(
        $selector,
        $event,
        $function,
        ?array $arguments = null,
    ) {
        $this->eventhandlers[] = ['selector' => $selector, 'event' => $event, 'function' => $function, 'arguments' => $arguments];
    }

    /**
     * Returns code needed for registering of event handlers.
     * @return string JS code
     */
    protected function get_event_handler_code() {
        $output = '';
        foreach ($this->eventhandlers as $h) {
            $output .= js_writer::event_handler($h['selector'], $h['event'], $h['function'], $h['arguments']);
        }
        return $output;
    }

    /**
     * Get the inline JavaScript code that need to appear in a particular place.
     * @param bool $ondomready
     * @return string
     */
    protected function get_javascript_code($ondomready) {
        $where = $ondomready ? 'ondomready' : 'normal';
        $output = '';
        if ($this->jscalls[$where]) {
            foreach ($this->jscalls[$where] as $data) {
                $output .= js_writer::function_call($data[0], $data[1], $data[2]);
            }
            if (!empty($ondomready)) {
                $output = "    Y.on('domready', function() {\n$output\n});";
            }
        }
        return $output;
    }

    /**
     * Returns js code to be executed when Y is available.
     * @return string
     */
    protected function get_javascript_init_code() {
        if (count($this->jsinitcode)) {
            return implode("\n", $this->jsinitcode) . "\n";
        }
        return '';
    }

    /**
     * Returns js code to load amd module loader, then insert inline script tags
     * that contain require() calls using RequireJS.
     * @return string
     */
    protected function get_amd_footercode() {
        global $CFG;
        $output = '';

        // We will cache JS if cachejs is not set, or it is true.
        $cachejs = !isset($CFG->cachejs) || $CFG->cachejs;
        $jsrev = $this->get_jsrev();

        $jsloader = new moodle_url('/lib/javascript.php');
        $jsloader->set_slashargument('/' . $jsrev . '/');
        $requirejsloader = new moodle_url('/lib/requirejs.php');
        $requirejsloader->set_slashargument('/' . $jsrev . '/');

        $requirejsconfig = file_get_contents($CFG->dirroot . '/lib/requirejs/moodle-config.js');

        // No extension required unless slash args is disabled.
        $jsextension = '.js';
        if (!empty($CFG->slasharguments)) {
            $jsextension = '';
        }

        $minextension = '.min';
        if (!$cachejs) {
            $minextension = '';
        }

        $requirejsconfig = str_replace('[BASEURL]', $requirejsloader, $requirejsconfig);
        $requirejsconfig = str_replace('[JSURL]', $jsloader, $requirejsconfig);
        $requirejsconfig = str_replace('[JSMIN]', $minextension, $requirejsconfig);
        $requirejsconfig = str_replace('[JSEXT]', $jsextension, $requirejsconfig);

        $output .= html_writer::script($requirejsconfig);
        if ($cachejs) {
            $output .= html_writer::script('', $this->js_fix_url('/lib/requirejs/require.min.js'));
        } else {
            $output .= html_writer::script('', $this->js_fix_url('/lib/requirejs/require.js'));
        }

        // First include must be to a module with no dependencies, this prevents multiple requests.
        $prefix = <<<EOF
M.util.js_pending("core/first");
require(['core/first'], function() {

EOF;

        if (during_initial_install()) {
            // Do not run a prefetch during initial install as the DB is not available to service WS calls.
            $prefetch = '';
        } else {
            $prefetch = "require(['core/prefetch'])\n";
        }

        $suffix = <<<EOF

    M.util.js_complete("core/first");
});
EOF;

        $output .= html_writer::script($prefix . $prefetch . implode(";\n", $this->amdjscode) . $suffix);
        return $output;
    }

    /**
     * Returns basic YUI3 CSS code.
     *
     * @return string
     */
    protected function get_yui3lib_headcss() {
        global $CFG;

        $yuiformat = '-min';
        if ($this->yui3loader->filter === 'RAW') {
            $yuiformat = '';
        }

        $code = '';
        if ($this->yui3loader->combine) {
            if (!empty($this->yuicssmodules)) {
                $modules = [];
                foreach ($this->yuicssmodules as $module) {
                    $modules[] = "$CFG->yui3version/$module/$module-min.css";
                }
                $code .= '<link rel="stylesheet" type="text/css" href="' .
                    $this->yui3loader->comboBase . implode('&amp;', $modules) . '" />';
            }
            $code .= '<link rel="stylesheet" type="text/css" href="' .
                $this->yui3loader->local_comboBase . 'rollup/' . $CFG->yui3version . '/yui-moodlesimple' . $yuiformat .
                '.css" />';
        } else {
            if (!empty($this->yuicssmodules)) {
                foreach ($this->yuicssmodules as $module) {
                    $code .= '<link rel="stylesheet" type="text/css"href="' .
                        $this->yui3loader->base . $module . '/' . $module .
                        '-min.css" />';
                }
            }
            $code .= '<link rel="stylesheet" type="text/css" href="' .
                $this->yui3loader->local_comboBase . 'rollup/' . $CFG->yui3version . '/yui-moodlesimple' . $yuiformat .
                '.css" />';
        }

        if ($this->yui3loader->filter === 'RAW') {
            $code = str_replace('-min.css', '.css', $code);
        } else if ($this->yui3loader->filter === 'DEBUG') {
            $code = str_replace('-min.css', '.css', $code);
        }
        return $code;
    }

    /**
     * Returns basic YUI3 JS loading code.
     *
     * @return string
     */
    protected function get_yui3lib_headcode() {
        global $CFG;

        $jsrev = $this->get_jsrev();

        $yuiformat = '-min';
        if ($this->yui3loader->filter === 'RAW') {
            $yuiformat = '';
        }

        $format = '-min';
        if ($this->YUI_config->groups['moodle']['filter'] === 'DEBUG') {
            $format = '-debug';
        }

        $rollupversion = $CFG->yui3version;
        if (!empty($CFG->yuipatchlevel)) {
            $rollupversion .= '_' . $CFG->yuipatchlevel;
        }

        $baserollups = [
            'rollup/' . $rollupversion . "/yui-moodlesimple{$yuiformat}.js",
        ];

        if ($this->yui3loader->combine) {
            return '<script src="' .
                    $this->yui3loader->local_comboBase .
                    implode('&amp;', $baserollups) .
                    '"></script>';
        } else {
            $code = '';
            foreach ($baserollups as $rollup) {
                $code .= '<script src="' . $this->yui3loader->local_comboBase . $rollup . '"></script>';
            }
            return $code;
        }
    }

    /**
     * Returns html tags needed for inclusion of theme CSS.
     *
     * @return string
     */
    protected function get_css_code() {
        // First of all the theme CSS, then any custom CSS
        // Please note custom CSS is strongly discouraged,
        // because it can not be overridden by themes!
        // It is suitable only for things like mod/data which accepts CSS from teachers.
        $attributes = ['rel' => 'stylesheet', 'type' => 'text/css'];

        // Add the YUI code first. We want this to be overridden by any Moodle CSS.
        $code = $this->get_yui3lib_headcss();

        // This line of code may look funny but it is currently required in order
        // to avoid MASSIVE display issues in Internet Explorer.
        // As of IE8 + YUI3.1.1 the reference stylesheet (firstthemesheet) gets
        // ignored whenever another resource is added until such time as a redraw
        // is forced, usually by moving the mouse over the affected element.
        $code .= html_writer::tag(
            'script',
            '/** Required in order to fix style inclusion problems in IE with YUI **/',
            ['id' => 'firstthemesheet', 'type' => 'text/css'],
        );

        $urls = $this->cssthemeurls + $this->cssurls;
        foreach ($urls as $url) {
            $attributes['href'] = $url;
            $code .= html_writer::empty_tag('link', $attributes) . "\n";
            // This id is needed in first sheet only so that theme may override YUI sheets loaded on the fly.
            unset($attributes['id']);
        }

        return $code;
    }

    /**
     * Adds extra modules specified after printing of page header.
     *
     * @return string
     */
    protected function get_extra_modules_code() {
        if (empty($this->extramodules)) {
            return '';
        }
        return html_writer::script(js_writer::function_call('M.yui.add_module', [$this->extramodules]));
    }

    /**
     * Generate any HTML that needs to go inside the <head> tag.
     *
     * Normally, this method is called automatically by the code that prints the
     * <head> tag. You should not normally need to call it in your own code.
     *
     * @param moodle_page $page
     * @param core_renderer $renderer
     * @return string the HTML code to to inside the <head> tag.
     */
    public function get_head_code(moodle_page $page, core_renderer $renderer) {
        global $CFG;

        // Note: the $page and $output are not stored here because it would
        // create circular references in memory which prevents garbage collection.
        $this->init_requirements_data($page, $renderer);

        $output = '';

        // Add all standard CSS for this page.
        $output .= $this->get_css_code();

        // Set up the M namespace.
        $js = "var M = {}; M.yui = {};\n";

        // Capture the time now ASAP during page load. This minimises the lag when
        // we try to relate times on the server to times in the browser.
        // An example of where this is used is the quiz countdown timer.
        $js .= "M.pageloadstarttime = new Date();\n";

        // Add a subset of Moodle configuration to the M namespace.
        $js .= js_writer::set_variable('M.cfg', $this->M_cfg, false);

        // Set up global YUI3 loader object - this should contain all code needed by plugins.
        // Note: in JavaScript just use "YUI().use('overlay', function(Y) { .... });",
        // this needs to be done before including any other script.
        $js .= $this->YUI_config->get_config_functions();
        $js .= js_writer::set_variable('YUI_config', $this->YUI_config, false) . "\n";
        $js .= "M.yui.loader = {modules: {}};\n"; // Backwards compatibility only, not used any more.
        $js = $this->YUI_config->update_header_js($js);

        $output .= html_writer::script($js);

        // Add variables.
        if ($this->jsinitvariables['head']) {
            $js = '';
            foreach ($this->jsinitvariables['head'] as $data) {
                [$var, $value] = $data;
                $js .= js_writer::set_variable($var, $value, true);
            }
            $output .= html_writer::script($js);
        }

        // Mark head sending done, it is not possible to anything there.
        $this->headdone = true;

        return $output;
    }

    /**
     * Generate any HTML that needs to go at the start of the <body> tag.
     *
     * Normally, this method is called automatically by the code that prints the
     * <head> tag. You should not normally need to call it in your own code.
     *
     * @param renderer_base $renderer
     * @return string the HTML code to go at the start of the <body> tag.
     */
    public function get_top_of_body_code(renderer_base $renderer) {
        global $CFG;

        // First the skip links.
        $output = $renderer->render_skip_links($this->skiplinks);

        // Include the Polyfills.
        $output .= html_writer::script('', $this->js_fix_url('/lib/polyfills/polyfill.js'));

        // YUI3 JS needs to be loaded early in the body. It should be cached well by the browser.
        $output .= $this->get_yui3lib_headcode();

        // Add hacked jQuery support, it is not intended for standard Moodle distribution!
        $output .= $this->get_jquery_headcode();

        // Link our main JS file, all core stuff should be there.
        $output .= html_writer::script('', $this->js_fix_url('/lib/javascript-static.js'));

        // All the other linked things from HEAD - there should be as few as possible.
        if ($this->jsincludes['head']) {
            foreach ($this->jsincludes['head'] as $url) {
                $output .= html_writer::script('', $url);
            }
        }

        // Then the clever trick for hiding of things not needed when JS works.
        $output .= html_writer::script("document.body.className += ' jsenabled';") . "\n";
        $this->topofbodydone = true;
        return $output;
    }

    /**
     * Generate any HTML that needs to go at the end of the page.
     *
     * Normally, this method is called automatically by the code that prints the
     * page footer. You should not normally need to call it in your own code.
     *
     * @return string the HTML code to to at the end of the page.
     */
    public function get_end_code() {
        global $CFG, $USER;
        $output = '';

        // Set the log level for the JS logging.
        $logconfig = new stdClass();
        $logconfig->level = 'warn';
        if ($CFG->debugdeveloper) {
            $logconfig->level = 'trace';
        }
        $this->js_call_amd('core/log', 'setConfig', [$logconfig]);
        // Add any global JS that needs to run on all pages.
        $this->js_call_amd('core/page_global', 'init');
        $this->js_call_amd('core/utility');
        $this->js_call_amd('core/storage_validation', 'init', [
            !empty($USER->currentlogin) ? (int) $USER->currentlogin : null
        ]);

        // Call amd init functions.
        $output .= $this->get_amd_footercode();

        // Add other requested modules.
        $output .= $this->get_extra_modules_code();

        $this->js_init_code('M.util.js_complete("init");', true);

        // All the other linked scripts - there should be as few as possible.
        if ($this->jsincludes['footer']) {
            foreach ($this->jsincludes['footer'] as $url) {
                $output .= html_writer::script('', $url);
            }
        }

        // Add all needed strings.
        // First add core strings required for some dialogues.
        $this->strings_for_js([
            'confirm',
            'yes',
            'no',
            'areyousure',
            'closebuttontitle',
            'unknownerror',
            'error',
            'file',
            'url',
            // TODO MDL-70830 shortforms should preload the collapseall/expandall strings properly.
            'collapseall',
            'expandall',
        ], 'moodle');
        $this->strings_for_js([
            'debuginfo',
            'line',
            'stacktrace',
        ], 'debug');
        $this->string_for_js('labelsep', 'langconfig');
        if (!empty($this->stringsforjs)) {
            $strings = [];
            foreach ($this->stringsforjs as $component => $v) {
                foreach ($v as $indentifier => $langstring) {
                    $strings[$component][$indentifier] = $langstring->out();
                }
            }
            $output .= html_writer::script(js_writer::set_variable('M.str', $strings));
        }

        // Add variables.
        if ($this->jsinitvariables['footer']) {
            $js = '';
            foreach ($this->jsinitvariables['footer'] as $data) {
                [$var, $value] = $data;
                $js .= js_writer::set_variable($var, $value, true);
            }
            $output .= html_writer::script($js);
        }

        $inyuijs = $this->get_javascript_code(false);
        $ondomreadyjs = $this->get_javascript_code(true);
        $jsinit = $this->get_javascript_init_code();
        $handlersjs = $this->get_event_handler_code();

        // There is a global Y, make sure it is available in your scope.
        $js = "(function() {{$inyuijs}{$ondomreadyjs}{$jsinit}{$handlersjs}})();";

        $output .= html_writer::script($js);

        return $output;
    }

    /**
     * Have we already output the code in the <head> tag?
     *
     * @return bool
     */
    public function is_head_done() {
        return $this->headdone;
    }

    /**
     * Have we already output the code at the start of the <body> tag?
     *
     * @return bool
     */
    public function is_top_of_body_done() {
        return $this->topofbodydone;
    }

    /**
     * Should we generate a bit of content HTML that is only required once  on
     * this page (e.g. the contents of the modchooser), now? Basically, we call
     * {@see has_one_time_item_been_created()}, and if the thing has not already
     * been output, we return true to tell the caller to generate it, and also
     * call {@see set_one_time_item_created()} to record the fact that it is
     * about to be generated.
     *
     * That is, a typical usage pattern (in a renderer method) is:
     * <pre>
     * if (!$this->page->requires->should_create_one_time_item_now($thing)) {
     *     return '';
     * }
     * // Else generate it.
     * </pre>
     *
     * @param string $thing identifier for the bit of content. Should be of the form
     *      frankenstyle_things, e.g. core_course_modchooser.
     * @return bool if true, the caller should generate that bit of output now, otherwise don't.
     */
    public function should_create_one_time_item_now($thing) {
        if ($this->has_one_time_item_been_created($thing)) {
            return false;
        }

        $this->set_one_time_item_created($thing);
        return true;
    }

    /**
     * Has a particular bit of HTML that is only required once  on this page
     * (e.g. the contents of the modchooser) already been generated?
     *
     * Normally, you can use the {@see should_create_one_time_item_now()} helper
     * method rather than calling this method directly.
     *
     * @param string $thing identifier for the bit of content. Should be of the form
     *      frankenstyle_things, e.g. core_course_modchooser.
     * @return bool whether that bit of output has been created.
     */
    public function has_one_time_item_been_created($thing) {
        return isset($this->onetimeitemsoutput[$thing]);
    }

    /**
     * Indicate that a particular bit of HTML that is only required once on this
     * page (e.g. the contents of the modchooser) has been generated (or is about to be)?
     *
     * Normally, you can use the {@see should_create_one_time_item_now()} helper
     * method rather than calling this method directly.
     *
     * @param string $thing identifier for the bit of content. Should be of the form
     *      frankenstyle_things, e.g. core_course_modchooser.
     */
    public function set_one_time_item_created($thing) {
        if ($this->has_one_time_item_been_created($thing)) {
            throw new coding_exception($thing . ' is only supposed to be ouput ' .
                    'once per page, but it seems to be being output again.');
        }
        return $this->onetimeitemsoutput[$thing] = true;
    }
}

// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(page_requirements_manager::class, \page_requirements_manager::class);

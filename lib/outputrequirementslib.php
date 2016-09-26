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
 * Library functions to facilitate the use of JavaScript in Moodle.
 *
 * Note: you can find history of this file in lib/ajax/ajaxlib.php
 *
 * @copyright 2009 Tim Hunt, 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package core
 * @category output
 */

defined('MOODLE_INTERNAL') || die();

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
 * There are some natural restrictions on some methods. For example, {@link css()}
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
    protected $stringsforjs = array();

    /**
     * @var array List of get_string $a parameters - used for validation only.
     */
    protected $stringsforjs_as = array();

    /**
     * @var array List of JS variables to be initialised
     */
    protected $jsinitvariables = array('head'=>array(), 'footer'=>array());

    /**
     * @var array Included JS scripts
     */
    protected $jsincludes = array('head'=>array(), 'footer'=>array());

    /**
     * @var array Inline scripts using RequireJS module loading.
     */
    protected $amdjscode = array('');

    /**
     * @var array List of needed function calls
     */
    protected $jscalls = array('normal'=>array(), 'ondomready'=>array());

    /**
     * @var array List of skip links, those are needed for accessibility reasons
     */
    protected $skiplinks = array();

    /**
     * @var array Javascript code used for initialisation of page, it should
     * be relatively small
     */
    protected $jsinitcode = array();

    /**
     * @var array of moodle_url Theme sheets, initialised only from core_renderer
     */
    protected $cssthemeurls = array();

    /**
     * @var array of moodle_url List of custom theme sheets, these are strongly discouraged!
     * Useful mostly only for CSS submitted by teachers that is not part of the theme.
     */
    protected $cssurls = array();

    /**
     * @var array List of requested event handlers
     */
    protected $eventhandlers = array();

    /**
     * @var array Extra modules
     */
    protected $extramodules = array();

    /**
     * @var array trackes the names of bits of HTML that are only required once
     * per page. See {@link has_one_time_item_been_created()},
     * {@link set_one_time_item_created()} and {@link should_create_one_time_item_now()}.
     */
    protected $onetimeitemsoutput = array();

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
     * @var YUI_config default YUI loader configuration
     */
    protected $YUI_config;

    /**
     * @var array $yuicssmodules
     */
    protected $yuicssmodules = array();

    /**
     * @var array Some config vars exposed in JS, please no secret stuff there
     */
    protected $M_cfg;

    /**
     * @var array list of requested jQuery plugins
     */
    protected $jqueryplugins = array();

    /**
     * @var array list of jQuery plugin overrides
     */
    protected $jquerypluginoverrides = array();

    /**
     * Page requirements constructor.
     */
    public function __construct() {
        global $CFG;

        // You may need to set up URL rewrite rule because oversized URLs might not be allowed by web server.
        $sep = empty($CFG->yuislasharguments) ? '?' : '/';

        $this->yui3loader = new stdClass();
        $this->YUI_config = new YUI_config();

        if (is_https()) {
            // On HTTPS sites all JS must be loaded from https sites,
            // YUI CDN does not support https yet, sorry.
            $CFG->useexternalyui = 0;
        }

        // Set up some loader options.
        $this->yui3loader->local_base = $CFG->httpswwwroot . '/lib/yuilib/'. $CFG->yui3version . '/';
        $this->yui3loader->local_comboBase = $CFG->httpswwwroot . '/theme/yui_combo.php'.$sep;

        if (!empty($CFG->useexternalyui)) {
            $this->yui3loader->base = 'http://yui.yahooapis.com/' . $CFG->yui3version . '/';
            $this->yui3loader->comboBase = 'http://yui.yahooapis.com/combo?';
        } else {
            $this->yui3loader->base = $this->yui3loader->local_base;
            $this->yui3loader->comboBase = $this->yui3loader->local_comboBase;
        }

        // Enable combo loader? This significantly helps with caching and performance!
        $this->yui3loader->combine = !empty($CFG->yuicomboloading);

        $jsrev = $this->get_jsrev();

        // Set up JS YUI loader helper object.
        $this->YUI_config->base         = $this->yui3loader->base;
        $this->YUI_config->comboBase    = $this->yui3loader->comboBase;
        $this->YUI_config->combine      = $this->yui3loader->combine;

        // If we've had to patch any YUI modules between releases, we must override the YUI configuration to include them.
        // For important information on patching YUI modules, please see http://docs.moodle.org/dev/YUI/Patching.
        if (!empty($CFG->yuipatchedmodules) && !empty($CFG->yuipatchlevel)) {
            $this->YUI_config->define_patched_core_modules($this->yui3loader->local_comboBase,
                    $CFG->yui3version,
                    $CFG->yuipatchlevel,
                    $CFG->yuipatchedmodules);
        }

        $configname = $this->YUI_config->set_config_source('lib/yui/config/yui2.js');
        $this->YUI_config->add_group('yui2', array(
            // Loader configuration for our 2in3, for now ignores $CFG->useexternalyui.
            'base' => $CFG->httpswwwroot . '/lib/yuilib/2in3/' . $CFG->yui2version . '/build/',
            'comboBase' => $CFG->httpswwwroot . '/theme/yui_combo.php'.$sep,
            'combine' => $this->yui3loader->combine,
            'ext' => false,
            'root' => '2in3/' . $CFG->yui2version .'/build/',
            'patterns' => array(
                'yui2-' => array(
                    'group' => 'yui2',
                    'configFn' => $configname,
                )
            )
        ));
        $configname = $this->YUI_config->set_config_source('lib/yui/config/moodle.js');
        $this->YUI_config->add_group('moodle', array(
            'name' => 'moodle',
            'base' => $CFG->httpswwwroot . '/theme/yui_combo.php' . $sep . 'm/' . $jsrev . '/',
            'combine' => $this->yui3loader->combine,
            'comboBase' => $CFG->httpswwwroot . '/theme/yui_combo.php'.$sep,
            'ext' => false,
            'root' => 'm/'.$jsrev.'/', // Add the rev to the root path so that we can control caching.
            'patterns' => array(
                'moodle-' => array(
                    'group' => 'moodle',
                    'configFn' => $configname,
                )
            )
        ));

        $this->YUI_config->add_group('gallery', array(
            'name' => 'gallery',
            'base' => $CFG->httpswwwroot . '/lib/yuilib/gallery/',
            'combine' => $this->yui3loader->combine,
            'comboBase' => $CFG->httpswwwroot . '/theme/yui_combo.php' . $sep,
            'ext' => false,
            'root' => 'gallery/' . $jsrev . '/',
            'patterns' => array(
                'gallery-' => array(
                    'group' => 'gallery',
                )
            )
        ));

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
     * @return array List of safe config values that are available to javascript.
     */
    public function get_config_for_javascript(moodle_page $page, renderer_base $renderer) {
        global $CFG;

        if (empty($this->M_cfg)) {
            // JavaScript should always work with $CFG->httpswwwroot rather than $CFG->wwwroot.
            // Otherwise, in some situations, users will get warnings about insecure content
            // on secure pages from their web browser.

            $this->M_cfg = array(
                'wwwroot'             => $CFG->httpswwwroot, // Yes, really. See above.
                'sesskey'             => sesskey(),
                'loadingicon'         => $renderer->pix_url('i/loading_small', 'moodle')->out(false),
                'themerev'            => theme_get_revision(),
                'slasharguments'      => (int)(!empty($CFG->slasharguments)),
                'theme'               => $page->theme->name,
                'jsrev'               => $this->get_jsrev(),
                'admin'               => $CFG->admin,
                'svgicons'            => $page->theme->use_svg_icons()
            );
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

        // Include block drag/drop if editing is on
        if ($page->user_is_editing()) {
            $params = array(
                'courseid' => $page->course->id,
                'pagetype' => $page->pagetype,
                'pagelayout' => $page->pagelayout,
                'subpage' => $page->subpage,
                'regions' => $page->blocks->get_regions(),
                'contextid' => $page->context->id,
            );
            if (!empty($page->cm->id)) {
                $params['cmid'] = $page->cm->id;
            }
            // Strings for drag and drop.
            $this->strings_for_js(array('movecontent',
                                        'tocontent',
                                        'emptydragdropregion'),
                                  'moodle');
            $page->requires->yui_module('moodle-core-blocks', 'M.core_blocks.init_dragdrop', array($params), null, true);
        }

        // Include the YUI CSS Modules.
        $page->requires->set_yuicssmodules($page->theme->yuicssmodules);
    }

    /**
     * Determine the correct JS Revision to use for this load.
     *
     * @return int the jsrev to use.
     */
    protected function get_jsrev() {
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
        $url = $this->js_fix_url($url);
        $where = $inhead ? 'head' : 'footer';
        $this->jsincludes[$where][$url->out()] = $url;
    }

    /**
     * Request inclusion of jQuery library in the page.
     *
     * NOTE: this should not be used in official Moodle distribution!
     *
     * {@see http://docs.moodle.org/dev/jQuery}
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
     * {@see http://docs.moodle.org/dev/jQuery}
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

        if ($component !== 'core' and in_array($plugin, array('jquery', 'ui', 'ui-css'))) {
            debugging("jQuery plugin '$plugin' is included in Moodle core, other components can not use the same name.", DEBUG_DEVELOPER);
            $component = 'core';
        } else if ($component !== 'core' and strpos($component, '_') === false) {
            // Let's normalise the legacy activity names, Frankenstyle rulez!
            $component = 'mod_' . $component;
        }

        if (empty($this->jqueryplugins) and ($component !== 'core' or $plugin !== 'jquery')) {
            // Make sure the jQuery itself is always loaded first,
            // the order of all other plugins depends on order of $PAGE_>requires->.
            $this->jquery_plugin('jquery', 'core');
        }

        if (isset($this->jqueryplugins[$plugin])) {
            // No problem, we already have something, first Moodle plugin to register the jQuery plugin wins.
            return true;
        }

        $componentdir = core_component::get_component_directory($component);
        if (!file_exists($componentdir) or !file_exists("$componentdir/jquery/plugins.php")) {
            debugging("Can not load jQuery plugin '$plugin', missing plugins.php in component '$component'.", DEBUG_DEVELOPER);
            return false;
        }

        $plugins = array();
        require("$componentdir/jquery/plugins.php");

        if (!isset($plugins[$plugin])) {
            debugging("jQuery plugin '$plugin' can not be found in component '$component'.", DEBUG_DEVELOPER);
            return false;
        }

        $this->jqueryplugins[$plugin] = new stdClass();
        $this->jqueryplugins[$plugin]->plugin    = $plugin;
        $this->jqueryplugins[$plugin]->component = $component;
        $this->jqueryplugins[$plugin]->urls      = array();

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
                $url = new moodle_url("$CFG->httpswwwroot/theme/jquery.php");
                $url->set_slashargument("/$component/$file");

            } else {
                // This is not really good, we need slasharguments for relative links, this means no caching...
                $path = realpath("$componentdir/jquery/$file");
                if (strpos($path, $CFG->dirroot) === 0) {
                    $url = $CFG->httpswwwroot.preg_replace('/^'.preg_quote($CFG->dirroot, '/').'/', '', $path);
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
     * {@see http://docs.moodle.org/dev/jQuery}
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

        $included = array();
        $urls = array();

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
                for ($i=0; $i<100; $i++) {
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
        $attributes = array('rel' => 'stylesheet', 'type' => 'text/css');
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
     * Returns the actual url through which a script is served.
     *
     * @param moodle_url|string $url full moodle url, or shortened path to script
     * @return moodle_url
     */
    protected function js_fix_url($url) {
        global $CFG;

        if ($url instanceof moodle_url) {
            return $url;
        } else if (strpos($url, '/') === 0) {
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
                    return new moodle_url($CFG->httpswwwroot.'/lib/javascript.php', array('rev'=>$jsrev, 'jsfile'=>$url));
                } else {
                    $returnurl = new moodle_url($CFG->httpswwwroot.'/lib/javascript.php');
                    $returnurl->set_slashargument('/'.$jsrev.$url);
                    return $returnurl;
                }
            } else {
                return new moodle_url($CFG->httpswwwroot.$url);
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
            switch($component) {
                case 'core_filepicker':
                    $module = array('name'     => 'core_filepicker',
                                    'fullpath' => '/repository/filepicker.js',
                                    'requires' => array('base', 'node', 'node-event-simulate', 'json', 'async-queue', 'io-base', 'io-upload-iframe', 'io-form', 'yui2-treeview', 'panel', 'cookie', 'datatable', 'datatable-sort', 'resize-plugin', 'dd-plugin', 'escape', 'moodle-core_filepicker'),
                                    'strings'  => array(array('lastmodified', 'moodle'), array('name', 'moodle'), array('type', 'repository'), array('size', 'repository'),
                                                        array('invalidjson', 'repository'), array('error', 'moodle'), array('info', 'moodle'),
                                                        array('nofilesattached', 'repository'), array('filepicker', 'repository'), array('logout', 'repository'),
                                                        array('nofilesavailable', 'repository'), array('norepositoriesavailable', 'repository'),
                                                        array('fileexistsdialogheader', 'repository'), array('fileexistsdialog_editor', 'repository'),
                                                        array('fileexistsdialog_filemanager', 'repository'), array('renameto', 'repository'),
                                                        array('referencesexist', 'repository'), array('select', 'repository')
                                                    ));
                    break;
                case 'core_comment':
                    $module = array('name'     => 'core_comment',
                                    'fullpath' => '/comment/comment.js',
                                    'requires' => array('base', 'io-base', 'node', 'json', 'yui2-animation', 'overlay'),
                                    'strings' => array(array('confirmdeletecomments', 'admin'), array('yes', 'moodle'), array('no', 'moodle'))
                                );
                    break;
                case 'core_role':
                    $module = array('name'     => 'core_role',
                                    'fullpath' => '/admin/roles/module.js',
                                    'requires' => array('node', 'cookie'));
                    break;
                case 'core_completion':
                    $module = array('name'     => 'core_completion',
                                    'fullpath' => '/course/completion.js');
                    break;
                case 'core_message':
                    $module = array('name'     => 'core_message',
                                    'requires' => array('base', 'node', 'event', 'node-event-simulate'),
                                    'fullpath' => '/message/module.js');
                    break;
                case 'core_group':
                    $module = array('name'     => 'core_group',
                                    'fullpath' => '/group/module.js',
                                    'requires' => array('node', 'overlay', 'event-mouseenter'));
                    break;
                case 'core_question_engine':
                    $module = array('name'     => 'core_question_engine',
                                    'fullpath' => '/question/qengine.js',
                                    'requires' => array('node', 'event'));
                    break;
                case 'core_rating':
                    $module = array('name'     => 'core_rating',
                                    'fullpath' => '/rating/module.js',
                                    'requires' => array('node', 'event', 'overlay', 'io-base', 'json'));
                    break;
                case 'core_dndupload':
                    $module = array('name'     => 'core_dndupload',
                                    'fullpath' => '/lib/form/dndupload.js',
                                    'requires' => array('node', 'event', 'json', 'core_filepicker'),
                                    'strings'  => array(array('uploadformlimit', 'moodle'), array('droptoupload', 'moodle'), array('maxfilesreached', 'moodle'),
                                                        array('dndenabled_inbox', 'moodle'), array('fileexists', 'moodle'), array('maxbytesfile', 'error'),
                                                        array('sizegb', 'moodle'), array('sizemb', 'moodle'), array('sizekb', 'moodle'), array('sizeb', 'moodle'),
                                                        array('maxareabytesreached', 'moodle'), array('serverconnection', 'error'),
                                                    ));
                    break;
            }

        } else {
            if ($dir = core_component::get_component_directory($component)) {
                if (file_exists("$dir/module.js")) {
                    if (strpos($dir, $CFG->dirroot.'/') === 0) {
                        $dir = substr($dir, strlen($CFG->dirroot));
                        $module = array('name'=>$component, 'fullpath'=>"$dir/module.js", 'requires' => array());
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

        if (empty($module) or empty($module['name']) or empty($module['fullpath'])) {
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
        if (!empty($module['requires'])){
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
     * this function before {@link get_head_code()} is called. That normally means before
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

        if ($stylesheet instanceof moodle_url) {
            // ok
        } else if (strpos($stylesheet, '/') === 0) {
            $stylesheet = new moodle_url($CFG->httpswwwroot.$stylesheet);
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
     * You must call this function before {@link get_top_of_body_code()}, (if not, an exception
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
     * @param array $arguments and array of arguments to be passed to the function.
     *      When generating the function call, this will be escaped using json_encode,
     *      so passing objects and arrays should work.
     * @param bool $ondomready If tru the function is only called when the dom is
     *      ready for manipulation.
     * @param int $delay The delay before the function is called.
     */
    public function js_function_call($function, array $arguments = null, $ondomready = false, $delay = 0) {
        $where = $ondomready ? 'ondomready' : 'normal';
        $this->jscalls[$where][] = array($function, $arguments, $delay);
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
     * This function creates a minimal JS script that requires and calls a single function from an AMD module with arguments.
     * If it is called multiple times, it will be executed multiple times.
     *
     * @param string $fullmodule The format for module names is <component name>/<module name>.
     * @param string $func The function from the module to call
     * @param array $params The params to pass to the function. They will be json encoded, so no nasty classes/types please.
     */
    public function js_call_amd($fullmodule, $func, $params = array()) {
        global $CFG;

        list($component, $module) = explode('/', $fullmodule, 2);

        $component = clean_param($component, PARAM_COMPONENT);
        $module = clean_param($module, PARAM_ALPHANUMEXT);
        $func = clean_param($func, PARAM_ALPHANUMEXT);

        $jsonparams = array();
        foreach ($params as $param) {
            $jsonparams[] = json_encode($param);
        }
        $strparams = implode(', ', $jsonparams);
        if ($CFG->debugdeveloper) {
            $toomanyparamslimit = 1024;
            if (strlen($strparams) > $toomanyparamslimit) {
                debugging('Too many params passed to js_call_amd("' . $fullmodule . '", "' . $func . '")', DEBUG_DEVELOPER);
            }
        }

        $js = 'require(["' . $component . '/' . $module . '"], function(amd) { amd.' . $func . '(' . $strparams . '); });';

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
     * @param array $arguments An array of arguments to pass to the function
     * @param string $galleryversion Deprecated: The gallery version to use
     * @param bool $ondomready
     */
    public function yui_module($modules, $function, array $arguments = null, $galleryversion = null, $ondomready = false) {
        if (!is_array($modules)) {
            $modules = array($modules);
        }

        if ($galleryversion != null) {
            debugging('The galleryversion parameter to yui_module has been deprecated since Moodle 2.3.');
        }

        $jscode = 'Y.use('.join(',', array_map('json_encode', convert_to_array($modules))).',function() {'.js_writer::function_call($function, $arguments).'});';
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
    public function set_yuicssmodules(array $modules = array()) {
        $this->yuicssmodules = $modules;
    }

    /**
     * Ensure that the specified JavaScript function is called from an inline script
     * from page footer.
     *
     * @param string $function the name of the JavaScritp function to with init code,
     *      usually something like 'M.mod_mymodule.init'
     * @param array $extraarguments and array of arguments to be passed to the function.
     *      The first argument is always the YUI3 Y instance with all required dependencies
     *      already loaded.
     * @param bool $ondomready wait for dom ready (helps with some IE problems when modifying DOM)
     * @param array $module JS module specification array
     */
    public function js_init_call($function, array $extraarguments = null, $ondomready = false, array $module = null) {
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
     * @param array $module JS module specification array
     */
    public function js_init_code($jscode, $ondomready = false, array $module = null) {
        $jscode = trim($jscode, " ;\n"). ';';

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
     * function. It implements the same logic as {@link get_string()}:
     *
     *     // Require the string in PHP but keep {$a} as it is.
     *     $PAGE->requires->string_for_js('fullnamedisplay', 'moodle');
     *     // Provide the values on the fly in Javascript.
     *     user = { firstname : 'Harry', lastname : 'Potter' }
     *     alert(M.util.get_string('fullnamedisplay', 'moodle', user);
     *
     * If you do need the same string expanded with different $a values in PHP
     * on server side, then the solution is to put them in your own data structure
     * (e.g. and array) that you pass to JavaScript with {@link data_for_js()}.
     *
     * @param string $identifier the desired string.
     * @param string $component the language file to look in.
     * @param mixed $a any extra data to add into the string (optional).
     */
    public function string_for_js($identifier, $component, $a = null) {
        if (!$component) {
            throw new coding_exception('The $component parameter is required for page_requirements_manager::string_for_js().');
        }
        if (isset($this->stringsforjs_as[$component][$identifier]) and $this->stringsforjs_as[$component][$identifier] !== $a) {
            throw new coding_exception("Attempt to re-define already required string '$identifier' " .
                    "from lang file '$component' with different \$a parameter?");
        }
        if (!isset($this->stringsforjs[$component][$identifier])) {
            $this->stringsforjs[$component][$identifier] = new lang_string($identifier, $component, $a);
            $this->stringsforjs_as[$component][$identifier] = $a;
        }
    }

    /**
     * Make an array of language strings available for JS.
     *
     * This function calls the above function {@link string_for_js()} for each requested
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
    public function data_for_js($variable, $data, $inhead=false) {
        $where = $inhead ? 'head' : 'footer';
        $this->jsinitvariables[$where][] = array($variable, $data);
    }

    /**
     * Creates a YUI event handler.
     *
     * @param mixed $selector standard YUI selector for elements, may be array or string, element id is in the form "#idvalue"
     * @param string $event A valid DOM event (click, mousedown, change etc.)
     * @param string $function The name of the function to call
     * @param array  $arguments An optional array of argument parameters to pass to the function
     */
    public function event_handler($selector, $event, $function, array $arguments = null) {
        $this->eventhandlers[] = array('selector'=>$selector, 'event'=>$event, 'function'=>$function, 'arguments'=>$arguments);
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
        $jsrev = $this->get_jsrev();

        $jsloader = new moodle_url($CFG->httpswwwroot . '/lib/javascript.php');
        $jsloader->set_slashargument('/' . $jsrev . '/');
        $requirejsloader = new moodle_url($CFG->httpswwwroot . '/lib/requirejs.php');
        $requirejsloader->set_slashargument('/' . $jsrev . '/');

        $requirejsconfig = file_get_contents($CFG->dirroot . '/lib/requirejs/moodle-config.js');

        // No extension required unless slash args is disabled.
        $jsextension = '.js';
        if (!empty($CFG->slasharguments)) {
            $jsextension = '';
        }

        $requirejsconfig = str_replace('[BASEURL]', $requirejsloader, $requirejsconfig);
        $requirejsconfig = str_replace('[JSURL]', $jsloader, $requirejsconfig);
        $requirejsconfig = str_replace('[JSEXT]', $jsextension, $requirejsconfig);

        $output .= html_writer::script($requirejsconfig);
        if ($CFG->debugdeveloper) {
            $output .= html_writer::script('', $this->js_fix_url('/lib/requirejs/require.js'));
        } else {
            $output .= html_writer::script('', $this->js_fix_url('/lib/requirejs/require.min.js'));
        }

        // First include must be to a module with no dependencies, this prevents multiple requests.
        $prefix = "require(['core/first'], function() {\n";
        $suffix = "\n});";
        $output .= html_writer::script($prefix . implode(";\n", $this->amdjscode) . $suffix);
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
                $modules = array();
                foreach ($this->yuicssmodules as $module) {
                    $modules[] = "$CFG->yui3version/$module/$module-min.css";
                }
                $code .= '<link rel="stylesheet" type="text/css" href="'.$this->yui3loader->comboBase.implode('&amp;', $modules).'" />';
            }
            $code .= '<link rel="stylesheet" type="text/css" href="'.$this->yui3loader->local_comboBase.'rollup/'.$CFG->yui3version.'/yui-moodlesimple' . $yuiformat . '.css" />';

        } else {
            if (!empty($this->yuicssmodules)) {
                foreach ($this->yuicssmodules as $module) {
                    $code .= '<link rel="stylesheet" type="text/css" href="'.$this->yui3loader->base.$module.'/'.$module.'-min.css" />';
                }
            }
            $code .= '<link rel="stylesheet" type="text/css" href="'.$this->yui3loader->local_comboBase.'rollup/'.$CFG->yui3version.'/yui-moodlesimple' . $yuiformat . '.css" />';
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

        $baserollups = array(
            'rollup/' . $rollupversion . "/yui-moodlesimple{$yuiformat}.js",
            'rollup/' . $jsrev . "/mcore{$format}.js",
        );

        if ($this->yui3loader->combine) {
            return '<script type="text/javascript" src="' .
                    $this->yui3loader->local_comboBase .
                    implode('&amp;', $baserollups) .
                    '"></script>';
        } else {
            $code = '';
            foreach ($baserollups as $rollup) {
                $code .= '<script type="text/javascript" src="'.$this->yui3loader->local_comboBase.$rollup.'"></script>';
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
        $attributes = array('rel'=>'stylesheet', 'type'=>'text/css');

        // Add the YUI code first. We want this to be overridden by any Moodle CSS.
        $code = $this->get_yui3lib_headcss();

        // This line of code may look funny but it is currently required in order
        // to avoid MASSIVE display issues in Internet Explorer.
        // As of IE8 + YUI3.1.1 the reference stylesheet (firstthemesheet) gets
        // ignored whenever another resource is added until such time as a redraw
        // is forced, usually by moving the mouse over the affected element.
        $code .= html_writer::tag('script', '/** Required in order to fix style inclusion problems in IE with YUI **/', array('id'=>'firstthemesheet', 'type'=>'text/css'));

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
        return html_writer::script(js_writer::function_call('M.yui.add_module', array($this->extramodules)));
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
        //       this needs to be done before including any other script.
        $js .= $this->YUI_config->get_config_functions();
        $js .= js_writer::set_variable('YUI_config', $this->YUI_config, false) . "\n";
        $js .= "M.yui.loader = {modules: {}};\n"; // Backwards compatibility only, not used any more.
        $js = $this->YUI_config->update_header_js($js);

        $output .= html_writer::script($js);

        // Add variables.
        if ($this->jsinitvariables['head']) {
            $js = '';
            foreach ($this->jsinitvariables['head'] as $data) {
                list($var, $value) = $data;
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
        // First the skip links.
        $output = $renderer->render_skip_links($this->skiplinks);

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
        global $CFG;
        $output = '';

        // Set the log level for the JS logging.
        $logconfig = new stdClass();
        $logconfig->level = 'warn';
        if ($CFG->debugdeveloper) {
            $logconfig->level = 'trace';
        }
        $this->js_call_amd('core/log', 'setConfig', array($logconfig));

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
        $this->strings_for_js(array(
            'confirm',
            'yes',
            'no',
            'areyousure',
            'closebuttontitle',
            'unknownerror',
        ), 'moodle');
        if (!empty($this->stringsforjs)) {
            $strings = array();
            foreach ($this->stringsforjs as $component=>$v) {
                foreach($v as $indentifier => $langstring) {
                    $strings[$component][$indentifier] = $langstring->out();
                }
            }
            $output .= html_writer::script(js_writer::set_variable('M.str', $strings));
        }

        // Add variables.
        if ($this->jsinitvariables['footer']) {
            $js = '';
            foreach ($this->jsinitvariables['footer'] as $data) {
                list($var, $value) = $data;
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
     * {@link has_one_time_item_been_created()}, and if the thing has not already
     * been output, we return true to tell the caller to generate it, and also
     * call {@link set_one_time_item_created()} to record the fact that it is
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
     * Normally, you can use the {@link should_create_one_time_item_now()} helper
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
     * Normally, you can use the {@link should_create_one_time_item_now()} helper
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

/**
 * This class represents the YUI configuration.
 *
 * @copyright 2013 Andrew Nicols
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.5
 * @package core
 * @category output
 */
class YUI_config {
    /**
     * These settings must be public so that when the object is converted to json they are exposed.
     * Note: Some of these are camelCase because YUI uses camelCase variable names.
     *
     * The settings are described and documented in the YUI API at:
     * - http://yuilibrary.com/yui/docs/api/classes/config.html
     * - http://yuilibrary.com/yui/docs/api/classes/Loader.html
     */
    public $debug = false;
    public $base;
    public $comboBase;
    public $combine;
    public $filter = null;
    public $insertBefore = 'firstthemesheet';
    public $groups = array();
    public $modules = array();

    /**
     * @var array List of functions used by the YUI Loader group pattern recognition.
     */
    protected $jsconfigfunctions = array();

    /**
     * Create a new group within the YUI_config system.
     *
     * @param String $name The name of the group. This must be unique and
     * not previously used.
     * @param Array $config The configuration for this group.
     * @return void
     */
    public function add_group($name, $config) {
        if (isset($this->groups[$name])) {
            throw new coding_exception("A YUI configuration group for '{$name}' already exists. To make changes to this group use YUI_config->update_group().");
        }
        $this->groups[$name] = $config;
    }

    /**
     * Update an existing group configuration
     *
     * Note, any existing configuration for that group will be wiped out.
     * This includes module configuration.
     *
     * @param String $name The name of the group. This must be unique and
     * not previously used.
     * @param Array $config The configuration for this group.
     * @return void
     */
    public function update_group($name, $config) {
        if (!isset($this->groups[$name])) {
            throw new coding_exception('The Moodle YUI module does not exist. You must define the moodle module config using YUI_config->add_module_config first.');
        }
        $this->groups[$name] = $config;
    }

    /**
     * Set the value of a configuration function used by the YUI Loader's pattern testing.
     *
     * Only the body of the function should be passed, and not the whole function wrapper.
     *
     * The JS function your write will be passed a single argument 'name' containing the
     * name of the module being loaded.
     *
     * @param $function String the body of the JavaScript function. This should be used i
     * @return String the name of the function to use in the group pattern configuration.
     */
    public function set_config_function($function) {
        $configname = 'yui' . (count($this->jsconfigfunctions) + 1) . 'ConfigFn';
        if (isset($this->jsconfigfunctions[$configname])) {
            throw new coding_exception("A YUI config function with this name already exists. Config function names must be unique.");
        }
        $this->jsconfigfunctions[$configname] = $function;
        return '@' . $configname . '@';
    }

    /**
     * Allow setting of the config function described in {@see set_config_function} from a file.
     * The contents of this file are then passed to set_config_function.
     *
     * When jsrev is positive, the function is minified and stored in a MUC cache for subsequent uses.
     *
     * @param $file The path to the JavaScript function used for YUI configuration.
     * @return String the name of the function to use in the group pattern configuration.
     */
    public function set_config_source($file) {
        global $CFG;
        $cache = cache::make('core', 'yuimodules');

        // Attempt to get the metadata from the cache.
        $keyname = 'configfn_' . $file;
        $fullpath = $CFG->dirroot . '/' . $file;
        if (!isset($CFG->jsrev) || $CFG->jsrev == -1) {
            $cache->delete($keyname);
            $configfn = file_get_contents($fullpath);
        } else {
            $configfn = $cache->get($keyname);
            if ($configfn === false) {
                require_once($CFG->libdir . '/jslib.php');
                $configfn = core_minify::js_files(array($fullpath));
                $cache->set($keyname, $configfn);
            }
        }
        return $this->set_config_function($configfn);
    }

    /**
     * Retrieve the list of JavaScript functions for YUI_config groups.
     *
     * @return String The complete set of config functions
     */
    public function get_config_functions() {
        $configfunctions = '';
        foreach ($this->jsconfigfunctions as $functionname => $function) {
            $configfunctions .= "var {$functionname} = function(me) {";
            $configfunctions .= $function;
            $configfunctions .= "};\n";
        }
        return $configfunctions;
    }

    /**
     * Update the header JavaScript with any required modification for the YUI Loader.
     *
     * @param $js String The JavaScript to manipulate.
     * @return String the modified JS string.
     */
    public function update_header_js($js) {
        // Update the names of the the configFn variables.
        // The PHP json_encode function cannot handle literal names so we have to wrap
        // them in @ and then replace them with literals of the same function name.
        foreach ($this->jsconfigfunctions as $functionname => $function) {
            $js = str_replace('"@' . $functionname . '@"', $functionname, $js);
        }
        return $js;
    }

    /**
     * Add configuration for a specific module.
     *
     * @param String $name The name of the module to add configuration for.
     * @param Array $config The configuration for the specified module.
     * @param String $group The name of the group to add configuration for.
     * If not specified, then this module is added to the global
     * configuration.
     * @return void
     */
    public function add_module_config($name, $config, $group = null) {
        if ($group) {
            if (!isset($this->groups[$name])) {
                throw new coding_exception('The Moodle YUI module does not exist. You must define the moodle module config using YUI_config->add_module_config first.');
            }
            if (!isset($this->groups[$group]['modules'])) {
                $this->groups[$group]['modules'] = array();
            }
            $modules = &$this->groups[$group]['modules'];
        } else {
            $modules = &$this->modules;
        }
        $modules[$name] = $config;
    }

    /**
     * Add the moodle YUI module metadata for the moodle group to the YUI_config instance.
     *
     * If js caching is disabled, metadata will not be served causing YUI to calculate
     * module dependencies as each module is loaded.
     *
     * If metadata does not exist it will be created and stored in a MUC entry.
     *
     * @return void
     */
    public function add_moodle_metadata() {
        global $CFG;
        if (!isset($this->groups['moodle'])) {
            throw new coding_exception('The Moodle YUI module does not exist. You must define the moodle module config using YUI_config->add_module_config first.');
        }

        if (!isset($this->groups['moodle']['modules'])) {
            $this->groups['moodle']['modules'] = array();
        }

        $cache = cache::make('core', 'yuimodules');
        if (!isset($CFG->jsrev) || $CFG->jsrev == -1) {
            $metadata = array();
            $metadata = $this->get_moodle_metadata();
            $cache->delete('metadata');
        } else {
            // Attempt to get the metadata from the cache.
            if (!$metadata = $cache->get('metadata')) {
                $metadata = $this->get_moodle_metadata();
                $cache->set('metadata', $metadata);
            }
        }

        // Merge with any metadata added specific to this page which was added manually.
        $this->groups['moodle']['modules'] = array_merge($this->groups['moodle']['modules'],
                $metadata);
    }

    /**
     * Determine the module metadata for all moodle YUI modules.
     *
     * This works through all modules capable of serving YUI modules, and attempts to get
     * metadata for each of those modules.
     *
     * @return Array of module metadata
     */
    private function get_moodle_metadata() {
        $moodlemodules = array();
        // Core isn't a plugin type or subsystem - handle it seperately.
        if ($module = $this->get_moodle_path_metadata(core_component::get_component_directory('core'))) {
            $moodlemodules = array_merge($moodlemodules, $module);
        }

        // Handle other core subsystems.
        $subsystems = core_component::get_core_subsystems();
        foreach ($subsystems as $subsystem => $path) {
            if (is_null($path)) {
                continue;
            }
            if ($module = $this->get_moodle_path_metadata($path)) {
                $moodlemodules = array_merge($moodlemodules, $module);
            }
        }

        // And finally the plugins.
        $plugintypes = core_component::get_plugin_types();
        foreach ($plugintypes as $plugintype => $pathroot) {
            $pluginlist = core_component::get_plugin_list($plugintype);
            foreach ($pluginlist as $plugin => $path) {
                if ($module = $this->get_moodle_path_metadata($path)) {
                    $moodlemodules = array_merge($moodlemodules, $module);
                }
            }
        }

        return $moodlemodules;
    }

    /**
     * Helper function process and return the YUI metadata for all of the modules under the specified path.
     *
     * @param String $path the UNC path to the YUI src directory.
     * @return Array the complete array for frankenstyle directory.
     */
    private function get_moodle_path_metadata($path) {
        // Add module metadata is stored in frankenstyle_modname/yui/src/yui_modname/meta/yui_modname.json.
        $baseyui = $path . '/yui/src';
        $modules = array();
        if (is_dir($baseyui)) {
            $items = new DirectoryIterator($baseyui);
            foreach ($items as $item) {
                if ($item->isDot() or !$item->isDir()) {
                    continue;
                }
                $metafile = realpath($baseyui . '/' . $item . '/meta/' . $item . '.json');
                if (!is_readable($metafile)) {
                    continue;
                }
                $metadata = file_get_contents($metafile);
                $modules = array_merge($modules, (array) json_decode($metadata));
            }
        }
        return $modules;
    }

    /**
     * Define YUI modules which we have been required to patch between releases.
     *
     * We must do this because we aggressively cache content on the browser, and we must also override use of the
     * external CDN which will serve the true authoritative copy of the code without our patches.
     *
     * @param String combobase The local combobase
     * @param String yuiversion The current YUI version
     * @param Int patchlevel The patch level we're working to for YUI
     * @param Array patchedmodules An array containing the names of the patched modules
     * @return void
     */
    public function define_patched_core_modules($combobase, $yuiversion, $patchlevel, $patchedmodules) {
        // The version we use is suffixed with a patchlevel so that we can get additional revisions between YUI releases.
        $subversion = $yuiversion . '_' . $patchlevel;

        if ($this->comboBase == $combobase) {
            // If we are using the local combobase in the loader, we can add a group and still make use of the combo
            // loader. We just need to specify a different root which includes a slightly different YUI version number
            // to include our patchlevel.
            $patterns = array();
            $modules = array();
            foreach ($patchedmodules as $modulename) {
                // We must define the pattern and module here so that the loader uses our group configuration instead of
                // the standard module definition. We may lose some metadata provided by upstream but this will be
                // loaded when the module is loaded anyway.
                $patterns[$modulename] = array(
                    'group' => 'yui-patched',
                );
                $modules[$modulename] = array();
            }

            // Actually add the patch group here.
            $this->add_group('yui-patched', array(
                'combine' => true,
                'root' => $subversion . '/',
                'patterns' => $patterns,
                'modules' => $modules,
            ));

        } else {
            // The CDN is in use - we need to instead use the local combobase for this module and override the modules
            // definition. We cannot use the local base - we must use the combobase because we cannot invalidate the
            // local base in browser caches.
            $fullpathbase = $combobase . $subversion . '/';
            foreach ($patchedmodules as $modulename) {
                $this->modules[$modulename] = array(
                    'fullpath' => $fullpathbase . $modulename . '/' . $modulename . '-min.js'
                );
            }
        }
    }
}

/**
 * Invalidate all server and client side JS caches.
 */
function js_reset_all_caches() {
    global $CFG;

    $next = time();
    if (isset($CFG->jsrev) and $next <= $CFG->jsrev and $CFG->jsrev - $next < 60*60) {
        // This resolves problems when reset is requested repeatedly within 1s,
        // the < 1h condition prevents accidental switching to future dates
        // because we might not recover from it.
        $next = $CFG->jsrev+1;
    }

    set_config('jsrev', $next);
}

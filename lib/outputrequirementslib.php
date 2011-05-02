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
 * @package    core
 * @subpackage lib
 * @copyright  2009 Tim Hunt, 2010 Petr Skoda
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// note: you can find history of this file in lib/ajax/ajaxlib.php

/**
 * This class tracks all the things that are needed by the current page.
 *
 * Normally, the only instance of this  class you will need to work with is the
 * one accessible via $PAGE->requires.
 *
 * Typical usage would be
 * <pre>
 *     $PAGE->requires->js_init_call('M.mod_forum.init_view');
 * </pre>
 *
 * It also supports obsoleted coding style withouth YUI3 modules.
 * <pre>
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
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class page_requirements_manager {
    /** List of string available from JS */
    protected $stringsforjs = array();
    /** List of JS variables to be initialised */
    protected $jsinitvariables = array('head'=>array(), 'footer'=>array());
    /** Included JS scripts */
    protected $jsincludes = array('head'=>array(), 'footer'=>array());
    /** List of needed function calls */
    protected $jscalls = array('normal'=>array(), 'ondomready'=>array());
    /**
     * List of skip links, those are needed for accessibility reasons
     * @var array
     */
    protected $skiplinks = array();
    /**
     * Javascript code used for initialisation of page, it should be relatively small
     * @var array
     */
    protected $jsinitcode = array();
    /**
     * Theme sheets, initialised only from core_renderer
     * @var array of moodle_url
     */
    protected $cssthemeurls = array();
    /**
     * List of custom theme sheets, these are strongly discouraged!
     * Useful mostly only for CSS submitted by teachers that is not part of the theme.
     * @var array of moodle_url
     */
    protected $cssurls = array();
    /**
     * List of requested event handlers
     * @var array
     */
    protected $eventhandlers = array();
    /**
     * Extra modules
     * @var array
     */
    protected $extramodules = array();
    /** Flag indicated head stuff already printed */
    protected $headdone = false;
    /** Flag indicating top of body already printed */
    protected $topofbodydone = false;

    /** YUI PHPLoader instance responsible for YUI2 loading from PHP only */
    protected $yui2loader;
    /** YUI PHPLoader instance responsible for YUI3 loading from PHP only */
    protected $yui3loader;
    /** YUI PHPLoader instance responsible for YUI3 loading from javascript */
    protected $M_yui_loader;
    /** some config vars exposed in JS, please no secret stuff there */
    protected $M_cfg;
    /** stores debug backtraces from when JS modules were included in the page */
    protected $debug_moduleloadstacktraces = array();

    /**
     * Page requirements constructor.
     */
    public function __construct() {
        global $CFG;

        require_once("$CFG->libdir/yui/phploader/phploader/loader.php");

        $this->yui3loader = new YAHOO_util_Loader($CFG->yui3version);
        $this->yui2loader = new YAHOO_util_Loader($CFG->yui2version);

        // set up some loader options
        if (debugging('', DEBUG_DEVELOPER)) {
            $this->yui3loader->filter = YUI_DEBUG; // alternatively we could use just YUI_RAW here
            $this->yui2loader->filter = YUI_DEBUG; // alternatively we could use just YUI_RAW here
        } else {
            $this->yui3loader->filter = null;
            $this->yui2loader->filter = null;
        }
        if (!empty($CFG->useexternalyui)) {
            $this->yui3loader->base = 'http://yui.yahooapis.com/' . $CFG->yui3version . '/build/';
            $this->yui2loader->base = 'http://yui.yahooapis.com/' . $CFG->yui2version . '/build/';
            $this->yui3loader->comboBase = 'http://yui.yahooapis.com/combo?';
            $this->yui2loader->comboBase = 'http://yui.yahooapis.com/combo?';
        } else {
            $this->yui3loader->base = $CFG->httpswwwroot . '/lib/yui/'. $CFG->yui3version . '/build/';
            $this->yui2loader->base = $CFG->httpswwwroot . '/lib/yui/'. $CFG->yui2version . '/build/';
            $this->yui3loader->comboBase = $CFG->httpswwwroot . '/theme/yui_combo.php?';
            $this->yui2loader->comboBase = $CFG->httpswwwroot . '/theme/yui_combo.php?';
        }

        // enable combo loader? this significantly helps with caching and performance!
        $this->yui3loader->combine = !empty($CFG->yuicomboloading);
        $this->yui2loader->combine = !empty($CFG->yuicomboloading);

        if (empty($CFG->cachejs)) {
            $jsrev = -1;
        } else if (empty($CFG->jsrev)) {
            $jsrev = 1;
        } else {
            $jsrev = $CFG->jsrev;
        }

        // set up JS YUI loader helper object
        $this->M_yui_loader = new stdClass();
        $this->M_yui_loader->base         = $this->yui3loader->base;
        $this->M_yui_loader->comboBase    = $this->yui3loader->comboBase;
        $this->M_yui_loader->combine      = $this->yui3loader->combine;
        $this->M_yui_loader->filter       = ($this->yui3loader->filter == YUI_DEBUG) ? 'debug' : '';
        $this->M_yui_loader->insertBefore = 'firstthemesheet';
        $this->M_yui_loader->modules      = array();
        $this->M_yui_loader->groups       = array(
            'moodle' => array(
                'name' => 'moodle',
                'base' => $CFG->httpswwwroot . '/theme/yui_combo.php?moodle/'.$jsrev.'/',
                'comboBase' => $CFG->httpswwwroot . '/theme/yui_combo.php?',
                'combine' => $this->yui3loader->combine,
                'filter' => '',
                'ext' => false,
                'root' => 'moodle/'.$jsrev.'/', // Add the rev to the root path so that we can control caching
                'patterns' => array(
                    'moodle-' => array(
                        'group' => 'moodle',
                        'configFn' => '@MOODLECONFIGFN@'
                    ),
                    'root' => 'moodle'
                )
            ),
            'local' => array(
                'name' => 'gallery',
                'base' => $CFG->wwwroot.'/lib/yui/gallery/',
                'comboBase' => $CFG->httpswwwroot . '/theme/yui_combo.php?',
                'combine' => $this->yui3loader->combine,
                'filter' => $this->M_yui_loader->filter,
                'ext' => false,
                'root' => 'gallery/',
                'patterns' => array(
                    'gallery-' => array(
                        'group' => 'gallery',
                        'configFn' => '@GALLERYCONFIGFN@',
                    ),
                    'root' => 'gallery'
                )
            )
        );
        $this->add_yui2_modules(); // adds loading info for all YUI2 modules
        $this->js_module($this->find_module('core_filepicker'));
        $this->js_module($this->find_module('core_dock'));

        // YUI3 init code
        $libs = array('cssreset', 'cssbase', 'cssfonts', 'cssgrids', 'node', 'loader'); // full CSS reset + basic libs
        foreach ($libs as $lib) {
            $this->yui3loader->load($lib);
        }
    }

    /**
     * This method adds yui2 modules into the yui3 JS loader-
     * @return void
     */
    protected function add_yui2_modules() {
        //note: this function is definitely not perfect, because
        //      it adds tons of markup into each page, but it can be
        //      abstracted into separate JS file with proper headers
        global $CFG;

        $GLOBALS['yui_current'] = array();
        require($CFG->libdir.'/yui/phploader/lib/meta/config_'.$CFG->yui2version.'.php');
        $info = $GLOBALS['yui_current'];
        unset($GLOBALS['yui_current']);

        if (empty($CFG->yuicomboloading)) {
            $urlbase = $this->yui2loader->base;
        } else {
            $urlbase = $this->yui2loader->comboBase.$CFG->yui2version.'/build/';
        }

        $modules = array();
        $ignored = array(); // list of CSS modules that are not needed
        foreach ($info['moduleInfo'] as $name => $module) {
            if ($module['type'] === 'css') {
                $ignored[$name] = true;
            } else {
                $modules['yui2-'.$name] = $module;
            }
        }
        foreach ($modules as $name=>$module) {
            $module['fullpath'] = $urlbase.$module['path']; // fix path to point to correct location
            unset($module['path']);
            unset($module['skinnable']); // we load all YUI2 css automatically, this prevents weird missing css loader problems
            foreach(array('requires', 'optional', 'supersedes') as $fixme) {
                if (!empty($module[$fixme])) {
                    $fixed = false;
                    foreach ($module[$fixme] as $key=>$dep) {
                        if (isset($ignored[$dep])) {
                            unset($module[$fixme][$key]);
                            $fixed = true;
                        } else {
                            $module[$fixme][$key] = 'yui2-'.$dep;
                        }
                    }
                    if ($fixed) {
                        $module[$fixme] = array_merge($module[$fixme]); // fix keys
                    }
                }
            }
            $this->M_yui_loader->modules[$name] = $module;
            if (debugging('', DEBUG_DEVELOPER)) {
                if (!array_key_exists($name, $this->debug_moduleloadstacktraces)) {
                    $this->debug_moduleloadstacktraces[$name] = array();
                }
                $this->debug_moduleloadstacktraces[$name][] = format_backtrace(debug_backtrace());
            }
        }
    }

    /**
     * Initialise with the bits of JavaScript that every Moodle page should have.
     *
     * @param moodle_page $page
     * @param core_renderer $output
     */
    protected function init_requirements_data(moodle_page $page, core_renderer $renderer) {
        global $CFG;

        // JavaScript should always work with $CFG->httpswwwroot rather than $CFG->wwwroot.
        // Otherwise, in some situations, users will get warnings about insecure content
        // on secure pages from their web browser.

        $this->M_cfg = array(
            'wwwroot'             => $CFG->httpswwwroot, // Yes, really. See above.
            'sesskey'             => sesskey(),
            'loadingicon'         => $renderer->pix_url('i/loading_small', 'moodle')->out(false),
            'themerev'            => theme_get_revision(),
            'theme'               => $page->theme->name,
            'jsrev'               => ((empty($CFG->cachejs) or empty($CFG->jsrev)) ? -1 : $CFG->jsrev),
        );
        if (debugging('', DEBUG_DEVELOPER)) {
            $this->M_cfg['developerdebug'] = true;
            $this->yui2_lib('logger');
        }

        // accessibility stuff
        $this->skip_link_to('maincontent', get_string('tocontent', 'access'));

        // to be removed soon
        $this->yui2_lib('dom');        // at least javascript-static.js needs to be migrated to YUI3

        $this->string_for_js('confirmation', 'admin');
        $this->string_for_js('cancel', 'moodle');
        $this->string_for_js('yes', 'moodle');

        if ($page->pagelayout === 'frametop') {
            $this->js_init_call('M.util.init_frametop');
        }
    }

    /**
     * Ensure that the specified JavaScript file is linked to from this page.
     *
     * NOTE: This function is to be used in rare cases only, please store your JS in module.js file
     * and use $PAGE->requires->js_init_call() instead.
     *
     * By default the link is put at the end of the page, since this gives best page-load performance.
     *
     * Even if a particular script is requested more than once, it will only be linked
     * to once.
     *
     * @param string|moodle_url $url The path to the .js file, relative to $CFG->dirroot / $CFG->wwwroot.
     *      For example '/mod/mymod/customscripts.js'; use moodle_url for external scripts
     * @param bool $inhead initialise in head
     * @return void
     */
    public function js($url, $inhead=false) {
        $url = $this->js_fix_url($url);
        $where = $inhead ? 'head' : 'footer';
        $this->jsincludes[$where][$url->out()] = $url;
    }

    /**
     * Ensure that the specified YUI2 library file, and all its required dependencies,
     * are linked to from this page.
     *
     * By default the link is put at the end of the page, since this gives best page-load
     * performance. Optional dependencies are not loaded automatically - if you want
     * them you will need to load them first with other calls to this method.
     *
     * Even if a particular library is requested more than once (perhaps as a dependency
     * of other libraries) it will only be linked to once.
     *
     * The library is leaded as soon as possible, if $OUTPUT->header() not used yet it
     * is put into the page header, otherwise it is loaded in the page footer.
     *
     * @param string|array $libname the name of the YUI2 library you require. For example 'autocomplete'.
     * @return void
     */
    public function yui2_lib($libname) {
        $libnames = (array)$libname;
        foreach ($libnames as $lib) {
            $this->yui2loader->load($lib);
        }
    }

    /**
     * Returns the actual url through which a script is served.
     * @param moodle_url|string $url full moodle url, or shortened path to script
     * @return moodle_url
     */
    protected function js_fix_url($url) {
        global $CFG;

        if ($url instanceof moodle_url) {
            return $url;
        } else if (strpos($url, '/') === 0) {
            if (debugging()) {
                // check file existence only when in debug mode
                if (!file_exists($CFG->dirroot . strtok($url, '?'))) {
                    throw new coding_exception('Attempt to require a JavaScript file that does not exist.', $url);
                }
            }
            if (!empty($CFG->cachejs) and !empty($CFG->jsrev) and strpos($url, '/lib/editor/') !== 0 and substr($url, -3) === '.js') {
                return new moodle_url($CFG->httpswwwroot.'/lib/javascript.php', array('file'=>$url, 'rev'=>$CFG->jsrev));
            } else {
                return new moodle_url($CFG->httpswwwroot.$url);
            }
        } else {
            throw new coding_exception('Invalid JS url, it has to be shortened url starting with / or moodle_url instance.', $url);
        }
    }

    /**
     * Find out if JS module present and return details.
     * @param string $component name of component in frankenstyle, ex: core_group, mod_forum
     * @return array description of module or null if not found
     */
    protected function find_module($component) {
        global $CFG;

        $module = null;


        if (strpos($component, 'core_') === 0) {
            // must be some core stuff - list here is not complete, this is just the stuff used from multiple places
            // so that we do nto have to repeat the definition of these modules over and over again
            switch($component) {
                case 'core_filepicker':
                    $module = array('name'     => 'core_filepicker',
                                    'fullpath' => '/repository/filepicker.js',
                                    'requires' => array('base', 'node', 'node-event-simulate', 'json', 'async-queue', 'io', 'yui2-button', 'yui2-container', 'yui2-layout', 'yui2-menu', 'yui2-treeview', 'yui2-dragdrop', 'yui2-cookie'),
                                    'strings'  => array(array('add', 'repository'), array('back', 'repository'), array('cancel', 'moodle'), array('close', 'repository'),
                                                        array('cleancache', 'repository'), array('copying', 'repository'), array('date', 'repository'), array('downloadsucc', 'repository'),
                                                        array('emptylist', 'repository'), array('error', 'repository'), array('federatedsearch', 'repository'),
                                                        array('filenotnull', 'repository'), array('getfile', 'repository'), array('help', 'moodle'), array('iconview', 'repository'),
                                                        array('invalidjson', 'repository'), array('linkexternal', 'repository'), array('listview', 'repository'),
                                                        array('loading', 'repository'), array('login', 'repository'), array('logout', 'repository'), array('noenter', 'repository'),
                                                        array('noresult', 'repository'), array('manageurl', 'repository'), array('popup', 'repository'), array('preview', 'repository'),
                                                        array('refresh', 'repository'), array('save', 'repository'), array('saveas', 'repository'), array('saved', 'repository'),
                                                        array('saving', 'repository'), array('search', 'repository'), array('searching', 'repository'), array('size', 'repository'),
                                                        array('submit', 'repository'), array('sync', 'repository'), array('title', 'repository'), array('upload', 'repository'),
                                                        array('uploading', 'repository'), array('xhtmlerror', 'repository'),
                                                        array('cancel'), array('chooselicense', 'repository'), array('author', 'repository'),
                                                        array('ok', 'moodle'), array('error', 'moodle'), array('info', 'moodle'), array('norepositoriesavailable', 'repository'), array('norepositoriesexternalavailable', 'repository'),
                                                        array('nofilesattached', 'repository'), array('filepicker', 'repository'),
                                                        array('nofilesavailable', 'repository'), array('overwrite', 'repository'),
                                                        array('renameto', 'repository'), array('fileexists', 'repository'),
                                                        array('fileexistsdialogheader', 'repository'), array('fileexistsdialog_editor', 'repository'),
                                                        array('fileexistsdialog_filemanager', 'repository')
                                                    ));
                    break;
                case 'core_comment':
                    $module = array('name'     => 'core_comment',
                                    'fullpath' => '/comment/comment.js',
                                    'requires' => array('base', 'io', 'node', 'json', 'yui2-animation', 'overlay'),
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
                case 'core_dock':
                    $module = array('name'     => 'core_dock',
                                    'fullpath' => '/blocks/dock.js',
                                    'requires' => array('base', 'node', 'event-custom', 'event-mouseenter', 'event-resize'),
                                    'strings' => array(array('addtodock', 'block'),array('undockitem', 'block'),array('undockall', 'block'),array('thisdirectionvertical', 'langconfig')));
                    break;
                case 'core_message':
                    $module = array('name'     => 'core_message',
                                    'fullpath' => '/message/module.js');
                    break;
                case 'core_flashdetect':
                    $module = array('name'     => 'core_flashdetect',
                                    'fullpath' => '/lib/flashdetect/flashdetect.js',
                                    'requires' => array('io'));
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
                                    'requires' => array('node', 'event', 'overlay', 'io', 'json'));
                    break;
                case 'core_filetree':
                    $module = array('name'     => 'core_filetree',
                                    'fullpath' => '/files/module.js',
                                    'requires' => array('node', 'event', 'overlay', 'io', 'json', 'yui2-treeview'));
                    break;
            }

        } else {
            if ($dir = get_component_directory($component)) {
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
     * The structure of module array is described at http://developer.yahoo.com/yui/3/yui/:
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

        // Don't load this module if we already have, no need to!
        if ($this->js_module_loaded($module['name'])) {
            if (debugging('', DEBUG_DEVELOPER)) {
                $this->debug_moduleloadstacktraces[$module['name']][] = format_backtrace(debug_backtrace());
            }
            return;
        }

        $module['fullpath'] = $this->js_fix_url($module['fullpath'])->out(false);
        // add all needed strings
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
            $this->M_yui_loader->modules[$module['name']] = $module;
        }
        if (debugging('', DEBUG_DEVELOPER)) {
            if (!array_key_exists($module['name'], $this->debug_moduleloadstacktraces)) {
                $this->debug_moduleloadstacktraces[$module['name']] = array();
            }
            $this->debug_moduleloadstacktraces[$module['name']][] = format_backtrace(debug_backtrace());
        }
    }

    /**
     * Returns true if the module has already been loaded.
     *
     * @param string|array $modulename
     * @return bool True if the module has already been loaded
     */
    protected function js_module_loaded($module) {
        if (is_string($module)) {
            $modulename = $module;
        } else {
            $modulename = $module['name'];
        }
        return array_key_exists($modulename, $this->M_yui_loader->modules) ||
               array_key_exists($modulename, $this->extramodules);
    }

    /**
     * Returns the stacktraces from loading js modules.
     * @return array
     */
    public function get_loaded_modules() {
        return $this->debug_moduleloadstacktraces;
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

        $this->cssurls[$stylesheet->out()] = $stylesheet; // overrides
    }

    /**
     * Add theme stylkesheet to page - do not use from plugin code,
     * this should be called only from the core renderer!
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
     * @param $target the name of anchor this link should go to. For example 'maincontent'.
     * @param $linktext The text to use for the skip link. Normally get_string('skipto', 'access', ...);
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
     * @param string $function the name of the JavaScritp function to call. Can
     *      be a compound name like 'Y.Event.purgeElement'. Can also be
     *      used to create and object by using a 'function name' like 'new user_selector'.
     * @param array $arguments and array of arguments to be passed to the function.
     *      When generating the function call, this will be escaped using json_encode,
     *      so passing objects and arrays should work.
     * @param bool $ondomready
     * @param int $delay
     * @return void
     */
    public function js_function_call($function, array $arguments = null, $ondomready = false, $delay = 0) {
        $where = $ondomready ? 'ondomready' : 'normal';
        $this->jscalls[$where][] = array($function, $arguments, $delay);
    }

    /**
     * Adds a call to make use of a YUI gallery module. DEPRECATED DO NOT USE!!!
     *
     * @deprecated DO NOT USE
     *
     * @param string|array $modules One or more gallery modules to require
     * @param string $version
     * @param string $function
     * @param array $arguments
     * @param bool $ondomready
     */
    public function js_gallery_module($modules, $version, $function, array $arguments = null, $ondomready = false) {
        global $CFG;
        debugging('This function will be removed before 2.0 is released please change it from js_gallery_module to yui_module', DEBUG_DEVELOPER);
        $this->yui_module($modules, $function, $arguments, $version, $ondomready);
    }

    /**
     * Creates a JavaScript function call that requires one or more modules to be loaded
     *
     * This function can be used to include all of the standard YUI module types within JavaScript:
     *     - YUI3 modules    [node, event, io]
     *     - YUI2 modules    [yui2-*]
     *     - Moodle modules  [moodle-*]
     *     - Gallery modules [gallery-*]
     *
     * @param array|string $modules One or more modules
     * @param string $function The function to call once modules have been loaded
     * @param array $arguments An array of arguments to pass to the function
     * @param string $galleryversion The gallery version to use
     * @param bool $ondomready
     */
    public function yui_module($modules, $function, array $arguments = null, $galleryversion = '2010.04.08-12-35', $ondomready = false) {
        global $CFG;

        if (!is_array($modules)) {
            $modules = array($modules);
        }
        if (empty($CFG->useexternalyui) || true) {
            // We need to set the M.yui.galleryversion to the correct version
            $jscode = 'M.yui.galleryversion='.json_encode($galleryversion).';';
        } else {
            // Set Y's config.gallery to the version
            $jscode = 'Y.config.gallery='.json_encode($galleryversion).';';
        }
        $jscode .= 'Y.use('.join(',', array_map('json_encode', $modules)).',function() {'.js_writer::function_call($function, $arguments).'})';
        if ($ondomready) {
            $jscode = "Y.on('domready', function() { $jscode });";
        }
        $this->jsinitcode[] = $jscode;
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
     * @return void
     */
    public function js_init_call($function, array $extraarguments = null, $ondomready = false, array $module = null) {
        $jscode = js_writer::function_call_with_Y($function, $extraarguments);
        if (!$module) {
            // detect module automatically
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
     * @param string $jscode
     * @param bool $ondomready wait for dom ready (helps with some IE problems when modifying DOM)
     * @param array $module JS module specification array
     * @return void
     */
    public function js_init_code($jscode, $ondomready = false, array $module = null) {
        $jscode = trim($jscode, " ;\n"). ';';

        if ($module) {
            $this->js_module($module);
            $modulename = $module['name'];
            $jscode = "Y.use('$modulename', function(Y) { $jscode });";
        }

        if ($ondomready) {
            $jscode = "Y.on('domready', function() { $jscode });";
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
     *     // require the string in PHP and replace the placeholder
     *     $PAGE->requires->string_for_js('fullnamedisplay', 'moodle', $USER);
     *     // use the result of the substitution in Javascript
     *     alert(M.str.moodle.fullnamedisplay);
     *
     * To substitute the placeholder at client side, use M.util.get_string()
     * function. It implements the same logic as {@see get_string()}:
     *
     *     // require the string in PHP but keep {$a} as it is
     *     $PAGE->requires->string_for_js('fullnamedisplay', 'moodle');
     *     // provide the values on the fly in Javascript
     *     user = { firstname : 'Harry', lastname : 'Potter' }
     *     alert(M.util.get_string('fullnamedisplay', 'moodle', user);
     *
     * If you do need the same string expanded with different $a values in PHP
     * on server side, then the solution is to put them in your own data structure
     * (e.g. and array) that you pass to JavaScript with {@link data_for_js()}.
     *
     * @param string $identifier the desired string.
     * @param string $module the language file to look in.
     * @param mixed $a any extra data to add into the string (optional).
     */
    public function string_for_js($identifier, $component, $a = NULL) {
        $string = get_string($identifier, $component, $a);
        if (!$component) {
            throw new coding_exception('The $module parameter is required for page_requirements_manager::string_for_js.');
        }
        if (isset($this->stringsforjs[$component][$identifier]) && $this->stringsforjs[$component][$identifier] !== $string) {
            throw new coding_exception("Attempt to re-define already required string '$identifier' " .
                    "from lang file '$component'. Did you already ask for it with a different \$a? {$this->stringsforjs[$component][$identifier]} !== $string");
        }
        $this->stringsforjs[$component][$identifier] = $string;
    }

    /**
     * Make an array of language strings available for JS
     *
     * This function calls the above function {@link string_for_js()} for each requested
     * string in the $identifiers array that is passed to the argument for a single module
     * passed in $module.
     *
     * <code>
     * $PAGE->strings_for_js(Array('one', 'two', 'three'), 'mymod', Array('a', null, 3));
     *
     * // The above is identitical to calling
     *
     * $PAGE->string_for_js('one', 'mymod', 'a');
     * $PAGE->string_for_js('two', 'mymod');
     * $PAGE->string_for_js('three', 'mymod', 3);
     * </code>
     *
     * @param array $identifiers An array of desired strings
     * @param string $component The module to load for
     * @param mixed $a This can either be a single variable that gets passed as extra
     *         information for every string or it can be an array of mixed data where the
     *         key for the data matches that of the identifier it is meant for.
     *
     */
    public function strings_for_js($identifiers, $component, $a=NULL) {
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
     * @param mixed $selector standard YUI selector for elemnts, may be array or string, element id is in the form "#idvalue"
     * @param string $event A valid DOM event (click, mousedown, change etc.)
     * @param string $function The name of the function to call
     * @param array  $arguments An optional array of argument parameters to pass to the function
     * @return void
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
     * @return bool $ondomready
     */
    protected function get_javascript_code($ondomready) {
        $where = $ondomready ? 'ondomready' : 'normal';
        $output = '';
        if ($this->jscalls[$where]) {
            foreach ($this->jscalls[$where] as $data) {
                $output .= js_writer::function_call($data[0], $data[1], $data[2]);
            }
            if (!empty($ondomready)) {
                $output = "    Y.on('domready', function() {\n$output\n    });";
            }
        }
        return $output;
    }

    /**
     * Returns js code to be executed when Y is available.
     * @return unknown_type
     */
    protected function get_javascript_init_code() {
        if (count($this->jsinitcode)) {
            return implode("\n", $this->jsinitcode) . "\n";
        }
        return '';
    }

    /**
     * Returns basic YUI3 JS loading code.
     * YUI3 is using autoloading of both CSS and JS code.
     *
     * Major benefit of this compared to standard js/csss loader is much improved
     * caching, better browser cache utilisation, much fewer http requests.
     *
     * @return string
     */
    protected function get_yui3lib_headcode() {
        $code = $this->yui3loader->tags();
        // unfortunately yui loader does not produce xhtml strict code, so let's fix it for now
        $code = str_replace('&amp;', '&', $code);
        $code = str_replace('&', '&amp;', $code);
        return $code;
    }

    /**
     * Returns basic YUI2 JS loading code.
     * It can be called manually at any time.
     * If called manually the result needs to be output using echo().
     *
     * Major benefit of this compared to standard js loader is much improved
     * caching, better browser cache utilisation, much fewer http requests.
     *
     * All YUI2 CSS is loaded automatically.
     *
     * @return string JS embedding code
     */
    public function get_yui2lib_code() {
        global $CFG;

        if ($this->headdone) {
            $code = $this->yui2loader->script();
        } else {
            $code = $this->yui2loader->script();
            if ($this->yui2loader->combine) {
                $skinurl = $this->yui2loader->comboBase . $CFG->yui2version . '/build/assets/skins/sam/skin.css';
            } else {
                $skinurl = $this->yui2loader->base . 'assets/skins/sam/skin.css';
            }
            // please note this is a temporary hack until we fully migrate to later YUI3 that has all the widgets
            $attributes = array('rel'=>'stylesheet', 'type'=>'text/css', 'href'=>$skinurl);
            $code .= "\n" . html_writer::empty_tag('link', $attributes) . "\n";
        }
        $code = str_replace('&amp;', '&', $code);
        $code = str_replace('&', '&amp;', $code);
        return $code;
    }

    /**
     * Returns html tags needed for inclusion of theme CSS
     * @return string
     */
    protected function get_css_code() {
        // First of all the theme CSS, then any custom CSS
        // Please note custom CSS is strongly discouraged,
        // because it can not be overridden by themes!
        // It is suitable only for things like mod/data which accepts CSS from teachers.
        $attributes = array('rel'=>'stylesheet', 'type'=>'text/css');

        // This line of code may look funny but it is currently required in order
        // to avoid MASSIVE display issues in Internet Explorer.
        // As of IE8 + YUI3.1.1 the reference stylesheet (firstthemesheet) gets
        // ignored whenever another resource is added until such time as a redraw
        // is forced, usually by moving the mouse over the affected element.
        $code = html_writer::tag('script', '/** Required in order to fix style inclusion problems in IE with YUI **/', array('id'=>'firstthemesheet', 'type'=>'text/css'));

        $urls = $this->cssthemeurls + $this->cssurls;
        foreach ($urls as $url) {
            $attributes['href'] = $url;
            $code .= html_writer::empty_tag('link', $attributes) . "\n";
            // this id is needed in first sheet only so that theme may override YUI sheets laoded on the fly
            unset($attributes['id']);
        }

        return $code;
    }

    /**
     * Adds extra modules specified after printing of page header
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
     * @return string the HTML code to to inside the <head> tag.
     */
    public function get_head_code(moodle_page $page, core_renderer $renderer) {
        global $CFG;

        // note: the $page and $output are not stored here because it would
        // create circular references in memory which prevents garbage collection
        $this->init_requirements_data($page, $renderer);

        // yui3 JS and CSS is always loaded first - it is cached in browser
        $output = $this->get_yui3lib_headcode();

        // BC: load basic YUI2 for now, all yui2 things should be loaded via Y.use('yui2-oldmodulename')
        $output .= $this->get_yui2lib_code();

        // now theme CSS + custom CSS in this specific order
        $output .= $this->get_css_code();

        // set up global YUI3 loader object - this should contain all code needed by plugins
        // note: in JavaScript just use "YUI(M.yui.loader).use('overlay', function(Y) { .... });"
        // this needs to be done before including any other script
        $js = "var M = {}; M.yui = {}; var moodleConfigFn = function(me) {var p = me.path, b = me.name.replace(/^moodle-/,'').split('-', 3), n = b.pop();if (/(skin|core)/.test(n)) {n = b.pop();me.type = 'css';};me.path = b.join('-')+'/'+n+'/'+n+'.'+me.type;}; var galleryConfigFn = function(me) {var p = me.path,v=M.yui.galleryversion,f;if(/-(skin|core)/.test(me.name)) {me.type = 'css';p = p.replace(/-(skin|core)/, '').replace(/\.js/, '.css').split('/'), f = p.pop().replace(/(\-(min|debug))/, '');if (/-skin/.test(me.name)) {p.splice(p.length,0,v,'assets','skins','sam', f);} else {p.splice(p.length,0,v,'assets', f);};} else {p = p.split('/'), f = p.pop();p.splice(p.length,0,v, f);};me.path = p.join('/');};\n";
        $js .= js_writer::set_variable('M.yui.loader', $this->M_yui_loader, false) . "\n";
        $js .= js_writer::set_variable('M.cfg', $this->M_cfg, false);
        $js = str_replace('"@GALLERYCONFIGFN@"', 'galleryConfigFn', $js);
        $js = str_replace('"@MOODLECONFIGFN@"', 'moodleConfigFn', $js);

        $output .= html_writer::script($js);

        // link our main JS file, all core stuff should be there
        $output .= html_writer::script('', $this->js_fix_url('/lib/javascript-static.js'));

        // add variables
        if ($this->jsinitvariables['head']) {
            $js = '';
            foreach ($this->jsinitvariables['head'] as $data) {
                list($var, $value) = $data;
                $js .= js_writer::set_variable($var, $value, true);
            }
            $output .= html_writer::script($js);
        }

        // all the other linked things from HEAD - there should be as few as possible
        if ($this->jsincludes['head']) {
            foreach ($this->jsincludes['head'] as $url) {
                $output .= html_writer::script('', $url);
            }
        }

        // mark head sending done, it is not possible to anything there
        $this->headdone = true;

        return $output;
    }

    /**
     * Generate any HTML that needs to go at the start of the <body> tag.
     *
     * Normally, this method is called automatically by the code that prints the
     * <head> tag. You should not normally need to call it in your own code.
     *
     * @return string the HTML code to go at the start of the <body> tag.
     */
    public function get_top_of_body_code() {
        // first the skip links
        $links = '';
        $attributes = array('class'=>'skip');
        foreach ($this->skiplinks as $url => $text) {
            $attributes['href'] = '#' . $url;
            $links .= html_writer::tag('a', $text, $attributes);
        }
        $output = html_writer::tag('div', $links, array('class'=>'skiplinks')) . "\n";

        // then the clever trick for hiding of things not needed when JS works
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
        // add other requested modules
        $output = $this->get_extra_modules_code();

        // add missing YUI2 YUI - to be removed once we convert everything to YUI3!
        $output .= $this->get_yui2lib_code();

        // all the other linked scripts - there should be as few as possible
        if ($this->jsincludes['footer']) {
            foreach ($this->jsincludes['footer'] as $url) {
                $output .= html_writer::script('', $url);
            }
        }

        // add all needed strings
        if (!empty($this->stringsforjs)) {
            $output .= html_writer::script(js_writer::set_variable('M.str', $this->stringsforjs));
        }

        // add variables
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

        // there is no global Y, make sure it is available in your scope
        $js = "YUI(M.yui.loader).use('node', function(Y) {\n{$inyuijs}{$ondomreadyjs}{$jsinit}{$handlersjs}\n});";

        $output .= html_writer::script($js);

        return $output;
    }

    /**
     * @return boolean Have we already output the code in the <head> tag?
     */
    public function is_head_done() {
        return $this->headdone;
    }

    /**
     * @return boolean Have we already output the code at the start of the <body> tag?
     */
    public function is_top_of_body_done() {
        return $this->topofbodydone;
    }
}

/**
 * Invalidate all server and client side JS caches.
 * @return void
 */
function js_reset_all_caches() {
    global $CFG;
    require_once("$CFG->libdir/filelib.php");

    set_config('jsrev', empty($CFG->jsrev) ? 1 : $CFG->jsrev+1);
    fulldelete("$CFG->dataroot/cache/js");
}


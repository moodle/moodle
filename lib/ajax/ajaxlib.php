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
 * @package   moodlecore
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


/**
 * This class tracks all the things that are needed by the current page.
 *
 * Normally, the only instance of this  class you will need to work with is the
 * one accessible via $PAGE->requires.
 *
 * Typical useage would be
 * <pre>
 *     $PAGE->requires->css('mod/mymod/userstyles.php?id='.$id); // not overriddable via themes!
 *     $PAGE->requires->js('mod/mymod/script.js');
 *     $PAGE->requires->js('mod/mymod/small_but_urgent.js')->in_head();
 *     $PAGE->requires->js_function_call('init_mymod', array($data))->on_dom_ready();
 * </pre>
 *
 * There are some natural restrictions on some methods. For example, {@link css()}
 * can only be called before the <head> tag is output. See the comments on the
 * individual methods for details.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class page_requirements_manager {
    const WHEN_IN_HEAD = 0;
    const WHEN_TOP_OF_BODY = 10;
    const WHEN_AT_END = 20;
    const WHEN_ON_DOM_READY = 30;

    protected $linkedrequirements = array();
    protected $stringsforjs = array();
    protected $requiredjscode = array();

    /**
     * Theme sheets, initialised only from core_renderer
     * @var array of moodle_url
     */
    protected $css_theme_urls = array();
    /**
     * List of custom theme sheets, these are strongly discouraged!
     * Useful mostly only for CSS submitted by teachers that is not part of the theme.
     * @var array of moodle_url
     */
    protected $css_urls = array();

    protected $variablesinitialised = array('mstr' => 1); // 'mstr' is special. See string_for_js.

    protected $headdone = false;
    protected $topofbodydone = false;

    /** YUI PHPLoader instance responsible for YUI2 loading from PHP only */
    protected $yui2loader;
    /** YUI PHPLoader instance responsible for YUI3 loading from PHP only */
    protected $yui3loader;
    /** YUI PHPLoader instance responsible for YUI3 loading from javascript */
    protected $json_yui3loader;

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

        // set up JS YUI loader helper object
        $this->json_yui3loader = new stdClass();
        $this->json_yui3loader->base         = $this->yui3loader->base;
        $this->json_yui3loader->comboBase    = $this->yui3loader->comboBase;
        $this->json_yui3loader->combine      = $this->yui3loader->combine;
        $this->json_yui3loader->filter       = ($this->yui3loader->filter == YUI_DEBUG) ? 'debug' : '';
        $this->json_yui3loader->insertBefore = 'firstthemesheet';
        $this->json_yui3loader->modules      = array();
        $this->add_yui2_modules(); // adds loading info for YUI2
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
            $this->json_yui3loader->modules[$name] = $module;
        }
    }

    /**
     * Initialise with the bits of JavaScript that every Moodle page should have.
     *
     * @param moodle_page $page
     * @param core_renderer $output
     */
    protected function setup_core_javascript(moodle_page $page, core_renderer $output) {
        global $CFG;

        // JavaScript should always work with $CFG->httpswwwroot rather than $CFG->wwwroot.
        // Otherwise, in some situations, users will get warnings about insecure content
        // on sercure pages from their web browser.

        //TODO: problem here is we may need this in some included JS - move this somehow to the very beginning
        //      right after the YUI loading
        $config = array(
            'wwwroot'             => $CFG->httpswwwroot, // Yes, really. See above.
            'sesskey'             => sesskey(),
            'loadingicon'         => $output->pix_url('i/loading_small', 'moodle')->out(false),
            'themerev'            => theme_get_revision(),
            'theme'               => $page->theme->name,
            'yui2loaderBase'      => $this->yui2loader->base,
            'yui3loaderBase'      => $this->yui3loader->base,
            'yui2loaderComboBase' => $this->yui2loader->comboBase,
            'yui3loaderComboBase' => $this->yui3loader->comboBase,
            'yuicombine'          => (int)$this->yui3loader->combine,
            'yuifilter'           => debugging('', DEBUG_DEVELOPER) ? 'debug' : '',

        );
        if (debugging('', DEBUG_DEVELOPER)) {
            $config['developerdebug'] = true;
        }
        $this->data_for_js('moodle_cfg', $config)->in_head();

        if (debugging('', DEBUG_DEVELOPER)) {
            $this->yui2_lib('logger');
        }

        // YUI3 init code
        $this->yui3_lib(array('cssreset', 'cssbase', 'cssfonts', 'cssgrids')); // full CSS reset
        $this->yui3_lib(array('yui', 'loader')); // allows autoloading of everything else


        $this->skip_link_to('maincontent', get_string('tocontent', 'access'));

        // Note that, as a short-cut, the code
        // $js = "document.body.className += ' jsenabled';\n";
        // is hard-coded in {@link page_requirements_manager::get_top_of_body_code)
        $this->yui2_lib('dom');        // needs to be migrated to YUI3 before we release 2.0
        $this->yui2_lib('container');  // needs to be migrated to YUI3 before we release 2.0
        $this->yui2_lib('connection'); // needs to be migrated to YUI3 before we release 2.0
        // File Picker use this module loading YUI2 widgets
        $this->yui2_lib('yuiloader');  // needs to be migrated to YUI3 before we release 2.0
        $this->js_module('filepicker'); // should be migrated elsewhere before 2.0

        $this->string_for_js('confirmation', 'admin');
        $this->string_for_js('cancel', 'moodle');
        $this->string_for_js('yes', 'moodle');
        $this->js_function_call('init_help_icons');
    }

    /**
     * Ensure that the specified JavaScript file is linked to from this page.
     *
     * By default the link is put at the end of the page, since this gives best page-load performance.
     *
     * Even if a particular script is requested more than once, it will only be linked
     * to once.
     *
     * @param $jsfile The path to the .js file, relative to $CFG->dirroot / $CFG->wwwroot.
     *      No leading slash. For example '/mod/mymod/customscripts.js';
     * @param boolean $fullurl This parameter is intended for internal use only.
     *      However, in exceptional circumstances you may wish to use it to link
     *      to JavaScript on another server. For example, lib/recaptchalib.php has to
     *      do this. This really should only be done in exceptional circumstances. This
     *      may change in the future without warning.
     *      (If true, $jsfile is treaded as a full URL, not relative $CFG->wwwroot.)
     * @return required_js The required_js object. This allows you to control when the
     *      link to the script is output by calling methods like {@link required_js::asap()} or
     *      {@link required_js::in_head()}.
     */
    public function js($jsfile, $fullurl = false) {
        global $CFG;
        if (!$fullurl) {
            $jsfile = ltrim($jsfile, '/'); // for now until we change all js urls to start with '/' like the rest of urls
            // strtok is used to trim off any GET string arguments before looking for the file
            if (!file_exists($CFG->dirroot . '/' . strtok($jsfile, '?'))) {
                throw new coding_exception('Attept to require a JavaScript file that does not exist.', $jsfile);
            }
            $url = $CFG->httpswwwroot . '/' . $jsfile;
        } else {
            $url = $jsfile;
        }
        if (!isset($this->linkedrequirements[$url])) {
            $this->linkedrequirements[$url] = new required_js($this, $url);
        }
        return $this->linkedrequirements[$url];
    }

    /**
     * Ensure that the specified YUI2 library file, and all its required dependancies,
     * are linked to from this page.
     *
     * By default the link is put at the end of the page, since this gives best page-load
     * performance. Optional dependencies are not loaded automatically - if you want
     * them you will need to load them first with other calls to this method.
     *
     * Even if a particular library is requested more than once (perhaps as a dependancy
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
     * Ensure that the specified YUI3 library file, and all its required dependancies,
     * are loaded automatically on this page.
     *
     * @param string|array $libname the name of the YUI3 library you require. For example 'overlay'.
     * @return void
     */
    public function yui3_lib($libname) {
        if ($this->headdone) {
            throw new coding_exception('YUI3 libraries can be preloaded by PHP only from HEAD, please use YUI autoloading instead: ', $libname);
        }
        $libnames = (array)$libname;
        foreach ($libnames as $lib) {
            $this->yui3loader->load($lib);
        }
    }

    /**
     * Append YUI3 module to default YUI3 JS loader.
     * The structure of module array is described at http://developer.yahoo.com/yui/3/yui/:
     * @param string $name unique module name based of full plugin name
     * @param array $module usually the module details may be detected from the $name
     * @return void
     */
    public function js_module($name, array $module = null) {
        global $CFG;

        if (strpos($name, 'core_') === 0) {
            // must be some core stuff
        } else {
            if ($name === 'filepicker') { // TODO: rename to 'core_filepicker' and move above
                $pathtofilepicker = $CFG->httpswwwroot.'/repository/filepicker.js';
                $module = array('fullpath'=>$pathtofilepicker, 'requires' => array('base', 'node', 'json', 'async-queue', 'io'));
            }
            //TODO: look for plugin info?
        }

        if ($module === null) {
            throw new coding_exception('Missing YUI3 module details.');
        }

        $this->json_yui3loader->modules[$name] = $module;
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
     * Please note sue of this feature is strongly discouraged,
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

        $this->css_urls[$stylesheet->out()] = $stylesheet; // overrides
    }

    /**
     * Add theme stylkesheet to page - do not use from plugin code,
     * this should be called only from the core renderer!
     * @param moodle_url $stylesheet
     * @return void
     */
    public function css_theme(moodle_url $stylesheet) {
        $this->css_theme_urls[] = $stylesheet;
    }

    /**
     * Ensure that a skip link to a given target is printed at the top of the <body>.
     *
     * You must call this function before {@link get_top_of_body_code()}, (if not, an exception
     * will be thrown). That normally means you must call this before the call to print_header.
     *
     * If you ask for a particular skip link to be printed, it is then your responsibility
     * to ensure that the appropraite <a name="..."> tag is printed in the body of the
     * page, so that the skip link goes somewhere.
     *
     * Even if a particular skip link is requested more than once, only one copy of it will be output.
     *
     * @param $target the name of anchor this link should go to. For example 'maincontent'.
     * @param $linktext The text to use for the skip link. Normally get_string('skipto', 'access', ...);
     */
    public function skip_link_to($target, $linktext) {
        if (!isset($this->linkedrequirements[$target])) {
            $this->linkedrequirements[$target] = new required_skip_link($this, $target, $linktext);
        }
    }

    /**
     * Ensure that the specified JavaScript function is called from an inline script
     * somewhere on this page.
     *
     * By default the call will be put in a script tag at the
     * end of the page, since this gives best page-load performance.
     *
     * If you request that a particular function is called several times, then
     * that is what will happen (unlike linking to a CSS or JS file, where only
     * one link will be output).
     *
     * @param string $function the name of the JavaScritp function to call. Can
     *      be a compound name like 'YAHOO.util.Event.addListener'. Can also be
     *      used to create and object by using a 'function name' like 'new user_selector'.
     * @param array $arguments and array of arguments to be passed to the function.
     *      When generating the function call, this will be escaped using json_encode,
     *      so passing objects and arrays should work.
     * @return required_js_function_call The required_js_function_call object.
     *      This allows you to control when the link to the script is output by
     *      calling methods like {@link required_js_function_call::in_head()},
     *      {@link required_js_function_call::at_top_of_body()},
     *      {@link required_js_function_call::on_dom_ready()} or
     *      {@link required_js_function_call::after_delay()} methods.
     */
    public function js_function_call($function, $arguments = array()) {
        $requirement = new required_js_function_call($this, $function, $arguments);
        $this->requiredjscode[] = $requirement;
        return $requirement;
    }

    /**
     * Make a language string available to JavaScript.
     *
     * All the strings will be available in a mstr object in the global namespace.
     * So, for example, after a call to $PAGE->requires->string_for_js('course', 'moodle');
     * then the JavaScript variable mstr.moodle.course will be 'Course', or the
     * equivalent in the current language.
     *
     * The arguments to this function are just like the arguments to get_string
     * except that $module is not optional, and there are limitations on how you
     * use $a. Because each string is only stored once in the JavaScript (based
     * on $identifier and $module) you cannot get the same string with two different
     * values of $a. If you try, an exception will be thrown.
     *
     * If you do need the same string expanded with different $a values, then
     * the solution is to put them in your own data structure (e.g. and array)
     * that you pass to JavaScript with {@link data_for_js()}.
     *
     * @param string $identifier the desired string.
     * @param string $module the language file to look in.
     * @param mixed $a any extra data to add into the string (optional).
     */
    public function string_for_js($identifier, $module, $a = NULL) {
        $string = get_string($identifier, $module, $a);
        if (!$module) {
            throw new coding_exception('The $module parameter is required for page_requirements_manager::string_for_js.');
        }
        if (isset($this->stringsforjs[$module][$identifier]) && $this->stringsforjs[$module][$identifier] != $string) {
            throw new coding_exception("Attempt to re-define already required string '$identifier' " .
                    "from lang file '$module'. Did you already ask for it with a different \$a?");
        }
        $this->stringsforjs[$module][$identifier] = $string;
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
     * // The above is identifical to calling
     *
     * $PAGE->string_for_js('one', 'mymod', 'a');
     * $PAGE->string_for_js('two', 'mymod');
     * $PAGE->string_for_js('three', 'mymod', 3);
     * </code>
     *
     * @param array $identifiers An array of desired strings
     * @param string $module The module to load for
     * @param mixed $a This can either be a single variable that gets passed as extra
     *         information for every string or it can be an array of mixed data where the
     *         key for the data matches that of the identifier it is meant for.
     *
     */
    public function strings_for_js($identifiers, $module, $a=NULL) {
        foreach ($identifiers as $key => $identifier) {
            if (is_array($a) && array_key_exists($key, $a)) {
                $extra = $a[$key];
            } else {
                $extra = $a;
            }
            $this->string_for_js($identifier, $module, $extra);
        }
    }

    /**
     * Make some data from PHP available to JavaScript code.
     *
     * For example, if you call
     * <pre>
     *      $PAGE->requires->data_for_js('mydata', array('name' => 'Moodle'));
     * </pre>
     * then in JavsScript mydata.name will be 'Moodle'.
     *
     * You cannot call this function more than once with the same variable name
     * (if you try, it will throw an exception). Your code should prepare all the
     * date you want, and then pass it to this method. There is no way to change
     * the value associated with a particular variable later.
     *
     * @param string $variable the the name of the JavaScript variable to assign the data to.
     *      Will probably work if you use a compound name like 'mybuttons.button[1]', but this
     *      should be considered an experimental feature.
     * @param mixed $data The data to pass to JavaScript. This will be escaped using json_encode,
     *      so passing objects and arrays should work.
     * @return required_data_for_js The required_data_for_js object.
     *      This allows you to control when the link to the script is output by
     *      calling methods like {@link required_data_for_js::asap()},
     *      {@link required_data_for_js::in_head()} or
     *      {@link required_data_for_js::at_top_of_body()} methods.
     */
    public function data_for_js($variable, $data) {
        if (isset($this->variablesinitialised[$variable])) {
            throw new coding_exception("A variable called '" . $variable .
                    "' has already been passed ot JavaScript. You cannot overwrite it.");
        }
        $requirement = new required_data_for_js($this, $variable, $data);
        $this->requiredjscode[] = $requirement;
        $this->variablesinitialised[$variable] = 1;
        return $requirement;
    }

    /**
     * Creates a YUI event handler.
     *
     * @param string $id The id of the DOM element that will be listening for the event
     * @param string $event A valid DOM event (click, mousedown, change etc.)
     * @param string $function The name of the function to call
     * @param array  $arguments An optional array of argument parameters to pass to the function
     * @return required_event_handler The event_handler object
     */
    public function event_handler($id, $event, $function, $arguments=array()) {
        $requirement = new required_event_handler($this, $id, $event, $function, $arguments);
        $this->requiredjscode[] = $requirement;
        $this->yui2_lib('event');
        return $requirement;
    }

    /**
     * Get the code for the linked resources that need to appear in a particular place.
     * @param $when one of the WHEN_... constants.
     * @return string the HTML that should be output in that place.
     */
    protected function get_linked_resources_code($when) {
        $output = '';
        foreach ($this->linkedrequirements as $requirement) {
            if (!$requirement->is_done() && $requirement->get_when() == $when) {
                $output .= $requirement->get_html();
                $requirement->mark_done();
            }
        }
        return $output;
    }

    /**
     * Get the inline JavaScript code that need to appear in a particular place.
     * @param $when one of the WHEN_... constants.
     * @return string the javascript that should be output in that place.
     */
    protected function get_javascript_code($when, $indent = '') {
        $output = '';
        foreach ($this->requiredjscode as $requirement) {
            if (!$requirement->is_done() && $requirement->get_when() == $when) {
                $output .= $indent . $requirement->get_js_code();
                $requirement->mark_done();
            }
        }
        return $output;
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
        $code = $this->yui3loader->css() . $this->yui3loader->script();
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
            $code = $this->yui2loader->script_embed();
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

        $code = '';
        $attributes = array('id'=>'firstthemesheet', 'rel'=>'stylesheet', 'type'=>'text/css');

        $urls = $this->css_theme_urls + $this->css_urls;

        foreach ($urls as $url) {
            $attributes['href'] = $url;
            $code .= html_writer::empty_tag('link', $attributes) . "\n";
            // this id is needed in first sheet only so that theme may override YUI sheets laoded on the fly
            unset($attributes['id']);
        }

        return $code;
    }

    /**
     * Generate any HTML that needs to go inside the <head> tag.
     *
     * Normally, this method is called automatically by the code that prints the
     * <head> tag. You should not normally need to call it in your own code.
     *
     * @return string the HTML code to to inside the <head> tag.
     */
    public function get_head_code(moodle_page $page, core_renderer $output) {
        // note: the $page and $output are not stored here because it would
        // create circular references in memory which prevents garbage collection
        $this->setup_core_javascript($page, $output);
        // yui3 JS and CSS is always loaded first - it is cached in browser
        $output = $this->get_yui3lib_headcode();
        // BC: load basic YUI2 for now, yui2 things should be loaded as yui3 modules
        $output .= $this->get_yui2lib_code();
        // now theme CSS, it must be loaded before the
        $output .= $this->get_css_code();
        $output .= $this->get_linked_resources_code(self::WHEN_IN_HEAD);
        $js = $this->get_javascript_code(self::WHEN_IN_HEAD);
        $output .= ajax_generate_script_tag($js);
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
        $output = '<div class="skiplinks">' . $this->get_linked_resources_code(self::WHEN_TOP_OF_BODY) . '</div>';
        $js = "document.body.className += ' jsenabled';\n";
        $js .= $this->get_javascript_code(self::WHEN_TOP_OF_BODY);
        $output .= ajax_generate_script_tag($js);
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
        // add missing YUI2 YUI - to be removed once we convert everything to YUI3!
        $output = $this->get_yui2lib_code();

        // set up global YUI3 loader object - this should contain all code needed by plugins
        // note: in JavaScript just use "YUI(yui3loader).use('overlay', function(Y) { .... });"
        $output .= $this->data_for_js('yui3loader', $this->json_yui3loader)->now();

        // now print all the stuff that was added through ->requires
        $output .= $this->get_linked_resources_code(self::WHEN_AT_END);

        if (!empty($this->stringsforjs)) {
            array_unshift($this->requiredjscode, new required_data_for_js($this, 'mstr', $this->stringsforjs));
        }

        $js = $this->get_javascript_code(self::WHEN_AT_END);

        $ondomreadyjs = $this->get_javascript_code(self::WHEN_ON_DOM_READY, '    ');

//TODO: do we really need the global "Y" defined in javasecript-static.js?
//      The problem is that we can not rely on it to be fully initialised
        $js .= <<<EOD

    Y = YUI(yui3loader).use('node-base', function(Y) {
        Y.on('domready', function() {
            $ondomreadyjs
        });
        // TODO: call user js functions from here so that they have the proper initialised Y
    });
EOD;

        $output .= ajax_generate_script_tag($js);

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
 * This is the base class for all sorts of requirements. just to factor out some
 * common code.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
abstract class requirement_base {
    protected $manager;
    protected $when;
    protected $done = false;

    /**
     * Constructor. Normally the class and its subclasses should not be created
     * directly. Client code should create them via a page_requirements_manager
     * method like ->js(...).
     *
     * @param page_requirements_manager $manager the page_requirements_manager we are associated with.
     */
    protected function __construct(page_requirements_manager $manager) {
        $this->manager = $manager;
    }

    /**
     * Mark that this requirement has been satisfied (that is, that the HTML
     * returned by {@link get_html()} has been output.
     * @return boolean has this requirement been satisfied yet? That is, has
     *      that the HTML returned by {@link get_html()} has been output already.
     */
    public function is_done() {
        return $this->done;
    }

    /**
     * Mark that this requirement has been satisfied (that is, that the HTML
     * returned by {@link get_html()} has been output.
     */
    public function mark_done() {
        $this->done = true;
    }

    /**
     * Where on the page the HTML this requirement is meant to go.
     * @return integer One of the {@link page_requirements_manager}::WHEN_... constants.
     */
    public function get_when() {
        return $this->when;
    }
}

/**
 * This class represents something that must be output somewhere in the HTML.
 *
 * Examples include links to JavaScript or CSS files. However, it should not
 * necessarily be output immediately, we may have to wait for an appropriate time.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
abstract class linked_requirement extends requirement_base {
    protected $url;

    /**
     * Constructor. Normally the class and its subclasses should not be created
     * directly. Client code should create them via a page_requirements_manager
     * method like ->js(...).
     *
     * @param page_requirements_manager $manager the page_requirements_manager we are associated with.
     * @param string $url The URL of the thing we are linking to.
     */
    protected function __construct(page_requirements_manager $manager, $url) {
        parent::__construct($manager);
        $this->url = $url;
    }

    /**
     * @return string the HTML needed to satisfy this requirement.
     */
    abstract public function get_html();
}


/**
 * A subclass of {@link linked_requirement} to represent a requried JavaScript file.
 *
 * You should not create instances of this class directly. Instead you should
 * work with a {@link page_requirements_manager} - and probably the only
 * page_requirements_manager you will ever need is the one at $PAGE->requires.
 *
 * The methods {@link asap()}, {@link in_head()} and {@link at_top_of_body()}
 * are indented to be used as a fluid API, so you can say things like
 *     $PAGE->requires->js('mod/mymod/script.js')->in_head();
 *
 * However, by default JavaScript files are included at the end of the HTML.
 * This is recommended practice because it means that the web browser will only
 * start loading the javascript files after the rest of the page is loaded, and
 * that gives the best performance for users.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class required_js extends linked_requirement {
    /**
     * Constructor. Normally instances of this class should not be created
     * directly. Client code should create them via the page_requirements_manager
     * method {@link page_requirements_manager::js()}.
     *
     * @param page_requirements_manager $manager the page_requirements_manager we are associated with.
     * @param string $url The URL of the JavaScript file we are linking to.
     */
    public function __construct(page_requirements_manager $manager, $url) {
        parent::__construct($manager, $url);
        $this->when = page_requirements_manager::WHEN_AT_END;
    }

    public function get_html() {
        return ajax_get_link_to_script($this->url);
    }

    /**
     * Indicate that the link to this JavaScript file should be output as soon as
     * possible. That is, if this requirement has already been output, this method
     * does nothing. Otherwise, if the <head> tag has not yet been printed, the link
     * to this script will be put in <head>. Otherwise, this method returns a
     * fragment of HTML that the caller is responsible for outputting as soon as
     * possible. In fact, it is recommended that you only call this function from
     * an echo statement, like:
     * <pre>
     *     echo $PAGE->requires->js(...)->asap();
     * </pre>
     *
     * @return string The HTML required to include this JavaScript file. The caller
     * is responsible for outputting this HTML promptly.
     */
    public function asap() {
        if (!$this->manager->is_head_done()) {
            $this->in_head();
            return '';
        } else {
            return $this->now();
        }
    }

    /**
     * Return the required JavaScript immediately, so it can be included in some
     * HTML that is being built.
     *
     * This is not really recommeneded. But is necessary in some legacy code that
     * includes a .js files that does document.write.
     *
     * @return string The HTML for the script tag. The caller
     * is responsible for making sure it is output.
     */
    public function now() {
        if ($this->is_done()) {
            return '';
        }
        $output = $this->get_html();
        $this->mark_done();
        return $output;
    }

    /**
     * Indicate that the link to this JavaScript file should be output in the
     * <head> section of the HTML. If it too late for this request to be
     * satisfied, an exception is thrown.
     */
    public function in_head() {
        if ($this->is_done() || $this->when <= page_requirements_manager::WHEN_IN_HEAD) {
            return;
        }
        if ($this->manager->is_head_done()) {
            throw new coding_exception('Too late to ask for a JavaScript file to be linked to from &lt;head>.');
        }
        $this->when = page_requirements_manager::WHEN_IN_HEAD;
    }

    /**
     * Indicate that the link to this JavaScript file should be output at the top
     * of the <body> section of the HTML. If it too late for this request to be
     * satisfied, an exception is thrown.
     */
    public function at_top_of_body() {
        if ($this->is_done() || $this->when <= page_requirements_manager::WHEN_TOP_OF_BODY) {
            return;
        }
        if ($this->manager->is_top_of_body_done()) {
            throw new coding_exception('Too late to ask for a JavaScript file to be linked to from the top of &lt;body>.');
        }
        $this->when = page_requirements_manager::WHEN_TOP_OF_BODY;
    }
}


/**
 * A subclass of {@link linked_requirement} to represent a skip link.
 * A skip link is a concept from accessibility. You have some links like
 * 'Skip to main content' linking to an #maincontent anchor, at the start of the
 * <body> tag, so that users using assistive technologies like screen readers
 * can easily get to the main content without having to work their way through
 * any navigation, blocks, etc. that comes before it in the HTML.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class required_skip_link extends linked_requirement {
    protected $linktext;

    /**
     * Constructor. Normally instances of this class should not be created directly.
     * Client code should create them via the page_requirements_manager
     * method {@link page_requirements_manager::yui2_lib()}.
     *
     * @param page_requirements_manager $manager the page_requirements_manager we are associated with.
     * @param string $target the name of the anchor in the page we are linking to.
     * @param string $linktext the test to use for the link.
     */
    public function __construct(page_requirements_manager $manager, $target, $linktext) {
        parent::__construct($manager, $target);
        $this->when = page_requirements_manager::WHEN_TOP_OF_BODY;
        $this->linktext = $linktext;
    }

    public function get_html() {
        return '<a class="skip" href="#' . $this->url . '">' . $this->linktext . "</a>\n";
    }
}


/**
 * This is the base class for requirements that are JavaScript code.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
abstract class required_js_code extends requirement_base {

    /**
     * Constructor.
     * @param page_requirements_manager $manager the page_requirements_manager we are associated with.
     */
    protected function __construct(page_requirements_manager $manager) {
        parent::__construct($manager);
        $this->when = page_requirements_manager::WHEN_AT_END;
    }

    /**
     * @return string the JavaScript code needed to satisfy this requirement.
     */
    abstract public function get_js_code();

   /**
     * Indicate that the link to this JavaScript file should be output as soon as
     * possible. That is, if this requirement has already been output, this method
     * does nothing. Otherwise, if the <head> tag has not yet been printed, the link
     * to this script will be put in <head>. Otherwise, this method returns a
     * fragment of HTML that the caller is responsible for outputting as soon as
     * possible. In fact, it is recommended that you only call this function from
     * an echo statement, like:
     * <pre>
     *     echo $PAGE->requires->js(...)->asap();
     * </pre>
     *
     * @return string The HTML for the script tag. The caller
     * is responsible for outputting this HTML promptly.
     */
    public function asap() {
        if ($this->manager->is_head_done()) {
            return $this->now();
        } else {
            $this->in_head();
            return '';
        }
    }

    /**
     * Return the required JavaScript immediately, so it can be included in some
     * HTML that is being built.
     * @return string The HTML for the script tag. The caller
     * is responsible for making sure it is output.
     */
    public function now() {
        if ($this->is_done()) {
            return '';
        }
        $js = $this->get_js_code();
        $output = ajax_generate_script_tag($js);
        $this->mark_done();
        return $output;
    }

    /**
     * Indicate that the link to this JavaScript file should be output in the
     * <head> section of the HTML. If it too late for this request to be
     * satisfied, an exception is thrown.
     */
    public function in_head() {
        if ($this->is_done() || $this->when <= page_requirements_manager::WHEN_IN_HEAD) {
            return;
        }
        if ($this->manager->is_head_done()) {
            throw new coding_exception('Too late to ask for some JavaScript code to be output in &lt;head>.');
        }
        $this->when = page_requirements_manager::WHEN_IN_HEAD;
    }

    /**
     * Indicate that the link to this JavaScript file should be output at the top
     * of the <body> section of the HTML. If it too late for this request to be
     * satisfied, an exception is thrown.
     */
    public function at_top_of_body() {
        if ($this->is_done() || $this->when <= page_requirements_manager::WHEN_TOP_OF_BODY) {
            return;
        }
        if ($this->manager->is_top_of_body_done()) {
            throw new coding_exception('Too late to ask for some JavaScript code to be output at the top of &lt;body>.');
        }
        $this->when = page_requirements_manager::WHEN_TOP_OF_BODY;
    }
}


/**
 * This class represents a JavaScript function that must be called from the HTML
 * page. By default the call will be made at the end of the page, but you can
 * chage that using the {@link asap()}, {@link in_head()}, etc. methods.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class required_js_function_call extends required_js_code {
    protected $function;
    protected $arguments;
    protected $delay = 0;

    /**
     * Constructor. Normally instances of this class should not be created directly.
     * Client code should create them via the page_requirements_manager
     * method {@link page_requirements_manager::js_function_call()}.
     *
     * @param page_requirements_manager $manager the page_requirements_manager we are associated with.
     * @param string $function the name of the JavaScritp function to call.
     *      Can be a compound name like 'YAHOO.util.Event.addListener'.
     * @param array $arguments and array of arguments to be passed to the function.
     *      When generating the function call, this will be escaped using json_encode,
     *      so passing objects and arrays should work.
     */
    public function __construct(page_requirements_manager $manager, $function, $arguments) {
        parent::__construct($manager);
        $this->function = $function;
        $this->arguments = $arguments;
    }

    public function get_js_code() {
        $quotedargs = array();
        foreach ($this->arguments as $arg) {
            $quotedargs[] = json_encode($arg);
        }
        $js = $this->function . '(' . implode(', ', $quotedargs) . ');';
        if ($this->delay) {
            $js = 'setTimeout(function() { ' . $js . ' }, ' . ($this->delay * 1000) . ');';
        }
        return $js . "\n";
    }

    /**
     * Indicate that this function should be called in YUI's onDomReady event.
     *
     * Not that this is probably not necessary most of the time. Just having the
     * function call at the end of the HTML should normally be sufficient.
     */
    public function on_dom_ready() {
        if ($this->is_done() || $this->when < page_requirements_manager::WHEN_AT_END) {
            return;
        }
        $this->manager->yui2_lib('event');
        $this->when = page_requirements_manager::WHEN_ON_DOM_READY;
    }

    /**
     * Indicate that this function should be called a certain number of seconds
     * after the page has finished loading. (More exactly, a number of seconds
     * after the onDomReady event fires.)
     *
     * @param integer $seconds the number of seconds delay.
     */
    public function after_delay($seconds) {
        if ($seconds) {
            $this->on_dom_ready();
        }
        $this->delay = $seconds;
    }
}


/**
 * This class represents some data from PHP that needs to be made available in a
 * global JavaScript variable. By default the data will be output at the end of
 * the page, but you can chage that using the {@link asap()}, {@link in_head()}, etc. methods.
 *
 * @copyright 2009 Tim Hunt
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class required_data_for_js extends required_js_code {
    protected $variable;
    protected $data;

    /**
     * Constructor. Normally the class and its subclasses should not be created directly.
     * Client code should create them via the page_requirements_manager
     * method {@link page_requirements_manager::data_for_js()}.
     *
     * @param page_requirements_manager $manager the page_requirements_manager we are associated with.
     * @param string $variable the the name of the JavaScript variable to assign the data to.
     *      Will probably work if you use a compound name like 'mybuttons.button[1]', but this
     *      should be considered an experimental feature.
     * @param mixed $data The data to pass to JavaScript. This will be escaped using json_encode,
     *      so passing objects and arrays should work.
     */
    public function __construct(page_requirements_manager $manager, $variable, $data) {
        parent::__construct($manager);
        $this->variable = $variable;
        $this->data = json_encode($data);
        // json_encode immediately, so that if $data is an object (and therefore was
        // passed in by reference) we get the data at the time the call was made, and
        // not whatever the data happened to be when this is output.
    }

    public function get_js_code() {
        $prefix = 'var ';
        if (strpos($this->variable, '.') || strpos($this->variable, '[')) {
            $prefix = '';
        }
        return $prefix . $this->variable . ' = ' . $this->data . ";\n";
    }
}

/**
 * This class represents a Javascript event handler, listening for a
 * specific Event to occur on a DOM element identified by a given id.
 * By default the data will be output at the end of the page, but you
 * can change that using the {@link asap()}, {@link in_head()}, etc. methods.
 *
 * @copyright 2009 Nicolas Connault
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 */
class required_event_handler extends required_js_code {
    protected $id;
    protected $event;
    protected $function;
    protected $args = array();

    /**
     * Constructor. Normally the class and its subclasses should not be created directly.
     * Client code should create them via the page_requirements_manager
     * method {@link page_requirements_manager::data_for_js()}.
     *
     * @param page_requirements_manager $manager the page_requirements_manager we are associated with.
     * @param string $id The id of the DOM element that will be listening for the event
     * @param string $event A valid DOM event (click, mousedown, change etc.)
     * @param string $function The name of the function to call
     * @param array  $arguments An optional array of argument parameters to pass to the function
     */
    public function __construct(page_requirements_manager $manager, $id, $event, $function, $args=array()) {
        parent::__construct($manager);
        $this->id = $id;
        $this->event = $event;
        $this->function = $function;
        $this->args = $args;
    }

    public function get_js_code() {
        $output = "YAHOO.util.Event.addListener('$this->id', '$this->event', $this->function";
        if (!empty($this->args)) {
            $output .= ', ' . json_encode($this->args);
        }
        return $output . ");\n";
    }
}

/**
 * Generate a script tag containing the the specified code.
 *
 * @param string $js the JavaScript code
 * @return string HTML, the code wrapped in <script> tags.
 */
function ajax_generate_script_tag($js) {
    if ($js) {
        return '<script type="text/javascript">' . "\n//<![CDATA[\n" .
                $js . "//]]>\n</script>\n";
    } else {
        return '';
    }
}


/**
 * Return the HTML required to link to a JavaScript file.
 * @param $url the URL of a JavaScript file.
 * @return string the required HTML.
 */
function ajax_get_link_to_script($url) {
    return '<script type="text/javascript"  src="' . $url . '"></script>' . "\n";
}


/**
 * Returns whether ajax is enabled/allowed or not.
 */
function ajaxenabled($browsers = array()) {

    global $CFG, $USER;

    if (!empty($browsers)) {
        $valid = false;
        foreach ($browsers as $brand => $version) {
            if (check_browser_version($brand, $version)) {
                $valid = true;
            }
        }

        if (!$valid) {
            return false;
        }
    }

    $ie = check_browser_version('MSIE', 6.0);
    $ff = check_browser_version('Gecko', 20051106);
    $op = check_browser_version('Opera', 9.0);
    $sa = check_browser_version('Safari', 412);

    if (!$ie && !$ff && !$op && !$sa) {
        /** @see http://en.wikipedia.org/wiki/User_agent */
        // Gecko build 20051107 is what is in Firefox 1.5.
        // We still have issues with AJAX in other browsers.
        return false;
    }

    if (!empty($CFG->enableajax) && (!empty($USER->ajax) || !isloggedin())) {
        return true;
    } else {
        return false;
    }
}


/**
 * Used to create view of document to be passed to JavaScript on pageload.
 * We use this class to pass data from PHP to JavaScript.
 */
class jsportal {

    var $currentblocksection = null;
    var $blocks = array();


    /**
     * Takes id of block and adds it
     */
    function block_add($id, $hidden=false){
        $hidden_binary = 0;

        if ($hidden) {
            $hidden_binary = 1;
        }
        $this->blocks[count($this->blocks)] = array($this->currentblocksection, $id, $hidden_binary);
    }


    /**
     * Prints the JavaScript code needed to set up AJAX for the course.
     */
    function print_javascript($courseid, $return=false) {
        global $CFG, $USER, $OUTPUT, $COURSE;

        $blocksoutput = $output = '';
        for ($i=0; $i<count($this->blocks); $i++) {
            $blocksoutput .= "['".$this->blocks[$i][0]."',
                             '".$this->blocks[$i][1]."',
                             '".$this->blocks[$i][2]."']";

            if ($i != (count($this->blocks) - 1)) {
                $blocksoutput .= ',';
            }
        }
        $output .= "<script type=\"text/javascript\">\n";
        $output .= "    main.portal.id = ".$courseid.";\n";
        $output .= "    main.portal.blocks = new Array(".$blocksoutput.");\n";
        $output .= "    main.portal.strings['courseformat']='".$COURSE->format."';\n";
        $output .= "    main.portal.strings['wwwroot']='".$CFG->wwwroot."';\n";
        $output .= "    main.portal.strings['marker']='".get_string('markthistopic', '', '_var_')."';\n";
        $output .= "    main.portal.strings['marked']='".get_string('markedthistopic', '', '_var_')."';\n";
        $output .= "    main.portal.numsections = ".$COURSE->numsections.";\n";
        $output .= "    main.portal.strings['hide']='".get_string('hide')."';\n";
        $output .= "    main.portal.strings['hidesection']='".get_string('hidesection', '', '_var_')."';\n";
        $output .= "    main.portal.strings['show']='".get_string('show')."';\n";
        $output .= "    main.portal.strings['delete']='".get_string('delete')."';\n";
        $output .= "    main.portal.strings['move']='".get_string('move')."';\n";
        $output .= "    main.portal.strings['movesection']='".get_string('movesection', '', '_var_')."';\n";
        $output .= "    main.portal.strings['moveleft']='".get_string('moveleft')."';\n";
        $output .= "    main.portal.strings['moveright']='".get_string('moveright')."';\n";
        $output .= "    main.portal.strings['update']='".get_string('update')."';\n";
        $output .= "    main.portal.strings['groupsnone']='".get_string('groupsnone')."';\n";
        $output .= "    main.portal.strings['groupsseparate']='".get_string('groupsseparate')."';\n";
        $output .= "    main.portal.strings['groupsvisible']='".get_string('groupsvisible')."';\n";
        $output .= "    main.portal.strings['clicktochange']='".get_string('clicktochange')."';\n";
        $output .= "    main.portal.strings['deletecheck']='".get_string('deletecheck','','_var_')."';\n";
        $output .= "    main.portal.strings['resource']='".get_string('resource')."';\n";
        $output .= "    main.portal.strings['activity']='".get_string('activity')."';\n";
        $output .= "    main.portal.strings['sesskey']='".sesskey()."';\n";
        $output .= "    main.portal.icons['spacerimg']='".$OUTPUT->pix_url('spaces')."';\n";
        $output .= "    main.portal.icons['marker']='".$OUTPUT->pix_url('i/marker')."';\n";
        $output .= "    main.portal.icons['ihide']='".$OUTPUT->pix_url('i/hide')."';\n";
        $output .= "    main.portal.icons['move_2d']='".$OUTPUT->pix_url('i/move_2d')."';\n";
        $output .= "    main.portal.icons['show']='".$OUTPUT->pix_url('t/show')."';\n";
        $output .= "    main.portal.icons['hide']='".$OUTPUT->pix_url('t/hide')."';\n";
        $output .= "    main.portal.icons['delete']='".$OUTPUT->pix_url('t/delete')."';\n";
        $output .= "    main.portal.icons['groupn']='".$OUTPUT->pix_url('t/groupn')."';\n";
        $output .= "    main.portal.icons['groups']='".$OUTPUT->pix_url('t/groups')."';\n";
        $output .= "    main.portal.icons['groupv']='".$OUTPUT->pix_url('t/groupv')."';\n";
        if (right_to_left()) {
            $output .= "    main.portal.icons['backwards']='".$OUTPUT->pix_url('t/right')."';\n";
            $output .= "    main.portal.icons['forwards']='".$OUTPUT->pix_url('t/left')."';\n";
        } else {
            $output .= "    main.portal.icons['backwards']='".$OUTPUT->pix_url('t/left')."';\n";
            $output .= "    main.portal.icons['forwards']='".$OUTPUT->pix_url('t/right')."';\n";
        }

        $output .= "    onloadobj.load();\n";
        $output .= "    main.process_blocks();\n";
        $output .= "</script>";
        if ($return) {
            return $output;
        } else {
            echo $output;
        }
    }

}

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
 * Classes for rendering HTML output for Moodle.
 *
 * Please see {@link http://docs.moodle.org/en/Developement:How_Moodle_outputs_HTML}
 * for an overview.
 *
 * Included in this file are the primary renderer classes:
 *     - renderer_base:         The renderer outline class that all renderers
 *                              should inherit from.
 *     - core_renderer:         The standard HTML renderer.
 *     - core_renderer_cli:     An adaption of the standard renderer for CLI scripts.
 *     - core_renderer_ajax:    An adaption of the standard renderer for AJAX scripts.
 *     - plugin_renderer_base:  A renderer class that should be extended by all
 *                              plugin renderers.
 *
 * @package core
 * @category output
 * @copyright  2009 Tim Hunt
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Simple base class for Moodle renderers.
 *
 * Tracks the xhtml_container_stack to use, which is passed in in the constructor.
 *
 * Also has methods to facilitate generating HTML output.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class renderer_base {
    /**
     * @var xhtml_container_stack The xhtml_container_stack to use.
     */
    protected $opencontainers;

    /**
     * @var moodle_page The Moodle page the renderer has been created to assist with.
     */
    protected $page;

    /**
     * @var string The requested rendering target.
     */
    protected $target;

    /**
     * @var Mustache_Engine $mustache The mustache template compiler
     */
    private $mustache;

    /**
     * Return an instance of the mustache class.
     *
     * @since 2.9
     * @return Mustache_Engine
     */
    protected function get_mustache() {
        global $CFG;

        if ($this->mustache === null) {
            require_once($CFG->dirroot . '/lib/mustache/src/Mustache/Autoloader.php');
            Mustache_Autoloader::register();

            $themename = $this->page->theme->name;
            $themerev = theme_get_revision();

            $cachedir = make_localcache_directory("mustache/$themerev/$themename");

            $loader = new \core\output\mustache_filesystem_loader();
            $stringhelper = new \core\output\mustache_string_helper();
            $jshelper = new \core\output\mustache_javascript_helper($this->page->requires);
            $pixhelper = new \core\output\mustache_pix_helper($this);

            // We only expose the variables that are exposed to JS templates.
            $safeconfig = $this->page->requires->get_config_for_javascript($this->page, $this);

            $helpers = array('config' => $safeconfig,
                             'str' => array($stringhelper, 'str'),
                             'js' => array($jshelper, 'help'),
                             'pix' => array($pixhelper, 'pix'));

            $this->mustache = new Mustache_Engine(array(
                'cache' => $cachedir,
                'escape' => 's',
                'loader' => $loader,
                'helpers' => $helpers));

        }

        return $this->mustache;
    }


    /**
     * Constructor
     *
     * The constructor takes two arguments. The first is the page that the renderer
     * has been created to assist with, and the second is the target.
     * The target is an additional identifier that can be used to load different
     * renderers for different options.
     *
     * @param moodle_page $page the page we are doing output for.
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        $this->opencontainers = $page->opencontainers;
        $this->page = $page;
        $this->target = $target;
    }

    /**
     * Renders a template by name with the given context.
     *
     * The provided data needs to be array/stdClass made up of only simple types.
     * Simple types are array,stdClass,bool,int,float,string
     *
     * @since 2.9
     * @param array|stdClass $context Context containing data for the template.
     * @return string|boolean
     */
    public function render_from_template($templatename, $context) {
        static $templatecache = array();
        $mustache = $this->get_mustache();

        // Provide 1 random value that will not change within a template
        // but will be different from template to template. This is useful for
        // e.g. aria attributes that only work with id attributes and must be
        // unique in a page.
        $mustache->addHelper('uniqid', new \core\output\mustache_uniqid_helper());
        if (isset($templatecache[$templatename])) {
            $template = $templatecache[$templatename];
        } else {
            try {
                $template = $mustache->loadTemplate($templatename);
                $templatecache[$templatename] = $template;
            } catch (Mustache_Exception_UnknownTemplateException $e) {
                throw new moodle_exception('Unknown template: ' . $templatename);
            }
        }
        return trim($template->render($context));
    }


    /**
     * Returns rendered widget.
     *
     * The provided widget needs to be an object that extends the renderable
     * interface.
     * If will then be rendered by a method based upon the classname for the widget.
     * For instance a widget of class `crazywidget` will be rendered by a protected
     * render_crazywidget method of this renderer.
     *
     * @param renderable $widget instance with renderable interface
     * @return string
     */
    public function render(renderable $widget) {
        $classname = get_class($widget);
        // Strip namespaces.
        $classname = preg_replace('/^.*\\\/', '', $classname);
        // Remove _renderable suffixes
        $classname = preg_replace('/_renderable$/', '', $classname);

        $rendermethod = 'render_'.$classname;
        if (method_exists($this, $rendermethod)) {
            return $this->$rendermethod($widget);
        }
        throw new coding_exception('Can not render widget, renderer method ('.$rendermethod.') not found.');
    }

    /**
     * Adds a JS action for the element with the provided id.
     *
     * This method adds a JS event for the provided component action to the page
     * and then returns the id that the event has been attached to.
     * If no id has been provided then a new ID is generated by {@link html_writer::random_id()}
     *
     * @param component_action $action
     * @param string $id
     * @return string id of element, either original submitted or random new if not supplied
     */
    public function add_action_handler(component_action $action, $id = null) {
        if (!$id) {
            $id = html_writer::random_id($action->event);
        }
        $this->page->requires->event_handler("#$id", $action->event, $action->jsfunction, $action->jsfunctionargs);
        return $id;
    }

    /**
     * Returns true is output has already started, and false if not.
     *
     * @return boolean true if the header has been printed.
     */
    public function has_started() {
        return $this->page->state >= moodle_page::STATE_IN_BODY;
    }

    /**
     * Given an array or space-separated list of classes, prepares and returns the HTML class attribute value
     *
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
     * Return the moodle_url for an image.
     *
     * The exact image location and extension is determined
     * automatically by searching for gif|png|jpg|jpeg, please
     * note there can not be diferent images with the different
     * extension. The imagename is for historical reasons
     * a relative path name, it may be changed later for core
     * images. It is recommended to not use subdirectories
     * in plugin and theme pix directories.
     *
     * There are three types of images:
     * 1/ theme images  - stored in theme/mytheme/pix/,
     *                    use component 'theme'
     * 2/ core images   - stored in /pix/,
     *                    overridden via theme/mytheme/pix_core/
     * 3/ plugin images - stored in mod/mymodule/pix,
     *                    overridden via theme/mytheme/pix_plugins/mod/mymodule/,
     *                    example: pix_url('comment', 'mod_glossary')
     *
     * @param string $imagename the pathname of the image
     * @param string $component full plugin name (aka component) or 'theme'
     * @return moodle_url
     */
    public function pix_url($imagename, $component = 'moodle') {
        return $this->page->theme->pix_url($imagename, $component);
    }
}


/**
 * Basis for all plugin renderers.
 *
 * @copyright Petr Skoda (skodak)
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class plugin_renderer_base extends renderer_base {

    /**
     * @var renderer_base|core_renderer A reference to the current renderer.
     * The renderer provided here will be determined by the page but will in 90%
     * of cases by the {@link core_renderer}
     */
    protected $output;

    /**
     * Constructor method, calls the parent constructor
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        if (empty($target) && $page->pagelayout === 'maintenance') {
            // If the page is using the maintenance layout then we're going to force the target to maintenance.
            // This way we'll get a special maintenance renderer that is designed to block access to API's that are likely
            // unavailable for this page layout.
            $target = RENDERER_TARGET_MAINTENANCE;
        }
        $this->output = $page->get_renderer('core', null, $target);
        parent::__construct($page, $target);
    }

    /**
     * Renders the provided widget and returns the HTML to display it.
     *
     * @param renderable $widget instance with renderable interface
     * @return string
     */
    public function render(renderable $widget) {
        $classname = get_class($widget);
        // Strip namespaces.
        $classname = preg_replace('/^.*\\\/', '', $classname);
        // Keep a copy at this point, we may need to look for a deprecated method.
        $deprecatedmethod = 'render_'.$classname;
        // Remove _renderable suffixes
        $classname = preg_replace('/_renderable$/', '', $classname);

        $rendermethod = 'render_'.$classname;
        if (method_exists($this, $rendermethod)) {
            return $this->$rendermethod($widget);
        }
        if ($rendermethod !== $deprecatedmethod && method_exists($this, $deprecatedmethod)) {
            // This is exactly where we don't want to be.
            // If you have arrived here you have a renderable component within your plugin that has the name
            // blah_renderable, and you have a render method render_blah_renderable on your plugin.
            // In 2.8 we revamped output, as part of this change we changed slightly how renderables got rendered
            // and the _renderable suffix now gets removed when looking for a render method.
            // You need to change your renderers render_blah_renderable to render_blah.
            // Until you do this it will not be possible for a theme to override the renderer to override your method.
            // Please do it ASAP.
            static $debugged = array();
            if (!isset($debugged[$deprecatedmethod])) {
                debugging(sprintf('Deprecated call. Please rename your renderables render method from %s to %s.',
                    $deprecatedmethod, $rendermethod), DEBUG_DEVELOPER);
                $debugged[$deprecatedmethod] = true;
            }
            return $this->$deprecatedmethod($widget);
        }
        // pass to core renderer if method not found here
        return $this->output->render($widget);
    }

    /**
     * Magic method used to pass calls otherwise meant for the standard renderer
     * to it to ensure we don't go causing unnecessary grief.
     *
     * @param string $method
     * @param array $arguments
     * @return mixed
     */
    public function __call($method, $arguments) {
        if (method_exists('renderer_base', $method)) {
            throw new coding_exception('Protected method called against '.get_class($this).' :: '.$method);
        }
        if (method_exists($this->output, $method)) {
            return call_user_func_array(array($this->output, $method), $arguments);
        } else {
            throw new coding_exception('Unknown method called against '.get_class($this).' :: '.$method);
        }
    }
}


/**
 * The standard implementation of the core_renderer interface.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class core_renderer extends renderer_base {
    /**
     * Do NOT use, please use <?php echo $OUTPUT->main_content() ?>
     * in layout files instead.
     * @deprecated
     * @var string used in {@link core_renderer::header()}.
     */
    const MAIN_CONTENT_TOKEN = '[MAIN CONTENT GOES HERE]';

    /**
     * @var string Used to pass information from {@link core_renderer::doctype()} to
     * {@link core_renderer::standard_head_html()}.
     */
    protected $contenttype;

    /**
     * @var string Used by {@link core_renderer::redirect_message()} method to communicate
     * with {@link core_renderer::header()}.
     */
    protected $metarefreshtag = '';

    /**
     * @var string Unique token for the closing HTML
     */
    protected $unique_end_html_token;

    /**
     * @var string Unique token for performance information
     */
    protected $unique_performance_info_token;

    /**
     * @var string Unique token for the main content.
     */
    protected $unique_main_content_token;

    /**
     * Constructor
     *
     * @param moodle_page $page the page we are doing output for.
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        $this->opencontainers = $page->opencontainers;
        $this->page = $page;
        $this->target = $target;

        $this->unique_end_html_token = '%%ENDHTML-'.sesskey().'%%';
        $this->unique_performance_info_token = '%%PERFORMANCEINFO-'.sesskey().'%%';
        $this->unique_main_content_token = '[MAIN CONTENT GOES HERE - '.sesskey().']';
    }

    /**
     * Get the DOCTYPE declaration that should be used with this page. Designed to
     * be called in theme layout.php files.
     *
     * @return string the DOCTYPE declaration that should be used.
     */
    public function doctype() {
        if ($this->page->theme->doctype === 'html5') {
            $this->contenttype = 'text/html; charset=utf-8';
            return "<!DOCTYPE html>\n";

        } else if ($this->page->theme->doctype === 'xhtml5') {
            $this->contenttype = 'application/xhtml+xml; charset=utf-8';
            return "<!DOCTYPE html>\n";

        } else {
            // legacy xhtml 1.0
            $this->contenttype = 'text/html; charset=utf-8';
            return ('<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">' . "\n");
        }
    }

    /**
     * The attributes that should be added to the <html> tag. Designed to
     * be called in theme layout.php files.
     *
     * @return string HTML fragment.
     */
    public function htmlattributes() {
        $return = get_html_lang(true);
        if ($this->page->theme->doctype !== 'html5') {
            $return .= ' xmlns="http://www.w3.org/1999/xhtml"';
        }
        return $return;
    }

    /**
     * The standard tags (meta tags, links to stylesheets and JavaScript, etc.)
     * that should be included in the <head> tag. Designed to be called in theme
     * layout.php files.
     *
     * @return string HTML fragment.
     */
    public function standard_head_html() {
        global $CFG, $SESSION;

        // Before we output any content, we need to ensure that certain
        // page components are set up.

        // Blocks must be set up early as they may require javascript which
        // has to be included in the page header before output is created.
        foreach ($this->page->blocks->get_regions() as $region) {
            $this->page->blocks->ensure_content_created($region, $this);
        }

        $output = '';
        $output .= '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n";
        $output .= '<meta name="keywords" content="moodle, ' . $this->page->title . '" />' . "\n";
        // This is only set by the {@link redirect()} method
        $output .= $this->metarefreshtag;

        // Check if a periodic refresh delay has been set and make sure we arn't
        // already meta refreshing
        if ($this->metarefreshtag=='' && $this->page->periodicrefreshdelay!==null) {
            $output .= '<meta http-equiv="refresh" content="'.$this->page->periodicrefreshdelay.';url='.$this->page->url->out().'" />';
        }

        // flow player embedding support
        $this->page->requires->js_function_call('M.util.load_flowplayer');

        // Set up help link popups for all links with the helptooltip class
        $this->page->requires->js_init_call('M.util.help_popups.setup');

        // Setup help icon overlays.
        $this->page->requires->yui_module('moodle-core-popuphelp', 'M.core.init_popuphelp');
        $this->page->requires->strings_for_js(array(
            'morehelp',
            'loadinghelp',
        ), 'moodle');

        $this->page->requires->js_function_call('setTimeout', array('fix_column_widths()', 20));

        $focus = $this->page->focuscontrol;
        if (!empty($focus)) {
            if (preg_match("#forms\['([a-zA-Z0-9]+)'\].elements\['([a-zA-Z0-9]+)'\]#", $focus, $matches)) {
                // This is a horrifically bad way to handle focus but it is passed in
                // through messy formslib::moodleform
                $this->page->requires->js_function_call('old_onload_focus', array($matches[1], $matches[2]));
            } else if (strpos($focus, '.')!==false) {
                // Old style of focus, bad way to do it
                debugging('This code is using the old style focus event, Please update this code to focus on an element id or the moodleform focus method.', DEBUG_DEVELOPER);
                $this->page->requires->js_function_call('old_onload_focus', explode('.', $focus, 2));
            } else {
                // Focus element with given id
                $this->page->requires->js_function_call('focuscontrol', array($focus));
            }
        }

        // Get the theme stylesheet - this has to be always first CSS, this loads also styles.css from all plugins;
        // any other custom CSS can not be overridden via themes and is highly discouraged
        $urls = $this->page->theme->css_urls($this->page);
        foreach ($urls as $url) {
            $this->page->requires->css_theme($url);
        }

        // Get the theme javascript head and footer
        if ($jsurl = $this->page->theme->javascript_url(true)) {
            $this->page->requires->js($jsurl, true);
        }
        if ($jsurl = $this->page->theme->javascript_url(false)) {
            $this->page->requires->js($jsurl);
        }

        // Get any HTML from the page_requirements_manager.
        $output .= $this->page->requires->get_head_code($this->page, $this);

        // List alternate versions.
        foreach ($this->page->alternateversions as $type => $alt) {
            $output .= html_writer::empty_tag('link', array('rel' => 'alternate',
                    'type' => $type, 'title' => $alt->title, 'href' => $alt->url));
        }

        if (!empty($CFG->additionalhtmlhead)) {
            $output .= "\n".$CFG->additionalhtmlhead;
        }

        return $output;
    }

    /**
     * The standard tags (typically skip links) that should be output just inside
     * the start of the <body> tag. Designed to be called in theme layout.php files.
     *
     * @return string HTML fragment.
     */
    public function standard_top_of_body_html() {
        global $CFG;
        $output = $this->page->requires->get_top_of_body_code();
        if (!empty($CFG->additionalhtmltopofbody)) {
            $output .= "\n".$CFG->additionalhtmltopofbody;
        }
        $output .= $this->maintenance_warning();
        return $output;
    }

    /**
     * Scheduled maintenance warning message.
     *
     * Note: This is a nasty hack to display maintenance notice, this should be moved
     *       to some general notification area once we have it.
     *
     * @return string
     */
    public function maintenance_warning() {
        global $CFG;

        $output = '';
        if (isset($CFG->maintenance_later) and $CFG->maintenance_later > time()) {
            $timeleft = $CFG->maintenance_later - time();
            // If timeleft less than 30 sec, set the class on block to error to highlight.
            $errorclass = ($timeleft < 30) ? 'error' : 'warning';
            $output .= $this->box_start($errorclass . ' moodle-has-zindex maintenancewarning');
            $a = new stdClass();
            $a->min = (int)($timeleft/60);
            $a->sec = (int)($timeleft % 60);
            $output .= get_string('maintenancemodeisscheduled', 'admin', $a) ;
            $output .= $this->box_end();
            $this->page->requires->yui_module('moodle-core-maintenancemodetimer', 'M.core.maintenancemodetimer',
                    array(array('timeleftinsec' => $timeleft)));
            $this->page->requires->strings_for_js(
                    array('maintenancemodeisscheduled', 'sitemaintenance'),
                    'admin');
        }
        return $output;
    }

    /**
     * The standard tags (typically performance information and validation links,
     * if we are in developer debug mode) that should be output in the footer area
     * of the page. Designed to be called in theme layout.php files.
     *
     * @return string HTML fragment.
     */
    public function standard_footer_html() {
        global $CFG, $SCRIPT;

        if (during_initial_install()) {
            // Debugging info can not work before install is finished,
            // in any case we do not want any links during installation!
            return '';
        }

        // This function is normally called from a layout.php file in {@link core_renderer::header()}
        // but some of the content won't be known until later, so we return a placeholder
        // for now. This will be replaced with the real content in {@link core_renderer::footer()}.
        $output = $this->unique_performance_info_token;
        if ($this->page->devicetypeinuse == 'legacy') {
            // The legacy theme is in use print the notification
            $output .= html_writer::tag('div', get_string('legacythemeinuse'), array('class'=>'legacythemeinuse'));
        }

        // Get links to switch device types (only shown for users not on a default device)
        $output .= $this->theme_switch_links();

        if (!empty($CFG->debugpageinfo)) {
            $output .= '<div class="performanceinfo pageinfo">This page is: ' . $this->page->debug_summary() . '</div>';
        }
        if (debugging(null, DEBUG_DEVELOPER) and has_capability('moodle/site:config', context_system::instance())) {  // Only in developer mode
            // Add link to profiling report if necessary
            if (function_exists('profiling_is_running') && profiling_is_running()) {
                $txt = get_string('profiledscript', 'admin');
                $title = get_string('profiledscriptview', 'admin');
                $url = $CFG->wwwroot . '/admin/tool/profiling/index.php?script=' . urlencode($SCRIPT);
                $link= '<a title="' . $title . '" href="' . $url . '">' . $txt . '</a>';
                $output .= '<div class="profilingfooter">' . $link . '</div>';
            }
            $purgeurl = new moodle_url('/admin/purgecaches.php', array('confirm' => 1,
                'sesskey' => sesskey(), 'returnurl' => $this->page->url->out_as_local_url(false)));
            $output .= '<div class="purgecaches">' .
                    html_writer::link($purgeurl, get_string('purgecaches', 'admin')) . '</div>';
        }
        if (!empty($CFG->debugvalidators)) {
            // NOTE: this is not a nice hack, $PAGE->url is not always accurate and $FULLME neither, it is not a bug if it fails. --skodak
            $output .= '<div class="validators"><ul>
              <li><a href="http://validator.w3.org/check?verbose=1&amp;ss=1&amp;uri=' . urlencode(qualified_me()) . '">Validate HTML</a></li>
              <li><a href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=-1&amp;url1=' . urlencode(qualified_me()) . '">Section 508 Check</a></li>
              <li><a href="http://www.contentquality.com/mynewtester/cynthia.exe?rptmode=0&amp;warnp2n3e=1&amp;url1=' . urlencode(qualified_me()) . '">WCAG 1 (2,3) Check</a></li>
            </ul></div>';
        }
        return $output;
    }

    /**
     * Returns standard main content placeholder.
     * Designed to be called in theme layout.php files.
     *
     * @return string HTML fragment.
     */
    public function main_content() {
        // This is here because it is the only place we can inject the "main" role over the entire main content area
        // without requiring all theme's to manually do it, and without creating yet another thing people need to
        // remember in the theme.
        // This is an unfortunate hack. DO NO EVER add anything more here.
        // DO NOT add classes.
        // DO NOT add an id.
        return '<div role="main">'.$this->unique_main_content_token.'</div>';
    }

    /**
     * The standard tags (typically script tags that are not needed earlier) that
     * should be output after everything else. Designed to be called in theme layout.php files.
     *
     * @return string HTML fragment.
     */
    public function standard_end_of_body_html() {
        global $CFG;

        // This function is normally called from a layout.php file in {@link core_renderer::header()}
        // but some of the content won't be known until later, so we return a placeholder
        // for now. This will be replaced with the real content in {@link core_renderer::footer()}.
        $output = '';
        if (!empty($CFG->additionalhtmlfooter)) {
            $output .= "\n".$CFG->additionalhtmlfooter;
        }
        $output .= $this->unique_end_html_token;
        return $output;
    }

    /**
     * Return the standard string that says whether you are logged in (and switched
     * roles/logged in as another user).
     * @param bool $withlinks if false, then don't include any links in the HTML produced.
     * If not set, the default is the nologinlinks option from the theme config.php file,
     * and if that is not set, then links are included.
     * @return string HTML fragment.
     */
    public function login_info($withlinks = null) {
        global $USER, $CFG, $DB, $SESSION;

        if (during_initial_install()) {
            return '';
        }

        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        $course = $this->page->course;
        if (\core\session\manager::is_loggedinas()) {
            $realuser = \core\session\manager::get_realuser();
            $fullname = fullname($realuser, true);
            if ($withlinks) {
                $loginastitle = get_string('loginas');
                $realuserinfo = " [<a href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&amp;sesskey=".sesskey()."\"";
                $realuserinfo .= "title =\"".$loginastitle."\">$fullname</a>] ";
            } else {
                $realuserinfo = " [$fullname] ";
            }
        } else {
            $realuserinfo = '';
        }

        $loginpage = $this->is_login_page();
        $loginurl = get_login_url();

        if (empty($course->id)) {
            // $course->id is not defined during installation
            return '';
        } else if (isloggedin()) {
            $context = context_course::instance($course->id);

            $fullname = fullname($USER, true);
            // Since Moodle 2.0 this link always goes to the public profile page (not the course profile page)
            if ($withlinks) {
                $linktitle = get_string('viewprofile');
                $username = "<a href=\"$CFG->wwwroot/user/profile.php?id=$USER->id\" title=\"$linktitle\">$fullname</a>";
            } else {
                $username = $fullname;
            }
            if (is_mnet_remote_user($USER) and $idprovider = $DB->get_record('mnet_host', array('id'=>$USER->mnethostid))) {
                if ($withlinks) {
                    $username .= " from <a href=\"{$idprovider->wwwroot}\">{$idprovider->name}</a>";
                } else {
                    $username .= " from {$idprovider->name}";
                }
            }
            if (isguestuser()) {
                $loggedinas = $realuserinfo.get_string('loggedinasguest');
                if (!$loginpage && $withlinks) {
                    $loggedinas .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
                }
            } else if (is_role_switched($course->id)) { // Has switched roles
                $rolename = '';
                if ($role = $DB->get_record('role', array('id'=>$USER->access['rsw'][$context->path]))) {
                    $rolename = ': '.role_get_name($role, $context);
                }
                $loggedinas = get_string('loggedinas', 'moodle', $username).$rolename;
                if ($withlinks) {
                    $url = new moodle_url('/course/switchrole.php', array('id'=>$course->id,'sesskey'=>sesskey(), 'switchrole'=>0, 'returnurl'=>$this->page->url->out_as_local_url(false)));
                    $loggedinas .= ' ('.html_writer::tag('a', get_string('switchrolereturn'), array('href' => $url)).')';
                }
            } else {
                $loggedinas = $realuserinfo.get_string('loggedinas', 'moodle', $username);
                if ($withlinks) {
                    $loggedinas .= " (<a href=\"$CFG->wwwroot/login/logout.php?sesskey=".sesskey()."\">".get_string('logout').'</a>)';
                }
            }
        } else {
            $loggedinas = get_string('loggedinnot', 'moodle');
            if (!$loginpage && $withlinks) {
                $loggedinas .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
            }
        }

        $loggedinas = '<div class="logininfo">'.$loggedinas.'</div>';

        if (isset($SESSION->justloggedin)) {
            unset($SESSION->justloggedin);
            if (!empty($CFG->displayloginfailures)) {
                if (!isguestuser()) {
                    // Include this file only when required.
                    require_once($CFG->dirroot . '/user/lib.php');
                    if ($count = user_count_login_failures($USER)) {
                        $loggedinas .= '<div class="loginfailures">';
                        $a = new stdClass();
                        $a->attempts = $count;
                        $loggedinas .= get_string('failedloginattempts', '', $a);
                        if (file_exists("$CFG->dirroot/report/log/index.php") and has_capability('report/log:view', context_system::instance())) {
                            $loggedinas .= ' ('.html_writer::link(new moodle_url('/report/log/index.php', array('chooselog' => 1,
                                    'id' => 0 , 'modid' => 'site_errors')), get_string('logs')).')';
                        }
                        $loggedinas .= '</div>';
                    }
                }
            }
        }

        return $loggedinas;
    }

    /**
     * Check whether the current page is a login page.
     *
     * @since Moodle 2.9
     * @return bool
     */
    protected function is_login_page() {
        // This is a real bit of a hack, but its a rarety that we need to do something like this.
        // In fact the login pages should be only these two pages and as exposing this as an option for all pages
        // could lead to abuse (or at least unneedingly complex code) the hack is the way to go.
        return in_array(
            $this->page->url->out_as_local_url(false, array()),
            array(
                '/login/index.php',
                '/login/forgot_password.php',
            )
        );
    }

    /**
     * Return the 'back' link that normally appears in the footer.
     *
     * @return string HTML fragment.
     */
    public function home_link() {
        global $CFG, $SITE;

        if ($this->page->pagetype == 'site-index') {
            // Special case for site home page - please do not remove
            return '<div class="sitelink">' .
                   '<a title="Moodle" href="http://moodle.org/">' .
                   '<img src="' . $this->pix_url('moodlelogo') . '" alt="'.get_string('moodlelogo').'" /></a></div>';

        } else if (!empty($CFG->target_release) && $CFG->target_release != $CFG->release) {
            // Special case for during install/upgrade.
            return '<div class="sitelink">'.
                   '<a title="Moodle" href="http://docs.moodle.org/en/Administrator_documentation" onclick="this.target=\'_blank\'">' .
                   '<img src="' . $this->pix_url('moodlelogo') . '" alt="'.get_string('moodlelogo').'" /></a></div>';

        } else if ($this->page->course->id == $SITE->id || strpos($this->page->pagetype, 'course-view') === 0) {
            return '<div class="homelink"><a href="' . $CFG->wwwroot . '/">' .
                    get_string('home') . '</a></div>';

        } else {
            return '<div class="homelink"><a href="' . $CFG->wwwroot . '/course/view.php?id=' . $this->page->course->id . '">' .
                    format_string($this->page->course->shortname, true, array('context' => $this->page->context)) . '</a></div>';
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
                    $this->page->requires->js_function_call('document.location.replace', array($url), false, ($delay + 3));
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
                    $this->page->requires->js_function_call('document.location.replace', array($url), false, $delay);
                }
                $output = $this->opencontainers->pop_all_but_last();
                break;
            case moodle_page::STATE_DONE :
                // Too late to be calling redirect now
                throw new coding_exception('You cannot redirect after the entire page has been generated');
                break;
        }
        $output .= $this->notification($message, 'redirectmessage');
        $output .= '<div class="continuebutton">(<a href="'. $encodedurl .'">'. get_string('continue') .'</a>)</div>';
        if ($debugdisableredirect) {
            $output .= '<p><strong>'.get_string('erroroutput', 'error').'</strong></p>';
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
     * @return string HTML that you must output this, preferably immediately.
     */
    public function header() {
        global $USER, $CFG;

        if (\core\session\manager::is_loggedinas()) {
            $this->page->add_body_class('userloggedinas');
        }

        // If the user is logged in, and we're not in initial install,
        // check to see if the user is role-switched and add the appropriate
        // CSS class to the body element.
        if (!during_initial_install() && isloggedin() && is_role_switched($this->page->course->id)) {
            $this->page->add_body_class('userswitchedrole');
        }

        // Give themes a chance to init/alter the page object.
        $this->page->theme->init_page($this->page);

        $this->page->set_state(moodle_page::STATE_PRINTING_HEADER);

        // Find the appropriate page layout file, based on $this->page->pagelayout.
        $layoutfile = $this->page->theme->layout_file($this->page->pagelayout);
        // Render the layout using the layout file.
        $rendered = $this->render_page_layout($layoutfile);

        // Slice the rendered output into header and footer.
        $cutpos = strpos($rendered, $this->unique_main_content_token);
        if ($cutpos === false) {
            $cutpos = strpos($rendered, self::MAIN_CONTENT_TOKEN);
            $token = self::MAIN_CONTENT_TOKEN;
        } else {
            $token = $this->unique_main_content_token;
        }

        if ($cutpos === false) {
            throw new coding_exception('page layout file ' . $layoutfile . ' does not contain the main content placeholder, please include "<?php echo $OUTPUT->main_content() ?>" in theme layout file.');
        }
        $header = substr($rendered, 0, $cutpos);
        $footer = substr($rendered, $cutpos + strlen($token));

        if (empty($this->contenttype)) {
            debugging('The page layout file did not call $OUTPUT->doctype()');
            $header = $this->doctype() . $header;
        }

        // If this theme version is below 2.4 release and this is a course view page
        if ((!isset($this->page->theme->settings->version) || $this->page->theme->settings->version < 2012101500) &&
                $this->page->pagelayout === 'course' && $this->page->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)) {
            // check if course content header/footer have not been output during render of theme layout
            $coursecontentheader = $this->course_content_header(true);
            $coursecontentfooter = $this->course_content_footer(true);
            if (!empty($coursecontentheader)) {
                // display debug message and add header and footer right above and below main content
                // Please note that course header and footer (to be displayed above and below the whole page)
                // are not displayed in this case at all.
                // Besides the content header and footer are not displayed on any other course page
                debugging('The current theme is not optimised for 2.4, the course-specific header and footer defined in course format will not be output', DEBUG_DEVELOPER);
                $header .= $coursecontentheader;
                $footer = $coursecontentfooter. $footer;
            }
        }

        send_headers($this->contenttype, $this->page->cacheable);

        $this->opencontainers->push('header/footer', $footer);
        $this->page->set_state(moodle_page::STATE_IN_BODY);

        return $header . $this->skip_link_target('maincontent');
    }

    /**
     * Renders and outputs the page layout file.
     *
     * This is done by preparing the normal globals available to a script, and
     * then including the layout file provided by the current theme for the
     * requested layout.
     *
     * @param string $layoutfile The name of the layout file
     * @return string HTML code
     */
    protected function render_page_layout($layoutfile) {
        global $CFG, $SITE, $USER;
        // The next lines are a bit tricky. The point is, here we are in a method
        // of a renderer class, and this object may, or may not, be the same as
        // the global $OUTPUT object. When rendering the page layout file, we want to use
        // this object. However, people writing Moodle code expect the current
        // renderer to be called $OUTPUT, not $this, so define a variable called
        // $OUTPUT pointing at $this. The same comment applies to $PAGE and $COURSE.
        $OUTPUT = $this;
        $PAGE = $this->page;
        $COURSE = $this->page->course;

        ob_start();
        include($layoutfile);
        $rendered = ob_get_contents();
        ob_end_clean();
        return $rendered;
    }

    /**
     * Outputs the page's footer
     *
     * @return string HTML fragment
     */
    public function footer() {
        global $CFG, $DB;

        $output = $this->container_end_all(true);

        $footer = $this->opencontainers->pop('header/footer');

        if (debugging() and $DB and $DB->is_transaction_started()) {
            // TODO: MDL-20625 print warning - transaction will be rolled back
        }

        // Provide some performance info if required
        $performanceinfo = '';
        if (defined('MDL_PERF') || (!empty($CFG->perfdebug) and $CFG->perfdebug > 7)) {
            $perf = get_performance_info();
            if (defined('MDL_PERFTOFOOT') || debugging() || $CFG->perfdebug > 7) {
                $performanceinfo = $perf['html'];
            }
        }

        // We always want performance data when running a performance test, even if the user is redirected to another page.
        if (MDL_PERF_TEST && strpos($footer, $this->unique_performance_info_token) === false) {
            $footer = $this->unique_performance_info_token . $footer;
        }
        $footer = str_replace($this->unique_performance_info_token, $performanceinfo, $footer);

        $footer = str_replace($this->unique_end_html_token, $this->page->requires->get_end_code(), $footer);

        $this->page->set_state(moodle_page::STATE_DONE);

        return $output . $footer;
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
    public function container_end_all($shouldbenone = false) {
        return $this->opencontainers->pop_all_but_last($shouldbenone);
    }

    /**
     * Returns course-specific information to be output immediately above content on any course page
     * (for the current course)
     *
     * @param bool $onlyifnotcalledbefore output content only if it has not been output before
     * @return string
     */
    public function course_content_header($onlyifnotcalledbefore = false) {
        global $CFG;
        if ($this->page->course->id == SITEID) {
            // return immediately and do not include /course/lib.php if not necessary
            return '';
        }
        static $functioncalled = false;
        if ($functioncalled && $onlyifnotcalledbefore) {
            // we have already output the content header
            return '';
        }
        require_once($CFG->dirroot.'/course/lib.php');
        $functioncalled = true;
        $courseformat = course_get_format($this->page->course);
        if (($obj = $courseformat->course_content_header()) !== null) {
            return html_writer::div($courseformat->get_renderer($this->page)->render($obj), 'course-content-header');
        }
        return '';
    }

    /**
     * Returns course-specific information to be output immediately below content on any course page
     * (for the current course)
     *
     * @param bool $onlyifnotcalledbefore output content only if it has not been output before
     * @return string
     */
    public function course_content_footer($onlyifnotcalledbefore = false) {
        global $CFG;
        if ($this->page->course->id == SITEID) {
            // return immediately and do not include /course/lib.php if not necessary
            return '';
        }
        static $functioncalled = false;
        if ($functioncalled && $onlyifnotcalledbefore) {
            // we have already output the content footer
            return '';
        }
        $functioncalled = true;
        require_once($CFG->dirroot.'/course/lib.php');
        $courseformat = course_get_format($this->page->course);
        if (($obj = $courseformat->course_content_footer()) !== null) {
            return html_writer::div($courseformat->get_renderer($this->page)->render($obj), 'course-content-footer');
        }
        return '';
    }

    /**
     * Returns course-specific information to be output on any course page in the header area
     * (for the current course)
     *
     * @return string
     */
    public function course_header() {
        global $CFG;
        if ($this->page->course->id == SITEID) {
            // return immediately and do not include /course/lib.php if not necessary
            return '';
        }
        require_once($CFG->dirroot.'/course/lib.php');
        $courseformat = course_get_format($this->page->course);
        if (($obj = $courseformat->course_header()) !== null) {
            return $courseformat->get_renderer($this->page)->render($obj);
        }
        return '';
    }

    /**
     * Returns course-specific information to be output on any course page in the footer area
     * (for the current course)
     *
     * @return string
     */
    public function course_footer() {
        global $CFG;
        if ($this->page->course->id == SITEID) {
            // return immediately and do not include /course/lib.php if not necessary
            return '';
        }
        require_once($CFG->dirroot.'/course/lib.php');
        $courseformat = course_get_format($this->page->course);
        if (($obj = $courseformat->course_footer()) !== null) {
            return $courseformat->get_renderer($this->page)->render($obj);
        }
        return '';
    }

    /**
     * Returns lang menu or '', this method also checks forcing of languages in courses.
     *
     * This function calls {@link core_renderer::render_single_select()} to actually display the language menu.
     *
     * @return string The lang menu HTML or empty string
     */
    public function lang_menu() {
        global $CFG;

        if (empty($CFG->langmenu)) {
            return '';
        }

        if ($this->page->course != SITEID and !empty($this->page->course->lang)) {
            // do not show lang menu if language forced
            return '';
        }

        $currlang = current_language();
        $langs = get_string_manager()->get_list_of_translations();

        if (count($langs) < 2) {
            return '';
        }

        $s = new single_select($this->page->url, 'lang', $langs, $currlang, null);
        $s->label = get_accesshide(get_string('language'));
        $s->class = 'langmenu';
        return $this->render($s);
    }

    /**
     * Output the row of editing icons for a block, as defined by the controls array.
     *
     * @param array $controls an array like {@link block_contents::$controls}.
     * @param string $blockid The ID given to the block.
     * @return string HTML fragment.
     */
    public function block_controls($actions, $blockid = null) {
        global $CFG;
        if (empty($actions)) {
            return '';
        }
        $menu = new action_menu($actions);
        if ($blockid !== null) {
            $menu->set_owner_selector('#'.$blockid);
        }
        $menu->set_constraint('.block-region');
        $menu->attributes['class'] .= ' block-control-actions commands';
        if (isset($CFG->blockeditingmenu) && !$CFG->blockeditingmenu) {
            $menu->do_not_enhance();
        }
        return $this->render($menu);
    }

    /**
     * Renders an action menu component.
     *
     * ARIA references:
     *   - http://www.w3.org/WAI/GL/wiki/Using_ARIA_menus
     *   - http://stackoverflow.com/questions/12279113/recommended-wai-aria-implementation-for-navigation-bar-menu
     *
     * @param action_menu $menu
     * @return string HTML
     */
    public function render_action_menu(action_menu $menu) {
        $menu->initialise_js($this->page);

        $output = html_writer::start_tag('div', $menu->attributes);
        $output .= html_writer::start_tag('ul', $menu->attributesprimary);
        foreach ($menu->get_primary_actions($this) as $action) {
            if ($action instanceof renderable) {
                $content = $this->render($action);
            } else {
                $content = $action;
            }
            $output .= html_writer::tag('li', $content, array('role' => 'presentation'));
        }
        $output .= html_writer::end_tag('ul');
        $output .= html_writer::start_tag('ul', $menu->attributessecondary);
        foreach ($menu->get_secondary_actions() as $action) {
            if ($action instanceof renderable) {
                $content = $this->render($action);
            } else {
                $content = $action;
            }
            $output .= html_writer::tag('li', $content, array('role' => 'presentation'));
        }
        $output .= html_writer::end_tag('ul');
        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Renders an action_menu_link item.
     *
     * @param action_menu_link $action
     * @return string HTML fragment
     */
    protected function render_action_menu_link(action_menu_link $action) {
        static $actioncount = 0;
        $actioncount++;

        $comparetoalt = '';
        $text = '';
        if (!$action->icon || $action->primary === false) {
            $text .= html_writer::start_tag('span', array('class'=>'menu-action-text', 'id' => 'actionmenuaction-'.$actioncount));
            if ($action->text instanceof renderable) {
                $text .= $this->render($action->text);
            } else {
                $text .= $action->text;
                $comparetoalt = (string)$action->text;
            }
            $text .= html_writer::end_tag('span');
        }

        $icon = '';
        if ($action->icon) {
            $icon = $action->icon;
            if ($action->primary || !$action->actionmenu->will_be_enhanced()) {
                $action->attributes['title'] = $action->text;
            }
            if (!$action->primary && $action->actionmenu->will_be_enhanced()) {
                if ((string)$icon->attributes['alt'] === $comparetoalt) {
                    $icon->attributes['alt'] = '';
                }
                if (isset($icon->attributes['title']) && (string)$icon->attributes['title'] === $comparetoalt) {
                    unset($icon->attributes['title']);
                }
            }
            $icon = $this->render($icon);
        }

        // A disabled link is rendered as formatted text.
        if (!empty($action->attributes['disabled'])) {
            // Do not use div here due to nesting restriction in xhtml strict.
            return html_writer::tag('span', $icon.$text, array('class'=>'currentlink', 'role' => 'menuitem'));
        }

        $attributes = $action->attributes;
        unset($action->attributes['disabled']);
        $attributes['href'] = $action->url;
        if ($text !== '') {
            $attributes['aria-labelledby'] = 'actionmenuaction-'.$actioncount;
        }

        return html_writer::tag('a', $icon.$text, $attributes);
    }

    /**
     * Renders a primary action_menu_filler item.
     *
     * @param action_menu_link_filler $action
     * @return string HTML fragment
     */
    protected function render_action_menu_filler(action_menu_filler $action) {
        return html_writer::span('&nbsp;', 'filler');
    }

    /**
     * Renders a primary action_menu_link item.
     *
     * @param action_menu_link_primary $action
     * @return string HTML fragment
     */
    protected function render_action_menu_link_primary(action_menu_link_primary $action) {
        return $this->render_action_menu_link($action);
    }

    /**
     * Renders a secondary action_menu_link item.
     *
     * @param action_menu_link_secondary $action
     * @return string HTML fragment
     */
    protected function render_action_menu_link_secondary(action_menu_link_secondary $action) {
        return $this->render_action_menu_link($action);
    }

    /**
     * Prints a nice side block with an optional header.
     *
     * The content is described
     * by a {@link core_renderer::block_contents} object.
     *
     * <div id="inst{$instanceid}" class="block_{$blockname} block">
     *      <div class="header"></div>
     *      <div class="content">
     *          ...CONTENT...
     *          <div class="footer">
     *          </div>
     *      </div>
     *      <div class="annotation">
     *      </div>
     * </div>
     *
     * @param block_contents $bc HTML for the content
     * @param string $region the region the block is appearing in.
     * @return string the HTML to be output.
     */
    public function block(block_contents $bc, $region) {
        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }
        if (!empty($bc->blockinstanceid)) {
            $bc->attributes['data-instanceid'] = $bc->blockinstanceid;
        }
        $skiptitle = strip_tags($bc->title);
        if ($bc->blockinstanceid && !empty($skiptitle)) {
            $bc->attributes['aria-labelledby'] = 'instance-'.$bc->blockinstanceid.'-header';
        } else if (!empty($bc->arialabel)) {
            $bc->attributes['aria-label'] = $bc->arialabel;
        }
        if ($bc->dockable) {
            $bc->attributes['data-dockable'] = 1;
        }
        if ($bc->collapsible == block_contents::HIDDEN) {
            $bc->add_class('hidden');
        }
        if (!empty($bc->controls)) {
            $bc->add_class('block_with_controls');
        }


        if (empty($skiptitle)) {
            $output = '';
            $skipdest = '';
        } else {
            $output = html_writer::tag('a', get_string('skipa', 'access', $skiptitle), array('href' => '#sb-' . $bc->skipid, 'class' => 'skip-block'));
            $skipdest = html_writer::tag('span', '', array('id' => 'sb-' . $bc->skipid, 'class' => 'skip-block-to'));
        }

        $output .= html_writer::start_tag('div', $bc->attributes);

        $output .= $this->block_header($bc);
        $output .= $this->block_content($bc);

        $output .= html_writer::end_tag('div');

        $output .= $this->block_annotation($bc);

        $output .= $skipdest;

        $this->init_block_hider_js($bc);
        return $output;
    }

    /**
     * Produces a header for a block
     *
     * @param block_contents $bc
     * @return string
     */
    protected function block_header(block_contents $bc) {

        $title = '';
        if ($bc->title) {
            $attributes = array();
            if ($bc->blockinstanceid) {
                $attributes['id'] = 'instance-'.$bc->blockinstanceid.'-header';
            }
            $title = html_writer::tag('h2', $bc->title, $attributes);
        }

        $blockid = null;
        if (isset($bc->attributes['id'])) {
            $blockid = $bc->attributes['id'];
        }
        $controlshtml = $this->block_controls($bc->controls, $blockid);

        $output = '';
        if ($title || $controlshtml) {
            $output .= html_writer::tag('div', html_writer::tag('div', html_writer::tag('div', '', array('class'=>'block_action')). $title . $controlshtml, array('class' => 'title')), array('class' => 'header'));
        }
        return $output;
    }

    /**
     * Produces the content area for a block
     *
     * @param block_contents $bc
     * @return string
     */
    protected function block_content(block_contents $bc) {
        $output = html_writer::start_tag('div', array('class' => 'content'));
        if (!$bc->title && !$this->block_controls($bc->controls)) {
            $output .= html_writer::tag('div', '', array('class'=>'block_action notitle'));
        }
        $output .= $bc->content;
        $output .= $this->block_footer($bc);
        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * Produces the footer for a block
     *
     * @param block_contents $bc
     * @return string
     */
    protected function block_footer(block_contents $bc) {
        $output = '';
        if ($bc->footer) {
            $output .= html_writer::tag('div', $bc->footer, array('class' => 'footer'));
        }
        return $output;
    }

    /**
     * Produces the annotation for a block
     *
     * @param block_contents $bc
     * @return string
     */
    protected function block_annotation(block_contents $bc) {
        $output = '';
        if ($bc->annotation) {
            $output .= html_writer::tag('div', $bc->annotation, array('class' => 'blockannotation'));
        }
        return $output;
    }

    /**
     * Calls the JS require function to hide a block.
     *
     * @param block_contents $bc A block_contents object
     */
    protected function init_block_hider_js(block_contents $bc) {
        if (!empty($bc->attributes['id']) and $bc->collapsible != block_contents::NOT_HIDEABLE) {
            $config = new stdClass;
            $config->id = $bc->attributes['id'];
            $config->title = strip_tags($bc->title);
            $config->preference = 'block' . $bc->blockinstanceid . 'hidden';
            $config->tooltipVisible = get_string('hideblocka', 'access', $config->title);
            $config->tooltipHidden = get_string('showblocka', 'access', $config->title);

            $this->page->requires->js_init_call('M.util.init_block_hider', array($config));
            user_preference_allow_ajax_update($config->preference, PARAM_BOOL);
        }
    }

    /**
     * Render the contents of a block_list.
     *
     * @param array $icons the icon for each item.
     * @param array $items the content of each item.
     * @return string HTML
     */
    public function list_block_contents($icons, $items) {
        $row = 0;
        $lis = array();
        foreach ($items as $key => $string) {
            $item = html_writer::start_tag('li', array('class' => 'r' . $row));
            if (!empty($icons[$key])) { //test if the content has an assigned icon
                $item .= html_writer::tag('div', $icons[$key], array('class' => 'icon column c0'));
            }
            $item .= html_writer::tag('div', $string, array('class' => 'column c1'));
            $item .= html_writer::end_tag('li');
            $lis[] = $item;
            $row = 1 - $row; // Flip even/odd.
        }
        return html_writer::tag('ul', implode("\n", $lis), array('class' => 'unlist'));
    }

    /**
     * Output all the blocks in a particular region.
     *
     * @param string $region the name of a region on this page.
     * @return string the HTML to be output.
     */
    public function blocks_for_region($region) {
        $blockcontents = $this->page->blocks->get_content_for_region($region, $this);
        $blocks = $this->page->blocks->get_blocks_for_region($region);
        $lastblock = null;
        $zones = array();
        foreach ($blocks as $block) {
            $zones[] = $block->title;
        }
        $output = '';

        foreach ($blockcontents as $bc) {
            if ($bc instanceof block_contents) {
                $output .= $this->block($bc, $region);
                $lastblock = $bc->title;
            } else if ($bc instanceof block_move_target) {
                $output .= $this->block_move_target($bc, $zones, $lastblock, $region);
            } else {
                throw new coding_exception('Unexpected type of thing (' . get_class($bc) . ') found in list of block contents.');
            }
        }
        return $output;
    }

    /**
     * Output a place where the block that is currently being moved can be dropped.
     *
     * @param block_move_target $target with the necessary details.
     * @param array $zones array of areas where the block can be moved to
     * @param string $previous the block located before the area currently being rendered.
     * @param string $region the name of the region
     * @return string the HTML to be output.
     */
    public function block_move_target($target, $zones, $previous, $region) {
        if ($previous == null) {
            if (empty($zones)) {
                // There are no zones, probably because there are no blocks.
                $regions = $this->page->theme->get_all_block_regions();
                $position = get_string('moveblockinregion', 'block', $regions[$region]);
            } else {
                $position = get_string('moveblockbefore', 'block', $zones[0]);
            }
        } else {
            $position = get_string('moveblockafter', 'block', $previous);
        }
        return html_writer::tag('a', html_writer::tag('span', $position, array('class' => 'accesshide')), array('href' => $target->url, 'class' => 'blockmovetarget'));
    }

    /**
     * Renders a special html link with attached action
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_action_link()} instead.
     *
     * @param string|moodle_url $url
     * @param string $text HTML fragment
     * @param component_action $action
     * @param array $attributes associative array of html link attributes + disabled
     * @param pix_icon optional pix icon to render with the link
     * @return string HTML fragment
     */
    public function action_link($url, $text, component_action $action = null, array $attributes = null, $icon = null) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $link = new action_link($url, $text, $action, $attributes, $icon);

        return $this->render($link);
    }

    /**
     * Renders an action_link object.
     *
     * The provided link is renderer and the HTML returned. At the same time the
     * associated actions are setup in JS by {@link core_renderer::add_action_handler()}
     *
     * @param action_link $link
     * @return string HTML fragment
     */
    protected function render_action_link(action_link $link) {
        global $CFG;

        $text = '';
        if ($link->icon) {
            $text .= $this->render($link->icon);
        }

        if ($link->text instanceof renderable) {
            $text .= $this->render($link->text);
        } else {
            $text .= $link->text;
        }

        // A disabled link is rendered as formatted text
        if (!empty($link->attributes['disabled'])) {
            // do not use div here due to nesting restriction in xhtml strict
            return html_writer::tag('span', $text, array('class'=>'currentlink'));
        }

        $attributes = $link->attributes;
        unset($link->attributes['disabled']);
        $attributes['href'] = $link->url;

        if ($link->actions) {
            if (empty($attributes['id'])) {
                $id = html_writer::random_id('action_link');
                $attributes['id'] = $id;
            } else {
                $id = $attributes['id'];
            }
            foreach ($link->actions as $action) {
                $this->add_action_handler($action, $id);
            }
        }

        return html_writer::tag('a', $text, $attributes);
    }


    /**
     * Renders an action_icon.
     *
     * This function uses the {@link core_renderer::action_link()} method for the
     * most part. What it does different is prepare the icon as HTML and use it
     * as the link text.
     *
     * Theme developers: If you want to change how action links and/or icons are rendered,
     * consider overriding function {@link core_renderer::render_action_link()} and
     * {@link core_renderer::render_pix_icon()}.
     *
     * @param string|moodle_url $url A string URL or moodel_url
     * @param pix_icon $pixicon
     * @param component_action $action
     * @param array $attributes associative array of html link attributes + disabled
     * @param bool $linktext show title next to image in link
     * @return string HTML fragment
     */
    public function action_icon($url, pix_icon $pixicon, component_action $action = null, array $attributes = null, $linktext=false) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $attributes = (array)$attributes;

        if (empty($attributes['class'])) {
            // let ppl override the class via $options
            $attributes['class'] = 'action-icon';
        }

        $icon = $this->render($pixicon);

        if ($linktext) {
            $text = $pixicon->attributes['alt'];
        } else {
            $text = '';
        }

        return $this->action_link($url, $text.$icon, $action, $attributes);
    }

   /**
    * Print a message along with button choices for Continue/Cancel
    *
    * If a string or moodle_url is given instead of a single_button, method defaults to post.
    *
    * @param string $message The question to ask the user
    * @param single_button|moodle_url|string $continue The single_button component representing the Continue answer. Can also be a moodle_url or string URL
    * @param single_button|moodle_url|string $cancel The single_button component representing the Cancel answer. Can also be a moodle_url or string URL
    * @return string HTML fragment
    */
    public function confirm($message, $continue, $cancel) {
        if ($continue instanceof single_button) {
            // ok
        } else if (is_string($continue)) {
            $continue = new single_button(new moodle_url($continue), get_string('continue'), 'post');
        } else if ($continue instanceof moodle_url) {
            $continue = new single_button($continue, get_string('continue'), 'post');
        } else {
            throw new coding_exception('The continue param to $OUTPUT->confirm() must be either a URL (string/moodle_url) or a single_button instance.');
        }

        if ($cancel instanceof single_button) {
            // ok
        } else if (is_string($cancel)) {
            $cancel = new single_button(new moodle_url($cancel), get_string('cancel'), 'get');
        } else if ($cancel instanceof moodle_url) {
            $cancel = new single_button($cancel, get_string('cancel'), 'get');
        } else {
            throw new coding_exception('The cancel param to $OUTPUT->confirm() must be either a URL (string/moodle_url) or a single_button instance.');
        }

        $output = $this->box_start('generalbox', 'notice');
        $output .= html_writer::tag('p', $message);
        $output .= html_writer::tag('div', $this->render($continue) . $this->render($cancel), array('class' => 'buttons'));
        $output .= $this->box_end();
        return $output;
    }

    /**
     * Returns a form with a single button.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_single_button()} instead.
     *
     * @param string|moodle_url $url
     * @param string $label button text
     * @param string $method get or post submit method
     * @param array $options associative array {disabled, title, etc.}
     * @return string HTML fragment
     */
    public function single_button($url, $label, $method='post', array $options=null) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $button = new single_button($url, $label, $method);

        foreach ((array)$options as $key=>$value) {
            if (array_key_exists($key, $button)) {
                $button->$key = $value;
            }
        }

        return $this->render($button);
    }

    /**
     * Renders a single button widget.
     *
     * This will return HTML to display a form containing a single button.
     *
     * @param single_button $button
     * @return string HTML fragment
     */
    protected function render_single_button(single_button $button) {
        $attributes = array('type'     => 'submit',
                            'value'    => $button->label,
                            'disabled' => $button->disabled ? 'disabled' : null,
                            'title'    => $button->tooltip);

        if ($button->actions) {
            $id = html_writer::random_id('single_button');
            $attributes['id'] = $id;
            foreach ($button->actions as $action) {
                $this->add_action_handler($action, $id);
            }
        }

        // first the input element
        $output = html_writer::empty_tag('input', $attributes);

        // then hidden fields
        $params = $button->url->params();
        if ($button->method === 'post') {
            $params['sesskey'] = sesskey();
        }
        foreach ($params as $var => $val) {
            $output .= html_writer::empty_tag('input', array('type' => 'hidden', 'name' => $var, 'value' => $val));
        }

        // then div wrapper for xhtml strictness
        $output = html_writer::tag('div', $output);

        // now the form itself around it
        if ($button->method === 'get') {
            $url = $button->url->out_omit_querystring(true); // url without params, the anchor part allowed
        } else {
            $url = $button->url->out_omit_querystring();     // url without params, the anchor part not allowed
        }
        if ($url === '') {
            $url = '#'; // there has to be always some action
        }
        $attributes = array('method' => $button->method,
                            'action' => $url,
                            'id'     => $button->formid);
        $output = html_writer::tag('form', $output, $attributes);

        // and finally one more wrapper with class
        return html_writer::tag('div', $output, array('class' => $button->class));
    }

    /**
     * Returns a form with a single select widget.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_single_select()} instead.
     *
     * @param moodle_url $url form action target, includes hidden fields
     * @param string $name name of selection field - the changing parameter in url
     * @param array $options list of options
     * @param string $selected selected element
     * @param array $nothing
     * @param string $formid
     * @return string HTML fragment
     */
    public function single_select($url, $name, array $options, $selected = '', $nothing = array('' => 'choosedots'), $formid = null) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $select = new single_select($url, $name, $options, $selected, $nothing, $formid);

        return $this->render($select);
    }

    /**
     * Internal implementation of single_select rendering
     *
     * @param single_select $select
     * @return string HTML fragment
     */
    protected function render_single_select(single_select $select) {
        $select = clone($select);
        if (empty($select->formid)) {
            $select->formid = html_writer::random_id('single_select_f');
        }

        $output = '';
        $params = $select->url->params();
        if ($select->method === 'post') {
            $params['sesskey'] = sesskey();
        }
        foreach ($params as $name=>$value) {
            $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>$name, 'value'=>$value));
        }

        if (empty($select->attributes['id'])) {
            $select->attributes['id'] = html_writer::random_id('single_select');
        }

        if ($select->disabled) {
            $select->attributes['disabled'] = 'disabled';
        }

        if ($select->tooltip) {
            $select->attributes['title'] = $select->tooltip;
        }

        $select->attributes['class'] = 'autosubmit';
        if ($select->class) {
            $select->attributes['class'] .= ' ' . $select->class;
        }

        if ($select->label) {
            $output .= html_writer::label($select->label, $select->attributes['id'], false, $select->labelattributes);
        }

        if ($select->helpicon instanceof help_icon) {
            $output .= $this->render($select->helpicon);
        }
        $output .= html_writer::select($select->options, $select->name, $select->selected, $select->nothing, $select->attributes);

        $go = html_writer::empty_tag('input', array('type'=>'submit', 'value'=>get_string('go')));
        $output .= html_writer::tag('noscript', html_writer::tag('div', $go), array('class' => 'inline'));

        $nothing = empty($select->nothing) ? false : key($select->nothing);
        $this->page->requires->yui_module('moodle-core-formautosubmit',
            'M.core.init_formautosubmit',
            array(array('selectid' => $select->attributes['id'], 'nothing' => $nothing))
        );

        // then div wrapper for xhtml strictness
        $output = html_writer::tag('div', $output);

        // now the form itself around it
        if ($select->method === 'get') {
            $url = $select->url->out_omit_querystring(true); // url without params, the anchor part allowed
        } else {
            $url = $select->url->out_omit_querystring();     // url without params, the anchor part not allowed
        }
        $formattributes = array('method' => $select->method,
                                'action' => $url,
                                'id'     => $select->formid);
        $output = html_writer::tag('form', $output, $formattributes);

        // and finally one more wrapper with class
        return html_writer::tag('div', $output, array('class' => $select->class));
    }

    /**
     * Returns a form with a url select widget.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_url_select()} instead.
     *
     * @param array $urls list of urls - array('/course/view.php?id=1'=>'Frontpage', ....)
     * @param string $selected selected element
     * @param array $nothing
     * @param string $formid
     * @return string HTML fragment
     */
    public function url_select(array $urls, $selected, $nothing = array('' => 'choosedots'), $formid = null) {
        $select = new url_select($urls, $selected, $nothing, $formid);
        return $this->render($select);
    }

    /**
     * Internal implementation of url_select rendering
     *
     * @param url_select $select
     * @return string HTML fragment
     */
    protected function render_url_select(url_select $select) {
        global $CFG;

        $select = clone($select);
        if (empty($select->formid)) {
            $select->formid = html_writer::random_id('url_select_f');
        }

        if (empty($select->attributes['id'])) {
            $select->attributes['id'] = html_writer::random_id('url_select');
        }

        if ($select->disabled) {
            $select->attributes['disabled'] = 'disabled';
        }

        if ($select->tooltip) {
            $select->attributes['title'] = $select->tooltip;
        }

        $output = '';

        if ($select->label) {
            $output .= html_writer::label($select->label, $select->attributes['id'], false, $select->labelattributes);
        }

        $classes = array();
        if (!$select->showbutton) {
            $classes[] = 'autosubmit';
        }
        if ($select->class) {
            $classes[] = $select->class;
        }
        if (count($classes)) {
            $select->attributes['class'] = implode(' ', $classes);
        }

        if ($select->helpicon instanceof help_icon) {
            $output .= $this->render($select->helpicon);
        }

        // For security reasons, the script course/jumpto.php requires URL starting with '/'. To keep
        // backward compatibility, we are removing heading $CFG->wwwroot from URLs here.
        $urls = array();
        foreach ($select->urls as $k=>$v) {
            if (is_array($v)) {
                // optgroup structure
                foreach ($v as $optgrouptitle => $optgroupoptions) {
                    foreach ($optgroupoptions as $optionurl => $optiontitle) {
                        if (empty($optionurl)) {
                            $safeoptionurl = '';
                        } else if (strpos($optionurl, $CFG->wwwroot.'/') === 0) {
                            // debugging('URLs passed to url_select should be in local relative form - please fix the code.', DEBUG_DEVELOPER);
                            $safeoptionurl = str_replace($CFG->wwwroot, '', $optionurl);
                        } else if (strpos($optionurl, '/') !== 0) {
                            debugging("Invalid url_select urls parameter inside optgroup: url '$optionurl' is not local relative url!");
                            continue;
                        } else {
                            $safeoptionurl = $optionurl;
                        }
                        $urls[$k][$optgrouptitle][$safeoptionurl] = $optiontitle;
                    }
                }
            } else {
                // plain list structure
                if (empty($k)) {
                    // nothing selected option
                } else if (strpos($k, $CFG->wwwroot.'/') === 0) {
                    $k = str_replace($CFG->wwwroot, '', $k);
                } else if (strpos($k, '/') !== 0) {
                    debugging("Invalid url_select urls parameter: url '$k' is not local relative url!");
                    continue;
                }
                $urls[$k] = $v;
            }
        }
        $selected = $select->selected;
        if (!empty($selected)) {
            if (strpos($select->selected, $CFG->wwwroot.'/') === 0) {
                $selected = str_replace($CFG->wwwroot, '', $selected);
            } else if (strpos($selected, '/') !== 0) {
                debugging("Invalid value of parameter 'selected': url '$selected' is not local relative url!");
            }
        }

        $output .= html_writer::empty_tag('input', array('type'=>'hidden', 'name'=>'sesskey', 'value'=>sesskey()));
        $output .= html_writer::select($urls, 'jump', $selected, $select->nothing, $select->attributes);

        if (!$select->showbutton) {
            $go = html_writer::empty_tag('input', array('type'=>'submit', 'value'=>get_string('go')));
            $output .= html_writer::tag('noscript', html_writer::tag('div', $go), array('class' => 'inline'));
            $nothing = empty($select->nothing) ? false : key($select->nothing);
            $this->page->requires->yui_module('moodle-core-formautosubmit',
                'M.core.init_formautosubmit',
                array(array('selectid' => $select->attributes['id'], 'nothing' => $nothing))
            );
        } else {
            $output .= html_writer::empty_tag('input', array('type'=>'submit', 'value'=>$select->showbutton));
        }

        // then div wrapper for xhtml strictness
        $output = html_writer::tag('div', $output);

        // now the form itself around it
        $formattributes = array('method' => 'post',
                                'action' => new moodle_url('/course/jumpto.php'),
                                'id'     => $select->formid);
        $output = html_writer::tag('form', $output, $formattributes);

        // and finally one more wrapper with class
        return html_writer::tag('div', $output, array('class' => $select->class));
    }

    /**
     * Returns a string containing a link to the user documentation.
     * Also contains an icon by default. Shown to teachers and admin only.
     *
     * @param string $path The page link after doc root and language, no leading slash.
     * @param string $text The text to be displayed for the link
     * @param boolean $forcepopup Whether to force a popup regardless of the value of $CFG->doctonewwindow
     * @return string
     */
    public function doc_link($path, $text = '', $forcepopup = false) {
        global $CFG;

        $icon = $this->pix_icon('docs', '', 'moodle', array('class'=>'iconhelp icon-pre', 'role'=>'presentation'));

        $url = new moodle_url(get_docs_url($path));

        $attributes = array('href'=>$url);
        if (!empty($CFG->doctonewwindow) || $forcepopup) {
            $attributes['class'] = 'helplinkpopup';
        }

        return html_writer::tag('a', $icon.$text, $attributes);
    }

    /**
     * Return HTML for a pix_icon.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_pix_icon()} instead.
     *
     * @param string $pix short pix name
     * @param string $alt mandatory alt attribute
     * @param string $component standard compoennt name like 'moodle', 'mod_forum', etc.
     * @param array $attributes htm lattributes
     * @return string HTML fragment
     */
    public function pix_icon($pix, $alt, $component='moodle', array $attributes = null) {
        $icon = new pix_icon($pix, $alt, $component, $attributes);
        return $this->render($icon);
    }

    /**
     * Renders a pix_icon widget and returns the HTML to display it.
     *
     * @param pix_icon $icon
     * @return string HTML fragment
     */
    protected function render_pix_icon(pix_icon $icon) {
        $data = $icon->export_for_template($this);
        return $this->render_from_template('core/pix_icon', $data);
    }

    /**
     * Return HTML to display an emoticon icon.
     *
     * @param pix_emoticon $emoticon
     * @return string HTML fragment
     */
    protected function render_pix_emoticon(pix_emoticon $emoticon) {
        $attributes = $emoticon->attributes;
        $attributes['src'] = $this->pix_url($emoticon->pix, $emoticon->component);
        return html_writer::empty_tag('img', $attributes);
    }

    /**
     * Produces the html that represents this rating in the UI
     *
     * @param rating $rating the page object on which this rating will appear
     * @return string
     */
    function render_rating(rating $rating) {
        global $CFG, $USER;

        if ($rating->settings->aggregationmethod == RATING_AGGREGATE_NONE) {
            return null;//ratings are turned off
        }

        $ratingmanager = new rating_manager();
        // Initialise the JavaScript so ratings can be done by AJAX.
        $ratingmanager->initialise_rating_javascript($this->page);

        $strrate = get_string("rate", "rating");
        $ratinghtml = ''; //the string we'll return

        // permissions check - can they view the aggregate?
        if ($rating->user_can_view_aggregate()) {

            $aggregatelabel = $ratingmanager->get_aggregate_label($rating->settings->aggregationmethod);
            $aggregatestr   = $rating->get_aggregate_string();

            $aggregatehtml  = html_writer::tag('span', $aggregatestr, array('id' => 'ratingaggregate'.$rating->itemid, 'class' => 'ratingaggregate')).' ';
            if ($rating->count > 0) {
                $countstr = "({$rating->count})";
            } else {
                $countstr = '-';
            }
            $aggregatehtml .= html_writer::tag('span', $countstr, array('id'=>"ratingcount{$rating->itemid}", 'class' => 'ratingcount')).' ';

            $ratinghtml .= html_writer::tag('span', $aggregatelabel, array('class'=>'rating-aggregate-label'));
            if ($rating->settings->permissions->viewall && $rating->settings->pluginpermissions->viewall) {

                $nonpopuplink = $rating->get_view_ratings_url();
                $popuplink = $rating->get_view_ratings_url(true);

                $action = new popup_action('click', $popuplink, 'ratings', array('height' => 400, 'width' => 600));
                $ratinghtml .= $this->action_link($nonpopuplink, $aggregatehtml, $action);
            } else {
                $ratinghtml .= $aggregatehtml;
            }
        }

        $formstart = null;
        // if the item doesn't belong to the current user, the user has permission to rate
        // and we're within the assessable period
        if ($rating->user_can_rate()) {

            $rateurl = $rating->get_rate_url();
            $inputs = $rateurl->params();

            //start the rating form
            $formattrs = array(
                'id'     => "postrating{$rating->itemid}",
                'class'  => 'postratingform',
                'method' => 'post',
                'action' => $rateurl->out_omit_querystring()
            );
            $formstart  = html_writer::start_tag('form', $formattrs);
            $formstart .= html_writer::start_tag('div', array('class' => 'ratingform'));

            // add the hidden inputs
            foreach ($inputs as $name => $value) {
                $attributes = array('type' => 'hidden', 'class' => 'ratinginput', 'name' => $name, 'value' => $value);
                $formstart .= html_writer::empty_tag('input', $attributes);
            }

            if (empty($ratinghtml)) {
                $ratinghtml .= $strrate.': ';
            }
            $ratinghtml = $formstart.$ratinghtml;

            $scalearray = array(RATING_UNSET_RATING => $strrate.'...') + $rating->settings->scale->scaleitems;
            $scaleattrs = array('class'=>'postratingmenu ratinginput','id'=>'menurating'.$rating->itemid);
            $ratinghtml .= html_writer::label($rating->rating, 'menurating'.$rating->itemid, false, array('class' => 'accesshide'));
            $ratinghtml .= html_writer::select($scalearray, 'rating', $rating->rating, false, $scaleattrs);

            //output submit button
            $ratinghtml .= html_writer::start_tag('span', array('class'=>"ratingsubmit"));

            $attributes = array('type' => 'submit', 'class' => 'postratingmenusubmit', 'id' => 'postratingsubmit'.$rating->itemid, 'value' => s(get_string('rate', 'rating')));
            $ratinghtml .= html_writer::empty_tag('input', $attributes);

            if (!$rating->settings->scale->isnumeric) {
                // If a global scale, try to find current course ID from the context
                if (empty($rating->settings->scale->courseid) and $coursecontext = $rating->context->get_course_context(false)) {
                    $courseid = $coursecontext->instanceid;
                } else {
                    $courseid = $rating->settings->scale->courseid;
                }
                $ratinghtml .= $this->help_icon_scale($courseid, $rating->settings->scale);
            }
            $ratinghtml .= html_writer::end_tag('span');
            $ratinghtml .= html_writer::end_tag('div');
            $ratinghtml .= html_writer::end_tag('form');
        }

        return $ratinghtml;
    }

    /**
     * Centered heading with attached help button (same title text)
     * and optional icon attached.
     *
     * @param string $text A heading text
     * @param string $helpidentifier The keyword that defines a help page
     * @param string $component component name
     * @param string|moodle_url $icon
     * @param string $iconalt icon alt text
     * @param int $level The level of importance of the heading. Defaulting to 2
     * @param string $classnames A space-separated list of CSS classes. Defaulting to null
     * @return string HTML fragment
     */
    public function heading_with_help($text, $helpidentifier, $component = 'moodle', $icon = '', $iconalt = '', $level = 2, $classnames = null) {
        $image = '';
        if ($icon) {
            $image = $this->pix_icon($icon, $iconalt, $component, array('class'=>'icon iconlarge'));
        }

        $help = '';
        if ($helpidentifier) {
            $help = $this->help_icon($helpidentifier, $component);
        }

        return $this->heading($image.$text.$help, $level, $classnames);
    }

    /**
     * Returns HTML to display a help icon.
     *
     * @deprecated since Moodle 2.0
     */
    public function old_help_icon($helpidentifier, $title, $component = 'moodle', $linktext = '') {
        throw new coding_exception('old_help_icon() can not be used any more, please see help_icon().');
    }

    /**
     * Returns HTML to display a help icon.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_help_icon()} instead.
     *
     * @param string $identifier The keyword that defines a help page
     * @param string $component component name
     * @param string|bool $linktext true means use $title as link text, string means link text value
     * @return string HTML fragment
     */
    public function help_icon($identifier, $component = 'moodle', $linktext = '') {
        $icon = new help_icon($identifier, $component);
        $icon->diag_strings();
        if ($linktext === true) {
            $icon->linktext = get_string($icon->identifier, $icon->component);
        } else if (!empty($linktext)) {
            $icon->linktext = $linktext;
        }
        return $this->render($icon);
    }

    /**
     * Implementation of user image rendering.
     *
     * @param help_icon $helpicon A help icon instance
     * @return string HTML fragment
     */
    protected function render_help_icon(help_icon $helpicon) {
        global $CFG;

        // first get the help image icon
        $src = $this->pix_url('help');

        $title = get_string($helpicon->identifier, $helpicon->component);

        if (empty($helpicon->linktext)) {
            $alt = get_string('helpprefix2', '', trim($title, ". \t"));
        } else {
            $alt = get_string('helpwiththis');
        }

        $attributes = array('src'=>$src, 'alt'=>$alt, 'class'=>'iconhelp');
        $output = html_writer::empty_tag('img', $attributes);

        // add the link text if given
        if (!empty($helpicon->linktext)) {
            // the spacing has to be done through CSS
            $output .= $helpicon->linktext;
        }

        // now create the link around it - we need https on loginhttps pages
        $url = new moodle_url($CFG->httpswwwroot.'/help.php', array('component' => $helpicon->component, 'identifier' => $helpicon->identifier, 'lang'=>current_language()));

        // note: this title is displayed only if JS is disabled, otherwise the link will have the new ajax tooltip
        $title = get_string('helpprefix2', '', trim($title, ". \t"));

        $attributes = array('href' => $url, 'title' => $title, 'aria-haspopup' => 'true', 'target'=>'_blank');
        $output = html_writer::tag('a', $output, $attributes);

        // and finally span
        return html_writer::tag('span', $output, array('class' => 'helptooltip'));
    }

    /**
     * Returns HTML to display a scale help icon.
     *
     * @param int $courseid
     * @param stdClass $scale instance
     * @return string HTML fragment
     */
    public function help_icon_scale($courseid, stdClass $scale) {
        global $CFG;

        $title = get_string('helpprefix2', '', $scale->name) .' ('.get_string('newwindow').')';

        $icon = $this->pix_icon('help', get_string('scales'), 'moodle', array('class'=>'iconhelp'));

        $scaleid = abs($scale->id);

        $link = new moodle_url('/course/scales.php', array('id' => $courseid, 'list' => true, 'scaleid' => $scaleid));
        $action = new popup_action('click', $link, 'ratingscale');

        return html_writer::tag('span', $this->action_link($link, $icon, $action), array('class' => 'helplink'));
    }

    /**
     * Creates and returns a spacer image with optional line break.
     *
     * @param array $attributes Any HTML attributes to add to the spaced.
     * @param bool $br Include a BR after the spacer.... DON'T USE THIS. Don't be
     *     laxy do it with CSS which is a much better solution.
     * @return string HTML fragment
     */
    public function spacer(array $attributes = null, $br = false) {
        $attributes = (array)$attributes;
        if (empty($attributes['width'])) {
            $attributes['width'] = 1;
        }
        if (empty($attributes['height'])) {
            $attributes['height'] = 1;
        }
        $attributes['class'] = 'spacer';

        $output = $this->pix_icon('spacer', '', 'moodle', $attributes);

        if (!empty($br)) {
            $output .= '<br />';
        }

        return $output;
    }

    /**
     * Returns HTML to display the specified user's avatar.
     *
     * User avatar may be obtained in two ways:
     * <pre>
     * // Option 1: (shortcut for simple cases, preferred way)
     * // $user has come from the DB and has fields id, picture, imagealt, firstname and lastname
     * $OUTPUT->user_picture($user, array('popup'=>true));
     *
     * // Option 2:
     * $userpic = new user_picture($user);
     * // Set properties of $userpic
     * $userpic->popup = true;
     * $OUTPUT->render($userpic);
     * </pre>
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_user_picture()} instead.
     *
     * @param stdClass $user Object with at least fields id, picture, imagealt, firstname, lastname
     *     If any of these are missing, the database is queried. Avoid this
     *     if at all possible, particularly for reports. It is very bad for performance.
     * @param array $options associative array with user picture options, used only if not a user_picture object,
     *     options are:
     *     - courseid=$this->page->course->id (course id of user profile in link)
     *     - size=35 (size of image)
     *     - link=true (make image clickable - the link leads to user profile)
     *     - popup=false (open in popup)
     *     - alttext=true (add image alt attribute)
     *     - class = image class attribute (default 'userpicture')
     *     - visibletoscreenreaders=true (whether to be visible to screen readers)
     * @return string HTML fragment
     */
    public function user_picture(stdClass $user, array $options = null) {
        $userpicture = new user_picture($user);
        foreach ((array)$options as $key=>$value) {
            if (array_key_exists($key, $userpicture)) {
                $userpicture->$key = $value;
            }
        }
        return $this->render($userpicture);
    }

    /**
     * Internal implementation of user image rendering.
     *
     * @param user_picture $userpicture
     * @return string
     */
    protected function render_user_picture(user_picture $userpicture) {
        global $CFG, $DB;

        $user = $userpicture->user;

        if ($userpicture->alttext) {
            if (!empty($user->imagealt)) {
                $alt = $user->imagealt;
            } else {
                $alt = get_string('pictureof', '', fullname($user));
            }
        } else {
            $alt = '';
        }

        if (empty($userpicture->size)) {
            $size = 35;
        } else if ($userpicture->size === true or $userpicture->size == 1) {
            $size = 100;
        } else {
            $size = $userpicture->size;
        }

        $class = $userpicture->class;

        if ($user->picture == 0) {
            $class .= ' defaultuserpic';
        }

        $src = $userpicture->get_url($this->page, $this);

        $attributes = array('src'=>$src, 'alt'=>$alt, 'title'=>$alt, 'class'=>$class, 'width'=>$size, 'height'=>$size);
        if (!$userpicture->visibletoscreenreaders) {
            $attributes['role'] = 'presentation';
        }

        // get the image html output fisrt
        $output = html_writer::empty_tag('img', $attributes);

        // then wrap it in link if needed
        if (!$userpicture->link) {
            return $output;
        }

        if (empty($userpicture->courseid)) {
            $courseid = $this->page->course->id;
        } else {
            $courseid = $userpicture->courseid;
        }

        if ($courseid == SITEID) {
            $url = new moodle_url('/user/profile.php', array('id' => $user->id));
        } else {
            $url = new moodle_url('/user/view.php', array('id' => $user->id, 'course' => $courseid));
        }

        $attributes = array('href'=>$url);
        if (!$userpicture->visibletoscreenreaders) {
            $attributes['tabindex'] = '-1';
            $attributes['aria-hidden'] = 'true';
        }

        if ($userpicture->popup) {
            $id = html_writer::random_id('userpicture');
            $attributes['id'] = $id;
            $this->add_action_handler(new popup_action('click', $url), $id);
        }

        return html_writer::tag('a', $output, $attributes);
    }

    /**
     * Internal implementation of file tree viewer items rendering.
     *
     * @param array $dir
     * @return string
     */
    public function htmllize_file_tree($dir) {
        if (empty($dir['subdirs']) and empty($dir['files'])) {
            return '';
        }
        $result = '<ul>';
        foreach ($dir['subdirs'] as $subdir) {
            $result .= '<li>'.s($subdir['dirname']).' '.$this->htmllize_file_tree($subdir).'</li>';
        }
        foreach ($dir['files'] as $file) {
            $filename = $file->get_filename();
            $result .= '<li><span>'.html_writer::link($file->fileurl, $filename).'</span></li>';
        }
        $result .= '</ul>';

        return $result;
    }

    /**
     * Returns HTML to display the file picker
     *
     * <pre>
     * $OUTPUT->file_picker($options);
     * </pre>
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_file_picker()} instead.
     *
     * @param array $options associative array with file manager options
     *   options are:
     *       maxbytes=>-1,
     *       itemid=>0,
     *       client_id=>uniqid(),
     *       acepted_types=>'*',
     *       return_types=>FILE_INTERNAL,
     *       context=>$PAGE->context
     * @return string HTML fragment
     */
    public function file_picker($options) {
        $fp = new file_picker($options);
        return $this->render($fp);
    }

    /**
     * Internal implementation of file picker rendering.
     *
     * @param file_picker $fp
     * @return string
     */
    public function render_file_picker(file_picker $fp) {
        global $CFG, $OUTPUT, $USER;
        $options = $fp->options;
        $client_id = $options->client_id;
        $strsaved = get_string('filesaved', 'repository');
        $straddfile = get_string('openpicker', 'repository');
        $strloading  = get_string('loading', 'repository');
        $strdndenabled = get_string('dndenabled_inbox', 'moodle');
        $strdroptoupload = get_string('droptoupload', 'moodle');
        $icon_progress = $OUTPUT->pix_icon('i/loading_small', $strloading).'';

        $currentfile = $options->currentfile;
        if (empty($currentfile)) {
            $currentfile = '';
        } else {
            $currentfile .= ' - ';
        }
        if ($options->maxbytes) {
            $size = $options->maxbytes;
        } else {
            $size = get_max_upload_file_size();
        }
        if ($size == -1) {
            $maxsize = '';
        } else {
            $maxsize = get_string('maxfilesize', 'moodle', display_size($size));
        }
        if ($options->buttonname) {
            $buttonname = ' name="' . $options->buttonname . '"';
        } else {
            $buttonname = '';
        }
        $html = <<<EOD
<div class="filemanager-loading mdl-align" id='filepicker-loading-{$client_id}'>
$icon_progress
</div>
<div id="filepicker-wrapper-{$client_id}" class="mdl-left" style="display:none">
    <div>
        <input type="button" class="fp-btn-choose" id="filepicker-button-{$client_id}" value="{$straddfile}"{$buttonname}/>
        <span> $maxsize </span>
    </div>
EOD;
        if ($options->env != 'url') {
            $html .= <<<EOD
    <div id="file_info_{$client_id}" class="mdl-left filepicker-filelist" style="position: relative">
    <div class="filepicker-filename">
        <div class="filepicker-container">$currentfile<div class="dndupload-message">$strdndenabled <br/><div class="dndupload-arrow"></div></div></div>
        <div class="dndupload-progressbars"></div>
    </div>
    <div><div class="dndupload-target">{$strdroptoupload}<br/><div class="dndupload-arrow"></div></div></div>
    </div>
EOD;
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * Returns HTML to display the 'Update this Modulename' button that appears on module pages.
     *
     * @param string $cmid the course_module id.
     * @param string $modulename the module name, eg. "forum", "quiz" or "workshop"
     * @return string the HTML for the button, if this user has permission to edit it, else an empty string.
     */
    public function update_module_button($cmid, $modulename) {
        global $CFG;
        if (has_capability('moodle/course:manageactivities', context_module::instance($cmid))) {
            $modulename = get_string('modulename', $modulename);
            $string = get_string('updatethis', '', $modulename);
            $url = new moodle_url("$CFG->wwwroot/course/mod.php", array('update' => $cmid, 'return' => true, 'sesskey' => sesskey()));
            return $this->single_button($url, $string);
        } else {
            return '';
        }
    }

    /**
     * Returns HTML to display a "Turn editing on/off" button in a form.
     *
     * @param moodle_url $url The URL + params to send through when clicking the button
     * @return string HTML the button
     */
    public function edit_button(moodle_url $url) {

        $url->param('sesskey', sesskey());
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $editstring = get_string('turneditingoff');
        } else {
            $url->param('edit', 'on');
            $editstring = get_string('turneditingon');
        }

        return $this->single_button($url, $editstring);
    }

    /**
     * Returns HTML to display a simple button to close a window
     *
     * @param string $text The lang string for the button's label (already output from get_string())
     * @return string html fragment
     */
    public function close_window_button($text='') {
        if (empty($text)) {
            $text = get_string('closewindow');
        }
        $button = new single_button(new moodle_url('#'), $text, 'get');
        $button->add_action(new component_action('click', 'close_window'));

        return $this->container($this->render($button), 'closewindow');
    }

    /**
     * Output an error message. By default wraps the error message in <span class="error">.
     * If the error message is blank, nothing is output.
     *
     * @param string $message the error message.
     * @return string the HTML to output.
     */
    public function error_text($message) {
        if (empty($message)) {
            return '';
        }
        $message = $this->pix_icon('i/warning', get_string('error'), '', array('class' => 'icon icon-pre', 'title'=>'')) . $message;
        return html_writer::tag('span', $message, array('class' => 'error'));
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
     * @return string the HTML to output.
     */
    public function fatal_error($message, $moreinfourl, $link, $backtrace, $debuginfo = null) {
        global $CFG;

        $output = '';
        $obbuffer = '';

        if ($this->has_started()) {
            // we can not always recover properly here, we have problems with output buffering,
            // html tables, etc.
            $output .= $this->opencontainers->pop_all_but_last();

        } else {
            // It is really bad if library code throws exception when output buffering is on,
            // because the buffered text would be printed before our start of page.
            // NOTE: this hack might be behave unexpectedly in case output buffering is enabled in PHP.ini
            error_reporting(0); // disable notices from gzip compression, etc.
            while (ob_get_level() > 0) {
                $buff = ob_get_clean();
                if ($buff === false) {
                    break;
                }
                $obbuffer .= $buff;
            }
            error_reporting($CFG->debug);

            // Output not yet started.
            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');
            if (empty($_SERVER['HTTP_RANGE'])) {
                @header($protocol . ' 404 Not Found');
            } else {
                // Must stop byteserving attempts somehow,
                // this is weird but Chrome PDF viewer can be stopped only with 407!
                @header($protocol . ' 407 Proxy Authentication Required');
            }

            $this->page->set_context(null); // ugly hack - make sure page context is set to something, we do not want bogus warnings here
            $this->page->set_url('/'); // no url
            //$this->page->set_pagelayout('base'); //TODO: MDL-20676 blocks on error pages are weird, unfortunately it somehow detect the pagelayout from URL :-(
            $this->page->set_title(get_string('error'));
            $this->page->set_heading($this->page->course->fullname);
            $output .= $this->header();
        }

        $message = '<p class="errormessage">' . $message . '</p>'.
                '<p class="errorcode"><a href="' . $moreinfourl . '">' .
                get_string('moreinformation') . '</a></p>';
        if (empty($CFG->rolesactive)) {
            $message .= '<p class="errormessage">' . get_string('installproblem', 'error') . '</p>';
            //It is usually not possible to recover from errors triggered during installation, you may need to create a new database or use a different database prefix for new installation.
        }
        $output .= $this->box($message, 'errorbox', null, array('data-rel' => 'fatalerror'));

        if ($CFG->debugdeveloper) {
            if (!empty($debuginfo)) {
                $debuginfo = s($debuginfo); // removes all nasty JS
                $debuginfo = str_replace("\n", '<br />', $debuginfo); // keep newlines
                $output .= $this->notification('<strong>Debug info:</strong> '.$debuginfo, 'notifytiny');
            }
            if (!empty($backtrace)) {
                $output .= $this->notification('<strong>Stack trace:</strong> '.format_backtrace($backtrace), 'notifytiny');
            }
            if ($obbuffer !== '' ) {
                $output .= $this->notification('<strong>Output buffer:</strong> '.s($obbuffer), 'notifytiny');
            }
        }

        if (empty($CFG->rolesactive)) {
            // continue does not make much sense if moodle is not installed yet because error is most probably not recoverable
        } else if (!empty($link)) {
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

        $classmappings = array(
            'notifyproblem' => \core\output\notification::NOTIFY_PROBLEM,
            'notifytiny' => \core\output\notification::NOTIFY_PROBLEM,
            'notifysuccess' => \core\output\notification::NOTIFY_SUCCESS,
            'notifymessage' => \core\output\notification::NOTIFY_MESSAGE,
            'redirectmessage' => \core\output\notification::NOTIFY_REDIRECT
        );

        // Identify what type of notification this is.
        $type = \core\output\notification::NOTIFY_PROBLEM;
        $classarray = explode(' ', self::prepare_classes($classes));
        if (count($classarray) > 0) {
            foreach ($classarray as $class) {
                if (isset($classmappings[$class])) {
                    $type = $classmappings[$class];
                    break;
                }
            }
        }

        $n = new \core\output\notification($message, $type);
        return $this->render($n);

    }

    /**
     * Output a notification at a particular level - in this case, NOTIFY_PROBLEM.
     *
     * @param string $message the message to print out
     * @return string HTML fragment.
     */
    public function notify_problem($message) {
        $n = new \core\output\notification($message, \core\output\notification::NOTIFY_PROBLEM);
        return $this->render($n);
    }

    /**
     * Output a notification at a particular level - in this case, NOTIFY_SUCCESS.
     *
     * @param string $message the message to print out
     * @return string HTML fragment.
     */
    public function notify_success($message) {
        $n = new \core\output\notification($message, \core\output\notification::NOTIFY_SUCCESS);
        return $this->render($n);
    }

    /**
     * Output a notification at a particular level - in this case, NOTIFY_MESSAGE.
     *
     * @param string $message the message to print out
     * @return string HTML fragment.
     */
    public function notify_message($message) {
        $n = new \core\output\notification($message, \core\output\notification::NOTIFY_MESSAGE);
        return $this->render($n);
    }

    /**
     * Output a notification at a particular level - in this case, NOTIFY_REDIRECT.
     *
     * @param string $message the message to print out
     * @return string HTML fragment.
     */
    public function notify_redirect($message) {
        $n = new \core\output\notification($message, \core\output\notification::NOTIFY_REDIRECT);
        return $this->render($n);
    }

    /**
     * Render a notification (that is, a status message about something that has
     * just happened).
     *
     * @param \core\output\notification $notification the notification to print out
     * @return string the HTML to output.
     */
    protected function render_notification(\core\output\notification $notification) {

        $data = $notification->export_for_template($this);

        $templatename = '';
        switch($data->type) {
            case \core\output\notification::NOTIFY_MESSAGE:
                $templatename = 'core/notification_message';
                break;
            case \core\output\notification::NOTIFY_SUCCESS:
                $templatename = 'core/notification_success';
                break;
            case \core\output\notification::NOTIFY_PROBLEM:
                $templatename = 'core/notification_problem';
                break;
            case \core\output\notification::NOTIFY_REDIRECT:
                $templatename = 'core/notification_redirect';
                break;
            default:
                $templatename = 'core/notification_message';
                break;
        }

        return self::render_from_template($templatename, $data);

    }

    /**
     * Returns HTML to display a continue button that goes to a particular URL.
     *
     * @param string|moodle_url $url The url the button goes to.
     * @return string the HTML to output.
     */
    public function continue_button($url) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $button = new single_button($url, get_string('continue'), 'get');
        $button->class = 'continuebutton';

        return $this->render($button);
    }

    /**
     * Returns HTML to display a single paging bar to provide access to other pages  (usually in a search)
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_paging_bar()} instead.
     *
     * @param int $totalcount The total number of entries available to be paged through
     * @param int $page The page you are currently viewing
     * @param int $perpage The number of entries that should be shown per page
     * @param string|moodle_url $baseurl url of the current page, the $pagevar parameter is added
     * @param string $pagevar name of page parameter that holds the page number
     * @return string the HTML to output.
     */
    public function paging_bar($totalcount, $page, $perpage, $baseurl, $pagevar = 'page') {
        $pb = new paging_bar($totalcount, $page, $perpage, $baseurl, $pagevar);
        return $this->render($pb);
    }

    /**
     * Internal implementation of paging bar rendering.
     *
     * @param paging_bar $pagingbar
     * @return string
     */
    protected function render_paging_bar(paging_bar $pagingbar) {
        $output = '';
        $pagingbar = clone($pagingbar);
        $pagingbar->prepare($this, $this->page, $this->target);

        if ($pagingbar->totalcount > $pagingbar->perpage) {
            $output .= get_string('page') . ':';

            if (!empty($pagingbar->previouslink)) {
                $output .= ' (' . $pagingbar->previouslink . ') ';
            }

            if (!empty($pagingbar->firstlink)) {
                $output .= ' ' . $pagingbar->firstlink . ' ...';
            }

            foreach ($pagingbar->pagelinks as $link) {
                $output .= "  $link";
            }

            if (!empty($pagingbar->lastlink)) {
                $output .= ' ...' . $pagingbar->lastlink . ' ';
            }

            if (!empty($pagingbar->nextlink)) {
                $output .= '  (' . $pagingbar->nextlink . ')';
            }
        }

        return html_writer::tag('div', $output, array('class' => 'paging'));
    }

    /**
     * Output the place a skip link goes to.
     *
     * @param string $id The target name from the corresponding $PAGE->requires->skip_link_to($target) call.
     * @return string the HTML to output.
     */
    public function skip_link_target($id = null) {
        return html_writer::tag('span', '', array('id' => $id));
    }

    /**
     * Outputs a heading
     *
     * @param string $text The text of the heading
     * @param int $level The level of importance of the heading. Defaulting to 2
     * @param string $classes A space-separated list of CSS classes. Defaulting to null
     * @param string $id An optional ID
     * @return string the HTML to output.
     */
    public function heading($text, $level = 2, $classes = null, $id = null) {
        $level = (integer) $level;
        if ($level < 1 or $level > 6) {
            throw new coding_exception('Heading level must be an integer between 1 and 6.');
        }
        return html_writer::tag('h' . $level, $text, array('id' => $id, 'class' => renderer_base::prepare_classes($classes)));
    }

    /**
     * Outputs a box.
     *
     * @param string $contents The contents of the box
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @param array $attributes An array of other attributes to give the box.
     * @return string the HTML to output.
     */
    public function box($contents, $classes = 'generalbox', $id = null, $attributes = array()) {
        return $this->box_start($classes, $id, $attributes) . $contents . $this->box_end();
    }

    /**
     * Outputs the opening section of a box.
     *
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @param array $attributes An array of other attributes to give the box.
     * @return string the HTML to output.
     */
    public function box_start($classes = 'generalbox', $id = null, $attributes = array()) {
        $this->opencontainers->push('box', html_writer::end_tag('div'));
        $attributes['id'] = $id;
        $attributes['class'] = 'box ' . renderer_base::prepare_classes($classes);
        return html_writer::start_tag('div', $attributes);
    }

    /**
     * Outputs the closing section of a box.
     *
     * @return string the HTML to output.
     */
    public function box_end() {
        return $this->opencontainers->pop('box');
    }

    /**
     * Outputs a container.
     *
     * @param string $contents The contents of the box
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @return string the HTML to output.
     */
    public function container($contents, $classes = null, $id = null) {
        return $this->container_start($classes, $id) . $contents . $this->container_end();
    }

    /**
     * Outputs the opening section of a container.
     *
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @return string the HTML to output.
     */
    public function container_start($classes = null, $id = null) {
        $this->opencontainers->push('container', html_writer::end_tag('div'));
        return html_writer::start_tag('div', array('id' => $id,
                'class' => renderer_base::prepare_classes($classes)));
    }

    /**
     * Outputs the closing section of a container.
     *
     * @return string the HTML to output.
     */
    public function container_end() {
        return $this->opencontainers->pop('container');
    }

    /**
     * Make nested HTML lists out of the items
     *
     * The resulting list will look something like this:
     *
     * <pre>
     * <<ul>>
     * <<li>><div class='tree_item parent'>(item contents)</div>
     *      <<ul>
     *      <<li>><div class='tree_item'>(item contents)</div><</li>>
     *      <</ul>>
     * <</li>>
     * <</ul>>
     * </pre>
     *
     * @param array $items
     * @param array $attrs html attributes passed to the top ofs the list
     * @return string HTML
     */
    public function tree_block_contents($items, $attrs = array()) {
        // exit if empty, we don't want an empty ul element
        if (empty($items)) {
            return '';
        }
        // array of nested li elements
        $lis = array();
        foreach ($items as $item) {
            // this applies to the li item which contains all child lists too
            $content = $item->content($this);
            $liclasses = array($item->get_css_type());
            if (!$item->forceopen || (!$item->forceopen && $item->collapse) || ($item->children->count()==0  && $item->nodetype==navigation_node::NODETYPE_BRANCH)) {
                $liclasses[] = 'collapsed';
            }
            if ($item->isactive === true) {
                $liclasses[] = 'current_branch';
            }
            $liattr = array('class'=>join(' ',$liclasses));
            // class attribute on the div item which only contains the item content
            $divclasses = array('tree_item');
            if ($item->children->count()>0  || $item->nodetype==navigation_node::NODETYPE_BRANCH) {
                $divclasses[] = 'branch';
            } else {
                $divclasses[] = 'leaf';
            }
            if (!empty($item->classes) && count($item->classes)>0) {
                $divclasses[] = join(' ', $item->classes);
            }
            $divattr = array('class'=>join(' ', $divclasses));
            if (!empty($item->id)) {
                $divattr['id'] = $item->id;
            }
            $content = html_writer::tag('p', $content, $divattr) . $this->tree_block_contents($item->children);
            if (!empty($item->preceedwithhr) && $item->preceedwithhr===true) {
                $content = html_writer::empty_tag('hr') . $content;
            }
            $content = html_writer::tag('li', $content, $liattr);
            $lis[] = $content;
        }
        return html_writer::tag('ul', implode("\n", $lis), $attrs);
    }

    /**
     * Construct a user menu, returning HTML that can be echoed out by a
     * layout file.
     *
     * @param stdClass $user A user object, usually $USER.
     * @param bool $withlinks true if a dropdown should be built.
     * @return string HTML fragment.
     */
    public function user_menu($user = null, $withlinks = null) {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');

        if (is_null($user)) {
            $user = $USER;
        }

        // Note: this behaviour is intended to match that of core_renderer::login_info,
        // but should not be considered to be good practice; layout options are
        // intended to be theme-specific. Please don't copy this snippet anywhere else.
        if (is_null($withlinks)) {
            $withlinks = empty($this->page->layout_options['nologinlinks']);
        }

        // Add a class for when $withlinks is false.
        $usermenuclasses = 'usermenu';
        if (!$withlinks) {
            $usermenuclasses .= ' withoutlinks';
        }

        $returnstr = "";

        // If during initial install, return the empty return string.
        if (during_initial_install()) {
            return $returnstr;
        }

        $loginpage = $this->is_login_page();
        $loginurl = get_login_url();
        // If not logged in, show the typical not-logged-in string.
        if (!isloggedin()) {
            $returnstr = get_string('loggedinnot', 'moodle');
            if (!$loginpage) {
                $returnstr .= " (<a href=\"$loginurl\">" . get_string('login') . '</a>)';
            }
            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'login'
                ),
                $usermenuclasses
            );

        }

        // If logged in as a guest user, show a string to that effect.
        if (isguestuser()) {
            $returnstr = get_string('loggedinasguest');
            if (!$loginpage && $withlinks) {
                $returnstr .= " (<a href=\"$loginurl\">".get_string('login').'</a>)';
            }

            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'login'
                ),
                $usermenuclasses
            );
        }

        // Get some navigation opts.
        $opts = user_get_user_navigation_info($user, $this->page, $this->page->course);

        $avatarclasses = "avatars";
        $avatarcontents = html_writer::span($opts->metadata['useravatar'], 'avatar current');
        $usertextcontents = $opts->metadata['userfullname'];

        // Other user.
        if (!empty($opts->metadata['asotheruser'])) {
            $avatarcontents .= html_writer::span(
                $opts->metadata['realuseravatar'],
                'avatar realuser'
            );
            $usertextcontents = $opts->metadata['realuserfullname'];
            $usertextcontents .= html_writer::tag(
                'span',
                get_string(
                    'loggedinas',
                    'moodle',
                    html_writer::span(
                        $opts->metadata['userfullname'],
                        'value'
                    )
                ),
                array('class' => 'meta viewingas')
            );
        }

        // Role.
        if (!empty($opts->metadata['asotherrole'])) {
            $role = core_text::strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['rolename'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['rolename'],
                'meta role role-' . $role
            );
        }

        // User login failures.
        if (!empty($opts->metadata['userloginfail'])) {
            $usertextcontents .= html_writer::span(
                $opts->metadata['userloginfail'],
                'meta loginfailures'
            );
        }

        // MNet.
        if (!empty($opts->metadata['asmnetuser'])) {
            $mnet = strtolower(preg_replace('#[ ]+#', '-', trim($opts->metadata['mnetidprovidername'])));
            $usertextcontents .= html_writer::span(
                $opts->metadata['mnetidprovidername'],
                'meta mnet mnet-' . $mnet
            );
        }

        $returnstr .= html_writer::span(
            html_writer::span($usertextcontents, 'usertext') .
            html_writer::span($avatarcontents, $avatarclasses),
            'userbutton'
        );

        // Create a divider (well, a filler).
        $divider = new action_menu_filler();
        $divider->primary = false;

        $am = new action_menu();
        $am->initialise_js($this->page);
        $am->set_menu_trigger(
            $returnstr
        );
        $am->set_alignment(action_menu::TR, action_menu::BR);
        $am->set_nowrap_on_items();
        if ($withlinks) {
            $navitemcount = count($opts->navitems);
            $idx = 0;
            foreach ($opts->navitems as $key => $value) {

                switch ($value->itemtype) {
                    case 'divider':
                        // If the nav item is a divider, add one and skip link processing.
                        $am->add($divider);
                        break;

                    case 'invalid':
                        // Silently skip invalid entries (should we post a notification?).
                        break;

                    case 'link':
                        // Process this as a link item.
                        $pix = null;
                        if (isset($value->pix) && !empty($value->pix)) {
                            $pix = new pix_icon($value->pix, $value->title, null, array('class' => 'iconsmall'));
                        } else if (isset($value->imgsrc) && !empty($value->imgsrc)) {
                            $value->title = html_writer::img(
                                $value->imgsrc,
                                $value->title,
                                array('class' => 'iconsmall')
                            ) . $value->title;
                        }
                        $al = new action_menu_link_secondary(
                            $value->url,
                            $pix,
                            $value->title,
                            array('class' => 'icon')
                        );
                        $am->add($al);
                        break;
                }

                $idx++;

                // Add dividers after the first item and before the last item.
                if ($idx == 1 || $idx == $navitemcount - 1) {
                    $am->add($divider);
                }
            }
        }

        return html_writer::div(
            $this->render($am),
            $usermenuclasses
        );
    }

    /**
     * Return the navbar content so that it can be echoed out by the layout
     *
     * @return string XHTML navbar
     */
    public function navbar() {
        $items = $this->page->navbar->get_items();
        $itemcount = count($items);
        if ($itemcount === 0) {
            return '';
        }

        $htmlblocks = array();
        // Iterate the navarray and display each node
        $separator = get_separator();
        for ($i=0;$i < $itemcount;$i++) {
            $item = $items[$i];
            $item->hideicon = true;
            if ($i===0) {
                $content = html_writer::tag('li', $this->render($item));
            } else {
                $content = html_writer::tag('li', $separator.$this->render($item));
            }
            $htmlblocks[] = $content;
        }

        //accessibility: heading for navbar list  (MDL-20446)
        $navbarcontent = html_writer::tag('span', get_string('pagepath'), array('class'=>'accesshide'));
        $navbarcontent .= html_writer::tag('nav', html_writer::tag('ul', join('', $htmlblocks)));
        // XHTML
        return $navbarcontent;
    }

    /**
     * Renders a navigation node object.
     *
     * @param navigation_node $item The navigation node to render.
     * @return string HTML fragment
     */
    protected function render_navigation_node(navigation_node $item) {
        $content = $item->get_content();
        $title = $item->get_title();
        if ($item->icon instanceof renderable && !$item->hideicon) {
            $icon = $this->render($item->icon);
            $content = $icon.$content; // use CSS for spacing of icons
        }
        if ($item->helpbutton !== null) {
            $content = trim($item->helpbutton).html_writer::tag('span', $content, array('class'=>'clearhelpbutton', 'tabindex'=>'0'));
        }
        if ($content === '') {
            return '';
        }
        if ($item->action instanceof action_link) {
            $link = $item->action;
            if ($item->hidden) {
                $link->add_class('dimmed');
            }
            if (!empty($content)) {
                // Providing there is content we will use that for the link content.
                $link->text = $content;
            }
            $content = $this->render($link);
        } else if ($item->action instanceof moodle_url) {
            $attributes = array();
            if ($title !== '') {
                $attributes['title'] = $title;
            }
            if ($item->hidden) {
                $attributes['class'] = 'dimmed_text';
            }
            $content = html_writer::link($item->action, $content, $attributes);

        } else if (is_string($item->action) || empty($item->action)) {
            $attributes = array('tabindex'=>'0'); //add tab support to span but still maintain character stream sequence.
            if ($title !== '') {
                $attributes['title'] = $title;
            }
            if ($item->hidden) {
                $attributes['class'] = 'dimmed_text';
            }
            $content = html_writer::tag('span', $content, $attributes);
        }
        return $content;
    }

    /**
     * Accessibility: Right arrow-like character is
     * used in the breadcrumb trail, course navigation menu
     * (previous/next activity), calendar, and search forum block.
     * If the theme does not set characters, appropriate defaults
     * are set automatically. Please DO NOT
     * use &lt; &gt; &raquo; - these are confusing for blind users.
     *
     * @return string
     */
    public function rarrow() {
        return $this->page->theme->rarrow;
    }

    /**
     * Accessibility: Left arrow-like character is
     * used in the breadcrumb trail, course navigation menu
     * (previous/next activity), calendar, and search forum block.
     * If the theme does not set characters, appropriate defaults
     * are set automatically. Please DO NOT
     * use &lt; &gt; &raquo; - these are confusing for blind users.
     *
     * @return string
     */
    public function larrow() {
        return $this->page->theme->larrow;
    }

    /**
     * Accessibility: Up arrow-like character is used in
     * the book heirarchical navigation.
     * If the theme does not set characters, appropriate defaults
     * are set automatically. Please DO NOT
     * use ^ - this is confusing for blind users.
     *
     * @return string
     */
    public function uarrow() {
        return $this->page->theme->uarrow;
    }

    /**
     * Returns the custom menu if one has been set
     *
     * A custom menu can be configured by browsing to
     *    Settings: Administration > Appearance > Themes > Theme settings
     * and then configuring the custommenu config setting as described.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@link core_renderer::render_custom_menu()} instead.
     *
     * @param string $custommenuitems - custom menuitems set by theme instead of global theme settings
     * @return string
     */
    public function custom_menu($custommenuitems = '') {
        global $CFG;
        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        if (empty($custommenuitems)) {
            return '';
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render($custommenu);
    }

    /**
     * Renders a custom menu object (located in outputcomponents.php)
     *
     * The custom menu this method produces makes use of the YUI3 menunav widget
     * and requires very specific html elements and classes.
     *
     * @staticvar int $menucount
     * @param custom_menu $menu
     * @return string
     */
    protected function render_custom_menu(custom_menu $menu) {
        static $menucount = 0;
        // If the menu has no children return an empty string
        if (!$menu->has_children()) {
            return '';
        }
        // Increment the menu count. This is used for ID's that get worked with
        // in JavaScript as is essential
        $menucount++;
        // Initialise this custom menu (the custom menu object is contained in javascript-static
        $jscode = js_writer::function_call_with_Y('M.core_custom_menu.init', array('custom_menu_'.$menucount));
        $jscode = "(function(){{$jscode}})";
        $this->page->requires->yui_module('node-menunav', $jscode);
        // Build the root nodes as required by YUI
        $content = html_writer::start_tag('div', array('id'=>'custom_menu_'.$menucount, 'class'=>'yui3-menu yui3-menu-horizontal javascript-disabled custom-menu'));
        $content .= html_writer::start_tag('div', array('class'=>'yui3-menu-content'));
        $content .= html_writer::start_tag('ul');
        // Render each child
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item);
        }
        // Close the open tags
        $content .= html_writer::end_tag('ul');
        $content .= html_writer::end_tag('div');
        $content .= html_writer::end_tag('div');
        // Return the custom menu
        return $content;
    }

    /**
     * Renders a custom menu node as part of a submenu
     *
     * The custom menu this method produces makes use of the YUI3 menunav widget
     * and requires very specific html elements and classes.
     *
     * @see core:renderer::render_custom_menu()
     *
     * @staticvar int $submenucount
     * @param custom_menu_item $menunode
     * @return string
     */
    protected function render_custom_menu_item(custom_menu_item $menunode) {
        // Required to ensure we get unique trackable id's
        static $submenucount = 0;
        if ($menunode->has_children()) {
            // If the child has menus render it as a sub menu
            $submenucount++;
            $content = html_writer::start_tag('li');
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#cm_submenu_'.$submenucount;
            }
            $content .= html_writer::link($url, $menunode->get_text(), array('class'=>'yui3-menu-label', 'title'=>$menunode->get_title()));
            $content .= html_writer::start_tag('div', array('id'=>'cm_submenu_'.$submenucount, 'class'=>'yui3-menu custom_menu_submenu'));
            $content .= html_writer::start_tag('div', array('class'=>'yui3-menu-content'));
            $content .= html_writer::start_tag('ul');
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode);
            }
            $content .= html_writer::end_tag('ul');
            $content .= html_writer::end_tag('div');
            $content .= html_writer::end_tag('div');
            $content .= html_writer::end_tag('li');
        } else {
            // The node doesn't have children so produce a final menuitem.
            // Also, if the node's text matches '####', add a class so we can treat it as a divider.
            $content = '';
            if (preg_match("/^#+$/", $menunode->get_text())) {

                // This is a divider.
                $content = html_writer::start_tag('li', array('class' => 'yui3-menuitem divider'));
            } else {
                $content = html_writer::start_tag(
                    'li',
                    array(
                        'class' => 'yui3-menuitem'
                    )
                );
                if ($menunode->get_url() !== null) {
                    $url = $menunode->get_url();
                } else {
                    $url = '#';
                }
                $content .= html_writer::link(
                    $url,
                    $menunode->get_text(),
                    array('class' => 'yui3-menuitem-content', 'title' => $menunode->get_title())
                );
            }
            $content .= html_writer::end_tag('li');
        }
        // Return the sub menu
        return $content;
    }

    /**
     * Renders theme links for switching between default and other themes.
     *
     * @return string
     */
    protected function theme_switch_links() {

        $actualdevice = core_useragent::get_device_type();
        $currentdevice = $this->page->devicetypeinuse;
        $switched = ($actualdevice != $currentdevice);

        if (!$switched && $currentdevice == 'default' && $actualdevice == 'default') {
            // The user is using the a default device and hasn't switched so don't shown the switch
            // device links.
            return '';
        }

        if ($switched) {
            $linktext = get_string('switchdevicerecommended');
            $devicetype = $actualdevice;
        } else {
            $linktext = get_string('switchdevicedefault');
            $devicetype = 'default';
        }
        $linkurl = new moodle_url('/theme/switchdevice.php', array('url' => $this->page->url, 'device' => $devicetype, 'sesskey' => sesskey()));

        $content  = html_writer::start_tag('div', array('id' => 'theme_switch_link'));
        $content .= html_writer::link($linkurl, $linktext, array('rel' => 'nofollow'));
        $content .= html_writer::end_tag('div');

        return $content;
    }

    /**
     * Renders tabs
     *
     * This function replaces print_tabs() used before Moodle 2.5 but with slightly different arguments
     *
     * Theme developers: In order to change how tabs are displayed please override functions
     * {@link core_renderer::render_tabtree()} and/or {@link core_renderer::render_tabobject()}
     *
     * @param array $tabs array of tabs, each of them may have it's own ->subtree
     * @param string|null $selected which tab to mark as selected, all parent tabs will
     *     automatically be marked as activated
     * @param array|string|null $inactive list of ids of inactive tabs, regardless of
     *     their level. Note that you can as weel specify tabobject::$inactive for separate instances
     * @return string
     */
    public final function tabtree($tabs, $selected = null, $inactive = null) {
        return $this->render(new tabtree($tabs, $selected, $inactive));
    }

    /**
     * Renders tabtree
     *
     * @param tabtree $tabtree
     * @return string
     */
    protected function render_tabtree(tabtree $tabtree) {
        if (empty($tabtree->subtree)) {
            return '';
        }
        $str = '';
        $str .= html_writer::start_tag('div', array('class' => 'tabtree'));
        $str .= $this->render_tabobject($tabtree);
        $str .= html_writer::end_tag('div').
                html_writer::tag('div', ' ', array('class' => 'clearer'));
        return $str;
    }

    /**
     * Renders tabobject (part of tabtree)
     *
     * This function is called from {@link core_renderer::render_tabtree()}
     * and also it calls itself when printing the $tabobject subtree recursively.
     *
     * Property $tabobject->level indicates the number of row of tabs.
     *
     * @param tabobject $tabobject
     * @return string HTML fragment
     */
    protected function render_tabobject(tabobject $tabobject) {
        $str = '';

        // Print name of the current tab.
        if ($tabobject instanceof tabtree) {
            // No name for tabtree root.
        } else if ($tabobject->inactive || $tabobject->activated || ($tabobject->selected && !$tabobject->linkedwhenselected)) {
            // Tab name without a link. The <a> tag is used for styling.
            $str .= html_writer::tag('a', html_writer::span($tabobject->text), array('class' => 'nolink moodle-has-zindex'));
        } else {
            // Tab name with a link.
            if (!($tabobject->link instanceof moodle_url)) {
                // backward compartibility when link was passed as quoted string
                $str .= "<a href=\"$tabobject->link\" title=\"$tabobject->title\"><span>$tabobject->text</span></a>";
            } else {
                $str .= html_writer::link($tabobject->link, html_writer::span($tabobject->text), array('title' => $tabobject->title));
            }
        }

        if (empty($tabobject->subtree)) {
            if ($tabobject->selected) {
                $str .= html_writer::tag('div', '&nbsp;', array('class' => 'tabrow'. ($tabobject->level + 1). ' empty'));
            }
            return $str;
        }

        // Print subtree.
        if ($tabobject->level == 0 || $tabobject->selected || $tabobject->activated) {
            $str .= html_writer::start_tag('ul', array('class' => 'tabrow'. $tabobject->level));
            $cnt = 0;
            foreach ($tabobject->subtree as $tab) {
                $liclass = '';
                if (!$cnt) {
                    $liclass .= ' first';
                }
                if ($cnt == count($tabobject->subtree) - 1) {
                    $liclass .= ' last';
                }
                if ((empty($tab->subtree)) && (!empty($tab->selected))) {
                    $liclass .= ' onerow';
                }

                if ($tab->selected) {
                    $liclass .= ' here selected';
                } else if ($tab->activated) {
                    $liclass .= ' here active';
                }

                // This will recursively call function render_tabobject() for each item in subtree.
                $str .= html_writer::tag('li', $this->render($tab), array('class' => trim($liclass)));
                $cnt++;
            }
            $str .= html_writer::end_tag('ul');
        }

        return $str;
    }

    /**
     * Get the HTML for blocks in the given region.
     *
     * @since Moodle 2.5.1 2.6
     * @param string $region The region to get HTML for.
     * @return string HTML.
     */
    public function blocks($region, $classes = array(), $tag = 'aside') {
        $displayregion = $this->page->apply_theme_region_manipulations($region);
        $classes = (array)$classes;
        $classes[] = 'block-region';
        $attributes = array(
            'id' => 'block-region-'.preg_replace('#[^a-zA-Z0-9_\-]+#', '-', $displayregion),
            'class' => join(' ', $classes),
            'data-blockregion' => $displayregion,
            'data-droptarget' => '1'
        );
        if ($this->page->blocks->region_has_content($displayregion, $this)) {
            $content = $this->blocks_for_region($displayregion);
        } else {
            $content = '';
        }
        return html_writer::tag($tag, $content, $attributes);
    }

    /**
     * Renders a custom block region.
     *
     * Use this method if you want to add an additional block region to the content of the page.
     * Please note this should only be used in special situations.
     * We want to leave the theme is control where ever possible!
     *
     * This method must use the same method that the theme uses within its layout file.
     * As such it asks the theme what method it is using.
     * It can be one of two values, blocks or blocks_for_region (deprecated).
     *
     * @param string $regionname The name of the custom region to add.
     * @return string HTML for the block region.
     */
    public function custom_block_region($regionname) {
        if ($this->page->theme->get_block_render_method() === 'blocks') {
            return $this->blocks($regionname);
        } else {
            return $this->blocks_for_region($regionname);
        }
    }

    /**
     * Returns the CSS classes to apply to the body tag.
     *
     * @since Moodle 2.5.1 2.6
     * @param array $additionalclasses Any additional classes to apply.
     * @return string
     */
    public function body_css_classes(array $additionalclasses = array()) {
        // Add a class for each block region on the page.
        // We use the block manager here because the theme object makes get_string calls.
        $usedregions = array();
        foreach ($this->page->blocks->get_regions() as $region) {
            $additionalclasses[] = 'has-region-'.$region;
            if ($this->page->blocks->region_has_content($region, $this)) {
                $additionalclasses[] = 'used-region-'.$region;
                $usedregions[] = $region;
            } else {
                $additionalclasses[] = 'empty-region-'.$region;
            }
            if ($this->page->blocks->region_completely_docked($region, $this)) {
                $additionalclasses[] = 'docked-region-'.$region;
            }
        }
        if (!$usedregions) {
            // No regions means there is only content, add 'content-only' class.
            $additionalclasses[] = 'content-only';
        } else if (count($usedregions) === 1) {
            // Add the -only class for the only used region.
            $region = array_shift($usedregions);
            $additionalclasses[] = $region . '-only';
        }
        foreach ($this->page->layout_options as $option => $value) {
            if ($value) {
                $additionalclasses[] = 'layout-option-'.$option;
            }
        }
        $css = $this->page->bodyclasses .' '. join(' ', $additionalclasses);
        return $css;
    }

    /**
     * The ID attribute to apply to the body tag.
     *
     * @since Moodle 2.5.1 2.6
     * @return string
     */
    public function body_id() {
        return $this->page->bodyid;
    }

    /**
     * Returns HTML attributes to use within the body tag. This includes an ID and classes.
     *
     * @since Moodle 2.5.1 2.6
     * @param string|array $additionalclasses Any additional classes to give the body tag,
     * @return string
     */
    public function body_attributes($additionalclasses = array()) {
        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', $additionalclasses);
        }
        return ' id="'. $this->body_id().'" class="'.$this->body_css_classes($additionalclasses).'"';
    }

    /**
     * Gets HTML for the page heading.
     *
     * @since Moodle 2.5.1 2.6
     * @param string $tag The tag to encase the heading in. h1 by default.
     * @return string HTML.
     */
    public function page_heading($tag = 'h1') {
        return html_writer::tag($tag, $this->page->heading);
    }

    /**
     * Gets the HTML for the page heading button.
     *
     * @since Moodle 2.5.1 2.6
     * @return string HTML.
     */
    public function page_heading_button() {
        return $this->page->button;
    }

    /**
     * Returns the Moodle docs link to use for this page.
     *
     * @since Moodle 2.5.1 2.6
     * @param string $text
     * @return string
     */
    public function page_doc_link($text = null) {
        if ($text === null) {
            $text = get_string('moodledocslink');
        }
        $path = page_get_doc_link_path($this->page);
        if (!$path) {
            return '';
        }
        return $this->doc_link($path, $text);
    }

    /**
     * Returns the page heading menu.
     *
     * @since Moodle 2.5.1 2.6
     * @return string HTML.
     */
    public function page_heading_menu() {
        return $this->page->headingmenu;
    }

    /**
     * Returns the title to use on the page.
     *
     * @since Moodle 2.5.1 2.6
     * @return string
     */
    public function page_title() {
        return $this->page->title;
    }

    /**
     * Returns the URL for the favicon.
     *
     * @since Moodle 2.5.1 2.6
     * @return string The favicon URL
     */
    public function favicon() {
        return $this->pix_url('favicon', 'theme');
    }

    /**
     * Renders preferences groups.
     *
     * @param  preferences_groups $renderable The renderable
     * @return string The output.
     */
    public function render_preferences_groups(preferences_groups $renderable) {
        $html = '';
        $html .= html_writer::start_div('row-fluid');
        $html .= html_writer::start_tag('div', array('class' => 'span12 preferences-groups'));
        $i = 0;
        $open = false;
        foreach ($renderable->groups as $group) {
            if ($i == 0 || $i % 3 == 0) {
                if ($open) {
                    $html .= html_writer::end_tag('div');
                }
                $html .= html_writer::start_tag('div', array('class' => 'row-fluid'));
                $open = true;
            }
            $html .= $this->render($group);
            $i++;
        }

        $html .= html_writer::end_tag('div');

        $html .= html_writer::end_tag('ul');
        $html .= html_writer::end_tag('div');
        $html .= html_writer::end_div();
        return $html;
    }

    /**
     * Renders preferences group.
     *
     * @param  preferences_group $renderable The renderable
     * @return string The output.
     */
    public function render_preferences_group(preferences_group $renderable) {
        $html = '';
        $html .= html_writer::start_tag('div', array('class' => 'span4 preferences-group'));
        $html .= $this->heading($renderable->title, 3);
        $html .= html_writer::start_tag('ul');
        foreach ($renderable->nodes as $node) {
            if ($node->has_children()) {
                debugging('Preferences nodes do not support children', DEBUG_DEVELOPER);
            }
            $html .= html_writer::tag('li', $this->render($node));
        }
        $html .= html_writer::end_tag('ul');
        $html .= html_writer::end_tag('div');
        return $html;
    }

    /**
     * Returns the header bar.
     *
     * @since Moodle 2.9
     * @param array $headerinfo An array of header information, dependant on what type of header is being displayed. The following
     *                          array example is user specific.
     *                          heading => Override the page heading.
     *                          user => User object.
     *                          usercontext => user context.
     * @param int $headinglevel What level the 'h' tag will be.
     * @return string HTML for the header bar.
     */
    public function context_header($headerinfo = null, $headinglevel = 1) {
        global $DB, $USER, $CFG;
        $context = $this->page->context;
        // Make sure to use the heading if it has been set.
        if (isset($headerinfo['heading'])) {
            $heading = $headerinfo['heading'];
        } else {
            $heading = null;
        }
        $imagedata = null;
        $subheader = null;
        $userbuttons = null;
        // The user context currently has images and buttons. Other contexts may follow.
        if (isset($headerinfo['user']) || $context->contextlevel == CONTEXT_USER) {
            if (isset($headerinfo['user'])) {
                $user = $headerinfo['user'];
            } else {
                // Look up the user information if it is not supplied.
                $user = $DB->get_record('user', array('id' => $context->instanceid));
            }
            // If the user context is set, then use that for capability checks.
            if (isset($headerinfo['usercontext'])) {
                $context = $headerinfo['usercontext'];
            }
            // Use the user's full name if the heading isn't set.
            if (!isset($heading)) {
                $heading = fullname($user);
            }

            $imagedata = $this->user_picture($user, array('size' => 100));
            // Check to see if we should be displaying a message button.
            if (!empty($CFG->messaging) && $USER->id != $user->id && has_capability('moodle/site:sendmessage', $context)) {
                $userbuttons = array(
                    'messages' => array(
                        'buttontype' => 'message',
                        'title' => get_string('message', 'message'),
                        'url' => new moodle_url('/message/index.php', array('id' => $user->id)),
                        'image' => 'message',
                        'linkattributes' => message_messenger_sendmessage_link_params($user),
                        'page' => $this->page
                    )
                );
                $this->page->requires->string_for_js('changesmadereallygoaway', 'moodle');
            }
        }

        $contextheader = new context_header($heading, $headinglevel, $imagedata, $userbuttons);
        return $this->render_context_header($contextheader);
    }

     /**
      * Renders the header bar.
      *
      * @param context_header $contextheader Header bar object.
      * @return string HTML for the header bar.
      */
    protected function render_context_header(context_header $contextheader) {

        // All the html stuff goes here.
        $html = html_writer::start_div('page-context-header');

        // Image data.
        if (isset($contextheader->imagedata)) {
            // Header specific image.
            $html .= html_writer::div($contextheader->imagedata, 'page-header-image');
        }

        // Headings.
        if (!isset($contextheader->heading)) {
            $headings = $this->heading($this->page->heading, $contextheader->headinglevel);
        } else {
            $headings = $this->heading($contextheader->heading, $contextheader->headinglevel);
        }

        $html .= html_writer::tag('div', $headings, array('class' => 'page-header-headings'));

        // Buttons.
        if (isset($contextheader->additionalbuttons)) {
            $html .= html_writer::start_div('btn-group header-button-group');
            foreach ($contextheader->additionalbuttons as $button) {
                if (!isset($button->page)) {
                    // Include js for messaging.
                    if ($button['buttontype'] === 'message') {
                        message_messenger_requirejs();
                    }
                    $image = $this->pix_icon($button['formattedimage'], $button['title'], 'moodle', array(
                        'class' => 'iconsmall',
                        'role' => 'presentation'
                    ));
                    $image .= html_writer::span($button['title'], 'header-button-title');
                } else {
                    $image = html_writer::empty_tag('img', array(
                        'src' => $button['formattedimage'],
                        'role' => 'presentation'
                    ));
                }
                $html .= html_writer::link($button['url'], html_writer::tag('span', $image), $button['linkattributes']);
            }
            $html .= html_writer::end_div();
        }
        $html .= html_writer::end_div();

        return $html;
    }

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
        $html = html_writer::start_tag('header', array('id' => 'page-header', 'class' => 'clearfix'));
        $html .= $this->context_header();
        $html .= html_writer::start_div('clearfix', array('id' => 'page-navbar'));
        $html .= html_writer::tag('nav', $this->navbar(), array('class' => 'breadcrumb-nav'));
        $html .= html_writer::div($this->page_heading_button(), 'breadcrumb-button');
        $html .= html_writer::end_div();
        $html .= html_writer::tag('div', $this->course_header(), array('id' => 'course-header'));
        $html .= html_writer::end_tag('header');
        return $html;
    }
}

/**
 * A renderer that generates output for command-line scripts.
 *
 * The implementation of this renderer is probably incomplete.
 *
 * @copyright 2009 Tim Hunt
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class core_renderer_cli extends core_renderer {

    /**
     * Returns the page header.
     *
     * @return string HTML fragment
     */
    public function header() {
        return $this->page->heading . "\n";
    }

    /**
     * Returns a template fragment representing a Heading.
     *
     * @param string $text The text of the heading
     * @param int $level The level of importance of the heading
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @return string A template fragment for a heading
     */
    public function heading($text, $level = 2, $classes = 'main', $id = null) {
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
     *
     * @param string $message The message to output
     * @param string $moreinfourl URL where more info can be found about the error
     * @param string $link Link for the Continue button
     * @param array $backtrace The execution backtrace
     * @param string $debuginfo Debugging information
     * @return string A template fragment for a fatal error
     */
    public function fatal_error($message, $moreinfourl, $link, $backtrace, $debuginfo = null) {
        global $CFG;

        $output = "!!! $message !!!\n";

        if ($CFG->debugdeveloper) {
            if (!empty($debuginfo)) {
                $output .= $this->notification($debuginfo, 'notifytiny');
            }
            if (!empty($backtrace)) {
                $output .= $this->notification('Stack trace: ' . format_backtrace($backtrace, true), 'notifytiny');
            }
        }

        return $output;
    }

    /**
     * Returns a template fragment representing a notification.
     *
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

    /**
     * There is no footer for a cli request, however we must override the
     * footer method to prevent the default footer.
     */
    public function footer() {}
}


/**
 * A renderer that generates output for ajax scripts.
 *
 * This renderer prevents accidental sends back only json
 * encoded error messages, all other output is ignored.
 *
 * @copyright 2010 Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 2.0
 * @package core
 * @category output
 */
class core_renderer_ajax extends core_renderer {

    /**
     * Returns a template fragment representing a fatal error.
     *
     * @param string $message The message to output
     * @param string $moreinfourl URL where more info can be found about the error
     * @param string $link Link for the Continue button
     * @param array $backtrace The execution backtrace
     * @param string $debuginfo Debugging information
     * @return string A template fragment for a fatal error
     */
    public function fatal_error($message, $moreinfourl, $link, $backtrace, $debuginfo = null) {
        global $CFG;

        $this->page->set_context(null); // ugly hack - make sure page context is set to something, we do not want bogus warnings here

        $e = new stdClass();
        $e->error      = $message;
        $e->stacktrace = NULL;
        $e->debuginfo  = NULL;
        $e->reproductionlink = NULL;
        if (!empty($CFG->debug) and $CFG->debug >= DEBUG_DEVELOPER) {
            $link = (string) $link;
            if ($link) {
                $e->reproductionlink = $link;
            }
            if (!empty($debuginfo)) {
                $e->debuginfo = $debuginfo;
            }
            if (!empty($backtrace)) {
                $e->stacktrace = format_backtrace($backtrace, true);
            }
        }
        $this->header();
        return json_encode($e);
    }

    /**
     * Used to display a notification.
     * For the AJAX notifications are discarded.
     *
     * @param string $message
     * @param string $classes
     */
    public function notification($message, $classes = 'notifyproblem') {}

    /**
     * Used to display a redirection message.
     * AJAX redirections should not occur and as such redirection messages
     * are discarded.
     *
     * @param moodle_url|string $encodedurl
     * @param string $message
     * @param int $delay
     * @param bool $debugdisableredirect
     */
    public function redirect_message($encodedurl, $message, $delay, $debugdisableredirect) {}

    /**
     * Prepares the start of an AJAX output.
     */
    public function header() {
        // unfortunately YUI iframe upload does not support application/json
        if (!empty($_FILES)) {
            @header('Content-type: text/plain; charset=utf-8');
            if (!core_useragent::supports_json_contenttype()) {
                @header('X-Content-Type-Options: nosniff');
            }
        } else if (!core_useragent::supports_json_contenttype()) {
            @header('Content-type: text/plain; charset=utf-8');
            @header('X-Content-Type-Options: nosniff');
        } else {
            @header('Content-type: application/json; charset=utf-8');
        }

        // Headers to make it not cacheable and json
        @header('Cache-Control: no-store, no-cache, must-revalidate');
        @header('Cache-Control: post-check=0, pre-check=0', false);
        @header('Pragma: no-cache');
        @header('Expires: Mon, 20 Aug 1969 09:23:00 GMT');
        @header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        @header('Accept-Ranges: none');
    }

    /**
     * There is no footer for an AJAX request, however we must override the
     * footer method to prevent the default footer.
     */
    public function footer() {}

    /**
     * No need for headers in an AJAX request... this should never happen.
     * @param string $text
     * @param int $level
     * @param string $classes
     * @param string $id
     */
    public function heading($text, $level = 2, $classes = 'main', $id = null) {}
}


/**
 * Renderer for media files.
 *
 * Used in file resources, media filter, and any other places that need to
 * output embedded media.
 *
 * @copyright 2011 The Open University
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_media_renderer extends plugin_renderer_base {
    /** @var array Array of available 'player' objects */
    private $players;
    /** @var string Regex pattern for links which may contain embeddable content */
    private $embeddablemarkers;

    /**
     * Constructor requires medialib.php.
     *
     * This is needed in the constructor (not later) so that you can use the
     * constants and static functions that are defined in core_media class
     * before you call renderer functions.
     */
    public function __construct() {
        global $CFG;
        require_once($CFG->libdir . '/medialib.php');
    }

    /**
     * Obtains the list of core_media_player objects currently in use to render
     * items.
     *
     * The list is in rank order (highest first) and does not include players
     * which are disabled.
     *
     * @return array Array of core_media_player objects in rank order
     */
    protected function get_players() {
        global $CFG;

        // Save time by only building the list once.
        if (!$this->players) {
            // Get raw list of players.
            $players = $this->get_players_raw();

            // Chuck all the ones that are disabled.
            foreach ($players as $key => $player) {
                if (!$player->is_enabled()) {
                    unset($players[$key]);
                }
            }

            // Sort in rank order (highest first).
            usort($players, array('core_media_player', 'compare_by_rank'));
            $this->players = $players;
        }
        return $this->players;
    }

    /**
     * Obtains a raw list of player objects that includes objects regardless
     * of whether they are disabled or not, and without sorting.
     *
     * You can override this in a subclass if you need to add additional
     * players.
     *
     * The return array is be indexed by player name to make it easier to
     * remove players in a subclass.
     *
     * @return array $players Array of core_media_player objects in any order
     */
    protected function get_players_raw() {
        return array(
            'vimeo' => new core_media_player_vimeo(),
            'youtube' => new core_media_player_youtube(),
            'youtube_playlist' => new core_media_player_youtube_playlist(),
            'html5video' => new core_media_player_html5video(),
            'html5audio' => new core_media_player_html5audio(),
            'mp3' => new core_media_player_mp3(),
            'flv' => new core_media_player_flv(),
            'wmp' => new core_media_player_wmp(),
            'qt' => new core_media_player_qt(),
            'rm' => new core_media_player_rm(),
            'swf' => new core_media_player_swf(),
            'link' => new core_media_player_link(),
        );
    }

    /**
     * Renders a media file (audio or video) using suitable embedded player.
     *
     * See embed_alternatives function for full description of parameters.
     * This function calls through to that one.
     *
     * When using this function you can also specify width and height in the
     * URL by including ?d=100x100 at the end. If specified in the URL, this
     * will override the $width and $height parameters.
     *
     * @param moodle_url $url Full URL of media file
     * @param string $name Optional user-readable name to display in download link
     * @param int $width Width in pixels (optional)
     * @param int $height Height in pixels (optional)
     * @param array $options Array of key/value pairs
     * @return string HTML content of embed
     */
    public function embed_url(moodle_url $url, $name = '', $width = 0, $height = 0,
            $options = array()) {

        // Get width and height from URL if specified (overrides parameters in
        // function call).
        $rawurl = $url->out(false);
        if (preg_match('/[?#]d=([\d]{1,4}%?)x([\d]{1,4}%?)/', $rawurl, $matches)) {
            $width = $matches[1];
            $height = $matches[2];
            $url = new moodle_url(str_replace($matches[0], '', $rawurl));
        }

        // Defer to array version of function.
        return $this->embed_alternatives(array($url), $name, $width, $height, $options);
    }

    /**
     * Renders media files (audio or video) using suitable embedded player.
     * The list of URLs should be alternative versions of the same content in
     * multiple formats. If there is only one format it should have a single
     * entry.
     *
     * If the media files are not in a supported format, this will give students
     * a download link to each format. The download link uses the filename
     * unless you supply the optional name parameter.
     *
     * Width and height are optional. If specified, these are suggested sizes
     * and should be the exact values supplied by the user, if they come from
     * user input. These will be treated as relating to the size of the video
     * content, not including any player control bar.
     *
     * For audio files, height will be ignored. For video files, a few formats
     * work if you specify only width, but in general if you specify width
     * you must specify height as well.
     *
     * The $options array is passed through to the core_media_player classes
     * that render the object tag. The keys can contain values from
     * core_media::OPTION_xx.
     *
     * @param array $alternatives Array of moodle_url to media files
     * @param string $name Optional user-readable name to display in download link
     * @param int $width Width in pixels (optional)
     * @param int $height Height in pixels (optional)
     * @param array $options Array of key/value pairs
     * @return string HTML content of embed
     */
    public function embed_alternatives($alternatives, $name = '', $width = 0, $height = 0,
            $options = array()) {

        // Get list of player plugins (will also require the library).
        $players = $this->get_players();

        // Set up initial text which will be replaced by first player that
        // supports any of the formats.
        $out = core_media_player::PLACEHOLDER;

        // Loop through all players that support any of these URLs.
        foreach ($players as $player) {
            // Option: When no other player matched, don't do the default link player.
            if (!empty($options[core_media::OPTION_FALLBACK_TO_BLANK]) &&
                    $player->get_rank() === 0 && $out === core_media_player::PLACEHOLDER) {
                continue;
            }

            $supported = $player->list_supported_urls($alternatives, $options);
            if ($supported) {
                // Embed.
                $text = $player->embed($supported, $name, $width, $height, $options);

                // Put this in place of the 'fallback' slot in the previous text.
                $out = str_replace(core_media_player::PLACEHOLDER, $text, $out);
            }
        }

        // Remove 'fallback' slot from final version and return it.
        $out = str_replace(core_media_player::PLACEHOLDER, '', $out);
        if (!empty($options[core_media::OPTION_BLOCK]) && $out !== '') {
            $out = html_writer::tag('div', $out, array('class' => 'resourcecontent'));
        }
        return $out;
    }

    /**
     * Checks whether a file can be embedded. If this returns true you will get
     * an embedded player; if this returns false, you will just get a download
     * link.
     *
     * This is a wrapper for can_embed_urls.
     *
     * @param moodle_url $url URL of media file
     * @param array $options Options (same as when embedding)
     * @return bool True if file can be embedded
     */
    public function can_embed_url(moodle_url $url, $options = array()) {
        return $this->can_embed_urls(array($url), $options);
    }

    /**
     * Checks whether a file can be embedded. If this returns true you will get
     * an embedded player; if this returns false, you will just get a download
     * link.
     *
     * @param array $urls URL of media file and any alternatives (moodle_url)
     * @param array $options Options (same as when embedding)
     * @return bool True if file can be embedded
     */
    public function can_embed_urls(array $urls, $options = array()) {
        // Check all players to see if any of them support it.
        foreach ($this->get_players() as $player) {
            // Link player (always last on list) doesn't count!
            if ($player->get_rank() <= 0) {
                break;
            }
            // First player that supports it, return true.
            if ($player->list_supported_urls($urls, $options)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Obtains a list of markers that can be used in a regular expression when
     * searching for URLs that can be embedded by any player type.
     *
     * This string is used to improve peformance of regex matching by ensuring
     * that the (presumably C) regex code can do a quick keyword check on the
     * URL part of a link to see if it matches one of these, rather than having
     * to go into PHP code for every single link to see if it can be embedded.
     *
     * @return string String suitable for use in regex such as '(\.mp4|\.flv)'
     */
    public function get_embeddable_markers() {
        if (empty($this->embeddablemarkers)) {
            $markers = '';
            foreach ($this->get_players() as $player) {
                foreach ($player->get_embeddable_markers() as $marker) {
                    if ($markers !== '') {
                        $markers .= '|';
                    }
                    $markers .= preg_quote($marker);
                }
            }
            $this->embeddablemarkers = $markers;
        }
        return $this->embeddablemarkers;
    }
}

/**
 * The maintenance renderer.
 *
 * The purpose of this renderer is to block out the core renderer methods that are not usable when the site
 * is running a maintenance related task.
 * It must always extend the core_renderer as we switch from the core_renderer to this renderer in a couple of places.
 *
 * @since Moodle 2.6
 * @package core
 * @category output
 * @copyright 2013 Sam Hemelryk
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class core_renderer_maintenance extends core_renderer {

    /**
     * Initialises the renderer instance.
     * @param moodle_page $page
     * @param string $target
     * @throws coding_exception
     */
    public function __construct(moodle_page $page, $target) {
        if ($target !== RENDERER_TARGET_MAINTENANCE || $page->pagelayout !== 'maintenance') {
            throw new coding_exception('Invalid request for the maintenance renderer.');
        }
        parent::__construct($page, $target);
    }

    /**
     * Does nothing. The maintenance renderer cannot produce blocks.
     *
     * @param block_contents $bc
     * @param string $region
     * @return string
     */
    public function block(block_contents $bc, $region) {
        // Computer says no blocks.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce blocks.
     *
     * @param string $region
     * @param array $classes
     * @param string $tag
     * @return string
     */
    public function blocks($region, $classes = array(), $tag = 'aside') {
        // Computer says no blocks.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce blocks.
     *
     * @param string $region
     * @return string
     */
    public function blocks_for_region($region) {
        // Computer says no blocks for region.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a course content header.
     *
     * @param bool $onlyifnotcalledbefore
     * @return string
     */
    public function course_content_header($onlyifnotcalledbefore = false) {
        // Computer says no course content header.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a course content footer.
     *
     * @param bool $onlyifnotcalledbefore
     * @return string
     */
    public function course_content_footer($onlyifnotcalledbefore = false) {
        // Computer says no course content footer.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a course header.
     *
     * @return string
     */
    public function course_header() {
        // Computer says no course header.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a course footer.
     *
     * @return string
     */
    public function course_footer() {
        // Computer says no course footer.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a custom menu.
     *
     * @param string $custommenuitems
     * @return string
     */
    public function custom_menu($custommenuitems = '') {
        // Computer says no custom menu.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce a file picker.
     *
     * @param array $options
     * @return string
     */
    public function file_picker($options) {
        // Computer says no file picker.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce and HTML file tree.
     *
     * @param array $dir
     * @return string
     */
    public function htmllize_file_tree($dir) {
        // Hell no we don't want no htmllized file tree.
        // Also why on earth is this function on the core renderer???
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';

    }

    /**
     * Does nothing. The maintenance renderer does not support JS.
     *
     * @param block_contents $bc
     */
    public function init_block_hider_js(block_contents $bc) {
        // Computer says no JavaScript.
        // Do nothing, ridiculous method.
    }

    /**
     * Does nothing. The maintenance renderer cannot produce language menus.
     *
     * @return string
     */
    public function lang_menu() {
        // Computer says no lang menu.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does nothing. The maintenance renderer has no need for login information.
     *
     * @param null $withlinks
     * @return string
     */
    public function login_info($withlinks = null) {
        // Computer says no login info.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Does nothing. The maintenance renderer cannot produce user pictures.
     *
     * @param stdClass $user
     * @param array $options
     * @return string
     */
    public function user_picture(stdClass $user, array $options = null) {
        // Computer says no user pictures.
        // debugging('Please do not use $OUTPUT->'.__FUNCTION__.'() when performing maintenance tasks.', DEBUG_DEVELOPER);
        return '';
    }
}

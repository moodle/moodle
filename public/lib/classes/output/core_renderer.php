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

namespace core\output;

use breadcrumb_navigation_node;
use cm_info;
use core\hook\output\after_http_headers;
use core_block\output\block_contents;
use core_block\output\block_move_target;
use core_completion\cm_completion_details;
use core\context;
use core_tag\output\taglist;
use core_text;
use core_useragent;
use core\check\check as check_check;
use core\check\result as check_result;
use core\context\system as context_system;
use core\context\course as context_course;
use core\di;
use core\exception\coding_exception;
use core\hook\manager as hook_manager;
use core\hook\output\after_standard_main_region_html_generation;
use core\hook\output\before_footer_html_generation;
use core\hook\output\before_html_attributes;
use core\hook\output\before_http_headers;
use core\hook\output\before_standard_footer_html_generation;
use core\hook\output\before_standard_top_of_body_html_generation;
use core\output\actions\component_action;
use core\output\actions\popup_action;
use core\output\local\properties\badge;
use core\plugin_manager;
use moodleform;
use moodle_page;
use moodle_url;
use navigation_node;
use rating;
use rating_manager;
use stdClass;
use HTML_QuickForm_element;

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
     * @var string used in {@see core_renderer::header()}.
     */
    const MAIN_CONTENT_TOKEN = '[MAIN CONTENT GOES HERE]';

    /**
     * @var string Used to pass information from {@see core_renderer::doctype()} to
     * @see core_renderer::standard_head_html()
     */
    protected $contenttype;

    /**
     * @var string Used by {@see core_renderer::redirect_message()} method to communicate
     * with {@see core_renderer::header()}.
     */
    protected $metarefreshtag = '';

    /**
     * @var string Unique token for the closing HTML
     */
    protected $unique_end_html_token; // phpcs:ignore moodle.NamingConventions.ValidVariableName.MemberNameUnderscore

    /**
     * @var string Unique token for performance information
     */
    protected $unique_performance_info_token; // phpcs:ignore moodle.NamingConventions.ValidVariableName.MemberNameUnderscore

    /**
     * @var string Unique token for the main content.
     */
    protected $unique_main_content_token; // phpcs:ignore moodle.NamingConventions.ValidVariableName.MemberNameUnderscore

    /** @var custom_menu_item language The language menu if created */
    protected $language = null;

    /** @var string The current selector for an element being streamed into */
    protected $currentselector = '';

    /** @var string The current element tag which is being streamed into */
    protected $currentelement = '';

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

        $this->unique_end_html_token = '%%ENDHTML-' . sesskey() . '%%';
        $this->unique_performance_info_token = '%%PERFORMANCEINFO-' . sesskey() . '%%';
        $this->unique_main_content_token = '[MAIN CONTENT GOES HERE - ' . sesskey() . ']';
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

        $hook = new before_html_attributes($this);

        if ($this->page->theme->doctype !== 'html5') {
            $hook->add_attribute('xmlns', 'http://www.w3.org/1999/xhtml');
        }

        $hook->process_legacy_callbacks();
        di::get(hook_manager::class)->dispatch($hook);

        foreach ($hook->get_attributes() as $key => $val) {
            $val = s($val);
            $return .= " $key=\"$val\"";
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
        global $CFG, $SESSION, $SITE;

        // Before we output any content, we need to ensure that certain
        // page components are set up.

        // Blocks must be set up early as they may require javascript which
        // has to be included in the page header before output is created.
        foreach ($this->page->blocks->get_regions() as $region) {
            $this->page->blocks->ensure_content_created($region, $this);
        }

        // Give plugins an opportunity to add any head elements. The callback
        // must always return a string containing valid html head content.

        $hook = new \core\hook\output\before_standard_head_html_generation($this);
        $hook->process_legacy_callbacks();
        di::get(hook_manager::class)->dispatch($hook);

        // Allow a url_rewrite plugin to setup any dynamic head content.
        if (isset($CFG->urlrewriteclass) && !isset($CFG->upgraderunning)) {
            $class = $CFG->urlrewriteclass;
            $hook->add_html($class::html_head_setup());
        }

        $hook->add_html('<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />' . "\n");
        $hook->add_html('<meta name="keywords" content="moodle, ' . $this->page->title . '" />' . "\n");
        // This is only set by the {@see redirect()} method
        $hook->add_html($this->metarefreshtag);

        // Check if a periodic refresh delay has been set and make sure we arn't
        // already meta refreshing
        if ($this->metarefreshtag == '' && $this->page->periodicrefreshdelay !== null) {
            $hook->add_html(
                html_writer::empty_tag('meta', [
                    'http-equiv' => 'refresh',
                    'content' => $this->page->periodicrefreshdelay . ';url=' . $this->page->url->out(false),
                ]),
            );
        }

        $output = $hook->get_output();

        // Set up help link popups for all links with the helptooltip class
        $this->page->requires->js_init_call('M.util.help_popups.setup');

        $focus = $this->page->focuscontrol;
        if (!empty($focus)) {
            if (preg_match("#forms\['([a-zA-Z0-9]+)'\].elements\['([a-zA-Z0-9]+)'\]#", $focus, $matches)) {
                // This is a horrifically bad way to handle focus but it is passed in
                // through messy formslib::moodleform
                $this->page->requires->js_function_call('old_onload_focus', [$matches[1], $matches[2]]);
            } else if (strpos($focus, '.') !== false) {
                // Old style of focus, bad way to do it
                debugging('This code is using the old style focus event, Please update this code to focus on an element id or the moodleform focus method.', DEBUG_DEVELOPER);
                $this->page->requires->js_function_call('old_onload_focus', explode('.', $focus, 2));
            } else {
                // Focus element with given id
                $this->page->requires->js_function_call('focuscontrol', [$focus]);
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
            $output .= html_writer::empty_tag('link', [
                'rel' => 'alternate',
                'type' => $type,
                'title' => $alt->title,
                'href' => $alt->url,
            ]);
        }

        // Add noindex tag if relevant page and setting applied.
        $allowindexing = isset($CFG->allowindexing) ? $CFG->allowindexing : 0;
        $loginpages = ['login-index', 'login-signup'];
        if ($allowindexing == 2 || ($allowindexing == 0 && in_array($this->page->pagetype, $loginpages))) {
            if (!isset($CFG->additionalhtmlhead)) {
                $CFG->additionalhtmlhead = '';
            }
            if (stripos($CFG->additionalhtmlhead, '<meta name="robots" content="noindex" />') === false) {
                $CFG->additionalhtmlhead .= '<meta name="robots" content="noindex" />';
            }
        }

        if (!empty($CFG->additionalhtmlhead)) {
            $output .= "\n" . $CFG->additionalhtmlhead;
        }

        if ($this->page->pagelayout == 'frontpage') {
            $summary = s(strip_tags(format_text($SITE->summary, FORMAT_HTML)));
            if (!empty($summary)) {
                $output .= "<meta name=\"description\" content=\"$summary\" />\n";
            }
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
        $output = $this->page->requires->get_top_of_body_code($this);
        if ($this->page->pagelayout !== 'embedded' && !empty($CFG->additionalhtmltopofbody)) {
            $output .= "\n" . $CFG->additionalhtmltopofbody;
        }

        // Allow components to add content to the top of the body.
        $hook = new before_standard_top_of_body_html_generation($this, $output);
        $hook->process_legacy_callbacks();
        di::get(hook_manager::class)->dispatch($hook);
        $output = $hook->get_output();

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
            $errorclass = ($timeleft < 30) ? 'alert-error alert-danger' : 'alert-warning';
            $output .= $this->box_start($errorclass . ' moodle-has-zindex maintenancewarning alert');
            $a = new stdClass();
            $a->hour = (int)($timeleft / 3600);
            $a->min = (int)(floor($timeleft / 60) % 60);
            $a->sec = (int)($timeleft % 60);
            if ($a->hour > 0) {
                $output .= get_string('maintenancemodeisscheduledlong', 'admin', $a);
            } else {
                $output .= get_string('maintenancemodeisscheduled', 'admin', $a);
            }

            $output .= $this->box_end();
            $this->page->requires->yui_module(
                'moodle-core-maintenancemodetimer',
                'M.core.maintenancemodetimer',
                [['timeleftinsec' => $timeleft]]
            );
            $this->page->requires->strings_for_js(
                ['maintenancemodeisscheduled', 'maintenancemodeisscheduledlong', 'sitemaintenance'],
                'admin'
            );
        }
        return $output;
    }

    /**
     * content that should be output in the footer area
     * of the page. Designed to be called in theme layout.php files.
     *
     * @return string HTML fragment.
     */
    public function standard_footer_html() {
        if (during_initial_install()) {
            // Debugging info can not work before install is finished,
            // in any case we do not want any links during installation!
            return '';
        }

        $hook = new before_standard_footer_html_generation($this);
        $hook->process_legacy_callbacks();
        di::get(hook_manager::class)->dispatch($hook);
        $output = $hook->get_output();

        if ($this->page->devicetypeinuse == 'legacy') {
            // The legacy theme is in use print the notification
            $output .= html_writer::tag('div', get_string('legacythemeinuse'), ['class' => 'legacythemeinuse']);
        }

        // Get links to switch device types (only shown for users not on a default device)
        $output .= $this->theme_switch_links();

        return $output;
    }

    /**
     * Performance information and validation links for debugging.
     *
     * @return string HTML fragment.
     */
    public function debug_footer_html() {
        global $CFG, $SCRIPT;
        $output = '';

        if (during_initial_install()) {
            // Debugging info can not work before install is finished.
            return $output;
        }

        // This function is normally called from a layout.php file
        // but some of the content won't be known until later, so we return a placeholder
        // for now. This will be replaced with the real content in the footer.
        $output .= $this->unique_performance_info_token;

        if (!empty($CFG->debugpageinfo)) {
            $output .= '<div class="performanceinfo pageinfo">' . get_string(
                'pageinfodebugsummary',
                'core_admin',
                $this->page->debug_summary()
            ) . '</div>';
        }
        if (debugging(null, DEBUG_DEVELOPER) and has_capability('moodle/site:config', context_system::instance())) {  // Only in developer mode
            // Add link to profiling report if necessary
            if (function_exists('profiling_is_running') && profiling_is_running()) {
                $txt = get_string('profiledscript', 'admin');
                $title = get_string('profiledscriptview', 'admin');
                $url = $CFG->wwwroot . '/admin/tool/profiling/index.php?script=' . urlencode($SCRIPT);
                $link = '<a title="' . $title . '" href="' . $url . '">' . $txt . '</a>';
                $output .= '<div class="profilingfooter">' . $link . '</div>';
            }
            $purgeurl = new moodle_url('/admin/purgecaches.php', [
                'confirm' => 1,
                'sesskey' => sesskey(),
                'returnurl' => $this->page->url->out_as_local_url(false),
            ]);
            $output .= '<div class="purgecaches">' .
                    html_writer::link($purgeurl, get_string('purgecaches', 'admin')) . '</div>';

            // Reactive module debug panel.
            $output .= $this->render_from_template('core/local/reactive/debugpanel', []);
        }
        if (!empty($CFG->debugvalidators)) {
            $siteurl = qualified_me();
            $nuurl = new moodle_url('https://validator.w3.org/nu/', ['doc' => $siteurl, 'showsource' => 'yes']);
            $waveurl = new moodle_url('https://wave.webaim.org/report#/' . urlencode($siteurl));
            $validatorlinks = [
                html_writer::link($nuurl, get_string('validatehtml')),
                html_writer::link($waveurl, get_string('wcagcheck')),
            ];
            $validatorlinkslist = html_writer::alist($validatorlinks, ['class' => 'list-unstyled ms-1']);
            $output .= html_writer::div($validatorlinkslist, 'validators');
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
        return '<div role="main">' . $this->unique_main_content_token . '</div>';
    }

    /**
     * Returns standard navigation between activities in a course.
     *
     * @return string the navigation HTML.
     */
    public function activity_navigation() {
        // First we should check if we want to add navigation.
        $context = $this->page->context;
        if (
            ($this->page->pagelayout !== 'incourse' && $this->page->pagelayout !== 'frametop')
            || $context->contextlevel != CONTEXT_MODULE
        ) {
            return '';
        }

        // If the activity is in stealth mode, show no links.
        if ($this->page->cm->is_stealth()) {
            return '';
        }

        $course = $this->page->cm->get_course();
        $courseformat = course_get_format($course);

        // If the theme implements course index and the current course format uses course index and the current
        // page layout is not 'frametop' (this layout does not support course index), show no links.
        if (
            $this->page->theme->usescourseindex && $courseformat->uses_course_index() &&
                $this->page->pagelayout !== 'frametop'
        ) {
            return '';
        }

        // Get a list of all the activities in the course.
        $modules = get_fast_modinfo($course->id)->get_cms();

        // Put the modules into an array in order by the position they are shown in the course.
        $mods = [];
        $activitylist = [];
        foreach ($modules as $module) {
            // Only add activities the user can access, aren't in stealth mode, are of a type that is visible on the course,
            // and have a url (eg. mod_label does not).
            if (!$module->uservisible || $module->is_stealth() || empty($module->url) || !$module->is_of_type_that_can_display()) {
                continue;
            }
            $mods[$module->id] = $module;

            // No need to add the current module to the list for the activity dropdown menu.
            if ($module->id == $this->page->cm->id) {
                continue;
            }
            // Module name.
            $modname = $module->get_formatted_name();
            // Display the hidden text if necessary.
            if (!$module->visible) {
                $modname .= ' ' . get_string('hiddenwithbrackets');
            }
            // Module URL.
            $linkurl = new moodle_url($module->url, ['forceview' => 1]);
            // Add module URL (as key) and name (as value) to the activity list array.
            $activitylist[$linkurl->out(false)] = $modname;
        }

        $nummods = count($mods);

        // If there are only one or fewer mods then do nothing.
        if ($nummods <= 1) {
            return '';
        }

        // Get an array of just the course module ids used to get the cmid value based on their position in the course.
        $modids = array_keys($mods);

        // Get the position in the array of the course module we are viewing.
        $position = array_search($this->page->cm->id, $modids);

        $prevmod = null;
        $nextmod = null;

        // Check if we have a previous mod to show.
        if ($position > 0) {
            $prevmod = $mods[$modids[$position - 1]];
        }

        // Check if we have a next mod to show.
        if ($position < ($nummods - 1)) {
            $nextmod = $mods[$modids[$position + 1]];
        }

        $activitynav = new \core_course\output\activity_navigation($prevmod, $nextmod, $activitylist);
        $renderer = $this->page->get_renderer('core', 'course');
        return $renderer->render($activitynav);
    }

    /**
     * The standard tags (typically script tags that are not needed earlier) that
     * should be output after everything else. Designed to be called in theme layout.php files.
     *
     * @return string HTML fragment.
     */
    public function standard_end_of_body_html() {
        global $CFG;

        // This function is normally called from a layout.php file in {@see core_renderer::header()}
        // but some of the content won't be known until later, so we return a placeholder
        // for now. This will be replaced with the real content in {@see core_renderer::footer()}.
        $output = '';
        if ($this->page->pagelayout !== 'embedded' && !empty($CFG->additionalhtmlfooter)) {
            $output .= "\n" . $CFG->additionalhtmlfooter;
        }
        $output .= $this->unique_end_html_token;
        return $output;
    }

    /**
     * The standard HTML that should be output just before the <footer> tag.
     * Designed to be called in theme layout.php files.
     *
     * @return string HTML fragment.
     */
    public function standard_after_main_region_html() {
        global $CFG;

        $hook = new after_standard_main_region_html_generation($this);

        if ($this->page->pagelayout !== 'embedded' && !empty($CFG->additionalhtmlbottomofbody)) {
            $hook->add_html("\n");
            $hook->add_html($CFG->additionalhtmlbottomofbody);
        }

        $hook->process_legacy_callbacks();
        di::get(hook_manager::class)->dispatch($hook);

        return $hook->get_output();
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
            $fullname = fullname($realuser);
            if ($withlinks) {
                $loginastitle = get_string('loginas');
                $realuserinfo = " [<a href=\"$CFG->wwwroot/course/loginas.php?id=$course->id&amp;sesskey=" . sesskey() . "\"";
                $realuserinfo .= "title =\"" . $loginastitle . "\">$fullname</a>] ";
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

            $fullname = fullname($USER);
            // Since Moodle 2.0 this link always goes to the public profile page (not the course profile page)
            if ($withlinks) {
                $userurl = new moodle_url('/user/profile.php', ['id' => $USER->id]);
                $username = html_writer::link($userurl, $fullname);
            } else {
                $username = $fullname;
            }
            if (is_mnet_remote_user($USER) and $idprovider = $DB->get_record('mnet_host', ['id' => $USER->mnethostid])) {
                if ($withlinks) {
                    $username .= " from <a href=\"{$idprovider->wwwroot}\">{$idprovider->name}</a>";
                } else {
                    $username .= " from {$idprovider->name}";
                }
            }
            if (isguestuser()) {
                $loggedinas = $realuserinfo . get_string('loggedinasguest');
                if (!$loginpage && $withlinks) {
                    $loggedinas .= " (<a href=\"$loginurl\">" . get_string('login') . '</a>)';
                }
            } else if (is_role_switched($course->id)) { // Has switched roles
                $rolename = '';
                if ($role = $DB->get_record('role', ['id' => $USER->access['rsw'][$context->path]])) {
                    $rolename = ': ' . role_get_name($role, $context);
                }
                $loggedinas = get_string('loggedinas', 'moodle', $username) . $rolename;
                if ($withlinks) {
                    $url = new moodle_url('/course/switchrole.php', ['id' => $course->id, 'sesskey' => sesskey(), 'switchrole' => 0, 'returnurl' => $this->page->url->out_as_local_url(false)]);
                    $loggedinas .= ' (' . html_writer::tag('a', get_string('switchrolereturn'), ['href' => $url]) . ')';
                }
            } else {
                $loggedinas = $realuserinfo . get_string('loggedinas', 'moodle', $username);
                if ($withlinks) {
                    $loggedinas .= " (<a href=\"$CFG->wwwroot/login/logout.php?sesskey=" . sesskey() . "\">" . get_string('logout') . '</a>)';
                }
            }
        } else {
            $loggedinas = get_string('loggedinnot', 'moodle');
            if (!$loginpage && $withlinks) {
                $loggedinas .= " (<a href=\"$loginurl\">" . get_string('login') . '</a>)';
            }
        }

        $loggedinas = '<div class="logininfo">' . $loggedinas . '</div>';

        if (isset($SESSION->justloggedin)) {
            unset($SESSION->justloggedin);
            if (!isguestuser()) {
                // Include this file only when required.
                require_once($CFG->dirroot . '/user/lib.php');
                if (($count = user_count_login_failures($USER)) && !empty($CFG->displayloginfailures)) {
                    $loggedinas .= '<div class="loginfailures">';
                    $a = new stdClass();
                    $a->attempts = $count;
                    $loggedinas .= get_string('failedloginattempts', '', $a);
                    if (file_exists("$CFG->dirroot/report/log/index.php") and has_capability('report/log:view', context_system::instance())) {
                        $loggedinas .= ' (' . html_writer::link(new moodle_url('/report/log/index.php', [
                            'chooselog' => 1,
                            'id' => 0,
                            'modid' => 'site_errors',
                        ]), get_string('logs')) . ')';
                    }
                    $loggedinas .= '</div>';
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
            $this->page->url->out_as_local_url(false, []),
            [
                '/login/index.php',
                '/login/forgot_password.php',
            ]
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
                   '<a title="Moodle" class="d-inline-block aalink" href="http://moodle.org/">' .
                   '<img src="' . $this->image_url('moodlelogo_grayhat') . '" alt="' . get_string('moodlelogo') . '" /></a></div>';
        } else if (!empty($CFG->target_release) && $CFG->target_release != $CFG->release) {
            // Special case for during install/upgrade.
            return '<div class="sitelink">' .
                   '<a title="Moodle" href="http://docs.moodle.org/en/Administrator_documentation" onclick="this.target=\'_blank\'">' .
                   '<img src="' . $this->image_url('moodlelogo_grayhat') . '" alt="' . get_string('moodlelogo') . '" /></a></div>';
        } else if ($this->page->course->id == $SITE->id || strpos($this->page->pagetype, 'course-view') === 0) {
            return '<div class="homelink"><a href="' . $CFG->wwwroot . '/">' .
                    get_string('home') . '</a></div>';
        } else {
            return '<div class="homelink"><a href="' . $CFG->wwwroot . '/course/view.php?id=' . $this->page->course->id . '">' .
                    format_string($this->page->course->shortname, true, ['context' => $this->page->context]) . '</a></div>';
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
     * @param string $messagetype The type of notification to show the message in.
     *         See constants on \core\output\notification.
     * @return string The HTML to display to the user before dying, may contain
     *         meta refresh, javascript refresh, and may have set header redirects
     */
    public function redirect_message(
        $encodedurl,
        $message,
        $delay,
        $debugdisableredirect,
        $messagetype = notification::NOTIFY_INFO,
    ) {
        global $CFG;
        $url = str_replace('&amp;', '&', $encodedurl);

        switch ($this->page->state) {
            case moodle_page::STATE_BEFORE_HEADER:
                // No output yet it is safe to delivery the full arsenal of redirect methods
                if (!$debugdisableredirect) {
                    // Don't use exactly the same time here, it can cause problems when both redirects fire at the same time.
                    $this->metarefreshtag = '<meta http-equiv="refresh" content="' . $delay . '; url=' . $encodedurl . '" />' . "\n";
                    $this->page->requires->js_function_call('document.location.replace', [$url], false, ($delay + 3));
                }
                $output = $this->header();
                break;
            case moodle_page::STATE_PRINTING_HEADER:
                // We should hopefully never get here
                throw new coding_exception('You cannot redirect while printing the page header');
                break;
            case moodle_page::STATE_IN_BODY:
                // We really shouldn't be here but we can deal with this
                debugging("You should really redirect before you start page output");
                if (!$debugdisableredirect) {
                    $this->page->requires->js_function_call('document.location.replace', [$url], false, $delay);
                }
                $output = $this->opencontainers->pop_all_but_last();
                break;
            case moodle_page::STATE_DONE:
                // Too late to be calling redirect now
                throw new coding_exception('You cannot redirect after the entire page has been generated');
                break;
        }
        $output .= $this->notification($message, $messagetype);
        $output .= '<div class="continuebutton">(<a href="' . $encodedurl . '">' . get_string('continue') . '</a>)</div>';
        if ($debugdisableredirect) {
            $output .= '<p><strong>' . get_string('erroroutput', 'error') . '</strong></p>';
        }
        $output .= $this->footer();
        return $output;
    }

    /**
     * Start output by sending the HTTP headers, and printing the HTML <head>
     * and the start of the <body>.
     *
     * To control what is printed, you should set properties on $PAGE.
     *
     * @return string HTML that you must output this, preferably immediately.
     */
    public function header() {
        global $USER, $CFG, $SESSION;

        $hook = new before_http_headers($this);
        $hook->process_legacy_callbacks();
        di::get(hook_manager::class)->dispatch($hook);

        if (\core\session\manager::is_loggedinas()) {
            $this->page->add_body_class('userloggedinas');
        }

        if (isset($SESSION->justloggedin) && !empty($CFG->displayloginfailures)) {
            require_once($CFG->dirroot . '/user/lib.php');
            // Set second parameter to false as we do not want reset the counter, the same message appears on footer.
            if ($count = user_count_login_failures($USER, false)) {
                $this->page->add_body_class('loginfailures');
            }
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
        if (
            (!isset($this->page->theme->settings->version) || $this->page->theme->settings->version < 2012101500) &&
                $this->page->pagelayout === 'course' && $this->page->url->compare(new moodle_url('/course/view.php'), URL_MATCH_BASE)
        ) {
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
                $footer = $coursecontentfooter . $footer;
            }
        }

        send_headers($this->contenttype, $this->page->cacheable);

        $this->opencontainers->push('header/footer', $footer);
        $this->page->set_state(moodle_page::STATE_IN_BODY);

        // If an activity record has been set, activity_header will handle this.
        if (!$this->page->cm || !empty($this->page->layout_options['noactivityheader'])) {
            $header .= $this->skip_link_target('maincontent');
        }

        $hook = new after_http_headers(
            renderer: $this,
            output: $header,
        );
        di::get(hook_manager::class)->dispatch($hook);

        return $hook->get_output();
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
        global $CFG, $DB, $PERF;

        $hook = new before_footer_html_generation($this);
        $hook->process_legacy_callbacks();
        di::get(hook_manager::class)->dispatch($hook);
        $hook->add_html($this->container_end_all(true));
        $output = $hook->get_output();

        $footer = $this->opencontainers->pop('header/footer');

        if (debugging() and $DB and $DB->is_transaction_started()) {
            // TODO: MDL-20625 print warning - transaction will be rolled back
        }

        // Provide some performance info if required
        $performanceinfo = '';
        if (MDL_PERF || (!empty($CFG->perfdebug) && $CFG->perfdebug > 7)) {
            if (MDL_PERFTOFOOT || debugging() || (!empty($CFG->perfdebug) && $CFG->perfdebug > 7)) {
                if (NO_OUTPUT_BUFFERING) {
                    // If the output buffer was off then we render a placeholder and stream the
                    // performance debugging into it at the very end in the shutdown handler.
                    $PERF->perfdebugdeferred = true;
                    $performanceinfo .= html_writer::tag(
                        'div',
                        get_string('perfdebugdeferred', 'admin'),
                        [
                            'id' => 'perfdebugfooter',
                            'style' => 'min-height: 30em',
                        ]
                    );
                } else {
                    $perf = get_performance_info();
                    $performanceinfo = $perf['html'];
                }
            }
        }

        // We always want performance data when running a performance test, even if the user is redirected to another page.
        if (MDL_PERF_TEST && strpos($footer, $this->unique_performance_info_token) === false) {
            $footer = $this->unique_performance_info_token . $footer;
        }
        $footer = str_replace($this->unique_performance_info_token, $performanceinfo, $footer);

        // Only show notifications when the current page has a context id.
        if (!empty($this->page->context->id)) {
            $this->page->requires->js_call_amd('core/notification', 'init', [
                $this->page->context->id,
                \core\notification::fetch_as_array($this),
            ]);
        }
        $footer = str_replace($this->unique_end_html_token, $this->page->requires->get_end_code(), $footer);

        $this->page->set_state(moodle_page::STATE_DONE);

        // Here we remove the closing body and html tags and store them to be added back
        // in the shutdown handler so we can have valid html with streaming script tags
        // which are rendered after the visible footer.
        $tags = '';
        preg_match('#\<\/body>#i', $footer, $matches);
        $tags .= $matches[0];
        $footer = str_replace($matches[0], '', $footer);

        preg_match('#\<\/html>#i', $footer, $matches);
        $tags .= $matches[0];
        $footer = str_replace($matches[0], '', $footer);

        $CFG->closingtags = $tags;

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
        static $functioncalled = false;
        if ($functioncalled && $onlyifnotcalledbefore) {
            // we have already output the content header
            return '';
        }

        // Output any session notification.
        $notifications = \core\notification::fetch();

        $bodynotifications = '';
        foreach ($notifications as $notification) {
            $bodynotifications .= $this->render_from_template(
                $notification->get_template_name(),
                $notification->export_for_template($this)
            );
        }

        $output = html_writer::span($bodynotifications, 'notifications', ['id' => 'user-notifications']);

        if ($this->page->course->id == SITEID) {
            // return immediately and do not include /course/lib.php if not necessary
            return $output;
        }

        require_once($CFG->dirroot . '/course/lib.php');
        $functioncalled = true;
        $courseformat = course_get_format($this->page->course);
        if (($obj = $courseformat->course_content_header()) !== null) {
            $output .= html_writer::div($courseformat->get_renderer($this->page)->render($obj), 'course-content-header');
        }
        return $output;
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
        require_once($CFG->dirroot . '/course/lib.php');
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
        require_once($CFG->dirroot . '/course/lib.php');
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
        require_once($CFG->dirroot . '/course/lib.php');
        $courseformat = course_get_format($this->page->course);
        if (($obj = $courseformat->course_footer()) !== null) {
            return $courseformat->get_renderer($this->page)->render($obj);
        }
        return '';
    }

    /**
     * Get the course pattern datauri to show on a course card.
     *
     * The datauri is an encoded svg that can be passed as a url.
     * @param int $id Id to use when generating the pattern
     * @return string datauri
     */
    public function get_generated_image_for_id($id) {
        $color = $this->get_generated_color_for_id($id);
        $pattern = new \core_geopattern();
        $pattern->setColor($color);
        $pattern->patternbyid($id);
        return $pattern->datauri();
    }

    /**
     * Get the course pattern image URL.
     *
     * @param context_course $context course context object
     * @return string URL of the course pattern image in SVG format
     */
    public function get_generated_url_for_course(context_course $context): string {
        return moodle_url::make_pluginfile_url($context->id, 'course', 'generated', null, '/', 'course.svg')->out();
    }

    /**
     * Get the course pattern in SVG format to show on a course card.
     *
     * @param int $id id to use when generating the pattern
     * @return string SVG file contents
     */
    public function get_generated_svg_for_id(int $id): string {
        $color = $this->get_generated_color_for_id($id);
        $pattern = new \core_geopattern();
        $pattern->setColor($color);
        $pattern->patternbyid($id);
        return $pattern->toSVG();
    }

    /**
     * Get the course color to show on a course card.
     *
     * @param int $id Id to use when generating the color.
     * @return string hex color code.
     */
    public function get_generated_color_for_id($id) {
        $colornumbers = range(1, 10);
        $basecolors = [];
        foreach ($colornumbers as $number) {
            $basecolors[] = get_config('core_admin', 'coursecolor' . $number);
        }

        $color = $basecolors[$id % 10];
        return $color;
    }

    /**
     * Returns lang menu or '', this method also checks forcing of languages in courses.
     *
     * This function calls {@see core_renderer::render_single_select()} to actually display the language menu.
     *
     * @return string The lang menu HTML or empty string
     */
    public function lang_menu() {
        $languagemenu = new language_menu($this->page);
        $data = $languagemenu->export_for_single_select($this);
        if ($data) {
            return $this->render_from_template('core/single_select', $data);
        }
        return '';
    }

    /**
     * Output the row of editing icons for a block, as defined by the controls array.
     *
     * @param array $actions an array like {@see block_contents::$controls}.
     * @param null|string $blockid The ID given to the block.
     * @return string HTML fragment.
     */
    public function block_controls($actions, $blockid = null) {
        if (empty($actions)) {
            return '';
        }
        $menu = new action_menu($actions);
        if ($blockid !== null) {
            $menu->set_owner_selector('#' . $blockid);
        }
        $menu->attributes['class'] .= ' block-control-actions commands';
        return $this->render($menu);
    }

    /**
     * Returns the HTML for a basic textarea field.
     *
     * @param string $name Name to use for the textarea element
     * @param string $id The id to use fort he textarea element
     * @param string $value Initial content to display in the textarea
     * @param int $rows Number of rows to display
     * @param int $cols Number of columns to display
     * @return string the HTML to display
     */
    public function print_textarea($name, $id, $value, $rows, $cols) {
        editors_head_setup();
        $editor = editors_get_preferred_editor(FORMAT_HTML);
        $editor->set_text($value);
        $editor->use_editor($id, []);

        $context = [
            'id' => $id,
            'name' => $name,
            'value' => $value,
            'rows' => $rows,
            'cols' => $cols,
        ];

        return $this->render_from_template('core_form/editor_textarea', $context);
    }

    /**
     * Renders an action menu component.
     *
     * @param action_menu $menu
     * @return string HTML
     */
    public function render_action_menu(action_menu $menu) {

        // We don't want the class icon there!
        foreach ($menu->get_secondary_actions() as $action) {
            if ($action instanceof action_menu\link && $action->has_class('icon')) {
                $action->attributes['class'] = preg_replace('/(^|\s+)icon(\s+|$)/i', '', $action->attributes['class']);
            }
        }

        if ($menu->is_empty()) {
            return '';
        }
        $context = $menu->export_for_template($this);

        return $this->render_from_template('core/action_menu', $context);
    }

    /**
     * Renders a full check API result including summary and details
     *
     * @param check_check $check the check that was run to get details from
     * @param check_result $result the result of a check
     * @param bool $includedetails if true, details are included as well
     * @return string rendered html
     */
    protected function render_check_full_result(check_check $check, check_result $result, bool $includedetails): string {
        // Initially render just badge itself.
        $renderedresult = $this->render_from_template($result->get_template_name(), $result->export_for_template($this));

        // Add summary.
        $renderedresult .= ' ' . $result->get_summary();

        // Wrap in notificaiton.
        $notificationmap = [
            check_result::NA => notification::NOTIFY_INFO,
            check_result::OK => notification::NOTIFY_SUCCESS,
            check_result::INFO => notification::NOTIFY_INFO,
            check_result::UNKNOWN => notification::NOTIFY_WARNING,
            check_result::WARNING => notification::NOTIFY_WARNING,
            check_result::ERROR => notification::NOTIFY_ERROR,
            check_result::CRITICAL => notification::NOTIFY_ERROR,
        ];

        // Get type, or default to error.
        $notificationtype = $notificationmap[$result->get_status()] ?? notification::NOTIFY_ERROR;
        $renderedresult = $this->notification($renderedresult, $notificationtype, false);

        // If adding details, add on new line.
        if ($includedetails) {
            $renderedresult .= $result->get_details();
        }

        // Add the action link.
        if ($actionlink = $check->get_action_link()) {
            $renderedresult .= $this->render_action_link($actionlink);
        }

        return $renderedresult;
    }

    /**
     * Renders a full check API result including summary and details
     *
     * @param check_check $check the check that was run to get details from
     * @param check_result $result the result of a check
     * @param bool $includedetails if details should be included
     * @return string HTML fragment
     */
    public function check_full_result(check_check $check, check_result $result, bool $includedetails = false) {
        return $this->render_check_full_result($check, $result, $includedetails);
    }

    /**
     * Renders a Check API result
     *
     * @param check_result $result
     * @return string HTML fragment
     */
    protected function render_check_result(check_result $result) {
        return $this->render_from_template($result->get_template_name(), $result->export_for_template($this));
    }

    /**
     * Renders a Check API result
     *
     * @param check_result $result
     * @return string HTML fragment
     */
    public function check_result(check_result $result) {
        return $this->render_check_result($result);
    }

    /**
     * @deprecated Since Moodle 4.5. Will be removed in MDL-83221
     */
    #[\core\attribute\deprecated(
        replacement: 'render_action_menu__link',
        since: '4.5',
        mdl: 'MDL-83164',
    )]
    protected function render_action_menu_link(\action_menu_link $action) {
        \core\deprecation::emit_deprecation([$this, __FUNCTION__]);
        return $this->render_action_menu__link($action);
    }

    /**
     * @deprecated Since Moodle 4.5. Will be removed in MDL-83221
     */
    #[\core\attribute\deprecated(
        replacement: 'render_action_menu__filler',
        since: '4.5',
        mdl: 'MDL-83164',
    )]
    protected function render_action_menu_filler(\action_menu_filler $action) {
        \core\deprecation::emit_deprecation([$this, __FUNCTION__]);
        return $this->render_action_menu__filler($action);
    }

    /**
     * @deprecated Since Moodle 4.5. Will be removed in MDL-83221
     */
    #[\core\attribute\deprecated(
        replacement: 'render_action_menu__link_primary',
        since: '4.5',
        mdl: 'MDL-83164',
    )]
    protected function render_action_menu_primary(\action_menu_link $action) {
        \core\deprecation::emit_deprecation([$this, __FUNCTION__]);
        return $this->render_action_menu__link_primary($action);
    }

    /**
     * @deprecated Since Moodle 4.5. Will be removed in MDL-83221
     */
    #[\core\attribute\deprecated(
        replacement: 'render_action_menu__link_secondary',
        since: '4.5',
        mdl: 'MDL-83164',
    )]
    protected function render_action_menu_secondary(\action_menu_link $action) {
        \core\deprecation::emit_deprecation([$this, __FUNCTION__]);
        return $this->render_action_menu__link_secondary($action);
    }

    /**
     * Renders an action_menu link item.
     *
     * @param action_menu\link $action
     * @return string HTML fragment
     */
    protected function render_action_menu__link(action_menu\link $action) {
        return $this->render_from_template('core/action_menu_link', $action->export_for_template($this));
    }

    /**
     * Renders a primary action_menu filler item.
     *
     * @param action_menu\filler $action
     * @return string HTML fragment
     */
    protected function render_action_menu__filler(action_menu\filler $action) {
        return html_writer::span('&nbsp;', 'filler');
    }

    /**
     * Renders a primary action_menu link item.
     *
     * @param action_menu\link_primary $action
     * @return string HTML fragment
     */
    protected function render_action_menu__link_primary(action_menu\link_primary $action) {
        return $this->render_action_menu__link($action);
    }

    /**
     * Renders a secondary action_menu link item.
     *
     * @param action_menu\link_secondary $action
     * @return string HTML fragment
     */
    protected function render_action_menu__link_secondary(action_menu\link_secondary $action) {
        return $this->render_action_menu__link($action);
    }

    /**
     * Prints a nice side block with an optional header.
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

        $id = !empty($bc->attributes['id']) ? $bc->attributes['id'] : uniqid('block-');
        $context = new stdClass();
        $context->skipid = $bc->skipid;
        $context->blockinstanceid = $bc->blockinstanceid ?: uniqid('fakeid-');
        $context->dockable = $bc->dockable;
        $context->id = $id;
        $context->hidden = $bc->collapsible == block_contents::HIDDEN;
        $context->skiptitle = strip_tags($bc->title);
        $context->showskiplink = !empty($context->skiptitle);
        $context->arialabel = $bc->arialabel;
        $context->ariarole = !empty($bc->attributes['role']) ? $bc->attributes['role'] : '';
        $context->class = $bc->attributes['class'];
        $context->type = $bc->attributes['data-block'];
        $context->title = (string) $bc->title;
        $context->showtitle = $context->title !== '';
        $context->content = $bc->content;
        $context->annotation = $bc->annotation;
        $context->footer = $bc->footer;
        $context->hascontrols = !empty($bc->controls);
        if ($context->hascontrols) {
            $context->controls = $this->block_controls($bc->controls, $id);
        }

        return $this->render_from_template('core/block', $context);
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
        $lis = [];
        foreach ($items as $key => $string) {
            $item = html_writer::start_tag('li', ['class' => 'r' . $row]);
            if (!empty($icons[$key])) { // test if the content has an assigned icon
                $item .= html_writer::tag('div', $icons[$key], ['class' => 'column c0 icon-size-3']);
            }
            $item .= html_writer::tag('div', $string, ['class' => 'column c1']);
            $item .= html_writer::end_tag('li');
            $lis[] = $item;
            $row = 1 - $row; // Flip even/odd.
        }
        return html_writer::tag('ul', implode("\n", $lis), ['class' => 'unlist']);
    }

    /**
     * Output all the blocks in a particular region.
     *
     * @param string $region the name of a region on this page.
     * @param boolean $fakeblocksonly Output fake block only.
     * @return string the HTML to be output.
     */
    public function blocks_for_region($region, $fakeblocksonly = false) {
        $blockcontents = $this->page->blocks->get_content_for_region($region, $this);
        $lastblock = null;
        $zones = [];
        foreach ($blockcontents as $bc) {
            if ($bc instanceof block_contents) {
                $zones[] = $bc->title;
            }
        }
        $output = '';

        foreach ($blockcontents as $bc) {
            if ($bc instanceof block_contents) {
                if ($fakeblocksonly && !$bc->is_fake()) {
                    // Skip rendering real blocks if we only want to show fake blocks.
                    continue;
                }
                $output .= $this->block($bc, $region);
                $lastblock = $bc->title;
            } else if ($bc instanceof block_move_target) {
                if (!$fakeblocksonly) {
                    $output .= $this->block_move_target($bc, $zones, $lastblock, $region);
                }
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
        return html_writer::tag('a', html_writer::tag('span', $position, ['class' => 'accesshide']), ['href' => $target->url, 'class' => 'blockmovetarget']);
    }

    /**
     * Renders a special html link with attached action
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@see core_renderer::render_action_link()} instead.
     *
     * @param string|moodle_url $url
     * @param string $text HTML fragment
     * @param null|component_action $action
     * @param null|array $attributes associative array of html link attributes + disabled
     * @param null|pix_icon optional pix icon to render with the link
     * @return string HTML fragment
     */
    public function action_link($url, $text, ?component_action $action = null, ?array $attributes = null, $icon = null) {
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
     * associated actions are setup in JS by {@see core_renderer::add_action_handler()}
     *
     * @param action_link $link
     * @return string HTML fragment
     */
    protected function render_action_link(action_link $link) {
        return $this->render_from_template('core/action_link', $link->export_for_template($this));
    }

    /**
     * Renders an action_icon.
     *
     * This function uses the {@see core_renderer::action_link()} method for the
     * most part. What it does different is prepare the icon as HTML and use it
     * as the link text.
     *
     * Theme developers: If you want to change how action links and/or icons are rendered,
     * consider overriding function {@see core_renderer::render_action_link()} and
     * {@see core_renderer::render_pix_icon()}.
     *
     * @param string|moodle_url $url A string URL or moodel_url
     * @param pix_icon $pixicon
     * @param null|component_action $action
     * @param null|array $attributes associative array of html link attributes + disabled
     * @param bool $linktext show title next to image in link
     * @return string HTML fragment
     */
    public function action_icon(
        $url,
        pix_icon $pixicon,
        ?component_action $action = null,
        ?array $attributes = null,
        $linktext = false,
    ) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $attributes = (array)$attributes;

        if (empty($attributes['class'])) {
            // let ppl override the class via $options
            $attributes['class'] = 'action-icon';
        }

        $text = $pixicon->attributes['alt'];
        // Set the icon as a decorative image. The accessible label should be within the link itself and not the icon.
        $pixicon->attributes['alt'] = '';
        $pixicon->attributes['title'] = '';
        $pixicon->attributes['aria-hidden'] = 'true';

        $attributes['class'] .= ' mx-1 p-1';
        if (!$linktext) {
            // Set a title attribute on the link for sighted users if no text is shown.
            $attributes['title'] = $text;
            // Style the icon button for increased target area.
            $attributes['class'] .= ' btn btn-link icon-no-margin';
            // Make the action text only available to screen readers.
            $attributes['aria-label'] = $text;
            $text = '';
        }

        $icon = $this->render($pixicon);

        return $this->action_link($url, $text . $icon, $action, $attributes);
    }

    /**
     * Print a message along with button choices for Continue/Cancel
     *
     * If a string or moodle_url is given instead of a single_button, method defaults to post.
     *
     * @param string $message The question to ask the user
     * @param single_button|moodle_url|string $continue The single_button component representing the Continue answer.
     *      Can also be a moodle_url or string URL
     * @param single_button|moodle_url|string $cancel The single_button component representing the Cancel answer.
     *      Can also be a moodle_url or string URL
     * @param array $displayoptions Display options (Optional).
     *      Possible options:
     *      - confirmtitle: The title to display above the message
     *      - continuestr: The label to use for the continue button (if $continue is not a single_button)
     *      - cancelstr: The label to use for the cancel button (if $cancel is not a single_button)
     *      - headinglevel: The heading level to use for the title (1-6). Default is 4.
     *      - type: The button type to use for the continue button (if $continue is not a single_button). Default is BUTTON_PRIMARY.
     * @return string HTML fragment
     */
    public function confirm($message, $continue, $cancel, array $displayoptions = []) {

        // Check existing displayoptions.
        $displayoptions['confirmtitle'] = $displayoptions['confirmtitle'] ?? get_string('confirm');
        $displayoptions['continuestr'] = $displayoptions['continuestr'] ?? get_string('continue');
        $displayoptions['cancelstr'] = $displayoptions['cancelstr'] ?? get_string('cancel');
        $headinglevel = $displayoptions['headinglevel'] ?? 4;
        if ($headinglevel < 1 || $headinglevel > 6) {
            throw new coding_exception('The headinglevel option to $OUTPUT->confirm() must be between 1 and 6.');
        }

        if ($continue instanceof single_button) {
            // Continue button should be primary if set to secondary type as it is the fefault.
            if ($continue->type === single_button::BUTTON_SECONDARY) {
                $continue->type = single_button::BUTTON_PRIMARY;
            }
        } else if (is_string($continue)) {
            $continue = new single_button(
                new moodle_url($continue),
                $displayoptions['continuestr'],
                'post',
                $displayoptions['type'] ?? single_button::BUTTON_PRIMARY
            );
        } else if ($continue instanceof moodle_url) {
            $continue = new single_button(
                $continue,
                $displayoptions['continuestr'],
                'post',
                $displayoptions['type'] ?? single_button::BUTTON_PRIMARY
            );
        } else {
            throw new coding_exception('The continue param to $OUTPUT->confirm() must be either a URL (string/moodle_url) or a single_button instance.');
        }

        if ($cancel instanceof single_button) {
            // ok
        } else if (is_string($cancel)) {
            $cancel = new single_button(new moodle_url($cancel), $displayoptions['cancelstr'], 'get');
        } else if ($cancel instanceof moodle_url) {
            $cancel = new single_button($cancel, $displayoptions['cancelstr'], 'get');
        } else {
            throw new coding_exception('The cancel param to $OUTPUT->confirm() must be either a URL (string/moodle_url) or a single_button instance.');
        }

        $attributes = [
            'role' => 'alertdialog',
            'aria-labelledby' => 'modal-header',
            'aria-describedby' => 'modal-body',
            'aria-modal' => 'true',
        ];

        $output = $this->box_start('generalbox modal modal-dialog modal-in-page show', 'notice', $attributes);
        $output .= $this->box_start('modal-content', 'modal-content');
        $output .= $this->box_start('modal-header px-3', 'modal-header');
        $output .= html_writer::tag('h' . $headinglevel, $displayoptions['confirmtitle'], ['class' => 'h4']);
        $output .= $this->box_end();
        $attributes = [
            'role' => 'alert',
            'data-aria-autofocus' => 'true',
        ];
        $output .= $this->box_start('modal-body', 'modal-body', $attributes);
        $output .= html_writer::tag('p', $message);
        $output .= $this->box_end();
        $output .= $this->box_start('modal-footer', 'modal-footer');
        $output .= html_writer::tag('div', $this->render($cancel) . $this->render($continue), ['class' => 'buttons']);
        $output .= $this->box_end();
        $output .= $this->box_end();
        $output .= $this->box_end();
        return $output;
    }

    /**
     * Returns a form with a single button.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@see core_renderer::render_single_button()} instead.
     *
     * @param string|moodle_url $url
     * @param string $label button text
     * @param string $method get or post submit method
     * @param null|array $options associative array {disabled, title, etc.}
     * @return string HTML fragment
     */
    public function single_button($url, $label, $method = 'post', ?array $options = null) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $button = new single_button($url, $label, $method);

        foreach ((array)$options as $key => $value) {
            if (property_exists($button, $key)) {
                $button->$key = $value;
            } else {
                $button->set_attribute($key, $value);
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
        return $this->render_from_template('core/single_button', $button->export_for_template($this));
    }

    /**
     * Returns a form with a single select widget.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@see core_renderer::render_single_select()} instead.
     *
     * @param moodle_url $url form action target, includes hidden fields
     * @param string $name name of selection field - the changing parameter in url
     * @param array $options list of options
     * @param string $selected selected element
     * @param array $nothing
     * @param string $formid
     * @param array $attributes other attributes for the single select
     * @return string HTML fragment
     */
    public function single_select(
        $url,
        $name,
        array $options,
        $selected = '',
        $nothing = ['' => 'choosedots'],
        $formid = null,
        $attributes = []
    ) {
        if (!($url instanceof moodle_url)) {
            $url = new moodle_url($url);
        }
        $select = new single_select($url, $name, $options, $selected, $nothing, $formid);

        if (array_key_exists('label', $attributes)) {
            $select->set_label($attributes['label']);
            unset($attributes['label']);
        }
        $select->attributes = $attributes;

        return $this->render($select);
    }

    /**
     * Returns a dataformat selection and download form
     *
     * @param string $label A text label
     * @param moodle_url|string $base The download page url
     * @param string $name The query param which will hold the type of the download
     * @param array $params Extra params sent to the download page
     * @return string HTML fragment
     */
    public function download_dataformat_selector($label, $base, $name = 'dataformat', $params = []) {
        $formats = plugin_manager::instance()->get_plugins_of_type('dataformat');
        $options = [];
        foreach ($formats as $format) {
            if ($format->is_enabled()) {
                $options[] = [
                    'value' => $format->name,
                    'label' => get_string('dataformat', $format->component),
                ];
            }
        }
        $hiddenparams = [];
        foreach ($params as $key => $value) {
            $hiddenparams[] = [
                'name' => $key,
                'value' => $value,
            ];
        }
        $data = [
            'label' => $label,
            'base' => $base,
            'name' => $name,
            'params' => $hiddenparams,
            'options' => $options,
            'sesskey' => sesskey(),
            'submit' => get_string('download'),
        ];

        return $this->render_from_template('core/dataformat_selector', $data);
    }


    /**
     * Internal implementation of single_select rendering
     *
     * @param single_select $select
     * @return string HTML fragment
     */
    protected function render_single_select(single_select $select) {
        return $this->render_from_template('core/single_select', $select->export_for_template($this));
    }

    /**
     * Returns a form with a url select widget.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@see core_renderer::render_url_select()} instead.
     *
     * @param array $urls list of urls - array('/course/view.php?id=1'=>'Frontpage', ....)
     * @param string $selected selected element
     * @param array $nothing
     * @param string $formid
     * @return string HTML fragment
     */
    public function url_select(array $urls, $selected, $nothing = ['' => 'choosedots'], $formid = null) {
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
        return $this->render_from_template('core/url_select', $select->export_for_template($this));
    }

    /**
     * Returns a string containing a link to the user documentation.
     * Also contains an icon by default. Shown to teachers and admin only.
     *
     * @param string $path The page link after doc root and language, no leading slash.
     * @param string $text The text to be displayed for the link
     * @param boolean $forcepopup Whether to force a popup regardless of the value of $CFG->doctonewwindow
     * @param array $attributes htm attributes
     * @return string
     */
    public function doc_link($path, $text = '', $forcepopup = false, array $attributes = []) {
        global $CFG;

        $icon = $this->pix_icon('book', '', 'moodle');

        $attributes['href'] = new moodle_url(get_docs_url($path));
        $newwindowicon = '';
        if (!empty($CFG->doctonewwindow) || $forcepopup) {
            $attributes['target'] = '_blank';
            $newwindowicon = $this->pix_icon(
                'i/externallink',
                get_string('opensinnewwindow'),
                attributes: ['class' => 'ms-1'],
            );
        }

        return html_writer::tag('a', $icon . $text . $newwindowicon, $attributes);
    }

    /**
     * Return HTML for an image_icon.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@see core_renderer::render_image_icon()} instead.
     *
     * @param string $pix short pix name
     * @param string $alt mandatory alt attribute
     * @param string $component standard compoennt name like 'moodle', 'mod_forum', etc.
     * @param null|array $attributes htm attributes
     * @return string HTML fragment
     */
    public function image_icon($pix, $alt, $component = 'moodle', ?array $attributes = null) {
        $icon = new image_icon($pix, $alt, $component, $attributes);
        return $this->render($icon);
    }

    /**
     * Renders a pix_icon widget and returns the HTML to display it.
     *
     * @param image_icon $icon
     * @return string HTML fragment
     */
    protected function render_image_icon(image_icon $icon) {
        $system = icon_system::instance(icon_system::STANDARD);
        return $system->render_pix_icon($this, $icon);
    }

    /**
     * Return HTML for a pix_icon.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@see core_renderer::render_pix_icon()} instead.
     *
     * @param string $pix short pix name
     * @param string $alt mandatory alt attribute
     * @param string $component standard compoennt name like 'moodle', 'mod_forum', etc.
     * @param null|array $attributes htm lattributes
     * @return string HTML fragment
     */
    public function pix_icon($pix, $alt, $component = 'moodle', ?array $attributes = null) {
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
        $system = icon_system::instance();
        return $system->render_pix_icon($this, $icon);
    }

    /**
     * Return HTML to display an emoticon icon.
     *
     * @param pix_emoticon $emoticon
     * @return string HTML fragment
     */
    protected function render_pix_emoticon(pix_emoticon $emoticon) {
        $system = icon_system::instance(icon_system::STANDARD);
        return $system->render_pix_icon($this, $emoticon);
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
            return null;// ratings are turned off
        }

        $ratingmanager = new rating_manager();
        // Initialise the JavaScript so ratings can be done by AJAX.
        $ratingmanager->initialise_rating_javascript($this->page);

        $strrate = get_string("rate", "rating");
        $ratinghtml = ''; // the string we'll return

        // permissions check - can they view the aggregate?
        if ($rating->user_can_view_aggregate()) {
            $aggregatelabel = $ratingmanager->get_aggregate_label($rating->settings->aggregationmethod);
            $aggregatelabel = html_writer::tag('span', $aggregatelabel, ['class' => 'rating-aggregate-label']);
            $aggregatestr   = $rating->get_aggregate_string();

            $aggregatehtml  = html_writer::tag('span', $aggregatestr, ['id' => 'ratingaggregate' . $rating->itemid, 'class' => 'ratingaggregate']) . ' ';
            if ($rating->count > 0) {
                $countstr = "({$rating->count})";
            } else {
                $countstr = '-';
            }
            $aggregatehtml .= html_writer::tag('span', $countstr, ['id' => "ratingcount{$rating->itemid}", 'class' => 'ratingcount']) . ' ';

            if ($rating->settings->permissions->viewall && $rating->settings->pluginpermissions->viewall) {
                $nonpopuplink = $rating->get_view_ratings_url();
                $popuplink = $rating->get_view_ratings_url(true);

                $action = new popup_action('click', $popuplink, 'ratings', ['height' => 400, 'width' => 600]);
                $aggregatehtml = $this->action_link($nonpopuplink, $aggregatehtml, $action);
            }

            $ratinghtml .= html_writer::tag('span', $aggregatelabel . $aggregatehtml, ['class' => 'rating-aggregate-container']);
        }

        $formstart = null;
        // if the item doesn't belong to the current user, the user has permission to rate
        // and we're within the assessable period
        if ($rating->user_can_rate()) {
            $rateurl = $rating->get_rate_url();
            $inputs = $rateurl->params();

            // start the rating form
            $formattrs = [
                'id'     => "postrating{$rating->itemid}",
                'class'  => 'postratingform',
                'method' => 'post',
                'action' => $rateurl->out_omit_querystring(),
            ];
            $formstart  = html_writer::start_tag('form', $formattrs);
            $formstart .= html_writer::start_tag('div', ['class' => 'ratingform hstack gap-2']);

            // add the hidden inputs
            foreach ($inputs as $name => $value) {
                $attributes = ['type' => 'hidden', 'class' => 'ratinginput', 'name' => $name, 'value' => $value];
                $formstart .= html_writer::empty_tag('input', $attributes);
            }

            if (empty($ratinghtml)) {
                $ratinghtml .= $strrate . ': ';
            }
            $ratinghtml = $formstart . $ratinghtml;

            $scalearray = [RATING_UNSET_RATING => $strrate . '...'] + $rating->settings->scale->scaleitems;
            $scaleattrs = ['class' => 'postratingmenu ratinginput', 'id' => 'menurating' . $rating->itemid];
            $ratinghtml .= html_writer::label($rating->rating, 'menurating' . $rating->itemid, false, ['class' => 'accesshide']);
            $ratinghtml .= html_writer::select($scalearray, 'rating', $rating->rating, false, $scaleattrs);

            // output submit button
            $ratinghtml .= html_writer::start_tag('span', ['class' => "ratingsubmit"]);

            $attributes = ['type' => 'submit', 'class' => 'postratingmenusubmit', 'id' => 'postratingsubmit' . $rating->itemid, 'value' => s(get_string('rate', 'rating'))];
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
            $image = $this->pix_icon($icon, $iconalt, $component);
        }

        $help = '';
        if ($helpidentifier) {
            $help = $this->help_icon($helpidentifier, $component);
        }

        return $this->heading($image . $text . $help, $level, $classnames);
    }

    /**
     * Returns HTML to display a help icon.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@see core_renderer::render_help_icon()} instead.
     *
     * @param string $identifier The keyword that defines a help page
     * @param string $component component name
     * @param string|bool $linktext true means use $title as link text, string means link text value
     * @param string|object|array|int $a An object, string or number that can be used
     *      within translation strings
     * @return string HTML fragment
     */
    public function help_icon($identifier, $component = 'moodle', $linktext = '', $a = null) {
        $icon = new help_icon($identifier, $component, $a);
        $icon->diag_strings();
        if ($linktext === true) {
            $icon->linktext = get_string($icon->identifier, $icon->component, $a);
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
        $context = $helpicon->export_for_template($this);
        return $this->render_from_template('core/help_icon', $context);
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

        $title = get_string('helpprefix2', '', $scale->name) . ' (' . get_string('newwindow') . ')';

        $icon = $this->pix_icon('help', get_string('scales'), 'moodle');

        $scaleid = abs($scale->id);

        $link = new moodle_url('/course/scales.php', ['id' => $courseid, 'list' => true, 'scaleid' => $scaleid]);
        $action = new popup_action('click', $link, 'ratingscale');

        return html_writer::tag('span', $this->action_link($link, $icon, $action), ['class' => 'helplink']);
    }

    /**
     * Creates and returns a spacer image with optional line break.
     *
     * @param null|array $attributes Any HTML attributes to add to the spaced.
     * @param bool $br Include a BR after the spacer.... DON'T USE THIS. Don't be
     *     laxy do it with CSS which is a much better solution.
     * @return string HTML fragment
     */
    public function spacer(?array $attributes = null, $br = false) {
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
     * {@see core_renderer::render_user_picture()} instead.
     *
     * @param stdClass $user Object with at least fields id, picture, imagealt, firstname, lastname
     *     If any of these are missing, the database is queried. Avoid this
     *     if at all possible, particularly for reports. It is very bad for performance.
     * @param null|array $options associative array with user picture options, used only if not a user_picture object,
     *     options are:
     *     - courseid=$this->page->course->id (course id of user profile in link)
     *     - size=35 (size of image)
     *     - link=true (make image clickable - the link leads to user profile)
     *     - popup=false (open in popup)
     *     - alttext=true (add image alt attribute)
     *     - class = image class attribute (default 'userpicture')
     *     - visibletoscreenreaders=true (whether to be visible to screen readers)
     *     - includefullname=false (whether to include the user's full name together with the user picture)
     *     - includetoken = false (whether to use a token for authentication. True for current user, int value for other user id)
     * @return string HTML fragment
     */
    public function user_picture(stdClass $user, ?array $options = null) {
        $userpicture = new user_picture($user);
        foreach ((array)$options as $key => $value) {
            if (property_exists($userpicture, $key)) {
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
        global $CFG;

        $user = $userpicture->user;
        $canviewfullnames = has_capability('moodle/site:viewfullnames', $this->page->context);

        $alt = '';
        if ($userpicture->alttext) {
            if (!empty($user->imagealt)) {
                $alt = trim($user->imagealt);
            }
        }

        // If the user picture is being rendered as a link but without the full name, an empty alt text for the user picture
        // would mean that the link displayed will not have any discernible text. This becomes an accessibility issue,
        // especially to screen reader users. Use the user's full name by default for the user picture's alt-text if this is
        // the case.
        if ($userpicture->link && !$userpicture->includefullname && empty($alt)) {
            $alt = fullname($user);
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

        $attributes = ['src' => $src, 'class' => $class, 'width' => $size, 'height' => $size];
        if (!$userpicture->visibletoscreenreaders) {
            $alt = '';
        }
        $attributes['alt'] = $alt;

        if (!empty($alt)) {
            $attributes['title'] = $alt;
        }

        // Get the image html output first, auto generated based on initials if one isn't already set.
        if ($user->picture == 0 && empty($CFG->enablegravatar) && !defined('BEHAT_SITE_RUNNING')) {
            $initials = \core_user::get_initials($user);
            $fullname = fullname($userpicture->user, $canviewfullnames);
            // Don't modify in corner cases where neither the firstname nor the lastname appears.
            $output = html_writer::tag(
                'span',
                $initials,
                [
                    'class' => 'userinitials size-' . $size,
                    'title' => $fullname,
                    'aria-label' => $fullname,
                    'role' => 'img',
                ]
            );
        } else {
            $output = html_writer::empty_tag('img', $attributes);
        }

        // Show fullname together with the picture when desired.
        if ($userpicture->includefullname) {
            $output .= fullname($userpicture->user, $canviewfullnames);
        }

        if (empty($userpicture->courseid)) {
            $courseid = $this->page->course->id;
        } else {
            $courseid = $userpicture->courseid;
        }
        if ($courseid == SITEID) {
            $url = new moodle_url('/user/profile.php', ['id' => $user->id]);
        } else {
            $url = new moodle_url('/user/view.php', ['id' => $user->id, 'course' => $courseid]);
        }

        // Then wrap it in link if needed. Also we don't wrap it in link if the link redirects to itself.
        if (
            !$userpicture->link ||
                ($this->page->has_set_url() && $this->page->url == $url)
        ) { // Protect against unset page->url.
            return $output;
        }

        $attributes = ['href' => $url, 'class' => 'd-inline-block aabtn'];
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
     * Returns HTML to display the file picker
     *
     * <pre>
     * $OUTPUT->file_picker($options);
     * </pre>
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@see core_renderer::render_file_picker()} instead.
     *
     * @param stdClass $options file manager options
     *   options are:
     *       maxbytes=>-1,
     *       itemid=>0,
     *       client_id=>uniqid(),
     *       acepted_types=>'*',
     *       return_types=>FILE_INTERNAL,
     *       context=>current page context
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
        $options = $fp->options;
        $client_id = $options->client_id;
        $strsaved = get_string('filesaved', 'repository');
        $straddfile = get_string('openpicker', 'repository');
        $strloading  = get_string('loading', 'repository');
        $strdndenabled = get_string('dndenabled_inbox', 'moodle');
        $strdroptoupload = get_string('droptoupload', 'moodle');
        $iconprogress = $this->pix_icon('i/loading_small', $strloading) . '';

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
            $maxsize = get_string('maxfilesize', 'moodle', display_size($size, 0));
        }
        if ($options->buttonname) {
            $buttonname = ' name="' . $options->buttonname . '"';
        } else {
            $buttonname = '';
        }
        $html = <<<EOD
<div class="filemanager-loading mdl-align" id='filepicker-loading-{$client_id}'>
$iconprogress
</div>
<div id="filepicker-wrapper-{$client_id}" class="mdl-left w-100" style="display:none">
    <div>
        <input type="button" class="btn btn-secondary fp-btn-choose" id="filepicker-button-{$client_id}" value="{$straddfile}"{$buttonname}/>
        <span> $maxsize </span>
    </div>
EOD;
        if ($options->env != 'url') {
            $html .= <<<EOD
    <div id="file_info_{$client_id}" class="mdl-left filepicker-filelist" style="position: relative">
    <div class="filepicker-filename">
        <div class="filepicker-container">$currentfile
            <div class="dndupload-message">$strdndenabled <br/>
                <div class="dndupload-arrow d-flex"><i class="fa fa-arrow-circle-o-down fa-3x m-auto"></i></div>
            </div>
        </div>
        <div class="dndupload-progressbars"></div>
    </div>
    <div>
        <div class="dndupload-target">{$strdroptoupload}<br/>
            <div class="dndupload-arrow d-flex"><i class="fa fa-arrow-circle-o-down fa-3x m-auto"></i></div>
        </div>
    </div>
    </div>
EOD;
        }
        $html .= '</div>';
        return $html;
    }

    /**
     * Returns HTML to display a "Turn editing on/off" button in a form.
     *
     * @param moodle_url $url The URL + params to send through when clicking the button
     * @param string $method
     * @return ?string HTML the button
     */
    public function edit_button(moodle_url $url, string $method = 'post') {

        if ($this->page->theme->haseditswitch == true) {
            return;
        }
        $url->param('sesskey', sesskey());
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $editstring = get_string('turneditingoff');
        } else {
            $url->param('edit', 'on');
            $editstring = get_string('turneditingon');
        }

        return $this->single_button($url, $editstring, $method);
    }

    /**
     * Create a navbar switch for toggling editing mode.
     *
     * @return ?string Html containing the edit switch
     */
    public function edit_switch() {
        if ($this->page->user_allowed_editing()) {
            $temp = (object) [
                'legacyseturl' => (new moodle_url('/editmode.php'))->out(false),
                'pagecontextid' => $this->page->context->id,
                'pageurl' => $this->page->url,
                'sesskey' => sesskey(),
            ];
            if ($this->page->user_is_editing()) {
                $temp->checked = true;
            }
            return $this->render_from_template('core/editswitch', $temp);
        }
    }

    /**
     * Returns HTML to display a simple button to close a window
     *
     * @param string $text The lang string for the button's label (already output from get_string())
     * @return string html fragment
     */
    public function close_window_button($text = '') {
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
        $message = $this->pix_icon('i/warning', get_string('error'), '', ['class' => 'icon', 'title' => '']) . $message;
        return html_writer::tag('span', $message, ['class' => 'error']);
    }

    /**
     * Do not call this function directly.
     *
     * To terminate the current script with a fatal error, throw an exception.
     * Doing this will then call this function to display the error, before terminating the execution.
     *
     * @param string $message The message to output
     * @param string $moreinfourl URL where more info can be found about the error
     * @param string $link Link for the Continue button
     * @param array $backtrace The execution backtrace
     * @param null|string $debuginfo Debugging information
     * @param string $errorcode
     * @return string the HTML to output.
     */
    public function fatal_error($message, $moreinfourl, $link, $backtrace, $debuginfo = null, $errorcode = "") {
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
            } else if (core_useragent::check_safari_ios_version(602) && !empty($_SERVER['HTTP_X_PLAYBACK_SESSION_ID'])) {
                // Coax iOS 10 into sending the session cookie.
                @header($protocol . ' 403 Forbidden');
            } else {
                // Must stop byteserving attempts somehow,
                // this is weird but Chrome PDF viewer can be stopped only with 407!
                @header($protocol . ' 407 Proxy Authentication Required');
            }

            $this->page->set_context(null); // ugly hack - make sure page context is set to something, we do not want bogus warnings here
            $this->page->set_url('/'); // no url
            // $this->page->set_pagelayout('base'); //TODO: MDL-20676 blocks on error pages are weird, unfortunately it somehow detect the pagelayout from URL :-(
            $this->page->set_title(get_string('error'));
            $this->page->set_heading($this->page->course->fullname);
            // No need to display the activity header when encountering an error.
            $this->page->activityheader->disable();
            $output .= $this->header();
        }

        $message = '<p class="errormessage">' . s($message) . '</p>' .
                '<p class="errorcode"><a href="' . s($moreinfourl) . '">' .
                get_string('moreinformation') . '</a></p>';
        if (empty($CFG->rolesactive)) {
            $message .= '<p class="errormessage">' . get_string('installproblem', 'error') . '</p>';
            // It is usually not possible to recover from errors triggered during installation, you may need to create a new database or use a different database prefix for new installation.
        }
        $output .= $this->box($message, 'errorbox alert alert-danger', null, ['data-rel' => 'fatalerror']);

        if ($CFG->debugdeveloper) {
            $labelsep = get_string('labelsep', 'langconfig');
            if (!empty($debuginfo)) {
                $debuginfo = s($debuginfo); // removes all nasty JS
                $debuginfo = str_replace("\n", '<br />', $debuginfo); // keep newlines
                $label = get_string('debuginfo', 'debug') . $labelsep;
                $output .= $this->notification("<strong>$label</strong> " . $debuginfo, 'notifytiny');
            }
            if (!empty($backtrace)) {
                $label = get_string('stacktrace', 'debug') . $labelsep;
                $output .= $this->notification("<strong>$label</strong> " . format_backtrace($backtrace), 'notifytiny');
            }
            if ($obbuffer !== '') {
                $label = get_string('outputbuffer', 'debug') . $labelsep;
                $output .= $this->notification("<strong>$label</strong> " . s($obbuffer), 'notifytiny');
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
     * Output a notification (that is, a status message about something that has just happened).
     *
     * Note: \core\notification::add() may be more suitable for your usage.
     *
     * @param string $message The message to print out.
     * @param ?string $type   The type of notification. See constants on notification.
     * @param bool $closebutton Whether to show a close icon to remove the notification (default true).
     * @param ?string $title  The title of the notification.
     * @param ?string $titleicon if the title should have an icon you can give the icon name with the component
     *  (e.g. 'i/circleinfo, core' or 'i/circleinfo' if the icon is from core)
     * @return string the HTML to output.
     */
    public function notification($message, $type = null, $closebutton = true, ?string $title = null, ?string $titleicon = null) {
        $typemappings = [
            // Valid types.
            'success'           => notification::NOTIFY_SUCCESS,
            'info'              => notification::NOTIFY_INFO,
            'warning'           => notification::NOTIFY_WARNING,
            'error'             => notification::NOTIFY_ERROR,

            // Legacy types mapped to current types.
            'notifyproblem'     => notification::NOTIFY_ERROR,
            'notifytiny'        => notification::NOTIFY_ERROR,
            'notifyerror'       => notification::NOTIFY_ERROR,
            'notifysuccess'     => notification::NOTIFY_SUCCESS,
            'notifymessage'     => notification::NOTIFY_INFO,
            'notifyredirect'    => notification::NOTIFY_INFO,
            'redirectmessage'   => notification::NOTIFY_INFO,
        ];

        $extraclasses = [];

        if ($type) {
            if (strpos($type, ' ') === false) {
                // No spaces in the list of classes, therefore no need to loop over and determine the class.
                if (isset($typemappings[$type])) {
                    $type = $typemappings[$type];
                } else {
                    // The value provided did not match a known type. It must be an extra class.
                    $extraclasses = [$type];
                }
            } else {
                // Identify what type of notification this is.
                $classarray = explode(' ', self::prepare_classes($type));

                // Separate out the type of notification from the extra classes.
                foreach ($classarray as $class) {
                    if (isset($typemappings[$class])) {
                        $type = $typemappings[$class];
                    } else {
                        $extraclasses[] = $class;
                    }
                }
            }
        }

        $notification = new notification($message, $type, $closebutton, $title, $titleicon);
        if (count($extraclasses)) {
            $notification->set_extra_classes($extraclasses);
        }

        // Return the rendered template.
        return $this->render_from_template($notification->get_template_name(), $notification->export_for_template($this));
    }

    /**
     * Render a notification (that is, a status message about something that has
     * just happened).
     *
     * @param notification $notification the notification to print out
     * @return string the HTML to output.
     */
    protected function render_notification(notification $notification) {
        return $this->render_from_template($notification->get_template_name(), $notification->export_for_template($this));
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
        $button = new single_button($url, get_string('continue'), 'get', single_button::BUTTON_PRIMARY);
        $button->class = 'continuebutton';

        return $this->render($button);
    }

    /**
     * Returns HTML to display a single paging bar to provide access to other pages  (usually in a search)
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@see core_renderer::render_paging_bar()} instead.
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
     * Returns HTML to display the paging bar.
     *
     * @param paging_bar $pagingbar
     * @return string the HTML to output.
     */
    protected function render_paging_bar(paging_bar $pagingbar) {
        // Any more than 10 is not usable and causes weird wrapping of the pagination.
        $pagingbar->maxdisplay = 10;
        return $this->render_from_template('core/paging_bar', $pagingbar->export_for_template($this));
    }

    /**
     * Returns HTML to display initials bar to provide access to other pages  (usually in a search)
     *
     * @param string $current the currently selected letter.
     * @param string $class class name to add to this initial bar.
     * @param string $title the name to put in front of this initial bar.
     * @param string $urlvar URL parameter name for this initial.
     * @param string $url URL object.
     * @param array $alpha of letters in the alphabet.
     * @param bool $minirender Return a trimmed down view of the initials bar.
     * @return string the HTML to output.
     */
    public function initials_bar($current, $class, $title, $urlvar, $url, $alpha = null, bool $minirender = false) {
        $ib = new initials_bar($current, $class, $title, $urlvar, $url, $alpha, $minirender);
        return $this->render($ib);
    }

    /**
     * Internal implementation of initials bar rendering.
     *
     * @param initials_bar $initialsbar
     * @return string
     */
    protected function render_initials_bar(initials_bar $initialsbar) {
        return $this->render_from_template('core/initials_bar', $initialsbar->export_for_template($this));
    }

    /**
     * Output the place a skip link goes to.
     *
     * @param string $id The target name from the corresponding $PAGE->requires->skip_link_to($target) call.
     * @return string the HTML to output.
     */
    public function skip_link_target($id = null) {
        return html_writer::span('', '', ['id' => $id]);
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
        $level = (int) $level;
        if ($level < 1 or $level > 6) {
            throw new coding_exception('Heading level must be an integer between 1 and 6.');
        }
        return html_writer::tag('h' . $level, $text, ['id' => $id, 'class' => renderer_base::prepare_classes($classes)]);
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
    public function box($contents, $classes = 'generalbox', $id = null, $attributes = []) {
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
    public function box_start($classes = 'generalbox', $id = null, $attributes = []) {
        $this->opencontainers->push('box', html_writer::end_tag('div'));
        $attributes['id'] = $id;
        $attributes['class'] = 'box py-3 ' . renderer_base::prepare_classes($classes);
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
     * Outputs a paragraph.
     *
     * @param string $contents The contents of the paragraph
     * @param string|null $classes A space-separated list of CSS classes
     * @param string|null $id An optional ID
     * @return string the HTML to output.
     */
    public function paragraph(string $contents, ?string $classes = null, ?string $id = null): string {
        return html_writer::tag(
            'p',
            $contents,
            ['id' => $id, 'class' => renderer_base::prepare_classes($classes)]
        );
    }

    /**
     * Outputs a screen reader only inline text.
     *
     * @param string $contents The contents of the paragraph
     * @return string the HTML to output.
     * @deprecated since 5.0. Use visually_hidden_text() instead.
     * @todo Final deprecation in Moodle 6.0. See MDL-83671.
     */
    #[\core\attribute\deprecated('core_renderer::visually_hidden_text()', since: '5.0', mdl: 'MDL-81825')]
    public function sr_text(string $contents): string {
        \core\deprecation::emit_deprecation([$this, __FUNCTION__]);
        return $this->visually_hidden_text($contents);
    }

    /**
     * Outputs a visually hidden inline text (but accessible to assistive technologies).
     *
     * @param string $contents The contents of the paragraph
     * @return string the HTML to output.
     */
    public function visually_hidden_text(string $contents): string {
        return html_writer::tag(
            'span',
            $contents,
            ['class' => 'visually-hidden']
        ) . ' ';
    }

    /**
     * Outputs a screen reader only inline text.
     *
     * @param string $contents The content of the badge
     * @param badge $badgestyle The style of the badge (default is PRIMARY)
     * @param string $title An optional title of the badge
     * @return string the HTML to output.
     */
    public function notice_badge(
        string $contents,
        badge $badgestyle = badge::PRIMARY,
        string $title = '',
    ): string {
        if ($contents === '') {
            return '';
        }
        // We want the badges to be read as content in parentesis.
        $contents = trim($this->visually_hidden_text(' ('))
            . $contents
            . trim($this->visually_hidden_text(')'));

        $attributes = ['class' => 'ms-1 ' . $badgestyle->classes()];
        if ($title !== '') {
            $attributes['title'] = $title;
        }

        return html_writer::tag('span', $contents, $attributes);
    }

    /**
     * Outputs a container.
     *
     * @param string $contents The contents of the box
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @param array $attributes Optional other attributes as array
     * @return string the HTML to output.
     */
    public function container($contents, $classes = null, $id = null, $attributes = []) {
        return $this->container_start($classes, $id, $attributes) . $contents . $this->container_end();
    }

    /**
     * Outputs the opening section of a container.
     *
     * @param string $classes A space-separated list of CSS classes
     * @param string $id An optional ID
     * @param array $attributes Optional other attributes as array
     * @return string the HTML to output.
     */
    public function container_start($classes = null, $id = null, $attributes = []) {
        $this->opencontainers->push('container', html_writer::end_tag('div'));
        $attributes = array_merge(['id' => $id, 'class' => renderer_base::prepare_classes($classes)], $attributes);
        return html_writer::start_tag('div', $attributes);
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
    public function tree_block_contents($items, $attrs = []) {
        // exit if empty, we don't want an empty ul element
        if (empty($items)) {
            return '';
        }
        // array of nested li elements
        $lis = [];
        foreach ($items as $item) {
            // this applies to the li item which contains all child lists too
            $content = $item->content($this);
            $liclasses = [$item->get_css_type()];
            if (!$item->forceopen || (!$item->forceopen && $item->collapse) || ($item->children->count() == 0  && $item->nodetype == navigation_node::NODETYPE_BRANCH)) {
                $liclasses[] = 'collapsed';
            }
            if ($item->isactive === true) {
                $liclasses[] = 'current_branch';
            }
            $liattr = ['class' => join(' ', $liclasses)];
            // class attribute on the div item which only contains the item content
            $divclasses = ['tree_item'];
            if ($item->children->count() > 0  || $item->nodetype == navigation_node::NODETYPE_BRANCH) {
                $divclasses[] = 'branch';
            } else {
                $divclasses[] = 'leaf';
            }
            if (!empty($item->classes) && count($item->classes) > 0) {
                $divclasses[] = join(' ', $item->classes);
            }
            $divattr = ['class' => join(' ', $divclasses)];
            if (!empty($item->id)) {
                $divattr['id'] = $item->id;
            }
            $content = html_writer::tag('p', $content, $divattr) . $this->tree_block_contents($item->children);
            if (!empty($item->preceedwithhr) && $item->preceedwithhr === true) {
                $content = html_writer::empty_tag('hr') . $content;
            }
            $content = html_writer::tag('li', $content, $liattr);
            $lis[] = $content;
        }
        return html_writer::tag('ul', implode("\n", $lis), $attrs);
    }

    /**
     * Returns a search box.
     *
     * @param  string $id     The search box wrapper div id, defaults to an autogenerated one.
     * @return string         HTML with the search form hidden by default.
     */
    public function search_box($id = false) {
        global $CFG;

        // Accessing $CFG directly as using \core_search::is_global_search_enabled would
        // result in an extra included file for each site, even the ones where global search
        // is disabled.
        if (empty($CFG->enableglobalsearch) || !has_capability('moodle/search:query', context_system::instance())) {
            return '';
        }

        $data = [
            'action' => new moodle_url('/search/index.php'),
            'hiddenfields' => (object) ['name' => 'context', 'value' => $this->page->context->id],
            'inputname' => 'q',
            'searchstring' => get_string('search'),
            'grouplabel' => get_string('sitewidesearch', 'search'),
        ];
        return $this->render_from_template('core/search_input_navbar', $data);
    }

    /**
     * Allow plugins to provide some content to be rendered in the navbar.
     * The plugin must define a PLUGIN_render_navbar_output function that returns
     * the HTML they wish to add to the navbar.
     *
     * @return string HTML for the navbar
     */
    public function navbar_plugin_output() {
        $output = '';

        // Give subsystems an opportunity to inject extra html content. The callback
        // must always return a string containing valid html.
        foreach (\core_component::get_core_subsystems() as $name => $path) {
            if ($path) {
                $output .= component_callback($name, 'render_navbar_output', [$this], '');
            }
        }

        if ($pluginsfunction = get_plugins_with_function('render_navbar_output')) {
            foreach ($pluginsfunction as $plugintype => $plugins) {
                foreach ($plugins as $pluginfunction) {
                    $output .= $pluginfunction($this);
                }
            }
        }

        return $output;
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

        // Get some navigation opts.
        $opts = user_get_user_navigation_info($user, $this->page);

        if (!empty($opts->unauthenticateduser)) {
            $returnstr = get_string($opts->unauthenticateduser['content'], 'moodle');
            // If not logged in, show the typical not-logged-in string.
            if (!$loginpage && (!$opts->unauthenticateduser['guest'] || $withlinks)) {
                $returnstr .= " (<a href=\"$loginurl\">" . get_string('login') . '</a>)';
            }

            return html_writer::div(
                html_writer::span(
                    $returnstr,
                    'login nav-link'
                ),
                $usermenuclasses
            );
        }

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
                ['class' => 'meta viewingas']
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
            html_writer::span($usertextcontents, 'usertext me-1') .
            html_writer::span($avatarcontents, $avatarclasses),
            'userbutton'
        );

        // Create a divider (well, a filler).
        $divider = new action_menu\filler();
        $divider->primary = false;

        $am = new action_menu();
        $am->set_menu_trigger(
            $returnstr,
            'nav-link'
        );
        $am->set_action_label(get_string('usermenu'));
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
                            $pix = new pix_icon($value->pix, '', null, ['class' => 'iconsmall']);
                        } else if (isset($value->imgsrc) && !empty($value->imgsrc)) {
                            $value->title = html_writer::img(
                                $value->imgsrc,
                                $value->title,
                                ['class' => 'iconsmall']
                            ) . $value->title;
                        }

                        $al = new action_menu\link_secondary(
                            $value->url,
                            $pix,
                            $value->title,
                            ['class' => 'icon']
                        );
                        if (!empty($value->titleidentifier)) {
                            $al->attributes['data-title'] = $value->titleidentifier;
                        }
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
     * Secure layout login info.
     *
     * @return string
     */
    public function secure_layout_login_info() {
        if (get_config('core', 'logininfoinsecurelayout')) {
            return $this->login_info(false);
        } else {
            return '';
        }
    }

    /**
     * Returns the language menu in the secure layout.
     *
     * No custom menu items are passed though, such that it will render only the language selection.
     *
     * @return string
     */
    public function secure_layout_language_menu() {
        if (get_config('core', 'langmenuinsecurelayout')) {
            $custommenu = new custom_menu('', current_language());
            return $this->render_custom_menu($custommenu);
        } else {
            return '';
        }
    }

    /**
     * This renders the navbar.
     * Uses bootstrap compatible html.
     */
    public function navbar() {
        return $this->render_from_template('core/navbar', $this->page->navbar);
    }

    /**
     * Renders a breadcrumb navigation node object.
     *
     * @param breadcrumb_navigation_node $item The navigation node to render.
     * @return string HTML fragment
     */
    protected function render_breadcrumb_navigation_node(breadcrumb_navigation_node $item) {

        if ($item->action instanceof moodle_url) {
            $content = $item->get_content();
            $title = $item->get_title();
            $attributes = [];
            $attributes['itemprop'] = 'url';
            if ($title !== '') {
                $attributes['title'] = $title;
            }
            if ($item->hidden) {
                $attributes['class'] = 'dimmed_text';
            }
            if ($item->is_last()) {
                $attributes['aria-current'] = 'page';
            }
            $content = html_writer::tag('span', $content, ['itemprop' => 'title']);
            $content = html_writer::link($item->action, $content, $attributes);

            $attributes = [];
            $attributes['itemscope'] = '';
            $attributes['itemtype'] = 'http://data-vocabulary.org/Breadcrumb';
            $content = html_writer::tag('span', $content, $attributes);
        } else {
            $content = $this->render_navigation_node($item);
        }
        return $content;
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
            $content = $icon . $content; // use CSS for spacing of icons
        }
        if ($item->helpbutton !== null) {
            $content = trim($item->helpbutton) . html_writer::tag('span', $content, ['class' => 'clearhelpbutton', 'tabindex' => '0']);
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
            $attributes = [];
            if ($title !== '') {
                $attributes['title'] = $title;
            }
            if ($item->hidden) {
                $attributes['class'] = 'dimmed_text';
            }
            $content = html_writer::link($item->action, $content, $attributes);
        } else if (is_string($item->action) || empty($item->action)) {
            $attributes = ['tabindex' => '0']; // add tab support to span but still maintain character stream sequence.
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
     * Accessibility: Down arrow-like character.
     * If the theme does not set characters, appropriate defaults
     * are set automatically.
     *
     * @return string
     */
    public function darrow() {
        return $this->page->theme->darrow;
    }

    /**
     * Returns the custom menu if one has been set
     *
     * A custom menu can be configured by browsing to a theme's settings page
     * and then configuring the custommenu config setting as described.
     *
     * Theme developers: DO NOT OVERRIDE! Please override function
     * {@see core_renderer::render_custom_menu()} instead.
     *
     * @param string $custommenuitems - custom menuitems set by theme instead of global theme settings
     * @return string
     */
    public function custom_menu($custommenuitems = '') {
        global $CFG;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }

        // If filtering of the primary custom menu is enabled, apply only the string filters.
        if (!empty($CFG->navfilter) && !empty($CFG->stringfilters)) {
            // Apply filters that are enabled for Content and Headings.
            $filtermanager = \filter_manager::instance();
            $custommenuitems = $filtermanager->filter_string($custommenuitems, \context_system::instance());
        }

        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }

    /**
     * We want to show the custom menus as a list of links in the footer on small screens.
     * Just return the menu object exported so we can render it differently.
     */
    public function custom_menu_flat() {
        global $CFG;
        $custommenuitems = '';

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }

        // If filtering of the primary custom menu is enabled, apply only the string filters.
        if (!empty($CFG->navfilter) && !empty($CFG->stringfilters)) {
            // Apply filters that are enabled for Content and Headings.
            $filtermanager = \filter_manager::instance();
            $custommenuitems = $filtermanager->filter_string($custommenuitems, \context_system::instance());
        }

        $custommenu = new custom_menu($custommenuitems, current_language());
        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }
            $this->language = $custommenu->add($currentlang, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, ['lang' => $langtype]), $langname);
            }
        }

        return $custommenu->export_for_template($this);
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
        global $CFG;

        $langs = get_string_manager()->get_list_of_translations();
        $haslangmenu = $this->lang_menu() != '';

        if (!$menu->has_children() && !$haslangmenu) {
            return '';
        }

        if ($haslangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();
            if (isset($langs[$currentlang])) {
                $currentlangstr = $langs[$currentlang];
            } else {
                $currentlangstr = $strlang;
            }
            $this->language = $menu->add($currentlangstr, new moodle_url('#'), $strlang, 10000);
            foreach ($langs as $langtype => $langname) {
                $attributes = [];
                // Set the lang attribute for languages different from the page's current language.
                if ($langtype !== $currentlang) {
                    $attributes[] = [
                        'key' => 'lang',
                        'value' => get_html_lang_attribute_value($langtype),
                    ];
                }
                $this->language->add($langname, new moodle_url($this->page->url, ['lang' => $langtype]), null, null, $attributes);
            }
        }

        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }

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
                $url = '#cm_submenu_' . $submenucount;
            }
            $content .= html_writer::link($url, $menunode->get_text(), ['class' => 'yui3-menu-label', 'title' => $menunode->get_title()]);
            $content .= html_writer::start_tag('div', ['id' => 'cm_submenu_' . $submenucount, 'class' => 'yui3-menu custom_menu_submenu']);
            $content .= html_writer::start_tag('div', ['class' => 'yui3-menu-content']);
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
                $content = html_writer::start_tag('li', ['class' => 'yui3-menuitem divider']);
            } else {
                $content = html_writer::start_tag(
                    'li',
                    [
                        'class' => 'yui3-menuitem',
                    ]
                );
                if ($menunode->get_url() !== null) {
                    $url = $menunode->get_url();
                } else {
                    $url = '#';
                }
                $content .= html_writer::link(
                    $url,
                    $menunode->get_text(),
                    ['class' => 'yui3-menuitem-content', 'title' => $menunode->get_title()]
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
        $linkurl = new moodle_url('/theme/switchdevice.php', ['url' => $this->page->url, 'device' => $devicetype, 'sesskey' => sesskey()]);

        $content  = html_writer::start_tag('div', ['id' => 'theme_switch_link']);
        $content .= html_writer::link($linkurl, $linktext, ['rel' => 'nofollow']);
        $content .= html_writer::end_tag('div');

        return $content;
    }

    /**
     * Renders tabs
     *
     * This function replaces print_tabs() used before Moodle 2.5 but with slightly different arguments
     *
     * Theme developers: In order to change how tabs are displayed please override functions
     * {@see core_renderer::render_tabtree()} and/or {@see core_renderer::render_tabobject()}
     *
     * @param array $tabs array of tabs, each of them may have it's own ->subtree
     * @param string|null $selected which tab to mark as selected, all parent tabs will
     *     automatically be marked as activated
     * @param array|string|null $inactive list of ids of inactive tabs, regardless of
     *     their level. Note that you can as weel specify tabobject::$inactive for separate instances
     * @return string
     */
    final public function tabtree($tabs, $selected = null, $inactive = null) {
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
        $data = $tabtree->export_for_template($this);
        return $this->render_from_template('core/tabtree', $data);
    }

    /**
     * Renders tabobject (part of tabtree)
     *
     * This function is called from {@see core_renderer::render_tabtree()}
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
            $str .= html_writer::tag('a', html_writer::span($tabobject->text), ['class' => 'nolink moodle-has-zindex']);
        } else {
            // Tab name with a link.
            if (!($tabobject->link instanceof moodle_url)) {
                // backward compartibility when link was passed as quoted string
                $str .= "<a href=\"$tabobject->link\" title=\"$tabobject->title\"><span>$tabobject->text</span></a>";
            } else {
                $str .= html_writer::link($tabobject->link, html_writer::span($tabobject->text), ['title' => $tabobject->title]);
            }
        }

        if (empty($tabobject->subtree)) {
            if ($tabobject->selected) {
                $str .= html_writer::tag('div', '&nbsp;', ['class' => 'tabrow' . ($tabobject->level + 1) . ' empty']);
            }
            return $str;
        }

        // Print subtree.
        if ($tabobject->level == 0 || $tabobject->selected || $tabobject->activated) {
            $str .= html_writer::start_tag('ul', ['class' => 'tabrow' . $tabobject->level]);
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
                $str .= html_writer::tag('li', $this->render($tab), ['class' => trim($liclass)]);
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
     * @param array $classes Wrapping tag classes.
     * @param string $tag Wrapping tag.
     * @param boolean $fakeblocksonly Include fake blocks only.
     * @return string HTML.
     */
    public function blocks($region, $classes = [], $tag = 'aside', $fakeblocksonly = false) {
        $displayregion = $this->page->apply_theme_region_manipulations($region);
        $headingid = $displayregion . '-block-region-heading';
        $classes = (array)$classes;
        $classes[] = 'block-region';
        $attributes = [
            'id' => 'block-region-' . preg_replace('#[^a-zA-Z0-9_\-]+#', '-', $displayregion),
            'class' => join(' ', $classes),
            'data-blockregion' => $displayregion,
            'data-droptarget' => '1',
            'aria-labelledby' => $headingid,
        ];
        // Generate an appropriate heading to uniquely identify the block region.
        $blocksheading = match ($displayregion) {
            'side-post' => get_string('blocks_supplementary'),
            'content' => get_string('blocks_main'),
            default => get_string('blocks'),
        };
        $content = html_writer::tag('h2', $blocksheading, ['class' => 'visually-hidden', 'id' => $headingid]);
        if ($this->page->blocks->region_has_content($displayregion, $this)) {
            $content .= $this->blocks_for_region($displayregion, $fakeblocksonly);
        }
        // Given that <aside> has a default role of a complementary landmark and is supposed to be a top-level landmark,
        // blocks rendered as part of the main content should not have a complementary role and should be rendered in a more generic
        // container.
        if ($displayregion === 'content' && $tag === 'aside') {
            $tag = 'section';
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
    public function body_css_classes(array $additionalclasses = []) {
        return $this->page->bodyclasses . ' ' . implode(' ', $additionalclasses);
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
    public function body_attributes($additionalclasses = []) {
        if (!is_array($additionalclasses)) {
            $additionalclasses = explode(' ', $additionalclasses);
        }
        return ' id="' . $this->body_id() . '" class="' . $this->body_css_classes($additionalclasses) . '"';
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
     * Returns the HTML for the site support email link
     *
     * @param array $customattribs Array of custom attributes for the support email anchor tag.
     * @param bool $embed Set to true if you want to embed the link in other inline content.
     * @return string The html code for the support email link.
     */
    public function supportemail(array $customattribs = [], bool $embed = false): string {
        global $CFG;

        // Do not provide a link to contact site support if it is unavailable to this user. This would be where the site has
        // disabled support, or limited it to authenticated users and the current user is a guest or not logged in.
        if (
            !isset($CFG->supportavailability) ||
                $CFG->supportavailability == CONTACT_SUPPORT_DISABLED ||
                ($CFG->supportavailability == CONTACT_SUPPORT_AUTHENTICATED && (!isloggedin() || isguestuser()))
        ) {
            return '';
        }

        $label = get_string('contactsitesupport', 'admin');
        $icon = $this->pix_icon('t/email', '');

        if (!$embed) {
            $content = $icon . $label;
        } else {
            $content = $label;
        }

        if (!empty($CFG->supportpage)) {
            $attributes = ['href' => $CFG->supportpage, 'target' => 'blank'];
            $content .= $this->pix_icon('i/externallink', '', 'moodle', ['class' => 'ms-1']);
        } else {
            $attributes = ['href' => $CFG->wwwroot . '/user/contactsitesupport.php'];
        }

        $attributes += $customattribs;

        return html_writer::tag('a', $content, $attributes);
    }

    /**
     * Returns the services and support link for the help pop-up.
     *
     * @return string
     */
    public function services_support_link(): string {
        global $CFG;

        if (
            during_initial_install() ||
            (isset($CFG->showservicesandsupportcontent) && $CFG->showservicesandsupportcontent == false) ||
            !is_siteadmin()
        ) {
            return '';
        }

        $liferingicon = $this->pix_icon('t/life-ring', '', 'moodle', ['class' => 'fa fa-life-ring']);
        $newwindowicon = $this->pix_icon('i/externallink', get_string('opensinnewwindow'), 'moodle', ['class' => 'ms-1']);
        $link = !empty($CFG->servicespage)
            ? $CFG->servicespage
            : 'https://moodle.com/help/?utm_source=CTA-banner&utm_medium=platform&utm_campaign=name~Moodle4+cat~lms+mp~no';
        $content = $liferingicon . get_string('moodleservicesandsupport') . $newwindowicon;

        return html_writer::tag('a', $content, ['target' => '_blank', 'href' => $link]);
    }

    /**
     * Helper function to decide whether to show the help popover header or not.
     *
     * @return bool
     */
    public function has_popover_links(): bool {
        return !empty($this->services_support_link()) || !empty($this->page_doc_link()) || !empty($this->supportemail());
    }

    /**
     * Helper function to decide whether to show the communication link or not.
     *
     * @return bool
     */
    public function has_communication_links(): bool {
        if (during_initial_install() || !\core_communication\api::is_available()) {
            return false;
        }
        return !empty($this->communication_link());
    }

    /**
     * Returns the communication link, complete with html.
     *
     * @return string
     */
    public function communication_link(): string {
        $link = $this->communication_url() ?? '';
        $commicon = $this->pix_icon('t/messages-o', '', 'moodle', ['class' => 'fa fa-comments']);
        $newwindowicon = $this->pix_icon('i/externallink', get_string('opensinnewwindow'), 'moodle', ['class' => 'ms-1']);
        $content = $commicon . get_string('communicationroomlink', 'course') . $newwindowicon;
        $html = html_writer::tag('a', $content, ['target' => '_blank', 'href' => $link]);

        return !empty($link) ? $html : '';
    }

    /**
     * Returns the communication url for a given instance if it exists.
     *
     * @return string
     */
    public function communication_url(): string {
        global $COURSE;
        return \core_communication\helper::get_course_communication_url($COURSE);
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
     * Returns the moodle_url for the favicon.
     *
     * @since Moodle 2.5.1 2.6
     * @return moodle_url The moodle_url for the favicon
     */
    public function favicon() {
        $logo = null;
        if (!during_initial_install()) {
            $logo = get_config('core_admin', 'favicon');
        }
        if (empty($logo)) {
            return $this->image_url('favicon', 'theme');
        }

        // Use $CFG->themerev to prevent browser caching when the file changes.
        return moodle_url::make_pluginfile_url(
            context_system::instance()->id,
            'core_admin',
            'favicon',
            '64x64/',
            theme_get_revision(),
            $logo
        );
    }

    /**
     * Renders preferences groups.
     *
     * @param  preferences_groups $renderable The renderable
     * @return string The output.
     */
    public function render_preferences_groups(preferences_groups $renderable) {
        return $this->render_from_template('core/preferences_groups', $renderable);
    }

    /**
     * Renders preferences group.
     *
     * @param  preferences_group $renderable The renderable
     * @return string The output.
     */
    public function render_preferences_group(preferences_group $renderable) {
        $html = '';
        $html .= html_writer::start_tag('div', ['class' => 'col-sm-4 preferences-group']);
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

    public function context_header($headerinfo = null, $headinglevel = 1) {
        global $DB, $USER, $CFG, $SITE;
        require_once($CFG->dirroot . '/user/lib.php');
        $context = $this->page->context;
        $heading = null;
        $imagedata = null;
        $subheader = null;
        $userbuttons = null;

        // Make sure to use the heading if it has been set.
        if (isset($headerinfo['heading'])) {
            $heading = $headerinfo['heading'];
        } else {
            $heading = $this->page->heading;
        }

        // The user context currently has images and buttons. Other contexts may follow.
        if ((isset($headerinfo['user']) || $context->contextlevel == CONTEXT_USER) && $this->page->pagetype !== 'my-index') {
            if (isset($headerinfo['user'])) {
                $user = $headerinfo['user'];
            } else {
                // Look up the user information if it is not supplied.
                $user = $DB->get_record('user', ['id' => $context->instanceid]);
            }

            // If the user context is set, then use that for capability checks.
            if (isset($headerinfo['usercontext'])) {
                $context = $headerinfo['usercontext'];
            }

            // Only provide user information if the user is the current user, or a user which the current user can view.
            // When checking user_can_view_profile(), either:
            // If the page context is course, check the course context (from the page object) or;
            // If page context is NOT course, then check across all courses.
            $course = ($this->page->context->contextlevel == CONTEXT_COURSE) ? $this->page->course : null;

            if (user_can_view_profile($user, $course)) {
                // Use the user's full name if the heading isn't set.
                if (empty($heading)) {
                    $heading = fullname($user);
                }

                $imagedata = $this->user_picture($user, ['size' => 100]);

                // Check to see if we should be displaying a message button.
                if (!empty($CFG->messaging) && has_capability('moodle/site:sendmessage', $context)) {
                    $userbuttons = [
                        'messages' => [
                            'buttontype' => 'message',
                            'title' => get_string('message', 'message'),
                            'url' => new moodle_url('/message/index.php', ['id' => $user->id]),
                            'image' => 't/message',
                            'linkattributes' => \core_message\helper::messageuser_link_params($user->id),
                            'page' => $this->page,
                        ],
                    ];

                    if ($USER->id != $user->id) {
                        $cancreatecontact = \core_message\api::can_create_contact($USER->id, $user->id);
                        $iscontact = \core_message\api::is_contact($USER->id, $user->id);
                        $isrequested = \core_message\api::get_contact_requests_between_users($USER->id, $user->id);
                        $contacturlaction = '';
                        $linkattributes = \core_message\helper::togglecontact_link_params(
                            $user,
                            $iscontact,
                            true,
                            !empty($isrequested),
                        );
                        // If the user is not a contact.
                        if (!$iscontact) {
                            if ($isrequested) {
                                // Set it to true if a request has been sent.
                                $cancreatecontact = true;

                                // We just need the first request.
                                $requests = array_shift($isrequested);
                                if ($requests->userid == $USER->id) {
                                    // If the user has requested to be a contact.
                                    $contacttitle = 'contactrequestsent';
                                } else {
                                    // If the user has been requested to be a contact.
                                    $contacttitle = 'waitingforcontactaccept';
                                }
                                $linkattributes = array_merge($linkattributes, [
                                    'class' => 'disabled border-0',
                                    'tabindex' => '-1',
                                ]);
                            } else {
                                // If the user is not a contact and has not requested to be a contact.
                                $contacttitle = 'addtoyourcontacts';
                                $contacturlaction = 'addcontact';
                            }
                            $contactimage = 't/addcontact';
                        } else {
                            // If the user is a contact.
                            $contacttitle = 'removefromyourcontacts';
                            $contacturlaction = 'removecontact';
                            $contactimage = 't/removecontact';
                        }
                        if ($cancreatecontact) {
                            $userbuttons['togglecontact'] = [
                                'buttontype' => 'togglecontact',
                                'title' => get_string($contacttitle, 'message'),
                                'url' => new moodle_url('/message/index.php', [
                                    'user1' => $USER->id,
                                    'user2' => $user->id,
                                    $contacturlaction => $user->id,
                                    'sesskey' => sesskey(),
                                ]),
                                'image' => $contactimage,
                                'linkattributes' => $linkattributes,
                                'page' => $this->page,
                            ];
                        }
                    }
                }
            } else {
                $heading = null;
            }
        }

        // Return the heading wrapped in an visually-hidden element so it is only visible to screen-readers
        // for nocontextheader layouts.
        if (!empty($this->page->layout_options['nocontextheader'])) {
            return html_writer::div($heading, 'visually-hidden');
        }

        $contextheader = new context_header($heading, $headinglevel, $imagedata, $userbuttons);
        return $this->render($contextheader);
    }

    /**
     * Renders the header bar.
     *
     * @param context_header $contextheader Header bar object.
     * @return string HTML for the header bar.
     * @deprecated since 4.5 Please use core_renderer::render($contextheader) instead
     * @todo MDL-82163 This will be deleted in Moodle 6.0.
     */
    #[\core\attribute\deprecated('core_renderer::render($contextheader)', since: '4.5', mdl: 'MDL-82160')]
    protected function render_context_header(context_header $contextheader) {
        $context = $contextheader->export_for_template($this);
        return $this->render_from_template('core/context_header', $context);
    }

    /**
     * Renders the skip links for the page.
     *
     * @param array $links List of skip links.
     * @return string HTML for the skip links.
     */
    public function render_skip_links($links) {
        $context = [ 'links' => []];

        foreach ($links as $url => $text) {
            $context['links'][] = [ 'url' => $url, 'text' => $text];
        }

        return $this->render_from_template('core/skip_links', $context);
    }

    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function full_header() {
        $pagetype = $this->page->pagetype;
        $homepage = get_home_page();
        $homepagetype = null;
        // Add a special case since /my/courses is a part of the /my subsystem.
        if ($homepage == HOMEPAGE_MY || $homepage == HOMEPAGE_MYCOURSES) {
            $homepagetype = 'my-index';
        } else if ($homepage == HOMEPAGE_SITE) {
            $homepagetype = 'site-index';
        }
        if (
            $this->page->include_region_main_settings_in_header_actions() &&
                !$this->page->blocks->is_block_present('settings')
        ) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $this->page->add_header_action(html_writer::div(
                $this->region_main_settings_menu(),
                'd-print-none',
                ['id' => 'region-main-settings-menu']
            ));
        }

        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();
        if (!empty($pagetype) && !empty($homepagetype) && $pagetype == $homepagetype) {
            $header->welcomemessage = \core_user::welcome_message();
        }
        return $this->render_from_template('core/full_header', $header);
    }

    /**
     * This is an optional menu that can be added to a layout by a theme. It contains the
     * menu for the course administration, only on the course main page.
     *
     * @return string
     */
    public function context_header_settings_menu() {
        $context = $this->page->context;
        $menu = new action_menu();

        $items = $this->page->navbar->get_items();
        $currentnode = end($items);

        $showcoursemenu = false;
        $showfrontpagemenu = false;
        $showusermenu = false;

        // We are on the course home page.
        if (
            ($context->contextlevel == CONTEXT_COURSE) &&
                !empty($currentnode) &&
                ($currentnode->type == navigation_node::TYPE_COURSE || $currentnode->type == navigation_node::TYPE_SECTION)
        ) {
            $showcoursemenu = true;
        }

        $courseformat = course_get_format($this->page->course);
        // This is a single activity course format, always show the course menu on the activity main page.
        if (
            $context->contextlevel == CONTEXT_MODULE &&
                !$courseformat->has_view_page()
        ) {
            $this->page->navigation->initialise();
            $activenode = $this->page->navigation->find_active_node();
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $showcoursemenu = true;
            } else if (
                !empty($activenode) && ($activenode->type == navigation_node::TYPE_ACTIVITY ||
                            $activenode->type == navigation_node::TYPE_RESOURCE)
            ) {
                // We only want to show the menu on the first page of the activity. This means
                // the breadcrumb has no additional nodes.
                if ($currentnode && ($currentnode->key == $activenode->key && $currentnode->type == $activenode->type)) {
                    $showcoursemenu = true;
                }
            }
        }

        // This is the site front page.
        if (
            $context->contextlevel == CONTEXT_COURSE &&
                !empty($currentnode) &&
                $currentnode->key === 'home'
        ) {
            $showfrontpagemenu = true;
        }

        // This is the user profile page.
        if (
            $context->contextlevel == CONTEXT_USER &&
                !empty($currentnode) &&
                ($currentnode->key === 'myprofile')
        ) {
            $showusermenu = true;
        }

        if ($showfrontpagemenu) {
            $settingsnode = $this->page->settingsnav->find('frontpage', navigation_node::TYPE_SETTING);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                // We only add a list to the full settings menu if we didn't include every node in the short menu.
                if ($skipped) {
                    $text = get_string('morenavigationlinks');
                    $url = \core\router\util::get_path_for_callable(
                        [\core_course\route\controller\course_management::class, 'administer_course'],
                        ['course' => $this->page->course->id],
                    );
                    $link = new action_link($url, $text, null, null, new pix_icon('t/edit', $text));
                    $menu->add_secondary_action($link);
                }
            }
        } else if ($showcoursemenu) {
            $settingsnode = $this->page->settingsnav->find('courseadmin', navigation_node::TYPE_COURSE);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $skipped = $this->build_action_menu_from_navigation($menu, $settingsnode, false, true);

                // We only add a list to the full settings menu if we didn't include every node in the short menu.
                if ($skipped) {
                    $text = get_string('morenavigationlinks');
                    $url = \core\router\util::get_path_for_callable(
                        [\core_course\route\controller\course_management::class, 'administer_course'],
                        ['course' => $this->page->course->id],
                    );
                    $link = new action_link($url, $text, null, null, new pix_icon('t/edit', $text));
                    $menu->add_secondary_action($link);
                }
            }
        } else if ($showusermenu) {
            // Get the course admin node from the settings navigation.
            $settingsnode = $this->page->settingsnav->find('useraccount', navigation_node::TYPE_CONTAINER);
            if ($settingsnode) {
                // Build an action menu based on the visible nodes from this navigation tree.
                $this->build_action_menu_from_navigation($menu, $settingsnode);
            }
        }

        return $this->render($menu);
    }

    /**
     * Take a node in the nav tree and make an action menu out of it.
     * The links are injected in the action menu.
     *
     * @param action_menu $menu
     * @param navigation_node $node
     * @param boolean $indent
     * @param boolean $onlytopleafnodes
     * @return boolean nodesskipped - True if nodes were skipped in building the menu
     */
    protected function build_action_menu_from_navigation(
        action_menu $menu,
        navigation_node $node,
        $indent = false,
        $onlytopleafnodes = false
    ) {
        $skipped = false;
        // Build an action menu based on the visible nodes from this navigation tree.
        foreach ($node->children as $menuitem) {
            if ($menuitem->display) {
                if ($onlytopleafnodes && $menuitem->children->count()) {
                    $skipped = true;
                    continue;
                }
                if ($menuitem->action) {
                    if ($menuitem->action instanceof action_link) {
                        $link = $menuitem->action;
                        // Give preference to setting icon over action icon.
                        if (!empty($menuitem->icon)) {
                            $link->icon = $menuitem->icon;
                        }
                    } else {
                        $link = new action_link($menuitem->action, $menuitem->text, null, null, $menuitem->icon);
                    }
                } else {
                    if ($onlytopleafnodes) {
                        $skipped = true;
                        continue;
                    }
                    $link = new action_link(new moodle_url('#'), $menuitem->text, null, ['disabled' => true], $menuitem->icon);
                }
                if ($indent) {
                    $link->add_class('ms-4');
                }
                if (!empty($menuitem->classes)) {
                    $link->add_class(implode(" ", $menuitem->classes));
                }

                $menu->add_secondary_action($link);
                $skipped = $skipped || $this->build_action_menu_from_navigation($menu, $menuitem, true);
            }
        }
        return $skipped;
    }

    /**
     * This is an optional menu that can be added to a layout by a theme. It contains the
     * menu for the most specific thing from the settings block. E.g. Module administration.
     *
     * @return string
     */
    public function region_main_settings_menu() {
        $context = $this->page->context;
        $menu = new action_menu();

        if ($context->contextlevel == CONTEXT_MODULE) {
            $this->page->navigation->initialise();
            $node = $this->page->navigation->find_active_node();
            $buildmenu = false;
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $buildmenu = true;
            } else if (
                !empty($node) && ($node->type == navigation_node::TYPE_ACTIVITY ||
                            $node->type == navigation_node::TYPE_RESOURCE)
            ) {
                $items = $this->page->navbar->get_items();
                $navbarnode = end($items);
                // We only want to show the menu on the first page of the activity. This means
                // the breadcrumb has no additional nodes.
                if ($navbarnode && ($navbarnode->key === $node->key && $navbarnode->type == $node->type)) {
                    $buildmenu = true;
                }
            }
            if ($buildmenu) {
                // Get the course admin node from the settings navigation.
                $node = $this->page->settingsnav->find('modulesettings', navigation_node::TYPE_SETTING);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }
        } else if ($context->contextlevel == CONTEXT_COURSECAT) {
            // For course category context, show category settings menu, if we're on the course category page.
            if ($this->page->pagetype === 'course-index-category') {
                $node = $this->page->settingsnav->find('categorysettings', navigation_node::TYPE_CONTAINER);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }
        } else {
            $items = $this->page->navbar->get_items();
            $navbarnode = end($items);

            if ($navbarnode && ($navbarnode->key === 'participants')) {
                $node = $this->page->settingsnav->find('users', navigation_node::TYPE_CONTAINER);
                if ($node) {
                    // Build an action menu based on the visible nodes from this navigation tree.
                    $this->build_action_menu_from_navigation($menu, $node);
                }
            }
        }
        return $this->render($menu);
    }

    /**
     * Displays the list of tags associated with an entry
     *
     * @param array $tags list of instances of core_tag or stdClass
     * @param string $label label to display in front, by default 'Tags' (get_string('tags')), set to null
     *               to use default, set to '' (empty string) to omit the label completely
     * @param string $classes additional classes for the enclosing div element
     * @param int $limit limit the number of tags to display, if size of $tags is more than this limit the "more" link
     *               will be appended to the end, JS will toggle the rest of the tags
     * @param context $pagecontext specify if needed to overwrite the current page context for the view tag link
     * @param bool $accesshidelabel if true, the label should have class="accesshide" added.
     * @param bool $displaylink Indicates whether the tag should be displayed as a link.
     * @return string
     */
    public function tag_list(
        $tags,
        $label = null,
        $classes = '',
        $limit = 10,
        $pagecontext = null,
        $accesshidelabel = false,
        $displaylink = true,
    ) {
        $list = new taglist($tags, $label, $classes, $limit, $pagecontext, $accesshidelabel, $displaylink);
        return $this->render_from_template('core_tag/taglist', $list->export_for_template($this));
    }

    /**
     * Renders element for inline editing of any value
     *
     * @param inplace_editable $element
     * @return string
     */
    public function render_inplace_editable(inplace_editable $element) {
        return $this->render_from_template('core/inplace_editable', $element->export_for_template($this));
    }

    /**
     * Renders a bar chart.
     *
     * @param \core\chart_bar $chart The chart.
     * @return string
     */
    public function render_chart_bar(\core\chart_bar $chart) {
        return $this->render_chart($chart);
    }

    /**
     * Renders a line chart.
     *
     * @param \core\chart_line $chart The chart.
     * @return string
     */
    public function render_chart_line(\core\chart_line $chart) {
        return $this->render_chart($chart);
    }

    /**
     * Renders a pie chart.
     *
     * @param \core\chart_pie $chart The chart.
     * @return string
     */
    public function render_chart_pie(\core\chart_pie $chart) {
        return $this->render_chart($chart);
    }

    /**
     * Renders a chart.
     *
     * @param \core\chart_base $chart The chart.
     * @param bool $withtable Whether to include a data table with the chart.
     * @return string
     */
    public function render_chart(\core\chart_base $chart, $withtable = true) {
        $chartdata = json_encode($chart);
        return $this->render_from_template('core/chart', (object) [
            'chartdata' => $chartdata,
            'withtable' => $withtable,
        ]);
    }

    /**
     * Renders the login form.
     *
     * @param \core_auth\output\login $form The renderable.
     * @return string
     */
    public function render_login(\core_auth\output\login $form) {
        global $CFG, $SITE;

        $context = $form->export_for_template($this);

        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context->logourl = $url;
        $context->sitename = format_string(
            $SITE->fullname,
            true,
            ['context' => context_course::instance(SITEID), "escape" => false]
        );

        return $this->render_from_template('core/loginform', $context);
    }

    /**
     * Renders an mform element from a template.
     *
     * @param HTML_QuickForm_element $element element
     * @param bool $required if input is required field
     * @param bool $advanced if input is an advanced field
     * @param string $error error message to display
     * @param bool $ingroup True if this element is rendered as part of a group
     * @return mixed string|bool
     */
    public function mform_element($element, $required, $advanced, $error, $ingroup) {
        $templatename = 'core_form/element-' . $element->getType();
        if ($ingroup) {
            $templatename .= "-inline";
        }
        try {
            // We call this to generate a file not found exception if there is no template.
            // We don't want to call export_for_template if there is no template.
            mustache_template_finder::get_template_filepath($templatename);

            if ($element instanceof templatable) {
                $elementcontext = $element->export_for_template($this);

                $helpbutton = '';
                if (method_exists($element, 'getHelpButton')) {
                    $helpbutton = $element->getHelpButton();
                }
                $label = $element->getLabel();
                $text = '';
                if (method_exists($element, 'getText')) {
                    // There currently exists code that adds a form element with an empty label.
                    // If this is the case then set the label to the description.
                    if (empty($label)) {
                        $label = $element->getText();
                    } else {
                        $text = $element->getText();
                    }
                }

                // Generate the form element wrapper ids and names to pass to the template.
                // This differs between group and non-group elements.
                if ($element->getType() === 'group') {
                    // Group element.
                    // The id will be something like 'fgroup_id_NAME'. E.g. fgroup_id_mygroup.
                    $elementcontext['wrapperid'] = $elementcontext['id'];

                    // Ensure group elements pass through the group name as the element name.
                    $elementcontext['name'] = $elementcontext['groupname'];
                } else {
                    // Non grouped element.
                    // Creates an id like 'fitem_id_NAME'. E.g. fitem_id_mytextelement.
                    $elementcontext['wrapperid'] = 'fitem_' . $elementcontext['id'];
                }

                $context = [
                    'element' => $elementcontext,
                    'label' => $label,
                    'text' => $text,
                    'required' => $required,
                    'advanced' => $advanced,
                    'helpbutton' => $helpbutton,
                    'error' => $error,
                ];
                return $this->render_from_template($templatename, $context);
            }
        } catch (\Exception $e) {
            // No template for this element.
            return false;
        }
    }

    /**
     * Render the login signup form into a nice template for the theme.
     *
     * @param moodleform $form
     * @return string
     */
    public function render_login_signup_form($form) {
        global $SITE;

        $context = $form->export_for_template($this);
        $url = $this->get_logo_url();
        if ($url) {
            $url = $url->out(false);
        }
        $context['logourl'] = $url;
        $context['sitename'] = format_string(
            $SITE->fullname,
            true,
            ['context' => context_course::instance(SITEID), "escape" => false]
        );

        return $this->render_from_template('core/signup_form_layout', $context);
    }

    /**
     * Render the verify age and location page into a nice template for the theme.
     *
     * @param \core_auth\output\verify_age_location_page $page The renderable
     * @return string
     */
    protected function render_verify_age_location_page($page) {
        $context = $page->export_for_template($this);

        return $this->render_from_template('core/auth_verify_age_location_page', $context);
    }

    /**
     * Render the digital minor contact information page into a nice template for the theme.
     *
     * @param \core_auth\output\digital_minor_page $page The renderable
     * @return string
     */
    protected function render_digital_minor_page($page) {
        $context = $page->export_for_template($this);

        return $this->render_from_template('core/auth_digital_minor_page', $context);
    }

    /**
     * Renders a progress bar.
     *
     * Do not use $OUTPUT->render($bar), instead use progress_bar::create().
     *
     * @param  progress_bar $bar The bar.
     * @return string HTML fragment
     */
    public function render_progress_bar(progress_bar $bar) {
        $data = $bar->export_for_template($this);
        return $this->render_from_template('core/progress_bar', $data);
    }

    /**
     * Renders an update to a progress bar.
     *
     * Note: This does not cleanly map to a renderable class and should
     * never be used directly.
     *
     * @param  string $id
     * @param  float $percent
     * @param  string $msg Message
     * @param  string $estimate time remaining message
     * @param  bool $error Was there an error?
     * @return string ascii fragment
     */
    public function render_progress_bar_update(string $id, float $percent, string $msg, string $estimate,
        bool $error = false): string {
        return html_writer::script(js_writer::function_call('updateProgressBar', [
            $id,
            round($percent, 1),
            $msg,
            $estimate,
            $error,
        ]));
    }

    /**
     * Renders element for a toggle-all checkbox.
     *
     * @param checkbox_toggleall $element
     * @return string
     */
    public function render_checkbox_toggleall(checkbox_toggleall $element) {
        return $this->render_from_template($element->get_template(), $element->export_for_template($this));
    }

    /**
     * Renders the tertiary nav for the participants page
     *
     * @param object $course The course we are operating within
     * @param string|null $renderedbuttons Any additional buttons/content to be displayed in line with the nav
     * @return string
     */
    public function render_participants_tertiary_nav(object $course, ?string $renderedbuttons = null) {
        $actionbar = new participants_action_bar($course, $this->page, $renderedbuttons);
        $content = $this->render_from_template('core_course/participants_actionbar', $actionbar->export_for_template($this));
        return $content ?: "";
    }

    /**
     * Renders release information in the footer popup
     * @return ?string Moodle release info.
     */
    public function moodle_release() {
        global $CFG;
        if (!during_initial_install() && is_siteadmin()) {
            return $CFG->release;
        }
    }

    /**
     * Generate the add block button when editing mode is turned on and the user can edit blocks.
     *
     * @param string $region where new blocks should be added.
     * @return string html for the add block button.
     */
    public function addblockbutton($region = ''): string {
        $addblockbutton = '';
        $regions = $this->page->blocks->get_regions();
        if (count($regions) == 0) {
            return '';
        }
        if (
            isset($this->page->theme->addblockposition) &&
                $this->page->user_is_editing() &&
                $this->page->user_can_edit_blocks() &&
                $this->page->pagelayout !== 'mycourses'
        ) {
            $params = ['bui_addblock' => '', 'sesskey' => sesskey()];
            if (!empty($region)) {
                $params['bui_blockregion'] = $region;
            }
            $url = new moodle_url($this->page->url, $params);
            $addblockbutton = $this->render_from_template(
                'core/add_block_button',
                [
                    'link' => $url->out(false),
                    'escapedlink' => "?{$url->get_query_string(false)}",
                    'pagehash' => $this->page->get_edited_page_hash(),
                    'blockregion' => $region,
                    // The following parameters are not used since Moodle 4.2 but are
                    // still passed for backward-compatibility.
                    'pageType' => $this->page->pagetype,
                    'pageLayout' => $this->page->pagelayout,
                    'subPage' => $this->page->subpage,
                ]
            );
        }
        return $addblockbutton;
    }

    /**
     * Prepares an element for streaming output
     *
     * This must be used with NO_OUTPUT_BUFFERING set to true. After using this method
     * any subsequent prints or echos to STDOUT result in the outputted content magically
     * being appended inside that element rather than where the current html would be
     * normally. This enables pages which take some time to render incremental content to
     * first output a fully formed html page, including the footer, and to then stream
     * into an element such as the main content div. This fixes a class of page layout
     * bugs and reduces layout shift issues and was inspired by Facebook BigPipe.
     *
     * Some use cases such as a simple page which loads content via ajax could be swapped
     * to this method wich saves another http request and its network latency resulting
     * in both lower server load and better front end performance.
     *
     * You should consider giving the element you stream into a minimum height to further
     * reduce layout shift as the content initally streams into the element.
     *
     * You can safely finish the output without closing the streamed element. You can also
     * call this method again to swap the target of the streaming to a new element as
     * often as you want.

     * https://www.youtube.com/watch?v=LLRig4s1_yA&t=1022s
     * Watch this video segment to explain how and why this 'One Weird Trick' works.
     *
     * @param string $selector where new content should be appended
     * @param string $element which contains the streamed content
     * @return string html to be written
     */
    public function select_element_for_append(string $selector = '#region-main [role=main]', string $element = 'div') {

        if (!CLI_SCRIPT && !NO_OUTPUT_BUFFERING) {
            throw new coding_exception(
                'select_element_for_append used in a non-CLI script without setting NO_OUTPUT_BUFFERING.',
                DEBUG_DEVELOPER
            );
        }

        // We are already streaming into this element so don't change anything.
        if ($this->currentselector === $selector && $this->currentelement === $element) {
            return;
        }

        // If we have a streaming element close it before starting a new one.
        $html = $this->close_element_for_append();

        $this->currentselector = $selector;
        $this->currentelement = $element;

        // Create an unclosed element for the streamed content to append into.
        $id = uniqid();
        $html .= html_writer::start_tag($element, ['id' => $id]);
        $html .= html_writer::tag('script', "document.querySelector('$selector').append(document.getElementById('$id'))");
        $html .= "\n";
        return $html;
    }

    /**
     * This closes any opened stream elements
     *
     * @return string html to be written
     */
    public function close_element_for_append() {
        $html = '';
        if ($this->currentselector !== '') {
            $html .= html_writer::end_tag($this->currentelement);
            $html .= "\n";
            $this->currentelement = '';
        }
        return $html;
    }

    /**
     * A companion method to select_element_for_append
     *
     * This must be used with NO_OUTPUT_BUFFERING set to true.
     *
     * This is similar but instead of appending into the element it replaces
     * the content in the element. Depending on the 3rd argument it can replace
     * the innerHTML or the outerHTML which can be useful to completely remove
     * the element if needed.
     *
     * @param string $selector where new content should be replaced
     * @param string $html A chunk of well formed html
     * @param bool $outer Wether it replaces the innerHTML or the outerHTML
     * @return string html to be written
     */
    public function select_element_for_replace(string $selector, string $html, bool $outer = false) {

        if (!CLI_SCRIPT && !NO_OUTPUT_BUFFERING) {
            throw new coding_exception(
                'select_element_for_replace used in a non-CLI script without setting NO_OUTPUT_BUFFERING.',
                DEBUG_DEVELOPER
            );
        }

        // Escape html for use inside a javascript string.
        $html = addslashes_js($html);
        $property = $outer ? 'outerHTML' : 'innerHTML';
        $output = html_writer::tag('script', "document.querySelector('$selector').$property = '$html';");
        $output .= "\n";
        return $output;
    }
}
// Alias this class to the old name.
// This file will be autoloaded by the legacyclasses autoload system.
// In future all uses of this class will be corrected and the legacy references will be removed.
class_alias(core_renderer::class, \core_renderer::class);

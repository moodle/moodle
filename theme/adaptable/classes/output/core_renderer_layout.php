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
 * A trait for the core renderer.
 *
 * @package    theme_adaptable
 * @copyright  2024 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable\output;

use core\url;
use stdClass;

/**
 * Trait for core and core maintenance renderers.
 */
trait core_renderer_layout {

    /**
     * Yes header.
     */
    public function yesheader($sidepostdrawer) {
        global $USER;
        $themesettings = \theme_adaptable\toolbox::get_settings();

        $bodyclasses = [];
        $bodyclasses[] = 'theme_adaptable';
        $bodyclasses[] = 'two-column';

        $pageclasses = [];

        // Screen size.
        theme_adaptable_initialise_zoom();
        $bodyclasses[] = theme_adaptable_get_zoom();

        theme_adaptable_initialise_full();
        $bodyclasses[] = theme_adaptable_get_full();

        $optionsdata = ['data' => []];

        // Main navbar.
        if (isset($themesettings->stickynavbar) && $themesettings->stickynavbar == 1) {
            $optionsdata['data']['stickynavbar'] = true;
        } else {
            $optionsdata['data']['stickynavbar'] = false;
        }

        // JS calls.
        $this->page->requires->js_call_amd('theme_adaptable/adaptable', 'init', $optionsdata);
        if (!empty($themesettings->pageloadingprogress)) {
            $this->page->requires->js_call_amd('theme_adaptable/pace_init', 'init', [$themesettings->pageloadingprogresstheme]);
        }

        // Layout.
        $left = (!right_to_left());

        // Navbar Menu.
        $shownavbar = false;
        if ((isloggedin() && !isguestuser()) ||
            (!empty($themesettings->enablenavbarwhenloggedout))) {
            // Show navbar unless disabled by config.
            if (empty($this->page->layout_options['nonavbar'])) {
                $shownavbar = true;
            }
        }
        // Load header background image if it exists.
        $headerbg = '';

        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();
        if (is_object($localtoolbox)) {
            ['currenttopcat' => $currenttopcat, 'headerbg' => $headerbg] =
                $localtoolbox->get_category_header($themesettings, $this->page);
        } else {
            $currenttopcat = false;
        }

        if ((empty($headerbg)) && (!empty($themesettings->headerbgimage))) {
            $headerbg = ' class="headerbgimage" style="background-image: ' .
                'url(\'' . $this->page->theme->setting_file_url('headerbgimage', 'headerbgimage') . '\');"';
        }
        if (!empty($headerbg)) {
            $bodyclasses[] = 'has-header-bg';
        }

        /* Choose the header style.  There styles available are:
           "style1"  (original header)
           "style2"  (2 row header).
        */

        if (!empty($themesettings->headerstyle)) {
            $adaptableheaderstyle = $themesettings->headerstyle;
        } else {
            $adaptableheaderstyle = "style1";
        }
        $bodyclasses[] = 'header-' . $adaptableheaderstyle;

        // Block icons class.
        if ($themesettings->blockicons == 1) {
            $bodyclasses[] = 'showblockicons';
        }

        if (!empty($themesettings->standardscreenwidth)) {
            $bodyclasses[] = $themesettings->standardscreenwidth;
        } else {
            $bodyclasses[] = 'standard';
        }

        $left = $themesettings->blockside;

        $courseindexheader = false;
        $courseindexopen = false;
        switch ($this->page->pagelayout) {
            case 'base':
            case 'standard':
            case 'course':
            case 'coursecategory':
            case 'incourse':
            case 'frontpage':
            case 'admin':
            case 'mycourses':
            case 'mydashboard':
            case 'mypublic':
            case 'report':
                [$courseindexopen, $courseindex, $courseindexmarkup, $courseindextogglemarkup] = $this->courseindexheader();
                $courseindexheader = true;
                break;
            default:
                $courseindex = false;
        }

        if ($sidepostdrawer) {
            [$hassidepost, $sidepostmarkup, $sideposttogglemarkup] = $this->sidepostheader();
        } else {
            $hassidepost = false;
        }

        if ($courseindexheader) {
            if ($courseindexopen) {
                $bodyclasses[] = 'drawer-open-index';
            }
        }

        if (($courseindex) || ($hassidepost)) {
            $bodyclasses[] = 'uses-drawers';
            $pageclasses[] = 'drawers';
        }

        if (!empty($themesettings->responsivesectionnav)) {
            $bodyclasses[] = 'responsivesectionnav';
        }

        $this->head($bodyclasses);

        if (!empty($courseindexmarkup)) {
            echo $courseindexmarkup;
        }
        if (!empty($sidepostmarkup)) {
            echo $sidepostmarkup;
        }
        if (!$optionsdata['data']['stickynavbar']) {
            echo '<div id="page" class="' . implode(' ', $pageclasses) . '">';
        }

        $headercontext = [
            'output' => $this,
        ];

        if (!empty($themesettings->mobileprimarynav)) {
            $primary = new navigation\primary($this->page);
            $renderer = $this->page->get_renderer('core');
            $primarymenu = $primary->export_for_template($renderer);
            $headercontext['mobileprimarynav'] = $primarymenu['mobileprimarynav'];
            $headercontext['mobileprimarynavicon'] = \theme_adaptable\toolbox::getfontawesomemarkup('bars');
            $headercontext['hasmobileprimarynav'] = true;
        }

        if ((!isloggedin() || isguestuser()) && ($this->page->pagetype != "login-index")) {
            if ($themesettings->displaylogin != 'no') {
                $loginformcontext = [
                    'displayloginbox' => ($themesettings->displaylogin == 'box') ? true : false,
                    'output' => $this,
                    'token' => s(\core\session\manager::get_login_token()),
                    'url' => new url('/login/index.php'),
                ];
                if (!$loginformcontext['displayloginbox']) {
                    $authsequence = get_enabled_auth_plugins(); // Get all auths.
                    if (in_array('oidc', $authsequence)) {
                        $authplugin = get_auth_plugin('oidc');
                        $oidc = $authplugin->loginpage_idp_list($this->page->url->out(false));
                        if (!empty($oidc)) {
                            $loginformcontext['hasoidc'] = true;
                            $loginformcontext['oidcdata'] = \auth_plugin_base::prepare_identity_providers_for_output($oidc, $this);
                        }
                    }
                }
                $headercontext['loginoruser'] = '<li class="nav-item">' .
                    $this->render_from_template('theme_adaptable/headerloginform', $loginformcontext) . '</li>';
            } else {
                $headercontext['loginoruser'] = '';
            }
        } else {
            // Display user profile menu.
            // Only used when user is logged in and not on the secure layout.
            if ((isloggedin()) && ($this->page->pagelayout != 'secure')) {
                $headercontext['loginoruser'] = '<li class="nav-item dropdown ml-3 ml-md-2 mr-2 mr-md-0">' . $this->user_menu() . '</li>';
            } else {
                $headercontext['loginoruser'] = '';
            }
        }

        /* Check if this is a course or module page and check setting to hide site title.
           If not one of these pages, by default show it (set $hideheadertitle to false). */
        if ((strstr($this->page->pagetype, 'course')) ||
            (strstr($this->page->pagetype, 'mod')) && ($this->page->course->id != SITEID)) {
            $hideheadertitle = !empty(($themesettings->coursepageheaderhidetitle)) ? true : false;
        } else {
            $hideheadertitle = false;
        }
        if (!$hideheadertitle) {
            $headercontext['headerlogo'] = $this->get_logo($currenttopcat, $shownavbar);
            $headercontext['headertitle'] = $this->get_title($currenttopcat);
        }

        $headercontext['headerbg'] = $headerbg;
        $headercontext['shownavbar'] = $shownavbar;

        // Navbar Menu.
        if ($shownavbar) {
            $headercontext['shownavbar'] = [
                'navigationmenu' => $this->navigation_menu('main-navigation'),
                'output' => $this,
                'toolsmenu' => $this->tools_menu(),
            ];

            $navbareditsettings = $themesettings->editsettingsbutton;
            $headercontext['shownavbar']['showcog'] = true;
            $showeditbuttons = false;

            if ($navbareditsettings == 'button') {
                $showeditbuttons = true;
                $headercontext['shownavbar']['showcog'] = false;
            } else if ($navbareditsettings == 'cogandbutton') {
                $showeditbuttons = true;
            }

            if ($headercontext['shownavbar']['showcog']) {
                $headercontext['shownavbar']['coursemenucontent'] = $this->context_header_settings_menu();
                $headercontext['shownavbar']['othermenucontent'] = $this->region_main_settings_menu();
            }

            /* Ensure to only hide the button on relevant pages.  Some pages will need the button, such as the
               dashboard page. Checking if the cog is being displayed above to figure out if it still needs to
               show (when there is no cog). Also show mod pages (e.g. Forum, Lesson) as these sometimes have
               a button for a specific purpose. */
            if (($showeditbuttons) ||
                (($headercontext['shownavbar']['showcog']) &&
                ((empty($headercontext['shownavbar']['coursemenucontent'])) &&
                (empty($headercontext['shownavbar']['othermenucontent'])))) ||
                (strstr($this->page->pagetype, 'mod-'))) {
                $headercontext['shownavbar']['pageheadingbutton'] = $this->page_heading_button();
            }

            if (isloggedin()) {
                if ($themesettings->enablezoom) {
                    $headercontext['shownavbar']['enablezoom'] = true;
                    $headercontext['shownavbar']['enablezoomshowtext'] = ($themesettings->enablezoomshowtext);
                }
            }
        }
        $headercontext['topmenus'] = $this->get_top_menus(false);

        if ($adaptableheaderstyle == "style1") {
            $headercontext['menuslinkright'] = (!empty($themesettings->menuslinkright));
            $headercontext['langmenu'] = (empty($this->page->layout_options['langmenu']) ||
                $this->page->layout_options['langmenu']);
            $headercontext['responsiveheader'] = $themesettings->responsiveheader;

            if (!empty($themesettings->pageheaderlayout)) {
                $headercontext['pageheaderoriginal'] = ($themesettings->pageheaderlayout == 'original');
            } else {
                $headercontext['pageheaderoriginal'] = true;
            }

            $headersearchandsocial = (!empty($themesettings->headersearchandsocial)) ?
                $themesettings->headersearchandsocial : 'none';

            // Search box and social icons.
            switch ($headersearchandsocial) {
                case 'socialheader':
                    $headersocialcontext = [
                        'classes' => $themesettings->responsivesocial,
                        'pageheaderoriginal' => $headercontext['pageheaderoriginal'],
                        'output' => $this,
                    ];
                    $headercontext['searchandsocialheader'] =
                        $this->render_from_template('theme_adaptable/headersocial', $headersocialcontext);
                    break;
                case 'searchmobilenav':
                    $headercontext['searchandsocialnavbar'] = $this->search_box();
                    $headercontext['searchandsocialnavbarextra'] = ' d-md-block d-lg-none my-auto';
                    $headersearchcontext = [
                        'pagelayout' => ($headercontext['pageheaderoriginal']) ? 'pagelayoutoriginal' : 'pagelayoutalternative',
                        'search' => $this->search_box(),
                    ];
                    $headercontext['searchandsocialheader'] =
                        $this->render_from_template('theme_adaptable/headersearch', $headersearchcontext);
                    break;
                case 'searchheader':
                    $headersearchcontext = [
                        'pagelayout' => ($headercontext['pageheaderoriginal']) ? 'pagelayoutoriginal' : 'pagelayoutalternative',
                        'search' => $this->search_box(),
                    ];
                    $headercontext['searchandsocialheader'] =
                        $this->render_from_template('theme_adaptable/headersearch', $headersearchcontext);
                    break;
                case 'searchnavbar':
                    $headercontext['searchandsocialnavbar'] = $this->search_box();
                    break;
                case 'searchnavbarsocialheader':
                    $headercontext['searchandsocialnavbar'] = $this->search_box();
                    $headersocialcontext = [
                        'classes' => $themesettings->responsivesocial,
                        'pageheaderoriginal' => $headercontext['pageheaderoriginal'],
                        'output' => $this,
                    ];
                    $headercontext['searchandsocialheader'] =
                        $this->render_from_template('theme_adaptable/headersocial', $headersocialcontext);
                    break;
            }

            echo $this->render_from_template('theme_adaptable/headerstyleone', $headercontext);
        } else if ($adaptableheaderstyle == "style2") {
            $headercontext['responsiveheader'] = $themesettings->responsiveheader;
            if (!empty($themesettings->pageheaderlayouttwo)) {
                $headercontext['pageheaderoriginal'] = ($themesettings->pageheaderlayouttwo == 'original');
            } else {
                $headercontext['pageheaderoriginal'] = true;
            }

            if ($headercontext['pageheaderoriginal']) {
                $headercontext['navbarsearch'] = $this->search_box();
            }

            if (empty($this->page->layout_options['langmenu']) || $this->page->layout_options['langmenu']) {
                $headercontext['langmenu'] = $this->lang_menu(false);
            }

            echo $this->render_from_template('theme_adaptable/headerstyletwo', $headercontext);
        }
        if ($optionsdata['data']['stickynavbar']) {
            echo '<div id="page" class="' . implode(' ', $pageclasses) . '">';
        }
        if (!empty($courseindextogglemarkup)) {
            echo $courseindextogglemarkup;
        }
        if (!empty($sideposttogglemarkup)) {
            echo $sideposttogglemarkup;
        }
        echo $this->get_alert_messages();
    }

    /**
     * No header.
     */
    public function noheader() {
        $themesettings = \theme_adaptable\toolbox::get_settings();

        $bodyclasses = [];
        $bodyclasses[] = 'theme_adaptable';
        $bodyclasses[] = 'two-column';
        $standardscreenwidthclass = 'standard';
        if (!empty($themesettings->standardscreenwidth)) {
            $bodyclasses[] = $themesettings->standardscreenwidth;
        } else {
            $bodyclasses[] = 'standard';
        }

        theme_adaptable_initialise_full();
        $bodyclasses[] = theme_adaptable_get_full();

        if (!empty($themesettings->pageloadingprogress)) {
            $this->page->requires->js_call_amd('theme_adaptable/pace_init', 'init', [$themesettings->pageloadingprogresstheme]);
        }

        // Include header.
        $this->head($bodyclasses);

        echo '<div id="page" class="container-fluid">';

        // Display alerts.
        echo $this->get_alert_messages();
    }

    /**
     * Head.
     *
     * @param array Of body classes.
     */
    protected function head($bodyclasses) {
        global $SITE;
        $themesettings = \theme_adaptable\toolbox::get_settings();

        $headcontext = new stdClass;
        $headcontext->output = $this;
        $headcontext->sitefullname = $SITE->fullname;
        $headcontext->pagetitle = $this->page_title();
        $siteurl = new url('');
        $headcontext->siteurl = $siteurl->out();
        $headcontext->maincolor = $themesettings->maincolor;

        if (!empty($themesettings->googlefonts)) {
            // Select fonts used.
            $fontssubset = '';
            if (!empty($themesettings->fontsubset)) {
                // Get the Google fonts subset.
                $fontssubset = '&subset='.$themesettings->fontsubset;
            }

            if (!empty($themesettings->fontname)) {
                switch ($themesettings->fontname) {
                    case 'default':
                    case 'sans-serif':
                        // Use 'sans-serif'.
                    break;

                    default:
                        // Get the Google main font.
                        $fontname = str_replace(" ", "+", $themesettings->fontname);
                        $fontweight = ':'.$themesettings->fontweight.','.$themesettings->fontweight.'i';
                        $headcontext->fontname = $fontname.$fontweight.$fontssubset;
                    break;
                }
            }

            if (!empty($themesettings->fontheadername)) {
                switch ($themesettings->fontheadername) {
                    case 'default':
                    case 'sans-serif':
                        // Use 'sans-serif'.
                    break;

                    default:
                        // Get the Google header font.
                        $fontheadername = str_replace(" ", "+", $themesettings->fontheadername);
                        $fontheaderweight = ':'.$themesettings->fontheaderweight.','.$themesettings->fontheaderweight.'i';
                        $headcontext->fontheadername = $fontheadername.$fontheaderweight.$fontssubset;
                    break;
                }
            }

            if (!empty($themesettings->fonttitlename)) {
                switch ($themesettings->fonttitlename) {
                    case 'default':
                    case 'sans-serif':
                        // Use 'sans-serif'.
                    break;

                    default:
                        // Get the Google title font.
                        $fonttitlename = str_replace(" ", "+", $themesettings->fonttitlename);
                        $fonttitleweight = ':'.$themesettings->fonttitleweight.','.$themesettings->fonttitleweight.'i';
                        $headcontext->fonttitlename = $fonttitlename.$fonttitleweight.$fontssubset;
                    break;
                }
            }
        }
        echo $this->render_from_template('theme_adaptable/head', $headcontext);

        echo '<body '.$this->body_attributes($bodyclasses).'>';
        echo $this->standard_top_of_body_html();
        echo '<div id="page-wrapper">';
    }

    /**
     * Course index header.
     */
    protected function courseindexheader() {
        global $CFG;
        $courseindex = \theme_adaptable\toolbox::get_setting('courseindexenabled');

        if ($courseindex) {
            require_once($CFG->dirroot . '/course/lib.php');
            $courseindex = core_course_drawer();
        }

        if (!$courseindex) {
            $courseindexopen = false;
            $courseindexmarkup = '';
            $courseindextogglemarkup = '';
        } else {
            if (isloggedin()) {
                $courseindexopen = (get_user_preferences('drawer-open-index', true) == true);
            } else {
                $courseindexopen = false;
            }

            $left = \theme_adaptable\toolbox::get_setting('blockside');
            $stickynavbar = \theme_adaptable\toolbox::get_setting('stickynavbar');

            $templatecontext = [
                'courseindexopen' => $courseindexopen,
                'courseindex' => $courseindex,
                'left' => $left,
                'stickynavbar' => $stickynavbar,
            ];

            $courseindexmarkup = $this->render_from_template('theme_adaptable/courseindex', $templatecontext);
            $courseindextogglemarkup = $this->render_from_template('theme_adaptable/courseindextoggle', $templatecontext);
        }

        return [$courseindexopen, $courseindex, $courseindexmarkup, $courseindextogglemarkup];
    }

    /**
     * Side post header.
     */
    protected function sidepostheader() {
        $left = \theme_adaptable\toolbox::get_setting('blockside');
        $stickynavbar = \theme_adaptable\toolbox::get_setting('stickynavbar');

        if (isloggedin()) {
            $sidepostopen = (get_user_preferences('drawer-open-block', true) == true);
        } else {
            $sidepostopen = false;
        }

        $sideposthtml = $this->blocks('side-post');
        // Blocks or add block button.
        $hassidepost = ((strpos($sideposthtml, 'data-block=') !== false) || (strpos($sideposthtml, 'data-key="addblock"') !== false));
        if (!$hassidepost) {
            $sidepostopen = false;
        }

        if (defined('BEHAT_SITE_RUNNING')) {
            $sidepostopen = true;
        }

        $sidepostcontext = [
            'hassidepost' => $hassidepost,
            'left' => $left,
            'sidepostopen' => $sidepostopen,
            'sidepost' => $sideposthtml,
            'stickynavbar' => $stickynavbar,
        ];

        $sideposttogglecontext = [
            'hassidepost' => $hassidepost,
            'left' => $left,
            'sidepostopen' => $sidepostopen,
        ];

        $sidepostmarkup = $this->render_from_template('theme_adaptable/sidepost', $sidepostcontext);
        $sideposttogglemarkup = $this->render_from_template('theme_adaptable/sideposttoggle', $sideposttogglecontext);

        return [$hassidepost, $sidepostmarkup, $sideposttogglemarkup];
    }

    /**
     * Secondary navigation.
     */
    public function secondarynav() {
        $secondarynavigation = '';
        $overflow = '';
        if ($this->page->has_secondary_navigation()) {
            $tablistnav = $this->page->has_tablist_secondary_navigation();
            $moremenu = new \core\navigation\output\more_menu($this->page->secondarynav, 'nav-tabs', true, $tablistnav);
            $secondarynavigation = $moremenu->export_for_template($this);
            if (!empty($secondarynavigation)) {
                $secondarynavigation = $this->render_from_template('theme_adaptable/secondarynav', $secondarynavigation);

                $overflowdata = $this->page->secondarynav->get_overflow_menu_data();
                if (!is_null($overflowdata)) {
                    $overflow = $overflowdata->export_for_template($this);
                    $overflow = $this->render_from_template('theme_adaptable/overflow', $overflow);
                }
            }
        }

        return [$secondarynavigation, $overflow];
    }

    /**
     * Yes footer.
     */
    public function yesfooter() {
        global $USER;

        $themesettings = \theme_adaptable\toolbox::get_settings();

        $context = new stdClass;
        $context->output = $this;
        $context->responsivepagefooter = $themesettings->responsivepagefooter;
        $context->showfooterblocks = $themesettings->showfooterblocks;

        if ($themesettings->hidefootersocial == 1) {
            $context->socialicons = $this->socialicons();
        }

        $context->footnote = \theme_adaptable\toolbox::get_setting('footnote', 'format_moodle');
        $context->moodledocs = $themesettings->moodledocs;

        $context->debug = $this->debug_footer_html();

        // If admin settings page, show template for floating save / discard buttons.
        if (strstr($this->page->pagetype, 'admin-setting')) {
            if ($themesettings->enablesavecanceloverlay) {
                $savediscardcontext = [
                    'topmargin' => ($themesettings->stickynavbar ? '35px' : '0'),
                    'savetext' => get_string('savebuttontext', 'theme_adaptable'),
                    'discardtext' => get_string('discardbuttontext', 'theme_adaptable'),
                ];
                $context->savediscard = $this->render_from_template('theme_adaptable/savediscard', $savediscardcontext);
            }
        }

        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();
        if (is_object($localtoolbox)) {
            if (method_exists($localtoolbox, 'get_custom_js')) { // Todo - Temporary until such time as not.
                $theme = \theme_adaptable\toolbox::get_theme();
                $context->customjsfiles =
                    \theme_adaptable\admin_setting_configstoredfiles::setting_file_urls('customjsfiles', 'customjsfiles', $theme);

                $context->customjs = $localtoolbox->get_custom_js($themesettings, $this->page, $this);
            }
        }

        echo $this->render_from_template('theme_adaptable/footer', $context);
    }

    /**
     * No footer.
     */
    public function nofooter() {
        $themesettings = \theme_adaptable\toolbox::get_settings();

        $context = new stdClass;
        $context->output = $this;
        $localtoolbox = \theme_adaptable\toolbox::get_local_toolbox();
        if (is_object($localtoolbox)) {
            if (method_exists($localtoolbox, 'get_custom_js')) { // Todo - Temporary until such time as not.
                $theme = \theme_adaptable\toolbox::get_theme();
                $context->customjsfiles =
                    \theme_adaptable\admin_setting_configstoredfiles::setting_file_urls('customjsfiles', 'customjsfiles', $theme);

                $context->customjs = $localtoolbox->get_custom_js($themesettings, $this->page, $this);
            }
        }

        echo $this->render_from_template('theme_adaptable/nofooter', $context);
    }

    /*
     * Todo - Template the layouts... somehow!
     */

    /**
     * One column layout.
     */
    public function columns_one_layout() {
        // Include header.
        $this->yesheader(false);
        // Include secondary navigation.
        [$secondarynavigation, $overflow] = $this->secondarynav();

        echo '<div id="maincontainer" class="container outercont">';
        echo $this->get_news_ticker();
        echo $this->page_navbar();
        echo '<div id="page-content" class="row">';
        echo '<div id="region-main-box" class="col-12">';
        echo '<section id="region-main">';
        echo $this->course_content_header();
        if (!empty($secondarynavigation)) {
            echo $secondarynavigation;
        }
        echo $this->activity_header();
        if (!empty($overflow)) {
            echo $overflow;
        }
        echo $this->main_content();
        echo $this->activity_navigation();
        echo $this->course_content_footer();
        echo '</section>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        if (empty($this->page->layout_options['nofooter'])) {
            // Footer.
            $this->yesfooter();
        } else {
            $this->nofooter();
        }
    }

    /**
     * Two column layout.
     */
    public function columns_two_layout() {
        $themesettings = \theme_adaptable\toolbox::get_settings();

        // Include header.
        $this->yesheader(true);

        // Include secondary navigation.
        [$secondarynavigation, $overflow] = $this->secondarynav();

        echo '<div id="maincontainer" class="container outercont">';
        echo $this->get_news_ticker();
        echo $this->page_navbar();
        echo '<div id="page-content" class="row">';
        echo '<div id="region-main-box" class="col-12">';
        echo '<section id="region-main">';
        echo $this->get_course_alerts();
        echo $this->course_content_header();
        if ($this->page->pagetype == 'user-profile') {
            echo $this->context_header();
        }
        if (!empty($secondarynavigation)) {
            echo $secondarynavigation;
        }
        echo $this->activity_header();
        if (!empty($overflow)) {
            echo $overflow;
        }
        echo $this->main_content();

        // Display course page block activity bottom region if this is a mod page of type where you're viewing
        // a section, page or book (chapter).
        if (!empty($themesettings->coursepageblockactivitybottomenabled)) {
            if (($this->page->pagetype == 'mod-book-view') || ($this->page->pagetype == 'mod-page-view')) {
                echo $this->get_block_regions('customrowsetting', 'course-section-', '12-0-0-0');
            }
        }

        echo $this->activity_navigation();
        echo $this->course_content_footer();

        echo '</section>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Footer.
        $this->yesfooter();
    }

    /**
     * Course layout.
     */
    public function course_layout() {
        global $COURSE;
        $themesettings = \theme_adaptable\toolbox::get_settings();

        $sidepostdrawer = true;
        $movesidebartofooter = !empty(($themesettings->coursepagesidebarinfooterenabled)) ? 2 : 1;
        if ((!empty($movesidebartofooter)) && ($movesidebartofooter == 2)) {
            $sidepostdrawer = false;
        }

        // Include header.
        $this->yesheader($sidepostdrawer);

        // Include secondary navigation.
        [$secondarynavigation, $overflow] = $this->secondarynav();

        // Definition of block regions for top and bottom.  These are used in potentially retrieving
        // any missing block regions.
        $blocksarray = [
            ['settingsname' => 'coursepageblocklayoutlayouttoprow',
             'classnamebeginswith' => 'course-top-', ],
            ['settingsname' => 'coursepageblocklayoutlayoutbottomrow',
             'classnamebeginswith' => 'course-bottom-', ],
        ];

        echo '<div id="maincontainer" class="container outercont">';
        echo $this->get_news_ticker();
        echo $this->page_navbar();
        echo '<div id="page-content" class="row">';

        // If course page, display course top block region.
        if (!empty($themesettings->coursepageblocksenabled)) {
            echo '<div id="frontblockregion" class="container">';
            echo '<div class="row">';
            echo $this->get_block_regions('coursepageblocklayoutlayouttoprow', 'course-top-');
            echo '</div>';
            echo '</div>';
        }

        echo '<div id="region-main-box" class="col-12">';
        echo '<section id="region-main">';

        if (!empty($themesettings->tabbedlayoutcoursepage)) {
            // Use Adaptable tabbed layout.
            $currentpage = theme_adaptable_get_current_page();

            $taborder = explode('-', $themesettings->tabbedlayoutcoursepage);

            echo '<main id="coursetabcontainer" class="tabcontentcontainer">';

            $sectionid = optional_param('sectionid', 0, PARAM_INT);
            $section = optional_param('section', 0, PARAM_INT);
            if ((!empty($themesettings->tabbedlayoutcoursepagelink)) &&
                (($sectionid) || ($section))) {
                $courseurl = new url('/course/view.php', ['id' => $COURSE->id]);
                echo '<div class="linktab"><a href="' . $courseurl->out(true) . '"><i class="fa fa-th-large"></i></a></div>';
            }

            $tabcount = 0;
            foreach ($taborder as $tabnumber) {
                if ($tabnumber == 0) {
                    $tabname = 'tab-content';
                    $tablabel = get_string('tabbedlayouttablabelcourse', 'theme_adaptable');
                } else {
                    $tabname = 'tab' . $tabnumber;
                    $tablabel = get_string('tabbedlayouttablabelcourse' . $tabnumber, 'theme_adaptable');
                }

                $checkedstatus = '';
                if (($tabcount == 0 && $currentpage == 'coursepage') ||
                    ($currentpage != 'coursepage' && $tabnumber == 0)) {
                    $checkedstatus = 'checked';
                }

                $extrastyles = '';

                if ($currentpage == 'coursepage') {
                    $extrastyles = ' style="display: none"';
                }

                echo '<input id="' . $tabname . '" type="radio" name="tabs" class="coursetab" ' . $checkedstatus . ' >' .
                    '<label for="' . $tabname . '" class="coursetab" ' . $extrastyles . '>' . $tablabel . '</label>';

                $tabcount++;
            }

            /* Basic array used by appropriately named blocks below (e.g. course-tab-one).  All this is to re-use existing
                functionality and the non-use of numbers in block region names. */
            $wordtonumber = [1 => 'one', 2 => 'two'];

            foreach ($taborder as $tabnumber) {
                if ($tabnumber == 0) {
                    echo '<section id="adaptable-course-tab-content" class="adaptable-tab-section tab-panel">';

                    echo $this->get_course_alerts();
                    if (!empty($themesettings->coursepageblocksliderenabled)) {
                        echo $this->get_block_regions('customrowsetting', 'news-slider-', '12-0-0-0');
                    }

                    echo $this->course_content_header();
                    if (!empty($secondarynavigation)) {
                        echo $secondarynavigation;
                    }
                    if (!empty($overflow)) {
                        echo $overflow;
                    }
                    echo $this->main_content();
                    echo $this->course_content_footer();

                    echo '</section>';
                } else {
                    echo '<section id="adaptable-course-tab-' . $tabnumber . '" class="adaptable-tab-section tab-panel">';

                    echo $this->get_block_regions(
                        'customrowsetting',
                        'course-tab-' . $wordtonumber[$tabnumber] . '-',
                        '12-0-0-0'
                    );
                    echo '</section>';
                }
            }
            echo '</main>';
        } else {
            echo $this->get_course_alerts();
            if (!empty($themesettings->coursepageblocksliderenabled)) {
                echo $this->get_block_regions('customrowsetting', 'news-slider-', '12-0-0-0');
            }
            echo $this->course_content_header();
            if (!empty($secondarynavigation)) {
                echo $secondarynavigation;
            }
            if (!empty($overflow)) {
                echo $overflow;
            }
            echo $this->main_content();
            echo $this->course_content_footer();
        }

        /* Check if the block regions are disabled in settings.  If it is and there were any blocks
           assigned to those regions, they would obviously not display.  This will allow to override
           the call to get_missing_block_regions to just display them all. */
        $displayall = false;
        if (empty($themesettings->coursepageblocksenabled)) {
            $displayall = true;
        }

        /* Check here if sidebar is configured to be in footer as we want to include
           the sidebar information in the main content. */
        if ($movesidebartofooter == 1) {
            echo '</section>';
            echo '</div>';

            /* Get any missing blocks from changing layout settings.  E.g. From 4-4-4-4 to 6-6-0-0, to recover
               what was in the last 2 spans that are now 0. */
            echo $this->get_missing_block_regions($blocksarray, 'col-12', $displayall);
        }

        // If course page, display course bottom block region.
        if (!empty($themesettings->coursepageblocksenabled)) {
            echo '<div id="frontblockregion" class="container">';
            echo '<div class="row">';
            echo $this->get_block_regions('coursepageblocklayoutlayoutbottomrow', 'course-bottom-');
            echo '</div>';
            echo '</div>';
        }

        if ($movesidebartofooter == 2) {
            $hassidepost = $this->page->blocks->region_has_content('side-post', $this);

            if ($hassidepost) {
                echo $this->blocks('side-post', 'col-12 d-print-none');
            }

            /* Get any missing blocks from changing layout settings.  E.g. From 4-4-4-4 to 6-6-0-0, to recover
               what was in the last 2 spans that are now 0. */
            echo $this->get_missing_block_regions($blocksarray, [], $displayall);

            echo '</section>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';

        // Include footer.
        $this->yesfooter();

        if (!empty($themesettings->tabbedlayoutcoursepage)) {
            if (!empty($themesettings->tabbedlayoutcoursepagetabpersistencetime)) {
                $tabbedlayoutcoursepagetabpersistencetime = $themesettings->tabbedlayoutcoursepagetabpersistencetime;
            } else {
                $tabbedlayoutcoursepagetabpersistencetime = 30;
            }
            $this->page->requires->js_call_amd('theme_adaptable/tabbed', 'init', ['currentpage' => $currentpage,
                'tabpersistencetime' => $tabbedlayoutcoursepagetabpersistencetime, ]);
        }
    }

    /**
     * Dashboard layout.
     */
    public function dashboard_layout() {
        global $CFG, $USER;
        $themesettings = \theme_adaptable\toolbox::get_settings();

        // Include header.
        $this->yesheader(true);

        // Include secondary navigation.
        [$secondarynavigation, $overflow] = $this->secondarynav();

        // Set layout.
        $dashblocksposition = (!empty($themesettings->dashblocksposition)) ? $themesettings->dashblocksposition : 'abovecontent';

        $dashblocklayoutlayoutrow = '';
        if (!empty($themesettings->dashblocksenabled)) {
            $dashblocklayoutlayoutrow = '<div id="frontblockregion" class="row">';
            $dashblocklayoutlayoutrow .= $this->get_block_regions('dashblocklayoutlayoutrow', 'frnt-market-');
            $dashblocklayoutlayoutrow .= '</div>';
        }

        echo '<div id="maincontainer" class="container outercont">';

        echo $this->get_news_ticker();

        if ((!empty($themesettings->dashblocksenabled)) &&
            (empty($themesettings->tabbedlayoutdashboard)) && ($dashblocksposition == 'abovecontent')) {
            echo $dashblocklayoutlayoutrow;
        }

        echo '<div id="page-content" class="row">';
        if (!empty($themesettings->tabbedlayoutdashboard)) {
            $showtabs = [0 => true, 1 => true, 2 => true];
            // Get any custom user profile field restriction for tab 1 and 2. (e.g. showtab1=false).
            require_once($CFG->dirroot . '/user/profile/lib.php');
            require_once($CFG->dirroot . '/user/lib.php');
            profile_load_data($USER);

            if (!empty($themesettings->tabbedlayoutdashboardtab1condition)) {
                $fields = explode('=', $themesettings->tabbedlayoutdashboardtab1condition);
                $ftype = $fields[0];
                $setvalue = $fields[1];

                // Get user profile field (if it exists).
                $ftype = "profile_field_$ftype";
                if (isset($USER->$ftype)) {
                    if ($USER->$ftype != $setvalue) {
                        // Condition is true, so don't show this tab.
                        $showtabs[1] = false;
                    }
                }
            }

            if (!empty($themesettings->tabbedlayoutdashboardtab2condition)) {
                $fields = explode('=', $themesettings->tabbedlayoutdashboardtab2condition);
                $ftype = $fields[0];
                $setvalue = $fields[1];

                // Get user profile field (if it exists).
                $ftype = "profile_field_$ftype";
                if (isset($USER->$ftype)) {
                    if ($USER->$ftype != $setvalue) {
                        // Condition is true, so don't show this tab.
                        $showtabs[2] = false;
                    }
                }
            }

            $taborder = explode('-', $themesettings->tabbedlayoutdashboard);
            echo '<div id="region-main-box" class="col-12">';
            echo '<section id="region-main">';

            echo '<main id="dashboardtabcontainer" class="tabcontentcontainer">';

            $tabcount = 0;
            foreach ($taborder as $tabnumber) {
                if ((!empty($showtabs[$tabnumber])) && ($showtabs[$tabnumber] == true)) {
                    // Tab 0 is the original content tab.
                    if ($tabnumber == 0) {
                        $tabname = 'dashboard-tab-content';
                        $tablabel = get_string('tabbedlayouttablabeldashboard', 'theme_adaptable');
                    } else {
                        $tabname = 'dashboard-tab' . $tabnumber;
                        $tablabel = get_string('tabbedlayouttablabeldashboard' . $tabnumber, 'theme_adaptable');
                    }

                    echo '<input id="' . $tabname . '" type="radio" name="tabs" class="dashboardtab" ' .
                        ($tabcount == 0 ? ' checked ' : '') . '>' .
                        '<label for="' . $tabname . '" class="dashboardtab">' . $tablabel . '</label>';
                    $tabcount++;
                }
            }

            // Basic array used by appropriately named blocks below (e.g. course-tab-one).  All this is due to the re-use of
            // existing functionality and non-use of numbers in block region names.
            $wordtonumber = [1 => 'one', 2 => 'two'];
            foreach ($taborder as $tabnumber) {
                if ($tabnumber == 0) {
                    echo '<section id="adaptable-dashboard-tab-content" class="adaptable-tab-section tab-panel">';

                    if ((!empty($themesettings->dashblocksenabled)) && ($dashblocksposition == 'abovecontent')) {
                        echo $dashblocklayoutlayoutrow;
                    }
                    echo $this->course_content_header();
                    if (!empty($secondarynavigation)) {
                        echo $secondarynavigation;
                    }
                    if (!empty($overflow)) {
                        echo $overflow;
                    }
                    echo $this->main_content();
                    echo $this->course_content_footer();
                    if ((!empty($themesettings->dashblocksenabled)) && ($dashblocksposition == 'belowcontent')) {
                        echo $dashblocklayoutlayoutrow;
                    }

                    echo '</section>';
                } else {
                    if ($showtabs[$tabnumber] == true) {
                        echo '<section id="adaptable-dashboard-tab-' . $tabnumber . '" class="adaptable-tab-section tab-panel">';
                        echo $this->get_block_regions('customrowsetting', 'my-tab-' . $wordtonumber[$tabnumber] .
                            '-', '12-0-0-0');
                        echo '</section>';
                    }
                }
            }

            echo '</main>';
            echo '</section>';
            echo '</div>';
        } else {
            echo '<div id="region-main-box" class="col-12">';
            echo '<section id="region-main">';
            echo $this->course_content_header();
            if (!empty($secondarynavigation)) {
                echo $secondarynavigation;
            }
            if (!empty($overflow)) {
                echo $overflow;
            }
            echo $this->main_content();
            echo $this->course_content_footer();
            echo '</section>';
            echo '</div>';
        }
        echo '</div>';

        if ((!empty($themesettings->dashblocksenabled)) && (empty($themesettings->tabbedlayoutdashboard))
            && ($dashblocksposition == 'belowcontent')) {
            echo $dashblocklayoutlayoutrow;
        }

        if (is_siteadmin()) {
            echo '<div class="hidden-blocks">';
            echo '<div class="row">';
            echo '<h3>' . get_string('frnt-footer', 'theme_adaptable') . '</h3>';
            echo $this->blocks('frnt-footer', 'col-10');
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';

        // Footer.
        $this->yesfooter();
    }

    /**
     * Embedded layout.
     */
    public function embedded_layout() {
        echo $this->doctype();
        echo '<html ' . $this->htmlattributes() . '>';
        echo '<head>';
        echo '<title>'. $this->page_title() .'</title>';
        echo '<link rel="shortcut icon" href="' . $this->favicon() . '" />';
        echo $this->standard_head_html();
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '</head>';
        echo '<body ' . $this->body_attributes() . '>';

        echo $this->standard_top_of_body_html();
        $fakeblocks = $this->blocks('side-pre', [], 'aside', true);
        $hasfakeblocks = strpos($fakeblocks, 'data-block="_fake"') !== false;

        echo '<div id="page-wrapper">';
        if ($hasfakeblocks) {
            echo '<div id="page" class="has-fake-blocks">';
            echo '<section class="embedded-blocks" aria-label="' . get_string('blocks') . '">';
            echo $fakeblocks;
            echo '</section>';
        } else {
            echo '<div id="page">';
        }

        echo '<section class="embedded-main">';
        echo $this->main_content();
        echo '</section>';
        echo '</div>';
        echo '</div>';

        $this->nofooter();
    }

    /**
     * Frontpage layout.
     */
    public function frontpage_layout() {
        global $USER;
        $themesettings = \theme_adaptable\toolbox::get_settings();

        // Header.
        $sidepostdrawer = false;
        if (($themesettings->frontpageuserblocksenabled) || (is_siteadmin($USER))) {
            $sidepostdrawer = true;
        }
        $this->yesheader($sidepostdrawer);

        // Include secondary navigation.
        [$secondarynavigation, $overflow] = $this->secondarynav();

        if (!empty($secondarynavigation)) {
            echo $secondarynavigation;
        }
        if (!empty($overflow)) {
            echo $overflow;
        }

        echo '<div class="container">';
        echo $this->get_news_ticker();
        echo '</div>';

        // Slider.
        echo $this->get_frontpage_slider();

        // And let's show Infobox 1 if enabled.
        if (!empty($themesettings->infobox)) {
            if (!empty($themesettings->infoboxfullscreen)) {
                echo '<div id="theinfo">';
            } else {
                echo '<div id="theinfo" class="container">';
            }
            echo '<div class="row">';
            echo '<div class="col-12">';
            echo \theme_adaptable\toolbox::get_setting('infobox', 'format_moodle');
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        echo '<div class="container">';
        // If Information Blocks are enabled then let's show them.
        if (!empty($themesettings->informationblocksenabled)) {
            echo $this->get_flexible_blocks('information');
        }

        // If Marketing Blocks are enabled then let's show them.
        if (!empty($themesettings->frontpagemarketenabled)) {
            echo $this->get_marketing_blocks();
        }
        echo '</div>';

        if (!empty($themesettings->frontpageblocksenabled)) {
            echo '<div id="frontblockregion" class="container">';
            echo '<div class="row">';
            echo $this->get_block_regions('blocklayoutlayoutrow', 'frnt-market-');
            echo '</div>';
            echo '</div>';
        }

        // And finally let's show the Infobox 2 if enabled.
        if (!empty($themesettings->infobox2)) {
            if (!empty($themesettings->infoboxfullscreen)) {
                echo '<div id="theinfo2">';
            } else {
                echo '<div id="theinfo2" class="container">';
            }
            echo '<div class="row">';
            echo '<div class="col-12">';
            echo \theme_adaptable\toolbox::get_setting('infobox2', 'format_moodle');
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }

        echo '<div id="maincontainer" class="container outercont">';
        echo '<div id="page-content" class="row">';
        echo '<div id="page-navbar" class="col-12">';
        echo '<nav class="breadcrumb-button">';
        echo $this->page_heading_button();
        echo '</nav>';
        echo '</div>';

        echo '<div id="region-main-box" class="col-12">';
        echo '<section id="region-main">';
        echo $this->course_content_header();
        echo $this->main_content();
        echo $this->course_content_footer();
        echo '</section>';
        echo '</div>';
        echo '</div>';

        // Let's show the hidden blocks region ONLY for administrators.
        if (is_siteadmin()) {
            echo '<div class="hidden-blocks">';
            echo '<div class="row">';

            if (!empty($themesettings->coursepageblocksliderenabled)) {
                echo $this->get_block_regions('customrowsetting', 'news-slider-', '12-0-0-0');
            }

            if (!empty($themesettings->coursepageblockactivitybottomenabled)) {
                echo $this->get_block_regions('customrowsetting', 'course-section-', '12-0-0-0');
            }

            if (!empty($themesettings->tabbedlayoutcoursepage)) {
                echo $this->get_block_regions('customrowsetting', 'course-tab-one-', '12-0-0-0');
                echo $this->get_block_regions('customrowsetting', 'course-tab-two-', '12-0-0-0');
            }

            if (!empty($themesettings->tabbedlayoutdashboard)) {
                echo $this->get_block_regions('customrowsetting', 'my-tab-one-', '12-0-0-0');
                echo $this->get_block_regions('customrowsetting', 'my-tab-two-', '12-0-0-0');
            }

            echo '<h3>' . get_string('frnt-footer', 'theme_adaptable') . '</h3>';
            echo $this->blocks('frnt-footer', 'col-10');
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';

        // Footer.
        $this->yesfooter();
    }

    /**
     * Login layout.
     */
    public function login_layout() {
        $logincontent = '<div class="login-wrapper"><div class="login-container">';
        $logincontent .= $this->main_content();
        $logincontent .= '</div></div>';

        $result = $this->generate_login($logincontent);

        if (!empty($result->header)) {
            $this->yesheader(false);
        } else {
            $this->noheader();
        }
        $this->page->set_secondary_navigation(false);

        echo '<div class="container outercont">';
        echo $this->page_navbar();
        echo '<div id="page-content" class="row">';
        echo '<div id="region-main-box" class="col-12">';
        echo '<section id="region-main">';
        echo $logincontent;
        echo '</section>';
        echo '</div>';
        echo '</div>';
        echo '</div>';

        // Footer.
        if (!empty($result->header)) {
            $this->yesfooter();
        } else {
            $this->nofooter();
        }
    }

    /**
     * Secure layout.
     */
    public function secure_layout() {
        // Header.
        $this->yesheader(true);

        // Include secondary navigation.
        [$secondarynavigation, $overflow] = $this->secondarynav();

        echo '<div id="page" class="container outercont">';
        echo $this->page_navbar();
        echo '<div id="page-content" class="row">';
        echo '<div id="region-main-box" class="col-12">';
        echo '<section id="region-main">';
        echo $this->get_course_alerts();
        echo $this->course_content_header();
        if (!empty($secondarynavigation)) {
            echo $secondarynavigation;
        }
        if (!empty($overflow)) {
            echo $overflow;
        }
        echo $this->main_content();
        echo '</section>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        $this->nofooter();
    }

    /**
     * Maintenance layout.
     */
    public function maintenance_layout() {
        echo $this->doctype();
        echo '<html ' . $this->htmlattributes() . '>';
        echo '<head>';
        echo '<title>'. $this->page_title() . '</title>';
        echo '<link rel="shortcut icon" href="'. $this->favicon() . '">';
        echo $this->standard_head_html();
        echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
        echo '</head>';
        echo '<body>';
        echo '<div class="container outercont">';
        echo '<div id="page-content" class="row">';
        echo '<section id="region-main" class="col-12">';
        echo $this->course_content_header();
        echo $this->main_content();
        echo $this->course_content_footer();
        echo '</section>';
        echo '</div>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
    }
}

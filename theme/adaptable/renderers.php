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
 * Version details
 *
 * @package    theme_adaptable
 * @copyright  2015 Jeremy Hopkins (Coventry University)
 * @copyright  2015-2017 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// Load libraries.
require_once($CFG->dirroot.'/course/renderer.php');
require_once($CFG->libdir.'/coursecatlib.php');
require_once($CFG->dirroot.'/message/lib.php');
require_once($CFG->dirroot.'/course/format/topics/renderer.php');
require_once($CFG->dirroot.'/course/format/weeks/renderer.php');

use \theme_adaptable\traits\single_section_page;

class theme_adaptable_format_topics_renderer extends format_topics_renderer {
    use single_section_page;
}

class theme_adaptable_format_weeks_renderer extends format_weeks_renderer {
    use single_section_page;
}

/******************************************************************************************
 * @copyright 2017 Gareth J Barnard
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 *
 * Grid format renderer for the Adaptable theme.
 */

// Check if GRID is installed before trying to override it.
if (file_exists("$CFG->dirroot/course/format/grid/renderer.php")) {
    include_once($CFG->dirroot."/course/format/grid/renderer.php");

    class theme_adaptable_format_grid_renderer extends format_grid_renderer {
        use single_section_page;

        /**
         * Generate the html for the 'Jump to' menu on a single section page.
         *
         * @param stdClass $course The course entry from DB.
         * @param array $sections The course_sections entries from the DB.
         * @param $displaysection the current displayed section number.
         *
         * @return string HTML to output.
         */
        protected function section_nav_selection($course, $sections, $displaysection) {
            $settings = $this->courseformat->get_settings();
            if (!$this->section0attop) {
                $section = 0;
            } else if ($settings['setsection0ownpagenogridonesection'] == 2) {
                $section = 0;
            } else {
                $section = 1;
            }
            return $this->section_nav_selection_content($course, $sections, $displaysection, $section);
        }

        /**
         * Generate next/previous section links for navigation.
         *
         * @param stdClass $course The course entry from DB.
         * @param array $sections The course_sections entries from the DB.
         * @param int $sectionno The section number in the coruse which is being displayed.
         * @return array associative array with previous and next section link.
         */
        public function get_nav_links($course, $sections, $sectionno) {
            $settings = $this->courseformat->get_settings();
            if (!$this->section0attop) {
                $buffer = -1;
            } else if ($settings['setsection0ownpagenogridonesection'] == 2) {
                $buffer = -1;
            } else {
                $buffer = 0;
            }
            return $this->get_nav_links_content($course, $sections, $sectionno, $buffer);
        }

        /**
         * Output the html for a single section page.
         *
         * @param stdClass $course The course entry from DB.
         * @param array $sections (argument not used).
         * @param array $mods (argument not used).
         * @param array $modnames (argument not used).
         * @param array $modnamesused (argument not used).
         * @param int $displaysection The section number in the course which is being displayed.
         */
        public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {
            $settings = $this->courseformat->get_settings();
            if (!$this->section0attop) {
                $section0attop = 0;
            } else if ($settings['setsection0ownpagenogridonesection'] == 2) {
                $section0attop = 0;
            } else {
                $section0attop = 1;
            }
            $this->print_single_section_page_content($course, $sections, $mods, $modnames, $modnamesused, $displaysection,
                $section0attop);
        }
    }
}

/******************************************************************************************
 * @copyright 2015 Jeremy Hopkins (Coventry University)
 * @copyright 2015 Fernando Acedo (3-bits.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * Core renderers for Adaptable theme
 */

class theme_adaptable_core_renderer extends core_renderer {
    /** @var custom_menu_item language The language menu if created */
    protected $language = null;

    /**
     * Internal implementation of user image rendering.
     *
     * @param user_picture $userpicture
     * @return string
     */
    protected function render_user_picture(\user_picture $userpicture) {
        if ($this->page->pagetype == 'mod-forum-discuss' ||
        $this->page->pagetype == 'course-view-socialwall' ||
        $this->page->pagetype == 'site-index') {
            $userpicture->size = 1;
        }
        return parent::render_user_picture($userpicture);
    }

    /**
     * Return list of the user's courses
     *
     * @return array list of courses
     */
    public function render_mycourses() {
        global $USER;

        // Set limit of courses to show in dropdown from setting.
        $coursedisplaylimit = '20';
        if (isset($this->page->theme->settings->mycoursesmenulimit)) {
            $coursedisplaylimit = $this->page->theme->settings->mycoursesmenulimit;
        }

        $courses = enrol_get_my_courses();

        $sortedcourses = array();
        $counter = 0;

        // Get courses in sort order into list.
        foreach ($courses as $course) {

            if (($counter >= $coursedisplaylimit) && ($coursedisplaylimit != 0)) {
                break;
            }

            $sortedcourses[] = $course;
            $counter++;

        }

        return array($sortedcourses);
    }



    /**
     * Returns the URL for the favicon.
     *
     * @return string The favicon URL
     */
    public function favicon() {
        if (!empty($this->page->theme->settings->favicon)) {
            return $this->page->theme->setting_file_url('favicon', 'favicon');
        }
        return parent::favicon();
    }

    /**
     * Returns settings as formatted text
     *
     * @param string $setting
     * @param string $format = false
     * @param string $theme = null
     * @return string
     */
    public function get_setting($setting, $format = false, $theme = null) {
        if (empty($theme)) {
            $theme = theme_config::load('adaptable');
        }

        if (empty($theme->settings->$setting)) {
            return false;
        } else if (!$format) {
            return $theme->settings->$setting;
        } else if ($format === 'format_text') {
            return format_text($theme->settings->$setting, FORMAT_PLAIN);
        } else if ($format === 'format_html') {
            return format_text($theme->settings->$setting, FORMAT_HTML, array('trusted' => true));
        } else {
            return format_string($theme->settings->$setting);
        }
    }

    /**
     * Returns user profile menu
     */
    public function user_profile_menu() {
         global $CFG, $COURSE, $PAGE;
         $retval = '';

         // False or theme setting name to first array param (not all links have settings).
         // False or Moodle version number to second param (only some links check version).
         // URL for link in third param.
         // Link text in fourth parameter.
         // Icon fa-icon in fifth param.
         $usermenuitems = array(
            array('enablemy', false, $CFG->wwwroot.'/my', get_string('myhome'), 'fa-dashboard'),
            array('enableprofile', false, $CFG->wwwroot.'/user/profile.php', get_string('viewprofile'), 'fa-user'),
            array('enableeditprofile', false, $CFG->wwwroot.'/user/edit.php', get_string('editmyprofile'), 'fa-cog'),
            array('enableprivatefiles', false, $CFG->wwwroot.'/user/files.php', get_string('privatefiles', 'block_private_files'),
                    'fa-file'),
            array('enablegrades', false, $CFG->wwwroot.'/grade/report/overview/index.php', get_string('grades'), 'fa-list-alt'),
            array('enablebadges', false, $CFG->wwwroot.'/badges/mybadges.php', get_string('badges'), 'fa-certificate'),
            array('enablepref', '2015051100', $CFG->wwwroot.'/user/preferences.php', get_string('preferences'), 'fa-cog'),
            array('enablenote', false, $CFG->wwwroot.'/message/edit.php', get_string('notifications'), 'fa-paper-plane'),
            array('enableblog', false, $CFG->wwwroot.'/blog/index.php', get_string('enableblog', 'theme_adaptable'), 'fa-rss'),
            array('enableposts', false, $CFG->wwwroot.'/mod/forum/user.php', get_string('enableposts', 'theme_adaptable'),
                    'fa-commenting'),
            array('enablefeed', false, $CFG->wwwroot.'/report/myfeedback/index.php', get_string('enablefeed',
                    'theme_adaptable'), 'fa-bullhorn'),
            array('enablecalendar', false, $CFG->wwwroot.'/calendar/view.php', get_string('pluginname', 'block_calendar_month'),
                    'fa-calendar'));

            $returnurl = $this->get_current_page_url(true);
            $context = context_course::instance($COURSE->id);

        if (($CFG->version > 2016120500) &&
            (!is_role_switched($COURSE->id)) && (has_capability('moodle/role:switchroles', $context))) {
                // TBR $returnurl = str_replace().
            $url = $CFG->wwwroot.'/course/switchrole.php?id='.$COURSE->id.'&switchrole=-1&returnurl='.$returnurl;
            $usermenuitems[] = array(false, false, $url, get_string('switchroleto'), 'fa-user-o');
        }

        if (($CFG->version > 2016120500) && (is_role_switched($COURSE->id))) {
                $url = $CFG->wwwroot.'/course/switchrole.php?id='.$COURSE->id.'&sesskey='.sesskey().
                        '&switchrole=0&returnurl='.$returnurl;
                $usermenuitems[] = array(false, false, $url, get_string('switchrolereturn'), 'fa-user-o');
        }

            $usermenuitems[] = array(false, false, $CFG->wwwroot.'/login/logout.php?sesskey='.sesskey(),
                                    get_string('logout'), 'fa-sign-out');

        for ($i = 0; $i < count($usermenuitems); $i++) {
            $additem = true;

            // If theme setting is specified in array but not enabled in theme settings do not add to menu.
            $usermenuitem = $usermenuitems[$i][0];
            if (empty($PAGE->theme->settings->$usermenuitem) && $usermenuitems[$i][0]) {
                $additem = false;
            }

            // If item requires version number and moodle is below that version to not add to menu.
            if ($usermenuitems[$i][1] && $CFG->version < $usermenuitems[$i][1]) {
                $additem = false;
            }

            if ($additem) {
                $retval .= '<li><a href="' . $usermenuitems[$i][2] . '" title="' . $usermenuitems[$i][3] . '">';
                $retval .= '<i class="fa ' . $usermenuitems[$i][4] . '"></i>' . $usermenuitems[$i][3] . '</a></li>';
            }
        }
        return $retval;
    }

    /**
     * Returns current url minus the value of $CFG->wwwroot
     * Should be replaced with inbuilt Moodle function if one can be found
     */
    public function get_current_page_url($stripwwwroot = false) {
        global $CFG;
        $pageurl = 'http';

        if ( isset( $_SERVER["HTTPS"] ) && strtolower( $_SERVER["HTTPS"] ) == "on" ) {
            $pageurl .= "s";
        }

        $pageurl .= "://";

        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageurl .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
        } else {
            $pageurl .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        }

        if ($stripwwwroot) {
            $pageurl = str_replace($CFG->wwwroot, '', $pageurl);
        }
        return $pageurl;
    }

    /**
     * Returns the user menu
     *
     * @param string $user = null
     * @param string $withlinks = null
     * @return the user menu
     */
    public function user_menu($user = null, $withlinks = null) {
        global $CFG;
        $usermenu = new custom_menu('', current_language());
        return $this->render_user_menu($usermenu);
    }

    /**
     * Returns list of alert messages for the user
     *
     * @return string
     */
    public function get_alert_messages() {
        global $PAGE, $CFG, $COURSE;
        $alerts = '';

        $alertcount = $PAGE->theme->settings->alertcount;

        if (core\session\manager::is_loggedinas()) {
            $alertindex = $alertcount + 1;
            $alertkey = "undismissable";
            $logininfo = $this->login_info();
            $logininfo = str_replace('<div class="logininfo">', '', $logininfo);
            $logininfo = str_replace('</div>', '', $logininfo);
            $alerts = $this->get_alert_message($logininfo, 'warning', $alertindex, $alertkey) . $alerts;
        }

        if (empty($PAGE->theme->settings->enablealerts)) {
            return $alerts;
        }

        for ($i = 1; $i <= $alertcount; $i++) {
            $enablealert = 'enablealert' . $i;
            $alerttext = 'alerttext' . $i;
            $alertsession = 'alert' . $i;

            if (isset($PAGE->theme->settings->$enablealert)) {
                $enablealert = $PAGE->theme->settings->$enablealert;
            } else {
                $enablealert = false;
            }

            if (isset($PAGE->theme->settings->$alerttext)) {
                $alerttext = $PAGE->theme->settings->$alerttext;
            } else {
                $alerttext = '';
            }

            if ($enablealert && !empty($alerttext)) {
                $alertprofilefield = 'alertprofilefield' . $i;
                $profilevals = array('', '');

                if (!empty($PAGE->theme->settings->$alertprofilefield)) {
                    $profilevals = explode('=', $PAGE->theme->settings->$alertprofilefield);
                }

                if (!empty($PAGE->theme->settings->enablealertstriptags)) {
                    $alerttext = strip_tags($alerttext);
                }

                $alerttype = 'alerttype' . $i;
                $alertaccess = 'alertaccess' . $i;
                $alertkey = 'alertkey' . $i;

                $alerttype = $PAGE->theme->settings->$alerttype;
                $alertaccess = $PAGE->theme->settings->$alertaccess;
                $alertkey = $PAGE->theme->settings->$alertkey;

                if ($this->get_alert_access($alertaccess, $profilevals[0], $profilevals[1], $alertsession)) {
                    $alerts .= $this->get_alert_message($alerttext, $alerttype, $i, $alertkey);
                }
            }
        }

        if (($CFG->version > 2016120500) && (is_role_switched($COURSE->id))) {
            $alertindex = $alertcount + 1;
            $alertkey = "undismissable";

            $returnurl = $this->get_current_page_url(true);
            $url = $CFG->wwwroot.'/course/switchrole.php?id='.$COURSE->id.'&sesskey='.sesskey().
                    '&switchrole=0&returnurl='.$returnurl;

            $message = get_string('actingasrole', 'theme_adaptable') . '. ';
            $message .= '<a href="' . $url . '">' . get_string('switchrolereturn') . '</a>';
            $alerts = $this->get_alert_message($message, 'warning', $alertindex, 'logedinas') . $alerts;
        }

        return $alerts;
    }

    /**
     * Returns formatted alert message
     *
     * @param string $text message text
     * @param string $type alert type
     * @param int $alertindex
     * @param int $alertkey
     */
    public function get_alert_message($text, $type, $alertindex, $alertkey) {
        if ($alertkey == '' || theme_adaptable_get_alertkey($alertindex) == $alertkey) {
            return '';
        }

        global $PAGE;

        $retval = '<div class="customalert alert alert-dismissable adaptable-alert-' . $type . ' fade in">';
        $retval .= '<button type="button" class="close" data-dismiss="alert" aria-label="Close" data-alertkey="' . $alertkey.
                    '" data-alertindex="' . $alertindex . '">';

        if ($alertkey != 'undismissable') {
            $retval .= '<span aria-hidden="true">&times;</span>';
        }

        $retval .= '</button>';
        $retval .= '<i class="fa fa-' . $this->alert_icon($type) . ' fa-lg"></i>&nbsp;';
        $retval .= $text;
        $retval .= '</div>';
        return $retval;
    }

    /**
     * Displays notices to alert teachers of problems with course such as being hidden
     */
    public function get_course_alerts() {
        global $PAGE, $CFG, $COURSE;
        $retval = '';
        $warninghidden = $PAGE->theme->settings->alerthiddencourse;

        if ($warninghidden != 'disabled') {
            if ($this->page->course->visible == 0) {
                $alerttext = get_string('alerthiddencoursetext-1', 'theme_adaptable')
                    . '<a href="' . $CFG->wwwroot . '/course/edit.php?id=' . $COURSE->id . '">'
                    . get_string('alerthiddencoursetext-2', 'theme_adaptable') . '</a>';

                $alerttype = $warninghidden;
                $alertindex = 'hiddencoursealert-' . $COURSE->id;
                $alertkey = $alertindex; // These keys are never reset so can use fixed value.

                $retval = $this->get_alert_message($alerttext, $alerttype, $alertindex, $alertkey);
            }
        }

        return $retval;
    }

    /**
     * Checks the users access to alerts
     * @param string $access the kind of access rule applied
     * @param string $profilefield the custom profile filed to check
     * @param string $profilevalue the expected value to be found in users profile
     * @param string $alertsession a token to be used to store access in session
     * @return boolean
     */
    public function get_alert_access($access, $profilefield, $profilevalue, $alertsession) {
        $retval = false;
        switch ($access) {
            case "global":
                $retval = true;
            break;
            case "user":
                if (isloggedin()) {
                    $retval = true;
                }
            break;
            case "admin":
                if (is_siteadmin()) {
                    $retval = true;
                }
            break;
            case "profile":
                /* Check if user is logged in and then check menu access for profile field. */
                if ( (isloggedin()) && ($this->check_menu_access($profilefield, $profilevalue, $alertsession)) ) {
                    $retval = true;
                }
            break;
        }
        return $retval;
    }

    /**
     * Returns FA icon depending on the type of alert selected
     *
     * @param string $alertclassglobal     *
     * @return string
     */
    public function alert_icon($alertclassglobal) {
        global $PAGE;
        switch ($alertclassglobal) {
            case "success":
                $alerticonglobal = $PAGE->theme->settings->alerticonsuccess;
                break;
            case "info":
                $alerticonglobal = $PAGE->theme->settings->alerticoninfo;
                break;
            case "warning":
                $alerticonglobal = $PAGE->theme->settings->alerticonwarning;
                break;
        }
        return $alerticonglobal;
    }

    /**
     * Returns html to render Development version alert message in the header
     *
     * @return string
     */
    public function get_dev_alert() {
        global $CFG;
        $output = '';

        // Development version.
        if (get_config('theme_adaptable', 'version') < '2017053000') {
                $output .= '<div id="beta"><h3>';
                $output .= get_string('beta', 'theme_adaptable');
                $output .= '</h3></div>';
        }

        // Deprecated moodle version (3.4.2 or older).
        if ($CFG->version < 2016120500) {
                $output .= '<div id="beta"><center><h3>';
                $output .= get_string('deprecated', 'theme_adaptable');
                $output .= '</h3></center></div>';
        }

        return $output;
    }

    /**
     * Returns Google Analytics code if analytics are enabled
     *
     * @return string
     */
    public function get_analytics() {
        global $PAGE;
        $analytics = '';
        $analyticscount = $PAGE->theme->settings->enableanalytics;
        $anonymize = true;

        // Anonymize IP.
        if (($PAGE->theme->settings->anonymizega = 1) || (empty($PAGE->theme->settings->anonymizega))) {
             $anonymize = true;
        } else {
             $anonymize = false;
        }

        // Load settings.
        if (isset($PAGE->theme->settings->enableanalytics)) {
            for ($i = 1; $i <= $analyticscount; $i++) {
                $analyticstext = 'analyticstext' . $i;
                $analyticsprofilefield = 'analyticsprofilefield' . $i;
                $analyticssession = 'analytics' . $i;
                $access = true;

                if (!empty($PAGE->theme->settings->$analyticsprofilefield)) {
                    $profilevals = explode('=', $PAGE->theme->settings->$analyticsprofilefield);
                    $profilefield = $profilevals[0];
                    $profilevalue = $profilevals[1];
                    if (!$this->check_menu_access($profilefield, $profilevalue, $analyticssession)) {
                        $access = false;
                    }
                }

                if (!empty($PAGE->theme->settings->$analyticstext) && $access) {
                    // The closing tag of PHP heredoc doesn't like being indented so do not meddle with indentation of 'EOT;' below!
                    $analytics .= <<<EOT

                    <script type="text/javascript">
                        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                        (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                        m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
                        })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

                        ga('create', '$analyticstext', 'auto');
                        ga('send', 'pageview');
                        ga('set', 'anonymizeIp', $anonymize);
                    </script>
EOT;
                }
            }
        }
        return $analytics;
    }

    /**
     * Returns Piwik code if enabled
     *
     * @copyright  2016 COMETE-UPO (Universit\E9 Paris Ouest)
     *
     * @return string
     */
    public function get_piwik() {
        global $CFG, $DB, $PAGE, $COURSE, $SITE;

        $enabled = $PAGE->theme->settings->piwikenabled;
        $imagetrack = $PAGE->theme->settings->piwikimagetrack;
        $siteurl = $PAGE->theme->settings->piwiksiteurl;
        $siteid = $PAGE->theme->settings->piwiksiteid;
        $trackadmin = $PAGE->theme->settings->piwiktrackadmin;

        $enabled = $PAGE->theme->settings->piwikenabled;
        $imagetrack = $PAGE->theme->settings->piwikimagetrack;
        $siteurl = $PAGE->theme->settings->piwiksiteurl;
        $siteid = $PAGE->theme->settings->piwiksiteid;
        $trackadmin = $PAGE->theme->settings->piwiktrackadmin;

        $analytics = '';
        if ($enabled && !empty($siteurl) && !empty($siteid) && (!is_siteadmin() || $trackadmin)) {
            if ($imagetrack) {
                $addition = '<noscript><p>
                            <img src="//'.$siteurl.'/piwik.php?idsite='.$siteid.' style="border:0;" alt="" /></p></noscript>';
            } else {
                $addition = '';
            }
            // Cleanurl.
            $pageinfo = get_context_info_array($PAGE->context->id);
            $trackurl = '';
            // Adds course category name.
            if (isset($pageinfo[1]->category)) {
                if ($category = $DB->get_record('course_categories', array('id' => $pageinfo[1]->category))) {
                    $cats = explode("/", $category->path);
                    foreach (array_filter($cats) as $cat) {
                        if ($categorydepth = $DB->get_record("course_categories", array("id" => $cat))) {
                            $trackurl .= $categorydepth->name.'/';
                        }
                    }
                }
            }
            // Adds course full name.
            if (isset($pageinfo[1]->fullname)) {
                if (isset($pageinfo[2]->name)) {
                    $trackurl .= $pageinfo[1]->fullname.'/';
                } else if ($PAGE->user_is_editing()) {
                    $trackurl .= $pageinfo[1]->fullname.'/'.get_string('edit', 'local_analytics');
                } else {
                    $trackurl .= $pageinfo[1]->fullname.'/'.get_string('view', 'local_analytics');
                }
            }
            // Adds activity name.
            if (isset($pageinfo[2]->name)) {
                $trackurl .= $pageinfo[2]->modname.'/'.$pageinfo[2]->name;
            }
            $trackurl = '"'.str_replace('"', '\"', $trackurl).'"';
            // Here we go.
            $analytics .= '<!-- Start Piwik Code -->'."\n".
                '<script type="text/javascript">'."\n".
                '    var _paq = _paq || [];'."\n".
                '    _paq.push(["setDocumentTitle", '.$trackurl.']);'."\n".
                '    _paq.push(["trackPageView"]);'."\n".
                '    _paq.push(["enableLinkTracking"]);'."\n".
                '    (function() {'."\n".
                '      var u="//'.$siteurl.'/";'."\n".
                '      _paq.push(["setTrackerUrl", u+"piwik.php"]);'."\n".
                '      _paq.push(["setSiteId", '.$siteid.']);'."\n".
                '      var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0];'."\n".
                '    g.type="text/javascript"; g.async=true; g.defer=true; g.src=u+"piwik.js";s.parentNode.insertBefore(g,s);'."\n".
                '    })();'."\n".
                '</script>'.$addition."\n".
                '<!-- End Piwik Code -->'."\n".
            '';
        }
        return $analytics;
    }

    /**
     * Returns all tracking methods (Analytics and Piwik)
     *
     * @return string
     */
    public function get_all_tracking_methods() {
        $analytics = '';
        $analytics .= $this->get_analytics();
        $analytics .= $this->get_piwik();
        return $analytics;
    }

    /**
     * Returns HTML to display a "Turn editing on/off" button in a form.
     *
     * @param moodle_url $url The URL + params to send through when clicking the button
     * @return string HTML the button
     * Written by G J Barnard
     */
    public function edit_button(moodle_url $url) {
        $url->param('sesskey', sesskey());
        if ($this->page->user_is_editing()) {
            $url->param('edit', 'off');
            $btn = 'btn-danger';
            $title = get_string('turneditingoff');
            $icon = 'fa-power-off';
        } else {
            $url->param('edit', 'on');
            $btn = 'btn-success';
            $title = get_string('turneditingon');
            $icon = 'fa-edit';
        }
        return html_writer::tag('a', html_writer::start_tag('i', array('class' => $icon . ' fa fa-fw')) .
            html_writer::end_tag('i') . $title, array('href' => $url, 'class' => 'btn ' . $btn, 'title' => $title));
    }

    /**
     * Returns the upper user menu
     *
     * @param custom_menu $menu
     * @return string
     */
    protected function render_user_menu(custom_menu $menu) {
        global $CFG, $DB, $PAGE, $OUTPUT;

        $addlangmenu = true;
        $addmessagemenu = true;
        $messagecount = 0;

        // Let's add the Message item in the left.
        if (!isloggedin() || isguestuser()) {
            $addmessagemenu = false;
        }

        if (!$CFG->messaging || !$PAGE->theme->settings->enablemessagemenu) {
            $addmessagemenu = false;
        } else {
            // Check whether or not the "popup" message output is enabled.
            // This is after we check if messaging is enabled to possibly save a DB query.
            $popup = $DB->get_record('message_processors', array('name' => 'popup'));
            if (!$popup) {
                $addmessagemenu = false;
            }
        }

        if ($addmessagemenu) {
            // In Moodle 3.1 or older we display a menu with a count badge.
            if ($CFG->version < 2016120500) {
                // First, go to count the number of unread messages.
                 $messages = $this->get_user_messages();
                 $messagecount = count($messages);

                // Edit by Matthew Anguige, only display unread popover when unread messages are waiting.
                if ($messagecount > 0) {
                    // If got some message then we add the badge with the pending messages number and no link to the messages page.
                    $messagemenu = $menu->add('<i class="fa fa-envelope"> </i>' . get_string('messages', 'message') .' '.
                    '<span class="badge">' . $messagecount . '</span>', new moodle_url('/message/index.php'),
                            get_string('messages', 'message'), 9999);
                } else {
                    // If no pending messages we add only a link to the messages page in the menu.
                    $messagemenu = $menu->add('<i class="fa fa-envelope"> </i>' . get_string('messages', 'message'),
                                              new moodle_url('/message/index.php'), get_string('messages', 'message'), 9999);
                }

                // We display the messages in a pop-up.
                foreach ($messages as $message) {
                    if (!isset($message->from) || !isset($message->from->id) || !isset($message->from->firstname)) {
                        continue;
                    }
                    // Following if to be removed once we are happy with check above correctly limits messages.
                    if (!isset($message->from)) {
                        $url = $OUTPUT->img_url('u/f2');
                        $attributes = array(
                            'src' => $url
                        );
                        $senderpicture = html_writer::empty_tag('img', $attributes);
                    } else {
                        $senderpicture = new user_picture($message->from);
                        $senderpicture->link = false;
                        $senderpicture = $this->render($senderpicture);
                    }

                    // Let's go to create the message to show in the screen.
                    $messagecontent = $senderpicture;
                    $messagecontent .= html_writer::start_tag('span', array('class' => 'msg-body'));
                    $messagecontent .= html_writer::start_tag('span', array('class' => 'msg-title'));
                    $messagecontent .= html_writer::tag('span', $message->from->firstname . ': ', array('class' => 'msg-sender'));
                    $messagecontent .= $message->text;
                    $messagecontent .= html_writer::end_tag('span');
                    $messagecontent .= html_writer::start_tag('span', array('class' => 'msg-time'));
                    $messagecontent .= html_writer::tag('i', '', array('class' => 'icon-time'));
                    $messagecontent .= html_writer::tag('span', $message->date);
                    $messagecontent .= html_writer::end_tag('span');

                    $messagemenu->add($messagecontent, new moodle_url('/message/index.php', array('user1' => $USER->id,
                        'user2' => $message->from->id)));
                }
            }
        }

        // Let's go to create the lang menu if available.
        $langs = get_string_manager()->get_list_of_translations();
        if (count($langs) < 2 || empty($CFG->langmenu) || ($this->page->course != SITEID and !empty($this->page->course->lang))) {
            $addlangmenu = false;
        }

        // And finally let's go to add the custom usermenus.
        $content = html_writer::start_tag('ul', array('class' => 'usermenu2 nav navbar-nav navbar-right'));
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }

        return $content.html_writer::end_tag('ul');
    }

    /**
     * Returns formats messages in the header with user profile images
     *
     * @return array
     */
    protected function process_user_messages() {
        $messagelist = array();
        foreach ($usermessages as $message) {
            $cleanmsg = new stdClass();
            $cleanmsg->from = fullname($message);
            $cleanmsg->msguserid = $message->id;

            $userpicture = new user_picture($message);
            $userpicture->link = false;
            $picture = $this->render($userpicture);

            $cleanmsg->text = $picture . ' ' . $cleanmsg->text;

            $messagelist[] = $cleanmsg;
        }

        return $messagelist;
    }

    /**
     * Get list of user messages if there are any to process
     *
     * @return array
     */
    protected function get_user_messages() {
        global $PAGE, $USER, $DB, $CFG;

        $messagelist = array();
        $newmessages = 0;

        if ($CFG->version < 2016120500) {
            // Moodle 3.1 or older.
            $newmessagesql = "SELECT id, smallmessage, useridfrom, useridto, timecreated, fullmessageformat, notification
                              FROM {message}
                              WHERE useridto = :userid
                              AND notification <> 1";

            if ($PAGE->theme->settings->filteradminmessages) {
                $newmessagesql .= " AND useridfrom > 2";

                $newmessages = $DB->get_records_sql($newmessagesql, array('userid' => $USER->id));
            }

            return $messagelist;
        }
    }

    /**
     * Process user messages
     *
     * @param array $message
     * @return array
     */
    protected function process_message($message) {
        global $DB, $USER;

        $messagecontent = new stdClass();
        if ($message->notification || $message->useridfrom < 1) {
            $messagecontent->text = $message->smallmessage;
            $messagecontent->type = 'notification';

            if (empty($message->contexturl)) {
                $messagecontent->url = new moodle_url('/message/index.php',
                                        array('user1' => $USER->id, 'viewing' => 'recentnotifications'));
            } else {
                $messagecontent->url = new moodle_url($message->contexturl);
            }

        } else {
            $messagecontent->type = 'message';
            if ($message->fullmessageformat == FORMAT_HTML) {
                $message->smallmessage = html_to_text($message->smallmessage);
            }
            if (strlen($message->smallmessage) > 18) {
                $messagecontent->text = substr($message->smallmessage, 0, 15) . '...';
            } else {
                $messagecontent->text = $message->smallmessage;
            }
            $messagecontent->from = $DB->get_record('user', array('id' => $message->useridfrom));
            $messagecontent->url = new moodle_url('/message/index.php',
                                    array('user1' => $USER->id, 'user2' => $message->useridfrom));
        }
        $messagecontent->date = userdate($message->timecreated, get_string('strftimetime', 'langconfig'));
        $messagecontent->unread = empty($message->timeread);
        return $messagecontent;
    }

    /**
     * This renders a notification message.
     * Uses bootstrap compatible html.
     *
     * @param string $message
     * @param string $classes for css
     */
    public function notification($message, $classes = 'notifyproblem') {
        $message = clean_text($message);
        $type = '';

        if ($classes == 'notifyproblem') {
            $type = 'alert alert-error';
        }
        if ($classes == 'notifysuccess') {
            $type = 'alert alert-success';
        }
        if ($classes == 'notifymessage') {
            $type = 'alert alert-info';
        }
        if ($classes == 'redirectmessage') {
            $type = 'alert alert-block alert-info';
        }
        return '<div class="' . $type . '">' . $message . '</div>';
    }

    /**
     * Returns html to render socialicons
     *
     * @return string
     */
    public function socialicons() {
        global $CFG, $PAGE;

        if (!isset($PAGE->theme->settings->socialiconlist)) {
            return '';
        }

        $target = '_blank';
        if (isset($PAGE->theme->settings->socialtarget)) {
            $target = $PAGE->theme->settings->socialtarget;
        }

        $retval = '<div class="socialbox">';

        $socialiconlist = $PAGE->theme->settings->socialiconlist;
        $lines = explode("\n", $socialiconlist);

        foreach ($lines as $line) {
            if (strstr($line, '|')) {
                $fields = explode('|', $line);
                $val = '<a';
                $val .= ' target="' . $target;
                $val .= '" title="' . $fields[1];
                $val .= '" href="' . $fields[0] . '">';
                $val .= '<i class="fa ' . $fields[2] . '"></i>';
                $val .= '</a>';
                $retval .= $val;
            }
        }

        $retval .= '</div>';
        return $retval;
    }

    /**
     * Returns html to render news ticker
     *
     * @return string
     */
    public function get_news_ticker() {
        global $PAGE, $OUTPUT;
        $retval = '';

        if (!isset($PAGE->theme->settings->enabletickermy)) {
            $PAGE->theme->settings->enabletickermy = 0;
        }

        // Display ticker if possible.
        if ((!empty($PAGE->theme->settings->enableticker) &&
        $PAGE->theme->settings->enableticker &&
        $PAGE->bodyid == "page-site-index") ||
        ($PAGE->theme->settings->enabletickermy && $PAGE->bodyid == "page-my-index")) {
            $msg = '';
            $tickercount = $PAGE->theme->settings->newstickercount;

            for ($i = 1; $i <= $tickercount; $i++) {
                $textfield = 'tickertext' . $i;
                $profilefield = 'tickertext' . $i . 'profilefield';

                format_text($textfield, FORMAT_HTML);

                $access = true;

                if (!empty($PAGE->theme->settings->$profilefield)) {
                    $profilevals = explode('=', $PAGE->theme->settings->$profilefield);
                    if (!$this->check_menu_access($profilevals[0], $profilevals[1], $textfield)) {
                        $access = false;
                    }
                }

                if ($access) {
                    $msg .= format_text($PAGE->theme->settings->$textfield, FORMAT_HTML, array('trusted' => true));
                }
            }

            $msg = preg_replace('#\<[\/]{0,1}(li|ul|div|pre|blockquote)\>#', '', $msg);
            if ($msg == '') {
                $msg = '<p>' . get_string('tickerdefault', 'theme_adaptable') . '</p>';
            }

            $retval .= '<div id="ticker-wrap" class="clearfix container">';
            $retval .= '<div class="pull-left" id="ticker-announce">';
            $retval .= get_string('ticker', 'theme_adaptable');
            $retval .= '</div>';
            $retval .= '<ul id="ticker">';
            $retval .= $msg;
            $retval .= '</ul>';
            $retval .= '</div>';
        }

        return $retval;
    }


    /**
     * Renders block regions on front page (or any other page
     * if specifying a different value for $settingsname). Used for various block region rendering.
     *
     * @param   string $settingsname  Setting name to retrieve from theme settings containing actual layout (e.g. 4-4-4-4)
     * @param   string $classnamebeginswith  Used when building the blockname to retrieve for display
     * @param   string $customrowsetting  If $settingsname value set to 'customrowsetting', then set this to
     *                 the layout required to display a one row layout.
     *                 When using this, ensure the appropriate number of block regions are defined in config.php.
     *                 E.g. if $classnamebeginswith = 'my-block' and $customrowsetting = '4-4-0-0', 2 regions called
     *                 'my-block-a' and 'my-block-a' are expected to exist.
     * @return  string HTML output
     */
    public function get_block_regions($settingsname = 'blocklayoutlayoutrow', $classnamebeginswith = 'frnt-market-'
        , $customrowsetting = null) {
        global $PAGE, $OUTPUT, $USER, $COURSE;
        $fields = array();
        $retval = '';
        $blockcount = 0;
        $style = '';
        $adminediting = false;

        // Check if user has capability to edit block on homepage.  This is used as part of checking if
        // blocks should display the dotted borders and labels for editing. (Issue #809).
        $context = context_course::instance($COURSE->id);

        // Check if front page and if has capability to edit blocks.  The $pageallowed variable will store
        // the correct state of whether user can edit that page.
        $caneditblock = has_capability('moodle/block:edit', $context);
        if ( ($PAGE->pagelayout == "frontpage") && ($caneditblock !== true) ) {
            $pageallowed = false;
        } else {
            $pageallowed = true;
        }

        if ( (isset($USER->editing) && $USER->editing == 1) && ($pageallowed == true) ) {
            $style = '" style="display: block; background: #EEEEEE; min-height: 50px; border: 2px dashed #BFBDBD; margin-top: 5px';
            $adminediting = true;
        }

        if ($settingsname == 'customrowsetting') {
            $fields[] = $customrowsetting;
        } else {
            for ($i = 1; $i <= 8; $i++) {
                $marketrow = $settingsname . $i;

                // Need to check if the setting exists as this function is now
                // called for variable row numbers in block regions (e.g. course page
                // which is a single row of block regions).

                if (isset($PAGE->theme->settings->$marketrow)) {
                    $marketrow = $PAGE->theme->settings->$marketrow;
                } else {
                    $marketrow = '0-0-0-0';
                }

                if ($marketrow != '0-0-0-0') {
                    $fields[] = $marketrow;
                }
            }
        }

        foreach ($fields as $field) {
            $retval .= '<div class="row" style="margin-left: 0px;">';
            $vals = explode('-', $field);
            foreach ($vals as $val) {
                if ($val > 0) {
                    $retval .= '<div class="span' . $val . $style . '">';

                    // Moodle does not seem to like numbers in region names so using letter instead.
                    $blockcount ++;
                    $block = $classnamebeginswith. chr(96 + $blockcount);

                    if ($adminediting) {
                        $retval .= '<span style="padding-left: 10px;"> ' . get_string('region-' . $block, 'theme_adaptable') .
                                '' . '</span>';
                    }

                    $retval .= $OUTPUT->blocks($block, 'block-region-front');
                    $retval .= '</div>';
                }
            }
            $retval .= '</div>';
        }
        return $retval;
    }

    /**
     * Renders block regions for potentially hidden blocks.  For example, 4-4-4-4 to 6-6-0-0
     * would mean the last two blocks get inadvertently hidden. This function can recover and
     * display those blocks.  An override option also available to display blocks for the region, regardless.
     *
     * @param array  $blocksarray Settings names containing the actual layout(s) (i.e. 4-4-4-4)
     * @param array  $classes Used when building the blockname to retrieve for display
     * @param bool   $displayall An override setting to simply display all blocks from the region
     * @return string HTML output
     */
    public function get_missing_block_regions($blocksarray, $classes = array(), $displayall = false) {
        global $PAGE, $OUTPUT, $USER;
        $retval = '';
        $style = '';
        $adminediting = false;

        if (isset($USER->editing) && $USER->editing == 1) {
            $adminediting = true;
        }

        if (!empty($blocksarray)) {

            $classes = (array)$classes;
            $retval .= '<aside class="' . join(' ', $classes) . '">';

            foreach ($blocksarray as $block) {

                // Do this for up to 8 rows (allows for expansion.  Be careful
                // of losing blocks if this value changes from a high to low number!).
                for ($i = 1; $i <= 8; $i++) {

                    // For each block region in a row, analyse the current layout (e.g. 6-6-0-0, 3-3-3-3).  Check if less than
                    // 4 blocks (meaning a change in settings from say 4-4-4-4 to 6-6.  Meaning missing blocks,
                    // i.e. 6-6-0-0 means the two end ones may have content that is inadvertantly lost.
                    $rowsetting = $block['settingsname'] . $i;

                    if (isset($PAGE->theme->settings->$rowsetting)) {
                        $rowvalue = $PAGE->theme->settings->$rowsetting;

                        $spannumbers = explode('-', $rowvalue);
                        $y = 0;
                        foreach ($spannumbers as $spannumber) {
                            $y++;

                            // Here's the crucial bit.  Check if span number is 0,
                            // or $displayall is true (override) and if so, print it out.
                            if ($spannumber == 0 || $displayall) {

                                $blockclass = $block['classnamebeginswith'] . chr(96 + $y);
                                $missingblock = $OUTPUT->blocks($blockclass, 'block');

                                // Check if the block actually has content to display before displaying.
                                if (strip_tags($missingblock)) {
                                    if ($adminediting) {
                                        $retval .= '<em>ORPHANED BLOCK - Originally displays in: <strong>' .
                                            get_string('region-' . $blockclass, 'theme_adaptable') .'</strong></em>';

                                    }
                                    $retval .= $missingblock;
                                }

                            }
                        } // End foreach.
                    }

                }

            }
            $retval .= '</aside>';

        }

        return $retval;
    }

    /**
     * Renders marketing blocks on front page
     *
     * @param string $layoutrow
     * @param string $settingname
     */
    public function get_marketing_blocks($layoutrow = 'marketlayoutrow', $settingname = 'market') {
        global $PAGE, $OUTPUT;
        $fields = array();
        $blockcount = 0;
        $style = '';

        $extramarketclass = $PAGE->theme->settings->frontpagemarketoption;

        $retval = '<div id="marketblocks" class="container '. $extramarketclass .'">';

        for ($i = 1; $i <= 5; $i++) {
            $marketrow = $layoutrow . $i;
            $marketrow = $PAGE->theme->settings->$marketrow;
            if ($marketrow != '0-0-0-0') {
                $fields[] = $marketrow;
            }
        }

        foreach ($fields as $field) {
            $retval .= '<div class="row-fluid marketrow">';
            $vals = explode('-', $field);
            foreach ($vals as $val) {
                if ($val > 0) {
                    $retval .= '<div class="span' . $val . ' ' . $extramarketclass . ' first">';
                    $blockcount ++;
                    $fieldname = $settingname . $blockcount;
                    if (isset($PAGE->theme->settings->$fieldname)) {
                        // Add HTML format.
                        $retval .= $OUTPUT->get_setting($fieldname, 'format_html');
                    }
                    $retval .= '</div>';
                }
            }
            $retval .= '</div>';
        }
        $retval .= '</div>';
        if ($blockcount == 0 ) {
            $retval = '';
        }
        return $retval;
    }

    /**
     * Returns footer visibility setting
     *
     */
    public function get_footer_visibility() {
        global $PAGE, $COURSE;
        $value = $PAGE->theme->settings->footerblocksplacement;

        if ($value == 1) {
            return true;
        }

        if ($value == 2 && $COURSE->id != 1) {
            return false;
        }

        if ($value == 3) {
            return false;
        }
        return true;
    }

    /**
     * Renders footer blocks
     *
     * @param string $layoutrow
     */
    public function get_footer_blocks($layoutrow = 'footerlayoutrow') {
        global $PAGE, $OUTPUT;
        $fields = array();
        $blockcount = 0;
        $style = '';

        if (!$this->get_footer_visibility()) {
            return '';
        }

        $output = '<div id="course-footer">' . $OUTPUT->course_footer() . '</div>
                <div class="container blockplace1">';

        for ($i = 1; $i <= 3; $i++) {
            $footerrow = $layoutrow . $i;
            $footerrow = $PAGE->theme->settings->$footerrow;
            if ($footerrow != '0-0-0-0') {
                $fields[] = $footerrow;
            }
        }

        foreach ($fields as $field) {
            $output .= '<div class="row-fluid">';
            $vals = explode('-', $field);
            foreach ($vals as $val) {
                if ($val > 0) {
                    $blockcount ++;
                    $footerheader = 'footer' . $blockcount . 'header';
                    $footercontent = 'footer' . $blockcount . 'content';
                    if (!empty($PAGE->theme->settings->$footercontent)) {
                        $output .= '<div class="left-col span' . $val . '">';
                        if (!empty($PAGE->theme->settings->$footerheader)) {
                            $output .= '<h3>';
                            $output .= $OUTPUT->get_setting($footerheader, 'format_text');
                            $output .= '</h3>';
                        }
                        $output .= $OUTPUT->get_setting($footercontent, 'format_html');
                        $output .= '</div>';
                    }
                }
            }
            $output .= '</div>';
        }
        $output .= '</div>';
        return $output;
    }

    /**
     * Renders frontpage slider
     *
     */
    public function get_frontpage_slider() {
        global $PAGE, $OUTPUT;
        $noslides = $PAGE->theme->settings->slidercount;
        $retval = '';

        if (!empty($PAGE->theme->settings->sliderfullscreen)) {
            $retval .= '<div class="slidewrap';
        } else {
            $retval .= '<div class="container slidewrap';
        }

        if ($PAGE->theme->settings->slideroption2 == 'slider2') {
            $retval .= " slidestyle2";
        }

        $retval .= '">
            <div id="main-slider" class="flexslider">
            <ul class="slides">';

        for ($i = 1; $i <= $noslides; $i++) {
            $sliderimage = 'p' . $i;
            $sliderurl = 'p' . $i . 'url';

            if (!empty($PAGE->theme->settings->$sliderimage)) {
                $slidercaption = 'p' . $i .'cap';
            }

            $closelink = '';
            if (!empty($PAGE->theme->settings->$sliderimage)) {
                $retval .= '<li>';

                if (!empty($PAGE->theme->settings->$sliderurl)) {
                    $retval .= '<a href="' . $PAGE->theme->settings->$sliderurl . '">';
                    $closelink = '</a>';
                }

                $retval .= '<img src="' . $PAGE->theme->setting_file_url($sliderimage, $sliderimage)
                    . '" alt="' . $sliderimage . '"/>';

                if (!empty($PAGE->theme->settings->$slidercaption)) {
                    $retval .= '<div class="flex-caption">';
                    $retval .= $OUTPUT->get_setting($slidercaption, 'format_html');
                    $retval .= '</div>';
                }
                $retval .= $closelink . '</li>';
            }
        }
        $retval .= '</ul></div></div>';
        return $retval;
    }

    /**
     * Renders the breadcrumb navbar.
     *
     */
    public function page_navbar($addbutton = false) {
        global $PAGE;
        $retval = '';
        $hidebreadcrumbmobile = $PAGE->theme->settings->hidebreadcrumbmobile;

        // If the device is a mobile and the breadcrumb is not hidden or it is a desktop then load and show the breadcrumb.
        if (((theme_adaptable_is_mobile()) && $hidebreadcrumbmobile = 1) || theme_adaptable_is_desktop()) {
            if (!isset($PAGE->theme->settings->enabletickermy)) {
                $PAGE->theme->settings->enabletickermy = 0;
            }

            // Do not show navbar on dashboard / my home if news ticker is rendering.
            if (!($PAGE->theme->settings->enabletickermy && $PAGE->bodyid == "page-my-index")) {
                $retval = '<div id="page-navbar" class="span12">';
                if ($addbutton) {
                    $retval .= '<nav class="breadcrumb-button">' . $this->page_heading_button() . '</nav>';
                }

                $retval .= $this->navbar();
                $retval .= '</div>';
            }
        }

        return $retval;
    }

    /*
     * Render the breadcrumb
     * @param array $items
     * @param string $breadcrumbs
     *
     * return string
     */
    public function navbar() {
        global $PAGE;

        $items = $this->page->navbar->get_items();
        $breadcrumbseparator = $PAGE->theme->settings->breadcrumbseparator;

        $breadcrumbs = "";

        if (empty($items)) {
            return '';
        }

        $i = 0;

        foreach ($items as $item) {
            $item->hideicon = true;

            // Text / Icon home.
            if ($i++ == 0) {
                $breadcrumbs .= '<li>';

                if (get_config('theme_adaptable', 'enablehome') && get_config('theme_adaptable', 'enablemyhome')) {
                    $breadcrumbs = html_writer::tag('i', '', array(
                      'title' => get_string('home', 'theme_adaptable'),
                      'class' => 'fa fa-folder-open-o fa-lg'
                    )
                    );
                } else if (get_config('theme_adaptable', 'breadcrumbhome') == 'icon') {
                    $breadcrumbs .= html_writer::link(new moodle_url('/'),
                    // Adds in a title for accessibility purposes.
                    html_writer::tag('i', '', array(
                        'title' => get_string('home', 'theme_adaptable'),
                        'class' => 'fa fa-home fa-lg')
                      )
                    );
                    $breadcrumbs .= '</li>';
                } else {
                    $breadcrumbs .= html_writer::link(new moodle_url('/'), get_string('home', 'theme_adaptable'));
                    $breadcrumbs .= '</li>';
                }
                continue;
            }

            $breadcrumbs .= '<span class="separator"><i class="fa-'.$breadcrumbseparator.' fa"></i>
                             </span><li>'.$this->render($item).'</li>';

        } // End loop.

        return '<ul class="breadcrumb">'.$breadcrumbs.'</ul>';
    }


    /**
     * Returns html to render footer
     *
     * @return string
     */
    public function footer() {
        global $CFG;

        $output = $this->container_end_all(true);

        $footer = $this->opencontainers->pop('header/footer');

        // Provide some performance info if required.
        $performanceinfo = '';
        if (defined('MDL_PERF') || (!empty($CFG->perfdebug) and $CFG->perfdebug > 7)) {
            $perf = get_performance_info();

            if (defined('MDL_PERFTOFOOT') || debugging() || $CFG->perfdebug > 7) {
                $performanceinfo = theme_adaptable_performance_output($perf);
            }
        }

        $footer = str_replace($this->unique_performance_info_token, $performanceinfo, $footer);

        $footer = str_replace($this->unique_end_html_token, $this->page->requires->get_end_code(), $footer);

        $this->page->set_state(moodle_page::STATE_DONE);

        return $output . $footer;
    }

    /**
     * Returns html to render main navigation menu
     *
     * @return string
     */
    public function navigation_menu() {
        global $PAGE, $COURSE, $OUTPUT, $CFG, $USER;
        $menu = new custom_menu();
        $access = true;
        $overridelist = false;
        $overridestrings = false;
        $overridetype = 'off';
        $sessttl = 0;
        $cache = cache::make('theme_adaptable', 'userdata');

        if (!empty($PAGE->theme->settings->navbardisplayicons)) {
            $navbardisplayicons = true;
        } else {
            $navbardisplayicons = false;
        }

        if ($sessttl > 0 && time() <= $cache->get('usernavbarttl')) {
            return $cache->get('mysitesvisibility');
        }

        $usernavbar = 'excludehidden';
        if (!empty($PAGE->theme->settings->enablemysites)) {
            $mysitesvisibility = $PAGE->theme->settings->enablemysites;
        }

        $mysitesmaxlength = '30';
        if (!empty($PAGE->theme->settings->mysitesmaxlength)) {
            $mysitesmaxlength = $PAGE->theme->settings->mysitesmaxlength;
        }

        $mysitesmaxlengthhidden = $mysitesmaxlength - 3;

        if (isloggedin() && !isguestuser()) {
            if (!empty($PAGE->theme->settings->enablehome)) {
                $branchtitle = get_string('home', 'theme_adaptable');
                $branchlabel = '';
                if ($navbardisplayicons) {
                    $branchlabel .= '<i class="fa fa-home"></i>';
                }
                $branchlabel .= ' ' . $branchtitle;

                if (!empty($PAGE->theme->settings->enablehomeredirect)) {
                    $branchurl   = new moodle_url('/?redirect=0');
                } else {
                    $branchurl   = new moodle_url('/');
                }
                $branchsort  = 9998;
                $branch = $menu->add($branchlabel, $branchurl, '', $branchsort);
            }

            if (!empty($PAGE->theme->settings->enablemyhome)) {
                $branchtitle = get_string('myhome');

                $branchlabel = '';
                if ($navbardisplayicons) {
                    $branchlabel .= '<i class="fa fa-dashboard"></i> ';
                }
                $branchlabel .= ' ' . $branchtitle;
                $branchurl   = new moodle_url('/my/index.php');
                $branchsort  = 9999;
                $branch = $menu->add($branchlabel, $branchurl, '', $branchsort);
            }

            if (!empty($PAGE->theme->settings->enableevents)) {
                $branchtitle = get_string('events', 'theme_adaptable');
                $branchlabel = '';
                if ($navbardisplayicons) {
                    $branchlabel .= '<i class="fa fa-calendar"></i>';
                }
                $branchlabel .= ' ' . $branchtitle;

                $branchurl   = new moodle_url('/calendar/view.php');
                $branchsort  = 10000;
                $branch = $menu->add($branchlabel, $branchurl, '', $branchsort);
            }

            if (!empty($PAGE->theme->settings->mysitessortoverride) && $PAGE->theme->settings->mysitessortoverride != 'off'
                && !empty($PAGE->theme->settings->mysitessortoverridefield)) {

                $overridetype = $PAGE->theme->settings->mysitessortoverride;
                $overridelist = $PAGE->theme->settings->mysitessortoverridefield;

                if ($overridetype == 'profilefields' || $overridetype == 'profilefieldscohort') {
                    $overridelist = $this->get_profile_field_contents($overridelist);

                    if ($overridetype == 'profilefieldscohort') {
                        $overridelist = array_merge($this->get_cohort_enrollments(), $overridelist);
                    }
                }

                if ($PAGE->theme->settings->mysitessortoverride == 'strings') {
                    $overridelist = explode(',', $overridelist);
                }
            }

            if ($mysitesvisibility != 'disabled') {

                $showmysites = true;

                // Check custom profile field to restrict display of menu.
                if (!empty($PAGE->theme->settings->enablemysitesrestriction)) {
                    $fields = explode('=', $PAGE->theme->settings->enablemysitesrestriction);
                    $ftype = $fields[0];
                    $setvalue = $fields[1];

                    if (!$this->check_menu_access($ftype, $setvalue, 'mysitesrestriction')) {
                        $showmysites = false;
                    }

                }

                if ($showmysites) {
                    $branchtitle = get_string('mysites', 'theme_adaptable');

                    $branchlabel = '';

                    if ($navbardisplayicons) {
                        $branchlabel .= '<i class="fa fa-briefcase"></i>';
                    }
                    $branchlabel .= ' ' . $branchtitle;

                    $branchurl   = new moodle_url('/my/index.php');
                    $branchsort  = 10001;

                    $menudisplayoption = '';
                    // Check menu hover settings.
                    if (isset($PAGE->theme->settings->mysitesmenudisplay)) {
                        $menudisplayoption = $PAGE->theme->settings->mysitesmenudisplay;
                    } else {
                        $menudisplayoption = 'shortcodehover';
                    }

                    // The two variables below will control the 4 options available from the settings above for mysitesmenuhover.
                    $showshortcode = true;  // If false, then display full course name.
                    $showhover = true;

                    switch ($menudisplayoption) {
                        case 'shortcodenohover':
                            $showhover = false;
                            break;
                        case 'fullnamenohover':
                            $showshortcode = false;
                            $showhover = false;
                        case 'fullnamehover':
                            $showshortcode = false;
                           break;
                    }

                    // Calls a local method (render_mycourses) to get list of a user's current courses that they are enrolled on.
                    list($sortedcourses) = $this->render_mycourses();

                    // After finding out if there will be at least one course to display, check
                    // for the option of displaying a sub-menu arrow symbol.
                    if (!empty($PAGE->theme->settings->navbardisplaysubmenuarrow)) {
                            $branchlabel .= ' &nbsp;<i class="fa fa-caret-down"></i>';
                    }

                    // Add top level menu option here after finding out if there will be at least one course to display.  This is
                    // for the option of displaying a sub-menu arrow symbol above, if configured in the theme settings.
                    $branch = $menu->add($branchlabel, $branchurl, '', $branchsort);

                    $icon = '';

                    if ($sortedcourses) {

                        foreach ($sortedcourses as $course) {

                            $coursename = '';
                            $rawcoursename = ''; // Untrimmed course name.
                            if ($showshortcode) {
                                $coursename = mb_strimwidth(format_string($course->shortname), 0,
                                                $mysitesmaxlength, '...', 'utf-8');
                                $rawcoursename = $course->shortname;
                            } else {
                                $coursename = mb_strimwidth(format_string($course->fullname), 0,
                                                $mysitesmaxlength, '...', 'utf-8');
                                $rawcoursename = $course->fullname;
                            }

                            if ($showhover) {
                                $alttext = $course->fullname;
                            } else {
                                $alttext = '';
                            }

                            if ($course->visible) {
                                if (!$overridelist) { // Feature not in use, add to menu as normal.
                                    $branch->add($coursename,
                                        new moodle_url('/course/view.php?id='.$course->id), $alttext);
                                } else { // We want to check against array from profile field.
                                    if ((($overridetype == 'profilefields' ||
                                        $overridetype == 'profilefieldscohort') &&
                                        in_array($course->shortname, $overridelist)) ||
                                        ($overridetype == 'strings' &&
                                        $this->check_if_in_array_string($overridelist, $course->shortname))) {
                                            $icon = '';
                                            $branch->add($icon . $coursename,
                                                         new moodle_url('/course/view.php?id='.$course->id), $alttext, 100);
                                    } else { // If not in array add to sub menu item.
                                        if (!isset($parent)) {
                                            $icon = '<i class="fa fa-history"></i> ';
                                            $parent = $branch->add($icon . $trunc = rtrim(
                                                      mb_strimwidth(format_string(get_string('pastcourses', 'theme_adaptable')),
                                                      0, $mysitesmaxlengthhidden)) . '...', $this->page->url, $alttext, 1000);
                                        }
                                        $parent->add($trunc = rtrim(mb_strimwidth(format_string($rawcoursename),
                                                     0, $mysitesmaxlengthhidden)) . '...',
                                                     new moodle_url('/course/view.php?id='.$course->id),
                                                    format_string($rawcoursename));
                                    }
                                }
                            }
                        }

                        $icon = '<i class="fa fa-eye-slash"></i> ';
                        $parent = null;
                        foreach ($sortedcourses as $course) {
                            if (!$course->visible && $mysitesvisibility == 'includehidden') {
                                if (empty($parent)) {
                                    $parent = $branch->add($icon .
                                        $trunc = rtrim(mb_strimwidth(format_string(get_string('hiddencourses', 'theme_adaptable')),
                                        0, $mysitesmaxlengthhidden)) . '...', $this->page->url, '', 2000);
                                }
                                $parent->add($icon . $trunc = rtrim(mb_strimwidth(format_string($course->fullname),
                                    0, $mysitesmaxlengthhidden)) . '...',
                                    new moodle_url('/course/view.php?id='.$course->id), format_string($course->shortname));
                            }
                        }
                    } else {
                        $noenrolments = get_string('noenrolments', 'theme_adaptable');
                        $branch->add('<em>'.$noenrolments.'</em>', new moodle_url('/'), $noenrolments);
                    }

                }
            }

            if (!empty($PAGE->theme->settings->enablethiscourse)) {
                if (ISSET($COURSE->id) && $COURSE->id > 1) {

                    $branchtitle = get_string('thiscourse', 'theme_adaptable');

                    $branchlabel = '';
                    if ($navbardisplayicons) {
                        $branchlabel .= '<i class="fa fa-sitemap"></i><span class="menutitle">';
                    }
                    $branchlabel .= $branchtitle . '</span>';

                    $data = theme_adaptable_get_course_activities();

                    // Check the option of displaying a sub-menu arrow symbol.
                    if (!empty($PAGE->theme->settings->navbardisplaysubmenuarrow)) {
                        $branchlabel .= ' &nbsp;<i class="fa fa-caret-down"></i>';
                    }

                    $branchurl = $this->page->url;
                    $branch = $menu->add($branchlabel, $branchurl, '', 10002);

                    // Display Participants.
                    if ($PAGE->theme->settings->displayparticipants) {
                        $branchtitle = get_string('people', 'theme_adaptable');
                        $branchlabel = '<i class="fa fa-users"></i>'.$branchtitle;
                        $branchurl = new moodle_url('/user/index.php', array('id' => $PAGE->course->id));
                        $branch->add($branchlabel, $branchurl, '', 100003);
                    }

                    // Display Grades.
                    if ($PAGE->theme->settings->displaygrades) {
                        $branchtitle = get_string('grades');
                        $branchlabel = $OUTPUT->pix_icon('i/grades', '', '', array('class' => 'icon')).$branchtitle;
                        $branchurl = new moodle_url('/grade/report/index.php', array('id' => $PAGE->course->id));
                        $branch->add($branchlabel, $branchurl, '', 100004);
                    }

                    // Display activities.
                    foreach ($data as $modname => $modfullname) {
                        if ($modname === 'resources') {
                            $icon = $OUTPUT->pix_icon('icon', '', 'mod_page', array('class' => 'icon'));
                            $branch->add($icon.$modfullname, new moodle_url('/course/resources.php',
                                         array('id' => $PAGE->course->id)));
                        } else {
                            $icon = $OUTPUT->pix_icon('icon', '', $modname, array('class' => 'icon'));
                            $branch->add($icon.$modfullname, new moodle_url('/mod/'.$modname.'/index.php',
                                         array('id' => $PAGE->course->id)));
                        }
                    }
                }
            }
        }

        if ($navbardisplayicons) {
            $helpicon = '<i class="fa fa-life-ring"></i>';
        } else {
            $helpicon = '';
        }

        if (!empty($PAGE->theme->settings->enablehelp)) {
            $access = true;

            if (!empty($PAGE->theme->settings->helpprofilefield)) {
                $fields = explode('=', $PAGE->theme->settings->helpprofilefield);
                $ftype = $fields[0];
                $setvalue = $fields[1];
                if (!$this->check_menu_access($ftype, $setvalue, 'help1')) {
                    $access = false;
                }
            }

            if ($access && !$this->hideinforum()) {
                $branchtitle = get_string('helptitle', 'theme_adaptable');
                $branchlabel = $helpicon . $branchtitle;
                $branchurl = new moodle_url($PAGE->theme->settings->enablehelp, array('helptarget' => $PAGE->theme->settings->helptarget));

                $branchsort  = 10003;
                $branch = $menu->add($branchlabel, $branchurl, '', $branchsort);
            }
        }

        if (!empty($PAGE->theme->settings->enablehelp2 )) {
            $access = true;
            if (!empty($PAGE->theme->settings->helpprofilefield2)) {
                $fields = explode('=', $PAGE->theme->settings->helpprofilefield2);
                $ftype = $fields[0];
                $setvalue = $fields[1];
                if (!$this->check_menu_access($ftype, $setvalue, 'help2')) {
                    $access = false;
                }
            }

            if ($access && !$this->hideinforum()) {
                $branchtitle = get_string('helptitle2', 'theme_adaptable');
                $branchlabel = $helpicon . $branchtitle;
                $branchurl   = new moodle_url($PAGE->theme->settings->enablehelp2, array('helptarget' => $PAGE->theme->settings->helptarget));
                $branchsort  = 10003;
                $branch = $menu->add($branchlabel, $branchurl, '', $branchsort);
            }
        }

        if ($sessttl > 0) {
            $cache->set('usernavbarttl', $sessttl);
            $cache->set('usernavbar', $this->render_custom_menu($menu));
        }

        return $this->render_custom_menu($menu);
    }

    /**
     * Returns true if needs from array found in haystack
     * @param array $needles a list of strings to check
     * @param string $haystack value which may contain string
     * @return boolean
     */
    public function check_if_in_array_string($needles, $haystack) {
        foreach ($needles as $needle) {
            $needle = trim($needle);
            if (strstr($haystack, $needle)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Returns html to render tools menu in main navigation bar
     *
     * @return string
     */
    public function tools_menu() {
        global $PAGE;
        $custommenuitems = '';
        $access = true;
        $retval = '';

        if (!isset($PAGE->theme->settings->toolsmenuscount)) {
            return '';
        }
        $toolsmenuscount = $PAGE->theme->settings->toolsmenuscount;

        $class = '';
        if (!empty($PAGE->theme->settings->navbardisplayicons)) {
            $class .= "<i class='fa fa-wrench'></i>";
        }
        $class .= "<span class='menutitle'>";

        for ($i = 1; $i <= $toolsmenuscount; $i++) {
            $menunumber = 'toolsmenu' . $i;
            $menutitle = $menunumber . 'title';
            $requirelogin = $menunumber . 'requirelogin';
            $accessrules = $menunumber . 'field';
            $access = true;

            if (!empty($PAGE->theme->settings->$accessrules)) {
                $fields = explode ('=', $PAGE->theme->settings->$accessrules);
                $ftype = $fields[0];
                $setvalue = $fields[1];
                if (!$this->check_menu_access($ftype, $setvalue, $menunumber)) {
                    $access = false;
                }
            }

            if (!empty($PAGE->theme->settings->$menunumber) && $access == true && !$this->hideinforum()) {
                $menu = ($PAGE->theme->settings->$menunumber);
                $label = $PAGE->theme->settings->$menutitle;

                // Check the option of displaying a sub-menu arrow symbol.
                if (!empty($PAGE->theme->settings->navbardisplaysubmenuarrow)) {
                    $label .= ' &nbsp;<i class="fa fa-caret-down"></i>';
                }
                $custommenuitems = $this->parse_custom_menu($menu, $label, $class, '</span>');
                $custommenu = new custom_menu($custommenuitems);
                $retval .= $this->render_custom_menu($custommenu);
            }
        }
        return $retval;
    }

    /**
     * Returns html to render logo / title area
     *
     * @return string
     */
    public function get_logo_title() {
        global $PAGE, $COURSE, $CFG, $SITE;
        $retval = '';

        $hidelogomobile = $PAGE->theme->settings->hidelogomobile;

        if (((theme_adaptable_is_mobile()) && ($hidelogomobile == 1)) || (theme_adaptable_is_desktop())) {
            if (!empty($PAGE->theme->settings->logo)) {
                // Logo.
                $retval .= '<div id="logocontainer">';
                $retval .= "<a href='$CFG->wwwroot'>";
                $retval .= '<img src=' . $PAGE->theme->setting_file_url('logo', 'logo') . ' alt="logo" id="logo" />';
                $retval .= '</a></div>';
            }
        }

        $hidecoursetitlemobile = $PAGE->theme->settings->hidecoursetitlemobile;

        $coursetitlemaxwidth = (!empty($PAGE->theme->settings->coursetitlemaxwidth) ? $PAGE->theme->settings->coursetitlemaxwidth : 0);

        // If it is a mobile and the site title/course is not hidden or it is a desktop then we display the site title / course.
        if (((theme_adaptable_is_mobile()) && ($hidecoursetitlemobile == 1)) || (theme_adaptable_is_desktop())) {
            // If course id is greater than 1 we display course title.
            if ($COURSE->id > 1) {
                // Select title.
                $coursetitle = '';

                switch ($PAGE->theme->settings->enableheading) {
                    case 'fullname':
                        // Full Course Name.
                        $coursetitle = $COURSE->fullname;
                        break;

                    case 'shortname':
                        // Short Course Name.
                        $coursetitle = $COURSE->shortname;
                        break;
                }

                // Check max width of course title and trim if appropriate.
                if (($coursetitlemaxwidth > 0) && ($coursetitle <> '')) {
                    if (strlen($coursetitle) > $coursetitlemaxwidth) {
                        $coursetitle = substr($coursetitle, 0, $coursetitlemaxwidth) . " ...";
                    }
                }

                switch ($PAGE->theme->settings->enableheading) {
                    case 'fullname':
                        // Full Course Name.
                        $retval .= '<div id="sitetitle"><h1>' . format_string($coursetitle) . '<h1></div>';
                        break;

                    case 'shortname':
                        // Short Course Name.
                        $retval .= '<div id="sitetitle"><h1>' . format_string($coursetitle) . '</h1></div>';
                        break;

                    default:
                        // None.
                        $header = theme_adaptable_remove_site_fullname($PAGE->theme->settings->sitetitletext);
                        $sitetitlehtml = $PAGE->theme->settings->sitetitletext;
                        $retval .= '<div id="sitetitle">' . format_text($sitetitlehtml, FORMAT_HTML) . '</div>';

                        break;
                }
            }

            // If course id is one we display the site title.
            if ($COURSE->id == 1) {
                switch ($PAGE->theme->settings->sitetitle) {
                    case 'default':
                        // Default site title.
                        $retval .= '<div id="sitetitle"><h1>' . format_string($SITE->fullname) . '</h1></div>';
                        break;

                    case 'custom':
                        // Custom site title.
                        if (!empty($PAGE->theme->settings->sitetitletext)) {
                            $header = theme_adaptable_remove_site_fullname($PAGE->theme->settings->sitetitletext);
                            $sitetitlehtml = $PAGE->theme->settings->sitetitletext;
                            $header = format_string($header);
                            $PAGE->set_heading($header);

                            $retval .= '<div id="sitetitle">' . format_text($sitetitlehtml, FORMAT_HTML) . '</div>';
                        }
                }
            }
        }

        return $retval;
    }

    /**
     * Returns html to render top menu items
     *
     * @return string
     */
    public function get_top_menus() {
        global $PAGE, $COURSE;
        $template = new stdClass();
        $menus = array();
        $visibility = true;
        $nummenus = 0;

        if (!empty($PAGE->theme->settings->menuuseroverride)) {
            $visibility = $this->check_menu_user_visibility();
        }

        $template->showright = false;
        if (!empty($PAGE->theme->settings->menuslinkright)) {
            $template->showright = true;
        }

        if ($visibility) {
            if (!empty($PAGE->theme->settings->topmenuscount) && !empty($PAGE->theme->settings->enablemenus)
                && (!$PAGE->theme->settings->disablemenuscoursepages || $COURSE->id == 1)) {

                $topmenuscount = $PAGE->theme->settings->topmenuscount;
                for ($i = 1; $i <= $topmenuscount; $i++) {
                    $menunumber = 'menu' . $i;
                    $newmenu = 'newmenu' . $i;
                    $class = 'newmenu' . ($i + 4);
                    $fieldsetting = 'newmenu' . $i . 'field';
                    $valuesetting = 'newmenu' . $i . 'value';
                    $newmenutitle = 'newmenu' . $i . 'title';
                    $requirelogin = 'newmenu' . $i . 'requirelogin';
                    $logincheck = true;
                    $custommenuitems = '';
                    $access = true;

                    if (empty($PAGE->theme->settings->$requirelogin) || isloggedin()) {
                        if (!empty($PAGE->theme->settings->$fieldsetting)) {
                            $fields = explode('=', $PAGE->theme->settings->$fieldsetting);
                            $ftype = $fields[0];
                            $setvalue = $fields[1];
                            if (!$this->check_menu_access($ftype, $setvalue, $menunumber)) {
                                $access = false;
                            }
                        }

                        if (!empty($PAGE->theme->settings->$newmenu) && $access == true) {
                            $nummenus++;
                            $menu = ($PAGE->theme->settings->$newmenu);
                            $title = ($PAGE->theme->settings->$newmenutitle);
                            $custommenuitems = $this->parse_custom_menu($menu, format_string($title));
                            $custommenu = new custom_menu($custommenuitems, current_language());
                            $menus[] = $this->render_overlay_menu($custommenu);
                        }
                    }
                }
            }
        }
        if ($nummenus == 0) {
            return '';
        }
        $template->rows = array();

        $grid = array(
            '5' => '3',
            '6' => '3',
            '7' => '4',
            '8' => '4',
            '9' => '3',
            '10' => '4',
            '11' => '4',
            '12' => '4'
        );
        if ($nummenus <= 4) {
            $row = new stdClass();
            $row->span = (12 / $nummenus);
            $row->menus = $menus;
            $template->rows[] = $row;
        } else {
            $numperrow = $grid[$nummenus];
            $chunks = array_chunk($menus, $numperrow);
            $menucount = 0;
            for ($i = 0; $i < $nummenus; $i++) {
                if ($i % $numperrow == 0) {
                    $row = new stdClass();
                    $row->span = (12 / $numperrow);
                    $row->menus = $chunks[$menucount++];
                    $template->rows[] = $row;
                }
            }
        }
        return $this->render_from_template('theme_adaptable/overlaymenu', $template);
    }

     /**
      * Render the menu items for the overlay menu
      *
      * @param custom_menu $menu
      * @return array of menus
      */
    private function render_overlay_menu(custom_menu $menu) {
        $template = new stdClass();
        if (!$menu->has_children()) {
            return '';
        }
        $template->menuitems = '';
        foreach ($menu->get_children() as $item) {
            $template->menuitems .= $this->render_overlay_menu_item($item, 0);
        }
        return $template;
    }

    /**
     * Render the overlay menu items.
     *
     * @param custom_menu_item $item
     * @return string html for item
     */
    private function render_overlay_menu_item(custom_menu_item $item, $level = 0) {
        $content = '';
        if ($item->has_children()) {
            $node = new stdClass;
            $node->title = $item->get_title();
            $node->text = $item->get_text();
            $node->class = 'level-' . $level;

            // Top level menu.  Check if URL contains a valid URL, if not
            // then use standard javascript:void(0).  Done to fix current
            // jquery / Bootstrap incompatibility with using # in target URLS.
            // Ref: Issue 617 on Adaptable theme issues on Bitbucket.
            if (empty($item->get_url())) {
                $node->url = "javascript:void(0)";
            } else {
                $node->url = $item->get_url();
            }

            $content .= $this->render_from_template('theme_adaptable/overlaymenuitem', $node);
            $level++;
            foreach ($item->get_children() as $subitem) {
                $content .= $this->render_overlay_menu_item($subitem, $level);
            }
        } else {
            $node = new stdClass;
            $node->title = $item->get_title();
            $node->text = $item->get_text();
            $node->class = 'level-' . $level;
            $node->url = $item->get_url();
            $content .= $this->render_from_template('theme_adaptable/overlaymenuitem', $node);
        }
        return $content;
    }

    /**
     * Checks menu visibility where setup to allow users to control via custom profile setting
     *
     * @return boolean
     */
    public function check_menu_user_visibility() {
        global $PAGE, $USER, $COURSE;
        $uservalue = '';

        if (empty($PAGE->theme->settings->menuuseroverride)) {
            return true;
        }

        if (isset($USER->theme_adaptable_menus['menuvisibility'])) {
            $uservalue = $USER->theme_adaptable_menus['menuvisibility'];
        } else {
            $profilefield = $PAGE->theme->settings->menuoverrideprofilefield;
            $profilefield = 'profile_field_' . $profilefield;
            $uservalue = $this->get_user_visibility($profilefield);
        }

        if ($uservalue == 0) {
            return true;
        }

        if ($uservalue == 1 && $COURSE->id != 1) {
            return false;
        }

        if ($uservalue == 2) {
            return false;
        }

        // Default to true means we dont have to evaluate sitewide setting and guarantees return value.
        return true;
    }

    /**
     * Check users menu visibility settings, will store in session to avaoid repeated loading of profile data
     * @param string $profilefield
     * @return boolean
     */
    public function get_user_visibility($profilefield) {
        global $USER, $CFG;
        $uservisibility = '';

        require_once($CFG->dirroot.'/user/profile/lib.php');
        require_once($CFG->dirroot.'/user/lib.php');
        profile_load_data($USER);

        $uservisibility = $USER->$profilefield;
        $USER->theme_adaptable_menus['menuvisibility'] = $uservisibility;
        return $uservisibility;
    }

    /**
     * Checks menu access based on admin settings and a users custom profile fields
     *
     * @param string $ftype the custom profile field
     * @param string $setvalue the expected value a user must have in their profile field
     * @param string $menu a token to identify the menu used to store access in session
     * @return boolean
     */
    public function check_menu_access($ftype, $setvalue, $menu) {
        global $PAGE, $USER, $CFG;
        $usersvalue = 'default-zz'; // Just want a value that will not be matched by accident.
        $sessttl = (time() + ($PAGE->theme->settings->menusessionttl * 60));
        $menuttl = $menu . 'ttl';

        if ($PAGE->theme->settings->menusession) {
            if (isset($USER->theme_adaptable_menus[$menu])) {

                // If cache hasn't yet expired.
                if ($USER->theme_adaptable_menus[$menuttl] >= time()) {
                    if ($USER->theme_adaptable_menus[$menu] == true) {
                        return true;
                    } else if ($USER->theme_adaptable_menus[$menu] == false) {
                        return false;
                    }
                }
            }
        }

        require_once($CFG->dirroot.'/user/profile/lib.php');
        require_once($CFG->dirroot.'/user/lib.php');
        profile_load_data($USER);
        $ftype = "profile_field_$ftype";
        if (isset($USER->$ftype)) {
            $usersvalue = $USER->$ftype;
        }

        if ($usersvalue == $setvalue) {
            $USER->theme_adaptable_menus[$menu] = true;
            $USER->theme_adaptable_menus[$menuttl] = $sessttl;
            return true;
        }

        $USER->theme_adaptable_menus[$menu] = false;
        $USER->theme_adaptable_menus[$menuttl] = $sessttl;
        return false;
    }

    /**
     * Returns list of cohort enrollments
     *
     * @return array
     */
    public function get_cohort_enrollments() {
        global $DB, $USER;
        $userscohorts = $DB->get_records('cohort_members', array('userid' => $USER->id));
        $courses = array();
        if ($userscohorts) {
            $cohortedcourseslist = $DB->get_records_sql('select '
                    . 'courseid '
                    . 'from {enrol} '
                    . 'where enrol = "cohort" '
                    . 'and customint1 in (?)', array_keys($userscohorts));
            $cohortedcourses = $DB->get_records_list('course', 'id', array_keys($cohortedcourseslist), null, 'shortname');
            foreach ($cohortedcourses as $course) {
                $courses[] = $course->shortname;
            }
        }
        return($courses);
    }

    /**
     * Returns contents of multiple comma delimited custom profile fields
     *
     * @param string $profilefields delimited list of fields
     * @return array
     */
    public function get_profile_field_contents($profilefields) {
        global $PAGE, $USER, $CFG;
        $timestamp = 'currentcoursestime';
        $list = 'currentcourseslist';

        if (isset($USER->theme_adaptable_menus[$timestamp])) {
            if ($USER->theme_adaptable_menus[$timestamp] >= time()) {
                if (isset($USER->theme_adaptable_menus[$list])) {
                    return $USER->theme_adaptable_menus[$list];
                }
            }
        }

        $sessttl = 1000 * 60 * 3;
        $sessttl = 0;
        $sessttl = time() + $sessttl;
        $retval = array();

        require_once($CFG->dirroot.'/user/profile/lib.php');
        require_once($CFG->dirroot.'/user/lib.php');
        profile_load_data($USER);

        $fields = explode(',', $profilefields);

        foreach ($fields as $field) {
            $field = trim($field);
            $field = "profile_field_$field";
            if (isset($USER->$field)) {
                $vals = explode(',', $USER->$field);
                foreach ($vals as $value) {
                    $retval[] = trim($value);
                }
            }
        }

        $USER->theme_adaptable_menus[$list] = $retval;
        $USER->theme_adaptable_menus[$timestamp] = $sessttl;
        return $retval;
    }

    /**
     * Parses / wraps custom menus in HTML
     *
     * @param string $menu
     * @param string $label
     * @param string $class
     * @param string $close
     *
     * @return string
     */
    public function parse_custom_menu($menu, $label, $class = '', $close = '') {

        // Top level menu option.  No URL added after $close (previously was #).
        // Done to fix current jquery / Bootstrap version incompatibility with using #
        // in target URLS. Ref: Issue 617 on Adaptable theme issues on Bitbucket.
        $custommenuitems = $class . $label. $close . "||".$label."\n";
        $arr = explode("\n", $menu);

        // We want to force everything inputted under this menu.
        foreach ($arr as $key => $value) {
            $arr[$key] = '-' . $arr[$key];
        }

        $custommenuitems .= implode("\n", $arr);
        return $custommenuitems;
    }

    /**
     * Hide tools menu in forum to make room for forum search optoin
     *
     * @return boolean
     */
    public function hideinforum() {
        global $PAGE;
        $hidelinks = false;
        if (!empty($PAGE->theme->settings->hideinforum)) {
            if (strstr($_SERVER['REQUEST_URI'], '/mod/forum/')) {
                $hidelinks = true;
            }
        }
        return $hidelinks;
    }

    /**
     * Wrap html round custom menu
     *
     * @param string $custommenu
     * @param string $classno
     *
     * @return string
     */
    public function wrap_custom_menu_top($custommenu, $classno) {
        $retval = '<div class="dropdown pull-right newmenus newmenu$classno">';
        $retval .= $custommenu;
        $retval .= '</div>';
        return $retval;
    }

    /**
     * Returns language menu
     *
     * @return string
     */
    public function lang_menu() {
        global $CFG;
        $langmenu = new custom_menu();

        $addlangmenu = true;
        $langs = get_string_manager()->get_list_of_translations();
        if (count($langs) < 2
            || empty($CFG->langmenu)
            || ($this->page->course != SITEID
            && !empty($this->page->course->lang))) {
                $addlangmenu = false;
        }

        if ($addlangmenu) {
            $strlang = get_string('language');
            $currentlang = current_language();

            if (isset($langs[$currentlang])) {
                $currentlang = $langs[$currentlang];
            } else {
                $currentlang = $strlang;
            }

            $this->language = $langmenu->add('<i class="fa fa-globe fa-lg"></i><span class="langdesc">'.$currentlang.'</span>',
                                             new moodle_url($this->page->url), $strlang, 10000);

            foreach ($langs as $langtype => $langname) {
                $this->language->add($langname, new moodle_url($this->page->url, array('lang' => $langtype)), $langname);
            }
        }
        return $this->render_custom_menu($langmenu);
    }


    /**
     * Returns html for custom menu
     *
     * @param string $custommenuitems = ''
     * @return array
     */
    public function custom_menu($custommenuitems = '') {
        global $CFG;

        if (empty($custommenuitems) && !empty($CFG->custommenuitems)) {
            $custommenuitems = $CFG->custommenuitems;
        }
        $custommenu = new custom_menu($custommenuitems, current_language());
        return $this->render_custom_menu($custommenu);
    }

    /**
     * This renders the bootstrap top menu.     *
     * This renderer is needed to enable the Bootstrap style navigation.
     *
     * @param custom_menu $menu
     * @param string $wrappre
     * @param string $wrappost
     * @return string
     */
    protected function render_custom_menu(custom_menu $menu, $wrappre = '', $wrappost = '') {
        global $CFG;

        // TODO: eliminate this duplicated logic, it belongs in core, not
        // here. See MDL-39565.
        $addlangmenu = true;
        $langs = get_string_manager()->get_list_of_translations();
        if (count($langs) < 2
            or empty($CFG->langmenu)
            or ($this->page->course != SITEID and !empty($this->page->course->lang))) {
            $addlangmenu = false;
        }

        if (!$menu->has_children() && $addlangmenu === false) {
            return '';
        }

        $content = '<ul class="nav navbar-nav">';
        foreach ($menu->get_children() as $item) {
            $content .= $this->render_custom_menu_item($item, 1);
        }
        $content = $wrappre . $content . '</ul>' . $wrappost;
        return $content;
    }

    /**
     * This code renders the custom menu items for the bootstrap dropdown menu.
     *
     * @param custom_menu_item $menunode
     * @param int $level = 0
     * @return string
     */
    protected function render_custom_menu_item(custom_menu_item $menunode, $level = 0) {
        static $submenucount = 0;

        if ($menunode->has_children()) {

            if ($level == 1) {
                $class = 'dropdown';
            } else {
                $class = 'dropdown-submenu';
            }

            if ($menunode === $this->language) {
                $class .= ' langmenu';
            }
            $content = html_writer::start_tag('li', array('class' => $class));
            // If the child has menus render it as a sub menu.
            $submenucount++;
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#cm_submenu_'.$submenucount;
            }
            $content .= html_writer::start_tag('a', array('href' => $url, 'class' => 'dropdown-toggle',
                    'data-toggle' => 'dropdown', 'title' => $menunode->get_title()));
            $content .= $menunode->get_text();
            $content .= '</a>';
            $content .= '<ul class="dropdown-menu">';
            foreach ($menunode->get_children() as $menunode) {
                $content .= $this->render_custom_menu_item($menunode, 0);
            }
            $content .= '</ul>';
        } else {
            $content = '<li>';
            // The node doesn't have children so produce a final menuitem.
            if ($menunode->get_url() !== null) {
                $url = $menunode->get_url();
            } else {
                $url = '#';
            }

            /* This is a bit of a cludge, but allows us to pass url, of type moodle_url with a param of
             * "helptarget", which when equal to "_blank", will create a link with target="_blank" to allow the link to open
             * in a new window.  This param is removed once checked.
             */
            if (is_object($url) && (get_class($url) == 'moodle_url') && ($url->get_param('helptarget') != null)) {
                $helptarget = $url->get_param('helptarget');
                $url->remove_params('helptarget');
                $content .= html_writer::link($url, $menunode->get_text(), array('title' => $menunode->get_title(),
                                             'target' => $helptarget));
            } else {
                $content .= html_writer::link($url, $menunode->get_text(), array('title' => $menunode->get_title()));
            }

            $content .= "</li>";
        }
        return $content;
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
        $firstrow = $secondrow = '';
        foreach ($tabtree->subtree as $tab) {
            $firstrow .= $this->render($tab);
            if (($tab->selected || $tab->activated) && !empty($tab->subtree) && $tab->subtree !== array()) {
                $secondrow = $this->tabtree($tab->subtree);
            }
        }
        return html_writer::tag('ul', $firstrow, array('class' => 'nav nav-tabs')) . $secondrow;
    }

    /**
     * Renders tabobject (part of tabtree)
     *
     * This function is called from {@link core_renderer::render_tabtree()}
     * and also it calls itself when printing the $tabobject subtree recursively.
     *
     * @param tabobject $tab
     * @return string HTML fragment
     */
    protected function render_tabobject(tabobject $tab) {
        if ($tab->selected or $tab->activated) {
            return html_writer::tag('li', html_writer::tag('a', $tab->text), array('class' => 'active'));
        } else if ($tab->inactive) {
            return html_writer::tag('li', html_writer::tag('a', $tab->text), array('class' => 'disabled'));
        } else {
            if (!($tab->link instanceof moodle_url)) {
                // Backward compartibility when link was passed as quoted string.
                $link = "<a href=\"$tab->link\" title=\"$tab->title\">$tab->text</a>";
            } else {
                $link = html_writer::link($tab->link, $tab->text, array('title' => $tab->title));
            }
            return html_writer::tag('li', $link);
        }
    }

    /**
     * Returns empty string
     *
     * @return string
     */
    protected function theme_switch_links() {
        // We're just going to return nothing and fail nicely, whats the point in bootstrap if not for responsive?
        return '';
    }

    /**
     * Render blocks
     * @param string $region
     * @param array $classes
     * @param string $tag
     * @return string
     */
    public function adaptableblocks($region, $classes = array(), $tag = 'aside') {
        $classes = (array)$classes;
        $classes[] = 'block-region';
        $attributes = array(
            'id' => 'block-region-'.preg_replace('#[^a-zA-Z0-9_\-]+#', '-', $region),
            'class' => join(' ', $classes),
            'data-blockregion' => $region,
            'data-droptarget' => '1'
        );
        return html_writer::tag($tag, $this->blocks_for_region($region), $attributes);
    }
}

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

namespace theme_schoollege\output;

use html_writer;
use custom_menu;
use action_menu_filler;
use action_menu_link_secondary;
use stdClass;
use moodle_url;
use action_menu;
use theme_config;
use core_text;
use help_icon;
use context_system;
use core_course_list_element;
use context_course;
use coding_exception;
use tabobject;
use tabtree;
use custom_menu_item;
use block_contents;
use navigation_node;
use action_link;
use single_button;
use single_select;
use url_select;
use pix_icon;

defined('MOODLE_INTERNAL') || die;

class core_renderer extends \theme_boost\output\core_renderer {

    public function full_header() {
        global $PAGE, $COURSE, $USER, $course, $DB;
        //$theme = theme_config::load('schoollege');
        
        if ($this->page->include_region_main_settings_in_header_actions() &&
                !$this->page->blocks->is_block_present('settings')) {
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
        //$header->pageheadingbutton = $this->page_heading_button();
        $header->hasnavbar = empty($this->page->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        //$header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = html_writer::link(new moodle_url('/course/view.php', array('id' => $PAGE->course->id)) , $this->context_header());
        $header->courseheader = $this->course_header();
        $header->headeractions = $this->page->get_header_actions();
        $header->hasoverlayimage = $COURSE->id <=1 && !empty($PAGE->theme->settings->headeroverlay) && $PAGE->theme->settings->showheaderimages == 1;
        $header->headerimage = $this->headerimage();
        $header->hasbrandlogo = $COURSE->id <=1 && null !== $PAGE->theme->setting_file_url('brandlogo', 'brandlogo');
        $header->brandlogourl = $PAGE->theme->setting_file_url('brandlogo', 'brandlogo', true);

        if($COURSE->id <=1 && !empty($PAGE->theme->settings->headeroverlay) && $PAGE->theme->settings->showheaderimages == 1){
            $header->hasoverlayimage = true;
        } else if ( !empty($PAGE->theme->settings->headeroverlay) && $PAGE->theme->settings->showheaderimages == 0) {
            $header->hasoverlayimage = true;
        }

        return $this->render_from_template('theme_schoollege/core/full_header', $header);
    }


    public function edit_button_schoollege() {
        global $SITE, $PAGE, $USER, $CFG, $COURSE;
        if (!$PAGE->user_allowed_editing() || $COURSE->id <= 1) {
            return '';
        }
        if ($PAGE->pagelayout == 'course' || $PAGE->pagelayout == 'admin') {
            $url = new moodle_url($PAGE->url);
            $url->param('sesskey', sesskey());
            if ($PAGE->user_is_editing()) {
                $url->param('edit', 'off');
                $btn = 'btn-danger sideicon courseedit ';
                $title = get_string('editoff', 'theme_rebel');
                $icon = 'fa-power-off';
            }
            else {
                $url->param('edit', 'on');
                $btn = ' attention courseedit';
                $title = get_string('editon', 'theme_rebel');
                $icon = 'fa-edit';
            }
            return html_writer::tag('a', html_writer::start_tag('i', array(
                'class' => $icon . ' fa fa-fw'
            )) . html_writer::end_tag('i') , array(
                'href' => $url,
                'class' => 'edit-btn sideicon ' . $btn,
                'data-tooltip' => "tooltip",
                'data-placement' => "bottom",
                'title' => $title,
            ));
            return $output;
        }
    }

    public function edit_button(moodle_url $url) {
        return '';
    }

    //Make Settings Menu Drop down appear on course and module pages.
    public function context_header_settings_menu() {
        $context = $this->page->context;
        $menu = new action_menu();

        $items = $this->page->navbar->get_items();
        $currentnode = end($items);

        $showcoursemenu = false;
        $showfrontpagemenu = false;
        $showusermenu = false;

        // We are on the course home page or course modules.  Schoollege changes here...
        if ($context->contextlevel == CONTEXT_COURSE || $context->contextlevel == CONTEXT_MODULE) {
            $showcoursemenu = true;
        }

        $courseformat = course_get_format($this->page->course);
        // This is a single activity course format, always show the course menu on the activity main page.
        if ($context->contextlevel == CONTEXT_MODULE &&
                !$courseformat->has_view_page()) {

            $this->page->navigation->initialise();
            $activenode = $this->page->navigation->find_active_node();
            // If the settings menu has been forced then show the menu.
            if ($this->page->is_settings_menu_forced()) {
                $showcoursemenu = true;
            } else if (!empty($activenode) && ($activenode->type == navigation_node::TYPE_ACTIVITY ||
                            $activenode->type == navigation_node::TYPE_RESOURCE)) {

                // We only want to show the menu on the first page of the activity. This means
                // the breadcrumb has no additional nodes.
                if ($currentnode && ($currentnode->key == $activenode->key && $currentnode->type == $activenode->type)) {
                    $showcoursemenu = true;
                }
            }
        }

        // This is the site front page.
        if ($context->contextlevel == CONTEXT_COURSE &&
                !empty($currentnode) &&
                $currentnode->key === 'home') {
            $showfrontpagemenu = true;
        }

        // This is the user profile page.
        if ($context->contextlevel == CONTEXT_USER &&
                !empty($currentnode) &&
                ($currentnode->key === 'myprofile')) {
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
                    $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
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
                    $url = new moodle_url('/course/admin.php', array('courseid' => $this->page->course->id));
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


    //Show on Course Category Page.
    public function enrolformcoursepage() {
        global $PAGE;
        $enrolform = '';
        $plugin = enrol_get_plugin('easy');
        $title = get_string('easyenrol_title', 'theme_schoollege');
        $blurp = get_string('easyenrol_blurp', 'theme_schoollege');
        $showeasyenrollmentform = $PAGE->pagelayout == 'coursecategory' || $PAGE->pagelayout == 'mydashboard';

        if (!isguestuser() && $showeasyenrollmentform && $plugin) {
            $enrolform = '<div class="easyenrollform" id="easyenrollform">';
            $enrolform .= $plugin->get_form();
            $enrolform .= '</div>';
        }
        return $enrolform;
    }

    Public function iconsidebarmenu() {
        global $PAGE, $COURSE, $CFG, $USER, $OUTPUT;

        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $theme = theme_config::load('schoollege');
        

        // Restrict Access.
        $hasadminlink = has_capability('moodle/site:configview', $context);
        $showincourseonly = isset($COURSE->id) && $COURSE->id > 1 && isloggedin() && !isguestuser();
        $showondashboardonly = $PAGE->pagelayout == 'mydashboard';
        $showloggedinonly = isloggedin();
        $showguestonly = !isloggedin();
        $userisediting = $PAGE->user_is_editing() && $PAGE->user_can_edit_blocks();
        $isteacherdash = has_capability('moodle/course:viewhiddenactivities', $context);

        // Icon Links.
        $mycourses = get_string('mycourses', 'moodle');
        $mycoursesurl = new moodle_url('/my/');

        $editpage = $this->edit_button_schoollege();

        $addblock = get_string('addblock', 'moodle');
        $addblockurl = new moodle_url($PAGE->url, ['bui_addblock' => '', 'sesskey' => sesskey()]);
        
        $courseadmin = get_string('dashboardiconbutton', 'theme_schoollege');

        $hascreatecourse = (isloggedin() && has_capability('moodle/course:create', $context)) && ($PAGE->pagelayout == 'coursecategory');
        $createcourseurl = new moodle_url('/course/edit.php');
        $createcourse = get_string('createnewcourse', 'moodle');;

        $viewcourses = get_string('findmorecourses', 'moodle');
        $viewcoursesurl = new moodle_url('/course/');

        $siteadmintitle = get_string('administrationsite', 'moodle');
        $siteadminurl = new moodle_url('/admin/search.php');


        $showcourseadminlink = has_capability('moodle/course:viewhiddenactivities', $context);
        $directcourseadminlink = new moodle_url('/course/admin.php', array('courseid' => $PAGE->course->id));

        $mycoursesmenu = $this->schoollege_mycourses();
        $coursedashboard = $this->teacherdash();

        // Send to template
        $iconsidebar = [
            'showincourseonly' => $showincourseonly,
            'courseadmin' => $courseadmin, 
            'hasadminlink' => $hasadminlink, 
            'siteadmintitle' => $siteadmintitle, 
            'siteadminurl' => $siteadminurl,
            'mycourses' => $mycourses, 
            'mycoursesurl' => $mycoursesurl, 
            'editpage' => $editpage, 
            'addblock' => $addblock, 
            'addblockurl' => $addblockurl, 
            'userisediting' => $userisediting, 
            'viewcoursesurl' => $viewcoursesurl , 
            'viewcourses' => $viewcourses, 
            'createcourse' => $createcourse, 
            'createcourseurl' => $createcourseurl, 
            'hascreatecourse' => $hascreatecourse, 
            'showondashboardonly' => $showondashboardonly, 
            'showloggedinonly' => $showloggedinonly,
            'showguestonly' => $showguestonly,
            'showcourseadminlink' => $showcourseadminlink,
            'directcourseadminlink' => $directcourseadminlink,
            'mycoursesmenu' => $mycoursesmenu,
            'coursedashboard' => $coursedashboard,
        ];

        return $this->render_from_template('theme_schoollege/iconsidebar', $iconsidebar);
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

    // Csutom login page
    public function render_login(\core_auth\output\login $form) {
        global $SITE, $PAGE;
        $context = $form->export_for_template($this);
        // Override because rendering is not supported in template yet.
        $context->cookieshelpiconformatted = $this->help_icon('cookiesenabled');
        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        // Custom logins.
        if (isset($PAGE->theme->settings->feature1text)) {
           $context->feature1text = format_text($PAGE->theme->settings->feature1text);
        }
        if (isset($PAGE->theme->settings->feature2text)) {
           $context->feature2text = format_text($PAGE->theme->settings->feature2text);
        }
        if (isset($PAGE->theme->settings->feature3text)) {
           $context->feature3text = format_text($PAGE->theme->settings->feature3text);
        }
        if (isset($PAGE->theme->settings->loginbottomtext)) {
           $context->loginbottomtext = format_text($PAGE->theme->settings->loginbottomtext);
        }
        if (isset($PAGE->theme->settings->logintoptext)) {
           $context->logintoptext = format_text($PAGE->theme->settings->logintoptext);
        }
        if (null !==$PAGE->theme->setting_file_url('logintopimage', 'logintopimage')) {
           $context->logintopimage = $PAGE->theme->setting_file_url('logintopimage', 'logintopimage');
        }
        if (isset($PAGE->theme->settings->showcustomlogin)) {
           $context->hascustomlogin = $PAGE->theme->settings->showcustomlogin == 1;
        }
        if (isset($PAGE->theme->settings->alertbox)) {
           $context->alertbox = format_text($PAGE->theme->settings->alertbox, FORMAT_HTML, array('noclean' => true));
        }

        if ($url) {
            $url = $url->out(false);
        }
        $context->logourl = $url;
        $context->sitename = format_string($SITE->fullname, true, ['context' => context_course::instance(SITEID) , "escape" => false]);
        return $this->render_from_template('core/loginform', $context);
    }

/*
     * This renders the bootstrap top menu.
     *
     * This renderer is needed to enable the Bootstrap style navigation.
    */

    protected static function timeaccesscompare($a, $b) {
            // Timeaccess is lastaccess entry and timestart an enrol entry.
            if ((!empty($a->timeaccess)) && (!empty($b->timeaccess))) {
                // Both last access.
                if ($a->timeaccess == $b->timeaccess) {
                    return 0;
                }
                return ($a->timeaccess > $b->timeaccess) ? -1 : 1;
            }
            else if ((!empty($a->timestart)) && (!empty($b->timestart))) {
                // Both enrol.
                if ($a->timestart == $b->timestart) {
                    return 0;
                }
                return ($a->timestart > $b->timestart) ? -1 : 1;
            }
            // Must be comparing an enrol with a last access.
            // -1 is to say that 'a' comes before 'b'.
            if (!empty($a->timestart)) {
                // 'a' is the enrol entry.
                return -1;
            }
            // 'b' must be the enrol entry.
            return 1;
        }

    public function schoollege_mycourses() {
        global $CFG, $COURSE, $PAGE, $OUTPUT;
        $context = $this->page->context;
        $menu = new custom_menu();
        
            $branchtitle = get_string('mycourses', 'moodle');
            $branchlabel = $branchtitle;
            $branchurl = new moodle_url('/my/index.php');
            $branchsort = 10000;
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
            $dashlabel = get_string('mymoodle', 'my');
            $dashurl = new moodle_url("/my");
            $dashtitle = $dashlabel;
            $branch->add($dashlabel, $dashurl, $dashtitle);
           
                $courses = enrol_get_my_courses(null, 'sortorder ASC');
                $nomycourses = '<div class="alert alert-info alert-block">' . get_string('nomycourses', 'theme_schoollege') . '</div>';
                if ($courses) {
                    // We have something to work with.  Get the last accessed information for the user and populate.
                    global $DB, $USER;
                    $lastaccess = $DB->get_records('user_lastaccess', array('userid' => $USER->id) , '', 'courseid, timeaccess');
                    if ($lastaccess) {
                        foreach ($courses as $course) {
                            if (!empty($lastaccess[$course->id])) {
                                $course->timeaccess = $lastaccess[$course->id]->timeaccess;
                            }
                        }
                    }
                    // Determine if we need to query the enrolment and user enrolment tables.
                    $enrolquery = false;
                    foreach ($courses as $course) {
                        if (empty($course->timeaccess)) {
                            $enrolquery = true;
                            break;
                        }
                    }
                    if ($enrolquery) {
                        // We do.
                        $params = array(
                            'userid' => $USER->id
                        );
                        $sql = "SELECT ue.id, e.courseid, ue.timestart
                            FROM {enrol} e
                            JOIN {user_enrolments} ue ON (ue.enrolid = e.id AND ue.userid = :userid)";
                        $enrolments = $DB->get_records_sql($sql, $params, 0, 0);
                        if ($enrolments) {
                            // Sort out any multiple enrolments on the same course.
                            $userenrolments = array();
                            foreach ($enrolments as $enrolment) {
                                if (!empty($userenrolments[$enrolment->courseid])) {
                                    if ($userenrolments[$enrolment->courseid] < $enrolment->timestart) {
                                        // Replace.
                                        $userenrolments[$enrolment->courseid] = $enrolment->timestart;
                                    }
                                }
                                else {
                                    $userenrolments[$enrolment->courseid] = $enrolment->timestart;
                                }
                            }
                            // We don't need to worry about timeend etc. as our course list will be valid for the user from above.
                            foreach ($courses as $course) {
                                if (empty($course->timeaccess)) {
                                    $course->timestart = $userenrolments[$course->id];
                                }
                            }
                        }
                    }
                    uasort($courses, array($this,'timeaccesscompare'));
                }
                else {
                    return $nomycourses;
                }
                $sortorder = $lastaccess;
            
                foreach ($courses as $course) {
                    if ($course->visible) {
                        $branch->add(format_string($course->fullname) , new moodle_url('/course/view.php?id=' . $course->id) , format_string($course->shortname));
                    }
                }
            
            
             $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('theme_schoollege/mycourses', $context);
        }
        return $content;
    }


    public function teacherdash() {
        global $PAGE, $COURSE, $CFG, $DB, $OUTPUT, $USER;
        require_once ($CFG->dirroot . '/completion/classes/progress.php');
        $togglebutton = '';
        $togglebuttonstudent = '';
        $hasteacherdash = '';
        $hasstudentdash = '';
        $editcog = html_writer::div($this->context_header_settings_menu() , 'pull-xs-right context-header-settings-menu');
        if (isloggedin() && ISSET($COURSE->id) && $COURSE->id > 1) {
            $course = $this->page->course;
            $context = context_course::instance($course->id);
            $hasteacherdash = has_capability('moodle/course:viewhiddenactivities', $context);
            $hasstudentdash = !has_capability('moodle/course:viewhiddenactivities', $context);
            if (has_capability('moodle/course:viewhiddenactivities', $context)) {
                $togglebutton = get_string('coursemanagementbutton', 'theme_schoollege');
            }
            else {
                $togglebuttonstudent = get_string('studentdashbutton', 'theme_schoollege');
            }
        }
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $coursemanagementmessage = (empty($PAGE->theme->settings->coursemanagementtextbox)) ? false : format_text($PAGE->theme->settings->coursemanagementtextbox);

        $showincourseonly = isset($COURSE->id) && $COURSE->id > 1 && isloggedin() && !isguestuser();
        $globalhaseasyenrollment = enrol_get_plugin('easy');
        $coursehaseasyenrollment = '';
        if ($globalhaseasyenrollment) {
            $coursehaseasyenrollment = $DB->record_exists('enrol', array(
                'courseid' => $COURSE->id,
                'enrol' => 'easy'
            ));
            $easyenrollinstance = $DB->get_record('enrol', array(
                'courseid' => $COURSE->id,
                'enrol' => 'easy'
            ));
        }
        // Link catagories.
        $haspermission = has_capability('enrol/category:config', $context) && isset($COURSE->id) && $COURSE->id > 1;
        $userlinks = get_string('userlinks', 'theme_schoollege');
        $userlinksdesc = get_string('userlinks_desc', 'theme_schoollege');
        $qbank = get_string('qbank', 'theme_schoollege');
        $qbankdesc = get_string('qbank_desc', 'theme_schoollege');
        $badges = get_string('badges', 'theme_schoollege');
        $badgesdesc = get_string('badges_desc', 'theme_schoollege');
        $coursemanage = get_string('coursemanage', 'theme_schoollege');
        $coursemanagedesc = get_string('coursemanage_desc', 'theme_schoollege');
        $cbank = get_string('cbank', 'theme_schoollege');
        $cbankdesc = get_string('cbank_desc', 'theme_schoollege');

        // User links.
        if ($coursehaseasyenrollment && isset($COURSE->id) && $COURSE->id > 1) {
            $easycodetitle = get_string('header_coursecodes', 'enrol_easy');
            $easycodelink = new moodle_url('/enrol/editinstance.php', array(
                'courseid' => $PAGE->course->id,
                'id' => $easyenrollinstance->id,
                'type' => 'easy'
            ));
        }
        $gradestitle = get_string('gradebooksetup', 'grades');
        $gradeslink = new moodle_url('/grade/edit/tree/index.php', array(
            'id' => $PAGE->course->id
        ));
        $gradebooktitle = get_string('gradebook', 'grades');
        $gradebooklink = new moodle_url('/grade/report/grader/index.php', array(
            'id' => $PAGE->course->id
        ));
        $participantstitle = get_string('participants', 'moodle');
        $participantslink = new moodle_url('/user/index.php', array(
            'id' => $PAGE->course->id
        ));
        (empty($participantstitle)) ? false : get_string('participants', 'moodle');
        $activitycompletiontitle = get_string('activitycompletion', 'completion');
        $activitycompletionlink = new moodle_url('/report/progress/index.php', array(
            'course' => $PAGE->course->id
        ));
        $grouptitle = get_string('groups', 'group');
        $grouplink = new moodle_url('/group/index.php', array(
            'id' => $PAGE->course->id
        ));
        $enrolmethodtitle = get_string('enrolmentinstances', 'enrol');
        $enrolmethodlink = new moodle_url('/enrol/instances.php', array(
            'id' => $PAGE->course->id
        ));
        // User reports.
        $logstitle = get_string('logs', 'moodle');
        $logslink = new moodle_url('/report/log/index.php', array(
            'id' => $PAGE->course->id
        ));
        $livelogstitle = get_string('loglive:view', 'report_loglive');
        $livelogslink = new moodle_url('/report/loglive/index.php', array(
            'id' => $PAGE->course->id
        ));
        $participationtitle = get_string('participation:view', 'report_participation');
        $participationlink = new moodle_url('/report/participation/index.php', array(
            'id' => $PAGE->course->id
        ));
        $activitytitle = get_string('outline:view', 'report_outline');
        $activitylink = new moodle_url('/report/outline/index.php', array(
            'id' => $PAGE->course->id
        ));
        $completionreporttitle = get_string('coursecompletion', 'completion');
        $completionreportlink = new moodle_url('/report/completion/index.php', array(
            'course' => $PAGE->course->id
        ));
        // Questionbank.
        $qbanktitle = get_string('questionbank', 'question');
        $qbanklink = new moodle_url('/question/edit.php', array(
            'courseid' => $PAGE->course->id
        ));
        $qcattitle = get_string('questioncategory', 'question');
        $qcatlink = new moodle_url('/question/category.php', array(
            'courseid' => $PAGE->course->id
        ));
        $qimporttitle = get_string('import', 'question');
        $qimportlink = new moodle_url('/question/import.php', array(
            'courseid' => $PAGE->course->id
        ));
        $qexporttitle = get_string('export', 'question');
        $qexportlink = new moodle_url('/question/export.php', array(
            'courseid' => $PAGE->course->id
        ));
        // Content Bank.
        $cbankaddtitle = get_string('contentbank', 'moodle');
        $cbankaddlink = new moodle_url('/contentbank/index.php', array(
            'contextid' => $context->id
        ));
        // Manage course.
        $courseadmintitle = get_string('courseadministration', 'moodle');
        $courseadminlink = new moodle_url('/course/admin.php', array(
            'courseid' => $PAGE->course->id
        ));
        $coursecompletiontitle = get_string('editcoursecompletionsettings', 'completion');
        $coursecompletionlink = new moodle_url('/course/completion.php', array(
            'id' => $PAGE->course->id
        ));
        $competencytitle = get_string('competencies', 'competency');
        $competencyurl = new moodle_url('/admin/tool/lp/coursecompetencies.php', array(
            'courseid' => $PAGE->course->id
        ));
        $courseresettitle = get_string('reset', 'moodle');
        $courseresetlink = new moodle_url('/course/reset.php', array(
            'id' => $PAGE->course->id
        ));
        $coursebackuptitle = get_string('backup', 'moodle');
        $coursebackuplink = new moodle_url('/backup/backup.php', array(
            'id' => $PAGE->course->id
        ));
        $courserestoretitle = get_string('restore', 'moodle');
        $courserestorelink = new moodle_url('/backup/restorefile.php', array(
            'contextid' => $PAGE->context->id
        ));
        $courseimporttitle = get_string('import', 'moodle');
        $courseimportlink = new moodle_url('/backup/import.php', array(
            'id' => $PAGE->course->id
        ));
        $courseedittitle = get_string('editcoursesettings', 'moodle');
        $courseeditlink = new moodle_url('/course/edit.php', array(
            'id' => $PAGE->course->id
        ));
        $badgemanagetitle = get_string('managebadges', 'badges');
        $badgemanagelink = new moodle_url('/badges/index.php?type=2', array(
            'id' => $PAGE->course->id
        ));
        $badgeaddtitle = get_string('newbadge', 'badges');
        $badgeaddlink = new moodle_url('/badges/newbadge.php?type=2', array(
            'id' => $PAGE->course->id
        ));
        $recyclebintitle = get_string('pluginname', 'tool_recyclebin');
        $recyclebinlink = new moodle_url('/admin/tool/recyclebin/index.php', array(
            'contextid' => $PAGE->context->id
        ));
        $filtertitle = get_string('filtersettings', 'filters');
        $filterlink = new moodle_url('/filter/manage.php', array(
            'contextid' => $PAGE->context->id
        ));
        $eventmonitoringtitle = get_string('managesubscriptions', 'tool_monitor');
        $eventmonitoringlink = new moodle_url('/admin/tool/monitor/managerules.php', array(
            'courseid' => $PAGE->course->id
        ));
        $copycoursetitle = get_string('copycourse', 'moodle');
        $copycourselink = new moodle_url('/backup/copy.php', array(
            'id' => $PAGE->course->id
        ));

        // Student Dash
        if (\core_completion\progress::get_course_progress_percentage($PAGE->course)) {
            $comppc = \core_completion\progress::get_course_progress_percentage($PAGE->course);
            $comppercent = number_format($comppc, 0);
        }
        else {
            $comppercent = 0;
        }

        $progresschartcontext = ['progress' => $comppercent];
        $progress = $this->render_from_template('theme_schoollege/progress-bar', $progresschartcontext);

        $gradeslinkstudent = new moodle_url('/grade/report/user/index.php', array(
            'id' => $PAGE->course->id
        ));
        $hascourseinfogroup = array(
            'title' => get_string('courseinfo', 'theme_schoollege') ,
            'icon' => 'map'
        );
        $summary = $COURSE->summary;
        $courseinfo = array(
            array(
                'content' => $summary,
            )
        );
        $hascoursestaff = array(
            'title' => get_string('coursestaff', 'theme_schoollege') ,
            'icon' => 'users'
        );
        $courseteachers = array();
        $courseother = array();

        $showonlygroupteachers = !empty(groups_get_all_groups($course->id, $USER->id));
        if ($showonlygroupteachers) {
            $groupids = array();
            $studentgroups = groups_get_all_groups($course->id, $USER->id);
            foreach ($studentgroups as $grp) {
                $groupids[] = $grp->id;
            }
        }

        // If you created custom roles, please change the shortname value to match the name of your role.  This is teacher.
        $role = $DB->get_record('role', array(
            'shortname' => 'editingteacher'
        ));
        if ($role) {
            $context = context_course::instance($PAGE->course->id);
            $teachers = get_role_users($role->id, $context, false, 'u.id, u.firstname, u.middlename, u.lastname, u.alternatename,
                    u.firstnamephonetic, u.lastnamephonetic, u.email, u.picture, u.maildisplay,
                    u.imagealt');
            foreach ($teachers as $staff) {
                if ($showonlygroupteachers) {
                    $staffgroups = groups_get_all_groups($course->id, $staff->id);
                    $found = false;
                    foreach ($staffgroups as $grp) {
                        if (in_array($grp->id, $groupids)) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        continue;
                    }
                }
                $picture = $OUTPUT->user_picture($staff, array(
                    'size' => 50
                ));
                $messaging = new moodle_url('/message/index.php', array(
                    'id' => $staff->id
                ));
                $hasmessaging = $CFG->messaging == 1;
                $courseteachers[] = array(
                    'name' => $staff->firstname . ' ' . $staff->lastname . ' ' . $staff->alternatename,
                    'email' => $staff->email,
                    'picture' => $picture,
                    'messaging' => $messaging,
                    'hasmessaging' => $hasmessaging,
                    'hasemail' => $staff->maildisplay
                );
            }
        }

        // If you created custom roles, please change the shortname value to match the name of your role.  This is non-editing teacher.
        $role = $DB->get_record('role', array(
            'shortname' => 'teacher'
        ));
        if ($role) {
            $context = context_course::instance($PAGE->course->id);
            $teachers = get_role_users($role->id, $context, false, 'u.id, u.firstname, u.middlename, u.lastname, u.alternatename,
                    u.firstnamephonetic, u.lastnamephonetic, u.email, u.picture, u.maildisplay,
                    u.imagealt');
            foreach ($teachers as $staff) {
                if ($showonlygroupteachers) {
                    $staffgroups = groups_get_all_groups($course->id, $staff->id);
                    $found = false;
                    foreach ($staffgroups as $grp) {
                        if (in_array($grp->id, $groupids)) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$found) {
                        continue;
                    }
                }
                $picture = $OUTPUT->user_picture($staff, array(
                    'size' => 50
                ));
                $messaging = new moodle_url('/message/index.php', array(
                    'id' => $staff->id
                ));
                $hasmessaging = $CFG->messaging == 1;
                $courseother[] = array(
                    'name' => $staff->firstname . ' ' . $staff->lastname,
                    'email' => $staff->email,
                    'picture' => $picture,
                    'messaging' => $messaging,
                    'hasmessaging' => $hasmessaging,
                    'hasemail' => $staff->maildisplay
                );
            }
        }
        $cmnotetitle = get_string('cmnotetitle', 'theme_schoollege');
        $cmnotetitle_desc = get_string('cmnotetitle_desc', 'theme_schoollege');
        $mygradestext = get_string('mygradestext', 'theme_schoollege');
        $studentcoursemanage = get_string('courseadministration', 'moodle');
        // Permissionchecks for teacher access.

        $hasquestionpermission = has_capability('moodle/question:add', $context);
        $hasbadgepermission = has_capability('moodle/badges:awardbadge', $context) && $CFG->enablebadges == 1;
        $hascontentbankpermission = has_capability('contenttype/h5p:access', $context);
        $hasquestionandcontent = $hasquestionpermission && $hascontentbankpermission;
        $hascoursepermission = has_capability('moodle/backup:backupcourse', $context);
        $hasuserpermission = has_capability('moodle/course:viewhiddenactivities', $context);
        $hasgradebookshow = true;
        $hascompletionshow =  $PAGE->course->enablecompletion == 1;
        $hascourseadminshow = true;
        $hascompetency = get_config('core_competency', 'enabled');


        $cmnotestudent = (empty($PAGE->theme->settings->cmnotestudent)) ? false : format_text($PAGE->theme->settings->cmnotestudent);
        $cmnoteteacher = (empty($PAGE->theme->settings->cmnoteteacher)) ? false : format_text($PAGE->theme->settings->cmnoteteacher);
        // Send to template.

        $dashlinks = [
            'cmnoteteacher' => $cmnoteteacher,
            'cmnotestudent' => $cmnotestudent,
            'showincourseonly' => $showincourseonly, 
            'haspermission' => $haspermission, 
            'togglebutton' => $togglebutton,
            'togglebuttonstudent' => $togglebuttonstudent, 
            'userlinkstitle' => $userlinks, 
            'userlinksdesc' => $userlinksdesc, 
            'qbanktitle' => $qbank, 
            'cmnotetitle' => $cmnotetitle, 
            'cmnotetitle_desc' => $cmnotetitle_desc, 
            'qbankdesc' => $qbankdesc, 
            'cbanktitle' => $cbank,
            'cbankdesc' => $cbankdesc, 
            'badgestitle' => $badges, 
            'badgesdesc' => $badgesdesc, 
            'coursemanagetitle' => $coursemanage, 
            'coursemanagedesc' => $coursemanagedesc, 
            'progress' => $progress, 
            'gradeslink' => $gradeslink, 
            'gradeslinkstudent' => $gradeslinkstudent, 
            'hascourseinfogroup' => $hascourseinfogroup, 
            'courseinfo' => $courseinfo, 
            'hascoursestaffgroup' => $hascoursestaff, 
            'courseteachers' => $courseteachers, 
            'courseother' => $courseother, 
            'mygradestext' => $mygradestext, 
            'hasteacherdash' => $hasteacherdash, 
            'editcog'=> $editcog, 
            'teacherdash' => array(
                'hasquestionpermission' => $hasquestionpermission,
                'hasbadgepermission' => $hasbadgepermission,
                'hascoursepermission' => $hascoursepermission,
                'hasuserpermission' => $hasuserpermission,
                'hascontentbankpermission' => $hascontentbankpermission
            ) , 
            'hasstudentdash' => $hasstudentdash, 
            'hasgradebookshow' => $hasgradebookshow, 
            'hascompletionshow' => $hascompletionshow, 
            'studentcourseadminlink' => $courseadminlink, 
            'studentcoursemanage' => $studentcoursemanage, 
            'hascourseadminshow' => $hascourseadminshow, 
            'hascompetency' => $hascompetency, 
            'competencytitle' => $competencytitle, 
            'competencyurl' => $competencyurl, 
            'dashlinks' => array(
                array(
                    'hasuserlinks' => $gradebooktitle,
                    'title' => $gradebooktitle,
                    'url' => $gradebooklink
                ) ,
                array(
                    'hasuserlinks' => $participantstitle,
                    'title' => $participantstitle,
                    'url' => $participantslink
                ) ,
                array(
                    'hasuserlinks' => $grouptitle,
                    'title' => $grouptitle,
                    'url' => $grouplink
                ) ,
                array(
                    'hasuserlinks' => $enrolmethodtitle,
                    'title' => $enrolmethodtitle,
                    'url' => $enrolmethodlink
                ) ,
                array(
                    'hasuserlinks' => $activitycompletiontitle,
                    'title' => $activitycompletiontitle,
                    'url' => $activitycompletionlink
                ) ,
                array(
                    'hasuserlinks' => $completionreporttitle,
                    'title' => $completionreporttitle,
                    'url' => $completionreportlink
                ) ,
                array(
                    'hasuserlinks' => $logstitle,
                    'title' => $logstitle,
                    'url' => $logslink
                ) ,
                array(
                    'hasuserlinks' => $livelogstitle,
                    'title' => $livelogstitle,
                    'url' => $livelogslink
                ) ,
                array(
                    'hasuserlinks' => $participationtitle,
                    'title' => $participationtitle,
                    'url' => $participationlink
                ) ,
                array(
                    'hasuserlinks' => $activitytitle,
                    'title' => $activitytitle,
                    'url' => $activitylink
                ) ,
                array(
                    'hasqbanklinks' => $qbanktitle,
                    'title' => $qbanktitle,
                    'url' => $qbanklink
                ) ,
                array(
                    'hascbanklinks' => $cbankaddtitle,
                    'title' => $cbankaddtitle,
                    'url' => $cbankaddlink
                ) ,
                array(
                    'hasqbanklinks' => $qcattitle,
                    'title' => $qcattitle,
                    'url' => $qcatlink
                ) ,
                array(
                    'hasqbanklinks' => $qimporttitle,
                    'title' => $qimporttitle,
                    'url' => $qimportlink
                ) ,
                array(
                    'hasqbanklinks' => $qexporttitle,
                    'title' => $qexporttitle,
                    'url' => $qexportlink
                ) ,
                array(
                    'hascoursemanagelinks' => $courseedittitle,
                    'title' => $courseedittitle,
                    'url' => $courseeditlink
                ) ,
                array(
                    'hascoursemanagelinks' => $gradestitle,
                    'title' => $gradestitle,
                    'url' => $gradeslink
                ) ,
                array(
                    'hascoursemanagelinks' => $coursecompletiontitle,
                    'title' => $coursecompletiontitle,
                    'url' => $coursecompletionlink
                ) ,
                array(
                    'hascoursemanagelinks' => $hascompetency,
                    'title' => $competencytitle,
                    'url' => $competencyurl
                ) ,
                array(
                    'hascoursemanagelinks' => $courseadmintitle,
                    'title' => $courseadmintitle,
                    'url' => $courseadminlink
                ) ,
                array(
                    'hascoursemanagelinks' => $copycoursetitle,
                    'title' => $copycoursetitle,
                    'url' => $copycourselink
                ) ,
                array(
                    'hascoursemanagelinks' => $courseresettitle,
                    'title' => $courseresettitle,
                    'url' => $courseresetlink
                ) ,
                array(
                    'hascoursemanagelinks' => $coursebackuptitle,
                    'title' => $coursebackuptitle,
                    'url' => $coursebackuplink
                ) ,
                array(
                    'hascoursemanagelinks' => $courserestoretitle,
                    'title' => $courserestoretitle,
                    'url' => $courserestorelink
                ) ,
                array(
                    'hascoursemanagelinks' => $courseimporttitle,
                    'title' => $courseimporttitle,
                    'url' => $courseimportlink
                ) ,
                array(
                    'hascoursemanagelinks' => $recyclebintitle,
                    'title' => $recyclebintitle,
                    'url' => $recyclebinlink
                ) ,
                array(
                    'hascoursemanagelinks' => $filtertitle,
                    'title' => $filtertitle,
                    'url' => $filterlink
                ) ,
                array(
                    'hascoursemanagelinks' => $eventmonitoringtitle,
                    'title' => $eventmonitoringtitle,
                    'url' => $eventmonitoringlink
                ) ,
                array(
                    'hasbadgelinks' => $badgemanagetitle,
                    'title' => $badgemanagetitle,
                    'url' => $badgemanagelink
                ) ,
                array(
                    'hasbadgelinks' => $badgeaddtitle,
                    'title' => $badgeaddtitle,
                    'url' => $badgeaddlink
                ) ,
            ) ,
            ];
        // Attach easy enrollment links if active.
        if ($globalhaseasyenrollment && $coursehaseasyenrollment) {
            $dashlinks['dashlinks'][] = array(
                'haseasyenrollment' => $coursehaseasyenrollment,
                'title' => $easycodetitle,
                'url' => $easycodelink
            );
        }
        return $this->render_from_template('theme_schoollege/teacherdash', $dashlinks);
    }

    public function headerimage() {
        global $CFG, $COURSE, $PAGE, $OUTPUT;
        // Get course overview files.
        if (empty($CFG->courseoverviewfileslimit)) {
            return '';
        }
        require_once ($CFG->libdir . '/filestorage/file_storage.php');
        require_once ($CFG->dirroot . '/course/lib.php');

        $fs = get_file_storage();
        $context = context_course::instance($COURSE->id);
        $files = $fs->get_area_files($context->id, 'course', 'overviewfiles', false, 'filename', false);
        if (count($files)) {
            $overviewfilesoptions = course_overviewfiles_options($COURSE->id);
            $acceptedtypes = $overviewfilesoptions['accepted_types'];
            if ($acceptedtypes !== '*') {
                // Filter only files with allowed extensions.
                require_once ($CFG->libdir . '/filelib.php');
                foreach ($files as $key => $file) {
                    if (!file_extension_in_typegroup($file->get_filename() , $acceptedtypes)) {
                        unset($files[$key]);
                    }
                }
            }
            if (count($files) > $CFG->courseoverviewfileslimit) {
                // Return no more than $CFG->courseoverviewfileslimit files.
                $files = array_slice($files, 0, $CFG->courseoverviewfileslimit, true);
            }
        }
        // Get course overview files as images - set $courseimage.
        // The loop means that the LAST stored image will be the one displayed if >1 image file.
        $courseimage = '';
        foreach ($files as $file) {
            $isimage = $file->is_valid_image();
            if ($isimage) {
                $courseimage = file_encode_url("$CFG->wwwroot/pluginfile.php", '/' . $file->get_contextid() . '/' . $file->get_component() . '/' . $file->get_filearea() . $file->get_filepath() . $file->get_filename() , !$isimage);
            }
        }
        
        $allowheader = $PAGE->theme->settings->showheaderimages ? true : false;
        $headerbg = $PAGE->theme->setting_file_url('defaultbackgroundimage', 'defaultbackgroundimage');
        $headerbgimgurl = $PAGE->theme->setting_file_url('defaultbackgroundimage', 'defaultbackgroundimage', true);
        $defaultimgurl = $OUTPUT->image_url('defaultbackgroundimage', 'theme');
        $coursetilebg = $PAGE->theme->setting_file_url('coursetilebg', 'coursetilebg');
        $coursetilebgimgurl = $PAGE->theme->setting_file_url('coursetilebg', 'coursetilebg', true);

        // Create html for header.
        $html = html_writer::start_div('headerbkg');
        // If course image display it in separate div to allow css styling of inline style.
        if ($courseimage && $allowheader) {
            $html .= html_writer::start_div('courseimage', array(
                'style' => 'background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("' . $courseimage . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End withimage inline style div.
        } else if (!$courseimage && isset($headerbg) && $COURSE->id <= 1 && $allowheader ) {
            $html .= html_writer::start_div('customimage', array(
                'style' => 'background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("' . $headerbgimgurl .  '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End withoutimage inline style div.
        } else if (!$courseimage && isset($coursetilebg) && $COURSE->id > 1 && $allowheader) {
            $html .= html_writer::start_div('customimagetilebg', array(
                'style' => 'background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("' . $coursetilebgimgurl .  '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End withoutimage inline style div.
        } else if ($COURSE->id > 1 && $allowheader ) {
            $html .= html_writer::start_div('default', array(
                'style' => 'background-image: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url("' . $defaultimgurl . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End default inline style div.
        }
        $html .= html_writer::end_div();
        return $html;
    }

    public function get_generated_image_for_id($id) {
        global $OUTPUT, $PAGE;
        //see if user uploaded a custom header background to the theme
        // $headerbg = $PAGE->theme->setting_file_url('defaultbackgroundimage', 'defaultbackgroundimage');
        //use the default theme image when no course image is detected
        $defaultimgurl = $OUTPUT->image_url('noimg', 'theme')->out();
        //See if custom course tile image is used.
        $coursetilebg = $PAGE->theme->setting_file_url('coursetilebg', 'coursetilebg');

        if (isset($coursetilebg)) {
            return $coursetilebg;
        } else {
            return $defaultimgurl;
        }
    }

    public function footnote() {
        global $PAGE;
        $footnote = '';
        $footnote = (empty($PAGE->theme->settings->footnote)) ? false : format_text($PAGE->theme->settings->footnote);
        return $footnote;
    }

    public function brandorganization_footer() {
        $theme = theme_config::load('schoollege');
        $setting = format_string($theme->settings->brandorganization);
        return $setting != '' ? $setting : '';
    }
    public function brandwebsite_footer() {
        $theme = theme_config::load('schoollege');
        $setting = $theme->settings->brandwebsite;
        return $setting != '' ? $setting : '';
    }
    public function brandphone_footer() {
        $theme = theme_config::load('schoollege');
        $setting = $theme->settings->brandphone;
        return $setting != '' ? $setting : '';
    }
    public function brandemail_footer() {
        $theme = theme_config::load('schoollege');
        $setting = $theme->settings->brandemail;
        return $setting != '' ? $setting : '';
    }
    public function brandlogo() {
        $theme = theme_config::load('schoollege');
        $setting = $theme->setting_file_url('brandlogo', 'brandlogo', true);
        return $setting != '' ? $setting : '';
    }
    public function dashboardtextbox() {
        $theme = theme_config::load('schoollege');
        $setting = format_text($theme->settings->dashboardtextbox);
        return $setting != '' ? $setting : '';
    }

    public function fpicons() {
        global $PAGE;
        $context = $this->page->context;

        $hasslideicon = (empty($PAGE->theme->settings->slideicon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->slideicon;
        $slideiconbuttonurl = 'data-toggle="collapse" data-target="#collapseExample';
        $slideiconbuttontext = (empty($PAGE->theme->settings->slideiconbuttontext)) ? false : format_string($PAGE->theme->settings->slideiconbuttontext);
        $hasnav1icon = (empty($PAGE->theme->settings->nav1icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav1icon;
        $hasnav2icon = (empty($PAGE->theme->settings->nav2icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav2icon;
        $hasnav3icon = (empty($PAGE->theme->settings->nav3icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav3icon;
        $hasnav4icon = (empty($PAGE->theme->settings->nav4icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav4icon;
        $hasnav5icon = (empty($PAGE->theme->settings->nav5icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav5icon;
        $hasnav6icon = (empty($PAGE->theme->settings->nav6icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav6icon;
        $hasnav7icon = (empty($PAGE->theme->settings->nav7icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav7icon;
        $hasnav8icon = (empty($PAGE->theme->settings->nav8icon && isloggedin() && !isguestuser())) ? false : $PAGE->theme->settings->nav8icon;
        $nav1buttonurl = (empty($PAGE->theme->settings->nav1buttonurl)) ? false : $PAGE->theme->settings->nav1buttonurl;
        $nav2buttonurl = (empty($PAGE->theme->settings->nav2buttonurl)) ? false : $PAGE->theme->settings->nav2buttonurl;
        $nav3buttonurl = (empty($PAGE->theme->settings->nav3buttonurl)) ? false : $PAGE->theme->settings->nav3buttonurl;
        $nav4buttonurl = (empty($PAGE->theme->settings->nav4buttonurl)) ? false : $PAGE->theme->settings->nav4buttonurl;
        $nav5buttonurl = (empty($PAGE->theme->settings->nav5buttonurl)) ? false : $PAGE->theme->settings->nav5buttonurl;
        $nav6buttonurl = (empty($PAGE->theme->settings->nav6buttonurl)) ? false : $PAGE->theme->settings->nav6buttonurl;
        $nav7buttonurl = (empty($PAGE->theme->settings->nav7buttonurl)) ? false : $PAGE->theme->settings->nav7buttonurl;
        $nav8buttonurl = (empty($PAGE->theme->settings->nav8buttonurl)) ? false : $PAGE->theme->settings->nav8buttonurl;
        $nav1buttontext = (empty($PAGE->theme->settings->nav1buttontext)) ? false : format_string($PAGE->theme->settings->nav1buttontext);
        $nav2buttontext = (empty($PAGE->theme->settings->nav2buttontext)) ? false : format_string($PAGE->theme->settings->nav2buttontext);
        $nav3buttontext = (empty($PAGE->theme->settings->nav3buttontext)) ? false : format_string($PAGE->theme->settings->nav3buttontext);
        $nav4buttontext = (empty($PAGE->theme->settings->nav4buttontext)) ? false : format_string($PAGE->theme->settings->nav4buttontext);
        $nav5buttontext = (empty($PAGE->theme->settings->nav5buttontext)) ? false : format_string($PAGE->theme->settings->nav5buttontext);
        $nav6buttontext = (empty($PAGE->theme->settings->nav6buttontext)) ? false : format_string($PAGE->theme->settings->nav6buttontext);
        $nav7buttontext = (empty($PAGE->theme->settings->nav7buttontext)) ? false : format_string($PAGE->theme->settings->nav7buttontext);
        $nav8buttontext = (empty($PAGE->theme->settings->nav8buttontext)) ? false : format_string($PAGE->theme->settings->nav8buttontext);
        $nav1target = (empty($PAGE->theme->settings->nav1target)) ? false : $PAGE->theme->settings->nav1target;
        $nav2target = (empty($PAGE->theme->settings->nav2target)) ? false : $PAGE->theme->settings->nav2target;
        $nav3target = (empty($PAGE->theme->settings->nav3target)) ? false : $PAGE->theme->settings->nav3target;
        $nav4target = (empty($PAGE->theme->settings->nav4target)) ? false : $PAGE->theme->settings->nav4target;
        $nav5target = (empty($PAGE->theme->settings->nav5target)) ? false : $PAGE->theme->settings->nav5target;
        $nav6target = (empty($PAGE->theme->settings->nav6target)) ? false : $PAGE->theme->settings->nav6target;
        $nav7target = (empty($PAGE->theme->settings->nav7target)) ? false : $PAGE->theme->settings->nav7target;
        $nav8target = (empty($PAGE->theme->settings->nav8target)) ? false : $PAGE->theme->settings->nav8target;
        $slidetextbox = (empty($PAGE->theme->settings->slidetextbox && isloggedin())) ? false : format_text($PAGE->theme->settings->slidetextbox, FORMAT_HTML, array(
            'noclean' => true
        ));

        $fp_icons = [
            'hasslidetextbox' => (!empty($PAGE->theme->settings->slidetextbox && isloggedin())) , 
            'slidetextbox' => $slidetextbox, 'hasfptextboxlogout' => !isloggedin(),
            'hasfpiconnav' => ($hasnav1icon || $hasnav2icon || $hasnav3icon || $hasnav4icon || $hasnav5icon || $hasnav6icon || $hasnav7icon || $hasnav8icon || $hasslideicon) ? true : false, 
            'fpiconnav' => array(
                array(
                    'hasicon' => $hasnav1icon,
                    'linkicon' => $hasnav1icon,
                    'link' => $nav1buttonurl,
                    'linktext' => $nav1buttontext,
                    'linktarget' => $nav1target
                ) ,
                array(
                    'hasicon' => $hasnav2icon,
                    'linkicon' => $hasnav2icon,
                    'link' => $nav2buttonurl,
                    'linktext' => $nav2buttontext,
                    'linktarget' => $nav2target
                ) ,
                array(
                    'hasicon' => $hasnav3icon,
                    'linkicon' => $hasnav3icon,
                    'link' => $nav3buttonurl,
                    'linktext' => $nav3buttontext,
                    'linktarget' => $nav3target
                ) ,
                array(
                    'hasicon' => $hasnav4icon,
                    'linkicon' => $hasnav4icon,
                    'link' => $nav4buttonurl,
                    'linktext' => $nav4buttontext,
                    'linktarget' => $nav4target
                ) ,
                array(
                    'hasicon' => $hasnav5icon,
                    'linkicon' => $hasnav5icon,
                    'link' => $nav5buttonurl,
                    'linktext' => $nav5buttontext,
                    'linktarget' => $nav5target
                ) ,
                array(
                    'hasicon' => $hasnav6icon,
                    'linkicon' => $hasnav6icon,
                    'link' => $nav6buttonurl,
                    'linktext' => $nav6buttontext,
                    'linktarget' => $nav6target
                ) ,
                array(
                    'hasicon' => $hasnav7icon,
                    'linkicon' => $hasnav7icon,
                    'link' => $nav7buttonurl,
                    'linktext' => $nav7buttontext,
                    'linktarget' => $nav7target
                ) ,
                array(
                    'hasicon' => $hasnav8icon,
                    'linkicon' => $hasnav8icon,
                    'link' => $nav8buttonurl,
                    'linktext' => $nav8buttontext,
                    'linktarget' => $nav8target
                ) ,
            ) ,
            'fpslideicon' => array(
                array(
                    'hasicon' => $hasslideicon,
                    'linkicon' => $hasslideicon,
                    'link' => $slideiconbuttonurl,
                    'linktext' => $slideiconbuttontext
                ) ,
            ) , 
        ];

        return $this->render_from_template('theme_schoollege/fpicons', $fp_icons);

    }


}

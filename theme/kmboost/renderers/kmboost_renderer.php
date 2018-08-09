<?php
// This file is part of the kmboost theme for Moodle
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

defined('MOODLE_INTERNAL') || die();

/**
 * Theme kmboost widget renderers file.
 *
 * @package    theme_kmboost
 * @copyright  2015 Bas Brands
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class theme_kmboost_widgets_renderer extends plugin_renderer_base {

    private $theme;
    private $settings;

    /**
     * The widget renderer for theme kmboost generates the additional features this
     * theme contains which are non-standard in Moodle. It renders banners, marketing spots,
     * message menus and more.
     * For each of the public methods used in this class there is a Mustache template
     * located in the /templates folder
     */

    public function __construct(moodle_page $page, $target) {
        $this->theme = theme_config::load('kmboost');
        $this->settings = $this->theme->settings;
        parent::__construct($page, $target);
    }
    
    /**
     * Renders the slideshow of images on the frontpage
     */
    public function banner($hasbanner) {
        if (!$hasbanner) {
            return '';
        }

        $theme = $this->theme;
        $settings = $this->settings;

        $slidenum = $settings->slidenumber;

        if ($slidenum == 0) {
            return '';
        }

        switch ($settings->togglebanner) {
            case 1:
                break;
            case 2:
                if (isloggedin()) {
                    return '';
                }
                break;
            case 3:
                if (!isloggedin()) {
                    return '';
                }
                break;
            case 4:
                return '';
                break;
        }

        $template = new stdClass();

        $template->slidespeed = $settings->slidespeed;
        $banners = array();
        $count = 0;
        foreach (range(1, $slidenum) as $bannernumber) {
            $banner = new stdClass();
            $banner->active = '';
            $banner->count = $count++;
            $enablebanner = 'enablebanner' . $bannernumber;
            $banner->enablebanner = (!empty($settings->$enablebanner));

            $bannerimage = 'bannerimage' . $bannernumber;
            $bannerimageset = (!empty($settings->$bannerimage));
            if ($bannerimageset) {
                $banner->bannerimage = $theme->setting_file_url($bannerimage, $bannerimage);
            }

            $bannertitle = 'bannertitle' . $bannernumber;
            if (!empty($settings->$bannertitle)) {
                $banner->bannertitle = $settings->$bannertitle;
            } else {
                $banner->bannertitle = false;
            }

            $bannertext = 'bannertext' . $bannernumber;
            if (!empty($settings->$bannertext)) {
                $banner->bannertext = $settings->$bannertext;
            } else {
                $banner->bannertext = false;
            }

            $bannerurl = 'bannerurl' . $bannernumber;
            if (!empty($settings->$bannerurl)) {
                $banner->bannerurl = $settings->$bannerurl;
            } else {
                $banner->bannerurl = false;
            }

            $bannercolor = 'bannercolor' . $bannernumber;
            if (!empty($settings->$bannercolor)) {
                $banner->bannercolor = $settings->$bannercolor;
            } else {
                $banner->bannercolor = "transparent";
            }

            $bannerlinktext = 'bannerlinktext' . $bannernumber;
            if (!empty($settings->$bannerlinktext)) {
                $banner->bannerlinktext = $settings->$bannerlinktext;
            } else {
                $banner->bannerlinktext = false;
            }

            $bannerlinkurl = 'bannerlinkurl' . $bannernumber;
            if (!empty($settings->$bannerlinkurl)) {
                $banner->bannerlinkurl = $settings->$bannerlinkurl;
            } else {
                $banner->bannerlinkurl = false;
            }
            $banners[] = $banner;
        }
        $banners[0]->active = 'active';
        $template->banners = $banners;

        return $this->render_from_template('theme_kmboost/banner', $template);
    }

    /**
     * Renders the marketing spots on the frontpage
     */
    public function marketing_spots($hasmarketing, $hassidemiddle) {
        global $OUTPUT;
        if (!$hasmarketing) {
            return '';
        }

        $blocksmiddle = $OUTPUT->blocks('side-middle');

        $theme = $this->theme;
        $settings = $this->settings;

        switch ($settings->togglemarketing) {
            case 1:
                break;
            case 2:
                if (isloggedin()) {
                    return '';
                }
                break;
            case 3:
                if (!isloggedin()) {
                    return '';
                }
                break;
            case 4:
                return '';
                break;
        }

        $spotsnr = $settings->marketingspotsnr;

        if ($spotsnr == 0) {
            return '';
        }

        $template = new stdClass();
        $template->spots = array();
        $template->title = '';
        $template->marketingtitletitleicon = '';
        if ($hassidemiddle) {
            $template->blocksmiddle = $blocksmiddle;
        }
        if (!empty($settings->marketingtitle)) {
            $template->marketingtitle = $settings->marketingtitle;

        }
        if (!empty($settings->marketingtitleicon)) {
            $template->marketingtitleicon = $settings->marketingtitleicon;
        }

        $choices = array();
        $choices[1] = 'col-sm-6 col-md-6';//2;
        $choices[2] = 'col-xs-6 col-sm-4 col-md-3';//4
        $choices[3] = 'col-xs-6 col-sm-3 col-md-2';//6;
        $choices[4] = 'col-xs-6 col-sm-3 col-md-2 col-lg-1';//12;

        if (!empty($settings->marketingspotsinrow)) {
            $template->spotclass = $choices[$settings->marketingspotsinrow];
        } else {
            $template->spotclass = $choices[2];
        }

        foreach (range(1, $spotsnr) as $spot) {
            $title = 'marketingtitle' . $spot;
            $icon = 'marketingicon' . $spot;
            $content = 'marketingcontent' . $spot;
            $url = 'marketingurl' . $spot;

            $marketingspot = new stdClass();
            if (!empty($settings->$title)) {
                $marketingspot->title = $settings->$title;
            }
            if (!empty($settings->$icon)) {
                $marketingspot->icon = $settings->$icon;
            }
            if (!empty($settings->$content)) {
                $marketingspot->content = $settings->$content;
            }
            if (!empty($settings->$url)) {
                $marketingspot->url = $settings->$url;
            }
            $template->spots[] = $marketingspot;
        } 

        return $this->render_from_template('theme_kmboost/marketingspots', $template);
    }

    /**
     * Renders the text area in the bottom left of the Footer
     */
    public function footerleft($hasfooter) {
        if (!$hasfooter) {
            return '';
        }
        $theme = $this->theme;
        $settings = $this->settings;

        $template = new stdClass();
        $template->footnote = $settings->footnote;

        return $this->render_from_template('theme_kmboost/footerleft', $template);
    }

    /**
     * Renders the social icons in the bottom right of the Footer
     */
    public function footerright($hasfooter) {
        if (!$hasfooter) {
            return '';
        }
        $theme = $this->theme;
        $settings = $this->settings;

        $template = new stdClass();

        $socialoptions = array('ios','android','windows','winphone','facebook', 'twitter', 'googleplus', 'linkedin', 'youtube', 'flickr', 'vk', 'pinterest',
            'instagram', 'skype', 'website', 'blog', 'vimeo', 'tumblr');

        foreach ($socialoptions as $so) {
            if (!empty($settings->$so)) {
                $template->$so = $settings->$so;
                $template->hasicons = true;
            }
        }
        return $this->render_from_template('theme_kmboost/footerright', $template);
    }

    /**
     * Renders rows of links just above the content area on the front page
     */
    public function quicklinks($hasquicklinks) {
        if (!$hasquicklinks) {
            return '';
        }

        $settings = $this->settings;

        switch ($settings->togglequicklinks) {
            case 1:
                break;
            case 2:
                if (isloggedin()) {
                    return '';
                }
                break;
            case 3:
                if (!isloggedin()) {
                    return '';
                }
                break;
            case 4:
                return '';
                break;
        }

        $template = new stdClass();

        $template->quicklinksicon = $settings->quicklinksicon;
        $template->quicklinkstitle = $settings->quicklinkstitle;
        $quicklinksnumber = $settings->quicklinksnumber;

        if ($quicklinksnumber == 0) {
            return '';
        }
        $template->quicklinks = array();
        foreach (range(1, $quicklinksnumber ) as $i) {
            $icon = 'quicklinkicon' . $i;
            $buttontext = 'quicklinkbuttontext' . $i;
            $url = 'quicklinkbuttonurl' . $i;
            $iconclass = 'quicklinkiconcolor' . $i;
            $buttonclass = 'quicklinkbuttoncolor' . $i;

            $quicklink = new stdClass();

            if (!empty($settings->$icon)) {
                $quicklink->icon = $settings->$icon;
            } else {
                $quicklink->icon = 'check';
            }
            if (!empty($settings->$buttontext)) {
                $quicklink->buttontext = $settings->$buttontext;
            } else {
                $quicklink->buttontext = 'Click here';
            }
            if (!empty($settings->$url)) {
                $quicklink->url = $settings->$url;
            }
            $quicklink->iconclass = $iconclass;
            $quicklink->buttonclass = $buttonclass;
            $template->quicklinks[] = $quicklink;
        }
        
        $count = count($template->quicklinks);

        if ($count < 4) {
            $template->classlarge = 'col-lg-' . (12 / $count);
        } else {
            $template->classlarge = 'col-lg-3';
        }
        if ($count < 3) {
            $template->classmedium = 'col-md-' . (12 / $count);
        } else {
            $template->classmedium = 'col-md-4';
        }
   
        return $this->render_from_template('theme_kmboost/quicklinks', $template);
    }

    /**
     * This addes the hidden blocks (only viewable by admins) under the content area
     */
    public function hiddenblocks() {
        global $OUTPUT;

        if (!is_siteadmin()) {
            return '';
        }

        $template = new stdClass();
        $template->blocks = $OUTPUT->blocks('hidden-dock');

        return $this->render_from_template('theme_kmboost/hiddenblocks', $template);
    }

    /**
     * Renders your custom content on the frontpage. Very simple, very boring.
     */
    public function frontpage_content($hasfrontpagecontent) {
        if (!$hasfrontpagecontent) {
            return '';
        }
        $template = new stdClass();

        $settings = $this->settings;

        $template->frontpagetext = $settings->frontpagecontent;

        return $this->render_from_template('theme_kmboost/frontpagetext', $template);
    }

    /**
     * The renders to top navigation bar. Parts of this are rendered in core_renderer.php too.
     */
    public function navbar($hasnavbar) {
        global $USER;

        if (!$hasnavbar) {
            return '';
        }

        global $OUTPUT, $CFG, $SITE;
        $template = new stdClass();
        
        $settings = $this->settings;

        $template->homeurl = $CFG->wwwroot;

        if ($settings->invert) {
            $template->navbartype = 'navbar-inverse';
        } else {
            $template->navbartype = 'navbar-default';
        }

        if ($settings->logo) {
            $template->logo = '<div id="logo"></div>';
        } else {
            $template->logo = $SITE->shortname;
        }

        if (!empty($settings->fixednavbar)) {
            $template->navbartype .= ' navbar-fixed-top';
        }

        if (!during_initial_install()) {

            $usermenu = $OUTPUT->user_menu();

            if (!empty($usermenu)) {
                $template->usermenu = $usermenu;
            }

            $custommenu = $OUTPUT->custom_menu();

            $template->hascustom = false;
            if (strlen($custommenu) > 0) {
                $template->custommenu = $custommenu;
                $template->hascustom = true;
            }

            $headingmenu = $OUTPUT->page_heading_menu();

            if (!empty($headingmenu)) {
                echo 'headingmenu';
                $template->headingmenu = $headingmenu;
                $template->hascustom = true;
            }

            $messagemenu = $this->message_menu();

            if (!empty($messagemenu)) {
                $template->messagemenu = $messagemenu;
            }
            
        }
       
        if(!empty($USER->id) && $USER->username != "guest") {
            $template->showuser = true;
        }
        
        $template->hometitle = get_string('home');
        $template->mycoursestitle = get_string('mycourses');
        $template->aboutustitle = get_string('aboutus', 'theme_kmboost');
        $template->techsupporttitle = get_string('techsupport', 'theme_kmboost');
        $template->availabilitytitle = get_string('availability', 'theme_kmboost');
        
        $template->mycourselink = $CFG->wwwroot."/my/";
        return $this->render_from_template('theme_kmboost/navbar', $template);
    }
    
    /**
     * Render "my courses" carousel in the frontpage
     */
    public function my_courses_slick($hascoursesslick) {
        global $OUTPUT, $DB, $USER;
        
        if(!$hascoursesslick || empty($USER->id) || $USER->username == "guest") {
            return '';
        }
        
       $template = new stdClass();
       $courses = enrol_get_all_users_courses($USER->id, true, null, 'visible DESC, sortorder ASC');
       /* $courses = $DB->get_fieldset_sql('SELECT enrol.courseid FROM mdl_enrol enrol, mdl_user_enrolments enrolment WHERE enrolment.enrolid = enrol.id AND enrolment.userid = ?', array($USER->id)); */
       
       $template->courses = array();
       foreach ($courses as $course) {
           $currcourse = $DB->get_record('course', array("id"=>$course->id));
           $currcoursecon = context_course::instance($currcourse->id);
           
           $tmp = new stdClass();
           $tmp->title = format_string($currcourse->fullname);
           $tmp->id = $currcourse->id;
           
           if($image = $DB->get_record_sql("SELECT * FROM {files} WHERE contextid = ? AND component LIKE 'course' AND filearea LIKE 'overviewfiles' AND itemid = ? AND filename NOT LIKE '.'", array($currcoursecon->id, 0))) {
               
               $fs = get_file_storage();
               $file = $fs->get_file($currcoursecon->id, 'course', 'overviewfiles', 0, '/', $image->filename);
               
               $tmp->img = moodle_url::make_pluginfile_url(
             $file->get_contextid(),
             $file->get_component(),
             $file->get_filearea(),
             '',
             $file->get_filepath(),
             $file->get_filename()
        );              
           }
           
           $template->courses[] = $tmp;
            
       }
       
       $template->leftbtn = $OUTPUT->pix_url('icons/left', 'theme_kmboost');
       $template->rightbtn = $OUTPUT->pix_url('icons/right', 'theme_kmboost');

	if (!empty($template->courses)){
       		return $this->render_from_template('theme_kmboost/mycourses', $template);
	}else{
		return '';
	}

    }
    
    /**
     * Render "my courses" carousel in the frontpage
     */
    public function login_overlay($hasloginoverlay) {
        global $USER, $OUTPUT, $DB, $PAGE;
        
        $url = $PAGE->url;
        
        if(!$hasloginoverlay || (!empty($USER->id) && $USER->username != "guest") || $url->get_path() == '/local/customlogin/view.php') {
            return '';
        }
        
        
        $template = new stdClass();
        
        $template->img = $OUTPUT->pix_url('icons/login', 'theme_kmboost');
        $template->loginbtntxt = get_string('loginbtn', 'local_customlogin');
        $template->loginasguest = get_string('loginasguest', 'local_customlogin');
        $template->username = 'guest';
        $template->password = 'guest';
        
        $template->techtxt = get_string('loginsupport', 'local_customlogin');
        
        return $this->render_from_template('theme_kmboost/loginoverlay', $template);
        
     
    }

    /**
     * Render the page breadcrumb including the breadcrumb button.
     */ 
    public function breadcrumb($hasbreadcrumb) {
        global $OUTPUT;

        if (!$hasbreadcrumb) {
            return '';
        }

        $template = new stdClass();
        $settings = $this->settings;

        $items = $this->page->navbar->get_items();

        $template->hascrumbs = false;
        $template->breadcrumbs = array();
        $template->addclasses = ' m-t-5';
        if (!empty($settings->bodybg)) {
            $template->addclasses = 'bg-white p-5 eboxshadow m-t-5';
        }
        $numitems = count($items);
        $cnt = 0;
        foreach ($items as $item) {
            $crumb = new stdClass();
            $cnt++;
            $addclass = '';
            if ($cnt == $numitems) {
                $addclass = 'active';
            }
            
            $item->hideicon = true;
            $crumb->item = $OUTPUT->render($item);
            $crumb->class = $addclass;
            $template->breadcrumbs[] = $crumb;
            $template->hascrumbs = true;
        }
        if ($template->button = $this->page_heading_button()) {
            $template->hascrumbs = true;
        }

        return $this->render_from_template('theme_kmboost/breadcrumb', $template);
    }

    /**
     * Adds a simple message menu to the page navbar.
     */
    private function message_menu() {
        global $USER, $PAGE, $CFG;

        if (!isloggedin() || isguestuser()) {
            return false;
        }
        // Check to see if messaging is enabled.
        if(!$CFG->messaging) {
            return false;
        }

        // Changed from $OUTPUT -> bsrender because of bug when selecting this theme
        // for the first time.
        $bsrender = $PAGE->get_renderer('theme_kmboost', 'core');

        $menu = new custom_menu();

        $messages = $this->get_user_messages();
        $messagecount = 0;
        foreach ($messages as $message) {
            if (!$message->from) { // Workaround for issue #103.
                continue;
            }
            $messagecount++;
        }
        if ($messagecount == 0) {
             $messagemenutext = ' <i class="glyphicon glyphicon-inbox"></i>';
        } else {
             $messagemenutext = ' <i class="glyphicon glyphicon-envelope"></i>';
        }
        $messagemenu = $menu->add(
            $messagemenutext,
            new moodle_url('/message/index.php', array('viewing' => 'recentconversations')),
            get_string('messages', 'message'),
            9999
        );
        foreach ($messages as $message) {
            if (!$message->from) { // Workaround for issue #103.
                continue;
            }
            $senderpicture = new user_picture($message->from);
            $senderpicture->link = false;
            $senderpicture = $bsrender->render($senderpicture);

            $messagecontent = $senderpicture;
            $messagecontent .= html_writer::start_span('msg-body');
            $messagecontent .= html_writer::start_span('msg-title');
            $messagecontent .= html_writer::span($message->from->firstname . ': ', 'msg-sender');
            $messagecontent .= $message->text;
            $messagecontent .= html_writer::end_span();
            $messagecontent .= html_writer::start_span('msg-time');
            $messagecontent .= html_writer::tag('i', '', array('class' => 'icon-time'));
            $messagecontent .= html_writer::span($message->date);
            $messagecontent .= html_writer::end_span();

            $messageurl = new moodle_url('/message/index.php', array('user1' => $USER->id, 'user2' => $message->from->id));
            $messagemenu->add($messagecontent, $messageurl, $message->text);
        }
        $content = '';
        foreach ($menu->get_children() as $item) {
            $content .= $bsrender->render_custom_menu_item($item, 1);
        }
        return $content;
    }

    /**
     * Gets the user messages used by the message_menu() function
     */
    protected function get_user_messages() {
        global $USER, $DB;
        $messagelist = array();

        $newmessagesql = "SELECT id, smallmessage, useridfrom, useridto, timecreated, fullmessageformat, notification
                            FROM {message}
                           WHERE useridto = :userid";

        $newmessages = $DB->get_records_sql($newmessagesql, array('userid' => $USER->id));

        foreach ($newmessages as $message) {
            $messagelist[] = $this->process_message($message);
        }

        $showoldmessages = (empty($this->page->theme->settings->showoldmessages)) ? 0 : $this->page->theme->settings->showoldmessages;
        if ($showoldmessages) {
            $maxmessages = 5;
            $readmessagesql = "SELECT id, smallmessage, useridfrom, useridto, timecreated, fullmessageformat, notification
                                 FROM {message_read}
                                WHERE useridto = :userid
                             ORDER BY timecreated DESC
                                LIMIT $maxmessages";

            $readmessages = $DB->get_records_sql($readmessagesql, array('userid' => $USER->id));

            foreach ($readmessages as $message) {
                $messagelist[] = $this->process_message($message);
            }
        }

        return $messagelist;
    }

    /**
     * Filters the messages, cleans them, link them to a user, filter old messages for messages
     * used in the message_menu() function.
     */
    protected function process_message($message) {
        global $DB;
        $messagecontent = new stdClass();

        if ($message->notification) {
            $messagecontent->text = get_string('unreadnewnotification', 'theme_kmboost');
        } else {
            if ($message->fullmessageformat == FORMAT_HTML) {
                $message->smallmessage = html_to_text($message->smallmessage);
            }
            if (core_text::strlen($message->smallmessage) > 15) {
                $messagecontent->text = core_text::substr($message->smallmessage, 0, 15).'...';
            } else {
                $messagecontent->text = $message->smallmessage;
            }
        }

        if ((time() - $message->timecreated ) <= (3600 * 3)) {
            $messagecontent->date = format_time(time() - $message->timecreated);
        } else {
            $messagecontent->date = userdate($message->timecreated, get_string('strftimedate', 'langconfig'));
        }

        $messagecontent->from = $DB->get_record('user', array('id' => $message->useridfrom));
        return $messagecontent;
    }
}

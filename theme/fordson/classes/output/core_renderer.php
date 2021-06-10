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
namespace theme_fordson\output;
use coding_exception;
use html_writer;
use tabobject;
use tabtree;
use custom_menu_item;
use custom_menu;
use block_contents;
use navigation_node;
use action_link;
use stdClass;
use moodle_url;
use preferences_groups;
use action_menu;
use help_icon;
use single_button;
use single_select;
use paging_bar;
use url_select;
use context_course;
use pix_icon;
use theme_config;
defined('MOODLE_INTERNAL') || die;
require_once ($CFG->dirroot . "/course/renderer.php");

/**
 * Renderers to align Moodle's HTML with that expected by Bootstrap
 *
 * @package    theme_fordson
 * @copyright  2012 Bas Brands, www.basbrands.nl
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class core_renderer extends \theme_boost\output\core_renderer {
    /**
     * Wrapper for header elements.
     *
     * @return string HTML to display the main header.
     */
    public function headerbkglocation() {
        $theme = theme_config::load('fordson');
        $setting = $theme->settings->pagelayout;
        return $setting <= 4 ? true : false;
    }
    public function full_header() {
        global $PAGE, $COURSE, $course;
        $theme = theme_config::load('fordson');
        $pagelayout = $theme->settings->pagelayout;
        $header = new stdClass();
        if ($pagelayout <= 4) {
            $header->headerimagelocation = false;
        }
        if (!$PAGE->theme->settings->coursemanagementtoggle) {
            $header->settingsmenu = $this->context_header_settings_menu();
        }
        else if (isset($COURSE->id) && $COURSE->id == 1) {
            $header->settingsmenu = $this->context_header_settings_menu();
        }
        $header->boostimage = $theme->settings->pagelayout == 5;
        $header->contextheader = html_writer::link(new moodle_url('/course/view.php', array(
            'id' => $PAGE->course->id
        )) , $this->context_header());
        $header->hasnavbar = empty($PAGE->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headerimage = $this->headerimage();
        $header->headeractions = $this->page->get_header_actions();

        if (theme_fordson_get_setting('jitsibuttontext') && $PAGE->pagelayout == 'course') {
            $jitsibuttonurl = $theme->settings->jitsibuttonurl;
            $jitsibuttontext = $theme->settings->jitsibuttontext;
            $header->jitsi = '<a class="btn btn-primary" href=" ' . $jitsibuttonurl . '/' . $course->id .' ' . $course->fullname . '" target="_blank"> <i class="fa fa-video-camera jitsivideoicon" aria-hidden="true"></i><span class="jistibuttontext">
' . $jitsibuttontext . ' </span></a>';
        }
        
        return $this->render_from_template('theme_fordson/header', $header);
    }
    
    public function image_url($imagename, $component = 'moodle') {
        // Strip -24, -64, -256  etc from the end of filetype icons so we
        // only need to provide one SVG, see MDL-47082.
        $imagename = \preg_replace('/-\d\d\d?$/', '', $imagename);
        return $this->page->theme->image_url($imagename, $component);
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
        $headerbg = $PAGE->theme->setting_file_url('headerdefaultimage', 'headerdefaultimage');
        $headerbgimgurl = $PAGE->theme->setting_file_url('headerdefaultimage', 'headerdefaultimage', true);
        $defaultimgurl = $OUTPUT->image_url('headerbg', 'theme');
        // Create html for header.
        $html = html_writer::start_div('headerbkg');
        // If course image display it in separate div to allow css styling of inline style.
        if (theme_fordson_get_setting('showcourseheaderimage') && $courseimage) {
            $html .= html_writer::start_div('withimage', array(
                'style' => 'background-image: url("' . $courseimage . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End withimage inline style div.
            
        }
        else if (theme_fordson_get_setting('showcourseheaderimage') && !$courseimage && isset($headerbg)) {
            $html .= html_writer::start_div('customimage', array(
                'style' => 'background-image: url("' . $headerbgimgurl . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End withoutimage inline style div.
            
        }
        else if ($courseimage && isset($headerbg) && !theme_fordson_get_setting('showcourseheaderimage')) {
            $html .= html_writer::start_div('customimage', array(
                'style' => 'background-image: url("' . $headerbgimgurl . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End withoutimage inline style div.
            
        }
        else if (!$courseimage && isset($headerbg) && !theme_fordson_get_setting('showcourseheaderimage')) {
            $html .= html_writer::start_div('customimage', array(
                'style' => 'background-image: url("' . $headerbgimgurl . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End withoutimage inline style div.
            
        }
        else {
            $html .= html_writer::start_div('default', array(
                'style' => 'background-image: url("' . $defaultimgurl . '"); background-size: cover; background-position:center;
                width: 100%; height: 100%;'
            ));
            $html .= html_writer::end_div(); // End default inline style div.
            
        }
        $html .= html_writer::end_div();
        return $html;
    }

    public function get_generated_image_for_id($id) {
        // See if user uploaded a custom header background to the theme.
        $headerbg = $this->page->theme->setting_file_url('headerdefaultimage', 'headerdefaultimage');
        if (isset($headerbg)) {
            return $headerbg;
        } else {
            // Use the default theme image when no course image is detected.
            return $this->image_url('noimg', 'theme')->out();
        }
    }
    public function edit_button(moodle_url $url) {
        return '';
    }

    public function edit_button_fhs() {
        global $SITE, $PAGE, $USER, $CFG, $COURSE;
        if (!$PAGE->user_allowed_editing() || $COURSE->id <= 1) {
            return '';
        }
        if ($PAGE->pagelayout == 'course') {
            $url = new moodle_url($PAGE->url);
            $url->param('sesskey', sesskey());
            if ($PAGE->user_is_editing()) {
                $url->param('edit', 'off');
                $btn = 'btn-danger editingbutton';
                $title = get_string('editoff', 'theme_fordson');
                $icon = 'fa-power-off';
            }
            else {
                $url->param('edit', 'on');
                $btn = 'btn-success editingbutton';
                $title = get_string('editon', 'theme_fordson');
                $icon = 'fa-edit';
            }
            return html_writer::tag('a', html_writer::start_tag('i', array(
                'class' => $icon . ' fa fa-fw'
            )) . html_writer::end_tag('i') , array(
                'href' => $url,
                'class' => 'btn edit-btn ' . $btn,
                'data-tooltip' => "tooltip",
                'data-placement' => "bottom",
                'title' => $title,
            ));
            return $output;
        }
    }
    /**
     * Generates an array of sections and an array of activities for the given course.
     *
     * This method uses the cache to improve performance and avoid the get_fast_modinfo call
     *
     * @param stdClass $course
     * @return array Array($sections, $activities)
     */
    protected function generate_sections_and_activities(stdClass $course) {
        global $CFG;
        require_once ($CFG->dirroot . '/course/lib.php');
        $modinfo = get_fast_modinfo($course);
        $sections = $modinfo->get_section_info_all();
        // For course formats using 'numsections' trim the sections list
        $courseformatoptions = course_get_format($course)->get_format_options();
        if (isset($courseformatoptions['numsections'])) {
            $sections = array_slice($sections, 0, $courseformatoptions['numsections'] + 1, true);
        }
        $activities = array();
        foreach ($sections as $key => $section) {
            // Clone and unset summary to prevent $SESSION bloat (MDL-31802).
            $sections[$key] = clone ($section);
            unset($sections[$key]->summary);
            $sections[$key]->hasactivites = false;
            if (!array_key_exists($section->section, $modinfo->sections)) {
                continue;
            }
            foreach ($modinfo->sections[$section->section] as $cmid) {
                $cm = $modinfo->cms[$cmid];
                $activity = new stdClass;
                $activity->id = $cm->id;
                $activity->course = $course->id;
                $activity->section = $section->section;
                $activity->name = $cm->name;
                $activity->icon = $cm->icon;
                $activity->iconcomponent = $cm->iconcomponent;
                $activity->hidden = (!$cm->visible);
                $activity->modname = $cm->modname;
                $activity->nodetype = navigation_node::NODETYPE_LEAF;
                $activity->onclick = $cm->onclick;
                $url = $cm->url;
                if (!$url) {
                    $activity->url = null;
                    $activity->display = false;
                }
                else {
                    $activity->url = $url->out();
                    $activity->display = $cm->is_visible_on_course_page() ? true : false;
                }
                $activities[$cmid] = $activity;
                if ($activity->display) {
                    $sections[$key]->hasactivites = true;
                }
            }
        }
        return array(
            $sections,
            $activities
        );
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
    public function fordson_custom_menu() {
        global $CFG, $COURSE, $PAGE, $OUTPUT;
        $context = $this->page->context;
        $menu = new custom_menu();
        $hasdisplaymycourses = (empty($this->page->theme->settings->displaymycourses)) ? false : $this->page->theme->settings->displaymycourses;
        if (isloggedin() && !isguestuser() && $hasdisplaymycourses) {
            $mycoursetitle = $this->page->theme->settings->mycoursetitle;
            if ($mycoursetitle == 'module') {
                $branchtitle = get_string('mymodules', 'theme_fordson');
                $thisbranchtitle = get_string('thismymodules', 'theme_fordson');
                $homebranchtitle = get_string('homemymodules', 'theme_fordson');
            }
            else if ($mycoursetitle == 'unit') {
                $branchtitle = get_string('myunits', 'theme_fordson');
                $thisbranchtitle = get_string('thismyunits', 'theme_fordson');
                $homebranchtitle = get_string('homemyunits', 'theme_fordson');
            }
            else if ($mycoursetitle == 'class') {
                $branchtitle = get_string('myclasses', 'theme_fordson');
                $thisbranchtitle = get_string('thismyclasses', 'theme_fordson');
                $homebranchtitle = get_string('homemyclasses', 'theme_fordson');
            }
            else if ($mycoursetitle == 'training') {
                $branchtitle = get_string('mytraining', 'theme_fordson');
                $thisbranchtitle = get_string('thismytraining', 'theme_fordson');
                $homebranchtitle = get_string('homemytraining', 'theme_fordson');
            }
            else if ($mycoursetitle == 'pd') {
                $branchtitle = get_string('myprofessionaldevelopment', 'theme_fordson');
                $thisbranchtitle = get_string('thismyprofessionaldevelopment', 'theme_fordson');
                $homebranchtitle = get_string('homemyprofessionaldevelopment', 'theme_fordson');
            }
            else if ($mycoursetitle == 'cred') {
                $branchtitle = get_string('mycred', 'theme_fordson');
                $thisbranchtitle = get_string('thismycred', 'theme_fordson');
                $homebranchtitle = get_string('homemycred', 'theme_fordson');
            }
            else if ($mycoursetitle == 'plan') {
                $branchtitle = get_string('myplans', 'theme_fordson');
                $thisbranchtitle = get_string('thismyplans', 'theme_fordson');
                $homebranchtitle = get_string('homemyplans', 'theme_fordson');
            }
            else if ($mycoursetitle == 'comp') {
                $branchtitle = get_string('mycomp', 'theme_fordson');
                $thisbranchtitle = get_string('thismycomp', 'theme_fordson');
                $homebranchtitle = get_string('homemycomp', 'theme_fordson');
            }
            else if ($mycoursetitle == 'program') {
                $branchtitle = get_string('myprograms', 'theme_fordson');
                $thisbranchtitle = get_string('thismyprograms', 'theme_fordson');
                $homebranchtitle = get_string('homemyprograms', 'theme_fordson');
            }
            else if ($mycoursetitle == 'lecture') {
                $branchtitle = get_string('mylectures', 'theme_fordson');
                $thisbranchtitle = get_string('thismylectures', 'theme_fordson');
                $homebranchtitle = get_string('homemylectures', 'theme_fordson');
            }
            else if ($mycoursetitle == 'lesson') {
                $branchtitle = get_string('mylessons', 'theme_fordson');
                $thisbranchtitle = get_string('thismylessons', 'theme_fordson');
                $homebranchtitle = get_string('homemylessons', 'theme_fordson');
            }
            else {
                $branchtitle = get_string('mycourses', 'theme_fordson');
                $thisbranchtitle = get_string('thismycourses', 'theme_fordson');
                $homebranchtitle = get_string('homemycourses', 'theme_fordson');
            }
            
            $branchlabel = $branchtitle;
            $branchurl = new moodle_url('/my/index.php');
            $branchsort = 10000;
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
            $dashlabel = get_string('mymoodle', 'my');
            $dashurl = new moodle_url("/my");
            $dashtitle = $dashlabel;
            $branch->add($dashlabel, $dashurl, $dashtitle);
           
            if ($courses = enrol_get_my_courses(NULL, 'fullname ASC')) {
                if (theme_fordson_get_setting('frontpagemycoursessorting')) {
                $courses = enrol_get_my_courses(null, 'sortorder ASC');
                $nomycourses = '<div class="alert alert-info alert-block">' . get_string('nomycourses', 'theme_fordson') . '</div>';
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
            }
                foreach ($courses as $course) {
                    if ($course->visible) {
                        $branch->add(format_string($course->fullname) , new moodle_url('/course/view.php?id=' . $course->id) , format_string($course->shortname));
                    }
                }
            }
            else {
                $noenrolments = get_string('noenrolments', 'theme_fordson');
                $branch->add('<em>' . $noenrolments . '</em>', new moodle_url('/') , $noenrolments);
            } 

            $hasdisplaythiscourse = (empty($this->page->theme->settings->displaythiscourse)) ? false : $this->page->theme->settings->displaythiscourse;
            $sections = $this->generate_sections_and_activities($COURSE);
            if ($sections && $COURSE->id > 1 && $hasdisplaythiscourse) {
                $branchlabel = $thisbranchtitle;
                $branch = $menu->add($branchlabel, $branchurl, $branchtitle, $branchsort);
                $course = course_get_format($COURSE)->get_course();
                $coursehomelabel = $homebranchtitle;
                $coursehomeurl = new moodle_url('/course/view.php?', array(
                    'id' => $PAGE->course->id
                ));
                $coursehometitle = $coursehomelabel;
                $branch->add($coursehomelabel, $coursehomeurl, $coursehometitle);
                $callabel = get_string('calendar', 'calendar');
                $calurl = new moodle_url('/calendar/view.php?view=month', array(
                    'course' => $PAGE->course->id
                ));
                $caltitle = $callabel;
                $branch->add($callabel, $calurl, $caltitle);
                $participantlabel = get_string('participants', 'moodle');
                $participanturl = new moodle_url('/user/index.php', array(
                    'id' => $PAGE->course->id
                ));
                $participanttitle = $participantlabel;
                $branch->add($participantlabel, $participanturl, $participanttitle);
                if ($CFG->enablebadges == 1) {
                    $badgelabel = get_string('badges', 'badges');
                    $badgeurl = new moodle_url('/badges/view.php?type=2', array(
                        'id' => $PAGE->course->id
                    ));
                    $badgetitle = $badgelabel;
                    $branch->add($badgelabel, $badgeurl, $badgetitle);
                }
                if (get_config('core_competency', 'enabled')) {
                    $complabel = get_string('competencies', 'competency');
                    $compurl = new moodle_url('/admin/tool/lp/coursecompetencies.php', array(
                        'courseid' => $PAGE->course->id
                    ));
                    $comptitle = $complabel;
                    $branch->add($complabel, $compurl, $comptitle);
                }
                foreach ($sections[0] as $sectionid => $section) {
                    $sectionname = get_section_name($COURSE, $section);
                    if (isset($course->coursedisplay) && $course->coursedisplay == COURSE_DISPLAY_MULTIPAGE) {
                        $sectionurl = '/course/view.php?id=' . $COURSE->id . '&section=' . $sectionid;
                    }
                    else {
                        $sectionurl = '/course/view.php?id=' . $COURSE->id . '#section-' . $sectionid;
                    }
                    $branch->add(format_string($sectionname) , new moodle_url($sectionurl) , format_string($sectionname));
                }
            }
        }
        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('core/custom_menu_item', $context);
        }
        return $content;
    }
    protected function render_courseactivities_menu(custom_menu $menu) {
        global $CFG;
        $content = '';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('theme_fordson/activitygroups', $context);
        }
        return $content;
    }
    public function courseactivities_menu() {
        global $PAGE, $COURSE, $OUTPUT, $CFG;
        $menu = new custom_menu();
        $context = $this->page->context;
        if (isset($COURSE->id) && $COURSE->id > 1) {
            $branchtitle = get_string('courseactivities', 'theme_fordson');
            $branchlabel = $branchtitle;
            $branchurl = new moodle_url('#');
            $branch = $menu->add($branchlabel, $branchurl, $branchtitle, 10002);
            $data = theme_fordson_get_course_activities();
            foreach ($data as $modname => $modfullname) {
                if ($modname === 'resources') {
                    $branch->add($modfullname, new moodle_url('/course/resources.php', array(
                        'id' => $PAGE->course->id
                    )));
                }
                else {
                    $branch->add($modfullname, new moodle_url('/mod/' . $modname . '/index.php', array(
                        'id' => $PAGE->course->id
                    )));
                }
            }
        }
        return $this->render_courseactivities_menu($menu);
    }
    public function social_icons() {
        global $PAGE;
        $hasfacebook = (empty($PAGE->theme->settings->facebook)) ? false : $PAGE->theme->settings->facebook;
        $hastwitter = (empty($PAGE->theme->settings->twitter)) ? false : $PAGE->theme->settings->twitter;
        $hasgoogleplus = (empty($PAGE->theme->settings->googleplus)) ? false : $PAGE->theme->settings->googleplus;
        $haslinkedin = (empty($PAGE->theme->settings->linkedin)) ? false : $PAGE->theme->settings->linkedin;
        $hasyoutube = (empty($PAGE->theme->settings->youtube)) ? false : $PAGE->theme->settings->youtube;
        $hasflickr = (empty($PAGE->theme->settings->flickr)) ? false : $PAGE->theme->settings->flickr;
        $hasvk = (empty($PAGE->theme->settings->vk)) ? false : $PAGE->theme->settings->vk;
        $haspinterest = (empty($PAGE->theme->settings->pinterest)) ? false : $PAGE->theme->settings->pinterest;
        $hasinstagram = (empty($PAGE->theme->settings->instagram)) ? false : $PAGE->theme->settings->instagram;
        $hasskype = (empty($PAGE->theme->settings->skype)) ? false : $PAGE->theme->settings->skype;
        $haswebsite = (empty($PAGE->theme->settings->website)) ? false : $PAGE->theme->settings->website;
        $hasblog = (empty($PAGE->theme->settings->blog)) ? false : $PAGE->theme->settings->blog;
        $hasvimeo = (empty($PAGE->theme->settings->vimeo)) ? false : $PAGE->theme->settings->vimeo;
        $hastumblr = (empty($PAGE->theme->settings->tumblr)) ? false : $PAGE->theme->settings->tumblr;
        $hassocial1 = (empty($PAGE->theme->settings->social1)) ? false : $PAGE->theme->settings->social1;
        $social1icon = (empty($PAGE->theme->settings->socialicon1)) ? 'globe' : $PAGE->theme->settings->socialicon1;
        $hassocial2 = (empty($PAGE->theme->settings->social2)) ? false : $PAGE->theme->settings->social2;
        $social2icon = (empty($PAGE->theme->settings->socialicon2)) ? 'globe' : $PAGE->theme->settings->socialicon2;
        $hassocial3 = (empty($PAGE->theme->settings->social3)) ? false : $PAGE->theme->settings->social3;
        $social3icon = (empty($PAGE->theme->settings->socialicon3)) ? 'globe' : $PAGE->theme->settings->socialicon3;
        $socialcontext = [
        // If any of the above social networks are true, sets this to true.
        'hassocialnetworks' => ($hasfacebook || $hastwitter || $hasgoogleplus || $hasflickr || $hasinstagram || $hasvk || $haslinkedin || $haspinterest || $hasskype || $haslinkedin || $haswebsite || $hasyoutube || $hasblog || $hasvimeo || $hastumblr || $hassocial1 || $hassocial2 || $hassocial3) ? true : false, 'socialicons' => array(
            array(
                'haslink' => $hasfacebook,
                'linkicon' => 'facebook'
            ) ,
            array(
                'haslink' => $hastwitter,
                'linkicon' => 'twitter'
            ) ,
            array(
                'haslink' => $hasgoogleplus,
                'linkicon' => 'google-plus'
            ) ,
            array(
                'haslink' => $haslinkedin,
                'linkicon' => 'linkedin'
            ) ,
            array(
                'haslink' => $hasyoutube,
                'linkicon' => 'youtube'
            ) ,
            array(
                'haslink' => $hasflickr,
                'linkicon' => 'flickr'
            ) ,
            array(
                'haslink' => $hasvk,
                'linkicon' => 'vk'
            ) ,
            array(
                'haslink' => $haspinterest,
                'linkicon' => 'pinterest'
            ) ,
            array(
                'haslink' => $hasinstagram,
                'linkicon' => 'instagram'
            ) ,
            array(
                'haslink' => $hasskype,
                'linkicon' => 'skype'
            ) ,
            array(
                'haslink' => $haswebsite,
                'linkicon' => 'globe'
            ) ,
            array(
                'haslink' => $hasblog,
                'linkicon' => 'bookmark'
            ) ,
            array(
                'haslink' => $hasvimeo,
                'linkicon' => 'vimeo-square'
            ) ,
            array(
                'haslink' => $hastumblr,
                'linkicon' => 'tumblr'
            ) ,
            array(
                'haslink' => $hassocial1,
                'linkicon' => $social1icon
            ) ,
            array(
                'haslink' => $hassocial2,
                'linkicon' => $social2icon
            ) ,
            array(
                'haslink' => $hassocial3,
                'linkicon' => $social3icon
            ) ,
        ) ];
        return $this->render_from_template('theme_fordson/socialicons', $socialcontext);
    }
    public function fp_wonderbox() {
        global $PAGE;
        $context = $this->page->context;
        $hascreateicon = (empty($PAGE->theme->settings->createicon && isloggedin() && has_capability('moodle/course:create', $context))) ? false : $PAGE->theme->settings->createicon;
        $createbuttonurl = (empty($PAGE->theme->settings->createbuttonurl)) ? false : $PAGE->theme->settings->createbuttonurl;
        $createbuttontext = (empty($PAGE->theme->settings->createbuttontext)) ? false : format_string($PAGE->theme->settings->createbuttontext);
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
        $fptextbox = (empty($PAGE->theme->settings->fptextbox && isloggedin())) ? false : format_text($PAGE->theme->settings->fptextbox, FORMAT_HTML, array(
            'noclean' => true
        ));
        $fptextboxlogout = (empty($PAGE->theme->settings->fptextboxlogout && !isloggedin())) ? false : format_text($PAGE->theme->settings->fptextboxlogout, FORMAT_HTML, array(
            'noclean' => true
        ));
        $slidetextbox = (empty($PAGE->theme->settings->slidetextbox && isloggedin())) ? false : format_text($PAGE->theme->settings->slidetextbox, FORMAT_HTML, array(
            'noclean' => true
        ));
        $alertbox = (empty($PAGE->theme->settings->alertbox)) ? false : format_text($PAGE->theme->settings->alertbox, FORMAT_HTML, array(
            'noclean' => true
        ));
        
        $hasmarketing1 = (empty($PAGE->theme->settings->marketing1 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_string($PAGE->theme->settings->marketing1);
        $marketing1content = (empty($PAGE->theme->settings->marketing1content)) ? false : format_text($PAGE->theme->settings->marketing1content);
        $marketing1buttontext = (empty($PAGE->theme->settings->marketing1buttontext)) ? false : format_string($PAGE->theme->settings->marketing1buttontext);
        $marketing1buttonurl = (empty($PAGE->theme->settings->marketing1buttonurl)) ? false : $PAGE->theme->settings->marketing1buttonurl;
        $marketing1target = (empty($PAGE->theme->settings->marketing1target)) ? false : $PAGE->theme->settings->marketing1target;
        $marketing1image = (empty($PAGE->theme->settings->marketing1image)) ? false : 'marketing1image';
        
        $hasmarketing2 = (empty($PAGE->theme->settings->marketing2 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_string($PAGE->theme->settings->marketing2);
        $marketing2content = (empty($PAGE->theme->settings->marketing2content)) ? false : format_text($PAGE->theme->settings->marketing2content);
        $marketing2buttontext = (empty($PAGE->theme->settings->marketing2buttontext)) ? false : format_string($PAGE->theme->settings->marketing2buttontext);
        $marketing2buttonurl = (empty($PAGE->theme->settings->marketing2buttonurl)) ? false : $PAGE->theme->settings->marketing2buttonurl;
        $marketing2target = (empty($PAGE->theme->settings->marketing2target)) ? false : $PAGE->theme->settings->marketing2target;
        $marketing2image = (empty($PAGE->theme->settings->marketing2image)) ? false : 'marketing2image';
        
        $hasmarketing3 = (empty($PAGE->theme->settings->marketing3 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_string($PAGE->theme->settings->marketing3);
        $marketing3content = (empty($PAGE->theme->settings->marketing3content)) ? false : format_text($PAGE->theme->settings->marketing3content);
        $marketing3buttontext = (empty($PAGE->theme->settings->marketing3buttontext)) ? false : format_string($PAGE->theme->settings->marketing3buttontext);
        $marketing3buttonurl = (empty($PAGE->theme->settings->marketing3buttonurl)) ? false : $PAGE->theme->settings->marketing3buttonurl;
        $marketing3target = (empty($PAGE->theme->settings->marketing3target)) ? false : $PAGE->theme->settings->marketing3target;
        $marketing3image = (empty($PAGE->theme->settings->marketing3image)) ? false : 'marketing3image';
        
        $hasmarketing4 = (empty($PAGE->theme->settings->marketing4 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_string($PAGE->theme->settings->marketing4);
        $marketing4content = (empty($PAGE->theme->settings->marketing4content)) ? false : format_text($PAGE->theme->settings->marketing4content);
        $marketing4buttontext = (empty($PAGE->theme->settings->marketing4buttontext)) ? false : format_string($PAGE->theme->settings->marketing4buttontext);
        $marketing4buttonurl = (empty($PAGE->theme->settings->marketing4buttonurl)) ? false : $PAGE->theme->settings->marketing4buttonurl;
        $marketing4target = (empty($PAGE->theme->settings->marketing4target)) ? false : $PAGE->theme->settings->marketing4target;
        $marketing4image = (empty($PAGE->theme->settings->marketing4image)) ? false : 'marketing4image';
        
        $hasmarketing5 = (empty($PAGE->theme->settings->marketing5 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_string($PAGE->theme->settings->marketing5);
        $marketing5content = (empty($PAGE->theme->settings->marketing5content)) ? false : format_text($PAGE->theme->settings->marketing5content);
        $marketing5buttontext = (empty($PAGE->theme->settings->marketing5buttontext)) ? false : format_string($PAGE->theme->settings->marketing5buttontext);
        $marketing5buttonurl = (empty($PAGE->theme->settings->marketing5buttonurl)) ? false : $PAGE->theme->settings->marketing5buttonurl;
        $marketing5target = (empty($PAGE->theme->settings->marketing5target)) ? false : $PAGE->theme->settings->marketing5target;
        $marketing5image = (empty($PAGE->theme->settings->marketing5image)) ? false : 'marketing5image';
        
        $hasmarketing6 = (empty($PAGE->theme->settings->marketing6 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_string($PAGE->theme->settings->marketing6);
        $marketing6content = (empty($PAGE->theme->settings->marketing6content)) ? false : format_text($PAGE->theme->settings->marketing6content);
        $marketing6buttontext = (empty($PAGE->theme->settings->marketing6buttontext)) ? false : format_string($PAGE->theme->settings->marketing6buttontext);
        $marketing6buttonurl = (empty($PAGE->theme->settings->marketing6buttonurl)) ? false : $PAGE->theme->settings->marketing6buttonurl;
        $marketing6target = (empty($PAGE->theme->settings->marketing6target)) ? false : $PAGE->theme->settings->marketing6target;
        $marketing6image = (empty($PAGE->theme->settings->marketing6image)) ? false : 'marketing6image';

        $hasmarketing7 = (empty($PAGE->theme->settings->marketing7 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_string($PAGE->theme->settings->marketing7);
        $marketing7content = (empty($PAGE->theme->settings->marketing7content)) ? false : format_text($PAGE->theme->settings->marketing7content);
        $marketing7buttontext = (empty($PAGE->theme->settings->marketing7buttontext)) ? false : format_string($PAGE->theme->settings->marketing7buttontext);
        $marketing7buttonurl = (empty($PAGE->theme->settings->marketing7buttonurl)) ? false : $PAGE->theme->settings->marketing7buttonurl;
        $marketing7target = (empty($PAGE->theme->settings->marketing7target)) ? false : $PAGE->theme->settings->marketing7target;
        $marketing7image = (empty($PAGE->theme->settings->marketing7image)) ? false : 'marketing7image';

        $hasmarketing8 = (empty($PAGE->theme->settings->marketing8 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_string($PAGE->theme->settings->marketing8);
        $marketing8content = (empty($PAGE->theme->settings->marketing8content)) ? false : format_text($PAGE->theme->settings->marketing8content);
        $marketing8buttontext = (empty($PAGE->theme->settings->marketing8buttontext)) ? false : format_string($PAGE->theme->settings->marketing8buttontext);
        $marketing8buttonurl = (empty($PAGE->theme->settings->marketing8buttonurl)) ? false : $PAGE->theme->settings->marketing8buttonurl;
        $marketing8target = (empty($PAGE->theme->settings->marketing8target)) ? false : $PAGE->theme->settings->marketing8target;
        $marketing8image = (empty($PAGE->theme->settings->marketing8image)) ? false : 'marketing8image';

        $hasmarketing9 = (empty($PAGE->theme->settings->marketing9 && $PAGE->theme->settings->togglemarketing == 1)) ? false : format_string($PAGE->theme->settings->marketing9);
        $marketing9content = (empty($PAGE->theme->settings->marketing9content)) ? false : format_text($PAGE->theme->settings->marketing9content);
        $marketing9buttontext = (empty($PAGE->theme->settings->marketing9buttontext)) ? false : format_string($PAGE->theme->settings->marketing9buttontext);
        $marketing9buttonurl = (empty($PAGE->theme->settings->marketing9buttonurl)) ? false : $PAGE->theme->settings->marketing9buttonurl;
        $marketing9target = (empty($PAGE->theme->settings->marketing9target)) ? false : $PAGE->theme->settings->marketing9target;
        $marketing9image = (empty($PAGE->theme->settings->marketing9image)) ? false : 'marketing9image';
        /*if (method_exists(new \core\session\manager, 'get_login_token')) {
            $logintoken = \core\session\manager::get_login_token();
        } else {
            $logintoken = false;
        }*/
        /*if( method_exists ( "\core\session\manager", "get_login_token" ) ){
            $logintoken = s(\core\session\manager::get_login_token());
            echo '<input type="hidden" name="logintoken" value="' . $logintoken . '" />';
        } else {
            $logintoken = false;
        }*/

        $logintoken = \core\session\manager::get_login_token();

        $fp_wonderboxcontext = ['logintoken' => $logintoken, 'hasfptextbox' => (!empty($PAGE->theme->settings->fptextbox && isloggedin())) , 'fptextbox' => $fptextbox, 'hasslidetextbox' => (!empty($PAGE->theme->settings->slidetextbox && isloggedin())) , 'slidetextbox' => $slidetextbox, 'hasfptextboxlogout' => !isloggedin() , 'fptextboxlogout' => $fptextboxlogout, 'hasshowloginform' => $PAGE->theme->settings->showloginform, 'alertbox' => $alertbox, 'hasmarkettiles' => ($hasmarketing1 || $hasmarketing2 || $hasmarketing3 || $hasmarketing4 || $hasmarketing5 || $hasmarketing6) ? true : false, 'markettiles' => array(
            array(
                'hastile' => $hasmarketing1,
                'tileimage' => $marketing1image,
                'content' => $marketing1content,
                'title' => $hasmarketing1,
                'button' => "<a href = '$marketing1buttonurl' title = '$marketing1buttontext' alt='$marketing1buttontext' class='btn btn-primary' target='$marketing1target'> $marketing1buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing2,
                'tileimage' => $marketing2image,
                'content' => $marketing2content,
                'title' => $hasmarketing2,
                'button' => "<a href = '$marketing2buttonurl' title = '$marketing2buttontext' alt='$marketing2buttontext' class='btn btn-primary' target='$marketing2target'> $marketing2buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing3,
                'tileimage' => $marketing3image,
                'content' => $marketing3content,
                'title' => $hasmarketing3,
                'button' => "<a href = '$marketing3buttonurl' title = '$marketing3buttontext' alt='$marketing3buttontext' class='btn btn-primary' target='$marketing3target'> $marketing3buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing4,
                'tileimage' => $marketing4image,
                'content' => $marketing4content,
                'title' => $hasmarketing4,
                'button' => "<a href = '$marketing4buttonurl' title = '$marketing4buttontext' alt='$marketing4buttontext' class='btn btn-primary' target='$marketing4target'> $marketing4buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing5,
                'tileimage' => $marketing5image,
                'content' => $marketing5content,
                'title' => $hasmarketing5,
                'button' => "<a href = '$marketing5buttonurl' title = '$marketing5buttontext' alt='$marketing5buttontext' class='btn btn-primary' target='$marketing5target'> $marketing5buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing6,
                'tileimage' => $marketing6image,
                'content' => $marketing6content,
                'title' => $hasmarketing6,
                'button' => "<a href = '$marketing6buttonurl' title = '$marketing6buttontext' alt='$marketing6buttontext' class='btn btn-primary' target='$marketing6target'> $marketing6buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing7,
                'tileimage' => $marketing7image,
                'content' => $marketing7content,
                'title' => $hasmarketing7,
                'button' => "<a href = '$marketing7buttonurl' title = '$marketing7buttontext' alt='$marketing7buttontext' class='btn btn-primary' target='$marketing7target'> $marketing7buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing8,
                'tileimage' => $marketing8image,
                'content' => $marketing8content,
                'title' => $hasmarketing8,
                'button' => "<a href = '$marketing8buttonurl' title = '$marketing8buttontext' alt='$marketing8buttontext' class='btn btn-primary' target='$marketing8target'> $marketing8buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing9,
                'tileimage' => $marketing9image,
                'content' => $marketing9content,
                'title' => $hasmarketing9,
                'button' => "<a href = '$marketing9buttonurl' title = '$marketing9buttontext' alt='$marketing9buttontext' class='btn btn-primary' target='$marketing9target'> $marketing9buttontext </a>"
            ) ,
        ) ,
        // If any of the above social networks are true, sets this to true.
        'hasfpiconnav' => ($hasnav1icon || $hasnav2icon || $hasnav3icon || $hasnav4icon || $hasnav5icon || $hasnav6icon || $hasnav7icon || $hasnav8icon || $hascreateicon || $hasslideicon) ? true : false, 'fpiconnav' => array(
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
        ) , 'fpcreateicon' => array(
            array(
                'hasicon' => $hascreateicon,
                'linkicon' => $hascreateicon,
                'link' => $createbuttonurl,
                'linktext' => $createbuttontext
            ) ,
        ) , 'fpslideicon' => array(
            array(
                'hasicon' => $hasslideicon,
                'linkicon' => $hasslideicon,
                'link' => $slideiconbuttonurl,
                'linktext' => $slideiconbuttontext
            ) ,
        ) , ];
        return $this->render_from_template('theme_fordson/fpwonderbox', $fp_wonderboxcontext);
    }
    public function customlogin() {
        global $PAGE;
        $hasloginnav1icon = (empty($PAGE->theme->settings->loginnav1icon)) ? false : $PAGE->theme->settings->loginnav1icon;
        $hasloginnav2icon = (empty($PAGE->theme->settings->loginnav2icon)) ? false : $PAGE->theme->settings->loginnav2icon;
        $hasloginnav3icon = (empty($PAGE->theme->settings->loginnav3icon)) ? false : $PAGE->theme->settings->loginnav3icon;
        $hasloginnav4icon = (empty($PAGE->theme->settings->loginnav4icon)) ? false : $PAGE->theme->settings->loginnav4icon;
        $loginnav1titletext = (empty($PAGE->theme->settings->loginnav1titletext)) ? false : format_text($PAGE->theme->settings->loginnav1titletext);
        $loginnav2titletext = (empty($PAGE->theme->settings->loginnav2titletext)) ? false : format_text($PAGE->theme->settings->loginnav2titletext);
        $loginnav3titletext = (empty($PAGE->theme->settings->loginnav3titletext)) ? false : format_text($PAGE->theme->settings->loginnav3titletext);
        $loginnav4titletext = (empty($PAGE->theme->settings->loginnav4titletext)) ? false : format_text($PAGE->theme->settings->loginnav4titletext);
        $loginnav1icontext = (empty($PAGE->theme->settings->loginnav1icontext)) ? false : format_text($PAGE->theme->settings->loginnav1icontext);
        $loginnav2icontext = (empty($PAGE->theme->settings->loginnav2icontext)) ? false : format_text($PAGE->theme->settings->loginnav2icontext);
        $loginnav3icontext = (empty($PAGE->theme->settings->loginnav3icontext)) ? false : format_text($PAGE->theme->settings->loginnav3icontext);
        $loginnav4icontext = (empty($PAGE->theme->settings->loginnav4icontext)) ? false : format_text($PAGE->theme->settings->loginnav4icontext);
        $hascustomlogin = $PAGE->theme->settings->showcustomlogin == 1;
        $hasdefaultlogin = $PAGE->theme->settings->showcustomlogin == 0;
        $customlogin_context = ['hascustomlogin' => $hascustomlogin, 'hasdefaultlogin' => $hasdefaultlogin, 'hasfeature1' => !empty($PAGE->theme->setting_file_url('feature1image', 'feature1image')) && !empty($PAGE->theme->settings->feature1text) , 'hasfeature2' => !empty($PAGE->theme->setting_file_url('feature2image', 'feature2image')) && !empty($PAGE->theme->settings->feature2text) , 'hasfeature3' => !empty($PAGE->theme->setting_file_url('feature3image', 'feature3image')) && !empty($PAGE->theme->settings->feature3text) , 'feature1image' => $PAGE->theme->setting_file_url('feature1image', 'feature1image') , 'feature2image' => $PAGE->theme->setting_file_url('feature2image', 'feature2image') , 'feature3image' => $PAGE->theme->setting_file_url('feature3image', 'feature3image') , 'feature1text' => (empty($PAGE->theme->settings->feature1text)) ? false : format_text($PAGE->theme->settings->feature1text, FORMAT_HTML, array(
            'noclean' => true
        )) , 'feature2text' => (empty($PAGE->theme->settings->feature2text)) ? false : format_text($PAGE->theme->settings->feature2text, FORMAT_HTML, array(
            'noclean' => true
        )) , 'feature3text' => (empty($PAGE->theme->settings->feature3text)) ? false : format_text($PAGE->theme->settings->feature3text, FORMAT_HTML, array(
            'noclean' => true
        )) ,
        // If any of the above social networks are true, sets this to true.
        'hasfpiconnav' => ($hasloginnav1icon || $hasloginnav2icon || $hasloginnav3icon || $hasloginnav4icon) ? true : false, 'fpiconnav' => array(
            array(
                'hasicon' => $hasloginnav1icon,
                'icon' => $hasloginnav1icon,
                'title' => $loginnav1titletext,
                'text' => $loginnav1icontext
            ) ,
            array(
                'hasicon' => $hasloginnav2icon,
                'icon' => $hasloginnav2icon,
                'title' => $loginnav2titletext,
                'text' => $loginnav2icontext
            ) ,
            array(
                'hasicon' => $hasloginnav3icon,
                'icon' => $hasloginnav3icon,
                'title' => $loginnav3titletext,
                'text' => $loginnav3icontext
            ) ,
            array(
                'hasicon' => $hasloginnav4icon,
                'icon' => $hasloginnav4icon,
                'title' => $loginnav4titletext,
                'text' => $loginnav4icontext
            ) ,
        ) , ];
        return $this->render_from_template('theme_fordson/customlogin', $customlogin_context);
    }
    public function fp_marketingtiles() {
        global $PAGE;
        $hasmarketing1 = (empty($PAGE->theme->settings->marketing1 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_string($PAGE->theme->settings->marketing1);
        $marketing1content = (empty($PAGE->theme->settings->marketing1content)) ? false : format_text($PAGE->theme->settings->marketing1content);
        $marketing1buttontext = (empty($PAGE->theme->settings->marketing1buttontext)) ? false : format_string($PAGE->theme->settings->marketing1buttontext);
        $marketing1buttonurl = (empty($PAGE->theme->settings->marketing1buttonurl)) ? false : $PAGE->theme->settings->marketing1buttonurl;
        $marketing1target = (empty($PAGE->theme->settings->marketing1target)) ? false : $PAGE->theme->settings->marketing1target;
        $marketing1image = (empty($PAGE->theme->settings->marketing1image)) ? false : 'marketing1image';
        
        $hasmarketing2 = (empty($PAGE->theme->settings->marketing2 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_string($PAGE->theme->settings->marketing2);
        $marketing2content = (empty($PAGE->theme->settings->marketing2content)) ? false : format_text($PAGE->theme->settings->marketing2content);
        $marketing2buttontext = (empty($PAGE->theme->settings->marketing2buttontext)) ? false : format_string($PAGE->theme->settings->marketing2buttontext);
        $marketing2buttonurl = (empty($PAGE->theme->settings->marketing2buttonurl)) ? false : $PAGE->theme->settings->marketing2buttonurl;
        $marketing2target = (empty($PAGE->theme->settings->marketing2target)) ? false : $PAGE->theme->settings->marketing2target;
        $marketing2image = (empty($PAGE->theme->settings->marketing2image)) ? false : 'marketing2image';
        
        $hasmarketing3 = (empty($PAGE->theme->settings->marketing3 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_string($PAGE->theme->settings->marketing3);
        $marketing3content = (empty($PAGE->theme->settings->marketing3content)) ? false : format_text($PAGE->theme->settings->marketing3content);
        $marketing3buttontext = (empty($PAGE->theme->settings->marketing3buttontext)) ? false : format_string($PAGE->theme->settings->marketing3buttontext);
        $marketing3buttonurl = (empty($PAGE->theme->settings->marketing3buttonurl)) ? false : $PAGE->theme->settings->marketing3buttonurl;
        $marketing3target = (empty($PAGE->theme->settings->marketing3target)) ? false : $PAGE->theme->settings->marketing3target;
        $marketing3image = (empty($PAGE->theme->settings->marketing3image)) ? false : 'marketing3image';
        
        $hasmarketing4 = (empty($PAGE->theme->settings->marketing4 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_string($PAGE->theme->settings->marketing4);
        $marketing4content = (empty($PAGE->theme->settings->marketing4content)) ? false : format_text($PAGE->theme->settings->marketing4content);
        $marketing4buttontext = (empty($PAGE->theme->settings->marketing4buttontext)) ? false : format_string($PAGE->theme->settings->marketing4buttontext);
        $marketing4buttonurl = (empty($PAGE->theme->settings->marketing4buttonurl)) ? false : $PAGE->theme->settings->marketing4buttonurl;
        $marketing4target = (empty($PAGE->theme->settings->marketing4target)) ? false : $PAGE->theme->settings->marketing4target;
        $marketing4image = (empty($PAGE->theme->settings->marketing4image)) ? false : 'marketing4image';
        
        $hasmarketing5 = (empty($PAGE->theme->settings->marketing5 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_string($PAGE->theme->settings->marketing5);
        $marketing5content = (empty($PAGE->theme->settings->marketing5content)) ? false : format_text($PAGE->theme->settings->marketing5content);
        $marketing5buttontext = (empty($PAGE->theme->settings->marketing5buttontext)) ? false : format_string($PAGE->theme->settings->marketing5buttontext);
        $marketing5buttonurl = (empty($PAGE->theme->settings->marketing5buttonurl)) ? false : $PAGE->theme->settings->marketing5buttonurl;
        $marketing5target = (empty($PAGE->theme->settings->marketing5target)) ? false : $PAGE->theme->settings->marketing5target;
        $marketing5image = (empty($PAGE->theme->settings->marketing5image)) ? false : 'marketing5image';
        
        $hasmarketing6 = (empty($PAGE->theme->settings->marketing6 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_string($PAGE->theme->settings->marketing6);
        $marketing6content = (empty($PAGE->theme->settings->marketing6content)) ? false : format_text($PAGE->theme->settings->marketing6content);
        $marketing6buttontext = (empty($PAGE->theme->settings->marketing6buttontext)) ? false : format_string($PAGE->theme->settings->marketing6buttontext);
        $marketing6buttonurl = (empty($PAGE->theme->settings->marketing6buttonurl)) ? false : $PAGE->theme->settings->marketing6buttonurl;
        $marketing6target = (empty($PAGE->theme->settings->marketing6target)) ? false : $PAGE->theme->settings->marketing6target;
        $marketing6image = (empty($PAGE->theme->settings->marketing6image)) ? false : 'marketing6image';
        
        $hasmarketing7 = (empty($PAGE->theme->settings->marketing7 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_string($PAGE->theme->settings->marketing7);
        $marketing7content = (empty($PAGE->theme->settings->marketing7content)) ? false : format_text($PAGE->theme->settings->marketing7content);
        $marketing7buttontext = (empty($PAGE->theme->settings->marketing7buttontext)) ? false : format_string($PAGE->theme->settings->marketing7buttontext);
        $marketing7buttonurl = (empty($PAGE->theme->settings->marketing7buttonurl)) ? false : $PAGE->theme->settings->marketing7buttonurl;
        $marketing7target = (empty($PAGE->theme->settings->marketing7target)) ? false : $PAGE->theme->settings->marketing7target;
        $marketing7image = (empty($PAGE->theme->settings->marketing7image)) ? false : 'marketing7image';

        $hasmarketing8 = (empty($PAGE->theme->settings->marketing8 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_string($PAGE->theme->settings->marketing8);
        $marketing8content = (empty($PAGE->theme->settings->marketing8content)) ? false : format_text($PAGE->theme->settings->marketing8content);
        $marketing8buttontext = (empty($PAGE->theme->settings->marketing8buttontext)) ? false : format_string($PAGE->theme->settings->marketing8buttontext);
        $marketing8buttonurl = (empty($PAGE->theme->settings->marketing8buttonurl)) ? false : $PAGE->theme->settings->marketing8buttonurl;
        $marketing8target = (empty($PAGE->theme->settings->marketing8target)) ? false : $PAGE->theme->settings->marketing8target;
        $marketing8image = (empty($PAGE->theme->settings->marketing8image)) ? false : 'marketing8image';

        $hasmarketing9 = (empty($PAGE->theme->settings->marketing9 && $PAGE->theme->settings->togglemarketing == 2)) ? false : format_string($PAGE->theme->settings->marketing9);
        $marketing9content = (empty($PAGE->theme->settings->marketing9content)) ? false : format_text($PAGE->theme->settings->marketing9content);
        $marketing9buttontext = (empty($PAGE->theme->settings->marketing9buttontext)) ? false : format_string($PAGE->theme->settings->marketing9buttontext);
        $marketing9buttonurl = (empty($PAGE->theme->settings->marketing9buttonurl)) ? false : $PAGE->theme->settings->marketing9buttonurl;
        $marketing9target = (empty($PAGE->theme->settings->marketing9target)) ? false : $PAGE->theme->settings->marketing9target;
        $marketing9image = (empty($PAGE->theme->settings->marketing9image)) ? false : 'marketing9image';

        $fp_marketingtiles = ['hasmarkettiles' => ($hasmarketing1 || $hasmarketing2 || $hasmarketing3 || $hasmarketing4 || $hasmarketing5 || $hasmarketing6) ? true : false, 'markettiles' => array(
            array(
                'hastile' => $hasmarketing1,
                'tileimage' => $marketing1image,
                'content' => $marketing1content,
                'title' => $hasmarketing1,
                'button' => "<a href = '$marketing1buttonurl' title = '$marketing1buttontext' alt='$marketing1buttontext' class='btn btn-primary' target='$marketing1target'> $marketing1buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing2,
                'tileimage' => $marketing2image,
                'content' => $marketing2content,
                'title' => $hasmarketing2,
                'button' => "<a href = '$marketing2buttonurl' title = '$marketing2buttontext' alt='$marketing2buttontext' class='btn btn-primary' target='$marketing2target'> $marketing2buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing3,
                'tileimage' => $marketing3image,
                'content' => $marketing3content,
                'title' => $hasmarketing3,
                'button' => "<a href = '$marketing3buttonurl' title = '$marketing3buttontext' alt='$marketing3buttontext' class='btn btn-primary' target='$marketing3target'> $marketing3buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing4,
                'tileimage' => $marketing4image,
                'content' => $marketing4content,
                'title' => $hasmarketing4,
                'button' => "<a href = '$marketing4buttonurl' title = '$marketing4buttontext' alt='$marketing4buttontext' class='btn btn-primary' target='$marketing4target'> $marketing4buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing5,
                'tileimage' => $marketing5image,
                'content' => $marketing5content,
                'title' => $hasmarketing5,
                'button' => "<a href = '$marketing5buttonurl' title = '$marketing5buttontext' alt='$marketing5buttontext' class='btn btn-primary' target='$marketing5target'> $marketing5buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing6,
                'tileimage' => $marketing6image,
                'content' => $marketing6content,
                'title' => $hasmarketing6,
                'button' => "<a href = '$marketing6buttonurl' title = '$marketing6buttontext' alt='$marketing6buttontext' class='btn btn-primary' target='$marketing6target'> $marketing6buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing7,
                'tileimage' => $marketing7image,
                'content' => $marketing7content,
                'title' => $hasmarketing7,
                'button' => "<a href = '$marketing7buttonurl' title = '$marketing7buttontext' alt='$marketing7buttontext' class='btn btn-primary' target='$marketing7target'> $marketing7buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing8,
                'tileimage' => $marketing8image,
                'content' => $marketing8content,
                'title' => $hasmarketing8,
                'button' => "<a href = '$marketing8buttonurl' title = '$marketing8buttontext' alt='$marketing8buttontext' class='btn btn-primary' target='$marketing8target'> $marketing8buttontext </a>"
            ) ,
            array(
                'hastile' => $hasmarketing9,
                'tileimage' => $marketing9image,
                'content' => $marketing9content,
                'title' => $hasmarketing9,
                'button' => "<a href = '$marketing9buttonurl' title = '$marketing9buttontext' alt='$marketing9buttontext' class='btn btn-primary' target='$marketing9target'> $marketing9buttontext </a>"
            ) ,
        ) , ];
        return $this->render_from_template('theme_fordson/fpmarkettiles', $fp_marketingtiles);
    }
    public function fp_slideshow() {
        global $PAGE;
        $theme = theme_config::load('fordson');
        $slideshowon = $PAGE->theme->settings->showslideshow == 1;
        $hasslide1 = (empty($theme->setting_file_url('slide1image', 'slide1image'))) ? false : $theme->setting_file_url('slide1image', 'slide1image');
        $slide1 = (empty($PAGE->theme->settings->slide1title)) ? false : $PAGE->theme->settings->slide1title;
        $slide1content = (empty($PAGE->theme->settings->slide1content)) ? false : format_text($PAGE->theme->settings->slide1content);
        $showtext1 = (empty($PAGE->theme->settings->slide1title)) ? false : format_text($PAGE->theme->settings->slide1title);
        $hasslide2 = (empty($theme->setting_file_url('slide2image', 'slide2image'))) ? false : $theme->setting_file_url('slide2image', 'slide2image');
        $slide2 = (empty($PAGE->theme->settings->slide2title)) ? false : $PAGE->theme->settings->slide2title;
        $slide2content = (empty($PAGE->theme->settings->slide2content)) ? false : format_text($PAGE->theme->settings->slide2content);
        $showtext2 = (empty($PAGE->theme->settings->slide2title)) ? false : format_text($PAGE->theme->settings->slide2title);
        $hasslide3 = (empty($theme->setting_file_url('slide3image', 'slide3image'))) ? false : $theme->setting_file_url('slide3image', 'slide3image');
        $slide3 = (empty($PAGE->theme->settings->slide3title)) ? false : $PAGE->theme->settings->slide3title;
        $slide3content = (empty($PAGE->theme->settings->slide3content)) ? false : format_text($PAGE->theme->settings->slide3content);
        $showtext3 = (empty($PAGE->theme->settings->slide3title)) ? false : format_text($PAGE->theme->settings->slide3title);
        $fp_slideshow = ['hasfpslideshow' => $slideshowon, 'hasslide1' => $hasslide1 ? true : false, 'hasslide2' => $hasslide2 ? true : false, 'hasslide3' => $hasslide3 ? true : false, 'showtext1' => $showtext1 ? true : false, 'showtext2' => $showtext2 ? true : false, 'showtext3' => $showtext3 ? true : false, 'slide1' => array(
            'slidetitle' => $slide1,
            'slidecontent' => $slide1content
        ) , 'slide2' => array(
            'slidetitle' => $slide2,
            'slidecontent' => $slide2content
        ) , 'slide3' => array(
            'slidetitle' => $slide3,
            'slidecontent' => $slide3content
        ) , ];
        return $this->render_from_template('theme_fordson/slideshow', $fp_slideshow);
    }
    public function teacherdashmenu() {
        global $PAGE, $COURSE, $CFG, $DB, $OUTPUT;
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $showincourseonly = isset($COURSE->id) && $COURSE->id > 1 && $PAGE->theme->settings->coursemanagementtoggle && isloggedin() && !isguestuser();
        $haspermission = has_capability('enrol/category:config', $context) && $PAGE->theme->settings->coursemanagementtoggle && isset($COURSE->id) && $COURSE->id > 1;
        $togglebutton = '';
        $togglebuttonstudent = '';
        $hasteacherdash = '';
        $hasstudentdash = '';
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
        if ($coursehaseasyenrollment && isset($COURSE->id) && $COURSE->id > 1) {
            $easycodetitle = get_string('header_coursecodes', 'enrol_easy');
            $easycodelink = new moodle_url('/enrol/editinstance.php', array(
                'courseid' => $PAGE->course->id,
                'id' => $easyenrollinstance->id,
                'type' => 'easy'
            ));
        }
        if (isloggedin() && ISSET($COURSE->id) && $COURSE->id > 1) {
            $course = $this->page->course;
            $context = context_course::instance($course->id);
            $hasteacherdash = has_capability('moodle/course:viewhiddenactivities', $context);
            $hasstudentdash = !has_capability('moodle/course:viewhiddenactivities', $context);
            if (has_capability('moodle/course:viewhiddenactivities', $context)) {
                $togglebutton = get_string('coursemanagementbutton', 'theme_fordson');
            }
            else {
                $togglebuttonstudent = get_string('studentdashbutton', 'theme_fordson');
            }
        }
        $siteadmintitle = get_string('siteadminquicklink', 'theme_fordson');
        $siteadminurl = new moodle_url('/admin/search.php');
        $hasadminlink = has_capability('moodle/site:configview', $context);
        $course = $this->page->course;
        // Send to template.
        $dashmenu = ['showincourseonly' => $showincourseonly, 'togglebutton' => $togglebutton, 'togglebuttonstudent' => $togglebuttonstudent, 'hasteacherdash' => $hasteacherdash, 'hasstudentdash' => $hasstudentdash, 'haspermission' => $haspermission, 'hasadminlink' => $hasadminlink, 'siteadmintitle' => $siteadmintitle, 'siteadminurl' => $siteadminurl, ];
        // Attach easy enrollment links if active.
        if ($globalhaseasyenrollment && $coursehaseasyenrollment) {
            $dashmenu['dashmenu'][] = array(
                'haseasyenrollment' => $coursehaseasyenrollment,
                'title' => $easycodetitle,
                'url' => $easycodelink
            );
        }
        return $this->render_from_template('theme_fordson/teacherdashmenu', $dashmenu);
    }

    public function teacherdash() {
        global $PAGE, $COURSE, $CFG, $DB, $OUTPUT, $USER;
        require_once ($CFG->dirroot . '/completion/classes/progress.php');
        $togglebutton = '';
        $togglebuttonstudent = '';
        $hasteacherdash = '';
        $hasstudentdash = '';
        $haseditcog = $PAGE->theme->settings->courseeditingcog;
        $editcog = html_writer::div($this->context_header_settings_menu() , 'pull-xs-right context-header-settings-menu');
        if (isloggedin() && ISSET($COURSE->id) && $COURSE->id > 1) {
            $course = $this->page->course;
            $context = context_course::instance($course->id);
            $hasteacherdash = has_capability('moodle/course:viewhiddenactivities', $context);
            $hasstudentdash = !has_capability('moodle/course:viewhiddenactivities', $context);
            if (has_capability('moodle/course:viewhiddenactivities', $context)) {
                $togglebutton = get_string('coursemanagementbutton', 'theme_fordson');
            }
            else {
                $togglebuttonstudent = get_string('studentdashbutton', 'theme_fordson');
            }
        }
        $course = $this->page->course;
        $context = context_course::instance($course->id);
        $coursemanagementmessage = (empty($PAGE->theme->settings->coursemanagementtextbox)) ? false : format_text($PAGE->theme->settings->coursemanagementtextbox);
        $courseactivities = $this->courseactivities_menu();
        $showincourseonly = isset($COURSE->id) && $COURSE->id > 1 && $PAGE->theme->settings->coursemanagementtoggle && isloggedin() && !isguestuser();
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
        $haspermission = has_capability('enrol/category:config', $context) && $PAGE->theme->settings->coursemanagementtoggle && isset($COURSE->id) && $COURSE->id > 1;
        $userlinks = get_string('userlinks', 'theme_fordson');
        $userlinksdesc = get_string('userlinks_desc', 'theme_fordson');
        $qbank = get_string('qbank', 'theme_fordson');
        $qbankdesc = get_string('qbank_desc', 'theme_fordson');
        $badges = get_string('badges', 'theme_fordson');
        $badgesdesc = get_string('badges_desc', 'theme_fordson');
        $coursemanage = get_string('coursemanage', 'theme_fordson');
        $coursemanagedesc = get_string('coursemanage_desc', 'theme_fordson');
        $coursemanagementmessage = (empty($PAGE->theme->settings->coursemanagementtextbox)) ? false : format_text($PAGE->theme->settings->coursemanagementtextbox, FORMAT_HTML, array(
            'noclean' => true
        ));
        $studentdashboardtextbox = (empty($PAGE->theme->settings->studentdashboardtextbox)) ? false : format_text($PAGE->theme->settings->studentdashboardtextbox, FORMAT_HTML, array(
            'noclean' => true
        ));
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
        $participantstitle = ($PAGE->theme->settings->studentdashboardtextbox == 1) ? false : get_string('participants', 'moodle');
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
        $progress = $this->render_from_template('theme_fordson/progress-bar', $progresschartcontext);

        $gradeslinkstudent = new moodle_url('/grade/report/user/index.php', array(
            'id' => $PAGE->course->id
        ));
        $hascourseinfogroup = array(
            'title' => get_string('courseinfo', 'theme_fordson') ,
            'icon' => 'map'
        );
        $summary = theme_fordson_strip_html_tags($COURSE->summary);
        $summarytrim = theme_fordson_course_trim_char($summary, 300);
        $courseinfo = array(
            array(
                'content' => format_text($summarytrim) ,
            )
        );
        $hascoursestaff = array(
            'title' => get_string('coursestaff', 'theme_fordson') ,
            'icon' => 'users'
        );
        $courseteachers = array();
        $courseother = array();

        $showonlygroupteachers = !empty(groups_get_all_groups($course->id, $USER->id)) && $PAGE->theme->settings->showonlygroupteachers == 1;
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
        $activitylinkstitle = get_string('activitylinkstitle', 'theme_fordson');
        $activitylinkstitle_desc = get_string('activitylinkstitle_desc', 'theme_fordson');
        $mygradestext = get_string('mygradestext', 'theme_fordson');
        $studentcoursemanage = get_string('courseadministration', 'moodle');
        // Permissionchecks for teacher access.
        $hasquestionpermission = has_capability('moodle/question:add', $context);
        $hasbadgepermission = has_capability('moodle/badges:awardbadge', $context);
        $hascoursepermission = has_capability('moodle/backup:backupcourse', $context);
        $hasuserpermission = has_capability('moodle/course:viewhiddenactivities', $context);
        $hasgradebookshow = $PAGE->course->showgrades == 1 && $PAGE->theme->settings->showstudentgrades == 1;
        $hascompletionshow = $PAGE->course->enablecompletion == 1 && $PAGE->theme->settings->showstudentcompletion == 1;
        $hascourseadminshow = $PAGE->theme->settings->showcourseadminstudents == 1;
        $hascompetency = get_config('core_competency', 'enabled');
        // Send to template.
        $haseditcog = $PAGE->theme->settings->courseeditingcog;
        $editcog = html_writer::div($this->context_header_settings_menu() , 'pull-xs-right context-header-settings-menu');
        $dashlinks = [
            'showincourseonly' => $showincourseonly, 
            'haspermission' => $haspermission, 
            'courseactivities' => $courseactivities, 
            'togglebutton' => $togglebutton, 
            'togglebuttonstudent' => $togglebuttonstudent, 
            'userlinkstitle' => $userlinks, 
            'userlinksdesc' => $userlinksdesc, 
            'qbanktitle' => $qbank, 
            'activitylinkstitle' => $activitylinkstitle, 
            'activitylinkstitle_desc' => $activitylinkstitle_desc, 
            'qbankdesc' => $qbankdesc, 
            'badgestitle' => $badges, 
            'badgesdesc' => $badgesdesc, 
            'coursemanagetitle' => $coursemanage, 
            'coursemanagedesc' => $coursemanagedesc, 
            'coursemanagementmessage' => $coursemanagementmessage, 
            'progress' => $progress, 
            'gradeslink' => $gradeslink, 
            'gradeslinkstudent' => $gradeslinkstudent, 
            'hascourseinfogroup' => $hascourseinfogroup, 
            'courseinfo' => $courseinfo, 
            'hascoursestaffgroup' => $hascoursestaff, 
            'courseteachers' => $courseteachers, 
            'courseother' => $courseother, 
            'mygradestext' => $mygradestext, 
            'studentdashboardtextbox' => $studentdashboardtextbox, 
            'hasteacherdash' => $hasteacherdash, 
            'haseditcog'=>$haseditcog, 
            'editcog'=> $editcog, 
            'teacherdash' => array(
                'hasquestionpermission' => $hasquestionpermission,
                'hasbadgepermission' => $hasbadgepermission,
                'hascoursepermission' => $hascoursepermission,
                'hasuserpermission' => $hasuserpermission
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
        return $this->render_from_template('theme_fordson/teacherdash', $dashlinks);
    }
    public function footnote() {
        global $PAGE;
        $footnote = '';
        $footnote = (empty($PAGE->theme->settings->footnote)) ? false : format_text($PAGE->theme->settings->footnote);
        return $footnote;
    }
    public function brandorganization_footer() {
        $theme = theme_config::load('fordson');
        $setting = format_string($theme->settings->brandorganization);
        return $setting != '' ? $setting : '';
    }
    public function brandwebsite_footer() {
        $theme = theme_config::load('fordson');
        $setting = $theme->settings->brandwebsite;
        return $setting != '' ? $setting : '';
    }
    public function brandphone_footer() {
        $theme = theme_config::load('fordson');
        $setting = $theme->settings->brandphone;
        return $setting != '' ? $setting : '';
    }
    public function brandemail_footer() {
        $theme = theme_config::load('fordson');
        $setting = $theme->settings->brandemail;
        return $setting != '' ? $setting : '';
    }

    public function logintext_custom() {
        global $PAGE;
        $logintext_custom = '';
        $logintext_custom = (empty($PAGE->theme->settings->fptextboxlogout)) ? false : format_text($PAGE->theme->settings->fptextboxlogout);
        return $logintext_custom;
    }

    public function render_login(\core_auth\output\login $form) {
        global $SITE, $PAGE;
        $context = $form->export_for_template($this);
        // Override because rendering is not supported in template yet.
        $context->cookieshelpiconformatted = $this->help_icon('cookiesenabled');
        $context->errorformatted = $this->error_text($context->error);
        $url = $this->get_logo_url();
        // Custom logins.
        $context->logintext_custom = format_text($PAGE->theme->settings->fptextboxlogout);
        $context->logintopimage = $PAGE->theme->setting_file_url('logintopimage', 'logintopimage');
        $context->hascustomlogin = $PAGE->theme->settings->showcustomlogin == 1;
        $context->hasdefaultlogin = $PAGE->theme->settings->showcustomlogin == 0;
        $context->alertbox = format_text($PAGE->theme->settings->alertbox, FORMAT_HTML, array(
            'noclean' => true
        ));
        if ($url) {
            $url = $url->out(false);
        }
        $context->logourl = $url;
        $context->sitename = format_string($SITE->fullname, true, ['context' => context_course::instance(SITEID) , "escape" => false]);
        return $this->render_from_template('core/loginform', $context);
    }

    public function favicon() {
        $favicon = $this->page->theme->setting_file_url('favicon', 'favicon');

        if (empty($favicon)) {
            return $this->page->theme->image_url('favicon', 'theme');
        } else {
            return $favicon;
        }
    }

    public function display_ilearn_secure_alert() {
        global $DB, $PAGE;

        if (strpos($PAGE->url, '/mod/quiz/view.php') === false) {
            return false;
        }

        $cm = $PAGE->cm;

        if ($cm) {
            $quiz = $DB->get_record('quiz', array(
                'id' => $cm->instance
            ));
            $globalhasilearnsecureplugin = $DB->get_manager()->table_exists('quizaccess_ilearnbrowser') ? true : false;
        }
        // Turn off alert while taking a quiz.
        if (strpos($PAGE->url, '/mod/quiz/attempt.php')) {
            return false;
        }
        if ($cm && $quiz && $globalhasilearnsecureplugin) {
            $quiz_record = $DB->get_record('quizaccess_ilearnbrowser', array(
                'quiz_id' => $quiz->id
            ));
            if ($quiz_record && $quiz_record->browserrequired == 1) {
                return true;
            }
        }
        return false;
    }

    public function show_teacher_navbarcolor() {
        global $PAGE;
        $theme = theme_config::load('fordson');
        $context = $this->page->context;
        $hasteacherrole = has_capability('moodle/course:viewhiddenactivities', $context);

        if ($PAGE->theme->settings->navbarcolorswitch == 1 && $hasteacherrole) {
            return true;
        }
        return false;
    }

    public function show_student_navbarcolor() {
        global $PAGE;
        $theme = theme_config::load('fordson');
        $context = $this->page->context;
        $hasstudentrole = !has_capability('moodle/course:viewhiddenactivities', $context);

        if ($PAGE->theme->settings->navbarcolorswitch == 1 && $hasstudentrole) {
            return true;
        }
        return false;
    }

}
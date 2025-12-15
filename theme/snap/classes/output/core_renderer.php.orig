<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Snap core renderer.
 *
 * @package   theme_snap
 * @copyright Copyright (c) 2015 Open LMS (https://www.openlms.net)
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace theme_snap\output;

defined('MOODLE_INTERNAL') || die();
require_once($CFG->dirroot.'/message/output/popup/lib.php');

use stdClass;
use context_course;
use context_system;
use coding_exception;
use single_button;
use DateTime;
use html_writer;
use moodle_url;
use navigation_node;
use user_picture;
use theme_snap\local;
use theme_snap\services\course;
use theme_snap\renderables\settings_link;
use theme_snap\renderables\genius_dashboard_link;
use theme_snap\renderables\course_card;
// BEGIN LSU Extra Course Tabs.
use theme_snap\renderables\remote_card;
// END LSU Extra Course Tabs.
use theme_snap\renderables\course_toc;
use theme_snap\renderables\featured_courses;
use theme_snap\renderables\featured_categories;
use lang_string;
use core_course_category;
use core\navigation\output\primary;

// We have to force include this class as it's on login and the auto loader may not have been updated via a cache dump.
require_once($CFG->dirroot.'/theme/snap/classes/renderables/login_alternative_methods.php');
use theme_snap\renderables\login_alternative_methods;

class core_renderer extends \theme_boost\output\core_renderer {

    /* Login option rendering variables */
    const ENABLED_LOGIN_BOTH = '0';
    const ENABLED_LOGIN_MOODLE = '1';
    const ENABLED_LOGIN_ALTERNATIVE = '2';
    const ORDER_LOGIN_MOODLE_FIRST = '0';
    const ORDER_LOGIN_ALTERNATIVE_FIRST = '1';


    /**
     * @var array|string[]
     */
    private array $listhidden = [
        'pluginxp' => '/blocks/xp/index.php',
    ];

    /**
     * Copied from outputrenderer.php
     * Heading with attached help button (same title text)
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
    public function heading_with_help($text, $helpidentifier, $component = 'moodle', $icon = '', $iconalt = '',
                                      $level = 2, $classnames = null) {
        global $USER;
        $image = '';
        if ($icon) {
            $image = $this->pix_icon($icon, $iconalt, $component, ['class' => 'icon iconlarge']);
        }

        $help = '';
        $collapsablehelp = '';
        if ($helpidentifier) {
            // Display header mod help as collapsable instead of popover for mods.
            if ($helpidentifier === 'modulename') {
                // Get mod help text.
                $modnames = get_module_types_names();
                $modname = $modnames[$component];
                $contentitemservice = new \core_course\local\service\content_item_service(
                    new \core_course\local\repository\content_item_readonly_repository()
                );
                $mod = $contentitemservice->get_content_items_by_name_pattern($USER, $modname);
                if (!empty($mod) && isset($mod[$component]) && is_object($mod[$component]) && $mod[$component]->help) {
                    $helptext = format_text($mod[$component]->help, FORMAT_MARKDOWN);
                    $data = (object) [
                        'helptext' => $helptext,
                        'modtitle' => $mod[$component]->title,
                    ];
                    $collapsablehelp = $this->render_from_template('theme_snap/heading_help_collapse', $data);
                    $classnames .= ' d-inline';
                }
                $heading = $this->heading($image.$text, $level, $classnames);
                // Return heading and help.
                return $heading.$collapsablehelp;
            } else {
                $help = $this->help_icon($helpidentifier, $component);
            }
        }

        return $this->heading($image.$text.$help, $level, $classnames);
    }

    /**
     * @return bool|string
     * @throws \moodle_exception
     */
    public function course_toc() {
        $coursetoc = new course_toc();
        return $this->render_from_template('theme_snap/course_toc', $coursetoc);
    }

    /**
     * get course image
     *
     * @return bool|\moodle_url
     */

    public function get_course_image() {
        global $COURSE;

        return \theme_snap\local::course_coverimage_url($COURSE->id);
    }

    /**
     * Print links to more information for personal menu colums.
     *
     * @author: SL
     * @param string $langstring
     * @param string $iconname
     * @param string $url
     * @return string
     */
    public function column_header_icon_link($langstring, $iconname, $url, $location = '') {
        // @codingStandardsIgnoreStart
        // Core renderer has not $output attribute, but code checker requires it.
        global $OUTPUT, $CFG;
        $text = get_string($langstring, 'theme_snap');
        $svgicon = file_get_contents($CFG->dirroot.'/theme/snap/pix/'.$iconname.'.svg');
        $snapfeedsurlparam = isset($CFG->theme_snap_feeds_url_parameter) ? $CFG->theme_snap_feeds_url_parameter : true;
        if ($location == 'snapfeedsmenu') {
            if ($snapfeedsurlparam) {
                if (str_contains($langstring, 'viewmessaging') || str_contains($langstring, 'viewmyfeedback')) {
                    $link = '<a class="snap-feeds-menu-more" href="' .$url. '?snapfeedsclicked=on" title="'.$text.'"><small>' .$text. '</small>' .$svgicon. '</a>';
                } else {
                    $link = '<a class="snap-feeds-menu-more" href="' .$url. '&snapfeedsclicked=on" title="'.$text.'"><small>' .$text. '</small>' .$svgicon. '</a>';
                }
            } else {
                $link = '<a class="snap-feeds-menu-more" href="' .$url. '" title="'.$text.'"><small>' .$text. '</small>' .$svgicon. '</a>';
            }

        } else if ($location == 'mycourses') {
            $link = '<a class="snap-sidebar-menu-more browseallcourses" href="' .$url. '"><small>' .$text. '</small>' .$svgicon. '</a>';
        } else {
            $link = '<a class="snap-sidebar-menu-more" href="' .$url. '"><small>' .$text. '</small>' .$svgicon. '</a>';
        }
        return $link;
        // @codingStandardsIgnoreEnd
    }


    /**
     * Print links for personal menu on mobile.
     *
     * @author: SL
     * @param string $langstring
     * @param string $iconname
     * @param string $url
     * @return string
     */
    public function mobile_menu_link($langstring, $iconname, $url) {
        // @codingStandardsIgnoreStart
        // Core renderer has not $output attribute, but code checker requires it.
        global $OUTPUT;
        $alt = get_string($langstring, 'theme_snap');
        $iconurl = $OUTPUT->image_url($iconname, 'theme');
        $icon = '<img class="svg-icon" alt="' .$alt. '" src="' .$iconurl. '">';
        $class = '';
        if ($iconname == 'courses') {
            $class = 'state-active'; // Initial menu iteam on load.
        }
        $link = '<a href="' .$url. '" class="' .$class. '">' .$icon. '</a>';
        return $link;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Print links for social media icons.
     *
     * @author: SL
     * @param string $iconname
     * @param string $url
     * @return string
     */
    public function social_menu_link($iconname, $url) {
        // @codingStandardsIgnoreStart
        // Core renderer has not $output attribute, but code checker requires it.
        global $OUTPUT;
        $iconurl = $OUTPUT->image_url($iconname, 'theme');
        $icon = '<img class="svg-icon" title="' .$iconname. '" alt="' .$iconname. '" src="' .$iconurl. '">';
        $link = '<a href="' .$url. '" target="_blank">' .$icon. '</a>';
        return $link;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Settings link for opening the Administration menu, only shown if needed.
     * @param settings_link $settingslink
     *
     * @return string
     */
    public function render_settings_link(settings_link $settingslink) {
        if (!$settingslink->output) {
            return '';
        }
        // @codingStandardsIgnoreStart
        $gearicon = '<svg xmlns="http://www.w3.org/2000/svg" id="snap-admin-icon" viewBox="0 0 100 100">
                        <title>'.get_string('toggleadmindrawer', 'theme_snap').'</title>
                        <path d="M85.2,54.9c0.2-1.4,0.3-2.9,0.3-4.5c0-1.5-0.1-3-0.3-4.5l9.6-7.5c0.9-0.7,1-1.9,0.6-2.9l-9.1-15.8c-0.6-1-1.8-1.3-2.8-1
                        l-11.3,4.6c-2.4-1.8-4.9-3.3-7.7-4.5l-1.8-12c-0.1-1-1-1.9-2.2-1.9H42.3c-1.1,0-2.1,0.9-2.2,1.9l-1.7,12.1c-2.8,1.1-5.3,2.7-7.7,4.5
                        l-11.3-4.6c-1-0.4-2.2,0-2.8,1L7.5,35.6c-0.6,1-0.3,2.2,0.6,2.9l9.6,7.5c-0.2,1.4-0.3,2.9-0.3,4.5c0,1.5,0.1,3,0.3,4.5L8,62.4
                        c-0.9,0.7-1,1.9-0.6,2.9l9.1,15.8c0.6,1,1.8,1.3,2.8,1l11.3-4.6c2.4,1.8,4.9,3.3,7.7,4.5L40,94.1c0.1,1,1,1.9,2.2,1.9h18.2
                        c1.1,0,2.1-0.9,2.2-1.9L64.3,82c2.8-1.1,5.3-2.7,7.7-4.5l11.3,4.6c1,0.4,2.2,0,2.8-1l9.1-15.8c0.6-1,0.3-2.2-0.6-2.9
                        C94.6,62.4,85.2,54.9,85.2,54.9z M51.4,34.6c8.8,0,15.9,7.1,15.9,15.9s-7.1,15.9-15.9,15.9s-15.9-7.1-15.9-15.9S42.6,34.6,51.4,34.6
                        z" class="snap-gear-icon"/>
                    </svg>';
         // @codingStandardsIgnoreEnd
        $url = '#inst' . $settingslink->instanceid;
        $attributes = [
            'id' => 'admin-menu-trigger',
            'class' => 'float-end',
            'data-placement' => 'bottom',
            'title' => get_string('toggleadmindrawer', 'theme_snap'),
            'aria-label' => get_string('toggleadmindrawer', 'theme_snap'),
            'data-original-title' => get_string('toggleadmindrawer', 'theme_snap'),
            'aria-expanded' => 'false',
        ];

        return html_writer::link($url, $gearicon, $attributes);
    }


    /**
     * Link to genius, only shown if needed.
     * @param genius_dashboard_link $geniuslink
     *
     * @return string
     */
    public function render_genius_dashboard_link(genius_dashboard_link $geniuslink) {

        if (!$geniuslink->output) {
            return '';
        }

        $linkcontent = $this->render(new \pix_icon('sso', get_string('openlms', 'local_geniusws'), 'local_geniusws')).
                get_string('dashboard', 'local_geniusws');
        $html = html_writer::link($geniuslink->loginurl, $linkcontent, ['class' => 'genius_dashboard_link hidden-md-down']);
        return $html;
    }

    public function activity_header() {
        global $COURSE;
        $renderer = $this->page->get_renderer('core');
        $header = $this->page->activityheader;
        $headercontext = $header->export_for_template($renderer);
        if ($COURSE->format !== 'singleactivity') {
            unset($headercontext['title']);
        }
        if (!empty($headercontext)) {
            return $this->render_from_template('core/activity_header', $headercontext);
        }
        return '';
    }

    /**
     * Render messages from users
     * @return string
     */
    protected function render_messages($location = '') {
        if (empty($this->page->theme->settings->messagestoggle)) {
            return '';
        }

        $heading = get_string('messages', 'theme_snap');
        if ($this->advanced_feeds_enabled()) {
            $o = ce_render_helper::get_instance()
                ->render_feed_web_component('messages', $heading, get_string('nomessages', 'theme_snap'),
                    false, true, 0, $location);
        } else {
            if ($location == 'snapfeedsmenu') {
                $o = '<h2>'.$heading.'</h2>';
                $o .= '<div id="snap-feeds-menu-messages"></div>';
            } else {
                $o = '<h2>'.$heading.'</h2>';
                $o .= '<div id="snap-sidebar-menu-messages"></div>';
            }
        }

        $url = new moodle_url('/message/');
        $o .= $this->column_header_icon_link('viewmessaging', 'messages-new', $url, $location);
        return $o;
    }


    /**
     * Render forumposts.
     *
     * @return string
     */
    protected function render_forumposts($location = '') {
        global $USER;
        if (empty($this->page->theme->settings->forumpoststoggle)) {
            return '';
        }

        $heading = get_string('forumposts', 'theme_snap');
        if ($this->advanced_feeds_enabled()) {
            $virtualpaging = true; // Web service retrieves all elements, need to do virtual paging.
            $o = ce_render_helper::get_instance()->render_feed_web_component('forumposts', $heading,
                            get_string('noforumposts', 'theme_snap'), $virtualpaging,
                 true, 0, $location);
        } else {
            if ($location == 'snapfeedsmenu') {
                $o = '<h2>'.$heading.'</h2>
                <div id="snap-feeds-menu-forumposts"></div>';
            } else {
                $o = '<h2>'.$heading.'</h2>
                <div id="snap-sidebar-menu-forumposts"></div>';
            }
        }

        $url = new moodle_url('/mod/forum/user.php', ['id' => $USER->id]);
        $o .= $this->column_header_icon_link('viewforumposts', 'forumposts-new', $url, $location);
        return $o;
    }


    /**
     * @param moodle_url|string $url
     * @param string $image
     * @param string $title
     * @param array|string $meta
     * @param string $content
     * @param string $extraclasses
     * @param string $attributes
     * @return string
     */
    public function snap_media_object($url, $image, $title, $meta, $content, $extraclasses = '', $attributes = '') {
        $formatoptions = new stdClass;
        $formatoptions->filter = false;
        $title = format_text($title, FORMAT_HTML, $formatoptions);
        $content = format_text($content, FORMAT_HTML, $formatoptions);

        $metastr = '';
        // For forum posts meta is an array with the course title / forum name.
        if (is_array($meta)) {
            $metastr = '<span class="snap-media-meta">';
            foreach ($meta as $metaitem) {
                $metastr .= $metaitem.'<br>';
            }
            $metastr .= '</span>';
        } else if ($meta) {
            $metastr = '<span class="snap-media-meta">' .$meta.'</span>';
        }

        $title = '<h3>' .$title. '</h3>' .$content;
        $link = html_writer::link($url, $title);

        $data = (object) [
                'image' => $image,
                'content' => $link.$metastr,
                'class' => $extraclasses,
                'attributes' => $attributes,
        ];
        return $this->render_from_template('theme_snap/media_object', $data);
    }


    /**
     * Return friendly text date (e.g. "Today", "Tomorrow") in a <time> tag
     * @return string
     */
    public function friendly_datetime($time) {
        $timetext = \calendar_day_representation($time);
        $timetext .= ', ' . \calendar_time_representation($time);
        $datetime = date(DateTime::W3C, $time);
        return html_writer::tag('time', $timetext, [
            'datetime' => $datetime, ]
        );
    }

    /**
     * Output moodle blocks and Snap wrapper with edit button.
     * @return string
     */
    public function snap_blocks() {
        // @codingStandardsIgnoreStart
        // Core renderer has not $output attribute, but code checker requires it.
        global $COURSE, $OUTPUT;

        $output = '';

        $oncoursepage = strpos($this->page->pagetype, 'course-view') === 0;
        $coursecontext = \context_course::instance($COURSE->id);
        if ($COURSE->format !== 'tiles') {
            $output .= '<div id="moodle-blocks" class="clearfix">';
            $output .= $OUTPUT->blocks('side-pre');
            $output .= '</div>';
        } else {
            if ($oncoursepage && $this->page->user_is_editing()) {
                $output .= '<div id="moodle-blocks" class="clearfix editing-tiles">';
            } else {
                $output .= '<div id="moodle-blocks" class="clearfix">';
            }
            $output .= $OUTPUT->blocks('side-pre');
            $output .= '</div>';
        }

        return $output;
        // @codingStandardsIgnoreEnd
    }

    private function get_calltoaction_url($key) {
        return '#snap-sidebar-menu-' .
            ($this->advanced_feeds_enabled() ? 'feed-' : '') .
            $key;
    }

    public function edit_button(moodle_url $url, string $method = 'post') {
        return '';
    }

    protected function render_callstoaction($location = '') {

        $mobilemenu = '<div id="snap-pm-mobilemenu">';
        $mobilemenu .= $this->mobile_menu_link('courses', 'courses', '#snap-pm-courses');
        $deadlines = $this->render_deadlines($location);
        if (!empty($deadlines)) {
            $columns[] = $deadlines;
            $mobilemenu .= $this->mobile_menu_link('deadlines', 'calendar-new', $this->get_calltoaction_url('deadlines'));
        }

        $graded = $this->render_graded($location);
        $grading = $this->render_grading($location);
        if (empty($grading)) {
            $gradebookmenulink = $this->mobile_menu_link('recentfeedback', 'grading-new', $this->get_calltoaction_url('graded'));
        } else {
            $gradebookmenulink = $this->mobile_menu_link('grading', 'grading-new', $this->get_calltoaction_url('grading'));
        }
        if (!empty($grading)) {
            $columns[] = $grading;
            $mobilemenu .= $gradebookmenulink;
        } else if (!empty($graded)) {
            $columns[] = $graded;
            $mobilemenu .= $gradebookmenulink;
        }

        $messages = $this->render_messages($location);
        if (!empty($messages)) {
            $columns[] = $messages;
            $mobilemenu .= $this->mobile_menu_link('messages', 'messages-new', $this->get_calltoaction_url('messages'));
        }

        $forumposts = $this->render_forumposts($location);
        if (!empty($forumposts)) {
            $columns[] = $forumposts;
            $mobilemenu .= $this->mobile_menu_link('forumposts', 'forumposts-new', $this->get_calltoaction_url('forumposts'));
        }

        $mobilemenu .= '</div>';

        if (empty($columns)) {
             return '';
        } else {
            $sections = [];
            $intelliboard = $this->render_intelliboard($location);
            $intellicart = $this->render_intellicart($location);
            if (!empty($intelliboard)) {
                $sections[] = $intelliboard;
            }
            if (!empty($intellicart)) {
                $sections[] = $intellicart;
            }
            foreach ($columns as $column) {
                if (!empty($column)) {
                    $sections[] = $column;
                }
            }
        }

        $data = (object) [
            'update' => $sections,
            'mobilemenu' => $mobilemenu,
        ];
        return $data;
    }

    /**
     * Render mobile Snap Feeds Menu
     * @return string
     * @throws \moodle_exception
     */
    protected function render_snap_feeds_mobile() {
        global $OUTPUT;

        $data = [];

        $intelliboardContent = $this->render_intelliboard('snapfeedsmenu');
        $data['intelliboard'] = [
            'enable' => !empty($intelliboardContent),
            'icon' => $OUTPUT->image_url('intelliboard-new', 'theme'),
            'alt' => 'Intelliboard',
            'content' => $intelliboardContent,
        ];

        $intellicartContent = $this->render_intellicart('snapfeedsmenu');
        $data['intellicart'] = [
            'enable' => !empty($intellicartContent),
            'icon' => $OUTPUT->image_url('intelliboard-new', 'theme'),
            'alt' => 'Intelliboard',
            'content' => $intellicartContent,
        ];

        $data['intellienabled'] = $data['intelliboard']['enable'] || $data['intellicart']['enable'];

        $deadlinesContent = $this->render_deadlines('snapsfeedsmenu');
        $data['deadlines'] = [
            'enable' => !empty($deadlinesContent),
            'icon' => $OUTPUT->image_url('calendar-new', 'theme'),
            'alt' => get_string('deadlines', 'theme_snap'),
            'content' => $deadlinesContent,
        ];

        $data['grading'] = [
            'enable' => $this->feedback_toggle_enabled(),
            'icon' => $OUTPUT->image_url('grading-new', 'theme'),
            'alt' => get_string('grading', 'theme_snap'),
            'content' => $this->render_grading('snapfeedsmenu'),
        ];

        $data['messages'] = [
            'enable' => !empty($this->page->theme->settings->messagestoggle),
            'icon' => $OUTPUT->image_url('messages-new', 'theme'),
            'alt' => get_string('messages', 'theme_snap'),
            'content' => $this->render_messages('snapfeedsmenu'),
        ];

        $data['forumposts'] = [
            'enable' => !empty($this->page->theme->settings->forumpoststoggle),
            'icon' => $OUTPUT->image_url('forumposts-new', 'theme'),
            'alt' => get_string('forumposts', 'theme_snap'),
            'content' => $this->render_forumposts('snapfeedsmenu'),
        ];

        return $this->render_from_template('theme_snap/snap_feeds_mobile_menu', $data);
    }


    /**
     * Is feedback toggle enabled?
     * Note: If setting has never been set then default to enabled (return true).
     *
     * @return bool
     */
    protected function feedback_toggle_enabled() {
        if (property_exists($this->page->theme->settings, 'feedbacktoggle')
            && $this->page->theme->settings->feedbacktoggle == 0) {
            return false;
        }
        return true;
    }

    /**
     * Is advanced feeds enabled?
     *
     * @return bool
     */
    private function advanced_feeds_enabled() {
        $advancedfeedsenabled = property_exists($this->page->theme->settings, 'advancedfeedsenable')
        && $this->page->theme->settings->advancedfeedsenable == 1;
        $anydependencyenabled = property_exists($this->page->theme->settings, 'deadlinestoggle')
        && $this->page->theme->settings->deadlinestoggle == 1 || property_exists($this->page->theme->settings, 'feedbacktoggle')
        && $this->page->theme->settings->feedbacktoggle == 1 || property_exists($this->page->theme->settings, 'messagestoggle')
        && $this->page->theme->settings->messagestoggle == 1 || property_exists($this->page->theme->settings, 'forumpoststoggle')
        && $this->page->theme->settings->forumpoststoggle == 1;
        if ($advancedfeedsenabled && $anydependencyenabled) {
            return true;
        }
        return false;
    }


    /**
     * Render all grading CTAs for markers
     * @return string
     */
    protected function render_grading($location = '') {
        global $USER;

        if (!$this->feedback_toggle_enabled()) {
            return '';
        }

        $courseids = local::gradeable_courseids($USER->id);

        if (empty($courseids)) {
            return '';
        }

        $heading = get_string('grading', 'theme_snap');
        if ($this->advanced_feeds_enabled()) {
            $virtualpaging = true; // Web service retrieves all elements, need to do virtual paging.
            $o = ce_render_helper::get_instance()->render_feed_web_component('grading', $heading,
                            get_string('nograding', 'theme_snap'), $virtualpaging,
                true, 0, $location);
        } else {
            if ($location == 'snapfeedsmenu') {
                $o = "<h2>$heading</h2>";
                $o .= '<div id="snap-feeds-menu-grading"></div>';
            } else {
                $o = "<h2>$heading</h2>";
                $o .= '<div id="snap-sidebar-menu-grading"></div>';
            }
        }

        return $o;
    }


    /**
     * Render all graded CTAs for students
     * @return string
     */
    protected function render_graded($location = '') {
        if (!$this->feedback_toggle_enabled()) {
            return '';
        }

        $heading = get_string('recentfeedback', 'theme_snap');
        if ($this->advanced_feeds_enabled()) {
            $virtualpaging = true; // Web service retrieves all elements, need to do virtual paging.
            $o = ce_render_helper::get_instance()->render_feed_web_component('graded', $heading,
                            get_string('nograded', 'theme_snap'), $virtualpaging,
                true, 0, $location);
        } else {
            if ($location == 'snapfeedsmenu') {
                $o = "<h2>$heading</h2>";
                $o .= '<div id="snap-feeds-menu-graded"></div>';
            } else {
                $o = "<h2>$heading</h2>";
                $o .= '<div id="snap-sidebar-menu-graded"></div>';
            }
        }

        $url = new moodle_url('/grade/report/mygrades.php');
        $o .= $this->column_header_icon_link('viewmyfeedback', 'grading-new', $url, $location);
        return $o;
    }

    /**
     * Render all course deadlines.
     * @return string
     */
    protected function render_deadlines($location = '') {
        global $CFG;

        if ($this->page->theme->settings->deadlinestoggle == 0) {
            return '';
        }

        $heading = get_string('deadlines', 'theme_snap');
        if ($this->advanced_feeds_enabled()) {
            $virtualpaging = true; // Web service retrieves all elements, need to do virtual paging.
            $o = ce_render_helper::get_instance()->render_feed_web_component('deadlines', $heading,
                get_string('nodeadlines', 'theme_snap'), $virtualpaging,
                true, 0, $location);
        } else {
            if ($location == 'snapfeedsmenu') {
                $o = "<h2>$heading</h2>";
                $o .= '<div id="snap-feeds-menu-deadlines"></div>';
            } else {
                $o = "<h2>$heading</h2>";
                $o .= '<div id="snap-sidebar-menu-deadlines"></div>';
            }
        }

        $calurl = $CFG->wwwroot.'/calendar/view.php?view=month';
        $o .= $this->column_header_icon_link('viewcalendar', 'calendar-new', $calurl, $location);
        return $o;
    }


    /**
     * Print login button
     *
     */
    public function login_button() {
        global $CFG;

        $output = '';
        $loginurl = $CFG->wwwroot.'/login/index.php';
        $loginatts = [
            'class' => 'btn btn-primary snap-login-button',
            'role' => 'button',
        ];

        // This check is here for the front page login.
        if (!isloggedin() || isguestuser()) {
            $output = html_writer::link($loginurl, get_string('login'), $loginatts);
        }
        return $output;
    }

    /**
     * @param login_alternative_methods $methods
     * @return string
     */
    public function render_login_alternative_methods(login_alternative_methods $methods) {
        if (empty($methods->potentialidps)) {
            return '';
        }
        return $this->render_from_template('theme_snap/login_alternative_methods', $methods);
    }
    public function render_login_base_method() {
        global $CFG;
        // Return login form.
        if (empty($CFG->loginhttps)) {
            $wwwroot = $CFG->wwwroot;
        } else {
            $wwwroot = str_replace("http://", "https://", $CFG->wwwroot);
        }

        $action = s($wwwroot).'/login/index.php';

        $logintoken = is_callable(['\\core\\session\\manager', 'get_login_token']) ?
            \core\session\manager::get_login_token() : '';

        $data = (object) [
            'action' => $action,
            'logintoken' => $logintoken,
        ];
        return $this->render_from_template('theme_snap/login_base_methods', $data);
    }

    // BEGIN LSU Course Card Quick Links.
    /**
     * Get the links to show on course cards.
     */
    public function get_quick_links($course) {
        $ccard = new course_card($course);
        return $ccard->get_course_quick_links();
    }
    // END LSU Course Card Quick Links.

    /**
     * Personal menu or authenticate form.
     *
    
    public function personal_menu() {
        // BEGIN LSU Course Card Quick Links.
        global $USER, $CFG;

        // Use the can view gradebook and can manage activities as the capability we check for.
        $caps = ['gradereport/grader:view', 'moodle/course:manageactivities'];
        // END LSU Course Card Quick Links.

        if (!isloggedin() || isguestuser()) {
            return '';
        }

        // User image.
        $userpicture = new user_picture($USER);
        $userpicture->link = false;
        $userpicture->alttext = false;
        $userpicture->size = 90;
        $picture = $this->render($userpicture);

        // User name and link to profile.
        // To the DOM structure, only one H1 can exists in it, so this link
        // can not act as a header, so no role="heading" attribute can be
        // assigned to it.
        $fullnamelink = '<a href="' .s($CFG->wwwroot). '/user/profile.php"
                    title="' .s(get_string('viewyourprofile', 'theme_snap')). '"
                    class="h1" id="snap-pm-user-profile">'
                    .format_string(fullname($USER)). '</a>';

        // Real user when logged in as.
        $realfullnamelink = '';
        if (\core\session\manager::is_loggedinas()) {
            $realuser = \core\session\manager::get_realuser();
            $realfullnamelink = '<br>' .get_string('via', 'theme_snap'). ' ' .format_string(fullname($realuser, true));
        }

        // User quicklinks.
        // We need to access the User id page.
        $userid = $USER->id;
        $profilelink = [
            'id' => 'snap-pm-profile',
            'link' => s($CFG->wwwroot). '/user/profile.php?id=' .$userid,
            'title' => get_string('profile'),
        ];
        $quicklinks = [$profilelink];
        // We need to verify the existence of My Account plugin in the code base to display this.
        if ((has_capability('moodle/site:config', context_system::instance())) &&
            (\core_component::get_component_directory('local_myaccount') !== null) &&
            is_callable('mr_on') &&
            mr_on("myaccount", "_MR_LOCAL")) {
            $myaccountlink = [
                'id' => 'snap-pm-myaccount',
                'link' => s($CFG->wwwroot) . '/local/myaccount/view.php?controller=default&action=view',
                'title' => get_string('myaccount', 'local_myaccount'),
            ];
            $quicklinks[] = $myaccountlink;
        }
        $dashboardlink = [
            'id' => 'snap-pm-dashboard',
            'link' => s($CFG->wwwroot). '/my',
            'title' => get_string('myhome'),
        ];
        $quicklinks[] = $dashboardlink;
        $gradelink = [
            'id' => 'snap-pm-grades',
            'link' => s($CFG->wwwroot). '/grade/report/overview/index.php',
            'title' => get_string('grades'),
        ];
        $quicklinks[] = $gradelink;
        $preferenceslink = [
            'id' => 'snap-pm-preferences',
            'link' => s($CFG->wwwroot). '/user/preferences.php',
            'title' => get_string('preferences'),
        ];
        $quicklinks[] = $preferenceslink;
        $logoutlink = [
            'id' => 'snap-pm-logout',
            'link' => s($CFG->wwwroot).'/login/logout.php?sesskey='.sesskey(),
            'title' => get_string('logout'),
        ];

        if (is_callable('mr_on') && mr_on('catalogue', '_MR_LOCAL')) {
            $coursecataloguelink = [
                'id' => 'snap-pm-course-catalogue',
                'link' => s($CFG->wwwroot) . '/local/catalogue/index.php',
                'title' => get_string('pluginname', 'local_catalogue'),
            ];
            $quicklinks[] = $coursecataloguelink;
        }
        if (is_callable('mr_on') && mr_on('programs', '_MR_ENROL')) {
            $programcataloguelink = [
                'id' => 'snap-pm-program-catalogue',
                'link' => s($CFG->wwwroot) . '/enrol/programs/catalogue/index.php',
                'title' => get_string('catalogue', 'enrol_programs'),
            ];
            $quicklinks[] = $programcataloguelink;
        }
        if (is_callable('mr_on') && mr_on('myprograms', '_MR_BLOCKS')) {
            $myprogramslink = [
                'id' => 'snap-pm-my-programs',
                'link' => s($CFG->wwwroot) . '/enrol/programs/my/index.php',
                'title' => get_string('pluginname', 'block_myprograms'),
            ];
            $quicklinks[] = $myprogramslink;
        }

        $courseid = $this->page->course->id;
        $coursecontext = context_course::instance($courseid);
        if (has_capability('moodle/role:switchroles', $coursecontext) || is_role_switched($courseid)) {
            $returnurl = $this->page->url->out_as_local_url(false);
            if (!is_role_switched($courseid)) {
                $link = new moodle_url('/course/switchrole.php', [
                    'id' => $courseid,
                    'sesskey' => sesskey(),
                    'switchrole' => -1,
                    'returnurl' => $returnurl,
                ]);
                $switchrole = [
                    'id' => 'snap-pm-switchroleto',
                    'link' => $link->out(false),
                    'title' => get_string('switchroleto'),
                ];
            } else {
                $link = new moodle_url('/course/switchrole.php', [
                    'id' => $courseid,
                    'sesskey' => sesskey(),
                    'switchrole' => 0,
                    'returnurl' => $returnurl,
                ]);
                $switchrole = [
                    'id' => 'snap-pm-switchrolereturn',
                    'link' => $link->out(false),
                    'title' => get_string('switchrolereturn'),
                ];
            }
            $quicklinks[] = $switchrole;
        }
        $quicklinks[] = $logoutlink;

        // Build up courses.
        $courseservice = course::service();
        [$pastcourses, $favorited, $notfavorited] = $courseservice->my_courses_split_by_favorites();
        // If we have past course, the template needs a variable.
        $coursenav = !empty($pastcourses);

        // Current courses data.
        // Note, we have to do this before we build up past or hidden courses so that the first 12 card images viewed
        // are loaded immediately - see course_card.php renderable and static $count.
        $currentcourses = $favorited + $notfavorited;
        $published = []; // Published course & favorites when user visible.
        $hidden = []; // Hidden courses.

        // BEGIN LSU Extra Course Tabs.
        // Get the debugging settings.
        $debugging  = $CFG->debugdisplay == 1 && is_siteadmin() ? 1 : 0;

        // Get the tab settings.
        $et1      = get_config('theme_snap', 'extratab1toggle');
        $et2      = get_config('theme_snap', 'extratab2toggle');
        $et3      = get_config('theme_snap', 'extratab3toggle');
        $dl1      = get_config('theme_snap', 'extratab1datelimits');
        $dl2      = get_config('theme_snap', 'extratab2datelimits');
        $dl3      = get_config('theme_snap', 'extratab3datelimits');
        $enr1     = get_config('theme_snap', 'extratab1enrolled');
        $enr2     = get_config('theme_snap', 'extratab2enrolled');
        $enr3     = get_config('theme_snap', 'extratab3enrolled');
        $et3repop = get_config('theme_snap', 'extratab3repop');
        // If extra tab 1 is enabled.
        if ($et1) {
            // Get the courses for extra tab 1.
            $et1courses = course::get_et_courses('extratab1', $et1, $dl1);
            // If we have any courses, merge them into the overall extra tabs array.
            if (isset($etcourses)) {
                $etcourses = array_merge($etcourses, $et1courses);
            } else {
                $etcourses = $et1courses;
            }
        }
        // If extra tab 2 is enabled.
        if ($et2) {
            // Get the courses for extra tab 2.
            $et2courses = course::get_et_courses('extratab2', $et2, $dl2);
            // If we have any courses, merge them into the overall extra tabs array.
            if (isset($etcourses)) {
                $etcourses = array_merge($etcourses, $et2courses);
            } else {
                $etcourses = $et2courses;
            }
        }
        // If extra tab 3 is enabled.
        if ($et3) {
            // Get the courses for extra tab 3.
            $et3courses = course::get_et_courses('extratab3', $et3, $dl3);
            // If we have any courses, merge them into the overall extra tabs array.
            if (isset($etcourses)) {
                $etcourses = array_merge($etcourses, $et3courses);
            } else {
                $etcourses = $et3courses;
            }
        }
        // END LSU Extra Course Tabs.
        
        foreach ($currentcourses as $course) {
            // BEGIN LSU Extra Course Tabs.
            // If we have extra courses, remove them from the array.
            if (isset($etcourses)) {
                // Loop through the extra tab courses.
                foreach ($etcourses as $etcourse) {
                    // Check for a match.
                    if ($course->id == $etcourse->id) {
                        // Remove it.
                        unset($course);
                        break 1;
                    }
                }
            }
            // If we have a course.
            if (isset($course)) {
                // Build a course card for it.
                $ccard = new course_card($course);
                if (isset($favorited[$course->id]) || $course->visible) {
                    $published[] = $ccard;
                }
            }
            // END LSU Extra Course Tabs.
        }

        // BEGIN LSU Extra Course Tabs.
        // Set this helper to false for now.
        $et3empty = false;

        if ($et3 && $et3repop) {
            // If we have 0 published courses and 0 current coutrses + we have courses in tab 3.
            if (count($published) == 0 && isset($et3courses)) {
                foreach ($et3courses as $course) {
                    // Get the course context for this course.
                    $coursecontext = context_course::instance($course->id);
                    // Check to see if the logged in user has any of thise caps anywhere.
                    $canmanageacts = has_any_capability($caps, $coursecontext);
                    // Students cannot manage those things.
                    $isstudent = !$canmanageacts;
                    if (isset($course)) {
                        $ccard = new course_card($course);
                        $et3empty = true;
                        // Add the visible or favorited courses to the main tab.
                        if (isset($favorited[$course->id]) || $course->visible) {
                            $published[] = $ccard;
                        }
                        // Add the hidden courses to the hidden tab for faculty.
                        if (!isset($favorited[$course->id]) && !$course->visible && !$isstudent) {
                            $hidden[] = $ccard;
                        }
                    }
                }
            }
        }
        // END LSU Extra Course Tabs.

        foreach ($currentcourses as $course) {
            // BEGIN LSU Extra Course Tabs.
            if (isset($etcourses)) {
                foreach ($etcourses as $etcourse) {
                    if ($course->id == $etcourse->id) {
                        unset($course);
                          break 1;
                    }
                }
            }
            // END LSU Extra Course Tabs.
            if (isset($course)) {
                $ccard = new course_card($course);
                if (!isset($favorited[$course->id]) && !$course->visible) {
                    $hidden[] = $ccard;
                }
            }
        }

        $currentcourses = [];
        if ($published) {
            $currentcourses = [
                'count' => count($published),
                'courses' => $published,
            ];
        }

        $hiddencourses = [];
        if ($hidden) {
            $hiddencourses = [
                'count' => count($hidden),
                'courses' => $hidden,
            ];
        }

        // BEGIN LSU Extra Course Tabs.
        // Are we using remote courses?
        $rc      = get_config('theme_snap', 'remotecoursestoggle');
        // Is it opt-in or not?
        $rcoptin = get_config('theme_snap', 'remotecoursesoptin');

        // Get the STORED user preference DO NOT CACHE OR USE USER object.
        $fieldvalue = isset($USER->profile['snap_remotecourses']) ? $USER->profile['snap_remotecourses'] : "I was in the pool!";

        if ($rcoptin) {
            // Make sure the preference is EXPRESSLY set to show, otherwise hide.
            $userc = $fieldvalue === "Opt-In" ? 1 : 0;
        } else {
            // Make sure the preference is EXPRESSLY set to hide, otherwise show.
            $userc = $fieldvalue === "Opt-Out" ? 0 : 1;
        }

        // Get the remote course cache timeout value.
        $rcdays = get_config('theme_snap', 'cachetimeout');
        $rcdays = $rcdays ? $rcdays : 10;
        $rccache = 86400 * $rcdays;

        // If we want to grab remote courses, do it.
        if ($rc && $userc == 1) {
            global $DB;

            // Grab the other courses from the local cache.
            $rccontainer = $DB->get_record('theme_snap_remotes', array('userid' => $USER->id));

            // If we have data for the user.
            if ($rccontainer) {

                // Decode the data for future use.
                $rccourses = json_decode($rccontainer->rcjson);

            // We have no stored local record.
            } else {

                // Set the data as false.
                $rccourses = false;
            }

            // We have no stored local record.
            if (!$rccontainer) {

                if ($debugging) {
                    echo("<span class = 'snap_debug_hidden'>We have no local cache for $USER->username.<br></span>");
                }

                // Fetch the remote courses.
                $remotecourses = course::get_remote_courses($USER, $debugging);

                // Insert the record.
                $inserted = course::insert_remote_courses(json_encode($remotecourses), $USER->id);

                // If we have an exception, set remote courses to false.
                if (isset($remotecourses->exception)) {
                    $remotecourses = false;
                }

            // We have a valid stored cache, but no courses in the remote system for this user.
            } else if (isset($rccourses->exception) && $rccontainer->lastupdated > (time() - $rccache)) {

                if ($debugging) {
                    echo("<span class = 'snap_debug_hidden'>We have a valid exception cache stored for $USER->username.<br></span>");
                }

                // If we have a valid cached exception, set remote courses to false.
                $remotecourses = false;

            // We have an expired stored local record.
            } else if ($rccontainer->lastupdated < (time() - $rccache)) {

                if ($debugging) {
                    echo("We have an expired cache for $USER->username.<br>");
                }

                // Fetch the remote courses.
                $remotecourses = course::get_remote_courses($USER, $debugging);

                // Update the local record.
                $updated = course::update_remote_courses(json_encode($remotecourses), $USER->id, $rccontainer->id);

                // If we have an exception, set remote courses to false.
                if (isset($remotecourses->exception)) {
                    $remotecourses = false;
                }

                // We are good, we just use the cached record.
            } else {

                if ($debugging) {
                    echo("We have a valid cache for $USER->username, use it.<br>");
                }

                // Decode the cached record.
                $remotecourses = $rccourses;
            }

            // Make sure the name has no spaces.
            $rcname = preg_replace('/\s+/', '-', get_string('remotecourses', 'theme_snap'));

            // Get the config from the remote courses plugin config.
            $lpid = get_config('theme_snap', 'localproxy');

            // Get the course object for the local proxy course.
            $localproxy = get_course($lpid);

            // Loop through the other courses and fill in the info from the local proxy as needed.
            if ($remotecourses) {
                foreach($remotecourses as $remotecourse) {
                    $remotecourse->remoteid  = $remotecourse->id;
                    $remotecourse->category  = $localproxy->category;
                    $remotecourse->id        = $localproxy->id;
                    $remotecourse->idnumber  = $localproxy->idnumber;
                    $remotecourse->startdate = $localproxy->startdate;
                    $remotecourse->enddate   = $localproxy->enddate;
                    $remotecourse->endyear   = $rcname;
                    $ccard = new course_card($remotecourse);
                    $ccard->archived = true;
                    $pastcourses[$rcname][] = $remotecourse;
                }
            }
        }

        // Get the extra tab 1 setting.
        $et1name = preg_replace('/\s+/', '-', get_config('theme_snap', 'extratab1name'));
        $et2name = preg_replace('/\s+/', '-', get_config('theme_snap', 'extratab2name'));
        $et3name = preg_replace('/\s+/', '-', get_config('theme_snap', 'extratab3name'));

        // If we want to grab and show extra tab 1, do it.
        if ($et1) {
            // Grab the courses for extra tab 1.
            $et1courses = course::get_et_courses('extratab1', $enr1, $dl1);

            // Loop through the other courses and fill in the info from the local proxy as needed.
            foreach($et1courses as $et1course) {
                $et1course->endyear   = $et1name;
                $ccard = new course_card($et1course);
                $ccard->archived = true;
                $pastcourses[$et1name][] = $et1course;
            }
        }

        if ($et2) {
            // Grab the courses for extra tab 2.
            $et2courses = course::get_et_courses('extratab2', $enr2, $dl2);

            // Loop through the other courses and fill in the info from the local proxy as needed.
            foreach($et2courses as $et2course) {
                $et2course->endyear   = $et2name;
                $ccard = new course_card($et2course);
                $ccard->archived = true;
                $pastcourses[$et2name][] = $et2course;
            }
        }

        if ($et3 && $et3empty == false) {
            // Grab the courses for extra tab 3.
            $et3courses = course::get_et_courses('extratab3', $enr3, $dl3);

            // Loop through the other courses and fill in the info from the local proxy as needed.
            foreach($et3courses as $et3course) {
                // Get the course context for this course.
                $coursecontext = context_course::instance($et3course->id);
                // Check to see if the logged in user has any of thise caps anywhere.
                $canmanageacts = has_any_capability($caps, $coursecontext);
                // Students cannot manage those things.
                $isstudent = !$canmanageacts;

                // If you are not a student, load the course regardless of the visibility.
                if (!$isstudent) {
                    $et3course->endyear   = $et3name;
                    $ccard = new course_card($et3course);
                    $ccard->archived = true;
                    $pastcourses[$et3name][] = $et3course;
                // If you are a student, only load visible courses.
                } else if ($et3course->visible == 1 && $isstudent) {
                    $et3course->endyear   = $et3name;
                    $ccard = new course_card($et3course);
                    $ccard->archived = true;
                    $pastcourses[$et3name][] = $et3course;
                }
            }
        }
        // END LSU Extra Course Tabs.

        // Past courses data.
        $pastcourselist = [];
        foreach ($pastcourses as $yearcourses) {
            // A courses array for each year.
            $courses = [];
            // Add course cards to each year.
            foreach ($yearcourses as $course) {
                $ccard = new course_card($course);
                $ccard->archived = true;
                $courses[] = $ccard;
            }
            $endyear = array_values($yearcourses)[0]->endyear;
            $year = (object) [
                 'year' => $endyear,
                 'courses' => $courses,
            ];
            // Append each year object.
            $pastcourselist[] = $year;
        }

        // When there are no currentcourses we set hiddencourses as the main list.
        if (!$currentcourses) {
            $currentcourses = $hiddencourses;
            $hiddencourses = '';
        }

        // We can only populate the currentcourselist if there is either currentcourses or hiddencourses available.
        // This is so the template will correctly show the coursefixydefaulttext when the user is not enrolled on any
        // visible or hidden courses.
        $currentcourselist = [];
        if (!empty($currentcourses) || !empty($hiddencourses)) {
            $currentcourselist = [
                'hidden' => $hiddencourses,
                'published' => $currentcourses,
            ];
        }

        $browseallcourses = '';
        if (!empty($CFG->navshowallcourses) || has_capability('moodle/site:config', context_system::instance())) {
            $url = new moodle_url('/course/');
            $browseallcourses = $this->column_header_icon_link('browseallcourses', 'courses', $url);
        }

        $maxcourses = !empty($CFG->theme_snap_bar_limit) ?
            $CFG->theme_snap_bar_limit : local::DEFAULT_COMPLETION_COURSE_LIMIT;
        $lowlimit = $maxcourses - 5;
        $courselimitclass = false;
        if (!empty($currentcourselist['published']['count'])) {
            $coursescount = $currentcourselist['published']['count'];
        } else {
            $coursescount = 0;
        }

        if ($coursescount > $maxcourses) {
            $courselimitclass = 'danger';
            $warningstring = get_string('courselimitstrdanger', 'theme_snap');
        } else if ($coursescount >= $lowlimit && $coursescount <= $maxcourses) {
            $courselimitclass = 'warning';
            $warningstring = get_string('courselimitstrwarning', 'theme_snap', $maxcourses);
        }

        // BEGIN LSU Extra Course Tabs.
        // Make sure we have the latest list of pastcourses to work from.
        $coursenav = !empty($pastcourses);
        // END LSU Extra Course Tabs.

        $data = (object) [
            'userpicture' => $picture,
            'fullnamelink' => $fullnamelink,
            'realfullnamelink' => $realfullnamelink,
            'quicklinks' => $quicklinks,
            'coursenav' => $coursenav,
            'currentcourselist' => $currentcourselist,
            'pastcourselist' => $pastcourselist,
            'browseallcourses' => $browseallcourses,
            'updates' => $this->render_callstoaction(),
            'advancedfeeds' => $this->advanced_feeds_enabled(),
        ];

        if ($courselimitclass) {
            $data->courselimitclass = $courselimitclass;
            $data->courselimitstr = $warningstring;
        }

        return $this->render_from_template('theme_snap/personal_menu', $data);
    }

    /**
     * Personal menu trigger - a login link or my courses link.
     *
     *
    public function personal_menu_trigger() {
        global $USER;
        $output = '';
        if (!isloggedin() || isguestuser()) {
            if (local::current_url_path() != '/login/index.php') {
                $output .= $this->login_button();
            }
        } else {
            $userpicture = new user_picture($USER);
            $userpicture->link = false;
            $userpicture->alttext = false;
            $userpicture->size = 40;
            $picture = $this->render($userpicture);

            $menu = '<span class="hidden-xs-down">' .get_string('menu', 'theme_snap'). '</span>';
            $linkcontent = $picture.$menu;
            $attributes = [
                'aria-haspopup' => 'true',
                'class' => 'js-snap-pm-trigger snap-my-courses-menu',
                'id' => 'snap-pm-trigger',
                'aria-controls' => 'snap-pm',
            ];
            $output .= html_writer::link('#', $linkcontent, $attributes);
        }
        return $output;
    }
    */

    /**
     * get section number by section id
     * @param int $sectionid
     * @return int|boolean (false if not found)
     */
    protected function get_section_for_id($sectionid) {
        global $COURSE;
        $modinfo = get_fast_modinfo($COURSE);
        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($thissection->id == $sectionid) {
                return $section;
            }
        }
        return false;
    }

    /**
     * Cover image selector.
     * @return bool|null|string
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function cover_image_selector() {
        if (has_capability('moodle/course:changesummary', $this->page->context)) {
            $vars = ['accepttypes' => local::supported_coverimage_typesstr()];
            return $this->render_from_template('theme_snap/cover_image_selector', $vars);
        }
        return null;
    }

    /**
     * Cover carousel.
     * @return string
     */
    public function cover_carousel() {
        if (empty($this->page->theme->settings->cover_carousel)) {
            return '';
        }

        $slidenames = ["slide_one", "slide_two", "slide_three"];
        $slides = [];
        $i = 0;
        foreach ($slidenames as $slidename) {
            $image = $slidename . '_image';
            $title = $slidename . '_title';
            $subtitle = $slidename . '_subtitle';
            if (!empty($this->page->theme->settings->$image) && !empty($this->page->theme->settings->$title)) {
                $slide = (object) [
                    'index' => $i++,
                    'active' => '',
                    'name' => $slidename,
                    'image' => $this->page->theme->setting_file_url($image, $image),
                    'title' => $this->page->theme->settings->$title,
                    'subtitle' => $this->page->theme->settings->$subtitle,
                ];
                $slides[] = $slide;
            }
        }
        $carouselhidecontrols = '';
        $carouselindicatorsbtn = count($slides);
        if ($carouselindicatorsbtn < 2) {
            // Add a class to hide the control buttons when only exists one slide,
            // with two or three the play and pause buttons will be displayed.
            $carouselhidecontrols = 'carouselhidecontrols';
        }
        if (empty($slides)) {
            return '';
        }
        $slides[0]->active = 'active';
        $carouselsronlytext = get_string('covercarouselsronly', 'theme_snap');
        $carouselplaybutton = get_string('covercarouselplaybutton', 'theme_snap');
        $carouselpausebutton = get_string('covercarouselpausebutton', 'theme_snap');
        $covercarousellabel = get_string('covercarousellabel', 'theme_snap');
        $data = ['carouselsronlytext' => $carouselsronlytext,
                'carouselplaybutton' => $carouselplaybutton,
                'carouselpausebutton' => $carouselpausebutton,
                'covercarousellabel' => $covercarousellabel,
                'carouselhidecontrols' => $carouselhidecontrols, ];
        $data['slides'] = $slides;
        return $this->render_from_template('theme_snap/carousel', $data);
    }

    /**
     * Login background slide images.
     * @return array
     */
    public function login_bg_slides() {
        if (empty($this->page->theme->settings->loginbgimg)) {
            return '';
        }
        $fs = get_file_storage();
        $files = $fs->get_area_files(\context_system::instance()->id, 'theme_snap', 'loginbgimg');
        $images = [];

        foreach ($files as $file) {
            if ($file->get_filename() != '.') {
                $images[] = moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(),
                    $file->get_filearea(), $file->get_itemid(), $file->get_filepath(), $file->get_filename(),
                    false)->out(false);
            }
        }
        return $images;
    }

    public function login_carousel_first() {
        if (empty($this->page->theme->settings->loginbgimg)) {
            return '';
        }
        $fs = get_file_storage();
        $files = $fs->get_area_files(\context_system::instance()->id, 'theme_snap', 'loginbgimg');
        foreach ($files as $file) {
            if ($file->get_filename() != '.') {
                return moodle_url::make_pluginfile_url($file->get_contextid(), $file->get_component(), $file->get_filearea(),
                    $file->get_itemid(), $file->get_filepath(), $file->get_filename(), false)->out(false);
            }
        }
        return ''; // Empty return to avoid errors.
    }

    /**
     * Renders the context header for the page.
     *
     * @param array $headerinfo Heading information.
     * @param int $headinglevel What 'h' level to make the heading.
     * @return string A rendered context header.
     */
    public function context_header($headerinfo = null, $headinglevel = 1): string {
        global $USER, $CFG;
        require_once($CFG->dirroot . '/user/lib.php');
        $context = $this->page->context;
        $imagedata = null;
        $userbuttons = null;
        // Make sure to use the heading if it has been set.
        if (isset($headerinfo['heading'])) {
            $heading = $headerinfo['heading'];
        } else {
            $heading = $this->page->heading;
        }

        $prefix = null;
        if ($context->contextlevel == CONTEXT_MODULE) {
            if ($this->page->course->format === 'singleactivity') {
                $heading = format_string($this->page->course->fullname, true, ['context' => $context]);
            } else {
                $heading = $this->page->cm->get_formatted_name();
                $iconurl = $this->page->cm->get_icon_url();
                $iconclass = $iconurl->get_param('filtericon') ? '' : 'nofilter';
                $iconattrs = [
                    'class' => "icon activityicon $iconclass",
                    'aria-hidden' => 'true'
                ];
                $imagedata = html_writer::img($iconurl->out(false), '', $iconattrs);
                $purposeclass = plugin_supports('mod', $this->page->activityname, FEATURE_MOD_PURPOSE);
                $purposeclass .= ' activityiconcontainer icon-size-6';
                $purposeclass .= ' modicon_' . $this->page->activityname;
                $isbranded = component_callback('mod_' . $this->page->activityname, 'is_branded', [], false);
                $imagedata = html_writer::tag('div', $imagedata, ['class' => $purposeclass . ($isbranded ? ' isbranded' : '')]);
                if (!empty($USER->editing)) {
                    $prefix = get_string('modulename', $this->page->activityname);
                }
            }
            // Return the heading wrapped in an sr-only element so it is only visible to screen-readers.
            if (!empty($this->page->layout_options['nocontextheader'])) {
                return html_writer::div($heading, 'sr-only');
            }

            $contextheader = new \context_header($heading, $headinglevel, $imagedata, $userbuttons, $prefix);
            return $this->render($contextheader); // Only context header for course modules.
        } else if ($context->contextlevel == CONTEXT_COURSE) {
          return parent::context_header($headerinfo, $headinglevel);
        }
        return ''; // Any other case we fall back to the Snap header.
    }

    /**
     * Get page heading.
     *
     * @param string $tag
     * @return string
     */
    public function page_heading($tag = 'h1') {
        global $COURSE;

        $heading = $this->page->heading;
        $pagetype = $this->page->pagetype;

        if ($this->page->pagelayout == 'mypublic' && $COURSE->id == SITEID) {
            // For the user profile page message button we need to call 2.9 content_header.
            $heading = parent::context_header();
        } else if (($COURSE->id != SITEID
            && (stripos($heading, format_string($COURSE->fullname)) === 0)
            || $pagetype === 'course-view-section-topics')) {
            // If we are on a course page which is not the site level course page.
            $courseurl = new moodle_url('/course/view.php', ['id' => $COURSE->id]);
            $heading = format_string($COURSE->fullname);
            $heading = html_writer::link($courseurl, $heading);
            if (!$this->snap_page_is_activity_view() && !$this->snap_page_is_edit_section() && !$this->snap_page_is_activity_mod() && !$this->snap_page_is_user_view()) {
                $heading = $this->context_header(['heading' => $heading]);
            } else {
                $heading = html_writer::tag($tag, $heading);
            }
        } else {
            // Default heading.
            $heading = html_writer::tag($tag, $heading);
        }

        // If we are on the main page of a course, add the cover image selector.
        if ($COURSE->id != SITEID) {
            $courseviewpage = local::current_url_path() === '/course/view.php';
            if ($courseviewpage) {
                $heading .= $this->cover_image_selector();
            }
        }

        // For the front page we add the site strapline.
        if ($this->page->pagelayout == 'frontpage') {
            $heading .= '<p class="snap-site-description">' . format_string($this->page->theme->settings->subtitle) . '</p>';
        }
        if ($this->page->user_is_editing() && $this->page->pagelayout == 'frontpage') {
            $url = new moodle_url('/admin/settings.php', ['section' => 'themesettingsnap']);
            $link = html_writer::link($url,
                            get_string('changefullname', 'theme_snap'),
                            ['class' => 'btn btn-secondary btn-sm']);
            $heading .= $link;
        }

        // Set core heading to Gradebook.
        if (strpos($pagetype, 'grade-report-') === 0 || strpos($pagetype, 'grade-edit-') === 0) {
            // If we are in a Gradebook page set default header.
            $heading = parent::context_header();
            $heading = $this->snap_make_coursename_link($heading);
        }

        return $heading;
    }


    public function favicon() {
        // Allow customized favicon from settings.
        $url = $this->page->theme->setting_file_url('favicon', 'favicon');
        return empty($url) ? parent::favicon() : $url;
    }

    /**
     * Renders custom menu as a navigation bar.
     *
     * @return string
     */
    protected function render_custom_menu(\custom_menu $menu) {
        if (!$menu->has_children()) {
            return '';
        }

        // We need to create this part of HTML here or multiple nav tags will exist for each item.
        $content = '<nav class="navbar navbar-expand-lg navbar-light">';
        $content .= '<div class="container-fluid">';
        $content .= '<ul class="navbar-collapse clearfix snap-navbar-content">';
        foreach ($menu->get_children() as $item) {
            $context = $item->export_for_template($this);
            $content .= $this->render_from_template('theme_snap/custom_menu_item', $context);
        }

        return $content.'</ul>'.'</div>'.'</nav>';
    }

    /**
     * Alternative rendering of front page news, called from layout/faux_site_index.php which
     * replaces the standard news output with this.
     *
     * @return string
     */
    public function site_frontpage_news() {
        global $CFG, $SITE;

        require_once($CFG->dirroot.'/mod/forum/lib.php');

        if (!$forum = forum_get_course_forum($SITE->id, 'news')) {
            throw new moodle_exception('cannotfindorcreateforum', 'forum');
        }
        $cm      = get_coursemodule_from_instance('forum', $forum->id, $SITE->id, false, MUST_EXIST);
        $context = \context_module::instance($cm->id, MUST_EXIST);

        $output  = html_writer::start_tag('div', ['id' => 'site-news-forum', 'class' => 'clearfix']);
        $output .= $this->heading(format_string($forum->name, true, ['context' => $context]));

        $groupmode    = groups_get_activity_groupmode($cm, $SITE);
        $currentgroup = groups_get_activity_group($cm);

        if (!$discussions = forum_get_discussions($cm,
            'p.modified DESC', true, null, $SITE->newsitems, false, -1, $SITE->newsitems)) {
            $output .= html_writer::tag('div', '('.get_string('nonews', 'forum').')', ['class' => 'forumnodiscuss']);

            if (forum_user_can_post_discussion($forum, $currentgroup, $groupmode, $cm, $context)) {
                $output .= html_writer::link(
                    new moodle_url('/mod/forum/post.php', ['forum' => $forum->id]),
                    get_string('addanewtopic', 'forum'),
                    ['class' => 'btn btn-primary']
                );
            } else {
                // No news and user cannot edit, so return nothing.
                return '';
            }

            return $output.'</div>';
        }

        $output .= html_writer::start_div('', ['id' => 'news-articles']);

        $counter = 0;
        foreach ($discussions as $discussion) {
            if (!forum_user_can_see_discussion($forum, $discussion, $context)) {
                continue;
            }
            $message    = file_rewrite_pluginfile_urls($discussion->message,
                          'pluginfile.php', $context->id, 'mod_forum', 'post', $discussion->id);

            $imageurl = '';

            $imgarr = \theme_snap\local::extract_first_image($message);
            if ($imgarr) {
                $imageurl   = s($imgarr['src']);
            }

            $name    = format_string($discussion->name, true, ['context' => $context]);
            $date    = userdate($discussion->modified, get_string('strftimedatetime', 'langconfig'));

            $message = format_text($message, $discussion->messageformat, ['context' => $context]);

            $readmorebtn = "<a tabindex='0' role='button' aria-expanded='false' aria-controls='news-article-message-id-{$counter}'
             class='btn btn-secondary toggle' href='".$CFG->wwwroot."/mod/forum/discuss.php?d=".$discussion->discussion."'>".
                get_string('readmore', 'theme_snap')."</a>";

            $preview = '';
            $newsimage = '';
            if (!$imageurl) {
                $preview = html_to_text($message, 0, false);
                $preview = "<div class='news-article-preview'><p>".shorten_text($preview, 200)."</p>
                <p class='text-right'>".$readmorebtn."</p></div>";
            } else {
                $newsimage = "<img class='news-article-image toggle' tabindex='0' role='button'".
                'alt="'. get_string('readmore', 'theme_snap').'" src="'. $imageurl.'">';
            }
            $close = get_string('closebuttontitle', 'moodle');

            $newsinner = <<<HTML
    <div class="news-article-inner">
        <div class="news-article-content">
            <h3 class='toggle'>
                <a role="button" tabindex='0' aria-expanded="false" aria-controls="news-article-message-id-{$counter}"
                href="$CFG->wwwroot/mod/forum/discuss.php?d=$discussion->discussion">{$name}</a>
            </h3>
            <em class="news-article-date">{$date}</em>
        </div>
    </div>
HTML;

            if ($counter % 2 === 0) {
                $newsordered = $newsinner . $preview . $newsimage;
            } else {
                $newsordered = $newsimage . $preview . $newsinner;
            }

            $arialabelnews = get_string('arialabelnewsarticle', 'theme_snap');

            $output .= <<<HTML
<div class="news-article clearfix" role="group" aria-label="$arialabelnews">
    {$newsordered}
    <div id="news-article-message-id-{$counter}" class="news-article-message" tabindex="-1">
        {$message}
        <div><hr><a role="button" tabindex='0' class="snap-action-icon snap-icon-close toggle" href="#">
        <small>{$close}</small></a></div>
    </div>
</div>
HTML;
            $counter++;
        }
        $actionlinks = html_writer::link(
            new moodle_url('/mod/forum/view.php', ['id' => $cm->id]),
            get_string('morenews', 'theme_snap'),
            ['class' => 'btn btn-secondary',
             'role' => 'button',
             'tabindex' => 0, ]
        );
        if (forum_user_can_post_discussion($forum, $currentgroup, $groupmode, $cm, $context)) {
            $actionlinks .= html_writer::link(
                new moodle_url('/mod/forum/post.php', ['forum' => $forum->id]),
                get_string('addanewtopic', 'forum'),
                ['class' => 'btn btn-primary',
                    'role' => 'button',
                    'tabindex' => 0, ]
            );
        }
        $output .= html_writer::end_div();
        $output .= "<br><div class='text-center'>$actionlinks</div>";
        $output .= html_writer::end_tag('div');

        return $output;
    }

    /**
     * add in additional classes that are used for Snap
     * get rid of YUI stuff so we can style it with bootstrap
     *
     * @param array $additionalclasses
     * @return array|string
     */
    public function body_css_classes(array $additionalclasses = []) {
        global $COURSE, $CFG, $USER;

        $classes = parent::body_css_classes($additionalclasses);
        $classes = explode (' ', $classes);

        $classes[] = 'device-type-'.$this->page->devicetypeinuse;

        // Define the page types we want to purge yui classes from the body  - e.g. local-joulegrader-view,
        // local-pld-view, etc.
        $killyuipages = [
            'local-pld-view',
            'local-joulegrader-view',
            'blocks-conduit-view',
            'blocks-reports-view',
            'admin-setting-modsettinglti',
            'blocks-campusvue-view',
            'enrol-instances',
            'admin-report-eventlist-index',
        ];
        if (in_array($this->page->pagetype, $killyuipages)) {
            $classes = array_diff ($classes, ['yui-skin-sam', 'yui3-skin-sam']);
            $classes[] = 'yui-bootstrapped';
        }

        if (!empty($this->page->url)) {
            $section = $this->page->url->param('section');
        }

        // Add completion tracking class.
        if (!empty($COURSE->enablecompletion)) {
            $classes[] = 'completion-tracking';
        }

        // Add resource display class.
        if (!empty($this->page->theme->settings->resourcedisplay)) {
            $classes[] = 'snap-resource-'.$this->page->theme->settings->resourcedisplay;
        } else {
            $classes[] = 'snap-resource-card';
        }

        // Add theme-snap class so modules can customise css for snap.
        $classes[] = 'theme-snap';

        if (get_config('theme_snap', 'coursepartialrender') && get_config('theme_snap', 'leftnav') == 'top'
            && $COURSE->format == 'topics') {
            $classes[] = 'no-number-toc';
        }

        if (!empty($CFG->allowcategorythemes)) {
            // This duplicates code triggered by allowcategorythemes, so no
            // need to repeat it if that setting is on.
            $catids = array_keys($this->page->categories);
            // Immediate parent category is always output by core code.
            array_shift($catids);
            foreach ($catids as $catid) {
                $classes[] = 'category-' . $catid;
            }
            // Put class category-x on body when loading editcategory page on course.
            // Categories and parent categories are added in ascendant order.
            if (strpos($this->page->url->get_path(), "course/editcategory.php") !== false
                && $this->page->url->get_param('id') !== null) {
                $parentcategories = self::get_parentcategories($this->page->url->get_param('id'));
                foreach ($parentcategories as $category) {
                    $classes[] = 'category-' . $category;
                }
            }

            // Put class category-x on body when loading add new course page.
            // Categories and parent categories are added in ascendant order.
            if (strpos($this->page->url->get_path(), "course/edit.php") !== false
                && $this->page->url->get_param('category') !== null) {
                $parentcategories = self::get_parentcategories($this->page->url->get_param('category'));
                foreach ($parentcategories as $category) {
                    $classes[] = 'category-' . $category;
                }
            }
        }

        // Add page layout.
        $classes[] = 'layout-'.$this->page->pagelayout;

        // Profile based branding.
        $pbbclass = local::get_profile_based_branding_class($USER);
        if (!empty($pbbclass)) {
            $classes[] = $pbbclass;
        }

        // Check if the custom menu is not empty.
        if (!empty($CFG->custommenuitems)) {
            $classes[] = 'contains-snap-custom_menu-spacer';
        }

        // Remove duplicates if necessary.
        $classes = array_unique($classes);

        $classes = implode(' ', $classes);
        return $classes;
    }

    /**
     * Returns all parent categories hierarchy from a category id
     * @param int $id
     * @return array
     * @throws \moodle_exception
     */
    private function get_parentcategories($id) {
        global $DB;
        if ($id == 0) {
            return [];
        }
        $category = $DB->get_record('course_categories', ['id' => $id]);
        if (!$category) {
            throw new \moodle_exception('unknowncategory');
        }
        $parentcategoryids = explode('/', trim($category->path, '/'));
        return $parentcategoryids;
    }

    /**
     * Override to add a class to differentiate from other
     * #notice.box.generalbox that have buttons after them,
     * rather than inside them.
     */
    public function confirm($message, $continue, $cancel, array $displayoptions = []) {
        // We need plain styling of confirm boxes on upgrade because we don't know which stylesheet we have (it could be
        // from any previous version of Moodle).
        if ($continue instanceof single_button) {
            $continue->type = single_button::BUTTON_PRIMARY;
        } else if (is_string($continue)) {
            $continue = new single_button(new moodle_url($continue), get_string('continue'), 'post', single_button::BUTTON_PRIMARY);
        } else if ($continue instanceof moodle_url) {
            $continue = new \single_button($continue, get_string('continue'), 'post', single_button::BUTTON_PRIMARY);
        } else {
            throw new coding_exception(
                'The continue param to $OUTPUT->confirm() must be either a URL (string/moodle_url) '
                . 'or a single_button instance.'
            );
        }

        if ($cancel instanceof single_button) {
            $output = '';
        } else if (is_string($cancel)) {
            $cancel = new single_button(new moodle_url($cancel), get_string('cancel'), 'get');
        } else if ($cancel instanceof moodle_url) {
            $cancel = new \single_button($cancel, get_string('cancel'), 'get');
        } else {
            throw new coding_exception(
                'The cancel param to $OUTPUT->confirm() must be either a URL (string/moodle_url) '
                . 'or a single_button instance.'
            );
        }

        $output = $this->box_start('generalbox snap-continue-cancel', 'notice');
        $output .= html_writer::tag('h4', get_string('confirm'));
        $output .= html_writer::tag('p', $message);
        $output .= html_writer::tag('div', $this->render($continue) . $this->render($cancel), ['class' => 'buttons']);
        $output .= $this->box_end();
        return $output;
    }

    public function image_url($imagename, $component = 'moodle') {
        // Strip -24, -64, -256  etc from the end of filetype icons so we
        // only need to provide one SVG, see MDL-47082.
        $imagename = \preg_replace('/-\d\d\d?$/', '', $imagename);
        return $this->page->theme->image_url($imagename, $component);
    }

    /**
     * Return feature spot cards html.
     *
     * @return string
     */
    public function feature_spot_cards() {
        $fsnames = ["fs_one", "fs_two", "fs_three", "fs_four", "fs_five", "fs_six"];
        $features = [];
        // Note - we are using underscores in the settings to make easier to read.

        foreach ($fsnames as $feature) {
            $title = $feature . '_title';
            $link = $feature . '_title_link';
            $cbopeninnewtab = $feature . '_title_link_cb';
            $text = $feature . '_text';
            $image = $feature . '_image';
            if (!empty($this->page->theme->settings->$title)) {
                $img = '';
                if (!empty($this->page->theme->settings->$image)) {
                    $url = $this->page->theme->setting_file_url($image, $image);
                    $img = '<!--Card image-->
                    <div class="snap-feature-image-wrap">
                        <img class="snap-feature-image" src="' .$url. '" alt="" role="presentation">
                    </div>';
                }
                $features[] = $this->feature_spot_card($this->page->theme->settings->$title,
                    $this->page->theme->settings->$link,
                    $this->page->theme->settings->$cbopeninnewtab,
                    $img,
                    $this->page->theme->settings->$text);
            }

        }

        $fscount = count($features);
        if ($fscount > 0) {
            $fstitle = '';
            if (!empty($this->page->theme->settings->fs_heading)) {
                $fstitle = '<h2 class="snap-feature-spots-heading">' . s($this->page->theme->settings->fs_heading) . '</h2>';
            }

            $colclass = 'col-sm-12'; // Default
            if ($fscount === 2) {
                $colclass = 'col-sm-6'; // Two cards = 50%.
            } elseif ($fscount === 3) {
                $colclass = 'col-sm-4'; // Three cards = 33.3%.
            } else {
                $colclass = 'col-sm-4'; // Default for more than 3 cards.
            }

            $extraClass = '';
            if ($fscount === 4) {
                $extraClass = 'col-sm-6'; // Fourth card = 50% when there are 4 cards.
            } elseif ($fscount === 5) {
                $extraClass = 'col-sm-6'; // Fourth and fifth cards = 50% when there are 5 cards.
            } elseif ($fscount === 6) {
                $extraClass = 'col-sm-4'; // Fourth, fifth, and sixth cards = 33.3% when there are 6 cards.
            }

            $cards = '';
            for ($i = 1; $i <= $fscount; $i++) {
                $feature = $features[$i - 1];
                // Open a new row every three cards.
                if ($i % 3 == 1) {
                    if ($i > 1) {
                        $cards .= '</div>'; // Close the previous row.
                    }
                    $cards .= '<div class="row py-4 justify-content-center">'; // Open a new row.
                }
                $currentcolclass = $colclass;
                if ($i > 3) {
                    $currentcolclass = $extraClass; // Apply the special class for the fourth, fifth, and sixth cards.
                }
                $cards .= '<div class="' . $currentcolclass . '" id="snap-feature-' . $i . '">' . $feature . '</div>';
            }
            $cards .= '</div>'; // Close the last row.

            $fsedit = '';
            if ($this->page->user_is_editing()) {
                $url = new moodle_url('/admin/settings.php', ['section' => 'themesettingsnap#themesnapfeaturespots']);
                $link = html_writer::link($url, get_string('featurespotsedit', 'theme_snap'), ['class' => 'btn btn-primary']);
                $link = rawurldecode($link);
                $fsedit = '<p class="text-center">' . $link . '</p>';
            }

            // Build feature spots.
            $featurespots = '<div id="snap-feature-spots">';
            $featurespots .= $fstitle;
            $featurespots .= $cards;
            $featurespots .= $fsedit;
            $featurespots .= '</div>';

            // Return feature spots.
            return $featurespots;
        }
    }

    /**
     * Return feature spot card html.
     *
     * @param string $title
     * @param string $link
     * @param string $cbopeninnewtab
     * @param string $image
     * @param string $text
     * @return string
     */
    protected function feature_spot_card($title, $link, $cbopeninnewtab, $image, $text) {

        $target = '';

        if ($cbopeninnewtab) {
            $target = "target='_blank'";
        }

        // Title with link.
        $linktitle = '<h3><a ' .$target. ' class="snap-feature-link h5 stretched-link" href="' .s($link). '">' .s($title). '</a></h3>';
        // Title without link.
        $nolinktitle = '<h3 class="snap-feature-title h5">' .s($title). '</h3>';
        // Content text for feature spots.
        $fscontenttext =
            '<p class="snap-feature-text">' . format_text($text, FORMAT_MOODLE, ['para' => false]) . '</p>';

        if ($link) {
            $card = '<div class="snap-feature">
                        <div class="snap-feature-block">' .$image.$linktitle.$fscontenttext. '</div>
                    </div>';
        } else {
            $card = '<div class="snap-feature">
                        <div class="snap-feature-block">' .$image.$nolinktitle.$fscontenttext. '</div>
                    </div>';
        }

        return $card;
    }

    /**
     * Return featured courses html.
     * There are intentionally no checks for hidden course status
     * OR current users enrolment status.
     *
     * @return string
     */
    public function render_featured_courses(featured_courses $fc) {
        if (empty($fc->cards)) {
            return '';
        }

        return $this->render_from_template('theme_snap/featured_courses', $fc);
    }

    /**
     * Return featured courses html.
     * There are intentionally no checks for hidden course status
     * OR current users enrolment status.
     *
     * @return string
     */
    public function render_featured_categories(featured_categories $fcat) {
        if (empty($fcat->cards)) {
            return '';
        }

        return $this->render_from_template('theme_snap/featured_categories', $fcat);
    }

    /**
     * Return snap modchooser modal.
     * @return string
     */
    protected function course_modchooser() {
        // @codingStandardsIgnoreStart
        // Core renderer has not $output attribute, but code checker requires it.
        global $COURSE, $OUTPUT, $USER;
        // Check to see if user can add menus and there are modules to add.
        if (!has_capability('moodle/course:manageactivities', context_course::instance($COURSE->id))
                || !($modnames = get_module_types_names()) || empty($modnames)) {
            return '';
        }
        // Retrieve all modules with associated metadata.
        $sectionreturn = null;

        foreach ($modnames as $module => $name) {
            if (is_callable('mr_off') && mr_off($module, '_MR_MODULES')) {
                unset($modnames[$module]);
            }
        }
        $contentitemservice = new \core_course\local\service\content_item_service(
            new \core_course\local\repository\content_item_readonly_repository()
        );
        $contentitems = $contentitemservice->get_content_items_for_user_in_course($USER, $COURSE);
        $resources = [];
        foreach ($contentitems as $mod) {
            $help = !empty($mod->help) ? $mod->help : '';
            $helptext = format_text($help, FORMAT_MARKDOWN);

            if ($mod->archetype === MOD_ARCHETYPE_RESOURCE) {
                $resources[] = (object) [
                    'name' => $mod->name,
                    'title' => $mod->title,
                    'icon' => ''.$OUTPUT->image_url('icon', $mod->name),
                    'link' => $mod->link .'&section=0', // Section is replaced by js.
                    'help' => $helptext
                ];
            } else if ($mod->archetype !== MOD_ARCHETYPE_SYSTEM) {
                // The name should be 'lti' instead of the module's URL which is the one we're getting.
                $imageurl = $OUTPUT->image_url('icon', $mod->name);
                if (strpos($mod->name, 'lti:') !== false) {
                    $imageurl = $OUTPUT->image_url('icon', 'lti');
                    if (preg_match('/src="([^"]*)"/i', $mod->icon, $matches)) {
                        $imageurl = $matches[1]; // Use the custom icon.
                    }
                }
                $activities[] = (object) [
                    'name' => $mod->name,
                    'title' => $mod->title,
                    'icon' => ''.$imageurl,
                    'link' => $mod->link .'&section=0', // Section is replaced by js.
                    'help' => $helptext
                ];
            }
        }

        return $this->course_activitychooser($COURSE->id);
        // @codingStandardsIgnoreEnd
    }

    /**
     * Build the HTML for the module chooser javascript popup.
     *
     * @param int $courseid The course id to fetch modules for.
     * @return string
     */
    public function course_activitychooser($courseid) {

        if (!$this->page->requires->should_create_one_time_item_now('core_course_modchooser')) {
            return '';
        }

        // Build an object of config settings that we can then hook into in the Activity Chooser.
        $chooserconfig = (object) [
            'tabmode' => get_config('core', 'activitychoosertabmode'),
        ];
        $this->page->requires->js_call_amd('core_course/activitychooser', 'init', [$courseid, $chooserconfig]);

        return '';
    }

    /**
     * Only for Unit testing purposes.
     */
    public function testhelper_course_modchooser() {
        if (defined('PHPUNIT_TEST') && PHPUNIT_TEST) {
            return $this->course_modchooser();
        }
    }

    /**
     * Override parent function so that all courses (except the front page) skip the 'turn editing on' button.
     */
    protected function render_navigation_node(navigation_node $item) {
        global $COURSE, $SITE;

        if ($item->action instanceof moodle_url) {
            // Hide the course 'turn editing on' link.
            $iscoursepath = $item->action->get_path() === '/course/view.php';
            $iseditlink = $item->action->get_param('edit') === 'on';
            $isfrontpage = $item->action->get_param('id') === SITEID;
            if ($iscoursepath && $iseditlink && !$isfrontpage) {
                return '';
            }

            // Remove links for Reports and Question Bank for INT-18042.
            $isreportslink = $item->key === 'coursereports';
            if ($isreportslink) {
                $item->action = null;
            }

        }

        // Bank content link where necessary (Front page - Course page - Category settings).
        $context = context_system::instance();

        $coursecatcontext = $this->page->context->contextlevel === CONTEXT_COURSECAT;

        if ($COURSE->id !== $SITE->id) {
            $context = context_course::instance($COURSE->id);
        }
        if ($coursecatcontext) {
            $context = $this->page->context;
        }

        if (has_capability('moodle/contentbank:access', $context)) {
            if (!in_array('contentbank', $item->get_children_key_list(), true) &&
                ($item->key === 'frontpage' || $item->key === 'courseadmin' || $item->key === 'categorysettings')) {
                $this->add_contentbank_navigation_node($item, $context->id);
            }
        }

        if ($item->key === 'courseadmin') {
            $this->add_switchroleto_navigation_node($item);
            if ($this->can_add_communication_node($context)){
                $this->add_communication_navigation_node($item, $context->id, $COURSE->id);
            }
        }

        $content = parent::render_navigation_node($item);
        if (strpos($content, 'fa-fw fa-fw')) {
            $content = str_replace('fa-fw fa-fw', 'fa-fw nav-missing-icon', $content);
        }
        return $content;
    }

    /**
     * Adds a content bank link to a navigation node.
     *
     * @param navigation_node $item
     * @param int $contextid
     */
    private function add_contentbank_navigation_node(navigation_node $item, $contextid) {
        $url = new moodle_url('/contentbank/index.php', ['contextid' => $contextid]);
        $item->add(get_string('contentbank'), $url, navigation_node::TYPE_CUSTOM, null, 'contentbank', new \pix_icon('brush', ''));
    }

    /**
     * Adds a switch role menu to a navigation node.
     * Inspiration taken from : lib/navigationlib.php
     * https://github.com/moodle/moodle/commit/70b03eff02a261b16130c52aca5cd87ebd810b5e
     *
     * @param navigation_node $item
     */
    private function add_switchroleto_navigation_node(navigation_node $item) {
        $course = $this->page->course;
        $coursecontext = context_course::instance($course->id);
        // Switch roles.
        $roles = [];
        $assumedrole = $this->in_alternative_role();
        if ($assumedrole !== false) {
            $roles[0] = get_string('switchrolereturn');
        }

        if (has_capability('moodle/role:switchroles', $coursecontext)) {
            $availableroles = get_switchable_roles($coursecontext);
            if (is_array($availableroles)) {
                foreach ($availableroles as $key => $role) {
                    if ($assumedrole == (int)$key) {
                        continue;
                    }
                    $roles[$key] = $role;
                }
            }
        }
        if (is_array($roles) && count($roles) > 0) {
            $switchroles = $item->add(get_string('switchroleto'), null, navigation_node::TYPE_CONTAINER, null, 'switchroleto');
            if ((count($roles) == 1 && array_key_exists(0, $roles)) || $assumedrole !== false) {
                $switchroles->force_open();
            }
            foreach ($roles as $key => $name) {
                $url = new moodle_url('/course/switchrole.php', [
                    'id' => $course->id, 'sesskey' => sesskey(),
                    'switchrole' => $key, 'returnurl' => $this->page->url->out_as_local_url(false),
                ]);
                $switchroles->add($name, $url, navigation_node::TYPE_SETTING, null, $key, new \pix_icon('i/switchrole', ''));
            }
        }
    }

    /**
     * Determine whether the user is assuming another role
     * Inspiration taken from : lib/navigationlib.php
     * https://github.com/moodle/moodle/commit/70b03eff02a261b16130c52aca5cd87ebd810b5e
     *
     * This function checks to see if the user is assuming another role by means of
     * role switching. In doing this we compare each RSW key (context path) against
     * the current context path. This ensures that we can provide the switching
     * options against both the course and any page shown under the course.
     *
     * @return bool|int The role(int) if the user is in another role, false otherwise
     */
    public function in_alternative_role() {
        global $USER;

        $course = $this->page->course;
        $coursecontext = context_course::instance($course->id);

        if (!empty($USER->access['rsw']) && is_array($USER->access['rsw'])) {
            if (!empty($this->page->context) && !empty($USER->access['rsw'][$this->page->context->path])) {
                return $USER->access['rsw'][$this->page->context->path];
            }
            foreach ($USER->access['rsw'] as $key => $role) {
                if (strpos($coursecontext->path, $key) === 0) {
                    return $role;
                }
            }
        }
        return false;
    }

    /**
     * Return Snap's logo url for login.mustache
     *
     * @param int $maxwidth not used in Snap.
     * @param int $maxheight not used in Snap.
     * @return moodle_url|false
     */
    public function get_logo_url($maxwidth = null, $maxheight = 200) {
        global $CFG;
        if (empty($this->page->theme->settings->logo)) {
            return false;
        }

        // Following code copied from  theme->setting_file_url but without the
        // bit that strips the protocol from the url.

        $itemid = theme_get_revision();
        $filepath = $this->page->theme->settings->logo;
        $syscontextid = context_system::instance()->id;

        $url = moodle_url::make_file_url("$CFG->httpswwwroot/pluginfile.php", "/$syscontextid/theme_snap/logo/$itemid".$filepath);
        return $url;
    }

    /**
     * Render intelliboard links in personal menu.
     * @return string
     */
    protected function render_intelliboard($location = '') {
        $o = '';
        $links = '';

        // Bail if no intelliboard.
        if (!get_config('local_intelliboard')) {
            return $o;
        }

        // Intelliboard adds links to the flatnav we use to check wich links to output.
        $nav = $this->page->navigation->find('myprofile', navigation_node::TYPE_ROOTNODE);
        $navlist = $nav->get_children_key_list();
        // Student dashboard link.
        if (in_array("intelliboard_student", $navlist, true)) {
            $node = $nav->get("intelliboard_student");
            $links .= $this->render_intelliboard_link($node->get_content(), $node->action(), 'intelliboard_learner', $location);
        }

        // Instructor dashboard link.
        if (in_array("intelliboard_instructor", $navlist, true)) {
            $node = $nav->get("intelliboard_instructor");
            $links .= $this->render_intelliboard_link($node->get_content(), $node->action(), 'intelliboard', $location);
        }

        // Competency dashboard link.
        if (in_array("intelliboard_competency", $navlist, true)) {
            $node = $nav->get("intelliboard_competency");
            $links .= $this->render_intelliboard_link($node->get_content(), $node->action(), 'intelliboard_competencies', $location);
        }

        // No links to display.
        if (!$links) {
            return $o;
        }

        $intelliboardheading = get_string('intelliboardroot', 'local_intelliboard');
        if ($location == 'snapfeedsmenu') {
            $o = '<h2>' .$intelliboardheading. '</h2>';
            $o .= '<div id="snap-feeds-menu-intelliboard">'
                .$links.
                '</div>';
        } else {
            $o = '<h2>' .$intelliboardheading. '</h2>';
            $o .= '<div id="snap-sidebar-menu-intelliboard">'
                .$links.
                '</div>';
        }

        return $o;
    }

    /**
     * Render intelliboard link in personal menu.
     * @param string $name of the link.
     * @param moodle_url $url of the link.
     * @param string $icon icon sufix.
     * @return string
     */
    public function render_intelliboard_link($name, $url, $icon, $location = '') {
        // @codingStandardsIgnoreStart
        // Core renderer has not $output attribute, but code checker requires it.
        global $OUTPUT, $CFG;
        $iconurl = $OUTPUT->image_url($icon, 'theme');
        $img = '<img class="svg-icon" role="presentation" src="'.$iconurl.'">';
        $snapfeedsurlparam = isset($CFG->theme_snap_feeds_url_parameter) ? $CFG->theme_snap_feeds_url_parameter : true;
        if ($location == 'snapfeedsmenu') {
            if ($snapfeedsurlparam) {
                $o = '<a href=" '.$url.'?snapfeedsclicked=on ">'.$img.s($name).'</a><br>';
            } else {
                $o = '<a href=" '.$url.' ">'.$img.s($name).'</a><br>';
            }
        } else {
            $o = '<a href=" '.$url.' ">'.$img.s($name).'</a><br>';
        }
        return $o;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Renders a wrap of the boost core notification popup area, which includes messages and notification popups
     * @return string notification popup area.
     */
    protected function render_notification_popups() {
        // @codingStandardsIgnoreStart
        // Core renderer has not $output attribute, but code checker requires it.
        global $OUTPUT, $CFG;

        $navoutput = '';
        if (\core_component::get_component_directory('local_intellicart') !== null) {
            require_once(__DIR__ . '/../../../../local/intellicart/lib.php');
            $navoutput .= local_intellicart_render_navbar_output($OUTPUT);
        }
        $messagingenabled = $CFG->messaging;
        $CFG->messaging = false;
        $navoutput .= message_popup_render_navbar_output($OUTPUT);
        $CFG->messaging = $messagingenabled;
        if (empty($navoutput)) {
            return '';
        }
        return $navoutput;
        // @codingStandardsIgnoreEnd
    }

    /**
     * Render intellicart link in personal menu.
     * @return string
     */
    protected function render_intellicart($location = '') {
        // @codingStandardsIgnoreStart
        // Core renderer has not $output attribute, but code checker requires it.
        global $OUTPUT, $CFG;
        $o = '';
        $link = '';

        // Prevent if no intellicart.
        if (\core_component::get_component_directory('local_intellicart') === null) {
            return $o;
        }

        // Intellicart adds a link to the flatnav.
        $nav = $this->page->navigation->find('myprofile', navigation_node::TYPE_ROOTNODE);
        $navlist = $nav->get_children_key_list();

        // Student dashboard link.
        if (in_array("intellicart_dashboard", $navlist, true)) {
            $node = $nav->get("intellicart_dashboard");
            $iconurl = $OUTPUT->image_url('intelliboard', 'theme');
            $img = '<img class="svg-icon" role="presentation" src="'.s($iconurl).'">';
            $snapfeedsurlparam = isset($CFG->theme_snap_feeds_url_parameter) ? $CFG->theme_snap_feeds_url_parameter : true;
            if ($location == 'snapfeedsmenu') {
                if ($snapfeedsurlparam) {
                    $link .= '<a href=" '. $node->action() .'?snapfeedsclicked=on ">'.$img.s($node->get_content()).'</a><br>';
                } else {
                    $link .= '<a href=" '. $node->action() .' ">'.$img.s($node->get_content()).'</a><br>';
                }
            } else {
                $link .= '<a href=" '. $node->action() .' ">'.$img.s($node->get_content()).'</a><br>';
            }
        }

        // No links to display.
        if (!$link) {
            return $o;
        }

        $intellicartheading = get_string('intellicart', 'local_intellicart');
        if ($location == 'snapfeedsmenu') {
            $o = '<h2>' .$intellicartheading. '</h2>';
            $o .= '<div id="snap-feeds-menu-intellicart">'
                .$link.
                '</div>';
        } else {
            $o = '<h2>' .$intellicartheading. '</h2>';
            $o .= '<div id="snap-sidebar-menu-intellicart">'
                .$link.
                '</div>';
        }
        return $o;
        // @codingStandardsIgnoreEnd
    }

    /**
     * This renders the navbar.
     * Uses bootstrap compatible html.
     * @param string $coverimage
     */
    public function snapnavbar($coverimage = '') {
        global $COURSE, $CFG;

        require_once($CFG->dirroot.'/course/lib.php');

        $breadcrumbs = '';
        $courseitem = null;
        $attrs['class'] = '';
        if (!empty($coverimage)) {
            $attrs['class'] .= ' mast-breadcrumb';
        }
        // BEGIN LSU Extra My Course or My Home.
        if (empty(get_config('theme_snap', 'personalmenuenablepersonalmenu'))) {
            $snapmycourses = html_writer::link(new moodle_url('/my/courses.php'), get_string('menu', 'theme_snap'), $attrs);
        } else {
            $snapmycourses = html_writer::link(new moodle_url('/my/'), get_string('myhome'), $attrs);
        }
        // END LSU Extra My Course or My Home.
        $snapmycourses = html_writer::link(new moodle_url('/my/courses.php'), get_string('menu', 'theme_snap'), $attrs);
        $filteredbreadcrumbs = $this->remove_duplicated_breadcrumbs($this->page->navbar->get_items());
        foreach ($filteredbreadcrumbs as $item) {
            $item->hideicon = true;

            // Add Breadcrumb links to all users types.
            if ($item->key === 'myhome') {
                $breadcrumbs .= '<li class="breadcrumb-item">';
                $breadcrumbs .= html_writer::link(new moodle_url('/my'), get_string($item->key), $attrs);
                $breadcrumbs .= '</li>';
                continue;
            }

            if ($item->key === 'home') {
                $breadcrumbs .= '<li class="breadcrumb-item">';
                $breadcrumbs .= html_writer::link(new moodle_url('/'), get_string($item->key), $attrs);
                $breadcrumbs .= '</li>';
                continue;
            }

            // Replace my courses none-link with link to snap personal menu.
            if ($item->key === 'mycourses') {
                $breadcrumbs .= '<li class="breadcrumb-item">' .$snapmycourses. '</li>';
                continue;
            }

            if ($item->type == \navigation_node::TYPE_COURSE) {
                $courseitem = $item;
            }

            if ($item->type == \navigation_node::TYPE_SECTION) {
                if ($courseitem != null) {
                    $url = $courseitem->action->out(false);
                    $item->action = $courseitem->action;
                    $sectionnumber = $this->get_section_for_id($item->key);

                    // Append section focus hash only for topics and weeks formats because we can
                    // trust the behaviour of these formats.
                    if ($COURSE->format == 'topics' || $COURSE->format == 'weeks') {
                        $url .= '#section-'.$sectionnumber;
                        if ($item->text == get_string('general')) {
                            $item->text = get_string('introduction', 'theme_snap');
                        }
                    } else {
                        $url = course_get_url($COURSE, $sectionnumber);
                    }
                    $item->action = new moodle_url($url);
                }
            }

            // Only output breadcrumb items which have links.
            if ($item->action !== null) {
                $attr = [];
                if (!empty($coverimage)) {
                    $attr = ['class' => 'mast-breadcrumb'];
                }
                if (!is_string($item->action) && !empty($item->action->url)) {
                    $link = html_writer::link($item->action->url, $item->text, $attr);
                } else {
                    $link = html_writer::link($item->action, $item->text, $attr);
                }
                $breadcrumbs .= '<li class="breadcrumb-item">' .$link. '</li>';
            }
        }

        if (!empty($breadcrumbs)) {
            return '<ol class="breadcrumb">' .$breadcrumbs .'</ol>';
        }
    }

    /**
     * Renders a div that is only shown when there are configured custom menu items.
     *
     * @return string
     */
    public function custom_menu_spacer() {
        global $CFG;
        $spacer = '';

        if (!empty($CFG->custommenuitems)) {
            $spacer  = '<div class="snap-custom-menu-spacer"></div>';

            // Style to fix the block settings menu when custom menu is active.
            $css = '#page-admin-purgecaches #notice, #notice.snap-continue-cancel';

            $spacer .= "<style> {$css} </style>";
        }
        return $spacer;
    }

    public function secure_layout_language_menu() {
        if (get_config('core', 'langmenuinsecurelayout')) {
            return $this->lang_menu();
        } else {
            return '';
        }
    }

    /**
     * Wrapper for header elements and create the necessary elements for content bank in Snap.
     * Taken from core in lib/outputrenderers.php, full_header() function.
     *
     * @return string.
     */
    public function snap_content_bank() {
        $header = new stdClass();
        $header->headeractions = $this->page->get_header_actions();
        return $this->render_from_template('core/full_header', $header);
    }

    /**
     * Advanced course management options for My Courses page in Snap.
     *
     * @return string.
     */
    public function snap_my_courses_management_options() {

        $coursecat = core_course_category::user_top();
        $coursemanagemenu = [];
        if ($coursecat && ($category = core_course_category::get_nearest_editable_subcategory($coursecat, ['create']))) {
            // The user has the capability to create course.
            $coursemanagemenu['newcourseurl'] = new moodle_url('/course/edit.php', ['category' => $category->id]);
        }
        if ($coursecat && ($category = core_course_category::get_nearest_editable_subcategory($coursecat, ['manage']))) {
            // The user has the capability to manage the course category.
            $coursemanagemenu['manageurl'] = new moodle_url('/course/management.php', ['categoryid' => $category->id]);
        }
        if ($coursecat) {
            $category = core_course_category::get_nearest_editable_subcategory($coursecat, ['moodle/course:request']);
            if ($category && $category->can_request_course()) {
                $coursemanagemenu['courserequesturl'] = new moodle_url('/course/request.php', ['categoryid' => $category->id]);

            }
        }

        if (!empty($coursemanagemenu)) {
            // Render the course management menu.
            $dropdown = $this->render_from_template('my/dropdown', $coursemanagemenu);
            $coursemanageoptions = "<div class='snap-page-my-courses-options d-flex'>";
            $coursemanageoptions .= $dropdown;
            $coursemanageoptions .= "</div>";

            return $coursemanageoptions;
        }
    }

    /**
     * Gets the subdomain to use to link to the Open LMS site.
     * @return string
     */
    public function get_poweredby_subdomain() {
        // Currently supported subdomains.
        $subdomains = [
            'es' => 'es',
            'fr' => 'fr',
            'ja' => 'jp',
            'pt_br' => 'br',
        ];

        if (isset($subdomains[current_language()])) {
            return $subdomains[current_language()];
        }
        // Default subdomain.
        return 'www';
    }

    /**
     * @param $pathurl string.
     *
     * @return bool $path
     */
    public function get_path_hiddentoc($pathurl = false): bool {

        $path = false;
        $listhidden = $this->listhidden ?? [];

        if (!empty($pathurl)) {
            if (in_array($pathurl, $listhidden)) {
                $path = true;
            }
        }
        return $path;
    }

    /**
     * When there are two or more breadcrumbs with the same name, remove the others and just leave one.
     * @param $breadcrumbs array.
     * @return array
     */
    public function remove_duplicated_breadcrumbs($breadcrumbs): array {
        $breadcrumbskeys = [];
        $filtereditems = array_filter($breadcrumbs, function($item) use (&$breadcrumbskeys) {
            $text = $item->text instanceof lang_string ? $item->text->out() : $item->text;
            if (array_key_exists($text, $breadcrumbskeys)) {
                return false;
            }
            $breadcrumbskeys[$text] = $item->key;
            return true;
        });
        return $filtereditems;
    }

    /**
     * My Courses navigation link.
     *
     */
    public function my_courses_nav_link() {
        $output = '';
        if (!isloggedin() || isguestuser()) {
            return $output;
        }
        $classes = 'snap-my-courses-menu snap-my-courses-link';
        $url = new \moodle_url('/my/courses.php');
        $menu = '<span class="hidden-xs-down">' .get_string('menu', 'theme_snap'). '</span>';
        $attributes = [
            'aria-haspopup' => 'true',
            'class' => $classes,
            'id' => 'snap-my-courses-trigger',
        ];
        $output .= html_writer::link($url, $menu, $attributes);
        return $output;
    }

    /**
     * User menu navigation dropdown.
     *
     */
    public function user_menu_nav_dropdown() {
        $output = '';
        if (!isloggedin() || isguestuser()) {
            if (local::current_url_path() != '/login/index.php') {
                $output .= $this->login_button();
            }
        } else {
            $primary = new primary($this->page);
            $data = $primary->get_user_menu($this);
            $preferencesposition = array_search('preferences,moodle', array_column($data['items'], 'titleidentifier'));

            if ($preferencesposition) {
                $additionallinks = [];

                // My courses link.
                $mycourses = new stdClass();
                $mycourses->itemtype = 'link';
                $mycourses->url = new moodle_url('/my/courses.php');
                $mycourses->link = $mycourses->itemtype == 'link';
                $mycourses->title = get_string('menu', 'theme_snap');
                $mycourses->titleidentifier = 'menu,theme_snap';
                $additionallinks[] = $mycourses;

                // My programs link.
                if (is_callable('mr_on') && mr_on('myprograms', '_MR_BLOCKS')) {
                    $myprograms = new stdClass();
                    $myprograms->itemtype = 'link';
                    $myprograms->url = new moodle_url( '/enrol/programs/my/index.php');
                    $myprograms->link = $myprograms->itemtype == 'link';
                    $myprograms->title = get_string('pluginname', 'block_myprograms');
                    $myprograms->titleidentifier = 'pluginname,block_myprograms';
                    $additionallinks[] = $myprograms;
                }

                // My reports link.
                if (is_callable('mr_on') && mr_on('reportbuilder', '_MR_LOCAL')) {
                    $reportbuilder = new stdClass();
                    $reportbuilder->itemtype = 'link';
                    $reportbuilder->url = new moodle_url( '/local/reportbuilder/myreports.php');
                    $reportbuilder->link = $reportbuilder->itemtype == 'link';
                    $reportbuilder->title = get_string('myreports', 'local_reportbuilder');
                    $reportbuilder->titleidentifier = 'myreports,local_reportbuilder';
                    $additionallinks[] = $reportbuilder;
                }

                // My account link.
                if ((has_capability('moodle/site:config', context_system::instance())) &&
                    (\core_component::get_component_directory('local_myaccount') !== null) &&
                    is_callable('mr_on') &&
                    mr_on("myaccount", "_MR_LOCAL")) {
                    $myaccount = new stdClass();
                    $myaccount->itemtype = 'link';
                    $myaccount->url = new moodle_url('/local/myaccount/view.php', [
                        'controller' => 'default',
                        'action' => 'view',
                    ]);
                    $myaccount->link = $myaccount->itemtype == 'link';
                    $myaccount->title = get_string('myaccount', 'local_myaccount');
                    $myaccount->titleidentifier = 'myaccount,local_myaccount';
                    $additionallinks[] = $myaccount;
                }

                $divider = new stdClass();
                $divider->divider = true;
                $additionallinks[] = $divider;

                // Dashboard link.
                $dashboardlink = new stdClass();
                $dashboardlink->itemtype = 'link';
                $dashboardlink->url = new moodle_url('/my');
                $dashboardlink->link = $dashboardlink->itemtype == 'link';
                $dashboardlink->title = get_string('myhome');
                $dashboardlink->titleidentifier = 'myhome,moodle';
                $additionallinks[] = $dashboardlink;

                // Course catalogue link.
                if (is_callable('mr_on') && mr_on('catalogue', '_MR_LOCAL')) {
                    $localcatalogue = new stdClass();
                    $localcatalogue->itemtype = 'link';
                    $localcatalogue->url = new moodle_url('/local/catalogue/index.php');
                    $localcatalogue->link = $localcatalogue->itemtype == 'link';
                    $localcatalogue->title = get_string('pluginname', 'local_catalogue');
                    $localcatalogue->titleidentifier = 'pluginname,local_catalogue';
                    $additionallinks[] = $localcatalogue;
                }
                // Program catalogue link.
                if (is_callable('mr_on') && mr_on('programs', '_MR_ENROL')) {
                    $programs = new stdClass();
                    $programs->itemtype = 'link';
                    $programs->url = new moodle_url('/enrol/programs/catalogue/index.php');
                    $programs->link = $programs->itemtype == 'link';
                    $programs->title = get_string('catalogue', 'enrol_programs');
                    $programs->titleidentifier = 'catalogue,enrol_programs';
                    $additionallinks[] = $programs;
                }

                if (count($additionallinks)) {
                    $dividerstart = new stdClass();
                    $dividerstart->divider = true;
                    $dividerend = new stdClass();
                    $dividerend->divider = true;
                    $additionallinks[] = $dividerend;
                    array_unshift( $additionallinks, $dividerstart );
                    array_splice( $data['items'], $preferencesposition, 1, $additionallinks );
                }
            }

            $output .= $this->render_from_template('core/user_menu', $data);
        }
        return $output;
    }

    /**
     * Snap feeds in My Courses.
     *
     * @return string.
     */
    public function snap_feeds($location) {
        global $CFG;

        $updatesid = 'feeds';

        $snapfeedsurlparam = isset($CFG->theme_snap_feeds_url_parameter) ? $CFG->theme_snap_feeds_url_parameter : true;

        $data = (object) [
            'updates' => $this->render_callstoaction($location),
            'mobileupdates' => $this->render_snap_feeds_mobile(),
            'location' => $updatesid,
            'urlparameter' => $snapfeedsurlparam,
        ];
        $feeds = $this->render_from_template('theme_snap/snap_feeds', $data);
        return $feeds;
    }

    /**
     * My courses page content.
     *
     */
    public function my_courses_snap_page_content() {

        global $DB, $USER;

        $browseallcourses = '';
        if (!empty($CFG->navshowallcourses) || has_capability('moodle/site:config', context_system::instance())) {
            $url = new moodle_url('/course/');
            $browseallcourses = $this->column_header_icon_link('browseallcourses', 'courses', $url, 'mycourses');
        }
        $manager = new \core_privacy\local\sitepolicy\manager();
        $policyurlexist = $manager->is_defined();

        // When there are not Snap feeds enabled in the settings, the block overview will be centered in the page.
        if (empty($feeds)) {
            $blockmyoverviewclasses = "block_myoverview_column col-12 single_column";
        } else {
            $blockmyoverviewclasses = "block_myoverview_column col-sm-12 col-xl-8";
        }

        // Check if Course overview block is enabled.
        $enableblockmessage = $DB->get_field('block', 'visible', ['name' => 'myoverview']) ? false : true;

        $data = (object) [
            'custommenuspacer' => $this->custom_menu_spacer(),
            'snapnavbar' => $this->snapnavbar(''),
            'pageheading' => $this->page_heading(),
            'courseheader' => $this->course_header(),
            'browseallcourses' => $browseallcourses,
            'maincontent' => $this->main_content(),
            'coursesoptions' => $this->snap_my_courses_management_options(),
            'standaraftermainregion' => $this->standard_after_main_region_html(),
            'snapblocks' => $this->snap_blocks(),
            'snapfeedssidemenu' => $this->snap_feeds_side_menu(),
            'blockmyoverviewclasses' => $blockmyoverviewclasses,
            'enableblockmessage' => $enableblockmessage,
        ];

        $content = $this->render_from_template('theme_snap/my_courses', $data);

        return $content;
    }

    /**
     * Snap feeds navigation link.
     *
     */
    public function snap_feeds_side_menu_trigger() {
        global $CFG;
        $output = '';
        if (!isloggedin() || isguestuser()) {
            return $output;
        }
        $feeds = $this->snap_feeds('snapfeedsmenu');
        if (empty($feeds)) {
            return $output;
        }

        $icon = file_get_contents($CFG->dirroot . '/theme/snap/pix/snapfeeds.svg');
        $url = '#snap_feeds_side_menu';
        $attributes = [
            'id' => 'snap_feeds_side_menu_trigger',
            'class' => 'js-snap-feeds-side-menu-trigger',
            'title' => get_string('togglesnapfeedsdrawer', 'theme_snap'),
            'aria-label' => get_string('togglesnapfeedsdrawer', 'theme_snap'),
            'aria-expanded' => "false",
        ];

        return html_writer::link($url, $icon, $attributes);
    }

    /**
     * Snap feeds in My Courses.
     *
     * @return string.
     */
    public function snap_feeds_side_menu() {
        $output = '';
        if (!isloggedin() || isguestuser()) {
            return $output;
        }
        $feeds = $this->snap_feeds('snapfeedsmenu');
        if (empty($feeds)) {
            return $output;
        }
        $output .= html_writer::tag('div', $feeds, ['id' => 'snap_feeds_side_menu']);
        return $output;
    }

    /**
     * Adds a link for configuring communication.
     *
     * @param navigation_node $item
     * @param int $contextid
     * @param int $courseid
     */
    private function add_communication_navigation_node(navigation_node $item, $contextid, $courseid) {
        $url = new moodle_url('/communication/configure.php', [
            'contextid' => $contextid,
            'instanceid' => $courseid,
            'instancetype' => 'coursecommunication',
            'component' => 'core_course',
        ]);
        $node = $item->create(
            get_string('communication', 'communication'), $url,
            navigation_node::TYPE_SETTING,
            null,
            'communication',
            new \pix_icon('t/messages-o','')
        );
        $item->add_node($node, 'filtermanagement'); // Put it before the Filters option.
    }

    /**
     * Checks if the communication configuration can be added.
     *
     * @param $context
     * @return bool
     */
    private function can_add_communication_node($context): bool {
        return \core_communication\api::is_available() &&
            has_capability('moodle/course:configurecoursecommunication', $context);
    }

    /**
     * Utility function to replace the course name with
     * a link.
     *
     * @param $element: Should contain the course fullname
     * @return string
     */
    protected function snap_make_coursename_link($element) {
        global $COURSE;
        $courseurl = new moodle_url('/course/view.php', ['id' => $COURSE->id]);
        $coursename = format_string($COURSE->fullname);
        $namelink = html_writer::link($courseurl, $coursename);
        $replacedname = str_replace($coursename, $namelink, $element);
        return $replacedname;
    }

    /**
     * Checks if the current page is an activity view.
     *
     * @return bool
     */
    protected function snap_page_is_activity_view() {
        return $this->page->context->contextlevel === CONTEXT_MODULE
               && strpos($this->page->pagetype, 'mod-') === 0;
    }

    /**
     * Checks if the current page is an activity mod.
     *
     * @return bool
     */
    protected function snap_page_is_activity_mod() {
        return $this->page->context->contextlevel === CONTEXT_MODULE
               && strpos($this->page->pagetype, 'mod-') === 0
               && substr($this->page->pagetype, -4) === '-mod';
    }

    /**
     * Checks if the current page is an edit section page.
     *
     * @return bool
     */
    protected function snap_page_is_edit_section() {
        return $this->page->pagetype === 'course-editsection';
    }

    /**
     * Checks if the current page is a user view.
     *
     * @return bool
     */
    protected function snap_page_is_user_view() {
        return $this->page->pagetype === 'user-view';
    }

    /**
     * Check if current page is a whitelisted mod that we need to show snap blocks in some special cases.
     * @return bool
     */
    protected function snap_page_is_whitelisted_mod() {
        $whitelist = ['book', 'lesson', 'quiz'];
        return $this->page->context->contextlevel === CONTEXT_MODULE
            && in_array($this->page->cm->modname, $whitelist);
    }
}

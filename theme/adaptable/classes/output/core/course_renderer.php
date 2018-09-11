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

namespace theme_adaptable\output\core;

defined('MOODLE_INTERNAL') || die();

/******************************************************************************************
 *
 * Overridden Core Course Renderer for Adaptable theme
 *
 * @package    theme_adaptable
 * @copyright 2015 Jeremy Hopkins (Coventry University)
 * @copyright 2015 Fernando Acedo (3-bits.com)
 * @copyright 2015 Moodlerooms Inc. (http://www.moodlerooms.com) (activity further information functionality)
 * @copyright 2017 Manoj Solanki (Coventry University)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

use cm_info;
use core_text;
use html_writer;
use context_course;
use moodle_url;
use coursecat_helper;
use lang_string;
use course_in_list;
use stdClass;
use renderable;
use action_link;

class course_renderer extends \core_course_renderer {

    /**
     * Render course category box
     *
     * @param coursecat_helper $chelper
     * @param string $course
     * @param string $additionalclasses
     * @return string
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        global $CFG, $OUTPUT, $PAGE;
        $type = theme_adaptable_get_setting('frontpagerenderer');
        if ($type == 3 || $OUTPUT->body_id() != 'page-site-index') {
            return parent::coursecat_coursebox($chelper, $course, $additionalclasses = '');
        }
        $additionalcss = '';
        if ($type == 2) {
            $additionalcss = 'hover';
        }

        if ($type == 4) {
            $additionalcss = 'hover covtiles';
            $type = 2;
            $covhidebutton = "true";
        } else {
            $covhidebutton = "false";
        }

        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        $content = '';
        $classes = trim($additionalclasses);

        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $classes .= ' collapsed';
        }

        // Control span to display course tiles.
        if (!isloggedin() || isguestuser()) {
            $spanclass = "span4";
        } else {
            $spanclass = "span4";
        }

        $content .= html_writer::start_tag('div',
                array('class' => ' '.$spanclass.' panel panel-default coursebox '.$additionalcss));
        $urlb = new moodle_url('/course/view.php', array('id' => $course->id));

        $content .= "<a href='$urlb'>";

        $coursename = $chelper->get_course_formatted_name($course);
        $content .= html_writer::start_tag('div', array('class' => 'panel-heading'));
        if ($type == 1) {
            $content .= html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                    $coursename, array('class' => $course->visible ? '' : 'dimmed', 'title' => $coursename));
        }
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()) {
                $url = new moodle_url('/course/info.php', array('id' => $course->id));
                $arrow = html_writer::tag('span', '', array('class' => 'glyphicon glyphicon-info-sign'));
                $content .= html_writer::link('#coursecollapse' . $course->id , '&nbsp;' . $arrow,
                        array('data-toggle' => 'collapse', 'data-parent' => '#frontpage-category-combo'));
            }
        }

        if ($type == 1) {
            $content .= $this->coursecat_coursebox_enrolmenticons($course, $type);
        }

        $content .= html_writer::end_tag('div'); // End .panel-heading.
        $content .= html_writer::end_tag('a'); // End a.

        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $content .= html_writer::start_tag('div', array('id' => 'coursecollapse' . $course->id,
                    'class' => 'panel-collapse collapse'));
        }

        $content .= html_writer::start_tag('div', array('class' => 'panel-body clearfix'));

        // This gets the course image or files.
        $content .= $this->coursecat_coursebox_content($chelper, $course, $type);

        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $icondirection = 'left';
            if ('ltr' === get_string('thisdirection', 'langconfig')) {
                $icondirection = 'right';
            }
            $arrow = html_writer::tag('span', '', array('class' => 'fa fa-chevron-'.$icondirection));
            $btn = html_writer::tag('span', get_string('course', 'theme_adaptable') . ' ' .
                    $arrow, array('class' => 'get_stringlink'));

            if (empty($PAGE->theme->settings->covhidebutton)) {
                $content .= html_writer::link(new moodle_url('/course/view.php',
                        array('id' => $course->id)), $btn, array('class' => " coursebtn submit btn btn-info btn-sm pull-right"));
            }
        }

        $content .= html_writer::end_tag('div'); // End .panel-body.

        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $content .= html_writer::end_tag('div'); // End .collapse.
        }

        $content .= html_writer::end_tag('div'); // End .panel.

        return $content;
    }

    /**
     * Returns enrolment icons
     *
     * @param string $course
     * @return string
     */
    protected function coursecat_coursebox_enrolmenticons($course) {
        $content = '';
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', array('class' => 'enrolmenticons'));
            foreach ($icons as $pixicon) {
                $content .= $this->render($pixicon);
            }
            $content .= html_writer::end_tag('div'); // Enrolmenticons.
        }
        return $content;
    }

    /**
     * Returns course box content for categories
     *
     * Type - 1 = No Overlay.
     * Type - 2 = Overlay.
     *
     * @param coursecat_helper $chelper
     * @param string $course
     * @param int $type = 3
     * @return string
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course, $type=3) {
        global $CFG, $OUTPUT, $PAGE;
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            require_once($CFG->libdir. '/coursecatlib.php');
            $course = new course_in_list($course);
        }
        if ($type == 3 || $OUTPUT->body_id() != 'page-site-index') {
            return parent::coursecat_coursebox_content($chelper, $course);
        }
        $content = '';

        // Display course overview files.
        $contentimages = '';
        $contentfiles = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = file_encode_url("$CFG->wwwroot/pluginfile.php",
                    '/'. $file->get_contextid(). '/'. $file->get_component(). '/'.
                    $file->get_filearea(). $file->get_filepath(). $file->get_filename(), !$isimage);
            if ($isimage) {
                if ($type == 1) {
                    $contentimages .= html_writer::start_tag('div', array('class' => 'courseimage'));
                    $link = new moodle_url('/course/view.php', array('id' => $course->id));
                    $contentimages .= html_writer::link($link, html_writer::empty_tag('img', array('src' => $url)));
                    $contentimages .= html_writer::end_tag('div');
                } else {
                    $contentimages .= "<div class='cimbox' style='background: #FFF url($url) no-repeat center center;
                    background-size: contain;'></div>";
                }
            } else {
                $image = $this->output->pix_icon(file_file_icon($file, 24), $file->get_filename(), 'moodle');
                $filename = html_writer::tag('span', $image, array('class' => 'fp-icon')).
                html_writer::tag('span', $file->get_filename(), array('class' => 'fp-filename'));
                $contentfiles .= html_writer::tag('span',
                        html_writer::link($url, $filename),
                        array('class' => 'coursefile fp-filename-icon'));
            }
        }
        if (strlen($contentimages) == 0 && $type == 2) {
            // Default image.
            $url = $PAGE->theme->setting_file_url('frontpagerendererdefaultimage', 'frontpagerendererdefaultimage');
            $contentimages .= "<div class='cimbox' style='background: #FFF url($url) no-repeat center center;
            background-size: contain;'></div>";
        }
        $content .= html_writer::link(new moodle_url('/course/view.php',
                array('id' => $course->id)), $contentimages. $contentfiles);

        if ($type == 2) {
            $content .= $this->coursecat_coursebox_enrolmenticons($course);
        }

        if ($type == 2) {
            $content .= html_writer::start_tag('div', array('class' => 'coursebox-content'));
            $coursename = $chelper->get_course_formatted_name($course);
            $content .= html_writer::tag('h3', html_writer::link(new moodle_url('/course/view.php', array('id' => $course->id)),
                    $coursename, array('class' => $course->visible ? '' : 'dimmed')));
        }
        $content .= html_writer::start_tag('div', array('class' => 'summary'));
        if (ISSET($coursename)) {
            $content .= html_writer::tag('p', html_writer::tag('strong', $coursename));
        }
        // Display course summary.
        if ($course->has_summary()) {
            $summs = $chelper->get_course_formatted_summary($course, array('overflowdiv' => false, 'noclean' => true,
                    'para' => false));
            $summs = strip_tags($summs);
            $truncsum = mb_strimwidth($summs, 0, 70, "...", 'utf-8');
            $content .= html_writer::tag('span', $truncsum, array('title' => $summs));
        }
        $coursecontacts = theme_adaptable_get_setting('tilesshowcontacts');
        if ($coursecontacts) {
            $coursecontacttitle = theme_adaptable_get_setting('tilescontactstitle');
            // Display course contacts. See course_in_list::get_course_contacts().
            if ($course->has_course_contacts()) {
                $content .= html_writer::start_tag('ul', array('class' => 'teachers'));
                foreach ($course->get_course_contacts() as $userid => $coursecontact) {
                    $name = ($coursecontacttitle ? $coursecontact['rolename'].': ' : html_writer::tag('i', '&nbsp;',
                            array('class' => 'fa fa-graduation-cap')) ).
                            html_writer::link(new moodle_url('/user/view.php',
                                    array('id' => $userid, 'course' => SITEID)),
                                    $coursecontact['username']);
                            $content .= html_writer::tag('li', $name);
                }
                $content .= html_writer::end_tag('ul'); // Teachers.
            }
        }
        $content .= html_writer::end_tag('div'); // Summary.

        // Display course category if necessary (for example in search results).
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            require_once($CFG->libdir. '/coursecatlib.php');
            if ($cat = coursecat::get($course->category, IGNORE_MISSING)) {
                $content .= html_writer::start_tag('div', array('class' => 'coursecat'));
                $content .= get_string('category').': '.
                        html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                        $content .= html_writer::end_tag('div'); // Coursecat.
            }
        }
        if ($type == 2) {
            $content .= html_writer::end_tag('div');
            // End course-content.
        }
        $content .= html_writer::tag('div', '', array('class' => 'boxfooter')); // Coursecat.

        return $content;
    }

    /**
     * Course search form
     *
     * @param string $value
     * @param string $format
     * @return string
     */
    public function course_search_form($value = '', $format = 'plain') {
        static $count = 0;
        $formid = 'coursesearch';
        if ((++$count) > 1) {
            $formid .= $count;
        }
        $inputid = 'coursesearchbox';
        $inputsize = 30;

        if ($format === 'navbar') {
            $formid = 'coursesearchnavbar';
            $inputid = 'navsearchbox';
        }

        $strsearchcourses = get_string("searchcourses", "theme_adaptable");
        $searchurl = new moodle_url('/course/search.php');

        $form = array('id' => $formid, 'action' => $searchurl, 'method' => 'get', 'class' => "form-inline", 'role' => 'form');
        $output = html_writer::start_tag('form', $form);
        $output .= html_writer::start_div('form-group');
        $output .= html_writer::tag('label', $strsearchcourses, array('for' => $inputid, 'class' => 'sr-only'));
        $search = array('type' => 'text', 'id' => $inputid, 'size' => $inputsize, 'name' => 'search',
                'class' => 'form-control', 'value' => s($value), 'placeholder' => $strsearchcourses);
        $output .= html_writer::empty_tag('input', $search);
        $button = array('type' => 'submit', 'class' => 'btn btn-default');
        $output .= html_writer::tag('button', get_string('go'), $button);
        $output .= html_writer::end_div(); // Close form-group.
        $output .= html_writer::end_tag('form');

        return $output;
    }

    /**
     * Frontpage course list
     *
     * @return string
     */
    public function frontpage_my_courses() {
        global $USER, $CFG, $DB;
        $output = '';
        if (!isloggedin() or isguestuser()) {
            return '';
        }
        // Calls a local method (render_mycourses) to get list of a user's current courses that they are enrolled on.
        $courses = $this->render_mycourses();
        list($sortedcourses) = $this->render_mycourses();

        if (!empty($sortedcourses) || !empty($rcourses) || !empty($rhosts)) {
            $chelper = new coursecat_helper();
            if (count($courses) > $CFG->frontpagecourselimit) {
                // There are more enrolled courses than we can display, display link to 'My courses'.
                $totalcount = count($sortedcourses);
                $courses = array_slice($sortedcourses, 0, $CFG->frontpagecourselimit, true);
                $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/my/'),
                        'viewmoretext' => new lang_string('mycourses')
                ));
            } else {
                // All enrolled courses are displayed, display link to 'All courses' if there are more courses in system.
                $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/course/index.php'),
                        'viewmoretext' => new lang_string('fulllistofcourses')
                ));
                $totalcount = $DB->count_records('course') - 1;
            }
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->set_attributes(
                    array('class' => 'frontpage-course-list-enrolled'));
            $output .= $this->coursecat_courses($chelper, $sortedcourses, $totalcount);

            if (!empty($rcourses)) {
                $output .= html_writer::start_tag('div', array('class' => 'courses'));
                foreach ($rcourses as $course) {
                    $output .= $this->frontpage_remote_course($course);
                }
                $output .= html_writer::end_tag('div');
            } else if (!empty($rhosts)) {
                $output .= html_writer::start_tag('div', array('class' => 'courses'));
                foreach ($rhosts as $host) {
                    $output .= $this->frontpage_remote_host($host);
                }
                $output .= html_writer::end_tag('div');
            }
        }
        return $output;
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
        // Iterate the navarray and display each node.
        $separator = get_separator();
        for ($i = 0; $i < $itemcount; $i++) {
            $item = $items[$i];
            $item->hideicon = true;
            if ($i === 0) {
                $content = html_writer::tag('li', $this->render($item));
            } else {
                $content = html_writer::tag('li', $separator.$this->render($item));
            }
            $htmlblocks[] = $content;
        }

        // Accessibility: heading for navbar list  (MDL-20446).
        $navbarcontent = html_writer::tag('span', get_string('pagepath'), array('class' => 'accesshide'));
        $navbarcontent .= html_writer::tag('ul', join('', $htmlblocks), array('role' => 'navigation'));
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
            $content = $icon.$content; // Use CSS for spacing of icons.
        }
        if ($item->helpbutton !== null) {
            $content = trim($item->helpbutton).html_writer::tag('span', $content, array('class' => 'clearhelpbutton',
                    'tabindex' => '0'));
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
            $attributes = array('tabindex' => '0'); // Add tab support to span but still maintain character stream sequence.
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
     * Overridden. Renders html to display a name with the link to the course module on a course page
     *
     * If module is unavailable for user but still needs to be displayed
     * in the list, just the name is returned without a link.
     *
     * Note that for course modules that never have separate pages (i.e. labels)
     * this function return an empty string.
     *
     * This method has only been overriden in order to strip -24 and similar from icon image filenames
     * to allow using of local theme icons in /pix_core/f.
     *
     * @param cm_info $mod
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm_name(cm_info $mod, $displayoptions = array()) {
        // If use adaptable icons is set to false, then just run parent method as normal.
        if (empty($this->page->theme->settings->coursesectionactivityuseadaptableicons)) {
            return parent::course_section_cm_name($mod, $displayoptions);
        }

        $output = '';
        if (!$mod->uservisible && empty($mod->availableinfo)) {
            // Nothing to be displayed to the user.
            return $output;
        }
        $url = $mod->url;
        if (!$url) {
            return $output;
        }

        // Accessibility: for files get description via icon, this is very ugly hack!
        $instancename = $mod->get_formatted_name();
        $altname = $mod->modfullname;
        // Avoid unnecessary duplication: if e.g. a forum name already
        // includes the word forum (or Forum, etc) then it is unhelpful
        // to include that in the accessible description that is added.
        if (false !== strpos(core_text::strtolower($instancename),
                core_text::strtolower($altname))) {
                    $altname = '';
        }

        // File type after name, for alphabetic lists (screen reader).
        if ($altname) {
            $altname = get_accesshide(' '.$altname);
        }

        // For items which are hidden but available to current user
        // ($mod->uservisible), we show those as dimmed only if the user has
        // viewhiddenactivities, so that teachers see 'items which might not
        // be available to some students' dimmed but students do not see 'item
        // which is actually available to current student' dimmed.
        $linkclasses = '';
        $accesstext = '';
        $textclasses = '';
        if ($mod->uservisible) {

            $conditionalhidden = $this->is_cm_conditionally_hidden($mod);
            $accessiblebutdim = (!$mod->visible || $conditionalhidden) &&
            has_capability('moodle/course:viewhiddenactivities', $mod->context);
            if ($accessiblebutdim) {
                $linkclasses .= ' dimmed';
                $textclasses .= ' dimmed_text';
                if ($conditionalhidden) {
                    $linkclasses .= ' conditionalhidden';
                    $textclasses .= ' conditionalhidden';
                }
                // Show accessibility note only if user can access the module himself.
                $accesstext = get_accesshide(get_string('hiddenfromstudents').':'. $mod->modfullname);
            }

        } else {

            $linkclasses .= ' dimmed';
            $textclasses .= ' dimmed_text';

        }

        // Get on-click attribute value if specified and decode the onclick - it
        // has already been encoded for display (puke).
        $onclick = htmlspecialchars_decode($mod->onclick, ENT_QUOTES);

        $groupinglabel = $mod->get_grouping_label($textclasses);

        // Display link itself.

        // Get icon url, but strip -24, -64, -256  etc from the end of filetype icons so we
        // only need to provide one SVG, see MDL-47082. (Used from snap theme).
        $imageurl = \preg_replace('/-\d\d\d?$/', '', $mod->get_icon_url());

        $activitylink = html_writer::empty_tag('img', array('src' => $imageurl,
                'class' => 'iconlarge activityicon', 'alt' => ' ', 'role' => 'presentation')) . $accesstext .
                html_writer::tag('span', $instancename . $altname, array('class' => 'instancename'));

        $outputlink = '';
        if ($mod->uservisible) {
            $outputlink .= html_writer::link($url, $activitylink, array('class' => $linkclasses, 'onclick' => $onclick)) .
            $groupinglabel;
        } else {
            // We may be displaying this just in order to show information
            // about visibility, without the actual link ($mod->uservisible).
            $outputlink .= html_writer::tag('div', $activitylink, array('class' => $textclasses)) .
            $groupinglabel;
        }

        $tmpl = new \core_course\output\course_module_name($mod, $this->page->user_is_editing(), $displayoptions);
        $templatedata = $tmpl->export_for_template($this->output);

        // Variable displayvalue element is purposely overriden below with link including custom icon created above.
        $templatedata['displayvalue'] = $outputlink;

        $output .= $this->output->render_from_template('core/inplace_editable', $templatedata);

        return $output;
    }

    // New methods added for activity styling below.  Adapted from snap theme by Moodleroooms.

    /**
     * Overridden.  Customise display.  Renders HTML to display one course module in a course section
     *
     * This includes link, content, availability, completion info and additional information
     * that module type wants to display (i.e. number of unread forum posts)
     *
     * This function calls:
     * {@link core_course_renderer::course_section_cm_name()}
     * {@link core_course_renderer::course_section_cm_text()}
     * {@link core_course_renderer::course_section_cm_availability()}
     * {@link core_course_renderer::course_section_cm_completion()}
     * {@link course_get_cm_edit_actions()}
     * {@link core_course_renderer::course_section_cm_edit_actions()}
     *
     * @param stdClass $course
     * @param completion_info $completioninfo
     * @param cm_info $mod
     * @param int|null $sectionreturn
     * @param array $displayoptions
     * @return string
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = array()) {
        global $PAGE;
        $output = '';
        // We return empty string (because course module will not be displayed at all) if
        // 1) The activity is not visible to users and
        // 2) The 'availableinfo' is empty, i.e. the activity was hidden in a way that leaves no info, such as using the
        // eye icon.

        if ( (method_exists($mod, 'is_visible_on_course_page')) && (!$mod->is_visible_on_course_page())
                || (!$mod->uservisible && empty($mod->availableinfo)) ) {
            return $output;
        }

        $indentclasses = 'mod-indent';
        if (!empty($mod->indent)) {
            $indentclasses .= ' mod-indent-'.$mod->indent;
            if ($mod->indent > 15) {
                $indentclasses .= ' mod-indent-huge';
            }
        }

        $output .= html_writer::start_tag('div');

        if ($this->page->user_is_editing()) {
            $output .= course_get_cm_move($mod, $sectionreturn);
        }

        $output .= html_writer::start_tag('div', array('class' => 'mod-indent-outer'));

        // This div is used to indent the content.
        $output .= html_writer::div('', $indentclasses);

        // Start a wrapper for the actual content to keep the indentation consistent.
        $output .= html_writer::start_tag('div', array('class' => 'activity-wrapper'));

        // Display the link to the module (or do nothing if module has no url).
        $cmname = $this->course_section_cm_name($mod, $displayoptions);

        if (!empty($cmname)) {
            // Start the div for the activity title, excluding the edit icons.
            $output .= html_writer::start_tag('div', array('class' => 'activityinstance'));
            $output .= $cmname;

            // Module can put text after the link (e.g. forum unread).
            $output .= $mod->afterlink;

            // Closing the tag which contains everything but edit icons. Content part of the module should not be part of this.
            $output .= html_writer::end_tag('div'); // .activityinstance class.
        }

        // If there is content but NO link (eg label), then display the
        // content here (BEFORE any icons). In this case icons must be
        // displayed after the content so that it makes more sense visually
        // and for accessibility reasons, e.g. if you have a one-line label
        // it should work similarly (at least in terms of ordering) to an
        // activity.
        $contentpart = $this->course_section_cm_text($mod, $displayoptions);
        $url = $mod->url;
        if (empty($url)) {
            $output .= $contentpart;
        }

        $modicons = '';
        if ($this->page->user_is_editing()) {
            $editactions = course_get_cm_edit_actions($mod, $mod->indent, $sectionreturn);
            $modicons .= ' '. $this->course_section_cm_edit_actions($editactions, $mod, $displayoptions);
            $modicons .= $mod->afterediticons;
        }

        $modicons .= $this->course_section_cm_completion($course, $completioninfo, $mod, $displayoptions);

        if (!empty($modicons)) {
            $output .= html_writer::start_tag('div', array('class' => 'actions-right'));
            $output .= html_writer::span($modicons, 'actions');
            $output .= html_writer::end_tag('div');
        }

        // Get further information.
        $settingname = 'coursesectionactivityfurtherinformation'. $mod->modname;
        if (isset ($PAGE->theme->settings->$settingname) && $PAGE->theme->settings->$settingname == true) {
            $output .= html_writer::start_tag('div', array('class' => 'activity-meta-container'));
            $output .= $this->course_section_cm_get_meta($mod);
            $output .= html_writer::end_tag('div');
            // TO BE DELETED    $output .= '<div style="clear: both;"></div>'; ????
        }

        // If there is content AND a link, then display the content here.
        // (AFTER any icons). Otherwise it was displayed before.
        if (!empty($url)) {
            $output .= $contentpart;
        }

        // Show availability info (if module is not available).
        $output .= $this->course_section_cm_availability($mod, $displayoptions);

        $output .= html_writer::end_tag('div');

        // End of indentation div.
        $output .= html_writer::end_tag('div');

        $output .= html_writer::end_tag('div');
        return $output;
    }

    /**
     * Get the module meta data for a specific module.
     *
     * @param cm_info $mod
     * @return string
     */
    protected function course_section_cm_get_meta(cm_info $mod) {

        global $COURSE, $OUTPUT;

        $content = '';

        if (is_guest(context_course::instance($COURSE->id))) {
            return '';
        }

        // Do we have an activity function for this module for returning meta data?
        $meta = \theme_adaptable\activity::module_meta($mod);
        if (!$meta->is_set(true)) {
            // Can't get meta data for this module.
            return '';
        }
        $content .= '';
        $duedate = '';

        $warningclass = '';
        if ($meta->submitted) {
            $warningclass = ' activity-date-submitted ';
        }

        $activitycontent = $this->submission_cta($mod, $meta);

        if (!(empty($activitycontent))) {
            if ( ($mod->modname == 'assign') && ($meta->submitted) ) {
                $content .= html_writer::start_tag('span', array('class' => ' activity-due-date ' . $warningclass));
                $content .= $activitycontent;
                $content .= html_writer::end_tag('span') . '<br>';
            } else {
                // Only display if this is really a student on the course (i.e. not anyone who can grade an assignment).
                if (!has_capability('mod/assign:grade', $mod->context)) {
                    $content .= html_writer::start_tag('div', array('class' => 'activity-mod-engagement' . $warningclass));
                    $content .= $activitycontent;
                    $content .= html_writer::end_tag('div');
                }
            }
        }

        // Activity due date.
        if (!empty($meta->extension) || !empty($meta->timeclose)) {
            $due = get_string('due', 'theme_adaptable');
            if (!empty($meta->extension)) {
                $field = 'extension';
            } else if (!empty($meta->timeclose)) {
                $field = 'timeclose';
            }

            $pastdue = $meta->$field < time();

            // Create URL for due date.
            $url = new \moodle_url("/mod/{$mod->modname}/view.php", ['id' => $mod->id]);
            $dateformat = get_string('strftimedate', 'langconfig');
            $labeltext = get_string('due', 'theme_adaptable', userdate($meta->$field, $dateformat));
            $warningclass = '';

            // Display assignment status (due, nearly due, overdue), as long as it hasn't been submitted,
            // or submission not required.
            if ( (!$meta->submitted) && (!$meta->submissionnotrequired) ) {
                $warningclass = '';
                $labeltext = '';

                // If assignment due in 7 days or less, display in amber, if overdue, then in red, or if submitted, turn to green.

                // If assignment is 7 days before date due(nearly due).
                $timedue = $meta->$field - (86400 * 7);
                if ( (time() > $timedue) &&  !(time() > $meta->$field) ) {
                    if ($mod->modname == 'assign') {
                        $warningclass = ' activity-date-nearly-due';
                    }
                } else if (time() > $meta->$field) { // If assignment is actually overdue.
                    if ($mod->modname == 'assign') {
                            $warningclass = ' activity-date-overdue';
                    }
                    $labeltext .= html_writer::tag('i', '&nbsp;', array('class' => 'fa fa-exclamation')) . ' ';
                }

                $labeltext .= get_string('due', 'theme_adaptable', userdate($meta->$field, $dateformat));

                $activityclass = '';
                if ($mod->modname == 'assign') {
                        $activityclass = ' activity-due-date ';
                }
                $duedate .= html_writer::start_tag('span', array('class' => $activityclass . $warningclass));
                $duedate .= html_writer::link($url, $labeltext);
                $duedate .= html_writer::end_tag('span');
            }

            $content .= html_writer::start_tag('div', array('class' => 'activity-mod-engagement'));
            $content .= $duedate . html_writer::end_tag('div');
        }

        if ($meta->isteacher) {
            // Teacher - useful teacher meta data.
            $engagementmeta = array();

            // Below, !== false means we get 0 out of x submissions.
            if (!$meta->submissionnotrequired && $meta->numsubmissions !== false) {
                $engagementmeta[] = get_string('xofy'.$meta->submitstrkey, 'theme_adaptable',
                        (object) array(
                                'completed' => $meta->numsubmissions,
                                'participants' => \theme_adaptable\utils::course_participant_count($COURSE->id, $mod->modname)
                        )
                        );
            }

            if ($meta->numrequiregrading) {
                $engagementmeta[] = get_string('xungraded', 'theme_adaptable', $meta->numrequiregrading);
            }
            if (!empty($engagementmeta)) {
                $engagementstr = implode(', ', $engagementmeta);

                $params = array(
                        'action' => 'grading',
                        'id' => $mod->id,
                        'tsort' => 'timesubmitted',
                        'filter' => 'require_grading'
                );
                $url = new moodle_url("/mod/{$mod->modname}/view.php", $params);

                $icon = html_writer::tag('i', '&nbsp;', array('class' => 'fa fa-info-circle'));
                $content .= html_writer::start_tag('div', array('class' => 'activity-mod-engagement'));
                $content .= html_writer::link($url, $icon . $engagementstr);
                $content .= html_writer::end_tag('div');
            }

        } else {
            // Feedback meta.
            if (!empty($meta->grade)) {
                   $url = new \moodle_url('/grade/report/user/index.php', ['id' => $COURSE->id]);
                if (in_array($mod->modname, ['quiz', 'assign'])) {
                    $url = new \moodle_url('/mod/'.$mod->modname.'/view.php?id='.$mod->id);
                }
                $content .= html_writer::start_tag('span', array('class' => 'activity-mod-feedback'));
                $feedbackavailable = html_writer::tag('i', '&nbsp;', array('class' => 'fa fa-commenting-o')) .
                    get_string('feedbackavailable', 'theme_adaptable');
                $content .= html_writer::link($url, $feedbackavailable);
                $content .= html_writer::end_tag('span');
            }

            // If submissions are not allowed, return the content.
            if (!empty($meta->timeopen) && $meta->timeopen > time()) {
                // TODO - spit out a 'submissions allowed from' tag.
                return $content;
            }

        }

        return $content;
    }

    /**
     * Submission call to action.
     *
     * @param cm_info $mod
     * @param activity_meta $meta
     * @return string
     * @throws coding_exception
     */
    public function submission_cta(cm_info $mod, \theme_adaptable\activity_meta $meta) {
        global $CFG;

        if (empty($meta->submissionnotrequired)) {

            $url = $CFG->wwwroot.'/mod/'.$mod->modname.'/view.php?id='.$mod->id;

            if ($meta->submitted) {
                if (empty($meta->timesubmitted)) {
                    $submittedonstr = '';
                } else {
                    $submittedonstr = ' '.userdate($meta->timesubmitted, get_string('strftimedate', 'langconfig'));
                }
                $message = html_writer::tag('i', '&nbsp;', array('class' => 'fa fa-check')) . $meta->submittedstr.$submittedonstr;
            } else {
                $warningstr = $meta->draft ? $meta->draftstr : $meta->notsubmittedstr;
                $warningstr = $meta->reopened ? $meta->reopenedstr : $warningstr;
                $message = $warningstr;
                $message = html_writer::tag('i', '&nbsp;', array('class' => 'fa fa-info-circle')) . $message;
            }

            return html_writer::link($url, $message);
        }
        return '';
    }

    // End.

}

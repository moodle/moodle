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
 * Renderer for use with the course section and all the goodness that falls
 * within it.
 *
 * This renderer should contain methods useful to courses, and categories.
 *
 * @package   moodlecore
 * @copyright 2010 Sam Hemelryk
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

 defined('MOODLE_INTERNAL') || die();

/**
 * The core course renderer
 *
 * Can be retrieved with the following:
 * $renderer = $PAGE->get_renderer('core','course');
 */
class core_course_renderer extends plugin_renderer_base {
    const COURSECAT_SHOW_COURSES_NONE = 0; /* do not show courses at all */
    const COURSECAT_SHOW_COURSES_COUNT = 5; /* do not show courses but show number of courses next to category name */
    const COURSECAT_SHOW_COURSES_COLLAPSED = 10;
    const COURSECAT_SHOW_COURSES_AUTO = 15; /* will choose between collapsed and expanded automatically */
    const COURSECAT_SHOW_COURSES_EXPANDED = 20;
    const COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT = 30;

    const COURSECAT_TYPE_CATEGORY = 0;
    const COURSECAT_TYPE_COURSE = 1;

    /**
     * A cache of strings
     * @var stdClass
     */
    protected $strings;

    /**
     * Whether a category content is being initially rendered with children. This is mainly used by the
     * core_course_renderer::corsecat_tree() to render the appropriate action for the Expand/Collapse all link on
     * page load.
     * @var bool
     */
    protected $categoryexpandedonload = false;

    /**
     * Override the constructor so that we can initialise the string cache
     *
     * @param moodle_page $page
     * @param string $target
     */
    public function __construct(moodle_page $page, $target) {
        $this->strings = new stdClass;
        $courseid = $page->course->id;
        parent::__construct($page, $target);
    }

    /**
     * @deprecated since 3.2
     */
    protected function add_modchoosertoggle() {
        throw new coding_exception('core_course_renderer::add_modchoosertoggle() can not be used anymore.');
    }

    /**
     * Renders course info box.
     *
     * @param stdClass $course
     * @return string
     */
    public function course_info_box(stdClass $course) {
        $content = '';
        $content .= $this->output->box_start('generalbox info');
        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
        $content .= $this->coursecat_coursebox($chelper, $course);
        $content .= $this->output->box_end();
        return $content;
    }

    /**
     * Renderers a structured array of courses and categories into a nice XHTML tree structure.
     *
     * @deprecated since 2.5
     *
     * @param array $ignored argument ignored
     * @return string
     */
    final public function course_category_tree(array $ignored) {
        debugging('Function core_course_renderer::course_category_tree() is deprecated, please use frontpage_combo_list()', DEBUG_DEVELOPER);
        return $this->frontpage_combo_list();
    }

    /**
     * Renderers a category for use with course_category_tree
     *
     * @deprecated since 2.5
     *
     * @param array $category
     * @param int $depth
     * @return string
     */
    final protected function course_category_tree_category(stdClass $category, $depth=1) {
        debugging('Function core_course_renderer::course_category_tree_category() is deprecated', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Render a modchooser.
     *
     * @param renderable $modchooser The chooser.
     * @return string
     */
    public function render_modchooser(renderable $modchooser) {
        return $this->render_from_template('core_course/modchooser', $modchooser->export_for_template($this));
    }

    /**
     * @deprecated since 3.9
     */
    public function course_modchooser() {
        throw new coding_exception('course_modchooser() can not be used anymore, please use course_activitychooser() instead.');
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
     * Build the HTML for a specified set of modules
     *
     * @param array $modules A set of modules as used by the
     * course_modchooser_module function
     * @return string The composed HTML for the module
     */
    protected function course_modchooser_module_types($modules) {
        debugging('Method core_course_renderer::course_modchooser_module_types() is deprecated, ' .
            'see core_course_renderer::render_modchooser().', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * Return the HTML for the specified module adding any required classes
     *
     * @param object $module An object containing the title, and link. An
     * icon, and help text may optionally be specified. If the module
     * contains subtypes in the types option, then these will also be
     * displayed.
     * @param array $classes Additional classes to add to the encompassing
     * div element
     * @return string The composed HTML for the module
     */
    protected function course_modchooser_module($module, $classes = array('option')) {
        debugging('Method core_course_renderer::course_modchooser_module() is deprecated, ' .
            'see core_course_renderer::render_modchooser().', DEBUG_DEVELOPER);
        return '';
    }

    protected function course_modchooser_title($title, $identifier = null) {
        debugging('Method core_course_renderer::course_modchooser_title() is deprecated, ' .
            'see core_course_renderer::render_modchooser().', DEBUG_DEVELOPER);
        return '';
    }

    /**
     * @deprecated since 4.0 - please do not use this function any more.
     */
    public function course_section_cm_edit_actions($actions, cm_info $mod = null, $displayoptions = array()) {

        throw new coding_exception(
            'course_section_cm_edit_actions can not be used any more. Please, use ' .
            'core_courseformat\\output\\local\\content\\cm\\controlmenu instead.'
        );
    }

    /**
     * Renders HTML for the menus to add activities and resources to the current course
     *
     * Renders the ajax control (the link which when clicked produces the activity chooser modal). No noscript fallback.
     *
     * @param stdClass $course
     * @param int $section relative section number (field course_sections.section)
     * @param int $sectionreturn The section to link back to
     * @param array $displayoptions additional display options, for example blocks add
     *     option 'inblock' => true, suggesting to display controls vertically
     * @return string
     */
    function course_section_add_cm_control($course, $section, $sectionreturn = null, $displayoptions = array()) {
        // Check to see if user can add menus.
        if (!has_capability('moodle/course:manageactivities', context_course::instance($course->id))
                || !$this->page->user_is_editing()) {
            return '';
        }

        $data = [
            'sectionid' => $section,
            'sectionreturn' => $sectionreturn
        ];
        $ajaxcontrol = $this->render_from_template('course/activitychooserbutton', $data);

        // Load the JS for the modal.
        $this->course_activitychooser($course->id);

        return $ajaxcontrol;
    }

    /**
     * Renders html to display a course search form
     *
     * @param string $value default value to populate the search field
     * @return string
     */
    public function course_search_form($value = '') {

        $data = [
            'action' => \core_search\manager::get_course_search_url(),
            'btnclass' => 'btn-primary',
            'inputname' => 'q',
            'searchstring' => get_string('searchcourses'),
            'hiddenfields' => (object) ['name' => 'areaids', 'value' => 'core_course-course'],
            'query' => $value
        ];
        return $this->render_from_template('core/search_input', $data);
    }

    /**
     * @deprecated since Moodle 3.11
     */
    public function course_section_cm_completion() {
        throw new coding_exception(__FUNCTION__ . ' is deprecated. Use the activity_completion output component instead.');
    }

    /**
     * @deprecated since 4.0 - please do not use this function any more.
     */
    public function is_cm_conditionally_hidden(cm_info $mod) {

        throw new coding_exception(
            'is_cm_conditionally_hidden can not be used any more. Please, use ' .
            '\core_availability\info_module::is_available_for_all instead'
        );
    }

    /**
     * @deprecated since 4.0 - please do not use this function any more.
     */
    public function course_section_cm_name(cm_info $mod, $displayoptions = array()) {

        throw new coding_exception(
            'course_section_cm_name can not be used any more. Please, use ' .
            'core_courseformat\\output\\local\\content\\cm\\cmname class instead.'
        );
    }

    /**
     * @deprecated since 4.0 - please do not use this function any more.
     */
    protected function course_section_cm_classes(cm_info $mod) {

        throw new coding_exception(
            'course_section_cm_classes can not be used any more. Now it is part of core_courseformat\\output\\local\\content\\cm'
        );
    }

    /**
     * @deprecated since 4.0 - please do not use this function any more.
     */
    public function course_section_cm_name_title(cm_info $mod, $displayoptions = array()) {

        throw new coding_exception(
            'course_section_cm_name_title can not be used any more. Please, use ' .
            'core_courseformat\\output\\local\\cm\\title class instead'
        );
    }

    /**
     * @deprecated since 4.0 - please do not use this function any more.
     */
    public function course_section_cm_text(cm_info $mod, $displayoptions = array()) {

        throw new coding_exception(
            'course_section_cm_text can not be used any more. Now it is part of core_courseformat\\output\\local\\content\\cm'
        );
    }

    /**
     * @deprecated since 4.0 - please do not use this function any more.
     */
    public function availability_info($text, $additionalclasses = '') {

        throw new coding_exception(
            'availability_info can not be used any more. Please, use ' .
            'core_courseformat\\output\\local\\content\\section\\availability instead'
        );
    }

    /**
     * @deprecated since 4.0 - please do not use this function any more.
     */
    public function course_section_cm_availability(cm_info $mod, $displayoptions = array()) {

        throw new coding_exception(
            'course_section_cm_availability can not be used any more. Please, use ' .
            'core_courseformat\\output\\local\\content\\cm\\availability instead'
        );
    }

    /**
     * @deprecated since 4.0 - use core_course output components or course_format::course_section_updated_cm_item instead.
     */
    public function course_section_cm_list_item($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = []) {

        throw new coding_exception(
            'course_section_cm_list_item can not be used any more. Please, use renderer course_section_updated_cm_item instead'
        );
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    public function course_section_cm($course, &$completioninfo, cm_info $mod, $sectionreturn, $displayoptions = []) {

        throw new coding_exception(
            'course_section_cm can not be used any more. Please, use core_courseformat\\output\\content\\cm output class instead'
        );
    }

    /**
     * Message displayed to the user when they try to access unavailable activity following URL
     *
     * This method is a very simplified version of {@link course_section_cm()} to be part of the error
     * notification only. It also does not check if module is visible on course page or not.
     *
     * The message will be displayed inside notification!
     *
     * @param cm_info $cm
     * @return string
     */
    public function course_section_cm_unavailable_error_message(cm_info $cm) {
        if ($cm->uservisible) {
            return null;
        }
        if (!$cm->availableinfo) {
            return get_string('activityiscurrentlyhidden');
        }

        $altname = get_accesshide(' ' . $cm->modfullname);
        $name = html_writer::empty_tag('img', array('src' => $cm->get_icon_url(),
                'class' => 'iconlarge activityicon', 'alt' => '')) .
            html_writer::tag('span', ' '.$cm->get_formatted_name() . $altname, array('class' => 'instancename'));
        $formattedinfo = \core_availability\info::format_info($cm->availableinfo, $cm->get_course());
        return html_writer::div($name, 'activityinstance-error') .
        html_writer::div($formattedinfo, 'availabilityinfo-error');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    public function course_section_cm_list($course, $section, $sectionreturn = null, $displayoptions = []) {

        throw new coding_exception(
            'course_section_cm_list can not be used any more. Please, use ' .
            'core_courseformat\\output\\local\\content\\section\\cmlist class instead'
        );
    }

    /**
     * Displays a custom list of courses with paging bar if necessary
     *
     * If $paginationurl is specified but $totalcount is not, the link 'View more'
     * appears under the list.
     *
     * If both $paginationurl and $totalcount are specified, and $totalcount is
     * bigger than count($courses), a paging bar is displayed above and under the
     * courses list.
     *
     * @param array $courses array of course records (or instances of core_course_list_element) to show on this page
     * @param bool $showcategoryname whether to add category name to the course description
     * @param string $additionalclasses additional CSS classes to add to the div.courses
     * @param moodle_url $paginationurl url to view more or url to form links to the other pages in paging bar
     * @param int $totalcount total number of courses on all pages, if omitted $paginationurl will be displayed as 'View more' link
     * @param int $page current page number (defaults to 0 referring to the first page)
     * @param int $perpage number of records per page (defaults to $CFG->coursesperpage)
     * @return string
     */
    public function courses_list($courses, $showcategoryname = false, $additionalclasses = null, $paginationurl = null, $totalcount = null, $page = 0, $perpage = null) {
        global $CFG;
        // create instance of coursecat_helper to pass display options to function rendering courses list
        $chelper = new coursecat_helper();
        if ($showcategoryname) {
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT);
        } else {
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
        }
        if ($totalcount !== null && $paginationurl !== null) {
            // add options to display pagination
            if ($perpage === null) {
                $perpage = $CFG->coursesperpage;
            }
            $chelper->set_courses_display_options(array(
                'limit' => $perpage,
                'offset' => ((int)$page) * $perpage,
                'paginationurl' => $paginationurl,
            ));
        } else if ($paginationurl !== null) {
            // add options to display 'View more' link
            $chelper->set_courses_display_options(array('viewmoreurl' => $paginationurl));
            $totalcount = count($courses) + 1; // has to be bigger than count($courses) otherwise link will not be displayed
        }
        $chelper->set_attributes(array('class' => $additionalclasses));
        $content = $this->coursecat_courses($chelper, $courses, $totalcount);
        return $content;
    }

    /**
     * Returns HTML to display course name.
     *
     * @param coursecat_helper $chelper
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_name(coursecat_helper $chelper, core_course_list_element $course): string {
        $content = '';
        if ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $nametag = 'h3';
        } else {
            $nametag = 'div';
        }
        $coursename = $chelper->get_course_formatted_name($course);
        $coursenamelink = html_writer::link(new moodle_url('/course/view.php', ['id' => $course->id]),
            $coursename, ['class' => $course->visible ? 'aalink' : 'aalink dimmed']);
        $content .= html_writer::tag($nametag, $coursenamelink, ['class' => 'coursename']);
        // If we display course in collapsed form but the course has summary or course contacts, display the link to the info page.
        $content .= html_writer::start_tag('div', ['class' => 'moreinfo']);
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            if ($course->has_summary() || $course->has_course_contacts() || $course->has_course_overviewfiles()
                || $course->has_custom_fields()) {
                $url = new moodle_url('/course/info.php', ['id' => $course->id]);
                $image = $this->output->pix_icon('i/info', $this->strings->summary);
                $content .= html_writer::link($url, $image, ['title' => $this->strings->summary]);
                // Make sure JS file to expand course content is included.
                $this->coursecat_include_js();
            }
        }
        $content .= html_writer::end_tag('div');
        return $content;
    }

    /**
     * Returns HTML to display course enrolment icons.
     *
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_enrolment_icons(core_course_list_element $course): string {
        $content = '';
        if ($icons = enrol_get_course_info_icons($course)) {
            $content .= html_writer::start_tag('div', ['class' => 'enrolmenticons']);
            foreach ($icons as $icon) {
                $content .= $this->render($icon);
            }
            $content .= html_writer::end_tag('div');
        }
        return $content;
    }

    /**
     * Displays one course in the list of courses.
     *
     * This is an internal function, to display an information about just one course
     * please use {@link core_course_renderer::course_info_box()}
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_list_element|stdClass $course
     * @param string $additionalclasses additional classes to add to the main <div> tag (usually
     *    depend on the course position in list - first/last/even/odd)
     * @return string
     */
    protected function coursecat_coursebox(coursecat_helper $chelper, $course, $additionalclasses = '') {
        if (!isset($this->strings->summary)) {
            $this->strings->summary = get_string('summary');
        }
        if ($chelper->get_show_courses() <= self::COURSECAT_SHOW_COURSES_COUNT) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        $content = '';
        $classes = trim('coursebox clearfix '. $additionalclasses);
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            $classes .= ' collapsed';
        }

        // .coursebox
        $content .= html_writer::start_tag('div', array(
            'class' => $classes,
            'data-courseid' => $course->id,
            'data-type' => self::COURSECAT_TYPE_COURSE,
        ));

        $content .= html_writer::start_tag('div', array('class' => 'info'));
        $content .= $this->course_name($chelper, $course);
        $content .= $this->course_enrolment_icons($course);
        $content .= html_writer::end_tag('div');

        $content .= html_writer::start_tag('div', array('class' => 'content'));
        $content .= $this->coursecat_coursebox_content($chelper, $course);
        $content .= html_writer::end_tag('div');

        $content .= html_writer::end_tag('div'); // .coursebox
        return $content;
    }

    /**
     * Returns HTML to display course summary.
     *
     * @param coursecat_helper $chelper
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_summary(coursecat_helper $chelper, core_course_list_element $course): string {
        $content = '';
        if ($course->has_summary()) {
            $content .= html_writer::start_tag('div', ['class' => 'summary']);
            $content .= $chelper->get_course_formatted_summary($course,
                array('overflowdiv' => true, 'noclean' => true, 'para' => false));
            $content .= html_writer::end_tag('div');
        }
        return $content;
    }

    /**
     * Returns HTML to display course contacts.
     *
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_contacts(core_course_list_element $course) {
        $content = '';
        if ($course->has_course_contacts()) {
            $content .= html_writer::start_tag('ul', ['class' => 'teachers']);
            foreach ($course->get_course_contacts() as $coursecontact) {
                $rolenames = array_map(function ($role) {
                    return $role->displayname;
                }, $coursecontact['roles']);
                $name = html_writer::tag('span', implode(", ", $rolenames).': ', ['class' => 'font-weight-bold']);
                $name .= html_writer::link(
                   \core_user::get_profile_url($coursecontact['user'], context_system::instance()),
                   $coursecontact['username']
                );
                $content .= html_writer::tag('li', $name);
            }
            $content .= html_writer::end_tag('ul');
        }
        return $content;
    }

    /**
     * Returns HTML to display course overview files.
     *
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_overview_files(core_course_list_element $course): string {
        global $CFG;

        $contentimages = $contentfiles = '';
        foreach ($course->get_course_overviewfiles() as $file) {
            $isimage = $file->is_valid_image();
            $url = moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$isimage);
            if ($isimage) {
                $contentimages .= html_writer::tag('div',
                    html_writer::empty_tag('img', ['src' => $url, 'alt' => '']),
                    ['class' => 'courseimage']);
            } else {
                $image = $this->output->pix_icon(file_file_icon($file), $file->get_filename(), 'moodle');
                $filename = html_writer::tag('span', $image, ['class' => 'fp-icon']).
                    html_writer::tag('span', $file->get_filename(), ['class' => 'fp-filename']);
                $contentfiles .= html_writer::tag('span',
                    html_writer::link($url, $filename),
                    ['class' => 'coursefile fp-filename-icon text-break']);
            }
        }
        return $contentimages . $contentfiles;
    }

    /**
     * Returns HTML to display course category name.
     *
     * @param coursecat_helper $chelper
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_category_name(coursecat_helper $chelper, core_course_list_element $course): string {
        $content = '';
        // Display course category if necessary (for example in search results).
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT) {
            if ($cat = core_course_category::get($course->category, IGNORE_MISSING)) {
                $content .= html_writer::start_tag('div', ['class' => 'coursecat']);
                $content .= html_writer::tag('span', get_string('category').': ', ['class' => 'font-weight-bold']);
                $content .= html_writer::link(new moodle_url('/course/index.php', ['categoryid' => $cat->id]),
                        $cat->get_formatted_name(), ['class' => $cat->visible ? '' : 'dimmed']);
                $content .= html_writer::end_tag('div');
            }
        }
        return $content;
    }

    /**
     * Returns HTML to display course custom fields.
     *
     * @param core_course_list_element $course
     * @return string
     */
    protected function course_custom_fields(core_course_list_element $course): string {
        $content = '';
        if ($course->has_custom_fields()) {
            $handler = core_course\customfield\course_handler::create();
            $customfields = $handler->display_custom_fields_data($course->get_custom_fields());
            $content .= \html_writer::tag('div', $customfields, ['class' => 'customfields-container']);
        }
        return $content;
    }

    /**
     * Returns HTML to display course content (summary, course contacts and optionally category name)
     *
     * This method is called from coursecat_coursebox() and may be re-used in AJAX
     *
     * @param coursecat_helper $chelper various display options
     * @param stdClass|core_course_list_element $course
     * @return string
     */
    protected function coursecat_coursebox_content(coursecat_helper $chelper, $course) {
        if ($chelper->get_show_courses() < self::COURSECAT_SHOW_COURSES_EXPANDED) {
            return '';
        }
        if ($course instanceof stdClass) {
            $course = new core_course_list_element($course);
        }
        $content = \html_writer::start_tag('div', ['class' => 'd-flex']);
        $content .= $this->course_overview_files($course);
        $content .= \html_writer::start_tag('div', ['class' => 'flex-grow-1']);
        $content .= $this->course_summary($chelper, $course);
        $content .= $this->course_contacts($course);
        $content .= $this->course_category_name($chelper, $course);
        $content .= $this->course_custom_fields($course);
        $content .= \html_writer::end_tag('div');
        $content .= \html_writer::end_tag('div');
        return $content;
    }

    /**
     * Renders the list of courses
     *
     * This is internal function, please use {@link core_course_renderer::courses_list()} or another public
     * method from outside of the class
     *
     * If list of courses is specified in $courses; the argument $chelper is only used
     * to retrieve display options and attributes, only methods get_show_courses(),
     * get_courses_display_option() and get_and_erase_attributes() are called.
     *
     * @param coursecat_helper $chelper various display options
     * @param array $courses the list of courses to display
     * @param int|null $totalcount total number of courses (affects display mode if it is AUTO or pagination if applicable),
     *     defaulted to count($courses)
     * @return string
     */
    protected function coursecat_courses(coursecat_helper $chelper, $courses, $totalcount = null) {
        global $CFG;
        if ($totalcount === null) {
            $totalcount = count($courses);
        }
        if (!$totalcount) {
            // Courses count is cached during courses retrieval.
            return '';
        }

        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO) {
            // In 'auto' course display mode we analyse if number of courses is more or less than $CFG->courseswithsummarieslimit
            if ($totalcount <= $CFG->courseswithsummarieslimit) {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            } else {
                $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
            }
        }

        // prepare content of paging bar if it is needed
        $paginationurl = $chelper->get_courses_display_option('paginationurl');
        $paginationallowall = $chelper->get_courses_display_option('paginationallowall');
        if ($totalcount > count($courses)) {
            // there are more results that can fit on one page
            if ($paginationurl) {
                // the option paginationurl was specified, display pagingbar
                $perpage = $chelper->get_courses_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_courses_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                        $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                            get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // the option for 'View more' link was specified, display more link
                $viewmoretext = $chelper->get_courses_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag(
                    'div',
                    html_writer::link($viewmoreurl, $viewmoretext, ['class' => 'btn btn-secondary']),
                    ['class' => 'paging paging-morelink']
                );
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string('showperpage', '', $CFG->coursesperpage)), array('class' => 'paging paging-showperpage'));
        }

        // display list of courses
        $attributes = $chelper->get_and_erase_attributes('courses');
        $content = html_writer::start_tag('div', $attributes);

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        $coursecount = 0;
        foreach ($courses as $course) {
            $coursecount ++;
            $classes = ($coursecount%2) ? 'odd' : 'even';
            if ($coursecount == 1) {
                $classes .= ' first';
            }
            if ($coursecount >= count($courses)) {
                $classes .= ' last';
            }
            $content .= $this->coursecat_coursebox($chelper, $course, $classes);
        }

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div'); // .courses
        return $content;
    }

    /**
     * Renders the list of subcategories in a category
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_category $coursecat
     * @param int $depth depth of the category in the current tree
     * @return string
     */
    protected function coursecat_subcategories(coursecat_helper $chelper, $coursecat, $depth) {
        global $CFG;
        $subcategories = array();
        if (!$chelper->get_categories_display_option('nodisplay')) {
            $subcategories = $coursecat->get_children($chelper->get_categories_display_options());
        }
        $totalcount = $coursecat->get_children_count();
        if (!$totalcount) {
            // Note that we call core_course_category::get_children_count() AFTER core_course_category::get_children()
            // to avoid extra DB requests.
            // Categories count is cached during children categories retrieval.
            return '';
        }

        // prepare content of paging bar or more link if it is needed
        $paginationurl = $chelper->get_categories_display_option('paginationurl');
        $paginationallowall = $chelper->get_categories_display_option('paginationallowall');
        if ($totalcount > count($subcategories)) {
            if ($paginationurl) {
                // the option 'paginationurl was specified, display pagingbar
                $perpage = $chelper->get_categories_display_option('limit', $CFG->coursesperpage);
                $page = $chelper->get_categories_display_option('offset') / $perpage;
                $pagingbar = $this->paging_bar($totalcount, $page, $perpage,
                        $paginationurl->out(false, array('perpage' => $perpage)));
                if ($paginationallowall) {
                    $pagingbar .= html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => 'all')),
                            get_string('showall', '', $totalcount)), array('class' => 'paging paging-showall'));
                }
            } else if ($viewmoreurl = $chelper->get_categories_display_option('viewmoreurl')) {
                // the option 'viewmoreurl' was specified, display more link (if it is link to category view page, add category id)
                if ($viewmoreurl->compare(new moodle_url('/course/index.php'), URL_MATCH_BASE)) {
                    $viewmoreurl->param('categoryid', $coursecat->id);
                }
                $viewmoretext = $chelper->get_categories_display_option('viewmoretext', new lang_string('viewmore'));
                $morelink = html_writer::tag('div', html_writer::link($viewmoreurl, $viewmoretext),
                        array('class' => 'paging paging-morelink'));
            }
        } else if (($totalcount > $CFG->coursesperpage) && $paginationurl && $paginationallowall) {
            // there are more than one page of results and we are in 'view all' mode, suggest to go back to paginated view mode
            $pagingbar = html_writer::tag('div', html_writer::link($paginationurl->out(false, array('perpage' => $CFG->coursesperpage)),
                get_string('showperpage', '', $CFG->coursesperpage)), array('class' => 'paging paging-showperpage'));
        }

        // display list of subcategories
        $content = html_writer::start_tag('div', array('class' => 'subcategories'));

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }

        foreach ($subcategories as $subcategory) {
            $content .= $this->coursecat_category($chelper, $subcategory, $depth + 1);
        }

        if (!empty($pagingbar)) {
            $content .= $pagingbar;
        }
        if (!empty($morelink)) {
            $content .= $morelink;
        }

        $content .= html_writer::end_tag('div');
        return $content;
    }

    /**
     * Make sure that javascript file for AJAX expanding of courses and categories content is included
     */
    protected function coursecat_include_js() {
        if (!$this->page->requires->should_create_one_time_item_now('core_course_categoryexpanderjsinit')) {
            return;
        }

        // We must only load this module once.
        $this->page->requires->yui_module('moodle-course-categoryexpander',
                'Y.Moodle.course.categoryexpander.init');
    }

    /**
     * Returns HTML to display the subcategories and courses in the given category
     *
     * This method is re-used by AJAX to expand content of not loaded category
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_category $coursecat
     * @param int $depth depth of the category in the current tree
     * @return string
     */
    protected function coursecat_category_content(coursecat_helper $chelper, $coursecat, $depth) {
        $content = '';
        // Subcategories
        $content .= $this->coursecat_subcategories($chelper, $coursecat, $depth);

        // AUTO show courses: Courses will be shown expanded if this is not nested category,
        // and number of courses no bigger than $CFG->courseswithsummarieslimit.
        $showcoursesauto = $chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_AUTO;
        if ($showcoursesauto && $depth) {
            // this is definitely collapsed mode
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_COLLAPSED);
        }

        // Courses
        if ($chelper->get_show_courses() > core_course_renderer::COURSECAT_SHOW_COURSES_COUNT) {
            $courses = array();
            if (!$chelper->get_courses_display_option('nodisplay')) {
                $courses = $coursecat->get_courses($chelper->get_courses_display_options());
            }
            if ($viewmoreurl = $chelper->get_courses_display_option('viewmoreurl')) {
                // the option for 'View more' link was specified, display more link (if it is link to category view page, add category id)
                if ($viewmoreurl->compare(new moodle_url('/course/index.php'), URL_MATCH_BASE)) {
                    $chelper->set_courses_display_option('viewmoreurl', new moodle_url($viewmoreurl, array('categoryid' => $coursecat->id)));
                }
            }
            $content .= $this->coursecat_courses($chelper, $courses, $coursecat->get_courses_count());
        }

        if ($showcoursesauto) {
            // restore the show_courses back to AUTO
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO);
        }

        return $content;
    }

    /**
     * Returns HTML to display a course category as a part of a tree
     *
     * This is an internal function, to display a particular category and all its contents
     * use {@link core_course_renderer::course_category()}
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_category $coursecat
     * @param int $depth depth of this category in the current tree
     * @return string
     */
    protected function coursecat_category(coursecat_helper $chelper, $coursecat, $depth) {
        // open category tag
        $classes = array('category');
        if (empty($coursecat->visible)) {
            $classes[] = 'dimmed_category';
        }
        if ($chelper->get_subcat_depth() > 0 && $depth >= $chelper->get_subcat_depth()) {
            // do not load content
            $categorycontent = '';
            $classes[] = 'notloaded';
            if ($coursecat->get_children_count() ||
                    ($chelper->get_show_courses() >= self::COURSECAT_SHOW_COURSES_COLLAPSED && $coursecat->get_courses_count())) {
                $classes[] = 'with_children';
                $classes[] = 'collapsed';
            }
        } else {
            // load category content
            $categorycontent = $this->coursecat_category_content($chelper, $coursecat, $depth);
            $classes[] = 'loaded';
            if (!empty($categorycontent)) {
                $classes[] = 'with_children';
                // Category content loaded with children.
                $this->categoryexpandedonload = true;
            }
        }

        // Make sure JS file to expand category content is included.
        $this->coursecat_include_js();

        $content = html_writer::start_tag('div', array(
            'class' => join(' ', $classes),
            'data-categoryid' => $coursecat->id,
            'data-depth' => $depth,
            'data-showcourses' => $chelper->get_show_courses(),
            'data-type' => self::COURSECAT_TYPE_CATEGORY,
        ));

        // category name
        $categoryname = $coursecat->get_formatted_name();
        $categoryname = html_writer::link(new moodle_url('/course/index.php',
                array('categoryid' => $coursecat->id)),
                $categoryname);
        if ($chelper->get_show_courses() == self::COURSECAT_SHOW_COURSES_COUNT
                && ($coursescount = $coursecat->get_courses_count())) {
            $categoryname .= html_writer::tag('span', ' ('. $coursescount.')',
                    array('title' => get_string('numberofcourses'), 'class' => 'numberofcourse'));
        }
        $content .= html_writer::start_tag('div', array('class' => 'info'));

        $content .= html_writer::tag(($depth > 1) ? 'h4' : 'h3', $categoryname, array('class' => 'categoryname aabtn'));
        $content .= html_writer::end_tag('div'); // .info

        // add category content to the output
        $content .= html_writer::tag('div', $categorycontent, array('class' => 'content'));

        $content .= html_writer::end_tag('div'); // .category

        // Return the course category tree HTML
        return $content;
    }

    /**
     * Returns HTML to display a tree of subcategories and courses in the given category
     *
     * @param coursecat_helper $chelper various display options
     * @param core_course_category $coursecat top category (this category's name and description will NOT be added to the tree)
     * @return string
     */
    protected function coursecat_tree(coursecat_helper $chelper, $coursecat) {
        // Reset the category expanded flag for this course category tree first.
        $this->categoryexpandedonload = false;
        $categorycontent = $this->coursecat_category_content($chelper, $coursecat, 0);
        if (empty($categorycontent)) {
            return '';
        }

        // Start content generation
        $content = '';
        $attributes = $chelper->get_and_erase_attributes('course_category_tree clearfix');
        $content .= html_writer::start_tag('div', $attributes);

        if ($coursecat->get_children_count()) {
            $classes = array(
                'collapseexpand', 'aabtn'
            );

            // Check if the category content contains subcategories with children's content loaded.
            if ($this->categoryexpandedonload) {
                $classes[] = 'collapse-all';
                $linkname = get_string('collapseall');
            } else {
                $linkname = get_string('expandall');
            }

            // Only show the collapse/expand if there are children to expand.
            $content .= html_writer::start_tag('div', array('class' => 'collapsible-actions'));
            $content .= html_writer::link('#', $linkname, array('class' => implode(' ', $classes)));
            $content .= html_writer::end_tag('div');
            $this->page->requires->strings_for_js(array('collapseall', 'expandall'), 'moodle');
        }

        $content .= html_writer::tag('div', $categorycontent, array('class' => 'content'));

        $content .= html_writer::end_tag('div'); // .course_category_tree

        return $content;
    }

    /**
     * Renders HTML to display particular course category - list of it's subcategories and courses
     *
     * Invoked from /course/index.php
     *
     * @param int|stdClass|core_course_category $category
     */
    public function course_category($category) {
        global $CFG;
        $usertop = core_course_category::user_top();
        if (empty($category)) {
            $coursecat = $usertop;
        } else if (is_object($category) && $category instanceof core_course_category) {
            $coursecat = $category;
        } else {
            $coursecat = core_course_category::get(is_object($category) ? $category->id : $category);
        }
        $site = get_site();
        $actionbar = new \core_course\output\category_action_bar($this->page, $coursecat);
        $output = $this->render_from_template('core_course/category_actionbar', $actionbar->export_for_template($this));

        if (core_course_category::is_simple_site()) {
            // There is only one category in the system, do not display link to it.
            $strfulllistofcourses = get_string('fulllistofcourses');
            $this->page->set_title($strfulllistofcourses);
        } else if (!$coursecat->id || !$coursecat->is_uservisible()) {
            $strcategories = get_string('categories');
            $this->page->set_title($strcategories);
        } else {
            $strfulllistofcourses = get_string('fulllistofcourses');
            $this->page->set_title($strfulllistofcourses);
        }

        // Print current category description
        $chelper = new coursecat_helper();
        if ($description = $chelper->get_category_formatted_description($coursecat)) {
            $output .= $this->box($description, array('class' => 'generalbox info'));
        }

        // Prepare parameters for courses and categories lists in the tree
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_AUTO)
                ->set_attributes(array('class' => 'category-browse category-browse-'.$coursecat->id));

        $coursedisplayoptions = array();
        $catdisplayoptions = array();
        $browse = optional_param('browse', null, PARAM_ALPHA);
        $perpage = optional_param('perpage', $CFG->coursesperpage, PARAM_INT);
        $page = optional_param('page', 0, PARAM_INT);
        $baseurl = new moodle_url('/course/index.php');
        if ($coursecat->id) {
            $baseurl->param('categoryid', $coursecat->id);
        }
        if ($perpage != $CFG->coursesperpage) {
            $baseurl->param('perpage', $perpage);
        }
        $coursedisplayoptions['limit'] = $perpage;
        $catdisplayoptions['limit'] = $perpage;
        if ($browse === 'courses' || !$coursecat->get_children_count()) {
            $coursedisplayoptions['offset'] = $page * $perpage;
            $coursedisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $catdisplayoptions['nodisplay'] = true;
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $catdisplayoptions['viewmoretext'] = new lang_string('viewallsubcategories');
        } else if ($browse === 'categories' || !$coursecat->get_courses_count()) {
            $coursedisplayoptions['nodisplay'] = true;
            $catdisplayoptions['offset'] = $page * $perpage;
            $catdisplayoptions['paginationurl'] = new moodle_url($baseurl, array('browse' => 'categories'));
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses'));
            $coursedisplayoptions['viewmoretext'] = new lang_string('viewallcourses');
        } else {
            // we have a category that has both subcategories and courses, display pagination separately
            $coursedisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1));
            $catdisplayoptions['viewmoreurl'] = new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1));
        }
        $chelper->set_courses_display_options($coursedisplayoptions)->set_categories_display_options($catdisplayoptions);

        // Display course category tree.
        $output .= $this->coursecat_tree($chelper, $coursecat);

        return $output;
    }

    /**
     * Serves requests to /course/category.ajax.php
     *
     * In this renderer implementation it may expand the category content or
     * course content.
     *
     * @return string
     * @throws coding_exception
     */
    public function coursecat_ajax() {
        global $DB, $CFG;

        $type = required_param('type', PARAM_INT);

        if ($type === self::COURSECAT_TYPE_CATEGORY) {
            // This is a request for a category list of some kind.
            $categoryid = required_param('categoryid', PARAM_INT);
            $showcourses = required_param('showcourses', PARAM_INT);
            $depth = required_param('depth', PARAM_INT);

            $category = core_course_category::get($categoryid);

            $chelper = new coursecat_helper();
            $baseurl = new moodle_url('/course/index.php', array('categoryid' => $categoryid));
            $coursedisplayoptions = array(
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url($baseurl, array('browse' => 'courses', 'page' => 1))
            );
            $catdisplayoptions = array(
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url($baseurl, array('browse' => 'categories', 'page' => 1))
            );
            $chelper->set_show_courses($showcourses)->
                    set_courses_display_options($coursedisplayoptions)->
                    set_categories_display_options($catdisplayoptions);

            return $this->coursecat_category_content($chelper, $category, $depth);
        } else if ($type === self::COURSECAT_TYPE_COURSE) {
            // This is a request for the course information.
            $courseid = required_param('courseid', PARAM_INT);

            $course = $DB->get_record('course', array('id' => $courseid), '*', MUST_EXIST);

            $chelper = new coursecat_helper();
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED);
            return $this->coursecat_coursebox_content($chelper, $course);
        } else {
            throw new coding_exception('Invalid request type');
        }
    }

    /**
     * Renders html to display search result page
     *
     * @param array $searchcriteria may contain elements: search, blocklist, modulelist, tagid
     * @return string
     */
    public function search_courses($searchcriteria) {
        global $CFG;
        $content = '';

        $search = '';
        if (!empty($searchcriteria['search'])) {
            $search = $searchcriteria['search'];
        }
        $content .= $this->course_search_form($search);

        if (!empty($searchcriteria)) {
            // print search results

            $displayoptions = array('sort' => array('displayname' => 1));
            // take the current page and number of results per page from query
            $perpage = optional_param('perpage', 0, PARAM_RAW);
            if ($perpage !== 'all') {
                $displayoptions['limit'] = ((int)$perpage <= 0) ? $CFG->coursesperpage : (int)$perpage;
                $page = optional_param('page', 0, PARAM_INT);
                $displayoptions['offset'] = $displayoptions['limit'] * $page;
            }
            // options 'paginationurl' and 'paginationallowall' are only used in method coursecat_courses()
            $displayoptions['paginationurl'] = new moodle_url('/course/search.php', $searchcriteria);
            $displayoptions['paginationallowall'] = true; // allow adding link 'View all'

            $class = 'course-search-result';
            foreach ($searchcriteria as $key => $value) {
                if (!empty($value)) {
                    $class .= ' course-search-result-'. $key;
                }
            }
            $chelper = new coursecat_helper();
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT)->
                    set_courses_display_options($displayoptions)->
                    set_search_criteria($searchcriteria)->
                    set_attributes(array('class' => $class));

            $courses = core_course_category::search_courses($searchcriteria, $chelper->get_courses_display_options());
            $totalcount = core_course_category::search_courses_count($searchcriteria);
            $courseslist = $this->coursecat_courses($chelper, $courses, $totalcount);

            if (!$totalcount) {
                if (!empty($searchcriteria['search'])) {
                    $content .= $this->heading(get_string('nocoursesfound', '', $searchcriteria['search']));
                } else {
                    $content .= $this->heading(get_string('novalidcourses'));
                }
            } else {
                $content .= $this->heading(get_string('searchresults'). ": $totalcount");
                $content .= $courseslist;
            }
        }
        return $content;
    }

    /**
     * Renders html to print list of courses tagged with particular tag
     *
     * @param int $tagid id of the tag
     * @param bool $exclusivemode if set to true it means that no other entities tagged with this tag
     *             are displayed on the page and the per-page limit may be bigger
     * @param int $fromctx context id where the link was displayed, may be used by callbacks
     *            to display items in the same context first
     * @param int $ctx context id where to search for records
     * @param bool $rec search in subcontexts as well
     * @param array $displayoptions
     * @return string empty string if no courses are marked with this tag or rendered list of courses
     */
    public function tagged_courses($tagid, $exclusivemode = true, $ctx = 0, $rec = true, $displayoptions = null) {
        global $CFG;
        if (empty($displayoptions)) {
            $displayoptions = array();
        }
        $showcategories = !core_course_category::is_simple_site();
        $displayoptions += array('limit' => $CFG->coursesperpage, 'offset' => 0);
        $chelper = new coursecat_helper();
        $searchcriteria = array('tagid' => $tagid, 'ctx' => $ctx, 'rec' => $rec);
        $chelper->set_show_courses($showcategories ? self::COURSECAT_SHOW_COURSES_EXPANDED_WITH_CAT :
                    self::COURSECAT_SHOW_COURSES_EXPANDED)->
                set_search_criteria($searchcriteria)->
                set_courses_display_options($displayoptions)->
                set_attributes(array('class' => 'course-search-result course-search-result-tagid'));
                // (we set the same css class as in search results by tagid)
        if ($totalcount = core_course_category::search_courses_count($searchcriteria)) {
            $courses = core_course_category::search_courses($searchcriteria, $chelper->get_courses_display_options());
            if ($exclusivemode) {
                return $this->coursecat_courses($chelper, $courses, $totalcount);
            } else {
                $tagfeed = new core_tag\output\tagfeed();
                $img = $this->output->pix_icon('i/course', '');
                foreach ($courses as $course) {
                    $url = course_get_url($course);
                    $imgwithlink = html_writer::link($url, $img);
                    $coursename = html_writer::link($url, $course->get_formatted_name());
                    $details = '';
                    if ($showcategories && ($cat = core_course_category::get($course->category, IGNORE_MISSING))) {
                        $details = get_string('category').': '.
                                html_writer::link(new moodle_url('/course/index.php', array('categoryid' => $cat->id)),
                                        $cat->get_formatted_name(), array('class' => $cat->visible ? '' : 'dimmed'));
                    }
                    $tagfeed->add($imgwithlink, $coursename, $details);
                }
                return $this->output->render_from_template('core_tag/tagfeed', $tagfeed->export_for_template($this->output));
            }
        }
        return '';
    }

    /**
     * Returns HTML to display one remote course
     *
     * @param stdClass $course remote course information, contains properties:
           id, remoteid, shortname, fullname, hostid, summary, summaryformat, cat_name, hostname
     * @return string
     */
    protected function frontpage_remote_course(stdClass $course) {
        $url = new moodle_url('/auth/mnet/jump.php', array(
            'hostid' => $course->hostid,
            'wantsurl' => '/course/view.php?id='. $course->remoteid
        ));

        $output = '';
        $output .= html_writer::start_tag('div', array('class' => 'coursebox remotecoursebox clearfix'));
        $output .= html_writer::start_tag('div', array('class' => 'info'));
        $output .= html_writer::start_tag('h3', array('class' => 'coursename'));
        $output .= html_writer::link($url, format_string($course->fullname), array('title' => get_string('entercourse')));
        $output .= html_writer::end_tag('h3'); // .name
        $output .= html_writer::tag('div', '', array('class' => 'moreinfo'));
        $output .= html_writer::end_tag('div'); // .info
        $output .= html_writer::start_tag('div', array('class' => 'content'));
        $output .= html_writer::start_tag('div', array('class' => 'summary'));
        $options = new stdClass();
        $options->noclean = true;
        $options->para = false;
        $options->overflowdiv = true;
        $output .= format_text($course->summary, $course->summaryformat, $options);
        $output .= html_writer::end_tag('div'); // .summary
        $addinfo = format_string($course->hostname) . ' : '
            . format_string($course->cat_name) . ' : '
            . format_string($course->shortname);
        $output .= html_writer::tag('div', $addinfo, array('class' => 'remotecourseinfo'));
        $output .= html_writer::end_tag('div'); // .content
        $output .= html_writer::end_tag('div'); // .coursebox
        return $output;
    }

    /**
     * Returns HTML to display one remote host
     *
     * @param array $host host information, contains properties: name, url, count
     * @return string
     */
    protected function frontpage_remote_host($host) {
        $output = '';
        $output .= html_writer::start_tag('div', array('class' => 'coursebox remotehost clearfix'));
        $output .= html_writer::start_tag('div', array('class' => 'info'));
        $output .= html_writer::start_tag('h3', array('class' => 'name'));
        $output .= html_writer::link($host['url'], s($host['name']), array('title' => s($host['name'])));
        $output .= html_writer::end_tag('h3'); // .name
        $output .= html_writer::tag('div', '', array('class' => 'moreinfo'));
        $output .= html_writer::end_tag('div'); // .info
        $output .= html_writer::start_tag('div', array('class' => 'content'));
        $output .= html_writer::start_tag('div', array('class' => 'summary'));
        $output .= $host['count'] . ' ' . get_string('courses');
        $output .= html_writer::end_tag('div'); // .content
        $output .= html_writer::end_tag('div'); // .coursebox
        return $output;
    }

    /**
     * Returns HTML to print list of courses user is enrolled to for the frontpage
     *
     * Also lists remote courses or remote hosts if MNET authorisation is used
     *
     * @return string
     */
    public function frontpage_my_courses() {
        global $USER, $CFG, $DB;

        if (!isloggedin() or isguestuser()) {
            return '';
        }

        $output = '';
        $courses  = enrol_get_my_courses('summary, summaryformat');
        $rhosts   = array();
        $rcourses = array();
        if (!empty($CFG->mnet_dispatcher_mode) && $CFG->mnet_dispatcher_mode==='strict') {
            $rcourses = get_my_remotecourses($USER->id);
            $rhosts   = get_my_remotehosts();
        }

        if (!empty($courses) || !empty($rcourses) || !empty($rhosts)) {

            $chelper = new coursecat_helper();
            $totalcount = count($courses);
            if (count($courses) > $CFG->frontpagecourselimit) {
                // There are more enrolled courses than we can display, display link to 'My courses'.
                $courses = array_slice($courses, 0, $CFG->frontpagecourselimit, true);
                $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/my/courses.php'),
                        'viewmoretext' => new lang_string('mycourses')
                    ));
            } else if (core_course_category::top()->is_uservisible()) {
                // All enrolled courses are displayed, display link to 'All courses' if there are more courses in system.
                $chelper->set_courses_display_options(array(
                        'viewmoreurl' => new moodle_url('/course/index.php'),
                        'viewmoretext' => new lang_string('fulllistofcourses')
                    ));
                $totalcount = $DB->count_records('course') - 1;
            }
            $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->
                    set_attributes(array('class' => 'frontpage-course-list-enrolled'));
            $output .= $this->coursecat_courses($chelper, $courses, $totalcount);

            // MNET
            if (!empty($rcourses)) {
                // at the IDP, we know of all the remote courses
                $output .= html_writer::start_tag('div', array('class' => 'courses'));
                foreach ($rcourses as $course) {
                    $output .= $this->frontpage_remote_course($course);
                }
                $output .= html_writer::end_tag('div'); // .courses
            } elseif (!empty($rhosts)) {
                // non-IDP, we know of all the remote servers, but not courses
                $output .= html_writer::start_tag('div', array('class' => 'courses'));
                foreach ($rhosts as $host) {
                    $output .= $this->frontpage_remote_host($host);
                }
                $output .= html_writer::end_tag('div'); // .courses
            }
        }
        return $output;
    }

    /**
     * Returns HTML to print list of available courses for the frontpage
     *
     * @return string
     */
    public function frontpage_available_courses() {
        global $CFG;

        $chelper = new coursecat_helper();
        $chelper->set_show_courses(self::COURSECAT_SHOW_COURSES_EXPANDED)->
                set_courses_display_options(array(
                    'recursive' => true,
                    'limit' => $CFG->frontpagecourselimit,
                    'viewmoreurl' => new moodle_url('/course/index.php'),
                    'viewmoretext' => new lang_string('fulllistofcourses')));

        $chelper->set_attributes(array('class' => 'frontpage-course-list-all'));
        $courses = core_course_category::top()->get_courses($chelper->get_courses_display_options());
        $totalcount = core_course_category::top()->get_courses_count($chelper->get_courses_display_options());
        if (!$totalcount && !$this->page->user_is_editing() && has_capability('moodle/course:create', context_system::instance())) {
            // Print link to create a new course, for the 1st available category.
            return $this->add_new_course_button();
        }
        return $this->coursecat_courses($chelper, $courses, $totalcount);
    }

    /**
     * Returns HTML to the "add new course" button for the page
     *
     * @return string
     */
    public function add_new_course_button() {
        global $CFG;
        // Print link to create a new course, for the 1st available category.
        $output = $this->container_start('buttons');
        $url = new moodle_url('/course/edit.php', array('category' => $CFG->defaultrequestcategory, 'returnto' => 'topcat'));
        $output .= $this->single_button($url, get_string('addnewcourse'), 'get');
        $output .= $this->container_end('buttons');
        return $output;
    }

    /**
     * Returns HTML to print tree with course categories and courses for the frontpage
     *
     * @return string
     */
    public function frontpage_combo_list() {
        global $CFG;
        // TODO MDL-10965 improve.
        $tree = core_course_category::top();
        if (!$tree->get_children_count()) {
            return '';
        }
        $chelper = new coursecat_helper();
        $chelper->set_subcat_depth($CFG->maxcategorydepth)->
            set_categories_display_options(array(
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url('/course/index.php',
                        array('browse' => 'categories', 'page' => 1))
            ))->
            set_courses_display_options(array(
                'limit' => $CFG->coursesperpage,
                'viewmoreurl' => new moodle_url('/course/index.php',
                        array('browse' => 'courses', 'page' => 1))
            ))->
            set_attributes(array('class' => 'frontpage-category-combo'));
        return $this->coursecat_tree($chelper, $tree);
    }

    /**
     * Returns HTML to print tree of course categories (with number of courses) for the frontpage
     *
     * @return string
     */
    public function frontpage_categories_list() {
        global $CFG;
        // TODO MDL-10965 improve.
        $tree = core_course_category::top();
        if (!$tree->get_children_count()) {
            return '';
        }
        $chelper = new coursecat_helper();
        $chelper->set_subcat_depth($CFG->maxcategorydepth)->
                set_show_courses(self::COURSECAT_SHOW_COURSES_COUNT)->
                set_categories_display_options(array(
                    'limit' => $CFG->coursesperpage,
                    'viewmoreurl' => new moodle_url('/course/index.php',
                            array('browse' => 'categories', 'page' => 1))
                ))->
                set_attributes(array('class' => 'frontpage-category-names'));
        return $this->coursecat_tree($chelper, $tree);
    }

    /**
     * Renders the activity information.
     *
     * Defer to template.
     *
     * @deprecated since Moodle 4.3 MDL-78744
     * @todo MDL-78926 This method will be deleted in Moodle 4.7
     * @param \core_course\output\activity_information $page
     * @return string html for the page
     */
    public function render_activity_information(\core_course\output\activity_information $page) {
        debugging('render_activity_information method is deprecated.', DEBUG_DEVELOPER);
        $data = $page->export_for_template($this->output);
        return $this->output->render_from_template('core_course/activity_info', $data);
    }

    /**
     * Renders the activity navigation.
     *
     * Defer to template.
     *
     * @param \core_course\output\activity_navigation $page
     * @return string html for the page
     */
    public function render_activity_navigation(\core_course\output\activity_navigation $page) {
        $data = $page->export_for_template($this->output);
        return $this->output->render_from_template('core_course/activity_navigation', $data);
    }

    /**
     * Display waiting information about backup size during uploading backup process
     * @param object $backupfile the backup stored_file
     * @return $html string
     */
    public function sendingbackupinfo($backupfile) {
        $sizeinfo = new stdClass();
        $sizeinfo->total = number_format($backupfile->get_filesize() / 1000000, 2);
        $html = html_writer::tag('div', get_string('sendingsize', 'hub', $sizeinfo),
            array('class' => 'courseuploadtextinfo'));
        return $html;
    }

    /**
     * Hub information (logo - name - description - link)
     * @param object $hubinfo
     * @return string html code
     */
    public function hubinfo($hubinfo) {
        $screenshothtml = html_writer::empty_tag('img',
            array('src' => $hubinfo['imgurl'], 'alt' => $hubinfo['name']));
        $hubdescription = html_writer::tag('div', $screenshothtml,
            array('class' => 'hubscreenshot'));

        $hubdescription .= html_writer::tag('a', $hubinfo['name'],
            array('class' => 'hublink', 'href' => $hubinfo['url'],
                'onclick' => 'this.target="_blank"'));

        $hubdescription .= html_writer::tag('div', format_text($hubinfo['description'], FORMAT_PLAIN),
            array('class' => 'hubdescription'));
        $hubdescription = html_writer::tag('div', $hubdescription, array('class' => 'hubinfo clearfix'));

        return $hubdescription;
    }

    /**
     * Output frontpage summary text and frontpage modules (stored as section 1 in site course)
     *
     * This may be disabled in settings
     *
     * @return string
     */
    public function frontpage_section1() {
        global $SITE, $USER;

        $output = '';
        $editing = $this->page->user_is_editing();

        if ($editing) {
            // Make sure section with number 1 exists.
            course_create_sections_if_missing($SITE, 1);
        }

        $modinfo = get_fast_modinfo($SITE);
        $section = $modinfo->get_section_info(1);


        if (($section && (!empty($modinfo->sections[1]) or !empty($section->summary))) or $editing) {

            $format = course_get_format($SITE);
            $frontpageclass = $format->get_output_classname('content\\frontpagesection');
            $frontpagesection = new $frontpageclass($format, $section);

            // The course outputs works with format renderers, not with course renderers.
            $renderer = $format->get_renderer($this->page);
            $output .= $renderer->render($frontpagesection);
        }

        return $output;
    }

    /**
     * Output news for the frontpage (extract from site-wide news forum)
     *
     * @param stdClass $forum record from db table 'forum' that represents the site news forum
     * @return string
     */
    protected function frontpage_news($forum) {
        global $CFG, $SITE, $SESSION, $USER;
        require_once($CFG->dirroot .'/mod/forum/lib.php');

        $output = '';

        if (isloggedin()) {
            $SESSION->fromdiscussion = $CFG->wwwroot;
            $subtext = '';
            if (\mod_forum\subscriptions::is_subscribed($USER->id, $forum)) {
                if (!\mod_forum\subscriptions::is_forcesubscribed($forum)) {
                    $subtext = get_string('unsubscribe', 'forum');
                }
            } else {
                $subtext = get_string('subscribe', 'forum');
            }
            $suburl = new moodle_url('/mod/forum/subscribe.php', array('id' => $forum->id, 'sesskey' => sesskey()));
            $output .= html_writer::tag('div', html_writer::link($suburl, $subtext), array('class' => 'subscribelink'));
        }

        $coursemodule = get_coursemodule_from_instance('forum', $forum->id);
        $context = context_module::instance($coursemodule->id);

        $entityfactory = mod_forum\local\container::get_entity_factory();
        $forumentity = $entityfactory->get_forum_from_stdclass($forum, $context, $coursemodule, $SITE);

        $rendererfactory = mod_forum\local\container::get_renderer_factory();
        $discussionsrenderer = $rendererfactory->get_frontpage_news_discussion_list_renderer($forumentity);
        $cm = \cm_info::create($coursemodule);
        return $output . $discussionsrenderer->render($USER, $cm, null, null, 0, $SITE->newsitems);
    }

    /**
     * Renders part of frontpage with a skip link (i.e. "My courses", "Site news", etc.)
     *
     * @param string $skipdivid
     * @param string $contentsdivid
     * @param string $header Header of the part
     * @param string $contents Contents of the part
     * @return string
     */
    protected function frontpage_part($skipdivid, $contentsdivid, $header, $contents) {
        if (strval($contents) === '') {
            return '';
        }
        $output = html_writer::link('#' . $skipdivid,
            get_string('skipa', 'access', core_text::strtolower(strip_tags($header))),
            array('class' => 'skip-block skip aabtn'));

        // Wrap frontpage part in div container.
        $output .= html_writer::start_tag('div', array('id' => $contentsdivid));
        $output .= $this->heading($header);

        $output .= $contents;

        // End frontpage part div container.
        $output .= html_writer::end_tag('div');

        $output .= html_writer::tag('span', '', array('class' => 'skip-block-to', 'id' => $skipdivid));
        return $output;
    }

    /**
     * Outputs contents for frontpage as configured in $CFG->frontpage or $CFG->frontpageloggedin
     *
     * @return string
     */
    public function frontpage() {
        global $CFG, $SITE;

        $output = '';

        if (isloggedin() and !isguestuser() and isset($CFG->frontpageloggedin)) {
            $frontpagelayout = $CFG->frontpageloggedin;
        } else {
            $frontpagelayout = $CFG->frontpage;
        }

        foreach (explode(',', $frontpagelayout) as $v) {
            switch ($v) {
                // Display the main part of the front page.
                case FRONTPAGENEWS:
                    if ($SITE->newsitems) {
                        // Print forums only when needed.
                        require_once($CFG->dirroot .'/mod/forum/lib.php');
                        if (($newsforum = forum_get_course_forum($SITE->id, 'news')) &&
                                ($forumcontents = $this->frontpage_news($newsforum))) {
                            $newsforumcm = get_fast_modinfo($SITE)->instances['forum'][$newsforum->id];
                            $output .= $this->frontpage_part('skipsitenews', 'site-news-forum',
                                $newsforumcm->get_formatted_name(), $forumcontents);
                        }
                    }
                    break;

                case FRONTPAGEENROLLEDCOURSELIST:
                    $mycourseshtml = $this->frontpage_my_courses();
                    if (!empty($mycourseshtml)) {
                        $output .= $this->frontpage_part('skipmycourses', 'frontpage-course-list',
                            get_string('mycourses'), $mycourseshtml);
                    }
                    break;

                case FRONTPAGEALLCOURSELIST:
                    $availablecourseshtml = $this->frontpage_available_courses();
                    $output .= $this->frontpage_part('skipavailablecourses', 'frontpage-available-course-list',
                        get_string('availablecourses'), $availablecourseshtml);
                    break;

                case FRONTPAGECATEGORYNAMES:
                    $output .= $this->frontpage_part('skipcategories', 'frontpage-category-names',
                        get_string('categories'), $this->frontpage_categories_list());
                    break;

                case FRONTPAGECATEGORYCOMBO:
                    $output .= $this->frontpage_part('skipcourses', 'frontpage-category-combo',
                        get_string('courses'), $this->frontpage_combo_list());
                    break;

                case FRONTPAGECOURSESEARCH:
                    $output .= $this->box($this->course_search_form(''), 'd-flex justify-content-center');
                    break;

            }
            $output .= '<br />';
        }

        return $output;
    }
}

/**
 * Class storing display options and functions to help display course category and/or courses lists
 *
 * This is a wrapper for core_course_category objects that also stores display options
 * and functions to retrieve sorted and paginated lists of categories/courses.
 *
 * If theme overrides methods in core_course_renderers that access this class
 * it may as well not use this class at all or extend it.
 *
 * @package   core
 * @copyright 2013 Marina Glancy
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class coursecat_helper {
    /** @var string [none, collapsed, expanded] how (if) display courses list */
    protected $showcourses = 10; /* core_course_renderer::COURSECAT_SHOW_COURSES_COLLAPSED */
    /** @var int depth to expand subcategories in the tree (deeper subcategories will be loaded by AJAX or proceed to category page by clicking on category name) */
    protected $subcatdepth = 1;
    /** @var array options to display courses list */
    protected $coursesdisplayoptions = array();
    /** @var array options to display subcategories list */
    protected $categoriesdisplayoptions = array();
    /** @var array additional HTML attributes */
    protected $attributes = array();
    /** @var array search criteria if the list is a search result */
    protected $searchcriteria = null;

    /**
     * Sets how (if) to show the courses - none, collapsed, expanded, etc.
     *
     * @param int $showcourses SHOW_COURSES_NONE, SHOW_COURSES_COLLAPSED, SHOW_COURSES_EXPANDED, etc.
     * @return coursecat_helper
     */
    public function set_show_courses($showcourses) {
        $this->showcourses = $showcourses;
        // Automatically set the options to preload summary and coursecontacts for core_course_category::get_courses()
        // and core_course_category::search_courses().
        $this->coursesdisplayoptions['summary'] = $showcourses >= core_course_renderer::COURSECAT_SHOW_COURSES_AUTO;
        $this->coursesdisplayoptions['coursecontacts'] = $showcourses >= core_course_renderer::COURSECAT_SHOW_COURSES_EXPANDED;
        $this->coursesdisplayoptions['customfields'] = $showcourses >= core_course_renderer::COURSECAT_SHOW_COURSES_COLLAPSED;
        return $this;
    }

    /**
     * Returns how (if) to show the courses - none, collapsed, expanded, etc.
     *
     * @return int - COURSECAT_SHOW_COURSES_NONE, COURSECAT_SHOW_COURSES_COLLAPSED, COURSECAT_SHOW_COURSES_EXPANDED, etc.
     */
    public function get_show_courses() {
        return $this->showcourses;
    }

    /**
     * Sets the maximum depth to expand subcategories in the tree
     *
     * deeper subcategories may be loaded by AJAX or proceed to category page by clicking on category name
     *
     * @param int $subcatdepth
     * @return coursecat_helper
     */
    public function set_subcat_depth($subcatdepth) {
        $this->subcatdepth = $subcatdepth;
        return $this;
    }

    /**
     * Returns the maximum depth to expand subcategories in the tree
     *
     * deeper subcategories may be loaded by AJAX or proceed to category page by clicking on category name
     *
     * @return int
     */
    public function get_subcat_depth() {
        return $this->subcatdepth;
    }

    /**
     * Sets options to display list of courses
     *
     * Options are later submitted as argument to core_course_category::get_courses() and/or core_course_category::search_courses()
     *
     * Options that core_course_category::get_courses() accept:
     *    - recursive - return courses from subcategories as well. Use with care,
     *      this may be a huge list!
     *    - summary - preloads fields 'summary' and 'summaryformat'
     *    - coursecontacts - preloads course contacts
     *    - customfields - preloads custom fields data
     *    - isenrolled - preloads indication whether this user is enrolled in the course
     *    - sort - list of fields to sort. Example
     *             array('idnumber' => 1, 'shortname' => 1, 'id' => -1)
     *             will sort by idnumber asc, shortname asc and id desc.
     *             Default: array('sortorder' => 1)
     *             Only cached fields may be used for sorting!
     *    - offset
     *    - limit - maximum number of children to return, 0 or null for no limit
     *
     * Options summary and coursecontacts are filled automatically in the set_show_courses()
     *
     * Also renderer can set here any additional options it wants to pass between renderer functions.
     *
     * @param array $options
     * @return coursecat_helper
     */
    public function set_courses_display_options($options) {
        $this->coursesdisplayoptions = $options;
        $this->set_show_courses($this->showcourses); // this will calculate special display options
        return $this;
    }

    /**
     * Sets one option to display list of courses
     *
     * @see coursecat_helper::set_courses_display_options()
     *
     * @param string $key
     * @param mixed $value
     * @return coursecat_helper
     */
    public function set_courses_display_option($key, $value) {
        $this->coursesdisplayoptions[$key] = $value;
        return $this;
    }

    /**
     * Return the specified option to display list of courses
     *
     * @param string $optionname option name
     * @param mixed $defaultvalue default value for option if it is not specified
     * @return mixed
     */
    public function get_courses_display_option($optionname, $defaultvalue = null) {
        if (array_key_exists($optionname, $this->coursesdisplayoptions)) {
            return $this->coursesdisplayoptions[$optionname];
        } else {
            return $defaultvalue;
        }
    }

    /**
     * Returns all options to display the courses
     *
     * This array is usually passed to {@link core_course_category::get_courses()} or
     * {@link core_course_category::search_courses()}
     *
     * @return array
     */
    public function get_courses_display_options() {
        return $this->coursesdisplayoptions;
    }

    /**
     * Sets options to display list of subcategories
     *
     * Options 'sort', 'offset' and 'limit' are passed to core_course_category::get_children().
     * Any other options may be used by renderer functions
     *
     * @param array $options
     * @return coursecat_helper
     */
    public function set_categories_display_options($options) {
        $this->categoriesdisplayoptions = $options;
        return $this;
    }

    /**
     * Return the specified option to display list of subcategories
     *
     * @param string $optionname option name
     * @param mixed $defaultvalue default value for option if it is not specified
     * @return mixed
     */
    public function get_categories_display_option($optionname, $defaultvalue = null) {
        if (array_key_exists($optionname, $this->categoriesdisplayoptions)) {
            return $this->categoriesdisplayoptions[$optionname];
        } else {
            return $defaultvalue;
        }
    }

    /**
     * Returns all options to display list of subcategories
     *
     * This array is usually passed to {@link core_course_category::get_children()}
     *
     * @return array
     */
    public function get_categories_display_options() {
        return $this->categoriesdisplayoptions;
    }

    /**
     * Sets additional general options to pass between renderer functions, usually HTML attributes
     *
     * @param array $attributes
     * @return coursecat_helper
     */
    public function set_attributes($attributes) {
        $this->attributes = $attributes;
        return $this;
    }

    /**
     * Return all attributes and erases them so they are not applied again
     *
     * @param string $classname adds additional class name to the beginning of $attributes['class']
     * @return array
     */
    public function get_and_erase_attributes($classname) {
        $attributes = $this->attributes;
        $this->attributes = array();
        if (empty($attributes['class'])) {
            $attributes['class'] = '';
        }
        $attributes['class'] = $classname . ' '. $attributes['class'];
        return $attributes;
    }

    /**
     * Sets the search criteria if the course is a search result
     *
     * Search string will be used to highlight terms in course name and description
     *
     * @param array $searchcriteria
     * @return coursecat_helper
     */
    public function set_search_criteria($searchcriteria) {
        $this->searchcriteria = $searchcriteria;
        return $this;
    }

    /**
     * Returns formatted and filtered description of the given category
     *
     * @param core_course_category $coursecat category
     * @param stdClass|array $options format options, by default [noclean,overflowdiv],
     *     if context is not specified it will be added automatically
     * @return string|null
     */
    public function get_category_formatted_description($coursecat, $options = null) {
        if ($coursecat->id && $coursecat->is_uservisible() && !empty($coursecat->description)) {
            if (!isset($coursecat->descriptionformat)) {
                $descriptionformat = FORMAT_MOODLE;
            } else {
                $descriptionformat = $coursecat->descriptionformat;
            }
            if ($options === null) {
                $options = array('noclean' => true, 'overflowdiv' => true);
            } else {
                $options = (array)$options;
            }
            $context = context_coursecat::instance($coursecat->id);
            if (!isset($options['context'])) {
                $options['context'] = $context;
            }
            $text = file_rewrite_pluginfile_urls($coursecat->description,
                    'pluginfile.php', $context->id, 'coursecat', 'description', null);
            return format_text($text, $descriptionformat, $options);
        }
        return null;
    }

    /**
     * Returns given course's summary with proper embedded files urls and formatted
     *
     * @param core_course_list_element $course
     * @param array|stdClass $options additional formatting options
     * @return string
     */
    public function get_course_formatted_summary($course, $options = array()) {
        global $CFG;
        require_once($CFG->libdir. '/filelib.php');
        if (!$course->has_summary()) {
            return '';
        }
        $options = (array)$options;
        $context = context_course::instance($course->id);
        if (!isset($options['context'])) {
            $options['context'] = $context;
        }
        $summary = file_rewrite_pluginfile_urls($course->summary, 'pluginfile.php', $context->id, 'course', 'summary', null);
        $summary = format_text($summary, $course->summaryformat, $options);
        if (!empty($this->searchcriteria['search'])) {
            $summary = highlight($this->searchcriteria['search'], $summary);
        }
        return $summary;
    }

    /**
     * Returns course name as it is configured to appear in courses lists formatted to course context
     *
     * @param core_course_list_element $course
     * @param array|stdClass $options additional formatting options
     * @return string
     */
    public function get_course_formatted_name($course, $options = array()) {
        $options = (array)$options;
        if (!isset($options['context'])) {
            $options['context'] = context_course::instance($course->id);
        }
        $name = format_string(get_course_display_name_for_list($course), true, $options);
        if (!empty($this->searchcriteria['search'])) {
            $name = highlight($this->searchcriteria['search'], $name);
        }
        return $name;
    }
}

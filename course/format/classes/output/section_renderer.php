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

namespace core_courseformat\output;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/renderer.php');

use cm_info;
use coding_exception;
use core_course_renderer;
use core_courseformat\base as course_format;
use html_writer;
use renderable;
use section_info;
use stdClass;

/**
 * Contains the default section course format output class.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class section_renderer extends core_course_renderer {

    /**
     * Renders the provided widget and returns the HTML to display it.
     *
     * Course format templates uses a similar subfolder structure to the renderable classes.
     * This method find out the specific template for a course widget. That's the reason why
     * this render method is different from the normal plugin renderer one.
     *
     * course format templatables can be rendered using the core_course/local/* templates.
     * Format plugins are free to override the default template location using render_xxx methods as usual.
     *
     * @param renderable $widget instance with renderable interface
     * @return string the widget HTML
     */
    public function render(renderable $widget) {
        global $CFG;
        $fullpath = str_replace('\\', '/', get_class($widget));
        $classparts = explode('/', $fullpath);
        // Strip namespaces.
        $classname = array_pop($classparts);
        // Remove _renderable suffixes.
        $classname = preg_replace('/_renderable$/', '', $classname);

        $rendermethod = 'render_' . $classname;
        if (method_exists($this, $rendermethod)) {
            return $this->$rendermethod($widget);
        }

        // If nothing works, let the parent class decide.
        return parent::render($widget);
    }

    /**
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course) {
        $title = get_section_name($course, $section);
        $url = course_get_url($course, $section->section, array('navigation' => true));
        if ($url) {
            $title = html_writer::link($url, $title);
        }
        return $title;
    }

    /**
     * Generate the section title to be displayed on the section page, without a link
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        return get_section_name($course, $section);
    }

    /**
     * Get the updated rendered version of a cm list item.
     *
     * This method is used when an activity is duplicated or copied in on the client side without refreshing the page.
     * Note that the previous method is used every time an activity is rendered, independent of it is the initial page
     * loading or an Ajax update. In this case, course_section_updated_cm_item will only be used when the course editor
     * requires to get an updated cm item HTML to perform partial page refresh. It will be used for suporting the course
     * editor webservices.
     *
     * By default, the template used for update a cm_item is the same as when it renders initially, but format plugins are
     * free to override this methos to provide extra affects or so.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @param cm_info $cm the course module ionfo
     * @param array $displayoptions optional extra display options
     * @return string the rendered element
     */
    public function course_section_updated_cm_item(
        course_format $format,
        section_info $section,
        cm_info $cm,
        array $displayoptions = []
    ) {

        $cmitemclass = $format->get_output_classname('content\\section\\cmitem');
        $cmitem = new $cmitemclass($format, $section, $cm, $displayoptions);
        return $this->render($cmitem);
    }

    /**
     * Get the updated rendered version of a section.
     *
     * This method will only be used when the course editor requires to get an updated cm item HTML
     * to perform partial page refresh. It will be used for supporting the course editor webservices.
     *
     * By default, the template used for update a section is the same as when it renders initially,
     * but format plugins are free to override this method to provide extra effects or so.
     *
     * @param course_format $format the course format
     * @param section_info $section the section info
     * @return string the rendered element
     */
    public function course_section_updated(
        course_format $format,
        section_info $section
    ): string {
        $sectionclass = $format->get_output_classname('content\\section');
        $output = new $sectionclass($format, $section);
        return $this->render($output);
    }

    /**
     * Get the course index drawer with placeholder.
     *
     * The default course index is loaded after the page is ready. Format plugins can override
     * this method to provide an alternative course index.
     *
     * If the format is not compatible with the course index, this method will return an empty string.
     *
     * @param course_format $format the course format
     * @return String the course index HTML.
     */
    public function course_index_drawer(course_format $format): ?String {
        if ($format->uses_course_index()) {
            include_course_editor($format);
            return $this->render_from_template('core_courseformat/local/courseindex/drawer', []);
        }
        return '';
    }

    /**
     * Render the enable bulk editing button.
     * @param course_format $format the course format
     * @return string|null the enable bulk button HTML (or null if no bulk available).
     */
    public function bulk_editing_button(course_format $format): ?string {
        if (!$format->show_editor() || !$format->supports_components()) {
            return null;
        }
        $widgetclass = $format->get_output_classname('content\\bulkedittoggler');
        $widget = new $widgetclass($format);
        return $this->render($widget);
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function section_edit_control_menu($controls, $course, $section) {

        throw new coding_exception('section_edit_control_menu() can not be used anymore. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render a section. In case you need to modify those controls ' .
            'override core_courseformat\\output\\local\\content\\section\\controlmenu in your format plugin.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function section_right_content($section, $course, $onsectionpage) {

        throw new coding_exception('section_right_content() can not be used anymore. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render a section.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function section_left_content($section, $course, $onsectionpage) {

        throw new coding_exception('section_left_content() can not be used anymore. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render a section.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn = null) {

        throw new coding_exception('section_header() can not be used any more. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render a section ' .
            'or core_courseformat\output\\local\\content\\section\\header ' .
            'to print only the header.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function section_footer() {

        throw new coding_exception('section_footer() can not be used any more. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render individual sections or .' .
            'core_courseformat\\output\\local\\content to render the full course');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function start_section_list() {

        throw new coding_exception('start_section_list() can not be used any more. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render individual sections or .' .
            'core_courseformat\\output\\local\\content to render the full course');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.y
     */
    protected function end_section_list() {

        throw new coding_exception('end_section_list() can not be used any more. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render individual sections or .' .
            'core_courseformat\\output\\local\\content to render the full course');
    }

    /**
     * Old method to print section edit controls. Do not use it!
     *
     * @deprecated since Moodle 3.0 MDL-48947 - Use core_courseformat\output\section_renderer::section_edit_control_items() instead
     */
    protected function section_edit_controls() {
        throw new coding_exception('section_edit_controls() can not be used anymore. Please use ' .
            'section_edit_control_items() instead.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function section_edit_control_items($course, $section, $onsectionpage = false) {

        throw new coding_exception('section_edit_control_items() can not be used any more. Please use or extend' .
            'core_courseformat\output\\local\\content\\section\\controlmenu instead (like topics format does).');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function section_summary($section, $course, $mods) {

        throw new coding_exception('section_summary() can not be used any more. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections. If you need to modify those summary, extend ' .
            'core_courseformat\output\\local\\content\\section\\summary in your format plugin.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function section_activity_summary($section, $course, $mods) {

        throw new coding_exception('section_activity_summary() can not be used any more. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections. ' .
            'If you need to modify those information, extend ' .
            'core_courseformat\output\\local\\content\\section\\cmsummary in your format plugin.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function section_availability_message($section, $canviewhidden) {

        throw new coding_exception('section_availability_message() can not be used any more. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections. If you need to modify this element, extend ' .
            'core_courseformat\output\\local\\content\\section\\availability in your format plugin.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    public function section_availability($section) {
        throw new coding_exception('section_availability() can not be used any more. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections. If you need to modify this element, extend ' .
            'core_courseformat\output\\local\\content\\section\\availability in your format plugin.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function course_activity_clipboard($course, $sectionno = null) {
        throw new coding_exception('Non ajax course edition using course_activity_clipboard is not supported anymore.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function get_nav_links($course, $sections, $sectionno) {

        throw new coding_exception('get_nav_links() can not be used any more. Please use ' .
            'core_courseformat\\output\\local\\content to render a course. If you need to modify this element, extend ' .
            'core_courseformat\\output\\local\\content\\sectionnavigation in your format plugin.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function stealth_section_header($sectionno) {

        throw new coding_exception('stealth_section_header() can not be used any more. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections.');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function stealth_section_footer() {

        throw new coding_exception('stealth_section_footer() can not be used any more. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections.');
    }

    /**
     * Generate the html for a hidden section
     *
     * @param int $sectionno The section number in the course which is being displayed
     * @param int|stdClass $courseorid The course to get the section name for (object or just course id)
     * @return string HTML to output.
     */
    protected function section_hidden($sectionno, $courseorid = null) {
        if ($courseorid) {
            $sectionname = get_section_name($courseorid, $sectionno);
            $strnotavailable = get_string('notavailablecourse', '', $sectionname);
        } else {
            $strnotavailable = get_string('notavailable');
        }

        $o = '';
        $o .= html_writer::start_tag('li', [
            'id' => 'section-' . $sectionno,
            'class' => 'section main clearfix hidden',
            'data-sectionid' => $sectionno
        ]);
        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $o .= html_writer::tag('div', '', array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));
        $o .= html_writer::tag('div', $strnotavailable);
        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');
        return $o;
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function section_nav_selection($course, $sections, $displaysection) {

        throw new coding_exception('section_nav_selection() can not be used anymore. Please use ' .
            'core_courseformat\\output\\local\\content to render a course. If you need to modify this element, extend ' .
            'core_courseformat\\output\\local\\content\\sectionnavigation or ' .
            'core_courseformat\\output\\local\\content\\sectionselector in your format plugin.');
    }

    /**
     * @deprecated since 4.0
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {

        throw new coding_exception('Method print_single_section_page can not be used anymore. Please use' .
            'core_courseformat\\output\\local\\content instead ' .
            'or override render_content method to use a different template');
    }

    /**
     * @deprecated since 4.0
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {

        throw new coding_exception('Method print_multiple_section_page can not be used anymore. Please use' .
            'core_courseformat\\output\\local\\content instead ' .
            'or override render_content method to use a diferent template');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function change_number_sections($course, $sectionreturn = null) {

        throw new coding_exception('Method change_number_sections can not be used anymore. Please use' .
            'core_courseformat\\output\\local\\content\\addsection instead');
    }

    /**
     * @deprecated since 4.0 - use core_course output components instead.
     */
    protected function format_summary_text($section) {

        throw new coding_exception('Method format_summary_text can not be used anymore. Please use' .
            'core_courseformat\output\\local\\content\\section\\summary::format_summary_text instead');
    }
}

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
 * Contains the default section course format output class.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once($CFG->dirroot . '/course/renderer.php');

use action_menu;
use action_menu_link_secondary;
use cm_info;
use coding_exception;
use context_course;
use core_course_renderer;
use core_courseformat\base as course_format;
use html_writer;
use moodle_page;
use moodle_url;
use pix_icon;
use renderable;
use section_info;
use templatable;
use url_select;

/**
 * Base class to render a course add section buttons.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class section_renderer extends core_course_renderer {

    /**
     * @var core_course_renderer contains an instance of core course renderer
     * @deprecated since 4.0 - use $this to access course renderer methods
     */
    protected $courserenderer;

    /**
     * Constructor method, calls the parent constructor.
     *
     * @deprecated since 4.0
     *
     * Note: this method exists only for compatibilitiy with legacy course formats. Legacy formats
     * depends on $this->courserenderer to access the course renderer methods. Since Moodle 4.0
     * core_courseformat\output\section_renderer extends core_course_renderer and all metdhos can be used directly from $this.
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct($page, $target);
        $this->courserenderer = $this->page->get_renderer('core', 'course');
    }

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
     * It replaces the course renderer course_section_cm_list_item method but it's scope is different.
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
     * Generate the edit control action menu
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * The section edit controls are now part of the main core_courseformat\output\local\content\section output
     * and does not use renderer methods anymore.
     *
     * @param array $controls The edit control items from section_edit_control_items
     * @param stdClass $course The course entry from DB (not used)
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function section_edit_control_menu($controls, $course, $section) {
        debugging('section_edit_control_menu() can not be used anymore. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render a section. In case you need to modify those controls ' .
            'override core_courseformat\\output\\local\\content\\section\\controlmenu in your format plugin.', DEBUG_DEVELOPER);

        $o = "";
        if (!empty($controls)) {
            $menu = new action_menu();
            $menu->set_menu_trigger(get_string('edit'));
            $menu->attributes['class'] .= ' section-actions';
            foreach ($controls as $value) {
                $url = empty($value['url']) ? '' : $value['url'];
                $icon = empty($value['icon']) ? '' : $value['icon'];
                $name = empty($value['name']) ? '' : $value['name'];
                $attr = empty($value['attr']) ? array() : $value['attr'];
                $class = empty($value['pixattr']['class']) ? '' : $value['pixattr']['class'];
                $al = new action_menu_link_secondary(
                    new moodle_url($url),
                    new pix_icon($icon, '', null, array('class' => "smallicon " . $class)),
                    $name,
                    $attr
                );
                $menu->add($al);
            }

            $o .= html_writer::div(
                $this->render($menu),
                'section_action_menu',
                array('data-sectionid' => $section->id)
            );
        }

        return $o;
    }

    /**
     * Generate the content to displayed on the right part of a section
     * before course modules are included
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * Spatial references like "left" or "right" are limiting the way formats and themes can
     * extend courses. The elements from this method are now included in the
     * core_courseformat\output\local\content\section output components.
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_right_content($section, $course, $onsectionpage) {

        debugging('section_right_content() can not be used anymore. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render a section.', DEBUG_DEVELOPER);

        $o = $this->output->spacer();

        $controls = $this->section_edit_control_items($course, $section, $onsectionpage);
        $o .= $this->section_edit_control_menu($controls, $course, $section);

        return $o;
    }

    /**
     * Generate the content to displayed on the left part of a section
     * before course modules are included
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * Spatial references like "left" or "right" are limiting the way formats and themes can
     * extend courses. The elements from this method are now included in the
     * core_courseformat\output\local\content\section output components.
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return string HTML to output.
     */
    protected function section_left_content($section, $course, $onsectionpage) {

        debugging('section_left_content() can not be used anymore. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render a section.', DEBUG_DEVELOPER);

        $o = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (course_get_format($course)->is_section_current($section)) {
                $o = get_accesshide(get_string('currentsection', 'format_' . $course->format));
            }
        }

        return $o;
    }

    /**
     * Generate the display of the header part of a section before
     * course modules are included
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a core_courseformat\output\content\section output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param bool $onsectionpage true if being printed on a single-section page
     * @param int $sectionreturn The section to return to after an action
     * @return string HTML to output.
     */
    protected function section_header($section, $course, $onsectionpage, $sectionreturn = null) {
        debugging('section_header() is deprecated. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render a section ' .
            'or core_courseformat\output\\local\\content\\section\\header ' .
            'to print only the header.', DEBUG_DEVELOPER);

        $o = '';
        $sectionstyle = '';

        if ($section->section != 0) {
            // Only in the non-general sections.
            if (!$section->visible) {
                $sectionstyle = ' hidden';
            }
            if (course_get_format($course)->is_section_current($section)) {
                $sectionstyle = ' current';
            }
        }

        $o .= html_writer::start_tag('li', [
            'id' => 'section-' . $section->section,
            'class' => 'section main clearfix' . $sectionstyle,
            'role' => 'region',
            'aria-labelledby' => "sectionid-{$section->id}-title",
            'data-sectionid' => $section->section,
            'data-sectionreturnid' => $sectionreturn
        ]);

        $leftcontent = $this->section_left_content($section, $course, $onsectionpage);
        $o .= html_writer::tag('div', $leftcontent, array('class' => 'left side'));

        $rightcontent = $this->section_right_content($section, $course, $onsectionpage);
        $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        // When not on a section page, we display the section titles except the general section if null.
        $hasnamenotsecpg = (!$onsectionpage && ($section->section != 0 || !is_null($section->name)));

        // When on a section page, we only display the general section title, if title is not the default one.
        $hasnamesecpg = ($onsectionpage && ($section->section == 0 && !is_null($section->name)));

        $classes = ' accesshide';
        if ($hasnamenotsecpg || $hasnamesecpg) {
            $classes = '';
        }
        $sectionname = html_writer::tag('span', $this->section_title($section, $course));
        $o .= $this->output->heading($sectionname, 3, 'sectionname' . $classes, "sectionid-{$section->id}-title");

        $o .= $this->section_availability($section);

        $o .= html_writer::start_tag('div', array('class' => 'summary'));
        if ($section->uservisible || $section->visible) {
            // Show summary if section is available or has availability restriction information.
            // Do not show summary if section is hidden but we still display it because of course setting
            // "Hidden sections are shown as not available".
            $o .= $this->format_summary_text($section);
        }
        $o .= html_writer::end_tag('div');

        return $o;
    }

    /**
     * Generate the display of the footer part of a section.
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is integrated into core_courseformat\output\local\content\section output component and it is
     * displayed using mustache templates instead of a renderer method.
     *
     * @return string HTML to output.
     */
    protected function section_footer() {

        debugging('section_footer() is deprecated. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render individual sections or .' .
            'core_courseformat\\output\\local\\content to render the full course', DEBUG_DEVELOPER);

        $o = html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');
        return $o;
    }

    /**
     * Generate the starting container html for a list of sections.
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * @return string HTML to output.
     */
    protected function start_section_list() {
        debugging('start_section_list() is deprecated. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render individual sections or .' .
            'core_courseformat\\output\\local\\content to render the full course', DEBUG_DEVELOPER);
        return html_writer::start_tag('ul', ['class' => 'sections']);
    }

    /**
     * Generate the closing container html for a list of sections.
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.y
     *
     * @return string HTML to output.
     */
    protected function end_section_list() {
        debugging('end_section_list() is deprecated. Please use ' .
            'core_courseformat\\output\\local\\content\\section to render individual sections or .' .
            'core_courseformat\\output\\local\\content to render the full course', DEBUG_DEVELOPER);
        return html_writer::end_tag('ul');
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
     * Generate the edit control items of a section
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a core_courseformat\output\content\section output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of edit control items
     */
    protected function section_edit_control_items($course, $section, $onsectionpage = false) {
        debugging('section_edit_control_items() is deprecated, please use or extend' .
            'core_courseformat\output\\local\\content\\section\\controlmenu instead (like topics format does).', DEBUG_DEVELOPER);

        $format = course_get_format($course);
        $modinfo = $format->get_modinfo();

        if ($onsectionpage) {
            $format->set_section_number($section->section);
        }

        // We need a section_info object, not a record.
        $section = $modinfo->get_section_info($section->section);

        $widgetclass = $format->get_output_classname('content\\section\\controlmenu');
        $widget = new $widgetclass($format, $section);
        return $widget->section_control_items();
    }

    /**
     * Generate a summary of a section for display on the 'course index page'
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a core_courseformat\output\content\section output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_summary($section, $course, $mods) {
        debugging('section_summary() is deprecated. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections. If you need to modify those summary, extend ' .
            'core_courseformat\output\\local\\content\\section\\summary in your format plugin.', DEBUG_DEVELOPER);

        $classattr = 'section main section-summary clearfix';
        $linkclasses = '';

        // If section is hidden then display grey section link.
        if (!$section->visible) {
            $classattr .= ' hidden';
            $linkclasses .= ' dimmed_text';
        } else if (course_get_format($course)->is_section_current($section)) {
            $classattr .= ' current';
        }

        $title = get_section_name($course, $section);
        $o = '';
        $o .= html_writer::start_tag('li', [
            'id' => 'section-' . $section->section,
            'class' => $classattr,
            'role' => 'region',
            'aria-label' => $title,
            'data-sectionid' => $section->section
        ]);

        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $o .= html_writer::tag('div', '', array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));

        if ($section->uservisible) {
            $title = html_writer::tag(
                'a',
                $title,
                array('href' => course_get_url($course, $section->section), 'class' => $linkclasses)
            );
        }
        $o .= $this->output->heading($title, 3, 'section-title');

        $o .= $this->section_availability($section);
        $o .= html_writer::start_tag('div', array('class' => 'summarytext'));

        if ($section->uservisible || $section->visible) {
            // Show summary if section is available or has availability restriction information.
            // Do not show summary if section is hidden but we still display it because of course setting
            // "Hidden sections are shown as not available".
            $o .= $this->format_summary_text($section);
        }
        $o .= html_writer::end_tag('div');
        $o .= $this->section_activity_summary($section, $course, null);

        $o .= html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');

        return $o;
    }

    /**
     * Generate a summary of the activites in a section
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a core_courseformat\output\content\section output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course the course record from DB
     * @param array    $mods (argument not used)
     * @return string HTML to output.
     */
    protected function section_activity_summary($section, $course, $mods) {

        debugging('section_activity_summary() is deprecated. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections. ' .
            'If you need to modify those information, extend ' .
            'core_courseformat\output\\local\\content\\section\\cmsummary in your format plugin.', DEBUG_DEVELOPER);

        $format = course_get_format($course);
        $widgetclass = $format->get_output_classname('content\\section\\cmsummary');
        $widget = new $widgetclass($format, $section);
        $this->render($widget);
    }

    /**
     * If section is not visible, display the message about that ('Not available
     * until...', that sort of thing). Otherwise, returns blank.
     *
     * For users with the ability to view hidden sections, it shows the
     * information even though you can view the section and also may include
     * slightly fuller information (so that teachers can tell when sections
     * are going to be unavailable etc). This logic is the same as for
     * activities.
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a core_courseformat\output\content\section output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param section_info $section The course_section entry from DB
     * @param bool $canviewhidden True if user can view hidden sections
     * @return string HTML to output
     */
    protected function section_availability_message($section, $canviewhidden) {
        global $CFG;

        debugging('section_availability_message() is deprecated. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections. If you need to modify this element, extend ' .
            'core_courseformat\output\\local\\content\\section\\availability in your format plugin.', DEBUG_DEVELOPER);

        $course = $section->course;
        $format = course_get_format($course);
        $widgetclass = $format->get_output_classname('content\\section\\availability');
        $widget = new $widgetclass($format, $section);
        $this->render($widget);
    }

    /**
     * Displays availability information for the section (hidden, not available unles, etc.)
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a core_courseformat\output\content\section output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param section_info $section
     * @return string
     */
    public function section_availability($section) {
        debugging('section_availability() is deprecated. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections. If you need to modify this element, extend ' .
            'core_courseformat\output\\local\\content\\section\\availability in your format plugin.', DEBUG_DEVELOPER);

        $context = context_course::instance($section->course);
        $canviewhidden = has_capability('moodle/course:viewhiddensections', $context);
        return html_writer::div($this->section_availability_message($section, $canviewhidden), 'section_availability');
    }

    /**
     * Show if something is on on the course clipboard (moving around)
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * While the non ajax course eidtion is still supported, the old clipboard will be
     * emulated by core_courseformat\output\local\content\section\cmlist.
     *
     * @param stdClass $course The course entry from DB
     * @param int $sectionno The section number in the course which is being displayed
     * @return string HTML to output.
     */
    protected function course_activity_clipboard($course, $sectionno = null) {
        global $USER;
        debugging('Non ajax course edition using course_activity_clipboard is not supported anymore.', DEBUG_DEVELOPER);

        $o = '';
        // If currently moving a file then show the current clipboard.
        if (ismoving($course->id)) {
            $url = new moodle_url(
                '/course/mod.php',
                array(
                    'sesskey' => sesskey(),
                    'cancelcopy' => true,
                    'sr' => $sectionno,
                )
            );

            $o .= html_writer::start_tag('div', array('class' => 'clipboard'));
            $o .= strip_tags(get_string('activityclipboard', '', $USER->activitycopyname));
            $o .= ' (' . html_writer::link($url, get_string('cancel')) . ')';
            $o .= html_writer::end_tag('div');
        }

        return $o;
    }

    /**
     * Generate next/previous section links for naviation.
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a core_courseformat\output\content\section output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param int $sectionno The section number in the course which is being displayed
     * @return array associative array with previous and next section link
     */
    protected function get_nav_links($course, $sections, $sectionno) {

        debugging('get_nav_links() is deprecated. Please use ' .
            'core_courseformat\\output\\local\\content to render a course. If you need to modify this element, extend ' .
            'core_courseformat\\output\\local\\content\\sectionnavigation in your format plugin.', DEBUG_DEVELOPER);

        // FIXME: This is really evil and should by using the navigation API.
        $course = course_get_format($course)->get_course();
        $canviewhidden = has_capability('moodle/course:viewhiddensections', context_course::instance($course->id))
            or !$course->hiddensections;

        $links = array('previous' => '', 'next' => '');
        $back = $sectionno - 1;
        while ($back > 0 and empty($links['previous'])) {
            if ($canviewhidden || $sections[$back]->uservisible) {
                $params = array();
                if (!$sections[$back]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $previouslink = html_writer::tag('span', $this->output->larrow(), array('class' => 'larrow'));
                $previouslink .= get_section_name($course, $sections[$back]);
                $links['previous'] = html_writer::link(course_get_url($course, $back), $previouslink, $params);
            }
            $back--;
        }

        $forward = $sectionno + 1;
        $numsections = course_get_format($course)->get_last_section_number();
        while ($forward <= $numsections and empty($links['next'])) {
            if ($canviewhidden || $sections[$forward]->uservisible) {
                $params = array();
                if (!$sections[$forward]->visible) {
                    $params = array('class' => 'dimmed_text');
                }
                $nextlink = get_section_name($course, $sections[$forward]);
                $nextlink .= html_writer::tag('span', $this->output->rarrow(), array('class' => 'rarrow'));
                $links['next'] = html_writer::link(course_get_url($course, $forward), $nextlink, $params);
            }
            $forward++;
        }

        return $links;
    }

    /**
     * Generate the header html of a stealth section
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a core_courseformat\output\content\section output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param int $sectionno The section number in the course which is being displayed
     * @return string HTML to output.
     */
    protected function stealth_section_header($sectionno) {
        debugging('stealth_section_header() is deprecated. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections.', DEBUG_DEVELOPER);

        $o = '';
        $o .= html_writer::start_tag('li', [
            'id' => 'section-' . $sectionno,
            'class' => 'section main clearfix orphaned hidden',
            'data-sectionid' => $sectionno
        ]);
        $o .= html_writer::tag('div', '', array('class' => 'left side'));
        $course = course_get_format($this->page->course)->get_course();
        $section = course_get_format($this->page->course)->get_section($sectionno);
        $rightcontent = $this->section_right_content($section, $course, false);
        $o .= html_writer::tag('div', $rightcontent, array('class' => 'right side'));
        $o .= html_writer::start_tag('div', array('class' => 'content'));
        $o .= $this->output->heading(
            get_string('orphanedactivitiesinsectionno', '', $sectionno),
            3,
            'sectionname'
        );
        return $o;
    }

    /**
     * Generate footer html of a stealth section
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a core_courseformat\output\content\section output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @return string HTML to output.
     */
    protected function stealth_section_footer() {
        debugging('stealth_section_footer() is deprecated. Please use ' .
            'core_courseformat\output\\local\\content\\section to render sections.', DEBUG_DEVELOPER);

        $o = html_writer::end_tag('div');
        $o .= html_writer::end_tag('li');
        return $o;
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
     * Generate the html for the 'Jump to' menu on a single section page.
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * This element is now a core_courseformat\output\content\section output component and it is displayed using
     * mustache templates instead of a renderer method.
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections The course_sections entries from the DB
     * @param int $displaysection the current displayed section number.
     *
     * @return string HTML to output.
     */
    protected function section_nav_selection($course, $sections, $displaysection) {

        debugging('section_nav_selection() can not be used anymore. Please use ' .
            'core_courseformat\\output\\local\\content to render a course. If you need to modify this element, extend ' .
            'core_courseformat\\output\\local\\content\\sectionnavigation or ' .
            'core_courseformat\\output\\local\\content\\sectionselector in your format plugin.', DEBUG_DEVELOPER);

        $o = '';
        $sectionmenu = array();
        $sectionmenu[course_get_url($course)->out(false)] = get_string('maincoursepage');
        $modinfo = get_fast_modinfo($course);
        $section = 1;
        $numsections = course_get_format($course)->get_last_section_number();
        while ($section <= $numsections) {
            $thissection = $modinfo->get_section_info($section);
            if (($thissection->uservisible) && ($section != $displaysection) && ($url = course_get_url($course, $section))) {
                $sectionmenu[$url->out(false)] = get_section_name($course, $section);
            }
            $section++;
        }

        $select = new url_select($sectionmenu, '', array('' => get_string('jumpto')));
        $select->class = 'jumpmenu';
        $select->formid = 'sectionmenu';
        $o .= $this->output->render($select);

        return $o;
    }

    /**
     * Output the html for a single section page.
     *
     * @deprecated since 4.0
     *
     * This is a deprecated method and it is mantain only for compatibilitiy with legacy course formats.
     * Please, to render a single section page use:
     *
     * $format = course_get_format($course);
     * // Set the section to display.
     * $format->set_section_number($displaysection);
     * $outputclass = $format->get_output_classname('content');
     * $widget = new $outputclass($format);
     * echo $this->render($widget);
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     * @param int $displaysection The section number in the course which is being displayed
     */
    public function print_single_section_page($course, $sections, $mods, $modnames, $modnamesused, $displaysection) {

        debugging('Method print_single_section_page is deprecated, please use' .
            'core_courseformat\\output\\local\\content instead ' .
            'or override render_content method to use a different template', DEBUG_DEVELOPER);

        // Some abstract methods are not needed anymore. We simulate them in case they are not present.
        if (method_exists($this, 'start_section_list')) {
            $startlist = $this->start_section_list();
        } else {
            $startlist = html_writer::start_tag('ul', ['class' => '']);
        }
        if (method_exists($this, 'end_section_list')) {
            $endlist = $this->end_section_list();
        } else {
            $endlist = html_writer::end_tag('ul');
        }

        $format = course_get_format($course);

        // Set the section to display.
        $format->set_section_number($displaysection);

        $modinfo = $format->get_modinfo();
        $course = $format->get_course();

        // Can we view the section in question?
        if (!($sectioninfo = $modinfo->get_section_info($displaysection)) || !$sectioninfo->uservisible) {
            // This section doesn't exist or is not available for the user.
            // We actually already check this in course/view.php but just in case exit from this function as well.
            print_error(
                'unknowncoursesection',
                'error',
                course_get_url($course),
                format_string($course->fullname)
            );
        }

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, $displaysection);
        $thissection = $modinfo->get_section_info(0);
        if ($thissection->summary or !empty($modinfo->sections[0]) or $format->show_editor()) {
            echo $startlist;
            echo $this->section_header($thissection, $course, true, $displaysection);
            echo $this->course_section_cm_list($course, $thissection, $displaysection);
            echo $this->course_section_add_cm_control($course, 0, $displaysection);
            echo $this->section_footer();
            echo $endlist;
        }

        // Start single-section div.
        echo html_writer::start_tag('div', array('class' => 'single-section'));

        // The requested section page.
        $thissection = $modinfo->get_section_info($displaysection);

        // Title with section navigation links.
        $sectionnavlinks = $this->get_nav_links($course, $modinfo->get_section_info_all(), $displaysection);
        $sectiontitle = '';
        $sectiontitle .= html_writer::start_tag('div', array('class' => 'section-navigation navigationtitle'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectiontitle .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        // Title attributes.
        $classes = 'sectionname';
        if (!$thissection->visible) {
            $classes .= ' dimmed_text';
        }
        $sectionname = html_writer::tag('span', $this->section_title_without_link($thissection, $course));
        $sectiontitle .= $this->output->heading($sectionname, 3, $classes);

        $sectiontitle .= html_writer::end_tag('div');
        echo $sectiontitle;

        // Now the list of sections.
        echo $startlist;

        echo $this->section_header($thissection, $course, true, $displaysection);

        echo $this->course_section_cm_list($course, $thissection, $displaysection);
        echo $this->course_section_add_cm_control($course, $displaysection, $displaysection);
        echo $this->section_footer();
        echo $endlist;

        // Display section bottom navigation.
        $sectionbottomnav = '';
        $sectionbottomnav .= html_writer::start_tag('div', array('class' => 'section-navigation mdl-bottom'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['previous'], array('class' => 'mdl-left'));
        $sectionbottomnav .= html_writer::tag('span', $sectionnavlinks['next'], array('class' => 'mdl-right'));
        $sectionbottomnav .= html_writer::tag(
            'div',
            $this->section_nav_selection($course, $sections, $displaysection),
            array('class' => 'mdl-align')
        );
        $sectionbottomnav .= html_writer::end_tag('div');
        echo $sectionbottomnav;

        // Close single-section div.
        echo html_writer::end_tag('div');
    }

    /**
     * Output the html for a multiple section page
     *
     * @deprecated since 4.0
     *
     * This is a deprecated method and it is mantain only for compatibilitiy with legacy course formats.
     * Please, to render a single section page use:
     *
     * $format = course_get_format($course);
     * $outputclass = $format->get_output_classname('content');
     * $widget = new $outputclass($format);
     * echo $this->render($widget);
     *
     * @param stdClass $course The course entry from DB
     * @param array $sections (argument not used)
     * @param array $mods (argument not used)
     * @param array $modnames (argument not used)
     * @param array $modnamesused (argument not used)
     */
    public function print_multiple_section_page($course, $sections, $mods, $modnames, $modnamesused) {

        debugging('Method print_multiple_section_page is deprecated, please use' .
            'core_courseformat\\output\\local\\content instead ' .
            'or override render_content method to use a diferent template', DEBUG_DEVELOPER);

        // Some abstract methods are not needed anymore. We simulate them in case they are not present.
        if (method_exists($this, 'start_section_list')) {
            $startlist = $this->start_section_list();
        } else {
            $startlist = html_writer::start_tag('ul', ['class' => '']);
        }
        if (method_exists($this, 'end_section_list')) {
            $endlist = $this->end_section_list();
        } else {
            $endlist = html_writer::end_tag('ul');
        }
        if (method_exists($this, 'page_title')) {
            $pagetitle = $this->page_title();
        } else {
            $pagetitle = '';
        }

        $format = course_get_format($course);

        $modinfo = $format->get_modinfo();
        $course = $format->get_course();

        $context = context_course::instance($course->id);
        echo $this->output->heading($pagetitle, 2, 'accesshide');

        // Copy activity clipboard..
        echo $this->course_activity_clipboard($course, 0);

        // Now the list of sections..
        echo $startlist;
        $numsections = course_get_format($course)->get_last_section_number();

        foreach ($modinfo->get_section_info_all() as $section => $thissection) {
            if ($section == 0) {
                // 0-section is displayed a little different then the others
                if ($thissection->summary or !empty($modinfo->sections[0]) or $format->show_editor()) {
                    echo $this->section_header($thissection, $course, false, 0);
                    echo $this->course_section_cm_list($course, $thissection, 0);
                    echo $this->course_section_add_cm_control($course, 0, 0);
                    echo $this->section_footer();
                }
                continue;
            }
            if ($section > $numsections) {
                // Activities inside this section are 'orphaned', this section will be printed as 'stealth' below.
                continue;
            }

            if (!$format->is_section_visible($thissection)) {
                continue;
            }

            if (!$format->show_editor() && $format->get_course_display() == COURSE_DISPLAY_MULTIPAGE) {
                // Display section summary only.
                echo $this->section_summary($thissection, $course, null);
            } else {
                echo $this->section_header($thissection, $course, false, 0);
                if ($thissection->uservisible) {
                    echo $this->course_section_cm_list($course, $thissection, 0);
                    echo $this->course_section_add_cm_control($course, $section, 0);
                }
                echo $this->section_footer();
            }
        }

        if ($format->show_editor()) {
            // Print stealth sections if present.
            foreach ($modinfo->get_section_info_all() as $section => $thissection) {
                if ($section <= $numsections or empty($modinfo->sections[$section])) {
                    // This is not stealth section or it is empty.
                    continue;
                }
                echo $this->stealth_section_header($section);
                echo $this->course_section_cm_list($course, $thissection, 0);
                echo $this->stealth_section_footer();
            }

            echo $endlist;

            echo $this->change_number_sections($course, 0);
        } else {
            echo $endlist;
        }
    }

    /**
     * Returns controls in the bottom of the page to increase/decrease number of sections
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * @param stdClass $course
     * @param int|null $sectionreturn
     */
    protected function change_number_sections($course, $sectionreturn = null) {
        debugging('Method change_number_sections is deprecated, please use' .
            'core_courseformat\\output\\local\\content\\addsection instead', DEBUG_DEVELOPER);

        $format = course_get_format($course);
        if ($sectionreturn) {
            $format->set_section_number($sectionreturn);
        }
        $outputclass = $format->get_output_classname('content\\addsection');
        $widget = new $outputclass($format);
        echo $this->render($widget);
    }

    /**
     * Generate html for a section summary text
     *
     * @deprecated since 4.0 MDL-72656 - use core_course output components instead.
     *
     * @param stdClass $section The course_section entry from DB
     * @return string HTML to output.
     */
    protected function format_summary_text($section) {
        debugging('Method format_summary_text is deprecated, please use' .
            'core_courseformat\output\\local\\content\\section\\summary::format_summary_text instead', DEBUG_DEVELOPER);

        $format = course_get_format($section->course);
        if (!($section instanceof section_info)) {
            $modinfo = $format->get_modinfo();
            $section = $modinfo->get_section_info($section->section);
        }
        $summaryclass = $format->get_output_classname('content\\section\\summary');
        $summary = new $summaryclass($format, $section);
        return $summary->format_summary_text();
    }
}

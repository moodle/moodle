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
 * Renderer for outputting the Soft Course format.
 *
 * @package format_softcourse
 * @copyright 2021 Pimenko <contact@pimneko.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since Moodle 4.0
 */

namespace format_softcourse\output;

use core_courseformat\output\section_renderer;
use html_writer;
use moodle_page;
use context_course;
use stdClass;

/**
 * Basic renderer for softcourse format.
 *
 * @copyright 2021 Pimenko <contact@pimneko.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class renderer extends section_renderer {

    /**
     * @var stdClass Course
     */
    private $course;

    /**
     * @var stdClass Course format
     */
    private $courseformat = null;

    /**
     * @var stdClass Mod info
     */
    private $modinfo = null;

    /**
     * Constructor method, calls the parent constructor
     *
     * @param moodle_page $page
     * @param string $target one of rendering target constants
     */
    public function __construct(moodle_page $page, $target) {
        parent::__construct(
            $page,
            $target,
        );
        $this->course = $page->course;
        $this->courseformat = course_get_format($this->course);
        $this->modinfo = get_fast_modinfo($this->course);

        // Since format_softcourse_renderer::section_edit_controls() only displays
        // the 'Set current section' control when editing mode is on
        // we need to be sure that the link 'Turn editing mode on' is available for a user
        // who does not have any other managing capability.
        $page->set_other_editing_capability('moodle/course:setcurrentsection');
    }

    /**
     * Generate the section title, wraps it in a link to the section page if page is to be displayed on a separate page
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title($section, $course) {
        return $this->render(course_get_format($course)->inplace_editable_render_section_name($section));
    }

    /**
     * Generate the section title to be displayed on the section page, without a link
     *
     * @param stdClass $section The course_section entry from DB
     * @param stdClass $course The course entry from DB
     * @return string HTML to output.
     */
    public function section_title_without_link($section, $course) {
        return $this->render(
            course_get_format($course)->inplace_editable_render_section_name(
                $section,
                false,
            ),
        );
    }

    /**
     * Renders the content of a widget based on user editing capabilities and course format options.
     *
     * @param mixed $widget The widget to render content for.
     * @return string The rendered content based on the widget and user capabilities.
     */
    public function render_content($widget) {

        $context = context_course::instance($this->course->id);
        $data = $widget->export_for_template($this);

        if ($this->page->user_is_editing() && has_capability(
                'moodle/course:update',
                $context,
            )) {
            // Base template.
            return $this->render_from_template(
                'core_courseformat/local/content',
                $data,
            );
        } else {
            // Our template.
            // Get course summary.
            $options = new stdClass();
            $options->noclean = true;
            $options->overflowdiv = true;
            $introduction = $this->courseformat->get_format_options()['introduction'];
            $data->courseintroduction = format_text(
                $introduction,
                1,
                $options,
            );

            if ($data->initialsection) {
                $data->start_url = $data->initialsection->start_url;
            } else {
                $data->start_url = null;
            }

            if ($this->courseformat->get_format_options()['hideallsections'] == 1) {

                if (!$data->start_url) {
                    foreach ($data->sections as $section) {
                        if ($section->skip != true && $section->start_url != null) {
                            $data->start_url = $section->start_url;
                            break;
                        }
                    }
                }
                $data->sections = false;

            } else {
                if (!$data->start_url) {
                    foreach ($data->sections as $section) {
                        if ($section->skip != true && $section->start_url != null) {
                            $data->start_url = $section->start_url;
                            break;
                        }
                    }
                }
            }

            return $this->render_from_template(
                'format_softcourse/content',
                $data,
            );
        }
    }

    /**
     * Generate the starting container html for a list of sections
     *
     * @return string HTML to output.
     */
    public function start_section_list(): string {
        return html_writer::start_tag(
            'ul',
            [ 'class' => 'softcourse' ],
        );
    }

    /**
     * Generate the closing container html for a list of sections
     *
     * @return string HTML to output.
     */
    public function end_section_list(): string {
        return html_writer::end_tag('ul');
    }

    /**
     * Generate the title for this section page
     *
     * @return string the page title
     */
    public function page_title(): string {
        return get_string('topicoutline');
    }

    /**
     * Generate the edit control items of a section
     *
     * @param stdClass $course The course entry from DB
     * @param stdClass $section The course_section entry from DB
     * @param bool $onsectionpage true if being printed on a section page
     * @return array of edit control items
     */
    protected function section_edit_control_items($course, $section, $onsectionpage = false) {
        if (!$this->page->user_is_editing()) {
            return [];
        }

        $coursecontext = context_course::instance($course->id);

        if ($onsectionpage) {
            $url = course_get_url(
                $course,
                $section->section,
            );
        } else {
            $url = course_get_url($course);
        }
        $url->param(
            'sesskey',
            sesskey(),
        );

        $controls = [];
        if ($section->section && has_capability(
                'moodle/course:setcurrentsection',
                $coursecontext,
            )) {
            if ($course->marker == $section->section) {  // Show the "light globe" on/off.
                $url->param(
                    'marker',
                    0,
                );
                $markedthissection = get_string('markedthistopic');
                $highlightoff = get_string('highlightoff');
                $controls['highlight'] = [
                    'url' => $url,
                    "icon" => 'i/marked',
                    'name' => $highlightoff,
                    'pixattr' => [
                        'class' => '',
                        'alt' => $markedthissection,
                    ],
                    'attr' => [
                        'class' => 'editing_highlight',
                        'title' => $markedthissection,
                        'data-action' => 'removemarker',
                    ],
                ];
            } else {
                $url->param(
                    'marker',
                    $section->section,
                );
                $markthissection = get_string('markedthistopic');
                $highlight = get_string('highlight');
                $controls['highlight'] = [
                    'url' => $url,
                    "icon" => 'i/marker',
                    'name' => $highlight,
                    'pixattr' => [
                        'class' => '',
                        'alt' => $markthissection,
                    ],
                    'attr' => [
                        'class' => 'editing_highlight',
                        'title' => $markthissection,
                        'data-action' => 'setmarker',
                    ],
                ];
            }
        }

        $parentcontrols = parent::section_edit_control_items(
            $course,
            $section,
            $onsectionpage,
        );

        // If the edit key exists, we are going to insert our controls after it.
        if (array_key_exists(
            "edit",
            $parentcontrols,
        )) {
            $merged = [];
            // We can't use splice because we are using associative arrays.
            // Step through the array and merge the arrays.
            foreach ($parentcontrols as $key => $action) {
                $merged[$key] = $action;
                if ($key == "edit") {
                    // If we have come to the edit key, merge these controls here.
                    $merged = array_merge(
                        $merged,
                        $controls,
                    );
                }
            }

            return $merged;
        } else {
            return array_merge(
                $controls,
                $parentcontrols,
            );
        }
    }
}

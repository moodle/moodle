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
 * Contains the default section selector.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace core_courseformat\output\local\content;

use core\output\named_templatable;
use core_courseformat\base as course_format;
use core_courseformat\output\local\courseformat_named_templatable;
use renderable;
use section_info;
use stdClass;
use url_select;

/**
 * Represents the section selector.
 *
 * @package   core_courseformat
 * @copyright 2020 Ferran Recio <ferran@moodle.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class sectionselector implements named_templatable, renderable {

    use courseformat_named_templatable;

    /** @var string the indenter */
    private const INDENTER = '&nbsp;&nbsp;&nbsp;&nbsp;';
    /** @var course_format the course format class */
    protected $format;
    /** @var sectionnavigation the main section navigation class */
    protected $navigation;

    /** @var array $sectionmenu the sections indexed by url. */
    protected $sectionmenu = [];

    /**
     * Constructor.
     *
     * In the current imeplementaiton the seciton selector is almost a variation of the section navigator
     * but in the 4.0 this selector will be a kind of dropdown menu. When this happens the construct params
     * will change.
     *
     * @param course_format $format the course format
     * @param sectionnavigation $navigation the current section navigation
     */
    public function __construct(course_format $format, sectionnavigation $navigation) {
        $this->format = $format;
        $this->navigation = $navigation;
    }

    /**
     * Export this data so it can be used as the context for a mustache template.
     *
     * @param \renderer_base $output typically, the renderer that's calling this function
     * @return stdClass data context for a mustache template
     */
    public function export_for_template(\renderer_base $output): stdClass {

        $format = $this->format;
        $course = $format->get_course();

        $modinfo = $this->format->get_modinfo();

        $data = $this->navigation->export_for_template($output);

        $this->sectionmenu[course_get_url($course)->out(false)] = get_string('maincoursepage');

        // Add the section selector.
        $allsections = $modinfo->get_section_info_all();
        $disabledlink = $this->get_section_url($course, $allsections[$data->currentsection]);
        $sectionwithchildren = [];
        // First get any section with chidren (easier to process later in a regular loop).
        foreach ($allsections as $section) {
            if (!$section->uservisible) {
                unset($allsections[$section->sectionnum]);
                continue;
            }
            $sectiondelegated = $section->get_component_instance();
            if ($sectiondelegated) {
                unset($allsections[$section->sectionnum]);
                $parentsection = $sectiondelegated->get_parent_section();
                // If the section is delegated we need to get the parent section and add the section to the parent section array.
                if ($parentsection) {
                    $sectionwithchildren[$parentsection->sectionnum][] = $section;
                }
            }
        }

        foreach ($allsections as $section) {
            $this->add_section_menu($format, $course, $section);
            if (isset($sectionwithchildren[$section->sectionnum])) {
                foreach ($sectionwithchildren[$section->sectionnum] as $subsection) {
                    $this->add_section_menu($format, $course, $subsection, true);
                }
            }
        }
        $select = new url_select(
            urls: $this->sectionmenu,
            selected: '',
            nothing: ['' => get_string('jumpto')],
        );
        // Disable the current section.
        if (!is_null($disabledlink)) {
            $select->set_option_disabled($disabledlink);
        }
        $select->class = 'jumpmenu';
        $select->formid = 'sectionmenu';

        $data->selector = $output->render($select);
        return $data;
    }

    /**
     * Add a section to the section menu.
     *
     * @param course_format $format
     * @param stdClass $course
     * @param section_info $section
     * @param bool $indent
     */
    private function add_section_menu(
        course_format $format,
        stdClass $course,
        section_info $section,
        bool $indent = false
    ) {
        $url = $this->get_section_url($course, $section);
        if (!is_null($url)) {
            $indentation = $indent ? self::INDENTER : '';
            $this->sectionmenu[$url] = $indentation . $format->get_section_name($section);
        }
    }

    /**
     * Get the section url.
     * @param stdClass $course
     * @param section_info $section
     * @return string|null
     */
    private function get_section_url(stdClass $course, section_info $section): ?string {
        return course_get_url($course, (object) $section, ['navigation' => true])?->out(false);
    }
}
